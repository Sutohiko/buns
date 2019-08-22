<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Карта товаров");

function expandCatalog($section = false, $depth = 0, $products = 0) {
    $q = CIBlockSection::GetList([], ['IBLOCK_ID' => 17, 'SECTION_ID' => $section, 'ACTIVE' => 'Y','CNT_ACTIVE'=>'Y'], true, ['ID', 'NAME', 'SECTION_PAGE_URL', 'DEPTH_LEVEL']);
    $arr = [];
    while($row = $q->GetNext()) {
        if (!$row['ELEMENT_CNT']) continue;
//         echo '<pre>';
//         var_dump($row);
//         echo '</pre>';
        $arr[] = $row;
    }

    foreach ($arr as $key => $row) {

        if($products != 0){
            $prow = expandProduct($row['ID']);
            foreach ($prow as $key => $product) {
                if (!$depth) echo '<ul class="map-list map-list_level-' . $depth . '">';
                echo '<li>';
                echo '<a href="' . $product['DETAIL_PAGE_URL'] . '">' . $product['NAME'] . '</a>';

                echo '</li>';
                if (!$depth) {
                    echo '</ul>';
                    if ($key && is_int(($key) / intval(count($prow) / 4))) echo '</td><td>';
                }
            }
        }

        expandCatalog($row['ID'], $row['DEPTH_LEVEL'], 1);
    }
}

function expandProduct($section) {
    $q = CIBlockElement::GetList([], ['IBLOCK_ID' => 17,'SECTION_ID' => $section, 'ACTIVE' => 'Y'], false, false, ['ID', 'NAME', 'DETAIL_PAGE_URL']);
    $arr = [];

    while($row = $q->GetNext()) {
        $arr[] = $row;
    }

    return $arr;
}

$html = "";
$cntIBLOCK_List = 17;
$cache = new CPHPCache();
$cache_time = 3600*24;
$cache_id = 'arIBlockListID'.$cntIBLOCK_List;
$cache_path = '/arIBlockListID/';
if ($cache_time > 0 && $cache->InitCache($cache_time, $cache_id, $cache_path)) {
    $res = $cache->GetVars();
    if ($res["HTML"])
        $html = $res["HTML"];
}
if (!$html)
{
    ob_start();
    expandCatalog();
    $html = ob_get_contents();
    ob_end_clean();
    
    if ($cache_time > 0)
    {
        $cache->StartDataCache($cache_time, $cache_id, $cache_path);
        $cache->EndDataCache(array("HTML"=>$html));
    }
}

echo $html;

?>

<style>
    .map-columns {
        background: transparent;
        border: none;
        margin: 0
    }

    .map-columns td {
        padding: 0;
        vertical-align: top
    }

    @media only screen and (max-width: 40em) {
        .map-columns,.map-columns td {
            display:block
        }
    }

    .map-list_level-0 {
        margin: 0 30px 30px 0
    }

    .map-list_level-0>li>a {
        font-weight: 700
    }
</style>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
