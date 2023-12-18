<?php

namespace App\Service\SpreadSheet;

use App\Entity\Events;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExportSubscribersService extends SpreadSheetService
{
    public const COLUMN_NAMES=[
        'Nom',
        'Prénom',
        'Émail'
    ];
    public function exportSubscribers(Events $event = null, Request $request)
    {
        if (!$event) {
            throw new NotFoundHttpException('Evenement  Introuvable');
        }
        $subscribers= [];
        foreach ($event->getSubscriptions() as $key=>$subscriber )
        {
            $subscribers[$key]['nom'] = $subscriber->getUser()->getLastname();
            $subscribers[$key]['prénom'] = $subscriber->getUser()->getFirstname();
            $subscribers[$key]['email']= $subscriber->getUser()->getEmail();
        }

        return $this->manage($subscribers, self::COLUMN_NAMES);

    }

}