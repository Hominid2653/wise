CREATE TABLE IF NOT EXISTS `course_settings` (
    `course_id` VARCHAR(50) NOT NULL,
    `title` VARCHAR(255),
    `subtitle` VARCHAR(255),
    `short_description` TEXT,
    `price` DECIMAL(10,2),
    `duration` VARCHAR(50),
    `level` VARCHAR(50),
    `category` ENUM(
        'Professional',
        'Position',
        'Skill',
        'Patient Education',
        'AI',
        'Software',
        'Exam Prep',
        'Technology'
    ) DEFAULT NULL,
    `thumbnail` VARCHAR(255),
    `status` ENUM('published', 'draft', 'hidden') DEFAULT 'draft',
    `featured` BOOLEAN DEFAULT FALSE,
    `display_order` INT DEFAULT 0,
    `custom_description` TEXT,
    `thinkific_status` VARCHAR(50),
    `is_locally_edited` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `last_synced` TIMESTAMP NULL DEFAULT NULL,
    `lessons_count` INT DEFAULT 0,
    `video_duration` VARCHAR(50) DEFAULT NULL,
    `url_slug` VARCHAR(255),
    PRIMARY KEY (`course_id`),
    UNIQUE INDEX `idx_url_slug` (`url_slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; 

ALTER TABLE `course_settings` 
ADD COLUMN `is_locally_edited` BOOLEAN DEFAULT FALSE AFTER `thinkific_status`; 

CREATE TABLE IF NOT EXISTS `thinkific_courses` (
    `course_id` VARCHAR(50) NOT NULL,
    `name` VARCHAR(255),
    `description` TEXT,
    `image_url` VARCHAR(255),
    `preview_url` VARCHAR(255),
    `enrollment_url` VARCHAR(255),
    `lessons_count` INT DEFAULT 0,
    `status` VARCHAR(50),
    `last_synced` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; 

-- Drop the category-related tables if they exist
DROP TABLE IF EXISTS `course_category_relations`;
DROP TABLE IF EXISTS `course_categories`;

-- Create instructors table
CREATE TABLE IF NOT EXISTS `course_instructors` (
    `instructor_id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `title` VARCHAR(100),
    `bio` TEXT,
    `avatar_url` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create course-instructor relationship table
CREATE TABLE IF NOT EXISTS `course_instructor_relations` (
    `course_id` VARCHAR(50),
    `instructor_id` INT,
    PRIMARY KEY (`course_id`, `instructor_id`),
    FOREIGN KEY (`course_id`) REFERENCES `course_settings`(`course_id`) ON DELETE CASCADE,
    FOREIGN KEY (`instructor_id`) REFERENCES `course_instructors`(`instructor_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; 

-- Add url_slug column if it doesn't exist
ALTER TABLE `course_settings` 
ADD COLUMN IF NOT EXISTS `url_slug` VARCHAR(255) AFTER `title`,
ADD UNIQUE INDEX IF NOT EXISTS `idx_url_slug` (`url_slug`); 