CREATE DATABASE IF NOT EXISTS library_borrowing;

USE library_borrowing;

CREATE TABLE IF NOT EXISTS members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS borrowings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    book_title VARCHAR(150) NOT NULL,
    book_category VARCHAR(100) NOT NULL,
    quantity INT NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'Borrowed',
    FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE CASCADE
);
