-- Create database
CREATE DATABASE IF NOT EXISTS college_src_voting;
USE college_src_voting;

-- Table: users (voters and admin)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    aadhar VARCHAR(20) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'voter') DEFAULT 'voter',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert Admin User (Default)
INSERT INTO users (name, email, aadhar, password, role) VALUES
('Admin', 'admin@college.com', '000000000000', MD5('admin123'), 'admin');

-- Table: positions (e.g., President, Vice-President)
CREATE TABLE positions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

-- Table: candidates
CREATE TABLE candidates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    party VARCHAR(50) NOT NULL,
    position_id INT NOT NULL,
    FOREIGN KEY (position_id) REFERENCES positions(id) ON DELETE CASCADE
);

-- Table: voting_status (only one row used to control voting state)
CREATE TABLE voting_status (
    id INT PRIMARY KEY,
    status ENUM('not_started', 'started', 'ended') DEFAULT 'not_started'
);

-- Insert default voting status
INSERT INTO voting_status (id, status) VALUES (1, 'not_started');

-- Table: votes
CREATE TABLE votes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    aadhar VARCHAR(20) NOT NULL,
    congress INT DEFAULT 0,
    bjp INT DEFAULT 0,
    ncp INT DEFAULT 0,
    aota INT DEFAULT 0,
    voted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (aadhar)
);
