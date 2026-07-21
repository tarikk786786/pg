<?php 
include "header.php"; 

if ($userdata["role"] != 'Admin') {
    echo '<script>window.location.href = "dashboard";</script>';
    exit;
}

if (isset($_POST['add_announcement'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    
    if (mysqli_query($conn, "INSERT INTO announcements (title, message) VALUES ('$title', '$message')")) {
        $success = "Announcement broadcasted successfully.";
    } else {
        $error = "Failed to add announcement.";
    }
}

if (isset($_POST['delete_announcement'])) {
    $id = (int)$_POST['id'];
    mysqli_query($conn, "DELETE FROM announcements WHERE id = $id");
    $success = "Announcement deleted.";
}

$announcements = mysqli_query($conn, "SELECT * FROM announcements ORDER BY id DESC");
?>

<div class="pi-hero-card pi-hero-card-merchant mb-4 p-4 text-white rounded-3">
    <div class="row g-4 align-items-center">
        <div class="col-md-8">
            <span class="pi-hero-eyebrow"><i class="bi bi-megaphone me-1"></i>Admin Action</span>
            <h2>Broadcast Announcements</h2>
            <p>Send notices, maintenance alerts, and updates to all merchants. They will see this on their dashboard.</p>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-5">
        <div class="pi-card p-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-plus-circle text-primary me-2"></i>New Broadcast</h5>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger py-2"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if (isset($success)): ?>
                <div class="alert alert-success py-2"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label text-dark fw-bold">Title / Subject</label>
                    <input type="text" name="title" class="form-control" required placeholder="e.g. Scheduled Maintenance">
                </div>
                <div class="mb-3">
                    <label class="form-label text-dark fw-bold">Message Content</label>
                    <textarea name="message" class="form-control" rows="5" required placeholder="Write your notice here..."></textarea>
                </div>
                <button type="submit" name="add_announcement" class="btn btn-primary w-100 fw-bold">
                    <i class="bi bi-send me-1"></i> Send Broadcast
                </button>
            </form>
        </div>
    </div>
    
    <div class="col-md-7">
        <div class="pi-card p-4">
            <h5 class="fw-bold mb-4"><i class="bi bi-list-stars text-success me-2"></i>Previous Broadcasts</h5>
            <?php if (mysqli_num_rows($announcements) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($announcements)): ?>
                    <div class="border rounded-3 p-3 mb-3 bg-light position-relative">
                        <div class="d-flex justify-content-between">
                            <h6 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($row['title']); ?></h6>
                            <form method="POST" class="m-0" onsubmit="return confirm('Delete this announcement?');">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="delete_announcement" class="btn btn-sm btn-link text-danger p-0 border-0"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                        <p class="small text-muted mb-2"><?php echo nl2br(htmlspecialchars($row['message'])); ?></p>
                        <div class="small text-muted" style="font-size: 11px;"><i class="bi bi-clock me-1"></i><?php echo date('d M Y, h:i A', strtotime($row['created_at'])); ?></div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center py-4 text-muted">No announcements yet.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>
