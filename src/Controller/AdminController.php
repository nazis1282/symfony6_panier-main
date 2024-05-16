<?php

namespace App\Controller;

use App\Entity\Products;
use App\Entity\Categories;
use App\Entity\OrdersDetails;
use App\Form\ProductsType;
use App\Form\CategoriesType;
use App\Form\EditProductsType;
use App\Repository\CategoriesRepository;
use App\Repository\ContactRepository;
use App\Repository\OrdersDetailsRepository;
use App\Repository\OrdersRepository;
use App\Repository\ProductsRepository;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class AdminController extends AbstractController
{
    // route vers page d'accueil de backoffice
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/admin_home.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    // route vers page "produits"
    #[Route('/admin_produits', name: 'admin_produits')]
    public function adminProduits(ProductsRepository $productRepo): Response
    {
        $produits = $productRepo->findAll();
        return $this->render('admin/admin_produits.html.twig', [
            'produits' => $produits
        ]);
    }

    // creation de produits

    #[Route('/produit_edit', name: 'produit-edit')]
    public function creationProduit(Products $produits = null, Request $request, EntityManagerInterface $maneger, SluggerInterface $slugger )
    {

        // si il y a le produit dans BDD => recupere l'image pour pouvoir la changer
        if($produits)
        { 
            $imageActuelle = $produits->getImage();
        }

        // si il n'y a pas de produit => creation de nouveau produit
        if(!$produits)
        {
            $produits = new Products;
        }

        
        $prodForm = $this->createForm(ProductsType::class, $produits);

        $prodForm->handleRequest($request);

        if($prodForm->isSubmitted() && $prodForm->isValid())  //si c'est ok... on traite l'image
        {
            //traitement d'image
            $image = $prodForm->get('image')->getData();

            if($image)
            {
            // On récupère le nom d'origine du fichier 
            $nomOrigineFichier = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            // cela est nécessaire pour inclure en toute sécurité le nom du fichier dans l'URL
            $secureNomFichier = $slugger->slug($nomOrigineFichier);
            $nouveauNomFichier = $secureNomFichier . '-' . uniqid() . '.' . $image->guessExtension();
                try
                {
                    // On copie l'image dans le bon dossier (images_directory service.yaml) 
                    $image->move(
                    $this->getParameter('images_directory'),
                    $nouveauNomFichier);
                }
                catch(FileException $e){}
            // On enregistre l'image en BDD
            $produits->setImage($nouveauNomFichier);
            
            }
            else
            {
                $produits->setImage($imageActuelle);
            }
            $maneger->persist($produits);
            $maneger->flush();

             // on se rederige vers affichage des categories
            return $this->redirectToRoute('admin_produits', [
                'id' => $produits->getId()
            ]);

        }
        
        return $this->render('admin/produit_edit.html.twig', [
            'prodForm' => $prodForm->createView(),
            'editMode' => $produits->getId()
        ]);

    }

    // route vers la modification de produit
    #[Route('/admin_modif/{id}', name: 'admin_modif')]
    public function modificationProduit(Products $produit, $id, Request $request, EntityManagerInterface $maneger, ProductsRepository $productRepo, SluggerInterface $slugger)
    {
        $produit = $productRepo->find($id);
 
        // si il y a le produit dans BDD => recupere l'image pour pouvoir la changer
        $imageActuelle = $produit->getImage();
        // dd($imageActuelle);

        $form = $this->createForm(EditProductsType::class, $produit);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            //traitement d'image
            $image = $form->get('image')->getData();

            // dd($image);
            
            if(!$image)
            {
            // On récupère le nom d'origine du fichier 
            $nomOrigineFichier = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            // cela est nécessaire pour inclure en toute sécurité le nom du fichier dans l'URL
            $secureNomFichier = $slugger->slug($nomOrigineFichier);
            $nouveauNomFichier = $secureNomFichier . '-' . uniqid() . '.' . $image->guessExtension();
                try
                {
                    // On copie l'image dans le bon dossier (images_directory service.yaml) 
                    $image->move(
                    $this->getParameter('images_directory'),
                    $nouveauNomFichier);
                }
                catch(FileException $e){}


            // On enregistre l'image en BDD
            $produit->setImage($nouveauNomFichier);
            
            }
            else
            {
                $produit->setImage($imageActuelle);
            }

            $maneger->persist($produit);
            $maneger->flush();

            $this->addFlash('message', "Le produit a été bien mis à jour !");

            return $this->redirectToRoute('admin_produits', [
                'id' => $produit->getId()
            ]);
        }

        return $this->render('admin/admin_modif.html.twig', [
            'form' => $form->createView(),
            'imageActuelle' => $produit->getImage()
        ]);

    }

    // route vers affichage des categories 
    #[Route('/admin_categories', name: 'admin_categories')]
    public function adminCategories(CategoriesRepository $repoCat): Response
    {
        $categories = $repoCat->findAll();

        return $this->render('admin/admin_categories.html.twig', [
            'categories' => $categories
        ]);
    }

    //  creation de nouvelle  categorie
    #[Route('/categorie_edit', name: 'categorie_edit')]
    public function creationCategorie(Categories $categories = null, Request $request, EntityManagerInterface $maneger)
    {
        //  si categorie est null , on declare new Categories
        if(!$categories)
        {
            $categories = new Categories;
        }

        $catForm = $this->createForm(CategoriesType::class, $categories);

        $catForm->handleRequest($request); //recupere et tranmet données
        
        // handleRequest() renseigne chaque setteur de l'entité $categories avec les données saisi dans le formulair

        if($catForm->isSubmitted() && $catForm->isValid())
        {
            $maneger->persist($categories);

            $maneger->flush();

            // on se rederige vers affichage des categories
            return $this->redirectToRoute('admin_categories', [
                'id' => $categories->getId()
            ]);
        }

        return $this->render('admin/categorie_edit.html.twig', [
            'catForm' => $catForm->createView(),
            'editMode' => $categories->getId()
        ]);

    }

    // affichage les messages reçues
    #[Route('admin_contact', name: 'admin_contact')]
    public function contact(ContactRepository $repoContact)
    {
        $contacts = $repoContact->findAll();
        return $this->render('admin/admin_contact.html.twig', [
            'contacts' => $contacts
        ]);
    }

    // route vers affichage des images 
    #[Route('/admin_images', name: 'admin_images')]
    public function adminImages(): Response
    {
        return $this->render('admin/admin_images.html.twig'
        );
    }

    // route vers affichege des utilisateurs 
    #[Route('/admin_users', name: 'admin_users')]
    public function adminUsers(UsersRepository $userRepo): Response
    {
        $users = $userRepo->findAll();

        return $this->render('admin/admin_users.html.twig', [
            'users' => $users

        ]);
    }

    
    // route vers affichage des commandes 
    #[Route('/admin_commandes', name: 'admin_commandes')]
    public function adminCommandes(OrdersRepository $ordersRepo): Response
    {
        $orders = $ordersRepo->findAll();

        return $this->render('admin/admin_commandes.html.twig', [ 
            'orders' => $orders

        ]);
    }

    // affichage de détails de commande
    #[Route('/admin_details/{id}', name: 'admin_details')]
    public function adminDetails($id, OrdersDetailsRepository $detailsRepo, OrdersRepository $orderRepo)
    {
      
        $order = $orderRepo->find($id);
        $orders_id = $id;
        $details = $detailsRepo->findByOrders($orders_id);

        // dd($details);

        return $this->render('admin/admin_details.html.twig', [
            'details' => $details,
            'order' => $order

        ]);


    }





}
