<?php
/**
 * Created by PhpStorm.
 * User: hebidu
 * Date: 2017/1/9
 * Time: 10:39
 */

namespace app\src\tool\helper;


use app\src\base\helper\ResultHelper;

class EmailHelper
{


    public function send($to_email,$title,$content,$from_email='postmaster@itboye.com'){

        vendor('phpmailer/phpmailer/PHPMailerAutoload');

        //Create a new PHPMailer instance
        $mail = new \PHPMailer();
        //SMTP 配置
        $mail->isSMTP();                                      // Set mailer to use SMTP
//        $mail->SMTPDebug = 4;
        $mail->Host = 'smtp.itboye.com';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'postmaster@itboye.com';                 // SMTP username
        $mail->Password = 'hbdHBD136799711';                           // SMTP password
        $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 25;                                    // TCP port to connect to

        $mail->CharSet = "UTF-8";
        //发件人
        $mail->setFrom('postmaster@itboye.com', 'itboye');
        //Set an alternative reply-to address
        $mail->addReplyTo('postmaster@itboye.com', 'itboye');
        //收件人
        $mail->addAddress($to_email, $to_email);
        //Set the subject line
        $mail->Subject = $title;
        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body
//        $mail->msgHTML("html=>'<b>best</b>'");
        //Replace the plain text body with one created manually
        $mail->AltBody = '邮件内容(可选)';
        $mail->Body = $content;// "邮件内容";
        $mail->isHTML();
        //Attach an image file
//        $mail->addAttachment('images/phpmailer_mini.png');

        //send the message, check for errors
        if (!$mail->send()) {
            return ResultHelper::error("Mailer Error: " . $mail->ErrorInfo);
        } else {
            return ResultHelper::success("Message sent!");
        }
    }
}