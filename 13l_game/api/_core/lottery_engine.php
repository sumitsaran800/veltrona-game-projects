<?php
// Lottery engine for demo/virtual-wallet backend.
// Handles WinGo, TrxWinGo, K3, 5D/D5 and MotoRace bet settlement.

function le_game_interval(string $gameCode): int
{
    if (stripos($gameCode, '30S') !== false) return 30;
    if (stripos($gameCode, '3Min') !== false || stripos($gameCode, '_3M') !== false) return 180;
    if (stripos($gameCode, '5Min') !== false || stripos($gameCode, '_5M') !== false) return 300;
    return 60;
}

function le_is_k3(string $gameCode): bool { return stripos($gameCode, 'K3') === 0; }
function le_is_d5(string $gameCode): bool { return stripos($gameCode, '5D') === 0 || stripos($gameCode, 'D5') === 0; }
function le_is_moto(string $gameCode): bool { return stripos($gameCode, 'MotoRace') === 0 || stripos($gameCode, 'Moto') === 0; }
function le_is_wingo(string $gameCode): bool { return !le_is_k3($gameCode) && !le_is_d5($gameCode) && !le_is_moto($gameCode); }

function le_default_settings(): array
{
    return [
        'win_rate' => 45.0,
        'force_mode' => 'auto', // auto / win / lose
        'force_result' => '',
        'fee_percent' => 0.0,
        'payout_number' => 9.0,
        'payout_color' => 2.0,
        'payout_violet' => 4.5,
        'payout_bigsmall' => 2.0,
        'payout_k3' => 2.0,
        'payout_5d' => 9.5,
        'payout_moto' => 9.5,
        'immediate_settle' => 0,
    ];
}

function le_get_settings(string $gameCode): array
{
    $s = le_default_settings();
    $conn = db();
    if (!$conn) return $s;
    $stmt = @$conn->prepare('SELECT * FROM lottery_game_settings WHERE game_code=? OR game_code="*" ORDER BY game_code="*" ASC LIMIT 1');
    if (!$stmt) return $s;
    $stmt->bind_param('s', $gameCode);
    if (!$stmt->execute()) return $s;
    $row = $stmt->get_result()->fetch_assoc();
    if (!$row) return $s;
    foreach ($s as $k => $v) {
        if (array_key_exists($k, $row) && $row[$k] !== null && $row[$k] !== '') {
            $s[$k] = is_numeric($v) ? (float)$row[$k] : $row[$k];
        }
    }
    $s['immediate_settle'] = (int)($row['immediate_settle'] ?? 1);
    return $s;
}

function le_issue_by_offset(string $gameCode, int $offset = 0): string
{
    $interval = le_game_interval($gameCode);
    $now = time() - ($offset * $interval);
    $slot = intdiv($now, $interval);
    return date('Ymd', $slot * $interval) . '1000' . str_pad((string)($slot % 100000), 5, '0', STR_PAD_LEFT);
}

function le_color_from_number(int $n): string
{
    if ($n === 0) return 'red,violet';
    if ($n === 5) return 'green,violet';
    return ($n % 2 === 0) ? 'red' : 'green';
}

function le_result_detail(string $gameCode, string $premium, string $issueNumber = ''): array
{
    if (le_is_moto($gameCode)) {
        $parts = array_values(array_filter(array_map('trim', explode(',', $premium)), 'strlen'));
        $first = (int)($parts[0] ?? 1);
        return [
            'issueNumber' => $issueNumber,
            'premium' => $premium,
            'number' => $premium,
            'firstNumber' => $first,
            'color' => $first % 2 ? 'green' : 'red',
            'bigSmall' => $first > 5 ? 'big' : 'small',
            'sum' => array_sum(array_map('intval', $parts)),
        ];
    }
    if (le_is_k3($gameCode)) {
        $parts = array_values(array_filter(array_map('trim', explode(',', str_replace('|', ',', $premium))), 'strlen'));
        if (count($parts) < 3) $parts = [random_int(1,6), random_int(1,6), random_int(1,6)];
        $sum = array_sum(array_map('intval', $parts));
        return [
            'issueNumber' => $issueNumber,
            'premium' => implode(',', $parts),
            'number' => implode('', $parts),
            'dice' => $parts,
            'color' => $sum % 2 ? 'green' : 'red',
            'bigSmall' => $sum >= 11 ? 'big' : 'small',
            'sum' => $sum,
        ];
    }
    if (le_is_d5($gameCode)) {
        $digits = preg_replace('/\D+/', '', $premium);
        if (strlen($digits) < 5) $digits = str_pad($digits, 5, (string)random_int(0,9));
        $digits = substr($digits, 0, 5);
        $arr = array_map('intval', str_split($digits));
        $sum = array_sum($arr);
        return [
            'issueNumber' => $issueNumber,
            'premium' => $digits,
            'number' => $digits,
            'color' => $sum % 2 ? 'green' : 'red',
            'bigSmall' => $sum >= 23 ? 'big' : 'small',
            'sum' => $sum,
        ];
    }
    $n = (int)preg_replace('/\D+/', '', $premium);
    $n = max(0, min(9, $n));
    return [
        'issueNumber' => $issueNumber,
        'premium' => (string)$n,
        'number' => (string)$n,
        'color' => le_color_from_number($n),
        'bigSmall' => $n > 4 ? 'big' : 'small',
        'sum' => $n,
    ];
}

function le_random_premium(string $gameCode): string
{
    if (le_is_moto($gameCode)) { $nums = range(1, 10); shuffle($nums); return implode(',', $nums); }
    if (le_is_k3($gameCode)) return implode(',', [random_int(1,6), random_int(1,6), random_int(1,6)]);
    if (le_is_d5($gameCode)) return implode('', [random_int(0,9), random_int(0,9), random_int(0,9), random_int(0,9), random_int(0,9)]);
    return (string)random_int(0, 9);
}

function le_result_for_issue(string $gameCode, string $issueNumber, bool $save = true): array
{
    $conn = db();
    if ($conn) {
        $stmt = @$conn->prepare('SELECT * FROM lottery_results WHERE game_code=? AND issue_number=? LIMIT 1');
        if ($stmt) {
            $stmt->bind_param('ss', $gameCode, $issueNumber);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            if ($row) return le_result_detail($gameCode, (string)$row['premium'], $issueNumber);
        }
    }
    $settings = le_get_settings($gameCode);
    $forceResult = trim((string)($settings['force_result'] ?? ''));
    if ($forceResult !== '') {
        $premium = $forceResult;
    } else {
        $premium = le_random_premium($gameCode);
        // Result ek baar issue-level par generate hota hai. Agar pending bet hai to admin win_rate/force_mode
        // ke hisaab se result choose ho sakta hai, phir wahi result history API aur settlement dono use karte hain.
        if ($conn && $issueNumber !== '') {
            $gEsc = $conn->real_escape_string($gameCode);
            $iEsc = $conn->real_escape_string($issueNumber);
            $q = $conn->query("SELECT bet_content FROM lottery_bets WHERE game_code='$gEsc' AND issue_number='$iEsc' AND state=2 ORDER BY id ASC LIMIT 1");
            $bet = $q ? $q->fetch_assoc() : null;
            if ($bet) {
                $choice = le_extract_choice($gameCode, (string)$bet['bet_content']);
                $force = (string)($settings['force_mode'] ?? 'auto');
                if ($force === 'win') {
                    $premium = le_premium_for_choice($gameCode, $choice, true);
                } elseif ($force === 'lose') {
                    $premium = le_premium_for_choice($gameCode, $choice, false);
                } else {
                    $shouldWin = (random_int(1,10000) <= (int)round(((float)$settings['win_rate'])*100));
                    $premium = le_premium_for_choice($gameCode, $choice, $shouldWin);
                }
            }
        }
    }
    $detail = le_result_detail($gameCode, $premium, $issueNumber);
    if ($conn && $save) {
        $premium = (string)$detail['premium'];
        $number = (string)$detail['number'];
        $color = (string)$detail['color'];
        $bigSmall = (string)$detail['bigSmall'];
        $sum = (int)$detail['sum'];
        $stmt = @$conn->prepare('INSERT IGNORE INTO lottery_results(game_code, issue_number, premium, number, color, big_small, sum_value, open_time, created_at) VALUES(?,?,?,?,?,?,?,NOW(),NOW())');
        if ($stmt) { $stmt->bind_param('ssssssi', $gameCode, $issueNumber, $premium, $number, $color, $bigSmall, $sum); @$stmt->execute(); }
    }
    return $detail;
}

function le_bet_content_from_request(array $d): string
{
    $keys = ['betContent','bettingContent','selectType','playBet','playType','betType','number','color','bigSmall','betInfo','betDetail','content','pick'];
    foreach ($keys as $k) {
        if (isset($d[$k]) && $d[$k] !== '' && !is_array($d[$k])) return (string)$d[$k];
    }
    foreach ($keys as $k) {
        if (isset($d[$k]) && is_array($d[$k])) return json_encode($d[$k], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
    return 'Num_0';
}

function le_total_amount_from_request(array $d): array
{
    $amount = (float)first_value($d, ['amount','betAmount','bettingAmount','singleAmount','money','baseAmount','selectAmount','contractMoney'], 0);
    $multiple = (int)first_value($d, ['betMultiple','multiple','quantity','bettingQuantity','betCount','count','contractCount'], 1);
    if ($amount <= 0 && isset($d['totalAmount'])) { $amount = (float)$d['totalAmount']; $multiple = 1; }
    if ($multiple <= 0) $multiple = 1;
    return [$amount, $multiple, round($amount * $multiple, 2)];
}

function le_extract_choice(string $gameCode, string $content): array
{
    $c = strtolower($content);
    $clean = preg_replace('/(wingo|trxwingo|k3|5d|d5|motorace|motogame|1min|3min|5min|30s|issue|gamecode|amount|multiple|betcontent|playtype|playbet)/i', ' ', $content);
    $lc = strtolower($clean);
    if (preg_match('/violet|purple/', $lc)) return ['kind'=>'color','value'=>'violet'];
    if (preg_match('/green/', $lc)) return ['kind'=>'color','value'=>'green'];
    if (preg_match('/red/', $lc)) return ['kind'=>'color','value'=>'red'];
    if (preg_match('/small|chota/', $lc)) return ['kind'=>'bigsmall','value'=>'small'];
    if (preg_match('/big|bada/', $lc)) return ['kind'=>'bigsmall','value'=>'big'];
    if (le_is_moto($gameCode) && preg_match('/\b(10|[1-9])\b/', $clean, $m)) return ['kind'=>'number','value'=>(string)(int)$m[1]];
    if (le_is_k3($gameCode) && preg_match('/\b([3-9]|1[0-8])\b/', $clean, $m)) return ['kind'=>'sum','value'=>(string)(int)$m[1]];
    if (preg_match('/\b([0-9])\b/', $clean, $m)) return ['kind'=>'number','value'=>(string)(int)$m[1]];
    if (preg_match('/(?:num|number|digit|select)[^0-9]*([0-9])/', $c, $m)) return ['kind'=>'number','value'=>(string)(int)$m[1]];
    return ['kind'=>'number','value'=>'0'];
}

function le_result_matches(array $choice, array $result, string $gameCode): bool
{
    $kind = $choice['kind'] ?? 'number';
    $value = strtolower((string)($choice['value'] ?? ''));
    if ($kind === 'color') return in_array($value, array_map('trim', explode(',', strtolower((string)$result['color']))), true);
    if ($kind === 'bigsmall') return strtolower((string)$result['bigSmall']) === $value;
    if ($kind === 'sum') return (string)((int)$result['sum']) === (string)((int)$value);
    if (le_is_moto($gameCode)) return (string)((int)($result['firstNumber'] ?? 0)) === (string)((int)$value);
    if (le_is_k3($gameCode)) return strpos((string)$result['number'], (string)((int)$value)) !== false;
    if (le_is_d5($gameCode)) return strpos((string)$result['number'], (string)((int)$value)) !== false;
    return (string)((int)$result['number']) === (string)((int)$value);
}

function le_premium_for_choice(string $gameCode, array $choice, bool $shouldWin): string
{
    for ($i=0; $i<60; $i++) {
        if ($shouldWin) {
            if (le_is_wingo($gameCode)) {
                if ($choice['kind'] === 'number') $premium = (string)((int)$choice['value']);
                elseif ($choice['kind'] === 'color') {
                    $map = ['red'=>[2,4,6,8], 'green'=>[1,3,7,9], 'violet'=>[0,5]];
                    $arr = $map[strtolower($choice['value'])] ?? [0];
                    $premium = (string)$arr[array_rand($arr)];
                } else {
                    $premium = strtolower($choice['value']) === 'big' ? (string)random_int(5,9) : (string)random_int(0,4);
                }
            } elseif (le_is_k3($gameCode)) {
                if (($choice['kind'] ?? '') === 'sum') {
                    $target = max(3, min(18, (int)$choice['value']));
                    $a = random_int(1,6); $b = random_int(1,6); $c = max(1, min(6, $target-$a-$b));
                    while ($a+$b+$c !== $target) { $a=random_int(1,6); $b=random_int(1,6); $c=random_int(1,6); }
                    $premium = "$a,$b,$c";
                } else $premium = le_random_premium($gameCode);
            } elseif (le_is_d5($gameCode)) {
                $digit = preg_match('/^[0-9]$/', (string)$choice['value']) ? (string)$choice['value'] : (string)random_int(0,9);
                $arr = [random_int(0,9),random_int(0,9),random_int(0,9),random_int(0,9),random_int(0,9)];
                $arr[random_int(0,4)] = (int)$digit;
                $premium = implode('', $arr);
            } elseif (le_is_moto($gameCode)) {
                $first = max(1, min(10, (int)$choice['value']));
                $nums = range(1,10); shuffle($nums); $nums = array_values(array_diff($nums, [$first])); array_unshift($nums, $first);
                $premium = implode(',', $nums);
            } else $premium = le_random_premium($gameCode);
        } else {
            $premium = le_random_premium($gameCode);
        }
        $detail = le_result_detail($gameCode, $premium);
        if (le_result_matches($choice, $detail, $gameCode) === $shouldWin) return $premium;
    }
    return le_random_premium($gameCode);
}

function le_payout_rate(string $gameCode, array $choice, array $settings): float
{
    if (le_is_k3($gameCode)) return (float)$settings['payout_k3'];
    if (le_is_d5($gameCode)) return (float)$settings['payout_5d'];
    if (le_is_moto($gameCode)) return (float)$settings['payout_moto'];
    if (($choice['kind'] ?? '') === 'color') return strtolower((string)$choice['value']) === 'violet' ? (float)$settings['payout_violet'] : (float)$settings['payout_color'];
    if (($choice['kind'] ?? '') === 'bigsmall') return (float)$settings['payout_bigsmall'];
    return (float)$settings['payout_number'];
}

function le_response_row(array $r): array
{
    $premium = trim((string)($r['premium'] ?? ''));
    if ($premium === '') {
        $result = ['number'=>'','color'=>'','bigSmall'=>'','sum'=>0,'premium'=>''];
    } else {
        $result = le_result_detail((string)$r['game_code'], $premium, (string)$r['issue_number']);
    }
    return [
        'orderNo' => (string)$r['order_no'],
        'issueNumber' => (string)$r['issue_number'],
        'gameCode' => (string)$r['game_code'],
        'betContent' => (string)$r['bet_content'],
        'amount' => (float)$r['amount'],
        'betMultiple' => (int)$r['bet_multiple'],
        'realAmount' => (float)$r['real_amount'],
        'fee' => (float)$r['fee'],
        'premium' => $premium,
        'number' => (string)$result['number'],
        'color' => (string)$result['color'],
        'bigSmall' => (string)$result['bigSmall'],
        'sum' => (int)$result['sum'],
        'playType' => explode('_', (string)$r['bet_content'])[0] ?? '',
        'state' => (int)$r['state'],
        'isWin' => (int)$r['state'] === 1,
        'isPending' => (int)$r['state'] === 2,
        'winLoseAmount' => (float)$r['win_lose_amount'],
        'betTime' => strtotime((string)$r['created_at']) * 1000,
        'createTime' => strtotime((string)$r['created_at']) * 1000,
    ];
}

function le_issue_is_closed(string $gameCode, string $issueNumber): bool
{
    $issueNumber = trim($issueNumber);
    if ($issueNumber === '') return false;
    $current = function_exists('lottery_issue') ? lottery_issue($gameCode)['issueNumber'] : le_issue_by_offset($gameCode, 0);
    return strcmp($issueNumber, (string)$current) < 0;
}

function le_settle_pending_bets(string $gameCode = '', string $issueNumber = '', int $userId = 0, string $orderNo = ''): int
{
    $conn = db();
    if (!$conn) return 0;
    $where = ['state=2'];
    if ($gameCode !== '') $where[] = "game_code='" . $conn->real_escape_string($gameCode) . "'";
    if ($issueNumber !== '') $where[] = "issue_number='" . $conn->real_escape_string($issueNumber) . "'";
    if ($userId > 0) $where[] = 'user_id=' . (int)$userId;
    if ($orderNo !== '') $where[] = "order_no='" . $conn->real_escape_string($orderNo) . "'";
    $sql = 'SELECT * FROM lottery_bets WHERE ' . implode(' AND ', $where) . ' ORDER BY id ASC LIMIT 500';
    $rs = $conn->query($sql);
    if (!$rs) return 0;
    $settled = 0;
    while ($r = $rs->fetch_assoc()) {
        $g = (string)$r['game_code'];
        $issue = (string)$r['issue_number'];
        if (!le_issue_is_closed($g, $issue)) continue;

        $result = le_result_for_issue($g, $issue, true); // same result that history/result API returns
        $choice = le_extract_choice($g, (string)$r['bet_content']);
        $settings = le_get_settings($g);
        $isWin = le_result_matches($choice, $result, $g);
        $rate = le_payout_rate($g, $choice, $settings);
        $stake = (float)$r['real_amount'];
        $fee = (float)$r['fee'];
        $debit = round($stake + $fee, 2);
        $payout = $isWin ? round($stake * $rate, 2) : 0.0;
        $net = round($payout - $debit, 2);
        $state = $isWin ? 1 : 0;
        $premium = (string)$result['premium'];
        $betId = (int)$r['id'];
        $uid = (int)$r['user_id'];
        $order = (string)$r['order_no'];
        $vendor = 'ARLottery';
        $remark = $g . ' ' . (string)$r['bet_content'] . ' result ' . $premium;
        $back = 0.0;

        @$conn->begin_transaction();
        $check = $conn->query('SELECT state FROM lottery_bets WHERE id=' . $betId . ' FOR UPDATE');
        $fresh = $check ? $check->fetch_assoc() : null;
        if (!$fresh || (int)$fresh['state'] !== 2) { @$conn->rollback(); continue; }

        $stmt = $conn->prepare('UPDATE lottery_bets SET premium=?, state=?, win_lose_amount=? WHERE id=?');
        if (!$stmt) { @$conn->rollback(); continue; }
        $stmt->bind_param('sidi', $premium, $state, $net, $betId);
        if (!$stmt->execute()) { @$conn->rollback(); continue; }

        if ($payout > 0) {
            $stmt = $conn->prepare('UPDATE users SET balance=balance+? WHERE id=?');
            if (!$stmt) { @$conn->rollback(); continue; }
            $stmt->bind_param('di', $payout, $uid);
            if (!$stmt->execute()) { @$conn->rollback(); continue; }
            $record = $order . 'W';
            $type = 'GameWin';
            $stmt = $conn->prepare('INSERT IGNORE INTO financial_records(user_id, record_no, order_no, vendor_code, type, amount, back_amount, remark, created_at) VALUES(?,?,?,?,?,?,?,?,NOW())');
            if ($stmt) { $stmt->bind_param('issssdds', $uid, $record, $order, $vendor, $type, $payout, $back, $remark); $stmt->execute(); }
        }
        @$conn->commit();
        $settled++;
    }
    return $settled;
}
