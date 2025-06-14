<?php

namespace App\Controller;

use App\Repository\SaleEventRepository;
use App\Repository\SaleSaleEventRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[IsGranted('ROLE_USER')]
final class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(SaleEventRepository $SaleEventRepository): Response
    {
         $today = (new \DateTimeImmutable());
        return $this->render('home/index.html.twig', [
            'todayEvents' => $SaleEventRepository->findEventsForSpecificDate($today),
        ]);
    }
}
