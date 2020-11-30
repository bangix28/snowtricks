<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\services\image\ImageServices;
use App\services\post\PostServices;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/trick")
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
     * @Route("/new", name="post_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        if ($this->getUser()) {
            $post = new Post();
            $form = $this->createForm(PostType::class, $post);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->imageServices->ImageUpload($form, $post, $img = []);
                $this->imageServices->videoAdd($form, $post, $url = []);
                $post->setCreatedAt(new \DateTime());
                $post->setUser($this->getUser());
                $this->manager->persist($post);
                $this->manager->flush();
                $this->addFlash('success', 'Tricks create with success');
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
     * @Route("/show/{id}", name="post_show")
     */
    public function show(Post $post, Request $request, PaginatorInterface $paginator): Response
    {
        $form = false;
        if ($this->getUser()) {
            $form = $this->commentServices->new($request, $post);
        }
        return $this->render('post/show.html.twig', [
            'trick' => $post,
            'form' => $form,
            'comment' => $paginator->paginate($post->getComment(), $request->query->getInt('page', 1), 10)
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
                $this->imageServices->ImageUpload($form, $post, $img = $post->getImages());
                $this->imageServices->videoAdd($form, $post, $url = $post->getVideo());
                $post->setModifiedAt(new \DateTime());
                $this->manager->persist($post);
                $this->manager->flush();
                $this->addFlash('success', 'Post edited !');
                return $this->redirectToRoute('post_show', ['id' => $post->getId()]);
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
        if ($this->getUser() && $this->getUser() === $post->getUser()) {
            if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->request->get('_token'))) {
                $this->manager->remove($post);
                $this->manager->flush();
            }
            return $this->redirectToRoute('index');
        }
        return $this->render('security/403.html.twig');
    }

    /**
     * @Route("/image/delete", name="image_delete")
     */
    public function imageDelete(Request $request, PostRepository $repository)
    {

        $post = $repository->findOneBy(array('id' => $request->request->get('id')));
        if ($request->request->get('type') === 'image') {
            $array = $post->getImages();
            $type = "image";
            $delete = $request->request->get('id_image');
            $set = 'setImages';
        } else {
            $array = $post->getVideo();
            $type = "video";
            $delete = $request->request->get('id_video') - 1;
            $set = 'setVideo';
        }
        unset($array[$delete]);
        $new = array_merge($array);
        $post->$set($new);
        $this->manager->persist($post);
        $this->manager->flush();
        $this->addFlash('success', $type . ' supprimer');
        return $this->render('security/403.html.twig');
    }

}
