-- Migration: Add gym_id to role_applications table
-- This links employee role applications to a specific registered gym.

ALTER TABLE `role_applications`
    ADD COLUMN `gym_id` INT(10) UNSIGNED DEFAULT NULL AFTER `membership_form_data`,
    ADD CONSTRAINT `fk_role_applications_gym`
        FOREIGN KEY (`gym_id`) REFERENCES `gym_owner_applications` (`id`)
        ON DELETE SET NULL ON UPDATE CASCADE;
