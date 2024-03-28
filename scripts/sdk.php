<?php
require '../vendor/autoload.php';
$key     = new Cloudflare\API\Auth\APIKey('carl.xu.work@gmail.com', 'HiAhcD774BG2FkJ3q4q9eQB3CgtKVFgGCj9bEwTF');
$adapter = new Cloudflare\API\Adapter\Guzzle($key);
$user    = new Cloudflare\API\Endpoints\User($adapter);

echo $user->getUserID();