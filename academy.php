<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academy - WissenWelle</title>
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
            <a href="index.php">Home</a>
            <a href="about.html">About</a>
            <a href="academy.php">Academy</a>
            <a href="#">Blog</a>
            <a href="https://courses.wissenwelle.com" class="login-btn">Login</a>
        </div>

        <!-- Add mobile menu container -->
        <div class="mobile-menu">
            <div class="mobile-menu-links">
                <a href="index.php">Home</a>
                <a href="about.html">About</a>
                <a href="academy.php">Academy</a>
                <a href="#">Blog</a>
                <a href="https://courses.wissenwelle.com" class="mobile-login-btn">Login</a>
            </div>
        </div>
    </nav>

    <style>
        /* Navbar styling */
        nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            width: 100%;
            background: rgba(0, 33, 71, 0.95);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid rgba(239, 223, 184, 0.1);
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 1001;
        }

        /* Logo styling */
        .logo {
            height: 50px;
        }

        .logo img {
            height: 100%;
            width: auto;
            object-fit: contain;
        }

        /* Navigation links */
        .nav-links {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .nav-links a {
            color: var(--dutch-white, #EFDFB8);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .nav-links a:hover {
            color: white;
        }

        /* Login button */
        .login-btn {
            padding: 0.5rem 1.5rem;
            background: var(--dutch-white, #EFDFB8);
            color: var(--oxford-blue, #002147) !important;
            border-radius: 9999px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .login-btn:hover {
            background: white;
            transform: translateY(-1px);
        }

        /* Search bar container adjustments */
        .search-toolbar {
            margin-top: 1rem;
            background: white;
            padding: 1rem;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.08);
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        /* Mobile menu button */
        .mobile-menu-btn {
            display: none;
        }

        @media (max-width: 768px) {
            .mobile-menu-btn {
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                width: 24px;
                height: 20px;
                background: none;
                border: none;
                cursor: pointer;
                padding: 0;
                z-index: 1001;
            }

            .mobile-menu-btn span {
                display: block;
                width: 100%;
                height: 2px;
                background-color: #EFDFBB;
                transition: all 0.3s ease;
            }

            .mobile-menu-btn.active span:nth-child(1) {
                transform: translateY(9px) rotate(45deg);
            }

            .mobile-menu-btn.active span:nth-child(2) {
                opacity: 0;
            }

            .mobile-menu-btn.active span:nth-child(3) {
                transform: translateY(-9px) rotate(-45deg);
            }

            .nav-links {
                display: none;
            }
        }

        /* Ensure sidebar is below navbar and above content */
        .filters-sidebar {
            z-index: 1000;
        }

        /* Prevent main content from being hidden under navbar on all screens */
        .academy-page {
            padding-top: 5rem;
            min-height: calc(100vh - 5rem);
        }

        /* On small screens, sidebar overlays content, so add overlay effect */
        @media (max-width: 768px) {
            .filters-sidebar {
                position: fixed;
                left: 0;
                top: 80px;
                width: 80vw;
                max-width: 350px;
                height: calc(100vh - 80px);
                background: white;
                box-shadow: 2px 0 8px rgba(0, 0, 0, 0.15);
                z-index: 1100;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .filters-sidebar.active {
                transform: translateX(0);
            }

            .academy-page>main {
                margin-left: 0;
            }

            /* Optional: add overlay when sidebar is open */
            body.sidebar-open::before {
                content: "";
                position: fixed;
                top: 80px;
                left: 0;
                width: 100vw;
                height: calc(100vh - 80px);
                background: rgba(0, 0, 0, 0.2);
                z-index: 1099;
            }
        }

        /* Academy-specific search bar styling */
        .search-container {
            flex: 1;
            position: relative;
        }

        .search-bar {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.5rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }

        .search-bar:focus {
            outline: none;
            border-color: var(--battery-blue);
            box-shadow: 0 0 0 3px rgba(41, 120, 160, 0.1);
        }

        .search-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            width: 1.25rem;
            height: 1.25rem;
            color: #94a3b8;
            pointer-events: none;
        }

        /* Search toolbar layout */
        .show-filters {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            color: #475569;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .show-filters:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
        }

        .show-filters svg {
            width: 1.25rem;
            height: 1.25rem;
        }

        /* Smooth transition for sidebar */
        .filters-sidebar {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        /* Overlay transition */
        body.sidebar-open::before {
            transition: opacity 0.3s ease;
            opacity: 0;
        }

        body.sidebar-open.active::before {
            opacity: 1;
        }

        /* Ensure the main content area adjusts properly */
        .academy-content {
            transition: margin-left 0.3s ease;
        }

        /* Active state styles */
        .show-filters.active {
            background: #f1f5f9;
            border-color: #94a3b8;
        }

        /* Prevent body scroll when sidebar is open on mobile */
        body.sidebar-open {
            overflow: hidden;
        }

        @media (min-width: 769px) {
            body.sidebar-open {
                overflow: auto;
            }
        }

        /* Price badge styling */
        .course-card .course-image {
            position: relative;
        }

        .price-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(0, 0, 0, 0.75);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-weight: 500;
            font-size: 0.875rem;
            backdrop-filter: blur(4px);
        }

        /* Update featured badge positioning to not conflict with price */
        .featured-badge {
            position: absolute;
            top: 1rem;
            left: 1rem;
            background: var(--battery-blue);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-weight: 500;
            font-size: 0.875rem;
        }

        /* Course card action buttons */
        .course-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .enroll-btn {
            background: var(--oxford-blue);
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.2s;
            flex: 1;
            text-align: center;
        }

        .enroll-btn:hover {
            background: var(--battery-blue);
            transform: translateY(-2px);
        }

        .preview-btn {
            padding: 0.75rem 1.5rem;
            border: 1px solid var(--oxford-blue);
            color: var(--oxford-blue);
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .preview-btn:hover {
            background: var(--oxford-blue);
            color: white;
        }

        /* Update course footer to better align with buttons */
        .course-footer {
            margin-top: auto;
            padding-top: 1rem;
            border-top: 1px solid #e5e7eb;
        }

        .stats {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #6b7280;
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }

        /* Course card and button styling */
        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .course-card {
            background: white;
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 16px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .course-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
        }

        .course-image {
            position: relative;
            width: 100%;
            padding-top: 56.25%;
            /* 16:9 aspect ratio */
        }

        .course-image img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .course-content {
            flex: 1;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
        }

        .course-footer {
            margin-top: auto;
            padding-top: 1rem;
            border-top: 1px solid #e5e7eb;
        }

        .course-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .enroll-btn {
            flex: 1;
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: var(--oxford-blue, #002147);
            color: white;
            text-align: center;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .enroll-btn:hover {
            background: var(--battery-blue, #2978A0);
            transform: translateY(-2px);
        }

        .preview-btn {
            padding: 0.75rem 1.5rem;
            border: 1px solid var(--oxford-blue, #002147);
            color: var(--oxford-blue, #002147);
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
            text-align: center;
        }

        .preview-btn:hover {
            background: var(--oxford-blue, #002147);
            color: white;
        }

        /* Make sure price badge stays on top */
        .price-badge {
            z-index: 2;
        }

        /* Active filters styling */
        .active-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .active-filter {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: #f1f5f9;
            border-radius: 9999px;
            font-size: 0.875rem;
            color: #1a202c;
        }

        .remove-filter {
            background: none;
            border: none;
            color: #64748b;
            cursor: pointer;
            padding: 0;
            font-size: 1.25rem;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .remove-filter:hover {
            color: #ef4444;
        }

        /* Price slider styling */
        .price-slider {
            width: 100%;
            height: 4px;
            background: #e2e8f0;
            border-radius: 2px;
            outline: none;
            -webkit-appearance: none;
        }

        .price-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 16px;
            height: 16px;
            background: #3b82f6;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .price-slider::-webkit-slider-thumb:hover {
            transform: scale(1.2);
        }

        /* Price range styling */
        .price-range {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .price-inputs {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .input-group {
            position: relative;
            flex: 1;
        }

        .currency {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            pointer-events: none;
        }

        .price-inputs input {
            width: 100%;
            padding: 0.5rem 0.5rem 0.5rem 1.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            font-size: 0.875rem;
        }

        .price-inputs .separator {
            color: #64748b;
        }

        .price-presets {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .price-preset {
            padding: 0.375rem 0.75rem;
            background: #f1f5f9;
            border: none;
            border-radius: 9999px;
            font-size: 0.75rem;
            color: #475569;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .price-preset:hover {
            background: #e2e8f0;
        }

        .price-preset.active {
            background: var(--battery-blue);
            color: white;
        }

        /* Update existing mobile menu styles */
        .mobile-menu {
            display: none;
            position: absolute;
            top: 80px;
            left: 1rem;
            right: 1rem;
            background: rgba(0, 33, 71, 0.95);
            backdrop-filter: blur(8px);
            border-radius: 12px;
            border: 1px solid rgba(239, 223, 184, 0.2);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transform: translateY(-20px);
            opacity: 0;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .mobile-menu.active {
            display: block;
            transform: translateY(0);
            opacity: 1;
        }

        .mobile-menu-links {
            padding: 0.5rem;
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .mobile-menu-links a {
            display: block;
            padding: 0.75rem 1rem;
            color: rgba(239, 223, 184, 0.9);
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .mobile-menu-links a:hover {
            color: #EFDFBB;
            background: rgba(239, 223, 184, 0.1);
        }

        .mobile-menu-links .mobile-login-btn {
            background: #EFDFBB;
            color: #002147;
            font-weight: 500;
            text-align: left;
            margin-top: 0.25rem;
        }

        .mobile-menu-links .mobile-login-btn:hover {
            background: rgba(239, 223, 184, 0.9);
            color: #002147;
        }
    </style>

    <main class="academy-page">
        <!-- Filter Sidebar -->
        <aside class="filters-sidebar">
            <div class="sidebar-header">
                <h3>Filters</h3>
                <button class="close-filters">×</button>
            </div>

            <div class="filter-section">
                <h4>Duration</h4>
                <div class="filter-options">
                    <label class="filter-option">
                        <input type="checkbox" name="duration" value="0-4">
                        <span>0-4 weeks</span>
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="duration" value="5-8">
                        <span>5-8 weeks</span>
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="duration" value="9-12">
                        <span>9-12 weeks</span>
                    </label>
                </div>
            </div>

            <div class="filter-section">
                <h4>Categories</h4>
                <div class="filter-options">
                    <label class="filter-option">
                        <input type="checkbox" name="category" value="Professional">
                        <span>Professional</span>
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="category" value="Position">
                        <span>Position</span>
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="category" value="Skill">
                        <span>Skill</span>
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="category" value="Patient Education">
                        <span>Patient Education</span>
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="category" value="AI">
                        <span>AI</span>
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="category" value="Software">
                        <span>Software</span>
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="category" value="Exam Prep">
                        <span>Exam Prep</span>
                    </label>
                    <label class="filter-option">
                        <input type="checkbox" name="category" value="Technology">
                        <span>Technology</span>
                    </label>
                </div>
            </div>

            <div class="filter-section">
                <h4>Price Range</h4>
                <div class="price-range">
                    <div class="price-inputs">
                        <div class="input-group">
                            <span class="currency">$</span>
                            <input type="number" class="min-price" placeholder="Min" min="0" max="1000" step="0.01">
                        </div>
                        <span class="separator">-</span>
                        <div class="input-group">
                            <span class="currency">$</span>
                            <input type="number" class="max-price" placeholder="Max" min="0" max="1000" step="0.01">
                        </div>
                    </div>
                    <div class="price-presets">
                        <button class="price-preset" data-min="0" data-max="0">Free</button>
                        <button class="price-preset" data-min="0.01" data-max="50">Under $50</button>
                        <button class="price-preset" data-min="50" data-max="100">$50-$100</button>
                        <button class="price-preset" data-min="100" data-max="1000">$100+</button>
                    </div>
                </div>
            </div>

            <button class="apply-filters">Apply Filters</button>
            <button class="reset-filters">Reset All</button>
        </aside>

        <!-- Main Content -->
        <div class="academy-content">
            <div class="search-toolbar">
                <button class="show-filters">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Filters
                </button>
                <div class="search-container">
                    <input type="text" class="search-bar" placeholder="Search courses...">
                    <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <div class="active-filters">
                <!-- Active filters will be dynamically added here -->
            </div>

            <div class="courses-grid" id="courses-grid">
                <!-- Courses will be dynamically loaded here -->
            </div>
        </div>
    </main>

    <script src="index.js"></script>
    <script>
        class CourseManager {
            async loadCourses() {
                try {
                    const response = await fetch('/wise/api/public-courses.php');
                    if (!response.ok) throw new Error('Failed to fetch courses');

                    const data = await response.json();
                    this.courses = data.courses;
                    this.renderCourses(this.courses);
                    this.initializeFilters();
                } catch (error) {
                    console.error('Error loading courses:', error);
                    document.getElementById('courses-grid').innerHTML =
                        '<p class="error-message">Failed to load courses. Please try again.</p>';
                }
            }

            renderCourses(courses) {
                const grid = document.getElementById('courses-grid');
                if (!courses.length) {
                    grid.innerHTML = '<p class="no-courses">No courses available at the moment.</p>';
                    return;
                }

                grid.innerHTML = courses.map(course => `
                <div class="course-card">
                    <div class="course-image">
                        <img src="${course.image_url}" alt="${course.name}">
                        ${course.featured ? '<span class="featured-badge">Featured</span>' : ''}
                        ${course.price ? `
                            <span class="price-badge">
                                ${course.price === 0 ? 'Free' : `$${course.price}`}
                            </span>
                        ` : ''}
                    </div>
                    <div class="course-content">
                        <div class="course-header">
                            <h3 class="course-title">${course.name}</h3>
                            ${course.category ? `
                                <div class="course-categories">
                                    <span class="category-tag">${course.category}</span>
                                </div>
                            ` : ''}
                        </div>
                        <p class="course-description">${course.description}</p>
                        ${course.instructor ? `
                            <div class="course-instructor">
                                <span class="instructor-name">${course.instructor.name}</span>
                                ${course.instructor.title ? `<span class="instructor-title">- ${course.instructor.title}</span>` : ''}
                            </div>
                        ` : ''}
                        <div class="course-footer">
                            <div class="stats">
                                ${course.video_duration ? `<span>${course.video_duration}</span><span>•</span>` : ''}
                                <span>${course.duration || '12 weeks'}</span>
                                <span>•</span>
                                <span>${course.lessons_count || 0} lessons</span>
                            </div>
                            <div class="course-actions">
                                <a href="${course.url}" class="enroll-btn" target="_blank">
                                    ${course.price === 0 ? 'Enroll Free' : 'Enroll Now'}
                                </a>
                                ${course.preview_url ? `
                                    <a href="${course.preview_url}" class="preview-btn" target="_blank">Preview</a>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
            }

            filterCourses() {
                // Get all selected categories
                const selectedCategories = Array.from(
                    document.querySelectorAll('input[name="category"]:checked')
                ).map(input => input.value);

                // Get selected duration ranges
                const selectedDurations = Array.from(
                    document.querySelectorAll('input[name="duration"]:checked')
                ).map(input => input.value);

                // Get price range
                const minPrice = parseFloat(document.querySelector('.min-price').value) || 0;
                const maxPrice = parseFloat(document.querySelector('.max-price').value) || Infinity;

                // Filter courses based on all criteria
                const filtered = this.courses.filter(course => {
                    // Category filter - now checking single category value
                    const categoryMatch = selectedCategories.length === 0 ||
                        (course.category && selectedCategories.includes(course.category));

                    // Duration filter
                    const durationMatch = selectedDurations.length === 0 ||
                        selectedDurations.some(range => {
                            const [min, max] = range.split('-').map(Number);
                            const courseDuration = parseInt(course.duration);
                            return courseDuration >= min && courseDuration <= max;
                        });

                    // Price filter with special handling for free courses
                    const coursePrice = parseFloat(course.price) || 0;
                    const priceMatch =
                        (minPrice === 0 && maxPrice === 0 && coursePrice === 0) || // Free courses
                        (coursePrice >= minPrice && (maxPrice === Infinity || coursePrice <= maxPrice)); // Price range

                    return categoryMatch && durationMatch && priceMatch;
                });

                this.renderCourses(filtered);
                this.updateActiveFilters(selectedCategories, selectedDurations, minPrice, maxPrice);
            }

            updateActiveFilters(categories, durations, minPrice, maxPrice) {
                const activeFiltersContainer = document.querySelector('.active-filters');
                let activeFiltersHTML = '';

                // Add category filters
                categories.forEach(category => {
                    activeFiltersHTML += `
                        <span class="active-filter">
                            ${category}
                            <button onclick="courseManager.removeFilter('category', '${category}')" class="remove-filter">×</button>
                        </span>
                    `;
                });

                // Add duration filters
                durations.forEach(duration => {
                    activeFiltersHTML += `
                        <span class="active-filter">
                            ${duration} weeks
                            <button onclick="courseManager.removeFilter('duration', '${duration}')" class="remove-filter">×</button>
                        </span>
                    `;
                });

                // Add price filter if set
                if (minPrice > 0 || maxPrice < Infinity) {
                    activeFiltersHTML += `
                        <span class="active-filter">
                            Price: $${minPrice} - $${maxPrice === Infinity ? '∞' : maxPrice}
                            <button onclick="courseManager.resetPriceFilter()" class="remove-filter">×</button>
                        </span>
                    `;
                }

                activeFiltersContainer.innerHTML = activeFiltersHTML;
            }

            removeFilter(type, value) {
                const checkbox = document.querySelector(`input[name="${type}"][value="${value}"]`);
                if (checkbox) {
                    checkbox.checked = false;
                    this.filterCourses();
                }
            }

            resetPriceFilter() {
                document.querySelector('.min-price').value = '';
                document.querySelector('.max-price').value = '';
                this.filterCourses();
            }

            initializeFilters() {
                // Category filters
                document.querySelectorAll('input[name="category"]').forEach(checkbox => {
                    checkbox.addEventListener('change', () => this.filterCourses());
                });

                // Duration filters
                document.querySelectorAll('input[name="duration"]').forEach(checkbox => {
                    checkbox.addEventListener('change', () => this.filterCourses());
                });

                // Price range inputs
                const priceInputs = document.querySelectorAll('.price-inputs input');
                priceInputs.forEach(input => {
                    input.addEventListener('input', () => {
                        this.clearPricePresets();
                        this.filterCourses();
                    });
                });

                // Price presets
                const pricePresets = document.querySelectorAll('.price-preset');
                pricePresets.forEach(preset => {
                    preset.addEventListener('click', () => {
                        const min = preset.dataset.min;
                        const max = preset.dataset.max;

                        // Update input values
                        document.querySelector('.min-price').value = min;
                        document.querySelector('.max-price').value = max === "1000" ? "" : max;

                        // Update active state
                        this.clearPricePresets();
                        preset.classList.add('active');

                        this.filterCourses();
                    });
                });

                // Search
                const searchInput = document.querySelector('.search-bar');
                let searchTimeout;
                searchInput.addEventListener('input', (e) => {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => this.searchCourses(e.target.value), 300);
                });

                // Reset filters
                document.querySelector('.reset-filters').addEventListener('click', () => {
                    // Uncheck all checkboxes
                    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                        checkbox.checked = false;
                    });

                    // Reset price inputs
                    document.querySelector('.min-price').value = '';
                    document.querySelector('.max-price').value = '';

                    // Reset search
                    searchInput.value = '';

                    // Show all courses
                    this.renderCourses(this.courses);
                });
            }

            clearPricePresets() {
                document.querySelectorAll('.price-preset').forEach(preset => {
                    preset.classList.remove('active');
                });
            }
        }

        // Initialize course manager
        const courseManager = new CourseManager();
        courseManager.loadCourses();
    </script>

    <script>
        // Sidebar toggle functionality
        const filterButton = document.querySelector('.show-filters');
        const closeButton = document.querySelector('.close-filters');
        const sidebar = document.querySelector('.filters-sidebar');
        const body = document.body;

        // Function to open sidebar
        function openSidebar() {
            sidebar.classList.add('active');
            body.classList.add('sidebar-open');
        }

        // Function to close sidebar
        function closeSidebar() {
            sidebar.classList.remove('active');
            body.classList.remove('sidebar-open');
        }

        // Event listeners
        filterButton.addEventListener('click', openSidebar);
        closeButton.addEventListener('click', closeSidebar);

        // Close sidebar when clicking outside
        document.addEventListener('click', (e) => {
            const isClickInside = sidebar.contains(e.target) || filterButton.contains(e.target);
            if (!isClickInside && sidebar.classList.contains('active')) {
                closeSidebar();
            }
        });

        // Close sidebar on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && sidebar.classList.contains('active')) {
                closeSidebar();
            }
        });

        // Prevent clicks inside sidebar from closing it
        sidebar.addEventListener('click', (e) => {
            e.stopPropagation();
        });

        // Optional: Close sidebar on window resize if in mobile view
        window.addEventListener('resize', () => {
            if (window.innerWidth > 768) {
                closeSidebar();
            }
        });
    </script>

    <script>
        // Add this to your existing JavaScript
        document.addEventListener('DOMContentLoaded', function () {
            const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
            const mobileMenu = document.querySelector('.mobile-menu');

            mobileMenuBtn.addEventListener('click', () => {
                mobileMenuBtn.classList.toggle('active');
                mobileMenu.classList.toggle('active');
            });

            // Close menu when clicking outside
            document.addEventListener('click', (e) => {
                if (!mobileMenu.contains(e.target) && !mobileMenuBtn.contains(e.target)) {
                    mobileMenuBtn.classList.remove('active');
                    mobileMenu.classList.remove('active');
                }
            });

            // Close menu when clicking a link
            document.querySelectorAll('.mobile-menu-links a').forEach(link => {
                link.addEventListener('click', () => {
                    mobileMenuBtn.classList.remove('active');
                    mobileMenu.classList.remove('active');
                });
            });
        });
    </script>

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
</body>

</html>