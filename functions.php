<?php
// add the function to get all data from the database
function getAllData() {
    global $conn;
    $sql = "SELECT * FROM data";
    $result = $conn->query($sql);
    $data = array();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    return $data;
}
// The databasestructure is as follows:
// id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY
// author varchar(255) NOT NULL
// title varchar(255) NOT NULL
// message varchar(255) NOT NULL
// image varchar(255) NOT NULL
// date datetime NOT NULL DEFAULT CURRENT_TIMESTAMP

function addData($data) {
    global $conn;
    $sql = "INSERT INTO data (author, title, message, image) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $data['author'], $data['title'], $data['message'], $data['image']);
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}
function deleteData($id) {
    global $conn;
    $sql = "DELETE FROM data WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}
function updateData($id, $data) {
    global $conn;
    $sql = "UPDATE data SET author = ?, title = ?, message = ?, image = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $data['author'], $data['title'], $data['message'], $data['image'], $id);
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}
function getData($id) {
    global $conn;
    $sql = "SELECT * FROM data WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}