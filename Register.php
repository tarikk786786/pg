<?php
include "auth/config.php";
include 'auth/function.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <title>Register | <?php echo $site_settings['brand_name']; ?></title>
    <meta name="description" content="Create your <?php echo $site_settings['brand_name']; ?> account - Start managing payments today">
    <link rel="icon" type="image/x-icon" href="<?php echo $site_settings['logo_url']; ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.1.0/fonts/remixicon.css">
    <link rel="stylesheet" href="auth/auth-custom.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* Step indicator */
        .zg-step-indicator {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin: 12px 0 4px;
        }

        .zg-step-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #e2e5ea;
            transition: all 0.3s ease;
        }

        .zg-step-dot.active {
            width: 24px;
            border-radius: 4px;
            background: #07352D;
        }

        .zg-step.hidden {
            display: none;
        }

        /* Mobile input styling */
        .zg-mobile-input {
            font-size: 22px;
            letter-spacing: 4px;
            font-weight: 700;
            text-align: center;
        }

        .zg-mobile-display {
            background: #f3f4f6 !important;
            color: #6b7280 !important;
            cursor: default !important;
        }

        /* Two column row */
        .zg-row {
            display: flex;
            gap: 10px;
        }

        .zg-col {
            flex: 1;
            min-width: 0;
        }

        /* Terms checkbox */
        .zg-terms {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            margin: 10px 0 4px;
            font-size: 12.5px;
            color: #6b7280;
        }

        .zg-terms input[type="checkbox"] {
            width: 15px;
            height: 15px;
            cursor: pointer;
            accent-color: #07352D;
            margin: 0;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .zg-terms a {
            color: #07352D;
            font-weight: 600;
            text-decoration: none;
        }

        .zg-terms a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .zg-row {
                flex-direction: column;
                gap: 0;
            }

            .zg-right {
                align-items: flex-start !important;
            }
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
             alt="Register Illustration"
             class="zg-left-illustration"
             onerror="this.style.display='none'">
        <div class="zg-feature-tags">
            <span class="zg-feature-tag"><i class="ri-shield-check-line"></i> Secure</span>
            <span class="zg-feature-tag"><i class="ri-timer-flash-line"></i> Quick Setup</span>
            <span class="zg-feature-tag"><i class="ri-customer-service-line"></i> 24/7 Support</span>
        </div>
    </div>

    <!-- Right Panel - Register Form -->
    <div class="zg-right">
        <div class="zg-form-container">
            <a href="./" class="zg-close" title="Go home">×</a>

            <div class="zg-logo">
                <h2>
                    <img src="<?php echo $site_settings['logo_url']; ?>" alt="<?php echo $site_settings['brand_name']; ?>">
                </h2>
            </div>

            <!-- Step Indicator -->
            <div class="zg-step-indicator">
                <div class="zg-step-dot active" id="dot1"></div>
                <div class="zg-step-dot" id="dot2"></div>
            </div>

            <?php
            // Handle registration
            if (isset($_POST['create'])) {
                $mobile = $_POST['mobile'];
                $email = $_POST['email'];

                $checkMobileQuery = "SELECT * FROM `users` WHERE `mobile` = '$mobile'";
                $checkMobileResult = mysqli_query($conn, $checkMobileQuery);

                $checkEmailQuery = "SELECT * FROM `users` WHERE `email` = '$email'";
                $checkEmailResult = mysqli_query($conn, $checkEmailQuery);

                if (mysqli_num_rows($checkMobileResult) > 0) {
                    echo '<script>
                    Swal.fire({
                        title: "Mobile Number Already Exists!",
                        text: "Please use a different mobile number.",
                        confirmButtonText: "Ok",
                        confirmButtonColor: "#07352D",
                        icon: "error"
                    })
                    </script>';
                    exit;
                } elseif (mysqli_num_rows($checkEmailResult) > 0) {
                    echo '<script>
                    Swal.fire({
                        title: "Email Already Exists!",
                        text: "Please use a different email address.",
                        confirmButtonText: "Ok",
                        confirmButtonColor: "#07352D",
                        icon: "error"
                    })
                    </script>';
                    exit;
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
                        Swal.fire({
                            title: "PAN Number Already Exists!",
                            text: "Please use a different PAN number.",
                            confirmButtonText: "Ok",
                            confirmButtonColor: "#07352D",
                            icon: "error"
                        })
                        </script>';
                        exit;
                    } elseif (mysqli_num_rows($checkAadharResult) > 0) {
                        echo '<script>
                        Swal.fire({
                            title: "Aadhaar Number Already Exists!",
                            text: "Please use a different Aadhaar number.",
                            confirmButtonText: "Ok",
                            confirmButtonColor: "#07352D",
                            icon: "error"
                        })
                        </script>';
                        exit;
                    } else {
                        $location = $_POST['location'];
                        $key = md5(rand(00000000, 99999999));
                        $pass = password_hash($password, PASSWORD_BCRYPT);
                        $today = date("Y-m-d", strtotime("+3 days"));

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

                        $instanceId = generateRandomInstanceId();

                        $register = "INSERT INTO `users`(`name`, `mobile`, `role`, `password`, `email`, `company`, `pin`, `pan`, `aadhaar`, `location`, `user_token`, `expiry`, `instance_id`) 
                        VALUES ('$name', '$mobile', 'User', '$pass', '$email', '$company', '$pin', '$pan', '$aadhaar', '$location', '$key', '$today', '$instanceId')";

                        $result = mysqli_query($conn, $register);

                        if ($result) {
                            echo '<script>
                            Swal.fire({
                                title: "Registration Successful!",
                                text: "Your account has been created. Please login.",
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
                                title: "Registration Failed!",
                                text: "Something went wrong. Please try again.",
                                confirmButtonText: "Ok",
                                confirmButtonColor: "#07352D",
                                icon: "error"
                            })
                            </script>';
                            exit;
                        }
                    }
                }
            }
            ?>

            <!-- STEP 1: Mobile Number -->
            <div class="zg-step" id="step1">
                <div class="zg-title">Create Account</div>
                <div class="zg-subtitle">Enter your mobile number to get started</div>

                <div class="zg-form-group">
                    <div class="zg-input-icon-wrap">
                        <i class="ri-smartphone-line input-icon"></i>
                        <input type="tel" class="zg-form-control zg-mobile-input"
                               id="mobileInput" placeholder="Mobile Number"
                               maxlength="10" inputmode="numeric"
                               style="padding-left:42px; text-align:left; font-size:16px; letter-spacing:1px;"
                               autofocus>
                    </div>
                    <div id="mobileError" style="color:#ef4444;font-size:12px;margin-top:5px;display:none;">
                        Please enter a valid 10-digit mobile number.
                    </div>
                </div>

                <div class="zg-form-group" id="otpGroup" style="display:none; margin-top:15px;">
                    <div class="zg-input-icon-wrap">
                        <i class="ri-message-3-line input-icon"></i>
                        <input type="text" class="zg-form-control zg-mobile-input"
                               id="otpInput" placeholder="Enter 6-digit OTP"
                               maxlength="6" inputmode="numeric"
                               style="padding-left:42px; text-align:left; font-size:16px; letter-spacing:4px;">
                    </div>
                    <div id="otpMessage" style="font-size:12px;margin-top:5px;display:none;"></div>
                    <div style="text-align:right; margin-top:8px;">
                        <a href="javascript:void(0);" onclick="sendOTP()" style="font-size:12px; color: #07352D; font-weight:600; text-decoration:none;">Resend OTP</a>
                    </div>
                </div>

                <button type="button" class="zg-btn-primary" id="btnSendOTP" onclick="sendOTP()">
                    Send WhatsApp OTP &rarr;
                </button>
                <button type="button" class="zg-btn-primary" id="btnVerifyOTP" style="display:none; margin-top:15px;" onclick="verifyOTP()">
                    Verify & Continue &rarr;
                </button>

                <p class="zg-auth-footer" style="margin-top:20px;">
                    Already have an account?
                    <a href="auth/index">Sign In</a>
                </p>
            </div>

            <!-- STEP 2: Complete Registration -->
            <div class="zg-step hidden" id="step2">
                <button type="button" class="zg-back-btn" onclick="goToStep1()">
                    <i class="ri-arrow-left-line"></i> Back
                </button>

                <div class="zg-title" style="text-align:left;font-size:19px;margin-bottom:3px;">Complete Registration</div>
                <div class="zg-subtitle" style="text-align:left;margin-bottom:16px;">Fill in your details below</div>

                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="registerForm">

                    <!-- Mobile (readonly) -->
                    <div class="zg-form-group">
                        <div class="zg-input-icon-wrap">
                            <i class="ri-smartphone-line input-icon"></i>
                            <input type="tel" class="zg-form-control zg-mobile-display"
                                   name="mobile" id="mobileDisplay"
                                   placeholder="Mobile Number *"
                                   maxlength="10" inputmode="numeric"
                                   style="padding-left:42px;"
                                   readonly required>
                        </div>
                    </div>

                    <!-- Name -->
                    <div class="zg-form-group">
                        <div class="zg-input-icon-wrap">
                            <i class="ri-user-line input-icon"></i>
                            <input type="text" class="zg-form-control" name="name"
                                   placeholder="Full Name *" style="padding-left:42px;" required>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="zg-form-group">
                        <div class="zg-input-icon-wrap">
                            <i class="ri-mail-line input-icon"></i>
                            <input type="email" class="zg-form-control" name="email"
                                   placeholder="Email Address *" style="padding-left:42px;" required>
                        </div>
                    </div>

                    <!-- Password Row -->
                    <div class="zg-row">
                        <div class="zg-col zg-form-group">
                            <div class="zg-input-icon-wrap zg-pwd-wrap">
                                <i class="ri-lock-line input-icon"></i>
                                <input type="password" class="zg-form-control" name="password"
                                       id="pwd1" placeholder="Password *" minlength="6"
                                       style="padding-left:42px; padding-right:42px;" required>
                                <button type="button" class="zg-pwd-toggle" onclick="togglePwd('pwd1','eye1')">
                                    <i class="ri-eye-off-line" id="eye1"></i>
                                </button>
                            </div>
                        </div>
                        <div class="zg-col zg-form-group">
                            <div class="zg-pwd-wrap">
                                <input type="password" class="zg-form-control" name="password_confirmation"
                                       id="pwd2" placeholder="Confirm *"
                                       style="padding-right:42px;" required>
                                <button type="button" class="zg-pwd-toggle" onclick="togglePwd('pwd2','eye2')">
                                    <i class="ri-eye-off-line" id="eye2"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Company -->
                    <div class="zg-form-group">
                        <div class="zg-input-icon-wrap">
                            <i class="ri-building-line input-icon"></i>
                            <input type="text" class="zg-form-control" name="company"
                                   placeholder="Company / Business Name *" style="padding-left:42px;" required>
                        </div>
                    </div>

                    <!-- Aadhaar & PAN Row -->
                    <div class="zg-row">
                        <div class="zg-col zg-form-group">
                            <div class="zg-input-icon-wrap">
                                <i class="ri-bank-card-line input-icon"></i>
                                <input type="text" class="zg-form-control" name="aadhaar"
                                       placeholder="Aadhaar Number *" style="padding-left:42px;"
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 12);"
                                       maxlength="12" required>
                            </div>
                        </div>
                        <div class="zg-col zg-form-group">
                            <div class="zg-input-icon-wrap">
                                <i class="ri-id-card-line input-icon"></i>
                                <input type="text" class="zg-form-control" name="pan"
                                       placeholder="PAN Number *" style="padding-left:42px;"
                                       pattern="[A-Za-z]{5}[0-9]{4}[A-Za-z]{1}"
                                       title="Enter PAN in format: ABCDE1234F"
                                       oninput="this.value = this.value.toUpperCase();"
                                       maxlength="10" required>
                            </div>
                        </div>
                    </div>

                    <!-- Location & Pin Row -->
                    <div class="zg-row">
                        <div class="zg-col zg-form-group">
                            <div class="zg-input-icon-wrap">
                                <i class="ri-map-pin-line input-icon"></i>
                                <input type="text" class="zg-form-control" name="location"
                                       placeholder="Location / City *" style="padding-left:42px;" required>
                            </div>
                        </div>
                        <div class="zg-col zg-form-group">
                            <div class="zg-input-icon-wrap">
                                <i class="ri-map-2-line input-icon"></i>
                                <input type="text" class="zg-form-control" name="pin"
                                       placeholder="Pincode *" style="padding-left:42px;"
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);"
                                       maxlength="6" required>
                            </div>
                        </div>
                    </div>

                    <!-- Terms -->
                    <div class="zg-terms">
                        <input type="checkbox" id="terms" required>
                        <label for="terms">
                            I agree to the
                            <a href="javascript:void(0);">Privacy Policy & Terms</a>
                        </label>
                    </div>

                    <button type="submit" name="create" class="zg-btn-primary" id="submitBtn">
                        <span id="submitText">Create Account</span>
                    </button>

                    <p class="zg-auth-footer" style="margin-top:16px;">
                        Already have an account? <a href="auth/index">Sign In</a>
                    </p>
                </form>
            </div>

            <div class="developer-credit" style="text-align: center; margin-top: 25px; font-size: 12px; color: #6b7280; font-weight: 500;">
                Developer By <a href="https://dgweb.in" style="color: #07352D; text-decoration: none; font-weight: 600;">DGWeb</a> | Support: +91 9219565158
            </div>
        </div>
    </div>
</div>

<script>
// Numbers only helper
function numbersOnly(el) {
    if (!el) return;
    el.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '').slice(0, 10);
    });
    el.addEventListener('paste', function(e) {
        e.preventDefault();
        this.value = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 10);
    });
}
numbersOnly(document.getElementById('mobileInput'));

// Password toggle
function togglePwd(inputId, iconId) {
    var inp = document.getElementById(inputId);
    var ico = document.getElementById(iconId);
    if (inp.type === 'password') {
        inp.type = 'text';
        ico.className = 'ri-eye-line';
    } else {
        inp.type = 'password';
        ico.className = 'ri-eye-off-line';
    }
}

// Send OTP
function sendOTP() {
    var mobile = document.getElementById('mobileInput').value;
    if (mobile.length !== 10) {
        document.getElementById('mobileError').style.display = 'block';
        document.getElementById('mobileInput').focus();
        return;
    }
    document.getElementById('mobileError').style.display = 'none';
    
    var btn = document.getElementById('btnSendOTP');
    btn.innerHTML = 'Sending...';
    btn.disabled = true;

    var formData = new FormData();
    formData.append('action', 'send_otp');
    formData.append('mobile', mobile);

    fetch('auth/otp_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        btn.innerHTML = 'Send WhatsApp OTP &rarr;';
        btn.disabled = false;

        if (data.status === 'success') {
            document.getElementById('mobileInput').readOnly = true;
            document.getElementById('btnSendOTP').style.display = 'none';
            document.getElementById('otpGroup').style.display = 'block';
            document.getElementById('btnVerifyOTP').style.display = 'block';
            
            var msgEl = document.getElementById('otpMessage');
            msgEl.style.color = '#10b981';
            msgEl.innerText = data.message;
            msgEl.style.display = 'block';
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    })
    .catch(error => {
        btn.innerHTML = 'Send WhatsApp OTP &rarr;';
        btn.disabled = false;
        Swal.fire('Error', 'Failed to communicate with server.', 'error');
    });
}

// Verify OTP
function verifyOTP() {
    var mobile = document.getElementById('mobileInput').value;
    var otp = document.getElementById('otpInput').value;
    
    if (otp.length !== 6) {
        var msgEl = document.getElementById('otpMessage');
        msgEl.style.color = '#ef4444';
        msgEl.innerText = 'Please enter a valid 6-digit OTP.';
        msgEl.style.display = 'block';
        return;
    }

    var btn = document.getElementById('btnVerifyOTP');
    btn.innerHTML = 'Verifying...';
    btn.disabled = true;

    var formData = new FormData();
    formData.append('action', 'verify_otp');
    formData.append('mobile', mobile);
    formData.append('otp', otp);

    fetch('auth/otp_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        btn.innerHTML = 'Verify & Continue &rarr;';
        btn.disabled = false;

        if (data.status === 'success') {
            // OTP is correct, proceed to step 2
            goToStep2(mobile);
        } else {
            var msgEl = document.getElementById('otpMessage');
            msgEl.style.color = '#ef4444';
            msgEl.innerText = data.message;
            msgEl.style.display = 'block';
        }
    })
    .catch(error => {
        btn.innerHTML = 'Verify & Continue &rarr;';
        btn.disabled = false;
        Swal.fire('Error', 'Failed to verify OTP.', 'error');
    });
}

// Step 1 -> Step 2
function goToStep2(mobileNumber) {
    document.getElementById('mobileDisplay').value = mobileNumber;
    document.getElementById('step1').classList.add('hidden');
    document.getElementById('step2').classList.remove('hidden');
    document.getElementById('dot1').classList.remove('active');
    document.getElementById('dot2').classList.add('active');
    window.scrollTo(0, 0);
}

// Step 2 -> Step 1
function goToStep1() {
    document.getElementById('step2').classList.add('hidden');
    document.getElementById('step1').classList.remove('hidden');
    document.getElementById('dot2').classList.remove('active');
    document.getElementById('dot1').classList.add('active');
}

// Enter key on mobile input
document.getElementById('mobileInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        sendOTP();
    }
});

// Enter key on OTP input
document.getElementById('otpInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        verifyOTP();
    }
});

// Submit button loading state
var registerForm = document.getElementById('registerForm');
if (registerForm) {
    registerForm.addEventListener('submit', function() {
        var btn = document.getElementById('submitBtn');
        var text = document.getElementById('submitText');
        text.textContent = 'Creating Account...';
        btn.disabled = true;
        btn.style.opacity = '0.7';
    });
}
</script>

</body>
</html>