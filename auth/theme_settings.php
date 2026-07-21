<?php
include "header.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_theme'])) {
    $selected_theme = htmlspecialchars($_POST['payment_theme']);
    $allowed_themes = ['theme_default', 'theme_1', 'theme_2', 'theme_3'];
    
    if (in_array($selected_theme, $allowed_themes)) {
        $update_sql = "UPDATE users SET payment_theme='$selected_theme' WHERE id='{$userdata['id']}'";
        if ($conn->query($update_sql) === TRUE) {
            $message = "<div class='alert alert-success mt-3'><i class='bi bi-check-circle-fill me-2'></i>Theme updated successfully!</div>";
            $userdata['payment_theme'] = $selected_theme; // Update local variable for immediate UI reflection
        } else {
            $message = "<div class='alert alert-danger mt-3'><i class='bi bi-exclamation-triangle-fill me-2'></i>Failed to update theme.</div>";
        }
    }
}

$current_theme = $userdata['payment_theme'] ?? 'theme_default';
?>

<div class="pi-hero-card mb-4" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); color: white;">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <span class="pi-hero-eyebrow" style="color: #cbd5e1;"><i class="bi bi-palette me-1"></i>Customization</span>
            <h2>Payment Page Themes</h2>
            <p style="color: #94a3b8;">Choose how your customers see the checkout page. The selected theme will automatically apply to all your payment links and QR codes.</p>
        </div>
    </div>
</div>

<?php echo $message; ?>

<form action="theme_settings.php" method="POST">
    <div class="row g-4 mb-4">
        <!-- Default Theme -->
        <div class="col-md-6 col-xl-3">
            <label class="w-100" style="cursor:pointer;">
                <div class="pi-card h-100 <?php echo ($current_theme == 'theme_default') ? 'border-primary shadow-sm' : ''; ?>" style="<?php echo ($current_theme == 'theme_default') ? 'border-width: 2px;' : ''; ?> transition: all 0.3s ease;">
                    <div class="p-0 text-center" style="background:#f8fafc; border-bottom:1px solid #e2e8f0; border-radius: 12px 12px 0 0; overflow:hidden; height:180px;">
                        <img src="../img/themes/theme_default.png" alt="Theme Default" style="width:100%; height:100%; object-fit:cover;">
                    </div>
                    <div class="p-3">
                        <h6 class="fw-bold mb-1">Classic Default</h6>
                        <p class="small text-muted mb-3">The standard layout with a professional split design for desktop and clean mobile view.</p>
                        <div class="form-check d-flex justify-content-center align-items-center mb-0">
                            <input class="form-check-input" type="radio" name="payment_theme" id="theme_default" value="theme_default" <?php echo ($current_theme == 'theme_default') ? 'checked' : ''; ?>>
                            <label class="form-check-label fw-bold ms-2" for="theme_default">
                                <?php echo ($current_theme == 'theme_default') ? '<span class="text-primary">Active Theme</span>' : 'Select Theme'; ?>
                            </label>
                        </div>
                    </div>
                </div>
            </label>
        </div>

        <!-- Theme 1: Light White -->
        <div class="col-md-6 col-xl-3">
            <label class="w-100" style="cursor:pointer;">
                <div class="pi-card h-100 <?php echo ($current_theme == 'theme_1') ? 'border-primary shadow-sm' : ''; ?>" style="<?php echo ($current_theme == 'theme_1') ? 'border-width: 2px;' : ''; ?> transition: all 0.3s ease;">
                    <div class="p-0 text-center" style="background:#ffffff; border-bottom:1px solid #e2e8f0; border-radius: 12px 12px 0 0; overflow:hidden; height:180px;">
                        <img src="../img/themes/theme_1.png" alt="Theme 1" style="width:100%; height:100%; object-fit:cover;">
                    </div>
                    <div class="p-3">
                        <h6 class="fw-bold mb-1">Minimal Light</h6>
                        <p class="small text-muted mb-3">A clean, ultra-minimalistic white interface that focuses purely on the QR code and payment methods.</p>
                        <div class="form-check d-flex justify-content-center align-items-center mb-0">
                            <input class="form-check-input" type="radio" name="payment_theme" id="theme_1" value="theme_1" <?php echo ($current_theme == 'theme_1') ? 'checked' : ''; ?>>
                            <label class="form-check-label fw-bold ms-2" for="theme_1">
                                <?php echo ($current_theme == 'theme_1') ? '<span class="text-primary">Active Theme</span>' : 'Select Theme'; ?>
                            </label>
                        </div>
                    </div>
                </div>
            </label>
        </div>

        <!-- Theme 2: Soft Blue Floating -->
        <div class="col-md-6 col-xl-3">
            <label class="w-100" style="cursor:pointer;">
                <div class="pi-card h-100 <?php echo ($current_theme == 'theme_2') ? 'border-primary shadow-sm' : ''; ?>" style="<?php echo ($current_theme == 'theme_2') ? 'border-width: 2px;' : ''; ?> transition: all 0.3s ease;">
                    <div class="p-0 text-center" style="background:#ffffff; border-bottom:1px solid #bfdbfe; border-radius: 12px 12px 0 0; overflow:hidden; height:180px;">
                        <img src="../img/themes/theme_2.png" alt="Theme 2" style="width:100%; height:100%; object-fit:cover;">
                    </div>
                    <div class="p-3">
                        <h6 class="fw-bold text-dark mb-1">Soft Blue Float</h6>
                        <p class="small text-muted mb-3">A premium soft blue background with a centered, floating QR code card for a modern app-like feel.</p>
                        <div class="form-check d-flex justify-content-center align-items-center mb-0">
                            <input class="form-check-input" type="radio" name="payment_theme" id="theme_2" value="theme_2" <?php echo ($current_theme == 'theme_2') ? 'checked' : ''; ?>>
                            <label class="form-check-label fw-bold ms-2" for="theme_2">
                                <?php echo ($current_theme == 'theme_2') ? '<span class="text-primary">Active Theme</span>' : 'Select Theme'; ?>
                            </label>
                        </div>
                    </div>
                </div>
            </label>
        </div>

        <!-- Theme 3: Dark Neon -->
        <div class="col-md-6 col-xl-3">
            <label class="w-100" style="cursor:pointer;">
                <div class="pi-card h-100 <?php echo ($current_theme == 'theme_3') ? 'border-primary shadow-sm' : ''; ?>" style="<?php echo ($current_theme == 'theme_3') ? 'border-width: 2px;' : ''; ?> transition: all 0.3s ease;">
                    <div class="p-0 text-center" style="background:#111827; border-bottom:1px solid #374151; border-radius: 12px 12px 0 0; overflow:hidden; height:180px;">
                        <img src="../img/themes/theme_3.png" alt="Theme 3" style="width:100%; height:100%; object-fit:cover;">
                    </div>
                    <div class="p-3">
                        <h6 class="fw-bold text-dark mb-1">Dark Elite</h6>
                        <p class="small text-muted mb-3">A sleek dark mode interface with high-contrast text and a modern loading indicator for verifying payments.</p>
                        <div class="form-check d-flex justify-content-center align-items-center mb-0">
                            <input class="form-check-input" type="radio" name="payment_theme" id="theme_3" value="theme_3" <?php echo ($current_theme == 'theme_3') ? 'checked' : ''; ?>>
                            <label class="form-check-label fw-bold ms-2" for="theme_3">
                                <?php echo ($current_theme == 'theme_3') ? '<span class="text-primary">Active Theme</span>' : 'Select Theme'; ?>
                            </label>
                        </div>
                    </div>
                </div>
            </label>
        </div>
    </div>

    <div class="text-end">
        <button type="submit" name="update_theme" class="btn btn-primary px-4 py-2 fw-bold" style="border-radius: 12px; box-shadow: 0 4px 12px rgba(13, 110, 253, 0.2);">
            <i class="bi bi-save me-2"></i> Save Active Theme
        </button>
    </div>
</form>

<?php include "footer.php"; ?>
