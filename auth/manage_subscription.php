<?php include "header.php"; ?>

<?php 
if($userdata["role"] != 'Admin'){
   echo '<script>window.location.href = "dashboard";</script>';
   exit;
}

// Handle DELETE
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["deletesub"])) {
    $del_id = intval($_POST['del_id']);
    $del_stmt = $conn->prepare("DELETE FROM subscription_plan WHERE id = ?");
    $del_stmt->bind_param("i", $del_id);
    if ($del_stmt->execute()) {
        echo "<script>Swal.fire({icon:'success',title:'Plan Deleted!',text:'The subscription plan has been permanently deleted.',confirmButtonColor:'#093c31'}).then(()=>{window.location.href='manage_subscription';});</script>";
    } else {
        echo "<script>Swal.fire({icon:'error',title:'Error!',text:'Failed to delete plan.',confirmButtonColor:'#dc3545'});</script>";
    }
    $del_stmt->close();
}

// Handle UPDATE
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["updatesub"])) {
    $srno             = intval($_POST['srno']);
    $planname         = trim($_POST['plan_name']);
    $amount           = trim($_POST['amount']);
    $expiry           = trim($_POST['expiry']);
    $qr_requests      = trim($_POST['qr_requests'] ?? 'Unlimited');
    $txn_limit        = trim($_POST['txn_limit'] ?? 'Unlimited');
    $status_val       = $_POST['plan_status'] === 'active' ? 'active' : 'inactive';

    // Feature toggles (checkbox = 1, unchecked = 0)
    $zero_txn_fee       = isset($_POST['zero_txn_fee'])       ? 1 : 0;
    $realtime_txn       = isset($_POST['realtime_txn'])       ? 1 : 0;
    $no_amount_limit    = isset($_POST['no_amount_limit'])    ? 1 : 0;
    $hdfc_vyapar        = isset($_POST['hdfc_vyapar'])        ? 1 : 0;
    $dynamic_qr         = isset($_POST['dynamic_qr'])         ? 1 : 0;
    $direct_upi         = isset($_POST['direct_upi'])         ? 1 : 0;
    $accept_all_upi     = isset($_POST['accept_all_upi'])     ? 1 : 0;
    $support_247        = isset($_POST['support_247'])        ? 1 : 0;
    $instant_settlement = isset($_POST['instant_settlement']) ? 1 : 0;
    $api_access         = isset($_POST['api_access'])         ? 1 : 0;
    $custom_checkout    = isset($_POST['custom_checkout'])    ? 1 : 0;
    $whatsapp_support   = isset($_POST['whatsapp_support'])   ? 1 : 0;
    $branding           = isset($_POST['branding'])           ? 1 : 0;

    $sql = "UPDATE subscription_plan SET 
        plan_name=?, amount=?, expiry=?, status=?, qr_requests=?, txn_limit=?,
        zero_txn_fee=?, realtime_txn=?, no_amount_limit=?, hdfc_vyapar=?,
        dynamic_qr=?, direct_upi=?, accept_all_upi=?, support_247=?,
        instant_settlement=?, api_access=?, custom_checkout=?, whatsapp_support=?, branding=?
        WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssiiiiiiiiiiiiii",
        $planname, $amount, $expiry, $status_val, $qr_requests, $txn_limit,
        $zero_txn_fee, $realtime_txn, $no_amount_limit, $hdfc_vyapar,
        $dynamic_qr, $direct_upi, $accept_all_upi, $support_247,
        $instant_settlement, $api_access, $custom_checkout, $whatsapp_support, $branding,
        $srno
    );

    if ($stmt->execute()) {
        echo "<script>Swal.fire({icon:'success',title:'Plan Updated!',text:'Subscription plan has been updated successfully.',confirmButtonColor:'#093c31'}).then(()=>{window.location.href='manage_subscription';});</script>";
    } else {
        echo "<script>Swal.fire({icon:'error',title:'Error!',text:'Failed to update plan. Please try again.',confirmButtonColor:'#dc3545'});</script>";
    }
    $stmt->close();
}

$query = $conn->query("SELECT * FROM `subscription_plan` ORDER BY CAST(amount AS UNSIGNED) ASC");
?>

<style>
/* ===== Page Hero ===== */
.msub-hero {
    background: linear-gradient(135deg, #093c31 0%, #0d5540 60%, #145c44 100%);
    border-radius: 18px; padding: 2rem 2.5rem;
    margin-bottom: 2rem; position: relative; overflow: hidden;
    border: 1px solid rgba(214,235,91,0.12);
    box-shadow: 0 12px 40px rgba(9,60,49,0.3);
}
.msub-hero::before {
    content: ''; position: absolute; top: -60px; right: -60px;
    width: 220px; height: 220px;
    background: radial-gradient(circle, rgba(214,235,91,0.08) 0%, transparent 70%);
    border-radius: 50%;
}
.msub-hero-eyebrow {
    font-size: 10.5px; font-weight: 700; letter-spacing: 2px;
    text-transform: uppercase; color: #d6eb5b;
    background: rgba(214,235,91,0.12); border: 1px solid rgba(214,235,91,0.25);
    border-radius: 30px; padding: 4px 14px; display: inline-block; margin-bottom: 10px;
}
.msub-hero h2 { font-size: 1.8rem; font-weight: 800; color: #fff; margin: 0 0 4px; }
.msub-hero p { color: rgba(255,255,255,0.55); font-size: 13.5px; margin: 0; }

/* ===== Plan Cards ===== */
.msub-plan-card {
    background: #fff; border: 2px solid #e2e8f0; border-radius: 18px;
    overflow: hidden; height: 100%; transition: all 0.3s ease;
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
}
.msub-plan-card:hover {
    border-color: #093c31; box-shadow: 0 10px 40px rgba(9,60,49,0.13);
    transform: translateY(-3px);
}
.msub-card-top {
    background: linear-gradient(135deg, #093c31 0%, #0d5540 100%);
    padding: 1.4rem 1.5rem 1.2rem; position: relative;
}
.msub-card-name {
    font-size: 11px; font-weight: 800; letter-spacing: 2px;
    text-transform: uppercase; color: rgba(255,255,255,0.5); margin-bottom: 4px;
}
.msub-card-price { font-size: 2rem; font-weight: 800; color: #fff; line-height: 1; }
.msub-card-duration { font-size: 12.5px; color: rgba(255,255,255,0.45); margin-top: 3px; }
.msub-card-status {
    position: absolute; top: 14px; right: 14px;
    font-size: 11px; font-weight: 700; padding: 3px 12px; border-radius: 50px;
}
.msub-card-status.active { background: rgba(214,235,91,0.2); color: #d6eb5b; border: 1px solid rgba(214,235,91,0.3); }
.msub-card-status.inactive { background: rgba(220,53,69,0.2); color: #f87171; border: 1px solid rgba(220,53,69,0.2); }
.msub-card-body { padding: 1.2rem 1.5rem 1.4rem; }
.msub-feature-list { list-style: none; padding: 0; margin: 0 0 1.2rem; }
.msub-feature-list li {
    display: flex; align-items: center; gap: 8px;
    font-size: 13px; color: #4b5563; padding: 4px 0;
}
.msub-feature-list .feat-ok { color: #16a34a; }
.msub-feature-list .feat-no { color: #dc2626; }
.msub-feature-list .feat-limit {
    font-size: 11px; margin-left: auto;
    background: #f1f5f9; color: #64748b;
    padding: 2px 8px; border-radius: 6px; font-weight: 600;
}
.msub-edit-btn {
    width: 100%; padding: 10px; border-radius: 10px;
    font-weight: 700; font-size: 13.5px;
    background: #093c31; color: #fff; border: 2px solid #093c31;
    transition: all 0.25s; cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 8px;
}
.msub-edit-btn:hover { background: #072e25; transform: translateY(-1px); }
.msub-delete-btn {
    width: 100%; padding: 10px; border-radius: 10px;
    font-weight: 700; font-size: 13.5px;
    background: transparent; color: #dc2626;
    border: 2px solid #fecaca;
    transition: all 0.25s; cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 8px;
}
.msub-delete-btn:hover {
    background: #fee2e2;
    border-color: #dc2626;
    transform: translateY(-1px);
}

/* ===== Edit Form ===== */
.msub-edit-card {
    background: #fff; border: 1px solid #e2e8f0; border-radius: 18px;
    overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.06);
}
.msub-edit-header {
    background: linear-gradient(135deg, #093c31, #0d5540);
    padding: 1.2rem 1.8rem; display: flex; align-items: center; gap: 12px;
}
.msub-edit-header h5 { color: #fff; font-weight: 700; margin: 0; font-size: 16px; }
.msub-edit-body { padding: 2rem; }
.msub-section-title {
    font-size: 11px; font-weight: 800; text-transform: uppercase;
    letter-spacing: 1.5px; color: #093c31;
    border-bottom: 2px solid #d6eb5b; padding-bottom: 6px;
    margin-bottom: 1.2rem; margin-top: 1.6rem; display: flex; align-items: center; gap: 8px;
}
.msub-form-label {
    font-size: 12px; font-weight: 700; text-transform: uppercase;
    letter-spacing: 0.8px; color: #64748b; margin-bottom: 6px; display: block;
}
.msub-form-input {
    width: 100%; padding: 10px 14px; border-radius: 10px;
    border: 1.5px solid #e2e8f0; font-size: 14px;
    background: #f8fafc; color: #1e293b; transition: all 0.2s; outline: none;
}
.msub-form-input:focus {
    border-color: #093c31; background: #fff;
    box-shadow: 0 0 0 3px rgba(9,60,49,0.08);
}
.msub-form-select {
    width: 100%; padding: 10px 14px; border-radius: 10px;
    border: 1.5px solid #e2e8f0; font-size: 14px;
    background: #f8fafc; color: #1e293b; transition: all 0.2s; outline: none;
    cursor: pointer;
}
.msub-form-select:focus { border-color: #093c31; background: #fff; box-shadow: 0 0 0 3px rgba(9,60,49,0.08); }

/* Feature Toggle Grid */
.feature-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
.feature-toggle {
    display: flex; align-items: center; gap: 12px;
    background: #f8fafc; border: 1.5px solid #e2e8f0;
    border-radius: 12px; padding: 12px 14px; cursor: pointer;
    transition: all 0.2s; user-select: none;
}
.feature-toggle:hover { border-color: #093c31; background: #f0fdf4; }
.feature-toggle.checked { border-color: #093c31; background: #f0fdf9; }
.feature-toggle input[type="checkbox"] { display: none; }
.toggle-indicator {
    width: 38px; height: 22px; border-radius: 50px;
    background: #e2e8f0; position: relative; flex-shrink: 0; transition: all 0.25s;
}
.toggle-indicator::after {
    content: ''; position: absolute; top: 3px; left: 3px;
    width: 16px; height: 16px; border-radius: 50%;
    background: #fff; transition: all 0.25s;
    box-shadow: 0 1px 4px rgba(0,0,0,0.2);
}
.feature-toggle.checked .toggle-indicator { background: #093c31; }
.feature-toggle.checked .toggle-indicator::after { left: 19px; }
.toggle-label { font-size: 13px; font-weight: 600; color: #374151; }
.toggle-icon { font-size: 15px; flex-shrink: 0; }

/* Buttons */
.msub-save-btn {
    background: linear-gradient(135deg, #093c31, #0d5540);
    color: #fff; border: none; padding: 12px 28px; border-radius: 10px;
    font-weight: 700; font-size: 14px; cursor: pointer; transition: all 0.25s;
    display: inline-flex; align-items: center; gap: 8px;
}
.msub-save-btn:hover { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(9,60,49,0.3); }
.msub-cancel-btn {
    background: #f1f5f9; color: #64748b; border: none;
    padding: 12px 24px; border-radius: 10px; font-weight: 600; font-size: 14px;
    cursor: pointer; transition: all 0.2s; text-decoration: none;
    display: inline-flex; align-items: center; gap: 8px;
}
.msub-cancel-btn:hover { background: #e2e8f0; color: #475569; }
</style>

<!-- Page Hero -->
<div class="msub-hero mb-4">
    <span class="msub-hero-eyebrow"><i class="bi bi-gear-fill me-1"></i>Admin Control</span>
    <h2>Manage Subscription Plans</h2>
    <p>Configure plan pricing, duration, and individual feature access for each tier.</p>
</div>

<?php if (isset($_GET["srno"]) && $_GET["srno"] != ''): 
    $srno_safe = intval($_GET["srno"]);
    $fd = $conn->query("SELECT * FROM `subscription_plan` WHERE id = $srno_safe")->fetch_assoc();
    if (!$fd): ?>
        <div class="alert alert-danger border-0 rounded-3">Plan not found.</div>
    <?php else: ?>

<!-- ===== Edit Form ===== -->
<div class="msub-edit-card">
    <div class="msub-edit-header">
        <i class="bi bi-pencil-square" style="color:#d6eb5b; font-size:18px;"></i>
        <h5>Edit Plan &nbsp;—&nbsp; <?php echo htmlspecialchars($fd['plan_name']); ?></h5>
    </div>
    <div class="msub-edit-body">
        <form method="POST" action="" id="editPlanForm">
            <input type="hidden" name="srno" value="<?php echo $fd['id']; ?>">

            <!-- Basic Info -->
            <div class="msub-section-title"><i class="bi bi-info-circle-fill"></i>Basic Information</div>
            <div class="row g-3 mb-2">
                <div class="col-md-6">
                    <label class="msub-form-label">Plan Name</label>
                    <input type="text" name="plan_name" value="<?php echo htmlspecialchars($fd['plan_name']); ?>" class="msub-form-input" required placeholder="e.g. Enterprise">
                </div>
                <div class="col-md-3">
                    <label class="msub-form-label">Amount (₹)</label>
                    <input type="text" name="amount" value="<?php echo htmlspecialchars($fd['amount']); ?>" class="msub-form-input" required placeholder="e.g. 1999">
                </div>
                <div class="col-md-3">
                    <label class="msub-form-label">Duration / Expiry</label>
                    <input type="text" name="expiry" value="<?php echo htmlspecialchars($fd['expiry']); ?>" class="msub-form-input" required placeholder="e.g. 1 Month">
                </div>
                <div class="col-md-3">
                    <label class="msub-form-label">QR Requests Limit</label>
                    <input type="text" name="qr_requests" value="<?php echo htmlspecialchars($fd['qr_requests'] ?? 'Unlimited'); ?>" class="msub-form-input" placeholder="e.g. 2,000 or Unlimited">
                </div>
                <div class="col-md-3">
                    <label class="msub-form-label">Transaction Limit</label>
                    <input type="text" name="txn_limit" value="<?php echo htmlspecialchars($fd['txn_limit'] ?? 'Unlimited'); ?>" class="msub-form-input" placeholder="e.g. 3,000 or Unlimited">
                </div>
                <div class="col-md-3">
                    <label class="msub-form-label">Plan Status</label>
                    <select name="plan_status" class="msub-form-select">
                        <option value="active" <?php echo ($fd['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo ($fd['status'] !== 'active') ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
            </div>

            <!-- Feature Toggles -->
            <div class="msub-section-title"><i class="bi bi-toggles"></i>Feature Access Control</div>
            <?php
            $toggles = [
                ['zero_txn_fee',       'bi-percent',             '0 Transaction Fee'],
                ['realtime_txn',       'bi-lightning-fill',      'Realtime Transaction'],
                ['no_amount_limit',    'bi-infinity',            'No Amount Limit'],
                ['hdfc_vyapar',        'bi-bank',                'HDFC Vyapar'],
                ['dynamic_qr',         'bi-qr-code',             'Dynamic QR Code'],
                ['direct_upi',         'bi-phone',               'Direct UPI Intent'],
                ['accept_all_upi',     'bi-wallet2',             'Accept All UPI Apps'],
                ['support_247',        'bi-headset',             '24×7 Support'],
                ['instant_settlement', 'bi-arrow-down-circle',   'Instant Settlement'],
                ['api_access',         'bi-code-slash',          'API Access'],
                ['custom_checkout',    'bi-brush',               'Customised Checkout'],
                ['whatsapp_support',   'bi-whatsapp',            'WhatsApp Support'],
                ['branding',           'bi-star-fill',           'Branding & Identity'],
            ];
            ?>
            <div class="feature-grid">
                <?php foreach ($toggles as [$key, $icon, $label]):
                    $is_checked = !empty($fd[$key]);
                ?>
                <label class="feature-toggle <?php echo $is_checked ? 'checked' : ''; ?>" id="lbl_<?php echo $key; ?>">
                    <input type="checkbox" name="<?php echo $key; ?>" id="chk_<?php echo $key; ?>" <?php echo $is_checked ? 'checked' : ''; ?> onchange="toggleFeature('<?php echo $key; ?>', this)">
                    <div class="toggle-indicator"></div>
                    <i class="bi <?php echo $icon; ?> toggle-icon" style="color: #093c31;"></i>
                    <span class="toggle-label"><?php echo $label; ?></span>
                </label>
                <?php endforeach; ?>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex align-items-center gap-3 mt-4 pt-3 border-top">
                <button type="submit" name="updatesub" class="msub-save-btn">
                    <i class="bi bi-check-circle-fill"></i> Save Changes
                </button>
                <a href="manage_subscription" class="msub-cancel-btn">
                    <i class="bi bi-arrow-left"></i> Back to Plans
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function toggleFeature(key, checkbox) {
    const label = document.getElementById('lbl_' + key);
    if (checkbox.checked) {
        label.classList.add('checked');
    } else {
        label.classList.remove('checked');
    }
}
</script>

<?php endif; ?>
<?php else: 
    // Show all plans grid
?>

<div class="row g-4">
    <?php while($row = $query->fetch_assoc()): 
        $is_active = strtolower($row['status'] ?? 'active') === 'active';

        $plan_features = [
            ['zero_txn_fee',        'bi-percent',             '0 Transaction Fee'],
            ['realtime_txn',        'bi-lightning-fill',      'Realtime Transaction'],
            ['no_amount_limit',     'bi-infinity',            'No Amount Limit'],
            ['hdfc_vyapar',         'bi-bank',                'HDFC Vyapar'],
            ['dynamic_qr',          'bi-qr-code',             'Dynamic QR Code'],
            ['direct_upi',          'bi-phone',               'Direct UPI Intent'],
            ['accept_all_upi',      'bi-wallet2',             'Accept All UPI Apps'],
            ['support_247',         'bi-headset',             '24×7 Support'],
            ['instant_settlement',  'bi-arrow-down-circle',   'Instant Settlement'],
            ['api_access',          'bi-code-slash',          'API Access'],
            ['custom_checkout',     'bi-brush',               'Custom Checkout'],
            ['whatsapp_support',    'bi-whatsapp',            'WhatsApp Support'],
            ['branding',            'bi-star-fill',           'Branding & Identity'],
        ];
    ?>
    <div class="col-md-6 col-xl-3">
        <div class="msub-plan-card">
            <div class="msub-card-top">
                <div class="msub-card-name"><?php echo htmlspecialchars($row['plan_name']); ?></div>
                <div class="msub-card-price">₹<?php echo number_format((int)$row['amount']); ?></div>
                <div class="msub-card-duration"><?php echo htmlspecialchars($row['expiry']); ?> &nbsp;·&nbsp; One-time</div>
                <span class="msub-card-status <?php echo $is_active ? 'active' : 'inactive'; ?>">
                    <i class="bi bi-<?php echo $is_active ? 'check-circle-fill' : 'x-circle-fill'; ?> me-1"></i>
                    <?php echo $is_active ? 'Active' : 'Inactive'; ?>
                </span>
            </div>
            <div class="msub-card-body">
                <!-- Limits -->
                <?php if (!empty($row['qr_requests']) || !empty($row['txn_limit'])): ?>
                <div class="d-flex gap-2 mb-3 flex-wrap">
                    <?php if (!empty($row['qr_requests'])): ?>
                    <span style="font-size:11.5px; background:#f0fdf9; color:#065f46; border:1px solid #a7f3d0; border-radius:6px; padding:3px 10px; font-weight:600;">
                        <i class="bi bi-qr-code me-1"></i><?php echo htmlspecialchars($row['qr_requests']); ?> QR
                    </span>
                    <?php endif; ?>
                    <?php if (!empty($row['txn_limit'])): ?>
                    <span style="font-size:11.5px; background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; border-radius:6px; padding:3px 10px; font-weight:600;">
                        <i class="bi bi-arrow-left-right me-1"></i><?php echo htmlspecialchars($row['txn_limit']); ?> Txn
                    </span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <ul class="msub-feature-list">
                    <?php foreach ($plan_features as [$key, $icon, $label]): 
                        $enabled = !empty($row[$key]);
                    ?>
                    <li>
                        <i class="bi <?php echo $enabled ? 'bi-check-circle-fill feat-ok' : 'bi-x-circle-fill feat-no'; ?>"></i>
                        <i class="bi <?php echo $icon; ?>" style="color:#94a3b8; font-size:12px;"></i>
                        <?php echo htmlspecialchars($label); ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <button onclick="window.location.href='manage_subscription?srno=<?php echo $row['id']; ?>'" class="msub-edit-btn mb-2">
                    <i class="bi bi-pencil-square"></i> Edit Plan
                </button>
                <form method="POST" action="" id="delform_<?php echo $row['id']; ?>">
                    <input type="hidden" name="del_id" value="<?php echo $row['id']; ?>">
                    <input type="hidden" name="deletesub" value="1">
                </form>
                <button type="button" class="msub-delete-btn" onclick="confirmDelete(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars(addslashes($row['plan_name'])); ?>')">
                    <i class="bi bi-trash3-fill"></i> Delete Plan
                </button>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
</div>

<?php endif; ?>

<?php include "footer.php"; ?>

<script>
function confirmDelete(planId, planName) {
    Swal.fire({
        title: 'Delete "' + planName + '" ?',
        html: '<p style="color:#64748b;font-size:14px;">This action is <strong>permanent</strong> and cannot be undone.<br>All merchants subscribed to this plan will <strong>not</strong> be affected.</p>',
        icon: 'warning',
        iconColor: '#dc2626',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="bi bi-trash3-fill me-1"></i> Yes, Delete',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-4',
            confirmButton: 'fw-bold',
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('delform_' + planId).submit();
        }
    });
}
</script>