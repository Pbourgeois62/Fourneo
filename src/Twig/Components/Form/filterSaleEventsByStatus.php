<?php

namespace App\Twig\Components\Form;

use App\Form\SaleEventStatusForm;
use App\Repository\SaleEventRepository;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[AsLiveComponent]
class filterSaleEventsByStatus extends AbstractController
{   
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp(writable: true)]
    public ?string $status = null;

    public array $saleEvents = [];

    private SaleEventRepository $saleEventRepository;

    public function __construct(SaleEventRepository $saleEventRepository)
    {
        $this->saleEventRepository = $saleEventRepository;
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(SaleEventStatusForm::class);
    }

    #[LiveAction]
    public function filterByStatus(): void
    {
        $this->submitForm();
        $this->status = $this->getForm()->get('status')->getData();
        switch ($this->status) {
            case 'passed':
                $this->saleEvents = $this->saleEventRepository->findPassedSaleEvents();
                break;
            case 'today':
                $this->saleEvents = $this->saleEventRepository->findTodaySaleEvents();
                break;
            case 'incoming':
                $this->saleEvents = $this->saleEventRepository->findIncomingSaleEvents();
                break;
            default:
                $this->saleEvents = $this->saleEventRepository->findIncomingSaleEvents();
                break;
        }
    }
}
