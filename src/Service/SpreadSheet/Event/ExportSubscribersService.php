<?php

namespace App\Service\SpreadSheet\Event;

use App\Entity\Events;
use App\Service\SpreadSheet\SpreadSheetService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use function sprintf;

class ExportSubscribersService extends SpreadSheetService
{
    public const COLUMN_NAMES=[
        'Nom',
        'Prénom',
        'Émail'
    ];
    public const EXTENSION = 'csv';
    public function exportSubscribers(Events $event = null): array
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

        return [
            'spreadsheet' => $this->create($subscribers, self::COLUMN_NAMES),
            'filename'    => $this->getFileName($event),
        ];



    }

    private function getFileName(Events $event)
    {
        return sprintf('Event_%s_%s.%s',
        $event->getId(),
        $event->getCreatedAt()->format('d-m-Y'),
        self::EXTENSION
        );
    }

}