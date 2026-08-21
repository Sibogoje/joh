<?php
// filepath: c:\Users\siboniso.sibandze\Downloads\johh\api\reading_materials.php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Cache-busting headers
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

require_once 'config/database.php';

class ReadingMaterialsAPI {
    private $conn;
    private $uploadDir = '../admin/uploads/materials/';

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

    public function getAllMaterials($session_id) {
        $user_id = $this->validateSession($session_id);
        if (!$user_id) {
            return ['success' => false, 'message' => 'Unauthorized'];
        }

        try {
            $query = "SELECT r.*, u.full_name as author_name 
                     FROM reading_materials r 
                     LEFT JOIN admin_users u ON r.author_id = u.id 
                     ORDER BY r.created_at DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            return ['success' => true, 'materials' => $stmt->fetchAll()];
        } catch (Exception $e) {
            error_log("Get reading materials error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to fetch reading materials'];
        }
    }

    public function getMaterial($id, $session_id = null) {
        // Public endpoint for published materials, requires session for drafts
        try {
            $query = "SELECT r.*, u.full_name as author_name 
                     FROM reading_materials r 
                     LEFT JOIN admin_users u ON r.author_id = u.id 
                     WHERE r.id = :id";
            
            if (!$session_id || !$this->validateSession($session_id)) {
                $query .= " AND r.status = 'published'";
            }

            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $id);
            $stmt->execute();

            if ($stmt->rowCount() == 1) {
                return ['success' => true, 'material' => $stmt->fetch()];
            }
            
            return ['success' => false, 'message' => 'Reading material not found'];
        } catch (Exception $e) {
            error_log("Get reading material error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to fetch reading material'];
        }
    }

    public function createMaterial($data, $session_id) {
        $user_id = $this->validateSession($session_id);
        if (!$user_id) {
            return ['success' => false, 'message' => 'Unauthorized'];
        }

        try {
            $slug = $this->generateSlug($data['title']);
            
            $query = "INSERT INTO reading_materials (title, slug, content, excerpt, category, status, file_name, file_path, author_id, published_at, meta_title, meta_description) 
                     VALUES (:title, :slug, :content, :excerpt, :category, :status, :file_name, :file_path, :author_id, :published_at, :meta_title, :meta_description)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':title', $data['title']);
            $stmt->bindValue(':slug', $slug);
            $stmt->bindValue(':content', $data['content']);
            $stmt->bindValue(':excerpt', $data['excerpt']);
            $stmt->bindValue(':category', $data['category']);
            $stmt->bindValue(':status', $data['status']);
            $stmt->bindValue(':file_name', $data['file_name'] ?? null);
            $stmt->bindValue(':file_path', $data['file_path'] ?? null);
            $stmt->bindValue(':author_id', $user_id);
            
            $published_at = $data['status'] === 'published' ? 
                ($data['published_at'] ?? date('Y-m-d H:i:s')) : null;
            $stmt->bindValue(':published_at', $published_at);
            
            $stmt->bindValue(':meta_title', $data['meta_title'] ?? $data['title']);
            $stmt->bindValue(':meta_description', $data['meta_description'] ?? $data['excerpt']);
            
            $stmt->execute();

            return ['success' => true, 'material_id' => $this->conn->lastInsertId()];
        } catch (Exception $e) {
            error_log("Create reading material error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to create reading material'];
        }
    }

    public function updateMaterial($id, $data, $session_id) {
        $user_id = $this->validateSession($session_id);
        if (!$user_id) {
            return ['success' => false, 'message' => 'Unauthorized'];
        }

        try {
            $slug = $this->generateSlug($data['title'], $id);
            
            $query = "UPDATE reading_materials SET 
                     title = :title, slug = :slug, content = :content, excerpt = :excerpt, 
                     category = :category, status = :status, file_name = :file_name, file_path = :file_path,
                     published_at = :published_at,
                     meta_title = :meta_title, meta_description = :meta_description,
                     updated_at = NOW()
                     WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $id);
            $stmt->bindValue(':title', $data['title']);
            $stmt->bindValue(':slug', $slug);
            $stmt->bindValue(':content', $data['content']);
            $stmt->bindValue(':excerpt', $data['excerpt']);
            $stmt->bindValue(':category', $data['category']);
            $stmt->bindValue(':status', $data['status']);
            $stmt->bindValue(':file_name', $data['file_name'] ?? null);
            $stmt->bindValue(':file_path', $data['file_path'] ?? null);
            
            $published_at = $data['status'] === 'published' ? 
                ($data['published_at'] ?? date('Y-m-d H:i:s')) : null;
            $stmt->bindValue(':published_at', $published_at);
            
            $stmt->bindValue(':meta_title', $data['meta_title'] ?? $data['title']);
            $stmt->bindValue(':meta_description', $data['meta_description'] ?? $data['excerpt']);
            
            $stmt->execute();

            return ['success' => true, 'message' => 'Reading material updated successfully'];
        } catch (Exception $e) {
            error_log("Update reading material error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to update reading material'];
        }
    }

    public function deleteMaterial($id, $session_id) {
        $user_id = $this->validateSession($session_id);
        if (!$user_id) {
            return ['success' => false, 'message' => 'Unauthorized'];
        }

        try {
            // Get material file path before deletion
            $query = "SELECT file_path FROM reading_materials WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            
            if ($stmt->rowCount() == 0) {
                return ['success' => false, 'message' => 'Reading material not found'];
            }
            
            $material = $stmt->fetch();
            
            // Delete from database
            $deleteQuery = "DELETE FROM reading_materials WHERE id = :id";
            $deleteStmt = $this->conn->prepare($deleteQuery);
            $deleteStmt->bindValue(':id', $id);
            $deleteStmt->execute();
            
            // Delete the file if it exists
            if (!empty($material['file_path'])) {
                $filepath = dirname(__DIR__) . '/' . ltrim($material['file_path'], './');
                // Fallback: try the stored path relative to admin dir
                if (!file_exists($filepath)) {
                    $filepath = __DIR__ . '/..' . $material['file_path'];
                }
                if (file_exists($filepath)) {
                    unlink($filepath);
                }
            }

            return ['success' => true, 'message' => 'Reading material deleted successfully'];
        } catch (Exception $e) {
            error_log("Delete reading material error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to delete reading material'];
        }
    }

    public function uploadDocument($data, $file, $session_id) {
        $user_id = $this->validateSession($session_id);
        if (!$user_id) {
            return ['success' => false, 'message' => 'Unauthorized'];
        }

        try {
            // Validate file
            if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
                return ['success' => false, 'message' => 'No file uploaded'];
            }

            // Allowed document types
            $allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'csv', 'rtf', 'odt'];
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if (!in_array($extension, $allowedExtensions)) {
                return ['success' => false, 'message' => 'Invalid file type. Allowed: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT, CSV, RTF, ODT'];
            }

            // Check file size (max 25MB)
            $maxSize = 25 * 1024 * 1024;
            if ($file['size'] > $maxSize) {
                return ['success' => false, 'message' => 'File too large. Maximum size is 25MB'];
            }

            // Generate unique filename
            $filename = uniqid() . '_' . time() . '.' . $extension;
            $filepath = $this->uploadDir . $filename;

            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                return ['success' => false, 'message' => 'Failed to save file'];
            }

            // Update material with file info
            $materialId = $data['material_id'] ?? null;
            $updateQuery = "UPDATE reading_materials SET 
                           file_name = :file_name, file_path = :file_path, updated_at = NOW() 
                           WHERE id = :id";
            $updateStmt = $this->conn->prepare($updateQuery);
            $updateStmt->bindValue(':file_name', $file['name']);
            $updateStmt->bindValue(':file_path', '../admin/uploads/materials/' . $filename);
            $updateStmt->bindValue(':id', $materialId);
            $updateStmt->execute();

            return ['success' => true, 'message' => 'Document uploaded successfully', 'file_path' => '../admin/uploads/materials/' . $filename];
        } catch (Exception $e) {
            error_log("Upload document error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to upload document'];
        }
    }

    private function generateSlug($title, $exclude_id = null) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        $original_slug = $slug;
        $counter = 1;

        while ($this->slugExists($slug, $exclude_id)) {
            $slug = $original_slug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function slugExists($slug, $exclude_id = null) {
        $query = "SELECT id FROM reading_materials WHERE slug = :slug";
        if ($exclude_id) {
            $query .= " AND id != :exclude_id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':slug', $slug);
        if ($exclude_id) {
            $stmt->bindValue(':exclude_id', $exclude_id);
        }
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function getPublicMaterials() {
        try {
            $query = "SELECT r.*, u.full_name as author_name 
                     FROM reading_materials r 
                     LEFT JOIN admin_users u ON r.author_id = u.id 
                     WHERE r.status = 'published' AND r.published_at <= NOW()
                     ORDER BY r.published_at DESC, r.created_at DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            return ['success' => true, 'materials' => $stmt->fetchAll()];
        } catch (Exception $e) {
            error_log("Get public reading materials error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to fetch reading materials'];
        }
    }
}

// Handle requests
$materials = new ReadingMaterialsAPI();
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            echo json_encode($materials->getMaterial($_GET['id'], $_GET['session_id'] ?? null));
        } else {
            // Check if session_id is provided for admin access
            if (isset($_GET['session_id'])) {
                echo json_encode($materials->getAllMaterials($_GET['session_id']));
            } else {
                // Public access - only published materials
                echo json_encode($materials->getPublicMaterials());
            }
        }
        break;
    
    case 'POST':
        // Check if this is a file upload request (multipart form data)
        if (isset($_FILES['document'])) {
            $data = $_POST;
            $file = $_FILES['document'];
            echo json_encode($materials->uploadDocument($data, $file, $data['session_id']));
        } else {
            echo json_encode($materials->createMaterial($input, $input['session_id']));
        }
        break;
    
    case 'PUT':
        echo json_encode($materials->updateMaterial($input['id'], $input, $input['session_id']));
        break;
    
    case 'DELETE':
        echo json_encode($materials->deleteMaterial($input['id'], $input['session_id']));
        break;
    
    default:
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
