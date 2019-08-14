<?
/*
    header add:

    <script>
        window.dataLayer = window.dataLayer || [];
    </script>

    use in console for debug: 

    JSON.stringify(dataLayer)

    add confirm.php in sale.order.ajax:

*/
    $id = $arResult["ORDER"]["ID"];
    $name = "site.ru";
    $summ = $arResult["ORDER"]["PRICE"];
    $arBasketItems = array();
    $dbBasketItems = CSaleBasket::GetList( array(
        "NAME" => "ASC",
        "ID"   => "ASC",
    ), array(
        "LID"      => SITE_ID,
        "ORDER_ID" => $id,
    ), false, false, array( "ID", "PRODUCT_ID", "QUANTITY", "PRICE", "NAME" ) );

    $price = 0;

    while ( $arItems = $dbBasketItems->Fetch() ) {
        $price += $arItems['PRICE'] * $arItems['QUANTITY'];
        $arBasketItems[] = $arItems;
    }

    $delivery = $summ - $price;

?>
<script>
window.dataLayer.push({
   'ecommerce': {
      'purchase': {
         'actionField': {
            'id': '<?=$id?>', // Transaction ID. Required for purchases and refunds.
            'affiliation': 'SITE',
            'revenue': <?=$price + $delivery?>, // Total transaction value (incl. tax and shipping)
            'tax': 0,
            'shipping': <?=$delivery?>,
            'coupon': ''
         },

         'products': [
            <? $i = 1; foreach ($arBasketItems as $key => $value) {?>
            { // List of productFieldObjects.
            'name': "<?=$value['NAME']?>", // Name or ID is required.
            'id': "<?=$value['PRODUCT_ID']?>",
            'price': <?=number_format( $value['PRICE'], 0, "", "" );?>,
            'quantity': <?=number_format( $value['QUANTITY'], 0 )?>,
            'coupon': '' // Optional fields may be omitted or set to empty string.
         }
         <? $i ++;?>
         <? if ($i <= count( $arBasketItems )) { ?>,<?}?>
         <? } ?>
         ]
      }
   }
});
</script>