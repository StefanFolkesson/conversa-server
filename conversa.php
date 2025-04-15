<?php
// read the url and if the url is conversa?getAll get all data from the database
// if its conversa?add red the POST data and add it to the database
// if its conversa?delete read the POST data and delete it from the database
// if its conversa?update read the POST data and update it in the database
require_once 'functions.php';
require_once 'db.php';

if(isset($_GET['validate'])) {
    if(isset($_GET['token'])) {
        // Validate the token
        $token = $_GET['token'];
        echo validateToken($token);
    } else {
        $username = $_GET['username'];
        $password = $_GET['password'];
        // Validate the user
        echo validateUser($username, $password);
    }
}


if(isset($_GET['getAll'])) {
    // Get all data from the database
    $data = getAllData();
    echo json_encode($data);
} elseif (isset($_POST['add'])) {
    // Add data to the database
    $data = $_POST['data'];
//    echo $data;
    echo addData($data);
} elseif (isset($_POST['delete'])) {
    // Delete data from the database
    $id = $_POST['id'];
    echo deleteData($id);
} elseif (isset($_POST['update'])) {
    // Update data in the database
    $id = $_POST['id'];
    $data = $_POST['data'];
    echo updateData($id, $data);
} else {
    echo "Invalid request";
}
?>
