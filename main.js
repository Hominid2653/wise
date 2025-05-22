// Navbar scroll effect with debouncing for better performance
let scrollTimer;
window.addEventListener('scroll', () => {
    if (scrollTimer) {
        clearTimeout(scrollTimer);
    }
    
    scrollTimer = setTimeout(() => {
        const nav = document.querySelector('nav');
        if (window.scrollY > 20) {  // Reduced threshold for earlier effect
            nav.classList.add('scrolled');
        } else {
            nav.classList.remove('scrolled');
        }
    }, 10);
});

// Mobile menu toggle
const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
const navLinks = document.querySelector('.nav-links');

mobileMenuBtn.addEventListener('click', () => {
    mobileMenuBtn.classList.toggle('active');
    navLinks.classList.toggle('active');
});

// Close mobile menu when clicking outside
document.addEventListener('click', (e) => {
    if (!navLinks.contains(e.target) && !mobileMenuBtn.contains(e.target)) {
        mobileMenuBtn.classList.remove('active');
        navLinks.classList.remove('active');
    }
});

// Close mobile menu when clicking a link
navLinks.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', () => {
        mobileMenuBtn.classList.remove('active');
        navLinks.classList.remove('active');
    });
});

// Add this to your existing main.js file
document.querySelectorAll('.faq-question').forEach(button => {
    button.addEventListener('click', () => {
        const faqItem = button.parentElement;
        const isActive = faqItem.classList.contains('active');
        
        // Close all FAQ items
        document.querySelectorAll('.faq-item').forEach(item => {
            item.classList.remove('active');
        });
        
        // Open clicked item if it wasn't already open
        if (!isActive) {
            faqItem.classList.add('active');
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const categoryTags = document.querySelectorAll('.category-tag');
    const searchInput = document.querySelector('.search-bar');
    
    // Handle category clicks
    categoryTags.forEach(tag => {
        tag.addEventListener('click', async () => {
            // Update active state
            categoryTags.forEach(t => t.classList.remove('active'));
            tag.classList.add('active');
            
            // Get courses for this category
            await filterCourses(tag.textContent, searchInput.value);
        });
    });
    
    // Handle search input
    let searchTimeout;
    searchInput.addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(async () => {
            const activeCategory = document.querySelector('.category-tag.active');
            await filterCourses(activeCategory?.textContent || 'All', e.target.value);
        }, 300);
    });
});

async function filterCourses(category, search = '') {
    try {
        const params = new URLSearchParams({
            category: category,
            search: search
        });
        
        const response = await fetch(`/wise/api/filter-courses.php?${params}`);
        if (!response.ok) throw new Error('Failed to fetch courses');
        
        const data = await response.json();
        if (data.success) {
            // If we're on the home page, update the featured courses section
            const coursesTrack = document.querySelector('.courses-track');
            if (coursesTrack) {
                updateCoursesDisplay(coursesTrack, data.courses);
            }
            
            // If we're on the academy page, update the courses grid
            const coursesGrid = document.getElementById('courses-grid');
            if (coursesGrid) {
                updateCoursesDisplay(coursesGrid, data.courses);
            }
        }
    } catch (error) {
        console.error('Error filtering courses:', error);
    }
}

function updateCoursesDisplay(container, courses) {
    if (!courses.length) {
        container.innerHTML = '<p class="no-courses">No courses found matching your criteria.</p>';
        return;
    }
    
    container.innerHTML = courses.map(course => `
        <div class="course-card">
            <div class="course-image">
                <img src="${course.image_url}" alt="${course.name}">
                ${course.featured ? '<span class="featured-badge">Featured</span>' : ''}
            </div>
            <div class="course-content">
                <h3 class="course-title">${course.name}</h3>
                <p class="course-description">${course.description}</p>
                <div class="course-footer">
                    <div class="stats">
                        ${course.video_duration ? `<span>${course.video_duration}</span><span>•</span>` : ''}
                        <span>${course.duration || '12 weeks'}</span>
                        <span>•</span>
                        <span>${course.lessons_count || 0} lessons</span>
                    </div>
                    <div class="course-actions">
                        <a href="${course.url}" class="enroll-btn">Enroll Now</a>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}
