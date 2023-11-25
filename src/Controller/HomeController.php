<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\Commande;
use App\Entity\DetailCommande;

use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home", methods={"GET","POST"})
     */
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupérer la liste des produits depuis votre base de données

        $produit = $entityManager->getRepository(Produit::class)->findAll();

        // Créer une nouvelle instance de l'entité Commande
        $commande = new Commande();

        // Créer le formulaire en passant la liste des produits et l'entité Commande comme options
        $form = $this->createForm(CommandeType::class, $commande, ['produit' => $produit]);

        // Gérer la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            // Traiter les données du formulaire ici, par exemple, sauvegarder la commande en base de données
            $commande->setNomClient($data->getnomClient());

            // Créer une nouvelle instance de DetailCommande et l'associer à la commande
            $detailCommande = new DetailCommande();
            $detailCommande->setNomProduit($data->getNomProduit()); // Ajustez la récupération du nom du produit selon vos besoins
            $detailCommande->setQuantite($data->getQuantite()); // Ajustez la récupération de la quantité selon vos besoins

            // Associer la DetailCommande à la Commande
            $commande->addDetailCommande($detailCommande);

            $entityManager->persist($commande);
            $entityManager->persist($detailCommande);

            $entityManager->flush();

            $this->addFlash('sucess', "{$data->getnomClient()}, Votre réservation a bien été enregistré !");

            // Redirection vers une autre page après la soumission réussie du formulaire
            return $this->redirectToRoute('home');
        }

        return $this->render('home/home.html.twig', [
            'commande' => $commande,
            'form' => $form->createView()
        ]);
    }
}
