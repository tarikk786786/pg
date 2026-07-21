<?php
// Dark Elite Theme
$vpa = $userdata['vpa'] ?? 'UPI ID';
$merchant_name = $userdata['name'] ?? 'Verified Business';
$amount = $txnamount ?? '0.00';
$qr_url = "upi://pay?pa={$vpa}&pn=" . urlencode($merchant_name) . "&am={$amount}&cu=INR";
$qr_image = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . urlencode($qr_url);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dark Payment</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #000000;
            color: #ffffff;
            margin: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
        }
        .app-container {
            width: 100%;
            max-width: 400px;
            background-color: #111111;
            display: flex;
            flex-direction: column;
            position: relative;
        }
        .top-bar {
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .upi-logo {
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .close-btn {
            background: #ff3b30;
            color: white;
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
        }
        .main-content {
            padding: 20px;
            text-align: center;
            flex: 1;
        }
        .paying-text {
            color: #8e8e93;
            font-size: 14px;
            margin-bottom: 4px;
        }
        .merchant-name {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 30px;
        }
        .qr-section {
            background: rgba(255, 255, 255, 0.05);
            padding: 30px;
            border-radius: 20px;
            margin-bottom: 30px;
        }
        .qr-wrapper {
            background: white;
            padding: 10px;
            border-radius: 12px;
            display: inline-block;
            margin-bottom: 20px;
        }
        .qr-wrapper img {
            width: 200px;
            height: 200px;
            display: block;
        }
        .loader-text {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            color: #0a84ff;
            font-size: 14px;
            font-weight: 500;
        }
        .loader {
            border: 2px solid rgba(10, 132, 255, 0.2);
            border-left-color: #0a84ff;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .bottom-actions {
            padding: 20px;
            background: #1c1c1e;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
        }
        .btn-neon {
            background: transparent;
            color: #0a84ff;
            border: 1px solid #0a84ff;
            width: 100%;
            padding: 16px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 12px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-neon:hover {
            background: rgba(10, 132, 255, 0.1);
        }
        .btn-solid {
            background: #0a84ff;
            color: white;
            border: none;
            width: 100%;
            padding: 16px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <div class="top-bar">
            <div class="upi-logo"><i class="fas fa-bolt" style="color:#0a84ff;"></i> UPI</div>
            <button class="close-btn"><i class="fas fa-times"></i></button>
        </div>
        
        <div class="main-content">
            <div class="paying-text">Paying to</div>
            <div class="merchant-name"><?php echo htmlspecialchars($merchant_name); ?></div>
            
            <div class="qr-section">
                <div class="qr-wrapper">
                    <img src="<?php echo $qr_image; ?>" alt="QR Code">
                </div>
                <div class="loader-text">
                    <div class="loader"></div>
                    Checking payment status...
                </div>
            </div>
            
            <div style="font-size:36px; font-weight:700; margin-bottom:20px;">₹<?php echo htmlspecialchars($amount); ?></div>
        </div>

        <div class="bottom-actions">
            <button class="btn-neon"><i class="fas fa-share-alt me-2"></i> Share QR on UPI</button>
            <a href="<?php echo $qr_url; ?>" style="text-decoration:none;"><button class="btn-solid">Pay via Any App</button></a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        var paymentProcessed = false;
        var interval;

        function startTimer(duration, display) {
            var timer = duration, minutes, seconds;
            interval = setInterval(function () {
                minutes = parseInt(timer / 60, 10);
                seconds = parseInt(timer % 60, 10);

                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;

                if(display) {
                    display.textContent = minutes + ":" + seconds;
                }

                if (--timer < 0) {
                    clearInterval(interval);
                    Swal.fire({
                        title: 'Session Expired',
                        text: 'This checkout page has expired. Please create a new payment request.',
                        icon: 'error'
                    });
                }
            }, 1000);
        }

        function check() {
            if (paymentProcessed || !interval) {
                clearInterval(interval); 
                return;
            }

            $.ajax({
                type: 'post',
                url: '../payment_status.php',
                data: { 
                    order_id: '<?php echo $order_id ?>',
                    byte_order_status: '<?php echo isset($cxrkalwaremark) ? $cxrkalwaremark : ""; ?>'
                },
                dataType: 'text',
                success: function (data) {
                    if (data && (data.trim() === 'success' || data.includes('success'))) {
                        paymentProcessed = true;
                        Swal.fire({
                            title: 'Payment Received Successfully ✅',
                            text: 'Please wait, redirecting...',
                            icon: 'success',
                            showConfirmButton: false,
                            allowOutsideClick: false
                        });
                        setTimeout(function() {
                            window.location.href = "<?php echo $redirect_url ?>";
                        }, 2000);
                       
                    } else if (data === 'FAILURE' || data === 'FAILED') {
                        paymentProcessed = true;
                        Swal.fire({
                            title: 'Payment Failed',
                            icon: 'error'
                        }).then(() => {
                            window.location.href = "<?php echo $redirect_url ?>";
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.log('AJAX Error:', status, error);
                }
            });
        }

        window.onload = function () {
            var duration = <?php echo $remaining_seconds ?? 300; ?>;
            var displayMob = document.querySelector('#timeout');
            var displayPc = document.querySelector('.desktop-timer');
            var displayFloat = document.querySelector('.timer-box');
            var displayTarget = displayMob || displayPc || displayFloat;
            startTimer(duration, displayTarget);
            check();
            interval = setInterval(check, 5000);
        };
    </script>
</body>
</html>

