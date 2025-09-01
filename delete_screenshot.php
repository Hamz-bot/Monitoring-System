<?php
require_once 'config.php';
require_once 'auth.php';
requireAuth();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $screenshot_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    
    if ($screenshot_id) {
        // Get the screenshot path and user_id before deleting
        $stmt = $conn->prepare("SELECT image_path, thumbnail_path, user_id FROM screenshots WHERE id = ?");
        $stmt->bind_param("i", $screenshot_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $screenshot = $result->fetch_assoc();
            $imagePath = $screenshot['image_path'];
            $thumbnailPath = $screenshot['thumbnail_path'];
            $user_id = $screenshot['user_id'];
            
            // Delete from database
            $stmt = $conn->prepare("DELETE FROM screenshots WHERE id = ?");
            $stmt->bind_param("i", $screenshot_id);
            
            if ($stmt->execute()) {
                // Delete files
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
                
                if (file_exists($thumbnailPath)) {
                    unlink($thumbnailPath);
                }
                
                // Log activity
                logActivity($user_id, 'screenshot', 'Deleted screenshot');
                
                header('Location: screenshots.php?success=2');
                exit();
            }
        }
    }
}

// If we get here, something went wrong
header('Location: screenshots.php?error=2');
exit();
?>