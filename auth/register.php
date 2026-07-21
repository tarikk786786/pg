<?php
include "config.php";
include 'function.php';

if (isset($_POST['create'])) {
    $mobile = $_POST['mobile'];
    $email = $_POST['email'];

    $checkMobileQuery = "SELECT * FROM `users` WHERE `mobile` = '$mobile'";
    $checkMobileResult = mysqli_query($conn, $checkMobileQuery);

    $checkEmailQuery = "SELECT * FROM `users` WHERE `email` = '$email'";
    $checkEmailResult = mysqli_query($conn, $checkEmailQuery);

    if (mysqli_num_rows($checkMobileResult) > 0) {
        echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                title: "Mobile Number Already Exists!",
                text: "Please use a different mobile number.",
                confirmButtonText: "Ok",
                confirmButtonColor: "#07352D",
                icon: "error"
            });
        });
        </script>';
    } elseif (mysqli_num_rows($checkEmailResult) > 0) {
        echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                title: "Email Already Exists!",
                text: "Please use a different email address.",
                confirmButtonText: "Ok",
                confirmButtonColor: "#07352D",
                icon: "error"
            });
        });
        </script>';
    } else {
        $password = $_POST['password'];
        $name = $_POST['name'];
        $company = $_POST['company'];
        $pin = $_POST['pin'];
        $pan = $_POST['pan'];
        $aadhaar = $_POST['aadhaar'];

        $checkpan = "SELECT * FROM `users` WHERE `pan` = '$pan'";
        $checkpanResult = mysqli_query($conn, $checkpan);

        $checkaadhar = "SELECT * FROM `users` WHERE `aadhaar` = '$aadhaar'";
        $checkAadharResult = mysqli_query($conn, $checkaadhar);

        if (mysqli_num_rows($checkpanResult) > 0) {
            echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "PAN Number Already Exists!",
                    text: "Please use a different PAN number.",
                    confirmButtonText: "Ok",
                    confirmButtonColor: "#07352D",
                    icon: "error"
                });
            });
            </script>';
        } elseif (mysqli_num_rows($checkAadharResult) > 0) {
            echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "Aadhaar Number Already Exists!",
                    text: "Please use a different Aadhaar number.",
                    confirmButtonText: "Ok",
                    confirmButtonColor: "#07352D",
                    icon: "error"
                });
            });
            </script>';
        } else {
            $location = $_POST['location'];
            $key = md5(rand(00000000, 99999999));
            $pass = password_hash($password, PASSWORD_BCRYPT);
            $today = date("Y-m-d", strtotime("+3 days"));

            if (!function_exists('generateRandomInstanceId')) {
                function generateRandomInstanceId($length = 16) {
                    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                    $randomString = 'I';
                    for ($i = 1; $i < $length - 6; $i++) {
                        $randomString .= $characters[rand(0, strlen($characters) - 1)];
                    }
                    $currentTime = time();
                    $lastSixDigits = substr(strval($currentTime), -6);
                    $randint = rand(100, 900);
                    return $randomString . $randint . $lastSixDigits;
                }
            }

            $instanceId = generateRandomInstanceId();

            $register = "INSERT INTO `users`(`name`, `mobile`, `role`, `password`, `email`, `company`, `pin`, `pan`, `aadhaar`, `location`, `user_token`, `expiry`, `instance_id`) 
            VALUES ('$name', '$mobile', 'User', '$pass', '$email', '$company', '$pin', '$pan', '$aadhaar', '$location', '$key', '$today', '$instanceId')";

            $result = mysqli_query($conn, $register);

            if ($result) {
                echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        title: "Registration Successful!",
                        text: "Your account has been created. Please login.",
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
                        title: "Registration Failed!",
                        text: "Something went wrong. Please try again.",
                        confirmButtonText: "Ok",
                        confirmButtonColor: "#07352D",
                        icon: "error"
                    });
                });
                </script>';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Dezo</title>
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
            max-width: 600px;
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
        
        .form-row {
            display: flex;
            gap: 15px;
        }

        .form-row .form-group {
            flex: 1;
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

        @media (max-width: 600px) {
            .form-row {
                flex-direction: column;
                gap: 0;
            }
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
        
        <h1 class="auth-title">Create your account</h1>
        <p class="auth-subtitle">Join Dezo to start accepting payments</p>

        <form action="register.php" method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" placeholder="John Doe" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Mobile Number</label>
                    <input type="tel" name="mobile" class="form-control" placeholder="10-digit mobile" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="you@company.com" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Business Name</label>
                    <input type="text" name="company" class="form-control" placeholder="Acme Corp" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">PAN Number</label>
                    <input type="text" name="pan" class="form-control" placeholder="ABCDE1234F" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Aadhaar Number</label>
                    <input type="text" name="aadhaar" class="form-control" placeholder="1234 5678 9012" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Location (City)</label>
                    <input type="text" name="location" class="form-control" placeholder="Mumbai" required>
                </div>
                <div class="form-group">
                    <label class="form-label">PIN Code</label>
                    <input type="text" name="pin" class="form-control" placeholder="400001" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" name="create" class="btn btn-primary auth-btn">Create Account</button>
        </form>

        <div class="auth-links">
            <p>Already have an account? <a href="index.php">Sign in</a></p>
        </div>

        <div class="auth-footer">
            Developer By <a href="https://tarikislam.in" target="_blank" style="color: var(--text-muted); text-decoration: underline;">tarikislam.in</a> | Made in India
        </div>
    </div>

</body>
</html>
