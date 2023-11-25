<?php

namespace App\Controller\Purchase;

use App\Entity\Commande;
use App\Cart\CartService;
use App\Form\CartConfirmationType;
use App\Purchase\PurchasePersister;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request as BrowserKitRequest;

class PurchasesConfirmationController extends AbstractController
{
    protected $cartService;
    protected $em;
    protected $persister;

    public function __construct(CartService $cartService, EntityManagerInterface $em, PurchasePersister $persister)
    {
        $this->cartService = $cartService;
        $this->em = $em;
        $this->persister = $persister;
    }

    /**
     * @Route("/commande/confirm", name="confirm")
     */
    public function confirm(Request $request)
    {
        // 1. Nous voulons lire les données du formulaire
        // FormFactoryInterface / Request
        $form = $this->createForm(CartConfirmationType::class);

        $form->handleRequest($request);

        // 2. Si le formulaire n'a pas été soumis : dégager
        if (!$form->isSubmitted()) {
            // Message Flash puis redirection (FlashBagInterface)
            $this->addFlash('warning', "Vous devez remplir le formulaire de confirmation");

            return $this->redirectToRoute('cart_show');
        }

        // 4. S'il n'y a pas de produits dans mon panier : dégager (CartService)
        $cartItems = $this->cartService->getDetailedCartItems();

        if (count($cartItems) === 0) {
            $this->addFlash('warning', "Vous ne pouvez pas confirmer une réservation sans avoir sélectionné le nombre de pain dont vous avez besoin !");

            return $this->redirectToRoute('commande_new');
        }

        // 5. Nous allons créer une Commande
        /** @var Commande */
        $commande = $form->getData();

        // Les 6., 7. et 8. sont dans Puchase/PurchasePersister.php et on l'appelle avec le persister

        $this->persister->storePurchase($commande);

        // Vider le panier après confirmation de la commande (On appelle la fonction du CartService.php)
        // $this->cartService->empty();

        $this->addFlash('success', "La commande a bien été enregistré !");

        return $this->redirectToRoute('/', [
            'id' => $commande->getId()
        ]);
    }
}
