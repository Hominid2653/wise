class ThinkificAPI {
    constructor(isAdmin = false) {
        this.apiKey = '6a22f5a9764c087aaee2661c8e38c944';
        this.subdomain = 'wissen-s-site';
        this.baseURL = `https://${this.subdomain}.thinkific.com/api/public/v1`;
        this.courses = []; // Store fetched courses
        this.isAdmin = isAdmin;
        this.initializeSearch();
    }

    async fetchCourses() {
        try {
            // In public mode, only fetch published courses
            const url = this.isAdmin 
                ? `${this.baseURL}/courses?page=1&limit=100&include[]=reviews&include[]=products`
                : `${this.baseURL}/courses?page=1&limit=100&include[]=reviews&include[]=products&status=published`;

            const response = await fetch(url, {
                headers: {
                    'X-Auth-API-Key': this.apiKey,
                    'X-Auth-Subdomain': this.subdomain,
                    'Content-Type': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('Failed to fetch courses');
            }

            const data = await response.json();
            console.log('Raw Thinkific courses:', data.items); // Debug log

            return data.items.map(course => {
                const product = course.products?.[0] || {};
                const mappedCourse = {
                    id: course.id.toString(), // Convert ID to string
                    name: course.name,
                    description: course.description,
                    category: course.category?.name,
                    image_url: course.course_card_image_url || course.image_url,
                    price: product.price || 0,
                    sale_price: product.sale_price,
                    duration: this.formatDuration(course.duration),
                    lessons_count: course.chapter_count || 0,
                    student_count: course.student_count || 0,
                    reviews_count: course.reviews?.length || 0,
                    average_rating: this.calculateAverageRating(course.reviews),
                    preview_url: course.preview_url || `https://${this.subdomain}.thinkific.com/courses/${course.id}/preview`,
                    enrollment_url: course.enrollment_url || `https://${this.subdomain}.thinkific.com/courses/${course.id}/enroll`,
                    url: `https://${this.subdomain}.thinkific.com/courses/${course.id}`,
                    status: course.status || 'draft'
                };
                console.log('Mapped course:', mappedCourse); // Debug log
                return mappedCourse;
            });
        } catch (error) {
            console.error('Error fetching courses:', error);
            return [];
        }
    }

    formatDuration(minutes) {
        if (!minutes) return '12 weeks'; // Default duration
        
        const weeks = Math.ceil(minutes / (7 * 24 * 60)); // Convert minutes to weeks
        return `${weeks} week${weeks > 1 ? 's' : ''}`;
    }

    calculateAverageRating(reviews) {
        if (!reviews || reviews.length === 0) return 5; // Default rating
        
        const total = reviews.reduce((sum, review) => sum + review.rating, 0);
        return (total / reviews.length).toFixed(1);
    }

    formatPrice(price) {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD'
        }).format(price);
    }

    createCourseCard(course) {
        const formattedPrice = this.formatPrice(course.price);
        const stars = '★'.repeat(Math.round(course.average_rating)) + 
                     '☆'.repeat(5 - Math.round(course.average_rating));

        return `
            <div class="course-card">
                <div class="course-image">
                    <img src="${course.image_url}" alt="${course.name}">
                    <div class="course-overlay">
                        <span class="badge">${course.category || 'Featured'}</span>
                        <span class="price">${formattedPrice}</span>
                        <div class="hover-content">
                            ${course.preview_url ? 
                                `<a href="${course.preview_url}" class="preview-btn" target="_blank">Preview Course</a>` :
                                `<a href="${course.enrollment_url}" class="preview-btn">Enroll Now</a>`
                            }
                        </div>
                    </div>
                </div>
                <div class="course-content">
                    <div class="course-meta">
                        <div class="course-category">
                            <img src="assets/images/icons/education.svg" alt="Education">
                            <span>${course.category || 'Education'}</span>
                        </div>
                        <div class="course-rating">
                            <span class="stars">${stars}</span>
                            <span class="count">(${course.reviews_count})</span>
                        </div>
                    </div>
                    <h3>${course.name}</h3>
                    <p>${course.description}</p>
                    <div class="course-footer">
                        <div class="stats">
                            <span>${course.duration}</span>
                            <span>•</span>
                            <span>${course.lessons_count} lessons</span>
                            <span>•</span>
                            <span>${course.student_count} students</span>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    async initializeCourses() {
        const coursesGrid = document.getElementById('courses-grid');
        if (!coursesGrid) return;

        coursesGrid.innerHTML = '<div class="loading-spinner">Loading courses...</div>';

        try {
            this.courses = await this.fetchCourses(); // Store courses
            if (this.courses.length === 0) {
                coursesGrid.innerHTML = '<p class="no-courses">No courses available at the moment.</p>';
                return;
            }

            this.renderCourses(this.courses);
        } catch (error) {
            coursesGrid.innerHTML = '<p class="error-message">Failed to load courses. Please try again later.</p>';
        }
    }

    renderCourses(courses) {
        const coursesGrid = document.getElementById('courses-grid');
        if (courses.length === 0) {
            coursesGrid.innerHTML = '<p class="no-courses">No courses match your search.</p>';
            return;
        }
        const courseCards = courses.map(course => this.createCourseCard(course)).join('');
        coursesGrid.innerHTML = courseCards;
    }

    initializeSearch() {
        const searchBar = document.querySelector('.search-bar');
        if (!searchBar) return;

        searchBar.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase().trim();
            this.searchCourses(searchTerm);
        });

        // Add category filter functionality
        const categoryTags = document.querySelectorAll('.category-tag');
        categoryTags.forEach(tag => {
            tag.addEventListener('click', () => {
                const category = tag.textContent.trim();
                // Remove active class from all tags
                categoryTags.forEach(t => t.classList.remove('active'));
                // Add active class to clicked tag
                tag.classList.add('active');
                
                if (category === 'All') {
                    this.renderCourses(this.courses);
                } else {
                    const filtered = this.courses.filter(course => 
                        course.category?.toLowerCase() === category.toLowerCase()
                    );
                    this.renderCourses(filtered);
                }
            });
        });
    }

    searchCourses(searchTerm) {
        if (!searchTerm) {
            this.renderCourses(this.courses);
            return;
        }

        const filtered = this.courses.filter(course => {
            return (
                course.name.toLowerCase().includes(searchTerm) ||
                course.description.toLowerCase().includes(searchTerm) ||
                course.category?.toLowerCase().includes(searchTerm)
            );
        });

        this.renderCourses(filtered);
    }
} 