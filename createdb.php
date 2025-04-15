// Connect to db then create the database
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
    echo "Table 'data' created successfully";
}

// The table 'users' structure is as follows:
// id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY
// username varchar(255) NOT NULL UNIQUE
// password varchar(255) NOT NULL
// display_name varchar(255) NOT NULL
// email varchar(255) NOT NULL UNIQUE
// date datetime NOT NULL DEFAULT CURRENT_TIMESTAMP

$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    display_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($sql);
if ($conn->error) {
    die("Error creating table: " . $conn->error);
} else {
    echo "Table 'users' created successfully";
}
// Insert three users into the users table, kalle, pelle, stina


$conn->close();