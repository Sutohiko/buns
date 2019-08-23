$seoResult = CIBlockSection::GetList([], ["ID" => $arResult["ID"], "IBLOCK_ID" => 17], false, ['UF_SECTION_METATITLE', 'UF_SECTION_METADESCR']);

if ($seoMeta = $seoResult -> GetNext()) {
    $APPLICATION->SetPageProperty('title', $seoMeta['UF_SECTION_METATITLE']);
    $APPLICATION->SetPageProperty('description', $seoMeta['UF_SECTION_METADESCR']);
}