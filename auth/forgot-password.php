<?php
include "config.php";
include 'function.php';

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
                
                // Send WhatsApp Message if function exists
                if (function_exists('sendWhatsAppMsg')) {
                    sendWhatsAppMsg($username, $msg);
                }

                echo '<script>
                    document.addEventListener("DOMContentLoaded", function() {
                        Swal.fire({
                            title: "Password Reset Successful!",
                            text: "Your new password has been sent to your registered WhatsApp number.",
                            confirmButtonText: "Login Now",
                            confirmButtonColor: "#07352D",
                            icon: "success"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "index.php";
                            }
                        });
                    });
                </script>';
            } else {
                echo '<script>
                    document.addEventListener("DOMContentLoaded", function() {
                        Swal.fire({
                            title: "Something Went Wrong!",
                            text: "Please try again later.",
                            confirmButtonText: "Ok",
                            confirmButtonColor: "#07352D",
                            icon: "error"
                        });
                    });
                </script>';
            }
        } else {
            echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        title: "PAN Number Mismatch!",
                        text: "The PAN number does not match our records.",
                        confirmButtonText: "Try Again",
                        confirmButtonColor: "#07352D",
                        icon: "error"
                    });
                });
            </script>';
        }
    } else {
        echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "Mobile Number Not Found!",
                    text: "This mobile number is not registered with us.",
                    confirmButtonText: "Try Again",
                    confirmButtonColor: "#07352D",
                    icon: "error"
                });
            });
        </script>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | Dezo</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../index.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .auth-container {
            width: 100%;
            max-width: 440px;
            padding: 40px;
            border-radius: 24px;
            text-align: center;
        }

        .auth-logo {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-color);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 30px;
        }

        .auth-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .auth-subtitle {
            color: var(--text-muted);
            margin-bottom: 32px;
            font-size: 15px;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control {
            width: 100%;
            padding: 14px 16px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            color: var(--text-color);
            font-size: 15px;
            font-family: inherit;
            transition: all 0.2s ease;
            box-sizing: border-box;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent-cyan);
            background: rgba(255, 255, 255, 0.05);
            box-shadow: 0 0 0 4px rgba(0, 242, 254, 0.1);
        }

        .auth-btn {
            width: 100%;
            padding: 14px;
            font-size: 16px;
            margin-top: 10px;
            border-radius: 12px;
        }

        .auth-links {
            margin-top: 24px;
            font-size: 14px;
            color: var(--text-muted);
        }

        .auth-links a {
            color: var(--accent-cyan);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .auth-links a:hover {
            color: var(--text-color);
        }

        .auth-footer {
            margin-top: 40px;
            font-size: 13px;
            color: var(--text-muted);
        }
    </style>
</head>
<body>
    
    <!-- Background Elements -->
    <div class="bg-shape bg-shape-1"></div>
    <div class="bg-shape bg-shape-2"></div>

    <div class="auth-container glass-panel">
        <a href="../index.html" class="auth-logo">
            <div class="logo-icon"><i class="bi bi-lightning-charge-fill"></i></div>
            Dezo
        </a>
        
        <h1 class="auth-title">Forgot Password?</h1>
        <p class="auth-subtitle">Enter your details to reset your password</p>

        <form action="forgot-password.php" method="POST">
            <div class="form-group">
                <label class="form-label">Mobile Number</label>
                <input type="text" name="username" class="form-control" placeholder="10-digit mobile number" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">PAN Number</label>
                <input type="text" name="pan" class="form-control" placeholder="Enter your PAN" required>
            </div>

            <button type="submit" name="submit" class="btn btn-primary auth-btn">Reset Password</button>
        </form>

        <div class="auth-links">
            <p>Remembered your password? <a href="index.php">Sign in</a></p>
        </div>

        <div class="auth-footer">
            Developer By <a href="https://tarikislam.in" target="_blank" style="color: var(--text-muted); text-decoration: underline;">tarikislam.in</a> | Made in India
        </div>
    </div>

</body>
</html>
