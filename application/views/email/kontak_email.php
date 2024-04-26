<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .email-header {
            background-color: #2e2e2e;
            color: white;
            padding: 10px 20px;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            text-align: center;
        }

        .email-body {
            padding: 20px;
            line-height: 2;
        }

        .email-footer {
            text-align: center;
            padding: 10px 20px;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="email-header">
            Contact Information
        </div>
        <div class="email-body">
            <strong>Name: </strong><?= $nama; ?><br>
            <strong>Email: </strong> <a href="mailto:<?= $email; ?>"><?= $email; ?></a><br>
            <strong>Phone Number: </strong><?= $no_tlp; ?><br>
            <strong>Message: </strong> <?= $pesan; ?>
        </div>
        <div class="email-footer">
            © 2024 Thriftex Official. All rights reserved.
        </div>
    </div>
</body>

</html>