<?php 
include "header.php"; 

if ($userdata["role"] != 'Admin') {
    echo '<script>window.location.href = "dashboard";</script>';
    exit;
}

if (isset($_POST['reply_ticket'])) {
    $ticket_id = (int)$_POST['ticket_id'];
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    
    // Check if ticket is open
    $chk = mysqli_query($conn, "SELECT status FROM support_tickets WHERE id = $ticket_id");
    if ($t = mysqli_fetch_assoc($chk)) {
        if ($t['status'] == 'Open') {
            mysqli_query($conn, "INSERT INTO ticket_replies (ticket_id, sender, message) VALUES ($ticket_id, 'Admin', '$message')");
            $success = "Reply sent.";
        } else {
            $error = "Cannot reply to a closed ticket.";
        }
    }
}

if (isset($_POST['close_ticket'])) {
    $ticket_id = (int)$_POST['ticket_id'];
    mysqli_query($conn, "UPDATE support_tickets SET status = 'Closed' WHERE id = $ticket_id");
    $success = "Ticket #$ticket_id closed.";
}

// Get all open tickets or all
$filter = $_GET['status'] ?? 'Open';
$query = "SELECT t.*, u.name, u.mobile FROM support_tickets t JOIN users u ON t.user_id = u.id ";
if ($filter == 'Open') {
    $query .= "WHERE t.status = 'Open' ";
}
$query .= "ORDER BY t.id DESC";
$tickets = mysqli_query($conn, $query);
?>

<div class="pi-hero-card pi-hero-card-merchant mb-4 p-4 text-white rounded-3">
    <div class="row g-4 align-items-center">
        <div class="col-md-8">
            <span class="pi-hero-eyebrow"><i class="bi bi-headset me-1"></i>Admin Action</span>
            <h2>Support Tickets Management</h2>
            <p>Respond to user queries and issues.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="?status=Open" class="btn btn-warning fw-bold px-3">Open Tickets</a>
            <a href="?status=All" class="btn btn-light text-dark fw-bold px-3">All Tickets</a>
        </div>
    </div>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger py-2"><?php echo $error; ?></div>
<?php endif; ?>
<?php if (isset($success)): ?>
    <div class="alert alert-success py-2"><?php echo $success; ?></div>
<?php endif; ?>

<div class="row g-4">
    <?php if (mysqli_num_rows($tickets) > 0): ?>
        <?php while($t = mysqli_fetch_assoc($tickets)): 
            $tid = $t['id'];
            $replies = mysqli_query($conn, "SELECT * FROM ticket_replies WHERE ticket_id = $tid ORDER BY id ASC");
        ?>
        <div class="col-12">
            <div class="pi-card border shadow-sm">
                <div class="p-4 border-bottom d-flex justify-content-between align-items-center bg-light">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">#<?php echo $tid; ?> - <?php echo htmlspecialchars($t['subject']); ?></h5>
                        <div class="small text-muted">By: <strong><?php echo htmlspecialchars($t['name']); ?></strong> (<?php echo htmlspecialchars($t['mobile']); ?>) | Created on <?php echo date('d M Y, h:i A', strtotime($t['created_at'])); ?></div>
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <?php if ($t['status'] == 'Open'): ?>
                            <span class="badge bg-warning text-dark px-3 py-2">Open</span>
                            <form method="POST" class="m-0">
                                <input type="hidden" name="ticket_id" value="<?php echo $tid; ?>">
                                <button type="submit" name="close_ticket" class="btn btn-sm btn-outline-danger">Close Ticket</button>
                            </form>
                        <?php else: ?>
                            <span class="badge bg-secondary px-3 py-2">Closed</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="p-4" style="max-height: 400px; overflow-y: auto; background:#f8fafc;">
                    <?php while($r = mysqli_fetch_assoc($replies)): ?>
                        <div class="mb-3 <?php echo $r['sender'] == 'Admin' ? 'text-end' : 'text-start'; ?>">
                            <div class="d-inline-block p-3 rounded-3 shadow-sm <?php echo $r['sender'] == 'Admin' ? 'bg-primary text-white' : 'bg-white text-dark border'; ?>" style="max-width: 80%; text-align: left;">
                                <div class="small fw-bold mb-1 <?php echo $r['sender'] == 'Admin' ? 'text-light' : 'text-success'; ?>">
                                    <?php echo $r['sender'] == 'Admin' ? 'You' : htmlspecialchars($t['name']); ?>
                                </div>
                                <div><?php echo nl2br(htmlspecialchars($r['message'])); ?></div>
                                <div class="small mt-2 <?php echo $r['sender'] == 'Admin' ? 'text-white-50' : 'text-muted'; ?>" style="font-size: 11px;">
                                    <?php echo date('h:i A', strtotime($r['created_at'])); ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
                <?php if ($t['status'] == 'Open'): ?>
                <div class="p-3 border-top bg-white">
                    <form method="POST" class="d-flex gap-2">
                        <input type="hidden" name="ticket_id" value="<?php echo $tid; ?>">
                        <input type="text" name="message" class="form-control" placeholder="Type your reply to user..." required>
                        <button type="submit" name="reply_ticket" class="btn btn-primary px-4">Reply</button>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="col-12 text-center py-5">
            <h5 class="text-muted">No support tickets found.</h5>
        </div>
    <?php endif; ?>
</div>

<?php include "footer.php"; ?>
