<?php

namespace App\Service\Cart;

use App\Entity\Products;
use App\Repository\ProductsRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    // on fait declarer requestStack pour recuperer la session (dans Symfony 6, 
    // on ne peut plus demander une SessionInterface dans un constructeur
    //  : à la place il faut demander un RequestStack,qui a sa place demendera "Session")

    private $requestStack;
    private $productRepo;

    public function __construct(RequestStack $requestStack,  ProductsRepository $productRepo)
    {
        $this->requestStack = $requestStack;   
        $this->productRepo = $productRepo; 
    }


    // function pour ajouter le produit dans le panier
    public function add(int $id)
    {
        // on recupere la session via RequesrStack(c'est nouveu , à partire la symfony6)
        $session = $this->requestStack->getSession();

        // on recupere le panier
        $panier = $session->get('panier', []);

        // pour REAjouter le produit qui est déjà dans le panier on utilise le condition if()
        if(!empty($panier[$id]))
        {
            $panier[$id]++;

        }else{
            // on ajoute le produit dans le panier (la promiere fois!)
            $panier[$id] = 1;
        }
        
        $session->set('panier', $panier);

    }

    // function pour enlever le produit dans le panier
    public function remove(int $id)
    {
        $session = $this->requestStack->getSession();        
        $panier = $session->get('panier', []);

        if(!empty($panier[$id]))
        {
            unset($panier[$id]);
        }

        $session->set('panier', $panier);

    }

    // function pour reduire le quantité de produit
    public function enlever(Products $products)
    {
        $session = $this->requestStack->getSession();
        
        // on recuper le panier
        $panier = $session->get('panier', []);
        $id = $products->getId();

        if(!empty($panier[$id])){
            if($panier[$id] > 1){
                $panier[$id]--;
            }else{
                unset($panier[$id]);
            }
        }

        // On sauvegarde dans la session
        $session->set("panier", $panier);

    }


    //  on recupere tout le panier
    public function getFullCart(): array
    {
        $session = $this->requestStack->getSession();
        
        // on recupere le panier
        $panier = $session->get('panier', []);

        // nous avons besoin de données à mettre dans le panier,
        // le panier est déclaré à vide
        $panierDonnees = [];
        
        // $id -> id de produit à ajouter, $quantity -> quantité
        foreach($panier as $id => $quantity)
        {
            $panierDonnees[] = [
                'product' => $this->productRepo->find($id), //on recupere les données de produit
                'quantity' => $quantity
            ];
        }

        return $panierDonnees;
    }

    // calcule de total de panier
    public function getTotal(): float
    {
        $total = 0; // on déclar $total à vide

        $panierDonnees = $this->getFullCart();

        foreach($panierDonnees as $item)
        {
            $totalItem = $item['product']->getPrice() * $item['quantity'];
            $total += $totalItem;
        }

        return $total;

    }




}