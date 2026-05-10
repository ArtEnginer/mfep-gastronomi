<?php

function excelColumnToIndex(string $column): int
{
    $column = strtoupper(trim($column));
    $length = strlen($column);
    $index = 0;

    for ($i = 0; $i < $length; $i++) {
        $char = ord($column[$i]);
        if ($char < 65 || $char > 90) {
            continue;
        }
        $index = $index * 26 + ($char - 64);
    }

    return max(0, $index - 1);
}

function normalizeHeader(string $value): string
{
    return strtolower(trim($value));
}

function parseCsvRows(string $filePath): array
{
    $rows = [];
    $handle = fopen($filePath, 'r');
    if (!$handle) {
        return $rows;
    }

    while (($row = fgetcsv($handle, 0, ',')) !== false) {
        $rows[] = $row;
    }

    fclose($handle);

    return $rows;
}

function parseXlsxRows(string $filePath, ?string &$error = null): array
{
    if (!class_exists('ZipArchive')) {
        $error = 'Ekstensi PHP ZipArchive belum aktif. Aktifkan extension=zip di php.ini.';
        return [];
    }

    $zip = new ZipArchive();
    if ($zip->open($filePath) !== true) {
        $error = 'File .xlsx tidak dapat dibuka.';
        return [];
    }

    $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
    if ($sheetXml === false) {
        $zip->close();
        $error = 'Sheet1 pada file .xlsx tidak ditemukan.';
        return [];
    }

    $sharedStrings = [];
    $sharedXml = $zip->getFromName('xl/sharedStrings.xml');
    if ($sharedXml !== false) {
        $shared = simplexml_load_string($sharedXml);
        if ($shared !== false) {
            $nsShared = $shared->getNamespaces(true);
            $sharedRoot = isset($nsShared['']) ? $shared->children($nsShared['']) : $shared;
            foreach ($sharedRoot->si as $si) {
                $siRoot = isset($nsShared['']) ? $si->children($nsShared['']) : $si;
                if (isset($siRoot->t)) {
                    $sharedStrings[] = (string)$siRoot->t;
                } elseif (isset($siRoot->r)) {
                    $value = '';
                    foreach ($siRoot->r as $run) {
                        $runRoot = isset($nsShared['']) ? $run->children($nsShared['']) : $run;
                        $value .= (string)$runRoot->t;
                    }
                    $sharedStrings[] = $value;
                } else {
                    $sharedStrings[] = '';
                }
            }
        }
    }

    $xml = simplexml_load_string($sheetXml);
    $zip->close();
    if ($xml === false) {
        $error = 'Format XML pada .xlsx tidak valid.';
        return [];
    }

    $rows = [];
    $ns = $xml->getNamespaces(true);
    $root = isset($ns['']) ? $xml->children($ns['']) : $xml;

    foreach ($root->sheetData->row as $row) {
        $rowData = [];
        foreach ($row->c as $cell) {
            $cellRef = (string)$cell['r'];
            $column = preg_replace('/\d+/', '', $cellRef);
            $index = excelColumnToIndex($column);
            $type = (string)$cell['t'];

            $value = '';
            if ($type === 's') {
                $sharedIndex = (int)$cell->v;
                $value = $sharedStrings[$sharedIndex] ?? '';
            } elseif ($type === 'inlineStr') {
                $value = (string)$cell->is->t;
            } else {
                $value = isset($cell->v) ? (string)$cell->v : '';
            }

            $rowData[$index] = trim($value);
        }

        if (!empty($rowData)) {
            ksort($rowData);
            $rows[] = $rowData;
        }
    }

    return $rows;
}

function readSpreadsheetAssociativeRows(array $fileInfo, ?string &$error = null): array
{
    if (!isset($fileInfo['tmp_name'], $fileInfo['name']) || $fileInfo['tmp_name'] === '') {
        $error = 'File belum dipilih.';
        return [];
    }

    $extension = strtolower(pathinfo($fileInfo['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ['xlsx', 'csv'], true)) {
        $error = 'Format file harus .xlsx atau .csv';
        return [];
    }

    $rawRows = [];
    if ($extension === 'csv') {
        $rawRows = parseCsvRows($fileInfo['tmp_name']);
    } else {
        $rawRows = parseXlsxRows($fileInfo['tmp_name'], $error);
    }

    if (empty($rawRows)) {
        if ($error === null) {
            $error = 'Data kosong atau format tidak sesuai.';
        }
        return [];
    }

    $headerRow = array_shift($rawRows);
    $headers = [];
    foreach ($headerRow as $header) {
        $headers[] = normalizeHeader((string)$header);
    }

    $rows = [];
    foreach ($rawRows as $rawRow) {
        $assoc = [];
        $hasValue = false;
        foreach ($headers as $i => $header) {
            if ($header === '') {
                continue;
            }
            $value = isset($rawRow[$i]) ? trim((string)$rawRow[$i]) : '';
            if ($value !== '') {
                $hasValue = true;
            }
            $assoc[$header] = $value;
        }

        if ($hasValue) {
            $rows[] = $assoc;
        }
    }

    return $rows;
}
