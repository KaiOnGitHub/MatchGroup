<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\MatchGroupEntity;
use App\Entity\MessageEntity;
use App\Entity\PlayerEntity;
use App\Form\AddMessageType;
use App\Form\AddPlayerToGroupType;
use App\Form\MatchGroupType;
use App\Repository\MatchGroupRepository;
use App\Service\ChatService;
use App\Service\MatchGroupService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;
use function unserialize;

class MatchGroupController extends AbstractController
{
    public function __construct(
        private MatchGroupService    $matchGroupService,
        private MatchGroupRepository $matchGroupRepository,
        private ChatService          $chatService)
    {
    }

    #[Route('/{shortId}', name: 'match_group_view', methods: ['GET'])]
    public function index(string $shortId, Request $request): Response
    {
        $matchGroup = $this->matchGroupRepository->findOneBy(['shortId' => $shortId]);

        if (!$matchGroup) {
            throw $this->createNotFoundException('MatchGroup not found.');
        }

        // Used to color chat messages of the current user
        $this->matchGroupService->storeAuthCookie();

        // Used to check which players can be deleted by the current user
        $authTokens = $request->cookies->get(MatchGroupService::$playerAuthTokensCookieName);
        if ($authTokens) {
            $authTokens = unserialize($authTokens);
        }

        $joinGroupForm = $this->createForm(AddPlayerToGroupType::class);

        $chatUserName = $this->matchGroupService->getChatUserNameFromCookie() ?? null;
        $addMessageForm = $this->createForm(AddMessageType::class, ['sender' => $chatUserName]);

        return $this->render('match_group/index.html.twig', [
            'matchGroup' => $matchGroup,
            'joinForm' => $joinGroupForm->createView(),
            'authTokens' => $authTokens,
            'addMessageForm' => $addMessageForm->createView(),
            'scrollTo' => $request->query->get('scrollTo'),
        ]);
    }

    #[Route('/matchgroup/new', name: 'match_group_new', methods: ['GET', 'POST'])]
    public function createMatchGroup(Request $request): Response
    {
        $newGroupForm = $this->createForm(MatchGroupType::class, new MatchGroupEntity());

        $newGroupForm->handleRequest($request);

        if ($newGroupForm->isSubmitted() && $newGroupForm->isValid()) {
            $matchGroup = $newGroupForm->getData();

            $this->matchGroupRepository->save($matchGroup, true);

            // Pass the MatchGroup we just created to the view route
            return $this->redirectToRoute('match_group_view', ['shortId' => $matchGroup->getShortId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('match_group/create.html.twig', [
            'form' => $newGroupForm->createView(),
        ]);
    }

    #[Route('/matchgroup/add-player', name: 'add_player_to_matchgroup', methods: ['POST'])]
    public function addPlayerToMatchGroup(Request $request, HubInterface $hub): Response
    {
        $form = $this->createForm(AddPlayerToGroupType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $playerData = $form->getData();

            $shortId = $playerData['matchGroupShortId'];

            $player = new PlayerEntity();

            $player->setName($playerData['name']);
            $player->setEmail($playerData['email']);
            $player->setWantsNotifications((bool)$playerData['wantsNotifications']);

            $matchGroup = $this->matchGroupService->addPlayer($shortId, $player);


            $authTokens = $request->cookies->get(MatchGroupService::$playerAuthTokensCookieName);
            if ($authTokens) {
                $authTokens = unserialize($authTokens);
            }

            // 🔥 The magic happens here! 🔥
            // The HTML update is pushed to the client using Mercure
            $topic = 'players_group_' . $matchGroup->getId();

            $hub->publish(new Update(
                $topic,
                $this->renderView('players/player.stream.html.twig', [
                    'player' => $player,
                    'topic' => $topic,
                    'authTokens' => $authTokens,
                ])
            ));

            return $this->redirectToRoute('match_group_view', [
                'shortId' => $shortId,
                'scrollTo' => '#anmeldung-anchor',
            ]);
        }

        return $this->redirectToRoute('match_group_new');
    }

    #[Route('/matchgroup/remove-player', name: 'remove_player_from_match_group', methods: ['POST'])]
    public function removePlayerFromMatchGroup(Request $request, HubInterface $hub): Response
    {
        $playerId = $request->request->get('playerId');
        $matchGroupShortId = $request->request->get('matchGroupShortId');

        $isAuthenticated = $this->matchGroupService->clientIsAllowedToRemovePlayer($matchGroupShortId, $playerId, $request);

        if ($isAuthenticated) {
            $matchGroup = $this->matchGroupService->removePlayer($matchGroupShortId, $playerId);

            // 🔥 The magic happens here! 🔥
            // The HTML update is pushed to the client using Mercure
            $topic = 'notification_group_' . $matchGroup->getId();

            $message = 'Ein/ Spieler/in wurde abgemeldet. Seite neu laden um die Änderung zu sehen.';

            $hub->publish(new Update(
                $topic,
                $this->renderView('match_group/notification.stream.html.twig', [
                    'notificationTopic' => $topic,
                    'message' => $message,
                ])
            ));
        } else {
            // TODO: Throw an error?
        }

        return $this->redirectToRoute('match_group_view', [
            'shortId' => $matchGroupShortId,
            'scrollTo' => '#anmeldung-anchor',
        ]);
    }

    #[Route('/matchgroup/add-message', name: 'chat_add_message', methods: ['POST'])]
    public function addMessageToGroup(Request $request, HubInterface $hub): Response
    {
        // Check if chatUserName Cookie is set and pass it to created form
        $chatUserName = $this->matchGroupService->getChatUserNameFromCookie() ?? null;

        // if we have a chatUserName pass it as default value for the sender field to the form
        $addMessageForm = $this->createForm(AddMessageType::class, ['sender' => $chatUserName]);

        $addMessageForm->handleRequest($request);

        if ($addMessageForm->isSubmitted() && $addMessageForm->isValid()) {
            $data = $addMessageForm->getData();

            $matchGroupShortId = $data['matchGroupShortId'];
            $matchGroup = $this->matchGroupRepository->findOneBy(['shortId' => $matchGroupShortId]);

            $message = new MessageEntity();
            $message->setMatchGroup($matchGroup);
            $message->setSender($data['sender']);
            $message->setMessage($data['message']);

            $existingToken = $_COOKIE[$this->matchGroupService::$userAuthTokenCookieName] ?? null;

            if ($existingToken === null) {
                $existingToken = $this->matchGroupService->storeAuthCookie();
            }

            if (!$chatUserName) {
                $chatUserName = $data['sender'];
            }
            $this->matchGroupService->setChatUserNameCookie($data['sender']);

            $colorId = $this->matchGroupService->hashStringToRandomNumber($existingToken);
            $message->setRandomColor($colorId);
            $message->setAuthToken($existingToken);

            $this->chatService->addMessage($message);

            // 🔥 The magic happens here! 🔥
            // The HTML update is pushed to the client using Mercure
            $topic = 'group_' . $matchGroup->getId();

            $hub->publish(new Update(
                $topic,
                $this->renderView('chat/message.stream.html.twig', [
                    'message' => $message,
                    'topic' => $topic,
                ])
            ));

            return $this->redirectToRoute('match_group_view', [
                'shortId' => $matchGroupShortId,
                'scrollTo' => '#add_message_message',
                'chatUserName' => $chatUserName,
            ]);
        }

        return $this->render('chat/add_message.html.twig', [
            'form' => $addMessageForm,
        ]);
    }
}
