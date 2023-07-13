<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\PlayerEntity;
use App\Service\Notification\NotificationServiceInterface;

class PlayerService
{
    public function __construct(private NotificationServiceInterface $notificationService)
    {
    }

    public function notifyPlayer(PlayerEntity $player, string $subject, string $message): void
    {
        if ($player->isWantsNotifications() === true) {
            $this->notificationService->sendNotification($player, $subject, $message);
        }
    }
}
