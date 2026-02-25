<?php
declare(strict_types=1);

namespace App\Infrastructure\Mail;

use App\Domain\Shared\Interfaces\MailerInterface;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
use PHPMailer\PHPMailer\SMTP;

final class PHPMailerMailer implements MailerInterface
{
    private PHPMailer $mailer;

    public function __construct(
        ?string $host,
        int $port,
        ?string $username,
        ?string $password,
        string $from,
        string $encryption = 'tls'
    ) {
        $this->mailer = new PHPMailer(true);
        $this->mailer->CharSet = 'UTF-8';

        if ($host !== null) {
            $this->mailer->isSMTP();
            $this->mailer->Host = $host;
            $this->mailer->Port = $port;
            $this->mailer->Timeout = 8;
            $this->mailer->SMTPDebug = SMTP::DEBUG_OFF;

            $secureMode = strtolower(trim($encryption));
            if ($secureMode === 'ssl') {
                $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } elseif ($secureMode === 'tls') {
                $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            } else {
                $this->mailer->SMTPSecure = '';
                $this->mailer->SMTPAutoTLS = false;
            }

            $this->mailer->SMTPAuth = !empty($username);
            $this->mailer->Username = $username ?? '';
            $this->mailer->Password = $password ?? '';

            $this->mailer->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];
        } else {
            $this->mailer->isMail();
        }

        $this->mailer->setFrom($from);
    }

    public function send(string $to, string $subject, string $body, bool $isHtml = false): bool
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($to);
            $this->mailer->Subject = $subject;
            $this->mailer->isHTML($isHtml);
            $this->mailer->Body = $body;
            if ($isHtml) {
                $this->mailer->AltBody = strip_tags($body);
            }
            $this->mailer->send();
            return true;
        } catch (PHPMailerException $e) {
            error_log("PHPMailer Error: " . $e->getMessage());
            throw new \RuntimeException("Erro ao enviar email: " . $e->getMessage());
        }
    }
}