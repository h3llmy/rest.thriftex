<?php

defined('BASEPATH') OR exit('No direct script access allowed');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Smtp {

  public function SendEmail($kepada, $subjek, $pesan, $buffer = null) {

    $mail = new PHPMailer(true); // Set to true to enable exceptions

    try {

      // Server settings
      $mail->isSMTP();                                            // Send using SMTP
      $mail->Host       = 'ssl://smtp.googlemail.com';  // Set the SMTP server address
      $mail->Port       = 465;                                    // Set the SMTP port number
      $mail->SMTPAuth   = true;                                   // Enable SMTP authentication

      // Set your Gmail credentials
      $mail->Username   = 'thriftexofficial@gmail.com';  // Replace with your Gmail address
      $mail->Password   = 'vtpxagsoquywuizj';                // Replace with your app password

      // Set email content
      $mail->setFrom('info@thriftex.id', 'thriftex.id');
      $mail->addAddress($kepada);                                // Add recipient
      $mail->isHTML(true);                                         // Set email format to HTML
      $mail->Subject = $subjek;
      $mail->Body    = $pesan;

      // Optional attachment
      if (!empty($buffer)) {
        $mail->addStringAttachment($buffer, 'report.pdf', 'attachment', 'application/pdf');
      }

      // Send the email
      $mail->send();

      $response = array(
        'status' => true,
        'msg' => 'Email berhasil dikirim'
      );

    } catch (Exception $e) {

      $response = array(
        'status' => false,
        'msg' => 'Error sending email: ' . $mail->ErrorInfo
      );
    }

    return $response;
  }
}
