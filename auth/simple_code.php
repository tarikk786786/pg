<?php include "header.php"; ?>

<!-- SDK Hero Banner -->
<div class="pi-hero-card pi-hero-card-merchant mb-4">
    <div class="row g-4 align-items-center">
        <div class="col-lg-12">
            <span class="pi-hero-eyebrow"><i class="bi bi-download me-1"></i>Developer Kits</span>
            <h2>SDK Downloads</h2>
            <p>Welcome to our SDK Downloads page. Below, you can find information about our various software development kits (SDKs) and how to integrate them into your applications.</p>
        </div>
    </div>
</div>

<div class="row g-4">
    <?php
    $sdks = [
        ["name" => "Android SDK", "desc" => "Our Android SDK allows you to seamlessly integrate our services into your Android applications.", "file" => "sdk/android.zip", "image" => "assets/img/android_sdk.png"],
        ["name" => "PHP SDK", "desc" => "Our PHP SDK provides you with the tools to interact with our API in your PHP-based web applications.", "file" => "sdk/php.zip", "image" => "assets/img/php_sdk.png"],
        ["name" => "Java SDK", "desc" => "Our Java SDK for Java applications.", "file" => "sdk/java.zip", "image" => "assets/img/generic_sdk.png"],
        ["name" => "Python SDK", "desc" => "Our Python SDK for Python applications.", "file" => "sdk/python.zip", "image" => "assets/img/python_sdk.png"],
        ["name" => "C# SDK", "desc" => "Our C# SDK for .NET applications.", "file" => "sdk/c#.zip", "image" => "assets/img/generic_sdk.png"],
        ["name" => "Ruby SDK", "desc" => "Our Ruby SDK for Ruby applications.", "file" => "sdk/ruby.zip", "image" => "assets/img/generic_sdk.png"],
        ["name" => "JavaScript SDK", "desc" => "Our JavaScript SDK for web applications.", "file" => "sdk/javascript.zip", "image" => "assets/img/js_sdk.png"],
        ["name" => "C++ SDK", "desc" => "Our C++ SDK for native performance applications.", "file" => "sdk/c++.zip", "image" => "assets/img/generic_sdk.png"],
        ["name" => "Kotlin SDK", "desc" => "Our Kotlin SDK for modern Android applications.", "file" => "sdk/kotlin.zip", "image" => "assets/img/generic_sdk.png"],
        ["name" => "TypeScript SDK", "desc" => "Our TypeScript SDK for strongly-typed web applications.", "file" => "sdk/typescript.zip", "image" => "assets/img/generic_sdk.png"],
        ["name" => "WordPress SDK", "desc" => "Our WordPress/WooCommerce plugin for easy checkout integration.", "file" => "sdk/upi-gateway-woocommerce.zip", "image" => "assets/img/wordpress_sdk.png"],
        ["name" => "Swift SDK", "desc" => "Our Swift SDK for iOS applications.", "file" => "sdk/swift.zip", "image" => "assets/img/generic_sdk.png"]
    ];

    foreach ($sdks as $sdk) {
    ?>
    <div class="col-md-6 col-lg-4">
        <div class="pi-card p-0 h-100 d-flex flex-column justify-content-between overflow-hidden" style="border: 1px solid rgba(0,0,0,0.06); border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.015); background: #ffffff; transition: transform 0.2s ease;">
            <div>
                <img src="<?php echo htmlspecialchars($sdk['image']); ?>" alt="<?php echo htmlspecialchars($sdk['name']); ?>" class="w-100" style="height: 140px; object-fit: cover; border-bottom: 1px solid rgba(0,0,0,0.06);">
                <div class="p-4">
                    <h5 class="fw-bold mb-2 text-dark" style="font-size: 16px;"><?php echo htmlspecialchars($sdk["name"]); ?></h5>
                    <p class="text-muted small mb-0" style="min-height: 48px; line-height: 1.5; font-size: 12.5px;"><?php echo htmlspecialchars($sdk["desc"]); ?></p>
                </div>
            </div>
            <div class="p-4 pt-0">
                <a href="<?php echo htmlspecialchars($sdk["file"]); ?>" class="btn w-100 fw-bold d-flex align-items-center justify-content-center gap-2" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: #ffffff; border: none; border-radius: 10px; padding: 11px; font-size: 13.5px; transition: all 0.2s ease; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);">
                    <i class="bi bi-download"></i> Download SDK
                </a>
            </div>
        </div>
    </div>
    <?php } ?>
</div>

<?php include "footer.php"; ?>
