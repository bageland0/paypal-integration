<?php
namespace App\Actions;

use App\Services\OrderService;

use function App\redirect;

$service = new OrderService();

$name = $_POST['name'];
$email = $_POST['email'];

if (!$name || !$email) {
    redirect('/index.php?error');
}

$service->processOrder($name, $email);
