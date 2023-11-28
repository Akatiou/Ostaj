<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\DetailCommande;
use App\Form\CommandeType;

use App\Repository\ProduitRepository;
use App\Repository\CommandeRepository;
use App\Repository\DetailCommandeRepository;
use Doctrine\ORM\EntityManager;
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
    public function commande(EntityManagerInterface $entityManager, ProduitRepository $produitRepository, CommandeRepository $commandeRepository, DetailCommandeRepository $detailCommandeRepository): Response
    {
        $commandes = $commandeRepository->findAll();

        // Créer un tableau pour stocker les détails de chaque commande
        $detailsCommandes = [];

        foreach ($commandes as $commande) {
            // Récupérer l'ID de la commande
            $commandeId = $commande->getId();

            // Charger les détails de la commande associés à l'ID de la commande
            $detailCommande = $entityManager->getRepository(DetailCommande::class)->findBy(['commande' => $commandeId]);

            // Stocker les détails de la commande dans le tableau avec l'ID de la commande comme clé
            $detailsCommandes[$commandeId] = $detailCommande;
            // $detailsCommandes[$commande->getId()] = $commande->getDetailCommande();
        }

        return $this->render('admin/commande/commande.html.twig', [
            'controller_name' => 'CommandeController',
            'commandes' => $commandes,
            // 'detailCommande' => $detailCommandeRepository->findAll(),
            'produits' => $produitRepository->findAll(),
            'detailsCommandes' => $detailsCommandes,
        ]);
    }

    //--------------------------------------------------
    //   Voir UNE commande
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


    //--------------------------------------------------
    //   Voir uniquement les commandes en cours
    //---------------------------------------------------

    /**
     * @Route("/admin/commande/encours", name="commande_encours", methods={"GET"})
     */
    public function commandeEncours(EntityManagerInterface $entityManager, CommandeRepository $commandeRepository, DetailCommandeRepository $detailCommandeRepository): Response
    {
        $commandesEnCours = $entityManager->getRepository(Commande::class)->findBy(['statut' => Commande::STATUS_ENCOURS]);

        // Récupérer toutes les commandes depuis votre base de données
        $commandes = $commandeRepository->findAll();

        // Initialiser un compteur pour les commandes en cours
        $countEnCours = 0;

        // Créez un tableau pour stocker les détails de chaque commande
        $detailsCommandes = [];

        // Parcourir chaque commande pour récupérer les détails de la commande
        foreach ($commandes as $commande) {
            // Récupérer l'ID de la commande
            $commandeId = $commande->getId();

            // Charger les détails de la commande associés à l'ID de la commande
            $detailCommande = $entityManager->getRepository(DetailCommande::class)->findBy(['commande' => $commandeId]);

            // Stocker les détails de la commande dans le tableau avec l'ID de la commande comme clé
            $detailsCommandes[$commandeId] = $detailCommande;

            // Vérifier si la commande est en cours et incrémenter le compteur
            if ($commande->getStatut() === Commande::STATUS_ENCOURS) {
                $countEnCours++;
            }
        };

        return $this->render('admin/commande/encours.html.twig', [
            'commande' => $commandesEnCours,
            'countEnCours' => $countEnCours,
            'commandes' => $commandes,
            'detailsCommandes' => $detailsCommandes
        ]);
    }


    //--------------------------------------------------
    //   Faire  passer une command d'en cours à terminer.
    //---------------------------------------------------

    /**
     * @Route("/admin/commande/terminer", name="commande_termine", methods={"POST"})
     */
    public function markAsCompleted(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupérer les identifiants des commandes à marquer comme terminées (via le formulaire ou d'autres moyens)
        $commandeIds = $request->request->get('commande_id');

        if (!$commandeIds) {
            // Gérer le cas où aucun identifiant de commande n'a été fourni
            // Vous pouvez ajouter une gestion des erreurs ou rediriger vers une page appropriée ici
            $this->addFlash('error', 'Il y a eu une erreur, le statut n\'a pas pu être marqué comme terminé.');
            return $this->redirectToRoute('commande_encours');
        }

        // Récupérer les commandes correspondant aux identifiants fournis
        $commande = $entityManager->getRepository(Commande::class)->findBy(['id' => $commandeIds]);

        foreach ($commande as $commande) {
            // Mettre à jour le statut de la commande à "Terminé"
            $commande->setStatut(Commande::STATUS_TERMINE);
            $entityManager->persist($commande);
        }

        $entityManager->flush();

        // Ajouter un message flash pour indiquer que les commandes ont été mises à jour avec succès
        $this->addFlash('success', 'Les commandes ont été marquées comme terminées.');

        // Redirection vers une autre page après la mise à jour réussie des commandes
        return $this->redirectToRoute('commande_encours');
    }
}
