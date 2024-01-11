CREATE DATABASE IF NOT EXISTS reminderdb;
USE reminderdb;

CREATE TABLE IF NOT EXISTS reminder (
    id INT AUTO_INCREMENT PRIMARY KEY,
    car_no VARCHAR(255) NOT NULL,
    description VARCHAR(255) NOT NULL,
    reg_date DATE NOT NULL,
    expiry DATE NOT NULL
);
