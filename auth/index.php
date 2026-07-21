<?php
session_start();
include "config.php";
include "function.php";

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Check if login form is submitted
if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE mobile = '$username'";
    $run = mysqli_query($conn, $query);
    $row = mysqli_fetch_array($run);

    if (mysqli_num_rows($run) > 0) {
        $hashFromDatabase = $row['password'];
        $acc_lock = $row['acc_lock'];
        $acc_ban = $row['acc_ban'];
        $byteuserid = $row['id'];

        if ($acc_ban == 'on') {
            echo '
            <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "Account Locked!",
                    text: "Please contact the administrator.",
                    icon: "error",
                    confirmButtonText: "Ok",
                    confirmButtonColor: "#07352D"
                }).then(() => {
                    window.location.href = "index.php";
                });
            });
            </script>';
        } else {
            if (password_verify($password, $hashFromDatabase)) {
                $_SESSION['username'] = $username;
                $_SESSION['user_id'] = $byteuserid;

                $query = "UPDATE users SET acc_lock = 0 WHERE mobile = '$username'";
                mysqli_query($conn, $query);

                echo '<script>window.location.href = "dashboard.php";</script>';
                exit;
            } else {
                $acc_lock++;
                $query = "UPDATE users SET acc_lock = $acc_lock WHERE mobile = '$username'";
                mysqli_query($conn, $query);

                if ($acc_lock >= 3) {
                    echo '
                    <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        Swal.fire({
                            title: "Account Locked!",
                            text: "Too many failed login attempts. Please contact the administrator.",
                            icon: "error",
                            confirmButtonText: "Ok",
                            confirmButtonColor: "#07352D"
                        }).then(() => {
                            window.location.href = "index.php";
                        });
                    });
                    </script>';
                } else {
                    echo '<script>document.addEventListener("DOMContentLoaded", function() { Swal.fire({title: "Invalid Password!", text: "Please try again.", icon: "error", confirmButtonColor: "#07352D"}); });</script>';
                }
            }
        }
    } else {
        echo '<script>document.addEventListener("DOMContentLoaded", function() { Swal.fire({title: "Invalid Username!", text: "No account found with this mobile number.", icon: "error", confirmButtonColor: "#07352D"}); });</script>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In | Dezo</title>
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
        
        <h1 class="auth-title">Welcome back</h1>
        <p class="auth-subtitle">Sign in to your Dezo account</p>


        <form action="index.php" method="POST">
            <div class="form-group">
                <label class="form-label">Mobile Number</label>
                <input type="text" name="username" class="form-control" placeholder="10-digit mobile number" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" name="submit" class="btn btn-primary auth-btn">Sign In</button>
        </form>

        <div class="auth-links">
            <p>Don't have an account? <a href="register.php">Sign up</a></p>
            <p style="margin-top: 8px;"><a href="forgot-password.php">Forgot your password?</a></p>
        </div>

        <div class="auth-footer">
            Developer By <a href="https://tarikislam.in" target="_blank" style="color: var(--text-muted); text-decoration: underline;">tarikislam.in</a> | Made in India
        </div>
    </div>

</body>
</html>
