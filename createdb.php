<?php
// Database configuration
require_once 'db.php';

// The table 'data' structure is as follows:
// id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY
// author int(11) NOT NULL
// title varchar(255) NOT NULL
// message varchar(255) NOT NULL
// image varchar(255) NOT NULL
// date datetime NOT NULL DEFAULT CURRENT_TIMESTAMP

$sql = "CREATE TABLE IF NOT EXISTS data (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    author INT(11) NOT NULL,
    title VARCHAR(255) NOT NULL,
    message VARCHAR(255) NOT NULL,
    image VARCHAR(255) NOT NULL,
    date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
)";

$conn->query($sql);
if ($conn->error) {
    die("Error creating table: " . $conn->error);
} else {
    echo "Table 'data' created successfully<br/>";
}

// The table 'users' structure is as follows:
// id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY
// username varchar(255) NOT NULL UNIQUE
// password varchar(255) NOT NULL
// display_name varchar(255) NOT NULL
// email varchar(255) NOT NULL UNIQUE
// valid_token varchar(255) NOT NULL UNIQUE
// token_expiration datetime NOT NULL DEFAULT CURRENT_TIMESTAMP

$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    display_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    valid_token VARCHAR(255) NOT NULL UNIQUE,
    token_expiration DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($sql);
if ($conn->error) {
    die("Error creating table: " . $conn->error);
} else {
    echo "Table 'users' created successfully<br/>";
}
// Insert three users into the users table, kalle, pelle, stina
$sql = "INSERT INTO `users` (`id`, `username`, `password`, `display_name`, `email`,`valid_token`,`token_expiration`) VALUES (NULL, 'admin', 'admin', 'admin', 'admin@admin.admin','1', current_timestamp())";
$conn->query($sql);
if ($conn->error) {
    die("Error inserting data: " . $conn->error);
} else {
    echo "User 'admin' inserted successfully<br/>";
}
$sql ="ALTER TABLE `data` ADD FOREIGN KEY (`author`) REFERENCES `users`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT";
$conn->query($sql);
if ($conn->error) {
    die("Error adding foreign key: " . $conn->error);
} else {
    echo "Foreign key added successfully<br/>";
}
// Insert a test record into the data table
$sql = "SELECT id FROM users WHERE username = 'admin'";
$result = $conn->query($sql);   
$data = $result->fetch_assoc();
if ($data) {
    $authorId = $data['id'];
} else {
    die("Error fetching author ID: " . $conn->error);
}
$sql = "INSERT INTO data (author, title, message, image) VALUES ($authorId, 'Test Title', 'This is a test message.', 'test_image.jpg')";
$conn->query($sql);
if ($conn->error) {
    die("Error inserting test record: " . $conn->error);
} else {
    echo "Test record inserted successfully<br/>";
}
$conn->close();