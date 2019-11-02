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
if (!isset($data->object_id)) {

    // message if value missed
    http_response_code(400);
    echo json_encode(array("error" => TRUE, "message" => "object id is missing."));

    die();
}

$locObject->id = $data->object_id;

if(!$locObject->getObjectDetails()){
    
    http_response_code(400);
    echo json_encode(array("error" => TRUE, "message" => "Unable to get object details."));
    die();
}

if(!$locObject->getObjectInfos()){
    
    http_response_code(400);
    echo json_encode(array("error" => TRUE, "message" => "Unable to get object infos."));
    die();
}

// set response code & answer
http_response_code(200);
echo json_encode(array(
    "error" => FALSE,
    "message" => "Found information.",
    "details" => utf8ize($locObject->entireObject),
    "information" => utf8ize($locObject->infos)));

// Debug
// echo json_last_error_msg();

?>