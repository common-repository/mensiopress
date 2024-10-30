<?php
add_shortcode( 'mns_product', 'mensiopress_product' );
if(!function_exists("mensiopress_product")){
function mensiopress_product($atts){
    if(empty($atts['titlesize']) && !empty($GLOBALS['MensioPressFontSize'])){
        $atts['titlesize']=$GLOBALS['MensioPressFontSize'];
    }
    if(empty($atts['fontsize']) && !empty($GLOBALS['MensioPressFontSize'])){
        $atts['fontsize']=$GLOBALS['MensioPressFontSize'];
    }
    if(!empty($atts['call']) && ($atts['call']=='func')){
        $html="<div class='mns-html-content'>";
    }
    else{
        $html="";
    }
    global $post;
    if(!empty(get_post_meta($post->ID,"mensio_page_function")) &&
        empty($_GET['action'])){
        $func=get_post_meta($post->ID,"mensio_page_function");
        $func=$func[0];
        if($func!="product_page"){
            return false;
        }
    }
    if((empty($atts['prod_id'])) && (!empty($GLOBALS['UUID']))){
        $prod_id=MensioEncodeUUID($GLOBALS['UUID']);
    }
    elseif((empty($atts['prod_id'])) && (!empty($_REQUEST['product']))){
        $prod_id=filter_var($_REQUEST['product']);
    }
    elseif((!empty($atts['prod_id'])) && (empty($_REQUEST['product']))){
        $prod_id=$atts['prod_id'];
    }
    elseif((!empty($atts['prod_id'])) && (!empty($_REQUEST['product']))){
        $prod_id=filter_var($_REQUEST['product']);
    }
    else{
        $random=new mnsFrontEndObject();
        $prod_id= MensioEncodeUUID($random->mnsFrontEndRandomProduct());
    }
    if(!empty($_REQUEST['var'])){
        $prod_id= filter_var($_REQUEST['var']);
    }
    $prod_id= MensioDecodeUUID($prod_id);
    if(!empty($_GET['var'])){
        $prod_id= MensioDecodeUUID($_GET['var']);
    }
    $prods=new mnsFrontEndObject();
    $prods=$prods->mnsFrontEndProduct(filter_var($prod_id));
    if(!$prods){
        return false;
    }
    $GLOBALS['UUID']=$prod_id;
    $price=number_format($prods['final_price'],2);
    $UserType=false;
    $prods['FinalPrice']=$prods['price'];
    if(!empty($atts['show-price-with-tax']) && $atts['show-price-with-tax']=="yes"){
        $prods['price']     =$prods['price']+($prods['price']*($prods['tax']/100));
        $prods['FinalPrice']=$prods['price'];
    }
    if(!empty($prods['discount'])){
        $prods['price']=$prods['price'];
        $prods['FinalPrice']=$prods['price']-($prods['price']*($prods['discount']/100));
    }
    $price=$prods['FinalPrice'];
    $FirstPrice=$prods['price'];
    $discount=$prods['discount'];
    $name=$prods['name'];
    $main_image="";
    if(!empty($prods['main_image'])){
        $main_image=$prods['main_image'];
    }
    if(!empty($prods['images'])){
        $images=$prods['images'];
    }
    else{
        $images=array();
    }
    if(!empty($prods['filters'])){
        $filters=$prods['filters'];
    }
    else{
        $filters=array();
    }    
    $description=$prods['description'];
    $LongDescription=$prods['longDescription'];
    if($prods['stock']>0){
        $stock=true;
    }
    else{
        $stock=false;
    }
    if(!empty($prods['advantages'][$_SESSION['MensioThemeLang']])){
        $advantages=$prods['advantages'][$_SESSION['MensioThemeLang']];
    }
    $sku=$prods['sku'];
    $brand=$prods['brand'];
    $_SESSION['mnsCurrentBrand']=$brand;
    $status=$prods['status'];
    $fontSize="1";
    if(!empty($atts['fontsize'])){
        $fontSize=str_replace("-",".",$atts['fontsize']);
    }
    $titleSize="2";
    if(!empty($atts['titlesize'])){
        $titleSize=str_replace("-",".",$atts['titlesize']);
    }
    $html.='
<div class="product-view '.$stock.'" prodid="'.MensioEncodeUUID($prod_id).'">';
    if(empty($prods['image'])){
        $prods['image']=plugin_dir_url(__FILE__)."/../../icons/mensiopress-noimage.png";
        $prods['image-thumb']=plugin_dir_url(__FILE__)."/../../icons/mensiopress-noimage.png";
    }
    $tt=time()."-".rand(1000, 9999);
    $tt=time();
    $BorderColor="";
    if(!empty($prods['BorderColor'])){
        $BorderColor="border-bottom:10px solid ".$prods['BorderColor'];
    }
    $html.='
    <div id="product-details">
        <h3 class="product-view-title mensioObjectTitle ResponsiveTitle" style="font-size:'.$titleSize.'rem !important;line-height:'.($titleSize+0.4).'rem !important;">
            '.$name.'
                <hr class="TitleLine" style="margin: 5px 0;">
        </h3>
        <div class="product-view-image">
            <div id="main-img">
                <img src="'.$prods['image-thumb'].'" id="image-'.$tt.'" alt="'.$name.'" data-high-res-src="'.$prods['image'].'" data-zoom-image="'.$prods['image'].'" style="'.$BorderColor.'">';
                if(!empty($price) && (!empty($discount))){
                    $DiscountBGcolor="FF0000";
                    if(!empty($atts['discount-background-color'])){
                        $DiscountBGcolor=$atts['discount-background-color'];
                    }
                    $DiscountTextcolor="FFFFFF";
                    if(!empty($atts['discount-text-color'])){
                        $DiscountTextcolor=$atts['discount-text-color'];
                    }
                    $html.='<div class="discount" style="background:#'.str_replace("#","",$DiscountBGcolor).';color:#'.str_replace("#","",$DiscountTextcolor).';">-'.$discount."%</div>";
                }
            $html.='
            </div>';
            if(!empty($prods['brand'])){
                $getLink=new mnsGetFrontEndLink();
                $getBrandImg=new mnsFrontEndObject();
                $brand=$getBrandImg->mnsFrontEndBrandProducts($prods['brand']);
                $brandImage=$brand['current_brand_image'];
                $BrandLink=$getLink->BrandLink($prods['brand']);
                $html.="<a href='".$BrandLink."'><img src='".$brandImage."' class='product-brand ResponsiveBrand'></a>";
            }
            $html.='<div class="more-images">
            ';
            foreach($images as $img){
                $html.= '
                <img src="'.$img['thumb'].'" data-zoom-image="'.$img['image'].'" data-high-res-src="'.$img['image'].'" alt="'.$name.'" class="mnsGallery-items">';
            }
            if(!empty($prods['bundleProducts'])){
                foreach($prods['bundleProducts'] as $bundle){
                    $html.= '
                    <img src="'.get_site_url()."/".$bundle->main_image.'" data-zoom-image="'.get_site_url()."/".$bundle->main_image.'" data-high-res-src="'.get_site_url()."/".$bundle->main_image.'" alt="'.$bundle->name.'" class="mnsGallery-items">';
                }
            }
            $html.= '
            </div>';
            if(!empty($atts['share'])){
            $CURurl="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $html.='
            <div class="MensioShareButtons">';
            $html.='
                <a href="https://www.facebook.com/sharer/sharer.php?u='.$CURurl.'&amp;t='.get_bloginfo('name').'-'.$name.'" title="Share on Facebook" target="_blank" onclick='."'".'window.open("https://www.facebook.com/sharer/sharer.php?u=" + encodeURIComponent(document.URL) + "&amp;t=" + encodeURIComponent(document.URL), "_blank","height=450, width=550, top=" + (jQuery(window).height() / 2 - 275) + ", left=" + (jQuery(window).width() / 2 - 225) + ", toolbar=0, location=0, menubar=0, directories=0, scrollbars=0" ); return false;'."'".'>
                    <i class="fa fa-facebook-square" aria-hidden="true"></i>
                </a>
                <a href="https://plus.google.com/share?url='.$CURurl.'" target="_blank" title="Share on Google+" onclick='."'".'window.open("https://plus.google.com/share?url=" + encodeURIComponent(document.URL), "_blank","height=450, width=550, top=" + (jQuery(window).height() / 2 - 275) + ", left=" + (jQuery(window).width() / 2 - 225) + ", toolbar=0, location=0, menubar=0, directories=0, scrollbars=0" ); return false;'."'".'>
                    <i class="fa fa-google-plus-official" aria-hidden="true"></i>
                </a>
                <a href="http://pinterest.com/pin/create/button/?url='.$CURurl.';description='.get_bloginfo('name').'-'.$name.'" target="_blank" title="Pin it" onclick='."'".'window.open("http://pinterest.com/pin/create/button/?url=" + encodeURIComponent(document.URL) + "&amp;description=" +encodeURIComponent(document.title), "_blank","height=450, width=550, top=" + (jQuery(window).height() / 2 - 275) + ", left=" + (jQuery(window).width() / 2 - 225) + ", toolbar=0, location=0, menubar=0, directories=0, scrollbars=0" ); return false;'."'".'>
                    <i class="fa fa-pinterest-p" aria-hidden="true"></i>
                </a>
                <a href="https://twitter.com/intent/tweet?text='.get_bloginfo('name').'-'.$name.'&url='.$CURurl.'" target="_blank" title="Pin it" onclick='."'".'window.open("https://twitter.com/intent/tweet?text='.urlencode(get_bloginfo('name').'-'.$name).'&url='.urlencode($CURurl).'", "_blank","height=450, width=550, top=" + (jQuery(window).height() / 2 - 275) + ", left=" + (jQuery(window).width() / 2 - 225) + ", toolbar=0, location=0, menubar=0, directories=0, scrollbars=0" ); return false;'."'".'>
                    <i class="fa fa-twitter" aria-hidden="true"></i>
                </a>
            </div>';
            }
        $html.='
        </div>
        <div class="product-view-details">
            <h3 class="product-view-title mensioObjectTitle" style="font-size:'.$titleSize.'rem;line-height:'.($titleSize+0.4).'rem;">
                '.$name.'
            </h3>
            ';
        $html.="<div style='margin-top:96px;'>";
            $html.='
        <div style="display:table;width:100%;">
        <div style="display:table-row;">
            <div class="product-view-price" style="font-size:'.($titleSize+0.5).'rem;">
                <span class="mensioPrice">'.number_format($price,2).'</span>';
                if($FirstPrice!=$price){
                    $html.='
                        <span class="mensioFirstPrice">
                            <span class="FirstPrice mensioPrice">'.number_format($FirstPrice,2).'</span>
                        </span>';
                }
            $html.='
            </div>
            ';
            if(!empty($prods['brand'])){
                $getLink=new mnsGetFrontEndLink();
                $getBrandImg=new mnsFrontEndObject();
                $brand=$getBrandImg->mnsFrontEndBrandProducts($prods['brand']);
                $brandImage=$brand['current_brand_image'];
                $BrandLink=$getLink->BrandLink($prods['brand']);
                $html.="<div style='display:table-cell;'><a href='".$BrandLink."' class='ProductBrand'><img src='".$brandImage."' class='product-brand'></a></div>";
            }
            $html.='
                    </div>
                    </div>
                    ';
        $Store=new mnsFrontEndObject();
        $StoreData=$Store->mnsFrontEndStoreData();
        $metrics=$StoreData['metrics'];
        $ar1=explode(";",$metrics);
        $metrics=array();
        foreach($ar1 as $ar){
            $ar2=explode(":",$ar);
            $metrics[$ar2[0]]=$ar2[1];
        }
        $html.='<hr style="margin-bottom:0;" />';
            $html.="<div class='MensioObjectTools ListButtons'";
            $html.=" style='";
            if(empty($prods['availability'])){
                $html.="width:100%;";
            }
            $html.="'>";
            if(empty($atts['hide-barcode']) || (!empty($atts['hide-barcode']) && $atts['hide-barcode']!="yes")){
                $html.='
                <div class="ListButton Codes">
                    <i class="fa fa-barcode" aria-hidden="true">
                    </i>
                        <div>';
                        if(!empty($prods['sku'])){
                            $html.=str_replace(" ","&nbsp;",$prods['sku'])."<br/>";
                        }
                        $html.='
                        </div>
                </div>';
            }
            $html.="</div>";
        if(empty($atts['hide-availability']) || (empty($atts['hide-availability']) && $atts['hide-availability']!="yes")){
            if($prods['availability-icon'] && $prods['availability']){
            $html.='
            <div class="product-view-extra product-view-availability">';
                $style=false;
                if(!empty($prods['availability-color'])){
                    $style.="color:".$prods['availability-color'].";'";
                }
                if($prods['availability-icon']){
                    $html.='<img src="'.$prods['availability-icon'].'" style="width:25px;height:25px;position:relative;" /> ';
                }
                $html.="<div class='availabilityText' style='".$style."'>".$prods['availability']."</div>";
            $html.='
        </div>';
            }
        }
        $html.='<hr style="margin-top:0;margin-bottom:10px;" class="availabilityHR" />';
        $html.='
                <div class="product-view add-to-cart-quant">
                    <input type="tel" value="1"  class="mns-quant">
                </div>
                <div class="product-view add-to-cart" id="product-'.MensioEncodeUUID($prod_id).'">
                    <input type="button" value="'.str_replace(" ","&nbsp;",$GLOBALS['MensioAddToCartText']).'">
                </div>
            </div>
        </div>
    </div>
    ';
        $html.='
    <div class="MensioProductDetails" style="font-size:'.$fontSize.'rem;">';
        if(!empty($description)){
            $TabHeader="Description";
            if(get_option('MensioProductPageTab_Description_'.$_SESSION['MensioThemeLangShortcode'])){
                $TabHeader=get_option('MensioProductPageTab_Description_'.$_SESSION['MensioThemeLangShortcode']);
            }
            $html.='
            <div id="mensioDescr">'.$TabHeader.'</div>';
        }
        if(!empty($advantages)){
            $TabHeader="Advantages";
            if(get_option('MensioProductPageTab_Advantages_'.$_SESSION['MensioThemeLangShortcode'])){
                $TabHeader=get_option('MensioProductPageTab_Advantages_'.$_SESSION['MensioThemeLangShortcode']);
            }
            $html.='
            <div id="mensioAdvantages">'.$TabHeader.'</div>';
        }
        if($filters){
            $TabHeader="Attributes";
            if(get_option('MensioProductPageTab_Attributes_'.$_SESSION['MensioThemeLangShortcode'])){
                $TabHeader=get_option('MensioProductPageTab_Attributes_'.$_SESSION['MensioThemeLangShortcode']);
            }
            $html.='
            <div id="mensioAttrs">'.$TabHeader.'</div>';
        }
        if(!empty($prods['bundleProducts'])){
            $TabHeader="Products";
            if(get_option('MensioProductPageTab_Products_'.$_SESSION['MensioThemeLangShortcode'])){
                $TabHeader=get_option('MensioProductPageTab_Products_'.$_SESSION['MensioThemeLangShortcode']);
            }
            $html.='
            <div id="mensioBundleProducts">'.$TabHeader.'</div>';
        }
        if(!empty($prods['files'])){
            $TabHeader="Files";
            if(get_option('MensioProductPageTab_Files_'.$_SESSION['MensioThemeLangShortcode'])){
                $TabHeader=get_option('MensioProductPageTab_Files_'.$_SESSION['MensioThemeLangShortcode']);
            }
            $html.='
            <div id="mensioProductFiles">'.$TabHeader.'</div>';
        }
        $html.='
    </div>
    <div id="Product-Tabs">';
    if(!empty($LongDescription)){
        $html.='
        <div id="mensioDescr-Tab" class="product-view-text" style="font-size:'.($fontSize-0.3).'rem;line-height:'.($fontSize-0.3).'rem;">
            <div id="">'.$LongDescription;
            if(!empty($prods['barcodes'])){
                $html.="<br /><br />
                        <div class='barcodes'>Barcodes:";
                foreach($prods['barcodes'] as $barcode){
                    $html.='<span class="barcode">'.$barcode.'</span>';
                }
                $html.='</div>';
            }
        $html.='</div>
        </div>';
    }
    if($filters){
        $seller=new mensio_seller();
        $html.='
        <div class="mnsAttributes" id="mensioAttrs-Tab" style="font-size:'.($fontSize-0.3).'rem;line-height:'.($fontSize-0.3).'rem;">
            <div style="display:table;width:100%;">
                ';
        foreach($filters as $filter){
                if(!empty($seller->FiltersHTML($filter['value'], $filter['name'], false, false,"value"))){
                    $html.= '
                        <div class="attribute">
                            <div class="attribute-title">'.$filter['FrontName'].':</div>'
                        . '<div class="attribute-value">';
                    $html.=$seller->FiltersHTML($filter['value'], $filter['name'], false, false,"value");
                    $html.='</div>'
                    . '</div>';
                }
        }
        $html.='</div>';
        $html.='</div>';
    }
    if(!empty($prods['files'])){
        $html.='
        <div class="mensioProductFiles" id="mensioProductFiles-Tab" style="font-size:'.($fontSize-0.3).'rem;line-height:'.($fontSize-0.3).'rem;">';
        foreach($prods['files'] as $fl){
            $type=wp_check_filetype($fl['path']);
            $ext=$type['ext'];
            if(getimagesize($fl['path'])){
                $fa="fa fa-image";
            }
            elseif($ext=="pdf"){
                $fa="fa fa-file-pdf-o";
            }
            elseif($ext=="doc" ||$ext=="docx"){
                $fa="fa fa-file-word-o";
            }
            elseif($ext=="xls" ||$ext=="xlsb" || $ext=="xlsm" || $ext=="xlsx"){
                $fa="fa fa-file-excel-o";
            }
            elseif($ext=="ppt" ||$ext=="pptm" || $ext=="pptx"){
                $fa="fa fa-file-excel-o";
            }
            elseif($ext=="mp3" ||$ext=="wav" || $ext=="wma"){
                $fa="fa fa-file-audio-o";
            }
            elseif($ext=="mp4" ||$ext=="mpg" || $ext=="mp3g" || $ext=="avi"){
                $fa="fa fa-file-video-o";
            }
            else{
                $fa="fa fa-file";
            }
            $html.="<a href='?MensioPressFileDownload=".MensioEncodeUUID($fl['id'])."' target='_blank'><i class='".$fa."' style='font-size:25px;'></i>&nbsp;".$fl['name']."</a><br /><br />";
        }
        $html.='</div>';
    }
    if(!empty($prods['bundleProducts'])){
        $html.='
        <div class="mnsBundleProducts" id="mensioBundleProducts-Tab" style="font-size:'.($fontSize-0.3).'rem;line-height:'.($fontSize-0.3).'rem;">';
        foreach($prods['bundleProducts'] as $bundle){
            $link=new mnsGetFrontEndLink();
            $link=$link->ProductLink($bundle->product);
            $html.= '
                <div class="Bundle">
                    <a href="'.$link.'">'.$bundle->name.'</a>
                </div>';
        }
        $html.='</div>';
    }
    if(!empty($prods['advantages'][$_SESSION['MensioThemeLang']])){
        $html.='
        <div class="mensioAdvantages-Tab" id="mensioAdvantages-Tab" style="font-size:'.($fontSize-0.3).'rem;line-height:'.($fontSize-0.3).'rem;">';
        foreach($prods['advantages'][$_SESSION['MensioThemeLang']] as $adv){
            $html.= '
                <div class="Advantage">
                    '.$adv.'
                </div>';
        }
        $html.='</div>';
    }
    $html.='</div>';
    if(empty($_GET['page']) || (!empty($_GET['page']) && $_GET['page']!='mns-html-edit')){
    wp_enqueue_script("MensioPress-ImageViewer", plugin_dir_url(__FILE__)."../../public/js/jquery.elevatezoom.js");
    $CustomScript='
    jQuery(function () {
        var viewer = ImageViewer();
        jQuery("#main-img").click(function () {
            var imgSrc = jQuery("#main-img img").attr("src"),
                highResolutionImage = jQuery("#main-img img").data("high-res-src");
            viewer.show(imgSrc, highResolutionImage);
        });
    });
    ';
    $CustomScript.="
    if(jQuery('body').width()>600){
        jQuery('#image-".$tt."').elevateZoom({
        zoomType: 'inner',
        cursor: 'crosshair',
        zoomWindowFadeIn: 500,
        zoomWindowFadeOut: 750,
        responsive:false
           });
    }
    ";
    wp_add_inline_script( 'MensioPress-ImageViewer',
               $CustomScript
               );
    }
    global $post;
    $tags=wp_get_post_tags($post->ID);
    if(!empty($tags)){
        $html.="<div class='product-tags'><b>#</b> ";
            $i=0;
            foreach($tags as $tag){
                if ( get_option('permalink_structure') ){
                    $link=get_site_url()."/tag/".$tag->slug;
                    $getLink=new mnsGetFrontEndLink();
                    $link=$getLink->SearchPage()."?mns_keyword=".urlencode("#".$tag->slug);
                }
                else{
                    $getLink=new mnsGetFrontEndLink();
                    $link=$getLink->SearchPage()."&mns_keyword=".urlencode("#".$tag->slug);
                }
                $html.="<span><a href='".$link ."'>".$tag->name."</a></span>";
                if($i!=(count($tags))-1){
                    $html.=", ";
                }
                $i++;
            }
        $html.="</div>";
    }
    $html.='
    </div>';
    if(!empty($atts['call']) && ($atts['call']=='func')){
        $html.="</div>";
    }
    return $html;
}
}