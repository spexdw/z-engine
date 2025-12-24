<?php

namespace ZEngine\Core\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService
{
    private array $config;
    private PHPMailer $mailer;

    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->mailer = new PHPMailer(true);
        $this->setupMailer();
    }

    private function setupMailer(): void
    {
        $this->mailer->isSMTP();
        $this->mailer->Host = $this->config['SMTP_HOST'] ?? 'localhost';
        $this->mailer->SMTPAuth = $this->config['SMTP_AUTH'] ?? true;
        $this->mailer->Username = $this->config['SMTP_USERNAME'] ?? '';
        $this->mailer->Password = $this->config['SMTP_PASSWORD'] ?? '';
        $this->mailer->SMTPSecure = $this->config['SMTP_ENCRYPTION'] ?? 'tls';
        $this->mailer->Port = $this->config['SMTP_PORT'] ?? 587;
        $this->mailer->Timeout = $this->config['SMTP_TIMEOUT'] ?? 30;
        $this->mailer->SMTPDebug = $this->config['SMTP_DEBUG'] ?? 0;
        $this->mailer->CharSet = 'UTF-8';

        $fromAddress = !empty($this->config['SMTP_FROM_ADDRESS']) ? $this->config['SMTP_FROM_ADDRESS'] : 'noreply@zengine.app';
        $fromName = !empty($this->config['SMTP_FROM_NAME']) ? $this->config['SMTP_FROM_NAME'] : 'ZEngine';

        $this->mailer->setFrom($fromAddress, $fromName);
    }

    public function send(string $to, string $subject, string $body, array $data = []): bool
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();

            $this->mailer->addAddress($to);
            $this->mailer->Subject = $subject;
            $this->mailer->isHTML(true);
            $this->mailer->Body = $body;
            $this->mailer->AltBody = strip_tags($body);

            return $this->mailer->send();
        } catch (Exception $e) {
            error_log('Mail error: ' . $e->getMessage());
            return false;
        }
    }

}