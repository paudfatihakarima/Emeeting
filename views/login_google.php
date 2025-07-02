<?php
require '../config/google-config.php';
$auth_url = $client->createAuthUrl();
?>
<a href="<?= $auth_url ?>">Login dengan Google</a>
