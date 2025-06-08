-- Drop tables if they exist
DROP TABLE IF EXISTS inbox;
DROP TABLE IF EXISTS users;

-- Create `users` table
CREATE TABLE users (
    username VARCHAR(10) NOT NULL PRIMARY KEY,
    fullname VARCHAR(20) NOT NULL,
    password VARCHAR(256) NOT NULL,
    avatar VARCHAR(100) NOT NULL
);

-- Create `inbox` table
CREATE TABLE inbox (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(10) NOT NULL,
    message VARCHAR(1000) NOT NULL,
    category VARCHAR(15) NOT NULL,
    timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (username) REFERENCES users(username) ON DELETE CASCADE
);
