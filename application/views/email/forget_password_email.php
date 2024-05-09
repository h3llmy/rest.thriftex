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
    <style>
      body {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
      }

      main {
        max-width: 500px;
        margin: 10px auto;
        padding: 20px;
        border: 1px solid black;
      }

      .logo {
        width: 150px;
        height: 50px;
        margin-bottom: 20px;
      }

      .button {
        padding: 15px 0;
        background-color: black;
        border-radius: 4px;
        width: 100%;
        display: inline-block;
        text-align: center;
        color: white;
        font-size: 16px;
        text-decoration: none;
        margin-top: 20px;
      }

      .button-redirect {
        color: white; /* Change anchor text color to white */
        text-decoration: none; /* Remove underline */
      }

      footer {
        margin-top: 20px;
        text-align: center;
      }

      footer .img-footer {
        width: 100px;
        height: 35px;
      }
    </style>
  </head>
  <body>
    <main>
      <header>
        <img
          src="https://thrifex.s3-id-jkt-1.kilatstorage.id/logo.jpeg"
          class="logo"
          alt="Thriftex Logo"
        />
      </header>
      <section>
        <p>Hi, <strong><?= $user->email ?></strong>,</p>
        <p>
          Sorry to hear that you’re having trouble logging into your account.
          Since we received a message that you forgot your password, you can reset it using the link below.
        </p>
      </section>
      <section>
        <a href="<?= $config->_frontend_url ?>/create-password/<?= $token ?>" class="button button-redirect">Reset Password</a>
      </section>
      <section>
        <p>
          If you didn’t request a login link or a password reset, you can ignore
          this message.
        </p>
      </section>
      <footer>
        <p>From</p>
        <img
          src="https://thrifex.s3-id-jkt-1.kilatstorage.id/logo.jpeg"
          class="img-footer"
          alt="Thriftex Logo"
        />
      </footer>
    </main>
  </body>
</html>
