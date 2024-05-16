<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Symfony\Component\Mime\Email;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Contact $contact = null, Request $request, EntityManagerInterface $manager, MailerInterface $mailer): Response
    {
        $contact = new Contact();

        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            // dd($contact);
            $manager->persist($contact);
            $manager->flush();
        
            // dd($contact);
            // envoie d'email après enregistrement
            $email = (new Email())
            ->from($contact->getEmail())
            ->to('contact@panier-de-symfony.fr')
            ->cc('alla.dumenil@mail.ru')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            ->priority(Email::PRIORITY_HIGH)
            ->subject($contact->getObject())
            ->text($contact->getMessage())
            ->html($contact->getMessage());

            // dd($email);

            $mailer->send($email);

            $this->addFlash('success', "Votre message a été bien envoyé !");

            return $this->redirectToRoute('app_contact');
        }


        return $this->render('contact/index.html.twig', [
            'form' => $form->createView() 
        ]);
    }
}
