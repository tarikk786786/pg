<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful | Dezo</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="index.css">
</head>
<body class="success-body">

<div class="success-card glass-panel">
    <!-- Animated Success Icon -->
    <div class="success-icon-wrapper">
        <div class="success-icon-bg"></div>
        <div class="success-icon">
            <i class="bi bi-check-lg"></i>
        </div>
    </div>

    <h1 style="font-size: 32px; font-weight: 800; margin-bottom: 12px; color: #fff;">Payment Successful!</h1>
    <p style="color: var(--text-muted); font-size: 15px; line-height: 1.6;">Your transaction has been processed securely. A confirmation email has been dispatched to your account.</p>

    <!-- Receipt Details -->
    <div class="receipt-box">
        <div class="receipt-row">
            <span class="receipt-label">Merchant</span>
            <span class="receipt-value">Dezo Gateway</span>
        </div>
        <div class="receipt-row">
            <span class="receipt-label">Transaction ID</span>
            <span class="receipt-value" style="font-family: monospace; letter-spacing: 0.5px; color: var(--accent-cyan);">DZ_TXN_<?php echo strtoupper(substr(md5(time()), 0, 10)); ?></span>
        </div>
        <div class="receipt-row">
            <span class="receipt-label">Status</span>
            <span class="receipt-value" style="background: rgba(20, 184, 166, 0.15); color: var(--accent-teal); padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 700; text-transform: uppercase;">Verified</span>
        </div>
        <div class="receipt-row" style="margin-top: 8px; padding-top: 16px; border-top: 1px solid rgba(255,255,255,0.1);">
            <span class="receipt-label" style="font-weight: 600; color: #fff;">Amount Paid</span>
            <span class="receipt-value price">$<?php echo isset($_GET['amount']) ? number_format((float)$_GET['amount'], 2) : "0.00"; ?></span>
        </div>
    </div>

    <!-- Actions -->
    <a href="index.html" class="btn-success">
        <i class="bi bi-house-door-fill"></i> Return to Homepage
    </a>

    <!-- Timer -->
    <div class="timer-text">
        Redirecting automatically in <span id="timer-sec">8</span>s
    </div>
</div>

<script>
    // Automated redirect timer
    let count = 8;
    const timerElement = document.getElementById("timer-sec");
    const interval = setInterval(() => {
        count--;
        timerElement.textContent = count;
        if (count <= 0) {
            clearInterval(interval);
            window.location.href = "index.html";
        }
    }, 1000);
</script>

</body>
</html>