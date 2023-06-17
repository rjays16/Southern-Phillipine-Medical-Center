-- Created by Matsuu 

DELIMITER $$

USE `hisdb`$$

DROP TRIGGER /*!50032 IF EXISTS */ `tg_care_personell_au`$$

CREATE
    /*!50017 DEFINER = 'root'@'localhost' */
    TRIGGER `tg_care_personell_au` AFTER UPDATE ON `care_personell` 
    FOR EACH ROW BEGIN
  
  SET @c_field = '';
  SET @old_val = '';
  SET @new_val = '';
  SET @CHECK = '';
  SET @UUID = UUID();
  SET @user = USER();
  SET @mod = (SELECT login_id
    FROM `care_users`
    WHERE NAME=NEW.modify_id LIMIT 1);
  IF (NEW.job_type_nr <> OLD.job_type_nr) THEN
    SET @CHECK = '1';
    SET @c_field = CONCAT(@c_field, 'Job Type', '+');
    
    IF (OLD.job_type_nr = '') THEN
      SET @old_val = CONCAT(@old_val, '[BLANK]', '+');
    ELSE
      SET @old_val = CONCAT(@old_val, (SELECT NAME FROM care_type_duty WHERE type_nr = OLD.job_type_nr), '+');
    END IF;
    
    IF (NEW.job_type_nr = '') THEN
      SET @new_val = CONCAT(@new_val, '[BLANK]', '+');
    ELSE
      SET @new_val = CONCAT(@new_val, (SELECT NAME FROM care_type_duty WHERE type_nr = NEW.job_type_nr),'+');
    END IF;  
  
  END IF;
  IF (ISNULL(OLD.job_function_title)) AND (NEW.job_function_title <> '') THEN
    
    IF (ISNULL(OLD.job_function_title) <> NEW.job_function_title) THEN
        SET @CHECK = '1';    
        SET @c_field = CONCAT(@c_field , 'Job Function','+');
      
      IF (ISNULL(OLD.job_function_title)) THEN 
        SET @old_val = CONCAT(@old_val , '[BLANK]','+');
      ELSE 
        SET @old_val = CONCAT(@old_val , OLD.job_function_title,'+');
      END IF;
      
      IF (ISNULL(NEW.job_function_title)) THEN
        SET @new_val = CONCAT(@new_val , '[BLANK]','+');
      ELSE
        SET @new_val = CONCAT(@new_val , NEW.job_function_title,'+');
      END IF;
    
    END IF;
  
  ELSE
    
    IF (NEW.job_function_title <> OLD.job_function_title) THEN
      SET @CHECK = '1';
      SET @c_field = CONCAT(@c_field, 'Job Function', '+');
    
      IF (OLD.job_function_title = '') THEN
        SET @old_val = CONCAT(@old_val, '[BLANK]', '+');
      ELSE
        SET @old_val = CONCAT(@old_val, OLD.job_function_title, '+');
      END IF;
      
      IF (NEW.job_function_title = '') THEN
        SET @new_val = CONCAT(@new_val, '[BLANK]', '+');
      ELSE
        SET @new_val = CONCAT(@new_val, NEW.job_function_title,'+');
      END IF;  
    
    END IF;
  
  END IF;
  IF (ISNULL(OLD.job_position)) AND (NEW.job_position <> '') THEN
    
    IF (ISNULL(OLD.job_position) <> NEW.job_position) THEN
        SET @CHECK = '1';    
        SET @c_field = CONCAT(@c_field , 'Job Position','+');
      
      IF (ISNULL(OLD.job_position)) THEN 
        SET @old_val = CONCAT(@old_val , '[BLANK]','+');
      ELSE 
        SET @old_val = CONCAT(@old_val , OLD.job_position,'+');
      END IF;
      
      IF (ISNULL(NEW.job_position)) THEN
        SET @new_val = CONCAT(@new_val , '[BLANK]','+');
      ELSE
        SET @new_val = CONCAT(@new_val , NEW.job_position,'+');
      END IF;
    
    END IF;
  
  ELSE
    
    IF (NEW.job_position <> OLD.job_position) THEN
      SET @CHECK = '1';
      SET @c_field = CONCAT(@c_field, 'Job Position', '+');
    
      IF (OLD.job_position = '') THEN
        SET @old_val = CONCAT(@old_val, '[BLANK]', '+');
      ELSE
        SET @old_val = CONCAT(@old_val, OLD.job_position, '+');
      END IF;
      
      IF (NEW.job_position = '') THEN
        SET @new_val = CONCAT(@new_val, '[BLANK]', '+');
      ELSE
        SET @new_val = CONCAT(@new_val, NEW.job_position,'+');
      END IF;  
    
    END IF;
  
  END IF;
--
IF (ISNULL(OLD.ris_id)) AND (NEW.ris_id <> '') THEN
    
    IF (ISNULL(OLD.ris_id) <> NEW.ris_id) THEN
        SET @CHECK = '1';    
        SET @c_field = CONCAT(@c_field , 'RIS ID','+');
      
      IF (ISNULL(OLD.ris_id)) THEN 
        SET @old_val = CONCAT(@old_val , '[BLANK]','+');
      ELSE 
        SET @old_val = CONCAT(@old_val , OLD.ris_id,'+');
      END IF;
      
      IF (ISNULL(NEW.ris_id)) THEN
        SET @new_val = CONCAT(@new_val , '[BLANK]','+');
      ELSE
        SET @new_val = CONCAT(@new_val , NEW.ris_id,'+');
      END IF;
    
    END IF;
  
  ELSE
    
    IF (NEW.ris_id <> OLD.ris_id) THEN
      SET @CHECK = '1';
      SET @c_field = CONCAT(@c_field, 'RIS IDs', '+');
    
      IF (OLD.ris_id = '') THEN
        SET @old_val = CONCAT(@old_val, '[BLANK]', '+');
      ELSE
        SET @old_val = CONCAT(@old_val, OLD.ris_id, '+');
      END IF;
      
      IF (NEW.ris_id = '') THEN
        SET @new_val = CONCAT(@new_val, '[BLANK]', '+');
      ELSE
        SET @new_val = CONCAT(@new_val, NEW.ris_id,'+');
      END IF;  
    
    END IF;
  
  END IF;
  
  
--
  IF (ISNULL(OLD.id_nr)) AND (NEW.id_nr <> '') THEN
    
    IF (ISNULL(OLD.id_nr) <> NEW.id_nr) THEN
        SET @CHECK = '1';    
        SET @c_field = CONCAT(@c_field , 'ID','+');
      
      IF (ISNULL(OLD.id_nr)) THEN 
        SET @old_val = CONCAT(@old_val , '[BLANK]','+');
      ELSE 
        SET @old_val = CONCAT(@old_val , OLD.id_nr,'+');
      END IF;
      
      IF (ISNULL(NEW.id_nr)) THEN
        SET @new_val = CONCAT(@new_val , '[BLANK]','+');
      ELSE
        SET @new_val = CONCAT(@new_val , NEW.id_nr,'+');
      END IF;
    
    END IF;
  
  ELSE
    
    IF (NEW.id_nr <> OLD.id_nr) THEN
      SET @CHECK = '1';
      SET @c_field = CONCAT(@c_field, 'ID', '+');
    
      IF (OLD.id_nr = '') THEN
        SET @old_val = CONCAT(@old_val, '[BLANK]', '+');
      ELSE
        SET @old_val = CONCAT(@old_val, OLD.id_nr, '+');
      END IF;
      
      IF (NEW.id_nr = '') THEN
        SET @new_val = CONCAT(@new_val, '[BLANK]', '+');
      ELSE
        SET @new_val = CONCAT(@new_val, NEW.id_nr,'+');
      END IF;  
    
    END IF;
  
  END IF;
  IF (ISNULL(OLD.bio_nr)) AND (NEW.bio_nr <> '') THEN
    
    IF (ISNULL(OLD.bio_nr) <> NEW.bio_nr) THEN
        SET @CHECK = '1';    
        SET @c_field = CONCAT(@c_field , 'Bio No.','+');
      
      IF (ISNULL(OLD.bio_nr)) THEN 
        SET @old_val = CONCAT(@old_val , '[BLANK]','+');
      ELSE 
        SET @old_val = CONCAT(@old_val , OLD.bio_nr,'+');
      END IF;
      
      IF (ISNULL(NEW.bio_nr)) THEN
        SET @new_val = CONCAT(@new_val , '[BLANK]','+');
      ELSE
        SET @new_val = CONCAT(@new_val , NEW.bio_nr,'+');
      END IF;
    
    END IF;
  
  ELSE
    
    IF (NEW.bio_nr <> OLD.bio_nr) THEN
      SET @CHECK = '1';
      SET @c_field = CONCAT(@c_field, 'Bio No.', '+');
    
      IF (OLD.bio_nr = '') THEN
        SET @old_val = CONCAT(@old_val, '[BLANK]', '+');
      ELSE
        SET @old_val = CONCAT(@old_val, OLD.bio_nr, '+');
      END IF;
      
      IF (NEW.bio_nr = '') THEN
        SET @new_val = CONCAT(@new_val, '[BLANK]', '+');
      ELSE
        SET @new_val = CONCAT(@new_val, NEW.bio_nr,'+');
      END IF;  
    
    END IF;
  
  END IF;
  
  IF (ISNULL(OLD.other_title)) AND (NEW.other_title <> '') THEN
    
    IF (ISNULL(OLD.other_title) <> NEW.other_title) THEN
        SET @CHECK = '1';    
        SET @c_field = CONCAT(@c_field , 'Other Title','+');
      
      IF (ISNULL(OLD.other_title)) THEN 
        SET @old_val = CONCAT(@old_val , '[BLANK]','+');
      ELSE 
        SET @old_val = CONCAT(@old_val , OLD.other_title,'+');
      END IF;
      
      IF (ISNULL(NEW.other_title)) THEN
        SET @new_val = CONCAT(@new_val , '[BLANK]','+');
      ELSE
        SET @new_val = CONCAT(@new_val , NEW.other_title,'+');
      END IF;
    
    END IF;
  
  ELSE
    
    IF (NEW.other_title <> OLD.other_title) THEN
      SET @CHECK = '1';
      SET @c_field = CONCAT(@c_field, 'Other Title', '+');
    
      IF (OLD.other_title = '') THEN
        SET @old_val = CONCAT(@old_val, '[BLANK]', '+');
      ELSE
        SET @old_val = CONCAT(@old_val, OLD.other_title, '+');
      END IF;
      
      IF (NEW.other_title = '') THEN
        SET @new_val = CONCAT(@new_val, '[BLANK]', '+');
      ELSE
        SET @new_val = CONCAT(@new_val, NEW.other_title,'+');
      END IF;  
    
    END IF;
  
  END IF;
  --
    IF (ISNULL(OLD.doctor_role)) AND (NEW.doctor_role <> '') THEN
    
    IF (ISNULL(OLD.doctor_role) <> NEW.doctor_role) THEN
        SET @CHECK = '1';    
        SET @c_field = CONCAT(@c_field , 'Doctor Role','+');
      
      IF (ISNULL(OLD.doctor_role)) THEN 
        SET @old_val = CONCAT(@old_val , '[BLANK]','+');
      ELSE 
        SET @old_val = CONCAT(@old_val , OLD.doctor_role,'+');
      END IF;
      
      IF (ISNULL(NEW.doctor_role)) THEN
        SET @new_val = CONCAT(@new_val , '[BLANK]','+');
      ELSE
        SET @new_val = CONCAT(@new_val , NEW.doctor_role,'+');
      END IF;
    
    END IF;
  
  ELSE
    
    IF (NEW.doctor_role <> OLD.doctor_role) THEN
      SET @CHECK = '1';
      SET @c_field = CONCAT(@c_field, 'Doctor Role', '+');
    
      IF (OLD.doctor_role = '') THEN
        SET @old_val = CONCAT(@old_val, '[BLANK]', '+');
      ELSE
        SET @old_val = CONCAT(@old_val, OLD.doctor_role, '+');
      END IF;
      
      IF (NEW.doctor_role = '') THEN
        SET @new_val = CONCAT(@new_val, '[BLANK]', '+');
      ELSE
        SET @new_val = CONCAT(@new_val, NEW.doctor_role,'+');
      END IF;  
    
    END IF;
  
  END IF;
  --
     IF (ISNULL(OLD.doctor_level)) AND (NEW.doctor_level <> '') THEN
    
    IF (ISNULL(OLD.doctor_level) <> NEW.doctor_level) THEN
        SET @CHECK = '1';    
        SET @c_field = CONCAT(@c_field , 'Doctor Level','+');
      
      IF (ISNULL(OLD.doctor_level)) THEN 
        SET @old_val = CONCAT(@old_val , '[BLANK]','+');
      ELSE 
        SET @old_val = CONCAT(@old_val , OLD.doctor_level,'+');
      END IF;
      
      IF (ISNULL(NEW.doctor_level)) THEN
        SET @new_val = CONCAT(@new_val , '[BLANK]','+');
      ELSE
        SET @new_val = CONCAT(@new_val , NEW.doctor_level,'+');
      END IF;
    
    END IF;
  
  ELSE
    
    IF (NEW.doctor_level <> OLD.doctor_level) THEN
      SET @CHECK = '1';
      SET @c_field = CONCAT(@c_field, 'Doctor Level', '+');
    
      IF (OLD.doctor_level = '') THEN
        SET @old_val = CONCAT(@old_val, '[BLANK]', '+');
      ELSE
        SET @old_val = CONCAT(@old_val, OLD.doctor_level, '+');
      END IF;
      
      IF (NEW.doctor_level = '') THEN
        SET @new_val = CONCAT(@new_val, '[BLANK]', '+');
      ELSE
        SET @new_val = CONCAT(@new_val, NEW.doctor_level,'+');
      END IF;  
    
    END IF;
  
  END IF;
  --
  IF (ISNULL(OLD.s2_nr)) AND (NEW.s2_nr <> '') THEN
    
    IF (ISNULL(OLD.s2_nr) <> NEW.s2_nr) THEN
        SET @CHECK = '1';    
        SET @c_field = CONCAT(@c_field , 'S2 No','+');
      
      IF (ISNULL(OLD.s2_nr)) THEN 
        SET @old_val = CONCAT(@old_val , '[BLANK]','+');
      ELSE 
        SET @old_val = CONCAT(@old_val , OLD.s2_nr,'+');
      END IF;
      
      IF (ISNULL(NEW.s2_nr)) THEN
        SET @new_val = CONCAT(@new_val , '[BLANK]','+');
      ELSE
        SET @new_val = CONCAT(@new_val , NEW.s2_nr,'+');
      END IF;
    
    END IF;
  
  ELSE
    
    IF (NEW.s2_nr <> OLD.s2_nr) THEN
      SET @CHECK = '1';
      SET @c_field = CONCAT(@c_field, 'S2 No', '+');
    
      IF (OLD.s2_nr = '') THEN
        SET @old_val = CONCAT(@old_val, '[BLANK]', '+');
      ELSE
        SET @old_val = CONCAT(@old_val, OLD.s2_nr, '+');
      END IF;
      
      IF (NEW.s2_nr = '') THEN
        SET @new_val = CONCAT(@new_val, '[BLANK]', '+');
      ELSE
        SET @new_val = CONCAT(@new_val, NEW.s2_nr,'+');
      END IF;  
    
    END IF;
  
  END IF;
  IF (ISNULL(OLD.multiple_employer)) AND (NEW.multiple_employer <> '') THEN
    
    IF (ISNULL(OLD.multiple_employer) <> NEW.multiple_employer) THEN
        SET @CHECK = '1';    
        SET @c_field = CONCAT(@c_field , 'Multi_employer','+');
      
      IF (ISNULL(OLD.multiple_employer)) THEN 
        SET @old_val = CONCAT(@old_val , '[BLANK]','+');
      ELSE 
        SET @old_val = CONCAT(@old_val , OLD.multiple_employer,'+');
      END IF;
      
      IF (ISNULL(NEW.multiple_employer)) THEN
        SET @new_val = CONCAT(@new_val , '[BLANK]','+');
      ELSE
        SET @new_val = CONCAT(@new_val , NEW.multiple_employer,'+');
      END IF;
    
    END IF;
  
  ELSE
    
    IF (NEW.multiple_employer <> OLD.multiple_employer) THEN
      SET @CHECK = '1';
      SET @c_field = CONCAT(@c_field, 'Multi_employer', '+');
    
      IF (OLD.multiple_employer = '') THEN
        SET @old_val = CONCAT(@old_val, '[BLANK]', '+');
      ELSE
        SET @old_val = CONCAT(@old_val, OLD.multiple_employer, '+');
      END IF;
      
      IF (NEW.multiple_employer = '') THEN
        SET @new_val = CONCAT(@new_val, '[BLANK]', '+');
      ELSE
        SET @new_val = CONCAT(@new_val, NEW.multiple_employer,'+');
      END IF;  
    
    END IF;
  
  END IF;
  IF (ISNULL(OLD.ptr_nr)) AND (NEW.ptr_nr <> '') THEN
    
    IF (ISNULL(OLD.ptr_nr) <> NEW.ptr_nr) THEN
        SET @CHECK = '1';    
        SET @c_field = CONCAT(@c_field , 'ptr no','+');
      
      IF (ISNULL(OLD.ptr_nr)) THEN 
        SET @old_val = CONCAT(@old_val , '[BLANK]','+');
      ELSE 
        SET @old_val = CONCAT(@old_val , OLD.ptr_nr,'+');
      END IF;
      
      IF (ISNULL(NEW.ptr_nr)) THEN
        SET @new_val = CONCAT(@new_val , '[BLANK]','+');
      ELSE
        SET @new_val = CONCAT(@new_val , NEW.ptr_nr,'+');
      END IF;
    
    END IF;
  
  ELSE
    
    IF (NEW.ptr_nr <> OLD.ptr_nr) THEN
      SET @CHECK = '1';
      SET @c_field = CONCAT(@c_field, 'ptr no', '+');
    
      IF (OLD.ptr_nr = '') THEN
        SET @old_val = CONCAT(@old_val, '[BLANK]', '+');
      ELSE
        SET @old_val = CONCAT(@old_val, OLD.ptr_nr, '+');
      END IF;
      
      IF (NEW.ptr_nr = '') THEN
        SET @new_val = CONCAT(@new_val, '[BLANK]', '+');
      ELSE
        SET @new_val = CONCAT(@new_val, NEW.ptr_nr,'+');
      END IF;  
    
    END IF;
  
  END IF;
  IF (ISNULL(OLD.tier_nr)) AND (NEW.tier_nr <> '') THEN
    
    IF (ISNULL(OLD.tier_nr) <> NEW.tier_nr) THEN
        SET @CHECK = '1';    
        SET @c_field = CONCAT(@c_field , 'tier no','+');
      
      IF (ISNULL(OLD.tier_nr)) THEN 
        SET @old_val = CONCAT(@old_val , '[BLANK]','+');
      ELSE 
        SET @old_val = CONCAT(@old_val , OLD.tier_nr,'+');
      END IF;
      
      IF (ISNULL(NEW.tier_nr)) THEN
        SET @new_val = CONCAT(@new_val , '[BLANK]','+');
      ELSE
        SET @new_val = CONCAT(@new_val , NEW.tier_nr,'+');
      END IF;
    
    END IF;
  
  ELSE
    
    IF (NEW.tier_nr <> OLD.tier_nr) THEN
      SET @CHECK = '1';
      SET @c_field = CONCAT(@c_field, 'tier no', '+');
    
      IF (OLD.tier_nr = '') THEN
        SET @old_val = CONCAT(@old_val, '[BLANK]', '+');
      ELSE
        SET @old_val = CONCAT(@old_val, OLD.tier_nr, '+');
      END IF;
      
      IF (NEW.tier_nr = '') THEN
        SET @new_val = CONCAT(@new_val, '[BLANK]', '+');
      ELSE
        SET @new_val = CONCAT(@new_val, NEW.ptr_nr,'+');
      END IF;  
    
    END IF;
  
  END IF;
  
  
  -- en by Matsuu 05092017
  
  IF (ISNULL(OLD.license_nr)) AND (NEW.license_nr <> '') THEN
    
    IF (ISNULL(OLD.license_nr) <> NEW.license_nr) THEN
        SET @CHECK = '1';    
        SET @c_field = CONCAT(@c_field , 'License No.','+');
      
      IF (ISNULL(OLD.license_nr)) THEN 
        SET @old_val = CONCAT(@old_val , '[BLANK]','+');
      ELSE 
        SET @old_val = CONCAT(@old_val , OLD.license_nr,'+');
      END IF;
      
      IF (ISNULL(NEW.license_nr)) THEN
        SET @new_val = CONCAT(@new_val , '[BLANK]','+');
      ELSE
        SET @new_val = CONCAT(@new_val , NEW.license_nr,'+');
      END IF;
    
    END IF;
  
  ELSE
    
    IF (NEW.license_nr <> OLD.license_nr) THEN
      SET @CHECK = '1';
      SET @c_field = CONCAT(@c_field, 'License No.', '+');
    
      IF (OLD.license_nr = '') THEN
        SET @old_val = CONCAT(@old_val, '[BLANK]', '+');
      ELSE
        SET @old_val = CONCAT(@old_val, OLD.license_nr, '+');
      END IF;
      
      IF (NEW.license_nr = '') THEN
        SET @new_val = CONCAT(@new_val, '[BLANK]', '+');
      ELSE
        SET @new_val = CONCAT(@new_val, NEW.license_nr,'+');
      END IF;  
    
    END IF;
  
  END IF;
  IF (ISNULL(OLD.tin)) AND (NEW.tin <> '') THEN
    
    IF (ISNULL(OLD.tin) <> NEW.tin) THEN
        SET @CHECK = '1';    
        SET @c_field = CONCAT(@c_field , 'Tin No.','+');
      
      IF (ISNULL(OLD.tin)) THEN 
        SET @old_val = CONCAT(@old_val , '[BLANK]','+');
      ELSE 
        SET @old_val = CONCAT(@old_val , OLD.tin,'+');
      END IF;
      
      IF (ISNULL(NEW.tin)) THEN
        SET @new_val = CONCAT(@new_val , '[BLANK]','+');
      ELSE
        SET @new_val = CONCAT(@new_val , NEW.tin,'+');
      END IF;
    
    END IF;
  
  ELSE
    
    IF (NEW.tin <> OLD.tin) THEN
      SET @CHECK = '1';
      SET @c_field = CONCAT(@c_field, 'Tin No.', '+');
    
      IF (OLD.tin = '') THEN
        SET @old_val = CONCAT(@old_val, '[BLANK]', '+');
      ELSE
        SET @old_val = CONCAT(@old_val, OLD.tin, '+');
      END IF;
      
      IF (NEW.tin = '') THEN
        SET @new_val = CONCAT(@new_val, '[BLANK]', '+');
      ELSE
        SET @new_val = CONCAT(@new_val, NEW.tin,'+');
      END IF;  
    
    END IF;
  
  END IF;
  IF (NEW.is_resident_dr <> OLD.is_resident_dr) THEN
    SET @CHECK = '1';
    SET @c_field = CONCAT(@c_field, 'Is Resident Doctor', '+');
    
    IF (OLD.is_resident_dr = '1') THEN
      SET @old_val = CONCAT(@old_val, 'Yes', '+');
    ELSE
      SET @old_val = CONCAT(@old_val, 'No', '+');
    END IF;
    
    IF (NEW.is_resident_dr = '1') THEN
      SET @new_val = CONCAT(@new_val, 'Yes', '+');
    ELSE
      SET @new_val = CONCAT(@new_val, 'No','+');
    END IF;   
  END IF;
  --
    IF (NEW.is_reliever <> OLD.is_reliever) THEN
    SET @CHECK = '1';
    SET @c_field = CONCAT(@c_field, 'Is Reliever', '+');
    
    IF (OLD.is_reliever = '1') THEN
      SET @old_val = CONCAT(@old_val, 'Yes', '+');
    ELSE
      SET @old_val = CONCAT(@old_val, 'No', '+');
    END IF;
    
    IF (NEW.is_reliever = '1') THEN
      SET @new_val = CONCAT(@new_val, 'Yes', '+');
    ELSE
      SET @new_val = CONCAT(@new_val, 'No','+');
    END IF;   
  END IF;
  
  --
    IF (NEW.category <> OLD.category) THEN
      SET @CHECK = '1';
      SET @c_field = CONCAT(@c_field, 'Category', '+');
    
      IF (OLD.category = '') THEN
        SET @old_val = CONCAT(@old_val, '[BLANK]', '+');
      ELSE
        SET @old_val = CONCAT(@old_val, OLD.category, '+');
      END IF;
      
      IF (NEW.category = '') THEN
        SET @new_val = CONCAT(@new_val, '[BLANK]', '+');
      ELSE
        SET @new_val = CONCAT(@new_val, NEW.category,'+');
      END IF;  
    
    END IF;
  
  --
  IF (NEW.date_join <> OLD.date_join) THEN
    SET @CHECK = '1';
    SET @c_field = CONCAT(@c_field, 'Date Join', '+');
    
    IF (OLD.date_join = '') THEN
      SET @old_val = CONCAT(@old_val, '[BLANK]', '+');
    ELSE
      SET @old_val = CONCAT(@old_val, (SELECT DATE_FORMAT(OLD.date_join,'%b %d %Y')), '+');
    END IF;
    
    IF (NEW.date_join = '') THEN
      SET @new_val = CONCAT(@new_val, '[BLANK]', '+');
    ELSE
      SET @new_val = CONCAT(@new_val, (SELECT DATE_FORMAT(NEW.date_join,'%b %d %Y')),'+');
    END IF;  
  
  END IF;
  IF (ISNULL(OLD.date_exit)) AND (NEW.date_exit <> '') THEN
    
    IF (ISNULL(OLD.date_exit) <> NEW.date_exit) THEN
        SET @CHECK = '1';    
        SET @c_field = CONCAT(@c_field , 'Date Exit','+');
      
      IF (ISNULL(OLD.date_exit)) THEN 
        SET @old_val = CONCAT(@old_val , '[BLANK]','+');
      ELSE 
        SET @old_val = CONCAT(@old_val , (SELECT DATE_FORMAT(OLD.date_exit,'%b %d %Y')),'+');
      END IF;
      
      IF (ISNULL(NEW.date_exit)) THEN
        SET @new_val = CONCAT(@new_val , '[BLANK]','+');
      ELSE
        SET @new_val = CONCAT(@new_val , (SELECT DATE_FORMAT(NEW.date_exit,'%b %d %Y')),'+');
      END IF;
    
    END IF;
  
  ELSE
    
    IF (NEW.date_exit <> OLD.date_exit) THEN
      SET @CHECK = '1';
      SET @c_field = CONCAT(@c_field, 'Date Exit', '+');
    
      IF (OLD.date_exit = '') THEN
        SET @old_val = CONCAT(@old_val, '[BLANK]', '+');
      ELSE
        SET @old_val = CONCAT(@old_val, (SELECT DATE_FORMAT(OLD.date_exit,'%b %d %Y')), '+');
      END IF;
      
      IF (NEW.date_exit = '') THEN
        SET @new_val = CONCAT(@new_val, '[BLANK]', '+');
      ELSE
        SET @new_val = CONCAT(@new_val, (SELECT DATE_FORMAT(NEW.date_exit,'%b %d %Y')),'+');
      END IF;  
    
    END IF;
  
  END IF;
  IF (NEW.contract_start <> OLD.contract_start) THEN
    SET @CHECK = '1';
    SET @c_field = CONCAT(@c_field, 'Contract Start', '+');
    
    IF (OLD.contract_start = '') THEN
      SET @old_val = CONCAT(@old_val, '[BLANK]', '+');
    ELSE
      SET @old_val = CONCAT(@old_val, (SELECT DATE_FORMAT(OLD.contract_start,'%b %d %Y')), '+');
    END IF;
    
    IF (NEW.contract_start = '') THEN
      SET @new_val = CONCAT(@new_val, '[BLANK]', '+');
    ELSE
      SET @new_val = CONCAT(@new_val, (SELECT DATE_FORMAT(NEW.contract_start,'%b %d %Y')),'+');
    END IF;  
  
  END IF;
  
  IF (ISNULL(OLD.contract_end)) AND (NEW.contract_end <> '') THEN
    
    IF (ISNULL(OLD.contract_end) <> NEW.contract_end) THEN
        SET @CHECK = '1';    
        SET @c_field = CONCAT(@c_field , 'Contract End','+');
      
      IF (ISNULL(OLD.contract_end)) THEN 
        SET @old_val = CONCAT(@old_val , '[BLANK]','+');
      ELSE 
        SET @old_val = CONCAT(@old_val , (SELECT DATE_FORMAT(OLD.contract_end,'%b %d %Y')),'+');
      END IF;
      
      IF (ISNULL(NEW.contract_end)) THEN
        SET @new_val = CONCAT(@new_val , '[BLANK]','+');
      ELSE
        SET @new_val = CONCAT(@new_val , (SELECT DATE_FORMAT(NEW.contract_end,'%b %d %Y')),'+');
      END IF;
    
    END IF;
  
  ELSE
    
    IF (NEW.contract_end <> OLD.contract_end) THEN
      SET @CHECK = '1';
      SET @c_field = CONCAT(@c_field, 'Contract End', '+');
    
      IF (OLD.contract_end = '') THEN
        SET @old_val = CONCAT(@old_val, '[BLANK]', '+');
      ELSE
        SET @old_val = CONCAT(@old_val, (SELECT DATE_FORMAT(OLD.contract_end,'%b %d %Y')), '+');
      END IF;
      
      IF (NEW.contract_end = '') THEN
        SET @new_val = CONCAT(@new_val, '[BLANK]', '+');
      ELSE
        SET @new_val = CONCAT(@new_val, (SELECT DATE_FORMAT(NEW.contract_end,'%b %d %Y')),'+');
      END IF;  
    
    END IF;
  
  END IF;
  IF (@CHECK <> '') THEN
   INSERT INTO seg_audit_trail
   (ID,date_changed,Action_type,login,table_name,field_c,new_value,old_value,pk_field,pk_value) 
   VALUES 
   (UUID(), NOW(),'Update',@mod,'care_personell',@c_field,@new_val,@old_val,'personell_nr',NEW.nr); 
 END IF;
 END;
$$
DELIMITER ;

-- Ended here..