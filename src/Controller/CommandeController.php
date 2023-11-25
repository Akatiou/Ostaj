<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\DetailCommande;
use App\Form\CommandeType;

use App\Repository\ProduitRepository;
use App\Repository\CommandeRepository;
use App\Repository\DetailCommandeRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommandeController extends AbstractController
{
    /**
     * @Route("/admin/commande", name="commande_index", methods={"GET"})
     */
    public function commande(ProduitRepository $produitRepository, CommandeRepository $commandeRepository, DetailCommandeRepository $detailCommandeRepository): Response
    {
        return $this->render('admin/commande/commande.html.twig', [
            'controller_name' => 'CommandeController',
            'commande' => $commandeRepository->findAll(),
            'detailCommande' => $detailCommandeRepository->findAll(),
            'produit' => $produitRepository->findAll()
        ]);
    }

    //--------------------------------------------------
    //   Voir UN user
    //---------------------------------------------------

    /**
     * @Route("/admin/commande/{id}", name="commande_show", priority=-1, methods={"GET"}, requirements={"id"="\d+"})
     * @param Commande $commande
     * @return Response
     */
    public function show_commande(Commande $commande, ProduitRepository $produitRepository, DetailCommandeRepository $detailCommandeRepository): Response
    {
        return $this->render('admin/commande/show.html.twig', [
            'commande' => $commande,
            'produit' => $produitRepository->findAll(),
            'detailCommande' => $detailCommandeRepository
        ]);
    }
}
