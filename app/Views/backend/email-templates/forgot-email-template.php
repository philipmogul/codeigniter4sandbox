<p>Dear <?= $mail_data['user']->username ?> </p>
<p>
    We received a request to reset your password for you account 
    <i><?= $mail_data['user']->email ?></i>. 
    <br />
    Click the link below to reset it:
    <br /><br />
    <a href="<?= $mail_data['actionLink'] ?>" target="_blank">RESET MY PASSWORD</a>

    <br /><br />
    If you did not request a password reset, please ignore this email or reply to let us know.
    <br /><br />
    This password reset link will expire in 15 minutes.

</p>