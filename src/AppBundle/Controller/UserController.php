<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\EditPasswordType;
use AppBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * @Route("/login", name="app_signin")
     */
    public function loginAction()
    {
        $utils = $this->get('security.authentication_utils');

        return $this->render('login.html.twig', [
            'last_username' => $utils->getLastUsername(),
            'error' => $utils->getLastAuthenticationError(),
        ]);
    }

    /**
     * @Route("/login-check", name="app_check_signin")
     */
    public function loginCheckAction()
    {

    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction()
    {

    }

    /**
     * @Route("/register", name="register")
     */
    public function registerAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            // On enregistre l'utilisateur en base
            $em = $this->getDoctrine()->getManager();

            $this->get('app.manager.user')->manageCredentials($user);

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render('register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/account", name="account")
     */
    public function accountAction(Request $request)
    {
        $form = $this->createForm(UserType::class, $this->getUser());
        $form->remove('password');

        $form->handleRequest($request);

        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($this->getUser());
            $em->flush();

            $this->addFlash('info', 'Votre compte a bien été mis à jour');

            return $this->redirectToRoute('account');
        }

        return $this->render('account.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/change-password", name="change_password")
     */
    public function changePasswordAction(Request $request)
    {
        $user = $this->getUser();

        $form = $this->createForm(EditPasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isValid())
        {
            $em = $this->getDoctrine()->getManager();

            $this->get('app.manager.user')->manageCredentials($user);

            $em->persist($this->getUser());
            $em->flush();

            $this->addFlash('info', 'Votre mot de passe a bien été mis à jour');

            return $this->redirectToRoute('account');
        }

        return $this->render('edit_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}