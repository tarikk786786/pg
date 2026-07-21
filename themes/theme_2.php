<?php
// Soft Blue Floating Theme
$vpa = $userdata['vpa'] ?? 'UPI ID';
$merchant_name = $userdata['name'] ?? 'Pay Zero';
$amount = $txnamount ?? '0.00';
$qr_url = "upi://pay?pa={$vpa}&pn=" . urlencode($merchant_name) . "&am={$amount}&cu=INR";
$qr_image = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . urlencode($qr_url);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Floating Checkout</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .app-container {
            width: 100%;
            max-width: 400px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 28px;
            padding: 30px;
            box-shadow: 0 20px 40px rgba(2, 132, 199, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.5);
            text-align: center;
        }
        .merchant-header {
            font-size: 24px;
            font-weight: 800;
            color: #0369a1;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .instruction-text {
            font-size: 15px;
            color: #475569;
            margin-bottom: 24px;
        }
        .qr-wrapper {
            background: white;
            padding: 16px;
            border-radius: 24px;
            box-shadow: inset 0 2px 10px rgba(0,0,0,0.05);
            display: inline-block;
            margin-bottom: 24px;
        }
        .qr-wrapper img {
            width: 220px;
            height: 220px;
            border-radius: 12px;
        }
        .timer-box {
            background: #f0f9ff;
            color: #0284c7;
            padding: 8px 16px;
            border-radius: 100px;
            font-weight: 700;
            font-size: 14px;
            display: inline-block;
        }
        .details-card {
            background: white;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(2, 132, 199, 0.08);
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px dashed #e2e8f0;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            color: #64748b;
            font-size: 14px;
        }
        .detail-value {
            color: #0f172a;
            font-weight: 700;
            font-size: 16px;
        }
        .pay-btn {
            background: #0ea5e9;
            color: white;
            border: none;
            width: 100%;
            padding: 16px;
            border-radius: 16px;
            font-size: 18px;
            font-weight: 700;
            margin-top: 16px;
            cursor: pointer;
            box-shadow: 0 8px 20px rgba(14, 165, 233, 0.3);
            transition: transform 0.2s;
        }
        .pay-btn:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="app-container">
        <div class="glass-card">
            <div class="merchant-header"><?php echo htmlspecialchars($merchant_name); ?></div>
            <div class="instruction-text">Scan to pay using any UPI app</div>
            
            <div class="qr-wrapper">
                <img src="<?php echo $qr_image; ?>" alt="QR Code">
            </div>

            <div>
                <div class="timer-box">
                    <i class="far fa-clock me-1"></i> Expires in 04:54
                </div>
            </div>
        </div>

        <div class="details-card">
            <div class="detail-row">
                <span class="detail-label">Amount Payable</span>
                <span class="detail-value" style="color:#0ea5e9; font-size:20px;">₹<?php echo htmlspecialchars($amount); ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Order ID</span>
                <span class="detail-value">#ORD<?php echo rand(10000, 99999); ?></span>
            </div>
            
            <a href="<?php echo $qr_url; ?>" style="text-decoration:none;"><button class="pay-btn">Pay via App</button></a>
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

