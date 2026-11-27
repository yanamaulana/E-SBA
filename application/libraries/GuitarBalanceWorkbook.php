<?php
defined('BASEPATH') or exit('No direct script access allowed');

class GuitarBalanceWorkbook
{
    public function makeRows(array $months)
    {
        $groups = []; $rows = [];
        foreach ($months as $m) {
            $values = [];
            foreach (['Order','Balance','Export'] as $section) {
                foreach (['AG','EG'] as $type) {
                    $values[] = (float)$m[$type.'_'.$section.'Qty'];
                    $values[] = (float)$m[$type.'_'.$section.'Amt'];
                }
                $values[] = $values[count($values)-3] + $values[count($values)-1];
            }
            $groups[(int)$m['Production_year']][] = [
                'year'=>(int)$m['Production_year'], 'month'=>date('M',mktime(0,0,0,(int)$m['Production_month'],1,2000)),
                'kind'=>'month','values'=>$values
            ];
        }
        foreach ($groups as $year=>$group) {
            $total = array_fill(0,15,0.0);
            foreach ($group as $row) {
                foreach ($row['values'] as $i=>$value) { $total[$i] += $value; }
            }
            // Display monthly rows before the totals for each selected year.
            foreach ($group as $row) { $rows[]=$row; }
            $rows[]=['year'=>$year,'month'=>'Total','kind'=>'total','values'=>$total];
            $rows[]=['year'=>$year,'month'=>'Avrg','kind'=>'average','values'=>array_map(function($v) use ($group) {return $v/count($group);},$total)];
        }
        return $rows;
    }

    public function note()
    {
        return 'Production period, not shipment date. SG/GS POs excluded. Export quantities use approved non-void shipment notes. Amounts use proportional booked SO values, not invoice values (provisional). Negative balances retained; no shipment cutoff. Totals and averages cover selected months only. Excel displays USD amounts and average quantities as whole numbers; CSV uses numeric values with decimal places.';
    }

    public function csv(array $rows, $period, $generated)
    {
        $fp = fopen('php://temp','w+');
        fwrite($fp,"\xEF\xBB\xBF");
        fputcsv($fp,['Guitar Order Balance Summary'], ',', '"', '');
        fputcsv($fp,[$period,'Generated: '.$generated], ',', '"', '');
        $head=['Year','Month'];
        foreach (['Order','Balance','Export'] as $s) {
            foreach (['AG Qty','AG Amt USD','EG Qty','EG Amt USD','Total Amt USD'] as $h) {$head[]=$s.' '.$h;}
        }
        fputcsv($fp,$head, ',', '"', '');
        foreach ($rows as $r) {
            $v=[]; foreach ($r['values'] as $i=>$n) {$v[]=number_format($n,in_array($i%5,[0,2],true)?4:2,'.','');}
            fputcsv($fp,array_merge([$r['year'],$r['month']],$v), ',', '"', '');
        }
        fputcsv($fp,[$this->note()], ',', '"', '');
        rewind($fp); $out=stream_get_contents($fp); fclose($fp); return $out;
    }

    private function esc($s) { return htmlspecialchars((string)$s,ENT_XML1|ENT_QUOTES,'UTF-8'); }
    private function col($i) { return chr(65+$i); } // A–Q only
    private function strCell($col,$row,$value,$style=0) {
        return '<c r="'.$this->col($col).$row.'" s="'.$style.'" t="inlineStr"><is><t xml:space="preserve">'.$this->esc($value).'</t></is></c>';
    }
    private function numCell($col,$row,$value,$style=0) {
        return '<c r="'.$this->col($col).$row.'" s="'.$style.'"><v>'.number_format($value,8,'.','').'</v></c>';
    }

    public function xlsx(array $rows,$period,$generated)
    {
        $ns='http://schemas.openxmlformats.org/spreadsheetml/2006/main';
        $rel='http://schemas.openxmlformats.org/officeDocument/2006/relationships';
        $xml='<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $sheet=$xml.'<worksheet xmlns="'.$ns.'"><sheetViews><sheetView workbookViewId="0"><pane xSplit="2" ySplit="5" topLeftCell="C6" activePane="bottomRight" state="frozen"/></sheetView></sheetViews><cols><col min="1" max="1" width="10" customWidth="1"/><col min="2" max="2" width="13" customWidth="1"/>';
        for($i=0;$i<15;$i++) {$sheet.='<col min="'.($i+3).'" max="'.($i+3).'" width="'.(in_array($i%5,[0,2],true)?12:19).'" customWidth="1"/>';}
        $sheet.='</cols><sheetData>';
        $sheet.='<row r="1" ht="27" customHeight="1">'.$this->strCell(0,1,'Guitar Order Balance Summary',1).'</row>';
        $sheet.='<row r="2">'.$this->strCell(0,2,$period.' | Generated: '.$generated).'</row>';
        $merges=['A1:Q1','A2:Q2','A3:A5','B3:B5'];
        $sheet.='<row r="3">'.$this->strCell(0,3,'Year',2).$this->strCell(1,3,'Month',2);
        foreach(['Order','Balance','Export'] as $i=>$s) {$c=2+5*$i; $sheet.=$this->strCell($c,3,$s,2); $merges[]=$this->col($c).'3:'.$this->col($c+4).'3';}
        $sheet.='</row><row r="4">';
        for($i=0;$i<3;$i++) {$c=2+5*$i; foreach([0=>'AG',2=>'EG',4=>'Total'] as $j=>$s) {$sheet.=$this->strCell($c+$j,4,$s,2); if($j!==4)$merges[]=$this->col($c+$j).'4:'.$this->col($c+$j+1).'4';}}
        $sheet.='</row><row r="5">';
        for($i=0;$i<15;$i++) {$sheet.=$this->strCell($i+2,5,in_array($i%5,[0,2],true)?'Qty':($i%5===4?'Total Amt':'Amt'),2);}
        $sheet.='</row>'; $r=6;
        foreach($rows as $row) {
            $sum=$row['kind']!=='month';
            $sheet.='<row r="'.$r.'">'.$this->numCell(0,$r,$row['year'],$sum?5:0).$this->strCell(1,$r,$row['month'],$sum?5:0);
            foreach($row['values'] as $i=>$n) {
                $qty=in_array($i%5,[0,2],true); $style=$sum?($qty?($row['kind']==='average'?10:6):7):($qty?3:($i%5===4?8:4));
                $sheet.=$this->numCell($i+2,$r,$n,$style);
            } $sheet.='</row>'; $r++;
        }
        $sheet.='<row r="'.($r+1).'" ht="32" customHeight="1">'.$this->strCell(0,$r+1,$this->note(),9).'</row></sheetData>';
        $merges[]='A'.($r+1).':Q'.($r+1);
        $sheet.='<mergeCells count="'.count($merges).'">'; foreach($merges as $m){$sheet.='<mergeCell ref="'.$m.'"/>';} $sheet.='</mergeCells><pageSetup orientation="landscape" paperSize="9" fitToWidth="1" fitToHeight="0"/></worksheet>';
        $styles=$xml.'<styleSheet xmlns="'.$ns.'"><numFmts count="3"><numFmt numFmtId="164" formatCode="#,##0.####;[Red](#,##0.####);&quot;–&quot;"/><numFmt numFmtId="165" formatCode="&quot;$&quot;* #,##0;[Red]&quot;$&quot;* -#,##0;&quot;$&quot;* &quot;–&quot;"/><numFmt numFmtId="166" formatCode="#,##0;[Red](#,##0);&quot;–&quot;"/></numFmts><fonts count="3"><font><sz val="11"/><name val="Calibri"/></font><font><b/><sz val="11"/><name val="Calibri"/></font><font><b/><sz val="18"/><name val="Calibri"/></font></fonts><fills count="4"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill><fill><patternFill patternType="solid"><fgColor rgb="FFDCE6F1"/><bgColor indexed="64"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor rgb="FFE2E8F0"/><bgColor indexed="64"/></patternFill></fill></fills><borders count="2"><border><left/><right/><top/><bottom/><diagonal/></border><border><left style="thin"/><right style="thin"/><top style="thin"/><bottom style="thin"/><diagonal/></border></borders><cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs><cellXfs count="11">';
        foreach([[0,0,0,1],[0,2,0,0],[0,1,2,1],[164,0,0,1],[165,0,0,1],[0,1,3,1],[164,1,3,1],[165,1,3,1],[165,1,0,1],[0,0,0,0],[166,1,3,1]] as $i=>$s){$styles.='<xf numFmtId="'.$s[0].'" fontId="'.$s[1].'" fillId="'.$s[2].'" borderId="'.$s[3].'" xfId="0" applyNumberFormat="1" applyFont="1" applyFill="1" applyBorder="1"'.(in_array($i,[2,9],true)?' applyAlignment="1"><alignment '.($i===2?'horizontal="center" vertical="center"':'wrapText="1"').'/></xf>':'/>');}
        $styles.='</cellXfs><cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles></styleSheet>';
        $parts=[
            '[Content_Types].xml'=>$xml.'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/><Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/></Types>',
            '_rels/.rels'=>$xml.'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="'.$rel.'/officeDocument" Target="xl/workbook.xml"/></Relationships>',
            'xl/workbook.xml'=>$xml.'<workbook xmlns="'.$ns.'" xmlns:r="'.$rel.'"><sheets><sheet name="Guitar Balance" sheetId="1" r:id="rId1"/></sheets></workbook>',
            'xl/_rels/workbook.xml.rels'=>$xml.'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="'.$rel.'/worksheet" Target="worksheets/sheet1.xml"/><Relationship Id="rId2" Type="'.$rel.'/styles" Target="styles.xml"/></Relationships>',
            'xl/worksheets/sheet1.xml'=>$sheet,'xl/styles.xml'=>$styles
        ];
        $path=tempnam(sys_get_temp_dir(),'gbr');
        if($path===false) throw new RuntimeException('Cannot create workbook.');
        try {
            $zip=new ZipArchive(); if($zip->open($path,ZipArchive::CREATE|ZipArchive::OVERWRITE)!==true) throw new RuntimeException('Cannot open workbook.');
            foreach($parts as $name=>$content){if(!$zip->addFromString($name,$content)){ $zip->close(); throw new RuntimeException('Cannot write workbook.'); }}
            if(!$zip->close()) throw new RuntimeException('Cannot finalize workbook.');
            $bytes=file_get_contents($path); if($bytes===false) throw new RuntimeException('Cannot read workbook.'); return $bytes;
        } finally { @unlink($path); }
    }
}
