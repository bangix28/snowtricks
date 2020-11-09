<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\UserEditType;
use App\Repository\UserRepository;
use App\services\image\ImageServices;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    private $manager;

    private $imageServices;

    public function __construct(EntityManagerInterface $manager,ImageServices $imageServices)
    {
        $this->manager = $manager;
        $this->imageServices = $imageServices;

    }

    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(UserInterface $user): Response
    {
        return $this->render('user/index.html.twig', [
            'user' => $user,
        ]);
    }


    /**
     * @Route("/edit", name="user_edit")
     */
    public function edit(Request $request,UserInterface $user,UserPasswordEncoderInterface $passwordEncoder)
    {
        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);
        $message = false;
        if ($form->isSubmitted() && $form->isValid())
        {
            if ($passwordEncoder->isPasswordValid($user,$form->get('plainPassword')->getData()))
            {
                if($form->get('image')->getData()) {
                    $this->imageServices->pictureUpload($form, $user);
                }
                $this->manager->persist($user);
                $this->manager->flush();
                $this->addFlash('success', 'Modification faites avec success');

            }else {
                $this->addFlash('error', 'Mot de passe erroné');
            }
        }
        return $this->render('user/edit.html.twig',  [
            'form' => $form->createView(),
            'user'=> $user,
            'message' => $message
        ]);
    }

    /**
     * @Route("/delete", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, UserInterface $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $this->container->get('security.token_storage')->setToken(null);

            $this->manager->remove($user);
            $this->manager->flush();
            $this->addFlash('success', 'Votre compte a bien été supprimer');
        }

        return $this->redirectToRoute('app_logout');
    }
}
