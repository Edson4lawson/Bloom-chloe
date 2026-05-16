<?php
// Simuler une requête GET pour tester
$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET['per_page'] = 5;
$_GET['sort_by'] = 'created_at';
$_GET['sort_order'] = 'ASC';

include 'products/get_all.php';
?>
