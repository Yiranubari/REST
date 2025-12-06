<?php
//headers

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: PUT'); // GET, PUT, DELETE
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization,X-Requested-With');

// Initialize api
include_once '../core/initialize.php';

// Instantiate post 
$post = new Post($db);

// Get raw posted data
$data = json_decode(file_get_contents("php://input"));

// Set post properties
$post->id = $data->id;
$post->title = $data->title;
$post->body = $data->body;
$post->author = $data->author;
$post->category_id = $data->category_id;

// Create post
if ($post->update()) {
    echo json_encode(array('message' => 'Post Updated'));
} else {
    echo json_encode(array('message' => 'Post Not Updated'));
}
