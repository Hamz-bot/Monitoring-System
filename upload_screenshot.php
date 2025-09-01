<?php
require_once 'config.php';
require_once 'auth.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['screenshot'])) {
    $userId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
    
    if ($userId && $_FILES['screenshot']['error'] === UPLOAD_ERR_OK) {
        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = mime_content_type($_FILES['screenshot']['tmp_name']);
        
        if (in_array($fileType, $allowedTypes)) {
            // Create directory if not exists
            $uploadDir = 'uploads/screenshots/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            // Generate unique filename
            $fileExt = pathinfo($_FILES['screenshot']['name'], PATHINFO_EXTENSION);
            $fileName = time() . '_' . bin2hex(random_bytes(8)) . '.' . $fileExt;
            $uploadPath = $uploadDir . $fileName;
            
            // Get image dimensions
            list($width, $height) = getimagesize($_FILES['screenshot']['tmp_name']);
            
            if (move_uploaded_file($_FILES['screenshot']['tmp_name'], $uploadPath)) {
                // Create thumbnail
                $thumbnailDir = $uploadDir . 'thumbnails/';
                if (!file_exists($thumbnailDir)) {
                    mkdir($thumbnailDir, 0777, true);
                }
                
                $thumbnailPath = $thumbnailDir . $fileName;
                $maxWidth = 300;
                $maxHeight = 200;
                
                // Calculate new dimensions
                $ratio = $width / $height;
                if ($width > $height) {
                    $newWidth = $maxWidth;
                    $newHeight = $maxWidth / $ratio;
                } else {
                    $newHeight = $maxHeight;
                    $newWidth = $maxHeight * $ratio;
                }
                
                // Create thumbnail
                $thumb = imagecreatetruecolor($newWidth, $newHeight);
                
                if ($fileType == 'image/jpeg') {
                    $source = imagecreatefromjpeg($uploadPath);
                    imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                    imagejpeg($thumb, $thumbnailPath, 80);
                } elseif ($fileType == 'image/png') {
                    $source = imagecreatefrompng($uploadPath);
                    imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                    imagepng($thumb, $thumbnailPath, 8);
                } elseif ($fileType == 'image/gif') {
                    $source = imagecreatefromgif($uploadPath);
                    imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                    imagegif($thumb, $thumbnailPath);
                }
                
                imagedestroy($thumb);
                
                // Save to database
                $stmt = $conn->prepare("INSERT INTO screenshots (user_id, image_path, thumbnail_path, file_size, width, height, capture_method) VALUES (?, ?, ?, ?, ?, ?, 'manual')");
                $fileSize = $_FILES['screenshot']['size'];
                $stmt->bind_param("isssii", $userId, $uploadPath, $thumbnailPath, $fileSize, $width, $height);
                
                if ($stmt->execute()) {
                    // Log activity
                    logActivity($userId, 'screenshot', 'Manual screenshot uploaded');
                    
                    header('Location: screenshots.php?success=1');
                    exit();
                }
            }
        }
    }
}

// If we get here, something went wrong
header('Location: screenshots.php?error=1');
exit();
?>