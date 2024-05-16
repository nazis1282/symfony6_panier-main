<?php

namespace App\Controller;

use App\Entity\Orders;
use App\Entity\OrdersDetails;
use App\Entity\Products;
use App\Repository\OrdersDetailsRepository;
use App\Repository\OrdersRepository;
use App\Repository\ProductsRepository;
use App\Service\Cart\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'cart')]    // j'ai renomé la route pour "cart"
    public function index(CartService $cartService): Response
    {

        $panierDonnees = $cartService->getFullCart();

        $total = $cartService->getTotal();
        

        return $this->render('cart/cart.html.twig', [
            'items' => $panierDonnees,   //on envoie les données au twig
            'total'  => $total
        ]);
    }
     
    // ajouter le produit dans le panier
    #[Route('cart/add/{id}', name: 'cart_add')]
    public function add($id, CartService $cartService)
    {

        $cartService->add($id);
        
        // pour cette function on fait redirecToRoute et NON render !!
        return $this->redirectToRoute('cart', []);
    }

    // reduir le quantité de produit selectionné
    #[Route('cart/enlever/{id}', name: 'cart_enlever')]
    public function enlever(Products $products, CartService $cartService)
    {

        $cartService->enlever($products);

        return $this->redirectToRoute('cart');

    }


    //supression d'un produit de panier

    #[Route('cart/remove/{id}', name: 'cart_remove')]
    public function remove($id, CartService $cartService)
    {
        $cartService->remove($id);


        return $this->redirectToRoute('cart');

    }

    // supression de panier total
    #[Route('cart/delete', name: 'delete')]
    public function deleteAll(SessionInterface $session)
    {
        $session->remove('panier');

        return $this->redirectToRoute('cart');

    }


    // validation de commande : je recuper les classes cartservices, Orders, OrdersDetails ,
    // EntityManagerInterface => tout ceux que nous avons besoins pour enregistrer la commande=order
    #[Route('cart/commande', name: 'commande')]
    public function validationCommande( ProductsRepository $repoProduit, SessionInterface $session, CartService $cartService, EntityManagerInterface $maneger, Orders $order = null, OrdersDetails $orderDetails = null, ): Response
    { 
        $order = new Orders;   
        // pour recupere utilisateur, on utilise la methode de symfony getUser() d'AbstractController.php  
        $order->setUsers($this->getUser());
        $order->setMontant($cartService->getTotal());
        // dd($order); 
        $maneger->persist($order);
        $maneger->flush(); // on enregister la commande

        // dd($order);
        // ___________________________________________________

        // creation de OrdersDetails
        // ___________________________________________________
        //on recupere le panier via $session;
        $panier = $session->get('panier');

        // on declare le tableau de donées de panier
        $panierDonnees = [];

        foreach($panier as $key => $value)
        {
            $panierDonnees[] = [
                'products' => $repoProduit->find($key),
                'quantity' => $value
            ];
        }

        //   dd($panierDonnees);

        foreach($panierDonnees as $key => $value)
        {
            // on declare variable $orderDetails pour y enregestrer les données
            $orderDetails = new OrdersDetails;

            //on recuper $id de produit de données de panier
            $id = $value['products']->getId();
            
            // on recupere l'objet "products"
            $products = $repoProduit->find($id);

            // on recuper stock de produit pour faire mis à jour 
            $stock = $products->getStock();

            // on rensegne setteur de $orderDetails 
            $orderDetails->setOrders($order);
            $orderDetails->setQuantity($value['quantity']);
            $orderDetails->setPrice($value['products']->getPrice());
            $orderDetails->setProducts($products);


            // on prepare données de orderDetails pour enregestrer
            $maneger->persist($orderDetails);
            
            // on reduit le stock de produit
            $products->setStock($stock - $value['quantity']);
            
            // on prepare enregistrer le changement de stock
            $maneger->persist($products);

        }
        // on enregister données
        $maneger->flush();

        // dd($orderDetails);
        // mis à jour stock dans BDD


        // on supprime le panier apres enregistrement de commande
        $session->remove('panier');

        return $this->render('cart/commande.html.twig', [
            'order' => $order,
            'details' => $orderDetails
        ]);
    }

    // affichage de détails de commande
    #[Route('/cart_details/{id}', name: 'cart_details', methods: ['GET'])]
    public function detailsCommande($id, OrdersDetailsRepository $detailsRepo, OrdersRepository $orderRepo)
    {
        $order = $orderRepo->find($id);
        $orders_id = $id;
        $details = $detailsRepo->findByOrders($orders_id);

        // dd($details);

        return $this->render('cart/cart_details.html.twig', [
            'details' => $details,
            'order' => $order
        ]);
    }







}
