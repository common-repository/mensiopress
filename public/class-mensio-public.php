<?php
if(!is_admin()){
    if(!empty($_SESSION['MensioThemeLangShortcode']) && get_option('MensioAddToCart_'.$_SESSION['MensioThemeLangShortcode'])){
        $GLOBALS['MensioAddToCartText']=get_option('MensioAddToCart_'.$_SESSION['MensioThemeLangShortcode']);
    }
    else{
        $GLOBALS['MensioAddToCartText']="Add&nbsp;to&nbsp;Cart";
    }
    if(get_option("MensioPressGlobalFontSize")){
        $GLOBALS['MensioPressFontSize']=get_option("MensioPressGlobalFontSize");
    }
    else{
        $GLOBALS['MensioPressFontSize']="1";
    }
    if(!empty($_SESSION['MensioThemeLangShortcode']) && get_option('MensioAddToComparisonListText_'.$_SESSION['MensioThemeLangShortcode'])){
        $GLOBALS['MensioAddToComparisonListText']=get_option('MensioAddToComparisonListText_'.$_SESSION['MensioThemeLangShortcode']);
    }
    else{
        $GLOBALS['MensioAddToComparisonListText']="Add&nbsp;to&nbsp;Comparison&nbsp;List";
    }
    add_filter( 'the_content', 'MensioFrontEndShowNonce' );
    function MensioFrontEndShowNonce ($title) {
        global $post;
        $nonce=wp_get_referer();
        $nonce=$post->ID;
        echo "<input type='hidden' id='MensioPressLang' value='".$_SESSION['MensioThemeLangShortcode']."'>";
        wp_create_nonce( "MensioPressFrontEnd-".$nonce );
        wp_nonce_field( "MensioPressFrontEnd-".$nonce ,"MensioPressNonce");
    }
    add_filter( 'pre_get_document_title', 'Mensio_change_page_title' );
    function Mensio_change_page_title ($title) {
        global $post;
        if(empty($post)){
            return false;
        }
        $PostType=$post->post_type;
        if($PostType=="mensio_product"){
            $ttl=$post->post_title;
            $prodID=str_replace(array('[mensioobject uuid="','"]'),"",$post->post_content);
            $getProd=new mnsFrontEndObject();
            $Prod=$getProd->mnsFrontEndProduct($prodID);
            if(!empty($Prod['name'])){
                $ttl=$Prod['name'];
            }
            $title=$ttl." - ". get_bloginfo("name");
        }
        elseif($PostType=="mensio_category"){
            $getUUID=explode('"',$post->post_content);
            $UUID= str_replace('"', '', $getUUID[1]);
            $getTTL=new mensio_seller();
            $getTTL->Set_CategoryID($UUID);
            if($getTTL->TranslateCategory()){
                $ttl=$getTTL->TranslateCategory();
                $ttl=$ttl[0]->name;
            }
            else{
                $ttl=$post->post_title;
            }
            $title=$ttl." - ". get_bloginfo("name");
        }
        return $title;
    }
    add_filter( 'single_post_title', 'Mensio_change_post_title');
    function Mensio_change_post_title ($title) {
        global $post;
        if(empty($post)){
            return false;
        }
        $PostType=$post->post_type;
        if($PostType=="mensio_product"){
            $ttl=$post->post_title;
            $prodID=str_replace(array('[mensioobject uuid="','"]'),"",$post->post_content);
            $getProd=new mnsFrontEndObject();
            $Prod=$getProd->mnsFrontEndProduct($prodID);
            if(!empty($Prod['name'])){
                $ttl=$Prod['name'];
            }
            $title=$ttl;
        }
        elseif($PostType=="mensio_category"){
            $getUUID=explode('"',$post->post_content);
            $UUID= str_replace('"', '', $getUUID[1]);
            $getTTL=new mensio_seller();
            $getTTL->Set_CategoryID($UUID);
            if($getTTL->TranslateCategory()){
                $ttl=$getTTL->TranslateCategory();
                $ttl=$ttl[0]->name;
            }
            else{
                $ttl=$post->post_title;
            }
            $title=$ttl;
        }
        return $title;
    }
    add_filter('the_title','MensioControlEntryTitle',true,true);
    function MensioControlEntryTitle($title,$id=null){
        $post_ID=get_post_meta( $id, '_menu_item_object_id', true );
        if(!$post=get_post($post_ID)){
            $ttl=$title;
            return $ttl;
        }
        $ttl=$title;
        $PostType=$post->post_type;
        if(!empty(get_post_meta($post_ID, "MensioTitles"))){
            $TranslTitle=get_post_meta($post->ID, "MensioTitles");
            $ttl="-";
            $ttl=$post->post_title;
            $TranslTitle=json_decode($TranslTitle[0]);
            $TranslTitle=json_decode($TranslTitle);
            foreach($TranslTitle[0] as $key => $val){
                if(
                        !empty($_SESSION['MensioThemeLangShortcode']) &&
                        $_SESSION['MensioThemeLangShortcode'] == $key
                        ){
                    $ttl=$val;
                }
            }   
        }
        elseif($PostType=="mensio_product"){
            $ttl=$post->post_title;
            $prodID=str_replace(array('[mensioobject uuid="','"]'),"",$post->post_content);
            $getProd=new mnsFrontEndObject();
            $Prod=$getProd->mnsFrontEndProduct($prodID);
            if(!empty($Prod['name'])){
                $ttl=$Prod['name'];
            }
        }
        elseif($PostType=="mensio_category"){
            $getUUID=explode('"',$post->post_content);
            $UUID= str_replace('"', '', $getUUID[1]);
            $getTTL=new mensio_seller();
            $getTTL->Set_CategoryID($UUID);
            if($getTTL->TranslateCategory()){
                $ttl=$getTTL->TranslateCategory();
                $ttl=$ttl[0]->name;
            }
            else{
                $ttl=$post->post_title;
            }
        }
        return $ttl;
    }
    add_filter( 'nav_menu_link_attributes', 'MensioChangeNevMenuLinks', 10, 3 );
    function MensioChangeNevMenuLinks( $atts, $item, $args ) {
        $postID=get_post_meta( $item->ID, '_menu_item_object_id', true );
        $post= get_post($postID);
        if(get_option("page_on_front")==$postID && $_SESSION['MensioThemeLang']!=$_SESSION['MensioDefaultLang']){
            $atts['href']= get_home_url()."/".$_SESSION['MensioThemeLangShortcode']."/action/".$post->post_name;
        }
        if ( !get_option('permalink_structure') && $post->post_type=="mensio_category") {
            $category= (str_replace(array('[mensioobject uuid="','"',']'),"",$post->post_content));
            $createLink=new mnsGetFrontEndLink();
            $atts['href']=$createLink->CategoryLink($category);
        }
        if ( !get_option('permalink_structure') && $post->post_type=="mensio_brand") {
            $brand= (str_replace(array('[mensioobject uuid="','"',']'),"",$post->post_content));
            $createLink=new mnsGetFrontEndLink();
            $atts['href']=$createLink->BrandLink($brand);
        }
        return $atts;
    }
}
if(!empty($_GET['page']) && $_GET['page']=="mensio-logout"){
    $_SESSION['mnsUser']="";
    unset($_SESSION['mnsUser']);
    $tt=rand(1,1000);
    wp_enqueue_script("MensioPressPublicJS".$tt,plugin_dir_url(__FILE__)."js/empty.js");
    $CustomScript= "window.location.href='". site_url()."';";
    wp_add_inline_script( "MensioPressPublicJS".$tt,
           $CustomScript
           );
    die;
}
    add_action("init","MensioPressGetCurrency");
    function MensioPressGetCurrency(){
        $Store = new mensio_store();
        $Data = $Store->LoadStoreData();
        if ((is_array($Data)) && (!empty($Data[0]))) {
          foreach ($Data as $Row) {
            $CurrencyID = $Row->currency;
          }
        }
        $GetSymbol=new mensio_currencies();
        $GetSymbol->Set_UUID($CurrencyID);
        $Currency=$GetSymbol->LoadCurrencyMainData();
        $_SESSION['MensioCurrency']=$Currency[0]->uuid;
        $_SESSION['MensioCurrencyPosition']=$Currency[0]->leftpos;
        $_SESSION['MensioCurrencySymbol']=$Currency[0]->symbol;
        $_SESSION['MensioCurrencyCode']=$Currency[0]->code;
    }
class mensio_Public {
	private $plugin_name;
	private $version;
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
                add_filter('the_content', 'MensioContent');
                function MensioContent() {
                    $page=MensioGetUUIDByPostName();
                    $UUID=$page['uuid'];
                    echo do_shortcode($page['post']->post_content);
                }
                add_filter( 'body_class','MensioPressBodyClasses' );
                function MensioPressBodyClasses( $classes ) {
                    if(empty($_SESSION['mnsUser'])){
                        $classes[] = 'mensio-is-not-logged-in';
                    }
                    else{
                        $classes[] = 'mensio-is-logged-in';
                    }
                    if(!empty($_SESSION['MensioThemeLangShortcode'])){
                        $classes[]="language-".$_SESSION['MensioThemeLangShortcode'];
                    }
                    global $post;
                    if(!empty($post) && !empty($pageType=get_post_meta($post->ID,"mensio_page_function"))){
                        $classes[]="mensio-post-".$pageType[0];
                    }
                    if(get_option("Mensio-hide-nav-links")=="yes"){
                        $classes[]="hide-nav-links";
                    }
                    return $classes;
                }
                add_filter( 'body_class','MensioHideEntryTitle' );
                function MensioHideEntryTitle( $classes ) {
                    if(!empty(get_option("hideentrytitle")) && get_option("hideentrytitle")=="yes"){
                        $classes[]="mnshideentrytitle";
                    }
                    return $classes;
                }
                function MensioPressreplaceLoginWithUsername() {
                    if(!empty($_SESSION['mnsUser']['UserName'])){
                    $CustomScript= 'jQuery("document").ready(function(){
                            jQuery("body.mensio-is-logged-in .menu-item.login_page > a").html("'.$_SESSION['mnsUser']['UserName'].'");
                        });';
                    $tt=rand(1,1000);
                    wp_enqueue_script("MensioPressPublicJS".$tt,plugin_dir_url(__FILE__)."js/empty.js");
                    wp_add_inline_script( "MensioPressPublicJS".$tt,
                           $CustomScript
                           );
                    }
                }
                add_action('wp_head', 'MensioPressreplaceLoginWithUsername');
                if(empty($_SESSION['UserInCountry'])){
                    function MensioPressGetUsersCountry(){
                        $countryCode='data.address.country_code';
                        if(!empty($_GET['testcountry'])){
                            $countryCode='"'.$_GET['testcountry'].'"';
                        }
                        $content= json_decode(file_get_contents("http://api.wipmania.com/json"),true);
                        $countryCode="'".$content['address']['country_code']."'";
                        $CustomScript='
                        jQuery(document).ready(function(){
                            jQuery.ajax({
                                 type: "post",
                                 url: ajaxurl,
                                 data: {
                                   "action": "mensiopress_getUsersCountry",
                                   "CountryCode":'.$countryCode.'
                                 }
                            });
                        });
                        ';
                        $tt=rand(1,1000);
                        wp_enqueue_script("MensioPressPublicJS".$tt,plugin_dir_url(__FILE__)."js/empty.js");
                        wp_add_inline_script( "MensioPressPublicJS".$tt,
                               $CustomScript
                               );
                    }
                    add_action('wp_head', 'MensioPressGetUsersCountry');
                }
                function MensioPressAddMetaDescription($title=false,$descr=false){
                    $keywords=false;
                    global $post;
                    if ( !empty($post) && has_shortcode( $post->post_content, 'mensiohomepage')) {
                        $postID=str_replace(array("[mensiohomepage page='","']"),"",$post->post_content);
                        $post= get_post($postID);
                    }
                    if(!empty($post) && $post->post_type=="mensio_page"){
                        $descr=$post->post_excerpt;
                        $title=$post->post_title;
                    }
                    elseif(!empty($post) && $post->post_type=="mensio_product"){
                        $UUID=str_replace( array('[mensioobject uuid="','"]'),'',$post->post_content);
                        $get=new mnsFrontEndObject();
                        $Product=$get->mnsFrontEndProduct($UUID);
                        $title=$Product['name'];
                        $descr=$Product['description'];
                        if(!empty($Product['tags'])){
                            foreach($Product['tags'] as $tag){
                                $keywords[]=$tag->tags;
                            }
                        }
                    }
                    elseif(!empty($post) && $post->post_type=="mensio_category"){
                        $UUID=str_replace( array('[mensioobject uuid="','"]'),'',$post->post_content);
                        $get=new mnsFrontEndObject();
                        $Category=$get->mnsFrontEndCategoryProducts($UUID);
                        $title=$Category['current_cat_name'];
                        if(empty($descr)){
                            $descr=$post->post_excerpt;
                        }
                    }
                    elseif(!empty($post) && $post->post_type=="mensio_brand"){
                        $UUID=str_replace( array('[mensioobject uuid="','"]'),'',$post->post_content);
                        $get=new mnsFrontEndObject();
                        $Brand=$get->mnsFrontEndBrandProducts($UUID);
                        $title=$Brand['current_brand_name'];
                        $descr=$Brand['current_brand_description'];
                    }
                    else{
                        if(!empty($post->post_title)){
                            $title=$post->post_title;
                        }
                        if(!empty($post->post_excerpt)){
                            $descr=$post->post_excerpt;
                        }
                        if(empty($post->post_excerpt) && !empty($post->post_content)){
                            $descr= str_replace(array("\r", "\n"), '', strip_tags($post->post_content));
                        }
                    }
                    if(strlen($descr)>320){
                        $descr=substr($descr,0,320);
                        if (strpos($descr, '.') !== false){
                            $desc=explode(".",$descr,-1);
                            $descr=$desc[0].".";
                        }
                    }
                    $html='
                    <meta name="title" content="'.$title.' - '. get_bloginfo().'">
                    <meta name="description" content="'.$descr.'">';
                    if(!empty($keywords) && count($keywords)>0){
                    $html.= '
                    <meta name="keywords" value="'.implode(",",$keywords).'">';
                    }
                    echo $html;
                }
                add_action('wp_head', 'MensioPressAddMetaDescription');
                if(get_option( "showMensiotopRightCart")=='show'){
                }
	}
	public function enqueue_styles() {
                wp_enqueue_style('G-material-icons','https://fonts.googleapis.com/icon?family=Material+Icons',array(), $this->version,'all');
		wp_enqueue_style( "Mensio-public", plugin_dir_url( __FILE__ ) . 'css/mensio-public.css', array(), $this->version, 'all' );
		wp_enqueue_style( "ImageViewer", plugin_dir_url( __FILE__ ) . 'css/imageviewer.css', array(), $this->version, 'all' );
                if(MENSIO_FLAVOR=='STD'){
                    wp_enqueue_style( "Mensio-extras", plugin_dir_url( __FILE__ ) . 'css/extras.css', array(), $this->version, 'all' );
                }
		wp_enqueue_style( "Mensio-ion-range-slider", plugin_dir_url( __FILE__ ) . 'css/ion.rangeSlider.css', array(), $this->version, 'all' );
		wp_enqueue_style( "Mensio-ion-range-sliderSkinFlat", plugin_dir_url( __FILE__ ) . 'css/ion.rangeSlider.skinFlat.css', array(), $this->version, 'all' );
                wp_enqueue_style(
                    $this->plugin_name.'-fontawesome',
                    plugin_dir_url( __FILE__ ) . '../admin/css/font-awesome.css',
                    array(), $this->version,
                    'all'
                );
                if(wp_get_theme()=="Twenty Seventeen"){
                    wp_enqueue_style(
                           'MensioPress - TwentySeventeen',
                        plugin_dir_url(__FILE__)."css/twenty-seventeen.css",
                           array(), $this->version,
                           'all'
                       );
                }
	}
	public function enqueue_scripts() {
                wp_enqueue_script( "MensioPressPublicJS", plugin_dir_url( __FILE__ ) . 'js/mensio-public.js', array( 'jquery' ), $this->version, false );
                wp_enqueue_script( $this->plugin_name.'-mensio-extras', plugin_dir_url( __FILE__ ) . 'js/mensio-extras.js', array( 'jquery' ), $this->version, false );
                wp_enqueue_script( $this->plugin_name.'-mensio-ion-range-slider', plugin_dir_url( __FILE__ ) . 'js/ion.rangeSlider.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name.'-mensio-imageViewer', plugin_dir_url( __FILE__ ) . 'js/imageviewer.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name.'-mensio-pagination', plugin_dir_url( __FILE__ ) . 'js/jquery.twbsPagination.min.js', array( 'jquery' ), $this->version, false );
	}
	public function Mensio_Public_Functions() {
	}
}
function MensioPressGlobalStyles() {
    $CustomStyle=false;
    wp_enqueue_style(
        'MensioPressCustomStyle',
        plugin_dir_url(__FILE__) . 'css/mensio-public.css'
    );
    if(!empty($_SESSION['MensioCurrencySymbol'])){
        if(!empty($_SESSION['MensioCurrencyPosition']) && $_SESSION['MensioCurrencyPosition']=="1"){
        $CustomStyle.="
            .mensioPrice:before{
                content:'".$_SESSION['MensioCurrencySymbol']."';
                font-size:smaller;
                margin-right:2px;
            }";
        }
        else{
            $CustomStyle.="
            .mensioPrice:after{
                content:'".$_SESSION['MensioCurrencySymbol']."';
                font-size:smaller;
                margin-left:2px;
            }";
        }
    }
        $CustomStyle.= "div.mns-html-content .add-to-cart input[type=button]{color:";
        if(get_option('MensioCartTextColor')){
            $CustomStyle.= "".get_option('MensioCartTextColor');
        }
        else{
            $CustomStyle.= "#000";
        }
        $CustomStyle.= ";
        background:";
        if(get_option('MensioCartBackgroundColor')){
            $CustomStyle.= "".get_option('MensioCartBackgroundColor');
        }
        else{
            $CustomStyle.= "#ccc";
        }
        $CustomStyle.= ";
        }";
        $CustomStyle.= "div.mns-html-content .add-to-cart input[type=button]:hover{color:";
        if(get_option('MensioHoverCartTextColor')){
            $CustomStyle.= get_option('MensioHoverCartTextColor');
        }
        else{
            $CustomStyle.= "#000";
        }
        $CustomStyle.= ";
        background:";
        if(get_option('MensioHoverCartBackgroundColor')){
            $CustomStyle.= get_option('MensioHoverCartBackgroundColor');
        }
        else{
            $CustomStyle.= "#ccc";
        }
        $CustomStyle.= ";
        }
        ";
        $NotificationTextcolor= get_option("MensioPressNotificationTextcolor");
        if(empty($NotificationTextcolor)){
            $NotificationTextcolor="#ffffff";
        }
        $NotificationBGcolor= get_option("MensioPressNotificationBGcolor");
        if(empty($NotificationBGcolor)){
            $NotificationBGcolor="#000000";
        }
        $NotificationCornerRadius= get_option("MensioPressNotificationCornerRadius");
        if(empty($NotificationCornerRadius)){
            $NotificationCornerRadius="0";
        }
        $CustomStyle.= "
        #MNSMessage{
            color:".$NotificationTextcolor.";
            background-color:".$NotificationBGcolor.";
            border-radius: 0px 0px ".$NotificationCornerRadius."px ".$NotificationCornerRadius."px;
        }
        ";
    wp_add_inline_style( 'MensioPressCustomStyle', $CustomStyle );
}
add_action( 'wp_enqueue_scripts', 'MensioPressGlobalStyles' );
function MensioFooterFunctions(){
    if(!empty($GLOBALS['brand_id'])){
        $ttl = new mnsFrontEndObject();
        $brand=$ttl->mnsFrontEndBrandProducts(MensioDecodeUUID($GLOBALS['brand_id']));
        if(!empty($brand['current_brand_name'])){
            $title=$brand['current_brand_name'];
        }
        $_SESSION['mnsCurrentBrand']= MensioDecodeUUID($_GET['brand']);
    }
    if(!empty($GLOBALS['cat_id'])){
        die;
        $ttl = new mnsFrontEndObject();
        $category=$ttl->mnsFrontEndCategoryProducts(MensioDecodeUUID(filter_var($_GET['category'])),array());
        $title=$category['current_cat_name'];
        $_SESSION['mnsCurrentCategory']=$_GET['category'];
    }
    elseif(!empty($_GET['product'])){
        $make_visit=new mensio_seller();
        $make_visit->Set_VisitID($_SESSION['mnsVisitID']);
        $make_visit->Set_ProductID(MensioDecodeUUID(filter_var($_GET['product'])));
        if(!$make_visit->CheckIfVisitedPageExists()){
            $make_visit->AddtoHistoryVisitedPage();
        }
        $_SESSION['mnsLastSeenProduct']=MensioDecodeUUID(filter_var($_GET['product']));
        $ttl=new mnsFrontEndObject();
        $ttl=$ttl->mnsFrontEndProduct(MensioDecodeUUID(filter_var($_GET['product'])));
        if($ttl){
            $title=$ttl['name'];
        }
    }
    if(!empty($title)){
        echo  "
        <div id='gotit'></div>";
        $CustomScript= "
        jQuery('document').ready(function(){
            jQuery('title,.entry-title').html('".str_replace(array("'",'"'),"",$title)." - ".get_bloginfo("name")."');
        });";
        $tt=rand(1,1000);
        wp_enqueue_script("MensioPressPublicJS".$tt,plugin_dir_url(__FILE__)."js/empty.js");
        wp_add_inline_script( "MensioPressPublicJS".$tt,
               $CustomScript
               );
    }
    $NotificationDuration= get_option("MensioPressNotificationDuration");
    if(empty($NotificationDuration)){
        $NotificationDuration="500";
    }
    $getStore=new mnsFrontEndObject();
    $Store=$getStore->mnsFrontEndStoreData();
    if(!empty($Store['logo'])){
        $storeLogo=$Store['logo'];
        echo
        "<div id='MNSMessage' data-duration='".$NotificationDuration."'></div>"
        ."<div id='MNSAlert'>"
            . "<div id='MNSAlertText'>"
            . "</div>"
        . "</div>";
        $CustomScript=
        "var eshopLogo='".get_site_url()."/".$storeLogo."';";
        $tt=rand(1,1000);
        wp_enqueue_script("MensioPressPublicJS".$tt,plugin_dir_url(__FILE__)."js/empty.js");
        wp_add_inline_script( "MensioPressPublicJS".$tt,
               $CustomScript
               );
    }
        global $post;
$storeAnalytics=false;
    if(!empty($Store['ganalytics']) && $Store['ganalytics']!="NOANALYTICS" && !empty($post->post_type)){
        $storeAnalytics=$Store['ganalytics'];
        $getCartID=new mnsGetFrontEndLink();
        $CartID=$getCartID->CartPageID();
        $postType=$post->post_type;
        if($postType=="mensio_product"){
            $getProduct=new mnsFrontEndObject();
            if(!empty($GLOBALS['UUID'])){
                $Product=$getProduct->mnsFrontEndProduct($GLOBALS['UUID']);
                $storeAnalytics= str_replace("[---itemprice---]", $Product['final_price'], $storeAnalytics);
                $storeAnalytics= str_replace("[---itemtype---]", "product", $storeAnalytics);
                $storeAnalytics= str_replace("[---itemid---]", MensioEncodeUUID($Product['gid']), $storeAnalytics);
            }
        }
        else{
            $storeAnalytics= str_replace("ga('set','dynx_totalvalue','[---itemprice---]');", "", $storeAnalytics);
            $storeAnalytics= str_replace("ga('set','dynx_itemid','[---itemid---]');", "", $storeAnalytics);
            $storeAnalytics= str_replace("ga('set','dynx_pagetype','[---itemtype---]');", "", $storeAnalytics);
        }
        $CustomScript=$storeAnalytics;
        $tt=rand(1,1000);
        wp_enqueue_script("MensioPressPublicJS".$tt,plugin_dir_url(__FILE__)."js/empty.js");
        wp_add_inline_script( "MensioPressPublicJS".$tt,
               $CustomScript
               );
    }
    if(!empty($_SESSION['NewTerms'])){
        echo "
        <div class='MensioPressModalBox'>
            <div class='NewTerms'>
            ".$_SESSION['NewTerms']."
            <div class='userHandlers'>
                <input type='button' value='Agree' class='agree'>
                <input type='button' value='Disagree' class='disagree'>
            </div>
            </div>
        </div>";
    }
}
add_action('get_footer', 'MensioFooterFunctions');
if(!function_exists("MensioGetCartTotal")){
    function MensioGetCartTotal(){
            if(!empty($_SESSION['MensioCart'])){
            $getCart=new mnsFrontEndObject();
            $cart=$getCart->mnsFrontEndCart(false);
            $total=0;
            foreach($cart as $prod){
                $total=$total+($prod['Price']*$prod['Quant']);
            }   
        }
        else{
            $total=0;
        }
        return $total;
    }
}
if(!empty($_GET['MensioVerifyUser'])){
    $userID= MensioDecodeUUID(filter_var($_GET['MensioVerifyUser']));
    $verify=new mensio_seller();
    $verify->Set_Customer($userID);
    $verify->VerifyUser();
}
if(is_admin()){
    add_action("init", "always_show_mensioPagesInMenu");
    function always_show_mensioPagesInMenu(){
        $userID=get_current_user_id();
        $userMeta=get_user_meta( $userID, "metaboxhidden_nav-menus", true );
        if((!empty($userMeta)) && (in_array("add-post-type-mensio_page", $userMeta))){
            $arNum=array_search("add-post-type-mensio_page", $userMeta);
            unset($userMeta[$arNum]);
            update_user_meta($userID, "metaboxhidden_nav-menus", $userMeta);
        }
    }
}
