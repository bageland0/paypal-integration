<?php
require_once('utils.php');
require_once('services/order-service.php');

use Services\OrderService;

$service = new OrderService;

$name = $_POST['name'];
$email = $_POST['email'];

if (!$name || !$email) {
    redirect('/index.php?error');
}


