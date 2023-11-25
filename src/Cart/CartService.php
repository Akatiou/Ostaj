<?php

namespace App\Cart;

use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    protected $session;
    protected $produitRepository;

    public function __construct(SessionInterface $session, ProduitRepository $produitRepository)
    {
        $this->session = $session;
        $this->produitRepository = $produitRepository;
    }

    protected function getCart(): array
    {
        return $this->session->get('cart', []);
    }

    protected function saveCart(array $cart)
    {
        $this->session->set('cart', $cart);
    }

    //------------------------------------------------------
    //   Réinitialiser (vider) le panier après une commande
    //-------------------------------------------------------

    public function empty()
    {
        $this->saveCart([]);
    }

    //------------------------------------------------------
    //   Ajout dans le panier
    //-------------------------------------------------------

    public function add(int $id)
    {
        // 1. Retrouver le panier dans la session (sous forme de tableau)
        // 2. Si il n'existe pas encore, alors prendre un tableau vide

        $cart = $this->getCart();

        // 3. Voir si le produit ($id) existe déjà dans le tableau
        // 4. Si c'est le cas, simplement augmenter la quantité
        // 5. Sinon, ajouter le produit avec la quantité 1

        if (!array_key_exists($id, $cart)) {
            $cart[$id] = 0;
        }

        $cart[$id]++;


        // 6. Enregistrer le tableau mis à jour dans la session

        $this->saveCart($cart);
    }

    //------------------------------------------------------
    //   Supprimer l'article du panier (complètement)
    //-------------------------------------------------------
    public function remove(int $id)
    {
        $cart = $this->getCart();

        unset($cart[$id]);

        $this->saveCart($cart);
    }

    //------------------------------------------------------
    //   (Décrémenter) un article en moins dans le panier
    //-------------------------------------------------------
    public function decrement(int $id)
    {
        $cart = $this->getCart();

        if (!array_key_exists($id, $cart)) {
            return;
        }

        // Soit le produit est à 1, alors il faut le supprimer
        if ($cart[$id] === 1) {
            $this->remove($id);
            return;
        }

        // Soit il est à plus de 1, alors il faut décrémenter
        $cart[$id]--;

        $this->saveCart($cart);
    }

    //------------------------------------------------------
    //   Calcul du total du panier
    //-------------------------------------------------------
    // public function getTotal(): int
    // {
    //     $total = 0;

    //     foreach ($this->getCart() as $id => $qty) {
    //         $product = $this->produitRepository->find($id);

    //         if (!$product) {
    //             continue;
    //         }

    //         $total += $product->getPrice() * $qty;
    //     }

    //     return $total;
    // }

    //------------------------------------------------------
    //   Détails des articles du panier
    //-------------------------------------------------------

    /**
     * 
     * @return CartItem[]
     */
    public function getDetailedCartItems(): array
    {
        $detailedCart = [];

        foreach ($this->getCart() as $id => $quantite) {
            $produit = $this->produitRepository->find($id);

            if (!$produit) {
                continue;
            }

            $detailedCart[] = new CartItem($produit, $quantite);
        }

        return $detailedCart;
    }
}
