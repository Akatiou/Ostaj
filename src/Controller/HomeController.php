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

            $entityManager->persist($commande);
            $entityManager->flush();

            // Maintenant, après avoir enregistré la commande, nous allons créer la DetailCommande associée à cette commande


            // Récupérez les données des produits et quantités soumises dans le formulaire
            $detailCommandes = $data->getDetailCommande();

            foreach ($detailCommandes as $detailCommandeData) {
                $produitId = $detailCommandeData->getProduit()->getId();
                $produit = $entityManager->getRepository(Produit::class)->find($produitId);

                if ($produit) {
                    $detailCommande = new DetailCommande();
                    $detailCommande->setNomProduit($produit->getNomProduit());
                    $detailCommande->setQuantite($detailCommandeData->getQuantite());

                    $detailCommande->setProduit($produit);
                    $detailCommande->setCommande($commande);

                    $entityManager->persist($detailCommande);
                } else {
                    $this->addFlash('error', 'Produit non trouvé');
                }
            }

            $entityManager->flush();

            $this->addFlash('success', "Merci {$data->getNomClient()}, Votre réservation a bien été enregistrée !");

            // Redirection vers une autre page après la soumission réussie du formulaire
            return $this->redirectToRoute('home');
        }

        return $this->render('home/home.html.twig', [
            'commande' => $commande,
            'form' => $form->createView()
        ]);
    }

    /*--------------------------------------------------------------
                Page "Confidentialite" 
--------------------------------------------------------------*/

    /**
     * @Route("/politiqueDeConfidentialite", name="politique_confidentialite")
     */
    public function politiqueDeConfidentialite()
    {
        return $this->render('home/confidentialite.html.twig', []);
    }
}
