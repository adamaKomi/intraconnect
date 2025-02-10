<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

// Récupérer les variables d'environnement
$outlookEmail = "adamakomi15@gmail.com"; //getenv('OUTLOOK_EMAIL');
$outlookPassword = "adama133?"; // getenv('OUTLOOK_PASSWORD');

// Paramètres SMTP pour Outlook.com
$mail->isSMTP();
$mail->Host = 'smtp-mail.outlook.com';
$mail->SMTPAuth = true;
$mail->Username = $outlookEmail;
$mail->Password = $outlookPassword;
$mail->SMTPSecure = 'tls';
$mail->Port = 587;

// $mail->SMTPDebug = 2; // Niveau de débogage élevé

// Expéditeur
$mail->setFrom($outlookEmail, 'IntraConnect');

$mail->CharSet = 'UTF-8';
$mail->isHTML(true);
