<?php
add_shortcode( 'mns_product_offers', 'mensiopress_product_offers' );
function mensiopress_product_offers($atts){
    if(empty($atts['maxproducts'])){
        $atts['maxproducts']=6;
    }
    $html=false;
    $products=new mnsFrontEndObject();
    $products=$products->mnsFrontProductOffers();
    $html=MensioList($products,$atts,"Product Offers",false);
    return $html;
}