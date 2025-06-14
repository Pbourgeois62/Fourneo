<?php

namespace App\Twig;

use Twig\TwigFunction;
use App\Entity\SaleEvent;
use Twig\Extension\AbstractExtension;
use Symfony\Component\Workflow\Registry;

class WorkflowExtension extends AbstractExtension
{
    public function __construct(private Registry $workflows) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('workflow_can_saleEvent', [$this, 'getEnabledTransitionsSaleEvent']),
        ];
    }


    /**
     * @return string[]
     */

    public function getEnabledTransitionsProfile(SaleEvent $saleEvent): array
    {
        $workflow = $this->workflows->get($saleEvent, 'saleEvent');
        return array_map(
            fn($transition) => $transition->getName(),
            $workflow->getEnabledTransitions($saleEvent)
        );
    }
}
