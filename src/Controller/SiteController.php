<?php
namespace App\Controller;

use App\Entity\Orders;
use App\Entity\Products;
use App\Form\EditProfilType;
use App\Repository\OrdersRepository;
use App\Repository\ProductsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SiteController extends AbstractController
{
    // route vers page accueil

    #[Route('/', name: 'home', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('site/index.html.twig');
    }

    //  route vers la page "produits"
    #[Route('/produits', name: 'produits', methods: ['GET'])]
    public function produits(ProductsRepository $repoProducts): Response
    {
        // affichage les produits
        $products = $repoProducts->findAll();

        return $this->render('site/produits.html.twig', [
            'products' => $products
        ]);
    }

    //  route vers détails d'un produit
    #[Route('/details/{id}', name: 'details', methods: ['GET'])]
    public function detailsProduit(Products $produit): Response
    {
        $produit->getId();

        return $this->render('site/details.html.twig', [
            'produit' => $produit
        ]);
    }

    // route vers profile de utilisateur et affichage des commandes de cet utilisateur
    #[Route('/profile', name: 'profile', methods: ['GET'])]
    public function profil()
    {
        return $this->render('site/profile.html.twig');
    }

    // route vers la modification de profile
    #[Route('/profile/edit', name: 'profil_edit')]
    public function profilEdit(Request $request, EntityManagerInterface $maneger)
    {
        // pn recupere l'utilisateur connecté
        $user = $this->getUser();

        $formEdit = $this->createForm(EditProfilType::class, $user);

        $formEdit->handleRequest($request);

        if($formEdit->isSubmitted() && $formEdit->isValid())
        {
            $maneger->persist($user);
            $maneger->flush();

            // message de mis à jour avec succes
            $this->addFlash('message', "Votre profil a été bien mis à jour !");

            return $this->redirectToRoute('profile');

        }

        return $this->render('site/editprofile.html.twig', [
            'form' => $formEdit->createView()
        ]);

    }


}