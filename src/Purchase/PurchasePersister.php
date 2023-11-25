<?php

namespace App\Purchase;

use App\Cart\CartService;
use DateTime;
use App\Entity\Commande;
use App\Entity\DetailCommande;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class PurchasePersister
{
    protected $security;
    protected $cartService;
    protected $em;


    public function __construct(Security $security, CartService $cartService, EntityManagerInterface $em)
    {
        $this->security = $security;
        $this->cartService = $cartService;
        $this->em = $em;
    }

    public function storePurchase(Commande $commande)
    {
        // Intégrer tout ce qu'il faut et persister la commande
        // 6. Nous allons la lier avec l'utilisateur actuellement connecté (Security)
        $commande->setDateCommande(new DateTime());

        $this->em->persist($commande);

        // 7. Nous allons la lier avec les produits qui sont dans le panier (CartService)
        foreach ($this->cartService->getDetailedCartItems() as $cartItem) {
            $detailCommande = new DetailCommande;
            $detailCommande->setCommande($commande)
                ->setProduit($cartItem->produit)
                ->setNomProduit($cartItem->produit->getNomProduit())
                ->setQuantite($cartItem->quantite);
            // ->setProductPrice($cartItem->product->getPrice())
            // ->setTotal($cartItem->getTotal());

            $this->em->persist($detailCommande);
        }

        // 8. Nous allons enregistrer la commande (EntityManagerInterface)
        $this->em->flush();
    }
}
