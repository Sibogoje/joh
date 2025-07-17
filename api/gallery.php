<?php
// filepath: g:\My Drive\Projects\joh\api\gallery.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Cache-busting headers
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

require_once 'config/database.php';

class GalleryAPI {
    private $conn;
    private $uploadDir = '../admin/uploads/gallery/';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        
        // Create upload directory if it doesn't exist
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    private function validateSession($session_id) {
        $query = "SELECT user_id FROM admin_sessions WHERE id = :session_id AND expires_at > NOW()";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':session_id', $session_id);
        $stmt->execute();
        
        return $stmt->rowCount() == 1 ? $stmt->fetch()['user_id'] : false;
    }

    public function getAllImages($session_id) {
        $user_id = $this->validateSession($session_id);
        if (!$user_id) {
            return ['success' => false, 'message' => 'Unauthorized'];
        }

        try {
            $query = "SELECT g.*, u.full_name as uploaded_by_name 
                     FROM gallery_images g 
                     LEFT JOIN admin_users u ON g.uploaded_by = u.id 
                     ORDER BY g.created_at DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            return ['success' => true, 'images' => $stmt->fetchAll()];
        } catch (Exception $e) {
            error_log("Get images error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to fetch images'];
        }
    }

    public function getPublicImages($category = null) {
        try {
            $query = "SELECT id, title, description, file_path, category, alt_text, created_at 
                     FROM gallery_images";
            
            if ($category) {
                $query .= " WHERE category = :category";
            }
            
            $query .= " ORDER BY created_at DESC";
            
            $stmt = $this->conn->prepare($query);
            if ($category) {
                $stmt->bindValue(':category', $category);
            }
            $stmt->execute();

            return ['success' => true, 'images' => $stmt->fetchAll()];
        } catch (Exception $e) {
            error_log("Get public images error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to fetch images'];
        }
    }

    public function uploadImage($data, $file, $session_id) {
        $user_id = $this->validateSession($session_id);
        if (!$user_id) {
            return ['success' => false, 'message' => 'Unauthorized'];
        }

        try {
            // Validate file
            if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
                return ['success' => false, 'message' => 'No file uploaded'];
            }

            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($file['type'], $allowedTypes)) {
                return ['success' => false, 'message' => 'Invalid file type'];
            }

            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '_' . time() . '.' . $extension;
            $filepath = $this->uploadDir . $filename;

            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                return ['success' => false, 'message' => 'Failed to save file'];
            }

            // Get image dimensions
            $imageInfo = getimagesize($filepath);
            $width = $imageInfo ? $imageInfo[0] : null;
            $height = $imageInfo ? $imageInfo[1] : null;

            // Save to database
            $query = "INSERT INTO gallery_images 
                     (title, description, filename, original_filename, file_path, file_size, mime_type, width, height, category, uploaded_by) 
                     VALUES (:title, :description, :filename, :original_filename, :file_path, :file_size, :mime_type, :width, :height, :category, :uploaded_by)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':title', $data['title']);
            $stmt->bindValue(':description', $data['description'] ?? '');
            $stmt->bindValue(':filename', $filename);
            $stmt->bindValue(':original_filename', $file['name']);
            $stmt->bindValue(':file_path', 'admin/uploads/gallery/' . $filename);
            $stmt->bindValue(':file_size', $file['size']);
            $stmt->bindValue(':mime_type', $file['type']);
            $stmt->bindValue(':width', $width);
            $stmt->bindValue(':height', $height);
            $stmt->bindValue(':category', $data['category']);
            $stmt->bindValue(':uploaded_by', $user_id);
            
            $stmt->execute();

            return ['success' => true, 'image_id' => $this->conn->lastInsertId()];
        } catch (Exception $e) {
            error_log("Upload image error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to upload image'];
        }
    }

    public function updateImage($id, $data, $session_id) {
        $user_id = $this->validateSession($session_id);
        if (!$user_id) {
            return ['success' => false, 'message' => 'Unauthorized'];
        }

        try {
            $query = "UPDATE gallery_images SET 
                     title = :title, description = :description, category = :category, updated_at = NOW()
                     WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $id);
            $stmt->bindValue(':title', $data['title']);
            $stmt->bindValue(':description', $data['description']);
            $stmt->bindValue(':category', $data['category']);
            
            $stmt->execute();

            return ['success' => true, 'message' => 'Image updated successfully'];
        } catch (Exception $e) {
            error_log("Update image error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to update image'];
        }
    }

    public function deleteImage($id, $session_id) {
        $user_id = $this->validateSession($session_id);
        if (!$user_id) {
            return ['success' => false, 'message' => 'Unauthorized'];
        }

        try {
            // Get image details before deletion
            $query = "SELECT filename FROM gallery_images WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            
            if ($stmt->rowCount() == 0) {
                return ['success' => false, 'message' => 'Image not found'];
            }
            
            $image = $stmt->fetch();
            
            // Delete from database
            $deleteQuery = "DELETE FROM gallery_images WHERE id = :id";
            $deleteStmt = $this->conn->prepare($deleteQuery);
            $deleteStmt->bindValue(':id', $id);
            $deleteStmt->execute();
            
            // Delete file
            $filepath = $this->uploadDir . $image['filename'];
            if (file_exists($filepath)) {
                unlink($filepath);
            }

            return ['success' => true, 'message' => 'Image deleted successfully'];
        } catch (Exception $e) {
            error_log("Delete image error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to delete image'];
        }
    }
}

// Handle requests
$gallery = new GalleryAPI();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['session_id'])) {
            echo json_encode($gallery->getAllImages($_GET['session_id']));
        } else {
            $category = $_GET['category'] ?? null;
            echo json_encode($gallery->getPublicImages($category));
        }
        break;
    
    case 'POST':
        $data = $_POST;
        $file = $_FILES['image'] ?? null;
        echo json_encode($gallery->uploadImage($data, $file, $data['session_id']));
        break;
    
    case 'PUT':
        $input = json_decode(file_get_contents('php://input'), true);
        echo json_encode($gallery->updateImage($input['id'], $input, $input['session_id']));
        break;
    
    case 'DELETE':
        $input = json_decode(file_get_contents('php://input'), true);
        echo json_encode($gallery->deleteImage($input['id'], $input['session_id']));
        break;
    
    default:
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>