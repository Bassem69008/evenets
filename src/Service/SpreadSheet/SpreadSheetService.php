<?php

namespace App\Service\SpreadSheet;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class SpreadSheetService
{
    public function processFile($uploadedFile): array
    {
        $spreadsheet = IOFactory::load($uploadedFile);
        return $spreadsheet->getActiveSheet()->toArray(null, true, true, false);

    }

    public function manage(array $columnValues, array $columnNames, )
    {
        $spreadsheet = new Spreadsheet();

        // Get active sheet - it is also possible to retrieve a specific sheet
        $sheet = $spreadsheet->getActiveSheet();

        // Set cell name and merge cells
        $sheet->setCellValue('A1', 'Browser characteristics')->mergeCells('A1:D1');

        // Set column names

        $columnLetter = 'A';
        foreach ($columnNames as $columnName) {
            // Allow to access AA column if needed and more
            $columnLetter++;
            $sheet->setCellValue($columnLetter.'2', $columnName);
        }


        $i = 3; // Beginning row for active sheet
        foreach ($columnValues as $columnValue) {
            $columnLetter = 'A';
            foreach ($columnValue as $value) {
                $columnLetter++;
            $sheet->setCellValue($columnLetter.$i, $value);
        }
            $i++;
        }


        return $spreadsheet;
    }
}