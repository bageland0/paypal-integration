<?php
namespace App\Mail;

use Exception;
use PHPMailer\PHPMailer\PHPMailer;

class Mailer {
    private static $instance;

    private $smtpHost;

    private $smtpPort;

    private $smtpUser;

    private $smtpPass;

    private $fromEmail;

    private $adminEmail;

    private function __construct() {
        $this->smtpHost = $_ENV['SMTP_HOST'];
        $this->smtpPort = $_ENV['SMTP_PORT'];
        $this->smtpUser = $_ENV['SMTP_USER'];
        $this->smtpPass = $_ENV['SMTP_PASS'];
        $this->fromEmail = $_ENV['SERVER_EMAIL'];
        $this->adminEmail = $_ENV['ADMIN_EMAIL'];
    }
        
    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function sendOrderEmails($customerEmail, $customerName, $uuid, $status) {
        $mailer = $this->getMailer();
        $mailer->addAddress($customerEmail);
        $mailer->addAddress($this->adminEmail);
        $subject = '';
        $body = '';

        if ($status === 'pending') {
            $subject = 'Заказ ' . $uuid;
            $body = "Заказ был создан";
        } else if ($status === 'paid') {
            $subject = 'Заказ ' . $uuid . ' оплачен';
            $body = "Заказ оплачен";
        } else {
            $subject = 'Заказ ' . $uuid . ' отменен';
            $body = "Заказ отменен";
        }

        $mailer->isHTML(true);
        $mailer->Subject = $subject;
        $mailer->Body = $body;
        try {
            $mailer->send();
        } catch (Exception $e) {
            error_log("Ошибка отправления почты: " . $mailer->ErrorInfo);
        }

    }

    private function getMailer() {
        $mailer = new PHPMailer(true);
        $mailer->isSMTP();
        $mailer->Host = $this->smtpHost;
        $mailer->SMTPAuth = true;
        $mailer->Username = $this->smtpUser; 
        $mailer->Password = $this->smtpPass;
        $mailer->Port = $this->smtpPort;
        $mailer->CharSet = 'UTF-8';
        $mailer->Encoding = 'base64';
        $mailer->setFrom($this->fromEmail, 'Отправитель');

        return $mailer;
    }
}
