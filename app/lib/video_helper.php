<?php
// app/lib/video_helper.php - Video processing helper functions

declare(strict_types=1);

/**
 * Generate a thumbnail for a video file
 * @param string $videoPath Path to the video file
 * @param string $thumbnailPath Path where thumbnail should be saved
 * @param int $timeOffset Time offset in seconds for thumbnail capture (default: 1)
 * @return bool Success status
 */
function generate_video_thumbnail(string $videoPath, string $thumbnailPath, int $timeOffset = 1): bool {
    // Check if video file exists
    if (!file_exists($videoPath)) {
        return false;
    }
    
    // Ensure thumbnail directory exists
    $thumbnailDir = dirname($thumbnailPath);
    if (!is_dir($thumbnailDir)) {
        if (!mkdir($thumbnailDir, 0755, true) && !is_dir($thumbnailDir)) {
            return false;
        }
    }
    
    // Try to use FFmpeg if available (most reliable method)
    if (function_exists('exec') && !in_array('exec', explode(',', ini_get('disable_functions')))) {
        $ffmpegCmd = "ffmpeg -i " . escapeshellarg($videoPath) .
                    " -ss {$timeOffset} -vframes 1 -f image2 " .
                    escapeshellarg($thumbnailPath) . " 2>/dev/null";
        
        exec($ffmpegCmd, $output, $returnCode);
        
        if ($returnCode === 0 && file_exists($thumbnailPath)) {
            return true;
        }
    }
    
    // Fallback: Create a generic video placeholder thumbnail
    return create_video_placeholder_thumbnail($thumbnailPath);
}

/**
 * Create a generic placeholder thumbnail for videos
 * @param string $thumbnailPath Path where thumbnail should be saved
 * @return bool Success status
 */
function create_video_placeholder_thumbnail(string $thumbnailPath): bool {
    // Check if GD extension is available
    if (!extension_loaded('gd')) {
        // If GD is not available, create a simple text file as fallback
        return file_put_contents($thumbnailPath . '.txt', 'Video thumbnail placeholder') !== false;
    }
    
    // Create a simple 200x150 placeholder image
    $width = 200;
    $height = 150;
    
    $image = imagecreate($width, $height);
    if (!$image) {
        return false;
    }
    
    // Colors
    $backgroundColor = imagecolorallocate($image, 45, 45, 45);  // Dark gray
    $textColor = imagecolorallocate($image, 255, 255, 255);    // White
    $iconColor = imagecolorallocate($image, 100, 149, 237);    // Cornflower blue
    
    // Fill background
    imagefill($image, 0, 0, $backgroundColor);
    
    // Draw play button (triangle)
    $playButton = [
        $width/2 - 20, $height/2 - 15,  // Top left
        $width/2 - 20, $height/2 + 15,  // Bottom left
        $width/2 + 15, $height/2        // Right point
    ];
    imagefilledpolygon($image, $playButton, 3, $iconColor);
    
    // Add "VIDEO" text
    $fontSize = 3;
    $text = "VIDEO";
    $textWidth = imagefontwidth($fontSize) * strlen($text);
    $textX = ($width - $textWidth) / 2;
    $textY = $height - 25;
    imagestring($image, $fontSize, (int)$textX, (int)$textY, $text, $textColor);
    
    // Save as JPEG
    $success = imagejpeg($image, $thumbnailPath, 80);
    imagedestroy($image);
    
    return $success;
}

/**
 * Get thumbnail path for a video file
 * @param string $videoPath Path to the video file
 * @return string Path where thumbnail should be stored
 */
function get_video_thumbnail_path(string $videoPath): string {
    $pathInfo = pathinfo($videoPath);
    $thumbnailDir = $pathInfo['dirname'] . '/thumbnails';
    $thumbnailName = $pathInfo['filename'] . '_thumb.jpg';
    
    return $thumbnailDir . '/' . $thumbnailName;
}

/**
 * Get thumbnail URL for a video file
 * @param string $videoPath Path to the video file
 * @return string URL to the thumbnail
 */
function get_video_thumbnail_url(string $videoPath): string {
    $thumbnailPath = get_video_thumbnail_path($videoPath);
    
    // Generate thumbnail if it doesn't exist
    if (!file_exists($thumbnailPath)) {
        $success = generate_video_thumbnail($videoPath, $thumbnailPath);
        // If thumbnail generation failed, return a default video icon URL
        if (!$success) {
            return base_url('images/video-placeholder.png');
        }
    }
    
    // Convert file path to URL
    $thumbnailUrl = str_replace('\\', '/', $thumbnailPath);
    if (strpos($thumbnailUrl, 'public/') === 0) {
        $thumbnailUrl = substr($thumbnailUrl, 7); // Remove 'public/' prefix
    }
    
    return base_url($thumbnailUrl);
}

/**
 * Check if a file is a video based on its extension
 * @param string $filename The filename to check
 * @return bool True if it's a video file
 */
function is_video_file(string $filename): bool {
    $videoExtensions = ['mp4', 'mov', 'avi', 'wmv', 'webm', 'mkv', 'flv', '3gp', 'mpeg', 'ogv'];
    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    return in_array($extension, $videoExtensions, true);
}

/**
 * Get video duration in seconds (requires FFmpeg)
 * @param string $videoPath Path to the video file
 * @return int Duration in seconds, or 0 if unable to determine
 */
function get_video_duration(string $videoPath): int {
    if (!file_exists($videoPath) || !function_exists('exec') || in_array('exec', explode(',', ini_get('disable_functions')))) {
        return 0;
    }
    
    $cmd = "ffprobe -v quiet -show_entries format=duration -of csv=p=0 " . escapeshellarg($videoPath);
    exec($cmd, $output, $returnCode);
    
    if ($returnCode === 0 && !empty($output[0])) {
        return (int)round((float)$output[0]);
    }
    
    return 0;
}

/**
 * Format duration in seconds to human readable format
 * @param int $seconds Duration in seconds
 * @return string Formatted duration (e.g., "2:30", "1:05:30")
 */
function format_video_duration(int $seconds): string {
    if ($seconds < 60) {
        return "0:" . str_pad((string)$seconds, 2, '0', STR_PAD_LEFT);
    } elseif ($seconds < 3600) {
        $minutes = intval($seconds / 60);
        $seconds = $seconds % 60;
        return $minutes . ":" . str_pad((string)$seconds, 2, '0', STR_PAD_LEFT);
    } else {
        $hours = intval($seconds / 3600);
        $minutes = intval(($seconds % 3600) / 60);
        $seconds = $seconds % 60;
        return $hours . ":" . str_pad((string)$minutes, 2, '0', STR_PAD_LEFT) . ":" . str_pad((string)$seconds, 2, '0', STR_PAD_LEFT);
    }
}