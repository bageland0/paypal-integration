<?php
namespace App;

function redirect($url, $statusCode = 303)
{
   header('Location: ' . $url, true, $statusCode);
   exit();
}
