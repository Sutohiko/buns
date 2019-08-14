<?php
/**
 * @param string $file The file name to get.
 * @param bool $fields Use first row as fields for assoc.
 * @param null|int $length Same as fgetcsv.
 * @param string $delimiter Same as fgetcsv.
 * @param string $enclosure Same as fgetcsv.
 * @return array|bool
 */

function file_get_csv($file, $fields = true, $length = null, $delimiter = ',', $enclosure = '"') { //чтение csv файла из фрога/паука, скопипасчено с гита :)

    if (!file_exists($file)) {
        user_error(__FUNCTION__ . '() "' . $file . '" file does not exist', E_USER_WARNING);
        return false;
    }
    if (!is_readable($file)) {
        user_error(__FUNCTION__ . '() "' . $file . '" file is not readable', E_USER_WARNING);
        return false;
    }
    $array = array();
    $fh = fopen($file, 'r');
    while (($data = fgetcsv($fh, $length, $delimiter, $enclosure)) !== FALSE) {
        $array[] = $data;
    }
    fclose($fh);
    if ($fields) {
        if (!is_array($fields)) {
            $fields = array_shift($array);
        }
        foreach ($array as $k => $a) {
            $array[$k] = array_combine($fields, $array[$k]);
        }
    }
    return $array;
}

function grab_image($url,$target){ // скачивание изображений

    $Headers = @get_headers($url);
    if(preg_match("|200|", $Headers[0])) {
        $image = file_get_contents($url);
        $path = $target  . str_replace('/','$',str_replace("http://xn--80ajaajgbqmnbkgpb1b2c.xn--p1ai",'',$url)); // не очень хорошая вещь, но она задает имя файла, не хорошая из-за $

        create_dir($target); // запуск создания папок

        file_put_contents(dirname(__FILE__).'\\images'.$path, $image); // сохранение
    } else {
        echo "Not Found";
    }
}
function create_dir($path){ // создание папок

    if(strlen($path) > 0) {
        $path = substr($path, 0, -1);
    }

    $upPath = dirname(__FILE__).'\\images' . $path; // полный путь до начальной папки
    $tags = explode('\\' ,$upPath);            // эксплод полного пути
    $mkDir = "";

    foreach($tags as $folder) {
        $mkDir = $mkDir . $folder ."\\";   // инициализация пути для папки
        #echo '"'.$mkDir.'"<br/>';         // расспечатка процесса
        if(!is_dir($mkDir)) {             // проверка на папку
            mkdir($mkDir, 0777);            // создание папки
        }
    }
}

// запуск, доделать для двух версий windows и linux, добавить парсер картинок, а то паук подох
$array = file_get_csv('all_images.csv');
foreach ($array as $key => $mass){

    $curSite = 'http://xn--80ajaajgbqmnbkgpb1b2c.xn--p1ai'; // задаем имя сайта без слэша в конце

    $curTarget = str_replace('/','\\', $array[$key]['path']); // задаем путь папки картинки

    $curUrl = $curSite . $array[$key]['destination']; // сливаем домен и url картинки

    grab_image($curUrl, $curTarget); // запуск скачавания

    // тесты данных
    #var_dump($array[$key]['destination']);
    #var_dump($array[$key]['path']);
}


