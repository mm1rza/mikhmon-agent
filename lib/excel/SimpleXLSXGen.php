<?php
/**
 * SimpleXLSXGen
 *
 * Lightweight XLSX generator (no Composer dependency).
 * Source: https://github.com/shuchkin/simplexlsxgen (MIT License)
 */

if (!class_exists('SimpleXLSXGen')) {
    class SimpleXLSXGen
    {
        private array $rows = [];
        private array $headerStyles = [];

        public static function fromArray(array $rows, ?array $headerStyles = null): self
        {
            $xlsx = new self();
            $xlsx->rows = $rows;
            if ($headerStyles !== null) {
                $xlsx->headerStyles = $headerStyles;
            }
            return $xlsx;
        }

        public function downloadAs(string $filename): void
        {
            $data = $this->buildXLSX();
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . strlen($data));
            echo $data;
            exit;
        }

        public function buildXLSX(): string
        {
            $sharedStrings = [];
            $sharedStringsCount = 0;
            $sheetData = '';

            foreach ($this->rows as $rowIndex => $row) {
                $sheetData .= '<row r="' . ($rowIndex + 1) . '">';
                foreach ($row as $columnIndex => $value) {
                    $cell = $this->convertToCell($value, $sharedStrings, $sharedStringsCount);
                    $cellRef = $this->cellAddress($rowIndex, $columnIndex);
                    $styleAttr = '';
                    if ($rowIndex === 0 && isset($this->headerStyles[$columnIndex])) {
                        $styleAttr = ' s="' . (int)$this->headerStyles[$columnIndex] . '"';
                    }
                    $sheetData .= '<c r="' . $cellRef . '"' . $cell['attributes'] . $styleAttr . '>' . $cell['value'] . '</c>';
                }
                $sheetData .= '</row>';
            }

            $xmlSheet = '<?xml version="1.0" encoding="UTF-8"?>'
                . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"'
                . ' xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
                . '<sheetData>' . $sheetData . '</sheetData>'
                . '</worksheet>';

            $sharedStringsXml = '<?xml version="1.0" encoding="UTF-8"?>'
                . '<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="' . $sharedStringsCount . '" uniqueCount="' . count($sharedStrings) . '">';
            foreach ($sharedStrings as $text) {
                $sharedStringsXml .= '<si><t>' . self::xmlEscape($text) . '</t></si>';
            }
            $sharedStringsXml .= '</sst>';

            $workbookXml = '<?xml version="1.0" encoding="UTF-8"?>'
                . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"'
                . ' xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
                . '<sheets><sheet name="Sheet1" sheetId="1" r:id="rId1"/></sheets>'
                . '</workbook>';

            $relsWorkbook = '<?xml version="1.0" encoding="UTF-8"?>'
                . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
                . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
                . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>'
                . '</Relationships>';

            $relsRoot = '<?xml version="1.0" encoding="UTF-8"?>'
                . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
                . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
                . '</Relationships>';

            $contentTypes = '<?xml version="1.0" encoding="UTF-8"?>'
                . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
                . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
                . '<Default Extension="xml" ContentType="application/xml"/>'
                . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
                . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
                . '<Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>'
                . '</Types>';

            $zip = new ZipArchive();
            $tmpFile = tempnam(sys_get_temp_dir(), 'xlsx');
            $zip->open($tmpFile, ZipArchive::OVERWRITE);
            $zip->addFromString('[Content_Types].xml', $contentTypes);
            $zip->addFromString('_rels/.rels', $relsRoot);
            $zip->addFromString('xl/_rels/workbook.xml.rels', $relsWorkbook);
            $zip->addFromString('xl/workbook.xml', $workbookXml);
            $zip->addFromString('xl/worksheets/sheet1.xml', $xmlSheet);
            $zip->addFromString('xl/sharedStrings.xml', $sharedStringsXml);
            $zip->close();

            $data = file_get_contents($tmpFile);
            unlink($tmpFile);
            return $data === false ? '' : $data;
        }

        private function convertToCell($value, array &$sharedStrings, int &$sharedStringsCount): array
        {
            if (is_numeric($value)) {
                return [
                    'attributes' => ' t="n"',
                    'value' => '<v>' . $value . '</v>'
                ];
            }

            if ($value === null || $value === '') {
                return [
                    'attributes' => '',
                    'value' => ''
                ];
            }

            $text = (string)$value;
            $key = array_search($text, $sharedStrings, true);
            if ($key === false) {
                $sharedStrings[] = $text;
                $key = count($sharedStrings) - 1;
            }
            $sharedStringsCount++;

            return [
                'attributes' => ' t="s"',
                'value' => '<v>' . $key . '</v>'
            ];
        }

        private function cellAddress(int $rowIndex, int $columnIndex): string
        {
            $column = '';
            $columnIndexCopy = $columnIndex;
            while ($columnIndexCopy >= 0) {
                $column = chr($columnIndexCopy % 26 + 65) . $column;
                $columnIndexCopy = (int)($columnIndexCopy / 26) - 1;
            }
            return $column . ($rowIndex + 1);
        }

        private static function xmlEscape(string $value): string
        {
            return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        }
    }
}
