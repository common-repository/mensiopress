<?php
if(!function_exists("MensioList")){
    function MensioList($array,$atts,$defaultTitle,$relation){
        $width=false;
        $height=false;
        if(empty($atts['titlesize']) && !empty($GLOBALS['MensioPressFontSize'])){
            $atts['titlesize']=$GLOBALS['MensioPressFontSize'];
        }
        if(empty($atts['fontsize']) && !empty($GLOBALS['MensioPressFontSize'])){
            $atts['fontsize']=$GLOBALS['MensioPressFontSize'];
        }
    $html='<div class="mns-list">';
            if(!empty($atts['cols'])){
                $cols=$atts['cols'];
            }
            else{
                $cols="3";
            }
            $DiscountStyle=' style="';
            if(!empty($atts['discount-background-color'])){
                $DiscountStyle.='background-color:'.$atts['discount-background-color'].";";
            }
            else{
                $DiscountStyle.='background-color:red;';
            }
            if(!empty($atts['discounttextcolor'])){
                $DiscountStyle.='color:'.$atts['discounttextcolor'].";";
            }
            else{
                $DiscountStyle.='color:white;';
            }
            if(!empty($atts['discountbold'])){
                $DiscountStyle.='font-weight:bold;';
            }
            $DiscountStyle.='"';
                if((!empty($atts)) && (!empty($atts['title'])) && ($atts['title']==true)){
                    $title=$atts['title'];
                }
                else{
                    $title=$defaultTitle;
                }
                if(!empty($GLOBALS['UUID'])){
                    $getCategories=new mnsFrontEndObject();
                    $CurrentCategories=$getCategories->mnsGetProductCategories($GLOBALS['UUID']);
                    if($CurrentCategories){
                        $getBrand=new mnsFrontEndObject();
                        $Brand=$getBrand->mnsFrontEndProduct($GLOBALS['UUID']);
                        $CurrentBrand=$Brand['brand'];
                    }
                }
                if(!empty($atts['ofbrand']) && $atts['ofbrand']=="Current"){
                    $atts['ofbrand']= MensioEncodeUUID($_SESSION['mnsCurrentBrand']);
                }
                $html.='';
                    $titleStyle='';
                    if(!empty($atts['titlecolor'])){
                        $titleStyle.="color:".$atts['titlecolor'];
                    }
                    $classes='';
                    if(empty($relation)){
                        if(!empty($atts['title-'.$_SESSION['MensioThemeLangShortcode']])){
                            $title=$atts['title-'.$_SESSION['MensioThemeLangShortcode']];
                        }
                        else{
                            $title=$defaultTitle;
                        }
                        if(!empty($atts['titlesize'])){
                            $fontSize=str_replace("-",".",$atts['titlesize']);
                        }
                        else{
                            $fontSize="1";
                        }
                        if($defaultTitle!="SubCategories" && !empty($defaultTitle)){
                            $html.="<h2 style='font-size:".str_replace("-",".",$fontSize)."rem;line-height:".str_replace("-",".",$fontSize)."rem;".$titleStyle."' class='mensioObjectTitle'>".$title."</h2><Hr class='titleLine' />";
                        }
                    }
                if(!empty($atts['fontsize'])){
                    $textSize=str_replace("-",".",$atts['fontsize']);
                }
                else{
                    $textSize="1";
                }
                if(!empty($atts['add-to-cart-text-size'])){
                    $addToCartTextSize=$atts['add-to-cart-text-size'];
                }
                else{
                    $addToCartTextSize=$textSize;
                }
                $MaxLines=2;
                if(!empty($atts['product-names-lines'])){
                    $MaxLines=$atts['product-names-lines'];
                }
                $addBY=0.5;
                $lineHeight=($textSize+$addBY);
                $NameStyle="
                    height: ".(($textSize+$addBY)*$MaxLines)."rem;
                    font-size: ".$textSize."rem;
                    line-height: ".$textSize."rem;
                    overflow:hidden;
                    -webkit-line-clamp: ".$MaxLines.";
                    -webkit-box-orient: vertical;
                    overflow: hidden;
                    max-height: ".(($textSize*$MaxLines)+0)."rem;
                    ";
                $tt="mns-".time()."-".rand(1000,9999);
                if(empty($atts['display'])){$simpleDis="block";$carouselDis="none";}
                if(!empty($atts['display']) && $atts['display']=="carousel"){$simpleDis="none";$carouselDis="block";}
                    $o=1;
                        if(empty($array)){
                            return false;
                        }
                    $hasReviews=false;
                    $ListhasDiscount=false;
                    $FilteredArray=$array;
                        $NotHere=0;
                        $FilteredArray=array();
                        $i=0;
$maxWidth=false;
$maxHeight=false;
if(!empty($atts['ofbrand']) && $atts['ofbrand']!="Any" && $atts['ofbrand']!="Current"){
    $atts['ofbrand']=MensioDecodeUUID($atts['ofbrand']);
}
                        foreach($array as $ar){
                                if(
                                        (!empty($atts['ofbrand']) && !empty($ar['brand']) && $atts['ofbrand']!=$ar['brand']) || 
                                        (!empty($ar['brand']) && !empty($atts['ofbrand']) && $atts['ofbrand']=='Current' && !empty($GLOBALS['UUID']) && $GLOBALS['UUID'] != $ar['brand']) ||
                                        (!empty($ar['brand']) && !empty($_GET['brand']) && $_GET['brand']!=$ar['brand'])
                                    ){
                                    $NotHere=1;
                                }
                            if(!empty($atts['ofcategory'])
                                    && $atts['ofcategory']=='Current'
                                        && !empty($ar['categories'])
                                            && !empty($GLOBALS['UUID'])
                                                && !in_array($GLOBALS['UUID'], $ar['categories'])){
                                $NotHere=1;
                            }
                            elseif(!empty($atts['ofcategory'])
                                    && $atts['ofcategory']!='Current'
                                        && !empty($ar['categories'])
                                            && !in_array($atts['ofcategory'], $ar['categories'])){
                                $NotHere=1;
                            }
                            if(!empty($atts['ofbrand']) && $atts['ofbrand']=='Any'){
                                $NotHere=0;
                            }
                            elseif(!empty($atts['ofbrand']) && !empty($_SESSION['mnsCurrentBrand']) && $ar['brand']!=$_SESSION['mnsCurrentBrand']){
                                $NotHere=1;
                            }
                            elseif(!empty($atts['ofbrand']) && !empty($_SESSION['mnsCurrentBrand']) && $ar['brand']==$_SESSION['mnsCurrentBrand']){
                                $NotHere=0;
                            }
                            if($NotHere==0){
                                $FilteredArray[$i]=$ar;
                                if(!empty($ar['reviews'])){
                                    $hasReviews="yes";
                                }
                                if(!empty($ar['discount'])){
                                    $ListhasDiscount="yes";
                                }
                                if(!empty($ar['image'])){
                                    list($width, $height, $type, $attr) = getimagesize($ar['image']);
                                    if($height<$maxHeight || $maxHeight==false){
                                        $maxHeight=$height;
                                    }
                                    if($width<$maxWidth || $maxWidth==false){
                                        $maxWidth=$width;
                                    }
                                }
                                $i++;
                            }
                            $NotHere=0;
                        }
                    if(count($FilteredArray)==0){
                        return false;
                    }
                        for($o=$i;$o<=($cols-1);$o++){
                            $FilteredArray[$o]['id']='empty-empty-empty-empty-empty';
                        }
                    if(!empty($Type['icon'])){
                        $Type=MensioGetActiveReviewType();
                        $ratingIcon= site_url()."/".$Type['icon'];
                    }
if(empty($atts['display']) || (!empty($atts['display']) && $atts['display']=="simple")){
                    $html.= '<div class="SimpleDisplay '.$tt.'" style="display:'.$simpleDis.';font-size:'.$textSize.'rem;line-height:'.($textSize+0.5).'rem;width: 99%;margin:0 auto;">';
                    $col=1;
                    $k=1;
                    if($cols>count($FilteredArray) && count($FilteredArray)>0){
                        for($t=0;$t<=($cols-count($FilteredArray));$t++){
                        }
                    }
                    $style=false;
                    foreach($FilteredArray as $ar){
                        if(empty($ar['id'])){
                            continue;
                        }
                        if(empty($ar['link'])){
                            $ar['link']=false;
                        }
                        if(empty($ar['image'])){
                            $ar['image']=plugin_dir_url(__FILE__)."/../../icons/mensiopress-noimage.png?ver=1.0";
                        }
                        $empty=false;
                        if($ar['id']=='empty-empty-empty-empty-empty'){
                            $empty=" invisible";
                        }
                        if(!empty($ar['price'])){
                        }
                        if(!empty($cols) && $col==1){
                            $html.="<div class='mns-list-row'>";
                        }
                        if(!empty($atts['box-border'])){
                            $style.="border-width:".$atts['box-border']."px;";
                            $style.="border-style:solid;padding: 12px 10px;";
                            if(!empty($atts['box-border-color'])){
                                $style.="border-color:".$atts['box-border-color'].";";
                            }
                        }
                        $html.='<div class="mns-list-item-new'.$empty.'" style="'.$style.'">';
                        $style=false;
                        $html.="<div class='mnsListRow'>";
                                $html.='<div class="mns-image">';
                                    if(!empty($ar['discount'])){
                                        $html.= '<div class="discount" style="';
                                        if(!empty($atts['discount-background-color'])){
                                            $html.='background-color:'.$atts['discount-background-color'].";";
                                        }
                                        if(!empty($atts['discount-text-color'])){
                                            $html.='color:'.$atts['discount-text-color'].";";
                                        }
                                        $html.='"><span>-'.$ar['discount'].'%</span></div>';
                                    }
                                    $borderColor=false;
                                    if(!empty($atts['imagebordercolors']) && $atts['imagebordercolors']=="yes" && !empty($ar['BorderColor'])){
                                        $borderColor="border-bottom:5px solid ".$ar['BorderColor'].";";
                                    }
                                    if(empty($ar['name'])){
                                        $ar['name']=false;
                                    }
                                    if(!empty($ar['image'])){
                                        $html.= "<a href='".$ar['link']."'>";
                                        if(!empty($ar['image-thumb'])){
                                            $ar['image']=$ar['image-thumb'];
                                        }
                                            $html.='<img src="'.$ar['image'].'?ver=1.0" style="width:100%;height:auto;'.$borderColor.'" alt="'.$ar['name'].'" title="'.$ar['name'].'">';
                                        $html.="</a>";
                                    }
                                if(!empty($hasReviews=="yes")){
                                    if(empty($ar['reviews']) && !empty($hasReviews=="yes")){
                                        $html.="<div class='MensioProductReviews-new emptyReviews'>";
                                        $Type=MensioGetActiveReviewType();
                                        $ReviewsIcon=$Type['icon'];
                                        $html.='<img src="/'.$ReviewsIcon.'?ver=1.0" class="good" alt="'.$ar['name'].'" title="'.$ar['name'].'" />';
                                        $html.="</div>";
                                    }
                                    elseif(!empty($ar['reviews'])){
                                        $html.="<div class='MensioProductReviews-new'>";
                                            $reviews=round($ar['reviews']);
                                            $Type=MensioGetActiveReviewType();
                                            $maxStars=0;
                                            if(!empty($Type['max'])){
                                                $maxStars=$Type['max'];
                                            }
                                            $minStars=0;
                                            if(!empty($Type['min'])){
                                                $minStars=$Type['min'];
                                            }
                                            $step=0;
                                            if(!empty($Type['step'])){
                                                $step=$Type['step'];
                                            }
                                            $icon=0;
                                            if(!empty($Type['icon'])){
                                                $icon=$Type['icon'];
                                            }
                                            for($i=$minStars;$i<=$maxStars;$i++){
                                                if($i<=$reviews){
                                                    $html.="<img src='".home_url()."/".$icon."?ver=1.0' class='good' alt='".$ar['name']." | Reviews:".$ar['reviews']."' title='".$ar['name']." | Reviews:".$ar['reviews']."' />";
                                                }
                                                else{
                                                    $html.="<img src='".home_url()."/".$icon."?ver=1.0' class='not-good' alt='".$ar['name']." | Reviews:".$ar['reviews']."' title='".$ar['name']." | Reviews:".$ar['reviews']."' />";
                                                }
                                                if($i>=5){
                                                    break;
                                                }
                                            }
                                        $html.="</div>";
                                    list($width,$height)=getimagesize(home_url()."/".$icon);
                                    }
                                }
                                $html.='</div>';
                            if(!empty($ar['name'])){
                                $html.="<div>";
                                    if(!empty($ar['link'])){
                                        $html.="<a href='".$ar['link']."'>";
                                    }
                                    $html.='<h3 class="mns-ListName">';
                                        $html.=$ar['name'];
                                    $html.="</h3>";
                                    if(!empty($ar['link'])){
                                        $html.="</a>";
                                    }
                                    if(!empty($ar['description'])){
                                        $html.='<div class="mns-Description" style="font-size:'.($textSize-0.1).'rem;line-height:'.($textSize+0.3).'rem;">';
                                        $html.=$ar['description'];
                                        $html.="</div>";
                                                $html.="<div>";
                                                    $html.="<div></div>";
                                                    if(!empty($ar['price'])){
                                                            $html.='
                                                            <div class="ListButtons">';
                                                                if(!empty($ar['availability'])){
                                                                    $color=false;
                                                                    if(!empty($ar['availability-color'])){
                                                                        $color=" style='color:".$ar['availability-color'].";'";
                                                                    }
                                                                    $html.="<div class='availability'".$color.">";
                                                                    if(!empty($ar['availability-icon'])){
                                                                        $html.="<img src='".$ar['availability-icon']."' title='".$ar['availability']."' alt='".$ar['availability']."' />";
                                                                    }
                                                                    $html.=$ar['availability'];
                                                                    $html.="</div>";
                                                                }
                                                                if((empty($atts['hide-barcode']) || (!empty($atts['hide-barcode']) && $atts['hide-barcode']!="yes")) ||
                                                                   (empty($atts['hide-sku']) || (!empty($atts['hide-sku']) && $atts['hide-sku']!="yes")) ){
                                                                    if(!empty($ar['sku']) || !empty($ar['barcodes'])){
                                                                        $html.='<div class="ListButton Codes">
                                                                        <i class="fa fa-barcode" aria-hidden="true"></i>
                                                                        <div class="BarCodes">';
                                                                        if(!empty($ar['sku'])){
                                                                            $html.="SKU:&nbsp;".str_replace(" ","&nbsp;",$ar['sku'])."<br/>";
                                                                        }
                                                                    }
                                                                    $html.='</div></div>';
                                                                }
                                                                $html.='<br  />';
                                                                $html.='<div class="ListButton add-to-cart" id="product-'.MensioEncodeUUID($ar['id']).'">
                                                                    <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                                                                        <div>'.str_replace(" ","&nbsp;",$GLOBALS['MensioAddToCartText']).'</div>
                                                                    </div>';
                                                            $html.='
                                                            </div>';
                                                    }
                                                $html.='</div>';
                                    }
                                    $html.="<br class='mensioGap' />";
                                $html.="</div>";
                            }
                            if(!empty($ar['price']) && !empty($atts['showprices']) && $atts['showprices']=="yes"){
                                    $html.='<div class="mns-price">';
                                        $firstPrice=false;
                                        $finalPrice=false;
                                                    if(!empty($ar['price'])){
                                                        $html.='<div class="MensioListBrand">';
                                                            $brand_id=$ar['brand'];
                                                            $link=new mnsGetFrontEndLink();
                                                            $products = new mnsFrontEndObject();
                                                            $brand_prods=$products->mnsFrontEndBrandProducts($brand_id);
                                                            $BrandLink=$link->BrandLink($brand_id);
                                                            $html.='<a href="'.$BrandLink.'"><img src="'.$brand_prods['current_brand_imageThumb'].'" title="'.$brand_prods['current_brand_name'].'" alt="'.$brand_prods['current_brand_name'].'" /></a>';
                                                        $html.='</div>';
                                                    }
                                        if(!empty($atts['show-price-with-tax']) && $atts['show-price-with-tax']=="yes"){
                                            $finalPrice=$ar['price']+($ar['price']*($ar['tax']/100));
                                            $finalPrice=$finalPrice-($finalPrice*($ar['discount']/100));
                                        }
                                        else{
                                            $finalPrice=$ar['price']-($ar['price']*($ar['discount']/100));
                                        }
                                        $priceColor="inherit";
                                        if(!empty($atts['pricecolor'])){
                                            $priceColor=$atts['pricecolor'];
                                        }
                                        $html.='<div>';
                                            $html.='<span class="mensioPrice" style="color:'.$priceColor.';font-size:'.($textSize*1.5).'rem;line-height:'.(($textSize*1.5)+0.5).'rem;">'.number_format($finalPrice,2).'</span>';
                                        $html.='</div>';
                                        if(!empty($ar['discount'])){
                                            if(!empty($atts['show-price-with-tax']) && $atts['show-price-with-tax']=="yes"){
                                                $firstPrice=$ar['price']+($ar['price']*($ar['tax']/100));
                                            }
                                            elseif(!empty($atts['show-price-with-tax']) && $atts['show-price-with-tax']=="yes"){
                                                $firstPrice=$ar['price']-($ar['price']*($ar['discount']/100));
                                            }
                                            else{
                                                $firstPrice=$ar['price'];
                                            }
                                            $html.='<div class="mensioFirstPrice">';
                                                $html.='<span class="mensioPrice" style="font-size:'.($textSize).'rem;line-height:'.(($textSize)+0.5).'rem;">'.number_format($firstPrice,2).'</span>';
                                            $html.='</div>';
                                        }
                                        elseif($ListhasDiscount="yes"){
                                            $html.='<div class="mensioFirstPrice emptyPrice">';
                                                $html.='<span class="mensioPrice" style="font-size:'.($textSize).'rem;line-height:'.(($textSize)+0.5).'rem;">0</span>';
                                            $html.='</div>';
                                        }
                                    $html.='</div>';
                            }
                        $html.="</div>";
                        $html.="</div>";
                        if((!empty($cols) && $col==$cols) || ($k==count($FilteredArray))){
                            $html.="</div>";
                            $col=0;
                        }
                        if(!empty($atts['maxproducts']) && $k==$atts['maxproducts']){
                            break;
                        }
                        elseif(empty($atts['maxproducts']) && $k==6){
                            break;
                        }
                        $k++;
                        $col++;
                    }
                            $CustomStyle=false;
                            $CustomStyle.="
                                .mns-block:not(.show-prods-in-rows) .mns-list .".$tt." .mns-list-row .mns-list-item-new .mns-ListName{
                                    height:".((($textSize+0.4)*$MaxLines)+0.4)."rem !important;
                                    line-height:".($textSize+0.4)."rem !important;
                                }
                                ";
                                if(!empty($atts['box-border-hover'])){
                                    $CustomStyle.="
                                    .".$tt." .mns-list-item-new:hover{
                                        border-width:".$atts['box-border-hover']."px;
                                        ";
                                        if(empty($atts['box-border-hover-color'])){
                                        $CustomStyle.="
                                        border-color: ".$atts['box-border-color'].";";
                                        }
                                        else{
                                        $CustomStyle.="
                                        border-color: ".$atts['box-border-hover-color'].";";
                                        }
                                    $CustomStyle.="
                                        border-style:solid;
                                    }";
                                }
                                if(!empty($atts['box-border-image'])){
                                    $CustomStyle.="
                                    .".$tt." .mns-list-item-new .mns-image a img{
                                        border-width:".$atts['box-border-image']."px;
                                        ";
                                        if(empty($atts['box-border-image-color'])){
                                        $CustomStyle.="
                                        border-color: ".$atts['box-border-image-color'].";";
                                        }
                                        else{
                                        $CustomStyle.="
                                        border-color: #000 !important;";
                                        }
                                    $CustomStyle.="
                                        border-style:solid;
                                    }";
                                }
                                if(!empty($atts['box-border-image'])){
                                    $CustomStyle.="
                                    .".$tt." .mns-list-item-new .mns-image a img{
                                        border-width:".$atts['box-border-image']."px;
                                        ";
                                        if(!empty($atts['box-border-image-color'])){
                                        $CustomStyle.="
                                        border-color: ".$atts['box-border-image-color']." !important;";
                                        }
                                        else{
                                        $CustomStyle.="
                                        border-color: #000 !important;";
                                        }
                                    $CustomStyle.="
                                        border-style:solid;
                                    }";
                                    if(!empty($atts['box-border-image-hover-color'])){
                                        $CustomStyle.="
                                        .".$tt." .mns-list-item-new:hover .mns-image a img{
                                        border-color: ".$atts['box-border-image-hover-color']." !important;
                                        }";
                                    }
                                }
                                if(!empty($atts['active-link-color'])){
                                    $CustomStyle.="
                                    .".$tt." .mns-list-item-new a:hover{
                                            color:".$atts['active-link-color']." !important;
                                        }";
                                }
                                if(!empty($atts['box-border-image-hover'])){
                                    $CustomStyle.="
                                    .".$tt." .mns-list-item-new .mns-image a img:hover{
                                        border-width:".$atts['box-border-image-hover']."px !important;
                                        ";
                                        if(!empty($atts['box-border-image-hover-color'])){
                                        $CustomStyle.="
                                        border-color: ".$atts['box-border-image-hover-color']." !important;";
                                        }
                                        elseif(!empty($atts['box-border-image-color'])){
                                        $CustomStyle.="
                                        border-color: ".$atts['box-border-image-color']." !important;";
                                        }
                                        else{
                                        $CustomStyle.="
                                        border-color: #000 !important;";
                                        }
                                    $CustomStyle.="
                                        border-style:solid;
                                    }";
                                }
                                if(!empty($atts['box-border-hover'])){
                                    $CustomStyle.="
                                    .".$tt." .mns-list-item-new:hover{
                                        border-width:".$atts['box-border-hover']."px !important;
                                        ";
                                        if(empty($atts['box-border-hover-color'])){
                                        $CustomStyle.="
                                        border-color: ".$atts['box-border-color']." !important;";
                                        }
                                        else{
                                        $CustomStyle.="
                                        border-color: ".$atts['box-border-hover-color']." !important;";
                                        }
                                    $CustomStyle.="
                                        border-style:solid;
                                    }";
                                }
                                if(!empty($atts['pricecolor'])){
                                    $CustomStyle.='
                                        .'.$tt.' .mns-price{
                                            color:'.$atts['pricecolor'].';
                                        }';
                                }
                                if(!empty($atts['textcolor'])){
                                $CustomStyle.= "
                                        .mns-block .SimpleDisplay.".$tt." .mensioFirstPrice:before{
                                            border:1px solid ".$atts['textcolor'].";
                                        } 
                                        ";
                                }
                                $CustomStyle.= '
                                    .good{
                                        width:15%;
                                        height:auto;
                                        max-width:'.$width.'px;
                                        max-height:'.$height.'px;
                                    }
                                    .not-good{
                                        width:15%;
                                        height:auto;
                                        opacity:0.5;
                                        max-width:'.$width.'px;
                                        max-height:'.$height.'px;
                                    }';
                                wp_enqueue_style(
                                    'MensioPressCustomListStyle',
                                    plugin_dir_url(__FILE__) . '../css/mensio-public.css'
                                );
                                wp_add_inline_style( 'MensioPressCustomListStyle', $CustomStyle);
                $html.="</div>";
}
elseif(!empty($atts['display']) && $atts['display']=="carousel"){
                    if((!empty($_GET['page']) && $_GET['page']=="mns-html-edit") || (empty($_GET['page']) && (empty($atts['display'])))){
                            return "<div class='CarouselDisplay'></div>";
                    }
                    else{
                        $html.="<div class='CarouselDisplay' style='display:".$carouselDis.";font-size:".$textSize."rem;line-height:".$textSize."rem;'>";
                        $navigationpreviouslabel="<i class='fas fa-angle-double-left'></i>";
                        $navigationnextlabel="<i class='fas fa-angle-double-right'></i>";
                    $html.='
                    <!-- Owl Stylesheets -->
                    <link rel="stylesheet" href="'. plugin_dir_url( __FILE__ ).'../css/owl.carousel.min.css">
                    <link rel="stylesheet" href="'. plugin_dir_url( __FILE__ ).'../css/owl.theme.default.min.css">
                    <!-- javascript -->
                    <div class="row">
                      <div class="large-12 columns">
                        <div class="owl-carousel owl-'.$tt.' owl-theme">
                        ';
                                                $cols=count($FilteredArray);
                                                if(!empty($atts['cols'])){
                                                    $cols=$atts['cols'];
                                                }
                                                elseif($cols>=8 && empty($atts['cols'])){
                                                    $cols=8;
                                                }
                                                $o=1;
                                                $i=0;
                                                foreach($FilteredArray as $ar){
                                                    if(empty($ar['link'])){$ar['link']=false;}
                                                    if(empty($ar['image'])){$ar['image']=false;}
                                                    if(empty($ar['name'])){continue;}
                                                    if(empty($ar['price'])){$ar['price']=false;}
                                                    if(empty($ar['tax'])){$ar['tax']=false;}
                                                    if(empty($ar['discount'])){$ar['discount']=false;}
                                                    $BorderColor="";
                                                    if(!empty($ar['BorderColor']) && !empty($atts['imagebordercolors']) && $atts['imagebordercolors']=="yes"){
                                                        $BorderColor='border-bottom:10px inset '.$ar['BorderColor'].";";
                                                    }
                                                        $html.= "
                                                            <a href='".$ar['link']."'>";
                                                                $html.= "<img src='".$ar['image']."?ver=1.0' alt='".$ar['name']."' title='".$ar['name']."' style='width:163px;".$BorderColor."'>";
                                                                $html.="<div class='MensioProductListName' style='".$NameStyle."'>".$ar['name']."</div>";
                                                                if(!empty($atts['showtimessold']) && $atts['showtimessold']=='yes'){
                                                                    $html.='<div class="Mensio-TimesSold">'.$ar['sold'].'</div>';
                                                                }
                                                                if(!empty($atts['showtimesrated']) && $atts['showtimesrated']=='yes'){
                                                                    $html.='<div class="Mensio-TimesRated">'.$ar['rated'].'</div>';
                                                                }
                                                                if(!empty($atts['showprices']) && $atts['showprices']=='yes'){
                                                                    $html.='<div class="Mensio-ShowPrice">';
                                                                                    $UserType=false;
                                                                                    if(!empty($atts['show-price-with-tax']) && $atts['show-price-with-tax']=="yes"){
                                                                                        $ar['price']=$ar['price']+($ar['price']*($ar['tax']/100));
                                                                                        $ar['FinalPrice']=$ar['price']+($ar['price']*($ar['discount']/100));
                                                                                    }
                                                                                    if(!empty($ar['discount'])){
                                                                                        $ar['price']=$ar['price'];
                                                                                        $ar['FinalPrice']=$ar['price']-($ar['price']*($ar['discount']/100));
                                                                                    }
                                                                                    if($ar['discount'] && !empty($atts['showfirstprices']) && $atts['showfirstprices']=='yes'){
                                                                                        $html.="<div class='MensioFirstPrice mensioPrice'>".number_format($ar['price'],2)."</div>";
                                                                                    }
                                                                                    else{
                                                                                        $html.="<div class='MensioFirstPrice mensioPrice emptyPrice'>0.0</div>";
                                                                                    }
                                                                                    if(!empty($ar['FinalPrice'])){
                                                                                        $html.="<div class='MensioFinalPrice mensioPrice'>".number_format($ar['FinalPrice'],2)."</div>";
                                                                                    }
                                                                    $html.='</div>';
                                                                }
                                                                if(!empty($atts['showdiscounts']) && $atts['showdiscounts']=='yes' && !empty($ar['discount'])){
                                                                    $html.='<div class="Mensio-ShowDiscount"'.$DiscountStyle.'>';
                                                                        $html.="-".$ar['discount']."%";
                                                                    $html.='</div>';
                                                                }
                                                                $html.= "</a>";
                                                        $o++;
                                                        $i++;
                                                }
                        if($cols>$i){
                            $cols=1;
                        }
                        $html.='
                            </div>
                      </div>
                    </div>';
                        wp_enqueue_script("MensioPress-Owl-Carousel",plugin_dir_url( __FILE__ ).'../js/owl.carousel.js');
                        $MensioPressScript='
                          jQuery(document).ready(function() {
                            jQuery(".owl-theme").show();
                            var owl = jQuery(".owl-carousel.owl-'.$tt.'");
                            owl.mouseout(function(){
                                owl.trigger("play.owl.autoplay",[1000])
                            });
                            owl.owlCarousel({
                                stagePadding: 50,
                                nav: true,
                                navText: ["'.urldecode($navigationpreviouslabel).'","'.urldecode($navigationnextlabel).'"],
                                margin:10,';
                            if($cols<$i){
                                $MensioPressScript.='
                                    loop:true,
                                ';
                            }
                            else{
                                $MensioPressScript.='
                                    autoWidth:false,
                                    ';
                            }
                            if(!empty($atts['carouselautoplay']) && $atts['carouselautoplay']=="yes"){
                            $MensioPressScript.='
                            autoplay:true,
                            ';
                                if(!empty($atts['carouselautoplaytime'])){
                                $MensioPressScript.='
                                autoplayTimeout:'.$atts['carouselautoplaytime'].',
                                ';
                                }
                                else{
                                $MensioPressScript.='
                                autoplayTimeout:1000,
                                ';
                                }
                            }
                            if(!empty($atts['carouselhoverpause']) && $atts['carouselhoverpause']=="yes"){
                            $MensioPressScript.='
                            autoplayHoverPause:true,';
                            }
                            $MensioPressScript.='
                            autoHeight: false,
                            autoHeightClass: "mensiopress-owl-height",
                              responsive: {
                                0: {
                                  items: 2
                                },
                                600: {
                                  items: 3
                                },
                                1000: {
                                  items: '.$cols.'
                                }
                              }
                            })
                          })
                        ';
                            $tt=rand(1,1000);
                        wp_enqueue_script("MensioPressPublicJS".$tt,plugin_dir_url(dirname(__FILE__))."js/empty.js");
                        wp_add_inline_script("MensioPressPublicJS".$tt,$MensioPressScript);
                }
                $html.="</div>";
}
$html.="</div>";
            if(count($FilteredArray)==0 && empty($_GET['action'])){
                $html=false;
            }
            if(count($FilteredArray)==0 && !empty($_GET['action']) && $defaultTitle=="Categories"){
                $html="<i>No Categories Found</i>";
            }
            if(count($FilteredArray)==0 && !empty($_GET['action']) && $defaultTitle=="New Products"){
                $html="<i>No Products Found</i>";
            }
    return $html;
    }
}
function MensioPressWidgetListProducts($what,$array,$instance,$Link,$LinkText,$NoProdsText){
    $html=false;
    $get=new mnsFrontEndObject();
    $Total=false;
    $Quant=0;
    if($what!="Cart"){
        $Quant=count($array);
    }
    else{
        foreach($array as $item){
            $Quant=$Quant+$item['Quant'];
        }
    }
        $grad1='rgba(140,51,16,1)';
        if(!empty($instance['gradient_1'])){
            $grad1=$instance['gradient_1'];
        }
        $grad2='rgba(140,51,16,1)';
        if(!empty($instance['gradient_2'])){
            $grad2=$instance['gradient_2'];
        }
        $html.="<div class='MensioPressWidget".$what."' style='
                background: rgb(117,34,1); 
                background: -moz-linear-gradient(top, $grad1 0%, $grad2 100%); 
                background: -webkit-linear-gradient(top, $grad1 0%, $grad2 100%); 
                background: linear-gradient(to bottom, $grad1 0%, $grad2 100%); 
                filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='$grad1', endColorstr='$grad2',GradientType=0 ); '>";
        $html.='
            <div class="MensioWidgetIcon '.$instance['Icon'].'">';
        if( (!empty($instance['show-quantities']) && $instance['show-quantities']=="yes") ||
            (empty($instance['show-quantities'])) ){
                $html.='<div id="MensioCartQuantities">'.$Quant.'</div>';
        }
        $html.='
            </div>';
        $html.="
            <div class='MensioWidgetTitle'>
                ".$instance['title']."
            </div>
            ";
        $html.='<div class="WidgetContent">';
        if(count($array)==0){
            $html.=$NoProdsText;
        }
        else{
                            foreach($array as $item){
                                $Product=$get->mnsFrontEndProduct($item['id']);
                                $html.='<div class="Widget-row">';
                                        $html.='<div class="cell image"><a href="'.$Product['link'].'"><img src="'.$Product['image'].'" alt="'.$Product['name'].'" title="'.$Product['name'].'"></a></div>';
                                        $html.='<div class="cell">';
                                            $html.='<div class="name">'.$Product['name'].'</div>';
                                            $html.='<div class="data-rows">';
                                        if($what=="Cart"){
                                                $html.='<div class="data prod-price"><span class="mensioPrice">'.number_format($Product['final_price'],2,".","").'</span></div>';
                                                $html.='<div class="data quantity">x'.$item['Quant'].'</div>';
                                            $Total=$Total+($Product['final_price']*$item['Quant']);
                                                $html.='<div class="data prod-final-price"><strong><span class="mensioPrice">'.number_format(($Product['final_price']*$item['Quant']),2,".","").'</span></strong></div>';
                                        }
                                        else{
                                                $html.='<div class="data prod-final-price"><strong><span class="mensioPrice">'.number_format($Product['final_price'],2,".","").'</span></strong></div>';
                                        }
                                            $html.='</div>';
                                        $html.='</div>';
                                $html.='</div>';
                            }
                                $html.='<div class="Widget-row">';
                                    $html.='<div class="cell"><hr /></div>';
                                    $html.='<div class="cell"><hr /></div>';
                                $html.='</div>';
                                $html.='<div class="Widget-row">';
                                    $html.='<div class="cell"><a href="'.$Link.'">'.$LinkText.'</a></div>';
                                    $html.='<div class="cell">&nbsp;';
                                    if($what=="Cart" && $Total){
                                        $html.='<div class="data-rows">';
                                            $html.='<div class="data prod-final-price"><strong><span class="mensioPrice">'.$Total.'</span></strong></div>';
                                        $html.='</div>';
                                    }
                                    $html.='</div>';
                                $html.='</div>';
        }
        $html.='</div>';
        $html.="</div>";
        return $html;
}