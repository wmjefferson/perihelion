<?php
header('Content-Type: application/json; charset=utf-8');

$baseDir   = dirname(__DIR__);   // /perihelion
$imagesDir = $baseDir . '/images';

// Current path inside images/, e.g. "", "2024", "2024/events"
$path = isset($_GET['path']) ? trim($_GET['path']) : '';
$path = str_replace(['..', '\\'], '', $path); // basic safety
$path = trim($path, "/");

// Compute the actual directory we’re listing
$targetDir = $imagesDir . ($path !== '' ? '/' . $path : '');

// If directory doesn’t exist, return empty set
if (!is_dir($targetDir)) {
    echo json_encode([
        'images'      => [],
        'directories' => [],
        'totalPages'  => 1,
        'total'       => 0,
    ]);
    exit;
}

// Scan directory
$entries = array_diff(scandir($targetDir), ['.', '..']);

$files = [];
$dirs  = [];

foreach ($entries as $entry) {
    $fullPath = $targetDir . '/' . $entry;

    if (is_dir($fullPath)) {
        // Subdirectory name only (no path prefix)
        $dirs[] = $entry;
        continue;
    }

    // File: keep everything, not just images
    $relative = ($path !== '' ? $path . '/' : '') . $entry;
    // Normalize to forward slashes
    $relative = str_replace('\\', '/', $relative);

    $files[] = $relative;
}

// Sort for stable UI
sort($dirs);
sort($files);

// Basic pagination placeholders (single page)
$total      = count($files);
$totalPages = 1;

// Optional: read limit/page params for future use
// For now we ignore them but keep the shape
$page  = isset($_GET['page'])  ? max(1, (int)$_GET['page'])  : 1;
$limit = isset($_GET['limit']) ? max(1, (int)$_GET['limit']) : $total;

// Emit JSON
echo json_encode([
    'images'      => $files,
    'directories' => $dirs,
    'totalPages'  => $totalPages,
    'total'       => $total,
]);