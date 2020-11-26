<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="index")
     * @param Request $request
     * @param PostRepository $postRepository
     * @param Response $response
     * @return Response
     */
    public function index(Request $request, PostRepository $postRepository): Response
    {
        return $this->render('index/index.html.twig',[
            'tricks' => $postRepository->findAll()
        ]);
    }

}
