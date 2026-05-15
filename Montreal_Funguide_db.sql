SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `Montreal_Funguide_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `Montreal_Funguide_db`;

--//* Initializing Tables:
-----------------------------------------------------------
--//? Auth Group:
CREATE TABLE users (
    user_id INT AUTO_INCREMENT,
    user_handle VARCHAR(48),
    user_verified BOOLEAN DEFAULT 0,
    user_fname VARCHAR(32),
    user_lname VARCHAR(32),
    user_email VARCHAR(255),
    user_password VARCHAR(255),
    user_role ENUM('admin','user','guest') DEFAULT 'guest',
    user_doc TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id)
);

CREATE TABLE devices (
    device_id INT AUTO_INCREMENT,
    device_name VARCHAR(255),
    device_type VARCHAR(100),
    device_os VARCHAR(100),
    device_ip VARCHAR(45),
    device_last_seen TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (device_id)
);

CREATE TABLE user_device (
    user_device_id INT AUTO_INCREMENT,
    user_id INT NOT NULL,
    device_id INT NOT NULL,
    first_seen TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_seen TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_ip VARCHAR(45),
    last_user_agent TEXT,
    login_count INT DEFAULT 1,
    PRIMARY KEY (user_device_id)
);

--//* Adding constraints to the Auth Group tables:
ALTER TABLE user_device
    ADD CONSTRAINT fk_user_device_user
        FOREIGN KEY (user_id) REFERENCES users(user_id)
        ON DELETE CASCADE,
    ADD CONSTRAINT fk_user_device_device
        FOREIGN KEY (device_id) REFERENCES devices(device_id)
        ON DELETE CASCADE;

--//* Adding triggers to the Auth Group tables:

--//? Trigger #01) Auto-generating anonymous user handle
--//? If a user does not provide a handle during registration,
--//? the system assigns a unique handle in the format "anon{user_id}".
DELIMITER $$

CREATE TRIGGER trg_users_generate_handle
AFTER INSERT ON users
FOR EACH ROW
BEGIN
    IF NEW.user_handle IS NULL OR NEW.user_handle = '' THEN
        UPDATE users
        SET user_handle = CONCAT('anon', NEW.user_id)
        WHERE user_id = NEW.user_id;
    END IF;
END $$

DELIMITER ;

--//? Trigger #02) Auto-Updating device activity
--//? Each time a user-device record is updated (login activity),
--//? the system updates last_seen and increments login count.
DELIMITER $$

CREATE TRIGGER trg_user_device_update
BEFORE UPDATE ON user_device
FOR EACH ROW
BEGIN
    SET NEW.last_seen = CURRENT_TIMESTAMP;
    SET NEW.login_count = OLD.login_count + 1;
END $$

DELIMITER ;

-----------------------------------------------------------
--//? Fungi Group:
CREATE TABLE species (
    species_id INT AUTO_INCREMENT,
    species_name VARCHAR(255) NOT NULL,
    species_family VARCHAR(255),
    species_genus VARCHAR(255),
    species_edibility BOOLEAN DEFAULT 0,
    species_toxicity ENUM('safe','mild','moderate','dangerous','deadly'),
    species_discovery INT,
    species_gbif_id VARCHAR(100),
    PRIMARY KEY (species_id)
);

CREATE TABLE fungi (
    fungi_id INT AUTO_INCREMENT,
    species_id INT NOT NULL,
    fungi_observation_source VARCHAR(100) DEFAULT 'GBIF',
    fungi_observation_date TIMESTAMP,
    fungi_notes TEXT,
    PRIMARY KEY (fungi_id)
);

CREATE TABLE habitat (
    habitat_id INT AUTO_INCREMENT,
    habitat_type VARCHAR(100),
    habitat_climate VARCHAR(100),
    habitat_soil VARCHAR(100),
    habitat_humindex VARCHAR(50),
    habitat_desc TEXT,
    PRIMARY KEY (habitat_id)
);

CREATE TABLE species_habitat (
    species_id INT NOT NULL,
    habitat_id INT NOT NULL,
    PRIMARY KEY (species_id, habitat_id)
);

CREATE TABLE fungi_location (
    fungi_location_id INT AUTO_INCREMENT,
    fungi_id INT NOT NULL,
    fungi_location_lat DECIMAL(10,8),
    fungi_location_long DECIMAL(11,8),
    fungi_location_borough VARCHAR(100),
    fungi_location_discovery TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (fungi_location_id)
);

--//* Adding constraints to the Fungi Group tables:

ALTER TABLE fungi
    ADD CONSTRAINT fk_fungi_species
        FOREIGN KEY (species_id) REFERENCES species(species_id)
        ON DELETE CASCADE;

ALTER TABLE species_habitat
    ADD CONSTRAINT fk_species_habitat_species
        FOREIGN KEY (species_id) REFERENCES species(species_id)
        ON DELETE CASCADE,
    ADD CONSTRAINT fk_species_habitat_habitat
        FOREIGN KEY (habitat_id) REFERENCES habitat(habitat_id)
        ON DELETE CASCADE;

ALTER TABLE fungi_location
    ADD CONSTRAINT fk_fungi_location_fungi
        FOREIGN KEY (fungi_id) REFERENCES fungi(fungi_id)
        ON DELETE CASCADE;

--//* Adding triggers to the Fungi Group tables:

--//? Trigger #03) Normalizing species name formatting
--//? Ensures there is consistent capitalization for all species names.
DELIMITER $$

CREATE TRIGGER trg_species_name_format
BEFORE INSERT ON species
FOR EACH ROW
BEGIN
    SET NEW.species_name = CONCAT(
        UPPER(SUBSTRING(NEW.species_name, 1, 1)),
        LOWER(SUBSTRING(NEW.species_name, 2))
    );
END $$

DELIMITER ;
-----------------------------------------------------------
--//? Location Group:
--//* Adding constraints to the Location Group tables:
--//* Adding triggers to the Location Group tables:
-----------------------------------------------------------
--//? Recipe Group:
--//* Adding constraints to the Recipe Group tables:
--//* Adding triggers to the Recipe Group tables:
