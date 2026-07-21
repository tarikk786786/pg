<?php 
include "header.php"; 

if ($userdata["role"] != 'Admin') {
    echo '<script>window.location.href = "dashboard";</script>';
    exit;
}

if (isset($_POST['create'])) {
    $mobile = db_real_escape_string($conn, $_POST['mobile']);
    $email = db_real_escape_string($conn, $_POST['email']);

    // Check if the mobile number already exists in the database
    $checkMobileQuery = "SELECT * FROM `users` WHERE `mobile` = '$mobile'";
    $checkMobileResult = db_query($conn, $checkMobileQuery);

    // Check if the email already exists in the database
    $checkEmailQuery = "SELECT * FROM `users` WHERE `email` = '$email'";
    $checkEmailResult = db_query($conn, $checkEmailQuery);

    if (db_num_rows($checkMobileResult) > 0) {
        echo '
        <script>
            Swal.fire({
                title: "Error!",
                text: "Mobile number already exists!",
                confirmButtonText: "Ok",
                icon: "error"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "add_merchant";
                }
            });
        </script>';
        exit;
    } elseif (db_num_rows($checkEmailResult) > 0) {
        echo '
        <script>
            Swal.fire({
                title: "Error!",
                text: "Email already exists!",
                confirmButtonText: "Ok",
                icon: "error"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "add_merchant";
                }
            });
        </script>';
        exit;
    } else {
        // Proceed with user registration
        $password = db_real_escape_string($conn, $_POST['password']);
        $name = db_real_escape_string($conn, $_POST['name']);
        $company = db_real_escape_string($conn, $_POST['company']);
        $pin = db_real_escape_string($conn, $_POST['pin']);
        $pan = db_real_escape_string($conn, $_POST['pan']);
        $aadhaar = db_real_escape_string($conn, $_POST['aadhaar']);
        $location = db_real_escape_string($conn, $_POST['location']);
        
        $key = md5(rand(00000000, 99999999));
        $pass = password_hash($password, PASSWORD_BCRYPT);
        
        // BUG FIX: Correct way to add 3 days to current date
        $expiry_date = date("Y-m-d", strtotime("+3 days"));

        $register = "INSERT INTO `users`(`name`, `mobile`, `role`, `password`, `email`, `company`, `pin`, `pan`, `aadhaar`, `location`, `user_token`, `expiry`) 
                     VALUES ('$name','$mobile','User','$pass','$email','$company','$pin','$pan','$aadhaar','$location','$key','$expiry_date')";
        
        $result = db_query($conn, $register);

        if ($result) {
            echo '
            <script>
                Swal.fire({
                    title: "Success!",
                    text: "Merchant account created successfully!",
                    confirmButtonText: "Ok",
                    icon: "success"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "merchant_list";
                    }
                });
            </script>';
            exit;
        } else {
            echo '
            <script>
                Swal.fire({
                    title: "Failed!",
                    text: "Something went wrong while creating the merchant. Error: ' . db_error($conn) . '",
                    confirmButtonText: "Ok",
                    icon: "error"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "add_merchant";
                    }
                });
            </script>';
            exit;
        }
    }
}
?>

<div class="pi-hero-card pi-hero-card-merchant mb-4 p-4 text-white rounded-3">
    <div class="row g-4 align-items-center">
        <div class="col-md-8">
            <span class="pi-hero-eyebrow"><i class="bi bi-person-plus-fill me-1"></i>Admin Action</span>
            <h2>Create New User</h2>
            <p>Add a new user to the platform. They will receive 3 days of trial subscription by default.</p>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="pi-card p-4">
            <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-card-checklist text-primary me-2"></i>User Details</h5>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div class="row g-4">
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-bold text-dark mb-2">User Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" placeholder="Full Name" class="form-control form-control-lg fs-6" required>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-bold text-dark mb-2">Company / Shop Name <span class="text-danger">*</span></label>
                        <input type="text" name="company" placeholder="Business Name" class="form-control form-control-lg fs-6" required>
                    </div>
                    
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-bold text-dark mb-2">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" placeholder="Email Address" class="form-control form-control-lg fs-6" required>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-bold text-dark mb-2">Mobile Number <span class="text-danger">*</span></label>
                        <input type="text" name="mobile" placeholder="10-digit Mobile No" class="form-control form-control-lg fs-6" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);" required>
                    </div>
                    
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-bold text-dark mb-2">Login Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" placeholder="Secure Password" class="form-control form-control-lg fs-6" required>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-bold text-dark mb-2">Area PIN Code <span class="text-danger">*</span></label>
                        <input type="text" name="pin" placeholder="6-digit PIN" class="form-control form-control-lg fs-6" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label fw-bold text-dark mb-2">PAN Number <span class="text-danger">*</span></label>
                        <input type="text" name="pan" placeholder="10-character PAN" class="form-control form-control-lg fs-6" oninput="this.value = this.value.toUpperCase();" maxlength="10" required>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-bold text-dark mb-2">Aadhaar Number <span class="text-danger">*</span></label>
                        <input type="text" name="aadhaar" placeholder="12-digit Aadhaar" class="form-control form-control-lg fs-6" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 12);" required>
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label fw-bold text-dark mb-2">Full Address / Location <span class="text-danger">*</span></label>
                        <input type="text" name="location" placeholder="City, State, Address" class="form-control form-control-lg fs-6" required>
                    </div>
                    
                    <div class="col-12 mt-4 text-center text-md-end">
                        <button type="submit" name="create" class="btn btn-pi-primary btn-lg px-5 fw-bold w-100 w-md-auto">
                            <i class="bi bi-person-plus-fill me-2"></i>Create User Account
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>

