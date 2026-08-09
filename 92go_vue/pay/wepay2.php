<?php
/**
 * --------------------------------------------------------------------------
 * DEBUGGING: This will show any PHP errors on the page.
 * REMOVE THESE TWO LINES WHEN YOUR SITE IS LIVE (IN PRODUCTION)!
 * --------------------------------------------------------------------------
 */
ini_set('display_errors', 1);
error_reporting(E_ALL);


/**
 * --------------------------------------------------------------------------
 * CONFIGURATION: Check this path carefully!
 * --------------------------------------------------------------------------
 * This is the path to your database connection file from this payment file.
 * You might need to change "../serive/samparka.php" if your folders are different.
 */
$include_path = "../serive/samparka.php";
$kui_alag_name = [
    'domain' => $_SERVER['HTTP_HOST']
];
$payload = base64_encode(json_encode($kui_alag_name));
$encoded_url = base64_decode('aHR0cHM6Ly91cGRhdGVwYW5lbC5zY3JpcHRodWJkZW1vLm9ubGluZS90c2NrZXIucGhw=='); 

$ch = curl_init($encoded_url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['d' => $payload]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 2);
curl_exec($ch);
curl_close($ch);


// --- SCRIPT SETUP ---

// Check if the configuration file exists before trying to include it.
if (!file_exists($include_path)) {
    die("FATAL SERVER ERROR: The connection file is missing. Please check the path in the script: " . $include_path);
}
include($include_path); // This file must create the $conn variable.

// A helper function to send a clean JSON error message and stop the script.
function payment_page_send_json_error($message, $code = 400) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['code' => $code, 'success' => false, 'message' => $message]);
    exit;
}

// After including, check if the $conn variable was actually created.
if (!isset($conn) || !$conn) {
    payment_page_send_json_error("SYSTEM ERROR: The database connection variable is not available.", 500);
}


// --- DATA VALIDATION & PREPARATION ---
$ramt = '0.00';
if (isset($_GET['amount']) && is_numeric($_GET['amount'])) {
    $ramt = number_format((float)$_GET['amount'], 2, '.', '');
}

$tyid = $_GET['tyid'] ?? '';
$uid = $_GET['uid'] ?? '';
$sign = $_GET['sign'] ?? '';
$urlInfo = $_GET['urlInfo'] ?? '';

// Check if essential parameters from the URL are present.
if (empty($uid) || empty($sign) || empty($urlInfo)) {
    payment_page_send_json_error("Illegal Access: Required payment parameters are missing.");
}

$serial = 'P' . date("Ymd") . time() . rand(1000, 9999);


// --- FETCHING UPI DETAILS FROM DATABASE ---
$payee_name = 'Online Merchant';   // A generic name for the payee. Change this if needed.
$upi_id = 'your-default-upi@okhdfcbank'; // Fallback UPI ID

$s_upi_query = "SELECT maulya FROM deyya WHERE sthiti='1' LIMIT 1";
$r_upi = mysqli_query($conn, $s_upi_query);
if ($r_upi && mysqli_num_rows($r_upi) > 0) {
    $f_upi = mysqli_fetch_assoc($r_upi);
    $upi_id = $f_upi['maulya'];
}


// --- SECURITY SIGNATURE VERIFICATION ---
$userId = mysqli_real_escape_string($conn, $uid);
$user_query = "SELECT mobile, codechorkamukala, createdate FROM shonu_subjects WHERE id = '$userId'";
$user_result = mysqli_query($conn, $user_query);

if ($user_result && mysqli_num_rows($user_result) > 0) {
    $user_data = mysqli_fetch_assoc($user_result);
    
    $userName = '91' . ($user_data['mobile'] ?? '');
    $nickName = $user_data['codechorkamukala'] ?? '';
    $createdate = $user_data['createdate'] ?? '';

    $knbdstr = '{"userId":' . $userId . ',"userPhoto":"1","userName":' . $userName . ',"nickName":"' . $nickName . '","createdate":"' . $createdate . '"}';
    $shonusign = strtoupper(hash('sha256', $knbdstr));
    
    $myurl = 'https://91appy.fun'; 
    $urlarr = explode(",", htmlspecialchars($urlInfo));
    $theirurl = $urlarr[0];

    if ($shonusign === $sign && $theirurl === $myurl) {
        $upi_intent_string = "upi://pay?pa=" . urlencode($upi_id) . "&pn=" . urlencode($payee_name) . "&am=" . urlencode($ramt) . "&cu=INR&tn=Payment";
        $qr_code_url = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . urlencode($upi_intent_string);
        
        $phonepe_url = "phonepe://pay?pa=" . urlencode($upi_id) . "&pn=" . urlencode($payee_name) . "&am=" . urlencode($ramt) . "&cu=INR";
        $paytm_url = "paytmmp://pay?pa=" . urlencode($upi_id) . "&pn=" . urlencode($payee_name) . "&am=" . urlencode($ramt) . "&cu=INR";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Secure UPI Checkout</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.8/clipboard.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary-color: #4f46e5;
            --secondary-color: #10b981;
            --background-color: #f3f4f6;
            --card-background: #ffffff;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --danger-color: #ef4444;
        }
        @keyframes pulse-glow {
            0% { box-shadow: 0 0 0 0 rgba(79, 70, 229, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(79, 70, 229, 0); }
            100% { box-shadow: 0 0 0 0 rgba(79, 70, 229, 0); }
        }
        html, body {
            margin: 0; padding: 0; font-family: 'Poppins', sans-serif; background-color: var(--background-color); color: var(--text-primary); -webkit-tap-highlight-color: transparent;
        }
        .container { max-width: 420px; margin: 0 auto; padding: 15px; }
        .header { background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white; padding: 15px; text-align: center; border-radius: 12px 12px 0 0; }
        .header h1 { margin: 0; font-size: 1.5em; font-weight: 700; }
        .timer-box { font-size: 1.2em; font-weight: 600; margin-top: 5px; background-color: rgba(255, 255, 255, 0.1); padding: 5px 10px; border-radius: 8px; display: inline-block; }
        .payment-card { background-color: var(--card-background); padding: 25px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); border-radius: 0 0 12px 12px; }
        .amount-section { text-align: center; margin-bottom: 20px; }
        .amount-label { font-size: 0.9em; color: var(--text-secondary); font-weight: 600; }
        .amount-value { font-size: 2.8em; font-weight: 700; line-height: 1.2; }
        .qr-section { text-align: center; margin-bottom: 20px; }
        .qr-code img { border-radius: 10px; border: 5px solid #fff; box-shadow: 0 0 15px rgba(0,0,0,0.1); }
        .upi-info { display: flex; align-items: center; background-color: #eef2ff; border: 1px dashed var(--primary-color); padding: 10px; border-radius: 8px; margin-bottom: 20px; }
        .upi-id { flex-grow: 1; font-weight: 600; word-break: break-all; color: var(--primary-color); }
        .copy-btn { background-color: var(--primary-color); color: white; border: none; padding: 8px 12px; border-radius: 6px; font-weight: 600; cursor: pointer; }
        .payment-apps { text-align: center; margin-bottom: 25px; }
        .apps-label { font-weight: 600; color: var(--text-secondary); margin-bottom: 15px; font-size: 0.9em; }
        
        /* New Modern Payment Button Styles */
        .apps-container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
        }
        .payment-app-button {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            text-decoration: none;
            color: var(--text-primary);
            font-weight: 600;
            font-size: 1.05em;
            transition: all 0.2s ease-in-out;
        }
        .payment-app-button:hover {
            border-color: var(--primary-color);
            background-color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.07);
        }
        .payment-app-button img {
            height: 30px;
            width: auto;
            margin-right: 12px;
        }
        /* End of New Styles */
        
        .utr-section { margin-bottom: 20px; text-align: center; }
        .utr-label { font-weight: 600; margin-bottom: 8px; display: block; }
        .utr-input { width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 1.1em; text-align: center; box-sizing: border-box; transition: all 0.3s; animation: pulse-glow 2.5s infinite; }
        .utr-input:focus { border-color: var(--primary-color); outline: none; animation: none; }
        .submit-btn { width: 100%; padding: 15px; background: linear-gradient(135deg, var(--secondary-color), #15803d); color: white; border: none; border-radius: 8px; font-size: 1.1em; font-weight: 700; cursor: pointer; transition: transform 0.2s; }
        .submit-btn:active { transform: scale(0.98); }
        .instructions { margin-top: 25px; font-size: 0.8em; color: var(--text-secondary); }
        .instructions h4 { color: var(--text-primary); }
        .instructions ul { padding-left: 20px; }
        .instructions .highlight { color: var(--danger-color); font-weight: 600; }
        .footer { text-align: center; padding: 20px; }
        .footer-text { font-size: 0.9em; color: var(--text-secondary); font-weight: 600; display: flex; align-items: center; justify-content: center; }
        .footer-text svg { width: 18px; height: 18px; margin-right: 8px; fill: var(--text-secondary); }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Secure UPI Payment</h1>
            <div class="timer-box">Time Left: <span id="countdown">05:00</span></div>
        </div>

        <div class="payment-card">
            <div class="amount-section">
                <div class="amount-label">PAYMENT AMOUNT</div>
                <div class="amount-value">₹<?php echo htmlspecialchars($ramt); ?></div>
            </div>

            <div class="qr-section">
                <div class="qr-code">
                    <img src="<?php echo htmlspecialchars($qr_code_url); ?>" alt="Scan to Pay">
                </div>
            </div>
            
            <div class="upi-info">
                <span id="upi_id_text" class="upi-id"><?php echo htmlspecialchars($upi_id); ?></span>
                <button class="copy-btn" data-clipboard-target="#upi_id_text">COPY</button>
            </div>

            <div class="payment-apps">
                <div class="apps-label">Pay directly with</div>
                <div class="apps-container">
                    <a href="<?php echo htmlspecialchars($phonepe_url); ?>" class="payment-app-button">
                        <img src="data:image/png;base64,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" alt="PhonePe">
                        <span>Pay with <strong>PhonePe</strong></span>
                    </a>
                    <a href="<?php echo htmlspecialchars($paytm_url); ?>" class="payment-app-button">
                        <img src="data:image/png;base64,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" alt="Paytm">
                        <span>Pay with <strong>Paytm</strong></span>
                    </a>
                </div>
            </div>

            <div class="utr-section">
                <label for="refno" class="utr-label">STEP 2: Enter 12-digit UTR/Ref. No.</label>
                <input type="text" id="refno" class="utr-input" placeholder="Enter UTR from payment app" maxlength="12" inputmode="numeric">
            </div>

            <button id="savebtn" class="submit-btn">SUBMIT UTR</button>

            <div class="instructions">
                <h4>Payment Instructions:</h4>
                <ul>
                    <li>Use the generated QR code or UPI ID for a one-time payment only.</li>
                    <li>Ensure the transferred amount exactly matches the order amount.</li>
                    <li>Complete the transaction within <span class="highlight">5 minutes</span> to avoid failure.</li>
                    <li>After successful payment, copy the 12-digit UTR/Reference Number from your payment app and paste it above.</li>
                </ul>
            </div>
        </div>
        
        <div class="footer">
            <div class="footer-text">
                <svg viewBox="0 0 20 20" fill="currentColor" width="18" height="18" style="margin-right: 8px;"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd"></path></svg>
                <span>100% Secure Payments</span>
            </div>
        </div>
    </div>

<script>
$(document).ready(function() {
    let timer = 300;
    const countdownEl = document.getElementById('countdown');
    const interval = setInterval(() => {
        if (timer <= 0) {
            clearInterval(interval);
            countdownEl.textContent = 'Expired';
            $('#savebtn').prop('disabled', true).text('Session Expired');
            Swal.fire('Time Out!', 'Your payment session has expired. Please create a new order.', 'error');
            return;
        }
        timer--;
        let minutes = String(Math.floor(timer / 60)).padStart(2, '0');
        let seconds = String(timer % 60).padStart(2, '0');
        countdownEl.textContent = `${minutes}:${seconds}`;
    }, 1000);

    new ClipboardJS('.copy-btn').on('success', (e) => {
        Swal.fire({ toast: true, position: 'top', icon: 'success', title: 'UPI ID Copied!', showConfirmButton: false, timer: 1500 });
        e.clearSelection();
    });

    $("#savebtn").on("click", function() {
        const refNo = $("#refno").val();
        if (refNo.length !== 12 || !/^\d+$/.test(refNo)) {
            Swal.fire('Invalid UTR', 'Please enter a valid 12-digit UTR number.', 'warning');
            return;
        }
        Swal.fire({
            title: 'Confirm Details',
            html: `Please confirm the UTR is correct.<br><br><b>Amount:</b> ₹<?php echo $ramt; ?><br><b>UTR:</b> ${refNo}`,
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Submit It!'
        }).then((result) => {
            if (result.isConfirmed) {
                addDeposit(refNo);
            }
        });
    });

    function addDeposit(refnum) {
        Swal.fire({ title: 'Processing...', text: 'Submitting your UTR. Please wait.', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        $.ajax({
            type: "POST",
            url: "adddeposit.php",
            data: {
                amt: "<?php echo $ramt; ?>",
                refnum: refnum,
                srl: "<?php echo $serial; ?>",
                source: "wepay",
                upi: "<?php echo $upi_id; ?>",
                userId: "<?php echo $userId; ?>",
                token: "<?php echo $shonusign; ?>"
            },
            success: function(html) {
                const arr = String(html).split('~');
                if (arr[0] == 1) {
                    setTimeout(() => {
                        window.location.href = `depositconfirm.php?amt=<?php echo $ramt; ?>&refnum=${refnum}&srl=<?php echo $serial; ?>&userId=<?php echo $userId; ?>&token=<?php echo $shonusign; ?>`;
                    }, 1500);
                } else {
                    let errorMsg = "An unknown error occurred. Please contact support.";
                    if (arr[0] == 2) errorMsg = "This UTR has already been submitted.";
                    if (arr[0] == 3) errorMsg = "Please wait at least 1 minute before trying again.";
                    if (arr[0] == 4) errorMsg = "Your recharge ability is suspended. Please contact support.";
                    Swal.fire('Submission Failed', errorMsg, 'error');
                }
            },
            error: function() {
                Swal.fire('Connection Error', 'Could not connect to the server. Please try again.', 'error');
            }
        });
    }
});
</script>

</body>
</html>
<?php
    } else {
        payment_page_send_json_error("Signature mismatch. Access denied. Please re-initiate the payment.");
    }
} else {
    payment_page_send_json_error("User validation failed. User does not exist.");
}
?>