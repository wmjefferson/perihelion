<?php
$baseDir   = dirname(__DIR__);   // /perihelion
$imagesDir = $baseDir . '/images';

$data = json_decode(file_get_contents('php://input'), true);
$files = $data['files'] ?? [];

$zipName = 'selected-images.zip';
$tmpZip = tempnam(sys_get_temp_dir(), 'zip');

$zip = new ZipArchive();
if ($zip->open($tmpZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
  http_response_code(500);
  echo 'Could not create zip';
  exit;
}

foreach ($files as $file) {
  $original = str_replace(['..', '\\'], '', $file['original'] ?? '');
  $newName = trim($file['newName'] ?? basename($original));

  $fullPath = $imagesDir . '/' . $original;
  if (is_file($fullPath)) {
    $zip->addFile($fullPath, $newName);
  }
}

$zip->close();

header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $zipName . '"');
header('Content-Length: ' . filesize($tmpZip));
readfile($tmpZip);
unlink($tmpZip);