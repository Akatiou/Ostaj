<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;

use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProduitController extends AbstractController
{
    /**
     * @Route("/admin/produit", name="produit_index", methods={"GET"})
     */
    public function indexProduit(ProduitRepository $produitRepository): Response
    {
        return $this->render('admin/produit/produit.html.twig', [
            'controller_name' => 'ProduitController',
            'produits' => $produitRepository->findAll()
        ]);
    }

    //--------------------------------------------------
    //   Ajout d'un produit
    //---------------------------------------------------

    /**
     * @Route("/admin/produit/new", name="produit_new", methods={"GET","POST"})
     */
    public function new_produit(Request $request, EntityManagerInterface $entityManager): Response
    {
        $produit = new Produit();

        $form = $this->createForm(ProduitType::class, $produit);

        if ($request->isMethod('POST')) {
            $form->submit($request->request->get($form->getName()));

            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();

                $produit->setNomProduit($data->getnomProduit());

                $entityManager->persist($produit);

                $entityManager->flush();

                $this->addFlash('success', "Votre avez ajouté le produit :  {$data->getnomProduit()} !");

                return $this->redirectToRoute('produit_index');
            }
        }

        return $this->render('admin/produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form->createView(),
        ]);
    }

    //--------------------------------------------------
    //   Voir UN produit
    //---------------------------------------------------

    /**
     * @Route("/admin/produit/{id}", name="produit_show", priority=-1, methods={"GET"}, requirements={"id"="\d+"})
     */
    public function show_produit($id, ProduitRepository $produitRepository): Response
    {
        $produit = $produitRepository->findOneBy([
            'id' => $id
        ]);

        if (!$produit) {
            throw $this->createNotFoundException("Le produit demandé n'existe pas");
        }

        return $this->render('admin/produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    /**
     * @Route("/admin/produit/{id}/edit", name="produit_edit")
     */

    public function edit($id, ProduitRepository $produitRepository, Request $request, EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $produit = $produitRepository->find($id);

        $form = $this->createForm(ProduitType::class, $produit);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $produit->setNomProduit($data->getnomProduit());

            $em->flush();

            $this->addFlash('success', "Le produit :  {$data->getnomProduit()} a bien été modifié !");

            return $this->redirectToRoute('produit_show', [
                'id' => $id
            ]);
        }

        $formView = $form->createView();

        return $this->render('admin/produit/edit.html.twig', [
            'produit' => $produit,
            'formView' => $formView
        ]);
    }

    //--------------------------------------------------
    //   Suppression d'un produit
    //---------------------------------------------------

    /**
     * @Route("/admin/produit/{id}", name="produit_delete", methods={"DELETE"})
     */
    public function delete_roduit($id, EntityManagerInterface $entityManager)
    {
        // Récupérer le repository de l'entité Produit
        $produitRepository = $entityManager->getRepository(Produit::class);

        // Récupérer le produit à supprimer en utilisant son ID
        $produit = $produitRepository->find($id);

        if (!$produit) {
            throw $this->createNotFoundException('Produit non trouvé avec l\'ID : ' . $id);
        }

        // Supprimer le produit
        $entityManager->remove($produit);
        $entityManager->flush();

        $this->addFlash('success', 'Le produit a bien été supprimé.');
        return $this->redirectToRoute('produit_index');
    }
}
