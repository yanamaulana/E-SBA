<?php
 defined('BASEPATH') or exit('No direct script access allowed');
/** Independent native XLSX exporter. No changes to the ERP or existing workbook library. */
class GuitarBalanceExcel
{
    private const ORDER = ['AG_OrderQty','AG_OrderAmt','EG_OrderQty','EG_OrderAmt'];
    private const SECTIONS = ['Order','Balance','Export'];
    private const MONTHS = ['JAN','FEB','MAR','APR','MAY','JUN','JUL','AUG','SEP','OCT','NOV','DEC'];
    private const ZERO = [0,0,0,0];
    private static function xml($s): string {
        return htmlspecialchars((string)$s, ENT_QUOTES | ENT_XML1 | ENT_SUBSTITUTE, 'UTF-8');
    }
    private static function col(int $c): string {
        $s=''; while ($c>0) { --$c; $s=chr(65+$c%26).$s; $c=intdiv($c,26); } return $s;
    }
    private static function number($v): string {
        if (!is_numeric($v) || !is_finite((float)$v)) { throw new RuntimeException('Invalid numeric report value.'); }
        return str_replace(',', '.', sprintf('%.15g', (float)$v)); // Excel supports 15 significant digits.
    }
    private static function num(array $r, string $key): float {
        if (!array_key_exists($key,$r) || !is_numeric($r[$key]) || !is_finite((float)$r[$key])) { throw new RuntimeException('Missing or invalid field: '.$key); }
        return (float)$r[$key];
    }
    private static function add(array $a,array $b): array {
        return array_map(static function($x,$y){return $x+$y;},$a,$b);
    }
    // All external strings are inline text, never interpreted as formulas.
    private static function cell(array &$s,int $r,int $c,$v,int $style=0,?string $formula=null): void {
        // Whole quantities have no trailing decimal point; fractional quantities retain up to 4 decimals.
        if (is_numeric($v) && abs((float)$v-round((float)$v))>0.0000001) { $style=[3=>16,7=>17,13=>18,27=>34,31=>39,44=>54,46=>55,49=>56,51=>57,62=>69,65=>70,72=>74][$style]??$style; }
        $ref=self::col($c).$r; $prefix='<c r="'.$ref.'" s="'.$style.'"';
        if ($formula!==null) $xml=$prefix.'><f>'.self::xml($formula).'</f><v>'.self::number($v).'</v></c>';
        elseif (is_int($v)||is_float($v)) $xml=$prefix.'><v>'.self::number($v).'</v></c>';
        elseif ($v===null) $xml=$prefix.'/>';
        else $xml=$prefix.' t="inlineStr"><is><t xml:space="preserve">'.self::xml($v).'</t></is></c>';
        $s['rows'][$r][$c]=$xml;
    }
    private static function merge(array &$s,int $r,int $c,int $r2,int $c2,$v,int $style): void {
        for ($i=$r;$i<=$r2;$i++) for ($j=$c;$j<=$c2;$j++) self::cell($s,$i,$j,null,$style);
        self::cell($s,$r,$c,$v,$style);
        if ($r!==$r2||$c!==$c2) $s['merges'][]=self::col($c).$r.':'.self::col($c2).$r2;
    }
    private static function sheet(array $widths,int $freeze): array {
        return ['rows'=>[],'merges'=>[],'widths'=>$widths,'heights'=>[],'freeze'=>$freeze];
    }
    private static function note(array &$s,int $r,string $v,int $style=11): void {
        self::merge($s,$r,1,$r,count($s['widths']),$v,$style); $s['heights'][$r]=$style===2?27:24;
    }
    private static function style(bool $amount,bool $total=false,bool $average=false): int {
        if ($average) return $amount?8:9;
        if ($total) return $amount?6:7;
        return $amount?4:3;
    }
    private function model(array $months,array $buyerRows,array $filters): array {
        $selected=[];$years=[];$byMonth=[];$buyers=[];
        $y=(int)$filters['from_year'];$m=(int)$filters['from_month'];
        $endY=(int)$filters['to_year'];$endM=(int)$filters['to_month'];
        while ($y<$endY||($y===$endY&&$m<=$endM)) {
            $k=sprintf('%04d-%02d',$y,$m);$selected[$k]=[$y,$m];$years[$y]=$y;
            if (++$m>12) {$m=1;++$y;} if(count($selected)>120) throw new RuntimeException('Invalid report period.');
        }
        if (!$selected) throw new RuntimeException('Empty period.');
        foreach ($months as $r) {
            $k=sprintf('%04d-%02d',(int)$r['Production_year'],(int)$r['Production_month']);
            if (!isset($selected[$k])||isset($byMonth[$k])) throw new RuntimeException('Unexpected balance period.');
            $v=[];foreach(self::SECTIONS as $section) foreach(['AG','EG'] as $type) foreach(['Qty','Amt'] as $metric) $v[]=self::num($r,$type.'_'.$section.$metric);
            $byMonth[$k]=$v;
        }
        if(count($byMonth)!==count($selected)) throw new RuntimeException('Missing balance period.');
        $totals=array_fill_keys(array_keys($selected),self::ZERO);
        foreach ($buyerRows as $r) {
            $k=sprintf('%04d-%02d',(int)$r['Production_year'],(int)$r['Production_month']);
            if (!isset($selected[$k])) throw new RuntimeException('Buyer query returned a period outside the selection.');
            $name=(string)$r['BuyerName'];$id='buyer:'.$name;
            $v=[];foreach(self::ORDER as $field) $v[]=self::num($r,$field);
            if(!isset($buyers[$id])) $buyers[$id]=['name'=>$name,'months'=>[],'years'=>[],'total'=>self::ZERO];
            $year=$selected[$k][0];$b=&$buyers[$id];
            $b['months'][$k]=self::add($b['months'][$k]??self::ZERO,$v);
            $b['years'][$year]=self::add($b['years'][$year]??self::ZERO,$v);
            $b['total']=self::add($b['total'],$v);unset($b);
            $totals[$k]=self::add($totals[$k],$v);
        }
        uasort($buyers,static function($a,$b){return (($b['total'][0]+$b['total'][2])<=>($a['total'][0]+$a['total'][2])) ?: (strcasecmp($a['name'],$b['name']) ?: strcmp($a['name'],$b['name']));});
        $bad=[];foreach($totals as $k=>$v) foreach($v as $i=>$n) if(abs($n-$byMonth[$k][$i])>0.0001){$bad[]=$k;break;}
        if ($bad) throw new RuntimeException('Buyer Order totals do not match the main report for: '.implode(', ',$bad).'. Please regenerate and reconcile before exporting.');
        return [$selected,array_values($years),$byMonth,$buyers];
    }
    // Summary-only formatting, matching the original Excel report layout.
    private function summary(array $selected,array $byMonth,string $period,string $generated): array {
        $w=[8,8];for($j=0;$j<3;$j++) $w=array_merge($w,[11,16,11,16,17]);
        $s=self::sheet($w,7);$s['freezeCols']=2;$s['defaultHeight']=16;
        self::merge($s,1,1,1,17,'Guitar Order Balance Summary',19);$s['heights'][1]=26;
        self::merge($s,2,1,2,17,null,20);$s['heights'][2]=3;
        self::merge($s,3,1,3,17,null,21);$s['heights'][3]=3;
        self::merge($s,4,1,4,13,null,22);
        $date=DateTimeImmutable::createFromFormat('Y-m-d H:i:s',$generated);
        self::merge($s,4,14,4,17,$date?$date->format('m/d/Y'):$generated,23);$s['heights'][4]=18;
        self::merge($s,5,1,7,1,'Year',24);self::merge($s,5,2,7,2,'Month',25);
        foreach(self::SECTIONS as $j=>$section){
            $c=3+$j*5;
            self::merge($s,5,$c,5,$c+4,$section,24);
            self::cell($s,5,$c+4,null,25);
            self::merge($s,6,$c,6,$c+1,'AG',24);
            self::merge($s,6,$c+2,6,$c+3,'EG',24);
            self::cell($s,6,$c+4,'Total',25);
            foreach(['Qty','Amt','Qty','Amt','Total Amt'] as $i=>$label) self::cell($s,7,$c+$i,$label,$i===4?25:24);
        }
        $s['heights'][5]=19;$s['heights'][6]=18;$s['heights'][7]=18;
        $r=8;$byYear=[];foreach($selected as $k=>[$y,$m]) $byYear[$y][$k]=$m;
        foreach($byYear as $y=>$keys){
            $start=$r;$tot=array_fill(0,15,0.0);
            foreach($keys as $k=>$m){
                self::cell($s,$r,2,ucfirst(strtolower(self::MONTHS[$m-1])),26);
                $v=$byMonth[$k];
                for($j=0;$j<3;$j++){
                    $c=3+5*$j;$a=array_slice($v,$j*4,4);$a[]=$a[1]+$a[3];
                    foreach($a as $i=>$n){
                        $f=$i===4?self::col($c+1).$r.'+'.self::col($c+3).$r:null;
                        $style=$i===4?29:(in_array($i,[0,2],true)?27:28);
                        self::cell($s,$r,$c+$i,$n,$style,$f);$tot[$j*5+$i]+=$n;
                    }
                }
                ++$r;
            }
            $last=$r-1;$n=count($keys);
            self::merge($s,$start,1,$last,1,(int)$y,26);
            foreach(['Total','Avrg'] as $i=>$label){
                self::cell($s,$r,1,null,$i?35:30);self::cell($s,$r,2,$label,$i?35:30);
                foreach($tot as $j=>$value){
                    $col=self::col($j+3);$amt=in_array($j%5,[1,3,4],true);
                    $f=($i?'AVERAGE':'SUM').'('.$col.$start.':'.$col.$last.')';
                    $style=$i?($amt?($j%5===4?38:37):36):($amt?($j%5===4?33:32):31);
                    self::cell($s,$r,$j+3,$i?$value/$n:$value,$style,$f);
                }
                ++$r;
            }
        }
        return $s;
    }
    // Buyer worksheet follows the reference: title/date, annual table, then each buyer's monthly table.
    private static function buyerHeader(array &$s,int $r,array $years,string $label): void {
        self::merge($s,$r,1,$r+1,1,$label,42);
        $s['heights'][$r]=17;$s['heights'][$r+1]=32;
        foreach($years as $j=>$year){
            $c=2+6*$j;
            self::merge($s,$r,$c,$r,$c+5,(int)$year,41);
            self::cell($s,$r,$c+5,null,42);
            foreach(["AG\nQuantity","AG\nAmount","EG\nQuantity","EG\nAmount","Total Qty","Total\nAmount"] as $i=>$v)
                self::cell($s,$r+1,$c+$i,$v,$i===5?42:41);
        }
    }
    private static function six(array $v): array {return [$v[0],$v[1],$v[2],$v[3],$v[0]+$v[2],$v[1]+$v[3]];}
    private static function buyerCellStyle(int $i,bool $total): int {
        return ($total?49:44)+($i>=4?$i-2:$i%2);
    }
    private static function buyerValues(array &$s,int $r,int $c,array $v,bool $total=false,?array $formulas=null): void {
        foreach(self::six($v) as $i=>$n){
            $f=$formulas[$i]??null;
            if($f===null&&$i>=4) $f=self::col($c+$i-4).$r.'+'.self::col($c+$i-2).$r;
            // Inactive guitar-type pairs appear blank; combined totals remain numeric.
            $blank=$f===null&&$i<4&&$v[$i-$i%2]==0&&$v[$i-$i%2+1]==0;
            self::cell($s,$r,$c+$i,$blank?null:$n,self::buyerCellStyle($i,$total),$f);
        }
    }
    private function buyerSheet(array $selected,array $years,array $buyers,string $period,string $generated): array {
        // Keep only buyers and calendar-month rows with activity in the selected period.
        $activeMonths = [];
        foreach ($buyers as $id => $b) {
            foreach ($b['months'] as $k => $v) {
                if (!isset($selected[$k])) continue;
                foreach ($v as $value) {
                    if ($value != 0) {
                        $activeMonths[$id][$selected[$k][1]] = true;
                        break;
                    }
                }
            }
            if (empty($activeMonths[$id])) unset($buyers[$id]);
        }
        // Excel can scroll horizontally, so keep all selected years in the same table.
        $longest=0;foreach($buyers as $b) $longest=max($longest,strlen($b['name']));
        $nameWidth=max(20,min(34,$longest+2));
        $w=[$nameWidth];
        foreach($years as $year){
            $widths=[11,15,11,15,12,16];
            foreach($buyers as $b){
                $vectors=[$b['years'][$year]??self::ZERO];
                for($m=1;$m<=12;$m++) $vectors[]=$b['months'][sprintf('%04d-%02d',$year,$m)]??self::ZERO;
                foreach($vectors as $v) foreach(self::six($v) as $i=>$n){
                    $amount=$i%2===1;
                    $decimals=!$amount&&abs($n-round($n))>0.0000001?4:0;
                    $len=strlen(number_format(abs($n),$decimals,'.',','))+($amount?1:0)+($n<0?2:0);
                    $widths[$i]=max($widths[$i],$len+3);
                }
            }
            $w=array_merge($w,$widths);
        }
        $s=self::sheet($w,3);$s['defaultHeight']=16;$s['gridLines']=true;$s['zoom']=85;
        $lastCol=count($w);
        self::merge($s,1,1,1,$lastCol-1,'SUMMARY PER BUYER',40);
        $date=DateTimeImmutable::createFromFormat('Y-m-d H:i:s',$generated);
        self::cell($s,1,$lastCol,$date?$date->format('m/d/Y'):substr($generated,0,10),23);
        $s['heights'][1]=24;
        self::buyerHeader($s,2,$years,'BUYER');
        $r=4;$annual=[];$refs=[];
        foreach($buyers as $id=>$b){
            self::cell($s,$r,1,$b['name'],43);
            $s['heights'][$r]=max(16,ceil(strlen($b['name'])/max(1,$nameWidth-3))*13);
            $annual[$id]=$r++;
        }
        // Four empty rows separate the overview and the first buyer's detail.
        $r+=4;
        foreach($buyers as $id=>$b){
            self::merge($s,$r,1,$r,$lastCol,$b['name'],53);$s['heights'][$r]=18;
            ++$r;self::buyerHeader($s,$r,$years,'Month');$r+=2;$first=$r;
            foreach(self::MONTHS as $i=>$label){
                if (empty($activeMonths[$id][$i+1])) continue;
                self::cell($s,$r,1,$label,43);
                foreach($years as $j=>$y){
                    $k=sprintf('%04d-%02d',$y,$i+1);$c=2+$j*6;
                    if(isset($selected[$k])) self::buyerValues($s,$r,$c,$b['months'][$k]??self::ZERO);
                    else for($i2=0;$i2<6;$i2++) self::cell($s,$r,$c+$i2,null,self::buyerCellStyle($i2,false));
                }
                ++$r;
            }
            self::cell($s,$r,1,'TOTAL',48);
            foreach($years as $j=>$y){
                $c=2+$j*6;$fs=[];
                for($i=0;$i<6;$i++){$col=self::col($c+$i);$fs[]='SUM('.$col.$first.':'.$col.($r-1).')';}
                self::buyerValues($s,$r,$c,$b['years'][$y]??self::ZERO,true,$fs);
            }
            $refs[$id]=$r;$r+=4;
        }
        // Annual figures link to the individual buyer's TOTAL row, retaining editable Excel formulas.
        foreach($buyers as $id=>$b) foreach($years as $j=>$y){
            $c=2+$j*6;$fs=[];
            for($i=0;$i<6;$i++) $fs[]=self::col($c+$i).$refs[$id];
            self::buyerValues($s,$annual[$id],$c,$b['years'][$y]??self::ZERO,false,$fs);
        }
        return $s;
    }
    private static function worksheet(array $s): string {
        ksort($s['rows']);$cols='';foreach($s['widths'] as $i=>$w)$cols.='<col min="'.($i+1).'" max="'.($i+1).'" width="'.$w.'" customWidth="1"/>';
        $rows='';foreach($s['rows'] as $r=>$cells){ksort($cells);$h=$s['heights'][$r]??($s['defaultHeight']??17);$rows.='<row r="'.$r.'" ht="'.$h.'" customHeight="1">'.implode('',$cells).'</row>';}
        $merges='';foreach($s['merges'] as $ref)$merges.='<mergeCell ref="'.$ref.'"/>';
        $last=max(array_keys($s['rows']));$end=self::col(count($s['widths']));$freeze=$s['freeze'];$freezeCols=$s['freezeCols']??1;$topLeft=self::col($freezeCols+1).($freeze+1);$gridLines=!empty($s['gridLines'])?'1':'0';$zoom=isset($s['zoom'])?' zoomScale="'.(int)$s['zoom'].'"':'';
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetPr><pageSetUpPr fitToPage="1"/></sheetPr><dimension ref="A1:'.$end.$last.'"/><sheetViews><sheetView workbookViewId="0" showGridLines="'.$gridLines.'"'.$zoom.'><pane xSplit="'.$freezeCols.'" ySplit="'.$freeze.'" topLeftCell="'.$topLeft.'" activePane="bottomRight" state="frozen"/><selection pane="bottomRight" activeCell="'.$topLeft.'" sqref="'.$topLeft.'"/></sheetView></sheetViews><sheetFormatPr defaultRowHeight="17"/><cols>'.$cols.'</cols><sheetData>'.$rows.'</sheetData>'.($merges?'<mergeCells count="'.count($s['merges']).'">'.$merges.'</mergeCells>':'').'<pageMargins left="0.25" right="0.25" top="0.4" bottom="0.4" header="0.2" footer="0.2"/><pageSetup paperSize="9" orientation="landscape" fitToWidth="1" fitToHeight="0"/></worksheet>';
    }
    private static function styles(): string {
        $fmt=[164=>'#,##0;[Red](#,##0);"–"',165=>'"$"#,##0;[Red]("$"#,##0);"$–"',166=>'#,##0;[Red](#,##0);"–"',167=>'#,##0.####;[Red](#,##0.####);"–"',168=>'_(* #,##0_);[Red]_(* (#,##0);_(* "-"_);_(@_)',169=>'_("$"* #,##0_);[Red]_("$"* (#,##0);_("$"* "-"_);_(@_)',170=>'_(* #,##0.####_);[Red]_(* (#,##0.####);_(* "-"_);_(@_)'];
        $fmt += [171=>'#,##0;[Red](#,##0);""',172=>'_("$"* #,##0_);[Red]_("$"* (#,##0);""',173=>'#,##0;[Red](#,##0);0',174=>'_("$"* #,##0_);[Red]_("$"* (#,##0);_("$"* 0_);_(@_)',175=>'#,##0.####;[Red](#,##0.####);""',176=>'#,##0.####;[Red](#,##0.####);0'];
        $fmt += [177=>'mm/dd',178=>'00',179=>'_("$"* #,##0_);[Red]_("$"* (#,##0);_(* ""_);_(@_)',180=>'_("$"* #,##0_);[Red]_("$"* (#,##0);_("$"* 0_);_(@_)'];
        // Production Orders: two decimal USD and dash for zero; other sheets unchanged.
        $fmt += [181=>'#,##0;[Red](#,##0);"-"',182=>'_("$"* #,##0.00_);[Red]_("$"* (#,##0.00);_("$"* "-"_);_(@_)',183=>'#,##0.####;[Red](#,##0.####);"-"'];
        $nums='';foreach($fmt as $id=>$v)$nums.='<numFmt numFmtId="'.$id.'" formatCode="'.self::xml($v).'"/>';
        $fonts='<fonts count="7"><font><sz val="11"/><name val="Arial"/><color rgb="FF181C32"/></font><font><b/><sz val="11"/><name val="Arial"/><color rgb="FF181C32"/></font><font><b/><sz val="18"/><name val="Arial"/><color rgb="FF181C32"/></font><font><sz val="10"/><name val="Arial"/><color rgb="FF555555"/></font><font><sz val="10"/><name val="Arial"/><color rgb="FF000000"/></font><font><b/><sz val="10"/><name val="Arial"/><color rgb="FF000000"/></font><font><b/><sz val="18"/><name val="Arial"/><color rgb="FF000000"/></font></fonts>';
        $fills='<fills count="13"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill><fill><patternFill patternType="solid"><fgColor rgb="FFDCE6F1"/><bgColor indexed="64"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor rgb="FFE2E8F0"/><bgColor indexed="64"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor rgb="FFF4F4F4"/><bgColor indexed="64"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor rgb="FFFFFF00"/><bgColor indexed="64"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor rgb="FFF2F2F2"/><bgColor indexed="64"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor rgb="FFFF4655"/><bgColor indexed="64"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor rgb="FFA50034"/><bgColor indexed="64"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor rgb="FFDAE3F0"/><bgColor indexed="64"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor rgb="FFFFC000"/><bgColor indexed="64"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor rgb="FFCCFFCC"/><bgColor indexed="64"/></patternFill></fill><fill><patternFill patternType="solid"><fgColor rgb="FF8DB4E2"/><bgColor indexed="64"/></patternFill></fill></fills>';
        $border='<borders count="8"><border><left/><right/><top/><bottom/><diagonal/></border><border>';foreach(['left','right','top','bottom'] as $a)$border.='<'.$a.' style="thin"><color rgb="FFA1ADBB"/></'.$a.'>';$border.='<diagonal/></border>';
        // Summary borders: black grid, strong section edge, and double year separators.
        foreach([['thin','thin','thin','thin'],['thin','medium','thin','thin'],['thin','thin','double','thin'],['thin','medium','double','thin'],['thin','thin','thin','double'],['thin','medium','thin','double']] as $edges){
            $border.='<border>';foreach(['left','right','top','bottom'] as $i=>$edge)$border.='<'.$edge.' style="'.$edges[$i].'"><color rgb="FF000000"/></'.$edge.'>';$border.='<diagonal/></border>';
        }$border.='</borders>';
        // format, font, fill, border, horizontal alignment, wrap
        $spec=[[0,0,0,0,'left',0],[0,1,2,1,'center',1],[0,2,0,0,'left',0],[164,0,0,1,'right',0],[165,0,0,1,'right',0],[0,1,3,1,'left',1],[165,1,3,1,'right',0],[164,1,3,1,'right',0],[165,1,3,1,'right',0],[166,1,3,1,'right',0],[0,0,0,1,'left',1],[0,3,0,0,'left',1],[165,1,0,1,'right',0],[164,1,0,1,'right',0],[0,1,2,0,'left',1],[0,3,4,1,'center',0],[167,0,0,1,'right',0],[167,1,3,1,'right',0],[167,1,0,1,'right',0]];
        // Append Summary styles only; the original buyer-sheet styles retain their IDs.
        $spec=array_merge($spec,[
            [0,6,0,0,'left',0], // 19 Title
            [0,4,7,0,'left',0], [0,4,8,0,'left',0], // 20–21 red rules
            [0,4,0,0,'left',0], [0,5,0,0,'right',0], // 22–23 metadata
            [0,5,9,2,'center',0], [0,5,9,3,'center',0], // 24–25 headers
            [0,4,0,2,'center',0], // 26 year/month
            [168,4,6,2,'right',0], [169,4,0,2,'right',0], [169,5,5,3,'right',0], // 27–29 monthly
            [0,5,6,4,'center',0], [168,5,6,4,'right',0], [169,5,6,4,'right',0], [169,5,6,5,'right',0], // 30–33 total
            [170,4,6,2,'right',0], // 34 fractional quantity
            [0,5,6,6,'center',0], [168,5,6,6,'right',0], [169,5,6,6,'right',0], [169,5,6,7,'right',0], // 35–38 average
            [170,5,6,4,'right',0] // 39 fractional total quantity
        ]);
        // Buyer-only styles: reference blue headers, black borders, compact bold numbers.
        $spec=array_merge($spec,[
            [0,6,0,0,'left',0], // 40 title
            [0,5,9,2,'center',1], [0,5,9,3,'center',1], // 41-42 headers
            [0,5,0,2,'center',1], // 43 buyer/month label
            [171,5,0,2,'right',0], [172,5,0,2,'right',0], // 44-45 AG/EG
            [173,5,0,2,'right',0], [174,5,0,3,'right',0], // 46-47 combined
            [0,5,9,2,'center',0], // 48 TOTAL label
            [171,5,9,2,'right',0], [172,5,9,2,'right',0], // 49-50 TOTAL AG/EG
            [173,5,9,2,'right',0], [174,5,9,3,'right',0], // 51-52 TOTAL combined
            [0,5,0,0,'left',0], // 53 buyer detail title
            [175,5,0,2,'right',0], [176,5,0,2,'right',0], // 54-55 fractional qty
            [175,5,9,2,'right',0], [176,5,9,2,'right',0] // 56-57 fractional TOTAL qty
        ]);
        // Production Orders only: appended IDs 58–74; existing styles unchanged.
        $spec=array_merge($spec,[
            [177,4,0,2,'center',0], // 58 receiving date
            [178,4,0,2,'center',0], // 59 two-digit year/month
            [177,4,10,2,'center',0], // 60 expected shipment date
            [0,4,0,2,'left',1], // 61 customer, PO, editable text
            [181,4,0,2,'right',0], // 62 quantity
            [182,4,0,2,'right',0], // 63 PO amount
            [182,4,0,2,'right',0], // 64 total amount
            [181,5,12,2,'right',0], // 65 monthly quantity
            [182,5,12,2,'right',0], // 66 monthly amount
            [0,5,12,2,'left',0], // 67 monthly label
            [0,5,9,2,'center',1], // 68 wrapped header
            [183,4,0,2,'right',0], // 69 fractional quantity
            [183,5,12,2,'right',0], // 70 fractional monthly quantity
            [0,5,11,2,'left',0], // 71 buyer subtotal label
            [181,5,11,2,'right',0], // 72 buyer subtotal quantity
            [182,5,11,2,'right',0], // 73 buyer subtotal amount
            [183,5,11,2,'right',0] // 74 fractional buyer subtotal quantity
        ]);
        $xfs='';foreach($spec as [$n,$f,$fi,$b,$a,$wrap])$xfs.='<xf numFmtId="'.$n.'" fontId="'.$f.'" fillId="'.$fi.'" borderId="'.$b.'" xfId="0" applyNumberFormat="1" applyFont="1" applyFill="1" applyBorder="1" applyAlignment="1"><alignment horizontal="'.$a.'" vertical="center" wrapText="'.$wrap.'"/></xf>';
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><numFmts count="'.count($fmt).'">'.$nums.'</numFmts>'.$fonts.$fills.$border.'<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs><cellXfs count="'.count($spec).'">'.$xfs.'</cellXfs><cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles></styleSheet>';
    }
    // Q018 remains one row per SO and production period. Never merge by PO alone.
    private function productionModel(array $detailRows,array $selected,array $byMonth): array {
        $keys=['AG Order','AG PO Amt','EG Order','EG PO Amt','AG Balance','EG Balance'];
        $indices=[0,1,2,3,4,6]; // Q012 model order and balance quantities
        $totals=array_fill_keys(array_keys($selected),array_fill(0,6,0.0));
        $groups=[];$seen=[];
        foreach($detailRows as $d){
            $y=self::num($d,'Prod Year');$m=self::num($d,'Prod Month');
            if($y!==floor($y)||$m!==floor($m)) throw new RuntimeException('Invalid production order period.');
            $k=sprintf('%04d-%02d',(int)$y,(int)$m);
            if(!isset($selected[$k])) throw new RuntimeException('Production orders returned a period outside the selection.');
            $so=(string)($d['SO_Number']??'');
            if(trim($so)===''||isset($seen[$k][$so])) throw new RuntimeException('Missing or duplicate production sales order.');
            $seen[$k][$so]=true;
            foreach($keys as $i=>$field) $totals[$k][$i]+=self::num($d,$field);
            if(abs(self::num($d,'Total Amount')-self::num($d,'AG PO Amt')-self::num($d,'EG PO Amt'))>0.0001)
                throw new RuntimeException('Invalid production order total amount.');
            $buyer=(string)($d['BuyerName']??'');
            if(trim($buyer)==='') throw new RuntimeException('Missing production order BuyerName. Check GuitarProductionOrders.sql.');
            // Prefix prevents PHP from coercing numeric buyer labels to integer keys.
            $groups[(int)$y][(int)$m]['buyer:'.$buyer][]=$d;
        }
        $bad=[];
        foreach($totals as $k=>$v) foreach($indices as $i=>$j){
            if(abs($v[$i]-$byMonth[$k][$j])>0.0001){$bad[]=$k;break;}
        }
        if($bad) throw new RuntimeException('Production Order totals do not match the main report for: '.implode(', ',$bad).'. Please regenerate and reconcile before exporting.');
        return $groups;
    }
    // Excel's 1900 date system. Keep the full underlying date, display MM/DD.
    private static function productionDate($value): ?int {
        if($value instanceof DateTimeInterface) $value=$value->format('Y-m-d');
        if($value===null||trim((string)$value)==='') return null;
        if(!preg_match('~^(\d{4})[-/](\d{1,2})[-/](\d{1,2})(?:$|[ T])~',(string)$value,$p)
            ||!checkdate((int)$p[2],(int)$p[3],(int)$p[1])) throw new RuntimeException('Invalid production order date.');
        $utc=new DateTimeZone('UTC');
        $d=new DateTimeImmutable(sprintf('%04d-%02d-%02d',(int)$p[1],(int)$p[2],(int)$p[3]),$utc);
        if($d<new DateTimeImmutable('1900-01-01',$utc)) throw new RuntimeException('Production order date predates Excel date system.');
        $serial=(int)(new DateTimeImmutable('1899-12-31',$utc))->diff($d)->format('%a');
        return $serial+($d>=new DateTimeImmutable('1900-03-01',$utc)?1:0);
    }
    private function productionSheet(int $year,array $months,string $generated): array {
        $w=[12,7,12,8,42,25,12,12,12,12,7,17,17,18,38];
        $s=self::sheet($w,6);$s['freezeCols']=6;$s['defaultHeight']=16;$s['zoom']=85;$s['gridLines']=true;
        self::merge($s,1,1,1,15,$year.' Guitar Production Orders',19);$s['heights'][1]=26;
        self::merge($s,2,1,2,15,null,20);$s['heights'][2]=3;
        self::merge($s,3,1,3,15,null,21);$s['heights'][3]=3;
        self::merge($s,4,1,4,11,null,22);
        self::merge($s,4,12,4,15,'Update : '.substr($generated,0,10),23);$s['heights'][4]=18;
        foreach([1=>"Order Receiving\nDate",2=>"Prod\nYear",3=>"Expected\nShip. Date",4=>"Prod\nMonth",5=>'Customer',6=>'P/O',11=>'ETC',12=>"AG\nPO Amt",13=>"EG\nPO Amt",14=>"Total\nAmount",15=>'Remark'] as $c=>$label)
            self::merge($s,5,$c,6,$c,$label,68);
        self::merge($s,5,7,5,8,'AG',68);self::merge($s,5,9,5,10,'EG',68);
        foreach([7=>'Order',8=>'Balance',9=>'Order',10=>'Balance'] as $c=>$label) self::cell($s,6,$c,$label,68);
        $s['heights'][5]=25;$s['heights'][6]=22;
        $r=7;ksort($months);
        $fields=[7=>'AG Order',8=>'AG Balance',9=>'EG Order',10=>'EG Balance',12=>'AG PO Amt',13=>'EG PO Amt',14=>'Total Amount'];
        foreach($months as $m=>$buyers){
            $tot=array_fill_keys(array_keys($fields),0.0);
            $subtotalRows=[];
            // Preserve Q018's buyer and detail ordering (same as browser preview).
            foreach($buyers as $orders){
                $first=$r;$buyerTot=array_fill_keys(array_keys($fields),0.0);
                foreach($orders as $d){
                    self::cell($s,$r,1,self::productionDate($d['Order Receiving Date']??null),58);
                    self::cell($s,$r,2,$year%100,59);
                    self::cell($s,$r,3,self::productionDate($d['Expected Ship. Date']??null),60);
                    self::cell($s,$r,4,(int)$m,59);
                    $customer=(string)($d['Customer']??'');$po=(string)($d['P/O']??'');
                    self::cell($s,$r,5,$customer,61);self::cell($s,$r,6,$po,61);
                    $s['heights'][$r]=max(16,14*ceil(strlen($customer)/38),14*ceil(strlen($po)/21));
                    self::cell($s,$r,11,null,61);self::cell($s,$r,15,null,61);
                    foreach($fields as $c=>$field){
                        $n=$c===14?self::num($d,'AG PO Amt')+self::num($d,'EG PO Amt'):self::num($d,$field);
                        self::cell($s,$r,$c,$n,$c<11?62:($c===14?64:63),$c===14?'L'.$r.'+M'.$r:null);
                        $buyerTot[$c]+=$n;
                    }
                    ++$r;
                }
                self::merge($s,$r,1,$r,2,null,71);
                self::merge($s,$r,3,$r,6,'SUB-TOTAL',71);
                self::cell($s,$r,11,null,71);self::cell($s,$r,15,null,71);
                foreach($buyerTot as $c=>$n){
                    $col=self::col($c);
                    self::cell($s,$r,$c,$n,$c<11?72:73,'SUM('.$col.$first.':'.$col.($r-1).')');
                    $tot[$c]+=$n;
                }
                $subtotalRows[]=$r++;
            }
            self::merge($s,$r,1,$r,2,null,67);
            self::merge($s,$r,3,$r,6,'TOTAL '.self::MONTHS[$m-1],67);
            self::cell($s,$r,11,null,67);self::cell($s,$r,15,null,67);
            foreach($tot as $c=>$n){
                $col=self::col($c);
                // Only subtotal cells, never the detail rows again. Chunk SUM arguments
                // to respect Excel's 255-argument limit if buyer counts grow.
                $refs=array_map(static function($row) use ($col){return $col.$row;},$subtotalRows);
                $chunks=array_chunk($refs,255);
                $formula=implode('+',array_map(static function($chunk){return 'SUM('.implode(',',$chunk).')';},$chunks));
                self::cell($s,$r,$c,$n,$c<11?65:66,$formula);
            }
            ++$r;
        }
        return $s;
    }
    private function parts(array $months,array $buyerRows,array $filters,string $period,string $generated,array $productionOrderRows): array {
        [$selected,$years,$byMonth,$buyers]=$this->model($months,$buyerRows,$filters);
        $detail=$this->productionModel($productionOrderRows,$selected,$byMonth);
        $sheets=[];
        foreach($years as $y) $sheets[]=[(string)$y,$this->productionSheet($y,$detail[$y]??[],$generated)];
        $sheets[]=['Summary',$this->summary($selected,$byMonth,$period,$generated)];
        $sheets[]=['Order by CUSTOMER',$this->buyerSheet($selected,$years,$buyers,$period,$generated)];
        $parts=[];$types='';$names='';$rels='';
        foreach($sheets as $i=>[$name,$sheet]){
            $id=$i+1;$file='worksheets/sheet'.$id.'.xml';
            $parts['xl/'.$file]=self::worksheet($sheet);
            $types.='<Override PartName="/xl/'.$file.'" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
            $names.='<sheet name="'.self::xml($name).'" sheetId="'.$id.'" r:id="rId'.$id.'"/>';
            $rels.='<Relationship Id="rId'.$id.'" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="'.$file.'"/>';
        }
        $rels.='<Relationship Id="rId'.(count($sheets)+1).'" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>';
        return $parts + [
            '[Content_Types].xml'=>'<?xml version="1.0" encoding="UTF-8"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'.$types.'</Types>',
            '_rels/.rels'=>'<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>',
            'xl/workbook.xml'=>'<?xml version="1.0" encoding="UTF-8"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><bookViews><workbookView activeTab="0"/></bookViews><sheets>'.$names.'</sheets><calcPr calcId="0" fullCalcOnLoad="1"/></workbook>',
            'xl/_rels/workbook.xml.rels'=>'<?xml version="1.0" encoding="UTF-8"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'.$rels.'</Relationships>',
            'xl/styles.xml'=>self::styles()
        ];
    }
    public function xlsx(array $months,array $buyerRows,array $filters,string $period,string $generated,array $productionOrderRows): string {
        if(!class_exists('ZipArchive'))throw new RuntimeException('PHP ZipArchive is unavailable.');
        $parts=$this->parts($months,$buyerRows,$filters,$period,$generated,$productionOrderRows);
        $path=tempnam(sys_get_temp_dir(),'gbr_xlsx_');if($path===false)throw new RuntimeException('Cannot allocate export file.');
        $zip=new ZipArchive();$opened=false;
        try {if($zip->open($path,ZipArchive::CREATE|ZipArchive::OVERWRITE)!==true)throw new RuntimeException('Cannot create Excel file.');$opened=true;
            foreach($parts as $name=>$data)if(!$zip->addFromString($name,$data))throw new RuntimeException('Cannot write Excel file.');
            $closed=$zip->close();$opened=false;if(!$closed)throw new RuntimeException('Cannot finalize Excel file.');
            $bytes=file_get_contents($path);if($bytes===false||$bytes==='')throw new RuntimeException('Cannot read Excel file.');return $bytes;
        }finally{if($opened)$zip->close();if(is_file($path))unlink($path);}
    }
}
