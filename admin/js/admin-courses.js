class AdminCoursesManager {
    constructor() {
        this.thinkific = new ThinkificAPI(true);
        this.courses = [];
        this.currentPage = 1;
        this.coursesPerPage = 18;
        this.currentFilter = 'all';
        this.init();
    }

    async loadCourses() {
        try {
            const coursesGrid = document.getElementById('courses-grid');
            coursesGrid.innerHTML = '<div class="loading-spinner">Loading courses...</div>';

            // Fetch courses from Thinkific
            const thinkificCourses = await this.thinkific.fetchCourses();
            console.log('Thinkific courses:', thinkificCourses); // Debug log
            
            // Fetch local settings
            const response = await fetch('/wise/api/courses.php');
            const localSettings = await response.json();
            console.log('Local settings:', localSettings); // Debug log
            
            // Merge Thinkific data with local settings
            this.courses = thinkificCourses.map(course => {
                const merged = {
                    ...course,
                    ...localSettings[course.id],
                    status: localSettings[course.id]?.status || 'draft',
                    isLocallyEdited: localSettings[course.id]?.isLocallyEdited || false,
                    featured: localSettings[course.id]?.featured || false,
                    displayOrder: localSettings[course.id]?.displayOrder || 0
                };
                console.log('Merged course:', merged); // Debug log
                return merged;
            }).sort((a, b) => {
                // First sort by featured status
                if (a.featured && !b.featured) return -1;
                if (!a.featured && b.featured) return 1;
                
                // Then sort by creation date (newest first)
                const dateA = new Date(a.created_at || 0);
                const dateB = new Date(b.created_at || 0);
                return dateB - dateA;
            });

            this.renderCourses(this.courses);
            // Apply current filter after loading
            if (this.currentFilter !== 'all') {
                this.filterByStatus(this.currentFilter);
            }
        } catch (error) {
            console.error('Error loading courses:', error);
            document.getElementById('courses-grid').innerHTML = 
                '<p class="error-message">Failed to load courses. Please try again.</p>';
        }
    }

    renderCourses(courses) {
        const coursesGrid = document.getElementById('courses-grid');
        if (courses.length === 0) {
            coursesGrid.innerHTML = '<p class="no-courses">No courses found.</p>';
            this.renderPagination(0);
            return;
        }

        // Update filter counts
        const counts = {
            all: this.courses.length,
            published: this.courses.filter(c => c.status === 'published' && c.isLocallyEdited).length,
            draft: this.courses.filter(c => c.status === 'draft').length,
            hidden: this.courses.filter(c => c.status === 'hidden').length
        };

        document.querySelectorAll('.filter-btn').forEach(btn => {
            const status = btn.dataset.status;
            btn.querySelector('.count').textContent = counts[status] || 0;
        });

        // Calculate pagination
        const startIndex = (this.currentPage - 1) * this.coursesPerPage;
        const endIndex = startIndex + this.coursesPerPage;
        const paginatedCourses = courses.slice(startIndex, endIndex);

        // Render course cards
        coursesGrid.innerHTML = paginatedCourses.map(course => this.createAdminCourseCard(course)).join('');
        
        // Add click handlers to edit buttons after rendering
        coursesGrid.querySelectorAll('.edit-course-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const courseId = btn.dataset.courseId;
                console.log('Clicked course ID:', courseId); // Debug log
                console.log('Available courses:', this.courses); // Debug log
                const course = this.courses.find(c => c.id === courseId);
                console.log('Found course:', course); // Debug log
                this.openEditModal(courseId);
            });
        });

        // Render pagination
        this.renderPagination(courses.length);
    }

    createAdminCourseCard(course) {
        const statusClass = course.status || 'draft';
        const statusText = course.isLocallyEdited 
            ? `${course.status} (Local)` 
            : `${course.thinkificStatus} (Thinkific)`;

        return `
            <div class="admin-course-card ${course.status}">
                <div class="course-image">
                    <img src="${course.image_url}" alt="${course.name}">
                    <span class="course-status ${course.status}">${statusText}</span>
                </div>
                <div class="course-content">
                    <div class="course-header">
                        <h3 class="course-title">${course.name}</h3>
                        ${course.categories ? `
                            <div class="course-categories">
                                ${course.categories.map(cat => `
                                    <span class="category-tag">${cat}</span>
                                `).join('')}
                            </div>
                        ` : ''}
                    </div>
                    <div class="course-meta">
                        <div class="meta-item">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            ${course.duration || 'No duration'}
                        </div>
                        <div class="meta-item">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            ${course.lessons_count} lessons
                        </div>
                        <div class="meta-item">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            ${course.student_count} students
                        </div>
                        ${course.featured ? `
                            <div class="meta-item">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                </svg>
                                Featured
                            </div>
                        ` : ''}
                    </div>
                    <p class="course-description">${course.customDescription || course.description}</p>
                    ${course.instructor ? `
                        <div class="course-instructor">
                            <span class="instructor-name">${course.instructor.name}</span>
                            <span class="instructor-title">${course.instructor.title}</span>
                        </div>
                    ` : ''}
                    <div class="course-footer">
                        <span class="course-price">${course.price ? `$${course.price}` : 'Free'}</span>
                        <div class="course-actions">
                            <div class="action-buttons">
                                <button class="edit-course-btn" data-course-id="${course.id}">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Edit
                                </button>
                                <a href="${course.preview_url || course.url}" target="_blank" class="preview-btn" title="Preview Course">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Preview
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    async syncCourses() {
        try {
            const courses = await this.thinkific.fetchCourses();
            
            const response = await fetch('/wise/api/courses.php?action=sync', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ courses })
            });

            if (!response.ok) throw new Error('Failed to sync courses');
            
            await this.loadCourses();
        } catch (error) {
            console.error('Error syncing courses:', error);
            alert('Failed to sync courses. Please try again.');
        }
    }

    filterCourses(searchTerm) {
        // Reset to first page when searching
        this.currentPage = 1;

        // If no search term, show all courses
        if (!searchTerm) {
            this.renderCourses(this.courses);
            return;
        }

        const filtered = this.courses.filter(course => {
            const search = searchTerm.toLowerCase();
            
            // Search across multiple fields
            return (
                // Title and description
                (course.name?.toLowerCase().includes(search)) ||
                (course.description?.toLowerCase().includes(search)) ||
                (course.subtitle?.toLowerCase().includes(search)) ||
                (course.customDescription?.toLowerCase().includes(search)) ||
                
                // Category
                (course.category?.toLowerCase().includes(search)) ||
                
                // Instructor
                (course.instructor?.name?.toLowerCase().includes(search)) ||
                (course.instructor?.title?.toLowerCase().includes(search)) ||
                
                // Level and duration
                (course.level?.toLowerCase().includes(search)) ||
                (course.duration?.toLowerCase().includes(search)) ||
                
                // Price (convert to string for search)
                (course.price?.toString().includes(search)) ||
                
                // Status
                (course.status?.toLowerCase().includes(search))
            );
        });

        this.renderCourses(filtered);
    }

    filterByStatus(status) {
        this.currentFilter = status;
        this.currentPage = 1;

        let filtered;
        if (status === 'all') {
            filtered = this.courses;
        } else if (status === 'published') {
            filtered = this.courses.filter(course => 
                course.status === 'published' && course.isLocallyEdited
            );
        } else {
            filtered = this.courses.filter(course => course.status === status);
        }
        this.renderCourses(filtered);
    }

    openEditModal(courseId) {
        const course = this.courses.find(c => c.id === courseId);
        if (!course) {
            console.error('Course not found:', courseId);
            return;
        }

        // Populate form fields
        document.getElementById('course-id').value = course.id;
        document.getElementById('course-title').value = course.title || '';
        document.getElementById('course-subtitle').value = course.subtitle || '';
        document.getElementById('course-description').value = course.shortDescription || '';
        document.getElementById('course-price').value = course.price || 0;
        document.getElementById('course-duration').value = course.duration || '';
        document.getElementById('course-level').value = course.level || 'Beginner';
        document.getElementById('course-category').value = course.category || '';
        document.getElementById('course-status').value = course.status || 'draft';
        document.getElementById('course-featured').checked = course.featured || false;
        document.getElementById('course-order').value = course.displayOrder || 0;
        document.getElementById('course-custom-description').value = course.customDescription || '';
        document.getElementById('course-lessons').value = course.lessonsCount || 0;
        document.getElementById('course-video-duration').value = course.videoDuration || '';
        document.getElementById('course-instructor').value = course.instructor?.name || '';
        document.getElementById('course-instructor-title').value = course.instructor?.title || '';

        // Show modal
        document.getElementById('course-modal').classList.add('active');
    }

    closeModal() {
        document.getElementById('course-modal').classList.remove('active');
    }

    async saveChanges(event) {
        event.preventDefault();
        
        try {
            const courseId = document.getElementById('course-id').value;
            const title = document.getElementById('course-title').value;
            let courseUrl = document.getElementById('course-url').value;

            // Validate URL format if provided
            if (courseUrl && !courseUrl.startsWith('https://courses.wissenwelle.com/products/courses/')) {
                throw new Error('Invalid course URL format. Please use the full Thinkific course URL.');
            }

            const updates = {
                title: title,
                url: courseUrl,
                subtitle: document.getElementById('course-subtitle').value,
                shortDescription: document.getElementById('course-description').value,
                price: parseFloat(document.getElementById('course-price').value) || 0,
                duration: document.getElementById('course-duration').value,
                level: document.getElementById('course-level').value,
                category: document.getElementById('course-category').value,
                status: document.getElementById('course-status').value,
                featured: document.getElementById('course-featured').checked,
                displayOrder: parseInt(document.getElementById('course-order').value) || 0,
                customDescription: document.getElementById('course-custom-description').value,
                lessonsCount: parseInt(document.getElementById('course-lessons').value) || 0,
                videoDuration: document.getElementById('course-video-duration').value,
                instructor: {
                    name: document.getElementById('course-instructor').value.trim(),
                    title: document.getElementById('course-instructor-title').value.trim()
                }
            };

            console.log('Saving changes:', { courseId, updates });

            const response = await fetch('/wise/api/courses.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ courseId, updates })
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.error || 'Failed to save changes');
            }

            if (!data.success) {
                throw new Error(data.error || 'Operation failed');
            }

            await this.loadCourses();
            this.closeModal();
            this.showNotification('Changes saved successfully!', 'success');

        } catch (error) {
            console.error('Error saving changes:', error);
            this.showNotification(error.message, 'error');
        }
    }

    showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Remove notification after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    renderPagination(totalCourses) {
        const totalPages = Math.ceil(totalCourses / this.coursesPerPage);
        const pageNumbers = document.querySelector('.page-numbers');
        const prevBtn = document.querySelector('.prev-page');
        const nextBtn = document.querySelector('.next-page');

        // Update prev/next buttons
        prevBtn.disabled = this.currentPage === 1;
        nextBtn.disabled = this.currentPage === totalPages;

        // Generate page numbers
        let pageNumbersHTML = '';
        for (let i = 1; i <= totalPages; i++) {
            pageNumbersHTML += `
                <button class="page-number ${i === this.currentPage ? 'active' : ''}" 
                        data-page="${i}">
                    ${i}
                </button>
            `;
        }
        pageNumbers.innerHTML = pageNumbersHTML;

        // Add click handlers
        pageNumbers.querySelectorAll('.page-number').forEach(btn => {
            btn.addEventListener('click', () => {
                this.currentPage = parseInt(btn.dataset.page);
                this.renderCourses(this.courses);
            });
        });
    }

    init() {
        // Check if we're on the admin page
        const isAdminPage = document.querySelector('.admin-body');
        if (!isAdminPage) {
            console.error('Not on admin page');
            return;
        }

        // Verify all required elements exist
        const requiredElements = [
            'sync-courses',
            'course-search',
            'course-modal',
            'course-edit-form',
            'courses-grid'
        ];

        const missingElements = requiredElements.filter(id => !document.getElementById(id));
        if (missingElements.length > 0) {
            console.error('Missing required elements:', missingElements);
            return;
        }

        // Setup search functionality
        const searchInput = document.getElementById('course-search');
        const searchContainer = searchInput.parentElement;
        
        // Add clear button to search
        searchContainer.insertAdjacentHTML('beforeend', `
            <button class="clear-search" type="button">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        `);

        // Setup search event listeners
        let searchTimeout;
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.filterCourses(e.target.value.trim());
            }, 300);
        });

        // Setup clear button
        const clearButton = searchContainer.querySelector('.clear-search');
        clearButton.addEventListener('click', () => {
            searchInput.value = '';
            this.filterCourses('');
        });

        // Sync courses button
        document.getElementById('sync-courses').addEventListener('click', () => this.syncCourses());

        // Filter buttons
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                const status = btn.dataset.status;
                this.filterByStatus(status);
            });
        });

        // Modal event listeners
        document.querySelector('.close-modal').addEventListener('click', () => this.closeModal());
        document.querySelector('.cancel-btn').addEventListener('click', () => this.closeModal());
        document.getElementById('course-edit-form').addEventListener('submit', (e) => this.saveChanges(e));

        // Add pagination event listeners
        document.querySelector('.prev-page').addEventListener('click', () => {
            if (this.currentPage > 1) {
                this.currentPage--;
                this.renderCourses(this.courses);
            }
        });

        document.querySelector('.next-page').addEventListener('click', () => {
            const totalPages = Math.ceil(this.courses.length / this.coursesPerPage);
            if (this.currentPage < totalPages) {
                this.currentPage++;
                this.renderCourses(this.courses);
            }
        });

        // Load courses initially
        this.loadCourses();
    }
}

// Initialize and make it globally available
window.adminCoursesManager = new AdminCoursesManager();

// Add this helper function
function generateUrlSlug(title) {
    return title.toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
} 