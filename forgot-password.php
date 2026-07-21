<?php
include "auth/config.php";
include 'auth/function.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <title><?php echo $site_settings['brand_name']; ?> | Forgot Password</title>
    <meta name="description" content="Reset your <?php echo $site_settings['brand_name']; ?> password">
    <link rel="icon" type="image/x-icon" href="<?php echo $site_settings['logo_url']; ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.1.0/fonts/remixicon.css">
    <link rel="stylesheet" href="auth/auth-custom.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* Lock icon animation */
        .zg-lock-icon-wrap {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(7, 53, 45, 0.08), rgba(208, 232, 90, 0.15));
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            animation: lockPulse 2.5s ease-in-out infinite;
        }

        .zg-lock-icon-wrap i {
            font-size: 28px;
            color: #07352D;
        }

        @keyframes lockPulse {
            0%, 100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(7, 53, 45, 0.1); }
            50% { transform: scale(1.05); box-shadow: 0 0 0 12px rgba(7, 53, 45, 0); }
        }

        /* Success state */
        .zg-success-icon-wrap {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(16, 185, 129, 0.2));
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }

        .zg-success-icon-wrap i {
            font-size: 28px;
            color: #10b981;
        }

        /* Info text */
        .zg-info-text {
            font-size: 12px;
            color: #9ca3af;
            text-align: center;
            margin-top: 12px;
            line-height: 1.5;
        }

        .zg-info-text i {
            color: #07352D;
            margin-right: 4px;
        }
    </style>
</head>
<body class="auth-page">

<div class="zg-split">

    <!-- Left Panel - Illustration -->
    <div class="zg-left">
        <div class="zg-blob1"></div>
        <div class="zg-blob2"></div>
        <div class="zg-blob3"></div>
        <img src="common/img/payment-illustration.png"
             alt="Forgot Password"
             class="zg-left-illustration"
             onerror="this.style.display='none'">
        <div class="zg-feature-tags">
            <span class="zg-feature-tag"><i class="ri-shield-check-line"></i> Secure Reset</span>
            <span class="zg-feature-tag"><i class="ri-time-line"></i> Instant</span>
            <span class="zg-feature-tag"><i class="ri-lock-line"></i> Encrypted</span>
        </div>
    </div>

    <!-- Right Panel - Forgot Password Form -->
    <div class="zg-right">
        <div class="zg-form-container">
            <a href="./" class="zg-close" title="Go home">×</a>

            <div class="zg-logo">
                <h2>
                    <img src="<?php echo $site_settings['logo_url']; ?>" alt="<?php echo $site_settings['brand_name']; ?>">
                </h2>
            </div>

            <?php
            include "auth/config.php";
            if(isset($_POST['submit'])){
                $username = mysqli_real_escape_string($conn, $_POST['username']);
                $pan = mysqli_real_escape_string($conn, $_POST['pan']);

                $pass = rand(000000, 999999);
                $password = password_hash($pass, PASSWORD_BCRYPT);

                $fetch = "SELECT * FROM users WHERE mobile='$username'";
                $res = mysqli_query($conn, $fetch);
                $row = mysqli_fetch_array($res);

                if(mysqli_num_rows($res) > 0){
                    if($pan == $row['pan']){
                        $update = "UPDATE users SET password='$password' WHERE mobile='$username'";
                        $quer = mysqli_query($conn, $update);

                        if($quer){
                            $msg = "Dear " . $row['name'] . ",\n\nYour New Password is below:\n*Password:* $pass\n\nThanks & Regards,\n" . $site_settings['brand_name'];
                            
                            // Send WhatsApp Message
                            sendWhatsAppMsg($username, $msg);

                            echo '<script>
                                Swal.fire({
                                    title: "Password Reset Successful!",
                                    text: "Your new password has been sent to your registered WhatsApp number.",
                                    confirmButtonText: "Login Now",
                                    confirmButtonColor: "#07352D",
                                    icon: "success"
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = "auth/index";
                                    }
                                });
                            </script>';
                            exit;
                        } else {
                            echo '<script>
                                Swal.fire({
                                    title: "Something Went Wrong!",
                                    text: "Please try again later.",
                                    confirmButtonText: "Ok",
                                    confirmButtonColor: "#07352D",
                                    icon: "error"
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = "forgot-password";
                                    }
                                });
                            </script>';
                            exit;
                        }
                    } else {
                        echo '<script>
                            Swal.fire({
                                title: "PAN Number Mismatch!",
                                text: "The PAN number does not match our records.",
                                confirmButtonText: "Try Again",
                                confirmButtonColor: "#07352D",
                                icon: "error"
                            });
                        </script>';
                    }
                } else {
                    echo '<script>
                        Swal.fire({
                            title: "Mobile Number Not Found!",
                            text: "This mobile number is not registered with us.",
                            confirmButtonText: "Try Again",
                            confirmButtonColor: "#07352D",
                            icon: "error"
                        });
                    </script>';
                }
            }
            ?>

            <!-- Lock Icon -->
            <div class="zg-lock-icon-wrap">
                <i class="ri-lock-password-line"></i>
            </div>

            <div class="zg-title">Forgot Password?</div>
            <div class="zg-subtitle">Enter your mobile number & PAN to verify your identity and reset your password</div>

            <form class="auth-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <!-- Mobile Number -->
                <div class="zg-form-group">
                    <div class="zg-input-icon-wrap">
                        <i class="ri-smartphone-line input-icon"></i>
                        <input type="text" class="zg-form-control" id="username" name="username"
                               placeholder="Registered Mobile Number"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);"
                               maxlength="10" inputmode="numeric"
                               style="padding-left:42px;"
                               autofocus required>
                    </div>
                </div>

                <!-- PAN Number -->
                <div class="zg-form-group">
                    <div class="zg-input-icon-wrap">
                        <i class="ri-id-card-line input-icon"></i>
                        <input type="text" class="zg-form-control" id="pan" name="pan"
                               placeholder="PAN Number (e.g. ABCDE1234F)"
                               pattern="[A-Za-z]{5}[0-9]{4}[A-Za-z]{1}"
                               title="Enter PAN in format: ABCDE1234F"
                               oninput="this.value = this.value.toUpperCase();"
                               maxlength="10"
                               style="padding-left:42px;"
                               required>
                    </div>
                </div>

                <button type="submit" name="submit" class="zg-btn-primary">Reset Password</button>

                <div class="zg-info-text">
                    <i class="ri-information-line"></i>
                    A new password will be generated and sent to your registered contact
                </div>
            </form>

            <!-- Back to Login -->
            <div class="zg-auth-footer" style="margin-top:20px;">
                <a href="auth/index" style="display:inline-flex;align-items:center;gap:6px;">
                    <i class="ri-arrow-left-s-line"></i> Back to Login
                </a>
            </div>

            <div class="developer-credit" style="text-align: center; margin-top: 25px; font-size: 12px; color: #6b7280; font-weight: 500;">
                Developer By <a href="https://dgweb.in" style="color: #07352D; text-decoration: none; font-weight: 600;">DGWeb</a> | Support: +91 9219565158
            </div>
        </div>
    </div>
</div>

</body>
</html>