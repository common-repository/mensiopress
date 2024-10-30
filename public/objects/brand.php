<?php
add_shortcode( 'mns_brands', 'mensiopress_get_brands' );
function mensiopress_get_brands($atts){
    if(empty($atts['titlesize']) && !empty($GLOBALS['MensioPressFontSize'])){
        $atts['titlesize']=$GLOBALS['MensioPressFontSize'];
    }
    if(empty($atts['fontsize']) && !empty($GLOBALS['MensioPressFontSize'])){
        $atts['fontsize']=$GLOBALS['MensioPressFontSize'];
    }
    if(empty($atts['ordering'])){
        $ordering="A-Z";
    }
    else{
        $ordering=$atts['ordering'];
    }
    $brands=new mnsFrontEndObject();
    $brands=$brands->mnsFrontEndBrands($ordering);
    $list=MensioList($brands,$atts,"Brands",false);
    if(!empty($_GET['action'])){
        $brands=new mnsFrontEndObject();
        $brands=$brands->mnsFrontEndBrands("A-Z");
        $list="<div ordering='A-Z'>".MensioList($brands,$atts,"Brands",false)."</div>";
        $brands=new mnsFrontEndObject();
        $brands=$brands->mnsFrontEndBrands("Z-A");
        $list="<div ordering='Z-A'>".MensioList($brands,$atts,"Brands",false)."</div>";
        $brands=new mnsFrontEndObject();
        $brands=$brands->mnsFrontEndBrands("MostProducts");
        $list="<div ordering='MostProducts'>".MensioList($brands,$atts,"Brands",false)."</div>";
        $brands=new mnsFrontEndObject();
        $brands=$brands->mnsFrontEndBrands("FewerProducts");
        $list="<div ordering='FewerProducts'>".MensioList($brands,$atts,"Brands",false)."</div>";
        $brands=new mnsFrontEndObject();
        $brands=$brands->mnsFrontEndBrands("Random");
        $list="<div ordering='Random'>".MensioList($brands,$atts,"Brands",false)."</div>";
    }
    return $list;
}
add_shortcode( 'mns_brand', 'mensiopress_get_brand' );
function mensiopress_get_brand($atts){
    if(empty($atts['titlesize']) && !empty($GLOBALS['MensioPressFontSize'])){
        $atts['titlesize']=$GLOBALS['MensioPressFontSize'];
    }
    if(empty($atts['fontsize']) && !empty($GLOBALS['MensioPressFontSize'])){
        $atts['fontsize']=$GLOBALS['MensioPressFontSize'];
    }
    global $post;
    $img=false;
    $description=false;
    $html=false;
    $title='Brand';
    if(!empty(get_post_meta($post->ID,"mensio_page_function")) &&
        empty($_GET['action'])){
        $func=get_post_meta($post->ID,"mensio_page_function");
        $func=$func[0];
        if($func!="brand_page"){
            return false;
        }
    }
    if(!empty(get_post_meta($post->ID,"mensio_category_title"))){
        $ttl=get_post_meta($post->ID,"mensio_category_title");
        $title=$ttl[0];
    }
    else{
        $title="Brand";
    }
    if((!empty($atts['brand_id'])) && (!empty($_REQUEST['brand'])) && empty($GLOBALS['UUID'])){
        $brand_id=$atts['brand_id'];
    }    
    elseif((empty($atts['brand_id'])) && (empty($_REQUEST['brand'])) && !empty($GLOBALS['UUID'])){
        $brand_id= MensioEncodeUUID($GLOBALS['UUID']);
    }
    elseif((empty($atts['brand_id'])) && (empty($_REQUEST['brand'])) && !empty($GLOBALS['brand_id'])){
        $brand_id= MensioEncodeUUID($GLOBALS['brand_id']);
    }
    elseif((empty($atts['brand_id'])) && (!empty($_REQUEST['brand']))){
        $brand_id=filter_var($_REQUEST['brand']);
    }
    elseif((!empty($atts['call'])) && (!empty($_REQUEST['brand'])) && ($atts['call']=='func')){
        $brand_id=filter_var($_REQUEST['brand']);
    }
    else{
        $brand_id=0;
        $brands=new mensio_products_brands();
        $brands=$brands->LoadProductBrandsDataSet();
        foreach($brands as $brand){
            $brand_id= MensioEncodeUUID($brand->uuid);
            break;
        }
        if(!$brands){
            $brand_id=0;
        }
    }
    $brand_id= MensioDecodeUUID($brand_id);
    $_SESSION['mnsCurrentBrand']=$brand_id;
    $products = new mnsFrontEndObject();
    $brand_prods=$products->mnsFrontEndBrandProducts($brand_id);
    $title=$brand_prods['current_brand_name'];
    $img=$brand_prods['current_brand_image'];
    if(empty($brand_prods['current_brand_imageThumb'])){
        $imgThumb=$brand_prods['current_brand_image'];
    }
    else{
        $imgThumb=$brand_prods['current_brand_imageThumb'];
    }
    $description=$brand_prods['current_brand_description'];
    if(empty($brand_prods['categories'])){
        $categories=array();
    }
    else{
        $categories=$brand_prods['categories'];
    }
    if(empty($atts['maxproducts'])){
        $break_on=20;
    }
    else{
        $break_on=$atts['maxproducts'];
    }
    $titleSize='h3';
    if(!empty($atts['titlesize'])){
        $titleSize= str_replace("-",".",$atts['titlesize']);
    }
    if(!$title){
        return false;
    }
    $fontSize=1;
    if(empty($atts['show-title']) || $atts['show-title']=='yes'){
        if(!empty($atts['title'])){
            $title=$atts['title'];
        }
        else{
            $title="";
        }
        if(!empty($atts['titlesize'])){
            $fontSize=str_replace("-",".",$atts['titlesize']);
        }
        else{
            $fontSize="1";
        }
        $title=$brand_prods['current_brand_name'];
        $html.="<div class='mns-brandTitle'><h2 style='font-size:".$fontSize."rem;' class='mensioObjectTitle'>".$title."</h2><hr class='titleLine' /></div>";
    }
    $BorderColor="";
    if(!empty($brand_prods['BorderColor']) && !empty($atts['imagebordercolors']) && $atts['imagebordercolors']=="yes"){
        $BorderColor="border-bottom:10px solid ".$brand_prods['BorderColor'];
    }
    $tt=time().rand(100,999);
    $html.='<div class="brand-cell">
            <img src="'.$imgThumb.'" alt="'.$title.'" align="left" class="mnsbrandLogo" data-zoom-image="'.$img.'" id="'.$tt.'" style="'.$BorderColor.'" />
                <p align="center" class="brand-link"><a href="'.$brand_prods['current_brand_webpage'].'" target="_blank"><i style="font-size:'.str_replace("-", ".", $fontSize).'rem;">'.$brand_prods['current_brand_webpage'].'</i></a></p>
            </div>
            ';
    $fontSize=1;
    if(!empty($atts['fontsize'])){
        $fontSize=$atts['fontsize'];
    }
    $html.='<div class="brand-cell" style="font-size:'.str_replace("-", ".", $fontSize).'rem;">'.$description."</div><br />";
    if(empty($_GET['page']) || (!empty($_GET['page']) && $_GET['page']!="mns-html-edit")){
        $CustomScript="
            jQuery('#".$tt."').elevateZoom({
                zoomType: 'inner',
                cursor: 'crosshair',
                zoomWindowFadeIn: 500,
                zoomWindowFadeOut: 750
            });";
        $tt=rand(1,1000);
        wp_enqueue_script("MensioPressPublicJS".$tt,plugin_dir_url(dirname(__FILE__))."js/jquery.elevatezoom.js");
        wp_add_inline_script( "MensioPressPublicJS".$tt,
               $CustomScript
               );
        echo wp_enqueue_scripts();
    }
    return $html;
}