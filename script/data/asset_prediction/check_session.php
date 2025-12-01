<?php
session_start();
echo "<pre>";
echo "Session Status: " . (session_status() == PHP_SESSION_ACTIVE ? "ACTIVE" : "INACTIVE") . "\n\n";
echo "Session Data:\n";
print_r($_SESSION);
echo "</pre>";
