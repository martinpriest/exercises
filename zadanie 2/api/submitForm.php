<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$filePath = __DIR__ . '/data.csv';

$data = json_decode(file_get_contents("php://input"));

if(is_string($data->firstname) && is_string($data->surname)
    && $data->firstname != '' && $data->surname != '') {
    // pasowalo by jeszcze zorbic sanityzacje :) 

    if(!file_exists($filePath)) fopen($filePath, "w");
    $fileContent = file_get_contents($filePath);
    $fileContent .= "\n{$data->firstname},{$data->surname}";
    file_put_contents($filePath, $fileContent);
    
    http_response_code(200);
    exit(json_encode(array("firstname" => "{$data->firstname}", "surname" => "{$data->surname}")));
} else {
    http_response_code(400);
    exit(json_encode(array("message" => "Podałeś mi fatalne dane :(")));
}
