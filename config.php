<?php
// Dynamic base URL configuration
// This will work regardless of project folder name

// Get the project root dynamically
$scriptPath = $_SERVER['SCRIPT_NAME'];
$pathParts = explode('/', trim($scriptPath, '/'));

// Find the base path (usually the first directory after domain)
if (count($pathParts) > 0) {
    $BASE_PATH = '/' . $pathParts[0];
} else {
    $BASE_PATH = '';
}

// Define base URL for assets and links
define('BASE_URL', $BASE_PATH);
define('PUBLIC_URL', BASE_URL . '/public');
define('VIEWS_URL', BASE_URL . '/resources/views');
define('CSS_URL', BASE_URL . '/resources/css');
define('JS_URL', BASE_URL . '/resources/js');
