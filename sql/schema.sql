/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `agencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `agencies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_person` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `address_line1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_line2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `alerts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `alerts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `candidate_id` bigint unsigned NOT NULL,
  `alert_type` enum('visa_expiry','work_permit_expiry','contract_expiry') COLLATE utf8mb4_unicode_ci NOT NULL,
  `alert_date` date NOT NULL,
  `due_in_days` int NOT NULL,
  `is_dismissed` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_alert` (`candidate_id`,`alert_type`,`alert_date`),
  KEY `idx_alert_type_date` (`alert_type`,`alert_date`),
  CONSTRAINT `fk_alerts_candidate` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `candidate_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `candidate_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `candidate_id` bigint unsigned NOT NULL,
  `doc_type` enum('passport','cv','certificate','contract','medical','education','work_reference','police_clearance','medical_certificate','photo','video','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `size_bytes` bigint unsigned NOT NULL,
  `uploaded_by` bigint unsigned DEFAULT NULL,
  `uploaded_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_canddocs_candidate` (`candidate_id`),
  KEY `idx_canddocs_type` (`doc_type`),
  KEY `fk_canddocs_uploaded_by` (`uploaded_by`),
  CONSTRAINT `fk_canddocs_candidate` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_canddocs_uploaded_by` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=235 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `candidate_education`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `candidate_education` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `candidate_id` bigint unsigned NOT NULL,
  `institution_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `degree` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `field_of_study` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `is_current` tinyint(1) NOT NULL DEFAULT '0',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_education_candidate` (`candidate_id`),
  CONSTRAINT `fk_education_candidate` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `candidate_job_applications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `candidate_job_applications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `candidate_id` bigint unsigned NOT NULL,
  `job_post_id` bigint unsigned NOT NULL,
  `application_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('applied','shortlisted','interviewed','offered','hired','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'applied',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `applied_by` bigint unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_applications_candidate` (`candidate_id`),
  KEY `idx_applications_job` (`job_post_id`),
  KEY `idx_applications_status` (`status`),
  KEY `fk_applications_applied_by` (`applied_by`),
  CONSTRAINT `fk_applications_applied_by` FOREIGN KEY (`applied_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_applications_candidate` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_applications_job` FOREIGN KEY (`job_post_id`) REFERENCES `job_posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `candidate_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `candidate_roles` (
  `candidate_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  `assigned_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`candidate_id`,`role_id`),
  KEY `fk_cand_roles_role` (`role_id`),
  CONSTRAINT `fk_cand_roles_candidate` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_cand_roles_role` FOREIGN KEY (`role_id`) REFERENCES `job_roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `candidate_skills`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `candidate_skills` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `candidate_id` bigint unsigned NOT NULL,
  `skill_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `proficiency_level` enum('beginner','intermediate','advanced','expert') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `years_of_experience` decimal(4,1) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_skill_candidate` (`candidate_id`,`skill_name`),
  CONSTRAINT `fk_skills_candidate` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `candidate_work_experience`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `candidate_work_experience` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `candidate_id` bigint unsigned NOT NULL,
  `employer_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `is_current` tinyint(1) NOT NULL DEFAULT '0',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_workexp_candidate` (`candidate_id`),
  CONSTRAINT `fk_workexp_candidate` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=108 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `candidates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `candidates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `candidate_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dob` date DEFAULT NULL,
  `place_of_birth` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `passport_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `passport_validity` date DEFAULT NULL,
  `mothers_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fathers_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nationality` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `marital_status` enum('single','married','divorced','widowed') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `num_children` tinyint unsigned DEFAULT '0',
  `height_cm` smallint unsigned DEFAULT NULL,
  `weight_kg` decimal(5,2) unsigned DEFAULT NULL,
  `is_smoker` tinyint(1) DEFAULT '0',
  `has_drivers_license` tinyint(1) DEFAULT '0',
  `drivers_license_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `religion` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_line1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_line2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_state` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_postal_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_country` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact_relation` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact_phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact_address` text COLLATE utf8mb4_unicode_ci,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `computer_skills_level` enum('basic','intermediate','advanced') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `computer_skills_notes` text COLLATE utf8mb4_unicode_ci,
  `personal_statement` text COLLATE utf8mb4_unicode_ci,
  `profile_photo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_intro_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('basic_profile_created','profile_in_progress','ready_for_selection','shortlisted','selected','deployed','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'basic_profile_created',
  `progress_percent` tinyint NOT NULL DEFAULT '0',
  `user_id` bigint unsigned DEFAULT NULL,
  `agency_id` bigint unsigned DEFAULT NULL,
  `recruitment_destination` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_employer_id` bigint unsigned DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `candidate_code` (`candidate_code`),
  UNIQUE KEY `uq_candidates_user` (`user_id`),
  KEY `idx_candidates_status_agency` (`status`,`agency_id`),
  KEY `idx_candidates_employer` (`current_employer_id`),
  KEY `fk_candidates_agency` (`agency_id`),
  KEY `fk_candidates_created_by` (`created_by`),
  KEY `fk_candidates_updated_by` (`updated_by`),
  KEY `idx_recruitment_destination` (`recruitment_destination`),
  CONSTRAINT `fk_candidates_agency` FOREIGN KEY (`agency_id`) REFERENCES `agencies` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_candidates_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_candidates_employer` FOREIGN KEY (`current_employer_id`) REFERENCES `employers` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_candidates_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_candidates_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = cp850 */ ;
/*!50003 SET character_set_results = cp850 */ ;
/*!50003 SET collation_connection  = cp850_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50003 TRIGGER `before_candidate_update` BEFORE UPDATE ON `candidates` FOR EACH ROW BEGIN
    DECLARE completion INT DEFAULT 0;
    DECLARE preserved_status TINYINT DEFAULT 0;
    
    -- Basic info (30%)
    IF (NEW.first_name IS NOT NULL AND NEW.last_name IS NOT NULL AND 
        NEW.dob IS NOT NULL AND NEW.gender IS NOT NULL AND 
        NEW.phone IS NOT NULL AND NEW.email IS NOT NULL) THEN
        SET completion = completion + 30;
    END IF;
    
    -- Documents (40%)
    IF EXISTS (SELECT 1 FROM candidate_documents WHERE candidate_id = NEW.id AND doc_type = 'passport') THEN
        SET completion = completion + 10;
    END IF;
    
    IF EXISTS (SELECT 1 FROM candidate_documents WHERE candidate_id = NEW.id AND doc_type = 'cv') THEN
        SET completion = completion + 10;
    END IF;
    
    IF EXISTS (SELECT 1 FROM candidate_education WHERE candidate_id = NEW.id LIMIT 1) THEN
        SET completion = completion + 10;
    END IF;
    
    IF EXISTS (SELECT 1 FROM candidate_work_experience WHERE candidate_id = NEW.id LIMIT 1) THEN
        SET completion = completion + 10;
    END IF;
    
    -- Job application (15%)
    IF EXISTS (SELECT 1 FROM candidate_job_applications WHERE candidate_id = NEW.id) THEN
        SET completion = completion + 15;
    END IF;
    
    -- Employment details (15%)
    IF (NEW.current_employer_id IS NOT NULL) THEN
        SET completion = completion + 15;
    END IF;
    
    -- Ensure completion doesn't exceed 100%
    IF completion > 100 THEN
        SET completion = 100;
    END IF;
    
    -- Preserve explicitly advanced workflow stages
    IF NEW.status IN ('shortlisted','selected','deployed','completed') OR OLD.status IN ('shortlisted','selected','deployed','completed') THEN
        SET preserved_status = 1;
    END IF;
    
    -- Update status based on completion only if not in an advanced stage
    IF preserved_status = 0 THEN
        IF completion < 30 THEN
            SET NEW.status = 'basic_profile_created';
        ELSEIF completion < 70 THEN
            SET NEW.status = 'profile_in_progress';
        ELSE
            SET NEW.status = 'ready_for_selection';
        END IF;
    END IF;
    
    SET NEW.progress_percent = completion;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
DROP TABLE IF EXISTS `compliance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `compliance` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `candidate_id` bigint unsigned NOT NULL,
  `visa_expiry` date DEFAULT NULL,
  `visa_status` enum('not_applied','applied','approved') COLLATE utf8mb4_unicode_ci DEFAULT 'not_applied',
  `work_permit_expiry` date DEFAULT NULL,
  `work_permit_status` enum('not_applied','applied','approved') COLLATE utf8mb4_unicode_ci DEFAULT 'not_applied',
  `contract_expiry` date DEFAULT NULL,
  `last_checked_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_compliance_candidate` (`candidate_id`),
  KEY `idx_compliance_visa` (`visa_expiry`),
  KEY `idx_compliance_wp` (`work_permit_expiry`),
  KEY `idx_compliance_contract` (`contract_expiry`),
  CONSTRAINT `fk_compliance_candidate` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `deployments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `deployments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `candidate_id` bigint unsigned NOT NULL,
  `employer_id` bigint unsigned NOT NULL,
  `agency_id` bigint unsigned DEFAULT NULL,
  `location_country` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location_city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location_site` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `salary_amount` decimal(12,2) DEFAULT NULL,
  `salary_currency` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contract_file_id` bigint unsigned DEFAULT NULL,
  `status` enum('planned','active','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'planned',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `visa_reference_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `signed_contract_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_deployments_candidate` (`candidate_id`),
  KEY `idx_deployments_employer` (`employer_id`),
  KEY `idx_deployments_status` (`status`),
  KEY `idx_deployments_dates` (`start_date`,`end_date`),
  KEY `fk_deploy_agency` (`agency_id`),
  KEY `fk_deploy_contract` (`contract_file_id`),
  KEY `idx_deployments_visa_ref` (`visa_reference_file`),
  KEY `idx_deployments_contract` (`signed_contract_file`),
  CONSTRAINT `fk_deploy_agency` FOREIGN KEY (`agency_id`) REFERENCES `agencies` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_deploy_candidate` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_deploy_contract` FOREIGN KEY (`contract_file_id`) REFERENCES `candidate_documents` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_deploy_employer` FOREIGN KEY (`employer_id`) REFERENCES `employers` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `document_templates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `document_templates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `template_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `template_type` enum('cv_lithuania','cv_turkey','cv_generic','kandidato_anketa','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `template_category` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `template_file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` bigint unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_template_type` (`template_type`),
  KEY `idx_template_active` (`is_active`),
  KEY `fk_template_created_by` (`created_by`),
  CONSTRAINT `fk_template_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `email_verification_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `email_verification_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_evt_token` (`token`),
  KEY `idx_evt_user` (`user_id`),
  CONSTRAINT `fk_evt_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `emergency_contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `emergency_contacts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `candidate_id` bigint unsigned NOT NULL,
  `full_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `relationship` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_line1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_line2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_emcon_candidate` (`candidate_id`),
  CONSTRAINT `fk_emcon_candidate` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `employers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `site` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_person` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `generated_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `generated_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `candidate_id` bigint unsigned NOT NULL,
  `questionnaire_request_id` bigint unsigned DEFAULT NULL,
  `template_id` bigint unsigned NOT NULL,
  `template_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `destination` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size` int unsigned NOT NULL DEFAULT '0',
  `version_number` int unsigned NOT NULL DEFAULT '1',
  `generated_by` bigint unsigned DEFAULT NULL,
  `generated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_generated_candidate` (`candidate_id`),
  KEY `idx_generated_template` (`template_id`),
  KEY `idx_generated_qr` (`questionnaire_request_id`),
  KEY `idx_generated_at` (`generated_at`),
  KEY `fk_generated_user` (`generated_by`),
  CONSTRAINT `fk_generated_candidate` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_generated_qr` FOREIGN KEY (`questionnaire_request_id`) REFERENCES `questionnaire_requests` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_generated_template` FOREIGN KEY (`template_id`) REFERENCES `document_templates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_generated_user` FOREIGN KEY (`generated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `interviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `interviews` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `candidate_id` bigint unsigned NOT NULL,
  `employer_id` bigint unsigned NOT NULL,
  `job_post_id` bigint unsigned DEFAULT NULL,
  `scheduled_at` datetime NOT NULL,
  `duration_mins` int NOT NULL DEFAULT '30',
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meeting_link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('proposed','confirmed','declined','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'proposed',
  `created_by` bigint unsigned NOT NULL,
  `updated_by` bigint unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `reminder_24_sent_at` datetime DEFAULT NULL,
  `reminder_2_sent_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_interviews_candidate` (`candidate_id`),
  KEY `idx_interviews_employer` (`employer_id`),
  KEY `idx_interviews_time` (`scheduled_at`),
  KEY `idx_interviews_status` (`status`),
  KEY `fk_interviews_job` (`job_post_id`),
  KEY `fk_interviews_created_by` (`created_by`),
  KEY `fk_interviews_updated_by` (`updated_by`),
  CONSTRAINT `fk_interviews_candidate` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_interviews_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_interviews_employer` FOREIGN KEY (`employer_id`) REFERENCES `employers` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_interviews_job` FOREIGN KEY (`job_post_id`) REFERENCES `job_posts` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_interviews_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_posts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `employer_id` bigint unsigned NOT NULL,
  `title` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` enum('open','closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `openings` int NOT NULL DEFAULT '1',
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_job_posts_employer` (`employer_id`),
  KEY `idx_job_posts_status` (`status`),
  KEY `fk_job_posts_created_by` (`created_by`),
  CONSTRAINT `fk_job_posts_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_job_posts_employer` FOREIGN KEY (`employer_id`) REFERENCES `employers` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_prt_token` (`token`),
  KEY `idx_prt_user` (`user_id`),
  CONSTRAINT `fk_prt_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `questionnaire_audit_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `questionnaire_audit_log` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `questionnaire_request_id` bigint unsigned NOT NULL,
  `action_type` enum('created','sent','opened','autosave','document_upload','submitted','expired','revoked','correction_requested','status_changed','link_regenerated','approved','generated_document') COLLATE utf8mb4_unicode_ci NOT NULL,
  `action_details` text COLLATE utf8mb4_unicode_ci,
  `user_id` bigint unsigned DEFAULT NULL COMMENT 'NULL for candidate actions, user ID for admin actions',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_audit_request` (`questionnaire_request_id`),
  KEY `idx_audit_action` (`action_type`),
  KEY `idx_audit_created` (`created_at`),
  CONSTRAINT `fk_audit_request` FOREIGN KEY (`questionnaire_request_id`) REFERENCES `questionnaire_requests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `questionnaire_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `questionnaire_documents` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `questionnaire_request_id` bigint unsigned NOT NULL,
  `document_type` enum('passport','national_id','cv','academic_certificates','professional_certificates','drivers_license','reference_letter','police_clearance','medical_certificate','passport_photograph','other_document') COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `original_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size` bigint unsigned NOT NULL,
  `mime_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uploaded_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idx_qd_request` (`questionnaire_request_id`),
  KEY `idx_qd_type` (`document_type`),
  CONSTRAINT `fk_qd_request` FOREIGN KEY (`questionnaire_request_id`) REFERENCES `questionnaire_requests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `questionnaire_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `questionnaire_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `request_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `candidate_id` bigint unsigned DEFAULT NULL,
  `secure_token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recruitment_destination` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Lithuania, Turkey, Generic, etc.',
  `status` enum('link_created','link_sent','opened','in_progress','incomplete','submitted','under_review','approved','correction_required','expired','revoked','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'link_created',
  `expiry_time` datetime NOT NULL,
  `submitted_at` datetime DEFAULT NULL,
  `instructions` text COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `request_code` (`request_code`),
  UNIQUE KEY `secure_token` (`secure_token`),
  KEY `idx_token` (`secure_token`),
  KEY `idx_candidate` (`candidate_id`),
  KEY `idx_status` (`status`),
  KEY `idx_expiry` (`expiry_time`),
  KEY `fk_qr_created_by` (`created_by`),
  CONSTRAINT `fk_qr_candidate` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_qr_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `questionnaire_requirements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `questionnaire_requirements` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `questionnaire_request_id` bigint unsigned NOT NULL,
  `requirement_type` enum('personal_info','employment_info','education_info','documents','additional_info','declaration','positions','references','travel_history','declarations','health','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_type` enum('passport','national_id','cv','academic_certificates','professional_certificates','drivers_license','reference_letter','police_clearance','medical_certificate','passport_photograph','other_document') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `field_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT '1',
  `display_order` int NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_qr_request` (`questionnaire_request_id`),
  CONSTRAINT `fk_qr_req_request` FOREIGN KEY (`questionnaire_request_id`) REFERENCES `questionnaire_requests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=366 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `questionnaire_responses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `questionnaire_responses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `questionnaire_request_id` bigint unsigned NOT NULL,
  `section` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `field_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `field_value` text COLLATE utf8mb4_unicode_ci,
  `data_type` enum('text','number','date','boolean','json') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_qr_response_request` (`questionnaire_request_id`),
  KEY `idx_qr_response_section` (`questionnaire_request_id`,`section`),
  CONSTRAINT `fk_qr_response_request` FOREIGN KEY (`questionnaire_request_id`) REFERENCES `questionnaire_requests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `template_field_mappings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `template_field_mappings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `template_id` bigint unsigned NOT NULL,
  `template_field_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `database_source` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `source_type` enum('candidate','questionnaire','static') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'candidate',
  `static_value` text COLLATE utf8mb4_unicode_ci,
  `field_order` int NOT NULL DEFAULT '0',
  `is_required` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_mapping_template` (`template_id`),
  CONSTRAINT `fk_mapping_template` FOREIGN KEY (`template_id`) REFERENCES `document_templates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','recruiter','employer','candidate') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'admin',
  `employer_id` bigint DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `vw_candidate_compliance`;
/*!50001 DROP VIEW IF EXISTS `vw_candidate_compliance`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_candidate_compliance` AS SELECT 
 1 AS `candidate_id`,
 1 AS `candidate_name`,
 1 AS `passport_number`,
 1 AS `passport_validity`,
 1 AS `visa_expiry`,
 1 AS `visa_status`,
 1 AS `work_permit_expiry`,
 1 AS `work_permit_status`,
 1 AS `contract_expiry`,
 1 AS `deployment_start_date`,
 1 AS `deployment_end_date`,
 1 AS `employer_name`,
 1 AS `agency_name`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `vw_candidate_profiles`;
/*!50001 DROP VIEW IF EXISTS `vw_candidate_profiles`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_candidate_profiles` AS SELECT 
 1 AS `id`,
 1 AS `candidate_code`,
 1 AS `first_name`,
 1 AS `last_name`,
 1 AS `dob`,
 1 AS `place_of_birth`,
 1 AS `passport_number`,
 1 AS `passport_validity`,
 1 AS `mothers_name`,
 1 AS `fathers_name`,
 1 AS `phone`,
 1 AS `email`,
 1 AS `nationality`,
 1 AS `gender`,
 1 AS `marital_status`,
 1 AS `num_children`,
 1 AS `height_cm`,
 1 AS `weight_kg`,
 1 AS `is_smoker`,
 1 AS `has_drivers_license`,
 1 AS `drivers_license_number`,
 1 AS `religion`,
 1 AS `address_line1`,
 1 AS `address_line2`,
 1 AS `city`,
 1 AS `state`,
 1 AS `postal_code`,
 1 AS `country`,
 1 AS `computer_skills_level`,
 1 AS `computer_skills_notes`,
 1 AS `personal_statement`,
 1 AS `profile_photo_path`,
 1 AS `video_intro_path`,
 1 AS `status`,
 1 AS `progress_percent`,
 1 AS `user_id`,
 1 AS `agency_id`,
 1 AS `current_employer_id`,
 1 AS `created_by`,
 1 AS `updated_by`,
 1 AS `created_at`,
 1 AS `updated_at`,
 1 AS `full_name`,
 1 AS `agency_name`,
 1 AS `employer_name`,
 1 AS `user_email`,
 1 AS `skills`,
 1 AS `application_count`,
 1 AS `document_count`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `vw_questionnaire_dashboard`;
/*!50001 DROP VIEW IF EXISTS `vw_questionnaire_dashboard`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_questionnaire_dashboard` AS SELECT 
 1 AS `id`,
 1 AS `request_code`,
 1 AS `position`,
 1 AS `recruitment_destination`,
 1 AS `status`,
 1 AS `expiry_time`,
 1 AS `created_at`,
 1 AS `candidate_name`,
 1 AS `candidate_email`,
 1 AS `candidate_phone`,
 1 AS `created_by_user`,
 1 AS `document_count`,
 1 AS `response_count`,
 1 AS `progress_status`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `vw_questionnaire_requirements_status`;
/*!50001 DROP VIEW IF EXISTS `vw_questionnaire_requirements_status`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_questionnaire_requirements_status` AS SELECT 
 1 AS `questionnaire_request_id`,
 1 AS `request_code`,
 1 AS `requirement_type`,
 1 AS `document_type`,
 1 AS `field_name`,
 1 AS `is_required`,
 1 AS `completion_status`*/;
SET character_set_client = @saved_cs_client;
/*!50001 DROP VIEW IF EXISTS `vw_candidate_compliance`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 SQL SECURITY INVOKER */
/*!50001 VIEW `vw_candidate_compliance` AS select `c`.`id` AS `candidate_id`,concat(`c`.`first_name`,' ',`c`.`last_name`) AS `candidate_name`,`c`.`passport_number` AS `passport_number`,`c`.`passport_validity` AS `passport_validity`,`co`.`visa_expiry` AS `visa_expiry`,`co`.`visa_status` AS `visa_status`,`co`.`work_permit_expiry` AS `work_permit_expiry`,`co`.`work_permit_status` AS `work_permit_status`,`co`.`contract_expiry` AS `contract_expiry`,`d`.`start_date` AS `deployment_start_date`,`d`.`end_date` AS `deployment_end_date`,`e`.`name` AS `employer_name`,`a`.`name` AS `agency_name` from ((((`candidates` `c` left join `compliance` `co` on((`c`.`id` = `co`.`candidate_id`))) left join `deployments` `d` on(((`c`.`id` = `d`.`candidate_id`) and (`d`.`status` = 'active')))) left join `employers` `e` on((`d`.`employer_id` = `e`.`id`))) left join `agencies` `a` on(((`d`.`agency_id` = `a`.`id`) or (`c`.`agency_id` = `a`.`id`)))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `vw_candidate_profiles`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 SQL SECURITY INVOKER */
/*!50001 VIEW `vw_candidate_profiles` AS select `c`.`id` AS `id`,`c`.`candidate_code` AS `candidate_code`,`c`.`first_name` AS `first_name`,`c`.`last_name` AS `last_name`,`c`.`dob` AS `dob`,`c`.`place_of_birth` AS `place_of_birth`,`c`.`passport_number` AS `passport_number`,`c`.`passport_validity` AS `passport_validity`,`c`.`mothers_name` AS `mothers_name`,`c`.`fathers_name` AS `fathers_name`,`c`.`phone` AS `phone`,`c`.`email` AS `email`,`c`.`nationality` AS `nationality`,`c`.`gender` AS `gender`,`c`.`marital_status` AS `marital_status`,`c`.`num_children` AS `num_children`,`c`.`height_cm` AS `height_cm`,`c`.`weight_kg` AS `weight_kg`,`c`.`is_smoker` AS `is_smoker`,`c`.`has_drivers_license` AS `has_drivers_license`,`c`.`drivers_license_number` AS `drivers_license_number`,`c`.`religion` AS `religion`,`c`.`address_line1` AS `address_line1`,`c`.`address_line2` AS `address_line2`,`c`.`city` AS `city`,`c`.`state` AS `state`,`c`.`postal_code` AS `postal_code`,`c`.`country` AS `country`,`c`.`computer_skills_level` AS `computer_skills_level`,`c`.`computer_skills_notes` AS `computer_skills_notes`,`c`.`personal_statement` AS `personal_statement`,`c`.`profile_photo_path` AS `profile_photo_path`,`c`.`video_intro_path` AS `video_intro_path`,`c`.`status` AS `status`,`c`.`progress_percent` AS `progress_percent`,`c`.`user_id` AS `user_id`,`c`.`agency_id` AS `agency_id`,`c`.`current_employer_id` AS `current_employer_id`,`c`.`created_by` AS `created_by`,`c`.`updated_by` AS `updated_by`,`c`.`created_at` AS `created_at`,`c`.`updated_at` AS `updated_at`,concat(`c`.`first_name`,' ',`c`.`last_name`) AS `full_name`,`a`.`name` AS `agency_name`,`e`.`name` AS `employer_name`,`u`.`email` AS `user_email`,(select group_concat(distinct `cs`.`skill_name` separator ', ') from `candidate_skills` `cs` where (`cs`.`candidate_id` = `c`.`id`)) AS `skills`,(select count(0) from `candidate_job_applications` `ca` where (`ca`.`candidate_id` = `c`.`id`)) AS `application_count`,(select count(0) from `candidate_documents` `cd` where (`cd`.`candidate_id` = `c`.`id`)) AS `document_count` from (((`candidates` `c` left join `agencies` `a` on((`c`.`agency_id` = `a`.`id`))) left join `employers` `e` on((`c`.`current_employer_id` = `e`.`id`))) left join `users` `u` on((`c`.`user_id` = `u`.`id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `vw_questionnaire_dashboard`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = cp850 */;
/*!50001 SET character_set_results     = cp850 */;
/*!50001 SET collation_connection      = cp850_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 SQL SECURITY INVOKER */
/*!50001 VIEW `vw_questionnaire_dashboard` AS select `qr`.`id` AS `id`,`qr`.`request_code` AS `request_code`,`qr`.`position` AS `position`,`qr`.`recruitment_destination` AS `recruitment_destination`,`qr`.`status` AS `status`,`qr`.`expiry_time` AS `expiry_time`,`qr`.`created_at` AS `created_at`,concat(`c`.`first_name`,' ',`c`.`last_name`) AS `candidate_name`,`c`.`email` AS `candidate_email`,`c`.`phone` AS `candidate_phone`,`u`.`username` AS `created_by_user`,count(distinct `qd`.`id`) AS `document_count`,count(distinct `qr_resp`.`id`) AS `response_count`,(case when (`qr`.`status` = 'submitted') then 'Completed' when (`qr`.`expiry_time` < now()) then 'Expired' when (`qr`.`status` = 'link_created') then 'Not Started' else 'In Progress' end) AS `progress_status` from ((((`questionnaire_requests` `qr` left join `candidates` `c` on((`qr`.`candidate_id` = `c`.`id`))) left join `users` `u` on((`qr`.`created_by` = `u`.`id`))) left join `questionnaire_documents` `qd` on(((`qr`.`id` = `qd`.`questionnaire_request_id`) and (`qd`.`is_active` = 1)))) left join `questionnaire_responses` `qr_resp` on((`qr`.`id` = `qr_resp`.`questionnaire_request_id`))) group by `qr`.`id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `vw_questionnaire_requirements_status`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = cp850 */;
/*!50001 SET character_set_results     = cp850 */;
/*!50001 SET collation_connection      = cp850_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 SQL SECURITY INVOKER */
/*!50001 VIEW `vw_questionnaire_requirements_status` AS select `qr`.`id` AS `questionnaire_request_id`,`qr`.`request_code` AS `request_code`,`qreq`.`requirement_type` AS `requirement_type`,`qreq`.`document_type` AS `document_type`,`qreq`.`field_name` AS `field_name`,`qreq`.`is_required` AS `is_required`,(case when (`qreq`.`document_type` is not null) then (case when exists(select 1 from `questionnaire_documents` `qd` where ((`qd`.`questionnaire_request_id` = `qr`.`id`) and (`qd`.`document_type` = `qreq`.`document_type`) and (`qd`.`is_active` = 1))) then 'uploaded' else 'missing' end) when (`qreq`.`field_name` is not null) then (case when exists(select 1 from `questionnaire_responses` `qr_resp` where ((`qr_resp`.`questionnaire_request_id` = `qr`.`id`) and (`qr_resp`.`field_name` = `qreq`.`field_name`) and (`qr_resp`.`field_value` is not null) and (`qr_resp`.`field_value` <> ''))) then 'completed' else 'missing' end) else 'not_applicable' end) AS `completion_status` from (`questionnaire_requests` `qr` join `questionnaire_requirements` `qreq` on((`qr`.`id` = `qreq`.`questionnaire_request_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

