<?php

namespace App\Controller;

use App\Entity\Membre;
use App\Form\MembreType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="app_home")
     */
    public function index(ManagerRegistry $doctrine,Membre $membre = null ,Request $request ): Response
    {
        if(!$membre){
            $membre =new Membre();
        }

        $form = $this->createForm(MembreType::class,$membre);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $membre = $form->getData();
            $nom = ucfirst(strtolower($membre->getNom()));
            $membre->setNom($nom);
            $entityManager = $doctrine->getManager();
            $entityManager->persist($membre);
            $entityManager->flush();

            $this->addFlash("success" , $membre->getNom()." membre ajouté avec succès");

            return $this->redirectToRoute('app_home'); 
        }

        $membres = $doctrine->getRepository(Membre::class)->findBy([] , ["nom" => "ASC"]);

        return $this->render('home/index.html.twig', [
            'form' => $form->createView(),
            "membres" => $membres,
        ]);
    }
}
