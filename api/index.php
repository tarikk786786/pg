<?php
// Set working directory to project root so includes/requires work properly
chdir(__DIR__ . '/..');

$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// If root is requested, serve index.php
if ($request_uri == '/' || $request_uri == '') {
    $file = 'index.php';
} else {
    // Remove leading slash
    $file = ltrim($request_uri, '/');
    
    // Check if it's a static asset (css, js, images)
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    if (in_array(strtolower($ext), ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'woff', 'woff2', 'ttf', 'ico'])) {
        if (file_exists($file)) {
            $mime_types = [
                'css' => 'text/css',
                'js' => 'application/javascript',
                'png' => 'image/png',
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'gif' => 'image/gif',
                'svg' => 'image/svg+xml',
                'ico' => 'image/x-icon'
            ];
            if (isset($mime_types[strtolower($ext)])) {
                header('Content-Type: ' . $mime_types[strtolower($ext)]);
            }
            readfile($file);
            exit;
        }
    }
    
    // If not a specific file extension, assume it's a php file without extension (based on previous .htaccess logic)
    if (empty($ext) || $ext == 'php') {
        if (!preg_match('/\.php$/', $file)) {
            $file .= '.php';
        }
    }
}

// Check if the target PHP file exists
if (file_exists($file) && preg_match('/\.php$/', $file)) {
    // We update SCRIPT_FILENAME so PHP scripts think they are accessed directly
    $_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/../' . $file;
    require $file;
} else {
    // Fallback if file not found
    http_response_code(404);
    echo "404 Not Found";
}
