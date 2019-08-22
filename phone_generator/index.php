<?php

function phone_generator($code, $start, $end){// одна одинаковая первая цифра

    $i1 = round($start / 1000000); // первое число
    $i2 = intval(($start % 1000000)); // остальные

    $k1 = round($end / 1000000); // первое число
    $k2 = intval(($end % 1000000)); // остальные

    $list = array();



    return $list;
}

function phone_generator1($code, $start, $end){// две одинаковые первые цифры

    $i1 = intval($start / 100000); // первое число
    $i2 = intval(($start % 100000)); // остальные

    $k1 = intval($end / 1000000); // первое число
    $k2 = intval(($end % 100000)); // остальные

    $list = array();

    if($i1 == $k1){
        while($i2 <= $k2){
            $str = '';
            if($i2 < 1000000)$str = '7'.$code.$i1.$i2;
            if($i2 < 100000)$str = '7'.$code.$i1.''.$i2;
            if($i2 < 10000)$str = '7'.$code.$i1.'0'.$i2;
            if($i2 < 1000)$str = '7'.$code.$i1.'00'.$i2;
            if($i2 < 100)$str = '7'.$code.$i1.'000'.$i2;
            if($i2 < 10)$str = '7'.$code.$i1.'0000'.$i2;

            $list[] = array($str);

            $i2++;
        }
    }else{
        $i1 = intval($start / 1000000); // первое число
        $i2 = intval(($start % 1000000)); // остальные

        $k1 = intval($end / 1000000); // первое число
        $k2 = intval(($end % 1000000)); // остальные

        while($i2 <= $k2){
            $str = '';
            if($i2 < 1000000)$str = '7'.$code.$i1.$i2;
            if($i2 < 100000)$str = '7'.$code.$i1.'0'.$i2;
            if($i2 < 10000)$str = '7'.$code.$i1.'00'.$i2;
            if($i2 < 1000)$str = '7'.$code.$i1.'000'.$i2;
            if($i2 < 100)$str = '7'.$code.$i1.'0000'.$i2;
            if($i2 < 10)$str = '7'.$code.$i1.'00000'.$i2;

            $list[] = array($str);

            $i2++;
        }

    }

    return $list;
}

function WriteCSV($list, $filename){ // запись в файл

    $fp = fopen($filename, 'w');

    foreach ($list as $fields) {
        fputcsv($fp, $fields);
    }

    fclose($fp);

}
$code = 996;
$start = 1700000;
$end = 1899999;

$phones = phone_generator1($code , $start, $end);
//var_dump($phones);
$filename = '7'.$code.'-'.$start.'-'.$end.'.csv';
WriteCSV($phones, $filename);


