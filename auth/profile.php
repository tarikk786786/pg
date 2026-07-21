<?php
include "header.php";
include "config.php";

$is_otp = '';
$whatsapp_alert = '';
$email_alert = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $is_otp = $_POST['is_otp'];
    $whatsapp_alert = $_POST['whatsapp_alert'];
    $email_alert = $_POST['email_alert'];
    $mobile = $_POST['mobile'];

    $query = "UPDATE users SET is_otp = ?, whatsapp_alert = ?, email_alert = ? WHERE mobile = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $is_otp, $whatsapp_alert, $email_alert, $mobile);

    if ($stmt->execute()) {
        echo "<script>
            Swal.fire({
                title: 'Success!',
                text: 'Profile updated successfully!',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                title: 'Error!',
                text: 'Error updating profile: " . addslashes($stmt->error) . "',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        </script>";
    }
    $stmt->close();
}

$query = "SELECT * FROM users WHERE mobile = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $mobile);
$stmt->execute();
$result = $stmt->get_result();
$userdata = $result->fetch_assoc();
$stmt->close();
?>

<!-- Profile Hero Banner -->
<div class="pi-hero-card pi-hero-card-merchant mb-4">
    <div class="row g-4 align-items-center">
        <div class="col-lg-12">
            <span class="pi-hero-eyebrow"><i class="bi bi-person-circle me-1"></i>Account Details</span>
            <h2>My Profile</h2>
            <p>Manage your login credentials, notification alerts, and security options.</p>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="pi-card p-4">
            <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom">
                <h5 class="fw-bold mb-0 text-primary">
                    <i class="bi bi-person-bounding-box me-2"></i>My Profile Settings
                </h5>
                <span class="badge bg-primary px-3 py-2">Account: <?php echo htmlspecialchars($userdata['role']); ?></span>
            </div>

            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-muted">Instance ID</label>
                        <input type="text" value="<?php echo htmlspecialchars($userdata['instance_id'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control bg-light" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-muted">Full Name</label>
                        <input type="text" value="<?php echo htmlspecialchars($userdata['name'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control bg-light" readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-muted">Mobile Number</label>
                        <input type="text" name="mobile" value="<?php echo htmlspecialchars($userdata['mobile'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-muted">Email Address</label>
                        <input type="text" value="<?php echo htmlspecialchars($userdata['email'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control bg-light" readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-muted">Company Name</label>
                        <input type="text" value="<?php echo htmlspecialchars($userdata['company'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control bg-light" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-muted">PAN Number</label>
                        <input type="text" value="<?php echo htmlspecialchars($userdata['pan'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control bg-light" readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-muted">Aadhaar Number</label>
                        <input type="text" value="<?php echo htmlspecialchars($userdata['aadhaar'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control bg-light" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-muted">Location / City</label>
                        <input type="text" value="<?php echo htmlspecialchars($userdata['location'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control bg-light" readonly>
                    </div>

                    <div class="col-12 my-3">
                        <hr>
                        <h6 class="fw-bold text-dark"><i class="bi bi-gear-fill me-2"></i>Preferences & Alerts</h6>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold small text-muted">Is OTP Required?</label>
                        <select name="is_otp" class="form-select">
                            <option value="YES" <?php echo ($userdata['is_otp'] === 'YES') ? 'selected' : ''; ?>>YES</option>
                            <option value="NO" <?php echo ($userdata['is_otp'] === 'NO') ? 'selected' : ''; ?>>NO</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small text-muted">WhatsApp Alert?</label>
                        <select name="whatsapp_alert" class="form-select">
                            <option value="YES" <?php echo ($userdata['whatsapp_alert'] === 'YES') ? 'selected' : ''; ?>>YES</option>
                            <option value="NO" <?php echo ($userdata['whatsapp_alert'] === 'NO') ? 'selected' : ''; ?>>NO</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small text-muted">Email Alert?</label>
                        <select name="email_alert" class="form-select">
                            <option value="YES" <?php echo ($userdata['email_alert'] === 'YES') ? 'selected' : ''; ?>>YES</option>
                            <option value="NO" <?php echo ($userdata['email_alert'] === 'NO') ? 'selected' : ''; ?>>NO</option>
                        </select>
                    </div>

                    <div class="col-12 mt-4 text-end">
                        <button type="submit" name="update" class="btn btn-primary px-5 py-2 fw-semibold">
                            <i class="bi bi-save me-1"></i>Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>
