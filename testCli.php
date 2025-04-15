<?php
// test_api.php

// Set the API endpoint URL
$baseUrl = 'http://localhost/conversa-server/conversa.php'; // Change to your actual URL

/**
 * Makes a GET request to the specified URL using cURL.
 *
 * @param string $url
 * @return string The response returned from the API.
 */
function getRequest($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    if ($response === false) {
        echo 'Curl error: ' . curl_error($ch) . "\n";
    }
    curl_close($ch);
    return $response;
}

/**
 * Makes a POST request to the specified URL with given post data using cURL.
 *
 * @param string $url
 * @param array $postData
 * @return string The response returned from the API.
 */
function postRequest($url, $postData) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    // Set the POST flag and pass the POST data
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    if ($response === false) {
        echo 'Curl error: ' . curl_error($ch) . "\n";
    }
    curl_close($ch);
    return $response;
}

// ------------------------------
// 1. Test GET: Retrieve all data
// ------------------------------
echo "Testing GET getAll:\n";
$getAllUrl = $baseUrl . '?getAll';
$response = getRequest($getAllUrl);
echo "Response:\n" . $response . "<br/>\n\n";

// ------------------------------
// 2. Test POST: Add a new record
// ------------------------------
echo "Testing POST add:<br/>\n";
// Prepare POST data for adding a new record.
// The API expects a POST parameter 'add' (it can be any value) and a nested array 'data'.
$addData = [
    'add' => '1', // Flag to trigger the add branch
    'data[author]'  => '1',
    'data[title]'   => 'Test Title',
    'data[message]' => 'This is a test message.',
    'data[image]'   => 'test_image.jpg'
];
$response = postRequest($baseUrl, $addData);
echo "Response:\n" . $response . "<br/>\n\n";
$response = json_decode($response, true); // Decode the JSON response to an associative array
$testId = $response['id'] ?? null; // Capture the ID of the newly added record for later use
if ($testId) {
    echo "New record ID: $testId<br/>\n";
} else {
    echo "Failed to add new record.($testId)<br/>\n";
}

// ------------------------------
// 4. Test POST: Update the selected record
// ------------------------------
echo "Testing POST update on record with ID $testId:<br/>\n";
// Prepare data for updating the record.
$updateData = [
    'update' => '1', // Flag to trigger the update branch
    'id'     => $testId,
    'data[author]'  => '1',
    'data[title]'   => 'Updated Title',
    'data[message]' => 'This is the updated message.',
    'data[image]'   => 'updated_image.jpg'
];
$response = postRequest($baseUrl, $updateData);
echo "Response:\n" . $response . "<br/>\n";

// ------------------------------
// 3. Retrieve the updated list to pick a record for update/delete tests
// ------------------------------
echo "Retrieving updated data:\n";
$response = getRequest($getAllUrl);
echo "Response:\n" . $response . "<br/>\n\n";


// ------------------------------
// 5. Test POST: Delete the selected record
// ------------------------------
echo "Testing POST delete on record with ID $testId:<br/>\n";
// Prepare data for deleting the record.
$deleteData = [
    'delete' => '1', // Flag to trigger the delete branch
    'id'     => $testId
];
$response = postRequest($baseUrl, $deleteData);
echo "Response:\n" . $response . "\n\n";

// Optionally, you can retrieve all data again to confirm deletion.
echo "Final data after deletion:<br/>\n";
$response = getRequest($getAllUrl);
echo "Response:\n" . $response . "<br/>\n";
?>
