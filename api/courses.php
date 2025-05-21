<?php
session_start();
require_once '../config/auth.php';
require_once '../config/database.php';

// Set JSON content type
header('Content-Type: application/json');

// Error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Turn off display_errors but keep logging

// Check if user is logged in and is admin
if (!isAdmin()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

// Handle GET request - Fetch all course settings
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $db->query("SELECT * FROM course_settings");
        $settings = [];

        while ($row = $stmt->fetch()) {
            $settings[$row['course_id']] = [
                'status' => $row['status'],
                'thinkificStatus' => $row['thinkific_status'],
                'isLocallyEdited' => (bool) $row['is_locally_edited'],
                'featured' => (bool) $row['featured'],
                'displayOrder' => (int) $row['display_order'],
                'title' => $row['title'],
                'urlSlug' => $row['url_slug'],
                'subtitle' => $row['subtitle'],
                'shortDescription' => $row['short_description'],
                'customDescription' => $row['custom_description'],
                'price' => $row['price'],
                'duration' => $row['duration'],
                'level' => $row['level'],
                'category' => $row['category'],
                'thumbnail' => $row['thumbnail']
            ];
        }

        echo json_encode($settings);
    } catch (PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Database error']);
    }
}

// Handle POST request - Update course settings
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db->beginTransaction();

        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['courseId']) || !isset($data['updates'])) {
            throw new Exception('Invalid request data');
        }

        // Validate URL slug
        if (empty($data['updates']['urlSlug'])) {
            $data['updates']['urlSlug'] = generateUrlSlug($data['updates']['title']);
        }

        // Check if URL is already taken
        $stmt = $db->prepare("
            SELECT course_id FROM course_settings 
            WHERE url_slug = ? AND course_id != ?
        ");
        $stmt->execute([$data['updates']['urlSlug'], $data['courseId']]);
        
        if ($stmt->rowCount() > 0) {
            throw new Exception('URL already in use');
        }

        // Check if course exists
        $stmt = $db->prepare("SELECT course_id FROM course_settings WHERE course_id = ?");
        $stmt->execute([$data['courseId']]);
        
        if ($stmt->rowCount() === 0) {
            // Insert new course
            $stmt = $db->prepare("
                INSERT INTO course_settings 
                (course_id, title, url_slug, subtitle, short_description, price, 
                duration, lessons_count, video_duration, level, category, status, featured, 
                display_order, custom_description, is_locally_edited)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, TRUE)
            ");
            $params = [
                $data['courseId'],
                $data['updates']['title'],
                $data['updates']['urlSlug'],
                $data['updates']['subtitle'] ?? '',
                $data['updates']['shortDescription'] ?? '',
                $data['updates']['price'] ?? 0,
                $data['updates']['duration'] ?? '',
                $data['updates']['lessonsCount'] ?? 0,
                $data['updates']['videoDuration'] ?? '',
                $data['updates']['level'] ?? 'Beginner',
                $data['updates']['category'] ?? '',
                $data['updates']['status'] ?? 'draft',
                $data['updates']['featured'] ? 1 : 0,
                $data['updates']['displayOrder'] ?? 0,
                $data['updates']['customDescription'] ?? ''
            ];
        } else {
            // Update existing course
            $stmt = $db->prepare("
                UPDATE course_settings 
                SET title = ?,
                    url_slug = ?,
                    subtitle = ?,
                    short_description = ?,
                    price = ?,
                    duration = ?,
                    lessons_count = ?,
                    video_duration = ?,
                    level = ?,
                    category = ?,
                    status = ?,
                    featured = ?,
                    display_order = ?,
                    custom_description = ?,
                    is_locally_edited = TRUE
                WHERE course_id = ?
            ");
            $params = [
                $data['updates']['title'],
                $data['updates']['urlSlug'],
                $data['updates']['subtitle'] ?? '',
                $data['updates']['shortDescription'] ?? '',
                $data['updates']['price'] ?? 0,
                $data['updates']['duration'] ?? '',
                $data['updates']['lessonsCount'] ?? 0,
                $data['updates']['videoDuration'] ?? '',
                $data['updates']['level'] ?? 'Beginner',
                $data['updates']['category'] ?? '',
                $data['updates']['status'] ?? 'draft',
                $data['updates']['featured'] ? 1 : 0,
                $data['updates']['displayOrder'] ?? 0,
                $data['updates']['customDescription'] ?? '',
                $data['courseId']
            ];
        }

        $stmt->execute($params);
        $db->commit();

        echo json_encode(['success' => true]);

    } catch (Exception $e) {
        $db->rollBack();
        error_log("Error in courses.php: " . $e->getMessage());
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
    exit();
}

// Add a new endpoint to sync course data
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && isset($_GET['action']) && $_GET['action'] === 'sync') {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        
        foreach ($data['courses'] as $course) {
            // Update thinkific_courses table
            $stmt = $db->prepare("
                INSERT INTO thinkific_courses 
                (course_id, name, description, image_url, preview_url, enrollment_url, lessons_count, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                name = VALUES(name),
                description = VALUES(description),
                image_url = VALUES(image_url),
                preview_url = VALUES(preview_url),
                enrollment_url = VALUES(enrollment_url),
                lessons_count = VALUES(lessons_count),
                status = VALUES(status),
                last_synced = CURRENT_TIMESTAMP
            ");

            $stmt->execute([
                $course['id'],
                $course['name'],
                $course['description'],
                $course['image_url'],
                $course['preview_url'],
                $course['enrollment_url'],
                $course['lessons_count'],
                $course['status']
            ]);

            // Update course_settings if not locally edited
            $stmt = $db->prepare("
                INSERT INTO course_settings 
                (course_id, title, short_description, status, thinkific_status)
                VALUES (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                title = CASE WHEN is_locally_edited = FALSE THEN VALUES(title) ELSE title END,
                short_description = CASE WHEN is_locally_edited = FALSE THEN VALUES(short_description) ELSE short_description END,
                thinkific_status = VALUES(thinkific_status)
            ");

            $stmt->execute([
                $course['id'],
                $course['name'],
                $course['description'],
                $course['status'],
                $course['status']
            ]);
        }

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        error_log("Sync Error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Failed to sync courses']);
    }
}

// Helper function to generate URL slug
function generateUrlSlug($title) {
    $slug = strtolower($title);
    $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    $slug = trim($slug, '-');
    return $slug;
}