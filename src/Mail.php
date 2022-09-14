<?php

namespace Awesome;

use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mail
{
    /**
     * Config
     * @var Config
     */
    protected $config;

    /**
     * Mail constructor
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Send email
     * @param string|array $to
     * @param string $subject
     * @param string $body
     * @param array $attachments
     * @return void
     * @throws \Exception
     */
    public function send($to, $subject, $body, $attachments = []): void
    {
        //Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_OFF;
            $mail->isSMTP();
            $mail->Host = $this->config->get('mail.host');
            $mail->SMTPAuth = true;
            $mail->Username = $this->config->get('mail.username');
            $mail->Password = $this->config->get('mail.password');
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = $this->config->get('mail.port');

            //Recipients
            $mail->setFrom($this->config->get('mail.from'), env('APP_NAME'));

            if (is_array($to)) {
                foreach ($to as $email) {
                    $mail->addAddress($email);
                }
            } else {
                $mail->addAddress($to);
            }

            //Attachments
            foreach ($attachments as $attachment) {
                $mail->addAttachment($attachment);
            }

            //Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = strip_tags($body);

            $mail->send();
        } catch (Exception $e) {
            throw new \Exception($mail->ErrorInfo);
        }
    }
}
