<?php
//headers

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Initialize api
include_once('../core/initialize.php');

error_log("Before instantiating Post object");
// Instantiate post 
$post = new Post($db);

$post->id = isset($_GET['id']) ? $_GET['id'] : die();
$post->read_single();
error_log("Post object after read_single(): " . print_r($post, true));

$post_arr = array(
    'id' => $post->id,
    'category_id' => $post->category_id,
    'category_name' => $post->category_name,
    'title' => $post->title,
    'body' => html_entity_decode($post->body),
    'author' => $post->author,
    'created_at' => $post->created_at
);

// Make JSON
print_r(json_encode($post_arr));