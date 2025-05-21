<?php
require_once 'config/database.php';

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Fetch featured courses
try {
    $stmt = $db->prepare("
        SELECT 
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
        WHERE cs.featured = TRUE AND cs.status = 'published'
        GROUP BY cs.course_id
        ORDER BY cs.display_order ASC, cs.title ASC
        LIMIT 4
    ");

    $stmt->execute();

    $featuredCourses = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $featuredCourses[] = [
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
            'categories' => $row['categories'] ? explode(',', $row['categories']) : [],
            'instructor' => $row['instructor_name'] ? [
                'name' => $row['instructor_name'],
                'title' => $row['instructor_title']
            ] : null
        ];
    }

} catch (PDOException $e) {
    error_log("Error fetching featured courses: " . $e->getMessage());
    $featuredCourses = []; // Empty array if query fails
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WissenWelle - Learn Anytime, Anywhere</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <nav>
        <div class="logo">
            <a href="/">
                <img src="assets/images/logo.png" alt="WissenWelle" />
            </a>
        </div>

        <button class="mobile-menu-btn" aria-label="Toggle menu">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <div class="nav-links">
            <a href="#">Home</a>
            <a href="about.html">About</a>
            <a href="academy.php">Academy</a>
            <a href="#">Blog</a>
            <a href="https://courses.wissenwelle.com" class="login-btn">Login</a>
        </div>
    </nav>

    <section class="hero">
        <!-- Video Background -->
        <div class="video-container">
            <video autoplay loop muted playsinline>
                <source src="assets/videos/hero.mp4" type="video/mp4">
            </video>
            <div class="overlay"></div>
        </div>

        <!-- Content -->
        <div class="hero-content">
            <h1>Learn Anytime, Anywhere</h1>
            <p>Explore expert-led courses tailored to elevate your career and personal growth.</p>

            <div class="search-container">
                <input type="text" class="search-bar" placeholder="Search our courses...">
                <svg class="search-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            <div class="categories">
                <button class="category-tag">Professional</button>
                <button class="category-tag">Position</button>
                <button class="category-tag">Skill</button>
                <button class="category-tag">Patient Education</button>
                <button class="category-tag">AI</button>
                <button class="category-tag">Software</button>
                <button class="category-tag">Exam Prep</button>
                <button class="category-tag">Technology</button>
            </div>

            <div class="cta-buttons">
                <a href="#" class="cta-btn learn-btn">Learn</a>
                <a href="#" class="cta-btn teach-btn">Teach</a>
            </div>
        </div>
    </section>

    <!-- Add this section after your hero section -->
    <section class="partners">
        <div class="container">
            <div class="partners-header">
                <h2>Our Leading Partners</h2>
            </div>

            <div class="partners-grid">
                <div class="partner-item">
                    <a href="https://snapaisle.com" target="_blank" rel="noopener noreferrer">
                        <img src="assets/images/partners/snap-aisle.png" alt="Snap Aisle">
                    </a>
                </div>
                <div class="partner-item">
                    <a href="https://sayajackson.com" target="_blank" rel="noopener noreferrer">
                        <img src="assets/images/partners/saya-jackson.png" alt="Saya Jackson">
                    </a>
                </div>
                <div class="partner-item">
                    <a href="https://bongea.org" target="_blank" rel="noopener noreferrer">
                        <img src="assets/images/partners/bongea.png" alt="BonGea Health Foundation">
                    </a>
                </div>
                <div class="partner-item">
                    <a href="https://dhabiti.com" target="_blank" rel="noopener noreferrer">
                        <img src="assets/images/partners/dhabiti.png" alt="Dhabiti">
                    </a>
                </div>
                <div class="partner-item">
                    <a href="https://andrewsinnovation.com" target="_blank" rel="noopener noreferrer">
                        <img src="assets/images/partners/andrews-innovation.png" alt="Andrews Innovation">
                    </a>
                </div>
                <div class="partner-item">
                    <a href="https://opendoor.com" target="_blank" rel="noopener noreferrer">
                        <img src="assets/images/partners/open-door.png" alt="Open Door">
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="how-it-works">
        <div class="container">
            <div class="section-header">
                <h2>How It Works</h2>
                <p>Your journey to success made simple and straightforward</p>
            </div>

            <div class="steps-grid">
                <div class="step-card">
                    <div class="step-number">
                        <span>01</span>
                    </div>
                    <div class="step-content">
                        <h3>Choose Your Course</h3>
                        <p>Browse through our 87+ professional courses designed to enhance your career prospects.</p>
                    </div>
                    <div class="connector-line"></div>
                </div>

                <div class="step-card">
                    <div class="step-number">
                        <span>02</span>
                    </div>
                    <div class="step-content">
                        <h3>Learn at Your Pace</h3>
                        <p>Access course materials 24/7, study when it suits you, and progress at your own speed.</p>
                    </div>
                    <div class="connector-line"></div>
                </div>

                <div class="step-card">
                    <div class="step-number">
                        <span>03</span>
                    </div>
                    <div class="step-content">
                        <h3>Practice & Apply</h3>
                        <p>Reinforce your learning through practical exercises and real-world projects.</p>
                    </div>
                    <div class="connector-line"></div>
                </div>

                <div class="step-card">
                    <div class="step-number">
                        <span>04</span>
                    </div>
                    <div class="step-content">
                        <h3>Get Certified</h3>
                        <p>Earn industry-recognized certifications to showcase your expertise to employers.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="benefits">
        <div class="container">
            <div class="section-header">
                <h2>Why learn with our courses?</h2>
            </div>

            <div class="benefits-grid">
                <div class="benefit-card">
                    <div class="benefit-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <h3>Expert Instructors</h3>
                    <p>Learn directly from industry leaders and professionals with real-world experience.</p>
                </div>

                <div class="benefit-card">
                    <div class="benefit-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                    </div>
                    <h3>Practical Skills</h3>
                    <p>Gain hands-on knowledge that you can apply immediately in your career or daily life.</p>
                </div>

                <div class="benefit-card">
                    <div class="benefit-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3>Flexible Learning</h3>
                    <p>Access courses anytime, anywhere, and fit learning into your busy schedule.</p>
                </div>

                <div class="benefit-card">
                    <div class="benefit-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3>High-Quality Content</h3>
                    <p>Engaging video lectures, interactive exercises, and downloadable resources.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="popular-courses">
        <div class="container">
            <div class="section-header">
                <h2>Most Popular Courses</h2>
                <p>Join our community of learners and advance your skills</p>
            </div>

            <div class="courses-track">
                <?php foreach ($featuredCourses as $course): ?>
                    <div class="course-card">
                        <div class="course-image">
                            <img src="<?php echo htmlspecialchars($course['image_url']); ?>"
                                alt="<?php echo htmlspecialchars($course['name']); ?>">
                            <?php if ($course['featured']): ?>
                                <span class="featured-badge">Featured</span>
                            <?php endif; ?>
                        </div>
                        <div class="course-content">
                            <h3 class="course-title"><?php echo htmlspecialchars($course['name']); ?></h3>
                            <p class="course-description"><?php echo htmlspecialchars($course['description']); ?></p>
                            <div class="course-footer">
                                <div class="stats">
                                    <?php if (!empty($course['video_duration'])): ?>
                                        <span><?php echo htmlspecialchars($course['video_duration']); ?></span>
                                        <span>•</span>
                                    <?php endif; ?>
                                    <span><?php echo htmlspecialchars($course['duration'] ?? '12 weeks'); ?></span>
                                    <span>•</span>
                                    <span><?php echo htmlspecialchars($course['lessons_count'] ?? 0); ?> lessons</span>
                                </div>
                                <div class="course-actions">
                                    <a href="<?php echo htmlspecialchars($course['url']); ?>" class="enroll-btn">Enroll
                                        Now</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="courses-cta">
                <a href="academy.php" class="browse-courses-btn">Browse All Courses</a>
                <p class="courses-count">87+ more courses available</p>
            </div>
        </div>
    </section>

    <section class="testimonials">
        <div class="container">
            <div class="section-header">
                <h2>What Our Students Say</h2>
                <p>Hear from our community of learners</p>
            </div>

            <div class="testimonials-slider">
                <div class="testimonials-track">
                    <!-- Elias Testimonial -->
                    <div class="testimonial-card">
                        <div class="testimonial-content">
                            <div class="quote-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                                </svg>
                            </div>
                            <p>"The flexibility of learning at my own pace while working full-time was perfect. The
                                programming courses helped me transition into tech seamlessly."</p>
                            <div class="testimonial-author">
                                <img src="assets/images/testimonials/elias.jpg" alt="Elias Cheruiyot">
                                <div class="author-info">
                                    <h4>
                                        Elias Cheruiyot
                                        <a href="https://linkedin.com/in/elias-cheruiyot" target="_blank"
                                            rel="noopener noreferrer" class="linkedin-link">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                fill="currentColor">
                                                <path
                                                    d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
                                            </svg>
                                        </a>
                                    </h4>
                                    <span>Software Developer</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rogers Testimonial -->
                    <div class="testimonial-card">
                        <div class="testimonial-content">
                            <div class="quote-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                                </svg>
                            </div>
                            <p>"The digital marketing course exceeded my expectations. The practical assignments helped
                                me build a strong portfolio, and I landed my dream job!"</p>
                            <div class="testimonial-author">
                                <img src="assets/images/testimonials/Rogers.png" alt="Rogers Kimani">
                                <div class="author-info">
                                    <h4>
                                        Rogers Kimani
                                        <a href="https://linkedin.com/in/rogers-kimani" target="_blank"
                                            rel="noopener noreferrer" class="linkedin-link">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                fill="currentColor">
                                                <path
                                                    d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
                                            </svg>
                                        </a>
                                    </h4>
                                    <span>Digital Marketer</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lucy Testimonial -->
                    <div class="testimonial-card">
                        <div class="testimonial-content">
                            <div class="quote-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                                </svg>
                            </div>
                            <p>"WissenWelle transformed my learning journey. The NCLEX course was comprehensive and the
                                practice tests were invaluable. I passed on my first attempt!"</p>
                            <div class="testimonial-author">
                                <img src="assets/images/testimonials/lucy.jpg" alt="Lucy Githuku">
                                <div class="author-info">
                                    <h4>
                                        Lucy Githuku
                                        <a href="https://linkedin.com/in/lucy-githuku" target="_blank"
                                            rel="noopener noreferrer" class="linkedin-link">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                fill="currentColor">
                                                <path
                                                    d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
                                            </svg>
                                        </a>
                                    </h4>
                                    <span>Nursing Student</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Joshua Testimonial -->
                    <div class="testimonial-card">
                        <div class="testimonial-content">
                            <div class="quote-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                                </svg>
                            </div>
                            <p>"The leadership and management courses have been instrumental in my career growth. The
                                real-world case studies and mentorship were exceptional."</p>
                            <div class="testimonial-author">
                                <img src="assets/images/testimonials/Joshua.png" alt="Joshua Rotich">
                                <div class="author-info">
                                    <h4>
                                        Joshua Rotich
                                        <a href="https://linkedin.com/in/joshua-rotich" target="_blank"
                                            rel="noopener noreferrer" class="linkedin-link">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                fill="currentColor">
                                                <path
                                                    d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
                                            </svg>
                                        </a>
                                    </h4>
                                    <span>Leadership Coach</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Joshua Testimonial -->
                    <div class="testimonial-card">
                        <div class="testimonial-content">
                            <div class="quote-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
                                </svg>
                            </div>
                            <p>"The leadership and management courses have been instrumental in my career growth. The
                                real-world case studies and mentorship were exceptional."</p>
                            <div class="testimonial-author">
                                <img src="assets/images/testimonials/Joshua.png" alt="Joshua Rotich">
                                <div class="author-info">
                                    <h4>
                                        Joshua Rotich
                                        <a href="https://linkedin.com/in/joshua-rotich" target="_blank"
                                            rel="noopener noreferrer" class="linkedin-link">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                fill="currentColor">
                                                <path
                                                    d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
                                            </svg>
                                        </a>
                                    </h4>
                                    <span>Leadership Coach</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="faqs">
        <div class="container">
            <div class="section-header">
                <h2>Frequently Asked Questions</h2>
                <p>Find answers to common questions about our platform</p>
            </div>

            <div class="faq-grid">
                <div class="faq-item">
                    <button class="faq-question">
                        <span>How do I get started with WissenWelle?</span>
                        <svg class="arrow-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="faq-answer">
                        <p>Getting started is easy! Simply create an account, browse our course catalog, and enroll in
                            any course that interests you. You can begin learning immediately after enrollment.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">
                        <span>What payment methods do you accept?</span>
                        <svg class="arrow-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="faq-answer">
                        <p>We accept all major credit cards (Visa, MasterCard, American Express), PayPal, and M-Pesa
                            Global for our East African students. All payments are processed securely through our
                            platform.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">
                        <span>How do I get an M-Pesa Global Visa Card?</span>
                        <svg class="arrow-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="faq-answer">
                        <p>To get your M-Pesa Global Visa Card:</p>
                        <ol class="faq-list">
                            <li>1. Dial *334# on your Safaricom line</li>
                            <li>2. Select "M-PESA Global"</li>
                            <li>3. Choose "Get Virtual Card"</li>
                            <li>4. Enter your M-PESA PIN</li>
                            <li>5. Create a unique 4-digit card PIN</li>
                            <li>6. Your virtual card will be created instantly</li>
                        </ol>
                        <p>The card is free to create and can be used for all online payments. You can view your card
                            details anytime in the M-PESA app under "Cards".</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">
                        <span>Are the courses self-paced?</span>
                        <svg class="arrow-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="faq-answer">
                        <p>Yes, all our courses are self-paced. You can learn at your own speed and access the course
                            content 24/7. There are no deadlines, allowing you to balance your learning with other
                            commitments.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">
                        <span>Do I get a certificate upon completion?</span>
                        <svg class="arrow-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="faq-answer">
                        <p>Yes! Upon successful completion of a course, you'll receive a verified digital certificate
                            that you can share on your LinkedIn profile or with potential employers.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">
                        <span>Can I download course materials for offline use?</span>
                        <svg class="arrow-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="faq-answer">
                        <p>Yes, most course materials including PDFs, worksheets, and practice exercises can be
                            downloaded for offline use. However, video content requires an internet connection to
                            stream.</p>
                    </div>
                </div>



                <div class="faq-item">
                    <button class="faq-question">
                        <span>Do courses get updated?</span>
                        <svg class="arrow-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="faq-answer">
                        <p>Yes, our courses are regularly updated to ensure content remains current and relevant. Once
                            enrolled, you'll have lifetime access to all course updates at no additional cost.</p>
                    </div>
                </div>


            </div>
        </div>
    </section>

    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>Ready to Start Your Learning Journey?</h2>
                <p>Join thousands of students and Instructors already on WissenWelle</p>
                <div class="cta-buttons">
                    <a href="#" class="cta-btn primary-btn">Browse Courses</a>
                    <a href="#" class="cta-btn secondary-btn">Teach At WissenWelle</a>
                </div>
                <div class="cta-stats">
                    <div class="stat-item">
                        <span class="stat-number">87+</span>
                        <span class="stat-label">Courses</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">2K+</span>
                        <span class="stat-label">Students</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">4.8</span>
                        <span class="stat-label">Rating</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <!-- Logo and description -->
                <div class="footer-brand">
                    <div class="footer-logo">
                        <img src="assets/images/logo.png" alt="WissenWelle" class="logo-img">
                    </div>
                    <p class="brand-description">Turn Knowledge into Impact & Income</p>
                    <div class="social-links">
                        <a href="https://twitter.com/wissenwelle" target="_blank" rel="noopener noreferrer"
                            class="social-link">
                            <svg fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                            </svg>
                        </a>
                        <a href="https://linkedin.com/company/wissenwelle" target="_blank" rel="noopener noreferrer"
                            class="social-link">
                            <svg fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
                            </svg>
                        </a>
                        <a href="https://facebook.com/wissenwelle" target="_blank" rel="noopener noreferrer"
                            class="social-link">
                            <svg fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd"
                                    d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="footer-links">
                    <div class="link-column">
                        <h3>Company</h3>
                        <ul>
                            <li><a href="/about">About Us</a></li>
                            <li><a href="/careers">Careers</a></li>
                            <li><a href="/blog">Blog</a></li>
                            <li><a href="/press">Press</a></li>
                        </ul>
                    </div>

                    <div class="link-column">
                        <h3>Resources</h3>
                        <ul>
                            <li><a href="/courses">Course Catalog</a></li>
                            <li><a href="/success">Student Success</a></li>
                            <li><a href="/enterprise">For Enterprise</a></li>
                            <li><a href="/partners">For Partners</a></li>
                        </ul>
                    </div>

                    <div class="link-column">
                        <h3>Support</h3>
                        <ul>
                            <li><a href="/help">Help Center</a></li>
                            <li><a href="/contact">Contact Us</a></li>
                            <li><a href="/privacy">Privacy Policy</a></li>
                            <li><a href="/terms">Terms of Service</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="footer-bottom">
                <p class="copyright">©
                    <script>document.write(new Date().getFullYear())</script> WissenWelle. All rights reserved.
                </p>
                <div class="bottom-links">
                    <a href="/privacy">Privacy Policy</a>
                    <a href="/terms">Terms of Service</a>
                    <a href="/cookies">Cookie Policy</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="index.js"></script>
</body>

</html>