<?php
// read the url and if the url is conversa?getAll get all data from the database
// if its conversa?add red the POST data and add it to the database
// if its conversa?delete read the POST data and delete it from the database
// if its conversa?update read the POST data and update it in the database
require_once 'functions.php';
require_once 'db.php';
$validated = false;
$admin = false;

if(isset($_GET['validate'])) {
    if(isset($_GET['token'])) {
        // Validate the token
        $token = $_GET['token'];
        $validated = validateToken($token);
        if($validated) {
            $username = getUsername($token);
            $admin = isAdmin($username);
        }
        
    } else {
        $username = $_GET['username'];
        $password = $_GET['password'];
        // Validate the user
        $validated = validateUser($username, $password);
        if($validated)
            $admin = isAdmin($username);
    }
    if($validated) {
        echo json_encode(array("status" => "success", "message" => "User validated successfully.", "admin" => $admin));
    } else {
        echo json_encode(array("status" => "error", "message" => "Invalid username or password."));
    }
}


if(isset($_GET['getAll'])) {
    // Get all data from the database
    echo getAllData();
} elseif (isset($_POST['add']) && $validated) {
    // Add data to the database
    $data = $_POST['data'];
    echo addData($data);
} elseif (isset($_POST['delete']) && $validated) {
    // Delete data from the database
    $id = $_POST['id'];
    // Check if the user is an admin or the owner of the data
    if(isYours($id, $token)) {
        echo deleteData($id);
    } else {
        echo json_encode(array("status" => "error", "message" => "You cannot delete this data."));
        exit;
    }
} elseif (isset($_POST['update']) && $validated) {
    // Update data in the database
    $id = $_POST['id'];
    if(isYours($id, $token)) {
        $data = $_POST['data'];
        echo updateData($id, $data);
    } else {
        echo json_encode(array("status" => "error", "message" => "You cannot update this data."));
        exit;
    }
} elseif (isset($_POST['addUser']) && $admin) {
    // Add user to the database
    $data = $_POST['data'];
    echo addUser($data);
} elseif( isset($_POST['getAllUsers']) && $admin) {
    // Get all users from the database
    echo getAllUsers();
}
else {
    echo "Invalid request";
}
?>
