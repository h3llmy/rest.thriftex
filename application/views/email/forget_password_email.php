<?php
require_once APPPATH . '/../configuration.php';
$config = new SConfig();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Reset Password Thriftex</title>
</head>

<body>
    <table width="100%" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td align="center">
                <table width="500" cellspacing="0" cellpadding="0" border="0"
                    style="margin: 10px; border: 1px solid black; padding: 20px;">
                    <tr>
                        <td align="left">
                            <img src="https://thrifex.s3-id-jkt-1.kilatstorage.id/logo.jpeg" width="150" height="50"
                                alt="Thriftex Logo" style="margin-bottom: 20px;">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>Hi, <strong>
                                    <?= $user->email ?>
                                </strong>,</p>
                            <p>Sorry to hear that you’re having trouble logging into your account. Since we received a
                                message that you forgot your password, you can reset it using the link below.</p>
                        </td>
                    </tr>
                    <tr>
    <td align="center" style="margin-top: 20px;">
        <a href="<?= $config->_frontend_url ?>/create-password/<?= $token ?>"
           style="display: block; width: 100%; padding: 15px 0; background-color: black; border-radius: 4px; text-align: center; color: white; text-decoration: none; font-size: 16px;">
            Reset Password
        </a>
    </td>
</tr>
                    <tr>
                        <td>
                            <p>If you didn’t request a login link or a password reset, you can ignore this message.</p>
                        </td>
                    </tr>
                    <tr>
                        <td align="center">
                            <p>From</p>
                            <img src="https://thrifex.s3-id-jkt-1.kilatstorage.id/logo.jpeg" width="100" height="35"
                                alt="Thriftex Logo" />
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
