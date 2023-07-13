<?php

namespace App\Service\Notification;

use App\Entity\PlayerEntity;

interface NotificationServiceInterface
{
    public function sendNotification(PlayerEntity $recipient, string $subject, string $message): void;
}
