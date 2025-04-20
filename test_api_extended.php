<?php
// test_api_extended.php

// Ange URL:en till din conversa.php – ändra denna efter behov
//$baseUrl = 'http://localhost/conversa-server/conversa.php';
$baseUrl = 'https://conversa-api.ntigskovde.se/conversa.php';



/**
 * Skickar en GET-förfrågan med cURL.
 * 
 * @param string $url URL att anropa
 * @return string Svaret från API:et.
 */
function getRequest($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    if ($response === false) {
        echo 'Curl error: ' . curl_error($ch) . "<br/>";
    }
    curl_close($ch);
    return $response;
}

/**
 * Skickar en POST-förfrågan med cURL.
 * 
 * @param string $url URL att anropa
 * @param array $postData Data att posta
 * @return string Svaret från API:et.
 */
function postRequest($url, $postData) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    if ($response === false) {
        echo 'Curl error: ' . curl_error($ch) . "<br/>";
    }
    curl_close($ch);
    return $response;
}

$token='admin'; // Giltig token för testning




echo "=== VALIDERINGSTESTER ===<br/>";


// 3. Validering med giltigt användarnamn och lösenord
echo "Test 3: Validering med giltigt användarnamn och lösenord...<br/>";
$url = $baseUrl . '?validate&username=admin&password=adminadmin';
$response = getRequest($url);
echo "Svar: " . $response . "<br/><br/>";

// 4. Validering med felaktigt lösenord
echo "Test 4: Validering med felaktigt användarnamn/lösenord...<br/>";
$url = $baseUrl . '?validate&username=testuser&password=wrongpass';
$response = getRequest($url);
echo "Svar: " . $response . "<br/><br/>";

echo "=== TESTA addUser ===<br/>";
// 14. Testa addUser med giltig token
echo "Test 13: Lägg till användare med giltig token...<br/>";
$url = $baseUrl . '?token='.$token;
$postData =[
    'data[username]' => 'testuser',
    'data[password]' => 'testpass',
    'data[display_name]' => 'Test User',
    'data[email]' => 'post@post',
    'data[admin]' => '0',
    'addUser' => '1'
];
$response = postRequest($url, $postData);
echo "Svar: " . $response . "<br/><br/>";

// 15. Testa addUser med ogiltig token
echo "Test 14: Lägg till användare med ogiltig token...<br/>";
$url = $baseUrl . '?token=invalidToken';
$postData =[
    'data[username]' => 'testuser2',
    'data[password]' => 'testpass2',
    'data[display_name]' => 'Test User 2',
    'data[email]' => 'post2@post',
    'data[admin]' => '0',
    'addUser' => '1'
];

$response = postRequest($url, $postData);
echo "Svar: " . $response . "<br/><br/>";

//16. getAllUsers med giltig token
echo "Test 15: Hämta alla användare med giltig token...<br/>";
$url = $baseUrl . '?token=admin';
$postData = [
    'getAllUsers' => '1'
];
$response = postRequest($url, $postData);
echo "Svar: " . $response . "<br/><br/>";



echo "=== GET ALL DATA TEST ===<br/>";
// 5. Hämta all data (fungerar oberoende av validering)
$url = $baseUrl . '?getAll';
$response = getRequest($url);
echo "Svar: " . $response . "<br/><br/>";

echo "=== POST ADD TESTER ===<br/>";
// 6. Positiv test: Lägg till ny post med giltig token
echo "Test 5: Lägg till post med giltig token...<br/>";
$url = $baseUrl . '?token='.$token;
$postData = [
    'add' => '1',
    'data[author]'  => '1',
    'data[title]'   => 'Valid Add',
    'data[message]' => 'Detta är ett positivt add-test.',
    'data[image]'   => 'valid_image.jpg'
];
$response = postRequest($url, $postData);
echo "Svar: " . $response . "<br/><br/>";

// 7. Negativt test: Försök lägga till post med ogiltig token (ska inte validera)
echo "Test 6: Lägg till post med ogiltig token...<br/>";
$url = $baseUrl . '?token=invalidToken';
$postData = [
    'add' => '1',
    'data[author]'  => '1',
    'data[title]'   => 'Invalid Add',
    'data[message]' => 'Detta är ett negativt add-test.',
    'data[image]'   => 'invalid_image.jpg'
];
$response = postRequest($url, $postData);
echo "Svar: " . $response . "<br/><br/>";


echo "=== FÖRBEREDD DATA FÖR UPDATE & DELETE TESTER ===<br/>";
// Hämta uppdaterad lista för att välja en post att uppdatera och radera
$url = $baseUrl . '?getAll';
$responseData = getRequest($url);
$dataArray = json_decode($responseData,true);
if (empty($dataArray)) {
    echo "Ingen data hittades. Avslutar testning.<br/>";
    exit;
} else {
    // Välj den senaste posten (som just lagts till)
    $lastRecord = end($dataArray);
    $recordId = $lastRecord['id'];
    echo "Vald post med ID: " . $recordId . "<br/><br/>";
}

echo "=== POST UPDATE TESTER ===<br/>";
// 8. Positivt test: Uppdatera post med giltig token
echo "Test 7: Uppdatera post med giltig token (bör lyckas)...<br/>";
$url = $baseUrl . '?token='.$token;
$postData = [
    'update' => '1',
    'id'     => $recordId,
    'data[author]'  => '1',
    'data[title]'   => 'Uppdaterad Titel',
    'data[message]' => 'Detta är ett positivt update-test.',
    'data[image]'   => 'updated_image.jpg'
];
$response = postRequest($url, $postData);
echo "Svar: " . $response . "<br/><br/>";

// 9. Negativt test: Uppdatera post med ogiltig token
echo "Test 8: Uppdatera post med ogiltig token (ska misslyckas)...<br/>";
$url = $baseUrl . '?token=invalidToken';
$postData = [
    'update' => '1',
    'id'     => $recordId,
    'data[author]'  => '1',
    'data[title]'   => 'Misslyckad Update',
    'data[message]' => 'Detta update-test ska misslyckas.',
    'data[image]'   => 'fail_image.jpg'
];
$response = postRequest($url, $postData);
echo "Svar: " . $response . "<br/><br/>";

// 10. Negativt test: Uppdatera en post som inte är din (antag id 9999 inte tillhör den validerade användaren)
echo "Test 9: Uppdatera post som inte är din (ska neka åtkomst)...<br/>";
$url = $baseUrl . '?token=testuser';
$postData = [
    'update' => '1',
    'id'     => '9999', // Vi antar att post med id 9999 inte ägs av användaren
    'data[author]'  => 'Unauthorized',
    'data[title]'   => 'Unauthorized Update',
    'data[message]' => 'Detta ska inte vara tillåtet.',
    'data[image]'   => 'unauth_image.jpg'
];
$response = postRequest($url, $postData);
echo "Svar: " . $response . "<br/><br/>";


echo "=== POST DELETE TESTER ===<br/>";

// 12. Negativt test: Försök radera post med ogiltig token
echo "Test 11: Radera post med ogiltig token (ska misslyckas)...<br/>";
$url = $baseUrl . '?token=invalidToken';
$postData = [
    'delete' => '1',
    'id'     => $recordId
];
$response = postRequest($url, $postData);
echo "Svar: " . $response . "<br/><br/>";

// 13. Negativt test: Försök radera en post som inte är din (t.ex. id 9999)
echo "Test 12: Radera post som inte är din (ska neka åtkomst)...<br/>";
$url = $baseUrl . '?token=testuser';
$postData = [
    'delete' => '1',
    'id'     => $recordId // Vi antar att denna post inte ägs av användaren
];
$response = postRequest($url, $postData);
echo "Svar: " . $response . "<br/><br/>";

// 11. Positivt test: Radera post med giltig token (posten antas tillhöra användaren)
echo "Test 10: Radera post med giltig token (bör lyckas)...<br/>";
$url = $baseUrl . '?token='.$token;
$postData = [
    'delete' => '1',
    'id'     => $recordId
];
$response = postRequest($url, $postData);
echo "Svar: " . $response . "<br/><br/>";



echo "=== SLUTLIG GET ALL DATA ===<br/>";
// Hämta all data efter testerna för att se tillståndet i databasen
$url = $baseUrl . '?getAll';
$response = getRequest($url);
echo "Svar: " . $response . "<br/>";


