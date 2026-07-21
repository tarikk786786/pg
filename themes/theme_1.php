<?php
// Minimal Light Theme
$vpa = $userdata['vpa'] ?? 'UPI ID';
$merchant_name = $userdata['name'] ?? 'Demo Business';
$amount = $txnamount ?? '0.00';
$qr_url = "upi://pay?pa={$vpa}&pn=" . urlencode($merchant_name) . "&am={$amount}&cu=INR";
$qr_image = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . urlencode($qr_url);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Payment</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .payment-card {
            background-color: #ffffff;
            width: 100%;
            max-width: 400px;
            border-radius: 24px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.05);
            padding: 30px 24px;
            text-align: center;
            margin: 20px;
        }
        .header-title {
            font-size: 20px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 4px;
        }
        .badge-verified {
            display: inline-flex;
            align-items: center;
            background-color: #ecfdf5;
            color: #059669;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 24px;
        }
        .timer-text {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 20px;
            font-weight: 500;
        }
        .qr-box {
            border: 1px solid #e5e7eb;
            padding: 16px;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: 24px;
        }
        .qr-box img {
            width: 200px;
            height: 200px;
            border-radius: 8px;
        }
        .amount-display {
            font-size: 32px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 8px;
        }
        .amount-display sup {
            font-size: 18px;
            color: #6b7280;
        }
        .btn-primary {
            background-color: #3b82f6;
            color: white;
            border: none;
            border-radius: 12px;
            padding: 16px;
            width: 100%;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-bottom: 12px;
            transition: all 0.2s;
        }
        .btn-primary:hover {
            background-color: #2563eb;
        }
        .btn-secondary {
            background-color: #f3f4f6;
            color: #4b5563;
            border: none;
            border-radius: 12px;
            padding: 16px;
            width: 100%;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-secondary:hover {
            background-color: #e5e7eb;
        }
        .app-icons {
            display: flex;
            justify-content: center;
            gap: 16px;
            margin-top: 24px;
        }
        .app-icons i {
            font-size: 24px;
            color: #9ca3af;
        }
    </style>
</head>
<body>
    <div class="payment-card">
        <div class="header-title"><?php echo htmlspecialchars($merchant_name); ?></div>
        <div class="badge-verified"><i class="fas fa-check-circle me-1" style="margin-right:4px;"></i> Verified Business</div>
        
        <div class="timer-text">Complete payment in <span style="color:#ef4444; font-weight:700;">04:59</span></div>
        
        <div class="amount-display"><sup>₹</sup><?php echo htmlspecialchars($amount); ?></div>

        <div class="qr-box">
            <img src="<?php echo $qr_image; ?>" alt="QR Code">
        </div>

        <a href="<?php echo $qr_url; ?>" style="text-decoration:none;"><button class="btn-primary">Pay with UPI App</button></a>
        <button class="btn-secondary" onclick="alert('Save QR functionality')">Save QR Code</button>
        
        <div class="app-icons">
            <i class="fab fa-google-pay"></i>
            <i class="fas fa-wallet"></i>
            <i class="fab fa-amazon-pay"></i>
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

