<?php
// add the function to get all data from the database
function getAllData() {
    global $conn;
    $sql = "SELECT data.id,display_name,title,message,image,date,author FROM data inner join users on data.author = users.id ORDER BY date DESC";
    $result = $conn->query($sql);
    $data = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            array_push($data, $row);
        }
    }
    return json_encode($data,true);
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
    $stmt->bind_param("isss", $data['author'], $data['title'], $data['message'], $data['image']);
    if ($stmt->execute()) {
        return json_encode(array("status" => "success", "id" => $stmt->insert_id));
    } else {
        return json_encode(array("status" => "error", "message" => $stmt->error));
    }
}
function deleteData($id) {
    global $conn;
    $sql = "DELETE FROM data WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        return json_encode(array("status" => "success", "message" => "Data deleted successfully."));
    } else {
        return json_encode(array("status" => "error", "message" => $stmt->error));
    }
}
function updateData($id, $data) {
    global $conn;
    $sql = "UPDATE data SET author = ?, title = ?, message = ?, image = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssi", $data['author'], $data['title'], $data['message'], $data['image'], $id);
    if ($stmt->execute()) {
        return json_encode(array("status" => "success", "message" => "Data updated successfully."));
    } else {
        return json_encode(array("status" => "error", "message" => $stmt->error));
    }
}
function getData($id) {
    global $conn;
    $sql = "SELECT * FROM data WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $returnarray = array();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            array_push($returnarray, $row);
        }
    }
    return json_encode($returnarray,true);
}


function validateToken($token){
    global $conn;
    $sql = "SELECT * FROM users WHERE valid_token = ? AND token_expiration > NOW()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows > 0) {
        setNewExpiration($token); // Update the expiration time
        return true;
    } else {
        return false;
    }
}
function validateUser($username,$password){
    global $conn;
    $sql = "SELECT * FROM users WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Generate a new token and update the database
        $newToken = bin2hex(random_bytes(16)); // Generate a random token
        // temporary use of token, should be replaced with a more secure method
        $newToken = $row['username'];
        $updateSql = "UPDATE users SET valid_token = ? WHERE id = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("si", $newToken, $row['id']);
        $updateStmt->execute();
        $updateStmt->close();
        // Return the new token
        $row['valid_token'] = $newToken;
        // Set the token expiration to 1 hour from now
        $newEx = setNewExpiration($newToken);
        return [$newToken,$row['id']];
    } else {
        return 0;
    }
}

function setNewExpiration($token) {
    global $conn;
    $sql = "UPDATE users SET token_expiration = ? WHERE valid_token = ?";
    $stmt = $conn->prepare($sql);
    $expirationTime = date("Y-m-d H:i:s", strtotime("+1 hour"));
    $stmt->bind_param("ss", $expirationTime, $token);
    if ($stmt->execute()) {
        return json_encode(array("status" => "success", "message" => "Token expiration updated successfully."));
    } else {
        return json_encode(array("status" => "error", "message" => $stmt->error));
    }
}

function isAdmin($username){
    global $conn;
    $sql = "SELECT * FROM users WHERE username = ? AND admin = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows > 0) {
        return true;
    } else {
        return false;
    }
}
function getUsername($token){
    global $conn;
    $sql = "SELECT * FROM users WHERE valid_token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['username'];
    } else {
        return false;
    }
}

function isYours($id, $token){
    global $conn;
    $sql = "SELECT * FROM data WHERE id = ? AND author = (SELECT id FROM users WHERE valid_token = ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $id, $token);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows > 0) {
        return true;
    } else {
        return false;
    }
}

function addUser($data){
    global $conn;
    $sql = "INSERT INTO users (username, password, display_name, email, admin) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $data['username'], $data['password'], $data['display_name'], $data['email'], $data['admin']);
    if ($stmt->execute()) {
        return json_encode(array("status" => "success", "id" => $stmt->insert_id));
    } else {
        return json_encode(array("status" => "error", "message" => $stmt->error));
    }
}

function getAllUsers(){
    global $conn;
    $sql = "SELECT * FROM users";
    $result = $conn->query($sql);
    $data = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            array_push($data, $row);
        }
    }
    return json_encode($data,true);
}