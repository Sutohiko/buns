<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Карта категорий");

function expandCatalog($section = false, $depth = 0) {
    $q = CIBlockSection::GetList([], ['IBLOCK_ID' => 17, 'SECTION_ID' => $section, 'ACTIVE' => 'Y','CNT_ACTIVE'=>'Y'], true, ['ID', 'NAME', 'SECTION_PAGE_URL', 'DEPTH_LEVEL']);
    $arr = [];
    while($row = $q->GetNext()) {
        if (!$row['ELEMENT_CNT']) continue;
//         echo '<pre>';
//         var_dump($row);
//         echo '</pre>';
        $arr[] = $row;
    }

    if ($depth) {
        echo '<ul class="map-list map-list_level-'.$depth.'">';
    } else {
        echo '<table class="map-columns"><tbody><td>';
    }
    foreach ($arr as $key => $row) {
        if (!$depth) echo '<ul class="map-list map-list_level-'.$depth.'">';
        echo '<li>';
        echo '<a href="'. $row['SECTION_PAGE_URL'] .'">'. $row['NAME'] .'</a>';
        expandCatalog($row['ID'], $row['DEPTH_LEVEL']);
        echo '</li>';
        if (!$depth) {
            echo '</ul>';
            if ($key && is_int(($key) / intval(count($arr) / 4))) echo '</td><td>';
        }
    }
    if ($depth) {
        echo '</ul>';
    } else {
        echo '</td></tbody></table>';
    }
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

    .right_block.wide_, .right_block.wide_N{
        float: unset;
    }
</style>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
