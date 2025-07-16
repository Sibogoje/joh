<?php
// filepath: g:\My Drive\Projects\joh\api\posts.php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once 'config/database.php';

class PostsAPI {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    private function validateSession($session_id) {
        $query = "SELECT user_id FROM admin_sessions WHERE id = :session_id AND expires_at > NOW()";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':session_id', $session_id);
        $stmt->execute();
        
        return $stmt->rowCount() == 1 ? $stmt->fetch()['user_id'] : false;
    }

    public function getAllPosts($session_id) {
        $user_id = $this->validateSession($session_id);
        if (!$user_id) {
            return ['success' => false, 'message' => 'Unauthorized'];
        }

        try {
            $query = "SELECT p.*, u.full_name as author_name 
                     FROM posts p 
                     LEFT JOIN admin_users u ON p.author_id = u.id 
                     ORDER BY p.created_at DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            return ['success' => true, 'posts' => $stmt->fetchAll()];
        } catch (Exception $e) {
            error_log("Get posts error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to fetch posts'];
        }
    }

    public function getPost($id, $session_id = null) {
        // Public endpoint for published posts, requires session for drafts
        try {
            $query = "SELECT p.*, u.full_name as author_name 
                     FROM posts p 
                     LEFT JOIN admin_users u ON p.author_id = u.id 
                     WHERE p.id = :id";
            
            if (!$session_id || !$this->validateSession($session_id)) {
                $query .= " AND p.status = 'published'";
            }

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            if ($stmt->rowCount() == 1) {
                return ['success' => true, 'post' => $stmt->fetch()];
            }
            
            return ['success' => false, 'message' => 'Post not found'];
        } catch (Exception $e) {
            error_log("Get post error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to fetch post'];
        }
    }

    public function createPost($data, $session_id) {
        $user_id = $this->validateSession($session_id);
        if (!$user_id) {
            return ['success' => false, 'message' => 'Unauthorized'];
        }

        try {
            $slug = $this->generateSlug($data['title']);
            
            $query = "INSERT INTO posts (title, slug, content, excerpt, category, status, author_id, published_at, meta_title, meta_description) 
                     VALUES (:title, :slug, :content, :excerpt, :category, :status, :author_id, :published_at, :meta_title, :meta_description)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':title', $data['title']);
            $stmt->bindParam(':slug', $slug);
            $stmt->bindParam(':content', $data['content']);
            $stmt->bindParam(':excerpt', $data['excerpt']);
            $stmt->bindParam(':category', $data['category']);
            $stmt->bindParam(':status', $data['status']);
            $stmt->bindParam(':author_id', $user_id);
            
            $published_at = $data['status'] === 'published' ? 
                ($data['published_at'] ?? date('Y-m-d H:i:s')) : null;
            $stmt->bindParam(':published_at', $published_at);
            
            $stmt->bindParam(':meta_title', $data['meta_title'] ?? $data['title']);
            $stmt->bindParam(':meta_description', $data['meta_description'] ?? $data['excerpt']);
            
            $stmt->execute();

            return ['success' => true, 'post_id' => $this->conn->lastInsertId()];
        } catch (Exception $e) {
            error_log("Create post error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to create post'];
        }
    }

    public function updatePost($id, $data, $session_id) {
        $user_id = $this->validateSession($session_id);
        if (!$user_id) {
            return ['success' => false, 'message' => 'Unauthorized'];
        }

        try {
            $slug = $this->generateSlug($data['title'], $id);
            
            $query = "UPDATE posts SET 
                     title = :title, slug = :slug, content = :content, excerpt = :excerpt, 
                     category = :category, status = :status, published_at = :published_at,
                     meta_title = :meta_title, meta_description = :meta_description,
                     updated_at = NOW()
                     WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':title', $data['title']);
            $stmt->bindParam(':slug', $slug);
            $stmt->bindParam(':content', $data['content']);
            $stmt->bindParam(':excerpt', $data['excerpt']);
            $stmt->bindParam(':category', $data['category']);
            $stmt->bindParam(':status', $data['status']);
            
            $published_at = $data['status'] === 'published' ? 
                ($data['published_at'] ?? date('Y-m-d H:i:s')) : null;
            $stmt->bindParam(':published_at', $published_at);
            
            $stmt->bindParam(':meta_title', $data['meta_title'] ?? $data['title']);
            $stmt->bindParam(':meta_description', $data['meta_description'] ?? $data['excerpt']);
            
            $stmt->execute();

            return ['success' => true, 'message' => 'Post updated successfully'];
        } catch (Exception $e) {
            error_log("Update post error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to update post'];
        }
    }

    public function deletePost($id, $session_id) {
        $user_id = $this->validateSession($session_id);
        if (!$user_id) {
            return ['success' => false, 'message' => 'Unauthorized'];
        }

        try {
            $query = "DELETE FROM posts WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            return ['success' => true, 'message' => 'Post deleted successfully'];
        } catch (Exception $e) {
            error_log("Delete post error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to delete post'];
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
        $query = "SELECT id FROM posts WHERE slug = :slug";
        if ($exclude_id) {
            $query .= " AND id != :exclude_id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':slug', $slug);
        if ($exclude_id) {
            $stmt->bindParam(':exclude_id', $exclude_id);
        }
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}

// Handle requests
$posts = new PostsAPI();
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            echo json_encode($posts->getPost($_GET['id'], $_GET['session_id'] ?? null));
        } else {
            echo json_encode($posts->getAllPosts($_GET['session_id']));
        }
        break;
    
    case 'POST':
        echo json_encode($posts->createPost($input, $input['session_id']));
        break;
    
    case 'PUT':
        echo json_encode($posts->updatePost($input['id'], $input, $input['session_id']));
        break;
    
    case 'DELETE':
        echo json_encode($posts->deletePost($input['id'], $input['session_id']));
        break;
    
    default:
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>