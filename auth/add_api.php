<?php
include "header.php"; // Include your header file
include "config.php"; // Ensure this is the correct path to your DB connection file

// Initialize variables
$whatsapp_api_url = '';
$sender_id = '';
$api_key = '';
$sender_email = '';

if($userdata["role"] != 'Admin'){
   echo '<script>
 window.location.href = "dashboard";
</script>';

    exit;
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
// Capture the POST data
$whatsapp_api_url = $_POST['whatsapp_api_url'];
$sender_id = $_POST['sender_id'];
$api_key = $_POST['api_key'];
$sender_email = $_POST['sender_email'];

// Check if settings already exist
$query = "SELECT * FROM api_settings LIMIT 1"; // Get any existing setting
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
// If settings exist, update them
$settings = $result->fetch_assoc();
$id = $settings['id'];

// Prepare the update statement
$update_query = "UPDATE api_settings SET whatsapp_api_url = ?, sender_id = ?, api_key = ?, sender_email = ? WHERE id = ?";
$update_stmt = $conn->prepare($update_query);
$update_stmt->bind_param("ssssi", $whatsapp_api_url, $sender_id, $api_key, $sender_email, $id);

if ($update_stmt->execute()) {
// Success message
echo "<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
    title: 'Success!',
    text: 'API settings updated successfully!',
    icon: 'success',
    confirmButtonText: 'OK'
    });
});
  </script>";
} else {
// Error during update
echo "<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
    title: 'Error!',
    text: 'Error updating settings: " . addslashes($update_stmt->error) . "',
    icon: 'error',
    confirmButtonText: 'OK'
    });
});
  </script>";
}

$update_stmt->close(); // Close the update statement
} else {
// If no settings exist, insert new ones
$insert_query = "INSERT INTO api_settings (whatsapp_api_url, sender_id, api_key, sender_email) VALUES (?, ?, ?, ?)";
$insert_stmt = $conn->prepare($insert_query);
$insert_stmt->bind_param("ssss", $whatsapp_api_url, $sender_id, $api_key, $sender_email);

if ($insert_stmt->execute()) {
// Success message for insert
echo "<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
    title: 'Success!',
    text: 'API settings added successfully!',
    icon: 'success',
    confirmButtonText: 'OK'
    });
});
  </script>";
} else {
// Error during insert
echo "<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
    title: 'Error!',
    text: 'Error adding settings: " . addslashes($insert_stmt->error) . "',
    icon: 'error',
    confirmButtonText: 'OK'
    });
});
  </script>";
}

$insert_stmt->close(); // Close the insert statement
}
}

// Fetch current API settings to display in the form if they exist
$query = "SELECT * FROM api_settings LIMIT 1"; // Get any existing setting
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
$settings = $result->fetch_assoc();
$whatsapp_api_url = $settings['whatsapp_api_url'];
$sender_id = $settings['sender_id'];
$api_key = $settings['api_key'];
$sender_email = $settings['sender_email'];
} else {
// No settings found, set default values
$whatsapp_api_url = '';
$sender_id = '';
$api_key = '';
$sender_email = '';
}

$stmt->close(); // Close the statement
?>

<!-- HTML Form for API Settings -->
<div class="pi-hero-card pi-hero-card-merchant mb-4 p-4 text-white rounded-3">
    <div class="row g-4 align-items-center">
        <div class="col-md-8">
            <span class="pi-hero-eyebrow"><i class="bi bi-whatsapp me-1"></i>Admin Action</span>
            <h2>SMTP & WhatsApp API</h2>
            <p>Configure your notification gateways and messaging API keys.</p>
        </div>
    </div>
</div>

<div class="pi-card p-4">
    <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-gear-wide-connected text-primary me-2"></i>API Gateway Settings</h5>
    
    <form method="POST" action="">
        <div class="row g-4">
            <div class="col-md-6">
                <label class="form-label text-dark fw-bold">WhatsApp API URL</label>
                <input type="text" name="whatsapp_api_url" value="<?php echo htmlspecialchars($whatsapp_api_url); ?>" class="form-control" placeholder="https://api.whatsapp.com/..." required>
            </div>
            <div class="col-md-6">
                <label class="form-label text-dark fw-bold">Sender ID</label>
                <input type="text" name="sender_id" value="<?php echo htmlspecialchars($sender_id); ?>" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label text-dark fw-bold">API Key</label>
                <input type="text" name="api_key" value="<?php echo htmlspecialchars($api_key); ?>" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label text-dark fw-bold">Sender Email (SMTP)</label>
                <input type="email" name="sender_email" value="<?php echo htmlspecialchars($sender_email); ?>" class="form-control" placeholder="no-reply@domain.com" required>
            </div>
            <div class="col-md-12 mt-4">
                <button type="submit" class="btn btn-primary fw-bold px-4 py-2">
                    <i class="bi bi-save me-1"></i> Save Settings
                </button>
            </div>
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
