<?php 
include "header.php"; 

if ($userdata["role"] != 'Admin') {
    echo '<script>window.location.href = "dashboard";</script>';
    exit;
}

if (isset($_POST['delete'])) {
    $mb = mysqli_real_escape_string($conn, $_POST['mobileno']);
    $del = "DELETE FROM `users` WHERE mobile='$mb'";
    $rpt = mysqli_query($conn, $del);

    if ($rpt) {
        echo '
        <script>
            Swal.fire({
                icon: "success",
                title: "User Deleted Successfully!",
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
                title: "Failed to Delete User!",
                text: "' . mysqli_error($conn) . '",
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
            <span class="pi-hero-eyebrow"><i class="bi bi-people-fill me-1"></i>Admin Action</span>
            <h2>User List</h2>
            <p>Manage all registered users on your platform. You can view details, edit profiles, or remove accounts.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="add_merchant" class="btn btn-light text-dark fw-bold px-4 py-2" style="border-radius: 12px;">
                <i class="bi bi-person-plus-fill me-2"></i>Add User
            </a>
        </div>
    </div>
</div>

<div class="pi-card">
    <div class="d-flex justify-content-between align-items-center p-4 border-bottom">
        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-list-check text-primary me-2"></i>Registered Users</h5>
        <div class="input-group" style="max-width: 250px;">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
            <input type="text" id="searchInput" class="form-control border-start-0 ps-0" placeholder="Search users...">
        </div>
    </div>
    <div class="table-responsive p-0">
        <table class="table table-hover mb-0 align-middle" id="merchantTable">
            <thead class="bg-light">
                <tr>
                    <th class="text-secondary small fw-semibold text-uppercase py-3 ps-4">Merchant Name</th>
                    <th class="text-secondary small fw-semibold text-uppercase py-3">Business / Mobile</th>
                    <th class="text-secondary small fw-semibold text-uppercase py-3">Location</th>
                    <th class="text-secondary small fw-semibold text-uppercase py-3">KYC (PAN/Aadhaar)</th>
                    <th class="text-secondary small fw-semibold text-uppercase py-3">Plan Expiry</th>
                    <th class="text-secondary small fw-semibold text-uppercase py-3 text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT `id`, `name`, `mobile`, `role`, `company`, `pan`, `aadhaar`, `location`, `expiry` FROM `users` WHERE role='User' ORDER BY id DESC";
                $query_run = mysqli_query($conn, $query);

                if ($query_run && mysqli_num_rows($query_run) > 0) {
                    while ($row = mysqli_fetch_assoc($query_run)) {
                        $is_expired = (strtotime($row['expiry']) < strtotime(date('Y-m-d')));
                        $expiry_class = $is_expired ? 'text-danger fw-bold' : 'text-success fw-bold';
                ?>
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width:40px; height:40px; border-radius:10px;">
                                <?php echo strtoupper(substr(htmlspecialchars($row['name']), 0, 1)); ?>
                            </div>
                            <div>
                                <div class="fw-bold text-dark" style="font-size: 14px;"><?php echo htmlspecialchars($row['name']); ?></div>
                                <div class="small text-muted">ID: #<?php echo $row['id']; ?></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="fw-semibold text-dark" style="font-size: 13px;"><i class="bi bi-shop me-1 text-muted"></i><?php echo htmlspecialchars($row['company'] ?: 'N/A'); ?></div>
                        <div class="small text-muted"><i class="bi bi-telephone me-1"></i><?php echo htmlspecialchars($row['mobile']); ?></div>
                    </td>
                    <td>
                        <div class="small text-dark" style="max-width: 150px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            <i class="bi bi-geo-alt me-1 text-muted"></i><?php echo htmlspecialchars($row['location'] ?: 'N/A'); ?>
                        </div>
                    </td>
                    <td>
                        <div class="small text-dark fw-semibold" style="font-family: monospace;">P: <?php echo htmlspecialchars($row['pan'] ?: '—'); ?></div>
                        <div class="small text-muted" style="font-family: monospace;">A: <?php echo htmlspecialchars($row['aadhaar'] ?: '—'); ?></div>
                    </td>
                    <td>
                        <span class="small <?php echo $expiry_class; ?>">
                            <?php echo date('d M Y', strtotime($row['expiry'])); ?>
                        </span>
                    </td>
                    <td class="text-end pe-4">
                        <div class="d-flex justify-content-end gap-2">
                            <form action="edituser.php" method="post" class="m-0">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <input type="hidden" name="mobileno" value="<?php echo $row['mobile']; ?>">
                                <button class="btn btn-sm btn-outline-primary shadow-none" name="edituser" title="Edit User">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                            </form>
                            
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="m-0 delete-form">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <input type="hidden" name="mobileno" value="<?php echo $row['mobile']; ?>">
                                <button type="button" class="btn btn-sm btn-outline-danger shadow-none delete-btn" title="Delete User">
                                    <i class="bi bi-trash"></i>
                                </button>
                                <button type="submit" name="delete" class="d-none real-delete"></button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php
                    }
                } else {
                ?>
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="bi bi-people fs-1 d-block mb-3 text-light"></i>
                        No users found. <a href="add_merchant" class="text-primary text-decoration-underline">Add one now</a>.
                    </td>
                </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#merchantTable tbody tr');
        
        rows.forEach(row => {
            let text = row.innerText.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });

    // Delete confirmation
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            let form = this.closest('.delete-form');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this! This deletes the merchant completely.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.querySelector('.real-delete').click();
                }
            });
        });
    });
</script>

<?php include "footer.php"; ?>