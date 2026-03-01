<?php

namespace App\Services;

/**
 * Zero-dependency XLSX generator.
 * Produces a valid .xlsx file (Office Open XML) using PHP's built-in ZipArchive.
 * Supports: shared strings, RTL sheet, external hyperlinks.
 */
class XlsxExporter
{
    /** @var array<int, array> Rows of cells */
    private array $rows = [];

    /** @var array<int, string> Shared strings pool */
    private array $strings = [];

    /**
     * Add a row of cells.
     * Each cell is either:
     *   - A string: plain text
     *   - An array ['value' => string, 'url' => string]: hyperlink cell
     */
    public function addRow(array $cells): static
    {
        $this->rows[] = $cells;
        return $this;
    }

    // -------------------------------------------------------------------------
    // Public generate
    // -------------------------------------------------------------------------

    public function generate(): string
    {
        [$sheetXml, $sheetRelsXml] = $this->buildSheet();
        $ssXml                     = $this->buildSharedStrings();

        // Use a unique non-existing path — ZipArchive::CREATE works cleanly on new files
        $tmpFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'xlsx_' . uniqid('', true) . '.xlsx';

        $zip    = new \ZipArchive();
        $result = $zip->open($tmpFile, \ZipArchive::CREATE);

        if ($result !== true) {
            throw new \RuntimeException('ZipArchive failed to open: code ' . $result);
        }

        $zip->addFromString('[Content_Types].xml',     $this->contentTypes());
        $zip->addFromString('_rels/.rels',              $this->rootRels());
        $zip->addFromString('xl/workbook.xml',          $this->workbook());
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->workbookRels());
        $zip->addFromString('xl/styles.xml',            $this->styles());
        $zip->addFromString('xl/sharedStrings.xml',     $ssXml);
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);

        if ($sheetRelsXml) {
            $zip->addFromString('xl/worksheets/_rels/sheet1.xml.rels', $sheetRelsXml);
        }

        $zip->close();

        $content = file_get_contents($tmpFile);
        unlink($tmpFile);

        return $content;
    }

    // -------------------------------------------------------------------------
    // Sheet builder
    // -------------------------------------------------------------------------

    private function buildSheet(): array
    {
        $hyperlinks   = [];
        $rowsXml      = '';

        foreach ($this->rows as $rowIdx => $row) {
            $rowNum  = $rowIdx + 1;
            $rowXml  = '<row r="' . $rowNum . '">';

            foreach ($row as $colIdx => $cell) {
                $ref = $this->cellRef($rowIdx, $colIdx);

                if (is_array($cell) && isset($cell['url'])) {
                    $hlId = 'hId' . (count($hyperlinks) + 1);
                    $hyperlinks[] = [
                        'ref' => $ref,
                        'url' => $cell['url'],
                        'id'  => $hlId,
                    ];
                    $si     = $this->si($cell['value'] ?? $cell['url']);
                    $rowXml .= '<c r="' . $ref . '" t="s"><v>' . $si . '</v></c>';
                } else {
                    $value  = is_array($cell) ? ($cell['value'] ?? '') : (string) $cell;
                    $si     = $this->si($value);
                    $rowXml .= '<c r="' . $ref . '" t="s"><v>' . $si . '</v></c>';
                }
            }

            $rowXml  .= '</row>';
            $rowsXml .= $rowXml;
        }

        // Hyperlinks block inside sheet
        $hlXml = '';
        if (!empty($hyperlinks)) {
            $hlXml = '<hyperlinks>';
            foreach ($hyperlinks as $hl) {
                $hlXml .= '<hyperlink ref="' . $hl['ref'] . '" r:id="' . $hl['id'] . '"/>';
            }
            $hlXml .= '</hyperlinks>';
        }

        $sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet'
            . ' xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"'
            . ' xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheetViews>'
            . '<sheetView workbookViewId="0" rightToLeft="1"/>'
            . '</sheetViews>'
            . '<sheetData>' . $rowsXml . '</sheetData>'
            . $hlXml
            . '</worksheet>';

        // Sheet relationships for hyperlinks
        $sheetRelsXml = '';
        if (!empty($hyperlinks)) {
            $sheetRelsXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">';
            foreach ($hyperlinks as $hl) {
                $sheetRelsXml .= '<Relationship'
                    . ' Id="' . $hl['id'] . '"'
                    . ' Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/hyperlink"'
                    . ' Target="' . $this->xe($hl['url']) . '"'
                    . ' TargetMode="External"/>';
            }
            $sheetRelsXml .= '</Relationships>';
        }

        return [$sheetXml, $sheetRelsXml];
    }

    private function buildSharedStrings(): string
    {
        $count = count($this->strings);
        $xml   = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"'
            . ' count="' . $count . '" uniqueCount="' . $count . '">';

        foreach ($this->strings as $s) {
            $xml .= '<si><t xml:space="preserve">' . $this->xe($s) . '</t></si>';
        }

        return $xml . '</sst>';
    }

    // -------------------------------------------------------------------------
    // Static XML parts
    // -------------------------------------------------------------------------

    private function contentTypes(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '<Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . '</Types>';
    }

    private function rootRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';
    }

    private function workbook(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"'
            . ' xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets><sheet name="نتائج القرعة" sheetId="1" r:id="rId1"/></sheets>'
            . '</workbook>';
    }

    private function workbookRels(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>'
            . '<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            . '</Relationships>';
    }

    private function styles(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<fonts count="1"><font><sz val="11"/><name val="Calibri"/></font></fonts>'
            . '<fills count="2">'
            . '<fill><patternFill patternType="none"/></fill>'
            . '<fill><patternFill patternType="gray125"/></fill>'
            . '</fills>'
            . '<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/></cellXfs>'
            . '</styleSheet>';
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /** Get or create shared string index */
    private function si(string $value): int
    {
        $idx = array_search($value, $this->strings, true);
        if ($idx === false) {
            $this->strings[] = $value;
            $idx = count($this->strings) - 1;
        }
        return $idx;
    }

    /** Excel column letter(s) from 0-based index */
    private function colLetter(int $col): string
    {
        $result = '';
        do {
            $result = chr(65 + ($col % 26)) . $result;
            $col    = intdiv($col, 26) - 1;
        } while ($col >= 0);
        return $result;
    }

    private function cellRef(int $rowIdx, int $colIdx): string
    {
        return $this->colLetter($colIdx) . ($rowIdx + 1);
    }

    /** XML-escape a string */
    private function xe(string $s): string
    {
        return htmlspecialchars($s, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
