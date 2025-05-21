<?php
require_once '../config/database.php';

header('Content-Type: application/json');

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get filter parameters
    $category = $_GET['category'] ?? null;
    $search = $_GET['search'] ?? null;
    
    // Build the query
    $query = "
        SELECT DISTINCT
            cs.*,
            tc.image_url,
            tc.preview_url,
            tc.enrollment_url,
            tc.lessons_count as thinkific_lessons_count,
            GROUP_CONCAT(DISTINCT cc.name) as categories,
            ci.name as instructor_name,
            ci.title as instructor_title
        FROM course_settings cs
        JOIN thinkific_courses tc ON cs.course_id = tc.course_id
        LEFT JOIN course_category_relations ccr ON cs.course_id = ccr.course_id
        LEFT JOIN course_categories cc ON ccr.category_id = cc.category_id
        LEFT JOIN course_instructor_relations cir ON cs.course_id = cir.course_id
        LEFT JOIN course_instructors ci ON cir.instructor_id = ci.instructor_id
        WHERE cs.status = 'published'
    ";
    
    // Add category filter
    if ($category && $category !== 'All') {
        $query .= " AND cc.name = :category";
    }
    
    // Add search filter
    if ($search) {
        $query .= " AND (
            cs.title LIKE :search 
            OR cs.description LIKE :search 
            OR cs.custom_description LIKE :search
        )";
    }
    
    $query .= " GROUP BY cs.course_id ORDER BY cs.display_order ASC, cs.title ASC";
    
    $stmt = $db->prepare($query);
    
    // Bind parameters
    if ($category && $category !== 'All') {
        $stmt->bindParam(':category', $category);
    }
    if ($search) {
        $searchTerm = "%$search%";
        $stmt->bindParam(':search', $searchTerm);
    }
    
    $stmt->execute();
    
    $courses = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $courses[] = [
            'id' => $row['course_id'],
            'name' => $row['title'],
            'description' => $row['custom_description'] ?: $row['short_description'],
            'image_url' => $row['image_url'],
            'preview_url' => $row['preview_url'],
            'url' => 'https://courses.wissenwelle.com/products/courses/' . $row['url_slug'],
            'duration' => $row['duration'],
            'video_duration' => $row['video_duration'],
            'lessons_count' => (int) ($row['lessons_count'] ?: $row['thinkific_lessons_count']),
            'featured' => (bool) $row['featured'],
            'categories' => $row['categories'] ? explode(',', $row['categories']) : []
        ];
    }
    
    echo json_encode(['success' => true, 'courses' => $courses]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to fetch courses']);
} 