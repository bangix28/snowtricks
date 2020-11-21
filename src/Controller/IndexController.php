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
     */
    public function index(Request $request, PostRepository $postRepository): Response
    {
        $loop =+ $request->get('loop');
       $tricks = 5 + $loop;
       dump($tricks);
        return $this->render('index/index.html.twig', [
            'tricks' => $postRepository->findBy(array(),array(),$tricks),
            'loop' => $tricks
        ]);
    }


}
