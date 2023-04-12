<?php

namespace App\Controller;
use App\Entity\Genre;
use App\Entity\Product;
use App\Entity\Todo;
use App\Form\TodoType;
use App\Form\WaterType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductController extends AbstractController
{

     #[Route(' /product', name: 'product_list')]

    public function ListAction(ManagerRegistry $doctrine): Response
    {
        $products = $doctrine->getRepository(Product::class)->findAll();
        $genres = $doctrine->getRepository(Genre::class)->findAll();
        return $this->render('product/index.html.twig',
            ['product' => $products,
            'genres' => $genres
        ]);
    }

    /**
     * @Route("/add/productByGenre/{id}", name="productByGenre")
     */
    public function GenreAction(ManagerRegistry $doctrine, $id): Response
    {
        $genre = $doctrine->getRepository(Genre::class)->find($id);
        $products = $genre->getProducts();
        $genres = $doctrine->getRepository(Genre::class)->findAll();
        return $this->render('product/index.html.twig', [
            'product' => $products,
            'genres' => $genres
        ]);

    }


    /**
     * @Route("admin/product/create", name="product_create")
     */
    public function create(ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger): \Symfony\Component\HttpFoundation\RedirectResponse|Response
    {
        $products = new Product();
        $form = $this->createForm(ProductType::class, $products);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form->get('Image')->getData();
            if ($uploadedFile) {
                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();

                // Move the file to the directory where image are stored
                try {
                    $uploadedFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash(
                        'error',
                        'Cannot Upload'
                    );
                    // ... handle exception if something happens during file upload
                }
                $products->setImage($newFilename);

                $entitymanager = $doctrine->getManager();
                $entitymanager->persist($products);
                $entitymanager->flush();

                $this->addFlash(
                    'notice',
                    'New Product Added'
                );
                return $this->redirectToRoute('product_create');
            }
        }
        return $this->render('product/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("admin/product/edit/{id}", name="app_edit_product")
     */
    public function edit(ManagerRegistry $doctrine, int $id, Request $request): Response
    {
        $entitymanager = $doctrine->getManager();
        $products = $entitymanager->getRepository(Product::class)->find($id);
        $form = $this->createForm(ProductType::class, @$products);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $entitymanager = $doctrine->getManager();
            $entitymanager->persist($products);
            $entitymanager->flush();

            return $this->redirectToRoute('product_list', [
                'id' => $products->getId()
            ]);
        }
        return $this->renderForm('product/edit.html.twig', ['form' => $form,]);
    }
    public function saveChanges(ManagerRegistry $doctrine, $form, $request, $products)
    {
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $products->setName($request->request->get('product')['name']);
            $products->setGenre($request->request->get('product')['genre']);
            $products->setDescription($request->request->get('product')['description']);
            $products->setPrice($request->request->get('product')['price']);
            $entitymanager = $doctrine->getManager();
            $entitymanager->persist($products);
            $entitymanager->flush();

            return true;
        }

        return false;
    }


    /**
     * @Route("admin/product/delete/{id}", name="app_product_water")
     */
    public function delete(ManagerRegistry $doctrine, $id): Response
    {
        $entitymanager = $doctrine->getManager();
        $products = $entitymanager->getRepository(Water::class)->find($id);
        $entitymanager->remove($products);
        $entitymanager->flush();

        $this->addFlash(
            'notice',
            'Product Deleted'
        );

        return $this->redirectToRoute('product_list');
    }

}
