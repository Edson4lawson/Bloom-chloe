<?php
require_once __DIR__ . '/config/db.php';
$ch = curl_init('http://localhost:8080/auth/login.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$data = json_encode(['email' => 'admin@example.com', 'password' => 'admin123']);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
$response = curl_exec($ch);
if($response===false){ echo 'Curl error: '.curl_error($ch); }
else{ $json = json_decode($response, true); if(isset($json['access_token'])){ echo $json['access_token']; } else { echo 'Login failed: '.print_r($json, true); } }

?>
