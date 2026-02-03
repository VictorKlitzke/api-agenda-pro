<?php
declare(strict_types=1);

namespace App\Infrastructure\Listeners;

use App\Domain\Shared\Events\EmailEventInterface;
use App\Domain\Shared\Interfaces\MailerInterface;

final class SendEmailListener
{
    public function __construct(
        private MailerInterface $mailer,
    ) {
    }

    public function handle(EmailEventInterface $event): void
    {
        $this->mailer->send(
            $event->getTo(),
            $event->getSubject(),
            $event->getBody(),
            $event->isHtml()
        );
    }
}