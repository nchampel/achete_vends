<?php

header('Content-Type: application/json');

echo json_encode([
    'method' => $_SERVER['REQUEST_METHOD'],
    'uri' => $_SERVER['REQUEST_URI'],
    'content_type' => $_SERVER['CONTENT_TYPE'] ?? null,
    'body' => file_get_contents('php://input'),
]);