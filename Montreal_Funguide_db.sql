--//* Initializing Tables:
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
--//?   Trigger #01) Auto-generating anonymous user handle
--//?   If a user does not provide a handle during registration,
--//?   the system assigns a unique handle in the format "anon{user_id}".
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

--//?   Trigger #02) Auto-Updating device activity
--//?   Each time a user-device record is updated (login activity),
--//?   the system updates last_seen and increments login count.
DELIMITER $$

CREATE TRIGGER trg_user_device_update
BEFORE UPDATE ON user_device
FOR EACH ROW
BEGIN
    SET NEW.last_seen = CURRENT_TIMESTAMP;
    SET NEW.login_count = OLD.login_count + 1;
END $$

DELIMITER ;