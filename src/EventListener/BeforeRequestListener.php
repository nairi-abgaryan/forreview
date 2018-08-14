<?php

namespace App\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class BeforeRequestListener
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * BeforeRequestListener constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $filter = $this->em
            ->getFilters()
            ->enable('locale_filter');
        $filter->setParameter('lang', $event->getRequest()->getLocale());
    }
}