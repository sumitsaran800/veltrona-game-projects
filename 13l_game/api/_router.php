<?php
require_once __DIR__ . '/_core/bootstrap.php';
require_once __DIR__ . '/_core/license_client.php';

$path = $GLOBALS['API_PATH'] ?? ($_GET['path'] ?? '');
if (!$path) {
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    $pos = strpos($script, '/api/');
    $path = $pos === false ? '' : substr($script, $pos + 5);
    $path = preg_replace('/\.php$/', '', $path);
}
$path = trim($path, '/');
$data = request_data();

// V20 local license endpoints + API lock
if ($path === 'License/Activate') { $k = (string)($data['licenseKey'] ?? $data['license_key'] ?? ''); api_success(lc_activate($k)); }
if ($path === 'License/Status') { api_success(['valid'=>lc_is_valid(), 'state'=>lc_state()]); }
if ($path === 'License/Chat') { api_success(lc_send_chat((string)($data['name']??''), (string)($data['message']??''), (string)($data['contact']??''))); }
if ($path === 'License/PopupState') { api_success(function_exists('lc_front_popup_state') ? lc_front_popup_state() : ['enabled'=>false]); }
// V21 owner package: local game/admin does not require license. License admin controls client sites.

$strictAuthPaths = [
    'User/GetUserInfo','User/UpdateUserNickName','User/UpdateUserPhoto','User/SetWithdrawPassword',
    'Withdraw/GetWithdrawBasicInfo','Withdraw/GetWithdrawHistory','Withdraw/GetUserWithdrawWallet','Withdraw/AddUserWithdrawWallet','Withdraw/WithdrawApply',
    'Recharge/DepositRecharge','Recharge/GoodsDepositRecharge','Recharge/SubmitCertificate','Recharge/ArUpiSubmitUtr','Recharge/CancelLocalRecharge',
    'Lottery/WinGoBet','Lottery/VideoWinGoBet','Lottery/K3Bet','Lottery/D5Bet','Lottery/MotoRaceBet','Lottery/TrxWinGoBet','Lottery/GameBetting',
];
if (in_array($path, $strictAuthPaths, true)) {
    $GLOBALS['STRICT_AUTH_USER'] = require_login_user();
}

switch ($path) {
    case 'Site/GetSettings': handle_v24_site_settings(); break;
    case 'Home/Register': handle_register($data); break;
    case 'Home/Login': handle_login($data); break;
    case 'Home/MobileAutoLogin':
    case 'Home/EmailAutoLogin':
    case 'Home/AutoLogin': handle_auto_login($data); break;
    case 'Home/RefreshToken': handle_refresh($data); break;
    case 'Home/LoginOff': handle_login_off(); break;
    case 'Home/CheckCanBet': api_success(true); break;
    case 'Home/HomeBasic': handle_home_basic(); break;
    case 'Home/GetHomeAllGameList': handle_home_games(); break;
    case 'Home/GetSpreadMaterial': handle_spread_material(); break;
    case 'Home/GetGiftInfo': handle_static_overlay_user($path); break;
    case 'Home/AppLaunch': api_success(null); break;
    case 'Home/Captcha': handle_captcha(); break;
    case 'Home/TenantFrontStyle': handle_static_overlay_user($path); break;
    case 'Home/GetCommonMessage': handle_static_overlay_user($path); break;
    case 'Home/GetCommonPopup': handle_static_overlay_user($path); break;
    case 'User/GetUserInfo': api_success(user_response(require_login_user())); break;
    case 'User/GetUserFinancialList': handle_financial_list($data); break;
    case 'User/GetUserOrderList': api_success(['list'=>[], 'pageNo'=>1, 'totalPage'=>0, 'totalCount'=>0]); break;
    case 'User/GetUserCouponList': handle_coupon_list(); break;
    case 'User/GetUserCouponDetail': handle_coupon_detail($data); break;
    case 'User/UseUserCoupon': handle_use_gift_code($data); break;
    case 'User/GetUserRechargeCouponList': handle_coupon_list(); break;
    case 'User/UpdateUserNickName': handle_update_user('nickname', first_value($data, ['nickName','nickname','name'], '')); break;
    case 'User/UpdateUserPhoto': handle_update_user('photo', first_value($data, ['userPhoto','photo','avatar'], '1')); break;
    case 'User/UpdateUserLoginSysLanguage': api_success(true); break;
    case 'User/SetWithdrawPassword': handle_set_withdraw_password($data); break;
    case 'Withdraw/GetWithdrawBasicInfo': handle_withdraw_basic(); break;
    case 'Withdraw/GetWithdrawHistory': handle_withdraw_history($data); break;
    case 'Withdraw/GetUserWithdrawWallet': handle_withdraw_wallets($data); break;
    case 'Withdraw/AddUserWithdrawWallet': handle_add_wallet($data); break;
    case 'Withdraw/WithdrawApply': handle_withdraw_apply($data); break;

    case 'Recharge/GetRechargeCategoryList': handle_recharge_categories($data); break;
    case 'Recharge/GetRechargeBasicInfo': handle_recharge_basic_info(); break;
    case 'Recharge/GetRechargeRecord': handle_recharge_record($data); break;
    case 'Recharge/DepositRecharge': handle_recharge_deposit($data); break;
    case 'Recharge/GoodsDepositRecharge': handle_recharge_deposit($data); break;
    case 'Recharge/GetLocalRechargeOrderDetail': handle_recharge_order_detail($data); break;
    case 'Recharge/SubmitCertificate': handle_recharge_submit_certificate($data); break;
    case 'Recharge/CancelLocalRecharge': handle_recharge_cancel($data); break;
    case 'Recharge/ArUpiGetBankListToken': api_success(['walletActivationPageUrl'=>'','returnUrl'=>'']); break;
    case 'Recharge/CreateRechargeOrderAppeal': api_success(['rechargeOrderAppealPageUrl'=>'']); break;
    case 'Recharge/ArUpiCancelRechargeOrder': api_success(true); break;
    case 'Recharge/GetArUpiOnGoingOrder': api_success(null); break;
    case 'Recharge/ArUpiSubmitUtr': handle_recharge_submit_certificate($data); break;
    case 'Recharge/ArBuriedPage': api_success(true); break;
    case 'Withdraw/GetWalletCodeList': handle_wallet_code_list($data); break;
    case 'Withdraw/GetArbWalletInfo': api_success(['isActive'=>false,'walletAddress'=>'']); break;
    case 'Withdraw/ActivityArbWallet': api_success(true); break;
    case 'Activity/GetLuckyDoubleTaskDetails': handle_lucky_double_tasks(); break;
    case 'Activity/GetLuckyDoubleRechargeTaskConfigs': handle_lucky_double_recharge_configs(); break;
    case 'Activity/CompleteJoinTelegramTask': handle_activity_complete($data, 'join_telegram'); break;
    case 'Activity/ClaimLuckyDoubleTaskReward': handle_activity_claim($data); break;
    case 'Activity/ReceivedLuckyDoubleReward': handle_activity_claim($data); break;
    case 'Activity/GetActivityInformationList': handle_activity_information_list(); break;
    case 'Activity/GetActivityInformationDetail': handle_activity_information_detail($data); break;
    case 'Activity/GetActivityGuideConfig': handle_activity_guide_config(); break;
    case 'Activity/ReportActivityGuideProcess': api_success(['token'=>base64_encode('{"OrderNo":"","CreateTime":0}')]); break;
    case 'Activity/ReceiveOpenPushGuideReward': handle_simple_reward('open_push', 1.00); break;
    case 'Activity/GetShareCopy': handle_share_copy(); break;
    case 'Activity/GetUserInvitedWheelInfo': handle_invited_wheel_info(); break;
    case 'Activity/SpinInvitedWheel': handle_spin_invited_wheel(); break;
    case 'Activity/SumitInvitedWheelWithdraw': handle_invited_wheel_withdraw(); break;
    case 'Activity/GetPageListInvitedWheelWithdrawRecord': handle_invited_wheel_withdraw_record($data); break;
    case 'Activity/ReceivedPromotionShareReward': handle_simple_reward('promotion_share', 1.00); break;
    case 'Activity/GetUserGiftPackList': handle_gift_pack_list(); break;
    case 'Activity/UpdateGiftPackSelection': api_success(true); break;
    case 'Activity/UpdateGiftPackCompleted': api_success(true); break;
    case 'Activity/ReceiveGiftPack': handle_simple_reward('gift_pack', 2.00); break;
    case 'Activity/GetUserRechargeGiftPackList': handle_gift_pack_list(); break;
    case 'Activity/ReceiveSpecialBonus': handle_simple_reward('special_bonus', 1.00); break;
    case 'Activity/GetUserDayWeekInfo': handle_day_week_info_v14(); break;
    case 'Activity/GetDayWeekTaskRule': handle_day_week_rule_v14(); break;
    case 'Activity/ReceiveDayWeekTaskReward': handle_day_week_claim_v14($data); break;
    case 'Activity/DayWeekAccumulateReceive': handle_day_week_claim_v14($data); break;
    case 'Activity/GetUserCheckInActivityData': handle_checkin_data(); break;
    case 'Activity/ReceiveDailyCheckInReward': handle_simple_reward('daily_checkin', 1.00); break;
    case 'Activity/GetUserLossReliefActivityList': handle_loss_relief(); break;
    case 'Activity/ReceiveUserLossReliefReward': handle_simple_reward('loss_relief', 1.00); break;
    case 'Activity/GetNextCashRainStatus': api_success(['state'=>0,'nextStartTime'=>now_ms()+3600000]); break;
    case 'Activity/GetCashRainRules': api_success(['content'=>'Cash rain demo rules.']); break;
    case 'Activity/GetLatestCashRainClaimRecords': api_success([]); break;
    case 'Activity/ReceivedCashRainReward': handle_simple_reward('cash_rain', 1.00); break;
    case 'Activity/GetCardPlanRechargeCategory': handle_recharge_categories($data); break;
    case 'Activity/RechargeCardPlanToPay': handle_recharge_deposit($data); break;
    case 'Activity/CardPlanReceiveReward': handle_simple_reward('card_plan', 1.00); break;
    case 'Activity/RechargeGiftToPay': handle_recharge_deposit($data); break;
    case 'Activity/GetUserRankRewardList': api_success(['list'=>[], 'pageNo'=>1, 'totalPage'=>0, 'totalCount'=>0]); break;
    case 'Activity/GetAgentRankRewardList': api_success(['list'=>[], 'pageNo'=>1, 'totalPage'=>0, 'totalCount'=>0]); break;
    case 'Activity/GetUserRankRecord': api_success(['list'=>[], 'pageNo'=>1, 'totalPage'=>0, 'totalCount'=>0]); break;
    case 'Activity/GetAgentRankRecord': api_success(['list'=>[], 'pageNo'=>1, 'totalPage'=>0, 'totalCount'=>0]); break;
    case 'Activity/GetCodewashingInfo': api_success(['washAmount'=>0,'canReceiveAmount'=>0]); break;
    case 'Activity/GetCodewashingPageList': api_success(['list'=>[], 'pageNo'=>1, 'totalPage'=>0, 'totalCount'=>0]); break;
    case 'Activity/GetCodewashingPageListFlow': api_success(['list'=>[], 'pageNo'=>1, 'totalPage'=>0, 'totalCount'=>0]); break;
    case 'Activity/OneClickCodeWashing': api_success(['amount'=>0]); break;
    case 'Activity/GetCodewashingDescription': api_success(['content'=>'Code washing demo is disabled.']); break;
    case 'Activity/GetChampionPageList': api_success(['list'=>[], 'pageNo'=>1, 'totalPage'=>0, 'totalCount'=>0]); break;
    case 'Activity/GetChampionInfo': api_success(['amount'=>0,'state'=>0]); break;
    case 'Activity/AddChampion': api_success(true); break;
    case 'Activity/GetBigJackpotConfigList': api_success([]); break;
    case 'Activity/GetHomeBigJackpotRecordPageList': api_success(['list'=>[], 'pageNo'=>1, 'totalPage'=>0, 'totalCount'=>0]); break;
    case 'Activity/GetUserBigJackpotRecordPageList': api_success(['list'=>[], 'pageNo'=>1, 'totalPage'=>0, 'totalCount'=>0]); break;
    case 'Activity/UserReceiveBigJackpotAward': handle_simple_reward('big_jackpot', 1.00); break;
    case 'Activity/UserReceiveAllBigJackpotAward': handle_simple_reward('big_jackpot_all', 1.00); break;
    case 'Activity/GetPageListRechargeWheelRewardRecord': handle_recharge_wheel_reward_record($data); break;
    case 'Activity/GetPageListRechargeWheelSpinRecord': handle_recharge_wheel_spin_record($data); break;
    case 'Activity/GetUserRechargeWheelInfo': handle_recharge_wheel_info(); break;
    case 'Activity/SpinRechargeWheel': handle_recharge_wheel_spin($data); break;
    case 'Activity/GetListRechargeWheelRewardHistory': handle_recharge_wheel_reward_history(); break;
    case 'AgentRebate/GetPromotionData': handle_promotion_data_v14(); break;
    case 'AgentRebate/GetCommissionDetail': handle_commission_detail_v14(); break;
    case 'AgentRebate/GetPageListNewSub': handle_agent_list_v14($data); break;
    case 'AgentRebate/GetPageListTeamDayReport': handle_agent_report_v14($data); break;
    case 'AgentRebate/GetPageListTeamDayReportRechargeWithdrawDiff': handle_agent_report_v14($data); break;
    case 'AgentRebate/GetPageListSubList': handle_agent_list_v14($data); break;
    case 'AgentRebate/GetPageListSubordinateUserInfo': handle_agent_list_v14($data); break;
    case 'AgentRebate/GetRebateLevelList': handle_rebate_levels_v14(); break;
    case 'AgentRebate/GetRebateLevelRateList': handle_rebate_rates_v14(); break;
    case 'AgentL3/GetMyTeamInfo': handle_agent_l3_team_info(); break;
    case 'AgentL3/GetMyInvitationInfo': handle_agent_l3_invitation_info(); break;
    case 'AgentL3/GetMySubDataSummry': handle_agent_l3_sub_summary(); break;
    case 'AgentL3/GetPageListSubData': handle_agent_l3_sub_data($data); break;
    case 'AgentL3/GetListCommissionRecord': handle_agent_l3_commission_record($data); break;
    case 'AgentL3/GetPageListCommissionDetailRecordByRecharge': handle_agent_l3_commission_detail_records($data, 'recharge'); break;
    case 'AgentL3/GetPageListCommissionDetailRecordByBet': handle_agent_l3_commission_detail_records($data, 'bet'); break;
    case 'AgentL3/GetPageListInviteRecord': handle_agent_l3_invite_record($data); break;
    case 'AgentL3/GetPageListInviteTaskRecord': handle_agent_l3_invite_task_record($data); break;
    case 'AgentL3/ReceiveNotSendCommissionAmount': handle_agent_l3_receive_commission(); break;
    case 'Game/GetVendorList': api_static_or_empty('Game/GetVendorList'); break;
    case 'Game/GetSubGamePageList': handle_sub_game_list($data); break;
    case 'Game/GetHotGameList': handle_hot_games(); break;
    case 'Game/GetGameListByName': handle_game_by_name($data); break;
    case 'Game/GetGameDrawTimeList': api_success([]); break;
    case 'ThirdGame/GetGameUrl': handle_game_url($data); break;
    case 'ThirdGame/GetARGameBalance': handle_ar_balance(); break;
    case 'ThirdGame/GetARGameAndPlatWallets': handle_ar_wallets(); break;
    case 'ThirdGame/Transfer': api_success_no_data(); break;
    case 'ThirdGame/RecoverSaasBalance': api_success(true); break;
    case 'ThirdGame/NotifyARGameRecover': api_success(true); break;
    case 'VipLevel/GetUserVipInfo': handle_vip_info(); break;
    case 'VipLevel/GetVipLevelConfig': handle_vip_config(); break;
    case 'VipLevel/GetUserVipRewardList': handle_vip_reward_list($data); break;
    case 'VipLevel/PickVipReward': handle_vip_pick_reward($data); break;
    case 'Lottery/GetGameList':
    case 'Lottery/GameListPage':
    case 'Lottery/GetGameListPage': handle_lottery_game_list($data); break;
    case 'Lottery/GetUserInfo':
    case 'Lottery/GetmyEmeralds': handle_lottery_user_info(); break;
    case 'Lottery/GetBalance':
    case 'Lottery/GetBalanceInfo': handle_lottery_balance(); break;
    case 'Lottery/GetGameInfo': handle_lottery_game_info($data); break;
    case 'Lottery/GetBetLimit': handle_lottery_bet_limit($data); break;
    case 'Lottery/GetGameIntroduce': handle_lottery_introduce($data); break;
    case 'Lottery/GetHistoryIssuePage':
    case 'Lottery/GetLotteryResultHistory':
    case 'Lottery/GetmyIssusPage': handle_lottery_history($data); break;
    case 'Lottery/GetRecordPage':
    case 'Lottery/GetMyGameRecordPageList':
    case 'Lottery/GetMyGameRecord': handle_lottery_record($data); break;
    case 'Lottery/GetTrendStatistics': handle_lottery_trend($data); break;
    case 'Lottery/GetWinLossResult': handle_win_loss($data); break;
    case 'Lottery/GetWingoLiveUrl': api_success(['url'=>'']); break;
    case 'Lottery/GetDragonList': api_success([]); break;
    case 'Admin/GetResultHistory': handle_admin_result_history($data); break;
    case 'Lottery/WinGoBet':
    case 'Lottery/VideoWinGoBet':
    case 'Lottery/K3Bet':
    case 'Lottery/D5Bet':
    case 'Lottery/MotoRaceBet':
    case 'Lottery/TrxWinGoBet':
    case 'Lottery/GameBetting': handle_lottery_bet($path, $data); break;
    case 'WorkOrder/GetFormList': handle_workorder_form_list(); break;
    case 'WorkOrder/GetHomePageConfigs': handle_workorder_home_config(); break;
    case 'WorkOrder/GetWorkOrderPageList':
    case 'WorkOrder/GetPageList':
    case 'WorkOrder/GetProgressQuery': handle_workorder_list($data); break;
    case 'WorkOrder/SubmitWorkOrder':
    case 'WorkOrder/CreateWorkOrder':
    case 'WorkOrder/AddWorkOrder': handle_workorder_submit($data); break;
    case 'WorkOrder/GetWorkOrderDetail':
    case 'WorkOrder/GetDetail': handle_workorder_detail($data); break;
    case 'WorkOrder/GetQuestionList': handle_workorder_questions($data); break;
    case 'GetFormFieldList':
    case 'api/GetFormFieldList':
    case 'WorkOrder/GetFormFieldList': handle_workorder_field_list($data); break;
    case 'WorkOrder/GetOutLinkList': handle_workorder_outlink_list($data); break;
    case 'WorkOrder/GetTutorial': handle_workorder_tutorial($data); break;
    case 'WorkOrder/GetFaqList':
    case 'WorkOrder/GetFAQList': handle_workorder_faq_list(); break;
    case 'WorkOrder/GetFaqDetail':
    case 'WorkOrder/GetFAQDetail': handle_workorder_faq_detail($data); break;
    case 'WorkOrder/GetCaptcha': handle_workorder_captcha(); break;
    case 'WorkOrder/GetCommentList':
    case 'WorkOrder/GetWorkOrderCommentList': handle_workorder_comment_list($data); break;
    case 'WorkOrder/SubmitComment':
    case 'WorkOrder/AddComment': handle_workorder_submit_comment($data); break;
    case 'WorkOrder/Submit': handle_workorder_submit($data); break;
    case 'WorkOrder/GetSettings': handle_workorder_settings(); break;
    case 'WorkOrder/GetFormTutorialInfo': handle_workorder_tutorial($data); break;
    case 'WorkOrder/DataCheckByOrderNo': handle_workorder_data_check($data); break;
    case 'WorkOrder/SendReminder': api_success(true); break;
    case 'WorkOrder/UploadToOss': handle_v26_upload_file(); break;
    case 'Upload/UploadImage':
    case 'Upload/UploadFile':
    case 'File/Upload':
    case 'Home/UploadFile':
    case 'Activity/UploadImage': handle_v26_upload_file(); break;
    default: api_static_or_empty($path);
}

function handle_static_overlay_user(string $path): void
{
    // Home banners come from GetCommonMessage. Do not replace original banner list with admin notices,
    // otherwise the homepage carousel becomes blank after install.
    if ($path === 'Home/GetCommonMessage') {
        $json = static_json('Home/GetCommonMessage');
        $list = [];
        if ($json && isset($json['data']) && is_array($json['data'])) {
            $list = $json['data'];
        }
        $set = site_settings();
        if (empty($set['home_banner_enabled'])) {
            $list = array_values(array_filter($list, function($m){ return (int)($m['type'] ?? 0) !== 4; }));
        }
        $conn = db();
        if ($conn) {
            $rs = @$conn->query("SELECT * FROM notifications WHERE status=1 ORDER BY id DESC LIMIT 10");
            if ($rs) {
                while ($r = $rs->fetch_assoc()) {
                    $list[] = [
                        'id' => (int)$r['id'],
                        'sysLanguage' => 'en',
                        'type' => ((string)$r['type'] === 'banner') ? 4 : 1,
                        'imageUrl' => (string)($r['image_url'] ?? ''),
                        'title' => (string)$r['title'],
                        'content' => (string)$r['content'],
                        'sort' => (int)$r['id'],
                        'jumpUrl' => (string)$r['jump_url'],
                        'buttonTxt' => '',
                        'messageJumpType' => $r['jump_url'] ? 1 : 0,
                        'pageType' => 0,
                        'vendorCode' => '',
                        'gameCode' => '',
                        'gameId' => '',
                        'customPopupId' => 0,
                        'lastUpdateTime' => now_ms(),
                    ];
                }
            }
        }
        api_success($list);
    }

    if ($path === 'Home/GetCommonPopup') {
        $set = site_settings();
        if (empty($set['popup_enabled'])) api_success(null);
        $json = static_json('Home/GetCommonPopup');
        $popup = ($json && array_key_exists('data', $json)) ? $json['data'] : null;
        $conn = db();
        if ($conn) {
            $rs = @$conn->query("SELECT * FROM notifications WHERE status=1 AND type='popup' ORDER BY id DESC LIMIT 1");
            if ($rs && ($r = $rs->fetch_assoc())) {
                $popup = [
                    'id' => (int)$r['id'],
                    'sysLanguage' => 'en',
                    'type' => 3,
                    'imageUrl' => (string)($r['image_url'] ?? ''),
                    'title' => (string)$r['title'],
                    'content' => (string)$r['content'],
                    'jumpUrl' => (string)$r['jump_url'],
                    'messageJumpType' => $r['jump_url'] ? 1 : 0,
                    'lastUpdateTime' => now_ms(),
                ];
            }
        }
        api_success($popup);
    }
    api_static_or_empty($path);
}


function v31_share_origin(): string
{
    $proto = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')) ? 'https' : 'http';
    $host = $_SERVER['HTTP_X_FORWARDED_HOST'] ?? ($_SERVER['HTTP_HOST'] ?? 'localhost');
    return $proto . '://' . $host;
}

function v31_invite_rewards(): array
{
    $pairs = [[1,50],[2,50],[3,50],[4,50],[5,50],[10,250],[15,250],[20,250],[30,500],[40,500],[50,500],[60,500],[70,500],[80,500],[90,500],[100,500],[150,2500],[200,2500],[250,2500],[300,2500],[350,2500],[400,2500],[450,2500],[500,2500],[600,5000],[700,5000],[800,5000],[900,5000],[1000,5000],[1500,25000],[2000,25000],[3000,40000],[4000,40000],[5000,40000],[6000,40000],[7000,40000],[8000,50000],[10000,100000],[15000,250000],[20000,300000]];
    return array_map(function($p){ return ['inviteUserCount'=>$p[0], 'rewardAmount'=>(float)$p[1]]; }, $pairs);
}

function handle_spread_material(): void
{
    // Share page material list. These keys are the ones used by the minified Vue share route.
    // Files are included in the ZIP under /img/6007/other so the share page does not fall back to wrong headset/icon images.
    $items = [
        ['id'=>1, 'file'=>'/img/6007/other/115317224-36312-file_20260415115317224.webp', 'title'=>'Joyful Free Bonus'],
        ['id'=>2, 'file'=>'/img/6007/other/115940251-36317-file_20260415115940251.webp', 'title'=>'Invite Friends Bonus'],
        ['id'=>3, 'file'=>'/img/6007/other/115907299-36316-file_20260415115907299.webp', 'title'=>'Exclusive Surprise Bonus'],
    ];
    $out = [];
    foreach ($items as $i => $it) {
        $out[] = [
            'id' => $it['id'],
            'sysLanguage' => 'en',
            'title' => $it['title'],
            'content' => 'Invite friends and earn rewards',
            'imgUrl' => $it['file'],
            'imageUrl' => $it['file'],
            'materialUrl' => $it['file'],
            'backgroundUrl' => $it['file'],
            'url' => $it['file'],
            'sort' => 100 - $i,
            'status' => 1,
            'type' => 1,
            'qrCodeInfo' => json_encode(['x'=>514,'y'=>570,'w'=>110,'h'=>110], JSON_UNESCAPED_SLASHES),
        ];
    }
    api_success($out);
}

function handle_register(array $d): void
{
    $username = first_value($d, ['userName','username','mobile','phone','account'], '');
    $password = first_value($d, ['password','pwd'], '123456');
    if (!$username) $username = '91' . random_int(7000000000, 9999999999);
    $conn = db();
    if (!$conn) {
        $u = demo_user(); $u['username'] = $username; $u['mobile'] = $username; $u['nickname'] = 'Member' . substr($username, -4);
        login_response($u, true);
    }
    $stmt = $conn->prepare('SELECT id FROM users WHERE username=? LIMIT 1');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    if ($stmt->get_result()->fetch_assoc()) api_error('User already exists', 1, 9);
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $nick = 'Member' . strtoupper(substr(md5($username), 0, 8));
    $invite = strtoupper(substr(md5($username . time()), 0, 6));
    $mobile = $username;
    $tenantUserId = (string)(APP_TENANT_ID . random_int(1000000000, 9999999999));
    $parentId = 0; $parentCode = first_value($d, ['inviteCode','invitationCode','referralCode'], '');
    if ($parentCode) { $ps = $conn->prepare('SELECT id FROM users WHERE invite_code=? LIMIT 1'); if ($ps) { $ps->bind_param('s', $parentCode); $ps->execute(); $pr=$ps->get_result()->fetch_assoc(); $parentId=(int)($pr['id']??0); } }
    $stmt = $conn->prepare('INSERT INTO users(tenant_user_id, username, mobile, password_hash, nickname, photo, balance, vip_level, invite_code, role, status, created_at) VALUES(?,?,?,?,? ,"1",0,1,?,"user",1,NOW())');
    $stmt->bind_param('ssssss', $tenantUserId, $username, $mobile, $hash, $nick, $invite);
    if (!$stmt->execute()) api_error('Register failed', 1, 314);
            $id = (int)$conn->insert_id;
    if ($parentId > 0) { $up=$conn->prepare('UPDATE users SET agent_parent_id=? WHERE id=?'); if($up){$up->bind_param('ii',$parentId,$id);$up->execute();} }
    $session = create_user_session($conn, $id, first_value($d, ['deviceId','deviceid','deviceNo','deviceType','fingerprint'], ''));
    $user = ['id'=>$id,'tenant_user_id'=>$tenantUserId,'username'=>$username,'mobile'=>$mobile,'nickname'=>$nick,'photo'=>'1','balance'=>0,'safe_box'=>0,'vip_level'=>1,'invite_code'=>$invite,'role'=>'user','status'=>1,'login_session_token'=>$session];
    login_response($user, true);
}

function handle_login(array $d): void
{
    $username = first_value($d, ['userName','username','mobile','phone','account'], '919119098026');
    $password = first_value($d, ['password','pwd'], '123456');
    $conn = db();
    if (!$conn) login_response(demo_user(), false);
    $stmt = $conn->prepare('SELECT * FROM users WHERE username=? OR mobile=? OR email=? LIMIT 1');
    $stmt->bind_param('sss', $username, $username, $username);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if (!$user || (isset($user['status']) && (int)$user['status'] !== 1)) api_error('User does not exist', 101, 101);
    if (!password_verify($password, $user['password_hash'] ?? '')) {
        // Demo convenience: allow 123456 for seeded test user only.
        if ($password !== '123456') api_error('Password error', 1, 1);
    }
    $stmt = $conn->prepare('UPDATE users SET last_login_at=NOW(), ip_last=?, device_id=? WHERE id=?');
    $uid = (int)$user['id']; $ip = $_SERVER['REMOTE_ADDR'] ?? ''; $deviceId = first_value($d, ['deviceId','deviceid','deviceNo','deviceType','fingerprint'], ''); if ($stmt) { $stmt->bind_param('ssi', $ip, $deviceId, $uid); $stmt->execute(); }
    $user['login_session_token'] = create_user_session($conn, $uid, $deviceId);
    $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 250); $log=$conn->prepare('INSERT INTO user_login_logs(user_id,ip,user_agent,device_id,created_at) VALUES(?,?,?,?,NOW())'); if($log){$log->bind_param('isss',$uid,$ip,$ua,$deviceId);$log->execute();}
    $user['ip_last']=$ip; $user['device_id']=$deviceId;
    login_response($user, false);
}

function handle_auto_login(array $d): void { login_response(require_login_user(), false); }

function handle_refresh(array $d = []): void
{
    $conn = db();
    if (!$conn) api_error('Database unavailable', 500, 500);

    $incomingRefresh = (string)first_value($d, ['refreshToken','refresh_token'], '');
    $payload = parse_token(bearer_token());
    $uid = (int)($payload['uid'] ?? 0);
    $sid = (string)($payload['sid'] ?? '');

    if ($incomingRefresh !== '') {
        $stmt = @$conn->prepare('SELECT s.user_id,u.* FROM user_sessions s INNER JOIN users u ON u.id=s.user_id WHERE s.refresh_token=? AND s.is_active=1 AND (s.refresh_expires_at IS NULL OR s.refresh_expires_at>NOW()) LIMIT 1');
        if ($stmt) {
            $stmt->bind_param('s', $incomingRefresh);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
            if ($user) { $uid = (int)$user['id']; }
        }
    }

    if ($uid <= 0 && $sid === '') api_error('Login expired. Please login again.', 401, 401);

    if ($uid > 0 && $sid !== '') {
        $stmt = @$conn->prepare('SELECT u.* FROM users u INNER JOIN user_sessions s ON s.user_id=u.id WHERE u.id=? AND s.session_token=? AND s.is_active=1 LIMIT 1');
        if ($stmt) {
            $stmt->bind_param('is', $uid, $sid);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();
        }
    }
    if (empty($user)) api_error('Login expired. Please login again.', 401, 401);

    $uid = (int)$user['id'];
    $deviceId = client_device_id($d);
    $user['login_session_token'] = create_user_session($conn, $uid, $deviceId);
    login_response($user, false);
}

function handle_login_off(): void { $conn=db(); $payload=parse_token(bearer_token()); if($conn && $payload){ $uid=(int)($payload['uid']??0); $sid=(string)($payload['sid']??''); if($uid>0){ $stmt=@$conn->prepare('UPDATE user_sessions SET is_active=0,last_seen_at=NOW() WHERE user_id=? AND session_token=?'); if($stmt){$stmt->bind_param('is',$uid,$sid);$stmt->execute();} $stmt=@$conn->prepare("UPDATE users SET login_session_token='' WHERE id=? AND login_session_token=?"); if($stmt){$stmt->bind_param('is',$uid,$sid);$stmt->execute();} } } api_success(true); }

function login_response(array $user, bool $isNew): void
{
    $token = make_token($user);
    $expires = time() + 86400;
    $refreshToken = (string)($GLOBALS['API_REFRESH_TOKEN'] ?? ($user['refresh_token'] ?? $token));
    $GLOBALS['API_REFRESH_TOKEN'] = '';
    $origin = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
    api_success([
        'tokenHeader' => 'Bearer ',
        'token' => $token,
        'expiresIn' => $expires,
        'refreshToken' => $refreshToken,
        'passwordErrorNum' => 0,
        'passwordErrorMaxNum' => null,
        'lotteryLoginUrl' => $origin . '/?Token=' . rawurlencode($token),
        'googleVerifyInfo' => null,
        'canBet' => true,
        'userId' => (int)$user['id'],
        'webSocketUrl' => '',
        'isNewRegister' => $isNew,
        'redirectUrl' => '',
        'apkDownloadUrl' => '',
        'shortLoginCode' => '',
        'packageTransferConfig' => null,
    ]);
}

function handle_captcha(): void
{
    $set = site_settings();
    if (empty($set['captcha_enabled'])) api_success(['enabled'=>false,'captchaId'=>'','captchaCode'=>'','img'=>'']);
    api_success(['enabled'=>true,'captchaId'=>(string)random_int(100000,999999),'captchaCode'=>'1234','img'=>'']);
}

function handle_home_basic(): void
{
    $set = site_settings();
    if (empty($set['home_enabled']) || !empty($set['maintenance_enabled'])) { api_success(['isMaintenance'=>true,'maintenanceText'=>$set['maintenance_text'] ?? 'Maintenance','floatWindows'=>[]]); }
    $json = static_json('Home/HomeBasic') ?: ['data'=>[]];
    $home = $json['data'] ?? [];
    $conn = db();
    if ($conn) {
        $rows = $conn->query('SELECT id, name, sort, icon_url, jump_type, jump_detail, display_target FROM banners WHERE status=1 ORDER BY sort DESC, id DESC');
        if ($rows && $rows->num_rows) {
            $home['floatWindows'] = [];
            while ($r = $rows->fetch_assoc()) {
                $home['floatWindows'][] = [
                    'id'=>(int)$r['id'], 'floatName'=>$r['name'], 'priority'=>(int)$r['sort'], 'floatIcon'=>$r['icon_url'],
                    'effectiveTimeType'=>0, 'jumpType'=>(int)$r['jump_type'], 'jumpDetail'=>$r['jump_detail'], 'displayTarget'=>(int)$r['display_target']
                ];
            }
        }
    }
    api_success($home);
}

function handle_home_games(): void
{
    $json = static_json('Home/GetHomeAllGameList');
    if (!$json) api_success(['games'=>[], 'hotGames'=>[], 'popularGames'=>[]]);
    $payload = $json['data'];
    $conn = db();
    if ($conn) {
        $disabled = [];
        $q = $conn->query('SELECT game_code, is_maintenance FROM games');
        if ($q) while ($r=$q->fetch_assoc()) $disabled[$r['game_code']] = (bool)$r['is_maintenance'];
        $walk = function (&$item) use ($disabled) {
            if (isset($item['gameCode']) && array_key_exists($item['gameCode'], $disabled)) {
                $item['isGameMaintenance'] = $disabled[$item['gameCode']];
            }
        };
        foreach ($payload['games'] as &$cat) foreach ($cat['gameList'] as &$g) $walk($g);
        foreach ($payload['hotGames'] as &$g) $walk($g);
        foreach ($payload['popularGames'] as &$g) $walk($g);
    }
    $set = site_settings();
    $allowedGame = function($code) use ($set) {
        $c = strtolower((string)$code);
        if (str_contains($c,'wingo') && empty($set['wingo_enabled'])) return false;
        if (str_contains($c,'k3') && empty($set['k3_enabled'])) return false;
        if ((str_contains($c,'d5') || str_contains($c,'5d')) && empty($set['d5_enabled'])) return false;
        if (str_contains($c,'moto') && empty($set['moto_enabled'])) return false;
        if (str_contains($c,'trx') && empty($set['trx_enabled'])) return false;
        return true;
    };
    if (isset($payload['games']) && is_array($payload['games'])) {
        foreach ($payload['games'] as &$cat) {
            if (isset($cat['gameList']) && is_array($cat['gameList'])) {
                $cat['gameList'] = array_values(array_filter($cat['gameList'], function($g) use ($allowedGame){ return $allowedGame($g['gameCode'] ?? $g['name'] ?? ''); }));
            }
        }
    }
    foreach (['hotGames','popularGames','topGames'] as $key) if (isset($payload[$key]) && is_array($payload[$key])) {
        $payload[$key] = array_values(array_filter($payload[$key], function($g) use ($allowedGame){ return $allowedGame($g['gameCode'] ?? $g['name'] ?? ''); }));
    }
    api_success($payload);
}

function handle_financial_list(array $d): void
{
    $u = current_user() ?: demo_user();
    $conn = db();
    if (!$conn) { api_static_or_empty('User/GetUserFinancialList'); }
    $pageNo = max(1, (int)($d['pageNo'] ?? 1));
    $pageSize = max(1, min(50, (int)($d['pageSize'] ?? 10)));
    $off = ($pageNo - 1) * $pageSize;
    $uid = (int)$u['id'];
    $total = 0;
    $stmt = $conn->prepare('SELECT COUNT(*) c FROM financial_records WHERE user_id=?');
    $stmt->bind_param('i',$uid); $stmt->execute(); $total=(int)$stmt->get_result()->fetch_assoc()['c'];
    $stmt = $conn->prepare('SELECT * FROM financial_records WHERE user_id=? ORDER BY id DESC LIMIT ? OFFSET ?');
    $stmt->bind_param('iii',$uid,$pageSize,$off); $stmt->execute(); $rs=$stmt->get_result();
    $list=[];
    while($r=$rs->fetch_assoc()){
        $list[]=['id'=>(string)$r['record_no'],'orderNo'=>(string)$r['order_no'],'vendorCode'=>$r['vendor_code'],'type'=>$r['type'],'subType'=>$r['sub_type'],'amount'=>(float)$r['amount'],'backAmount'=>(float)$r['back_amount'],'createTime'=>strtotime($r['created_at'])*1000,'remark'=>$r['remark']];
    }
    api_success(['list'=>$list,'pageNo'=>$pageNo,'totalPage'=>(int)ceil($total/$pageSize),'totalCount'=>$total]);
}

function handle_update_user(string $field, string $value): void
{
    if (!in_array($field, ['nickname','photo'], true) || $value === '') api_success(true);
    $u=current_user() ?: demo_user(); $conn=db(); if($conn){$stmt=$conn->prepare("UPDATE users SET $field=? WHERE id=?"); $uid=(int)$u['id']; $stmt->bind_param('si',$value,$uid); $stmt->execute();}
    api_success(true);
}

function handle_withdraw_basic(): void
{
    $json = static_json('Withdraw/GetWithdrawBasicInfo') ?: ['data'=>[]];
    $u = require_login_user();
    $payload = $json['data'];
    $set = site_settings();
    $payload['balance'] = (float)($u['balance'] ?? 0);
    $payload['realName'] = $u['real_name'] ?? ($u['nickname'] ?? 'User');
    $payload['minWithdrawAmount'] = (float)($set['min_withdraw_amount'] ?? ($payload['minWithdrawAmount'] ?? 110));
    $payload['minimumWithdrawAmount'] = (float)($set['min_withdraw_amount'] ?? ($payload['minimumWithdrawAmount'] ?? 110));
    $payload['needBetMultiplier'] = (float)($set['withdraw_need_bet_multiplier'] ?? 0);
    $payload['isNeedWithdrawPassword'] = true;
    $payload['hasWithdrawPassword'] = !empty($u['withdraw_password_hash']);
    $payload['addWalletNeedEmailVerifyCode'] = false;
    $payload['addWalletNeedSmsVerifyCode'] = false;
    api_success($payload);
}
function handle_withdraw_history(array $d): void { api_static_or_empty('Withdraw/GetWithdrawHistory'); }
function handle_withdraw_wallets(array $d = []): void
{
    $u = require_login_user();
    $conn = db();
    if (!$conn) api_success([]);
    $uid = (int)$u['id'];
    $typeRaw = (string)first_value($d, ['withdrawType','walletType','type'], '');
    if ($typeRaw !== '') {
        $type = normalize_withdraw_type($typeRaw);
        $stmt = $conn->prepare('SELECT * FROM withdraw_wallets WHERE user_id=? AND status=1 AND wallet_type=? ORDER BY id DESC');
        if ($stmt) { $stmt->bind_param('is',$uid,$type); $stmt->execute(); $rs=$stmt->get_result(); }
    } else {
        $stmt = $conn->prepare('SELECT * FROM withdraw_wallets WHERE user_id=? AND status=1 ORDER BY id DESC');
        if ($stmt) { $stmt->bind_param('i',$uid); $stmt->execute(); $rs=$stmt->get_result(); }
    }
    $list=[]; if(isset($rs) && $rs) while($r=$rs->fetch_assoc()) $list[] = wallet_response_row($r);
    api_success($list);
}

function handle_wallet_code_list(array $d): void
{
    $type = normalize_withdraw_type((string)first_value($d, ['withdrawType','walletType','type'], 'BankCard'));
    api_success(bank_code_options($type));
}

function handle_set_withdraw_password(array $d): void
{
    $u = require_login_user();
    $conn = db();
    if (!$conn) api_success(true);
    $pwd = (string)first_value($d, ['withdrawPassword','newWithdrawPassword','password','pwd'], '');
    if (!preg_match('/^[0-9]{6}$/', $pwd)) {
        api_error('Please enter 6 digit withdraw password', 401, 401);
    }
    $hash = password_hash($pwd, PASSWORD_BCRYPT);
    $uid = (int)$u['id'];
    $stmt = @$conn->prepare('UPDATE users SET withdraw_password_hash=? WHERE id=?');
    if ($stmt) { $stmt->bind_param('si', $hash, $uid); $stmt->execute(); }
    admin_audit_log('user_set_withdraw_password', 'user', $uid, ['user_id'=>$uid]);
    api_success(true);
}

function handle_add_wallet(array $d): void
{
    $u = require_login_user();
    $conn = db();
    if (!$conn) api_success(true);
    $uid = (int)$u['id'];
    $type = infer_withdraw_type($d);
    $bankCode = strtoupper((string)first_value($d, ['bankCode'], ''));
    $holderName = first_value($d, ['holderName','realName','name','accountName'], '');
    if ($type === 'BankCard' && $bankCode !== '') {
        $name = bank_name_by_code($bankCode);
    } else {
        $name = first_value($d, ['bankName','walletName','realName','name','accountName','holderName'], '');
    }
    if ($name === '') $name = $type;

    $account = first_value($d, ['accountNo','bankAccountNo','cardNo','upiId','usdtAddress','address','walletAddress'], '');
    $ifsc = strtoupper((string)first_value($d, ['ifscCode','ifsc'], ''));
    $mobile = first_value($d, ['mobileNo','phone','mobile'], $u['mobile'] ?? '');
    $account = trim((string)$account);

    if ($account === '') api_error('Please enter account number', 401, 401);
    if ($type === 'BankCard') {
        if (!preg_match('/^[0-9]{6,25}$/', $account)) api_error('Please enter correct bank account number', 401, 401);
        if ($ifsc !== '' && !preg_match('/^[A-Z]{4}0[A-Z0-9]{6}$/', $ifsc)) api_error('Please enter correct IFSC format', 401, 401);
    }
    if ($type === 'UPI' && !valid_upi_id($account)) {
        api_error('Please enter correct UPI ID format', 401, 401);
    }
    if ($type === 'USDT' && !preg_match('/^T[1-9A-HJ-NP-Za-km-z]{33}$/', $account)) {
        api_error('Please enter correct USDT TRC20 address', 401, 401);
    }

    $normAccount = normalize_account_no($account);
    $stmt = @$conn->prepare('SELECT id FROM withdraw_wallets WHERE user_id=? AND wallet_type=? AND LOWER(REPLACE(account_no," ",""))=? AND status=1 LIMIT 1');
    if ($stmt) {
        $stmt->bind_param('iss', $uid, $type, $normAccount);
        $stmt->execute();
        if ($stmt->get_result()->fetch_assoc()) api_error('This withdraw account is already added', 409, 409);
    }

    $rawData = $d;
    $rawData['withdrawType'] = $type;
    $rawData['walletType'] = $type;
    $rawData['bankName'] = $name;
    if ($holderName !== '') $rawData['holderName'] = $holderName;
    if ($type === 'UPI') $rawData['upiId'] = $account;
    if ($type === 'USDT') $rawData['usdtAddress'] = $account;
    if ($bankCode !== '') $rawData['bankCode'] = $bankCode;
    $raw = json_encode($rawData, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

    $stmt = $conn->prepare('INSERT INTO withdraw_wallets(user_id,wallet_type,wallet_name,account_no,ifsc_code,mobile_no,wallet_data,status,created_at) VALUES(?,?,?,?,?,?,?,1,NOW())');
    if ($stmt) { $stmt->bind_param('issssss',$uid,$type,$name,$account,$ifsc,$mobile,$raw); $stmt->execute(); }
    $wid = (int)$conn->insert_id;
    admin_audit_log('user_add_withdraw_wallet', 'withdraw_wallet', $wid, ['user_id'=>$uid,'wallet_type'=>$type,'account_no'=>$account]);
    api_success(['walletId'=>(string)$wid]);
}

function handle_withdraw_apply(array $d): void
{
    $u = require_login_user();
    $amount=(float)first_value($d,['amount','withdrawAmount'],0);
    $set = site_settings();
    $minWd = (float)($set['min_withdraw_amount'] ?? 0);
    if ($amount <= 0) api_error('Invalid amount', 401, 401);
    if ($minWd > 0 && $amount < $minWd) api_error('Minimum withdraw amount is '.number_format($minWd,2,'.',''), 401, 401);

    $conn=db();
    if($conn){
        $uid=(int)$u['id'];
        if (!empty($u['withdraw_password_hash'])) {
            $pwd = (string)first_value($d, ['withdrawPassword','password','pwd'], '');
            if ($pwd === '' || !password_verify($pwd, (string)$u['withdraw_password_hash'])) {
                api_error('Withdraw password error', 401, 401);
            }
        } else {
            api_error('Please set withdraw password first', 401, 401);
        }

        $walletInfo = '';
        $wid = (int)first_value($d, ['walletId','withdrawWalletId','userWithdrawWalletId'], 0);
        $method=normalize_withdraw_type((string)first_value($d,['withdrawType','method'],'BankCard'));
        if ($wid > 0) {
            $stmt=$conn->prepare('SELECT * FROM withdraw_wallets WHERE id=? AND user_id=? AND status=1 AND wallet_type=? LIMIT 1');
            $stmt->bind_param('iis',$wid,$uid,$method); $stmt->execute(); $wr=$stmt->get_result()->fetch_assoc();
            if (!$wr) api_error('Withdraw account not found for this user', 404, 404);
            $walletInfo = json_encode(wallet_response_row($wr), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        } else {
            $walletInfo = json_encode($d, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        }
        $stmt=$conn->prepare('INSERT INTO withdraw_requests(user_id, amount, status, method, wallet_info, created_at) VALUES(?, ?, "pending", ?, ?, NOW())');
        $stmt->bind_param('idss',$uid,$amount,$method,$walletInfo); $stmt->execute();
        $reqId = (int)$conn->insert_id;
        admin_audit_log('user_create_withdraw_request', 'withdraw_request', $reqId, ['user_id'=>$uid,'amount'=>$amount,'method'=>$method]);
    }
    api_success(['orderNo'=>'WD'.date('ymdHis').random_int(100,999)]);
}




function default_quick_config(string $type='UPI'): array
{
    if ($type === 'USDT') {
        $amounts = [10,30,50,100,300,500,1000,3000,5000,10000,50000,100000];
        return array_map(fn($a)=>['rechargeAmount'=>(float)$a,'giftAmount'=>0.0], $amounts);
    }
    $pairs = [[100,0],[300,0],[500,10],[1000,30],[2000,40],[3000,50],[5000,60],[8000,80],[10000,100],[20000,200],[30000,300],[50000,500]];
    return array_map(fn($p)=>['rechargeAmount'=>(float)$p[0],'giftAmount'=>(float)$p[1]], $pairs);
}

function recharge_categories_data(): array
{
    static $payload = null;
    if ($payload !== null) return $payload;
    $conn = db();
    if ($conn) {
        $rs = @$conn->query("SELECT * FROM payment_methods WHERE state=1 ORDER BY sort DESC,id DESC");
        if ($rs instanceof mysqli_result && $rs->num_rows > 0) {
            $payload = [];
            while ($r = $rs->fetch_assoc()) {
                $name = trim((string)($r['name'] ?? ''));
                $type = trim((string)($r['recharge_type'] ?? 'UPI')) ?: 'UPI';
                $minAmount = (float)($r['min_amount'] ?? 0);
                $maxAmount = (float)($r['max_amount'] ?? 0);
                // Broken/half-saved admin rows frontend ko blank bana dete hain; unhe skip karo.
                if ($name === '' || $minAmount <= 0 || $maxAmount <= 0 || $maxAmount < $minAmount) continue;

                $quick = json_decode($r['quick_config_json'] ?? '[]', true);
                if (!is_array($quick) || !$quick) $quick = default_quick_config($type);
                $gift = (float)($r['gift_ratio'] ?? 2);
                $row = [
                    'id'=>(int)$r['id'], 'name'=>$name, 'rechargeType'=>$type,
                    'state'=>(int)$r['state'], 'sort'=>(int)$r['sort'], 'iconUrl'=>(string)$r['icon_url'],
                    'selectedIconUrl'=>(string)$r['selected_icon_url'], 'rate'=>(float)$r['rate'],
                    'minAmount'=>$minAmount, 'maxAmount'=>$maxAmount,
                    'rechargeGiftRatio'=>['giftRatioType'=>1,'scaleType'=>1,'uniformRatioData'=>['giftRatio'=>$gift,'isCheckInterval'=>false,'minScale'=>null,'maxScale'=>null],'intervalRatioList'=>null],
                    'quickConfigList'=>$quick, 'giftRatioType'=>0, 'giftAmount'=>0.0, 'isUsedArUpiRechargeAmount'=>false,
                ];
                // Alias keys: kuch original build in keys ko payment method render me use karta hai.
                $row['rechargeChannelId'] = $row['id'];
                $row['rechargeChannelName'] = $name;
                $row['channelName'] = $name;
                $row['paymentName'] = $name;
                $row['payName'] = $name;
                $payload[] = $row;
            }
            if (count($payload) > 0) return $payload;
            // Agar DB rows corrupt/empty hon to neeche original static response fallback hoga.
        }
    }
    $json = <<<'JSON'
{"data":[{"id":400088,"name":"UPI-QR","rechargeType":"UPI","state":1,"sort":45,"iconUrl":"/img/6007/bankLogo/081619759-31176-file_20260414081619737.webp","selectedIconUrl":"/img/6007/bankLogo/081625196-31177-file_20260414081625174.webp","rate":1.0,"minAmount":100.0000,"maxAmount":50000.0000,"rechargeGiftRatio":{"giftRatioType":1,"scaleType":1,"uniformRatioData":{"giftRatio":2.0,"isCheckInterval":false,"minScale":null,"maxScale":null},"intervalRatioList":null},"quickConfigList":[{"rechargeAmount":100.0,"giftAmount":0.0},{"rechargeAmount":300.0,"giftAmount":0.0},{"rechargeAmount":500.0,"giftAmount":10.0},{"rechargeAmount":1000.0,"giftAmount":30.0},{"rechargeAmount":2000.0,"giftAmount":40.0},{"rechargeAmount":3000.0,"giftAmount":50.0},{"rechargeAmount":5000.0,"giftAmount":60.0},{"rechargeAmount":8000.0,"giftAmount":80.0},{"rechargeAmount":10000.0,"giftAmount":100.0},{"rechargeAmount":20000.0,"giftAmount":200.0},{"rechargeAmount":30000.0,"giftAmount":300.0},{"rechargeAmount":50000.0,"giftAmount":500.0}],"giftRatioType":0,"giftAmount":0.0,"isUsedArUpiRechargeAmount":false},{"id":400084,"name":"UPI*QR","rechargeType":"UPI","state":1,"sort":40,"iconUrl":"/img/6007/bankLogo/033005260-32516-file_20260422153005258.webp","selectedIconUrl":"/img/6007/bankLogo/033017566-32517-file_20260422153017564.webp","rate":1.0,"minAmount":100.0000,"maxAmount":50000.0000,"rechargeGiftRatio":{"giftRatioType":1,"scaleType":1,"uniformRatioData":{"giftRatio":2.0,"isCheckInterval":false,"minScale":null,"maxScale":null},"intervalRatioList":null},"quickConfigList":[{"rechargeAmount":100.0,"giftAmount":0.0},{"rechargeAmount":300.0,"giftAmount":0.0},{"rechargeAmount":500.0,"giftAmount":10.0},{"rechargeAmount":1000.0,"giftAmount":30.0},{"rechargeAmount":2000.0,"giftAmount":40.0},{"rechargeAmount":3000.0,"giftAmount":50.0},{"rechargeAmount":5000.0,"giftAmount":60.0},{"rechargeAmount":8000.0,"giftAmount":80.0},{"rechargeAmount":10000.0,"giftAmount":100.0},{"rechargeAmount":20000.0,"giftAmount":200.0},{"rechargeAmount":30000.0,"giftAmount":300.0},{"rechargeAmount":50000.0,"giftAmount":500.0}],"giftRatioType":0,"giftAmount":0.0,"isUsedArUpiRechargeAmount":false},{"id":400086,"name":"EWallet","rechargeType":"BankCard","state":1,"sort":30,"iconUrl":"/img/6007/bankLogo/081307018-31172-file_20260414081307017.webp","selectedIconUrl":"/img/6007/bankLogo/081312092-31173-file_20260414081312091.webp","rate":1.0,"minAmount":100.0000,"maxAmount":50000.0000,"rechargeGiftRatio":{"giftRatioType":1,"scaleType":1,"uniformRatioData":{"giftRatio":2.0,"isCheckInterval":false,"minScale":null,"maxScale":null},"intervalRatioList":null},"quickConfigList":[{"rechargeAmount":100.0,"giftAmount":0.0},{"rechargeAmount":300.0,"giftAmount":0.0},{"rechargeAmount":500.0,"giftAmount":10.0},{"rechargeAmount":1000.0,"giftAmount":30.0},{"rechargeAmount":2000.0,"giftAmount":40.0},{"rechargeAmount":3000.0,"giftAmount":50.0},{"rechargeAmount":5000.0,"giftAmount":60.0},{"rechargeAmount":8000.0,"giftAmount":80.0},{"rechargeAmount":10000.0,"giftAmount":100.0},{"rechargeAmount":20000.0,"giftAmount":200.0},{"rechargeAmount":30000.0,"giftAmount":300.0},{"rechargeAmount":50000.0,"giftAmount":500.0}],"giftRatioType":0,"giftAmount":0.0,"isUsedArUpiRechargeAmount":false},{"id":400085,"name":"Paytm*QR","rechargeType":"BankCard","state":1,"sort":25,"iconUrl":"/img/6007/bankLogo/032817571-32514-file_20260422152817570.webp","selectedIconUrl":"/img/6007/bankLogo/032830034-32515-file_20260422152830031.webp","rate":1.0,"minAmount":100.0000,"maxAmount":5000.0000,"rechargeGiftRatio":{"giftRatioType":1,"scaleType":1,"uniformRatioData":{"giftRatio":2.0,"isCheckInterval":false,"minScale":null,"maxScale":null},"intervalRatioList":null},"quickConfigList":[{"rechargeAmount":100.0,"giftAmount":0.0},{"rechargeAmount":300.0,"giftAmount":0.0},{"rechargeAmount":500.0,"giftAmount":10.0},{"rechargeAmount":1000.0,"giftAmount":30.0},{"rechargeAmount":2000.0,"giftAmount":40.0},{"rechargeAmount":3000.0,"giftAmount":50.0},{"rechargeAmount":5000.0,"giftAmount":60.0},{"rechargeAmount":8000.0,"giftAmount":80.0},{"rechargeAmount":10000.0,"giftAmount":100.0},{"rechargeAmount":20000.0,"giftAmount":200.0},{"rechargeAmount":30000.0,"giftAmount":300.0},{"rechargeAmount":50000.0,"giftAmount":500.0}],"giftRatioType":0,"giftAmount":0.0,"isUsedArUpiRechargeAmount":false},{"id":400087,"name":"USDT","rechargeType":"USDT","state":1,"sort":10,"iconUrl":"/img/6007/bankLogo/032547277-32512-file_20260422152547275.webp","selectedIconUrl":"/img/6007/bankLogo/032559385-32513-file_20260422152559383.webp","rate":97.0,"minAmount":10.0000,"maxAmount":100000.0000,"rechargeGiftRatio":{"giftRatioType":1,"scaleType":1,"uniformRatioData":{"giftRatio":2.0,"isCheckInterval":false,"minScale":null,"maxScale":null},"intervalRatioList":null},"quickConfigList":[{"rechargeAmount":10.0,"giftAmount":0.00},{"rechargeAmount":30.0,"giftAmount":0.00},{"rechargeAmount":50.0,"giftAmount":0.00},{"rechargeAmount":100.0,"giftAmount":0.00},{"rechargeAmount":300.0,"giftAmount":0.00},{"rechargeAmount":500.0,"giftAmount":0.00},{"rechargeAmount":1000.0,"giftAmount":0.00},{"rechargeAmount":3000.0,"giftAmount":0.00},{"rechargeAmount":5000.0,"giftAmount":0.00},{"rechargeAmount":10000.0,"giftAmount":0.00},{"rechargeAmount":50000.0,"giftAmount":0.00},{"rechargeAmount":100000.0,"giftAmount":0.00}],"giftRatioType":0,"giftAmount":0.0,"isUsedArUpiRechargeAmount":false},{"id":400095,"name":"ARPAY","rechargeType":"ARPay","state":1,"sort":5,"iconUrl":"/img/6007/bankLogo/032439802-32510-file_20260422152439790.webp","selectedIconUrl":"/img/6007/bankLogo/032453856-32511-file_20260422152453843.webp","rate":1.0,"minAmount":100.0000,"maxAmount":100000.0000,"rechargeGiftRatio":{"giftRatioType":1,"scaleType":1,"uniformRatioData":{"giftRatio":2.0,"isCheckInterval":false,"minScale":null,"maxScale":null},"intervalRatioList":null},"quickConfigList":[{"rechargeAmount":100.0,"giftAmount":0.00},{"rechargeAmount":300.0,"giftAmount":0.00},{"rechargeAmount":500.0,"giftAmount":0.00},{"rechargeAmount":1000.0,"giftAmount":0.00},{"rechargeAmount":2000.0,"giftAmount":0.00},{"rechargeAmount":3000.0,"giftAmount":0.00},{"rechargeAmount":5000.0,"giftAmount":0.00},{"rechargeAmount":8000.0,"giftAmount":0.00},{"rechargeAmount":10000.0,"giftAmount":0.00},{"rechargeAmount":20000.0,"giftAmount":0.00},{"rechargeAmount":30000.0,"giftAmount":0.00},{"rechargeAmount":50000.0,"giftAmount":0.00}],"giftRatioType":0,"giftAmount":0.0,"isUsedArUpiRechargeAmount":false}],"code":0,"msg":"Succeed","msgCode":0,"serverTime":1778896804496}
JSON;
    $decoded = json_decode($json, true);
    $payload = $decoded['data'] ?? [];
    foreach ($payload as &$row) {
        $row['rechargeChannelId'] = (int)($row['id'] ?? 0);
        $row['rechargeChannelName'] = (string)($row['name'] ?? '');
        $row['channelName'] = (string)($row['name'] ?? '');
        $row['paymentName'] = (string)($row['name'] ?? '');
        $row['payName'] = (string)($row['name'] ?? '');
    }
    unset($row);
    return $payload;
}

function recharge_category_by_id($id): ?array
{
    foreach (recharge_categories_data() as $cat) {
        if ((string)($cat['id'] ?? '') === (string)$id) return $cat;
    }
    return null;
}

function handle_recharge_categories(array $d): void
{
    $list = recharge_categories_data();
    $id = first_value($d, ['rechargeCategoryId','id'], '');
    if ($id !== '') {
        $one = recharge_category_by_id($id);
        $list = $one ? [$one] : [];
    }
    api_success($list);
}

function handle_recharge_basic_info(): void
{
    $u = current_user() ?: demo_user();
    $balance = (float)($u['balance'] ?? 0);
    api_success([
        'onGoingOrder' => null,
        'advisementList' => [],
        'classicBonusDetails' => [],
        'goodsList' => [],
        'gameSaasBalance' => [
            ['vendorCode' => 'PlatForm', 'balance' => $balance, 'withdrawAbleAmount' => $balance]
        ],
        'amountCoding' => 0,
        'cashBalance' => $balance,
        'balance' => $balance,
        'withdrawAbleAmount' => $balance,
        'tenantCurrency' => APP_CURRENCY,
        'currencySign' => APP_CURRENCY_SIGN,
    ]);
}

function recharge_state_to_num(string $status): int
{
    if ($status === 'Payed') return 1;
    if ($status === 'Cancel') return 4;
    if ($status === 'PendingReview') return 2;
    return 0;
}

function v23_clean_asset_url(string $url): string
{
    $url = trim($url);
    if ($url === '') return '';

    if (preg_match('~^(https?:)?//|^data:|^blob:|^upi:~i', $url)) {
        return $url;
    }

    return '/' . ltrim($url, '/');
}

function v23_qr_from_upi_link(string $upiLink): string
{
    if ($upiLink === '') return '';
    return 'https://api.qrserver.com/v1/create-qr-code/?size=260x260&margin=10&data=' . rawurlencode($upiLink);
}

function v23_is_bad_qr(string $qr, string $icon = '', string $selectedIcon = ''): bool
{
    $qr = trim($qr);
    if ($qr === '') return true;

    $q = strtolower(ltrim($qr, '/'));
    $i = strtolower(ltrim($icon, '/'));
    $s = strtolower(ltrim($selectedIcon, '/'));

    if ($i !== '' && $q === $i) return true;
    if ($s !== '' && $q === $s) return true;

    // Agar galti se icon/bankLogo QR me save ho gaya ho to real UPI QR auto generate hoga.
    if (str_contains($q, 'banklogo/')) return true;

    return false;
}

function recharge_order_row(array $r): array
{
    $created = strtotime((string)($r['created_at'] ?? 'now')) * 1000;
    $status = (string)($r['status'] ?? 'Wait');

    $customerArr = [];
    $rechargeArr = [];

    if (!empty($r['customer_info'])) {
        $tmp = json_decode((string)$r['customer_info'], true);
        if (is_array($tmp)) $customerArr = $tmp;
    }

    if (!empty($r['recharge_info'])) {
        $tmp = json_decode((string)$r['recharge_info'], true);
        if (is_array($tmp)) $rechargeArr = $tmp;
    }

    $categoryId = (int)($r['recharge_category_id'] ?? 0);
    $amount = (float)($r['amount'] ?? 0);
    $orderNo = (string)($r['order_no'] ?? '');

    // Admin Payment / UPI me jo latest UPI aur QR save hoga,
    // arUpiV2/recharge detail page par wahi live show hoga.
    $livePay = payment_info_for_category($categoryId, $amount, $orderNo);

    $customerArr = array_merge($customerArr, $livePay['customerInfo']);
    $rechargeArr = array_merge($rechargeArr, $livePay['rechargeInfo']);

    $qr = (string)(
        $rechargeArr['QRCodeURL']
        ?? $rechargeArr['qrCodeUrl']
        ?? $rechargeArr['qrCode']
        ?? $rechargeArr['qrcode']
        ?? $customerArr['QRCodeURL']
        ?? $customerArr['qrCodeUrl']
        ?? $customerArr['qrCode']
        ?? $customerArr['qrcode']
        ?? ''
    );

    $payUrl = (string)(
        $rechargeArr['payUrl']
        ?? $customerArr['payUrl']
        ?? ''
    );

    $methodName = (string)(
        $r['channel_name']
        ?: ($customerArr['channelName'] ?? '')
        ?: ($rechargeArr['channelName'] ?? '')
        ?: $r['recharge_type']
        ?: 'UPI-QR'
    );

    $rechargeType = (string)(
        $r['recharge_type']
        ?: ($customerArr['rechargeType'] ?? '')
        ?: ($rechargeArr['rechargeType'] ?? '')
        ?: 'UPI'
    );

    $statusName = ($status === 'Payed')
        ? 'Fulfilled'
        : (($status === 'PendingReview')
            ? 'Under review'
            : (($status === 'Cancel') ? 'Failed' : 'To be paid'));

    $canSubmit = in_array($status, ['Wait'], true);

    return [
        'id' => (int)$r['id'],
        'orderNo' => $orderNo,
        'merchantOrderNo' => $orderNo,
        'rechargeNumber' => $orderNo,

        'amount' => $amount,
        'rechargeAmount' => $amount,
        'giftAmount' => (float)($r['gift_amount'] ?? 0),
        'payAmount' => (float)($r['pay_amount'] ?? $amount),

        'rechargeCategoryId' => $categoryId,
        'rechargeChannelId' => 26001,
        'rechargeChannelName' => $methodName,
        'rechargeType' => $rechargeType,
        'rechargeCategoryName' => $methodName,

        'typeName' => $methodName,
        'method' => $methodName,
        'methodName' => $methodName,
        'payName' => $methodName,
        'payTypeName' => $methodName,
        'paymentName' => $methodName,

        'arOrderStatus' => ($status === 'Wait' ? 2048 : 0),
        'payID' => 0,

        'statusName' => $statusName,
        'rechargeState' => $status,
        'state' => recharge_state_to_num($status),
        'recordState' => recharge_state_to_num($status),

        'createTime' => $created,
        'createdTime' => $created,
        'expiredTime' => $created + 15 * 60 * 1000,

        'payUrl' => $payUrl,
        'submitUrl' => '/arUpiV2?token=' . rawurlencode($orderNo) . '&lang=en',
        'submitType' => 1,
        'orderResult' => 1,

        'customerInfo' => $customerArr,
        'rechargeInfo' => $rechargeArr,

        'upi' => (string)($customerArr['upi'] ?? ''),
        'upiId' => (string)($customerArr['upiId'] ?? ($customerArr['upi'] ?? '')),
        'upiName' => (string)($customerArr['upiName'] ?? ''),

        // Original frontend alag-alag QR key read kar sakta hai, isliye sab me same QR diya.
        'qrCode' => $qr,
        'qrcode' => $qr,
        'qrCodeUrl' => $qr,
        'QRCode' => $qr,
        'QRCodeURL' => $qr,
        'imageUrl' => $qr,
        'paymentCodeUrl' => $qr,
        'bankQRCode' => $qr,
        'bankQRUrl' => $qr,

        'token' => $orderNo,
        'utr' => (string)($r['utr'] ?? ''),
        'utrNo' => (string)($r['utr'] ?? ''),

        'isShowUtr' => $canSubmit,
        'canSubmitUtr' => $canSubmit,
        'canSubmitCertificate' => $canSubmit,
        'buttonText' => $canSubmit ? 'Submit Utr' : ''
    ];
}


function payment_info_for_category(int $categoryId, float $amount, string $orderNo): array
{
    $upi = 'demo@upi';
    $upiName = '13L GAME';
    $qr = '';
    $note = 'Pay exact amount and submit UTR.';
    $icon = '';
    $selectedIcon = '';
    $channelName = 'UPI-QR';
    $rechargeType = 'UPI';

    $conn = db();
    if ($conn) {
        $stmt = @$conn->prepare('SELECT name, recharge_type, upi_id, upi_name, qr_image, icon_url, selected_icon_url, note FROM payment_methods WHERE id=? LIMIT 1');
        if ($stmt) {
            $stmt->bind_param('i', $categoryId);
            $stmt->execute();
            $r = $stmt->get_result()->fetch_assoc();

            if ($r) {
                $channelName = trim((string)($r['name'] ?? '')) ?: $channelName;
                $rechargeType = trim((string)($r['recharge_type'] ?? '')) ?: $rechargeType;
                $upi = trim((string)($r['upi_id'] ?? '')) ?: $upi;
                $upiName = trim((string)($r['upi_name'] ?? '')) ?: $upiName;
                $qr = trim((string)($r['qr_image'] ?? ''));
                $icon = trim((string)($r['icon_url'] ?? ''));
                $selectedIcon = trim((string)($r['selected_icon_url'] ?? ''));
                $note = trim((string)($r['note'] ?? '')) ?: $note;
            }
        }
    }

    $upiLink = 'upi://pay?pa=' . rawurlencode($upi)
        . '&pn=' . rawurlencode($upiName)
        . '&am=' . rawurlencode(number_format($amount, 2, '.', ''))
        . '&cu=INR'
        . '&tn=' . rawurlencode($orderNo);

    // Admin ne QR set kiya hai to wahi dikhana hai.
    // Agar QR blank/galat/icon path hai to UPI link ka real QR auto generate hoga.
    if (v23_is_bad_qr($qr, $icon, $selectedIcon)) {
        $qr = v23_qr_from_upi_link($upiLink);
    } else {
        $qr = v23_clean_asset_url($qr);
    }

    $info = [
        'upi' => $upi,
        'upiId' => $upi,
        'upiName' => $upiName,
        'name' => $upiName,
        'holderName' => $upiName,
        'HolderName' => $upiName,
        'accountNo' => $upi,
        'AccountNo' => $upi,
        'qrCode' => $qr,
        'qrcode' => $qr,
        'qrCodeUrl' => $qr,
        'QRCode' => $qr,
        'QRCodeURL' => $qr,
        'imageUrl' => $qr,
        'paymentCodeUrl' => $qr,
        'bankQRCode' => $qr,
        'bankQRUrl' => $qr,
        'payUrl' => $upiLink,
        'upiLink' => $upiLink,
        'note' => $note,
        'channelName' => $channelName,
        'rechargeType' => $rechargeType,
        'orderNo' => $orderNo,
        'amount' => $amount
    ];

    return [
        'customerInfo' => $info,
        'rechargeInfo' => $info
    ];
}

function handle_recharge_record(array $d): void
{
    $u = current_user() ?: demo_user();
    $conn = db();
    $pageNo = max(1, (int)($d['pageNo'] ?? 1));
    $pageSize = max(1, min(50, (int)($d['pageSize'] ?? 10)));
    $off = ($pageNo - 1) * $pageSize;
    $list = []; $total = 0;
    if ($conn) {
        $uid = (int)$u['id'];
        $stmt = $conn->prepare('SELECT COUNT(*) c FROM recharge_orders WHERE user_id=?');
        $stmt->bind_param('i', $uid); $stmt->execute(); $total = (int)$stmt->get_result()->fetch_assoc()['c'];
        $stmt = $conn->prepare('SELECT * FROM recharge_orders WHERE user_id=? ORDER BY id DESC LIMIT ? OFFSET ?');
        $stmt->bind_param('iii', $uid, $pageSize, $off); $stmt->execute();
        $rs = $stmt->get_result();
        while ($r = $rs->fetch_assoc()) $list[] = recharge_order_row($r);
    }
    api_success(['list'=>$list, 'pageNo'=>$pageNo, 'totalPage'=>(int)ceil(($total ?: 0)/$pageSize), 'totalCount'=>$total]);
}

function handle_recharge_deposit(array $d): void
{
    $u = current_user() ?: demo_user();
    $conn = db();
    $categoryId = (int)first_value($d, ['rechargeCategoryId','rechargeChannelId','id'], 400088);
    $cat = recharge_category_by_id($categoryId) ?: recharge_categories_data()[0];
    $amount = (float)first_value($d, ['amount','rechargeAmount','money'], 0);
    if ($amount <= 0 && isset($d['rechargeGoodsId'])) $amount = (float)($cat['quickConfigList'][0]['rechargeAmount'] ?? 100);
    if ($amount <= 0) api_error('Invalid amount', 401, 401);
    $min = (float)($cat['minAmount'] ?? 1); $max = (float)($cat['maxAmount'] ?? 100000);
    if ($amount < $min) api_error('Minimum amount is ' . $min, 401, 401);
    if ($amount > $max) api_error('Maximum amount is ' . $max, 401, 401);
    $gift = 0.0;
    foreach (($cat['quickConfigList'] ?? []) as $q) {
        if ((float)$q['rechargeAmount'] === $amount) { $gift = (float)($q['giftAmount'] ?? 0); break; }
    }
    $orderNo = 'RC' . date('ymdHis') . random_int(1000, 9999);
    $createTime = now_ms();
    if ($conn) {
        $uid = (int)$u['id'];
        $raw = json_encode($d, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $name = (string)($cat['name'] ?? 'UPI-QR');
        $type = (string)($cat['rechargeType'] ?? 'UPI');
        $pay = payment_info_for_category($categoryId, $amount, $orderNo);
        $customerInfo = json_encode($pay['customerInfo'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        $rechargeInfo = json_encode($pay['rechargeInfo'], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
        $stmt = $conn->prepare('INSERT INTO recharge_orders(user_id, order_no, recharge_category_id, channel_name, recharge_type, amount, gift_amount, pay_amount, status, raw_data, customer_info, recharge_info, created_at) VALUES(?,?,?,?,?,?,?,? ,"Wait",?,?,?,NOW())');
        if ($stmt) { $stmt->bind_param('isissdddsss', $uid, $orderNo, $categoryId, $name, $type, $amount, $gift, $amount, $raw, $customerInfo, $rechargeInfo); $stmt->execute(); }
    }
    api_success([
        'orderNo' => $orderNo,
        'merchantOrderNo' => $orderNo,
        'createTime' => $createTime,
        'amount' => $amount,
        'rechargeAmount' => $amount,
        'giftAmount' => $gift,
        'rechargeChannelId' => 26001,
        'rechargeChannelName' => (string)($cat['name'] ?? 'UPI-QR'),
        // The frontend arUpiV2 route reads token/lang from submitUrl and stores it in localStorage.
        'submitUrl' => '/arUpiV2?token=' . rawurlencode($orderNo) . '&lang=en',
        'payUrl' => '/arUpiV2?token=' . rawurlencode($orderNo) . '&lang=en',
        'orderDetailUrl' => '/wallet/rechargeDetail/' . rawurlencode($orderNo) . '/' . $createTime,
        'submitType' => 1,
        'orderResult' => 1,
        'scanCodePay' => null,
        'redirectUrl' => '',
    ]);
}

function handle_recharge_order_detail(array $d): void
{
    $u = current_user() ?: demo_user();
    $conn = db();
    $orderNo = first_value($d, ['orderNo','rechargeNumber','merchantOrderNo','token','payOrderNo'], $_GET['token'] ?? '');
    if ($conn) {
        $uid = (int)$u['id'];
        if ($orderNo !== '') {
            $stmt = $conn->prepare('SELECT * FROM recharge_orders WHERE user_id=? AND order_no=? LIMIT 1');
            $stmt->bind_param('is', $uid, $orderNo); $stmt->execute();
            $r = $stmt->get_result()->fetch_assoc();
            if ($r) api_success(recharge_order_row($r));
        }
        // arUpiV2 kabhi-kabhi payload me orderNo nahi bhejta; latest pending user order return karo.
        $stmt = $conn->prepare("SELECT * FROM recharge_orders WHERE user_id=? AND status IN('Wait','PendingReview') ORDER BY id DESC LIMIT 1");
        if ($stmt) { $stmt->bind_param('i', $uid); $stmt->execute(); $r=$stmt->get_result()->fetch_assoc(); if($r) api_success(recharge_order_row($r)); }
    }
    api_success(null);
}

function handle_recharge_submit_certificate(array $d): void
{
    $u = current_user() ?: demo_user();
    $conn = db();
    $orderNo = first_value($d, ['orderNo','rechargeNumber','merchantOrderNo','token','payOrderNo'], $_GET['token'] ?? '');
    $utr = trim((string)first_value($d, ['utr','utrNo','UTR','utrNumber','referenceNo','refNo','transactionId','transactionNo'], ''));
    $note = trim((string)first_value($d, ['remark','note','userSubmitNote'], ''));
    $proof = trim((string)first_value($d, ['image','imageUrl','paymentProof','certificateUrl'], ''));
    if (!$conn) api_success(true);
    $uid = (int)$u['id'];
    if ($orderNo === '') {
        $stmt = $conn->prepare("SELECT order_no FROM recharge_orders WHERE user_id=? AND status='Wait' ORDER BY id DESC LIMIT 1");
        if ($stmt) { $stmt->bind_param('i',$uid); $stmt->execute(); $row=$stmt->get_result()->fetch_assoc(); $orderNo=(string)($row['order_no'] ?? ''); }
    }
    if ($orderNo === '') api_error('Recharge order not found', 404, 404);
    if ($utr === '') api_error('Please enter UTR / Reference number', 400, 400);
    $raw = json_encode($d, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $stmt = $conn->prepare('UPDATE recharge_orders SET status="PendingReview", utr=?, user_submit_note=?, payment_proof=?, utr_submit_at=NOW(), updated_at=NOW(), raw_data=? WHERE user_id=? AND order_no=? AND status<>"Payed"');
    if ($stmt) { $stmt->bind_param('ssssis', $utr, $note, $proof, $raw, $uid, $orderNo); $stmt->execute(); }
    $stmt = $conn->prepare('SELECT * FROM recharge_orders WHERE user_id=? AND order_no=? LIMIT 1');
    if($stmt){$stmt->bind_param('is',$uid,$orderNo);$stmt->execute();$r=$stmt->get_result()->fetch_assoc(); if($r) api_success(recharge_order_row($r));}
    api_success(['orderNo'=>$orderNo,'utr'=>$utr,'status'=>'PendingReview']);
}

function handle_recharge_cancel(array $d): void
{
    $u = current_user() ?: demo_user();
    $conn = db();
    $orderNo = first_value($d, ['orderNo','rechargeNumber','merchantOrderNo'], '');
    if ($conn && $orderNo !== '') {
        $uid = (int)$u['id'];
        $stmt = $conn->prepare('UPDATE recharge_orders SET status="Cancel", updated_at=NOW() WHERE user_id=? AND order_no=? AND status<>"Payed"');
        $stmt->bind_param('is', $uid, $orderNo); $stmt->execute();
    }
    api_success(true);
}

function handle_sub_game_list(array $d): void
{
    $json=static_json('Home/GetHomeAllGameList'); $list=[]; $category=first_value($d,['categoryCode','vendorCode','gameName'],'');
    foreach (($json['data']['games']??[]) as $cat) {
        foreach ($cat['gameList'] as $g) {
            if (!$category || stripos($g['vendorCode']??'', $category)!==false || stripos($g['name']??'', $category)!==false || ($cat['categoryCode']??'')===$category) $list[]=$g;
        }
    }
    api_success(['list'=>array_slice($list,0,50),'pageNo'=>1,'totalPage'=>1,'totalCount'=>count($list)]);
}
function handle_hot_games(): void { $j=static_json('Home/GetHomeAllGameList'); api_success($j['data']['hotGames']??[]); }
function handle_game_by_name(array $d): void
{
    $name=strtolower(first_value($d,['name','gameName','keyword'],'')); $j=static_json('Home/GetHomeAllGameList'); $list=[];
    foreach(($j['data']['games']??[]) as $cat) foreach($cat['gameList'] as $g) if(!$name || str_contains(strtolower($g['name']??''),$name)) $list[]=$g;
    api_success(['list'=>array_slice($list,0,50),'pageNo'=>1,'totalPage'=>1,'totalCount'=>count($list)]);
}
function handle_game_url(array $d): void
{
    $gameCode   = first_value($d, ['gameCode'], 'demo');
    $vendorCode = strtoupper(first_value($d, ['vendorCode'], ''));
    $origin     = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
    $u          = current_user() ?: demo_user();
    $token      = make_token($u);

    // ── LOCAL GAMES (self-hosted HTML games) ─────────────────────
    $localGames = [
        'aviator' => '/av/index.html',
        '800'     => '/av/index.html',     // TB_Chess Aviator
        '100'     => '/game100/mine.html', // TB_Chess Mines
        '121'     => '/game121/index.html',// TB_Chess Chicken Road
    ];

    if (isset($localGames[$gameCode])) {
        $gameUrl = $origin . $localGames[$gameCode];
        api_success([
            'url'          => $gameUrl,
            'gameUrl'      => $gameUrl,
            'returnUrl'    => $origin . '/',
            'returnType'   => 1,
            'isOpenWindow' => true,
        ]);
    }

    if (in_array($vendorCode, ['SPRIBE','AVIATOR'], true)) {
        $gameUrl = $origin . '/av/index.html';
        api_success([
            'url'          => $gameUrl,
            'gameUrl'      => $gameUrl,
            'returnUrl'    => $origin . '/',
            'returnType'   => 1,
            'isOpenWindow' => true,
        ]);
    }

    if ($vendorCode === 'TB_CHESS') {
        $gameUrl = $origin . '/av/index.html';
        api_success([
            'url'          => $gameUrl,
            'gameUrl'      => $gameUrl,
            'returnUrl'    => $origin . '/',
            'returnType'   => 1,
            'isOpenWindow' => true,
        ]);
    }

    // If Unified API is enabled, route external games to the new Unified Gaming API
    if (defined('UNIFIED_API_ENABLED') && UNIFIED_API_ENABLED) {
        if ($vendorCode !== 'ARLOTTERY' && !preg_match('/^(WinGo|TrxWinGo|K3|5D|D5|MotoRace)_/i', $gameCode)) {
            $gameUrl = unified_get_game_url($vendorCode, $gameCode, $u, $origin);
            api_success([
                'url'          => $gameUrl,
                'gameUrl'      => $gameUrl,
                'returnUrl'    => $origin . '/',
                'returnType'   => 1,
                'isOpenWindow' => true,
            ]);
        }
    }

    // ── ARLottery / WinGo / K3 / 5D / TrxWinGo → Internal SPA route ──────────

    if ($vendorCode === 'ARLOTTERY' || preg_match('/^(WinGo|TrxWinGo|K3|5D|D5|MotoRace)_/i', $gameCode)) {
        $route  = lottery_route_from_game_code($gameCode);
        $target = $origin . '/' . $route . '/' . rawurlencode($gameCode) . '?Token=' . rawurlencode($token);
        api_success([
            'url'          => $target,
            'gameUrl'      => $target,
            'returnUrl'    => $origin . '/',
            'returnType'   => 0,
            'isOpenWindow' => false,
            'token'        => $token,
        ]);
    }

    // ── JDB Games (Slots, Fishing, Arcade) ────────────────────────────────────
    if (in_array($vendorCode, ['JDB','JDB711'], true)) {
        if (defined('JDB_ENABLED') && JDB_ENABLED) {
            $gameUrl = jdb_get_game_url($gameCode, $u, $origin);
        } else {
            $gameUrl = $origin . '/game-demo.html?gameCode=' . rawurlencode($gameCode) . '&vendor=JDB';
        }
        api_success([
            'url'          => $gameUrl,
            'gameUrl'      => $gameUrl,
            'returnUrl'    => $origin . '/',
            'returnType'   => 1,
            'isOpenWindow' => true,
        ]);
    }

    // ── JILI Games (JILI Slots, Table) ────────────────────────────────────────
    if (in_array($vendorCode, ['JILI','JILISPORTS'], true)) {
        if (defined('JILI_ENABLED') && JILI_ENABLED) {
            $gameUrl = jili_get_game_url($gameCode, $u, $origin);
        } else {
            $gameUrl = $origin . '/game-demo.html?gameCode=' . rawurlencode($gameCode) . '&vendor=JILI';
        }
        api_success([
            'url'          => $gameUrl,
            'gameUrl'      => $gameUrl,
            'returnUrl'    => $origin . '/',
            'returnType'   => 1,
            'isOpenWindow' => true,
        ]);
    }

    // ── Spribe / Aviator ──────────────────────────────────────────────────────
    if (in_array($vendorCode, ['SPRIBE','AVIATOR'], true) || strtolower($gameCode) === 'aviator') {
        if (defined('SPRIBE_ENABLED') && SPRIBE_ENABLED) {
            $gameUrl = spribe_get_game_url($gameCode, $u, $origin);
        } else {
            $gameUrl = $origin . '/game-demo.html?gameCode=aviator&vendor=Spribe';
        }
        api_success([
            'url'          => $gameUrl,
            'gameUrl'      => $gameUrl,
            'returnUrl'    => $origin . '/',
            'returnType'   => 1,
            'isOpenWindow' => true,
        ]);
    }

    // ── Default fallback demo ─────────────────────────────────────────────────
    api_success([
        'url'          => $origin . '/game-demo.html?gameCode=' . rawurlencode($gameCode),
        'gameUrl'      => $origin . '/game-demo.html?gameCode=' . rawurlencode($gameCode),
        'returnUrl'    => $origin . '/',
        'returnType'   => 0,
        'isOpenWindow' => false,
    ]);
}

function lottery_route_from_game_code(string $gameCode): string
{
    if (stripos($gameCode, 'K3') === 0) return 'K3';
    if (stripos($gameCode, '5D') === 0 || stripos($gameCode, 'D5') === 0) return 'D5';
    if (stripos($gameCode, 'MotoRace') === 0) return 'MotoRace';
    if (stripos($gameCode, 'TrxWinGo') === 0) return 'TrxWinGo';
    return 'WinGo';
}

function handle_ar_balance(): void { $u=current_user() ?: demo_user(); le_settle_pending_bets('', '', (int)$u['id']); $u=current_user() ?: $u; api_success(['balance'=>(float)$u['balance'], 'currency'=>APP_CURRENCY]); }
function handle_ar_wallets(): void { $u=current_user() ?: demo_user(); le_settle_pending_bets('', '', (int)$u['id']); $u=current_user() ?: $u; api_success([['vendorCode'=>'ARGame','balance'=>0.0,'currency'=>APP_CURRENCY,'tenantId'=>APP_TENANT_ID,'userId'=>(int)$u['tenant_user_id']],['vendorCode'=>'PlatForm','balance'=>(float)$u['balance'],'currency'=>APP_CURRENCY,'tenantId'=>APP_TENANT_ID,'userId'=>(int)$u['tenant_user_id']]]); }

function handle_coupon_list(): void
{
    $conn=db(); $list=[];
    if($conn){$rs=@$conn->query('SELECT id,code,amount,max_claim,claimed_count,expires_at,status FROM gift_codes WHERE status=1 ORDER BY id DESC LIMIT 20'); if($rs) while($r=$rs->fetch_assoc()) $list[]=['id'=>(int)$r['id'],'couponCode'=>$r['code'],'code'=>$r['code'],'amount'=>(float)$r['amount'],'couponAmount'=>(float)$r['amount'],'state'=>1,'expireTime'=>$r['expires_at']?strtotime($r['expires_at'])*1000:0];}
    api_success(['list'=>$list,'pageNo'=>1,'totalPage'=>1,'totalCount'=>count($list)]);
}
function handle_coupon_detail(array $d): void
{
    $code=first_value($d,['code','couponCode','giftCode'],''); $conn=db();
    if($conn && $code){$stmt=$conn->prepare('SELECT * FROM gift_codes WHERE code=? LIMIT 1');$stmt->bind_param('s',$code);$stmt->execute();$r=$stmt->get_result()->fetch_assoc(); if($r) api_success(['id'=>(int)$r['id'],'code'=>$r['code'],'amount'=>(float)$r['amount'],'state'=>(int)$r['status']]);}
    api_success(null);
}
function handle_use_gift_code(array $d): void
{
    $set = site_settings();
    if (empty($set['gift_enabled'])) api_error('Gift code is closed', 1, 1);
    $u=current_user() ?: demo_user(); $uid=(int)$u['id']; $code=strtoupper(trim(first_value($d,['code','couponCode','giftCode','redeemCode'],''))); $conn=db();
    if(!$conn || !$code) api_error('Invalid gift code', 1, 1);
    $stmt=$conn->prepare('SELECT * FROM gift_codes WHERE code=? AND status=1 LIMIT 1'); $stmt->bind_param('s',$code);$stmt->execute();$g=$stmt->get_result()->fetch_assoc();
    if(!$g) api_error('Gift code not found', 1, 1);
    if($g['expires_at'] && strtotime($g['expires_at']) < time()) api_error('Gift code expired', 1, 1);
    if((int)$g['claimed_count'] >= (int)$g['max_claim']) api_error('Gift code used up', 1, 1);
    $minRecharge = (float)($g['min_recharge'] ?? 0);
    if($minRecharge > 0){$stmt=$conn->prepare("SELECT COALESCE(SUM(amount+gift_amount),0) s FROM recharge_orders WHERE user_id=? AND status='Payed'");$stmt->bind_param('i',$uid);$stmt->execute();$dep=(float)$stmt->get_result()->fetch_assoc()['s']; if($dep < $minRecharge) api_error('Minimum recharge required '.$minRecharge,1,1);}
    $gid=(int)$g['id']; $amount=(float)$g['amount'];
    $stmt=$conn->prepare('INSERT IGNORE INTO gift_code_claims(gift_code_id,user_id,amount,created_at) VALUES(?,?,?,NOW())'); $stmt->bind_param('iid',$gid,$uid,$amount); $stmt->execute();
    if($stmt->affected_rows<=0) api_error('You already used this gift code', 1, 1);
    $stmt=$conn->prepare('UPDATE gift_codes SET claimed_count=claimed_count+1 WHERE id=?');$stmt->bind_param('i',$gid);$stmt->execute();
    credit_user_reward($uid,$amount,'gift_code_'.$code);
    api_success(['amount'=>$amount,'code'=>$code,'couponAmount'=>$amount]);
}

function vip_progress(int $uid): array
{
    $conn = db();
    $deposit = 0.0; $withdraw = 0.0; $bet = 0.0; $weekBet = 0.0; $monthBet = 0.0; $weekDeposit = 0.0; $monthDeposit = 0.0;
    if ($conn) {
        $stmt = @$conn->prepare("SELECT COALESCE(SUM(amount+gift_amount),0) s FROM recharge_orders WHERE user_id=? AND status='Payed'");
        if ($stmt) { $stmt->bind_param('i',$uid); $stmt->execute(); $deposit=(float)$stmt->get_result()->fetch_assoc()['s']; }
        $stmt = @$conn->prepare("SELECT COALESCE(SUM(amount),0) s FROM withdraw_requests WHERE user_id=? AND status='approved'");
        if ($stmt) { $stmt->bind_param('i',$uid); $stmt->execute(); $withdraw=(float)$stmt->get_result()->fetch_assoc()['s']; }
        $stmt = @$conn->prepare("SELECT COALESCE(SUM(real_amount),0) s FROM lottery_bets WHERE user_id=?");
        if ($stmt) { $stmt->bind_param('i',$uid); $stmt->execute(); $bet=(float)$stmt->get_result()->fetch_assoc()['s']; }
        $stmt = @$conn->prepare("SELECT COALESCE(SUM(real_amount),0) s FROM lottery_bets WHERE user_id=? AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
        if ($stmt) { $stmt->bind_param('i',$uid); $stmt->execute(); $weekBet=(float)$stmt->get_result()->fetch_assoc()['s']; }
        $stmt = @$conn->prepare("SELECT COALESCE(SUM(real_amount),0) s FROM lottery_bets WHERE user_id=? AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
        if ($stmt) { $stmt->bind_param('i',$uid); $stmt->execute(); $monthBet=(float)$stmt->get_result()->fetch_assoc()['s']; }
        $stmt = @$conn->prepare("SELECT COALESCE(SUM(amount+gift_amount),0) s FROM recharge_orders WHERE user_id=? AND status='Payed' AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
        if ($stmt) { $stmt->bind_param('i',$uid); $stmt->execute(); $weekDeposit=(float)$stmt->get_result()->fetch_assoc()['s']; }
        $stmt = @$conn->prepare("SELECT COALESCE(SUM(amount+gift_amount),0) s FROM recharge_orders WHERE user_id=? AND status='Payed' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
        if ($stmt) { $stmt->bind_param('i',$uid); $stmt->execute(); $monthDeposit=(float)$stmt->get_result()->fetch_assoc()['s']; }
        @$conn->query("UPDATE users SET total_deposit=$deposit,total_withdraw=$withdraw,total_bet=$bet WHERE id=$uid");
    }
    return compact('deposit','withdraw','bet','weekBet','monthBet','weekDeposit','monthDeposit');
}

function vip_level_for_progress(float $deposit, float $bet): int
{
    $conn = db(); $level = 0;
    if ($conn) {
        $rs = @$conn->query('SELECT level, deposit_required, bet_required FROM vip_levels WHERE status=1 ORDER BY level ASC');
        if ($rs) while ($r=$rs->fetch_assoc()) {
            if ($deposit >= (float)$r['deposit_required'] && $bet >= (float)$r['bet_required']) $level = (int)$r['level'];
        }
    }
    return $level;
}

function handle_vip_info(): void
{
    $u = current_user() ?: demo_user(); $uid=(int)$u['id']; $p = vip_progress($uid); $level=vip_level_for_progress($p['deposit'],$p['bet']);
    $conn=db(); if($conn) @$conn->query('UPDATE users SET vip_level='.(int)$level.' WHERE id='.(int)$uid);
    $nextDeposit = 0.0; $nextBet = 0.0; $received=[];
    if($conn){
        $stmt=@$conn->prepare('SELECT deposit_required, bet_required FROM vip_levels WHERE level>? AND status=1 ORDER BY level ASC LIMIT 1');
        if($stmt){$stmt->bind_param('i',$level);$stmt->execute();$r=$stmt->get_result()->fetch_assoc(); if($r){$nextDeposit=max(0,(float)$r['deposit_required']-$p['deposit']);$nextBet=max(0,(float)$r['bet_required']-$p['bet']);}}
        $stmt=@$conn->prepare('SELECT level FROM vip_rewards WHERE user_id=? AND reward_type="level"');
        if($stmt){$stmt->bind_param('i',$uid);$stmt->execute();$rs=$stmt->get_result(); while($r=$rs->fetch_assoc()) $received[]=(string)$r['level'];}
    }
    api_success([
        'userId'=>(int)($u['tenant_user_id'] ?? $uid),'vipLevel'=>$level,'daysLeft'=>30,
        'upLevelBetAmount'=>$nextBet,'upLevelRechargeAmount'=>$nextDeposit,
        'weekBetAmount'=>$p['weekBet'],'weekRechargeAmount'=>$p['weekDeposit'],'weekRewardState'=>true,
        'monthBetAmount'=>$p['monthBet'],'monthRechargeAmount'=>$p['monthDeposit'],'monthRewardState'=>true,
        'receivedLevels'=>implode(',', $received),'vipAmountOfCode'=>1,
        'totalRechargeAmount'=>$p['deposit'],'totalBetAmount'=>$p['bet']
    ]);
}

function handle_vip_config(): void
{
    $conn=db(); $list=[];
    if($conn){ $rs=@$conn->query('SELECT * FROM vip_levels WHERE status=1 ORDER BY level ASC'); if($rs) while($r=$rs->fetch_assoc()){
        $list[]=[
            'level'=>(int)$r['level'], 'vipLevel'=>(int)$r['level'], 'levelName'=>$r['name'], 'name'=>$r['name'],
            'depositAmount'=>(float)$r['deposit_required'], 'betAmount'=>(float)$r['bet_required'],
            'upLevelRechargeAmount'=>(float)$r['deposit_required'], 'upLevelBetAmount'=>(float)$r['bet_required'],
            'levelReward'=>(float)$r['level_reward'], 'weekReward'=>(float)$r['weekly_reward'], 'monthReward'=>(float)$r['monthly_reward'],
            'weeklyReward'=>(float)$r['weekly_reward'], 'monthlyReward'=>(float)$r['monthly_reward'],
            'icon'=>$r['icon_url'], 'iconUrl'=>$r['icon_url'], 'status'=>1
        ];
    }}
    api_success($list);
}

function handle_vip_reward_list(array $d): void
{
    $u=current_user() ?: demo_user(); $uid=(int)$u['id']; $conn=db(); $list=[]; $total=0;
    if($conn){
        $stmt=@$conn->prepare('SELECT COUNT(*) c FROM vip_rewards WHERE user_id=?'); if($stmt){$stmt->bind_param('i',$uid);$stmt->execute();$total=(int)$stmt->get_result()->fetch_assoc()['c'];}
        $stmt=@$conn->prepare('SELECT * FROM vip_rewards WHERE user_id=? ORDER BY id DESC LIMIT 50'); if($stmt){$stmt->bind_param('i',$uid);$stmt->execute();$rs=$stmt->get_result(); while($r=$rs->fetch_assoc()) $list[]=['id'=>(int)$r['id'],'rewardType'=>$r['reward_type'],'rewardLevel'=>(int)$r['level'],'amount'=>(float)$r['amount'],'state'=>1,'createTime'=>strtotime($r['created_at'])*1000];}
    }
    api_success(['list'=>$list,'pageNo'=>1,'totalPage'=>1,'totalCount'=>$total]);
}

function handle_vip_pick_reward(array $d): void
{
    $u=current_user() ?: demo_user(); $uid=(int)$u['id']; $level=(int)first_value($d,['rewardLevel','level'],0); $type=first_value($d,['rewardType','type'],'level');
    $conn=db(); $amount=0.0;
    if($conn){
        $col = $type==='week' || $type==='weekly' ? 'weekly_reward' : (($type==='month' || $type==='monthly') ? 'monthly_reward' : 'level_reward');
        $stmt=@$conn->prepare("SELECT $col amount FROM vip_levels WHERE level=? LIMIT 1"); if($stmt){$stmt->bind_param('i',$level);$stmt->execute();$r=$stmt->get_result()->fetch_assoc();$amount=(float)($r['amount']??0);}
        if($amount>0){
            $stmt=@$conn->prepare('INSERT IGNORE INTO vip_rewards(user_id,level,reward_type,amount,status,created_at) VALUES(?,?,?,?,"Received",NOW())'); if($stmt){$stmt->bind_param('iisd',$uid,$level,$type,$amount);$stmt->execute(); if($stmt->affected_rows>0){$stmt2=$conn->prepare('UPDATE users SET balance=balance+? WHERE id=?');$stmt2->bind_param('di',$amount,$uid);$stmt2->execute();}}
        }
    }
    api_success(['amount'=>$amount,'balance'=>(float)((current_user() ?: $u)['balance'] ?? 0)]);
}



function credit_user_reward(int $uid, float $amount, string $remark): void
{
    if ($amount <= 0) return;
    $conn=db(); if(!$conn) return;
    $stmt=$conn->prepare('UPDATE users SET balance=balance+? WHERE id=?'); if($stmt){$stmt->bind_param('di',$amount,$uid);$stmt->execute();}
    $no='RW'.date('ymdHis').random_int(100,999);
    $stmt=$conn->prepare('INSERT INTO financial_records(user_id,record_no,order_no,vendor_code,type,sub_type,amount,back_amount,remark,created_at) VALUES(?,?,?,?,"Reward",?, ?,0,?,NOW())');
    if($stmt){$vendor='Activity';$sub=$remark;$stmt->bind_param('issssds',$uid,$no,$no,$vendor,$sub,$amount,$remark);$stmt->execute();}
}

function activity_progress_for_user(int $uid, string $taskType): float
{
    $conn=db(); if(!$conn) return 0;
    if($taskType==='telegram') return 0;
    if($taskType==='withdraw') { $stmt=@$conn->prepare("SELECT COUNT(*) c FROM withdraw_requests WHERE user_id=? AND status='approved'"); }
    elseif($taskType==='deposit_count') { $stmt=@$conn->prepare("SELECT COUNT(*) c FROM recharge_orders WHERE user_id=? AND status='Payed'"); }
    elseif($taskType==='deposit') { $stmt=@$conn->prepare("SELECT COUNT(*) c FROM recharge_orders WHERE user_id=? AND status='Payed'"); }
    else { return 0; }
    if($stmt){$stmt->bind_param('i',$uid);$stmt->execute();$r=$stmt->get_result()->fetch_assoc();return (float)($r['c']??0);} return 0;
}

function handle_lucky_double_tasks(): void
{
    $u=current_user() ?: demo_user(); $uid=(int)$u['id']; $conn=db(); $tasks=[]; $total=0; $done=0; $rewardTotal=0.0;
    if($conn){
        $rs=@$conn->query('SELECT * FROM activity_tasks WHERE status=1 ORDER BY sort DESC,id ASC');
        if($rs) while($t=$rs->fetch_assoc()){
            $code=$t['code']; $progress=activity_progress_for_user($uid,$t['task_type']);
            $stmt=@$conn->prepare('SELECT completed,claimed,progress FROM user_activity_tasks WHERE user_id=? AND task_code=? LIMIT 1');
            $completed=0; $claimed=0; $savedProgress=0;
            if($stmt){$stmt->bind_param('is',$uid,$code);$stmt->execute();$r=$stmt->get_result()->fetch_assoc(); if($r){$completed=(int)$r['completed'];$claimed=(int)$r['claimed'];$savedProgress=(float)$r['progress'];}}
            $progress=max($progress,$savedProgress); if($progress >= (float)$t['target_value']) $completed=1;
            $stmt=@$conn->prepare('INSERT INTO user_activity_tasks(user_id,task_code,progress,completed,claimed,created_at,updated_at) VALUES(?,?,?,?,0,NOW(),NOW()) ON DUPLICATE KEY UPDATE progress=GREATEST(progress,VALUES(progress)), completed=GREATEST(completed,VALUES(completed)), updated_at=NOW()');
            if($stmt){$stmt->bind_param('isdi',$uid,$code,$progress,$completed);$stmt->execute();}
            $total++; if($completed) $done++; $rewardTotal+=(float)$t['reward'];
            $tasks[]=[
                'id'=>(int)$t['id'],'taskId'=>(int)$t['id'],'taskCode'=>$code,'taskName'=>$t['title'],'title'=>$t['title'],
                'rewardAmount'=>(float)$t['reward'],'amount'=>(float)$t['reward'],'target'=>(float)$t['target_value'],'progress'=>$progress,
                'state'=>$claimed?3:($completed?1:0),'completed'=>(bool)$completed,'claimed'=>(bool)$claimed,
                'jumpUrl'=>$t['jump_url'],'taskType'=>$t['task_type']
            ];
        }
    }
    api_success(['rewardAmount'=>$rewardTotal,'totalRewardAmount'=>$rewardTotal,'totalMultiplier'=>1,'taskCompleted'=>$done,'taskTotal'=>$total,'taskList'=>$tasks,'list'=>$tasks,'progress'=>['completed'=>$done,'total'=>$total]]);
}

function handle_lucky_double_recharge_configs(): void
{
    api_success([['amount'=>100,'rewardAmount'=>1.29],['amount'=>500,'rewardAmount'=>10],['amount'=>1000,'rewardAmount'=>30]]);
}

function handle_activity_complete(array $d, string $code=''): void
{
    $u=current_user() ?: demo_user(); $uid=(int)$u['id']; $conn=db(); $code=$code ?: first_value($d,['taskCode','code'],'join_telegram');
    if($conn){$stmt=$conn->prepare('INSERT INTO user_activity_tasks(user_id,task_code,progress,completed,claimed,created_at,updated_at) VALUES(?,?,1,1,0,NOW(),NOW()) ON DUPLICATE KEY UPDATE progress=1, completed=1, updated_at=NOW()'); if($stmt){$stmt->bind_param('is',$uid,$code);$stmt->execute();}}
    api_success(true);
}

function handle_activity_claim(array $d): void
{
    $u=current_user() ?: demo_user(); $uid=(int)$u['id']; $conn=db(); $code=first_value($d,['taskCode','code'],''); $amount=0.0;
    if($conn){
        if(!$code){$rs=@$conn->query('SELECT task_code FROM user_activity_tasks WHERE user_id='.(int)$uid.' AND completed=1 AND claimed=0 LIMIT 1');$r=$rs?$rs->fetch_assoc():null;$code=$r['task_code']??'join_telegram';}
        $stmt=@$conn->prepare('SELECT t.reward FROM activity_tasks t JOIN user_activity_tasks u ON u.task_code=t.code WHERE u.user_id=? AND t.code=? AND u.completed=1 AND u.claimed=0 LIMIT 1');
        if($stmt){$stmt->bind_param('is',$uid,$code);$stmt->execute();$r=$stmt->get_result()->fetch_assoc();$amount=(float)($r['reward']??0);}
        if($amount>0){$stmt=@$conn->prepare('UPDATE user_activity_tasks SET claimed=1,updated_at=NOW() WHERE user_id=? AND task_code=?');$stmt->bind_param('is',$uid,$code);$stmt->execute();credit_user_reward($uid,$amount,'activity_'.$code);}    
    }
    api_success(['amount'=>$amount]);
}

function handle_simple_reward(string $code, float $amount): void
{
    $u=current_user() ?: demo_user(); $uid=(int)$u['id']; $conn=db(); $claimed=false;
    if($conn){
        $stmt=$conn->prepare('INSERT IGNORE INTO user_activity_tasks(user_id,task_code,progress,completed,claimed,created_at,updated_at) VALUES(?,?,1,1,1,NOW(),NOW())'); if($stmt){$stmt->bind_param('is',$uid,$code);$stmt->execute();$claimed=$stmt->affected_rows>0;}
        if($claimed) credit_user_reward($uid,$amount,$code);
    }
    api_success(['amount'=>$claimed?$amount:0,'received'=>$claimed]);
}

function handle_activity_information_list(): void
{
    $conn=db(); $list=[];
    if($conn){$rs=@$conn->query('SELECT * FROM notifications WHERE status=1 ORDER BY id DESC LIMIT 20'); if($rs) while($r=$rs->fetch_assoc()) $list[]=['id'=>(int)$r['id'],'title'=>$r['title'],'content'=>$r['content'],'type'=>$r['type'],'jumpUrl'=>$r['jump_url'],'createTime'=>strtotime($r['created_at'])*1000];}
    if(!$list) $list[]=['id'=>1,'title'=>'Welcome Bonus','content'=>'Complete tasks and claim rewards.','type'=>'activity','jumpUrl'=>'/activity','createTime'=>now_ms()];
    api_success($list);
}
function handle_activity_information_detail(array $d): void { handle_activity_information_list(); }
function handle_activity_guide_config(): void { api_success(['id'=>1,'state'=>1,'rewardAmount'=>1,'orderNo'=>'','createTime'=>now_ms(),'recordState'=>0]); }
function handle_share_copy(): void
{
    $u = current_user() ?: demo_user();
    $origin = v31_share_origin();
    $code = (string)($u['invite_code'] ?? '37L3UFN');
    if ($code === '') $code = '37L3UFN';
    $link = $origin . '/register?inviteCode=' . rawurlencode($code) . '&from=web';
    api_success([
        'shareContent' => '#inviteLink#',
        'inviteCode' => $code,
        'promotionCode' => $code,
        'invitationCode' => $code,
        'shareCode' => strtolower(substr(md5($code . date('Ymd')), 0, 10)),
        'giftAmount' => 0.00,
        'officialUrl' => '13L.GAME',
        'shareDomain' => $origin,
        'inviteRewardUserCount' => 0,
        'inviteRewards' => v31_invite_rewards(),
        'agentL6InviteTaskSwitch' => true,
        'copyText' => $link,
        'shareUrl' => $link,
        'inviteUrl' => $link,
        'telegramUrl' => site_settings()['service_telegram'] ?? 'https://t.me/GAME13L_BOT',
    ]);
}

function handle_invited_wheel_info(): void
{
    $u = current_user() ?: demo_user(); $conn = db();
    if (!$conn) api_success(['isOpenInvitedWheel'=>true,'inviteCode'=>$u['invite_code']??'37L3UFN','userInvitedWheelAmount'=>0,'invitedWheelTotalPrizeAmount'=>500,'userInvitedWheelCount'=>3,'expiredTime'=>now_ms()+86400000]);
    $uid=(int)$u['id']; $settings=invited_wheel_settings(); $cycle=invited_wheel_get_cycle($conn,$uid);
    $records=[]; $stmt=@$conn->prepare('SELECT * FROM invited_wheel_records WHERE user_id=? ORDER BY id DESC LIMIT 5');
    if($stmt){$stmt->bind_param('i',$uid);$stmt->execute();$rs=$stmt->get_result(); while($r=$rs->fetch_assoc()) $records[]=['id'=>(int)$r['id'],'prizeAmount'=>(float)$r['prize_amount'],'amount'=>(float)$r['prize_amount'],'createTime'=>strtotime($r['created_at'])*1000,'isWin'=>(bool)$r['is_win']];}
    api_success([
        'isOpen' => (bool)($settings['enabled'] ?? true),
        'isOpenInvitedWheel' => (bool)($settings['enabled'] ?? true),
        'inviteCode' => $u['invite_code'] ?? '37L3UFN',
        'isFirstInvitedWheel' => ((int)($cycle['first_opened'] ?? 0) === 0),
        'isOpenDiskDisplay' => true,
        'diskDisplayAmount' => invited_wheel_free_amounts($conn),
        'noWinningRandomAmount' => $settings['no_winning_random_amount'] ?? [1,15],
        'userInvitedWheelAmount' => (float)($cycle['amount'] ?? 0),
        'invitedWheelTotalPrizeAmount' => (float)($settings['target_amount'] ?? 500),
        'userInvitedWheelCount' => (int)($cycle['spin_count'] ?? 0),
        'remainSpinCount' => (int)($cycle['spin_count'] ?? 0),
        'expiredTime' => strtotime((string)$cycle['expires_at']) * 1000,
        'currentValidDate' => date('Y-m-d H:i:s', strtotime((string)$cycle['expires_at'])),
        'isCashToMainWallet' => (bool)($settings['cash_to_main_wallet'] ?? true),
        'cashToMainWalletCodeWash' => (string)($settings['code_wash'] ?? '0'),
        'hasUnreceivedGiftPack' => false,
        'lastWheelRecordList' => $records,
        'minimumRechargeAmount' => (float)($settings['invite_recharge_required'] ?? 300),
        'minWithdrawAmount' => (float)($settings['min_withdraw_amount'] ?? 500),
        'needBetAmount' => max(0, (float)($cycle['turnover_required'] ?? 0) - user_turnover_after($conn, $uid, (string)($cycle['started_at'] ?? date('Y-m-d H:i:s')))),
        'turnoverCompleted' => user_turnover_after($conn, $uid, (string)($cycle['started_at'] ?? date('Y-m-d H:i:s'))),
    ]);
}
function handle_spin_invited_wheel(): void
{
    $u=current_user() ?: demo_user(); $conn=db(); if(!$conn) api_success(['isWin'=>true,'prizeAmount'=>0.10,'rewardAmount'=>0.10]);
    $uid=(int)$u['id']; $settings=invited_wheel_settings(); $cycle=invited_wheel_get_cycle($conn,$uid);
    if ((int)($cycle['spin_count'] ?? 0) <= 0) api_error('No spin count left', 400, 400);
    $first = ((int)($cycle['first_opened'] ?? 0) === 0);
    // User ne bola first/free spins me amount 0.10-3.00 ke beech rahe; yaha admin-configurable small prize list use hoti hai.
    $prize = invited_wheel_pick_small_prize($conn, $settings);
    $amount = round((float)$prize['amount'], 2);
    $maxSpin = (float)($settings['max_spin_reward'] ?? 3.00);
    if ($amount > $maxSpin) $amount = $maxSpin;
    $cid=(int)$cycle['id'];
    $turnoverMultiplier = max(0, (float)($settings['turnover_multiplier'] ?? 0));
    $turnoverAdd = round($amount * $turnoverMultiplier, 2);
    $stmt=@$conn->prepare('UPDATE invited_wheel_cycles SET amount=amount+?, spin_count=GREATEST(spin_count-1,0), first_opened=1, turnover_required=turnover_required+? WHERE id=? AND user_id=?');
    if($stmt){$stmt->bind_param('ddii',$amount,$turnoverAdd,$cid,$uid);$stmt->execute();}
    $src=$first?'first_free':'free'; $stmt=@$conn->prepare('INSERT INTO invited_wheel_records(user_id,cycle_id,prize_amount,spin_source,is_win,created_at) VALUES(?,?,?,?,1,NOW())');
    if($stmt){$stmt->bind_param('iids',$uid,$cid,$amount,$src);$stmt->execute();}
    api_success([
        'isFirstInvitedWheel'=>$first,
        'firstInvitedWheelDatas'=>$first ? invited_wheel_first_boxes($conn) : [],
        'isWin'=>true,
        'id'=>(int)($prize['id'] ?? 0),
        'prizeAmount'=>$amount,
        'rewardAmount'=>$amount,
        'amount'=>$amount,
        'needBetAmount'=>$turnoverAdd,
        'remainSpinCount'=>max(0,(int)$cycle['spin_count']-1),
    ]);
}
function handle_invited_wheel_withdraw(): void
{
    $u=current_user() ?: demo_user(); $conn=db(); if(!$conn) api_success(true);
    $uid=(int)$u['id']; $settings=invited_wheel_settings(); $cycle=invited_wheel_get_cycle($conn,$uid); $amount=(float)($cycle['amount']??0);
    $min=(float)($settings['min_withdraw_amount'] ?? $settings['target_amount'] ?? 500);
    if($amount < $min) api_error('Minimum wheel withdraw amount is '.number_format($min,2,'.',''), 400, 400);
    $needBet = max(0, (float)($cycle['turnover_required'] ?? 0) - user_turnover_after($conn, $uid, (string)($cycle['started_at'] ?? date('Y-m-d H:i:s'))));
    if($needBet > 0) api_error('Need to bet '.number_format($needBet,2,'.','').' before cash out', 400, 400);
    $order='IW'.date('ymdHis').random_int(100,999); $cid=(int)$cycle['id'];
    $stmt=@$conn->prepare('INSERT INTO invited_wheel_withdraws(user_id,cycle_id,order_no,amount,status,created_at) VALUES(?,?,?,?,"Pass",NOW())'); if($stmt){$stmt->bind_param('iisd',$uid,$cid,$order,$amount);$stmt->execute();}
    $stmt=@$conn->prepare('UPDATE users SET balance=balance+? WHERE id=?'); if($stmt){$stmt->bind_param('di',$amount,$uid);$stmt->execute();}
    $stmt=@$conn->prepare('UPDATE invited_wheel_cycles SET status="cashed", amount=0 WHERE id=? AND user_id=?'); if($stmt){$stmt->bind_param('ii',$cid,$uid);$stmt->execute();}
    $vendor='Activity'; $type='InvitedWheel'; $sub='CashOut'; $remark='Invited wheel cash out';
    $stmt=@$conn->prepare('INSERT INTO financial_records(user_id,record_no,order_no,vendor_code,type,sub_type,amount,back_amount,remark,created_at) VALUES(?,?,?,?,?,?,?,0,?,NOW())'); if($stmt){$stmt->bind_param('isssssds',$uid,$order,$order,$vendor,$type,$sub,$amount,$remark);$stmt->execute();}
    api_success(['orderNo'=>$order,'amount'=>$amount,'status'=>'Pass']);
}
function handle_invited_wheel_withdraw_record(array $d): void
{
    $u=current_user() ?: demo_user(); $conn=db(); $uid=(int)$u['id']; $list=[]; $total=0; $pageNo=max(1,(int)($d['pageNo']??1)); $pageSize=max(1,min(50,(int)($d['pageSize']??10))); $off=($pageNo-1)*$pageSize;
    if($conn){$stmt=@$conn->prepare('SELECT COUNT(*) c FROM invited_wheel_withdraws WHERE user_id=?'); if($stmt){$stmt->bind_param('i',$uid);$stmt->execute();$total=(int)$stmt->get_result()->fetch_assoc()['c'];} $stmt=@$conn->prepare('SELECT * FROM invited_wheel_withdraws WHERE user_id=? ORDER BY id DESC LIMIT ? OFFSET ?'); if($stmt){$stmt->bind_param('iii',$uid,$pageSize,$off);$stmt->execute();$rs=$stmt->get_result(); while($r=$rs->fetch_assoc()) $list[]=['orderNo'=>$r['order_no'],'amount'=>(float)$r['amount'],'status'=>$r['status'],'state'=>$r['status'],'createTime'=>strtotime($r['created_at'])*1000];}}
    api_success(['list'=>$list,'pageNo'=>$pageNo,'totalPage'=>(int)ceil(($total?:0)/$pageSize),'totalCount'=>$total]);
}
function handle_gift_pack_list(): void {
    $set = site_settings();
    if (empty($set['gift_enabled'])) api_success([]);
    $conn=db(); $list=[['id'=>1,'name'=>'Welcome Gift','rewardAmount'=>2,'state'=>0,'giftIcon'=>'/assets/darkRed/gift.webp']];
    if($conn){$rs=@$conn->query('SELECT id,code,title,amount,max_claim,claimed_count,expires_at,status FROM gift_codes WHERE status=1 ORDER BY id DESC LIMIT 30'); if($rs) while($g=$rs->fetch_assoc()) $list[]=['id'=>(int)$g['id'],'name'=>$g['title'] ?: ('Gift '.$g['code']),'code'=>$g['code'],'rewardAmount'=>(float)$g['amount'],'state'=>0,'claimedCount'=>(int)$g['claimed_count'],'maxClaim'=>(int)$g['max_claim'],'expireTime'=>$g['expires_at']?strtotime($g['expires_at'])*1000:0,'giftIcon'=>'/assets/darkRed/gift.webp'];}
    api_success($list);
}
function handle_day_week_info(): void { api_success(['dayTaskList'=>[], 'weekTaskList'=>[], 'dayRewardAmount'=>0, 'weekRewardAmount'=>0]); }
function handle_day_week_rule(): void { api_success(['content'=>'Daily and weekly task reward rules.']); }
function handle_checkin_data(): void { api_success(['checkedToday'=>false,'checkInDay'=>0,'rewardAmount'=>1,'list'=>[]]); }
function handle_loss_relief(): void { api_success([]); }

function handle_promotion_data(): void
{
    $u=current_user() ?: demo_user(); $origin=((isset($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off')?'https':'http').'://'.($_SERVER['HTTP_HOST']??'localhost'); $code=$u['invite_code']??'37L3UFN';
    api_success(['myInviteCode'=>$code,'inviteCode'=>$code,'promotionCode'=>$code,'inviteUrl'=>$origin.'/#/register?inviteCode='.$code,'copyText'=>$origin.'/#/register?inviteCode='.$code,'teamCount'=>0,'childCount'=>0,'firstChildCount'=>0,'yesterdayTotalCommission'=>0,'weekTotalCommission'=>0,'todayCommission'=>0,'totalCommission'=>0,'agentLevel'=>($u['is_agent']??0)?1:0]);
}
function handle_commission_detail(): void { api_success(['todayCommission'=>0,'yesterdayCommission'=>0,'totalCommission'=>0,'salaryAmount'=>0]); }
function handle_agent_list(array $d): void
{
    $u=current_user() ?: demo_user(); $uid=(int)$u['id']; $conn=db(); $list=[]; $total=0;
    if($conn){$stmt=@$conn->prepare('SELECT id,username,nickname,created_at,total_deposit,total_bet FROM users WHERE agent_parent_id=? ORDER BY id DESC LIMIT 50'); if($stmt){$stmt->bind_param('i',$uid);$stmt->execute();$rs=$stmt->get_result(); while($r=$rs->fetch_assoc()) $list[]=['userId'=>(int)$r['id'],'userName'=>$r['username'],'nickName'=>$r['nickname'],'registerTime'=>strtotime($r['created_at'])*1000,'rechargeAmount'=>(float)$r['total_deposit'],'betAmount'=>(float)$r['total_bet']]; $total=count($list);}}
    api_success(['list'=>$list,'pageNo'=>1,'totalPage'=>1,'totalCount'=>$total]);
}
function handle_agent_report(array $d): void {
    $u=current_user() ?: demo_user(); $conn=db(); $uid=(int)$u['id']; $list=[]; $total=0; $totalRecharge=0; $totalWithdraw=0; $totalBet=0;
    if($conn){$stmt=@$conn->prepare('SELECT id,username,mobile,total_deposit,total_withdraw,total_bet,created_at FROM users WHERE agent_parent_id=? ORDER BY id DESC LIMIT 100'); if($stmt){$stmt->bind_param('i',$uid);$stmt->execute();$rs=$stmt->get_result(); while($r=$rs->fetch_assoc()){ $total++; $totalRecharge+=(float)$r['total_deposit']; $totalWithdraw+=(float)$r['total_withdraw']; $totalBet+=(float)$r['total_bet']; $list[]=['userId'=>(int)$r['id'],'uid'=>(int)$r['id'],'userName'=>$r['username'],'mobile'=>$r['mobile'],'depositAmount'=>(float)$r['total_deposit'],'withdrawAmount'=>(float)$r['total_withdraw'],'betAmount'=>(float)$r['total_bet'],'createTime'=>strtotime($r['created_at'])*1000];}}}
    api_success(['list'=>$list,'pageNo'=>1,'totalPage'=>1,'totalCount'=>$total,'totalRecharge'=>$totalRecharge,'totalWithdraw'=>$totalWithdraw,'totalBet'=>$totalBet]);
}
function handle_rebate_levels(): void { api_success([['level'=>1,'teamCount'=>0,'rate'=>0.005],['level'=>2,'teamCount'=>5,'rate'=>0.01],['level'=>3,'teamCount'=>20,'rate'=>0.02]]); }
function handle_rebate_rates(): void { api_success([['gameType'=>'Lottery','level1'=>0.006,'level2'=>0.003,'level3'=>0.001]]); }


function handle_promotion_data_v14(): void
{
    $u = maybe_user();
    $conn = db();
    $uid = (int)($u['id'] ?? 0);
    $origin = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
    $code = (string)($u['invite_code'] ?? '37L3UFN');
    $sum = ($conn ? v14_agent_summary($conn, $uid) : ['registered'=>0,'totalDeposit'=>0,'totalBet'=>0,'todayCommission'=>0,'totalCommission'=>0,'firstDepositUsers'=>0,'team'=>[]]);
    $link = $origin . '/register?inviteCode=' . rawurlencode($code) . '&from=web';
    $data = [
        // Original AgentRebate keys
        'myInviteCode'=>$code,
        'inviteCode'=>$code,
        'promotionCode'=>$code,
        'shareCode'=>$code,
        'invitationCode'=>$code,
        'inviteUrl'=>$link,
        'promotionUrl'=>$link,
        'shareUrl'=>$link,
        'copyText'=>$link,
        'officialUrl'=>$origin,
        'shareDomain'=>$origin,
        'shareContent'=>'#inviteLink#',
        'yesterdayCommission'=>0.00,
        'yesterdayRewardAmount'=>0.00,
        'todayCommission'=>(float)$sum['todayCommission'],
        'totalCommission'=>(float)$sum['totalCommission'],
        'yesterdayTotalCommission'=>(float)$sum['totalCommission'],
        'weekTotalCommission'=>(float)$sum['totalCommission'],
        'yesterdayDirectSubRegisterCount'=>(int)$sum['registered'],
        'yesterdayDirectSubRechargeCount'=>(int)$sum['firstDepositUsers'],
        'yesterdayDirectSubRechargeAmount'=>(float)$sum['totalDeposit'],
        'yesterdayDirectSubFirstRechargeCount'=>(int)$sum['firstDepositUsers'],
        'yesterdayTeamRegisterCount'=>(int)$sum['registered'],
        'yesterdayTeamRechargeCount'=>(int)$sum['firstDepositUsers'],
        'yesterdayTeamRechargeAmount'=>(float)$sum['totalDeposit'],
        'yesterdayTeamFirstRechargeCount'=>(int)$sum['firstDepositUsers'],
        'firstChildCount'=>(int)$sum['firstDepositUsers'],
        'childCount'=>(int)$sum['registered'],
        'totalRewardAmount'=>(float)$sum['totalCommission'],
        'todayRewardAmount'=>(float)$sum['todayCommission'],
        'directSubordinateCount'=>(int)$sum['registered'],
        'teamSubordinateCount'=>(int)$sum['registered'],
        'directRechargeAmount'=>(float)$sum['totalDeposit'],
        'teamRechargeAmount'=>(float)$sum['totalDeposit'],
        'directBetAmount'=>(float)$sum['totalBet'],
        'teamBetAmount'=>(float)$sum['totalBet'],
        'firstDepositUsers'=>(int)$sum['firstDepositUsers'],
        'agentLevel'=>($u['is_agent']??0)?1:0,
        'telegramUrl'=>site_settings()['service_telegram'] ?? 'https://t.me/GAME13L',
        // Extra names used by different frontend builds
        'directInvites'=>[
            'registeredUsers'=>(int)$sum['registered'],
            'depositUsers'=>(int)$sum['firstDepositUsers'],
            'depositAmount'=>(float)$sum['totalDeposit'],
            'firstDepositUsers'=>(int)$sum['firstDepositUsers'],
        ],
        'teamInvites'=>[
            'registeredUsers'=>(int)$sum['registered'],
            'depositUsers'=>(int)$sum['firstDepositUsers'],
            'depositAmount'=>(float)$sum['totalDeposit'],
            'firstDepositUsers'=>(int)$sum['firstDepositUsers'],
        ],
        'dataOverview'=>[
            'registeredUsers'=>(int)$sum['registered'],
            'depositUsers'=>(int)$sum['firstDepositUsers'],
            'depositAmount'=>(float)$sum['totalDeposit'],
            'totalBet'=>(float)$sum['totalBet'],
            'totalCommission'=>(float)$sum['totalCommission'],
        ]
    ];
    api_success($data);
}

function handle_commission_detail_v14(): void
{
    $u=maybe_user(); $conn=db(); $uid=(int)($u['id'] ?? 0);
    $sum=$conn?v14_agent_summary($conn,$uid):['team'=>[],'todayCommission'=>0,'totalCommission'=>0,'totalBet'=>0,'totalDeposit'=>0,'registered'=>0];
    $list=[];
    if($conn){
        $stmt=@$conn->prepare('SELECT c.*,u.username,u.nickname FROM agent_commissions c LEFT JOIN users u ON u.id=c.from_user_id WHERE c.user_id=? ORDER BY c.id DESC LIMIT 50');
        if($stmt){$stmt->bind_param('i',$uid);$stmt->execute();$rs=$stmt->get_result(); while($r=$rs->fetch_assoc()) $list[]=['id'=>(int)$r['id'],'userName'=>$r['username'],'nickName'=>$r['nickname'],'betAmount'=>(float)$r['bet_amount'],'rate'=>(float)$r['rate'],'commission'=>(float)$r['commission_amount'],'commissionAmount'=>(float)$r['commission_amount'],'createTime'=>strtotime($r['created_at'])*1000];}
    }
    $rebateItems = [[
        'type'=>1,
        'rebateLevel'=>1,
        'betPeoples'=>(int)$sum['registered'],
        'betOrderAmount'=>(float)$sum['totalBet'],
        'rebateAmount'=>(float)$sum['totalCommission'],
        'rebateWhereItemDetails'=>[[
            'levelId'=>1,
            'orderAmount'=>(float)$sum['totalBet'],
            'rebateRate'=>0.60,
            'rebateAmount'=>(float)$sum['totalCommission']
        ]]
    ]];
    api_success([
        'settlementTime'=>date('Y-m-d H:i:s'),
        'reportDate'=>date('Y-m-d H:i:s'),
        'rechargePeoples'=>(int)$sum['firstDepositUsers'],
        'rechargeAmount'=>(float)$sum['totalDeposit'],
        'totalBetOrderAmount'=>(float)$sum['totalBet'],
        'totalCommissioned'=>(float)$sum['totalCommission'],
        'rebateItems'=>$rebateItems,
        'todayCommission'=>(float)$sum['todayCommission'],
        'yesterdayCommission'=>0.00,
        'totalCommission'=>(float)$sum['totalCommission'],
        'salaryAmount'=>(float)($u['agent_salary']??0),
        'teamBetAmount'=>(float)$sum['totalBet'],
        'teamRechargeAmount'=>(float)$sum['totalDeposit'],
        'teamCount'=>(int)$sum['registered'],
        'list'=>$list,
        'pageNo'=>1,'totalPage'=>1,'totalCount'=>count($list)
    ]);
}

function handle_agent_list_v14(array $d): void
{
    $u=maybe_user(); $conn=db(); $uid=(int)$u['id']; $pageNo=max(1,(int)($d['pageNo']??1)); $pageSize=max(1,min(100,(int)($d['pageSize']??20))); $off=($pageNo-1)*$pageSize; $list=[]; $total=0;
    if($conn){
        $stmt=@$conn->prepare('SELECT COUNT(*) c FROM users WHERE agent_parent_id=?'); if($stmt){$stmt->bind_param('i',$uid);$stmt->execute();$total=(int)$stmt->get_result()->fetch_assoc()['c'];}
        $stmt=@$conn->prepare('SELECT id,username,mobile,nickname,total_deposit,total_withdraw,total_bet,created_at,last_login_at,vip_level,status FROM users WHERE agent_parent_id=? ORDER BY id DESC LIMIT ? OFFSET ?');
        if($stmt){$stmt->bind_param('iii',$uid,$pageSize,$off);$stmt->execute();$rs=$stmt->get_result(); while($r=$rs->fetch_assoc()) $list[]=[
            'uid'=>(int)$r['id'],'userId'=>(int)$r['id'],'userName'=>$r['username'],'mobile'=>$r['mobile'],'nickName'=>$r['nickname'],
            'level'=>(int)$r['vip_level'],'state'=>(int)$r['status'],'registerTime'=>strtotime($r['created_at'])*1000,
            'lastLoginTime'=>$r['last_login_at']?strtotime($r['last_login_at'])*1000:0,
            'depositAmount'=>(float)$r['total_deposit'],'withdrawAmount'=>(float)$r['total_withdraw'],'betAmount'=>(float)$r['total_bet'],
            'firstDepositAmount'=>(float)$r['total_deposit'],'commissionAmount'=>round(((float)$r['total_bet'])*0.006,2)
        ];}
    }
    api_success(['list'=>$list,'pageNo'=>$pageNo,'totalPage'=>(int)ceil(($total?:0)/$pageSize),'totalCount'=>$total]);
}

function handle_agent_report_v14(array $d): void
{
    $u=maybe_user(); $conn=db(); $uid=(int)$u['id']; $sum=$conn?v14_agent_summary($conn,$uid):['team'=>[],'registered'=>0,'totalDeposit'=>0,'totalWithdraw'=>0,'totalBet'=>0,'todayDeposit'=>0,'todayBet'=>0,'todayCommission'=>0,'totalCommission'=>0,'firstDepositUsers'=>0];
    $list=[]; foreach($sum['team'] as $r){ $list[]=['uid'=>(int)$r['id'],'userId'=>(int)$r['id'],'userName'=>$r['username'],'mobile'=>$r['mobile'],'depositAmount'=>(float)$r['total_deposit'],'withdrawAmount'=>(float)$r['total_withdraw'],'betAmount'=>(float)$r['total_bet'],'firstDepositAmount'=>(float)$r['total_deposit'],'createTime'=>strtotime($r['created_at'])*1000]; }
    api_success([
        'list'=>$list,'pageNo'=>1,'totalPage'=>1,'totalCount'=>(int)$sum['registered'],
        'registeredUsers'=>(int)$sum['registered'],'depositUsers'=>(int)$sum['firstDepositUsers'],'firstDepositUsers'=>(int)$sum['firstDepositUsers'],
        'depositAmount'=>(float)$sum['totalDeposit'],'withdrawAmount'=>(float)$sum['totalWithdraw'],'betAmount'=>(float)$sum['totalBet'],
        'totalDeposit'=>(float)$sum['totalDeposit'],'totalWithdraw'=>(float)$sum['totalWithdraw'],'totalBet'=>(float)$sum['totalBet'],
        'todayDeposit'=>(float)$sum['todayDeposit'],'todayBet'=>(float)$sum['todayBet'],'todayCommission'=>(float)$sum['todayCommission'],
        'totalCommission'=>(float)$sum['totalCommission']
    ]);
}

function handle_rebate_levels_v14(): void
{
    api_success([
      ['level'=>1,'directSubordinate'=>0,'teamBet'=>0,'rate'=>0.006,'commissionRatio'=>0.6],
      ['level'=>2,'directSubordinate'=>5,'teamBet'=>50000,'rate'=>0.008,'commissionRatio'=>0.8],
      ['level'=>3,'directSubordinate'=>20,'teamBet'=>200000,'rate'=>0.010,'commissionRatio'=>1.0]
    ]);
}
function handle_rebate_rates_v14(): void
{
    api_success([
      ['gameType'=>'Lottery','gameTypeName'=>'Lottery','level1'=>0.006,'level2'=>0.003,'level3'=>0.001],
      ['gameType'=>'Slots','gameTypeName'=>'Slots','level1'=>0.004,'level2'=>0.002,'level3'=>0.001]
    ]);
}


function handle_agent_l3_team_info(): void
{
    $u = maybe_user(); $conn = db(); $uid = (int)($u['id'] ?? 0);
    $sum = $conn ? v14_agent_summary($conn, $uid) : ['registered'=>0,'totalDeposit'=>0,'totalBet'=>0,'todayCommission'=>0,'totalCommission'=>0,'firstDepositUsers'=>0,'todayBet'=>0,'team'=>[]];
    $levels = [
        ['teamLevel'=>0,'teamLevelName'=>'VIP0','teamPeoples'=>0,'teamBetAmount'=>0,'betUpRate'=>0.6,'rechargeUpRate'=>0.6,'invitationTotalRewardAmount'=>0],
        ['teamLevel'=>1,'teamLevelName'=>'VIP1','teamPeoples'=>5,'teamBetAmount'=>5000,'betUpRate'=>0.8,'rechargeUpRate'=>0.8,'invitationTotalRewardAmount'=>10],
        ['teamLevel'=>2,'teamLevelName'=>'VIP2','teamPeoples'=>20,'teamBetAmount'=>50000,'betUpRate'=>1.0,'rechargeUpRate'=>1.0,'invitationTotalRewardAmount'=>25],
        ['teamLevel'=>3,'teamLevelName'=>'VIP3','teamPeoples'=>100,'teamBetAmount'=>200000,'betUpRate'=>1.2,'rechargeUpRate'=>1.2,'invitationTotalRewardAmount'=>100],
    ];
    api_success([
        'teamLevelConfig'=>$levels,
        'myTeamLevel'=>0,
        'myTeamBetAmount'=>(float)$sum['totalBet'],
        'myTeamPeoples'=>(int)$sum['registered'],
        'todayRewardAmount'=>(float)$sum['todayCommission'],
        'totalRewardAmount'=>(float)$sum['totalCommission'],
        'notSendCommissionAmount'=>0,
        'receivedAmount'=>0,
        'autoSendCommission'=>true,
        'isOpenAgentRank'=>true,
        'externalAgentLinkList'=>[
            ['id'=>1,'state'=>1,'linkIndex'=>1,'buttonText'=>'Share','jumpUrl'=>'/share','imgUrl'=>'/img/6007/other/icon_home-6edfbc6.webp'],
            ['id'=>2,'state'=>1,'linkIndex'=>2,'buttonText'=>'Gift code','jumpUrl'=>'/gift','imgUrl'=>'/img/6007/other/icon_gift-dlp1v3tc.webp'],
        ],
        'inviteCode'=>(string)($u['invite_code'] ?? '37L3UFN')
    ]);
}

function handle_agent_l3_invitation_info(): void
{
    $u = maybe_user(); $conn = db(); $uid = (int)($u['id'] ?? 0);
    $sum = $conn ? v14_agent_summary($conn, $uid) : ['registered'=>0,'todayCommission'=>0,'totalCommission'=>0,'firstDepositUsers'=>0];
    $configs = [
        ['id'=>1,'userCount'=>1,'rewardAmount'=>1.00,'isRewarded'=>((int)$sum['registered']>=1)],
        ['id'=>2,'userCount'=>3,'rewardAmount'=>3.00,'isRewarded'=>((int)$sum['registered']>=3)],
        ['id'=>3,'userCount'=>10,'rewardAmount'=>10.00,'isRewarded'=>((int)$sum['registered']>=10)],
        ['id'=>4,'userCount'=>30,'rewardAmount'=>30.00,'isRewarded'=>((int)$sum['registered']>=30)],
    ];
    api_success([
        'inviteTaskConfig'=>$configs,
        'inviteDayLimitCount'=>50,
        'inviteRewardAmount'=>1.00,
        'invitedRewardAmount'=>0.00,
        'myTodayInviteUserCount'=>(int)$sum['registered'],
        'myTotalInviteTaskRewardAmount'=>(float)$sum['totalCommission'],
        'myTotalInviteUserCount'=>(int)$sum['registered'],
        'inviteCode'=>(string)($u['invite_code'] ?? '37L3UFN')
    ]);
}

function handle_agent_l3_sub_summary(): void
{
    $u=maybe_user(); $conn=db(); $uid=(int)($u['id'] ?? 0); $sum=$conn?v14_agent_summary($conn,$uid):['registered'=>0,'totalDeposit'=>0,'totalBet'=>0,'totalWithdraw'=>0,'firstDepositUsers'=>0];
    api_success([
        'totalCount_L1'=>(int)$sum['registered'], 'totalCount_L2'=>0, 'totalCount_L3'=>0,
        'depositNumber'=>(int)$sum['firstDepositUsers'], 'depositAmount'=>(float)$sum['totalDeposit'],
        'betNumber'=>(int)$sum['registered'], 'betAmount'=>(float)$sum['totalBet'],
        'commission'=>(float)($sum['totalBet']*0.006), 'totalCommission'=>(float)($sum['totalBet']*0.006)
    ]);
}

function handle_agent_l3_sub_data(array $d): void
{
    $u=maybe_user(); $conn=db(); $uid=(int)($u['id'] ?? 0); $pageNo=max(1,(int)($d['pageNo']??1)); $pageSize=max(1,min(100,(int)($d['pageSize']??10))); $off=($pageNo-1)*$pageSize; $list=[]; $total=0;
    if($conn){
        $stmt=@$conn->prepare('SELECT COUNT(*) c FROM users WHERE agent_parent_id=?'); if($stmt){$stmt->bind_param('i',$uid);$stmt->execute();$total=(int)$stmt->get_result()->fetch_assoc()['c'];}
        $stmt=@$conn->prepare('SELECT id,username,mobile,nickname,total_deposit,total_bet,created_at,vip_level FROM users WHERE agent_parent_id=? ORDER BY id DESC LIMIT ? OFFSET ?');
        if($stmt){$stmt->bind_param('iii',$uid,$pageSize,$off);$stmt->execute();$rs=$stmt->get_result(); while($r=$rs->fetch_assoc()) $list[]=[
            'uid'=>(int)$r['id'],'userId'=>(int)$r['id'],'userName'=>$r['username'],'mobile'=>$r['mobile'],'nickName'=>$r['nickname'],'level'=>(int)$r['vip_level'],
            'depositAmount'=>(float)$r['total_deposit'],'betAmount'=>(float)$r['total_bet'],'commission'=>round(((float)$r['total_bet'])*0.006,2),
            'registerTime'=>strtotime($r['created_at'])*1000,'createTime'=>strtotime($r['created_at'])*1000
        ];}
    }
    api_success(['list'=>$list,'pageNo'=>$pageNo,'totalPage'=>(int)ceil(($total?:0)/$pageSize),'totalCount'=>$total,'totalCount_L1'=>$total,'totalCount_L2'=>0,'totalCount_L3'=>0]);
}

function handle_agent_l3_commission_record(array $d): void
{
    $u=maybe_user(); $conn=db(); $uid=(int)($u['id'] ?? 0); $list=[];
    if($conn){$stmt=@$conn->prepare('SELECT c.*,u.username,u.nickname FROM agent_commissions c LEFT JOIN users u ON u.id=c.from_user_id WHERE c.user_id=? ORDER BY c.id DESC LIMIT 100'); if($stmt){$stmt->bind_param('i',$uid);$stmt->execute();$rs=$stmt->get_result(); while($r=$rs->fetch_assoc()) $list[]=['id'=>(int)$r['id'],'userName'=>$r['username'],'nickName'=>$r['nickname'],'commission'=>(float)$r['commission_amount'],'amount'=>(float)$r['commission_amount'],'betAmount'=>(float)$r['bet_amount'],'createTime'=>strtotime($r['created_at'])*1000];}}
    api_success($list);
}

function handle_agent_l3_commission_detail_records(array $d, string $type): void
{
    $u=maybe_user(); $conn=db(); $uid=(int)($u['id'] ?? 0); $pageNo=max(1,(int)($d['pageNo']??1)); $pageSize=max(1,min(100,(int)($d['pageSize']??10))); $off=($pageNo-1)*$pageSize; $list=[]; $total=0; $totalCommission=0;
    if($conn){
        $stmt=@$conn->prepare('SELECT COUNT(*) c, COALESCE(SUM(commission_amount),0) s FROM agent_commissions WHERE user_id=?'); if($stmt){$stmt->bind_param('i',$uid);$stmt->execute();$r=$stmt->get_result()->fetch_assoc();$total=(int)($r['c']??0);$totalCommission=(float)($r['s']??0);}
        $stmt=@$conn->prepare('SELECT c.*,u.username,u.nickname FROM agent_commissions c LEFT JOIN users u ON u.id=c.from_user_id WHERE c.user_id=? ORDER BY c.id DESC LIMIT ? OFFSET ?'); if($stmt){$stmt->bind_param('iii',$uid,$pageSize,$off);$stmt->execute();$rs=$stmt->get_result(); while($r=$rs->fetch_assoc()) $list[]=['id'=>(int)$r['id'],'uid'=>(int)$r['from_user_id'],'userName'=>$r['username'],'nickName'=>$r['nickname'],'orderAmount'=>(float)$r['bet_amount'],'betAmount'=>(float)$r['bet_amount'],'rebateRate'=>(float)$r['rate'],'rebateAmount'=>(float)$r['commission_amount'],'commission'=>(float)$r['commission_amount'],'createTime'=>strtotime($r['created_at'])*1000];}
    }
    api_success(['list'=>$list,'pageNo'=>$pageNo,'totalPage'=>(int)ceil(($total?:0)/$pageSize),'totalCount'=>$total,'totalCommission'=>$totalCommission]);
}

function handle_agent_l3_invite_record(array $d): void
{
    handle_agent_l3_sub_data($d);
}

function handle_agent_l3_invite_task_record(array $d): void
{
    $u=maybe_user(); $conn=db(); $uid=(int)($u['id'] ?? 0); $sum=$conn?v14_agent_summary($conn,$uid):['registered'=>0];
    api_success(['list'=>[], 'pageNo'=>1, 'totalPage'=>0, 'totalCount'=>0, 'totalCommission'=>0, 'myTotalInviteUserCount'=>(int)$sum['registered']]);
}

function handle_agent_l3_receive_commission(): void
{
    api_success(['receivedAmount'=>0]);
}

function handle_day_week_info_v14(): void
{
    $u=maybe_user(); $conn=db(); $uid=(int)$u['id']; $day=[]; $week=[]; $dayReward=0; $weekReward=0;
    if($conn){
        $rs=@$conn->query('SELECT * FROM day_week_tasks WHERE status=1 ORDER BY period_type ASC, sort DESC, id ASC');
        if($rs) while($t=$rs->fetch_assoc()){
            $period=$t['period_type']; $start=v14_start_time($period); $key=v14_period_key($period); $progress=v14_user_metric($conn,$uid,$t['target_type'],$start); $target=(float)$t['target_value']; $reward=(float)$t['reward_amount'];
            $claimed=0; $stmt=@$conn->prepare('SELECT id FROM day_week_task_claims WHERE user_id=? AND task_code=? AND period_key=? LIMIT 1'); if($stmt){$code=$t['task_code'];$stmt->bind_param('iss',$uid,$code,$key);$stmt->execute();$claimed=$stmt->get_result()->num_rows>0?1:0;}
            $state=$claimed?2:($progress>=$target?1:0); $row=['id'=>(int)$t['id'],'taskId'=>(int)$t['id'],'taskCode'=>$t['task_code'],'title'=>$t['title'],'name'=>$t['title'],'taskName'=>$t['title'],'targetType'=>$t['target_type'],'targetValue'=>$target,'needAmount'=>max(0,$target-$progress),'progress'=>$progress,'finishValue'=>$progress,'rewardAmount'=>$reward,'amount'=>$reward,'state'=>$state,'isCompleted'=>$progress>=$target,'isReceived'=>(bool)$claimed];
            if($period==='week'){ $week[]=$row; $weekReward+=$state===1?$reward:0; } else { $day[]=$row; $dayReward+=$state===1?$reward:0; }
        }
    }
    api_success(['dayTaskList'=>$day,'weekTaskList'=>$week,'dailyTaskList'=>$day,'weeklyTaskList'=>$week,'dayRewardAmount'=>$dayReward,'weekRewardAmount'=>$weekReward,'serverDate'=>date('Y-m-d'),'resetTime'=>strtotime('tomorrow')*1000]);
}
function handle_day_week_rule_v14(): void
{
    api_success(['content'=>'Complete recharge, bet and invite targets to claim day/week task bonus. Admin can manage targets and rewards in Bonus / Gift / Notice tab.','rule'=>'Rewards are manual virtual wallet credits after task claim.']);
}
function handle_day_week_claim_v14(array $d): void
{
    $u=current_user() ?: demo_user(); $conn=db(); if(!$conn) api_success(['amount'=>0]); $uid=(int)$u['id'];
    $code=(string)first_value($d,['taskCode','code','id'], '');
    if($code!=='' && ctype_digit($code)){ $id=(int)$code; $stmt=@$conn->prepare('SELECT task_code FROM day_week_tasks WHERE id=? LIMIT 1'); if($stmt){$stmt->bind_param('i',$id);$stmt->execute();$r=$stmt->get_result()->fetch_assoc();$code=(string)($r['task_code']??'');}}
    if($code===''){ $rs=@$conn->query('SELECT task_code FROM day_week_tasks WHERE status=1 ORDER BY sort DESC LIMIT 1'); $r=$rs?$rs->fetch_assoc():null; $code=(string)($r['task_code']??''); }
    $stmt=@$conn->prepare('SELECT * FROM day_week_tasks WHERE task_code=? AND status=1 LIMIT 1'); if(!$stmt) api_error('Task not found',404,404); $stmt->bind_param('s',$code); $stmt->execute(); $t=$stmt->get_result()->fetch_assoc(); if(!$t) api_error('Task not found',404,404);
    $period=$t['period_type']; $key=v14_period_key($period); $progress=v14_user_metric($conn,$uid,$t['target_type'],v14_start_time($period)); if($progress<(float)$t['target_value']) api_error('Conditions are not met',400,400);
    $amount=(float)$t['reward_amount']; $stmt=@$conn->prepare('INSERT IGNORE INTO day_week_task_claims(user_id,task_code,period_key,amount,created_at) VALUES(?,?,?,?,NOW())'); if($stmt){$stmt->bind_param('issd',$uid,$code,$key,$amount);$stmt->execute(); if($stmt->affected_rows>0){ credit_user_reward($uid,$amount,'day_week_'.$code); api_success(['amount'=>$amount,'received'=>true]); }}
    api_success(['amount'=>0,'received'=>false]);
}

function handle_workorder_form_list(): void
{
    // Exact production-style list expected by the bundled Vue work-order JS.
    // NOTE: id = formId, workOrderTypeId = typeId used in route query.
    $forms = [
        ['id'=>91,'workOrderTypeId'=>4,'workOrderTypeName'=>'存款未到账自动化','displayName'=>'Deposit Not Received ','sort'=>20,'icon'=>'/img/6007/other/112154568-31026-file_20260413112154567.webp','type'=>1,'outLink'=>''],
        ['id'=>89,'workOrderTypeId'=>5,'workOrderTypeName'=>'取款未到账','displayName'=>'Withdrawal problem ','sort'=>19,'icon'=>'/img/6007/other/110752511-31006-file_20260413110752511.webp','type'=>1,'outLink'=>''],
        ['id'=>90,'workOrderTypeId'=>11,'workOrderTypeName'=>'修改ISFC自动化','displayName'=>'IFSC Modification','sort'=>18,'icon'=>'/img/6007/other/111449517-31009-file_20260413111449516.webp','type'=>1,'outLink'=>''],
        ['id'=>97,'workOrderTypeId'=>12,'workOrderTypeName'=>'修改银行名称自动化','displayName'=>'Change bank name','sort'=>17,'icon'=>'/img/6007/other/124756266-31043-file_20260413124756266.webp','type'=>1,'outLink'=>''],
        ['id'=>94,'workOrderTypeId'=>8,'workOrderTypeName'=>'修改登录密码半自动','displayName'=>'Change ID Login Password','sort'=>15,'icon'=>'/img/6007/other/121844044-31037-file_20260413121844043.webp','type'=>1,'outLink'=>''],
        ['id'=>100,'workOrderTypeId'=>3,'workOrderTypeName'=>'其他问题','displayName'=>'WINGO WIN STREAK BONUS','sort'=>1,'icon'=>'/img/6007/other/010031310-31416-file_20260415130031201.webp','type'=>1,'outLink'=>''],
        ['id'=>92,'workOrderTypeId'=>2,'workOrderTypeName'=>'一对一客服','displayName'=>'Game Problems','sort'=>1,'icon'=>'/img/6007/other/094549007-32153-file_20260420094549005.webp','type'=>1,'outLink'=>''],
        ['id'=>98,'workOrderTypeId'=>1,'workOrderTypeName'=>'外部链接','displayName'=>'Online customer service','sort'=>1,'icon'=>'/img/6007/other/094813528-32155-file_20260420094813527.webp','type'=>1,'outLink'=>'Online customer service  👩‍💻💖👆=https://t.me/GAME13L_BOT'],
        ['id'=>102,'workOrderTypeId'=>3,'workOrderTypeName'=>'其他问题','displayName'=>'Change UPI account','sort'=>1,'icon'=>'/img/6007/other/014114524-31420-file_20260415134114407.webp','type'=>1,'outLink'=>''],
        ['id'=>99,'workOrderTypeId'=>17,'workOrderTypeName'=>'新增USDT半自动','displayName'=>'Add a new USDT address','sort'=>1,'icon'=>'/img/6007/other/073527998-31362-file_20260415073527997.webp','type'=>1,'outLink'=>''],
    ];
    api_success($forms);
}
function handle_workorder_home_config(): void
{
    $set=site_settings();
    api_success(['isOpen'=>(bool)($set['workorder_enabled']??true),'serviceUrl'=>$set['service_telegram']??'https://t.me/GAME13L','tips'=>'Please select the relevant query and submit it for review. After successful submission, admin reply will show in Progress Query.','hasDepositForm'=>true,'hasRechargeForm'=>true,'hasFaq'=>true]);
}
function handle_workorder_questions(array $d): void { handle_workorder_faq_list(); }
function handle_workorder_field_list(array $d): void
{
    // V28 FIX: frontend can send serviceId/formId/id and expects either data.list or data.formFields.
    // Earlier response sometimes became list:[] because serviceId was not mapped and response shape
    // did not match every bundled JS chunk. This handler returns all common keys.
    $formId = (int)first_value($d, ['formId','formID','id','serviceId','serviceID','serviceid','workOrderFormId'], 0);
    $typeId = (int)first_value($d, ['typeId','typeID','typeid','typeLd','typeld','serviceTypeId','workOrderTypeId','workOrderTypeID'], 0);

    if ($typeId <= 0 && $formId > 0) {
        $map = [
            91 => 4,   // Deposit Not Received
            89 => 5,   // Withdrawal problem
            90 => 11,  // IFSC Modification
            97 => 12,  // Change bank name
            94 => 8,   // Change ID Login Password
            100 => 3,  // WINGO WIN STREAK BONUS
            92 => 2,   // Game Problems
            98 => 1,   // Online customer service
            102 => 3,  // Change UPI account
            99 => 17,  // Add USDT
        ];
        $typeId = (int)($map[$formId] ?? 3);
    }
    if ($typeId <= 0) $typeId = 3;

    $display = 'selfService';
    $fields = [];

    $mk = function(int $id, string $code, string $label, string $inputType, string $placeholder = '', bool $required = true, int $sort = 1, array $options = []) {
        // Native/mobile JS chunks from different builds use different property names.
        // Keep all aliases so the same response works with original JS/CSS without custom UI hacks.
        $fieldTypeMap = [
            'input' => 1, 'text' => 1, 'textarea' => 2, 'longtext' => 2,
            'password' => 8, 'select' => 11, 'captcha' => 15,
            'upload' => 20, 'file' => 20, 'image' => 19, 'number' => 1,
        ];
        $inputType = strtolower($inputType);
        $fieldType = (int)($fieldTypeMap[$inputType] ?? 1);
        return [
            'id' => $id,
            'fieldId' => $id,
            'code' => $code,
            'key' => $code,
            'name' => $code,
            'fieldCode' => $code,
            'typeCode' => $code,
            'fieldName' => $label,
            'displayName' => $label,
            'title' => $label,
            'label' => $label,
            'placeholder' => $placeholder ?: $label,
            'tips' => $placeholder ?: $label,
            'type' => $inputType,
            'inputType' => $inputType,
            'componentType' => $inputType,
            'fieldType' => $fieldType,
            'required' => $required ? 1 : 0,
            'isRequired' => $required ? 1 : 0,
            'isShow' => 1,
            'status' => 1,
            'sort' => $sort,
            'value' => '',
            'defaultValue' => '',
            'fieldValue' => '',
            'optionList' => $options,
            'options' => $options,
            'list' => $options,
            'typeName' => $label,
            'inputName' => $label,
            'regex' => '',
            'validateRule' => '',
            'maxlength' => 255,
            'minlength' => 0,
        ];
    };

    switch ($typeId) {
        case 4: // Deposit Not Received
            $display = 'Deposit Not Received';
            $fields = [
                $mk(101, 'DepositOrderNo', 'Order No', 'input', 'Input order number', true, 1),
                $mk(102, 'OrderAmount', 'Amount', 'number', 'Input amount', true, 2),
                $mk(103, 'UTR', 'UTR', 'input', 'Input 12 digits here', true, 3),
                $mk(104, 'LongText', 'Describe', 'textarea', 'Describe your payment issue', false, 4),
                $mk(105, 'FileUpload', 'Latest Deposit Receipt Proof', 'upload', 'Photo', false, 9),
            ];
            break;

        case 5: // Withdrawal problem
            $display = 'Withdrawal problem';
            $fields = [
                $mk(201, 'WithdrawOrderNo', 'Withdraw Order No', 'input', 'Input withdraw order no', true, 1),
                $mk(202, 'WithdrawAmount', 'Amount', 'number', 'Input amount', true, 2),
                $mk(203, 'LongText', 'Describe', 'textarea', 'Describe your withdrawal problem', true, 3),
                $mk(204, 'FileUpload', 'Provide detailed screenshots and photos', 'upload', 'Photo', false, 9),
            ];
            break;

        case 11: // IFSC Modification
            $display = 'IFSC Modification';
            $fields = [
                $mk(301, 'BankAccountNumber', 'Bank Account Number', 'input', 'Please enter Bank Account Number', true, 1),
                $mk(302, 'IFSC', 'IFSC', 'input', 'Please enter IFSC', true, 2),
                $mk(303, 'PhoneEmailCaptcha', 'Phone/Email Captcha', 'captcha', 'Enter verification code', true, 3),
            ];
            break;

        case 12: // Change bank name
            $display = 'Change bank name';
            $fields = [
                $mk(401, 'BankName', 'Bank Name', 'input', 'Please enter bank name', true, 1),
                $mk(402, 'BankAccountNumber', 'Bank Account Number', 'input', 'Please enter Bank Account Number', true, 2),
                $mk(403, 'PhoneEmailCaptcha', 'Phone/Email Captcha', 'captcha', 'Enter verification code', false, 3),
                $mk(404, 'FileUpload', 'Provide detailed screenshots and photos', 'upload', 'Photo', false, 9),
            ];
            break;

        case 8: // Change ID Login Password
            $display = 'Change ID Login Password';
            $fields = [
                $mk(501, 'NewPassword', 'New Password', 'password', 'Please enter New Password', true, 1),
                $mk(502, 'ConfirmPassword', 'Password', 'password', 'Please enter New Password', true, 2),
                $mk(503, 'FileUpload', 'Latest Deposit Receipt Proof', 'upload', 'Photo', false, 3),
                $mk(504, 'IdentityPhoto', 'Photo Selfie Hold Identity Card', 'upload', 'Photo', false, 4),
                $mk(505, 'BankBookPhoto', 'Photo Selfie Hold Passbook Bank', 'upload', 'Photo', false, 5),
            ];
            break;

        case 17: // Add a new USDT address
            $display = 'Add a new USDT address';
            $fields = [
                $mk(601, 'Network', 'Network', 'select', 'Please select network', true, 1, [
                    ['label'=>'TRC20','value'=>'TRC20','name'=>'TRC20'],
                    ['label'=>'BEP20','value'=>'BEP20','name'=>'BEP20'],
                ]),
                $mk(602, 'UsdtAddress', 'USDT Address', 'input', 'Please enter USDT address', true, 2),
                $mk(603, 'LongText', 'Describe', 'textarea', 'Describe your request', false, 3),
                $mk(604, 'FileUpload', 'Provide detailed screenshots and photos', 'upload', 'Photo', false, 9),
            ];
            break;

        case 2: // Game Problems
            $display = 'Game Problems';
            $fields = [
                $mk(701, 'GameName', 'Game Name', 'input', 'Please enter game name', true, 1),
                $mk(702, 'LongText', 'Describe your application in detail', 'textarea', 'Please enter Describe your application in detail', true, 2),
                $mk(703, 'FileUpload', 'Provide detailed screenshots and photos', 'upload', 'Photo', false, 9),
            ];
            break;

        case 1: // Online customer service
            $display = 'Online customer service';
            $fields = [
                $mk(801, 'LongText', 'Describe your application in detail', 'textarea', 'Please enter Describe your application in detail', true, 1),
            ];
            break;

        default: // WINGO WIN STREAK BONUS / Change UPI / Other
            $display = ($formId === 102) ? 'Change UPI account' : (($formId === 100) ? 'WINGO WIN STREAK BONUS' : 'selfService');
            $fields = [
                $mk(901, 'LongText', 'Describe your application in detail', 'textarea', 'Please enter Describe your application in detail', true, 1),
                $mk(902, 'FileUpload', 'Provide detailed screenshots and photos', 'upload', 'Photo', false, 9),
            ];
            break;
    }

    $payload = [
        'displayName' => $display,
        'formTitle' => $display,
        'title' => $display,
        'serviceId' => $formId,
        'typeId' => $typeId,
        'workOrderTypeId' => $typeId,
        'hasUserGuide' => true,
        'userGuideContent' => 'Please submit correct details. Admin reply will show in Progress Query.',
        'list' => $fields,
        'formFields' => $fields,
        'fieldList' => $fields,
        'fields' => $fields,
        'records' => $fields,
        'rows' => $fields,
        'items' => $fields,
        'pageNo' => 1,
        'pageSize' => count($fields),
        'totalPage' => 1,
        'totalCount' => count($fields),
    ];
    api_success($payload);
}
function handle_workorder_outlink_list(array $d): void { api_success(['formTitle'=>'Customer Service','outLinkList'=>[]]); }
function handle_workorder_tutorial(array $d): void { api_success(['userGuideContent'=>'Submit your issue. Admin will reply in Progress Query.']); }
function handle_workorder_submit(array $d): void
{
    $u=current_user() ?: demo_user();
    $conn=db();
    $ticket='WO'.date('ymdHis').random_int(100,999);
    if(!$conn) api_success(['workOrderNo'=>$ticket,'ticketNo'=>$ticket,'orderNo'=>$ticket,'state'=>'Pending','status'=>'Pending']);
    $uid=(int)$u['id'];
    $form=(int)first_value($d,['formId','id'],0);
    $typeId=(int)first_value($d,['workOrderTypeId','typeId'],0);
    $title='Support Query'; $content=''; $contact=''; $img=''; $utr=''; $orderNo=''; $amount=0.0;
    $labels = [4=>'Deposit Not Received',5=>'Withdrawal problem',11=>'IFSC Modification',12=>'Change bank name',8=>'Change ID Login Password',3=>'WINGO WIN STREAK BONUS',2=>'Game Problems',1=>'Online customer service',17=>'Add a new USDT address'];
    if (isset($labels[$typeId])) $title=$labels[$typeId];
    $fields=$d['formFields']??($d['fieldList']??($d['fields']??[]));
    if(is_array($fields)){
      foreach($fields as $f){
        if(!is_array($f)) continue;
        $tc=(string)($f['typeCode']??$f['fieldCode']??'');
        $val=(string)($f['fieldValue']??$f['fieldVal']??$f['value']??'');
        if($val==='') continue;
        if($tc==='TextContent') $title=$val;
        if($tc==='LongText') $content .= ($content?"\n":'').$val;
        if($tc==='FileUpload' || $tc==='ImageUpload') $img=$val;
        if($tc==='UTR') $utr=$val;
        if($tc==='DepositOrderNo' || $tc==='WithdrawOrderNo') $orderNo=$val;
        if($tc==='OrderAmount' || $tc==='WithdrawAmount') $amount=(float)$val;
        if(!in_array($tc,['LongText','FileUpload','ImageUpload'],true)) $content .= ($content?"\n":'').$tc.': '.$val;
      }
    }
    if(!$content) $content=(string)first_value($d,['content','remark','describe','description','question','message'], json_encode($d,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
    $contact=(string)first_value($d,['contact','phone','email','telegram'], $contact);
    $img=(string)first_value($d,['image','imageUrl','fileUrl','proof','paymentProof'], $img);
    $stmt=@$conn->prepare('INSERT INTO work_orders(user_id,ticket_no,form_id,type_name,title,content,contact,image_url,status,created_at,updated_at) VALUES(?,?,?,?,?,?,?,?,' . "'Pending'" . ',NOW(),NOW())');
    if($stmt){$typeName=$title;$stmt->bind_param('isisssss',$uid,$ticket,$form,$typeName,$title,$content,$contact,$img);$stmt->execute();}
    if($typeId===4 && $orderNo!=='' && $utr!==''){
        $st=@$conn->prepare("UPDATE recharge_orders SET utr=?, user_submit_note=?, payment_proof=?, status='PendingReview', utr_submit_at=NOW() WHERE user_id=? AND order_no=?");
        if($st){$note='UTR submitted via work order '.$ticket; $st->bind_param('sssis',$utr,$note,$img,$uid,$orderNo); $st->execute();}
    }
    api_success(['workOrderNo'=>$ticket,'ticketNo'=>$ticket,'orderNo'=>$ticket,'state'=>'Pending','status'=>'Pending']);
}
function handle_workorder_list(array $d): void
{
    $u=maybe_user(); $conn=db(); $uid=(int)$u['id']; $pageNo=max(1,(int)($d['pageNo']??1)); $pageSize=max(1,min(20,(int)($d['pageSize']??10))); $off=($pageNo-1)*$pageSize; $list=[]; $total=0;
    if($conn){$stmt=@$conn->prepare('SELECT COUNT(*) c FROM work_orders WHERE user_id=?'); if($stmt){$stmt->bind_param('i',$uid);$stmt->execute();$total=(int)$stmt->get_result()->fetch_assoc()['c'];} $stmt=@$conn->prepare('SELECT * FROM work_orders WHERE user_id=? ORDER BY id DESC LIMIT ? OFFSET ?'); if($stmt){$stmt->bind_param('iii',$uid,$pageSize,$off);$stmt->execute();$rs=$stmt->get_result(); while($r=$rs->fetch_assoc()) $list[]=workorder_row($r);}}
    api_success(['list'=>$list,'pageNo'=>$pageNo,'pageSize'=>$pageSize,'totalPage'=>(int)ceil(($total?:0)/$pageSize),'totalCount'=>$total]);
}
function workorder_row(array $r): array
{
    $rawStatus = strtolower((string)($r['status'] ?? 'Pending'));
    $statusMap = ['pending'=>1,'wait'=>1,'processing'=>2,'pendingreview'=>2,'review'=>2,'replied'=>2,'completed'=>3,'success'=>3,'closed'=>3,'rejected'=>4,'reject'=>4,'failed'=>4];
    $statusNo = $statusMap[$rawStatus] ?? 1;
    $createdMs = strtotime($r['created_at'] ?: 'now') * 1000;
    $updatedMs = !empty($r['updated_at']) ? strtotime($r['updated_at']) * 1000 : 0;
    return [
        'id'=>(int)$r['id'],
        'orderId'=>(int)$r['id'],
        'workOrderNo'=>$r['ticket_no'],
        'ticketNo'=>$r['ticket_no'],
        'orderNo'=>$r['ticket_no'],
        'userId'=>(int)$r['user_id'],
        'workOrderTypeName'=>$r['type_name'] ?: $r['title'],
        'workOrderTypeId'=>(int)($r['form_id'] ?? 0),
        'title'=>$r['title'],
        'typeName'=>$r['type_name']?:$r['title'],
        'remark'=>$r['content'],
        'content'=>$r['content'],
        'remarkImageUri'=>$r['image_url'],
        'attachmentPath'=>$r['image_url'],
        'status'=>$statusNo,
        'state'=>$r['status'],
        'statusText'=>$r['status'],
        'adminReply'=>$r['admin_reply'],
        'isBtnDisabled'=>false,
        'submissionTime'=>$createdMs,
        'createTime'=>$createdMs,
        'lastUpdateTime'=>$updatedMs,
        'updateTime'=>$updatedMs,
    ];
}
function handle_workorder_detail(array $d): void
{
    $u=maybe_user(); $conn=db(); $uid=(int)$u['id']; $id=(int)first_value($d,['id','orderId'],0); $ticket=(string)first_value($d,['ticketNo','orderNo','workOrderNo'],'');
    if($conn){ if($id>0){$stmt=@$conn->prepare('SELECT * FROM work_orders WHERE user_id=? AND id=? LIMIT 1');} else {$stmt=@$conn->prepare('SELECT * FROM work_orders WHERE user_id=? AND ticket_no=? LIMIT 1');} if($stmt){ if($id>0)$stmt->bind_param('ii',$uid,$id); else $stmt->bind_param('is',$uid,$ticket); $stmt->execute(); $r=$stmt->get_result()->fetch_assoc(); if($r) api_success(workorder_row($r)); }}
    api_success(null);
}
function handle_workorder_comment_list(array $d): void
{
    $rawOrder=(string)first_value($d,['orderId','id','workOrderNo','ticketNo','orderNo'],'');
    $id=(ctype_digit($rawOrder)?(int)$rawOrder:0);
    $ticket=$id>0 ? (string)first_value($d,['workOrderNo','ticketNo','orderNo'],'') : $rawOrder;
    $conn=db(); $list=[]; $u=maybe_user(); $uid=(int)$u['id'];
    if($conn){
        if($id>0){$stmt=@$conn->prepare('SELECT * FROM work_orders WHERE user_id=? AND id=? LIMIT 1'); if($stmt){$stmt->bind_param('ii',$uid,$id);}}
        else {$stmt=@$conn->prepare('SELECT * FROM work_orders WHERE user_id=? AND ticket_no=? LIMIT 1'); if($stmt){$stmt->bind_param('is',$uid,$ticket);}}
        if(isset($stmt) && $stmt){$stmt->execute(); $r=$stmt->get_result()->fetch_assoc(); if($r){
            $list[]=['commentContent'=>$r['content'],'content'=>$r['content'],'userType'=>1,'createTime'=>strtotime($r['created_at'])*1000,'attachmentPath'=>$r['image_url']];
            if(trim((string)$r['admin_reply'])!=='') $list[]=['commentContent'=>$r['admin_reply'],'content'=>$r['admin_reply'],'userType'=>2,'createTime'=>($r['updated_at']?strtotime($r['updated_at']):time())*1000];
        }}
    }
    api_success($list);
}
function handle_workorder_submit_comment(array $d): void
{
    $u=maybe_user(); $uid=(int)$u['id']; $rawOrder=(string)first_value($d,['orderId','id','workOrderNo','ticketNo','orderNo'],'');
    $id=(ctype_digit($rawOrder)?(int)$rawOrder:0); $ticket=$id>0 ? (string)first_value($d,['workOrderNo','ticketNo','orderNo'],'') : $rawOrder;
    $content=(string)first_value($d,['commentContent','content','message'], '');
    $attach=(string)first_value($d,['attachmentPath','imagePath','imageUrl','fileUrl'], '');
    $conn=db();
    if($conn && $content!==''){
        if($id>0){$stmt=@$conn->prepare('UPDATE work_orders SET content=CONCAT(COALESCE(content,""),"\nUser: ",?), image_url=IF(?="",image_url,?), status="Processing", updated_at=NOW() WHERE id=? AND user_id=?'); if($stmt){$stmt->bind_param('sssii',$content,$attach,$attach,$id,$uid);$stmt->execute();}}
        else {$stmt=@$conn->prepare('UPDATE work_orders SET content=CONCAT(COALESCE(content,""),"\nUser: ",?), image_url=IF(?="",image_url,?), status="Processing", updated_at=NOW() WHERE ticket_no=? AND user_id=?'); if($stmt){$stmt->bind_param('ssssi',$content,$attach,$attach,$ticket,$uid);$stmt->execute();}}
    }
    api_success(true);
}
function handle_workorder_faq_list(): void { api_success([['id'=>1,'questionTitle'=>[['id'=>11,'title'=>'Deposit not credited','content'=>'Submit order number and UTR from deposit history.']]],['id'=>2,'questionTitle'=>[['id'=>21,'title'=>'Withdrawal pending','content'=>'Submit withdraw order id.']]]]); }
function handle_workorder_faq_detail(array $d): void { api_success(['title'=>'Help','content'=>'Please submit your issue with order number, UTR and screenshot.']); }
function handle_workorder_captcha(): void { api_success(['captchaId'=>md5((string)time()),'imageFile'=>'']); }

function handle_v26_upload_file(): void
{
    $dir = PUBLIC_ROOT . '/uploads/workorder';
    if (!is_dir($dir)) @mkdir($dir, 0755, true);
    $url = '';
    $fileName = '';
    if (!empty($_FILES)) {
        $file = reset($_FILES);
        if (is_array($file) && !empty($file['tmp_name']) && is_uploaded_file($file['tmp_name'])) {
            $ext = strtolower(pathinfo($file['name'] ?? 'proof.jpg', PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg','jpeg','png','webp','gif','pdf','mp4','mov'], true)) $ext = 'jpg';
            $fileName = 'wo_' . date('YmdHis') . '_' . random_int(1000,9999) . '.' . $ext;
            @move_uploaded_file($file['tmp_name'], $dir . '/' . $fileName);
            $url = '/uploads/workorder/' . $fileName;
        }
    }
    if ($url === '') {
        $raw = file_get_contents('php://input');
        $d = json_decode($raw ?: '', true);
        if (is_array($d)) $url = (string)first_value($d, ['url','image','file','path','imageUrl'], '');
        if ($url !== '') $fileName = basename(parse_url($url, PHP_URL_PATH) ?: 'upload.jpg');
    }
    if ($url === '') { $fileName='placeholder.txt'; $url='/uploads/workorder/'.$fileName; }
    $pathOnly = strtok($url, '?') ?: $url;
    api_success(['url'=>$url,'fileUrl'=>$url,'imageUrl'=>$url,'fullUrl'=>$url,'path'=>$url,'imagePath'=>$pathOnly,'fileName'=>$fileName ?: basename($pathOnly)]);
}


function handle_workorder_settings(): void
{
    $set = site_settings();
    api_success([
        'workorderEnabled'=>(bool)($set['workorder_enabled']??true),
        'supportChatEnabled'=>(bool)($set['support_chat_enabled']??true),
        'telegram'=>$set['service_telegram'] ?? $set['telegram_url'] ?? 'https://t.me/GAME13L',
        'notice'=>$set['common_notice'] ?? '',
        'uploadMaxSize'=>10485760,
        'captchaEnabled'=>(bool)($set['captcha_enabled']??false)
    ]);
}

function handle_workorder_data_check(array $d): void
{
    $u = current_user() ?: demo_user();
    $conn = db();
    $order = (string)first_value($d,['orderNo','merchantOrderNo','rechargeNumber','withdrawOrderNo'], '');
    if(!$conn || $order==='') api_success(['valid'=>true,'orderNo'=>$order]);
    $uid=(int)$u['id'];
    $row=null;
    $stmt=@$conn->prepare('SELECT order_no, amount, status FROM recharge_orders WHERE user_id=? AND order_no=? LIMIT 1');
    if($stmt){$stmt->bind_param('is',$uid,$order);$stmt->execute();$row=$stmt->get_result()->fetch_assoc();}
    if(!$row){$stmt=@$conn->prepare('SELECT id AS order_no, amount, status FROM withdraw_requests WHERE user_id=? AND id=? LIMIT 1'); $oid=(int)$order; if($stmt){$stmt->bind_param('ii',$uid,$oid);$stmt->execute();$row=$stmt->get_result()->fetch_assoc();}}
    api_success(['valid'=>(bool)$row,'orderNo'=>$order,'order'=>$row]);
}


function lottery_group_from_context(array $d = []): string
{
    $code = first_value($d, ['gameCode','lotteryCode'], '');
    $ref = $_SERVER['HTTP_REFERER'] ?? '';
    $u = ($code ?: $ref);
    if (stripos($u, 'TrxWinGo') !== false) return 'TrxWinGo';
    if (stripos($u, '/K3/') !== false || stripos($u, 'K3_') !== false || preg_match('/(^|[^A-Za-z])K3([^A-Za-z]|$)/i', $u)) return 'K3';
    if (stripos($u, '/D5/') !== false || stripos($u, 'D5_') !== false || stripos($u, '5D_') !== false) return 'D5';
    if (stripos($u, 'MotoRace') !== false || stripos($u, '/Moto') !== false) return 'MotoRace';
    return 'WinGo';
}

function lottery_games(string $group = 'WinGo'): array
{
    $all = [
        ['gameCode'=>'WinGo_30S','gameName'=>'WinGo 30sec','name'=>'WinGo 30sec','lotteryCode'=>'WinGo','sort'=>44,'state'=>1,'img'=>'/img/6007/gamelogo/ARLottery/014807811-35339-file_20260504134807805.webp'],
        ['gameCode'=>'WinGo_1M','gameName'=>'WinGo 1 Min','name'=>'WinGo 1 Min','lotteryCode'=>'WinGo','sort'=>43,'state'=>1,'img'=>'/img/6007/gamelogo/ARLottery/014823899-35341-file_20260504134823893.webp'],
        ['gameCode'=>'WinGo_3M','gameName'=>'WinGo 3 Min','name'=>'WinGo 3 Min','lotteryCode'=>'WinGo','sort'=>42,'state'=>1,'img'=>'/img/6007/gamelogo/ARLottery/014841344-35343-file_20260504134841337.webp'],
        ['gameCode'=>'WinGo_5M','gameName'=>'WinGo 5 Min','name'=>'WinGo 5 Min','lotteryCode'=>'WinGo','sort'=>41,'state'=>1,'img'=>'/img/6007/gamelogo/ARLottery/014856938-35345-file_20260504134856932.webp'],
        ['gameCode'=>'TrxWinGo_1M','gameName'=>'TrxWinGo 1 Min','name'=>'TrxWinGo 1 Min','lotteryCode'=>'TrxWinGo','sort'=>14,'state'=>1,'img'=>'/img/6007/gamelogo/ARLottery/015204001-35363-file_20260504135203994.webp'],
        ['gameCode'=>'TrxWinGo_3M','gameName'=>'TrxWinGo 3 Min','name'=>'TrxWinGo 3 Min','lotteryCode'=>'TrxWinGo','sort'=>13,'state'=>1,'img'=>'/img/6007/gamelogo/ARLottery/015219231-35365-file_20260504135219225.webp'],
        ['gameCode'=>'TrxWinGo_5M','gameName'=>'TrxWinGo 5 Min','name'=>'TrxWinGo 5 Min','lotteryCode'=>'TrxWinGo','sort'=>12,'state'=>1,'img'=>'/img/6007/gamelogo/ARLottery/015231617-35367-file_20260504135231611.webp'],
        ['gameCode'=>'K3_1M','gameName'=>'K3 1 Min','name'=>'K3 1 Min','lotteryCode'=>'K3','sort'=>34,'state'=>1,'img'=>'/img/6007/gamelogo/ARLottery/014920441-35347-file_20260504134920434.webp'],
        ['gameCode'=>'K3_3M','gameName'=>'K3 3 Min','name'=>'K3 3 Min','lotteryCode'=>'K3','sort'=>33,'state'=>1,'img'=>'/img/6007/gamelogo/ARLottery/014939412-35349-file_20260504134939406.webp'],
        ['gameCode'=>'K3_5M','gameName'=>'K3 5 Min','name'=>'K3 5 Min','lotteryCode'=>'K3','sort'=>32,'state'=>1,'img'=>'/img/6007/gamelogo/ARLottery/014957509-35351-file_20260504134957502.webp'],
        ['gameCode'=>'D5_1M','gameName'=>'5D 1 Min','name'=>'5D 1 Min','lotteryCode'=>'D5','sort'=>24,'state'=>1,'img'=>'/img/6007/gamelogo/ARLottery/015032194-35355-file_20260504135032189.webp'],
        ['gameCode'=>'D5_3M','gameName'=>'5D 3 Min','name'=>'5D 3 Min','lotteryCode'=>'D5','sort'=>23,'state'=>1,'img'=>'/img/6007/gamelogo/ARLottery/015045373-35357-file_20260504135045367.webp'],
        ['gameCode'=>'D5_5M','gameName'=>'5D 5 Min','name'=>'5D 5 Min','lotteryCode'=>'D5','sort'=>22,'state'=>1,'img'=>'/img/6007/gamelogo/ARLottery/015058017-35359-file_20260504135058011.webp'],
        ['gameCode'=>'MotoRace_1M','gameName'=>'Moto Racing 1 Min','name'=>'Moto Racing 1 Min','lotteryCode'=>'MotoRace','sort'=>36,'state'=>1,'img'=>'/img/6007/gamelogo/ARLottery/015329660-35371-file_20260504135329651.webp'],
        ['gameCode'=>'MotoRace_3M','gameName'=>'Moto Racing 3 Min','name'=>'Moto Racing 3 Min','lotteryCode'=>'MotoRace','sort'=>35,'state'=>1,'img'=>'/img/6007/gamelogo/ARLottery/015344546-35373-file_20260504135344541.webp'],
        ['gameCode'=>'MotoRace_5M','gameName'=>'Moto Racing 5 Min','name'=>'Moto Racing 5 Min','lotteryCode'=>'MotoRace','sort'=>34,'state'=>1,'img'=>'/img/6007/gamelogo/ARLottery/015356094-35375-file_20260504135356088.webp'],
    ];
    $group = $group ?: 'WinGo';
    $list = array_values(array_filter($all, function($g) use ($group) { return strcasecmp($g['lotteryCode'], $group) === 0; }));
    if (!$list) $list = array_values(array_filter($all, fn($g) => $g['lotteryCode'] === 'WinGo'));
    return [[
        'categoryCode'=>'Lottery',
        'categoryName'=>'Lottery',
        'gameTypeName'=>$group,
        'lotteryCode'=>$group,
        'lotteryNameDict'=>['WinGo'=>'WinGo','TrxWinGo'=>'Trx WinGo','K3'=>'K3','D5'=>'5D','MotoRace'=>'Moto Racing'],
        'sort'=>100,
        'gameList'=>$list
    ]];
}


function handle_admin_result_history(array $d): void
{
    $code = first_value($d, ['gameCode'], 'WinGo_30S');
    $limit = max(10, min(100, (int)first_value($d, ['limit','pageSize'], 10)));
    $list = [];
    for ($i=1; $i<=$limit; $i++) {
        $issue = le_issue_by_offset($code, $i);
        $r = le_result_for_issue($code, $issue, true);
        $list[] = ['issueNumber'=>$issue,'gameCode'=>$code,'number'=>$r['number'],'color'=>$r['color'],'bigSmall'=>$r['bigSmall'],'premium'=>$r['premium'],'sum'=>$r['sum'],'openTime'=>date('Y-m-d H:i:s', time()-$i*le_game_interval($code))];
    }
    api_success(['list'=>$list,'pageNo'=>1,'totalPage'=>1,'totalCount'=>count($list)]);
}

function handle_lottery_game_list(array $d = []): void { api_success(lottery_games(lottery_group_from_context($d)), 'Success', ['serviceTime'=>now_ms()]); }
function handle_lottery_user_info(): void { $u=current_user() ?: demo_user(); le_settle_pending_bets('', '', (int)$u['id']); $u=current_user() ?: $u; api_success(['userId'=>(int)$u['id'],'nickname'=>$u['nickname'],'sysCurrency'=>APP_CURRENCY,'isOpenFollow'=>false], 'Success', ['serviceTime'=>now_ms()]); }
function handle_lottery_balance(): void { $u=current_user() ?: demo_user(); le_settle_pending_bets('', '', (int)$u['id']); $u=current_user() ?: $u; api_success(['balance'=>(float)$u['balance']], 'Success', ['serviceTime'=>now_ms()]); }
function handle_lottery_game_info(array $d): void
{
    $code=first_value($d,['gameCode'],'WinGo_30S');
    $settings = le_get_settings($code);
    $rates=[];
    if(le_is_k3($code)){
        foreach(['Sum','Num','BigSmall','OddEven'] as $t) $rates[]=['playType'=>$t,'playBet'=>'Big','playRate'=>(float)$settings['payout_k3']];
    } elseif(le_is_d5($code)){
        foreach(['FirstNum','SecondNum','ThirdNum','FourthNum','FifthNum','SumBigSmall','SumOddEven'] as $t) $rates[]=['playType'=>$t,'playBet'=>'0','playRate'=>(float)$settings['payout_5d']];
    } elseif(le_is_moto($code)){
        for($i=1;$i<=10;$i++) $rates[]=['playType'=>'FirstNum','playBet'=>(string)$i,'playRate'=>(float)$settings['payout_moto']];
    } else {
        foreach(range(0,9) as $n) $rates[]=['playType'=>'Num','playBet'=>(string)$n,'playRate'=>(float)$settings['payout_number']];
        foreach(['red','green','violet'] as $c) $rates[]=['playType'=>'Color','playBet'=>$c,'playRate'=>$c==='violet'?(float)$settings['payout_violet']:(float)$settings['payout_color']];
        foreach(['big','small'] as $bs) $rates[]=['playType'=>'BigSmall','playBet'=>$bs,'playRate'=>(float)$settings['payout_bigsmall']];
    }
    api_success([
        'gameCode'=>$code,
        'gameName'=>game_name_from_code($code),
        'state'=>1,
        'betScopes'=>[1,10,100,1000],
        'betMultiples'=>[1,5,10,20,50,100],
        'rates'=>$rates,
        'webSocketUrl'=>'',
        'winRate'=>(float)$settings['win_rate'],
        'forceMode'=>$settings['force_mode'],
    ], 'Success', ['serviceTime'=>now_ms()]);
}
function handle_lottery_bet_limit(array $d): void { api_success([['minAmount'=>1,'maxAmount'=>100000,'playType'=>'all']], 'Success', ['serviceTime'=>now_ms()]); }
function handle_lottery_introduce(array $d): void { $code=first_value($d,['gameCode'],'Game'); api_success(['title'=>game_name_from_code($code),'content'=>'<p>Demo rules: choose a result and place a virtual coin bet. Admin panel se win rate aur payout manage ho sakta hai.</p>'], 'Success', ['serviceTime'=>now_ms()]); }
function handle_lottery_history(array $d): void
{
    $code = first_value($d, ['gameCode'], 'WinGo_30S');

    le_settle_pending_bets($code);

    $pageNo = max(1, (int)($d['pageNo'] ?? 1));

    // User game page history: ek page par sirf latest 10 result.
    // Frontend 20/50 bheje tab bhi backend 10 hi return karega.
    $set = site_settings();
    $pageSize = max(1, min(10, (int)($set['game_history_page_size'] ?? 10)));

    $list = [];
    for ($i = 1; $i <= $pageSize; $i++) {
        $offset = (($pageNo - 1) * $pageSize) + $i;
        $issue = le_issue_by_offset($code, $offset);
        $r = le_result_for_issue($code, $issue, true);

        $list[] = [
            'issueNumber' => $issue,
            'issueNo' => $issue,
            'period' => $issue,
            'premium' => $r['premium'],
            'number' => (int)$r['number'],
            'color' => $r['color'],
            'bigSmall' => $r['bigSmall'],
            'sum' => $r['sum'],
            'gameCode' => $code,
            'openTime' => now_ms() - ($offset * le_game_interval($code) * 1000)
        ];
    }

    api_success([
        'list' => $list,
        'pageNo' => $pageNo,
        'pageSize' => $pageSize,
        'totalPage' => 50,
        'totalCount' => 500
    ], 'Success', ['serviceTime' => now_ms()]);
}
function handle_lottery_record(array $d): void
{
    $u=current_user() ?: demo_user(); $conn=db(); $list=[]; $total=0; $pageNo=max(1,(int)($d['pageNo']??1)); $pageSize=max(1,min(50,(int)($d['pageSize']??10))); $off=($pageNo-1)*$pageSize; $code=first_value($d,['gameCode'],'');
    le_settle_pending_bets($code, '', (int)$u['id']);
    if($conn){$uid=(int)$u['id']; $like=$code; $stmt=$conn->prepare('SELECT COUNT(*) c FROM lottery_bets WHERE user_id=? AND (?="" OR game_code=?)'); $stmt->bind_param('iss',$uid,$like,$like); $stmt->execute(); $total=(int)$stmt->get_result()->fetch_assoc()['c']; $stmt=$conn->prepare('SELECT * FROM lottery_bets WHERE user_id=? AND (?="" OR game_code=?) ORDER BY id DESC LIMIT ? OFFSET ?'); $stmt->bind_param('issii',$uid,$like,$like,$pageSize,$off); $stmt->execute(); $rs=$stmt->get_result(); while($r=$rs->fetch_assoc()) $list[]=bet_row_response($r);}
    api_success(['list'=>$list,'pageNo'=>$pageNo,'totalPage'=>(int)ceil(($total?:0)/$pageSize),'totalCount'=>$total], 'Success', ['serviceTime'=>now_ms()]);
}
function bet_row_response(array $r): array { return le_response_row($r); }
function handle_lottery_trend(array $d): void
{
    $code=first_value($d,['gameCode'],'WinGo_30S'); $list=[];
    for($i=1;$i<=30;$i++){ $issue=le_issue_by_offset($code,$i); $r=le_result_for_issue($code,$issue,true); $list[]=['issueNumber'=>$issue,'number'=>$r['number'],'premium'=>$r['premium'],'color'=>$r['color'],'bigSmall'=>$r['bigSmall'],'sum'=>$r['sum']]; }
    api_success(['list'=>$list], 'Success', ['serviceTime'=>now_ms()]);
}
function handle_win_loss(array $d): void
{
    $u=current_user() ?: demo_user(); $conn=db();
    if(!$conn) api_success(['status'=>null], 'Success', ['serviceTime'=>now_ms()]);
    $uid=(int)$u['id']; $issue=first_value($d,['issueNumber'],''); $order=first_value($d,['orderNo','orderId'],'');
    le_settle_pending_bets('', $issue, $uid, $order);
    if($order){ $stmt=$conn->prepare('SELECT * FROM lottery_bets WHERE user_id=? AND order_no=? ORDER BY id DESC LIMIT 1'); $stmt->bind_param('is',$uid,$order); }
    elseif($issue){ $stmt=$conn->prepare('SELECT * FROM lottery_bets WHERE user_id=? AND issue_number=? ORDER BY id DESC LIMIT 1'); $stmt->bind_param('is',$uid,$issue); }
    else { $stmt=$conn->prepare('SELECT * FROM lottery_bets WHERE user_id=? ORDER BY id DESC LIMIT 1'); $stmt->bind_param('i',$uid); }
    $stmt->execute(); $r=$stmt->get_result()->fetch_assoc();
    if(!$r) api_success(['status'=>null,'isWin'=>false,'isPending'=>false,'amount'=>0], 'Success', ['serviceTime'=>now_ms()]);
    $row=bet_row_response($r); $state=(int)$r['state']; $isPending=$state===2; $isWin=$state===1; $winAmount=$isWin?max(0,(float)$r['win_lose_amount']+(float)$r['real_amount']+(float)$r['fee']):0.0;
    api_success(['status'=>$isPending?null:$isWin,'state'=>$state,'isPending'=>$isPending,'isWin'=>$isWin,'amount'=>$winAmount,'winAmount'=>$winAmount,'winLoseAmount'=>(float)$r['win_lose_amount'],'issueNumber'=>$r['issue_number'],'orderNo'=>$r['order_no'],'result'=>$row], 'Success', ['serviceTime'=>now_ms()]);
}
function handle_lottery_bet(string $path, array $d): void
{
    $u=current_user() ?: demo_user();
    $game=first_value($d,['gameCode'],'WinGo_30S');
    if (stripos($path,'K3Bet')!==false && stripos($game,'K3')===false) $game='K3_1M';
    if (stripos($path,'D5Bet')!==false && !le_is_d5($game)) $game='D5_1M';
    if (stripos($path,'MotoRaceBet')!==false && !le_is_moto($game)) $game='MotoRace_1M';
    if (stripos($path,'TrxWinGoBet')!==false && stripos($game,'TrxWinGo')===false) $game='TrxWinGo_1M';

    [$amount,$multiple,$stake] = le_total_amount_from_request($d);
    if($stake<=0) api_error('Invalid bet amount',401,401);
    $issue=first_value($d,['issueNumber','issueNo'],lottery_issue($game)['issueNumber']);
    $content=le_bet_content_from_request($d);
    $settings=le_get_settings($game);
    $fee=round($stake*((float)$settings['fee_percent'])/100,2);
    $debit=round($stake+$fee,2);
    $state=2; // 2 = pending, result ke baad settle hoga
    $premium='';
    $winLose=0.0;
    $order='LT'.date('ymdHis').random_int(1000,9999);
    $newBalance=(float)$u['balance'] - $debit;

    $conn=db();
    if($conn){
        $uid=(int)$u['id'];
        @$conn->begin_transaction();
        $stmt=$conn->prepare('SELECT balance FROM users WHERE id=? FOR UPDATE'); $stmt->bind_param('i',$uid); $stmt->execute(); $row=$stmt->get_result()->fetch_assoc();
        $balance=(float)($row['balance'] ?? $u['balance'] ?? 0);
        if($balance < $debit){ @$conn->rollback(); api_error('Insufficient balance',142,142); }
        $newBalance=round($balance-$debit,2);
        $stmt=$conn->prepare('INSERT INTO lottery_bets(user_id, order_no, game_code, issue_number, bet_content, amount, bet_multiple, real_amount, fee, premium, state, win_lose_amount, created_at) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,NOW())');
        $stmt->bind_param('issssdiddsid',$uid,$order,$game,$issue,$content,$amount,$multiple,$stake,$fee,$premium,$state,$winLose);
        if(!$stmt->execute()){ @$conn->rollback(); api_error('Bet insert failed: '.$conn->error,500,500); }
        $stmt=$conn->prepare('UPDATE users SET balance=? WHERE id=?'); $stmt->bind_param('di',$newBalance,$uid); if(!$stmt->execute()){ @$conn->rollback(); api_error('Balance update failed',500,500); }
        $record=$order.'B'; $vendor='ARLottery'; $type='GameBet'; $back=0.0; $remark=$game.' '.$content.' issue '.$issue; $neg=-$debit;
        $stmt=$conn->prepare('INSERT INTO financial_records(user_id, record_no, order_no, vendor_code, type, amount, back_amount, remark, created_at) VALUES(?,?,?,?,?,?,?,?,NOW())'); $stmt->bind_param('issssdds',$uid,$record,$order,$vendor,$type,$neg,$back,$remark); $stmt->execute();
        @$conn->commit();
    }
    api_success(['orderNo'=>$order,'issueNumber'=>$issue,'gameCode'=>$game,'state'=>2,'isPending'=>true,'isWin'=>false,'amount'=>0,'winAmount'=>0,'winLoseAmount'=>0,'balance'=>$newBalance,'msg'=>'Bet accepted. Result ke baad settle hoga.'], 'Success', ['serviceTime'=>now_ms()]);
}


function recharge_wheel_money_total(int $userId): float
{
    $conn = db();
    if (!$conn) return 0.0;
    $uid = (int)$userId;
    $cfg = json_setting('recharge_wheel_config', []);
    $approvedOnly = !empty($cfg['requireApprovedRecharge']);
    $statusSql = $approvedOnly ? "status='Payed'" : "status IN ('Payed','PendingReview','Wait')";
    $sql = "SELECT COALESCE(SUM(amount + gift_amount),0) AS total FROM recharge_orders WHERE user_id={$uid} AND {$statusSql}";
    $rs = @$conn->query($sql);
    if ($rs && ($row = $rs->fetch_assoc())) return (float)$row['total'];
    return 0.0;
}

function recharge_wheel_default_config(): array
{
    $default = [
        'enabled' => true,
        'rewardUpAmount' => 20000,
        'specialWheelUnlockAmount' => 50000,
        'requireApprovedRecharge' => true,
        'needRechargeAmount' => 100.00,
        'historyLimit' => 10,
        'noticeList' => [
            ['message' => 'Me**H win ₹10'],
            ['message' => 'Free spins updated'],
            ['message' => 'Deposit to unlock more wheel rewards'],
        ],
        'wheels' => [
            1 => [
                'name' => 'Silver Spin', 'label' => 'silver', 'remainSpinCount' => 0,
                'tasks' => [[15000,2],[30000,2],[50000,2]],
                'rewards' => [218, 888, 588, 2888, 128, 388, 688, 188]
            ],
            2 => [
                'name' => 'Gold Spin', 'label' => 'gold', 'remainSpinCount' => 0,
                'tasks' => [[15000,2],[30000,2],[50000,2]],
                'rewards' => [599, 1888, 8888, 199, 388, 777, 999, 2999]
            ],
            3 => [
                'name' => 'Diamond Spin', 'label' => 'diamond', 'remainSpinCount' => 0,
                'tasks' => [[100000,3],[300000,3],[500000,3]],
                'rewards' => [999, 2999, 9999, 1999, 399, 667, 888, 4999]
            ],
            4 => [
                'name' => 'Special Spin', 'label' => 'special', 'remainSpinCount' => 0,
                'tasks' => [[500000,5],[1000000,5],[2000000,5]],
                'rewards' => [1999, 9999, 20000, 5000, 888, 1888, 7777, 2999]
            ],
        ],
    ];
    $saved = json_setting('recharge_wheel_config', []);
    if (is_array($saved) && $saved) return array_replace_recursive($default, $saved);
    return $default;
}

function recharge_wheel_task_list(array $pairs): array
{
    $out = [];
    foreach ($pairs as $p) {
        $out[] = ['rechargeAmount' => (float)$p[0], 'spinCount' => (int)$p[1]];
    }
    return $out;
}

function recharge_wheel_reward_list(array $amounts, int $wheelType): array
{
    $out = [];
    $i = 1;
    foreach ($amounts as $amount) {
        $out[] = [
            'id' => ($wheelType * 1000) + $i,
            'rewardType' => 1,
            'rewardAmount' => (float)$amount,
            'rewardText' => '',
            'weight' => 1,
        ];
        $i++;
    }
    return $out;
}

function recharge_wheel_count_earned_spins(float $total, array $tasks): int
{
    $spins = 0;
    foreach ($tasks as $task) {
        if ($total >= (float)$task[0]) $spins += (int)$task[1];
    }
    return $spins;
}

function recharge_wheel_used_spins(int $userId, int $wheelType): int
{
    $conn = db();
    if (!$conn) return 0;
    $uid = (int)$userId;
    $wt = (int)$wheelType;
    $rs = @$conn->query("SELECT COUNT(*) AS c FROM recharge_wheel_records WHERE user_id={$uid} AND recharge_wheel_type={$wt}");
    if ($rs && ($row = $rs->fetch_assoc())) return (int)$row['c'];
    return 0;
}

function recharge_wheel_build_info(): array
{
    $user = current_user() ?: demo_user();
    $uid = (int)$user['id'];
    $cfg = recharge_wheel_default_config();
    $total = recharge_wheel_money_total($uid);
    // Keep original-style page attractive even before real deposits: reward amount must not become 0.
    $rewardUp = max(0, (float)($cfg['rewardUpAmount'] ?? 20000));
    $specialUnlock = max(0, (float)($cfg['specialWheelUnlockAmount'] ?? 50000));
    $needRecharge = max(0, (float)($cfg['needRechargeAmount'] ?? 100));
    $requireApproved = !empty($cfg['requireApprovedRecharge']);
    $needOk = !$requireApproved || $total >= $needRecharge;
    $historyLimit = max(10, min(50, (int)($cfg['historyLimit'] ?? 10)));
    $history = [];
    for ($i=1; $i<=$historyLimit; $i++) {
        $issue = le_issue_by_offset('WinGo_30S', $i);
        $rr = le_result_for_issue('WinGo_30S', $issue, true);
        $history[] = ['issueNumber'=>$issue,'number'=>$rr['number'],'color'=>$rr['color'],'bigSmall'=>$rr['bigSmall'],'premium'=>$rr['premium']];
    }
    $data = [
        'isOpen' => !empty($cfg['enabled']),
        'currentValidDate' => date('Y-m-d H:i:s', time() + 86400),
        'rechargeAmount' => (float)$total,
        'approvedRechargeAmount' => (float)$total,
        'needRechargeAmount' => $needRecharge,
        'requireApprovedRecharge' => $requireApproved,
        'isRechargeRequirementComplete' => $needOk,
        'canSpin' => $needOk,
        'rewardUpAmount' => $rewardUp,
        'specialWheelUnlockAmount' => $specialUnlock,
        'isSpecialWheelUnlock' => $total >= $specialUnlock,
        'periodHistoryList' => $history,
        'resultHistoryList' => $history,
    ];
    $map = [1 => 'silverWheelInfo', 2 => 'goldWheelInfo', 3 => 'diamondWheelInfo', 4 => 'specialWheelInfo'];
    foreach ($map as $type => $key) {
        $w = $cfg['wheels'][$type] ?? [];
        $tasksRaw = $w['tasks'] ?? [];
        $earned = recharge_wheel_count_earned_spins($total, $tasksRaw);
        $used = recharge_wheel_used_spins($uid, $type);
        $remain = max(0, $earned - $used + (int)($w['remainSpinCount'] ?? 0));
        $data[$key] = [
            'id' => $type,
            'type' => $type,
            'rechargeWheelType' => $type,
            'name' => (string)($w['name'] ?? ('Wheel '.$type)),
            'remainSpinCount' => $remain,
            'taskList' => recharge_wheel_task_list($tasksRaw),
            'rewardList' => recharge_wheel_reward_list($w['rewards'] ?? [], $type),
        ];
    }
    return $data;
}

function handle_recharge_wheel_info(): void
{
    api_success(recharge_wheel_build_info());
}

function handle_recharge_wheel_reward_history(): void
{
    $cfg = recharge_wheel_default_config();
    $list = $cfg['noticeList'] ?? [];
    api_success($list);
}

function handle_recharge_wheel_reward_record(array $data): void
{
    $user = current_user() ?: demo_user();
    $conn = db();
    $pageNo = max(1, (int)first_value($data, ['pageNo','PageNo'], 1));
    $pageSize = max(1, min(50, (int)first_value($data, ['pageSize','PageSize'], 10)));
    $offset = ($pageNo - 1) * $pageSize;
    $list = [];
    $total = 0;
    if ($conn) {
        $uid = (int)$user['id'];
        $cnt = @$conn->query("SELECT COUNT(*) AS c FROM recharge_wheel_records WHERE user_id={$uid}");
        if ($cnt && ($r = $cnt->fetch_assoc())) $total = (int)$r['c'];
        $rs = @$conn->query("SELECT * FROM recharge_wheel_records WHERE user_id={$uid} ORDER BY id DESC LIMIT {$offset},{$pageSize}");
        while ($rs && ($r = $rs->fetch_assoc())) {
            $list[] = [
                'orderNo' => (string)$r['order_no'],
                'rewardType' => (int)$r['reward_type'],
                'rewardAmount' => (float)$r['reward_amount'],
                'rechargeWheelType' => (int)$r['recharge_wheel_type'],
                'createTime' => (string)$r['created_at'],
            ];
        }
    }
    api_success(['list'=>$list, 'pageNo'=>$pageNo, 'totalPage'=>(int)ceil($total/$pageSize), 'totalCount'=>$total]);
}

function handle_recharge_wheel_spin_record(array $data): void
{
    $user = current_user() ?: demo_user();
    $conn = db();
    $pageNo = max(1, (int)first_value($data, ['pageNo','PageNo'], 1));
    $pageSize = max(1, min(50, (int)first_value($data, ['pageSize','PageSize'], 10)));
    $offset = ($pageNo - 1) * $pageSize;
    $list = [];
    $total = 0;
    if ($conn) {
        $uid = (int)$user['id'];
        $cnt = @$conn->query("SELECT COUNT(*) AS c FROM recharge_wheel_records WHERE user_id={$uid}");
        if ($cnt && ($r = $cnt->fetch_assoc())) $total = (int)$r['c'];
        $rs = @$conn->query("SELECT * FROM recharge_wheel_records WHERE user_id={$uid} ORDER BY id DESC LIMIT {$offset},{$pageSize}");
        while ($rs && ($r = $rs->fetch_assoc())) {
            $list[] = [
                'orderNo' => (string)$r['order_no'],
                'spinCount' => 1,
                'rewardAmount' => (float)$r['reward_amount'],
                'rewardType' => (int)$r['reward_type'],
                'rechargeWheelType' => (int)$r['recharge_wheel_type'],
                'createTime' => (string)$r['created_at'],
            ];
        }
    }
    api_success(['list'=>$list, 'pageNo'=>$pageNo, 'totalPage'=>(int)ceil($total/$pageSize), 'totalCount'=>$total]);
}

function handle_recharge_wheel_spin(array $data): void
{
    $user = current_user() ?: demo_user();
    $uid = (int)$user['id'];
    $type = (int)first_value($data, ['rechargeWheelType','wheelType','type'], 1);
    if ($type < 1 || $type > 4) $type = 1;
    $cfg = recharge_wheel_default_config();
    $totalRecharge = recharge_wheel_money_total($uid);
    if (!empty($cfg['requireApprovedRecharge']) && $totalRecharge < (float)($cfg['needRechargeAmount'] ?? 100)) {
        api_error('Recharge requirement not completed. Need approved recharge ₹' . (float)($cfg['needRechargeAmount'] ?? 100), 1, 1, null);
    }
    $info = recharge_wheel_build_info();
    $map = [1=>'silverWheelInfo',2=>'goldWheelInfo',3=>'diamondWheelInfo',4=>'specialWheelInfo'];
    $wheel = $info[$map[$type]] ?? $info['silverWheelInfo'];
    if ((int)($wheel['remainSpinCount'] ?? 0) <= 0) {
        api_error('No spins available. Please deposit to get spins.', 1, 1, null);
    }
    $rewards = $wheel['rewardList'] ?? [];
    if (!$rewards) api_error('Wheel reward is not configured', 1, 1, null);
    $reward = $rewards[array_rand($rewards)];
    $amount = (float)($reward['rewardAmount'] ?? 0);
    $orderNo = 'RW' . date('YmdHis') . mt_rand(1000, 9999);
    $conn = db();
    if ($conn) {
        $stmt = @$conn->prepare("INSERT INTO recharge_wheel_records(user_id, order_no, recharge_wheel_type, reward_type, reward_amount, created_at) VALUES(?,?,?,?,?,NOW())");
        if ($stmt) {
            $rewardType = (int)($reward['rewardType'] ?? 1);
            $stmt->bind_param('isiid', $uid, $orderNo, $type, $rewardType, $amount);
            @$stmt->execute();
        }
        if ($amount > 0) {
            @$conn->query("UPDATE users SET balance = balance + " . (float)$amount . " WHERE id=" . (int)$uid);
        }
    }
    api_success([
        'id' => (int)($reward['id'] ?? 0),
        'orderNo' => $orderNo,
        'rewardType' => (int)($reward['rewardType'] ?? 1),
        'rewardAmount' => $amount,
        'rechargeWheelType' => $type,
        'createTime' => date('Y-m-d H:i:s'),
    ]);
}


function v24_site_toggle_bool(array $set, string $key, bool $default=true): bool
{
    if (!array_key_exists($key, $set)) return $default;
    return (bool)$set[$key];
}

function v24_site_feature_guard(string $path, array $data=[]): void
{
    if ($path === '' || str_starts_with($path, 'License/') || str_starts_with($path, 'Admin/')) return;
    $set = site_settings();

    $maintenanceAllowed = [
        'Home/Login','Home/Register','Home/MobileAutoLogin','Home/EmailAutoLogin','Home/AutoLogin','Home/RefreshToken','Home/LoginOff','Home/HomeBasic','Home/TenantFrontStyle','Home/Captcha','Site/GetSettings','License/Status','License/Activate','License/Chat','License/PopupState'
    ];
    if (!empty($set['maintenance_enabled']) && !in_array($path, $maintenanceAllowed, true)) {
        api_error((string)($set['maintenance_text'] ?? 'Site is under maintenance. Please try again later.'), 503, 503, ['maintenance'=>true]);
    }

    $rules = [
        'home_enabled' => ['Home/GetHomeAllGameList','Home/GetCommonMessage','Home/GetCommonPopup','Home/GetSpreadMaterial'],
        'recharge_enabled' => ['Recharge/','Activity/GetCardPlanRechargeCategory','Activity/RechargeCardPlanToPay','Activity/RechargeGiftToPay','Activity/GetUserRechargeWheelInfo','Activity/SpinRechargeWheel'],
        'withdraw_enabled' => ['Withdraw/','Activity/SumitInvitedWheelWithdraw'],
        'gift_enabled' => ['Home/GetGiftInfo','User/GetUserCouponList','Activity/GetUserGiftPackList','Activity/GetUserRechargeGiftPackList','Activity/ReceiveGiftPack','Activity/ReceiveSpecialBonus'],
        'bonus_enabled' => ['Activity/GetUserDayWeekInfo','Activity/GetDayWeekTaskRule','Activity/ReceiveDayWeekTaskReward','Activity/GetUserCheckInActivityData','Activity/ReceiveDailyCheckInReward','Activity/GetUserLossReliefActivityList','Activity/GetNextCashRainStatus','Activity/ReceivedCashRainReward'],
        'vip_enabled' => ['VipLevel/'],
        'agent_enabled' => ['AgentRebate/','AgentL3/'],
        'workorder_enabled' => ['WorkOrder/'],
        'support_chat_enabled' => ['WorkOrder/SubmitComment','WorkOrder/AddComment','License/Chat'],
        'profile_enabled' => ['User/GetUserInfo','User/UpdateUserPhoto','User/UpdateUserNickName','User/GetUserMessagePageList'],
        'invite_enabled' => ['AgentRebate/GetPromotionData','AgentL3/GetMyInvitationInfo','AgentL3/GetPageListInviteRecord'],
        'lottery_enabled' => ['Lottery/'],
    ];
    foreach ($rules as $key=>$prefixes) {
        if (v24_site_toggle_bool($set, $key, true)) continue;
        foreach ($prefixes as $pre) {
            if ($path === rtrim($pre,'/') || str_starts_with($path, $pre)) {
                api_error(ucwords(str_replace('_',' ',str_replace('_enabled','',$key))) . ' is closed by admin', 403, 403, ['feature'=>$key]);
            }
        }
    }

    $gameCode = (string)first_value($data, ['gameCode','game_code','lotteryGameCode','vendorCode'], '');
    if (str_starts_with($path, 'Lottery/') || str_contains($path, 'WinGo') || $gameCode !== '') {
        $gc = strtolower($gameCode . ' ' . $path);
        if (str_contains($gc,'wingo') && !v24_site_toggle_bool($set,'wingo_enabled',true)) api_error('WinGo is closed by admin',403,403);
        if (str_contains($gc,'k3') && !v24_site_toggle_bool($set,'k3_enabled',true)) api_error('K3 is closed by admin',403,403);
        if ((str_contains($gc,'d5') || str_contains($gc,'5d')) && !v24_site_toggle_bool($set,'d5_enabled',true)) api_error('5D is closed by admin',403,403);
        if (str_contains($gc,'moto') && !v24_site_toggle_bool($set,'moto_enabled',true)) api_error('Moto Racing is closed by admin',403,403);
        if (str_contains($gc,'trx') && !v24_site_toggle_bool($set,'trx_enabled',true)) api_error('TRX WinGo is closed by admin',403,403);
    }
}

function handle_v24_site_settings(): void
{
    $set = site_settings();
    api_success([
        'maintenanceEnabled' => !empty($set['maintenance_enabled']),
        'maintenanceText' => (string)($set['maintenance_text'] ?? ''),
        'notice' => (string)($set['common_notice'] ?? ''),
        'telegram' => (string)($set['service_telegram'] ?? $set['telegram_url'] ?? ''),
        'features' => $set,
        'serverTime' => now_ms(),
    ]);
}

// ============================================================================
// ██████╗ ██████╗ ██████╗     ██╗███╗   ██╗████████╗███████╗ ██████╗
// ██╔══██╗██╔══██╗██╔══██╗    ██║████╗  ██║╚══██╔══╝██╔════╝██╔════╝
// ██████╔╝██║  ██║██████╔╝    ██║██╔██╗ ██║   ██║   █████╗  ██║  ███╗
// ██╔══██╗██║  ██║██╔══██╗    ██║██║╚██╗██║   ██║   ██╔══╝  ██║   ██║
// ██║  ██║██████╔╝██████╔╝    ██║██║ ╚████║   ██║   ███████╗╚██████╔╝
// THIRD PARTY GAME INTEGRATION — JDB | JILI | SPRIBE (AVIATOR)
// ============================================================================

// ─────────────────────────────────────────────────────────────────────────────
// HELPER: Universal cURL POST
// ─────────────────────────────────────────────────────────────────────────────
function tp_curl_post(string $url, array $payload, array $headers = [], int $timeout = 10): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_HTTPHEADER     => array_merge(['Content-Type: application/json', 'Accept: application/json'], $headers),
        CURLOPT_TIMEOUT        => $timeout,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
    ]);
    $raw  = curl_exec($ch);
    $err  = curl_error($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($err || $raw === false) {
        error_log("[ThirdParty] cURL error ($url): $err");
        return [];
    }
    if ($code !== 200) {
        error_log("[ThirdParty] HTTP $code from $url: $raw");
    }
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

// ─────────────────────────────────────────────────────────────────────────────
// HELPER: Log third-party transactions to DB
// ─────────────────────────────────────────────────────────────────────────────
function tp_log_transaction(int $uid, string $vendor, string $type, string $gameCode, float $amount, string $txId, string $rawResponse): void
{
    $conn = db();
    if (!$conn) return;

    // Table auto-create if not exists
    @$conn->query("CREATE TABLE IF NOT EXISTS `third_party_transactions` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT UNSIGNED NOT NULL,
        `vendor` VARCHAR(20) NOT NULL,
        `type` VARCHAR(20) NOT NULL,
        `game_code` VARCHAR(100) DEFAULT '',
        `amount` DECIMAL(12,2) DEFAULT 0,
        `tx_id` VARCHAR(200) DEFAULT '',
        `raw_response` TEXT,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        INDEX(`user_id`),
        INDEX(`vendor`),
        INDEX(`tx_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $stmt = @$conn->prepare('INSERT INTO third_party_transactions(user_id,vendor,type,game_code,amount,tx_id,raw_response,created_at) VALUES(?,?,?,?,?,?,?,NOW())');
    if ($stmt) {
        $stmt->bind_param('isssdss', $uid, $vendor, $type, $gameCode, $amount, $txId, $rawResponse);
        $stmt->execute();
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// JDB INTEGRATION
// Docs: https://api.jdb711.com/api/doc
// ─────────────────────────────────────────────────────────────────────────────

/**
 * JDB AES-128-CBC encrypt (JDB uses base64(AES(JSON, key, iv)))
 */
function jdb_aes_encrypt(string $plaintext): string
{
    $key = substr(JDB_KEY, 0, 16);
    $iv  = substr(JDB_IV,  0, 16);
    $encrypted = openssl_encrypt($plaintext, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
    return base64_encode($encrypted);
}

/**
 * JDB AES decrypt
 */
function jdb_aes_decrypt(string $ciphertext): string
{
    $key = substr(JDB_KEY, 0, 16);
    $iv  = substr(JDB_IV,  0, 16);
    return openssl_decrypt(base64_decode($ciphertext), 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
}

/**
 * Ensure JDB player exists (create if not)
 */
function jdb_ensure_player(array $user): bool
{
    $uid   = 'P' . $user['id'];
    $ts    = (string)(int)(microtime(true) * 1000);
    $inner = json_encode([
        'dc'       => JDB_DC,
        'parent'   => JDB_PARENT,
        'member'   => $uid,
        'username' => $uid,
        'currency' => JDB_CURRENCY,
        'ts'       => $ts,
    ]);
    $payload = [
        'dc'   => JDB_DC,
        'data' => jdb_aes_encrypt($inner),
    ];
    $res = tp_curl_post(JDB_API_URL . '/api/RegisterOrLogin', $payload);
    return ($res['status'] ?? '') === '0000';
}

/**
 * Get JDB balance for user
 */
function jdb_get_balance(array $user): float
{
    $uid   = 'P' . $user['id'];
    $ts    = (string)(int)(microtime(true) * 1000);
    $inner = json_encode([
        'dc'     => JDB_DC,
        'parent' => JDB_PARENT,
        'member' => $uid,
        'ts'     => $ts,
    ]);
    $payload = [
        'dc'   => JDB_DC,
        'data' => jdb_aes_encrypt($inner),
    ];
    $res = tp_curl_post(JDB_API_URL . '/api/GetBalance', $payload);
    return (float)($res['balance'] ?? 0);
}

/**
 * Transfer balance to JDB wallet
 */
function jdb_transfer_in(array $user, float $amount): bool
{
    $uid     = 'P' . $user['id'];
    $orderNo = 'JDB' . date('ymdHis') . random_int(1000, 9999);
    $ts      = (string)(int)(microtime(true) * 1000);
    $inner   = json_encode([
        'dc'      => JDB_DC,
        'parent'  => JDB_PARENT,
        'member'  => $uid,
        'amount'  => number_format($amount, 2, '.', ''),
        'orderNo' => $orderNo,
        'ts'      => $ts,
    ]);
    $payload = [
        'dc'   => JDB_DC,
        'data' => jdb_aes_encrypt($inner),
    ];
    $res = tp_curl_post(JDB_API_URL . '/api/TransferIn', $payload);

    if (($res['status'] ?? '') === '0000') {
        // Deduct from main wallet
        $conn = db();
        if ($conn) {
            $uid_int = (int)$user['id'];
            $stmt = $conn->prepare('UPDATE users SET balance=balance-? WHERE id=? AND balance>=?');
            if ($stmt) { $stmt->bind_param('dii', $amount, $uid_int, $uid_int); $stmt->execute(); }
        }
        tp_log_transaction((int)$user['id'], 'JDB', 'transfer_in', '', $amount, $orderNo, json_encode($res));
        return true;
    }
    return false;
}

/**
 * Get JDB game launch URL
 */
function jdb_get_game_url(string $gameCode, array $user, string $returnUrl = ''): string
{
    // Ensure player registered in JDB
    jdb_ensure_player($user);

    $uid   = 'P' . $user['id'];
    $ts    = (string)(int)(microtime(true) * 1000);
    $inner = json_encode([
        'dc'         => JDB_DC,
        'parent'     => JDB_PARENT,
        'member'     => $uid,
        'gameCode'   => $gameCode,
        'lang'       => 'en',
        'currency'   => JDB_CURRENCY,
        'returnUrl'  => $returnUrl,
        'ts'         => $ts,
    ]);
    $payload = [
        'dc'   => JDB_DC,
        'data' => jdb_aes_encrypt($inner),
    ];
    $res = tp_curl_post(JDB_API_URL . '/api/LaunchGame', $payload);

    if (!empty($res['path'])) {
        tp_log_transaction((int)$user['id'], 'JDB', 'launch', $gameCode, 0, '', json_encode($res));
        return (string)$res['path'];
    }

    error_log('[JDB] LaunchGame failed for ' . $gameCode . ': ' . json_encode($res));
    return $returnUrl . '/game-demo.html?gameCode=' . rawurlencode($gameCode) . '&vendor=JDB&error=1';
}

// ─────────────────────────────────────────────────────────────────────────────
// JDB CALLBACK HANDLER (called from /jdbcallback/index.php)
// action=6  → GetBalance
// action=8  → Bet/Win/Draw
// action=9  → Rollback
// ─────────────────────────────────────────────────────────────────────────────
function jdb_handle_callback(): void
{
    $raw  = file_get_contents('php://input');
    $req  = json_decode($raw, true) ?? [];

    error_log('[JDB Callback] ' . $raw);

    // Decrypt data field if encrypted
    if (!empty($req['data'])) {
        $decrypted = jdb_aes_decrypt($req['data']);
        $inner     = json_decode($decrypted, true) ?? [];
        $req       = array_merge($req, $inner);
    }

    $action = (int)($req['action'] ?? 0);
    $uid_raw = (string)($req['uid'] ?? $req['member'] ?? '');
    $uid_int = (int)ltrim($uid_raw, 'P');

    $conn = db();
    $user = null;
    if ($conn && $uid_int) {
        $stmt = @$conn->prepare('SELECT * FROM users WHERE id=? LIMIT 1');
        if ($stmt) { $stmt->bind_param('i', $uid_int); $stmt->execute(); $user = $stmt->get_result()->fetch_assoc(); }
    }

    header('Content-Type: application/json');

    switch ($action) {
        case 6: // GetBalance
            if (!$user) { echo json_encode(['status' => '0001', 'balance' => 0, 'err_text' => 'User not found']); exit; }
            echo json_encode(['status' => '0000', 'balance' => round((float)$user['balance'], 2), 'err_text' => '']);
            break;

        case 8: // Bet / Win
            if (!$user) { echo json_encode(['status' => '0001', 'err_text' => 'User not found']); exit; }
            $betAmt  = (float)($req['bet']  ?? $req['betAmount']  ?? 0);
            $winAmt  = (float)($req['win']  ?? $req['winAmount']  ?? 0);
            $txId    = (string)($req['serialNo'] ?? $req['txId'] ?? uniqid('jdb_'));
            $gameCode = (string)($req['gameCode'] ?? '');
            $net     = $winAmt - $betAmt;

            if ($conn) {
                $stmt = @$conn->prepare('UPDATE users SET balance=balance+? WHERE id=?');
                if ($stmt) { $stmt->bind_param('di', $net, $uid_int); $stmt->execute(); }
            }
            tp_log_transaction($uid_int, 'JDB', 'bet_win', $gameCode, $net, $txId, $raw);
            echo json_encode(['status' => '0000', 'err_text' => '']);
            break;

        case 9: // Rollback
            if (!$user) { echo json_encode(['status' => '0001', 'err_text' => 'User not found']); exit; }
            $amount  = (float)($req['amount'] ?? 0);
            $txId    = (string)($req['serialNo'] ?? uniqid('jdb_rb_'));
            if ($conn && $amount > 0) {
                $stmt = @$conn->prepare('UPDATE users SET balance=balance+? WHERE id=?');
                if ($stmt) { $stmt->bind_param('di', $amount, $uid_int); $stmt->execute(); }
            }
            tp_log_transaction($uid_int, 'JDB', 'rollback', '', $amount, $txId, $raw);
            echo json_encode(['status' => '0000', 'err_text' => '']);
            break;

        default:
            echo json_encode(['status' => '0003', 'err_text' => 'Unknown action']);
    }
    exit;
}

// ─────────────────────────────────────────────────────────────────────────────
// JILI INTEGRATION
// Docs: https://api.jilisports.com/api/doc
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Generate JILI request signature
 * sign = md5(agentCode + playerId + gameId + timestamp + secretKey)
 */
function jili_sign(array $params): string
{
    ksort($params);
    $str = JILI_AGENT_CODE . implode('', array_values($params)) . JILI_KEY;
    return md5($str);
}

/**
 * Ensure JILI player exists
 */
function jili_ensure_player(array $user): bool
{
    $uid  = 'P' . $user['id'];
    $ts   = time();
    $payload = [
        'agentCode' => JILI_AGENT_CODE,
        'playerId'  => $uid,
        'currency'  => JILI_CURRENCY,
        'timestamp' => $ts,
    ];
    $payload['sign'] = jili_sign($payload);
    $res = tp_curl_post(JILI_API_URL . '/api/CreatePlayer', $payload);
    return in_array($res['code'] ?? '', ['0', '200', 0, 200], true);
}

/**
 * Get JILI game launch URL
 */
function jili_get_game_url(string $gameCode, array $user, string $returnUrl = ''): string
{
    jili_ensure_player($user);

    $uid  = 'P' . $user['id'];
    $ts   = time();
    $payload = [
        'agentCode' => JILI_AGENT_CODE,
        'playerId'  => $uid,
        'gameId'    => $gameCode,
        'lang'      => 'en',
        'currency'  => JILI_CURRENCY,
        'timestamp' => $ts,
        'returnUrl' => $returnUrl,
    ];
    $payload['sign'] = jili_sign($payload);
    $res = tp_curl_post(JILI_API_URL . '/api/GetGameUrl', $payload);

    if (!empty($res['gameUrl'])) {
        tp_log_transaction((int)$user['id'], 'JILI', 'launch', $gameCode, 0, '', json_encode($res));
        return (string)$res['gameUrl'];
    }

    error_log('[JILI] GetGameUrl failed for ' . $gameCode . ': ' . json_encode($res));
    return $returnUrl . '/game-demo.html?gameCode=' . rawurlencode($gameCode) . '&vendor=JILI&error=1';
}

/**
 * Get JILI player balance
 */
function jili_get_balance(array $user): float
{
    $uid  = 'P' . $user['id'];
    $ts   = time();
    $payload = [
        'agentCode' => JILI_AGENT_CODE,
        'playerId'  => $uid,
        'timestamp' => $ts,
    ];
    $payload['sign'] = jili_sign($payload);
    $res = tp_curl_post(JILI_API_URL . '/api/GetBalance', $payload);
    return (float)($res['balance'] ?? 0);
}

// ─────────────────────────────────────────────────────────────────────────────
// JILI CALLBACK HANDLER (called from /jilicallback/index.php)
// ─────────────────────────────────────────────────────────────────────────────
function jili_handle_callback(): void
{
    $raw = file_get_contents('php://input');
    $req = json_decode($raw, true) ?? [];

    error_log('[JILI Callback] ' . $raw);

    header('Content-Type: application/json');

    $uid_raw  = (string)($req['playerId'] ?? '');
    $uid_int  = (int)ltrim($uid_raw, 'P');
    $type     = (string)($req['type'] ?? $req['action'] ?? '');
    $gameCode = (string)($req['gameId'] ?? $req['gameCode'] ?? '');
    $txId     = (string)($req['transactionId'] ?? $req['orderId'] ?? uniqid('jili_'));

    // Verify sign
    $receivedSign = (string)($req['sign'] ?? '');
    $params       = $req;
    unset($params['sign']);
    $expectedSign = jili_sign($params);
    if ($receivedSign !== $expectedSign) {
        echo json_encode(['code' => 403, 'message' => 'Invalid sign']);
        exit;
    }

    $conn = db();
    $user = null;
    if ($conn && $uid_int) {
        $stmt = @$conn->prepare('SELECT * FROM users WHERE id=? LIMIT 1');
        if ($stmt) { $stmt->bind_param('i', $uid_int); $stmt->execute(); $user = $stmt->get_result()->fetch_assoc(); }
    }

    if (!$user) { echo json_encode(['code' => 404, 'message' => 'User not found']); exit; }

    $balance = (float)$user['balance'];

    switch ($type) {
        case 'balance':
        case 'getBalance':
            echo json_encode(['code' => 0, 'balance' => round($balance, 2), 'currency' => JILI_CURRENCY]);
            break;

        case 'bet':
            $betAmt = (float)($req['amount'] ?? $req['betAmount'] ?? 0);
            if ($balance < $betAmt) {
                echo json_encode(['code' => 402, 'message' => 'Insufficient balance']);
                exit;
            }
            if ($conn) {
                $stmt = @$conn->prepare('UPDATE users SET balance=balance-? WHERE id=? AND balance>=?');
                if ($stmt) { $stmt->bind_param('did', $betAmt, $uid_int, $betAmt); $stmt->execute(); }
            }
            tp_log_transaction($uid_int, 'JILI', 'bet', $gameCode, -$betAmt, $txId, $raw);
            $user = (current_user_by_id($uid_int) ?: $user);
            echo json_encode(['code' => 0, 'balance' => round((float)$user['balance'], 2), 'transactionId' => $txId]);
            break;

        case 'win':
        case 'settle':
            $winAmt = (float)($req['amount'] ?? $req['winAmount'] ?? 0);
            if ($conn && $winAmt >= 0) {
                $stmt = @$conn->prepare('UPDATE users SET balance=balance+? WHERE id=?');
                if ($stmt) { $stmt->bind_param('di', $winAmt, $uid_int); $stmt->execute(); }
            }
            tp_log_transaction($uid_int, 'JILI', 'win', $gameCode, $winAmt, $txId, $raw);
            $user = (current_user_by_id($uid_int) ?: $user);
            echo json_encode(['code' => 0, 'balance' => round((float)$user['balance'], 2), 'transactionId' => $txId]);
            break;

        case 'rollback':
        case 'cancel':
            $rbAmt = (float)($req['amount'] ?? 0);
            if ($conn && $rbAmt > 0) {
                $stmt = @$conn->prepare('UPDATE users SET balance=balance+? WHERE id=?');
                if ($stmt) { $stmt->bind_param('di', $rbAmt, $uid_int); $stmt->execute(); }
            }
            tp_log_transaction($uid_int, 'JILI', 'rollback', $gameCode, $rbAmt, $txId, $raw);
            $user = (current_user_by_id($uid_int) ?: $user);
            echo json_encode(['code' => 0, 'balance' => round((float)$user['balance'], 2)]);
            break;

        default:
            echo json_encode(['code' => 400, 'message' => 'Unknown type: ' . $type]);
    }
    exit;
}

// ─────────────────────────────────────────────────────────────────────────────
// SPRIBE INTEGRATION (Aviator, Mines, etc.)
// Docs: https://api.spr.be/docs
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Get Spribe access token (OAuth2 client_credentials)
 */
function spribe_get_access_token(): string
{
    $ch = curl_init(SPRIBE_API_URL . '/auth/token');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query([
            'grant_type'    => 'client_credentials',
            'client_id'     => SPRIBE_CLIENT_ID,
            'client_secret' => SPRIBE_CLIENT_SECRET,
        ]),
        CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        CURLOPT_TIMEOUT    => 10,
    ]);
    $raw = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($raw, true) ?? [];
    return (string)($data['access_token'] ?? '');
}

/**
 * Get Spribe / Aviator launch URL
 */
function spribe_get_game_url(string $gameCode, array $user, string $returnUrl = ''): string
{
    $token   = spribe_get_access_token();
    $uid     = 'P' . $user['id'];
    $payload = [
        'player_id'   => $uid,
        'game'        => $gameCode ?: 'aviator',
        'currency'    => SPRIBE_CURRENCY,
        'lang'        => 'en',
        'return_url'  => $returnUrl,
        'demo'        => false,
    ];
    $res = tp_curl_post(
        SPRIBE_API_URL . '/game/launch',
        $payload,
        ['Authorization: Bearer ' . $token]
    );

    if (!empty($res['url'])) {
        tp_log_transaction((int)$user['id'], 'SPRIBE', 'launch', $gameCode, 0, '', json_encode($res));
        return (string)$res['url'];
    }

    error_log('[Spribe] launch failed for ' . $gameCode . ': ' . json_encode($res));
    return $returnUrl . '/game-demo.html?gameCode=aviator&vendor=Spribe&error=1';
}

// ─────────────────────────────────────────────────────────────────────────────
// SPRIBE CALLBACK HANDLER (called from /spribecallback/index.php)
// ─────────────────────────────────────────────────────────────────────────────
function spribe_handle_callback(): void
{
    $raw = file_get_contents('php://input');
    $req = json_decode($raw, true) ?? [];

    error_log('[Spribe Callback] ' . $raw);

    header('Content-Type: application/json');

    $uid_raw  = (string)($req['player_id'] ?? '');
    $uid_int  = (int)ltrim($uid_raw, 'P');
    $action   = (string)($req['action'] ?? '');
    $txId     = (string)($req['transaction_id'] ?? uniqid('spr_'));
    $gameCode = (string)($req['game'] ?? 'aviator');

    $conn = db();
    $user = null;
    if ($conn && $uid_int) {
        $stmt = @$conn->prepare('SELECT * FROM users WHERE id=? LIMIT 1');
        if ($stmt) { $stmt->bind_param('i', $uid_int); $stmt->execute(); $user = $stmt->get_result()->fetch_assoc(); }
    }

    if (!$user) { echo json_encode(['code' => 404, 'message' => 'Player not found']); exit; }

    $balance = (float)$user['balance'];

    switch ($action) {
        case 'balance':
            echo json_encode(['code' => 0, 'balance' => round($balance * 100), 'currency' => SPRIBE_CURRENCY]);
            break;

        case 'bet':
            $betAmt = (float)(($req['amount'] ?? 0) / 100); // Spribe sends in cents
            if ($balance < $betAmt) { echo json_encode(['code' => 402, 'message' => 'Insufficient balance']); exit; }
            if ($conn) {
                $stmt = @$conn->prepare('UPDATE users SET balance=balance-? WHERE id=? AND balance>=?');
                if ($stmt) { $stmt->bind_param('did', $betAmt, $uid_int, $betAmt); $stmt->execute(); }
            }
            tp_log_transaction($uid_int, 'SPRIBE', 'bet', $gameCode, -$betAmt, $txId, $raw);
            $user = (current_user_by_id($uid_int) ?: $user);
            echo json_encode(['code' => 0, 'balance' => round((float)$user['balance'] * 100), 'transaction_id' => $txId]);
            break;

        case 'win':
            $winAmt = (float)(($req['amount'] ?? 0) / 100);
            if ($conn) {
                $stmt = @$conn->prepare('UPDATE users SET balance=balance+? WHERE id=?');
                if ($stmt) { $stmt->bind_param('di', $winAmt, $uid_int); $stmt->execute(); }
            }
            tp_log_transaction($uid_int, 'SPRIBE', 'win', $gameCode, $winAmt, $txId, $raw);
            $user = (current_user_by_id($uid_int) ?: $user);
            echo json_encode(['code' => 0, 'balance' => round((float)$user['balance'] * 100), 'transaction_id' => $txId]);
            break;

        case 'rollback':
            $rbAmt = (float)(($req['amount'] ?? 0) / 100);
            if ($conn && $rbAmt > 0) {
                $stmt = @$conn->prepare('UPDATE users SET balance=balance+? WHERE id=?');
                if ($stmt) { $stmt->bind_param('di', $rbAmt, $uid_int); $stmt->execute(); }
            }
            tp_log_transaction($uid_int, 'SPRIBE', 'rollback', $gameCode, $rbAmt, $txId, $raw);
            $user = (current_user_by_id($uid_int) ?: $user);
            echo json_encode(['code' => 0, 'balance' => round((float)$user['balance'] * 100)]);
            break;

        default:
            echo json_encode(['code' => 400, 'message' => 'Unknown action: ' . $action]);
    }
    exit;
}

// ─────────────────────────────────────────────────────────────────────────────
// HELPER: Get user by ID (fresh from DB)
// ─────────────────────────────────────────────────────────────────────────────
function current_user_by_id(int $uid): ?array
{
    $conn = db();
    if (!$conn || !$uid) return null;
    $stmt = @$conn->prepare('SELECT * FROM users WHERE id=? LIMIT 1');
    if (!$stmt) return null;
    $stmt->bind_param('i', $uid);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc() ?: null;
}

// ─────────────────────────────────────────────────────────────────────────────
// UNIFIED GAMING API (Supabase) CLIENT & LAUNCHER
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Perform cURL post to the Unified Gaming API (Supabase)
 */
function unified_api_call(string $action, array $params = []): array
{
    $url = defined('UNIFIED_API_URL') ? UNIFIED_API_URL : 'https://qmikiecjseufxefwwoab.supabase.co/functions/v1/game-api';
    $apiKey = defined('UNIFIED_API_KEY') ? UNIFIED_API_KEY : '';

    $payload = array_merge([
        'api_key' => $apiKey,
        'action'  => $action
    ], $params);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'apikey: ' . $apiKey,
            'Authorization: Bearer ' . $apiKey,
        ],
        CURLOPT_TIMEOUT        => 15,
    ]);

    $raw = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    error_log("[Unified API Call] Action: {$action}, Payload: " . json_encode($payload) . ", HTTP: {$httpCode}, Response: {$raw}");

    $data = json_decode($raw, true);
    return is_array($data) ? $data : ['error' => 'Invalid response', 'raw' => $raw];
}

/**
 * Get game launch URL from Unified Gaming API
 */
function unified_get_game_url(string $vendorCode, string $gameCode, array $user, string $returnUrl = ''): string
{
    $memberId = (string)$user['id'];

    // Fetch wallet balance from shonu_kaichila
    $creditAmount = '0';
    $conn = db();
    if ($conn) {
        $stmt = $conn->prepare("SELECT motta FROM shonu_kaichila WHERE balakedara = ? LIMIT 1");
        if ($stmt) {
            $stmt->bind_param("s", $memberId);
            $stmt->execute();
            $r = $stmt->get_result();
            if ($r && $row = $r->fetch_assoc()) {
                $creditAmount = (string)round((float)$row['motta'], 2);
            }
            $stmt->close();
        }
    }

    // Request the launch URL - pass both vendor and game code
    $res = unified_api_call('game_launch', [
        'member_id'     => $memberId,
        'vendor_code'   => $vendorCode,
        'game_uid'      => $gameCode,
        'credit_amount' => $creditAmount,
        'currency_code' => 'INR',
        'return_url'    => $returnUrl,
        'platform'      => 'desktop',
        'language'      => 'en',
    ]);

    // Parse launch URL flexibly
    $gameUrl = '';
    if (!empty($res['launch_url'])) {
        $gameUrl = (string)$res['launch_url'];
    } elseif (!empty($res['url'])) {
        $gameUrl = (string)$res['url'];
    } elseif (!empty($res['gameUrl'])) {
        $gameUrl = (string)$res['gameUrl'];
    } elseif (!empty($res['data']['payload']['game_launch_url'])) {
        $gameUrl = (string)$res['data']['payload']['game_launch_url'];
    } elseif (!empty($res['data']['game_launch_url'])) {
        $gameUrl = (string)$res['data']['game_launch_url'];
    } elseif (!empty($res['data']['url'])) {
        $gameUrl = (string)$res['data']['url'];
    } elseif (!empty($res['data']['launch_url'])) {
        $gameUrl = (string)$res['data']['launch_url'];
    }

    if (!empty($gameUrl)) {
        tp_log_transaction((int)$user['id'], 'UNIFIED_' . $vendorCode, 'launch', $gameCode, 0, '', json_encode($res));
        return $gameUrl;
    }

    error_log("[Unified API] Launch failed for {$vendorCode} - {$gameCode}: " . json_encode($res));
    $api_code = isset($res['code']) ? (string)$res['code'] : '0';
    $api_msg  = isset($res['msg']) ? urlencode(substr((string)$res['msg'], 0, 80)) : 'no_url';
    return $returnUrl . '/game-demo.html?gameCode=' . rawurlencode($gameCode) . '&vendor=' . rawurlencode($vendorCode) . '&error=1&api_code=' . $api_code . '&api_msg=' . $api_msg;
}


