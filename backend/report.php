<?php
// config.php
class Database
{
    private $host = 'localhost';
    private $db_name = 'field_reporter';
    private $username = 'root';
    private $password = 'Gayu251005777';
    private $conn;

    public function getConnection()
    {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8mb4");
        } catch (PDOException $e) {
            echo "Connection error: " . $e->getMessage();
        }

        return $this->conn;
    }
}

// submit_report.php
header('Content-Type: application/json');

try {
    // Initialize database connection
    require_once 'config.php';
    $database = new Database();
    $db = $database->getConnection();

    // Start transaction
    $db->beginTransaction();

    try {
        // Validate request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new Exception('Invalid request method');
        }

        // Validate required fields
        $required_fields = ['who', 'what', 'where', 'when', 'why', 'how'];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("Field '$field' is required");
            }
        }

        // Insert report
        $query = "INSERT INTO reports (who, what, `where`, `when`, why, how, additional_details) 
                  VALUES (:who, :what, :where, :when, :why, :how, :details)";

        $stmt = $db->prepare($query);
        $stmt->execute([
            ':who' => $_POST['who'],
            ':what' => $_POST['what'],
            ':where' => $_POST['where'],
            ':when' => $_POST['when'],
            ':why' => $_POST['why'],
            ':how' => $_POST['how'],
            ':details' => $_POST['details'] ?? null
        ]);

        $report_id = $db->lastInsertId();

        // Handle photo uploads
        $upload_dir = 'uploads/' . date('Y/m/');
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Process photos
        if (!empty($_FILES['photos'])) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 5 * 1024 * 1024; // 5MB

            foreach ($_FILES['photos']['tmp_name'] as $key => $tmp_name) {
                $file_type = $_FILES['photos']['type'][$key];
                $file_size = $_FILES['photos']['size'][$key];
                $file_name = $_FILES['photos']['name'][$key];

                // Validate file type
                if (!in_array($file_type, $allowed_types)) {
                    throw new Exception("Invalid file type: $file_name");
                }

                // Validate file size
                if ($file_size > $max_size) {
                    throw new Exception("File too large: $file_name");
                }

                // Generate unique filename
                $extension = pathinfo($file_name, PATHINFO_EXTENSION);
                $unique_filename = uniqid() . '_' . time() . '.' . $extension;
                $file_path = $upload_dir . $unique_filename;

                // Move uploaded file
                if (move_uploaded_file($tmp_name, $file_path)) {
                    // Save file info to database
                    $query = "INSERT INTO report_photos (report_id, file_name, file_path, file_size, mime_type) 
                             VALUES (:report_id, :file_name, :file_path, :file_size, :mime_type)";

                    $stmt = $db->prepare($query);
                    $stmt->execute([
                        ':report_id' => $report_id,
                        ':file_name' => $file_name,
                        ':file_path' => $file_path,
                        ':file_size' => $file_size,
                        ':mime_type' => $file_type
                    ]);
                } else {
                    throw new Exception("Failed to upload file: $file_name");
                }
            }
        }

        // Log the creation
        $query = "INSERT INTO report_audit_logs (report_id, action, new_status) 
                  VALUES (:report_id, 'create', 'pending')";

        $stmt = $db->prepare($query);
        $stmt->execute([
            ':report_id' => $report_id
        ]);

        // Commit transaction
        $db->commit();

        // Return success response
        http_response_code(200);
        echo json_encode([
            'status' => 'success',
            'message' => 'Report submitted successfully',
            'report_id' => $report_id
        ]);
    } catch (Exception $e) {
        // Rollback transaction on error
        $db->rollBack();
        throw $e;
    }
} catch (Exception $e) {
    // Return error response
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

// get_report.php
if (isset($_GET['id'])) {
    try {
        require_once 'config.php';
        $database = new Database();
        $db = $database->getConnection();

        // Get report details
        $query = "SELECT r.*, 
                    GROUP_CONCAT(rp.file_path) as photo_paths
                 FROM reports r
                 LEFT JOIN report_photos rp ON r.id = rp.report_id
                 WHERE r.id = :id
                 GROUP BY r.id";

        $stmt = $db->prepare($query);
        $stmt->execute([':id' => $_GET['id']]);
        $report = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($report) {
            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'data' => $report
            ]);
        } else {
            throw new Exception('Report not found');
        }
    } catch (Exception $e) {
        http_response_code(404);
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}
