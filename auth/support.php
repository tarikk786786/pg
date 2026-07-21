<?php 
include "header.php"; 

// Role restriction removed so admin can test user ticket flow

$user_id = $userdata['id'];

if (isset($_POST['create_ticket'])) {
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    
    if (empty($subject) || empty($message)) {
        $error = "Subject and message are required.";
    } else {
        mysqli_begin_transaction($conn);
        try {
            $insert_ticket = "INSERT INTO support_tickets (user_id, subject, status) VALUES ($user_id, '$subject', 'Open')";
            if (mysqli_query($conn, $insert_ticket)) {
                $ticket_id = mysqli_insert_id($conn);
                $insert_reply = "INSERT INTO ticket_replies (ticket_id, sender, message) VALUES ($ticket_id, 'User', '$message')";
                mysqli_query($conn, $insert_reply);
                mysqli_commit($conn);
                $success = "Ticket created successfully! We will get back to you soon.";
            } else {
                throw new Exception("Error creating ticket.");
            }
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $error = $e->getMessage();
        }
    }
}

if (isset($_POST['reply_ticket'])) {
    $ticket_id = (int)$_POST['ticket_id'];
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    
    // Check if ticket belongs to user and is open
    $chk = mysqli_query($conn, "SELECT status FROM support_tickets WHERE id = $ticket_id AND user_id = $user_id");
    if ($t = mysqli_fetch_assoc($chk)) {
        if ($t['status'] == 'Open') {
            mysqli_query($conn, "INSERT INTO ticket_replies (ticket_id, sender, message) VALUES ($ticket_id, 'User', '$message')");
            $success_reply = "Reply sent.";
        } else {
            $error_reply = "Cannot reply to a closed ticket.";
        }
    }
}

$tickets = mysqli_query($conn, "SELECT * FROM support_tickets WHERE user_id = $user_id ORDER BY id DESC");
?>

<div class="pi-hero-card pi-hero-card-merchant mb-4 p-4 text-white rounded-3">
    <div class="row g-4 align-items-center">
        <div class="col-md-8">
            <span class="pi-hero-eyebrow"><i class="bi bi-headset me-1"></i>Helpdesk</span>
            <h2>Support Tickets</h2>
            <p>Raise an issue or ask a question. Our team is here to help you.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <button class="btn btn-light text-dark fw-bold px-4 py-2" data-bs-toggle="modal" data-bs-target="#newTicketModal" style="border-radius: 12px;">
                <i class="bi bi-plus-circle-fill me-2"></i>Create New Ticket
            </button>
        </div>
    </div>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger py-2"><?php echo $error; ?></div>
<?php endif; ?>
<?php if (isset($success)): ?>
    <div class="alert alert-success py-2"><?php echo $success; ?></div>
<?php endif; ?>
<?php if (isset($error_reply)): ?>
    <div class="alert alert-danger py-2"><?php echo $error_reply; ?></div>
<?php endif; ?>
<?php if (isset($success_reply)): ?>
    <div class="alert alert-success py-2"><?php echo $success_reply; ?></div>
<?php endif; ?>

<div class="row g-4">
    <?php if (mysqli_num_rows($tickets) > 0): ?>
        <?php while($t = mysqli_fetch_assoc($tickets)): 
            $tid = $t['id'];
            $replies = mysqli_query($conn, "SELECT * FROM ticket_replies WHERE ticket_id = $tid ORDER BY id ASC");
        ?>
        <div class="col-12">
            <div class="pi-card border border-light shadow-sm">
                <div class="p-4 border-bottom d-flex justify-content-between align-items-center bg-light">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">#<?php echo $tid; ?> - <?php echo htmlspecialchars($t['subject']); ?></h5>
                        <div class="small text-muted">Created on <?php echo date('d M Y, h:i A', strtotime($t['created_at'])); ?></div>
                    </div>
                    <div>
                        <?php if ($t['status'] == 'Open'): ?>
                            <span class="badge bg-warning text-dark px-3 py-2">Open</span>
                        <?php else: ?>
                            <span class="badge bg-secondary px-3 py-2">Closed</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="p-4" style="max-height: 400px; overflow-y: auto; background:#f8fafc;">
                    <?php while($r = mysqli_fetch_assoc($replies)): ?>
                        <div class="mb-3 <?php echo $r['sender'] == 'User' ? 'text-end' : 'text-start'; ?>">
                            <div class="d-inline-block p-3 rounded-3 shadow-sm <?php echo $r['sender'] == 'User' ? 'bg-primary text-white' : 'bg-white text-dark border'; ?>" style="max-width: 80%; text-align: left;">
                                <div class="small fw-bold mb-1 <?php echo $r['sender'] == 'User' ? 'text-light' : 'text-primary'; ?>">
                                    <?php echo $r['sender'] == 'User' ? 'You' : 'Admin Support'; ?>
                                </div>
                                <div><?php echo nl2br(htmlspecialchars($r['message'])); ?></div>
                                <div class="small mt-2 <?php echo $r['sender'] == 'User' ? 'text-white-50' : 'text-muted'; ?>" style="font-size: 11px;">
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
                        <input type="text" name="message" class="form-control" placeholder="Type your reply..." required>
                        <button type="submit" name="reply_ticket" class="btn btn-primary px-4"><i class="bi bi-send-fill"></i></button>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="col-12 text-center py-5">
            <i class="bi bi-inboxes text-muted" style="font-size: 3rem;"></i>
            <h5 class="text-muted mt-3">No support tickets found.</h5>
        </div>
    <?php endif; ?>
</div>

<!-- Modal for New Ticket -->
<div class="modal fade" id="newTicketModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Create New Ticket</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST">
      <div class="modal-body">
            <div class="mb-3">
                <label class="form-label text-dark fw-bold">Subject</label>
                <input type="text" name="subject" class="form-control" required placeholder="Brief description of issue">
            </div>
            <div class="mb-3">
                <label class="form-label text-dark fw-bold">Message</label>
                <textarea name="message" class="form-control" rows="4" required placeholder="Describe your problem in detail..."></textarea>
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" name="create_ticket" class="btn btn-primary">Submit Ticket</button>
      </div>
      </form>
    </div>
  </div>
</div>

<?php include "footer.php"; ?>
