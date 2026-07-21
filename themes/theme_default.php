<?php 
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
    <title>Secure Checkout</title>
    <!-- FontAwesome for Premium Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Premium Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        :root {
            --primary: #093c31;
            --primary-dark: #06261f;
            --accent: #d6eb5b;
            --accent-hover: #c5d94b;
            --bg: #f8fafc;
            --card: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border: #f1f5f9;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            background-color: #f1f5f9;
            color: var(--text-main);
            min-height: 100vh;
        }

        /* ------------------ COMMON MOBILE STYLES ------------------ */
        @media (max-width: 768px) {
            .page-footer-sticky {
                display: none !important;
            }

            body {
                display: flex;
                flex-direction: column;
                justify-content: flex-start;
                align-items: center;
            }

            .desktop-view {
                display: none !important;
            }

            .mobile-view {
                display: flex !important;
                width: 100%;
                min-height: 100vh;
                background-color: var(--card);
                flex-direction: column;
                position: relative;
            }

            /* Premium Dark Header */
            .checkout-header {
                background-color: var(--primary);
                padding: 8px 12px;
                color: #ffffff;
                position: relative;
            }

            .header-top {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 8px;
            }

            .back-btn {
                background: rgba(255, 255, 255, 0.1);
                border: none;
                width: 26px;
                height: 26px;
                border-radius: 50%;
                color: #ffffff;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.2s ease;
                font-size: 10px;
            }

            .back-btn:hover {
                background: rgba(255, 255, 255, 0.2);
                transform: scale(1.05);
            }

            .merchant-profile {
                display: flex;
                align-items: center;
                gap: 6px;
            }

            .avatar {
                width: 24px;
                height: 24px;
                border-radius: 50%;
                background-color: rgba(255, 255, 255, 0.15);
                border: 1px solid rgba(255, 255, 255, 0.2);
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 700;
                font-size: 10px;
                color: #ffffff;
            }

            .merchant-name {
                font-size: 12px;
                font-weight: 600;
                letter-spacing: -0.2px;
                line-height: 1.2;
            }

            .verified-badge {
                display: flex;
                align-items: center;
                gap: 2px;
                font-size: 9px;
                color: rgba(255, 255, 255, 0.7);
            }

            .verified-badge img {
                width: 12px;
                height: 12px;
                object-fit: contain;
            }

            .timer-badge {
                background-color: rgba(255, 255, 255, 0.1);
                border: 1px solid rgba(255, 255, 255, 0.15);
                padding: 2px 8px;
                border-radius: 12px;
                font-size: 10px;
                font-weight: 600;
                display: flex;
                align-items: center;
                gap: 3px;
                color: var(--accent);
            }

            /* Order/Price Box */
            .order-summary-card {
                background-color: rgba(255, 255, 255, 0.07);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 8px;
                padding: 6px 10px;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .amount-section {
                display: flex;
                flex-direction: column;
            }

            .amount-label {
                font-size: 8px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                color: rgba(255, 255, 255, 0.6);
                font-weight: 500;
            }

            .amount-value {
                font-size: 15px;
                font-weight: 700;
                color: #ffffff;
                letter-spacing: -0.2px;
            }

            .order-id-section {
                text-align: right;
            }

            .order-label {
                font-size: 8px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                color: rgba(255, 255, 255, 0.6);
                font-weight: 500;
            }

            .order-value {
                font-size: 10px;
                font-weight: 600;
                color: rgba(255, 255, 255, 0.95);
            }

            /* Scrollable Area */
            .checkout-body {
                flex: 1;
                overflow-y: auto;
                padding: 20px;
                background-color: var(--bg);
            }

            .section-title {
                font-size: 11px;
                font-weight: 700;
                color: var(--text-muted);
                letter-spacing: 1px;
                text-transform: uppercase;
                margin-bottom: 10px;
                margin-top: 14px;
            }

            /* Payment Methods Card */
            .method-card {
                background-color: var(--card);
                border-radius: 16px;
                border: 1px solid var(--border);
                padding: 18px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.01), 0 2px 4px -1px rgba(0, 0, 0, 0.01);
                margin-bottom: 16px;
            }

            /* UPI QR Details */
            .qr-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 16px;
            }

            .qr-title {
                font-size: 14px;
                font-weight: 600;
                color: var(--text-main);
            }

            .upi-brands {
                display: flex;
                gap: 6px;
                align-items: center;
            }

            .upi-brand-badge {
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 4px;
                padding: 2px 6px;
                font-size: 9px;
                font-weight: 700;
                color: #475569;
            }

            .qr-main-content {
                display: flex;
                gap: 20px;
                align-items: center;
            }

            @media (max-width: 400px) {
                .qr-main-content {
                    flex-direction: column;
                    text-align: center;
                }
            }

            .qr-code-wrapper {
                background: #ffffff;
                border: 1px solid #cbd5e1;
                border-radius: 12px;
                padding: 8px;
                display: inline-block;
                cursor: pointer;
                transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.2s ease;
            }
            .qr-code-wrapper:hover {
                transform: scale(1.05);
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            }

            .qr-code-img {
                width: 120px;
                height: 120px;
                display: block;
            }

            .qr-instructions {
                flex: 1;
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

            .instruction-text {
                font-size: 13px;
                color: var(--text-muted);
                line-height: 1.4;
            }

            .download-qr-btn {
                background-color: #f1f5f9;
                color: #0f172a;
                border: none;
                padding: 8px 14px;
                border-radius: 8px;
                font-size: 12px;
                font-weight: 600;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
                transition: all 0.2s ease;
                width: fit-content;
            }

            .download-qr-btn:hover {
                background-color: #e2e8f0;
            }

            /* Recommended options */
            .option-selector {
                background-color: var(--card);
                border-radius: 16px;
                border: 1px solid var(--border);
                overflow: hidden;
                margin-bottom: 24px;
            }

            .option-item {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 16px 20px;
                cursor: pointer;
                border-bottom: 1px solid var(--border);
                transition: background 0.2s ease;
            }

            .option-item:last-child {
                border-bottom: none;
            }

            .option-item:hover {
                background-color: #fafbfb;
            }

            .option-left {
                display: flex;
                align-items: center;
                gap: 14px;
            }

            .option-icon-box {
                width: 32px;
                height: 32px;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .option-icon-img-mob {
                width: 24px;
                height: 24px;
                object-fit: contain;
            }

            .option-details {
                display: flex;
                flex-direction: column;
            }

            .option-title {
                font-size: 14px;
                font-weight: 600;
                color: var(--text-main);
            }

            .option-subtitle {
                font-size: 12px;
                color: var(--text-muted);
                margin-top: 1px;
            }

            .option-radio {
                width: 20px;
                height: 20px;
                border-radius: 50%;
                border: 2px solid #cbd5e1;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.2s ease;
            }

            .option-item.selected .option-radio {
                border-color: var(--primary);
            }

            .option-radio::after {
                content: '';
                width: 10px;
                height: 10px;
                border-radius: 50%;
                background-color: var(--primary);
                transform: scale(0);
                transition: transform 0.2s ease;
            }

            .option-item.selected .option-radio::after {
                transform: scale(1);
            }

            /* VPA Dropdown form */
            .upi-input-container {
                padding: 0 20px 20px 20px;
                background-color: #ffffff;
                border-bottom: 1px solid var(--border);
                display: none;
            }

            .vpa-form {
                width: 100%;
            }

            .upi-input-wrapper {
                display: flex;
                gap: 10px;
                margin-top: 8px;
            }

            .vpa-input {
                flex: 1;
                border: 1.5px solid #e2e8f0;
                border-radius: 10px;
                padding: 12px 16px;
                font-size: 14px;
                outline: none;
                transition: all 0.2s ease;
            }

            .vpa-input:focus {
                border-color: var(--primary);
                box-shadow: 0 0 0 3px rgba(9, 60, 49, 0.08);
            }

            /* Bottom Sticky Bar */
            .checkout-footer {
                background-color: #ffffff;
                border-top: 1px solid rgba(0, 0, 0, 0.05);
                padding: 16px 24px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 16px;
                box-shadow: 0 -8px 24px rgba(0, 0, 0, 0.04);
                position: sticky;
                bottom: 0;
                left: 0;
                right: 0;
                z-index: 100;
            }

            .footer-price-info {
                display: flex;
                flex-direction: column;
            }

            .footer-amount {
                font-size: 22px;
                font-weight: 800;
                color: #0f172a;
                letter-spacing: -0.5px;
            }

            .view-breakup-link {
                font-size: 11px;
                color: var(--primary);
                text-decoration: none;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                margin-top: 4px;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 3px;
                transition: color 0.2s ease;
            }

            .view-breakup-link:hover {
                color: var(--primary-dark);
            }

            .pay-now-btn {
                flex: 1;
                background: linear-gradient(135deg, var(--accent) 0%, #c5d94b 100%);
                color: var(--primary-dark);
                border: none;
                padding: 14px 24px;
                border-radius: 12px;
                font-size: 16px;
                font-weight: 800;
                cursor: pointer;
                box-shadow: 0 4px 14px rgba(214, 235, 91, 0.45);
                transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
                text-align: center;
            }

            .pay-now-btn:hover {
                background: linear-gradient(135deg, #e2f56e 0%, var(--accent-hover) 100%);
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(214, 235, 91, 0.6);
            }

            .pay-now-btn:active {
                transform: translateY(0);
            }

            .secure-footer-text {
                text-align: center;
                font-size: 11px;
                color: var(--text-muted);
                padding: 12px;
                background-color: var(--bg);
                border-top: 1px solid var(--border);
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
            }
        }

        /* ------------------ PREMIUM DESKTOP SPLIT STYLES ------------------ */
        @media (min-width: 769px) {
            body {
                background-color: #e2e8f0;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                padding: 40px 20px 100px 20px; /* Space for fixed footer */
            }

            .mobile-view {
                display: none !important;
            }

            .desktop-view {
                display: flex !important;
                width: 960px;
                max-width: 95vw;
                height: 520px;
                background-color: #ffffff;
                border-radius: 16px;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
                overflow: hidden;
            }

            /* Sidebar Left (Green Area) */
            .sidebar-left {
                width: 32%;
                background-color: #0b3c33;
                color: #ffffff;
                padding: 30px 24px;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                position: relative;
            }

            .sidebar-top {
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .sidebar-avatar {
                width: 44px;
                height: 44px;
                border-radius: 8px;
                border: 1px solid rgba(255, 255, 255, 0.25);
                background-color: rgba(255, 255, 255, 0.1);
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 700;
                font-size: 16px;
                letter-spacing: 0.5px;
            }

            .sidebar-name {
                font-size: 16px;
                font-weight: 700;
                letter-spacing: -0.2px;
            }

            .sidebar-price-card {
                background: rgba(255, 255, 255, 0.05);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 12px;
                padding: 16px 20px;
                margin-top: 30px;
            }

            .sidebar-price-lbl {
                font-size: 11px;
                color: rgba(255, 255, 255, 0.6);
                text-transform: uppercase;
                margin-bottom: 6px;
                font-weight: 500;
            }

            .sidebar-price-val {
                font-size: 32px;
                font-weight: 700;
                letter-spacing: -0.5px;
            }

            .sidebar-order-id {
                background: rgba(255, 255, 255, 0.05);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 8px;
                padding: 10px 14px;
                font-size: 12px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                color: rgba(255, 255, 255, 0.8);
                margin-top: 16px;
            }

            .sidebar-order-id span {
                font-family: monospace;
            }

            .sidebar-copy-btn {
                background: transparent;
                border: none;
                color: #ffffff;
                cursor: pointer;
                opacity: 0.7;
                transition: opacity 0.2s;
            }

            .sidebar-copy-btn:hover {
                opacity: 1;
            }

            .sidebar-brand-footer {
                display: flex;
                align-items: center;
                gap: 6px;
                font-size: 13px;
                font-weight: 700;
                color: #ffffff;
            }

            .sidebar-brand-badge {
                background: #d6eb5b;
                color: #0b3c33;
                font-size: 9px;
                padding: 2px 6px;
                border-radius: 4px;
                text-transform: uppercase;
            }

            /* Main Area Right */
            .main-right {
                width: 68%;
                display: flex;
                flex-direction: column;
                background-color: #ffffff;
            }

            .right-header {
                padding: 18px 24px;
                border-bottom: 1px solid #e2e8f0;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .right-title {
                font-size: 15px;
                font-weight: 700;
                color: #0f172a;
            }

            .right-window-controls {
                display: flex;
                gap: 8px;
            }

            .window-dot {
                width: 26px;
                height: 26px;
                border-radius: 50%;
                background-color: #f8fafc;
                border: 1px solid #cbd5e1;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 10px;
                color: #64748b;
                cursor: pointer;
            }

            .right-content {
                flex: 1;
                display: flex;
                height: calc(100% - 63px);
            }

            .tab-column-left {
                width: 32%;
                border-right: 1px solid #e2e8f0;
                background-color: #ffffff;
                padding-top: 12px;
            }

            .tab-item {
                padding: 16px 20px;
                font-size: 13px;
                font-weight: 700;
                color: #64748b;
                cursor: pointer;
                border-left: 3px solid transparent;
                display: flex;
                flex-direction: column;
                gap: 4px;
            }

            .tab-item-subtitle {
                font-size: 10px;
                color: #94a3b8;
                font-weight: 500;
            }

            .tab-item.active {
                background-color: #f0fdf4;
                color: #0b3c33;
                border-left-color: #0b3c33;
            }

            .tab-item.active .tab-item-subtitle {
                color: #093c31;
            }

            .details-column-right {
                width: 68%;
                padding: 24px;
                overflow-y: auto;
                background-color: #ffffff;
            }

            .details-column-right::-webkit-scrollbar {
                width: 6px;
            }
            .details-column-right::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 3px;
            }

            .qr-details-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 18px;
                border-bottom: 1.5px solid #f1f5f9;
                padding-bottom: 8px;
            }

            .qr-details-title {
                font-size: 14px;
                font-weight: 700;
                color: #0f172a;
            }

            .qr-timer {
                font-size: 12px;
                font-weight: 700;
                color: #d97706;
                display: flex;
                align-items: center;
                gap: 4px;
            }

            .qr-section-desktop {
                display: flex;
                gap: 20px;
                align-items: center;
                margin-bottom: 24px;
            }

            .qr-image-desktop {
                width: 140px;
                height: 140px;
                border: 1px solid #e2e8f0;
                border-radius: 12px;
                padding: 8px;
                background: #ffffff;
            }

            .qr-info-desktop {
                flex: 1;
            }

            .qr-info-desktop h3 {
                font-size: 13px;
                color: #64748b;
                font-weight: 500;
                margin-bottom: 10px;
            }

            .app-icons-grid {
                display: flex;
                gap: 8px;
            }

            .app-icon-img {
                width: 28px;
                height: 28px;
                border-radius: 6px;
                border: 1px solid #cbd5e1;
                padding: 3px;
                background: #ffffff;
                object-fit: contain;
            }

            /* Custom desktop selector styles */
            .desktop-payment-options {
                display: flex;
                flex-direction: column;
                gap: 12px;
                margin-top: 14px;
            }

            .desktop-option-item {
                display: flex;
                align-items: center;
                justify-content: space-between;
                border: 1px solid #e2e8f0;
                border-radius: 8px;
                padding: 14px 16px;
                cursor: pointer;
                transition: all 0.2s ease;
            }

            .desktop-option-item:hover {
                border-color: #cbd5e1;
                background: #fafbfb;
            }

            .desktop-option-item.selected {
                border-color: #0b3c33;
                background: #f0fdf4;
            }

            .desktop-option-left {
                display: flex;
                align-items: center;
                gap: 10px;
                font-size: 13px;
                font-weight: 600;
            }

            .desktop-option-img {
                width: 20px;
                height: 20px;
                object-fit: contain;
            }

            .desktop-option-chevron {
                color: #cbd5e1;
                font-size: 12px;
            }

            /* UPI ID field directly visible on desktop option click */
            .desktop-vpa-container {
                display: none;
                margin-top: 12px;
                padding: 16px;
                border: 1.5px solid #f1f5f9;
                background: #f8fafc;
                border-radius: 8px;
            }

            /* Page sticky footer on PC */
            .page-footer-sticky {
                display: flex;
                position: fixed;
                bottom: 0;
                left: 0;
                width: 100%;
                background-color: #ffffff;
                border-top: 1px solid rgba(0, 0, 0, 0.05);
                padding: 16px 40px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                z-index: 1000;
                box-shadow: 0 -8px 30px rgba(0, 0, 0, 0.05);
                backdrop-filter: blur(8px);
            }

            .page-footer-left {
                display: flex;
                flex-direction: column;
            }

            .page-footer-amount-lbl {
                font-size: 24px;
                font-weight: 800;
                color: #0f172a;
                letter-spacing: -0.5px;
            }

            .page-footer-breakup {
                font-size: 11px;
                color: var(--primary);
                text-decoration: none;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                margin-top: 4px;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 3px;
                transition: color 0.2s ease;
            }

            .page-footer-breakup:hover {
                color: var(--primary-dark);
            }

            .page-footer-btn {
                background: linear-gradient(135deg, var(--accent) 0%, #c5d94b 100%);
                color: var(--primary-dark);
                border: none;
                padding: 14px 38px;
                border-radius: 12px;
                font-size: 16px;
                font-weight: 800;
                cursor: pointer;
                box-shadow: 0 4px 14px rgba(214, 235, 91, 0.45);
                transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .page-footer-btn:hover {
                background: linear-gradient(135deg, #e2f56e 0%, var(--accent-hover) 100%);
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(214, 235, 91, 0.6);
            }

            .page-footer-btn:active {
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>

    <!-- ------------------ DESKTOP SPLIT VIEW ------------------ -->
    <div class="desktop-view">
        <div class="sidebar-left">
            <div>
                <div class="sidebar-top">
                    <div class="sidebar-avatar"><?php echo $initials; ?></div>
                    <span class="sidebar-name"><?php echo htmlspecialchars($USERNAME); ?></span>
                </div>

                <div class="sidebar-price-card">
                    <div class="sidebar-price-lbl">Price Summary</div>
                    <div class="sidebar-price-val">₹<?php echo number_format($amount, 2); ?></div>
                </div>

                <div class="sidebar-order-id">
                    <span><?php echo htmlspecialchars($order_id); ?></span>
                    <button class="sidebar-copy-btn" onclick="copyOrderId()" title="Copy Order ID">
                        <i class="fa-regular fa-copy"></i>
                    </button>
                </div>
            </div>

            <div class="sidebar-footer">
                <span class="sidebar-brand-badge">Dezo</span>
                <span>Verified Payment</span>
            </div>
        </div>

        <div class="main-right">
            <div class="right-header">
                <span class="right-title">Payment Options</span>
                <div class="right-window-controls">
                    <div class="window-dot"><i class="fa-solid fa-ellipsis"></i></div>
                    <div class="window-dot" onclick="window.history.back();"><i class="fa-solid fa-xmark"></i></div>
                </div>
            </div>

            <div class="right-content">
                <div class="tab-column-left">
                    <div class="tab-item active" onclick="switchDesktopTab('upi')">
                        <span>UPI</span>
                        <span class="tab-item-subtitle">PhonePe, Paytm & more</span>
                    </div>
                </div>

                <div class="details-column-right">
                    <!-- UPI QR Column Sub-Details -->
                    <div class="qr-details-header">
                        <span class="qr-details-title">UPI QR</span>
                        <div class="qr-timer">
                            <i class="fa-regular fa-clock"></i>
                            <span class="desktop-timer">00:00</span>
                        </div>
                    </div>

                    <div class="qr-section-desktop">
                        <div class="qr-code-wrapper" onclick="downloadQR()" title="Click to Download QR Code">
                            <?php if (isset($qr_image) && $qr_image): ?>
                                <img src="<?php echo $qr_image; ?>" alt="QR Code" class="qr-image-desktop">
                            <?php else: ?>
                                <p style="font-size:12px;color:red;padding:10px;">Error QR</p>
                            <?php endif; ?>
                        </div>
                        <div class="qr-info-desktop">
                            <h3>Scan the QR using any UPI App</h3>
                            <div class="app-icons-grid">
                                <img class="app-icon-img" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTo4x8kSTmPUq4PFzl4HNT0gObFuEhivHOFYg&s" alt="PhonePe">
                                <img class="app-icon-img" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQyVO9LUWF81Ov6LZR50eDNu5rNFCpkn0LwYQ&s" alt="GPay">
                                <img class="app-icon-img" src="https://w7.pngwing.com/pngs/305/719/png-transparent-paytm-ecommerce-shopping-social-icons-circular-color-icon-thumbnail.png" alt="Paytm">
                                <img class="app-icon-img" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRSouM4icV33KEDtJakZiySZN3HH2LPfv3-BA&s" alt="BHIM">
                            </div>
                        </div>
                    </div>

                    <span class="section-title">RECOMMENDED</span>
                    <div class="desktop-payment-options">
                        <div class="desktop-option-item selected" id="opt-paytm" onclick="selectDesktopOption('paytm')">
                            <div class="desktop-option-left">
                                <img class="desktop-option-img" src="https://w7.pngwing.com/pngs/305/719/png-transparent-paytm-ecommerce-shopping-social-icons-circular-color-icon-thumbnail.png" alt="Paytm Logo">
                                <span>Paytm</span>
                            </div>
                            <i class="fa-solid fa-chevron-right desktop-option-chevron"></i>
                        </div>

                        <div class="desktop-option-item" id="opt-upi-id" onclick="selectDesktopOption('upi-id')">
                            <div class="desktop-option-left">
                                <img class="desktop-option-img" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQyVO9LUWF81Ov6LZR50eDNu5rNFCpkn0LwYQ&s" alt="GPay Logo">
                                <span>Share QR</span>
                            </div>
                            <i class="fa-solid fa-chevron-right desktop-option-chevron"></i>
                        </div>

                        <!-- Expandable desktop VPA input -->
                        <div class="desktop-vpa-container">
                            <input type="text" id="desktopUpiInput" placeholder="Enter UPI ID (e.g. name@upi)" class="vpa-input">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sticky footer layout for PC view -->
    <div class="page-footer-sticky">
        <div class="page-footer-left">
            <span class="page-footer-amount-lbl">₹<?php echo number_format($amount, 2); ?></span>
            <span class="page-footer-breakup" onclick="viewBreakup()">View Breakup</span>
        </div>
        <button class="page-footer-btn" onclick="handleDesktopSubmit()">Pay Now</button>
    </div>


    <!-- ------------------ MOBILE VERTICAL VIEW ------------------ -->
    <div class="mobile-view">
        <!-- Premium Dark Header -->
        <div class="checkout-header">
            <div class="header-top">
                <button class="back-btn" onclick="window.history.back();">
                    <i class="fa fa-arrow-left"></i>
                </button>
                <div class="merchant-profile">
                    <div class="avatar"><?php echo $initials; ?></div>
                    <div class="merchant-info">
                        <span class="merchant-name"><?php echo htmlspecialchars($USERNAME); ?></span>
                        <div class="verified-badge">
                            <img src="https://d6xcmfyh68wv8.cloudfront.net/assets/trusted-badge/1st-fold/top-illustration-mob.svg" alt="Verified">
                            <span>Verified Merchant</span>
                        </div>
                    </div>
                </div>
                <div class="timer-badge">
                    <i class="fa-regular fa-clock"></i>
                    <span id="timeout">00:00</span>
                </div>
            </div>

            <!-- Overlapping Amount Card -->
            <div class="order-summary-card">
                <div class="amount-section">
                    <span class="amount-label">Amount to Pay</span>
                    <span class="amount-value">₹<?php echo number_format($amount, 2); ?></span>
                </div>
                <div class="order-id-section">
                    <span class="order-label">Order ID</span>
                    <span class="order-value"><?php echo htmlspecialchars($order_id); ?></span>
                </div>
            </div>
        </div>

        <!-- Checkout Contents -->
        <div class="checkout-body">
            <!-- QR Card -->
            <div class="section-title">UPI QR</div>
            <div class="method-card">
                <div class="qr-header">
                    <span class="qr-title">Scan & Pay</span>
                    <div class="upi-brands">
                        <span class="upi-brand-badge">UPI</span>
                        <span class="upi-brand-badge">BHIM</span>
                    </div>
                </div>
                <div class="qr-main-content">
                    <div class="qr-code-wrapper" onclick="downloadQR()" title="Click to Download QR Code">
                        <?php if (isset($qr_image) && $qr_image): ?>
                            <img src="<?php echo $qr_image; ?>" alt="QR Code" class="qr-code-img">
                        <?php else: ?>
                            <p style="font-size:12px;color:red;">Error generating QR code.</p>
                        <?php endif; ?>
                    </div>
                    <div class="qr-instructions">
                        <p class="instruction-text">Open any UPI app and scan to pay instantly</p>
                        <button class="download-qr-btn" onclick="downloadQR();">
                            <i class="fa fa-download"></i>
                            Download QR
                        </button>
                    </div>
                </div>
            </div>

            <!-- Recommended Payment Methods -->
            <div class="section-title">Recommended</div>
            <div class="option-selector">
                <div class="option-item selected" onclick="selectPaymentOption('paytm')">
                    <div class="option-left">
                        <div class="option-icon-box">
                            <img class="option-icon-img-mob" src="https://w7.pngwing.com/pngs/305/719/png-transparent-paytm-ecommerce-shopping-social-icons-circular-color-icon-thumbnail.png" alt="Paytm Logo">
                        </div>
                        <div class="option-details">
                            <span class="option-title">Paytm</span>
                            <span class="option-subtitle">Pay via Paytm UPI</span>
                        </div>
                    </div>
                    <div class="option-radio"></div>
                </div>

                <div class="option-item" onclick="selectPaymentOption('gpay')">
                    <div class="option-left">
                        <div class="option-icon-box">
                            <img class="option-icon-img-mob" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQyVO9LUWF81Ov6LZR50eDNu5rNFCpkn0LwYQ&s" alt="GPay Logo">
                        </div>
                        <div class="option-details">
                            <span class="option-title">Google Pay</span>
                            <span class="option-subtitle">Pay via Google Pay</span>
                        </div>
                    </div>
                    <div class="option-radio"></div>
                </div>

                <div class="option-item" onclick="selectPaymentOption('phonepe')">
                    <div class="option-left">
                        <div class="option-icon-box">
                            <img class="option-icon-img-mob" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTo4x8kSTmPUq4PFzl4HNT0gObFuEhivHOFYg&s" alt="PhonePe Logo">
                        </div>
                        <div class="option-details">
                            <span class="option-title">PhonePe</span>
                            <span class="option-subtitle">Pay via PhonePe</span>
                        </div>
                    </div>
                    <div class="option-radio"></div>
                </div>

                <div class="option-item" onclick="selectPaymentOption('upi-id')">
                    <div class="option-left">
                        <div class="option-icon-box">
                            <img class="option-icon-img-mob" src="https://cdn-icons-png.flaticon.com/512/1828/1828956.png" alt="Share Logo">
                        </div>
                        <div class="option-details">
                            <span class="option-title">Share QR</span>
                            <span class="option-subtitle">Share QR to pay via any app</span>
                        </div>
                    </div>
                    <div class="option-radio"></div>
                </div>

                <!-- Hidden inputs for UPI submission -->
                <div class="upi-input-container">
                    <form id="upiForm" action="https://<?= $_SERVER["SERVER_NAME"] ?>/payment/instant-pay/hdfcupipay/<?php echo $link_token; ?>" method="post" class="vpa-form">
                        <input type="hidden" name="cxr_XsRFtoken" value="<?php echo isset($nonce) ? $nonce : ''; ?>">
                        <input type="hidden" name="TransactionId" value="<?php echo $cxrkalwaremark; ?>">
                        <div class="upi-input-wrapper">
                            <input type="text" id="upiIdInput" name="upiId" placeholder="Enter Your UPI Id (e.g. name@upi)" class="vpa-input">
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sticky Footer bar -->
        <div class="checkout-footer">
            <div class="footer-price-info">
                <span class="footer-amount">₹<?php echo number_format($amount, 2); ?></span>
                <span class="view-breakup-link" onclick="viewBreakup()">View Breakup</span>
            </div>
            <button class="pay-now-btn" id="payNowBtn" onclick="handlePaymentSubmit()">
                Pay Now
            </button>
        </div>

        <div class="secure-footer-text">
            <i class="fa-solid fa-shield-halved" style="color:#16a34a;"></i>
            Secured by SSL & PCI-DSS Compliant Gateway
        </div>
    </div>

    <!-- Interactive JS scripts -->
    <script>
        var selectedOption = 'paytm';

        async function shareQRCode() {
            var qrImg = document.querySelector('.qr-code-img') || document.querySelector('.qr-image-desktop');
            var qrSrc = qrImg ? qrImg.src : '';
            
            // Check if we can share the actual QR image file
            if (navigator.canShare && qrSrc && (qrSrc.startsWith('data:') || qrSrc.startsWith('http') || qrSrc.startsWith('blob:'))) {
                try {
                    let blob;
                    if (qrSrc.startsWith('data:')) {
                        const parts = qrSrc.split(';base64,');
                        const contentType = parts[0].split(':')[1];
                        const raw = window.atob(parts[1]);
                        const rawLength = raw.length;
                        const uInt8Array = new Uint8Array(rawLength);
                        for (let i = 0; i < rawLength; ++i) {
                            uInt8Array[i] = raw.charCodeAt(i);
                        }
                        blob = new Blob([uInt8Array], { type: contentType });
                    } else {
                        const res = await fetch(qrSrc);
                        blob = await res.blob();
                    }
                    
                    const file = new File([blob], 'qr-payment.png', { type: blob.type });
                    const shareData = {
                        title: 'Pay using QR Code',
                        text: 'Scan this QR code or use the link to pay ₹<?php echo number_format($amount, 2); ?> using Dezo:',
                        url: window.location.href,
                        files: [file]
                    };
                    
                    if (navigator.canShare(shareData)) {
                        await navigator.share(shareData);
                        return;
                    }
                } catch (e) {
                    console.error("Error creating QR file for share:", e);
                }
            }
            
            if (navigator.share) {
                try {
                    await navigator.share({
                        title: 'Pay using QR Code',
                        text: 'Scan QR code or use this link to pay ₹<?php echo number_format($amount, 2); ?> using Dezo:',
                        url: window.location.href
                    });
                } catch (e) {
                    console.error("Error sharing text/URL:", e);
                }
            } else {
                navigator.clipboard.writeText(window.location.href).then(function() {
                    Swal.fire({
                        title: 'Link Copied!',
                        text: 'Payment link copied to clipboard. You can now share it with anyone.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }).catch(function() {
                    Swal.fire({
                        title: 'Share Info',
                        text: 'Share this link to pay: ' + window.location.href,
                        icon: 'info'
                    });
                });
            }
        }

        function selectPaymentOption(option) {
            selectedOption = option;
            $('.option-item').removeClass('selected');
            
            if (option === 'paytm') {
                $('.option-item').eq(0).addClass('selected');
                $('.upi-input-container').slideUp(200);
            } else if (option === 'gpay') {
                $('.option-item').eq(1).addClass('selected');
                $('.upi-input-container').slideUp(200);
            } else if (option === 'phonepe') {
                $('.option-item').eq(2).addClass('selected');
                $('.upi-input-container').slideUp(200);
            } else if (option === 'upi-id') {
                $('.option-item').eq(3).addClass('selected');
                $('.upi-input-container').slideDown(200);
                setTimeout(function() {
                    $('#upiIdInput').focus();
                }, 250);
                shareQRCode();
            }
        }

        // Desktop Selection
        var selectedDesktopOpt = 'paytm';
        function selectDesktopOption(opt) {
            selectedDesktopOpt = opt;
            $('.desktop-option-item').removeClass('selected');
            if (opt === 'paytm') {
                $('#opt-paytm').addClass('selected');
                $('.desktop-vpa-container').slideUp(200);
            } else if (opt === 'upi-id') {
                $('#opt-upi-id').addClass('selected');
                $('.desktop-vpa-container').slideDown(200);
                setTimeout(function() {
                    $('#desktopUpiInput').focus();
                }, 250);
                shareQRCode();
            }
        }

        function switchDesktopTab(tab) {
            // Placeholder for multiple tabs if ever needed
        }

        function copyOrderId() {
            var orderIdText = "<?php echo $order_id; ?>";
            navigator.clipboard.writeText(orderIdText).then(function() {
                Swal.fire({
                    title: 'Copied!',
                    text: 'Order ID copied to clipboard.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
            });
        }

        function downloadQR() {
            var qrImg = document.querySelector('.qr-code-img') || document.querySelector('.qr-image-desktop');
            if (qrImg && qrImg.src) {
                var src = qrImg.src;
                if (src.startsWith('data:')) {
                    var link = document.createElement('a');
                    link.href = src;
                    link.download = 'qr-code-<?php echo $order_id; ?>.png';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                } else {
                    fetch(src)
                        .then(response => response.blob())
                        .then(blob => {
                            var link = document.createElement('a');
                            link.href = URL.createObjectURL(blob);
                            link.download = 'qr-code-<?php echo $order_id; ?>.png';
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                        })
                        .catch(error => {
                            var link = document.createElement('a');
                            link.href = src;
                            link.target = '_blank';
                            link.download = 'qr-code-<?php echo $order_id; ?>.png';
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                        });
                }
            } else {
                Swal.fire({
                    title: 'Error',
                    text: 'QR code image not found.',
                    icon: 'error'
                });
            }
        }

        function viewBreakup() {
            Swal.fire({
                title: 'Order Breakup',
                html: '<div style="text-align:left; font-size:14px; padding:10px 20px;">' +
                      '<p style="display:flex; justify-content:space-between; margin-bottom:8px;"><span>Order Amount:</span> <strong>₹<?php echo number_format($amount, 2); ?></strong></p>' +
                      '<p style="display:flex; justify-content:space-between; margin-bottom:8px;"><span>Convenience Fee:</span> <strong>₹0.00</strong></p>' +
                      '<hr style="border:none; border-top:1px solid #e2e8f0; margin:10px 0;">' +
                      '<p style="display:flex; justify-content:space-between; font-weight:bold; font-size:16px;"><span>Total:</span> <span>₹<?php echo number_format($amount, 2); ?></span></p>' +
                      '</div>',
                icon: 'info',
                confirmButtonColor: '#093c31'
            });
        }

        function handleDesktopSubmit() {
            if (selectedDesktopOpt === 'paytm') {
                Swal.fire({
                    title: 'Scan QR Code to Pay',
                    text: 'Please scan the QR code using Paytm or any other UPI app to make the payment.',
                    icon: 'info',
                    confirmButtonColor: '#093c31'
                });
            } else if (selectedDesktopOpt === 'upi-id') {
                var vpa = $('#desktopUpiInput').val().trim();
                if (!vpa || !vpa.includes('@')) {
                    Swal.fire({
                        title: 'Invalid UPI ID',
                        text: 'Please enter a valid UPI address (e.g. name@upi)',
                        icon: 'warning',
                        confirmButtonColor: '#093c31'
                    });
                    return;
                }
                
                $('#upiIdInput').val(vpa);
                submitUPIForm();
            }
        }

        function handlePaymentSubmit() {
            if (selectedOption === 'paytm') {
                window.location.href = "<?php echo isset($paytm) && !empty($paytm) ? $paytm : $orders; ?>";
            } else if (selectedOption === 'gpay') {
                window.location.href = "<?php echo str_replace('upi://pay', 'gpay://upi/pay', $orders); ?>";
            } else if (selectedOption === 'phonepe') {
                window.location.href = "<?php echo str_replace('upi://pay', 'phonepe://pay', $orders); ?>";
            } else if (selectedOption === 'upi-id') {
                var vpa = $('#upiIdInput').val().trim();
                if (!vpa || !vpa.includes('@')) {
                    Swal.fire({
                        title: 'Invalid UPI ID',
                        text: 'Please enter a valid UPI address (e.g. name@upi)',
                        icon: 'warning',
                        confirmButtonColor: '#093c31'
                    });
                    return;
                }
                submitUPIForm();
            }
        }

        function submitUPIForm() {
            Swal.fire({
                title: 'Sending UPI Request...',
                text: 'Please check your UPI app for the payment request.',
                icon: 'info',
                showConfirmButton: false,
                allowOutsideClick: false
            });

            $('<input>').attr({
                type: 'hidden',
                name: 'subupireq',
                value: '1'
            }).appendTo('#upiForm');

            $('#upiForm').submit();
        }

        // Timer & AJAX status check
        var paymentProcessed = false;
        var interval;

        function startTimer(duration, displayMob, displayPc) {
            var timer = duration, minutes, seconds;
            interval = setInterval(function () {
                minutes = parseInt(timer / 60, 10);
                seconds = parseInt(timer % 60, 10);

                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;

                displayMob.textContent = minutes + ":" + seconds;
                displayPc.textContent = minutes + ":" + seconds;

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
                    order_id: '<?php echo $order_id; ?>',
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
            var duration = <?php echo $remaining_seconds; ?>;
            var displayMob = document.querySelector('#timeout');
            var displayPc = document.querySelector('.desktop-timer');
            startTimer(duration, displayMob, displayPc);
            check();
            interval = setInterval(check, 5000);
        };
    </script>
    <script disable-devtool-auto="" src="https://pay.imb.org.in/Qrcode/disable-devtool.js" data-url="https://www.google.com/"></script> 
</body>
</html>


