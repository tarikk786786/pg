<?php 
include "header.php"; 

if ($userdata["role"] != 'Admin') {
    echo '<script>window.location.href = "dashboard";</script>';
    exit;
}

// Removed ALTER TABLE for otp_status since we use is_otp

$mobileno = $_REQUEST['mobileno'] ?? '';
$qyt = "SELECT * FROM users WHERE mobile='$mobileno'";
$act = db_query($conn, $qyt);
$day = db_fetch_array($act);

if (!$day) {
    echo '<script>window.location.href = "merchant_list";</script>';
    exit;
}

if (isset($_REQUEST['update'])) {
    $mobilex = db_real_escape_string($conn, $_REQUEST['mobile']);
    $email = db_real_escape_string($conn, $_REQUEST['email']);
    $name = db_real_escape_string($conn, $_REQUEST['name']);
    $company = db_real_escape_string($conn, $_REQUEST['company']);
    $pin = db_real_escape_string($conn, $_REQUEST['pin']);
    $pan = db_real_escape_string($conn, $_REQUEST['pan']);
    $aadhaar = db_real_escape_string($conn, $_REQUEST['aadhaar']);
    $location = db_real_escape_string($conn, $_REQUEST['location']);
    $exp = db_real_escape_string($conn, $_REQUEST['expiry']); 
    $is_otp = db_real_escape_string($conn, $_REQUEST['is_otp'] ?? 'YES'); // OTP option

    $upgc = "UPDATE users SET name='$name', email='$email', company='$company', pin='$pin', pan='$pan', aadhaar='$aadhaar', location='$location', expiry='$exp', is_otp='$is_otp' WHERE mobile='$mobilex'";
    $resvp = db_query($conn, $upgc);

    if ($resvp) {
        $admin_id = $userdata['id'];
        $details = db_real_escape_string($conn, "Updated profile for merchant mobile $mobilex");
        db_query($conn, "INSERT INTO audit_logs (user_id, action, details) VALUES ($admin_id, 'Update Merchant', '$details')");
        
        echo '
        <script>
            Swal.fire({
                icon: "success",
                title: "Merchant Updated Successfully!",
                confirmButtonText: "Ok",
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
                icon: "error",
                title: "Update Failed!",
                text: "' . db_error($conn) . '",
                confirmButtonText: "Ok",
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "merchant_list";
                }
            });
        </script>';
        exit;
    }
}
?>

<div class="pi-hero-card pi-hero-card-merchant mb-4 p-4 text-white rounded-3">
    <div class="row g-4 align-items-center">
        <div class="col-md-8">
            <span class="pi-hero-eyebrow"><i class="bi bi-pencil-square me-1"></i>Admin Action</span>
            <h2>Edit Merchant</h2>
            <p>Update personal details, KYC info, subscription expiry, and OTP settings for <?php echo htmlspecialchars($day['name']); ?>.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="merchant_list" class="btn btn-light text-dark fw-bold px-4 py-2" style="border-radius: 12px;">
                <i class="bi bi-arrow-left me-2"></i>Back to List
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="pi-card p-4">
            <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-person-lines-fill text-primary me-2"></i>Merchant Profile Update</h5>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="mobileno" value="<?php echo htmlspecialchars($mobileno); ?>">
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-muted">Merchant Name</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($day['name']); ?>" class="form-control py-2" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-muted">Company / Business Name</label>
                        <input type="text" name="company" value="<?php echo htmlspecialchars($day['company']); ?>" class="form-control py-2" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-muted">Email Address</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($day['email']); ?>" class="form-control py-2" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-muted">Mobile Number (Non-Editable)</label>
                        <input type="text" name="mobile" value="<?php echo htmlspecialchars($day['mobile']); ?>" class="form-control py-2 bg-light text-muted" readonly>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-muted">Area PIN Code</label>
                        <input type="text" name="pin" value="<?php echo htmlspecialchars($day['pin']); ?>" class="form-control py-2" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-muted">Location / Address</label>
                        <input type="text" name="location" value="<?php echo htmlspecialchars($day['location']); ?>" class="form-control py-2" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-muted">PAN Number</label>
                        <input type="text" name="pan" value="<?php echo htmlspecialchars($day['pan']); ?>" class="form-control py-2" oninput="this.value = this.value.toUpperCase();" maxlength="10" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-muted">Aadhaar Number</label>
                        <input type="text" name="aadhaar" value="<?php echo htmlspecialchars($day['aadhaar']); ?>" class="form-control py-2" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 12);" required>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-dark"><i class="bi bi-calendar-event text-danger me-1"></i> Subscription Expiry</label>
                        <input type="date" name="expiry" value="<?php echo htmlspecialchars($day['expiry'] ?? date('Y-m-d')); ?>" class="form-control py-2" required>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-dark"><i class="bi bi-shield-lock text-success me-1"></i> OTP Status</label>
                        <select name="is_otp" class="form-select py-2">
                            <option value="YES" <?php echo (($day['is_otp'] ?? 'YES') == 'YES') ? 'selected' : ''; ?>>Enabled (YES)</option>
                            <option value="NO" <?php echo (($day['is_otp'] ?? '') == 'NO') ? 'selected' : ''; ?>>Disabled (NO)</option>
                        </select>
                        <div class="small text-muted mt-1">Enable or disable login OTP for this merchant.</div>
                    </div>
                    
                    <div class="col-12 mt-4 text-end">
                        <button type="submit" name="update" class="btn btn-pi-primary px-5 py-2 fw-semibold">
                            <i class="bi bi-save me-2"></i>Update Merchant
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>