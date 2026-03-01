<?php

namespace App\Services;

/**
 * Zero-dependency Excel exporter using XML Spreadsheet 2003 (SpreadsheetML).
 *
 * Produces a .xls file that is pure XML — no ZipArchive, no extensions needed.
 * Supports: RTL layout, bold headers, hyperlinks, UTF-8 Arabic text.
 */
class XlsxExporter
{
    /** @var array<int, array> */
    private array $rows = [];

    /**
     * Add a row of cells.
     * Each cell is either:
     *   - A plain string
     *   - ['value' => string, 'url' => string]  → hyperlink cell
     *   - ['value' => string, 'bold' => true]   → bold cell
     */
    public function addRow(array $cells): static
    {
        $this->rows[] = $cells;
        return $this;
    }

    public function generate(): string
    {
        $rowsXml = '';

        foreach ($this->rows as $rowIdx => $row) {
            $rowsXml .= '<Row>';

            foreach ($row as $cell) {
                if (is_array($cell) && isset($cell['url'])) {
                    // Hyperlink cell
                    $url      = $this->xe($cell['url']);
                    $value    = $this->xe($cell['value'] ?? $cell['url']);
                    $rowsXml .= '<Cell ss:StyleID="Link" ss:HRef="' . $url . '">'
                        . '<Data ss:Type="String">' . $value . '</Data>'
                        . '</Cell>';
                } elseif (is_array($cell) && ($cell['bold'] ?? false)) {
                    // Bold cell (used for header row)
                    $value    = $this->xe($cell['value'] ?? '');
                    $rowsXml .= '<Cell ss:StyleID="Header">'
                        . '<Data ss:Type="String">' . $value . '</Data>'
                        . '</Cell>';
                } else {
                    // Plain text cell
                    $value    = $this->xe(is_array($cell) ? ($cell['value'] ?? '') : (string) $cell);
                    $rowsXml .= '<Cell ss:StyleID="Default">'
                        . '<Data ss:Type="String">' . $value . '</Data>'
                        . '</Cell>';
                }
            }

            $rowsXml .= '</Row>' . "\n";
        }

        return '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<?mso-application progid="Excel.Sheet"?>' . "\n"
            . '<Workbook'
            . ' xmlns="urn:schemas-microsoft-com:office:spreadsheet"'
            . ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"'
            . ' xmlns:x="urn:schemas-microsoft-com:office:excel"'
            . ' xmlns:o="urn:schemas-microsoft-com:office:office">' . "\n"
            . '<DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">'
            . '<Title>نتائج القرعة</Title>'
            . '</DocumentProperties>' . "\n"
            . '<Styles>' . "\n"
            . '<Style ss:ID="Default">'
            . '<Alignment ss:ReadingOrder="RightToLeft" ss:Horizontal="Right"/>'
            . '</Style>' . "\n"
            . '<Style ss:ID="Header">'
            . '<Font ss:Bold="1"/>'
            . '<Alignment ss:ReadingOrder="RightToLeft" ss:Horizontal="Right"/>'
            . '</Style>' . "\n"
            . '<Style ss:ID="Link">'
            . '<Font ss:Color="#0563C1" ss:Underline="Single"/>'
            . '<Alignment ss:ReadingOrder="RightToLeft" ss:Horizontal="Right"/>'
            . '</Style>' . "\n"
            . '</Styles>' . "\n"
            . '<Worksheet ss:Name="نتائج القرعة">' . "\n"
            . '<Table>' . "\n"
            . $rowsXml
            . '</Table>' . "\n"
            . '<WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">'
            . '<DisplayRightToLeft/>'
            . '</WorksheetOptions>' . "\n"
            . '</Worksheet>' . "\n"
            . '</Workbook>';
    }

    private function xe(string $s): string
    {
        return htmlspecialchars($s, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
