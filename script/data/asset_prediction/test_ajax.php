<?php
/**
 * TEST FILE - Debug AJAX path & DB connection
 */
header('Content-Type: application/json');

// Test DB connection
include "../../library/db_connection.php";

$db_ok = isset($db_connection) && $db_connection !== null;

echo json_encode([
    'success' => true,
    'message' => 'Connection OK!',
    'db_connection' => $db_ok ? 'Connected' : 'Failed',
    'file' => __FILE__,
    'time' => date('Y-m-d H:i:s')
]);
?>
