<?php

namespace App\Controller;

use App\Entity\Processus;
use App\Form\ProcessusType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/processus')]
class ProcessusController extends AbstractController
{
    #[Route('/new', name: 'app_processus_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em, ProductRepository $productRepo): Response
    {
        $processus = new Processus();
        $productId = $request->query->get('product_id');
        if ($productId) {
            $product = $productRepo->find($productId);
            if ($product) { $processus->setProduct($product); }
        }

        $form = $this->createForm(ProcessusType::class, $processus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($processus);
            $em->flush();
            return $this->redirectToRoute('app_product_show', ['id' => $processus->getProduct()->getId()]);
        }

        return $this->render('processus/new.html.twig', [
            'form' => $form->createView(),
            'isEdit' => false
        ]);
    }

    #[Route('/{id}/edit', name: 'app_processus_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Processus $processus, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ProcessusType::class, $processus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('app_product_show', ['id' => $processus->getProduct()->getId()]);
        }

        return $this->render('processus/new.html.twig', [
            'form' => $form->createView(),
            'isEdit' => true
        ]);
    }

    #[Route('/{id}', name: 'app_processus_show', methods: ['GET'])]
    public function show(Processus $processus): Response
    {
        return $this->render('processus/show.html.twig', [
            'processus' => $processus,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_processus_delete', methods: ['POST'])]
    public function delete(Request $request, Processus $processus, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$processus->getId(), $request->request->get('_token'))) {
            $em->remove($processus);
            $em->flush();
        }
        return $this->redirectToRoute('app_dashboard');
    }
}