<?php
function MensioBreadcrumbs($atts){
    $html=false;
    global $post;
    $html="<ul class='Mensio-Breadcrumbs'>";
    if($post->post_type=="mensio_category" || !empty($_GET['category'])){
        $CurrentUUID=str_replace(array('[mensioobject',' ','uuid','=',']','"'),"",$post->post_content);
            if(!empty($_GET['category'])){
                $CurrentUUID= MensioDecodeUUID(filter_var($_GET['category']));
            }
        $CategoryID=$CurrentUUID;
        $findTopCat=new mensio_seller();
        $findTopCat->Set_CategoryID($CategoryID);
        $CategoryName=$findTopCat->TranslateCategory();
        $BreadCrumbs[0]['CategoryID']=$CategoryID;
        if(!empty($CategoryName[0]->name)){
            $BreadCrumbs[0]['CategoryName']=$CategoryName[0]->name;
        }
        else{
            $BreadCrumbs[0]['CategoryName']=$post->post_title;
        }
        for($i=1;$i<=1000;$i++){
            $CategoryID=$findTopCat->LoadCategoryParent($CategoryID);
            if(empty($CategoryID->name)){
                break;
            }
            $CategoryName=$CategoryID->name;
            $CategoryID=$CategoryID->parent;
            $findTopCat->Set_CategoryID($CategoryID);
            $GetCategoryName=$findTopCat->TranslateCategory();
            if(!empty($GetCategoryName[0]->name)){
                $CategoryName=$GetCategoryName[0]->name;
            }
            $BreadCrumbs[$i]['CategoryID']=$CategoryID;
            $BreadCrumbs[$i]['CategoryName']=$CategoryName;
        }
        $BreadCrumbs2=array();
        for($i=(count($BreadCrumbs)-1);$i>=0;$i--){
            $BreadCrumbs2[$i]['CategoryID']=$BreadCrumbs[$i]['CategoryID'];
            $BreadCrumbs2[$i]['CategoryName']=$BreadCrumbs[$i]['CategoryName'];
        }
    }
    else{
        return false;
    }
    $i=1;
    $GetLink=new mnsGetFrontEndLink();
    foreach($BreadCrumbs2 as $BreadCrumb){
        $Class=false;
        if(count($BreadCrumbs2)==$i){
            $Class=' class="Current"';
        }
        $html.="
            <li".$Class.">
                <a href='".$GetLink->CategoryLink($BreadCrumb['CategoryID'])."' style='font-size:".$atts['fontsize']."rem;line-height:".$atts['fontsize']."rem;'>".$BreadCrumb['CategoryName']."</a>
            </li>";
        $i++;
    }
    $html.="</ul>";
    return $html;
}
add_shortcode( 'mns_category', 'mensiopress_get_category' );
function mensiopress_get_category($atts){
    if(empty($atts['titlesize']) && !empty($GLOBALS['MensioPressFontSize'])){
        $atts['titlesize']=$GLOBALS['MensioPressFontSize'];
    }
    if(empty($atts['fontsize']) && !empty($GLOBALS['MensioPressFontSize'])){
        $atts['fontsize']=$GLOBALS['MensioPressFontSize'];
    }
    if(!empty($atts['call']) && $atts['call']=="func"){
        $html= "<div class='mns-html-content'>";
    }
    else{
        $html="";
    }
    global $post;
    if( !empty(get_post_meta($post->ID,"mensio_page_function")) &&
        empty($_GET['action'])
        ){
        $func=get_post_meta($post->ID,"mensio_page_function");
        $func=$func[0];
        if($func!="category_page"){
            return false;
        }
    }
    if(!empty($atts['filters'])){
        $prod_atts['filters']=$atts['filters'];
    }
    elseif(!empty($_REQUEST['filter'])){
        $prod_atts['filters']=$_REQUEST['filter'];
    }
    else{
        $prod_atts['filters']=array();
    }
    if(!empty($_REQUEST['price-max'])){
        $prod_atts['max_price']=$_GET['price-max'];
    }
    $cur_price=0;
    if( (empty($_REQUEST['category'])) && (empty($atts['cat_id'])) && !empty($GLOBALS['UUID'])){
        $cat_id= MensioEncodeUUID($GLOBALS['UUID']);
    }
    elseif( (!empty($_REQUEST['category'])) && (empty($atts['cat_id'])) ){
        $cat_id=filter_var($_REQUEST['category']);
    }
    elseif( (empty($_REQUEST['category'])) && (!empty($atts['cat_id'])) ){
        $cat_id=$atts['cat_id'];
    }
    elseif( (!empty($_REQUEST['category'])) && (!empty($atts['cat_id'])) ){
        $cat_id=$_REQUEST['category'];
    }
    else{
        $cat_id=0;
        $filters=array();
        $products=array();
    }
    $cat_id= MensioDecodeUUID($cat_id);
    $products = new mnsFrontEndObject();
    if(!empty($_GET['sort'])){
        $prod_atts['ordering']=filter_var($_GET['sort']);
    }
    if(empty($_GET['cat-page'])){
        $limit=false;
    }
    else{
        $from="0";
        $limit=$from.",10";
    }
    $cat_prods=$products->mnsFrontEndCategoryProducts($cat_id,$prod_atts,$limit);
    $title=$cat_prods['current_cat_name'];
    $img=$cat_prods['current_cat_image'];
    $brands=new mnsFrontEndObject();
    $brands=$brands->mnsFrontEndBrands("");
    $brands=new mensio_seller();
    $brands->Set_CategoryID($cat_id);
    $brands=$brands->LoadCategoryBrands();
    if(empty($brands)){
        $brands=array();
    }
    if(!empty($_GET['price-max'])){
        $cur_price=$_GET['price-max'];
    }
    if(empty($cat_prods['products'])){
        $products=array();
    }
    else{
        $products=$cat_prods['products'];
    }
    if(empty($atts['maxproducts'])){
        $break_on=20;
    }
    else{
        $break_on=$atts['maxproducts'];
    }
    if(!$title){
        $title='Product Category';
    }
    if(!empty($atts['titlesize'])){
        $fontSize= str_replace("-",".",$atts['titlesize']);
    }
    else{
        $fontSize="1";
    }
    $titleStyle='';
    $html.="<h2 style='font-size:".str_replace("-",".",$fontSize)."rem;line-height:".str_replace("-",".",$fontSize)."rem;".$titleStyle.";' class='mensioObjectTitle'>".$title."</h2>";
    $Breadcrumbs=MensioBreadcrumbs($atts);
    if(!empty($Breadcrumbs)){
        $html.="<hr style='margin-bottom:3px;' />";
        $html.=$Breadcrumbs;
        $html.="<hr style='margin:3px 0 5px;' />";
    }
    else{
        $html.="<hr style='margin:3px 0 5px;' />";
    }
    if (!empty($cat_prods['childcategories'])){
        $subCats=$cat_prods['childcategories'];
        $cols= " col-3";
        if(!empty($atts['cols'])){
            $cols= " col-".$atts['cols'];
        }
        $html.= "<div class='mns-subcategories mns-block".$cols."'>";
        $html.=MensioList($subCats, $atts, "SubCategories", "")."</div>";
    }
    $MensioScript=false;
    if(!empty($_SESSION['MensioPressViewType'])){
        $MensioScript.="
                jQuery(document).ready(function(){";
                if($_SESSION['MensioPressViewType']=="table"){
                    $MensioScript.="jQuery('.mns-block.mns-category').removeClass('show-prods-in-rows');";
                }
                else{
                    $MensioScript.="jQuery('.mns-block.mns-category').addClass('show-prods-in-rows');";
                }
                $MensioScript.="
                });";
    }
    if(!empty($_SESSION['MensioPressViewOrdering'])){
        $MensioScript.="
                jQuery(document).ready(function(){
                    jQuery('.change-sort[sort=".$_SESSION['MensioPressViewOrdering']."]').click();
                });";
    }
    $tt=rand(1,1000);
    wp_enqueue_script("MensioPressPublicJS".$tt,plugin_dir_url(dirname(__FILE__))."js/empty.js");
        wp_add_inline_script( "MensioPressPublicJS".$tt,
               $MensioScript
               );
    $settings=false;
    $settings.='
            <i class="fa fa-list change-view" aria-hidden="true"></i>
            <i class="fa fa-th change-view" aria-hidden="true"></i>
            <div class="MensioSorting">
                <i class="fa fa-sort-alpha-asc change-sort checked" sort="NAME-A"></i>
                <i class="fa fa-sort-alpha-desc change-sort" sort="NAME-Z"></i>
                <i class="fa fa-sort-numeric-asc change-sort" sort="CHEAP"></i>
                <i class="fa fa-sort-numeric-desc change-sort" sort="EXPENSIVE"></i>
                <select name="products-list-sort">
                    <option value="NAME-A">Name Ascending</option>
                    <option value="NAME-Z">Name Descending</option>
                    <option value="CHEAP">Price Ascending</option>
                    <option value="EXPENSIVE">Price Descending</option>
                    <option value="CREATED-A">Newest</option>
                    <option value="CREATED-Z">Oldest</option>
                    <option value="AVAILABILITY">Availability</option>
                </select>
            </div>
            ';
            $get=new mensio_product_filtering();
            $products=$get->ProductSelectionFiltering('',array($cat_id),"price",'','',array(),array(),$atts);
            if(empty($cat_prods['childcategories']) && empty($products['Data'])){
                $html.= "-";
            }
            $mnsContent='
            <div class="mns-content">';
            $o=1;
            $filtersTable=false;
            if(!empty($products)){
                if(empty($atts['fontsize'])){
                    $atts['fontsize']=1;
                }
                $filtersTable.='
                <div class="mns-product-filters mns-filters-closed" style="font-size:'.str_replace("-",".",$atts['fontsize']).'rem;line-height:'.str_replace("-",".",$atts['fontsize']).'rem;">
                ';
                    if(!empty($atts['searchbox']) && $atts['searchbox']=='yes' && empty($cat_prods['childcategories'])){
                        $filtersTable.='<div class="FilterAttributesRow">';
                        $filtersTable.='<div class="FilterAttributesCell">
                                <div class="filter-title" style="margin-top:0;"><span>Search</span></div>
                            </div>
                            <div class="FilterAttributesCell">
                                <div id="category-search">
                                    <div id="mnsSearch"> 
                                        <input type="text" name="search" id="mnsSearch-text">
                                    </div>
                                </div>
                            </div>
                            </div>';
                    }
                    $i=0;
                    $Ar=array();
                    $Object=new mnsFrontEndObject();
                    $get=new mensio_product_filtering();
                    $Result=$get->ProductSelectionFiltering("",array($cat_id),"price",'','',array(),array(),$atts);
                    $min_price=$Result['MinPrice'];
                    $max_price=$Result['MaxPrice'];
                    if(empty($Result['Data'])){
                        $max_price=0;
                        $min_price=0;
                        $maxPages=0;
                    }
                    else{
                        $maxPages=ceil(count($Result['Data'])/10);
                    }
                    foreach($products as $prod){
                        if(empty($prod['uuid'])){
                            continue;
                        }
                        $Product=$Object->mnsFrontEndProduct($prod['uuid']);
                        if($prod['price']){
                            if($Product['final_price']<$min_price){
                                $min_price=floor($Product['final_price']);
                            }
                            if($Product['final_price']>$max_price){
                                $max_price=ceil($Product['final_price']);
                            }
                            $Ar[]=$Product;
                        }
                        if($o==$break_on){
                            break;
                        }
                        $o++;
                        unset($show_this_product);
                    }
                    if($cat_id!=0){
                        $filtra=new mensio_seller();
                        $filtra->Set_CategoryID($cat_id);
                        $CatFilters=$filtra->LoadCategoryFilters();
                    }
                    else{
                        $CatFilters=array();
                    }
                    $tt=rand(1000,9999).time();
                    $filtersTable.='<div class="FilterAttributesRow">';
                    $filtersTable.='<div class="FilterAttributesCell"><div class="filter-title" style="margin-top:0;"><span>Brand</span></div></div>';
                    $filtersTable.='<div class="FilterAttributesCell">
                        <div class="NotFound"></div>
                            <div class="owl-carousel owl-'.$tt.'">
                            ';
                    for($p=1;$p<=1;$p++){
                    foreach($brands as $brand){
                            $checked='';
                            if(
                                    (!empty($_GET['brand']) && !empty($brand['uuid']) && in_array($brand['uuid'], $_GET['brand']) ) || (!empty($_SESSION['mnsCurrentBrand']) && $_SESSION['mnsCurrentBrand']==$brand['uuid']) ||
                                    (!empty($_SESSION['mnsCurrentBrand']) && $_SESSION['mnsCurrentBrand']==$brand['uuid'])
                                )
                            {
                                $checked=" checked";
                            }
                        $filtersTable.= '<label filter="'.MensioEncodeUUID($brand['uuid']).'" class="filter brand-sel check"><span>'.$brand['name'].'</span><span><input type="checkbox" name="brand" value="'.MensioEncodeUUID($brand['uuid']).'"'.$checked.'></span></label>';
                    }
                    }
                    $filtersTable.='</div></div></div>';
                    $filtersTable.='<div class="FilterAttributesRow">';
                    $filtersTable.='<div class="FilterAttributesCell">';
                    $filtersTable.= '
                            <div class="filter-title"><span>Prices</span></div>
                            </div>
                            <div class="FilterAttributesCell">
                            <div class="pricesrange">
                                <input type="tel" name="price-min" value="'.$min_price.'">
                                <input type="tel" name="price-max" value="'.$max_price.'">
                            </div>
                            <div class="price-range" maxprice="'.ceil($max_price).'" minprice="'.$min_price.'"></div>
                            <div id="max-price-to-show"><span class="mensioPrice">'.number_format($min_price,2).'</span> - <span class="mensioPrice">'.number_format($max_price,2).'</span></div>';
                    $filtersTable.='</div></div>';
                            $i=1;
                            $filtValues=array();
                            $filts=array();
                            foreach($CatFilters as $filt){
                                if(empty($filtra->FiltersHTML($filt->value,$filt->name,$filt->uuid,$filt->attribute))){
                                    continue;
                                }
                                if(!in_array($filt->name, $filts)){
                                    if($i!=1){
                                        $filtersTable.="
                                                </div></div></div>";
                                    }
                                    $filts[]=$filt->name;
                                    $filtersTable.='
                                        <div class="FilterAttributesRow">
                                    <div class="FilterAttributesCell">
                                        <div class="filter-title">
                                            <span>'.$filt->finalName.'</span></div>
                                        </div>
                                            <div class="FilterAttributesCell">
                                            <div class="NotFound">&nbsp;</div>
                                            <div class="owl-carousel owl-'.$tt.'">';
                                }
                                $checked='';
                                if(in_array($filt->uuid, $prod_atts['filters'])){
                                    $checked=" checked";
                                }
                                if(!in_array($filtra->FiltersHTML($filt->value,$filt->name,$filt->uuid,$filt->attribute), $filtValues)){
                                    $filtValues[]=$filtra->FiltersHTML($filt->value,$filt->name,$filt->uuid,$filt->attribute);
                                    for($p=1;$p<=1;$p++){
                                        $filtersTable.=$filtra->FiltersHTML($filt->value,$filt->name,$filt->uuid,$filt->attribute);
                                    }
                                }
                                $i++;
                            }
                            if($i!=1){
                                $filtersTable.="</div></div></div>";
                            }
                            wp_enqueue_script("MensioPress-Owl-Carousel",plugin_dir_url( __FILE__ ).'../js/owl.carousel.js');
                            wp_add_inline_script('MensioPress-Owl-Carousel',
                            '
                            jQuery(document).ready(function() {
                                var owl = jQuery(".owl-carousel.owl-'.$tt.'");
                                owl.width( owl.closest(".mns-block").width()-owl.closest(".mns-block").find(".filter-title").width() );
                                owl.owlCarousel({
                                    autoHeight: true,
                                    stagePadding: 0,
                                    nav: false,
                                    autoHeight:true,
                                    autoWidth:true,
                                    margin:0,
                                    loop:false
                                });
                              });'                                    
                                    );
                            $filtersTable.='
                            <!-- Owl Stylesheets -->
                            <link rel="stylesheet" href="'. plugin_dir_url( __FILE__ ).'../css/owl.carousel.min.css">
                            <link rel="stylesheet" href="'. plugin_dir_url( __FILE__ ).'../css/owl.theme.default.min.css">
                            ';
                            $ResetFiltersName="resetFilters";
                    $filtersTable.='
                    <div class="FilterAttributesRow MensioOpenFilters-wrapper">
                        <div class="FilterAttributesCell">
                            <!--<input type="button" value="Reset" class="resetFilters">-->
                            <div class="filter-title '.$ResetFiltersName.'">Reset</div>
                        </div>
                        <button class="MensioOpenFilters">
                            <i class="fa fa-arrow-down" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>';
                    $settings.=$filtersTable;
                    $filtersTable=false;
            }
            if(!empty($products)){
            }
                $mnsCategories='
                <div class="mns-category-products mns-list" atts="'.str_replace('"',"'",json_encode($atts)).'" categoryid="'.MensioEncodeUUID($cat_id).'" totalpages="'.$maxPages.'" page="1" items="10">';
                if(!empty($Result['Data']) && is_array($Result['Data']) && count($Result['Data'])>0){
                    $mnsCategories.= mensiopress_FilterSearch($cat_id,false,"app",$max_price,$atts,$Result);
                }
                $mnsCategories.='
                    </div>';
                if(empty($atts['filters-align']) || (!empty($atts['filters-align']) && $atts['filters-align']=="left")){
                    $mnsContent.=$filtersTable.$mnsCategories;
                }
                elseif(!empty($atts['filters-align']) && $atts['filters-align']=="right"){
                    $mnsContent.=$mnsCategories.$filtersTable;
                }
                $mnsContent.='
                </div>';
                if(!empty($Result['Data']) && is_array($Result['Data']) && count($Result['Data'])>0){
                    $html.=$settings.$mnsContent;
                }
                else{
                    $html.=$mnsContent;
                }
    return $html;
}
add_action('wp_ajax_mensiopress_FilterSearch','mensiopress_FilterSearch' );
add_action('wp_ajax_nopriv_mensiopress_FilterSearch','mensiopress_FilterSearch' );
if(!function_exists("mensiopress_FilterSearch")){
    function mensiopress_FilterSearch($cats=false,$atts=false,$from=false,$maxPrice=0,$attrs=false,$SearchArray=array()){
        if(!empty($_POST) &&
                    (empty($_POST['action']) || (!empty($_POST['action']) && $_POST['action']!="mnsPreviewShortCodes"))
                ){
            $url     = wp_get_referer();
            $url     = str_replace($_POST['mns_lang']."/","",$url);
            $ref     = url_to_postid( $url ); 
            $seller=new mensio_seller();
            $verification=$seller->VerifyPageIntegrity(filter_var($_POST['mns_sec']), "MensioPressFrontEnd-".$ref);
            if($verification==false){
                echo "Unauthorized";
                die;
            }
        }
        $limit=0;
        if(!empty($_SESSION['MensioThemeLangShortcode']) && get_option('MensioAddToCart_'.$_SESSION['MensioThemeLangShortcode'])){
            $GLOBALS['MensioAddToCartText']=get_option('MensioAddToCart_'.$_SESSION['MensioThemeLangShortcode']);
        }
        else{
            $GLOBALS['MensioAddToCartText']="Add&nbsp;to&nbsp;Cart";
        }
        if(!empty($_REQUEST['mns_keyword'])){
            $keyword=stripslashes_deep(filter_var($_REQUEST['mns_keyword']));
        }
        $brands=array();
        if(!empty($_POST['mns_Brands'])){
            $brands=$_POST['mns_Brands'];
        }
        if(!empty($_POST['mns_Cats'])){
            $cats=$_POST['mns_Cats'];
        }
        if(!empty($_POST['mns_Attributes'])){
            $atts=$_POST['mns_Attributes'];
        }
        if(!empty($_POST['mns_Filters'])){
            $filters=array_unique($_POST['mns_Filters']);
        }
        if(!empty($_REQUEST['mns_Page'])){
            $limit=$_REQUEST['mns_Page'];
        }
        if(!empty($_POST['SearchAtts'])){
            $searchAtts=str_replace("'",'"',stripslashes($_POST['SearchAtts']));
            $searchAtts= json_decode($searchAtts,true);
        }
        elseif($attrs==true){
            $searchAtts=$attrs;
        }
        $minPrice=0;
        if(!empty($_POST['MinPrice'])){
            $minPrice=$_POST['MinPrice'];
        }
        if(!empty($_POST['MaxPrice'])){
            $maxPrice=$_POST['MaxPrice'];
        }
        if(empty($_REQUEST['mns_keyword'])){
            $keyword="*";
        }
        $kats=array();
        $seller=new mensio_seller();
        if(!empty($_POST['mns_Cats'])){
            if(is_array($_POST['mns_Cats'])){
                foreach($_POST['mns_Cats'] as $cat){
                    $kats[]= MensioDecodeUUID($cat);
                    foreach($seller->LoadChildCategories(MensioDecodeUUID($cat)) as $kat){
                        $kats[]=$kat->category;
                    }
                }
            }
            else{
                $kats[]= MensioDecodeUUID($_POST['mns_Cats']);
            }
        }
        elseif(empty($_POST['mns_Cats']) && $cats==true){
            $kats[]=$cats;
        }
        if($from=="app"){
            $cats=array($cats);
        }
        if(!empty($atts)){
            foreach($atts as $att){
                $AttrSet=explode(":::",$att);
                $Attrs[$AttrSet[0]][]=MensioDecodeUUID($AttrSet[1]);
            }
        }
        $Attributes=array();
        if(!empty($Attrs)){
            $Attributes=array();
            foreach($Attrs as $key=>$val){
                $Attributes[]=implode("::",$val);
            }
        }
        $html=false;
        $AllowBrands=array();
        $AllowCats=array();
        $pagination=false;
        $Order=false;
        if(!empty($_REQUEST['Order']) && $_REQUEST['Order']=="CHEAP"){
            $Order="`price` ASC";
        }
        if(!empty($_REQUEST['Order']) && $_REQUEST['Order']=="EXPENSIVE"){
            $Order="`price` DESC";
        }
        if(!empty($_REQUEST['Order']) && $_REQUEST['Order']=="CREATED-A"){
            $Order="`created` ASC";
        }
        if(!empty($_REQUEST['Order']) && $_REQUEST['Order']=="CREATED-Z"){
            $Order="`created` DESC";
        }
        if(!empty($_REQUEST['Order']) && $_REQUEST['Order']=="AVAILABILITY"){
            $Order="`stock` DESC";
        }
        $Page=0;
        if(!empty($_REQUEST['Page'])){
            $Page=$_REQUEST['Page'];
            $Page=$Page-1;
        }
        $Items=10;
        if(!empty($_REQUEST['Items'])){
            $Items=$_REQUEST['Items'];
        }
        if(count($SearchArray)==0){
            $get=new mensio_product_filtering();
            $Result=$get->ProductSelectionFiltering($keyword,$kats,"price",'','',$Attributes,array($Order));
        }
        else{
            $Result=$SearchArray;
        }
        if(!empty($_REQUEST['Items'])){
            $Items=filter_var($_REQUEST['Items']);
        }
        else{
            $Items=10;
        }
        if(!empty($_GET['mns_keyword'])){
        }
        $AllowAtts=array();
        $seller=new mensio_seller();
        $seller->Set_CategoryID($cats[0]);
        $CatFilters=$seller->LoadCategoryFilters();
        $Attrs=array();
        foreach($CatFilters as $flt){
            if(!empty($_REQUEST['mns_Attributes'])){
                foreach($_REQUEST['mns_Attributes'] as $att){
                    $attr=explode(":::",$att);
                    if($get->ProductSelectionFiltering($keyword,$kats,"price",'','',array($flt->uuid,MensioDecodeUUID($attr[1])),array($Order))){
                        $AllowAtts[]=$flt->uuid;
                    }
                }
            }
        }
$lg=false;
        $filterMaxPrice=0;
        $filterMinPrice=0;
        $List=array();
        if(!empty($Result['Data'])){
            $seller=new mensio_seller();
            $prods=1;
            foreach($Result['Data'] as $res){
                $res['final_price']=$res['price']-($res['price']*($res['discount']/100));
                $res['final_price']=$res['final_price']+($res['final_price']*($res['tax']/100));
                $res['final_price']= number_format($res['final_price'],2);
                if(!empty($_POST['MinPrice']) && !empty($_POST['MaxPrice'])){
                    if($minPrice>$res['final_price'] || $maxPrice<$res['final_price']){
                        continue;
                    }
                }
                if(!empty($_POST['mns_Brands']) && !in_array(MensioEncodeUUID($res['brand']), $_POST['mns_Brands'])){
                    continue;
                }
                if(!empty($_REQUEST['Order']) && ($_REQUEST['Order']=="NAME-A" || $_REQUEST['Order']=="NAME-Z")){
                    $i=$res['name']."-".$res['uuid'];
                }
                elseif(!empty($_REQUEST['Order']) && ($_REQUEST['Order']=="CHEAP" || $_REQUEST['Order']=="EXPENSIVE")){
                    $i=number_format($res['price'],2)."-".$res['uuid'];
                }
                elseif(!empty($_REQUEST['Order']) && $_REQUEST['Order']=="RATINGS-A"){
                    if(empty($res['reviews'])){
                        continue;
                    }
                    else{
                        $i=$res['reviews']."-".$res['uuid'];
                    }
                }
                else{
                    $i=$res['name']."-".$res['uuid'];
                }
                $getLink=new mnsGetFrontEndLink();
                if(empty($res['name'])){
                    $VarInfo=new mensio_product_filtering();
                    $VarInfo=$VarInfo->GetVariationInfo($res['uuid']);
                    $res['name']=$VarInfo[0]->name;
                    $res['description']=$VarInfo[0]->description;
                    $List[$i]['link']=$getLink->ProductLink($VarInfo[0]->product)."?var=".MensioEncodeUUID($res['uuid']);
                }
                elseif(!empty($res['name']) && MENSIO_FLAVOR=='FREE'){
                    $List[$i]['link']=$getLink->ProductLink($res['uuid']);
                }
                else{
                    continue;
                }
                $seller->Set_UUID($res['uuid']);
                $atts=$seller->LoadProductAttributeValues();
                foreach($atts as $att){
                    $AllowAtts[]=MensioEncodeUUID($att->attribute_value);
                }
                $AllowBrands[]= MensioEncodeUUID($res['brand']);
                $List[$i]['id']=$res['uuid'];
                $List[$i]['brand']=$res['brand'];
                $List[$i]['name']=$res['name'];
                $List[$i]['description']=$res['description'];
                $getThumb=new mnsFrontEndObject();
                $List[$i]['image']= $getThumb->MensioGetThumb(array(300,300), $res['image']);
                $List[$i]['sku']=$res['code'];
                $List[$i]['reviews']=$res['reviews'];
                $List[$i]['discount']=$res['discount'];
                $List[$i]['price']=$res['price'];
                $List[$i]['tax']=$res['tax'];
                $List[$i]['btbprice']=$res['btbprice'];
                $List[$i]['btbtax']=$res['btbtax'];
                $List[$i]['availability']=$res['status_name'];
                $List[$i]['availability-color']=$res['status_color'];
                $List[$i]['availability-icon']=site_url()."/".$res['status_icon'];
                $AllowBrands[]=$res['brand'];
                $prods++;
            }
            if(!empty($_REQUEST['Order']) && $_REQUEST['Order']=="PRICE-A"){
                $Order="`price` ASC";
            }
            $start=1;
            $pagination="<hr /><div class='Pagination'>";
                    if(!empty($_REQUEST['Items']) && $_REQUEST['Items']=="2"){
                    }
                    $checked=false;
                    if(!empty($_REQUEST['Items']) && $_REQUEST['Items']=="5"){
                        $checked=" active";
                    }
                        if(!empty($attrs['pagination-links']) && $attrs['pagination-links']=="yes"){
                            $pagination.= "<span class='ItemShow".$checked."'><a href='//".$_SERVER['HTTP_HOST']. preg_replace("#&Items=.*#", '', $_SERVER['REQUEST_URI'])."&Items=5'>5</a></span>";
                        }
                        else{
                            $pagination.= "<span class='ItemShow".$checked."'>5</span>";
                        }
                    $checked=false;
                    if(empty($_REQUEST['Items']) || (!empty($_REQUEST['Items']) && $_REQUEST['Items']=="10")){
                        $checked=" active";
                    }
                        if(!empty($attrs['pagination-links']) && $attrs['pagination-links']=="yes"){
                            $pagination.= "<span class='ItemShow".$checked."'><a href='//".$_SERVER['HTTP_HOST']. preg_replace("#&Items=.*#", '', $_SERVER['REQUEST_URI'])."&Items=10'>10</a></span>";
                        }
                        else{
                            $pagination.= "<span class='ItemShow".$checked."'>10</span>";
                        }
                    $checked=false;
                    if(!empty($_REQUEST['Items']) && $_REQUEST['Items']=="20"){
                        $checked=" active";
                    }
                        if(!empty($attrs['pagination-links']) && $attrs['pagination-links']=="yes"){
                            $pagination.= "<span class='ItemShow".$checked."'><a href='//".$_SERVER['HTTP_HOST']. preg_replace("#&Items=.*#", '', $_SERVER['REQUEST_URI'])."&Items=20'>20</a></span>";
                        }
                        else{
                            $pagination.= "<span class='ItemShow".$checked."'>20</span>";
                        }
                        if(!empty($_REQUEST['Page'])){
                            $curPage=$_REQUEST['Page'];
                        }
                        else{
                            $curPage=1;
                        }
                    $checked=false;
                    $pagination.= "<ul class='pagination'>"
                    . "<li><i class='fas fa-angle-double-left'></i></li>";
                    if(!empty($_POST['TotalPages'])){
                        $maxPages=$_POST['TotalPages'];
                    }
                    $maxPages=ceil(count($List)/$Items);
                    $style=false;
                    for($i=1;$i<=$maxPages;$i++){
                        $checked=false;
                        if(empty($style) || (!empty($active) && $i>($active+5))){
                        }
                        $classes=" class='";
                        if((!empty($_REQUEST['Page']) && $i==$_REQUEST['Page']) ||
                         (empty($_REQUEST['Page']) && $i==1) ||
                         (!empty($_REQUEST['Page']) && $_REQUEST['Page']=="NaN" && $i==1)
                                ){
                            $classes.=" active";
                            $style=" style='display:inline-block;'";
                            $active=$i;
                        }
                        if(!empty($_REQUEST['Page']) && $i==$_REQUEST['Page']){
                            $active=$i;
                            $start=(($i-1)*$Items);
                        }
                            $style=" style='display:inline-block;'";
                        if(!empty($curPage) && $i>=($curPage-2) && empty($active)){
                            $classes.=" showPagination";
                        }
                        elseif(!empty($curPage) && $i<=($curPage+2) && $active==true){
                            $classes.=" showPagination";
                        }
                        $classes.="'";
                        $pagination.="<li".$classes.$style.">";
                        if(!empty($attrs['pagination-links']) && $attrs['pagination-links']=="yes"){
                            $pagination.="<a href='//".$_SERVER['HTTP_HOST']. preg_replace("#&Page=.*#", '&Page='.$i, $_SERVER['REQUEST_URI'])."'".$classes.">".$i."</a>";
                        }
                        else{
                            $pagination.=$i;
                        }
                        $pagination.="</li>";
                    }
                $pagination.="<li><i class='fas fa-angle-double-right'></i></li>"
                        . "</ul>"
                        . "</div>";
                if(!empty($_REQUEST['Page'])){
                    $Page=$_REQUEST['Page'];
                }
                else{
                    $Page=1;
                }
            $List= array_slice($List, $start ,$Items);
        }
        if(count($List)==0){
            $html.="No Products Found";
        }
        if((!empty($_REQUEST['Order']) &&
                (
                    ($_REQUEST['Order']=="CHEAP") ||
                    ($_REQUEST['Order']=="NAME-A")
                )) || (empty($_REQUEST))){
            ksort($List);
        }
        if((!empty($_REQUEST['Order']) &&
                (
                    ($_REQUEST['Order']=="EXPENSIVE") ||
                    ($_REQUEST['Order']=="NAME-Z") ||
                    ($_REQUEST['Order']=="RATINGS-A")
                ))  || (empty($_REQUEST))){
            krsort($List);
        }
        if(empty($_REQUEST['Order'])){
            ksort($List);
        }
        if(!empty($_POST['SearchAtts'])){
            $atts= json_decode(stripslashes(filter_var($_POST['SearchAtts'])),true);
        }
        if(empty($searchAtts)){
            $searchAtts=false;
        }
        $searchAtts["maxproducts"]=$Items;
        $html.=MensioList($List, $searchAtts, "", false);
        if($from==false){
            echo json_encode(array(
                "Products"=>$html.$pagination,
                "AllowAtts"=>json_encode($AllowAtts),
                "AllowBrands"=>json_encode($AllowBrands),
                "lg"=>$lg
            ));
            die;
        }
        else{
            return $html.$pagination;
        }
    }
}
if(!function_exists("MensioShowProductsInList")){
    function MensioShowProductsInList($prod,$atts=false){
        $mnsContent='
                        <div class="mns-list-item">
                            <a href="'.$prod['link'].'">
                                <img src="'.$prod['image'].'" alt="'.$prod['name'].'">
                                <div class="mns-prod-title MensioProductListName">'.$prod['name'].'</div>
                            ';
                            if(!empty($atts['show-price-with-tax']) && $atts['show-price-with-tax']=="yes" && $prod['discount']>0){
                                $prod['first_price']=number_format($prod['first_price']+($prod['first_price']*($prod['tax']/100)),2);
                                $prod['final_price']=number_format($prod['first_price']-($prod['first_price']*($prod['discount']/100)),2);
                            }
                            if(empty($atts['show-price-with-tax']) && $ar['discount']>0){
                                $prod['first_price']=number_format($prod['first_price'],2);
                                $prod['final_price']=number_format($ar['first_price']-($prod['first_price']*($ar['discount']/100)),2);
                            }
                            else{
                                $prod['first_price']=number_format($prod['first_price'],2);
                                $prod['final_price']= number_format($prod['first_price'],2);
                            }
                            $mnsContent.='
                            <div class="mns-prod-firstprice mensioPrice">'.$prod['first_price'].'</div>';
                            $mnsContent.='
                            <div class="mns-prod-price mensioPrice">'.number_format($prod['final_price'],2).'</div>
                            </a>
                            <div class="add-to-cart" id="product-'.MensioEncodeUUID($prod['id']).'">
                                <input type="button" value="'.$GLOBALS['MensioAddToCartText'].'" class="add-to-cart-button">
                            </div>
                        </div>
                        <hr />
                        ';
        return $mnsContent;
    }
}
add_action('wp_ajax_mensiopress_CategorySearch','mensiopress_CategorySearch' );
add_action('wp_ajax_nopriv_mensiopress_CategorySearch','mensiopress_CategorySearch' );
function mensiopress_CategorySearch($html=false){
    if(!empty($_POST)){
        $url     = wp_get_referer();
        $url     = str_replace($_POST['mns_lang']."/","",$url);
        $ref     = url_to_postid( $url ); 
        $seller=new mensio_seller();
        $verification=$seller->VerifyPageIntegrity(filter_var($_POST['mns_sec']), "MensioPressFrontEnd-".$ref);
        if($verification==false){
            echo "Unauthorized";
            die;
        }
    }
    if(!empty($_SESSION['MensioThemeLangShortcode']) && get_option('MensioAddToCart_'.$_SESSION['MensioThemeLangShortcode'])){
        $GLOBALS['MensioAddToCartText']=get_option('MensioAddToCart_'.$_SESSION['MensioThemeLangShortcode']);
    }
    else{
        $GLOBALS['MensioAddToCartText']="Add&nbsp;to&nbsp;Cart";
    }
    $search=new mensio_seller();
    $search->Set_SearchString(filter_var($_REQUEST['mns_search']));
    $results=$search->GetSearchResults();
    $i=1;
    if(!empty($_REQUEST['mns_atrs'])){
        $attrs=$_REQUEST['mns_atrs'];
    }
    else{
        $attrs=array();
    }
    if($results['Error']==false){
        echo '<h2>Searching for... "'.$_REQUEST['mns_search'].'"</h2>';
            foreach($results['Data']["Products"] as $product){
                $link= new mnsGetFrontEndLink();
                $link=$link->ProductLink($product->uuid);
                $prod=new mensio_products();
                $prod->Set_UUID($product->uuid);
                $attributes=$prod->LoadProductAttributeValues();
                if(!empty($_REQUEST['mns_atrs']) && is_array($_REQUEST['mns_atrs']) && count($_REQUEST['mns_atrs'])>0){
                    $show_this_product=false;
                    foreach($attributes as $att){
                        if(in_array($att->attribute_value, $attrs)){
                            $show_this_product=1;
                        }
                    }
                    if($show_this_product==false){
                        continue; 
                    }
                }
                $price=($product->price+($product->price * $product->tax/100));
                if($product->discount>0){
                    $price=($product->price-($product->price*($product->discount/100)));
                    $price=number_format($price+($price * $product->tax/100 ),2);
                }
                if(!empty($_REQUEST['mns_max_price']) && $price>filter_var($_REQUEST['mns_max_price'])){
                    continue;
                }
                if(empty($key)){
                    $key=false;
                }
                $html.='<div class="mns-list-item">'.$key.'
                            <a href="'.$link.'">
                                <img src="'.site_url()."/".$product->MainImage.'">
                                <div class="mns-prod-title">'.$product->ProductName.'</div>
                                <div class="mns-prod-price mensioPrice">'.number_format($price,2).'</div>
                            </a>
                            <div class="add-to-cart" id="'.MensioEncodeUUID($product->uuid).'">
                                <input type="button" value="'.$GLOBALS['MensioAddToCartText'].'" class="add-to-cart-button">
                            </div>
                        </div>';
                $i++;
            }
            if(count($results['Data']['Products'])==0){
                echo "Nothing found... Try again!";
            }
    }
    echo $html;
    die;
}
add_action('wp_ajax_mensiopress_SaveViewWay','mensiopress_SaveViewWay' );
add_action('wp_ajax_nopriv_mensiopress_SaveViewWay','mensiopress_SaveViewWay' );
function mensiopress_SaveViewWay(){
    if(!empty($_POST)){
        $url     = wp_get_referer();
        $url     = str_replace($_POST['mns_lang']."/","",$url);
        $ref     = url_to_postid( $url ); 
        $seller=new mensio_seller();
        $verification=$seller->VerifyPageIntegrity(filter_var($_POST['mns_sec']), "MensioPressFrontEnd-".$ref);
        if($verification==false){
            echo "0";
            die;
        }
    }
    if(!empty($_REQUEST['view'])){
        $_SESSION['MensioPressViewType']=filter_var($_REQUEST['view']);
    }
    die;
}
add_action('wp_ajax_mensiopress_SaveOrderingWay','mensiopress_SaveOrderingWay' );
add_action('wp_ajax_nopriv_mensiopress_SaveOrderingWay','mensiopress_SaveOrderingWay' );
function mensiopress_SaveOrderingWay(){
    if(!empty($_POST)){
        $url     = wp_get_referer();
        $url     = str_replace($_POST['mns_lang']."/","",$url);
        $ref     = url_to_postid( $url ); 
        $seller=new mensio_seller();
        $verification=$seller->VerifyPageIntegrity(filter_var($_POST['mns_sec']), "MensioPressFrontEnd-".$ref);
        if($verification==false){
            echo "Unauthorized";
            die;
        }
    }
    if(!empty($_REQUEST['DefaultSort'])){
        $_SESSION['MensioPressViewOrdering']=filter_var($_REQUEST['DefaultSort']);
    }
    die;
}