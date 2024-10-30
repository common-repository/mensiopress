<?php
if(MENSIO_FLAVOR=='STD'){
function mnsExtraObjects(){
    $array=array(
       'related_products'=>array(
           "id"=>"related_products",
           "object"=>"Related Products",
           "explain"=>false,
           "atts"=>array(
               "title","maxproducts",
               "show_discount","show_prices","product-names-lines",
               "display","text-size",
               "mensio-list-product-controls",
               "imageBorderColor","round_images",
               "bg-color","border","box-border"
               )
           ),
       'product_offers'=>array(
           "id"=>"product_offers",
           "object"=>"Product Offers",
           "explain"=>false,
           "atts"=>array("title","maxproducts","of_brand","of_category",
               "show_discount","show_prices","product-names-lines",
               "display","text-size",
               "mensio-list-product-controls",
               "imageBorderColor","round_images",
               "bg-color","border","box-border")
           ),
       'advanced_search'=>array(
           "id"=>"advanced_search",
           "object"=>"Advanced Search",
           "explain"=>false,
           "atts"=>array("title",
               "show_discount","show_prices","product-names-lines",
               "display","text-size",
               "mensio-list-product-controls",
               "imageBorderColor","round_images",
               "bg-color","border","box-border"
               )
           ),
       'favorites'=>array(
           "id"=>"favorites",
           "object"=>"Favorites",
           "explain"=>false,
           "atts"=>array("title","text-size","bg-color","border")
           ),
       'product_comparison'=>array(
           "id"=>"product_comparison",
           "object"=>"Product Comparison",
           "explain"=>false,
           "atts"=>array("title","show_prices","text-size","imageBorderColor","bg-color","border")
           ),
    );
    return $array;
}
function mensiopress_MensioTheObjects($MensioBottomButtons){
    $post_id= filter_var($_REQUEST['post']);
    $post=get_post($post_id);
    if(!empty($_REQUEST['post'])){
        $post=get_post(filter_var($_REQUEST['post']));
        $current_title=$post->post_title;
        $current_content=$post->post_content;
    }
    $objects=mnsObjects();
    $page_types=mensio_page_types();
    $CustomScript=false;
    $CustomScript.='
        function create_object(d){
            var elem_num=$("div.mns-html-content *").length+1;
            var elem;
            ';
            foreach($objects as $obj){
                $CustomScript.= '
            if(d==="'.$obj['id'].'"){
                elem="<div class=\"mns-block mns-'.$obj['id'].' col-4 mns-preview mns-elem="+elem_num+" \"><div class=\"mns-obj-to-rem\">x</div>[mns_'.$obj['id'].']</div>";
            }
            ';
            }
            $CustomScript.= '
            return elem;
        }';
   wp_enqueue_script(
           'MensioPressCreateObject',
           plugin_dir_url( __FILE__ ) . '../../js/custom.js',
           array(),
           '1.0' );
   wp_add_inline_script( 'MensioPressCreateObject',
           $CustomScript
           );
$Layout=false;
    $Layout.='<div id="mnsPageEditorLogo"></div>
        <!--<div id="mnsGrad"></div>-->
    <div id="settings">';
    $pgFunction='';
                    $post_meta=get_post_meta( $post_id, 'mensio_page_function');
                    foreach($page_types as $type){
                        if((!empty($post_meta)) && ($post_meta[0]==$type['id'])){
                            $pgFunction=$type['id'];
                        }
                    }
    $Layout.='          
<div id="mns-objects" class="tab">
             ';
    $mnsObjects='';
    if(!empty($_REQUEST['tempPgFunction'])){
        $pgFunction=$_REQUEST['tempPgFunction'];
    }
            foreach($objects as $obj){
                if(
                        ($pgFunction=='brand_page' && $obj['id']=='product') ||
                        ($pgFunction=='brand_page' && $obj['id']=='category') ||
                        ($pgFunction!='brand_page' && $obj['id']=='brand') ||
                        ($pgFunction=='category_page' && $obj['id']=='brand') ||
                        ($pgFunction=='category_page' && $obj['id']=='product') ||
                        ($pgFunction!='category_page' && $obj['id']=='category') ||
                        ($pgFunction=='product_page' && $obj['id']=='brand') ||
                        ($pgFunction=='product_page' && $obj['id']=='category') ||
                        ($pgFunction!='product_page' && $obj['id']=='product') ||
                        ($pgFunction!='product_page' && $obj['id']=='related_products')
                    ){
                    $dis=' disabled';
                }
                else{
                    $dis='';
                }
                $mnsObjects.= "
                <div class='mns-element-to-create drag-drop".$dis."' id='".$obj['id']."' shortcode='mns_".$obj['id']."' title='".$obj['explain']."'>";
                    $mnsObjects.=$obj['object']."
                </div>
                ";
            }
    if(!empty($_REQUEST['tempPgFunction'])){
        echo $mnsObjects;
        die;
    }
        $Layout.=$mnsObjects.'
        <Br /><Br />
        <Br /><Br />
        <Br /><Br />
        </div>
</div>
<input type="hidden" id="edit-object">
<input type="hidden" id="preview-shortcode">
<div class="bottom-buttons">
    <button class="open-settings" class="button BtnGreen BTN_Save" title="Settings" postID="'.$post_id.'">
        <div class="bubbleDescr" for="open-settings">'.$MensioBottomButtons['Settings'].'</div>
        <i class="fa fa-cog" aria-hidden="true"></i>
    </button>
</div>
';
    return $Layout;
}
function OtherTranslationsInputs(){
    $ContactInputs=array(
        "Cart"=>"Cart",
        "NoProdsInCart"=>"No Products found in your cart",
        "RemoveAll"=>"Remove All",
        "AddToFav"=>"Add to favorites",
        "NoProdsinFavoritesList"=>"No Products in Favorites List",
        "FavoritesPage"=>"Favorites Page",
        "AddToCompList"=>"Add to Comparison List",
        "ComparisonPage"=>"Comparison Page",
        "NoProdsinComparisonList"=>"No Products in Comparison List",
        "NoShipping"=>"No Shipping found for your country",
        "WrongCreds"=>"Wrong Credentials",
        "AddedToCart"=>"Added To Cart",
        "AddedMoreToCart"=>"Added More To Cart",
        "AddedToComparisonList"=>"Added To Comparison List",
        "RemovedFromComparisonList"=>"Removed From Comparison List",
        "AlreadyInComparisonList"=>"Already In Comparison List",
        "ComparisonListFull"=>"Comparison List is full",
        "AddedToFavoritesList"=>"Added To Favorites List",
        "RemovedFromFavoritesList"=>"Removed From Favorites List",
        "AlreadyInFavoritesList"=>"Already In Favorites List",
        "LoginToRate"=>"Please Login to post a rating"
        );
    return $ContactInputs;
}
function MoreProductTabsTranslations($langs){
    $Layout='
        <div class="Input">
            <div>Bundle Products</div>
            <div></div>
        </div>';
    foreach($langs as $lang){
        if(!empty($_REQUEST['MensioProductPageTab_Products_'.$lang->code])){
            if(!update_option( 'MensioProductPageTab_Products_'.$lang->code, filter_var($_POST['MensioProductPageTab_Advantages_'.$lang->code]))){
                add_option('MensioProductPageTab_Products_'.$lang->code,filter_var($_POST['MensioProductPageTab_Advantages_'.$lang->code]),'','no');
            }
        }
        if(get_option('MensioProductPageTab_Products_'.$lang->code)){
            $Value=get_option('MensioProductPageTab_Products_'.$lang->code);
        }
        else{
            $Value="";
        }
        $Layout.='
            <div>
                <div><img src="'.plugin_dir_url(__FILE__).'../../icons/flags/'.$lang->icon.'.png" width="30" /></div>
                <div><input type="text" name="MensioProductPageTab_Products_'.$lang->code.'" placeHolder="Advantages" value="'.$Value.'"></div>
            </div>';
    }
    $Layout.='<div class="Input"><div>Advantages</div><div></div></div>';
    foreach($langs as $lang){
        if(!empty($_REQUEST['MensioProductPageTab_Advantages_'.$lang->code])){
            if(!update_option( 'MensioProductPageTab_Advantages_'.$lang->code, filter_var($_POST['MensioProductPageTab_Advantages_'.$lang->code]))){
                add_option('MensioProductPageTab_Advantages_'.$lang->code,filter_var($_POST['MensioProductPageTab_Advantages_'.$lang->code]),'','no');
            }
        }
        if(get_option('MensioProductPageTab_Advantages_'.$lang->code)){
            $Value=get_option('MensioProductPageTab_Advantages_'.$lang->code);
        }
        else{
            $Value="";
        }
        $Layout.='
            <div>
                <div><img src="'.plugin_dir_url(__FILE__).'../../icons/flags/'.$lang->icon.'.png" width="30" /></div>
                <div><input type="text" name="MensioProductPageTab_Advantages_'.$lang->code.'" placeHolder="Advantages" value="'.$Value.'"></div>
            </div>';
    }
    return $Layout;
}
}