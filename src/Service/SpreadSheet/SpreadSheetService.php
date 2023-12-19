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

    public function create(array $columnValues, array $columnNames): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();

        // Get active sheet - it is also possible to retrieve a specific sheet
        $sheet = $spreadsheet->getActiveSheet();

        // Set column names
        $columnLetter = 'A';
        foreach ($columnNames as $columnName) {
            // Allow to access AA column if needed and more
            $sheet->setCellValue($columnLetter.'1', $columnName);
            ++$columnLetter;
        }

        // set column values
        $i = 2; // Beginning row for active sheet
        foreach ($columnValues as $columnValue) {
            $columnLetter = 'A';
            foreach ($columnValue as $value) {
                $sheet->setCellValue($columnLetter.$i, $value);
                ++$columnLetter;
            }
            ++$i;
        }

        return $spreadsheet;
    }
}
