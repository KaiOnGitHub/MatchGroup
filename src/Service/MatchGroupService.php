<?php declare(strict_types=1);
// src/Service/MatchGroupService.php

namespace App\Service;

use App\Entity\MatchGroupEntity;
use App\Entity\PlayerEntity;
use App\Repository\MatchGroupRepository;
use App\Repository\PlayerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Uid\Uuid;
use function time;
use function unserialize;

class MatchGroupService
{
    static string $playerAuthTokensCookieName = 'playerAuthTokens';
    static string $userAuthTokenCookieName = 'userAuthToken';
    static string $userChatNameCookieName = 'userChatName';

    public function __construct(
        private EntityManagerInterface $entityManager,
        private MatchGroupRepository   $matchGroupRepository,
        private PlayerRepository       $playerRepository,
        private PlayerService          $playerService,
    )
    {
    }

    public function addPlayer(string $matchGroupShortId, PlayerEntity $player): MatchGroupEntity
    {
        $matchGroup = $this->matchGroupRepository->findOneBy(['shortId' => $matchGroupShortId]);

        // Check if the number of required players is already reached
        if ($matchGroup->getNumPlayersRequired() <= $matchGroup->getPlayers()->count()) {
            throw new \Exception('Die Gruppe ist leider bereits vollständig.');

            // TODO: Add a flash error

        }

        $matchGroup->addPlayer($player);

        $authToken = $this->storeAuthTokenForAddedPlayer($player);

        $this->matchGroupRepository->save($matchGroup, true);
        $this->playerRepository->save($player, true);

        // TODO: We should handle the notifications with event listeners and remove it from this method.
        if ($matchGroup->getNumPlayersRequired() === $matchGroup->getPlayers()->count()) {
            $subject = 'Gruppe nun vollständig: "' . $matchGroup->getName() . '"';
            $message = 'Neue Person beigetreten zu Gruppe <a href="' . $matchGroup->getUrl() . '">' . $matchGroup->getName() . '</a>' . '. Folgende Leute sind nun dabei:<br>';
            foreach ($matchGroup->getPlayers() as $player) {
                $message .= "<br>" . $player->getName();
            }

            foreach ($matchGroup->getPlayers() as $player) {
                $this->playerService->notifyPlayer($player, $subject, $message);
            }
        }

        $subject = $player->getName() . ' beigetreten zu: "' . $matchGroup->getName() . '"';
        $message = 'Neue Person beigetreten zu Gruppe <a href="' . $matchGroup->getUrl() . '">' . $matchGroup->getName() . '</a>' . '. Folgende Leute sind nun dabei:<br>';
        foreach ($matchGroup->getPlayers() as $player) {
            $message .= "<br>" . $player->getName();
        }

        foreach ($matchGroup->getPlayers() as $player) {
            $this->playerService->notifyPlayer($player, $subject, $message);
        }

        return $matchGroup;
    }

    public function removePlayer(string $matchGroupShortId, string $playerId): MatchGroupEntity
    {
        $matchGroup = $this->matchGroupRepository->findOneBy(['shortId' => $matchGroupShortId]);
        $player = $this->playerRepository->find($playerId);

        $matchGroup->removePlayer($player);

        // TODO: Handle this via events
        $subject = $player->getName() . ' ausgetreten aus: "' . $matchGroup->getName() . '"';
        $message = $player->getName() . ' hat die Gruppe <a href="' . $matchGroup->getUrl() . '">' . $matchGroup->getName() . '</a>' . ' verlassen. Folgende Leute sind noch dabei:<br>';
        foreach ($matchGroup->getPlayers() as $player) {
            $message .= "<br>" . $player->getName();
        }

        foreach ($matchGroup->getPlayers() as $player) {
            $this->playerService->notifyPlayer($player, $subject, $message);
        }

        $this->entityManager->persist($matchGroup);
        $this->entityManager->flush();

        return $matchGroup;
    }

    public function clientIsAllowedToRemovePlayer(string $matchGroupShortId, string $playerId, Request $request): bool
    {
        $matchGroup = $this->matchGroupRepository->findOneBy(['shortId' => $matchGroupShortId]);
        $player = $this->playerRepository->find($playerId);

        // Check if the client is allowed to remove the player
        $authToken = $request->cookies->get(self::$playerAuthTokensCookieName);
        $authToken = unserialize($authToken);

        if (!in_array($player->getAuthToken(), $authToken)) {
            return false;
        }

        return true;
    }

    /*
     * We use this cookie to identify the user and color his chat messages
     */
    public function storeAuthCookie(): string
    {
        $cookieName = self::$userAuthTokenCookieName;

        $existingToken = $_COOKIE[$cookieName] ?? null;

        if ($existingToken === null) {
            // generate an unique string as auth token
            $authToken = Uuid::v4()->toBase32();

            // Set the token as a cookie, expire after 4 years
            $expiration = time() + 3600 * 24 * 30 * 12 * 4;
            setcookie($cookieName, $authToken, $expiration, '/');
        }

        return $authToken ?? $existingToken;
    }

    /*
    * TODO: We could use the same cookie here as in storeAuthCookie(). There is probably no need to have a unique cookie for each player.
    */
    public function storeAuthTokenForAddedPlayer(PlayerEntity $player): string
    {
        // generate an unique string as auth token
        $authToken = Uuid::v4()->toBase32();

        $player->setAuthToken($authToken);
        $this->updateAuthTokensCookie($authToken);

        $this->entityManager->persist($player);
        $this->entityManager->flush();

        return $authToken;
    }

    public function updateAuthTokensCookie(string $authToken): void
    {
        $cookieName = self::$playerAuthTokensCookieName;

        $existingTokens = $_COOKIE[$cookieName] ?? [];
        if (!is_array($existingTokens)) {
            $existingTokens = unserialize($existingTokens);
        }

        // Add the new token to the existing tokens array
        $existingTokens[] = $authToken;

        // Serialize the tokens array
        $serializedTokens = serialize($existingTokens);

        // Set the serialized tokens as a cookie, expire after 1 month
        $expiration = time() + 3600 * 24 * 30;
        setcookie($cookieName, $serializedTokens, $expiration, '/');
    }

    public function setChatUserNameCookie(string $userName): void
    {
        // Set the token as a cookie, expire after 4 years
        $expiration = time() + 3600 * 24 * 30 * 12 * 4;
        setcookie($this::$userChatNameCookieName, $userName, $expiration, '/');
    }

    public function getChatUserNameFromCookie(): string
    {
        return $_COOKIE[$this::$userChatNameCookieName] ?? '';
    }

    public function hashStringToRandomNumber(string $string): int
    {
        // Generate a hash value from the input string
        $hashValue = md5($string);

        // Convert the hash value to an integer
        $decimalValue = hexdec(substr($hashValue, 0, 8));

        // Map the integer to a value between 1 and 20
        $mappedValue = (($decimalValue % 20) + 1);

        return $mappedValue;
    }

}
