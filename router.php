<?php
$requestedFile = $_SERVER['REQUEST_URI'];

// Map requested URLs to actual files
$fileMapping = array(
  '/' => __DIR__ . '/landingpage.php',
  '/home' => __DIR__ . '/landingpage.php',
  '/login' => __DIR__ . '/login.php',
  // Add more mappings as needed
  '/evts' => __DIR__ . '/eventusr.php',
);

if (array_key_exists($requestedFile, $fileMapping)) {
  $fileToServe = $fileMapping[$requestedFile];
  if (file_exists($fileToServe)) {
    // Serve the requested file
    include_once $fileToServe;
    return false;
  }
}

// If requested file doesn't exist, redirect to custom 404 page
http_response_code(404);
include('custom-404.html');
