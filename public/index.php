<?php

require_once __DIR__ . '/../src/config.php';

if ($_SERVER['REQUEST_URI'] == '/' && $_SERVER['REQUEST_METHOD'] == 'GET') {
    require_once __DIR__ . '/../src/index.php';
} else if ($_SERVER['REQUEST_URI'] == '/actions/order-action' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once __DIR__ . '/../src/Actions/order-action.php';
} else {
    echo '404';
}
