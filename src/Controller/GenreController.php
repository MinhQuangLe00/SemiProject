<?php

namespace App\Controller;

use App\Entity\Genre;
use App\Form\GenreType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;


class GenreController extends AbstractController
{

     #[Route("/genre/create", name: "app_create_genre")]

    public function add(ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger)
    {
        $genre = new Genre();
        $form = $this->createForm(GenreType::class, $genre);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entitymanager = $doctrine->getManager();
            $entitymanager->persist($genre);
            $entitymanager->flush();

            $this->addFlash(
                'notice',
                'New Genre Added'
            );
            return $this->redirectToRoute('app_create_genre');
        }
        return $this->render('genre/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
