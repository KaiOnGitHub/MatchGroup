<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * We just redirect to the match group creation page
     */
    #[Route('/', name: 'home')]
    public function index(): Response {
        return $this->redirectToRoute('match_group_new');
    }
}
