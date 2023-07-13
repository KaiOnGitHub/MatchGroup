<?php

namespace App\Service;

use App\Entity\MessageEntity;
use App\Repository\MatchGroupRepository;
use App\Repository\MessageRepository;

class ChatService
{
    public function __construct(
        private MessageRepository $messageRepository,
        private PlayerService $playerService,
    )
    {
    }

    public function addMessage(MessageEntity $message): void
    {
        $this->messageRepository->save($message, true);

        $matchGroup = $message->getMatchGroup();

        $players = $matchGroup->getPlayers();

        $subject = 'Neue Nachricht von ' . $message->getSender() . ' in "' . $matchGroup->getName() . '"';

        $content = $message->getSender() . ' schreibt: <br>' . $message->getMessage().'<br>';
        $content .= '<br><a href="' . $matchGroup->getUrl() . '">Zur Gruppe</a>';

        foreach ($players as $player) {
            $this->playerService->notifyPlayer($player, $subject, $content);
        }

    }
}
