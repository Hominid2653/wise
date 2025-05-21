<?php
session_start();
require_once '../config/auth.php';

// Check if user is logged in and is admin
if (!isAdmin()) {
    header('Location: /admin/login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Management - WissenWelle Admin</title>

    <link rel="stylesheet" href="css/admin.css">
</head>

<body class="admin-body">
    <nav class="admin-nav">
        <div class="nav-left">
            <div class="logo">
                <a href="/admin">
                    <img src="../assets/images/logo.png" alt="WissenWelle" />
                </a>
            </div>
            <div class="nav-links">
                <a href="dashboard.php">Dashboard</a>
                <a href="academy.php" class="active">Courses</a>
                <a href="blog.php">Blog</a>
                <a href="settings.php">Settings</a>
            </div>
        </div>
        <div class="nav-right">
            <span class="admin-user">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <?php echo htmlspecialchars($_SESSION['admin_username']); ?>
            </span>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </nav>

    <main class="admin-main">
        <div class="admin-header">
            <div class="header-left">
                <h1>Course Management</h1>
                <p class="subtitle">Manage and organize your Thinkific courses</p>
            </div>
            <button id="sync-courses" class="primary-btn">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Sync with Thinkific
            </button>
        </div>

        <div class="admin-content">
            <div class="content-toolbar">
                <div class="toolbar-left">
                    <div class="search-container">
                        <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" id="course-search" placeholder="Search courses...">
                    </div>
                </div>
                <div class="toolbar-right">
                    <div class="filter-buttons">
                        <button class="filter-btn active" data-status="all">
                            <span class="filter-label">All Courses</span>
                            <span class="count">0</span>
                        </button>
                        <button class="filter-btn" data-status="published">
                            <span class="filter-label">Published</span>
                            <span class="count">0</span>
                        </button>
                        <button class="filter-btn" data-status="draft">
                            <span class="filter-label">Drafts</span>
                            <span class="count">0</span>
                        </button>
                        <button class="filter-btn" data-status="hidden">
                            <span class="filter-label">Hidden</span>
                            <span class="count">0</span>
                        </button>
                    </div>
                </div>
            </div>

            <div id="courses-grid" class="admin-courses-grid">
                <div class="loading-spinner">
                    <svg class="spinner" viewBox="0 0 50 50">
                        <circle class="path" cx="25" cy="25" r="20" fill="none" stroke-width="5"></circle>
                    </svg>
                    <span>Loading courses...</span>
                </div>
            </div>

            <!-- Add pagination -->
            <div class="pagination">
                <button class="pagination-btn prev-page" disabled>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Previous
                </button>
                <div class="page-numbers"></div>
                <button class="pagination-btn next-page">
                    Next
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>
    </main>

    <!-- Course Edit Modal -->
    <div id="course-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Course</h2>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="course-edit-form">
                    <input type="hidden" id="course-id">
                    <div class="form-group">
                        <label for="course-title">Title</label>
                        <input type="text" id="course-title" required>
                    </div>
                    <div class="form-group">
                        <label for="course-subtitle">Subtitle</label>
                        <input type="text" id="course-subtitle">
                    </div>
                    <div class="form-group">
                        <label for="course-description">Description</label>
                        <textarea id="course-description" rows="4"></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="course-price">Price</label>
                            <input type="number" id="course-price" min="0" step="0.01">
                        </div>
                        <div class="form-group">
                            <label for="course-duration">Duration</label>
                            <input type="text" id="course-duration">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="course-level">Level</label>
                            <select id="course-level">
                                <option value="Beginner">Beginner</option>
                                <option value="Intermediate">Intermediate</option>
                                <option value="Advanced">Advanced</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="course-category">Category</label>
                            <select id="course-category">
                                <option value="">Select a category</option>
                                <option value="Professional">Professional</option>
                                <option value="Position">Position</option>
                                <option value="Skill">Skill</option>
                                <option value="Patient Education">Patient Education</option>
                                <option value="AI">AI</option>
                                <option value="Software">Software</option>
                                <option value="Exam Prep">Exam Prep</option>
                                <option value="Technology">Technology</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="course-instructor">Instructor Name</label>
                            <input type="text" id="course-instructor" placeholder="e.g. Elias Cheruiyot">
                        </div>
                        <div class="form-group">
                            <label for="course-instructor-title">Instructor Title</label>
                            <input type="text" id="course-instructor-title" placeholder="e.g. Senior Photographer">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="course-status">Status</label>
                        <select id="course-status">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                            <option value="hidden">Hidden</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" id="course-featured">
                            Featured Course
                        </label>
                    </div>
                    <div class="form-group">
                        <label for="course-order">Display Order</label>
                        <input type="number" id="course-order" min="0">
                    </div>
                    <div class="form-group">
                        <label for="course-custom-description">Custom Description</label>
                        <textarea id="course-custom-description" rows="4"></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="course-lessons">Number of Lessons</label>
                            <input type="number" id="course-lessons" min="0" step="1">
                        </div>
                        <div class="form-group">
                            <label for="course-video-duration">Video Content Duration</label>
                            <input type="text" id="course-video-duration" placeholder="e.g. 12h 30m">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="course-url">Course URL</label>
                        <input type="url" id="course-url" class="full-width"
                            placeholder="https://courses.wissenwelle.com/products/courses/your-course-name">
                        <small class="input-help">Copy the full course URL from Thinkific</small>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="save-btn">Save Changes</button>
                        <button type="button" class="cancel-btn">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../js/thinkific-api.js"></script>
    <script src="js/admin-courses.js"></script>

    <!-- Pagination styling -->
    <style>
        /* Pagination styling */
        .pagination {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
            padding: 1rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .pagination-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: white;
            color: #475569;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .pagination-btn:hover:not(:disabled) {
            background: #f8fafc;
            border-color: var(--battery-blue);
            color: var(--battery-blue);
        }

        .pagination-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            border-color: #e2e8f0;
        }

        .pagination-btn svg {
            width: 1.25rem;
            height: 1.25rem;
        }

        .page-numbers {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .page-number {
            min-width: 2.5rem;
            height: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 0.5rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            background: white;
            color: #475569;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .page-number:hover {
            background: #f8fafc;
            border-color: var(--battery-blue);
            color: var(--battery-blue);
        }

        .page-number.active {
            background: var(--battery-blue);
            color: white;
            border-color: var(--battery-blue);
        }

        /* Responsive adjustments */
        @media (max-width: 640px) {
            .pagination {
                flex-wrap: wrap;
                gap: 0.5rem;
            }

            .page-numbers {
                order: -1;
                width: 100%;
                justify-content: center;
                margin-bottom: 0.5rem;
            }

            .pagination-btn {
                flex: 1;
                justify-content: center;
            }
        }

        /* Content toolbar styling */
        .content-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 2rem;
            padding: 1.5rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }

        .toolbar-left {
            flex: 1;
        }

        /* Search container improvements */
        .search-container {
            position: relative;
            max-width: 500px;
            width: 100%;
        }

        .search-container input {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 2.75rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.875rem;
            color: #1a202c;
            transition: all 0.2s ease;
        }

        .search-container input:focus {
            outline: none;
            background: white;
            border-color: var(--battery-blue);
            box-shadow: 0 0 0 3px rgba(41, 120, 160, 0.1);
        }

        .search-icon {
            position: absolute;
            left: 0.875rem;
            top: 50%;
            transform: translateY(-50%);
            width: 1.25rem;
            height: 1.25rem;
            color: #94a3b8;
            pointer-events: none;
        }

        .clear-search {
            position: absolute;
            right: 0.875rem;
            top: 50%;
            transform: translateY(-50%);
            padding: 0.25rem;
            border: none;
            background: none;
            color: #94a3b8;
            cursor: pointer;
            opacity: 0.6;
            transition: all 0.2s ease;
        }

        .clear-search:hover {
            opacity: 1;
            color: #ef4444;
        }

        /* Filter buttons styling */
        .filter-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .filter-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            color: #475569;
            font-weight: 500;
            transition: all 0.2s ease;
            white-space: nowrap;
            position: relative;
            overflow: hidden;
        }

        .filter-btn:hover {
            background: rgba(41, 120, 160, 0.05);
            border-color: var(--battery-blue);
            color: var(--battery-blue);
        }

        .filter-btn.active {
            background: rgba(41, 120, 160, 0.1);
            border-color: var(--battery-blue);
            color: var(--battery-blue);
            font-weight: 600;
        }

        .filter-btn .count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 1.5rem;
            height: 1.5rem;
            padding: 0 0.5rem;
            background: rgba(41, 120, 160, 0.1);
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .filter-btn.active .count {
            background: #2978A0;
            color: white;
            transform: scale(1.05);
            box-shadow: 0 2px 4px rgba(41, 120, 160, 0.2);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .content-toolbar {
                flex-direction: column;
                gap: 1rem;
                padding: 1rem;
            }

            .toolbar-left,
            .toolbar-right {
                width: 100%;
            }

            .filter-buttons {
                width: 100%;
                overflow-x: auto;
                padding-bottom: 0.5rem;
            }

            .filter-btn {
                flex: 0 0 auto;
            }
        }

        /* Course action buttons */
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .edit-course-btn,
        .preview-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            background: white;
            color: #475569;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .edit-course-btn:hover,
        .preview-btn:hover {
            background: #f8fafc;
            border-color: var(--battery-blue);
            color: var(--battery-blue);
        }

        .edit-course-btn svg,
        .preview-btn svg {
            width: 1.25rem;
            height: 1.25rem;
        }

        .preview-btn {
            background: #f8fafc;
        }

        .preview-btn:hover {
            background: white;
        }
    </style>
</body>

</html>