<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\Produit;

use App\Repository\UserRepository;
use App\Repository\ProduitRepository;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="dashboard", methods={"GET"})
     */
    public function dashboard(ProduitRepository $produitRepository, UserRepository $userRepository, CommandeRepository $commandeRepository): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'controller_name' => 'AdminController',
            'produit' => $produitRepository->findAll(),
            'users' => $userRepository->findAll(),
            'commande' => $commandeRepository->findAll()
        ]);
    }


    //--------------------------------------------------
    //               ADMIN USER !!!!
    //---------------------------------------------------

    //--------------------------------------------------
    //   Voir tous les users
    //---------------------------------------------------

    /**
     * @Route("/admin/user", name="user_index", methods={"GET"})
     */
    public function indexUser(UserRepository $userRepository): Response
    {
        return $this->render('admin/user/user.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    //--------------------------------------------------
    //   Voir UN user
    //---------------------------------------------------

    /**
     * @Route("/admin/user/{id}", name="user_show", priority=-1, methods={"GET"}, requirements={"id"="\d+"})
     * @param User $user
     * @return Response
     */
    public function show_user(User $user): Response
    {
        return $this->render('admin/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    //--------------------------------------------------
    //   Ajout d'un user
    //---------------------------------------------------

    /**
     * @Route("/admin/user/new", name="user_new", methods={"GET","POST"})
     */
    public function new_user(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($user);

            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('admin/user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /*--------------------------------------------------------------
    MODIFIER LE "PROFIL" D'UN UTILISATEUR (EDIT)
--------------------------------------------------------------*/

    /**
     * @Route("/admin/user/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit_user(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /*--------------------------------------------------------------
    SUPPRIMER LE "PROFIL" D'UN UTILISATEUR (DELETE)
--------------------------------------------------------------*/

    /**
     * @Route("/admin/user/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete_user(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            // $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }
}