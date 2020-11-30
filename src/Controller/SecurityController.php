<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use App\Form\ForgotPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('index');
        }
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/forgotPassword", name="security_forgot_password")
     */
    public function forgotPassword(Request $request, MailerInterface $mailer, TokenGeneratorInterface $tokenGenerator)
    {
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->manager->getRepository('App:User')->findOneBy(array('mail' => $form->get('mail')->getData()));
            if ($user) {
                $token = $tokenGenerator->generateToken();
                $user->setResetToken($token);
                $this->manager->persist($user);
                $this->manager->flush();
                $email = (new TemplatedEmail())
                    ->from('contact@kenolane-granger.com')
                    ->to($form->get('mail')->getData())
                    ->subject('Password recovery')
                    ->htmlTemplate('security/forgotPassword.html.twig')
                    ->context([
                        'user' => $user,
                        'token' => $token
                    ]);

                $mailer->send($email);
                $this->addFlash('success', 'Email send with success');
                return $this->redirectToRoute('index');
            } else {
                $this->addFlash('error', "this email does not exist");
            }
        }

        return $this->render('security/recoverPassword.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/recoverPassword/{token}", name="security_recover_password")
     * @param Request $request
     * @param $token
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function recoverPassword(Request $request, UserPasswordEncoderInterface $passwordEncoder, $token)
    {
        $user = $this->manager->getRepository('App:User')->findOneBy(array('reset_token' => $token));

        if (!$user) {
            $this->addFlash('error', 'Unknow token');
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(ChangePasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setResetToken(null);
            $user->setPassword($passwordEncoder->encodePassword(
                $user, $form->get('plainPassword')->getData()
            ));
            $this->manager->persist($user);
            $this->manager->flush();

            $this->addFlash('success', 'Password change');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/recoverPassword.html.twig', ['form' => $form->createView()]);
    }
}
