<?php

namespace App\Services;

// Small native OOXML report. PharData creates ZIP containers without requiring
// the optional PHP zip extension or installing an Excel server dependency.
class ReportWorkbookWriter
{
    private function xml(string $value): string
    {
        return htmlspecialchars(preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', '', $value), ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    private function column(int $index): string
    {
        $result = '';
        do {
            $result = chr(65 + $index % 26).$result;
            $index = intdiv($index, 26) - 1;
        } while ($index >= 0);

        return $result;
    }

    public function make(array $columns, array $data, float $funds, float $expenses): string
    {
        $sheet = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetViews><sheetView workbookViewId="0"><pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews><cols>';
        foreach (array_values($columns) as $i => $col) {
            $sheet .= '<col min="'.($i + 1).'" max="'.($i + 1).'" width="'.($col[1] === 'text' ? 28 : 19).'" customWidth="1"/>';
        }
        $sheet .= '</cols><sheetData><row r="1" ht="32" customHeight="1">';
        foreach (array_values($columns) as $i => $col) {
            $sheet .= '<c r="'.$this->column($i).'1" t="inlineStr" s="1"><is><t>'.$this->xml($col[0]).'</t></is></c>';
        }
        $sheet .= '</row>';
        foreach ($data as $rowIndex => $row) {
            $n = $rowIndex + 2;
            $sheet .= '<row r="'.$n.'">';
            $i = 0;
            foreach ($columns as $key => [$label, $type]) {
                $value = $row[$key] ?? null;
                $address = $this->column($i++).$n;
                if ($value !== null && $type === 'date') {
                    $value = (new \DateTimeImmutable('1899-12-30'))->diff(new \DateTimeImmutable($value))->days;
                }
                if ($value !== null && in_array($type, ['number', 'money', 'date'])) {
                    $sheet .= '<c r="'.$address.'" s="'.($type === 'date' ? 2 : ($type === 'money' ? 3 : 0)).'"><v>'.(float) $value.'</v></c>';
                } else {
                    $sheet .= '<c r="'.$address.'" t="inlineStr"><is><t xml:space="preserve">'.$this->xml((string) $value).'</t></is></c>';
                }
            }
            $sheet .= '</row>';
        }
        $total = count($data) + 2;
        $sheet .= '<row r="'.$total.'"><c r="A'.$total.'" t="inlineStr"><is><t>TOTALES SIN DUPLICAR</t></is></c>';
        foreach (['counted_funds' => $funds, 'counted_expenses' => $expenses] as $key => $value) {
            $address = $this->column(array_search($key, array_keys($columns), true)).$total;
            $sheet .= '<c r="'.$address.'" s="3"><v>'.$value.'</v></c>';
        }
        $sheet .= '</row></sheetData><autoFilter ref="A1:'.$this->column(count($columns) - 1).(count($data) + 1).'"/></worksheet>';
        $base = tempnam(sys_get_temp_dir(), 'seges-report-');
        $path = $base.'.zip';
        try {
            $zip = new \PharData($path, 0, null, \Phar::ZIP);
            $zip['[Content_Types].xml'] = '<?xml version="1.0"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/><Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/></Types>';
            $zip['_rels/.rels'] = '<?xml version="1.0"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>';
            $zip['xl/workbook.xml'] = '<?xml version="1.0"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="Rendiciones" sheetId="1" r:id="rId1"/></sheets></workbook>';
            $zip['xl/_rels/workbook.xml.rels'] = '<?xml version="1.0"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/></Relationships>';
            $zip['xl/styles.xml'] = '<?xml version="1.0"?><styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><numFmts count="1"><numFmt numFmtId="164" formatCode="yyyy-mm-dd"/></numFmts><fonts count="2"><font><sz val="11"/><name val="Arial"/></font><font><b/><color rgb="FFFFFFFF"/><sz val="11"/><name val="Arial"/></font></fonts><fills count="3"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill><fill><patternFill patternType="solid"><fgColor rgb="FF0F172A"/><bgColor indexed="64"/></patternFill></fill></fills><borders count="1"><border/></borders><cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs><cellXfs count="4"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/><xf numFmtId="0" fontId="1" fillId="2" borderId="0" xfId="0" applyAlignment="1"><alignment wrapText="1"/></xf><xf numFmtId="164" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="1"/><xf numFmtId="3" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="1"/></cellXfs><cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles></styleSheet>';
            $zip['xl/worksheets/sheet1.xml'] = $sheet;
            unset($zip);

            return file_get_contents($path);
        } finally {
            if (is_file($path)) {
                unlink($path);
            } if (is_file($base)) {
                unlink($base);
            }
        }
    }
}
