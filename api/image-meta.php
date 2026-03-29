<?php
header('Content-Type: application/json; charset=utf-8');

$baseDir   = dirname(__DIR__);   // /perihelion
$imagesDir = $baseDir . '/images';

$file = isset($_GET['file']) ? trim($_GET['file']) : '';
$file = str_replace(['..', '\\'], '', $file);
$fullPath = $imagesDir . '/' . $file;

if (!$file || !file_exists($fullPath) || !is_file($fullPath)) {
  echo json_encode(['error' => 'File not found']);
  exit;
}

$size = filesize($fullPath);
$info = @getimagesize($fullPath);

echo json_encode([
  'type' => mime_content_type($fullPath),
  'size' => $size,
  'width' => $info ? $info[0] : null,
  'height' => $info ? $info[1] : null,
]);