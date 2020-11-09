<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Paginator;
use Knp\Component\Pager\PaginatorInterface;
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
    public function index(Request $request,PaginatorInterface $paginator): Response
    {
        return $this->render('index/index.html.twig', [
            'tricks' => $paginator->paginate(
                $this->entityManager->getRepository('App:Post')->findAll(),
                $request->query->getInt('page',1),
                15
            ),
        ]);
    }
}
