<?php

namespace App\Service\SpreadSheet;

use PhpOffice\PhpSpreadsheet\IOFactory;

class SpreadSheetService
{
    public function processFile($uploadedFile): array
    {
        $spreadsheet = IOFactory::load($uploadedFile);
        return $spreadsheet->getActiveSheet()->toArray(null, true, true, false);

    }
}