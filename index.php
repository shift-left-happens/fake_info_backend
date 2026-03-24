<?php

require_once 'src/FakeInfo.php';

use App\FakeInfo;

$reportError = function (int $error = -1) {
    switch ($error) {
        case 0:
            http_response_code(405);
            $message = 'Incorrect HTTP method';
            break;
        case 1:
            http_response_code(404);
            $message = 'Incorrect API endpoint';
            break;
        case 2:
            http_response_code(400);
            $message = 'Incorrect GET parameter value';
            break;
        default:
            http_response_code(500);
            $message = 'Unknown error';
    }
    echo json_encode([
        'error' => $message
    ]);
    exit();
};

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    $reportError(0);
}

$url = strtok($_SERVER['REQUEST_URI'], "?");    // GET parameters are removed
// If there is a trailing slash, it is removed, so that it is not taken into account by the explode function
if (substr($url, strlen($url) - 1) == '/') {
    $url = substr($url, 0, strlen($url) - 1);
}
// Everything up to the folder where this file exists is removed.
// This allows the API to be deployed to any directory in the server
$url = substr($url, strpos($url, basename(__DIR__)));

$urlPieces = explode('/', urldecode($url));

header('Content-Type: application/json; charset=utf-8');
header('Accept-version: v1');

$fakePerson = new FakeInfo();
http_response_code(200);

if (count($urlPieces) === 1) {
    $reportError(1);
}

switch ($urlPieces[1]) {
    case 'cpr':
        echo json_encode(['CPR' => $fakePerson->getCpr()]);
        break;
    case 'name-gender':
        echo json_encode($fakePerson->getFullNameAndGender());
        break;
    case 'name-gender-dob':
        echo json_encode($fakePerson->getFullNameGenderAndBirthDate());
        break;
    case 'cpr-name-gender':
        echo json_encode($fakePerson->getCprFullNameAndGender());
        break;
    case 'cpr-name-gender-dob':
        echo json_encode($fakePerson->getCprFullNameGenderAndBirthDate());
        break;
    case 'address':
        echo json_encode($fakePerson->getAddress());
        break;
    case 'phone':
        echo json_encode(['phoneNumber' => $fakePerson->getPhoneNumber()]);
        break;
    case 'person':
        $numPersons = $_GET['n'] ?? 1;
        $numPersons = abs((int)$numPersons);
        switch (true) {
            case $numPersons === 0:
                $reportError(2);
                break;
            case $numPersons === 1:
                echo json_encode($fakePerson->getFakePerson());
                break;
            case $numPersons > 1 && $numPersons <= 100:
                echo json_encode($fakePerson->getFakePersons($numPersons), JSON_INVALID_UTF8_SUBSTITUTE);
                break;
            default:
                $reportError(2);
        }
        break;
    default:
        $reportError(1);
}
