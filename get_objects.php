<?php

function utf8ize( $mixed ) {
    if (is_array($mixed)) {
        foreach ($mixed as $key => $value) {
            $mixed[$key] = utf8ize($value);
        }
    } elseif (is_string($mixed)) {
        return mb_convert_encoding($mixed, "UTF-8", "UTF-8");
    }
    return $mixed;
}

// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// files needed to connect to database
include_once 'config/Database.php';
include_once 'objects/Object.php';

// get database connection
$database = new Database();
$db = $database->getConnection();

// instantiate other objects
$locObject = new Object($db);

// get posted data
$data = json_decode(file_get_contents("php://input"));

// check if data is set
if (!isset($data->user_lat) or !isset($data->user_long)) {

    // message if value missed
    http_response_code(400);
    echo json_encode(array("error" => TRUE, "message" => "user position is missing."));

    die();
}

$locObject->user_lat = $data->user_lat;
$locObject->user_long = $data->user_long;

if(!$locObject->getNearObjects()){
    
    http_response_code(400);
    echo json_encode(array("error" => TRUE, "message" => "Unable to get objects."));
    die();
}

// set response code & answer
http_response_code(200);
echo json_encode(array(
    "error" => FALSE,
    "message" => "Found objects.",
    "objects" => utf8ize($locObject->multi_objects)));

// Debug
// echo json_last_error_msg();

?>