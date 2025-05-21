<?php
require_once '../config/database.php';

// Set JSON content type
header('Content-Type: application/json');

// Error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

try {
    // Query to get published courses with their details
    $query = "
        SELECT 
            cs.course_id,
            cs.title as name,
            cs.short_description as description,
            cs.price,
            cs.duration,
            cs.category,
            cs.featured,
            cs.lessons_count,
            cs.video_duration,
            tc.image_url,
            tc.preview_url,
            tc.enrollment_url as url,
            i.name as instructor_name,
            i.title as instructor_title
        FROM course_settings cs
        LEFT JOIN thinkific_courses tc ON cs.course_id = tc.course_id
        LEFT JOIN course_instructor_relations cir ON cs.course_id = cir.course_id
        LEFT JOIN course_instructors i ON cir.instructor_id = i.instructor_id
        WHERE cs.status = 'published'
        ORDER BY cs.display_order DESC, cs.title ASC
    ";

    $stmt = $db->query($query);
    $courses = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Format instructor data if it exists
        $instructor = null;
        if ($row['instructor_name']) {
            $instructor = [
                'name' => $row['instructor_name'],
                'title' => $row['instructor_title']
            ];
        }

        // Build course object
        $courses[] = [
            'id' => $row['course_id'],
            'name' => $row['name'],
            'description' => $row['description'],
            'price' => (float)$row['price'],
            'duration' => $row['duration'],
            'category' => $row['category'],
            'featured' => (bool)$row['featured'],
            'lessons_count' => (int)$row['lessons_count'],
            'video_duration' => $row['video_duration'],
            'image_url' => $row['image_url'],
            'preview_url' => $row['preview_url'],
            'url' => $row['url'],
            'instructor' => $instructor
        ];
    }

    echo json_encode(['courses' => $courses]);

} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
} 