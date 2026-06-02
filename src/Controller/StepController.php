<?php

namespace App\Controller;

use App\Entity\Step;
use App\Form\StepType;
use App\Repository\ProcessusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/step')]
class StepController extends AbstractController
{
    #[Route('/new', name: 'app_step_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ProcessusRepository $processusRepository): Response
    {
        $step = new Step();

        // On intercepte la session envoyée par notre bouton Twig
        $processusId = $request->query->get('processus_id');
        if ($processusId) {
            $processus = $processusRepository->find($processusId);
            if ($processus) {
                // On lie automatiquement l'étape au processus en arrière-plan
                $step->setProcessus($processus);
            }
        }

        $form = $this->createForm(StepType::class, $step);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($step);
            $entityManager->flush();

            $processusId = $step->getProcessus()->getId();

            // On redirige proprement vers la fiche du produit parent !
            return $this->redirectToRoute('app_processus_show', ['id' => $processusId]);
        }

        return $this->render('step/new.html.twig', [
            'step' => $step,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_step_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Step $step, EntityManagerInterface $em): Response
    {
        $processusId = $step->getProcessus()->getId();
        $form = $this->createForm(StepType::class, $step);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('app_processus_show', ['id' => $processusId]);
        }

        return $this->render('step/new.html.twig', [
            'form' => $form->createView(),
            'isEdit' => true
        ]);
    }

    #[Route('/{id}/delete', name: 'app_step_delete', methods: ['POST'])]
    public function delete(Request $request, Step $step, EntityManagerInterface $em): Response
    {
        $processusId = $step->getProcessus()->getId();
        if ($this->isCsrfTokenValid('delete'.$step->getId(), $request->request->get('_token'))) {
            $em->remove($step);
            $em->flush();
        }
        return $this->redirectToRoute('app_processus_show', ['id' => $processusId]);
    }
}