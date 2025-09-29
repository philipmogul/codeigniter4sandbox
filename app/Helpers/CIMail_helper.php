<?php 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Send Email function using phpmailer 
if (!function_exists("sendEmail")) {
    function sendEmail($mailConfig) {
        require 'PHPMailer/src/Exception.php';
        require 'PHPMailer/src/PHPMailer.php';
        require 'PHPMailer/src/SMTP.php';

        $mail = new PHPMailer(true);
        $mail->SMTPDebug = 0;
        $mail->isSMTP();
        $mail->Host = env('EMAIL_HOST');
        $mail->SMTPAuth = true;
        $mail->Username = env('EMAIL_USERNAME');
        $mail->Password = env('EMAIL_PASSWORD');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = env('EMAIL_PORT');

        $mail->setFrom(  env('EMAIL_FROM_ADDRESS'), env('EMAIL_FROM_NAME') );
        $mail->addAddress($mailConfig['mail_recepient_email'], $mailConfig['mail_recepient_name']);
        $mail->isHTML(true);
        $mail->Subject = $mailConfig['mail_subject'];
        $mail->Body = $mailConfig['mail_body'];
        if($mail->send()) {
            return true;
        } else {
            log_message('error', 'Mail Error: '.$mail->ErrorInfo);
            return false;
        }

    }

}



