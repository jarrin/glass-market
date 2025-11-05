<?php
/**
 * Debug Rust Mailer
 */

$binaryPath = __DIR__ . '/email-service/target/release/glass-market-mailer.exe';

// Create test JSON
$request = [
    'to' => 'musieatsbeha633@gmail.com',
    'to_name' => 'Test User',
    'subject' => 'Test Subject',
    'body' => 'Test Body',
    'is_html' => false,
];

$jsonFile = tempnam(sys_get_temp_dir(), 'email_');
file_put_contents($jsonFile, json_encode($request));

echo "JSON file: $jsonFile\n";
echo "JSON content: " . file_get_contents($jsonFile) . "\n\n";

// Set environment and run
$cmd = "set GMAIL_USER=musieatsbeha633@gmail.com && set GMAIL_PASS=dfylmduqfpapcsqp && \"$binaryPath\" \"$jsonFile\" 2>&1";

echo "Command: $cmd\n\n";
echo "Output:\n";
system($cmd, $returnCode);

echo "\n\nReturn code: $returnCode\n";

unlink($jsonFile);
