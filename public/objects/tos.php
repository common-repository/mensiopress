<?php
add_shortcode( 'mns_tos', 'mensiopress_terms_of_service' );
function mensiopress_terms_of_service($atts){
    if(empty($atts['titlesize']) && !empty($GLOBALS['MensioPressFontSize'])){
        $atts['titlesize']=$GLOBALS['MensioPressFontSize'];
    }
    if(empty($atts['fontsize']) && !empty($GLOBALS['MensioPressFontSize'])){
        $atts['fontsize']=$GLOBALS['MensioPressFontSize'];
    }
    $tos=new mnsFrontEndObject();
    $tos=$tos->mnsFrontEndTOS();
    if(empty($atts['title'])){
        $title="Terms of Service";
    }
    else{
        $title=$atts['title'];
    }
    if(!empty($atts['titlesize'])){
        $titleSize=str_replace("-",".",$atts['titlesize']);
    }
    else{
        $titleSize="1";
    }
    if(!empty($atts['fontsize'])){
        $textSize=str_replace("-",".",$atts['fontsize']);
    }
    else{
        $textSize="1";
    }
    $html="<h2 style='font-size:".$titleSize."rem;' class='mensioObjectTitle'>".$title."</h2>
            <hr class='titleLine' /><div style='font-size:".$textSize."rem;'>".$tos."</div>";
    return $html;
}