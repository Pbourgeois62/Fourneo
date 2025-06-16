<?php

namespace App\Twig\Components\Form;

use App\Entity\SaleEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\LiveCollectionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use App\Form\SaleEventUnsoldQuantityCollectionForm;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[AsLiveComponent]
class saleEventFormCollection extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use LiveCollectionTrait;

    public function __construct() {}

    #[LiveProp(writable: true)]
    public ?SaleEvent $saleEvent = null;

    #[LiveAction]
    public function __invoke(EntityManagerInterface $entityManager)
    {
        $this->submitForm();

        /** @var SaleEvent $saleEvent */
        $saleEvent = $this->getForm()->getData();

        foreach ($saleEvent->getProductEvents() as $productEvent) {
            if ($productEvent->getUnsoldQuantity() === 0) {
                $productEvent->setOutOfStockDateTime(new \DateTimeImmutable());
            } else {
                $productEvent->setOutOfStockDateTime(null);
            }
            $productEvent->setUnsoldPrice($productEvent->getUnsoldQuantity() * $productEvent->getProduct()->getPrice());
            $productEvent->setBenefit($productEvent->getLotPrice() - ($productEvent->getUnsoldQuantity() * $productEvent->getProduct()->getPrice()));
            $entityManager->persist($productEvent);
        }
        $entityManager->persist($saleEvent);
        $entityManager->flush();
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(SaleEventUnsoldQuantityCollectionForm::class, $this->saleEvent);
    }
}
