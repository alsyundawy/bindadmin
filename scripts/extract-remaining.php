<?php
declare(strict_types=1);

/**
 * Extract remaining source blobs (gzip+base64) into place.
 * Usage: php scripts/extract-remaining.php
 */
$base = dirname(__DIR__);
$blobDir = $base . '/storage/src-blobs';

if (!is_dir($blobDir)) {
    echo "No src-blobs directory found.\n";
    exit(0);
}

$files = glob($blobDir . '/*.gz.b64');
if (!$files) {
    echo "No blob files to extract.\n";
    exit(0);
}

foreach ($files as $blob) {
    $name = basename($blob, '.gz.b64');
    $rel = str_replace('__', '/', $name);
    $target = $base . '/' . $rel;
    $dir = dirname($target);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $data = base64_decode(file_get_contents($blob), true);
    if ($data === false) {
        echo "Failed decode: $blob\n";
        continue;
    }
    $raw = gzdecode($data);
    if ($raw === false) {
        echo "Failed gunzip: $blob\n";
        continue;
    }
    file_put_contents($target, $raw);
    echo "Extracted: $rel (" . strlen($raw) . " bytes)\n";
}
echo "Done.\n";
