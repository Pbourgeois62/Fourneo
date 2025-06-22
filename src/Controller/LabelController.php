<?php

namespace App\Controller;

use App\Entity\Label;
use App\Entity\Product;
use App\Form\LabelTypeForm;
use App\Form\AddLabelToProductForm;
use App\Repository\LabelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_USER')]
#[Route('/label')]
final class LabelController extends AbstractController
{
    #[Route('', name: 'label_index', methods: ['GET'])]
    public function indexLabel(LabelRepository $labelRepository): Response
    {
        return $this->render('label/index.html.twig', [
            'labels' => $labelRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'label_new', methods: ['GET', 'POST'])]
    #[Route('/new/{product}', name: 'label_new_for_product', methods: ['GET', 'POST'])]
    public function createLabel(
        Request $request,
        EntityManagerInterface $em,
        ?Product $product
    ): Response {
        $label = new Label();
        if ($product) {
            $label->setProduct($product);
        }

        $form = $this->createForm(LabelTypeForm::class, $label);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($label);
            $em->flush();

            $this->addFlash('success', 'Etiquette créé avec succès.');

            if ($product) {               
                return $this->redirectToRoute('label_show', ['id' => $product->getId()]);
            }

            return $this->redirectToRoute('label_index');
        }

        return $this->render('label/edit.html.twig', [
            'form' => $form,
            'product' => $product,
        ]);
    }

    #[Route('/{id}', name: 'label_show', methods: ['GET'])]
    public function showLabel(Label $label): Response
    {
        return $this->render('label/show.html.twig', [
            'label' => $label,
        ]);
    }

    #[Route('/{id}/edit', name: 'label_edit', methods: ['GET', 'POST'])]
    public function editLabel(Label $label, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(LabelTypeForm::class, $label);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Étiquette modifiée avec succès.');
            return $this->redirectToRoute('label_index');
        }

        return $this->render('label/edit.html.twig', [
            'form' => $form,
            'label' => $label,
        ]);
    }

    #[Route('/{id}/delete', name: 'label_delete', methods: ['POST'])]
    public function deleteLabel(Label $label, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_label_' . $label->getId(), $request->request->get('_token'))) {
            $em->remove($label);
            $em->flush();
            $this->addFlash('success', 'Étiquette supprimée avec succès.');
        }

        return $this->redirectToRoute('label_index');
    }
}
