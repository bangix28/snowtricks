<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\services\image\ImageServices;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/post")
 */
class PostController extends AbstractController
{
    private $manager;

    private $imageServices;

    private $commentServices;

    public function __construct(EntityManagerInterface $manager, ImageServices $imageServices, CommentController $commentServices)
    {
        $this->manager = $manager;
        $this->imageServices = $imageServices;
        $this->commentServices = $commentServices;
    }

    /**
     * @Route("/", name="post_index", methods={"GET"})
     */
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('post/index.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="post_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        if ($this->getUser()){
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->imageServices->thumbnailUpload($form,$post);
            $this->imageServices->ImageUpload($form,$post);
            $post->setUser($this->getUser());
            $this->manager->persist($post);
            $this->manager->flush();
            return $this->redirectToRoute('post_index');
        }

        return $this->render('post/new.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
        }
        return $this->render('security/403.html.twig');
    }

    /**
     * @Route("/{id}", name="post_show")
     */
    public function show(Post $post, Request $request): Response
    {
        $form = false;
        if ($this->getUser())
        {
            $form = $this->commentServices->new($request,$post);
        }
            return $this->render('post/show.html.twig', [
                'post' => $post,
                'form' => $form
            ]);
    }

    /**
     * @Route("/{id}/edit", name="post_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Post $post): Response
    {
        if ($this->getUser() && $this->getUser() === $post->getUser()) {
            $form = $this->createForm(PostType::class, $post);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('post_index');
            }

            return $this->render('post/edit.html.twig', [
                'post' => $post,
                'form' => $form->createView(),
            ]);
        }
        return $this->render('security/403.html.twig');
    }

    /**
     * @Route("/delete/{id}", name="post_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Post $post): Response
    {
        if ($this->getUser()) {
            if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->request->get('_token'))) {
                $this->manager->remove($post);
                $this->manager->flush();
            }
            return $this->redirectToRoute('post_index');
        }
        return $this->render('security/403.html.twig');
    }
}
