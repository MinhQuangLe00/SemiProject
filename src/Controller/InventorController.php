<?php

namespace App\Controller;

use App\Entity\Inventor;
use App\Form\InventorType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class InventorController extends AbstractController
{
    #[Route('/inventor', name: 'app_inventor')]
    public function index(): Response
    {
        return $this->render('inventor/index.html.twig', [
            'controller_name' => 'InventorController',
        ]);
    }

    /**
     * @Route("admin/inventor/create", name="app_create_inventor")
     */
    public function create(ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger)
    {
        $inventor = new Inventor();
        $form = $this->createForm(InventorType::class, $inventor);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entitymanager = $doctrine->getManager();
            $entitymanager->persist($form->getData());
            $entitymanager->flush();

            $this->addFlash(
                'notice',
                'New Inventor Added'
            );
            return $this->redirectToRoute('app_create_artist');
        }
        return $this->render('inventor/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
