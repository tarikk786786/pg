<?php
// Enable error reporting for debugging
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// Start output buffering to prevent premature output
ob_start();

include "header.php";
include "config.php"; // Assuming config.php contains the database connection

// Initialize message variable
$message = "";

// Fetch existing settings from the database
$query = "SELECT * FROM site_settings LIMIT 1";
$result = mysqli_query($conn, $query);
$settings = mysqli_fetch_assoc($result);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $brand_name = $_POST['brand_name'];
    $logo_url = $_POST['logo_url'];
    $site_link = $_POST['site_link'];
    $whatsapp_number = $_POST['whatsapp_number'];
    $copyright_text = $_POST['copyright_text'];

    if ($settings) {
        // Update existing record
        $update_query = "UPDATE site_settings SET brand_name='$brand_name', logo_url='$logo_url', site_link='$site_link', whatsapp_number='$whatsapp_number', copyright_text='$copyright_text' WHERE id=".$settings['id'];
        if (mysqli_query($conn, $update_query)) {
            $message = "Settings updated successfully.";
        } else {
            $message = "Error updating settings: " . mysqli_error($conn);
        }
    } else {
        // Insert new record
        $insert_query = "INSERT INTO site_settings (brand_name, logo_url, site_link, whatsapp_number, copyright_text) VALUES ('$brand_name', '$logo_url', '$site_link', '$whatsapp_number', '$copyright_text')";
        if (mysqli_query($conn, $insert_query)) {
            $message = "Settings saved successfully.";
        } else {
            $message = "Error saving settings: " . mysqli_error($conn);
        }
    }

    // Redirect after form submission
    header("Location: sitesetting.php?message=" . urlencode($message));
    exit();
}

if($userdata["role"] != 'Admin'){
   echo '<script>
 window.location.href = "dashboard";
</script>';

    exit;
}


// End output buffering and flush output
ob_end_flush();
?>
<!-- START PAGE CONTENT-->
<div class="pi-hero-card pi-hero-card-merchant mb-4 p-4 text-white rounded-3">
    <div class="row g-4 align-items-center">
        <div class="col-md-8">
            <span class="pi-hero-eyebrow"><i class="bi bi-globe me-1"></i>Admin Action</span>
            <h2>Website Management</h2>
            <p>Configure your brand name, logos, and other global site settings.</p>
        </div>
    </div>
</div>

<div class="pi-card p-4">
    <!-- Display success/error message with SweetAlert2 -->
    <?php if (isset($_GET['message'])): ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: '<?php echo htmlspecialchars($_GET['message']); ?>',
                    confirmButtonText: 'Ok',
                    customClass: {
                        confirmButton: 'btn btn-primary px-4'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    window.location.href = "sitesetting.php";
                });
            });
        </script>
    <?php endif; ?>

    <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-sliders text-primary me-2"></i>Configure Brand & Web Settings</h5>
    
    <!-- Site Settings Form -->
    <form method="post" action="sitesetting.php">
        <div class="row g-4">
            <div class="col-md-6">
                <label class="form-label text-dark fw-bold">Brand Name</label>
                <input class="form-control" type="text" name="brand_name" placeholder="e.g. Dezo" value="<?php echo htmlspecialchars($settings['brand_name'] ?? ''); ?>" required>
            </div>
            
            <div class="col-md-6">
                <label class="form-label text-dark fw-bold">WhatsApp Number</label>
                <input class="form-control" type="text" name="whatsapp_number" placeholder="e.g. 9234456535" value="<?php echo htmlspecialchars($settings['whatsapp_number'] ?? ''); ?>" required>
            </div>

            <div class="col-md-12">
                <label class="form-label text-dark fw-bold">Logo URL</label>
                <input class="form-control" type="url" name="logo_url" placeholder="https://example.com/logo.png" value="<?php echo htmlspecialchars($settings['logo_url'] ?? ''); ?>" required>
            </div>

            <div class="col-md-12">
                <label class="form-label text-dark fw-bold">Site Link</label>
                <input class="form-control" type="url" name="site_link" placeholder="https://example.com" value="<?php echo htmlspecialchars($settings['site_link'] ?? ''); ?>" required>
            </div>

            <div class="col-md-12">
                <label class="form-label text-dark fw-bold">Copyright Text</label>
                <input class="form-control" type="text" name="copyright_text" placeholder="e.g. Dezo All Rights Reserved" value="<?php echo htmlspecialchars($settings['copyright_text'] ?? ''); ?>" required>
            </div>
        </div>

        <div class="mt-4">
            <button class="btn btn-primary fw-bold px-4 py-2" type="submit">
                <i class="bi bi-check-lg me-1"></i> Save Settings
            </button>
        </div>
    </form>
</div>

 <!-- Include necessary scripts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="./assets/vendors/jquery/dist/jquery.min.js" type="text/javascript"></script>
<script src="assets/js/plugin/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script>
<script src="assets/js/core/popper.min.js"></script>
<script src="assets/js/core/bootstrap.min.js"></script>
<script src="assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>
<script src="assets/js/plugin/bootstrap-toggle/bootstrap-toggle.min.js"></script>
<script src="assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
<script src="assets/js/ready.min.js"></script>
<script src="assets/js/app.min.js" type="text/javascript"></script>
 <!-- PAGE LEVEL SCRIPTS-->
<script src="./assets/js/scripts/dashboard_1_demo.js" type="text/javascript"></script>
</body>
</html>