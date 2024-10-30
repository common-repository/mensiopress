<?php
add_shortcode( 'mns_new_products', 'mensiopress_get_new_products' );
function mensiopress_get_new_products($atts){
    if(empty($atts['maxproducts'])){
        $atts['maxproducts']=9;
    }
    $top=new mnsFrontEndObject();
    $prods=$top->mnsFrontEndNewProducts($atts['maxproducts']);
    $html=MensioList($prods,$atts,"New Products",false);
    return $html;
}