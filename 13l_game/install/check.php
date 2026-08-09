<?php
require_once __DIR__ . '/../api/_core/bootstrap.php';
header('Content-Type: text/html; charset=utf-8');
echo '<h2>13L Backend Check</h2>';
echo '<p>PHP Version: '.htmlspecialchars(PHP_VERSION).'</p>';
echo '<p>mysqli: '.(class_exists('mysqli') ? 'Enabled' : 'Missing').'</p>';
$c = db();
echo '<p>Database: '.($c ? 'Connected' : 'Not connected - edit api/_core/config.php').'</p>';
echo '<p><a href="../api/Home/HomeBasic">Test HomeBasic API</a></p>';
echo '<p><a href="../admin/">Open Admin</a></p>';
