<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful | AdityaLoot Secure Gateway</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --bg-color: #0b0617;
            --card-bg: rgba(26, 17, 46, 0.7);
            --neon-purple: #9d4edd;
            --neon-cyan: #00f2fe;
            --neon-green: #00ff87;
            --neon-pink: #ff007f;
            --text-main: #f3f0ff;
            --text-muted: #a39bb8;
            --border-glow: rgba(0, 255, 135, 0.3);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow-x: hidden;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(157, 78, 221, 0.15) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(0, 255, 135, 0.1) 0%, transparent 45%);
            padding: 20px;
        }

        .success-wrapper {
            background: var(--card-bg);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            max-width: 480px;
            width: 100%;
            border-radius: 28px;
            border: 1px solid rgba(0, 255, 135, 0.25);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.6), 0 0 30px var(--border-glow);
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
            animation: slide-up 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes slide-up {
            0% { opacity: 0; transform: translateY(30px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        /* Success Pulse Ring Animation */
        .success-ring-container {
            position: relative;
            width: 120px;
            height: 120px;
            margin: 0 auto 30px auto;
        }

        .success-pulse-ring {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            border-radius: 50%;
            background: rgba(0, 255, 135, 0.08);
            border: 2px solid var(--neon-green);
            animation: pulse-ring 2s infinite ease-out;
            box-shadow: 0 0 20px rgba(0, 255, 135, 0.2);
        }

        @keyframes pulse-ring {
            0% { transform: scale(0.9); opacity: 1; }
            100% { transform: scale(1.3); opacity: 0; }
        }

        .success-icon-box {
            position: absolute;
            top: 10px; left: 10px;
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, var(--neon-green) 0%, #00b358 100%);
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 10px 25px rgba(0, 255, 135, 0.4);
            z-index: 2;
        }

        .success-icon-box i {
            font-size: 50px;
            color: #0b0617;
            animation: scale-in 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) 0.2s both;
        }

        @keyframes scale-in {
            0% { transform: scale(0); }
            100% { transform: scale(1); }
        }

        h1 {
            font-size: 32px;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 12px;
            background: linear-gradient(90deg, #ffffff, var(--neon-green));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .success-subtitle {
            color: var(--text-muted);
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        /* Structured Invoice Receipt Details */
        .receipt-card {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: left;
            position: relative;
        }

        .receipt-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px dashed rgba(255, 255, 255, 0.05);
        }

        .receipt-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .receipt-row:first-child {
            padding-top: 0;
        }

        .receipt-label {
            font-size: 13px;
            color: var(--text-muted);
            font-weight: 500;
        }

        .receipt-value {
            font-size: 14px;
            color: var(--text-main);
            font-weight: 600;
        }

        .receipt-value.price {
            color: var(--neon-green);
            font-size: 18px;
            font-weight: 800;
            text-shadow: 0 0 10px rgba(0, 255, 135, 0.2);
        }

        .receipt-value.badge {
            background: rgba(0, 255, 135, 0.1);
            border: 1px solid rgba(0, 255, 135, 0.2);
            color: var(--neon-green);
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }

        /* Action Buttons */
        .btn-done {
            background: linear-gradient(90deg, var(--neon-green), #00b358);
            color: #0b0617;
            border: none;
            border-radius: 18px;
            padding: 16px 24px;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            font-size: 16px;
            font-weight: 800;
            cursor: pointer;
            text-decoration: none;
            box-shadow: 0 10px 25px rgba(0, 255, 135, 0.35);
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .btn-done:hover {
            box-shadow: 0 15px 35px rgba(0, 255, 135, 0.5);
            transform: translateY(-2px);
        }

        .btn-done:active {
            transform: translateY(0);
        }

        .redirect-timer {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 500;
        }

        .redirect-timer span {
            color: var(--neon-green);
            font-weight: 700;
        }
    </style>
</head>
<body>

<div class="success-wrapper">
    <!-- Success Rings -->
    <div class="success-ring-container">
        <div class="success-pulse-ring"></div>
        <div class="success-icon-box">
            <i class="bi bi-check-lg"></i>
        </div>
    </div>

    <h1>Payment Successful!</h1>
    <p class="success-subtitle">Thank you! Your transaction was processed securely. A confirmation email has been dispatched to your registered account.</p>

    <!-- Receipt Details -->
    <div class="receipt-card">
        <div class="receipt-row">
            <span class="receipt-label">Merchant Name</span>
            <span class="receipt-value">AdityaLoot Gateway</span>
        </div>
        <div class="receipt-row">
            <span class="receipt-label">Transaction ID</span>
            <span class="receipt-value" style="font-family: monospace; letter-spacing: 0.5px;">AL_TXN_<?php echo strtoupper(substr(md5(time()), 0, 10)); ?></span>
        </div>
        <div class="receipt-row">
            <span class="receipt-label">Status</span>
            <span class="receipt-value badge">Success</span>
        </div>
        <div class="receipt-row">
            <span class="receipt-label">Amount Paid</span>
            <span class="receipt-value price">₹<?php echo isset($_GET['amount']) ? number_format((float)$_GET['amount'], 2) : "1.00"; ?></span>
        </div>
    </div>

    <!-- Done Action Button -->
    <a href="index.html" class="btn-done">
        <i class="bi bi-house-door-fill"></i> Return to Homepage
    </a>

    <!-- Autoredirect countdown timer -->
    <div class="redirect-timer">
        Redirecting you back to homepage automatically in <span id="timer-sec">8</span> seconds...
    </div>
</div>

<script>
    // Automated dynamic redirection countdown timer
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