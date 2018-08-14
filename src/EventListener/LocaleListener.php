<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class LocaleListener
{
    public function onKernelRequest(GetResponseEvent $event)
    {
        $locale = $event->getRequest()->headers->all();
        if (strlen($locale['accept-language']['0']) > 2)
        {
            $locale['accept-language'][0] = 'en';
        }

        $event->getRequest()->setLocale($locale['accept-language'][0]);
    }
}