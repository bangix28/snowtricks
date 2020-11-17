<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Form\SearchType;
use App\Repository\PostRepository;
use App\services\image\ImageServices;
use App\services\post\PostServices;
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

    private $postServices;

    public function __construct(EntityManagerInterface $manager, ImageServices $imageServices, CommentController $commentServices, PostServices $postServices)
    {
        $this->manager = $manager;
        $this->imageServices = $imageServices;
        $this->commentServices = $commentServices;
        $this->postServices = $postServices;
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
            $this->imageServices->ImageUpload($form,$post);
            $url = [];
            foreach($data = $form->get('video')->getData() as $video){
               $a =  $this->postServices->verifyURL($video);
               array_push($url,$a);

            }
            $post->setVideo($url);
            $post->setCreatedAt(new \DateTime());
            $post->setUser($this->getUser());
            $this->manager->persist($post);
            $this->manager->flush();
            return $this->redirectToRoute('post_show', ['id' => $post->getId()]);
        }

        return $this->render('post/new.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
        }
        return $this->render('security/403.html.twig');
    }

    /**
     * @Route("/listPost", name="post_list")
     * @param Request $request
     * @param PostRepository $postRepository
     * @return Response
     */
    public function handleSearch(Request $request, PostRepository $postRepository)
    {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $search = $form->get('search')->getData();
            return $this->render('post/index.html.twig', [
                'posts' => $postRepository->search($search),
                'search' => $form->createView()

            ]);
        }
        return $this->render('post/index.html.twig', [
            'posts' => $postRepository->findAll(),
            'search' => $form->createView()
        ]);
    }

    /**
     * @Route("/show/{id}", name="post_show")
     */
    public function show(Post $post, Request $request): Response
    {
        $form = false;
            if ($this->getUser()) {
                $form = $this->commentServices->new($request, $post);
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
                $post->setModifiedAt(new \DateTime());
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
