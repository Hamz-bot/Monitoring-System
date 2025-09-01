<?php
require_once 'config.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
    $imageData = $_POST['image_data'];
    
    if ($userId && $imageData) {
        // Remove the data URL prefix
        $imageData = str_replace('data:image/jpeg;base64,', '', $imageData);
        $imageData = base64_decode($imageData);
        
        // Generate unique filename
        $timestamp = time();
        $filename = "auto_{$userId}_{$timestamp}.jpg";
        $uploadDir = 'uploads/screenshots/';
        $uploadPath = $uploadDir . $filename;
        
        // Create directory if not exists
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Save the image
        if (file_put_contents($uploadPath, $imageData)) {
            // Create thumbnail directory
            $thumbnailDir = $uploadDir . 'thumbnails/';
            if (!file_exists($thumbnailDir)) {
                mkdir($thumbnailDir, 0777, true);
            }
            
            $thumbnailPath = $thumbnailDir . $filename;
            
            // Create thumbnail
            $source = imagecreatefromjpeg($uploadPath);
            $width = imagesx($source);
            $height = imagesy($source);
            
            // Calculate thumbnail size
            $thumbWidth = 300;
            $thumbHeight = 200;
            
            $thumb = imagecreatetruecolor($thumbWidth, $thumbHeight);
            imagecopyresampled($thumb, $source, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, $height);
            
            imagejpeg($thumb, $thumbnailPath, 80);
            
            // Clean up
            imagedestroy($source);
            imagedestroy($thumb);
            
            // Save to database
            $stmt = $conn->prepare("INSERT INTO screenshots (user_id, image_path, thumbnail_path, timestamp, capture_method) VALUES (?, ?, ?, NOW(), 'automatic')");
            $stmt->bind_param("iss", $userId, $uploadPath, $thumbnailPath);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Screenshot saved successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to save screenshot to database']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save image file']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>