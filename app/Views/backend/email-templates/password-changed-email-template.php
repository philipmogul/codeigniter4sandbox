<p> Dear <b><?= $mail_data['user']->username; ?></b>, </p>
<p> Your password has been changed successfully. </p>
<p>
    Your new password is: <b><?= $mail_data['new_password']; ?></b>
</p>
<p> If you did not perform this action, please contact support immediately. </p>
<p> Regards, <br> Admin Team </p>