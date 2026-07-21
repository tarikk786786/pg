<?php
include "header.php";

if(isset($_POST['get_api_token'])){
    $bbbyteuserid=$_SESSION['user_id'];
    $sanitizedMobile = db_real_escape_string($conn, $mobile);

    $uniqueNumber = mt_rand(1000000000, 9999999999);
    $uniqueNumber = str_pad($uniqueNumber, 10, '0', STR_PAD_LEFT); 

    $key = md5($uniqueNumber);
    $keyquery = "UPDATE `users` SET user_token='$key' WHERE mobile = '$sanitizedMobile'";
    $queryres = db_query($conn, $keyquery);
    
    db_query($conn, "UPDATE `orders` SET user_token='$key' WHERE user_id = $bbbyteuserid");
    db_query($conn, "UPDATE `reports` SET user_token='$key' WHERE user_id = $bbbyteuserid");
    db_query($conn, "UPDATE `hdfc` SET user_token='$key' WHERE user_id = $bbbyteuserid");
    db_query($conn, "UPDATE `bharatpe_tokens` SET user_token='$key' WHERE user_id = '$bbbyteuserid'");
    db_query($conn, "UPDATE `phonepe_tokens` SET user_token='$key' WHERE user_id = '$bbbyteuserid'");
    db_query($conn, "UPDATE `store_id` SET user_token='$key' WHERE user_id = '$bbbyteuserid'");
    db_query($conn, "UPDATE `paytm_tokens` SET user_token='$key' WHERE user_id = '$bbbyteuserid'");
    db_query($conn, "UPDATE `googlepay_transactions` SET user_token='$key' WHERE user_id = '$bbbyteuserid'");
    db_query($conn, "UPDATE `googlepay_tokens` SET user_token='$key' WHERE user_id = '$bbbyteuserid'");
    
    if($queryres){
        echo '<script>
            Swal.fire({
                icon: "success",
                title: "New API Key generated! 🎉",
                showConfirmButton: true,
                confirmButtonText: "Ok!",
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "apidetails";
                }
            });
        </script>';
        exit;
    } else {
        echo '<script>
            Swal.fire({
                icon: "error",
                title: "API Key Generating Failed! ❌",
                showConfirmButton: true,
                confirmButtonText: "Ok!",
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "apidetails";
                }
            });
        </script>';
        exit;
    }
}

// Custom function to validate URLs
function isValidUrl($url) {
    $parsed_url = parse_url($url);
    return isset($parsed_url['host']) && preg_match("/\.\w+$/", $parsed_url['host']);
}

if(isset($_POST['update_webhook'])){
    $bytecallbackurl=db_real_escape_string($conn,$_POST['webhook_url']);
    
    if (!isValidUrl($bytecallbackurl)) {
        echo '<script>
            Swal.fire({
                icon: "error",
                title: "Invalid webhook url! ⚠️",
                showConfirmButton: true,
                confirmButtonText: "Ok!",
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "apidetails";
                }
            });
        </script>';
        exit();
    }

    $sanitizedMobile = db_real_escape_string($conn, $mobile);
    $keyquery = "UPDATE `users` SET callback_url='$bytecallbackurl' WHERE mobile = '$sanitizedMobile'";
    $queryres = db_query($conn, $keyquery);
    
    if($queryres){
        echo '<script>
            Swal.fire({
                icon: "success",
                title: "Webhook Updated Successfully! ✅",
                showConfirmButton: true,
                confirmButtonText: "Ok!",
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "apidetails";
                }
            });
        </script>';
        exit;
    } else {
        echo '<script>
            Swal.fire({
                icon: "error",
                title: "Error Updating Webhook Try again Later! ❌",
                showConfirmButton: true,
                confirmButtonText: "Ok!",
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "apidetails";
                }
            });
        </script>';
        exit;
    }
}
?>

<style>
    .api-doc-container {
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .badge-method {
        font-size: 11px;
        font-weight: 800;
        padding: 6px 12px;
        border-radius: 6px;
        letter-spacing: 0.5px;
    }
    .badge-post {
        background-color: #ecfdf5;
        color: #10b981;
    }
    .endpoint-input {
        background-color: #f8fafc !important;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        font-family: monospace;
        font-size: 13px;
        font-weight: 600;
        color: #2563eb;
        padding: 12px 16px;
    }
    .btn-copy-custom {
        background-color: #ffffff;
        color: #2563eb;
        border: 1.5px solid #2563eb;
        border-radius: 8px;
        padding: 6px 14px;
        font-size: 13px;
        font-weight: 700;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
    }
    .btn-copy-custom:hover {
        background-color: #2563eb;
        color: #ffffff;
    }
    
    /* Codeblock styling */
    .code-block-wrapper {
        background-color: #0f172a;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        position: relative;
        border: 1px solid #1e293b;
    }
    .code-block-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
        font-size: 12px;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .code-block-content {
        margin: 0;
        font-family: 'Courier New', Courier, monospace;
        font-size: 13px;
        color: #e2e8f0;
        overflow-x: auto;
        white-space: pre-wrap;
        word-break: break-all;
    }
    
    /* Table parameter styling */
    .table-param {
        width: 100%;
        margin-bottom: 24px;
    }
    .table-param th {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        color: #94a3b8;
        letter-spacing: 0.5px;
        border-bottom: 1.5px solid #f1f5f9;
        padding: 12px 16px;
    }
    .table-param td {
        font-size: 13px;
        color: #475569;
        padding: 14px 16px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .param-name {
        font-family: monospace;
        font-weight: 700;
        color: #2563eb;
    }
    .badge-req {
        background-color: #fee2e2;
        color: #ef4444;
        font-size: 9px;
        font-weight: 800;
        padding: 2px 6px;
        border-radius: 4px;
        text-transform: uppercase;
    }
    .badge-opt {
        background-color: #f1f5f9;
        color: #64748b;
        font-size: 9px;
        font-weight: 800;
        padding: 2px 6px;
        border-radius: 4px;
        text-transform: uppercase;
    }
    
    /* Step Cards */
    .step-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 20px;
        height: 100%;
        transition: all 0.2s ease;
    }
    .step-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.02);
    }
    .step-num {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background-color: #eff6ff;
        color: #2563eb;
        font-weight: 800;
        font-size: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 14px;
    }
    .step-title {
        font-size: 14px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 6px;
    }
    .step-desc {
        font-size: 12px;
        color: #64748b;
        line-height: 1.5;
        margin-bottom: 0;
    }

    /* Warning card */
    .warning-alert-card {
        background-color: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 12px;
        padding: 16px 20px;
        color: #b45309;
        font-size: 13px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 24px;
    }
</style>

<div class="api-doc-container">

    <div class="pi-hero-card pi-hero-card-merchant mb-4 p-4 text-white rounded-3">
        <div class="row g-4 align-items-center">
            <div class="col-md-8">
                <span class="pi-hero-eyebrow"><i class="bi bi-code-slash me-1"></i>Developers</span>
                <h2>API Documentation</h2>
                <p>Integrate our instant payment gateway directly into your application using these APIs.</p>
            </div>
        </div>
    </div>
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="step-card">
                <div class="step-num">1</div>
                <div class="step-title"><i class="bi bi-pencil-square me-2 text-primary"></i>Create Order</div>
                <p class="step-desc">Call API to generate a dynamic checkout link using your secure user API token.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="step-card">
                <div class="step-num">2</div>
                <div class="step-title"><i class="bi bi-box-arrow-up-right me-2 text-primary"></i>Redirect Customer</div>
                <p class="step-desc">Redirect customer to the returned payment link url to complete checkout.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="step-card">
                <div class="step-num">3</div>
                <div class="step-title"><i class="bi bi-check-circle-fill me-2 text-primary"></i>Verify Payment</div>
                <p class="step-desc">Verify checkout status via dashboard logs or automated POST webhook callbacks.</p>
            </div>
        </div>
    </div>

    <!-- Credentials forms side by side -->
    <div class="row g-4 mb-4">
        <!-- API Credentials Card -->
        <div class="col-lg-6">
            <div class="pi-card p-4 h-100">
                <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-key-fill text-primary me-2"></i>API Credentials</h5>
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="mb-4">
                        <label class="form-label text-dark fw-bold d-block mb-2">API Token</label>
                        <div class="input-group">
                            <input type="text" placeholder="Generate Token" value="<?php echo htmlspecialchars($userdata['user_token'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control" style="font-family:monospace; border-top-right-radius: 0; border-bottom-right-radius: 0;" readonly>
                            <button type="button" class="btn btn-primary d-flex align-items-center px-3" style="border-top-right-radius: 10px; border-bottom-right-radius: 10px;" onclick="navigator.clipboard.writeText('<?php echo htmlspecialchars($userdata['user_token'], ENT_QUOTES, 'UTF-8'); ?>'); Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'API Token Copied! 📋', showConfirmButton: false, timer: 1500 });">
                                <i class="bi bi-clipboard"></i>
                            </button>
                        </div>
                    </div>
                    <button type="submit" name="get_api_token" class="btn btn-primary fw-bold w-100 py-2">
                        <i class="bi bi-arrow-clockwise me-1"></i>Generate New Token
                    </button>
                </form>
            </div>
        </div>

        <!-- Webhook Settings Card -->
        <div class="col-lg-6">
            <div class="pi-card p-4 h-100">
                <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-link-45deg text-success me-2"></i>Webhook Callback URL</h5>
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="mb-4">
                        <label class="form-label text-dark fw-bold d-block mb-2">Webhook URL</label>
                        <input type="url" name="webhook_url" placeholder="https://example.com/callback" value="<?php echo htmlspecialchars($userdata['callback_url'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control" required pattern="https?://[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}/?[a-zA-Z0-9.-]*\??[a-zA-Z0-9.-]*" title="Enter a valid URL">
                        <div class="form-text text-danger small mt-2">Note: URL must include protocol (http / https)</div>
                    </div>
                    <button type="submit" name="update_webhook" class="btn btn-success fw-bold w-100 py-2">
                        <i class="bi bi-save me-1"></i>Update Callback URL
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- API Reference Section -->
    <!-- Create Payment Order API -->
    <div class="pi-card p-4 mb-4">
        <div class="d-flex align-items-center gap-2 mb-3">
            <span class="badge-method badge-post">POST</span>
            <h5 class="fw-bold text-dark mb-0"><i class="bi bi-credit-card-fill me-2 text-primary"></i>Create Payment Order</h5>
        </div>
        <div class="input-group mb-4">
            <input type="text" value="https://<?php echo htmlspecialchars($server, ENT_QUOTES, 'UTF-8'); ?>/api/create-order" class="form-control endpoint-input" readonly>
            <button class="btn-copy-custom" onclick="navigator.clipboard.writeText('https://<?php echo htmlspecialchars($server, ENT_QUOTES, 'UTF-8'); ?>/api/create-order'); Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Endpoint URL Copied! 📋', showConfirmButton: false, timer: 1500 });">
                <i class="bi bi-clipboard"></i>Copy
            </button>
        </div>

        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-list-columns-reverse me-2"></i>Request Parameters</h6>
        <div class="table-responsive">
            <table class="table-param">
                <thead>
                    <tr>
                        <th>Parameter</th>
                        <th>Type</th>
                        <th>Required</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="param-name">user_token</td>
                        <td>string</td>
                        <td><span class="badge-req">Required</span></td>
                        <td>Your unique API user token (e.g. <?php echo htmlspecialchars($userdata['user_token'], ENT_QUOTES, 'UTF-8'); ?>)</td>
                    </tr>
                    <tr>
                        <td class="param-name">amount</td>
                        <td>number / float</td>
                        <td><span class="badge-req">Required</span></td>
                        <td>Amount in INR (e.g. 1.00)</td>
                    </tr>
                    <tr>
                        <td class="param-name">order_id</td>
                        <td>string</td>
                        <td><span class="badge-req">Required</span></td>
                        <td>Unique order ID from your website</td>
                    </tr>
                    <tr>
                        <td class="param-name">customer_mobile</td>
                        <td>string</td>
                        <td><span class="badge-req">Required</span></td>
                        <td>10 digit customer mobile number</td>
                    </tr>
                    <tr>
                        <td class="param-name">redirect_url</td>
                        <td>string</td>
                        <td><span class="badge-req">Required</span></td>
                        <td>URL to redirect customer after payment status</td>
                    </tr>
                    <tr>
                        <td class="param-name">remark1</td>
                        <td>string</td>
                        <td><span class="badge-opt">Optional</span></td>
                        <td>Payment title / Remark title to show on checkouts</td>
                    </tr>
                    <tr>
                        <td class="param-name">remark2</td>
                        <td>string</td>
                        <td><span class="badge-opt">Optional</span></td>
                        <td>Additional transaction remarks</td>
                    </tr>
                    <tr>
                        <td class="param-name">expiry_seconds</td>
                        <td>integer</td>
                        <td><span class="badge-opt">Optional</span></td>
                        <td>Validity window in seconds. Default 7200</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="code-block-wrapper">
                    <div class="code-block-header">
                        <span><i class="bi bi-folder2-open me-2"></i>JSON Request Payload</span>
                        <button class="btn btn-sm btn-link text-white p-0 text-decoration-none" onclick="navigator.clipboard.writeText(this.nextElementSibling.innerText); Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'JSON Request Copied! 📋', showConfirmButton: false, timer: 1500 });"><i class="bi bi-clipboard me-1"></i>Copy</button>
                    </div>
                    <pre class="code-block-content">{
  "customer_mobile": "9219565158",
  "user_token": "<?php echo htmlspecialchars($userdata['user_token'], ENT_QUOTES, 'UTF-8'); ?>",
  "amount": "1.00",
  "order_id": "8787772321800",
  "redirect_url": "https://yourwebsite.com/redirect",
  "remark1": "Dezo",
  "remark2": "Payment Description",
  "expiry_seconds": 7200
}</pre>
                </div>
            </div>
            <div class="col-md-6">
                <div class="code-block-wrapper">
                    <div class="code-block-header">
                        <span><i class="bi bi-box-seam me-2"></i>JSON Response Payload</span>
                        <button class="btn btn-sm btn-link text-white p-0 text-decoration-none" onclick="navigator.clipboard.writeText(this.nextElementSibling.innerText); Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'JSON Response Copied! 📋', showConfirmButton: false, timer: 1500 });"><i class="bi bi-clipboard me-1"></i>Copy</button>
                    </div>
                    <pre class="code-block-content">{
  "status": true,
  "message": "Order Created Successfully",
  "result": {
    "orderId": "8787772321800",
    "payment_url": "https://<?php echo htmlspecialchars($server, ENT_QUOTES, 'UTF-8'); ?>/payment/instant-pay/xxxx"
  }
}</pre>
                </div>
            </div>
        </div>

        <!-- Integration Script Code Block (PHP & curl) -->
        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-terminal-fill me-2"></i>cURL Request Snippet</h6>
        <div class="code-block-wrapper">
            <div class="code-block-header">
                <span>Bash / curl script</span>
                <button class="btn btn-sm btn-link text-white p-0 text-decoration-none" onclick="navigator.clipboard.writeText(this.nextElementSibling.innerText); Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'cURL Snippet Copied! 📋', showConfirmButton: false, timer: 1500 });"><i class="bi bi-clipboard me-1"></i>Copy</button>
            </div>
            <pre class="code-block-content">curl --location --request POST \'https://<?php echo htmlspecialchars($server, ENT_QUOTES, 'UTF-8'); ?>/api/create-order\' \
--header \'Content-Type: application/x-www-form-urlencoded\' \
--data-urlencode \'customer_mobile=9219565158\' \
--data-urlencode \'user_token=<?php echo htmlspecialchars($userdata['user_token'], ENT_QUOTES, 'UTF-8'); ?>\' \
--data-urlencode \'amount=1.00\' \
--data-urlencode \'order_id=8787772321800\' \
--data-urlencode \'redirect_url=https://yourwebsite.com/redirect\' \
--data-urlencode \'remark1=Dezo\' \
--data-urlencode \'remark2=Payment Description\' \
--data-urlencode \'expiry_seconds=7200\'</pre>
        </div>

        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-filetype-php me-2 text-primary"></i>PHP Integration Example</h6>
        <div class="code-block-wrapper">
            <div class="code-block-header">
                <span>PHP Curl Script</span>
                <button class="btn btn-sm btn-link text-white p-0 text-decoration-none" onclick="navigator.clipboard.writeText(this.nextElementSibling.innerText); Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'PHP Snippet Copied! 📋', showConfirmButton: false, timer: 1500 });"><i class="bi bi-clipboard me-1"></i>Copy</button>
            </div>
            <pre class="code-block-content">&lt;?php
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL =&gt; \'https://<?php echo htmlspecialchars($server, ENT_QUOTES, 'UTF-8'); ?>/api/create-order\',
  CURLOPT_RETURNTRANSFER =&gt; true,
  CURLOPT_ENCODING =&gt; \'\',
  CURLOPT_MAXREDIRS =&gt; 10,
  CURLOPT_TIMEOUT =&gt; 0,
  CURLOPT_FOLLOWLOCATION =&gt; true,
  CURLOPT_HTTP_VERSION =&gt; CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST =&gt; \'POST\',
  CURLOPT_POSTFIELDS =&gt; http_build_query(array(
    \'customer_mobile\' =&gt; \'9219565158\',
    \'user_token\' =&gt; \'<?php echo htmlspecialchars($userdata['user_token'], ENT_QUOTES, 'UTF-8'); ?>\',
    \'amount\' =&gt; \'1.00\',
    \'order_id\' =&gt; \'8787772321800\',
    \'redirect_url\' =&gt; \'https://yourwebsite.com/redirect\',
    \'remark1\' =&gt; \'Dezo\',
    \'remark2\' =&gt; \'Payment Description\',
    \'expiry_seconds\' =&gt; \'7200\'
  )),
  CURLOPT_HTTPHEADER =&gt; array(
    \'Content-Type: application/x-www-form-urlencoded\'
  ),
));
$response = curl_exec($curl);
curl_close($curl);
echo $response;
?&gt;</pre>
        </div>
    </div>

    <!-- Check Order Status API -->
    <div class="pi-card p-4 mb-4">
        <div class="d-flex align-items-center gap-2 mb-3">
            <span class="badge-method badge-post" style="background-color: #eff6ff; color: #2563eb;">POST</span>
            <h5 class="fw-bold text-dark mb-0"><i class="bi bi-search me-2 text-primary"></i>Check Order Status</h5>
        </div>
        <div class="input-group mb-4">
            <input type="text" value="https://<?php echo htmlspecialchars($server, ENT_QUOTES, 'UTF-8'); ?>/api/check-order-status" class="form-control endpoint-input" readonly>
            <button class="btn-copy-custom" onclick="navigator.clipboard.writeText('https://<?php echo htmlspecialchars($server, ENT_QUOTES, 'UTF-8'); ?>/api/check-order-status'); Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Status Endpoint Copied! 📋', showConfirmButton: false, timer: 1500 });">
                <i class="bi bi-clipboard"></i>Copy
            </button>
        </div>

        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-list-columns-reverse me-2"></i>Request Parameters</h6>
        <div class="table-responsive">
            <table class="table-param">
                <thead>
                    <tr>
                        <th>Parameter</th>
                        <th>Required</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="param-name">user_token</td>
                        <td><span class="badge-req">Required</span></td>
                        <td>Your unique API user token (e.g. <?php echo htmlspecialchars($userdata['user_token'], ENT_QUOTES, 'UTF-8'); ?>)</td>
                    </tr>
                    <tr>
                        <td class="param-name">order_id</td>
                        <td><span class="badge-req">Required</span></td>
                        <td>Order ID you generated during order creation</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="code-block-wrapper">
            <div class="code-block-header">
                <span><i class="bi bi-pc-display me-2"></i>cURL Status Command</span>
                <button class="btn btn-sm btn-link text-white p-0 text-decoration-none" onclick="navigator.clipboard.writeText(this.nextElementSibling.innerText); Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'cURL Snippet Copied! 📋', showConfirmButton: false, timer: 1500 });"><i class="bi bi-clipboard me-1"></i>Copy</button>
            </div>
            <pre class="code-block-content">curl --location --request POST \'https://<?php echo htmlspecialchars($server, ENT_QUOTES, 'UTF-8'); ?>/api/check-order-status\' \
--header \'Content-Type: application/x-www-form-urlencoded\' \
--data-urlencode \'user_token=<?php echo htmlspecialchars($userdata['user_token'], ENT_QUOTES, 'UTF-8'); ?>\' \
--data-urlencode \'order_id=8787772321800\'</pre>
        </div>

        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-bar-chart-fill me-2"></i>JSON Status Response</h6>
        <div class="code-block-wrapper">
            <div class="code-block-header">
                <span>JSON Response Payload</span>
                <button class="btn btn-sm btn-link text-white p-0 text-decoration-none" onclick="navigator.clipboard.writeText(this.nextElementSibling.innerText); Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'JSON Response Copied! 📋', showConfirmButton: false, timer: 1500 });"><i class="bi bi-clipboard me-1"></i>Copy</button>
            </div>
            <pre class="code-block-content">{
  "status": "SUCCESS",
  "order_id": "8787772321800",
  "amount": "1.00",
  "paymentApp": "Paytm",
  "UTR": "416802195610",
  "date": "2026-06-07 18:15:30"
}</pre>
        </div>
    </div>

    <!-- Webhook Response details (POST Callback) -->
    <div class="pi-card p-4 mb-4">
        <h5 class="fw-bold mb-4 text-dark"><i class="bi bi-broadcast text-primary me-2"></i>Webhook Callback Details ⚡</h5>
        <p class="text-secondary small mb-4">When a payment is processed, our system posts callback transaction logs directly to your configured Webhook URL endpoint.</p>

        <div class="row g-4">
            <div class="col-md-7">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-list-columns-reverse me-2"></i>Parameters Received</h6>
                <div class="table-responsive">
                    <table class="table-param" style="font-size:12px;">
                        <thead>
                            <tr>
                                <th>Parameter</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="param-name">status</td>
                                <td>Transaction status (SUCCESS / FAILURE)</td>
                            </tr>
                            <tr>
                                <td class="param-name">amount</td>
                                <td>Transaction Amount in INR</td>
                            </tr>
                            <tr>
                                <td class="param-name">order_id</td>
                                <td>Your unique order ID reference</td>
                            </tr>
                            <tr>
                                <td class="param-name">UTR</td>
                                <td>12-digit transaction UTR number</td>
                            </tr>
                            <tr>
                                <td class="param-name">paymentApp</td>
                                <td>The UPI App used (e.g. GPay, Paytm, BHIM)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-5">
                <div class="code-block-wrapper">
                    <div class="code-block-header">
                        <span><i class="bi bi-box-seam me-2"></i>Webhook JSON Payload</span>
                        <button class="btn btn-sm btn-link text-white p-0 text-decoration-none" onclick="navigator.clipboard.writeText(this.nextElementSibling.innerText); Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Webhook Payload Copied! 📋', showConfirmButton: false, timer: 1500 });"><i class="bi bi-clipboard me-1"></i>Copy</button>
                    </div>
                    <pre class="code-block-content">{
  "status": "SUCCESS",
  "order_id": "8787772321800",
  "amount": "1.00",
  "paymentApp": "Paytm",
  "UTR": "416802195610"
}</pre>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Codes Table -->
    <div class="pi-card p-0 overflow-hidden mb-4">
        <div class="p-4" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border-bottom: 1px solid #e2e8f0;">
            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <span><i class="bi bi-stoplights-fill me-2"></i>API Response Codes & Resolutions</span>
            </h5>
        </div>
        <div class="table-responsive">
            <table class="table-custom align-middle mb-0">
                <thead>
                    <tr style="background-color: #f8fafc;">
                        <th style="width: 15%; padding: 16px 24px; font-weight: 700; color: #475569;"><i class="bi bi-key me-1"></i>HTTP Code</th>
                        <th style="width: 15%; padding: 16px 24px; font-weight: 700; color: #475569;"><i class="bi bi-lightning-charge me-1"></i>Status</th>
                        <th style="width: 30%; padding: 16px 24px; font-weight: 700; color: #475569;"><i class="bi bi-chat-dots me-1"></i>Message</th>
                        <th style="width: 40%; padding: 16px 24px; font-weight: 700; color: #475569;"><i class="bi bi-tools me-1"></i>Resolution</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="transition: all 0.2s ease;">
                        <td style="padding: 18px 24px;">
                            <span class="badge" style="background-color: #fef2f2; color: #ef4444; border: 1px solid #fee2e2; padding: 6px 12px; border-radius: 8px; font-weight: 800; font-family: monospace;">400</span>
                        </td>
                        <td style="padding: 18px 24px;">
                            <span class="status-badge status-failed" style="font-weight: 700;"><i class="bi bi-exclamation-triangle-fill me-1"></i>Error</span>
                        </td>
                        <td class="fw-semibold text-dark" style="padding: 18px 24px; font-size: 14px;">Invalid Webhook / Callback url</td>
                        <td style="padding: 18px 24px; color: #64748b; font-size: 13.5px;">
                            <i class="bi bi-info-circle text-warning me-1"></i> Make sure your webhook URL has the prefix <code>http://</code> or <code>https://</code>
                        </td>
                    </tr>
                    <tr style="transition: all 0.2s ease;">
                        <td style="padding: 18px 24px;">
                            <span class="badge" style="background-color: #fef2f2; color: #ef4444; border: 1px solid #fee2e2; padding: 6px 12px; border-radius: 8px; font-weight: 800; font-family: monospace;">400</span>
                        </td>
                        <td style="padding: 18px 24px;">
                            <span class="status-badge status-failed" style="font-weight: 700;"><i class="bi bi-exclamation-triangle-fill me-1"></i>Error</span>
                        </td>
                        <td class="fw-semibold text-dark" style="padding: 18px 24px; font-size: 14px;">Your Plan Expired Please Renew</td>
                        <td style="padding: 18px 24px; color: #64748b; font-size: 13.5px;">
                            <i class="bi bi-info-circle text-warning me-1"></i> Renew your subscription package under subscription settings
                        </td>
                    </tr>
                    <tr style="transition: all 0.2s ease;">
                        <td style="padding: 18px 24px;">
                            <span class="badge" style="background-color: #fef2f2; color: #ef4444; border: 1px solid #fee2e2; padding: 6px 12px; border-radius: 8px; font-weight: 800; font-family: monospace;">400</span>
                        </td>
                        <td style="padding: 18px 24px;">
                            <span class="status-badge status-failed" style="font-weight: 700;"><i class="bi bi-exclamation-triangle-fill me-1"></i>Error</span>
                        </td>
                        <td class="fw-semibold text-dark" style="padding: 18px 24px; font-size: 14px;">Invalid User Token</td>
                        <td style="padding: 18px 24px; color: #64748b; font-size: 13.5px;">
                            <i class="bi bi-info-circle text-warning me-1"></i> Check your secure user API token code parameter
                        </td>
                    </tr>
                    <tr style="transition: all 0.2s ease;">
                        <td style="padding: 18px 24px;">
                            <span class="badge" style="background-color: #fef2f2; color: #ef4444; border: 1px solid #fee2e2; padding: 6px 12px; border-radius: 8px; font-weight: 800; font-family: monospace;">400</span>
                        </td>
                        <td style="padding: 18px 24px;">
                            <span class="status-badge status-failed" style="font-weight: 700;"><i class="bi bi-exclamation-triangle-fill me-1"></i>Error</span>
                        </td>
                        <td class="fw-semibold text-dark" style="padding: 18px 24px; font-size: 14px;">Order ID already exists</td>
                        <td style="padding: 18px 24px; color: #64748b; font-size: 13.5px;">
                            <i class="bi bi-info-circle text-warning me-1"></i> Use a unique random order ID for every order creation request
                        </td>
                    </tr>
                    <tr style="transition: all 0.2s ease;">
                        <td style="padding: 18px 24px;">
                            <span class="badge" style="background-color: #f0fdf4; color: #15803d; border: 1px solid #dcfce7; padding: 6px 12px; border-radius: 8px; font-weight: 800; font-family: monospace;">201</span>
                        </td>
                        <td style="padding: 18px 24px;">
                            <span class="status-badge status-active" style="font-weight: 700;"><i class="bi bi-check-circle-fill me-1"></i>Success</span>
                        </td>
                        <td class="fw-semibold text-dark" style="padding: 18px 24px; font-size: 14px;">Success</td>
                        <td style="padding: 18px 24px; color: #64748b; font-size: 13.5px;">
                            <i class="bi bi-check-circle-fill text-success me-1"></i> Order created, checkout link generated successfully
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Integration Security Checklist -->
    <div class="pi-card p-4 mb-4">
        <h5 class="fw-bold text-dark mb-3"><i class="bi bi-shield-lock-fill me-2 text-success"></i>Integration Security Checklist</h5>
        <div class="row g-3">
            <div class="col-md-6">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-check-circle-fill text-success"></i>
                    <span class="small text-secondary">Keep API tokens confidential; do not expose them on client sides.</span>
                </div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-check-circle-fill text-success"></i>
                    <span class="small text-secondary">Verify UTR numbers strictly via check status API queries.</span>
                </div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-check-circle-fill text-success"></i>
                    <span class="small text-secondary">Configure callback HTTPS webhooks for instant notifications.</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-check-circle-fill text-success"></i>
                    <span class="small text-secondary">Sanitize inputs properly to prevent injection vulnerabilities.</span>
                </div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-check-circle-fill text-success"></i>
                    <span class="small text-secondary">Avoid hardcoding order status variables.</span>
                </div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <i class="bi bi-check-circle-fill text-success"></i>
                    <span class="small text-secondary">Enforce SSL / HTTPS on callback webhook endpoints.</span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>