<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


function sendMail($senderEmail, $senderName, $senderPassword, $recipientName, $recipientEmail, $subject, $body, $attachmentsArray = [])
{

    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = $senderEmail;                     //SMTP username
        $mail->Password   = $senderPassword;                               //SMTP password //jfltubgqbwniivya
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom($senderEmail, $senderName);
        $mail->addAddress($recipientEmail);    //Name is optional
        $mail->addReplyTo($recipientEmail, $recipientName);
        $mail->addCC($recipientEmail);
        $mail->addBCC($recipientName);

        //Attachments
        if (count($attachmentsArray) > 0) {
            $mail->addAttachment('/var/tmp/file.tar.gz');         
            foreach ($attachmentsArray as $attachment) {
                $mail->addAttachment($attachment->path, $attachment->name);
            }
        }

        //Content
        $mail->isHTML(true);                                  
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
