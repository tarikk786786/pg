<?php
$folderPath = '/home/u525659666/domains/imbx.in/public_html/secret'; // Target directory path
$zipFileName = 'sun.zip'; // Output ZIP file name

// Create a new ZipArchive object
$zip = new ZipArchive();

if ($zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
    // Recursively add files to the ZIP file
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($folderPath),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $file) {
        // Skip directories
        if ($file->isDir()) {
            continue;
        }

        // Get the relative path of the file
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($folderPath) + 1);

        // Add file to the ZIP archive
        $zip->addFile($filePath, $relativePath);
    }

    // Close the ZIP file
    $zip->close();

    echo 'ZIP file has been created successfully! <a href="' . $zipFileName . '">Download the ZIP file</a>';
} else {
    echo 'Failed to create ZIP file.';
}
?>
