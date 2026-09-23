<?php
// app/lib/file_upload.php - Secure file upload helper

declare(strict_types=1);

function ensure_dir(string $dir): void {
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
}

function random_filename(string $original): string {
    $ext = pathinfo($original, PATHINFO_EXTENSION);
    return bin2hex(random_bytes(16)) . ($ext ? ('.' . strtolower($ext)) : '');
}

function allowed_mime_for_doc(string $mime): bool {
    $allowed = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/bmp',
        // Video formats
        'video/mp4',
        'video/quicktime',
        'video/x-msvideo',     // AVI
        'video/x-ms-wmv',      // WMV
        'video/webm',          // WebM
        'video/x-matroska',    // MKV
        'video/x-flv',         // FLV
        'video/3gpp',          // 3GP
        'video/mpeg',          // MPEG
        'video/ogg',           // OGV
    ];
    return in_array($mime, $allowed, true);
}

function handle_upload(array $file, string $destDir): array {
    if (!isset($file['error']) || is_array($file['error'])) {
        throw new RuntimeException('Invalid upload parameters');
    }
    switch ($file['error']) {
        case UPLOAD_ERR_OK: break;
        case UPLOAD_ERR_NO_FILE: throw new RuntimeException('No file sent');
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE: throw new RuntimeException('Exceeded filesize limit');
        default: throw new RuntimeException('Unknown errors');
    }
    if ($file['size'] > MAX_UPLOAD_BYTES) {
        throw new RuntimeException('Exceeded file size limit');
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']) ?: 'application/octet-stream';
    if (!allowed_mime_for_doc($mime)) {
        throw new RuntimeException('File type not allowed');
    }

    ensure_dir($destDir);
    $target = $destDir . DIRECTORY_SEPARATOR . random_filename($file['name']);
    if (!move_uploaded_file($file['tmp_name'], $target)) {
        throw new RuntimeException('Failed to move uploaded file');
    }
    // Normalize path to use forward slashes for web compatibility
    $normalizedTarget = str_replace('\\', '/', $target);
    // If the path is under the public directory, strip any leading 'public/'
    if (strpos($normalizedTarget, 'public/') === 0) {
        $normalizedTarget = substr($normalizedTarget, 7);
    }

    return [
        'path' => $normalizedTarget,
        'mime' => $mime,
        'size' => (int)$file['size'],
        'original' => $file['name'],
    ];
}
