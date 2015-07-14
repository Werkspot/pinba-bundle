<?php

namespace Intaro\PinbaBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ScriptNameConfigureListener
{
    public function onRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        if (!function_exists('pinba_script_name_set') || PHP_SAPI === 'cli') {
            return;
        }

        $controller = $event->getRequest()->attributes->get('_controller');

        // When we render an assetic asset we have a url like: /assets/js/all.min_part_1_modal.core_4.js but the action
        // is always 'assetic.controller:render', so if we use that, we'll never know what was really rendered, so
        // in that case just use the uri so we have more insight what was rendered
        $scriptName = $controller !== 'assetic.controller:render'
            ? $controller
            : $event->getRequest()->getRequestUri();

        pinba_script_name_set($scriptName);
    }
}
