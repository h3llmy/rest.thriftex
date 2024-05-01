<?php
require_once APPPATH . '/../configuration.php';
$config = new SConfig();
?>

<h1><?= $user->email; ?></h1>
<a href="<?= $config->_frontend_url . '/forgetPassword/' . $token; ?>">go to link</a>
