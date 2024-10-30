<?php
if(is_admin()){
    add_action( 'wp_ajax_mensiopress_Open_Object_Settings_Modal', 'mensiopress_Open_Object_Settings_Modal' );
    add_action( 'wp_ajax_nopriv_mensiopress_Open_Object_Settings_Modal', 'mensiopress_Open_Object_Settings_Modal' );
    add_action( 'wp_ajax_mensiopress_mns_update', 'mensiopress_mns_update' );
    add_action( 'wp_ajax_nopriv_mensiopress_mns_update', 'mensiopress_mns_update' );
    add_action( 'wp_ajax_mensiopress_newMensioPage', 'mensiopress_newMensioPage' );
    add_action( 'wp_ajax_nopriv_mensiopress_newMensioPage', 'mensiopress_newMensioPage' );
    add_action( 'wp_ajax_mensiopress_delMensioPage', 'mensiopress_delMensioPage' );
    add_action( 'wp_ajax_nopriv_mensiopress_delMensioPage', 'mensiopress_delMensioPage' );
    add_action( 'wp_ajax_mensiopress_mnsPreviewShortCodes', 'mensiopress_mnsPreviewShortCodes' );
    add_action( 'wp_ajax_nopriv_mensiopress_mnsPreviewShortCodes', 'mensiopress_mnsPreviewShortCodes' );
    add_action( 'wp_ajax_mensiopress_MensioTheObjects', 'mensiopress_MensioTheObjects' );
    add_action( 'wp_ajax_nopriv_mensiopress_MensioTheObjects', 'mensiopress_MensioTheObjects' );
    add_action( 'wp_ajax_mensiopress_mns_OpenModalBox', 'mensiopress_mns_OpenModalBox' );
    add_action( 'wp_ajax_nopriv_mensiopress_mns_OpenModalBox', 'mensiopress_mns_OpenModalBox' );
    add_action( 'wp_ajax_mensiopress_MensioCheckSlug', 'mensiopress_MensioCheckSlug' );
    add_action( 'wp_ajax_nopriv_mensiopress_MensioCheckSlug', 'mensiopress_MensioCheckSlug' );
    add_action( 'wp_ajax_mensiopress_MensioOpenPage', 'mensiopress_MensioOpenPage' );
    add_action( 'wp_ajax_nopriv_mensiopress_MensioOpenPage', 'mensiopress_MensioOpenPage' );
    add_action( 'wp_ajax_mensiopress_MensioUpdateHomePage', 'mensiopress_MensioUpdateHomePage' );
    add_action( 'wp_ajax_nopriv_mensiopress_MensioUpdateHomePage', 'mensiopress_MensioUpdateHomePage' );
    add_action( 'wp_ajax_mensio_ajax_Table_Pages', 'mensiopress_mensio_ajax_Table_Pages' );
    add_action( 'wp_ajax_nopriv_mensio_ajax_Table_Pages', 'mensiopress_mensio_ajax_Table_Pages' );
}
class Mensio_Admin_Design_Pages_Form extends mensio_core_form {
  public function __construct() {
    if (!defined('WPINC')) { die(); }
    $this->Set_MainTemplate();
    $this->ActivePage = 'PageDesigner';
  }
    public function Load_Page_CSS() {
      wp_enqueue_style(
       MENSIO_PLGTITLE.'-design',
       plugin_dir_url( __FILE__ ) . '../../css/mensio-admin-design-pages.css',
       array(),
       MENSIO_VERSION,
       'all'
      );
    }
    public function Load_Page_JS() {
     wp_enqueue_script(
      MENSIO_PLGTITLE.'-design',
      plugin_dir_url( __FILE__ ) . '../../js/mensio-admin-design-pages.js',
      array( 'jquery' ),
      MENSIO_VERSION,
      false
     );
     wp_enqueue_script(
      MENSIO_PLGTITLE.'-design_all',
      plugin_dir_url( __FILE__ ) . '../../js/mensio-design-pages-all_pages.js',
      array( 'jquery' ),
      MENSIO_VERSION,
      false
     );
    }
}
function mensiopress_mensio_ajax_Table_Pages(){
    $RtrnTable = '';
    if ((defined('WPINC')) && (current_user_can('manage_options'))) {
      $InPage = filter_var($_REQUEST['Page'],FILTER_SANITIZE_STRING);
      $InRows = filter_var($_REQUEST['Rows'],FILTER_SANITIZE_STRING);
      $InSearch = filter_var($_REQUEST['Search'],FILTER_SANITIZE_STRING);
      $InSorter = filter_var($_REQUEST['Sorter'],FILTER_SANITIZE_STRING);
      $JSONData = $_REQUEST['ExtraActions'];
      $Page = new MensioPressPages();
      $RtrnTable = $Page->GetDataTable($InPage,$InRows,$InSorter,$InSearch,$JSONData);
      unset($Page);
    }
    echo $RtrnTable;
    die();
}
function mensiopress_mns_update() {
    $check=explode("::",$_POST['Security']);
    $check=$check[0];
    if(empty($_POST['Security']) || wp_verify_nonce($check,"Active_Page_PageDesigner")==false){
        die;
    }
    if(!is_admin()){
        die;
    }
    $titles=filter_var($_POST['post_title']);
    $my_post = array(
        'ID'           => filter_var($_POST['post_id']),
        'post_title'   => $titles,
        'post_content' => stripslashes(filter_var($_POST['content']))
    );
    if(!empty($_POST['post_slug'])){
        $my_post['post_name']=filter_var($_POST['post_slug']);
    }
    if(!empty($_POST['post_excerpt'])){
        $my_post['post_excerpt']=filter_var($_POST['post_excerpt']);
    }
    wp_update_post( $my_post );
    if(!empty(filter_var($_POST['page_function']))){
        if ( ! add_post_meta( filter_var($_POST['post_id']), 'mensio_page_function', filter_var($_POST['page_function']), true ) ) { 
           update_post_meta ( filter_var($_POST['post_id']), 'mensio_page_function', filter_var($_POST['page_function']) );
        }
    }
    if ( ! add_post_meta(filter_var($_POST['post_id']), "MensioTitles", filter_var($_POST['post_titles']),true) ) { 
       update_post_meta ( filter_var($_POST['post_id']), 'MensioTitles', filter_var($_POST['post_titles']) );
    }
    if ( ! add_post_meta(filter_var($_POST['post_id']), "MensioDescriptions", filter_var($_POST['post_descriptions']),true) ) { 
       update_post_meta ( filter_var($_POST['post_id']), 'MensioDescriptions', filter_var($_POST['post_descriptions']) );
    }
    die;
}
function mensiopress_mnsPreviewShortCodes(){
    $check=explode("::",$_POST['Security']);
    $check=$check[0];
    if(empty($_POST['Security']) || wp_verify_nonce($check,"Active_Page_PageDesigner")==false){
    }
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
    echo do_shortcode(strip_tags(stripslashes('['.$_REQUEST['previewShortCode'].']')));
    die;
}
if(!function_exists("mensiopress_MensioTheObjects")){
    function mensiopress_MensioTheObjects($MensioBottomButtons){
    if(!empty($_POST['Security'])){
        $check=explode("::",$_POST['Security']);
        $check=$check[0];
        if(empty($_POST['Security']) || wp_verify_nonce($check,"Active_Page_PageDesigner")==false){
            die;
        }
    }
        $post_id= filter_var($_REQUEST['post']);
        $post=get_post($post_id);
        if(!empty($_REQUEST['post'])){
            $post=get_post(filter_var($_REQUEST['post']));
            $current_title=$post->post_title;
            $current_content=$post->post_content;
        }
        $objects=mnsObjects();
        $page_types=mensio_page_types();
        $CustomScript='
            function create_object(d){
                var elem_num=$("div.mns-html-content *").length+1;
                var elem;
                ';
                foreach($objects as $obj){
                    $CustomScript.= '
                if(d==="'.$obj['id'].'"){
                    elem="<div class=\"mns-block mns-'.$obj['id'].' col-4 mns-preview mns-elem="+elem_num+" \"><div class=\"mns-obj-to-rem\"><i class=\"fa fa-close\"></i></div>[mns_'.$obj['id'].']</div>";
                }
                ';
                }
                $CustomScript.= '
                return elem;
            }
            ';
            wp_enqueue_script(
                    'MensioPressCreateObject',
                    plugin_dir_url( __FILE__ ) . '../../js/custom.js',
                    array(),
                    '1.0' );
            wp_add_inline_script( 'MensioPressCreateObject',
                    $CustomScript
                    );
        $Layout='<div id="mnsPageEditorLogo"></div>
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
    ';
        return $Layout;
    }
}
function MensioTheEdits($MensioBottomButtons){
    $post_id=filter_var($_REQUEST['post']);
    $post=get_post($post_id);
    $objects=mnsObjects();
    $page_types=mensio_page_types();
    $current_title=$post->post_title;
    $current_descr=$post->post_excerpt;
    $current_slug=$post->post_name;
    $current_content=$post->post_content;
    $LangTitles= get_post_meta($post_id, "MensioTitles");
    $CustomScript=false;
    $CustomScript.='
        function editSettings(element) {
            $("#mns_settings > *").css("display","none");
            var object=$(".mns-block[mns-elem="+$("#edit-object").val()+"]");
            ';
            foreach($objects as $obj){
                if($obj=="html_block"){
                }
            $CustomScript.= '
            if(object.hasClass("mns-'.$obj['id'].'")){
                $("#edit-'.$obj['id'].'").scrollTop(0);
                $(".close-properties").click(function(){
                    $("#edit-'.$obj['id'].'").scrollTop(0);
                });
                $("#edit-'.$obj['id'].'").css("display","table");
                $("#edit-'.$obj['id'].' input[name=maxbrands]").val( object.attr("maxbrands") );
                $("#edit-'.$obj['id'].' input[name=maxcats]").val( object.attr("maxcategories") );
                if(object.attr("maxproducts")){
                    $("#edit-'.$obj['id'].' input[name=maxprods]").val( object.attr("maxproducts") );
                }
                else{
                    $("#edit-'.$obj['id'].' input[name=maxprods]").val( "6" );
                }
                if(!object.hasClass("no-filters")){
                    $("#edit-'.$obj['id'].' input[name=show-filters]").attr("checked","true");
                }
                $("#edit-'.$obj['id'].' input[name=show-filters]").change(function(){
                    if($(this).prop("checked")==false){
                        object.addClass("no-filters");
                    }
                    else{
                        object.removeClass("no-filters");
                    }
                });
                if(!object.attr("filters-align")){
                    $("#edit-'.$obj['id'].' input[name=show-product-brand]").attr("checked","true");
                }
                $("#edit-'.$obj['id'].' input[name=show-product-brand]").change(function(){
                    object.attr("show-brands","yes");
                });
                if(!object.attr("filters-align")){
                    $("#edit-'.$obj['id'].' select[name=filters-align]").attr("checked","true");
                }
                $("#edit-'.$obj['id'].' select[name=filters-align]").change(function(){
                    object.attr("filters-align",$(this).val());
                });
                if(object.attr("box-border-image")){
                    $("#edit-'.$obj['id'].' .box-border-image").val( object.attr("box-border-image") );
                }
                else{
                    $("#edit-'.$obj['id'].' .box-border-image").val( "0" );
                }
                $(".box-border-image").on("change",function(){
                    if($(this).val()!=""){
                        object.attr("box-border-image",$(this).val());
                        if(object.attr("box-border-image-color")){
                            clr=object.attr("box-border-image-color");
                        }
                        else{
                            clr="000";
                        }
                    }
                });
                if(object.attr("box-border-image-color")){
                    $("#edit-'.$obj['id'].' input[name=box-border-image-color]")
                        .attr("value",""+ object.attr("box-border-image-color") ).css("background-color",object.attr("box-border-image-color"))
                        .closest(".wp-picker-container").find("button").css("background",object.attr("box-border-image-color"));
                }
                else{
                    $("#edit-'.$obj['id'].' input[name=box-border-image-color]")
                        .closest(".my-color-field-wrapper").find("button").addClass("undefinedColor").html("Undefined");
                }
                $("input[name=box-border-image-color]").on("blur change keyup",function(){
                    object.attr("box-border-image-color",$(this).val() );
                });
                object.find(".mns-prod-category").mouseover(function(){
                });
                object.find(".mns-prod-category").mouseleave(function(){
                });
                if(object.attr("box-border")){
                    $("#edit-'.$obj['id'].' .box-border").val( object.attr("box-border") );
                }
                else{
                    $("#edit-'.$obj['id'].' .box-border").val( "0" );
                }
                $(".box-border").on("change",function(){
                    if($(this).val()!=""){
                        object.attr("box-border",$(this).val());
                    }
                });
                if(object.attr("box-border-hover")){
                    $("#edit-'.$obj['id'].' .box-border-hover").val( object.attr("box-border-hover") );
                }
                else{
                    $("#edit-'.$obj['id'].' .box-border-hover").val( "0" );
                }
                $(".box-border-hover").on("change",function(){
                    if($(this).val()!=""){
                        object.attr("box-border-hover",$(this).val());
                    }
                });
                if(object.attr("box-border-image-hover")){
                    $("#edit-'.$obj['id'].' .box-border-image-hover").val( object.attr("box-border-image-hover") );
                }
                else{
                    $("#edit-'.$obj['id'].' .box-border-image-hover").val( "0" );
                }
                $(".box-border-image-hover").on("change",function(){
                    if($(this).val()!=""){
                        object.attr("box-border-image-hover",$(this).val());
                    }
                });
                if(object.attr("border-c")){
                    $("#edit-'.$obj['id'].' input[name=border-c]").val( object.attr("border-c") )
                        .closest(".wp-picker-container").find(".wp-color-result").removeClass("undefinedColor")
                        .html(object.attr("border-c"))
                        .css("background-color",object.attr("border-c"));
                }
                else{
                    $("#edit-'.$obj['id'].' input[name=border-c]").val( "#000000" )
                        .closest(".wp-picker-container").find(".wp-color-result").addClass("undefinedColor");
                }
                if(object.attr("background-color")){
                    $("#edit-'.$obj['id'].' input[name=background-color]").val( object.attr("background-color") )
                        .closest(".wp-picker-container").find(".wp-color-result").removeClass("undefinedColor")
                        .html(object.attr("background-color"))
                        .css("background-color",object.attr("background-color"));
                }
                else{
                    $("#edit-'.$obj['id'].' input[name=background-color]").val( "#000000" )
                        .closest(".wp-picker-container").find(".wp-color-result").addClass("undefinedColor");
                }
                if(object.attr("box-border-image-color")){
                    $("#edit-'.$obj['id'].' input[name=box-border-image-color]").val( object.attr("box-border-image-color") )
                        .closest(".wp-picker-container").find(".wp-color-result").removeClass("undefinedColor")
                        .html(object.attr("box-border-image-color"))
                        .css("background-color",object.attr("box-border-image-color"));
                }
                else{
                    $("#edit-'.$obj['id'].' input[name=text-color]").val( "#000000" )
                        .closest(".wp-picker-container").find(".wp-color-result").addClass("undefinedColor");
                }
                if(object.attr("active-link-color")){
                    $("#edit-'.$obj['id'].' input[name=active-link-color]").val( object.attr("active-link-color") )
                        .closest(".wp-picker-container").find(".wp-color-result").removeClass("undefinedColor")
                        .html(object.attr("active-link-color"))
                        .css("background-color",object.attr("active-link-color"));
                }
                else{
                    $("#edit-'.$obj['id'].' input[name=text-color]").val( "#000000" )
                        .closest(".wp-picker-container").find(".wp-color-result").addClass("undefinedColor");
                }
                if(object.attr("text-color")){
                    $("#edit-'.$obj['id'].' input[name=text-color]").val( object.attr("text-color") )
                        .closest(".wp-picker-container").find(".wp-color-result").removeClass("undefinedColor")
                        .html(object.attr("text-color"))
                        .css("background-color",object.attr("text-color"));
                }
                else{
                    $("#edit-'.$obj['id'].' input[name=text-color]").val( "#000000" )
                        .closest(".wp-picker-container").find(".wp-color-result").addClass("undefinedColor");
                }
                if(object.attr("box-border-image-hover-color")){
                    $("#edit-'.$obj['id'].' input[name=box-border-image-hover-color]").val( object.attr("box-border-image-hover-color") )
                        .closest(".wp-picker-container").find(".wp-color-result").removeClass("undefinedColor")
                        .html(object.attr("box-border-image-hover-color"))
                        .css("background-color",object.attr("box-border-image-hover-color"));
                }
                else{
                    $("#edit-'.$obj['id'].' input[name=box-border-image-hover-color]").val( "#000000" )
                        .closest(".wp-picker-container").find(".wp-color-result").addClass("undefinedColor");
                }
                if(object.attr("box-border-color")){
                    $("#edit-'.$obj['id'].' input[name=box-border-color]").val( object.attr("box-border-color") )
                        .closest(".wp-picker-container").find(".wp-color-result").removeClass("undefinedColor")
                        .html(object.attr("box-border-color"))
                        .css("background-color",object.attr("box-border-color"));
                }
                else{
                    $("#edit-'.$obj['id'].' input[name=box-border-color]").val( "#000000" )
                        .closest(".wp-picker-container").find(".wp-color-result").addClass("undefinedColor");
                }
                if(object.attr("box-border-hover-color")){
                    $("#edit-'.$obj['id'].' input[name=box-border-hover-color]").val( object.attr("box-border-hover-color") )
                        .closest(".wp-picker-container").find(".wp-color-result").removeClass("undefinedColor")
                        .html(object.attr("box-border-hover-color"))
                        .css("background-color",object.attr("box-border-hover-color"));
                }
                else{
                    $("#edit-'.$obj['id'].' input[name=box-border-hover-color]").val( "#000000" )
                        .closest(".wp-picker-container").find(".wp-color-result").addClass("undefinedColor");
                }
                if(object.attr("pricecolor")){
                    $("#edit-'.$obj['id'].' input[name=priceColor]").val( object.attr("pricecolor") )
                        .closest(".wp-picker-container").find(".wp-color-result").removeClass("undefinedColor")
                        .html(object.attr("pricecolor"))
                        .css("background-color",object.attr("pricecolor"));
                }
                else{
                    $("#edit-'.$obj['id'].' input[name=priceColor]").val( "#000000" )
                        .closest(".wp-picker-container").find(".wp-color-result").addClass("undefinedColor");
                }
                if(object.hasClass("hide-barcode")){
                    $("#edit-'.$obj['id'].' input[name=hide-barcode]").attr("checked","true");
                }
                $("#edit-'.$obj['id'].' input[name=hide-barcode]").change(function(){
                    if($(this).prop("checked")==true){
                        object.addClass("hide-barcode");
                    }
                    else{
                        object.removeClass("hide-barcode");
                    }
                });
                if(object.hasClass("showmensiolistbuttonsonmouseover")){
                    $("#edit-'.$obj['id'].' input[name=showmensiolistbuttonsonmouseover]").attr("checked","true");
                }
                $("#edit-'.$obj['id'].' input[name=showmensiolistbuttonsonmouseover]").change(function(){
                    if($(this).prop("checked")==true){
                        object.addClass("showmensiolistbuttonsonmouseover");
                    }
                    else{
                        object.removeClass("showmensiolistbuttonsonmouseover");
                    }
                });
                if(object.hasClass("hide-sku")){
                    $("#edit-'.$obj['id'].' input[name=hide-sku]").attr("checked","true");
                }
                $("#edit-'.$obj['id'].' input[name=hide-sku]").change(function(){
                    if($(this).prop("checked")==true){
                        object.addClass("hide-sku");
                    }
                    else{
                        object.removeClass("hide-sku");
                    }
                });
                if(object.attr("product-names-lines")){
                    $("#edit-'.$obj['id'].' select[name=product-names-lines]").val( object.attr("product-names-lines") );
                }
                else{
                    $("#edit-'.$obj['id'].' select[name=product-names-lines]").val( "2" );
                }
                $("#edit-'.$obj['id'].' select[name=product-names-lines]").change(function(){
                    object.attr("product-names-lines", $(this).val()  );
                });
                if(object.hasClass("hide-add-to-comparison-button")){
                    $("#edit-'.$obj['id'].' input[name=hide-add-to-comparison-button]").attr("checked","true")
                        .parent().find(".add-to-comparison-controls").hide();
                }
                $("#edit-'.$obj['id'].' input[name=hide-add-to-comparison-button]").change(function(){
                    if($(this).prop("checked")==true){
                        object.addClass("hide-add-to-comparison-button");
                        $(this).parent().find(".add-to-comparison-controls").hide();
                    }
                    else{
                        $(this).parent().find(".add-to-comparison-controls").show();
                        object.removeClass("hide-add-to-comparison-button");
                    }
                });
                if(object.hasClass("hide-attributes")){
                    $("#edit-'.$obj['id'].' input[name=hide-attributes]").attr("checked","true");
                }
                $("#edit-'.$obj['id'].' input[name=hide-attributes]").change(function(){
                    if($(this).prop("checked")==true){
                        object.addClass("hide-attributes");
                    }
                    else{
                        object.removeClass("hide-attributes");
                    }
                });
                if(object.hasClass("hide-add-to-favorites")){
                    $("#edit-'.$obj['id'].' input[name=hide-add-to-favorites-button]").attr("checked","true")
                        .parent().find(".add-to-favorites-controls").hide();
                }
                $("#edit-'.$obj['id'].' input[name=hide-add-to-favorites-button]").change(function(){
                    if($(this).prop("checked")==true){
                        object.addClass("hide-add-to-favorites-button");
                        $(this).parent().find(".add-to-favorites-controls").hide();
                    }
                    else{
                        $(this).parent().find(".add-to-favorites-controls").show();
                        object.removeClass("hide-add-to-favorites-button");
                    }
                });
                if(object.attr("add-to-cart-text-size")){
                    $("#edit-'.$obj['id'].' select[name=addToCartSize]").val( object.attr("add-to-cart-text-size") );
                }
                $("#edit-'.$obj['id'].' select[name=addToCartSize]").change(function(){
                    object.attr("add-to-cart-cart-size", $(this).val()  );
                });
                if(object.hasClass("showaddtocartonmouseover")){
                    $("#edit-'.$obj['id'].' input[name=showaddtocartonmouseover]").attr("checked","true")
                        .parent().find(".add-to-cart-controls").hide();
                }
                $("#edit-'.$obj['id'].' input[name=showaddtocartonmouseover]").change(function(){
                    if($(this).prop("checked")==true){
                        object.addClass("showaddtocartonmouseover");
                    }
                    else{
                        object.removeClass("showaddtocartonmouseover");
                    }
                });
                if(object.hasClass("addtocartbold")){
                    $("#edit-'.$obj['id'].' input[name=addtocartbold]").attr("checked","true")
                        .parent().find(".add-to-cart-controls").hide();
                }
                $("#edit-'.$obj['id'].' input[name=addtocartbold]").change(function(){
                    if($(this).prop("checked")==true){
                        object.addClass("addtocartbold");
                    }
                    else{
                        object.removeClass("addtocartbold");
                    }
                });
                if(object.hasClass("addtocartitalic")){
                    $("#edit-'.$obj['id'].' input[name=addtocartitalic]").attr("checked","true")
                        .parent().find(".add-to-cart-controls").hide();
                }
                $("#edit-'.$obj['id'].' input[name=addtocartitalic]").change(function(){
                    if($(this).prop("checked")==true){
                        object.addClass("addtocartitalic");
                    }
                    else{
                        object.removeClass("addtocartitalic");
                    }
                });
                if(object.hasClass("addtocartunderline")){
                    $("#edit-'.$obj['id'].' input[name=addtocartunderline]").attr("checked","true")
                        .parent().find(".add-to-cart-controls").hide();
                }
                $("#edit-'.$obj['id'].' input[name=addtocartunderline]").change(function(){
                    if($(this).prop("checked")==true){
                        object.addClass("addtocartunderline");
                    }
                    else{
                        object.removeClass("addtocartunderline");
                    }
                });
                if(object.hasClass("hide-add-to-cart")){
                    $("#edit-'.$obj['id'].' input[name=hide-add-to-cart]").attr("checked","true")
                        .parent().find(".add-to-cart-controls").hide();
                }
                $("#edit-'.$obj['id'].' input[name=hide-add-to-cart]").change(function(){
                    if($(this).prop("checked")==true){
                        object.addClass("hide-add-to-cart");
                        $(this).parent().find(".add-to-cart-controls").hide();
                    }
                    else{
                        $(this).parent().find(".add-to-cart-controls").show();
                        object.removeClass("hide-add-to-cart");
                    }
                });
                if(object.css("margin-top")){
                    $("#edit-'.$obj['id'].' input[name=margin-top]").val(object.css("margin-top").replace("px",""));
                }
                if(object.css("margin-bottom")){
                    $("#edit-'.$obj['id'].' input[name=margin-bottom]").val(object.css("margin-bottom").replace("px",""));
                }
                if(object.hasClass("hide-categories")){
                    $("#edit-'.$obj['id'].' input[name=hide-categories]").attr("checked","true");
                }
                $("#edit-'.$obj['id'].' input[name=hide-categories]").change(function(){
                    if($(this).prop("checked")==true){
                        object.addClass("hide-categories");
                    }
                    else{
                        object.removeClass("hide-categories");
                    }
                });
                if(object.attr("mnsText")){
                    $("#edit-'.$obj['id'].' textarea[name=mnsText]").val( object.attr("mnsText") );
                }
                if(object.attr("ratingType")){
                    $("#edit-'.$obj['id'].' select[name=ratingType]").val( object.attr("ratingType") );
                }
                $("#edit-'.$obj['id'].' select[name=ratingType]").change(function(){
                    object.attr("ratingType", $(this).val() );
                });
                if(object.css("border-top-width") && object.attr("style")){
                    $("select[name=border-w]").val( object.css("border-bottom-width").replace("px","") );
                }
                else{
                    $("#border-w option:first").attr("selected",true);
                }
                if(object.attr("ordering")){
                    $("select[name=Ordering] option[value="+object.attr("ordering")+"]").prop("selected",true);
                    object.find("div[ordering]").hide();
                    object.find("div[ordering="+object.attr("ordering")+"]").show();
                }
                else{
                    $("select[name=Ordering] option").first().prop("selected",true);
                }
                $("select[name=Ordering]").change(function(){
                    object.attr("ordering",$(this).val());
                    object.find("div[ordering]").hide();
                    object.find("div[ordering="+$(this).val()+"]").show();
                });
                $("#width-50").prop("checked",false);
                if(object.hasClass("width-50")){
                    $("#width-50").prop("checked",true);
                }
                if(object.attr("category-box-border-color")){
                    $("#edit-'.$obj['id'].' #category-box-border-color").val( object.attr("category-box-border-color") ).css("background-color", ""+object.attr("category-box-border-color") );
                }
                if(object.attr("contact_inputs")){
                    var contact_inputs=object.attr("contact_inputs").split(",");
                    $.each( contact_inputs ,function(val,res){
                        $(".contact-inputs.contact_input-"+res).prop("checked",true);
                    });
                    $("#edit-contact .contact-inputs").each(function(){
                    });
                    $("#edit-contact .contact-inputs").change(function(){
                    });
                }
                if(!object.attr("titlesize")){
                }
                else{
                    $("#edit-'.$obj['id'].' .titleSizeSel option[value="+object.attr("titlesize")+"]").attr("selected","true");
                }
                if(object.attr("title-bold")){
                    $("#edit-'.$obj['id'].' .title-bold").prop("checked",true);
                }
                else{
                    $("#edit-'.$obj['id'].' .title-bold").prop("checked",false);
                }
                $("#edit-'.$obj['id'].' .title-bold").change(function(){
                    if($(this).prop("checked")){
                        object.attr("title-bold","1");
                    }
                    else{
                        object.attr("title-bold","");
                    }
                });
                if(object.attr("title-italics")){
                    $("#edit-'.$obj['id'].' .title-italics").prop("checked",true);
                }
                else{
                    $("#edit-'.$obj['id'].' .title-italics").prop("checked",false);
                }
                $("#edit-'.$obj['id'].' .title-italics").change(function(){
                    if($(this).prop("checked")){
                        object.attr("title-italics","1");
                    }
                    else{
                        object.attr("title-italics","");
                    }
                });
                if(object.attr("title-underline")){
                    $("#edit-'.$obj['id'].' .title-underline").prop("checked",true);
                }
                else{
                    $("#edit-'.$obj['id'].' .title-underline").prop("checked",false);
                }
                $("#edit-'.$obj['id'].' .title-underline").change(function(){
                    if($(this).prop("checked")){
                        object.attr("title-underline","1");
                    }
                    else{
                        object.attr("title-underline","");
                    }
                });
                if(!object.attr("title-align")){
                    $(".titleAlignSel option[value=left]").attr("selected",true);
                }
                else{
                    $(".titleAlignSel option[value="+object.attr("title-align")+"]").attr("selected",true);
                }
                $("#edit-'.$obj['id'].' .titleAlignSel").change(function(){
                    object.attr("title-align",$(this).val());
                });
                if(!object.attr("textalign") ){
                    $("#edit-'.$obj['id'].' select[name=text-align] option").prop("selected",false);
                    if(object.find(".mns-list-item-new").length > 0){
                        $("#edit-'.$obj['id'].' select[name=text-align] option[value=center]").prop("selected",true);
                    }
                    else{
                        $("#edit-'.$obj['id'].' select[name=text-align] option[value=left]").prop("selected",true);
                    }
                }
                else{
                    $("#edit-'.$obj['id'].' #edit-text-align").val( object.attr("textalign") );
                }
                if(object.hasClass("col-2")){
                    $("#edit-'.$obj['id'].' .edit-col-2").attr("selected","true");
                }
                if(object.hasClass("col-3")){
                    $("#edit-'.$obj['id'].' .edit-col-3").attr("selected","true");
                }
                if(object.hasClass("col-4")){
                    $("#edit-'.$obj['id'].' .edit-col-4").attr("selected","true");
                }
                if(object.hasClass("col-5")){
                    $("#edit-'.$obj['id'].' .edit-col-5").attr("selected","true");
                }
                if(object.hasClass("col-6")){
                    $("#edit-'.$obj['id'].' .edit-col-6").attr("selected","true");
                }
                if(object.hasClass("col-8")){
                    $("#edit-'.$obj['id'].' .edit-col-8").attr("selected","true");
                }
                if((!object.hasClass("col-2")) && (!object.hasClass("col-3")) && (!object.hasClass("col-4")) && (!object.hasClass("col-5")) && (!object.hasClass("col-6")) && (!object.hasClass("col-8"))){
                    $("#edit-'.$obj['id'].' .edit-col-3").attr("selected","true");
                }
                $("#edit-'.$obj['id'].' select[name=edit-col]").change(function(){
                   object.removeClass("col-2").removeClass("col-3").removeClass("col-4").removeClass("col-5").removeClass("col-6").removeClass("col-8")
                   .addClass("col-"+$(this).val());
                });
                ';
                $langs=new mensio_languages();
                $langs=$langs->GetActiveLanguages();
                foreach($langs as $lang){
                $CustomScript.='
                    $("#edit-'.$obj['id'].' input[name=title-'.$lang->code.']").val( object.attr("mensio-block-title-'.$lang->code.'") );
                    $("#edit-'.$obj['id'].' input[name=title-'.$lang->code.']").on("keyup change",function(){
                        object.attr("mensio-block-title-'.$lang->code.'", $(this).val() );
                    });';
                }
                $CustomScript.='
                if(object.css("border-radius")){
                    $("#edit-'.$obj['id'].' #border-r").val( object.css("border-top-left-radius").replace("px","") );
                }
                else{
                    $("#edit-'.$obj['id'].' #border-r").val( "1" );
                }';
                $langs=new mensio_languages();
                $langs=$langs->GetActiveLanguages();
                $o=0;
                foreach($langs as $lang){
                $CustomScript.='
                    $("#edit-'.$obj['id'].' textarea[name=mnsText_'.$lang->code.']").val( object.attr("mnsText_'.$lang->code.'") );
                    $("#edit-'.$obj['id'].' textarea[name=mnsText_'.$lang->code.']").on("keyup change",function(){
                        object.attr("mnsText_'.$lang->code.'", $(this).val() )';
                        if($o==0){
                            $CustomScript.='
                            .find(".mnsText").html( $(this).val() );';
                        }
                        else{
                            $CustomScript.=';';
                        }
                        $CustomScript.='
                    });';
                $o++;
                }
                $CustomScript.='
                if(object.css("border-radius")){
                    $("#edit-'.$obj['id'].' #border-r").val( object.css("border-top-left-radius").replace("px","") );
                }
                else{
                    $("#edit-'.$obj['id'].' #border-r").val( "1" );
                }
                $("#edit-'.$obj['id'].' input[name=maxcats]").on("keyup change",function(){
                    object.attr("maxcategories", $(this).val() );
                });
                $("#edit-'.$obj['id'].' input[name=maxbrands]").on("keyup change",function(){
                    object.attr("maxbrands", $(this).val() );
                });
                $("#edit-'.$obj['id'].' input[name=maxprods]").on("keyup change",function(){
                    object.attr("maxproducts", $(this).val() );
                });
                $("#edit-'.$obj['id'].' .HowToDisplay").val( object.attr("display") );
                $("#edit-'.$obj['id'].' .MnsCustomSelect.ElementDisplay .category[opt=simple]").addClass("active").css("display","block");
                if(!object.attr("display")){
                    $("#edit-'.$obj['id'].' .HowToDisplay option").first().attr("selected",true);
                }
                if(object.attr("display")=="carousel"){
                    $("#edit-'.$obj['id'].' .HowToDisplay option[value=carousel]").attr("selected","selected");
                    $("#edit-'.$obj['id'].' .MnsCustomSelect.ElementDisplay .category").css("display","none");
                    $("#edit-'.$obj['id'].' .MnsCustomSelect.ElementDisplay .category[opt=carousel]").addClass("active").css("display","block");
                    $("#edit-'.$obj['id'].' .CarouselOptions").show();
                }
                else{
                    $("#edit-'.$obj['id'].' .HowToDisplay").val("simple");
                    $("#edit-'.$obj['id'].' .CarouselOptions").hide();
                }
                $("#edit-'.$obj['id'].' .HowToDisplay").change(function(){
                    if($(this).val()=="carousel"){
                        object.attr("display","carousel");
                        object.find(".CarouselDisplay").show();
                        object.find(".SimpleDisplay").hide();
                    }
                    else{
                        object.attr("display","simple");
                        object.find(".CarouselDisplay").hide();
                        object.find(".SimpleDisplay").show();
                        $(".CarouselOptions").hide();
                    }
                });
                if(object.hasClass("carousel-autoplay")){
                    $("#edit-'.$obj['id'].' .carousel-autoplay").prop("checked",true);
                    $("#edit-'.$obj['id'].' .carousel-autoplay-options").removeClass("closed");
                }
                if(object.hasClass("carousel-HoverPause")){
                    $("#edit-'.$obj['id'].' .carousel-hoverpause").prop("checked",true);
                }
                if(object.attr("carouselautoplaytime")){
                    $("#edit-'.$obj['id'].' .autoplayTime").val(object.attr("carouselautoplaytime"));
                }
                if(object.hasClass("show-prods-in-rows")){
                    $("#edit-'.$obj['id'].' .show-prods-in-rows").attr("checked","true");
                }
                $("#edit-'.$obj['id'].' .show-prods-in-rows").change(function(){
                    object.removeClass("show-prods-in-rows");
                    if($(this).prop("checked")==true){
                        object.addClass("show-prods-in-rows");
                    }
                });
                if(object.hasClass("hide-title")){
                    $("#edit-'.$obj['id'].' .hide-title").attr("checked","true");
                }
                $("#edit-'.$obj['id'].' .hide-title").change(function(){
                    object.removeClass("hide-title");
                    if($(this).prop("checked")==true){
                        object.addClass("hide-title");
                    }
                });
                if(object.hasClass("hide-tags")){
                    $("#edit-'.$obj['id'].' .hide-tags").attr("checked","true");
                }
                $("#edit-'.$obj['id'].' .hide-tags").change(function(){
                    object.removeClass("hide-tags");
                    if($(this).prop("checked")==true){
                        object.addClass("hide-tags");
                    }
                });
                if(object.attr("imagebordercolors")){
                    $("#edit-'.$obj['id'].' .image-border-colors").attr("checked","true");
                }
                $("#edit-'.$obj['id'].' .image-border-colors").change(function(){
                    object.attr("imagebordercolors","");
                    if($(this).prop("checked")==true){
                        object.attr("imagebordercolors","yes");
                    }
                });
                if(object.hasClass("hide-quantity")){
                    $("#edit-'.$obj['id'].' .hide-quantity").attr("checked","true");
                }
                $("#edit-'.$obj['id'].' .hide-quantity").change(function(){
                    object.removeClass("hide-quantity");
                    if($(this).prop("checked")==true){
                        object.addClass("hide-quantity");
                    }
                });
                if(object.hasClass("hide-sku")){
                    $("#edit-'.$obj['id'].' .hide-sku").attr("checked","true");
                }
                $("#edit-'.$obj['id'].' .hide-sku").change(function(){
                    object.removeClass("hide-sku");
                    if($(this).prop("checked")==true){
                        object.addClass("hide-sku");
                    }
                });
                if(object.hasClass("hide-availability")){
                    $("#edit-'.$obj['id'].' .hide-availability").attr("checked","true");
                }
                $("#edit-'.$obj['id'].' .hide-availability").change(function(){
                    object.removeClass("hide-availability");
                    if($(this).prop("checked")==true){
                        object.addClass("hide-availability");
                    }
                });
                if(object.hasClass("round-images")){
                    $("#edit-'.$obj['id'].' .round-images").prop("checked",true);
                }
                $("#edit-'.$obj['id'].' .round-images").change(function(){
                    object.removeClass("round-images");
                    if($(this).prop("checked")==true){
                        object.addClass("round-images");
                    }
                });
                if(object.hasClass("hide-breadcrumbs")){
                    $("#edit-'.$obj['id'].' .hide-breadcrumbs").prop("checked",true);
                }
                $("#edit-'.$obj['id'].' .hide-breadcrumbs").change(function(){
                    object.removeClass("hide-breadcrumbs");
                    if($(this).prop("checked")==true){
                        object.addClass("hide-breadcrumbs");
                    }
                });
                if(object.attr("fontsize")){
                    var fontSize=object.attr("fontsize");
                    if($("select[name=text-size] option[value="+fontSize+"]").length>0){
                        $("select[name=text-size] option[value="+fontSize+"]").attr("selected","true");
                    }
                    else{
                        $("select[name=text-size] option[value="+$("select[name=text-size]").attr("font-size").replace(".","-")+"]").attr("selected","true");
                    }
                }
                else{
                    $("select[name=text-size] option[value="+$("select[name=text-size]").attr("font-size").replace(".","-")+"]").attr("selected","true");
                }
                if(object.hasClass("shareInSocial")){
                    $("#edit-'.$obj['id'].' .share").val("Yes");
                }
                $("#edit-'.$obj['id'].' .share").change(function(){
                    object.removeClass("shareInSocial");
                    if($(this).val()=="Yes"){
                        object.addClass("shareInSocial");
                    }
                });
                if(object.hasClass("has-search-box")){
                    $("#edit-'.$obj['id'].' .add-search-box").attr("checked","true");
                }
                if(object.hasClass("show-timesSold")){
                    $("#edit-'.$obj['id'].' .show-timesSold").attr("checked","true");
                }
                $("#edit-'.$obj['id'].' .add-search-box").change(function(){
                    object.removeClass("has-search-box");
                    if($(this).val()=="1"){
                        object.addClass("has-search-box");
                    }
                });
                $("#edit-'.$obj['id'].' .show-timesSold").change(function(){
                    object.removeClass("show-timesSold");
                    if($(this).prop("checked")==true){
                        object.addClass("show-timesSold");
                    }
                });
                if(object.hasClass("show-timesRated")){
                    $("#edit-'.$obj['id'].' .show-timesRated").attr("checked","true");
                }
                $("#edit-'.$obj['id'].' .show-timesRated").change(function(){
                    object.removeClass("show-timesRated");
                    if($(this).prop("checked")==true){
                        object.addClass("show-timesRated");
                    }
                });
                $("#edit-'.$obj['id'].' .show-what-prices").hide();
                if(object.hasClass("show-prices")){
                    $("#edit-'.$obj['id'].' .show-prices").attr("checked","true");
                    $("#edit-'.$obj['id'].' .show-prices").parent().find("label").show();
                    $("#edit-'.$obj['id'].' .show-what-prices").show();
                }
                $("#edit-'.$obj['id'].' .show-prices").change(function(){
                    object.removeClass("show-prices");
                    object.removeClass("show-firstprices");
                    object.removeClass("show-price-with-tax");
                    $("label[for="+ $("#edit-'.$obj['id'].' .show-firstprices").attr("id")+"]").css("display","none");
                    $("label[for="+ $("#edit-'.$obj['id'].' .show-price-with-tax").attr("id")+"]").css("display","none");
                    if($(this).prop("checked")==true){
                        object.addClass("show-prices");
                        $("label[for="+ $("#edit-'.$obj['id'].' .show-firstprices").attr("id")+"]").css("display","block");
                        $("label[for="+ $("#edit-'.$obj['id'].' .show-price-with-tax").attr("id")+"]").css("display","block");
                        $("#edit-'.$obj['id'].' .show-what-prices").show();
                    }
                    else{
                        $("label[for="+ $("#edit-'.$obj['id'].' .show-firstprices").attr("id")+"]").parent().find("input[type=checkbox]").prop("checked",false);
                        $("label[for="+ $("#edit-'.$obj['id'].' .show-price-with-tax").attr("id")+"]").parent().find("input[type=checkbox]").prop("checked",false);
                        $("#edit-'.$obj['id'].' .show-what-prices").hide();
                    }
                });
                if(object.hasClass("show-price-with-tax")){
                    $("#edit-'.$obj['id'].' .show-price-with-tax").attr("checked","true");
                    $("#edit-'.$obj['id'].' .show-price-with-tax").parent().find("label").show();
                }
                $("#edit-'.$obj['id'].' .show-price-with-tax").change(function(){
                    object.removeClass("show-price-with-tax");
                    if($(this).prop("checked")==true){
                        object.addClass("show-price-with-tax");
                    }
                });
                if(object.hasClass("show-firstprices")){
                    $("#edit-'.$obj['id'].' .show-firstprices").attr("checked","true");
                }
                $("#edit-'.$obj['id'].' .show-firstprices").change(function(){
                    object.removeClass("show-firstprices");
                    if($(this).prop("checked")==true){
                        object.addClass("show-firstprices");
                    }
                });
                if(object.hasClass("show-discounts")){
                    $("#edit-'.$obj['id'].' .show-discount").attr("checked","true");
                }
                else{
                    $(".DiscountProperties").hide();
                }
                $("#edit-'.$obj['id'].' .show-discount").change(function(){
                    $(".DiscountProperties").hide();
                    object.removeClass("show-discounts");
                    if($(this).prop("checked")==true){
                        object.addClass("show-discounts");
                        $(".DiscountProperties").show();
                    }
                });
                if(object.attr("discount-background-color")){
                    var Color=object.attr("discount-background-color");
                    $(".mnsObjectProperties .DiscountProperties input[name=discount-background-color]").val( Color )
                    .closest(".wp-picker-container").find(".wp-color-result").html( Color )
                    .css("background-color",Color);
                }
                if(object.attr("discount-text-color")){
                    var Color=object.attr("discount-text-color");
                    $(".mnsObjectProperties .DiscountProperties input[name=discount-text-color]").val( Color )
                    .closest(".wp-picker-container").find(".wp-color-result").html( Color )
                    .css("background-color",Color);
                }
                if(object.attr("of-brand")){
                    $("#edit-'.$obj['id'].' .of_brand option[value="+object.attr("of-brand")+"]").attr("selected",true);
                }
                $("#edit-'.$obj['id'].' .of_brand").change(function(){
                    object.attr("of-brand", $(this).val() );
                });
                if(object.attr("of-category")){
                    $("#edit-'.$obj['id'].' .of_category option[value="+object.attr("of-category")+"]").attr("selected",true);
                }
                $("#edit-'.$obj['id'].' .of_category").change(function(){
                    object.attr("of-category", $(this).val() );
                });
                if(object.attr("contact_inputs")==true){
                    var cont_inputs=object.attr("contact_inputs").split(",");
                    $.each( cont_inputs ,function(index,value){
                        $(".contact_input-"+value).prop("checked",true);
                    });
                }
                $("#edit-'.$obj['id'].' .contact-inputs").change(function(){
                    var contact_inputs="";
                    $("#edit-'.$obj['id'].' .contact-inputs").each(function(){
                        if($(this).prop("checked")==true){
                            contact_inputs=contact_inputs+$(this).val()+",";
                        }
                    });
                    object.attr("contact_inputs",contact_inputs);
                });
            }';
            }
            $CustomScript.= '
        }
        function replace_content_with_shortcodes(Action,Block=false) {
        ';
        foreach($objects as $obj){
                $CustomScript.= '
                $("#thePage div.mns-block.mns-'.$obj['id'].'").each(function(){
                    var atts="";
                    if($(this).attr("pricecolor")){
                        atts+="     pricecolor=\""   +$(this).attr("pricecolor") +   "\" ";
                    }
                    if($(this).attr("product-names-lines")){
                        atts+="     product-names-lines=\""   +$(this).attr("product-names-lines") +   "\" ";
                    }
                    if($(this).attr("add-to-cart-text-size")){
                        atts+="     add-to-cart-text-size=\""   +$(this).attr("add-to-cart-text-size") +   "\" ";
                    }
                    if($(this).attr("show-brands")){
                        atts+="     show-brands=\""   +$(this).attr("show-brands") +   "\" ";
                    }
                    if($(this).attr("filters-align")){
                        atts+="     filters-align=\""   +$(this).attr("filters-align") +   "\" ";
                    }
                    if($(this).attr("mnsText")){
                        atts+="     text=\""   +$(this).find(".mnsText").text() +   "\" ";
                    }
                    if($(this).attr("box-border-image")){
                        atts+="     box-border-image=\""   + $(this).attr("box-border-image") +   "\" ";
                    }
                    if($(this).attr("box-border-image-color")){
                        atts+="     box-border-image-color=\""   + $(this).attr("box-border-image-color") +   "\" ";
                    }
                    if($(this).attr("box-border-image-hover")){
                        atts+="     box-border-image-hover=\""   + $(this).attr("box-border-image-hover") +   "\" ";
                    }
                    if($(this).attr("box-border-image-hover-color")){
                        atts+="     box-border-image-hover-color=\""   + $(this).attr("box-border-image-hover-color") +   "\" ";
                    }
                    if($(this).attr("box-border")){
                        atts+="     box-border=\""   + $(this).attr("box-border") +   "\" ";
                    }
                    if($(this).attr("box-border-hover")){
                        atts+="     box-border-hover=\""   + $(this).attr("box-border-hover") +   "\" ";
                    }
                    if($(this).attr("box-border-color")){
                        atts+="     box-border-color=\""   + $(this).attr("box-border-color") +   "\" ";
                    }
                    if($(this).attr("box-border-hover-color")){
                        atts+="     box-border-hover-color=\""   + $(this).attr("box-border-hover-color") +   "\" ";
                    }
                    if($(this).attr("customhtml")){
                        atts+="     text=\""   +$(this).attr("customhtml") +   "\" ";
                    }
                    ';
                    $langs=new mensio_languages();
                    $langs=$langs->GetActiveLanguages();
                    foreach($langs as $lang){
                    $CustomScript.='
                        if($(this).attr("mensio-block-title-'.$lang->code.'")){
                            atts+="     title-'.$lang->code.'=\""   +$(this).attr("mensio-block-title-'.$lang->code.'") +   "\" ";
                        }
                        if($(this).attr("mnsText_'.$lang->code.'")){
                            atts+="     mnsText_'.$lang->code.'=\""   +encodeURI($(this).attr("mnsText_'.$lang->code.'")) +   "\" ";
                        }';
                    }
                    $CustomScript.='
                    if($(this).attr("titlecolor")){
                        atts+="     titleColor=\""   +$(this).attr("titlecolor") +   "\" ";
                    }
                    if($(this).css("color")){
                        atts+="     textColor=\""   +$(this).css("color") +   "\" ";
                    }
                    if($(this).attr("navigationnextlabel")){
                        atts+="     navigationnextlabel=\""   +$(this).attr("navigationnextlabel") +   "\" ";
                    }
                    if($(this).attr("navigationpreviouslabel")){
                        atts+="     navigationpreviouslabel=\""   +$(this).attr("navigationpreviouslabel") +   "\" ";
                    }
                    if($(this).attr("titlesize")){
                        atts+="     titleSize=\""   +$(this).attr("titlesize") +   "\" ";
                    }
                    if($(this).attr("maxproducts")){
                        atts+="     maxproducts=\""   +$(this).attr("maxproducts") +   "\" ";
                    }
                    if($(this).attr("maxcategories")){
                        atts+="     maxcategories=\""   +$(this).attr("maxcategories") +   "\" ";
                    }
                    if($(this).attr("maxbrands")){
                        atts+="     maxbrands=\""   +$(this).attr("maxbrands") +   "\" ";
                    }
                    if($(this).hasClass("has-search-box")){
                        atts+="     searchbox=\"yes\" ";
                    }
                    if($(this).hasClass("shareInSocial")){
                        atts+="     share=\"yes\" ";
                    }
                    if($(this).hasClass("show-timesSold")){
                        atts+="     showTimesSold=\"yes\" ";
                    }
                    if($(this).hasClass("show-timesRated")){
                        atts+="     showTimesRated=\"yes\" ";
                    }
                    if($(this).hasClass("hide-barcode")){
                        atts+="     hide-barcode=\"yes\" ";
                    }
                    if($(this).hasClass("hide-add-to-comparison-button")){
                        atts+="     hide-add-to-comparison-button=\"yes\" ";
                    }
                    if($(this).hasClass("hide-add-to-favorites-button")){
                        atts+="     hide-add-to-favorites-button=\"yes\" ";
                    }
                    if($(this).hasClass("show-prices")){
                        atts+="     showPrices=\"yes\" ";
                    }
                    if($(this).hasClass("show-firstprices")){
                        atts+="     showfirstprices=\"yes\" ";
                    }
                    if($(this).hasClass("show-price-with-tax")){
                        atts+="     show-price-with-tax=\"yes\" ";
                    }
                    if($(this).hasClass("show-discounts")){
                        atts+="     showdiscounts=\"yes\" ";
                    }
                    if($(this).attr("discount-background-color")){
                        atts+="     discount-background-color=\""   +$(this).attr("discount-background-color") +   "\" ";
                    }
                    if($(this).attr("discount-text-color")){
                        atts+="     discount-text-color=\""   +$(this).attr("discount-text-color") +   "\" ";
                    }
                    if($(this).attr("active-link-color")){
                        atts+="     active-link-color=\""   +$(this).attr("active-link-color") +   "\" ";
                    }
                    if($(this).attr("discountbold")){
                        atts+="     discountbold=\""   +$(this).attr("discountbold") +   "\" ";
                    }
                    if($(this).attr("of-brand")){
                        atts+="     ofBrand=\""   +$(this).attr("of-brand") +   "\" ";
                    }
                    if($(this).attr("of-category")){
                        atts+="     ofCategory=\""   +$(this).attr("of-category") +   "\" ";
                    }
                    if($(this).attr("display")=="carousel"){
                        atts+="     display=\"carousel\" ";
                    }
                    if($(this).hasClass("carousel-autoplay")==true){
                        atts+="     carouselautoplay=\"yes\" ";
                    }
                    if($(this).attr("carouselautoplaytime")){
                        atts+="     carouselautoplayTime=\""+$(this).attr("carouselautoplaytime")+"\" ";
                    }
                    if($(this).hasClass("carousel-HoverPause")==true){
                        atts+="     carouselHoverPause=\"yes\" ";
                    }
                    if($(this).hasClass("show-prods-in-rows")){
                        atts+="     ShowProds=\"rows\" ";
                    }
                    if($(this).hasClass("hide-title")){
                        atts+="     show-title=\"no\" ";
                    }
                    if($(this).hasClass("hide-sku")){
                        atts+="     hide-sku=\"yes\" ";
                    }
                    if($(this).hasClass("hide-availability")){
                        atts+="     hide-availability=\"yes\" ";
                    }
                    if($(this).attr("imagebordercolors")=="yes"){
                        atts+="     imagebordercolors=\"yes\" ";
                    }
                    if($(this).attr("fontSize")){
                        atts+="     fontSize=\""+$(this).attr("fontSize")+"\" ";
                    }
                    if($(this).attr("ratingType")){
                        atts+="     ratingType=\""+$(this).attr("ratingType")+"\" ";
                    }
                    if($(this).attr("contact_inputs")){
                        atts+="     contact_inputs=\""
                        var i=1;
                        var cont_inputs=$(this).attr("contact_inputs").split(",");
                        $.each(cont_inputs,function(index,value){
                                atts+=value;
                                if(i!=cont_inputs.length){
                                    atts+=",";
                                }
                            i++;
                        });
                        atts+="\" ";
                    }
                    if($(this).hasClass("col-2")){
                        atts+="     cols=\"2\" ";
                    }
                    if($(this).hasClass("col-3")){
                        atts+="     cols=\"3\" ";
                    }
                    if($(this).hasClass("col-4")){
                        atts+="     cols=\"4\" ";
                    }
                    if($(this).hasClass("col-5")){
                        atts+="     cols=\"5\" ";
                    }
                    if($(this).hasClass("col-6")){
                        atts+="     cols=\"6\" ";
                    }
                    if($(this).hasClass("col-8")){
                        atts+="     cols=\"8\" ";
                    }
                    if($(this).attr("editing")=="yes"){
                        atts+="     editing=\"yes\" ";
                    }
                    if($(this).attr("ordering")){
                        atts+="     ordering=\""+$(this).attr("ordering")+"\" ";
                    }
                    var shortcode = "mns_'.$obj['id'].'"+atts;
                    if(Action=="onPage"){
                        $(this).html("["+shortcode+"]");
                    }
                    else if(Action=="Preview" && Block==$(this).attr("mns-elem")){
                        $("#preview-shortcode").val( shortcode );
                    }
                });';
            }
            $CustomScript.= '
        }
        function create_object(d){
            var elem_num=$("div.mns-html-content *").length+1;
            var elem;
            ';
            foreach($objects as $obj){
                $CustomScript.= '
            if(d==="'.$obj['id'].'"){
                elem="<div class=\"mns-block mns-'.$obj['id'].' col-4 mns-preview mns-elem="+elem_num+" \"><div class=\"mns-obj-to-rem\"><i class=\"fa fa-close\"></i></div>[mns_'.$obj['id'].']</div>";
            }
            ';
            }
            $CustomScript.= '
            return elem;
        }
';
   wp_enqueue_script(
           'MensioPressFrontEndEditObjects',
           plugin_dir_url( __FILE__ ) . '../../js/custom.js',
           array(),
           '1.0' );
   wp_add_inline_script( 'MensioPressFrontEndEditObjects',
           $CustomScript
           );
   $Layout=false;
        $post_id= filter_var($_REQUEST['post']);
        $post_meta=get_post_meta( $post_id, 'mensio_page_function');
        foreach($page_types as $type){
            if((!empty($post_meta)) && ($post_meta[0]==$type['id'])){
                $pgFunction=$type['id'];
            }
        }
        $Layout.='
        <div id="currentObjects">
            <div id="page_title_div" class="settings-row">
            ';
            $langs=new mensio_languages();
            $langs=$langs->GetActiveLanguages();
            $MensioTitles= get_post_meta($post_id, "MensioTitles");
            if(!empty($MensioTitles)){
                $MensioTitles= json_decode($MensioTitles[0],true);
                $MensioTitles=json_decode($MensioTitles);
                foreach($MensioTitles[0] as $key => $val){
                    $MensioTitles[$key]=$val;
                }
            }
            $MensioDescriptions= get_post_meta($post_id, "MensioDescriptions");
            if(!empty($MensioDescriptions)){
                $MensioDescriptions= json_decode($MensioDescriptions[0],true);
                $MensioDescriptions=json_decode($MensioDescriptions);
                foreach($MensioDescriptions[0] as $key => $val){
                    $MensioDescriptions[$key]=$val;
                }
            }
            $Layout.='
                <div class="settings-cell">
                    Title 
                </div>';
            $i=0;
            foreach($langs as $lang){
                if(!empty($MensioTitles[$lang->code])){
                    $thisTitle=$MensioTitles[$lang->code];
                }
                else{
                    $thisTitle=$current_title;
                }
                if(!empty($MensioDescrs[$lang->code])){
                    $thisDescr=$MensioDescrs[$lang->code];
                }
                else{
                    $thisDescr=$current_descr;
                }
                $Layout.='
                <div style="position:relative;" class="page-meta">
                    <div class="settings-cell flag" style="display:table-cell;">
                        <img src="'.plugin_dir_url(__FILE__).'../../icons/flags/'.$lang->icon.'.png" width="30" />
                    </div>
                    <div class="settings-cell PageTitle" style="display:table-cell;padding-bottom:4px;">
                        <input type="text" value="'.$thisTitle.'" name="page_title" lang="'.$lang->code.'" />
                    </div>
                    <div class="page-description">
                    </div>
                    <div class="addMetaDescr select">
                        <div class="descr">Add Description</div>
                        <textarea placeholder="Page Description" name="page_description" lang="'.$lang->code.'"></textarea>
                    </div>
                </div>
                ';
                $i++;
            }
            $Layout.='
                <div class="settings-cell">
                    Page Slug
                </div>
                <div class="settings-cell">
                    <input type="text" value="'.$current_slug.'" name="page_slug" id="page_slug" data-slug="'.$current_slug.'">
                    <div id="slugStatus"></div>
                </div>
            </div>
            <div id="pg_function" class="settings-row">
                <div class="settings-cell">Page Function:</div>
                <div class="settings-cell">
                    <div class="select">
                        <select id="NewMensioPageFunction">';
                            $selected="";
                            foreach($page_types as $type){
                                if((!empty($post_meta)) && ($post_meta[0]==$type['id'])){
                                    $selected=" selected";
                                    $pgFunction=$type['id'];
                                }
                                $Layout.='<option value="'.$type['id'].'"'.$selected.'>'. $type['description'].'</option>';
                                $selected="";
                            }
                            $Layout.='
                        </select>
                    </div>
                </div>
            </div>
            <div id="seeAsBrand" class="settings-row" style="display:none;">
                <div class="settings-cell">See as Brand:</div>
                <div class="settings-cell">
                    <select class="see_as_brand" name="see_as_brand">';
                    $getBrands=new mensio_products_brands();
                    $getBrands->Set_Sorter("name");
                    $Brands=$getBrands->LoadProductBrandsDataSet();
                    foreach($Brands as $brand){
                        $sel="";
                        if(!empty($_GET['brand']) && $_GET['brand']== MensioEncodeUUID($brand->uuid)){
                            $sel=" selected";
                        }
                        $Layout.='<option value="'.MensioEncodeUUID($brand->uuid).'"'.$sel.'>'.$brand->name.'</option>';
                    }
                    $Layout.='
                    </select>
                </div>
            </div>
            <div id="seeAsCategory" class="settings-row" style="display:none;">
                <div class="settings-cell">See as Category:</div>
                <div class="settings-cell">
                    <select class="see_as_category" name="see_as_category">
                    <option></option>';
                    $getCategories=new mensio_products_categories();
                    $Categories=$getCategories->LoadProductCategoriesDataSet();
                    foreach($Categories as $category){
                        $sel="";
                        if(!empty($_GET['category']) && $_GET['category']== MensioEncodeUUID($category->uuid)){
                            $sel=" selected";
                        }
                        $Layout.='<option value="'.MensioEncodeUUID($category->uuid).'"'.$sel.'>'.$category->name.'</option>';
                    }
                    $Layout.='
                    </select>
                </div>
            </div>
            <div id="currentBlocks" class="settings-row">Current Blocks</div>
            <div id="tempHTML"></div>
        </div>
<div id="theEditsProperties">
    <!--
    <input type="button" value="<< Back" class="backToEdits">
    -->
    <div id="mnsEditsTitle"></div>';
    $Layout.= mensio_ajax_Open_Object_Settings_ModalForm();
    $Layout.='
<br /><br /><br /><br />
<br /><br /><br /><br />
<br /><br /><br /><br />
<br /><br /><br /><br />
</div>';
    return $Layout;
}
if(!function_exists("mnsObjects")){
    function mnsObjects(){
    $objects=array(
       'blank'=>array(
           "id"=>"blank",
           "object"=>"Blank",
           "explain"=>false,
           "atts"=>array("margins")
           ),
       'text_block'=>array(
           "id"=>"text_block",
           "object"=>"Rich Text",
           "explain"=>false,
           "atts"=>array("text","text-size","bg-color","border")
           ),
       'brand'=>array(
           "id"=>"brand",
           "object"=>"Brand",
           "explain"=>false,
           "atts"=>array("title","imageBorderColor","text-size","bg-color","border")
           ),
       'brands'=>array(
           "id"=>"brands",
           "object"=>"Brands",
           "explain"=>false,
           "atts"=>array("display","ordering","title","text-size","bg-color",
                "imageBorderColor","box-border","border")
           ),
       'categories'=>array(
           "id"=>"categories",
           "object"=>"Categories",
           "explain"=>false,
           "atts"=>array("title","display","text-size",
               "bg-color","box-border","border")
           ),
       'category'=>array(
           "id"=>"category",
           "object"=>"Products",
           "explain"=>false,
           "atts"=>array("show_prods_in_rows","hide_breadcrumbs",
               "round_images","mensio-list-product-controls","show_prices",
               "show_product_brand","search","box-border","text-size","bg-color","border")
           ),
       'search'=>array(
           "id"=>"search",
           "object"=>"Search",
           "explain"=>false,
           "atts"=>array("show_discount","show_prices","product-names-lines",
               "text-size",
               "mensio-list-product-controls","box-border",
               "imageBorderColor","round_images",
               "text-size","bg-color","border")
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
       'cart'=>array(
           "id"=>"cart",
           "object"=>"Cart",
           "explain"=>false,
           "atts"=>array("title","text-size","bg-color","border")
           ),
       'login'=>array(
           "id"=>"login",
           "object"=>"Login",
           "explain"=>false,
           "atts"=>array("title","text-size","bg-color","border")
           ),
       'signup'=>array(
           "id"=>"signup",
           "object"=>"Signup",
           "explain"=>false,
           "atts"=>array("title","text-size","bg-color","border")
           ),
       'contact'=>array(
           "id"=>"contact",
           "object"=>"Contact",
           "explain"=>false,
           "atts"=>array("title","contact_inputs","text-size","bg-color","border")
           ),
       'new_products'=>array(
           "id"=>"new_products",
           "object"=>"New Products",
           "explain"=>false,
           "atts"=>array("title","maxproducts","of_brand",
               "show_discount","show_prices","product-names-lines",
               "display","text-size",
               "mensio-list-product-controls",
               "imageBorderColor","round_images",
               "bg-color","border","box-border")
           ),
       'product_ratings'=>array(
           "id"=>"ratings",
            "object"=>"Ratings",
            "explain"=>false,
            "atts"=>array("title","text-size","bg-color","border")
           ),
       'product'=>array(
           "id"=>"product",
           "object"=>"Product",
           "explain"=>false,
           "atts"=>array("title","show_prices","hide-quantity","hide-availability","hide-sku","share",
               "show_discount","text-size","hide-tags","imageBorderColor","bg-color","border")
           ),
       'user'=>array(
           "id"=>"user",
           "object"=>"User",
           "explain"=>false,
           "atts"=>array("title","text-size","bg-color","border")
           ),
       'tos'=>array(
           "id"=>"tos",
           "object"=>"Terms of Service",
           "explain"=>false,
           "atts"=>array("title","text-size","bg-color","border")
           ),
       'checkout'=>array(
           "id"=>"checkout",
           "object"=>"Checkout",
           "explain"=>false,
           "atts"=>array("title","text-size","bg-color","border")
           )
    );
    if(function_exists("mnsExtraObjects")){
       $objects=array_merge($objects, mnsExtraObjects());
    }
    return $objects;
    }
}
function mensio_page_types(){
    $types[0]['id']="mensio_page";
    $types[0]['description']="Page";
    $types[1]['id']="product_page";
    $types[1]['description']="Product";
    $types[2]['id']="brands_page";
    $types[2]['description']="Brands";
    $types[3]['id']="brand_page";
    $types[3]['description']="Brand";
    $types[4]['id']="categories_page";
    $types[4]['description']="Categories";
    $types[5]['id']="category_page";
    $types[5]['description']="Products";
    $types[6]['id']="login_page";
    $types[6]['description']="Login";
    $types[7]['id']="signup_page";
    $types[7]['description']="Signup";
    $types[8]['id']="contact_page";
    $types[8]['description']="Contact";
    $types[9]['id']="cart_page";
    $types[9]['description']="Cart";
    $types[10]['id']="tos_page";
    $types[10]['description']="Terms Of Service";
    $types[11]['id']="checkout_page";
    $types[11]['description']="Checkout";
    $types[12]['id']="user_page";
    $types[12]['description']="User";
    if(MENSIO_FLAVOR=='STD'){
        $types[13]['id']="product_comparison_page";
        $types[13]['description']="Product Comparison";
        $types[14]['id']="product_favorites_page";
        $types[14]['description']="Favorite Products";
    }
    $types[15]['id']="search_results_page";
    $types[15]['description']="Search Results";
    return $types;
}
function mensio_front_end_settings_atts($att,$what){
    if($att=='show_product_brand'){
        if($what=='title'){
            $atts="";
        }
        if($what=='atts'){
            $tt=time().rand(100,500);
            $atts='
                    <input type="checkbox" id="show-product-brand'.$tt.'" name="show-product-brand" class="show-product-brand" value="yes">
                    <label for="show-product-brand'.$tt.'">Show Product Brand</label>
                    ';
        }
    }
    if($att=='product-names-lines'){
        if($what=='title'){
            $atts= "Product Name Lines";
        }
        if($what=='atts'){
            $atts='
                <div class="select">
                    <select name="product-names-lines">';
                    for($i=1;$i<=3;$i++){
                        $atts.='
                        <option>'.$i.'</option>';
                    }
            $atts.='
                    </select>
                </div>';
        }
    }
    if($att=='mensio-list-product-controls'){
        $tt=time().rand(100,500);
        if($what=='title'){
            $atts= "";
        }
        if($what=='atts'){
            $atts=false;
            if(MENSIO_FLAVOR=='STD'){
            $atts.= '
                    <input type="checkbox" id="hide-barcode'.$tt.'" name="hide-barcode" class="hide-barcode" value="yes">
                    <label for="hide-barcode'.$tt.'">Hide Barcode</label>
                        <hr />
                    <input type="checkbox" id="hide-sku'.$tt.'" name="hide-sku" class="hide-sku" value="yes">
                    <label for="hide-sku'.$tt.'">Hide Product Code</label>
                        <hr />
                    <input type="checkbox" id="hide-add-to-favorites-button'.$tt.'" name="hide-add-to-favorites-button" class="hide-add-to-favorites-button" value="yes">
                    <label for="hide-add-to-favorites-button'.$tt.'">Hide Add to Favorites List</label>
                        <div class="add-to-favorites-controls">
                        </div>
                        <hr />
                    <input type="checkbox" id="hide-add-to-comparison-button'.$tt.'" name="hide-add-to-comparison-button" class="hide-add-to-comparison-button" value="yes">
                    <label for="hide-add-to-comparison-button'.$tt.'">Hide Add to Comparison List</label>
                        <div class="add-to-comparison-controls">
                        </div>
                    <hr />';
            }
            else{
            $atts.= '
                    <input type="checkbox" id="hide-sku'.$tt.'" name="hide-sku" class="hide-sku" value="yes">
                    <label for="hide-sku'.$tt.'">Hide Product Code</label>
                        <hr />';
            }
            $atts.='
                    <input type="checkbox" id="showmensiolistbuttonsonmouseover'.$tt.'" name="showmensiolistbuttonsonmouseover" class="showmensiolistbuttonsonmouseover" value="yes">
                    <label for="showmensiolistbuttonsonmouseover'.$tt.'">Show on Mouse Over</label>
                        <hr />
                    <input type="checkbox" id="hide-availability'.$tt.'" name="hide-availability" class="hide-availability" value="yes">
                    <label for="hide-availability'.$tt.'">Hide Availability</label>';
            if(MENSIO_FLAVOR=='STD'){
                    $atts.= '
                        <hr />
                    <input type="checkbox" id="hide-attributes'.$tt.'" name="hide-attributes" class="hide-attributes" value="yes">
                    <label for="hide-attributes'.$tt.'">Hide Attributes</label>';
            }
        }
    }
    if($att=='add-to-cart-controls'){
        $tt=time().rand(100,500);
        if($what=='title'){
            $atts= "";
        }
        if($what=='atts'){
            $atts= '
                    <input type="checkbox" id="hide-add-to-cart'.$tt.'" name="hide-add-to-cart" class="hide-add-to-cart" value="yes">
                    <label for="hide-add-to-cart'.$tt.'">Hide Add to Cart</label>
                        <div class="add-to-cart-controls">
                                <hr />
                            <input type="checkbox" id="showaddtocartonmouseover'.$tt.'" name="showaddtocartonmouseover" class="showaddtocartonmouseover" value="yes">
                            <label for="showaddtocartonmouseover'.$tt.'">Show on Mouse Over</label>
                                <hr />
                            <input type="checkbox" id="addtocartbold'.$tt.'" name="addtocartbold" class="addtocartbold" value="yes">
                            <label for="addtocartbold'.$tt.'">Bold</label>
                                <hr />
                            <input type="checkbox" id="addtocartitalic'.$tt.'" name="addtocartitalic" class="addtocartitalic" value="yes">
                            <label for="addtocartitalic'.$tt.'">Italics</label>
                                <hr />
                            <input type="checkbox" id="addtocartunderline'.$tt.'" name="addtocartunderline" class="addtocartunderline" value="yes">
                            <label for="addtocartunderline'.$tt.'">Underline</label>
                            <hr />
                            <strong>Text Size</strong>
                            <div class="select">
                                <select name="addToCartSize" class="addToCartSize">';
                                    for($i=1;$i<=3.10;$i=$i+0.1){
                                    $atts.='
                                    <option value="'.str_replace(".","-",$i).'">'.$i.'</option>';
                                    }
                                $atts.='
                                </select>
                            </div>
                        </div>
                    ';
        }
    }
    if($att=='ordering'){
        if($what=='title'){
            $atts= "Ordering";
        }
        if($what=='atts'){
            $atts= '
                <div class="select">
                    <select name="Ordering">
                        <option value="A-Z">A-Z</option>
                        <option value="Z-A">Z-A</option>
                        <option value="MostProducts">More Products</option>
                        <option value="FewerProducts">Fewer Products</option>
                        <option value="Random">Random</option>
                    </select>
                </div>';
        }
    }
    if($att=='display'){
        if($what=='title'){
            $atts="Display";
        }
        if($what=='atts'){
            $tt=time().rand(100,500);
            $atts= '
                <div class="select">
                    <select name="display" class="HowToDisplay">
                        <option value="simple" class="edit-simple-display">Simple</option>
                        <option value="carousel" class="edit-carousel-display">Carousel</option>
                    </select>
                </div>
                <hr />
                Columns
                <div class="select">
                    <select name="edit-col">
                        <option value="2" class="edit-col-2">2</option>
                        <option value="3" class="edit-col-3">3</option>
                        <option value="4" class="edit-col-4">4</option>
                    </select>
                </div>
                <div class="CarouselOptions">
                    <hr />
                        <input type="checkbox" name="carousel-autoplay'.$tt.'" id="carousel-autoplay'.$tt.'" class="carousel-autoplay" value="1">
                        <label for="carousel-autoplay'.$tt.'">Autoplay</label>
                        <div class="carousel-autoplay-options closed">
                        <Hr />
                            Autoplay Time <input type="text" placeholder="1000" value="" class="autoplayTime">
                            <hr />
                            <input type="checkbox" name="carousel-hoverpause'.$tt.'" id="carousel-hoverpause'.$tt.'" class="carousel-hoverpause" value="1">
                            <label for="carousel-hoverpause'.$tt.'">Pause on Hover</label>
                        </div>
                </div>';
        }
    }
    if($att=='round_images'){
        if($what=='title'){
            $tt=time().rand(100,500);
            $atts='<input type="checkbox" id="round-images'.$tt.'" name="round-images" class="round-images" value="round">
                    <label for="round-images'.$tt.'">Round Images</label>
                   ';
        }
        if($what=='atts'){
            $atts= '
                ';
        }
    }
    if($att=='hide_breadcrumbs'){
        if($what=='title'){
            $tt=time().rand(100,500);
            $atts='<input type="checkbox" id="hide-breadcrumbs'.$tt.'" name="hide-breadcrumbs" class="hide-breadcrumbs" value="1">
                    <label for="hide-breadcrumbs'.$tt.'">Hide BreadCrumbs</label>
                   ';
        }
        if($what=='atts'){
            $atts= '
                ';
        }
    }
    if($att=='imageBorderColor'){
        if($what=='title'){
            $tt=time().rand(100,500);
            $atts='<input type="checkbox" id="image-border-colors'.$tt.'" name="image-border-colors" class="image-border-colors" value="yes">
                    <label for="image-border-colors'.$tt.'">Brand Colors Visible</label>
                   ';
        }
        if($what=='atts'){
            $atts= '
                ';
        }
    }
    if($att=='show_prods_in_rows'){
        if($what=='title'){
            $tt=time().rand(100,500);
            $atts='<input type="checkbox" id="show-prods-in-rows'.$tt.'" name="show-prods-in-rows" class="show-prods-in-rows" value="rows">
                    <label for="show-prods-in-rows'.$tt.'">Show in Rows</label>
                   ';
        }
        if($what=='atts'){
            $atts= '
                ';
        }
    }
    if($att=='hide-categories'){
        if($what=='title'){
            $tt=time().rand(100,500);
            $atts='
                    <input type="checkbox" id="hide-categories'.$tt.'" name="hide-categories" class="hide-categories" value="yes">
                    <label for="hide-categories'.$tt.'">Hide Categories</label>
                    ';
        }
        if($what=='atts'){
            $atts= '
                ';
            $atts='';
        }
    }
    if($att=='box-border'){
        if($what=='title'){
            $atts="";
        }
        if($what=='atts'){
            $tt=time().rand(100,500);
            $atts= '
                Image Border<br />
                <div class="select">
                <select id="'.$att.'-image" class="box-border-image">';
                for($k=0;$k<=10;$k++){
                    $atts.='<option value="'.$k.'">'.$k.'</option>';
                }
                $atts.='
                </select>
                </div>
                <hr>
                Image Border Color<br />
                <div class="my-color-field-wrapper">
                    <input type="text"  name="box-border-image-color" class="my-color-field" data-default-color="#000000" value="" />
                </div>
                <hr>
                Image Hover Border<br />
                <div class="select">
                <select id="'.$att.'-image-hover" class="box-border-image-hover">';
                for($k=0;$k<=10;$k++){
                    $atts.='<option value="'.$k.'">'.$k.'</option>';
                }
                $atts.='
                </select>
                </div>
                <hr>
                Image Hover Border Color<br />
                <div class="my-color-field-wrapper">
                    <input type="text"  name="box-border-image-hover-color" class="my-color-field" data-default-color="#000000" value="" />
                </div>
                <hr>
                Box Border<br />
                <div class="select">
                <select id="'.$att.'-border" class="box-border">';
                for($k=0;$k<=10;$k++){
                    $atts.='<option value="'.$k.'">'.$k.'</option>';
                }
                $atts.='
                </select>
                </div>
                <hr>
                Box Border Color<br />
                <div class="my-color-field-wrapper">
                    <input type="text"  name="box-border-color" class="my-color-field" data-default-color="#000000" value="" />
                </div>
                Box Hover Border<br />
                <div class="select">
                <select id="'.$att.'-image" class="box-border-hover">';
                for($k=0;$k<=10;$k++){
                    $atts.='<option value="'.$k.'">'.$k.'</option>';
                }
                $atts.='
                </select>
                </div>
                <hr>
                Box Hover Border Color<br />
                <div class="my-color-field-wrapper">
                    <input type="text"  name="box-border-hover-color" class="my-color-field" data-default-color="#000000" value="" />
                </div>
                ';
        }
    }
    if($att=='category-box-border-color'){
        if($what=='title'){
            $atts="Category Box Border Color";
        }
        if($what=='atts'){
            $atts= '
                <div class="my-color-field-wrapper">
                    <input type="text"  name="category-box-border-color" class="my-color-field" data-default-color="#000000" value="" />
                </div>
                ';
        }
    }
    if($att=='maxbrands'){
        if($what=='title'){
            $atts= "Max Brands";
        }
        if($what=='atts'){
            $atts= '<input type="number" name="maxbrands" value="1" min="1">';
        }
    }
    if($att=='maxcategories'){
        if($what=='title'){
            $atts= "Max Categories";
        }
        if($what=='atts'){
            $atts= '<input type="number" name="maxcats" value="1" min="1">';
        }
    }
    if($att=='maxproducts'){
        if($what=='title'){
            $atts= "Max Products";
        }
        if($what=='atts'){
            $atts= '<input type="number" name="maxprods" value="12" min="1">';
        }
    }
    if($att=='title'){
        if($what=='title'){
            $atts= "<div class='settings-cell flagPageTitle'>Title</div>";
        }
        if($what=='atts'){
            $tt=time().rand(100,500);
            $atts='';
            $langs=new mensio_languages();
            $langs=$langs->GetActiveLanguages();
            foreach($langs as $lang){
                $atts.= '
                    <div class="table-row">
                        <div class="settings-cell flagPageTitle">
                            <img src="'.plugin_dir_url(__FILE__).'../../icons/flags/'.$lang->icon.'.png" width="30" />
                        </div>
                        <div class="settings-cell PageTitle">
                            <input type="text" name="title-'.$lang->code.'" value="">
                        </div>
                    </div>';
            }
                    $atts.='
                    <hr />
                    <strong>Title Color</strong>
                        <div class="my-color-field-wrapper">
                            <input type="text"  name="titlecolor" class="my-color-field" data-default-color="#000000" value="" />
                        </div>
                    <hr />
                    <strong>Title Size</strong>
                <div class="select">
                    <select name="TitleSize" class="titleSizeSel" font-size="'.$GLOBALS['MensioPressFontSize'].'">';
                        for($i=1;$i<=3.10;$i=$i+0.1){
                        $atts.='
                        <option value="'.str_replace(".","-",$i).'">'.$i.'</option>';
                        }
                    $atts.='
                    </select>
                </div>
                    <hr />
                    <input type="checkbox" name="title-bold" id="title-bold'.$tt.'" class="title-bold" value="1">
                    <label for="title-bold'.$tt.'">Title Bold</label>
                    <hr />
                    <input type="checkbox" name="title-italics" id="title-italics'.$tt.'" class="title-italics" value="1">
                    <label for="title-italics'.$tt.'">Title Italics</label>
                    <hr />
                    <input type="checkbox" name="title-underline" id="title-underline'.$tt.'" class="title-underline" value="1">
                    <label for="title-underline'.$tt.'">Title Underline</label>
                <hr />
                <strong>Title Align</strong>
                <div class="select">
                    <select name="TitleAlign" class="titleAlignSel">
                        <option value="left">Left</option>
                        <option value="center">Center</option>
                        <option value="right">Right</option>
                    </select>
                </div>';
                $tt=time().rand(100,500);
                $atts.='<Hr />
                <input type="checkbox" name="hide-title" id="hide-title'.$tt.'" class="hide-title" value="rows">
                <label for="hide-title'.$tt.'">Hide Title</label>
                ';
        }
    }
    if($att=='hide-tags'){
        if($what=='title'){
            $atts= "";
        }
        if($what=='atts'){
                $tt=time().rand(100,500);
                $atts='<Hr />
                <input type="checkbox" name="hide-tags" id="hide-tags'.$tt.'" class="hide-tags" value="yes">
                <label for="hide-tags'.$tt.'">Hide Tags</label>
                ';
        }
    }
    if($att=='hide-quantity'){
        if($what=='title'){
            $atts= "";
        }
        if($what=='atts'){
                $tt=time().rand(100,500);
                $atts='<Hr />
                <input type="checkbox" name="hide-quantity" id="hide-quantity'.$tt.'" class="hide-quantity" value="yes">
                <label for="hide-quantity'.$tt.'">Hide Quantity</label>
                ';
        }
    }
    if($att=='hide-sku'){
        if($what=='title'){
            $atts= "";
        }
        if($what=='atts'){
                $tt=time().rand(100,500);
                $atts='
                <input type="checkbox" name="hide-sku" id="hide-sku'.$tt.'" class="hide-sku" value="yes">
                <label for="hide-sku'.$tt.'">Hide Product Code</label>
                ';
        }
    }
    if($att=='hide-availability'){
        if($what=='title'){
            $atts= "";
        }
        if($what=='atts'){
                $tt=time().rand(100,500);
                $atts='
                <input type="checkbox" name="hide-availability" id="hide-availability'.$tt.'" class="hide-availability" value="yes">
                <label for="hide-availability'.$tt.'">Hide Availability</label>
                ';
        }
    }
    if($att=='text'){
        if($what=='title'){
            $atts= "";
        }
        if($what=='atts'){
            $atts= '';
            $langs=new mensio_languages();
            $langs=$langs->GetActiveLanguages();
            foreach($langs as $lang){
                $atts.='
                    <div class="table-row">
                        <div class="settings-cell flagPageTitle" style="vertical-align:top;">
                        Text
                            <img src="'.plugin_dir_url(__FILE__).'../../icons/flags/'.$lang->icon.'.png" width="30" />
                        </div>
                        <div class="settings-cell PageTitle">
                            <textarea name="mnsText_'.$lang->code.'"></textarea>
                        </div>
                    </div>';
            }
        }
    }
    if($att=='filter'){
        if($what=='title'){
            $tt=time().rand(100,500);
            $atts= '
                    <input type="checkbox" id="show-filters'.$tt.'" name="show-filters" class="show-filters" value="1">
                    <label for="show-filters'.$tt.'">Filters</label>
                    <select id="filters-align'.$tt.'" name="filters-align" class="filters-align">
                        <option value="left">Left</option>
                        <option value="right">Right</option>
                    </select>
                    <label for="filters-align'.$tt.'">Filters Align</label>';
        }
        if($what=='atts'){
            $atts= '';
        }
    }
    if($att=='contact_inputs'){
        if($what=='title'){
            $atts= "Contact Inputs";
        }
        if($what=='atts'){
            $tt=time();
            $atts= '
            <input type="checkbox" id="contact-name-'.$tt.'" class="contact-inputs contact_input-name" name="contact-inputs[]" value="name">
            <label for="contact-name-'.$tt.'">Name</label><br />
            <input type="checkbox" id="contact-lastname-'.$tt.'" class="contact-inputs contact_input-lastname" name="contact-inputs[]" value="lastname">
            <label for="contact-lastname-'.$tt.'">LastName</label><br />
            <input type="checkbox" id="contact-phone-'.$tt.'" class="contact-inputs contact_input-phone" name="contact-inputs[]" value="phone">
            <label for="contact-phone-'.$tt.'">Phone</label><br />
            <input type="checkbox" id="contact-cellphone-'.$tt.'" class="contact-inputs contact_input-cellphone" name="contact-inputs[]" value="cellphone">
            <label for="contact-cellphone-'.$tt.'">Cellphone</label><br />
            <input type="checkbox" id="contact-email-'.$tt.'" class="contact-inputs contact_input-email" name="contact-inputs[]" value="email">
            <label for="contact-email-'.$tt.'">email</label><br />
            <input type="checkbox" id="contact-message-'.$tt.'" class="contact-inputs contact_input-message" name="contact-inputs[]" value="message" checked disabled>
            <label for="contact-message-'.$tt.'">Message</label><br />';
        }
    }
    if($att=='search'){
        if($what=='title'){
            $tt=time().rand(100,500);
            $atts= '
                   <input type="checkbox" id="add-search-box'.$tt.'" name="add-search-box" class="add-search-box" value="1">
                   <label for="add-search-box'.$tt.'">Search Box</label>';
        }
        if($what=='atts'){
            $atts= '';
        }
    }
    if($att=='show_timesSold'){
        if($what=='title'){
            $tt=time().rand(100,500);
            $atts= '
                   <input type="checkbox" id="show-timesSold'.$tt.'" name="show-timesSold" class="show-timesSold" value="1">
                   <label for="show-timesSold'.$tt.'">Show Times Sold</label>';
        }
        if($what=='atts'){
            $atts= '';
        }
    }
    if($att=='show_prices'){
        if($what=='title'){
            $tt=time().rand(100,500);
            $atts= '
                    <input type="checkbox" id="show-prices'.$tt.'" name="show-prices" class="show-prices" value="1">
                    <label for="show-prices'.$tt.'">Show Final Prices</label>
                    <div class="show-what-prices">
                            <hr />
                        <input type="checkbox" id="show-firstprices'.$tt.'" name="show-firstprices" class="show-firstprices" value="1">
                        <label for="show-firstprices'.$tt.'" style="display:none;">Show Pre Discount Prices</label>
                            <hr />
                        <input type="checkbox" id="show-price-with-tax'.$tt.'" name="show-price-with-tax" class="show-price-with-tax" value="1">
                        <label for="show-price-with-tax'.$tt.'" style="display:none;">Tax Included</label>
                            <hr />
                        <label>Price Color:</label>
                        <div class="my-color-field-wrapper">
                            <input type="text"  name="priceColor" class="my-color-field" data-default-color="#000000" value="" />
                        </div>
                    </div>
                   ';
        }
        if($what=='atts'){
            $atts= '';
        }
    }
    if($att=='show_discount'){
        if($what=='title'){
            $tt=time().rand(100,500);
            $atts= '
                    <input type="checkbox" id="show-discount'.$tt.'" name="show-discount" class="show-discount" value="1">
                    <label for="show-discount'.$tt.'">Show Discounts</label>
                    <div class="DiscountProperties">
                    <Hr />
                        <div class="my-color-field-wrapper">
                            <input type="text"  name="discount-background-color" class="my-color-field" data-default-color="#ff0000" value="#ff0000" />
                        </div>
                        <div class="my-color-field-wrapper">
                            <input type="text"  name="discount-text-color" class="my-color-field" data-default-color="#ffffff" value="#ffffff" />
                        </div>
                       <hr />
                        <input type="checkbox" id="discount-bold-'.$tt.'" name="discount-bold" class="discount-bold" value="1">
                        <label for="discount-bold-'.$tt.'">Bold</label>
                    </div>
                    ';
        }
        if($what=='atts'){
            $atts= '';
        }
    }
    if($att=='show_ratings'){
        if($what=='title'){
            $tt=time().rand(100,500);
            $atts= '
                   <input type="checkbox" id="show-times-rated'.$tt.'" name="show-timesRated" class="show-timesRated" value="1">
                   <label for="show-times-rated'.$tt.'">Show Times Rated</label>';
        }
        if($what=='atts'){
            $atts= '';
        }
    }
    if($att=='of_category'){
        if($what=='title'){
            $atts= 'Of Category';
        }
        if($what=='atts'){
            $atts= '<div class="select"><select class="of_category" name="of_category">';
            $getCats=new mensio_products_categories();
            $Cats=$getCats->LoadProductCategoriesDataSet();
            $atts.='<option>Any</option>';
            $atts.='<option value="Current">Current Category</option>';
            foreach($Cats as $cat){
                if($cat->visibility){
                    $atts.='<option value="'.MensioEncodeUUID($cat->uuid).'">'.$cat->name.'</option>';
                }
            }
            $atts.='</select></div>';
        }
    }
    if($att=='of_brand'){
        if($what=='title'){
            $atts= 'Of Brand';
        }
        if($what=='atts'){
            $atts= '<div class="select">
                    <select class="of_brand" name="of_brand"';
            if(!empty($_GET['brand'])){
                $atts.=' curBrand="'.$_GET['brand'].'"';
            }
            $atts.='>';
            $getBrands=new mensio_products_brands();
            $getBrands->Set_Sorter("name");
            $Brands=$getBrands->LoadProductBrandsDataSet();
            $atts.='<option>Any</option>';
            $atts.='<option value="Current">Current Brand</option>';
            foreach($Brands as $brand){
                $atts.='<option value="'.MensioEncodeUUID($brand->uuid).'">'.$brand->name.'</option>';
            }
            $atts.='</select>
                    </div>';
        }
    }
    if($att=='show_ratingsAverage'){
        if($what=='title'){
            $atts= 'Of Brand';
        }
        if($what=='atts'){
            $atts= '<select class="show_ratingsAverageof_brand" name="show_ratingsAverageof_brand">';
            $getBrands=new mensio_products_brands();
            $getBrands->Set_Sorter("name");
            $Brands=$getBrands->LoadProductBrandsDataSet();
            $atts.='<option></option>';
            $atts.='<option value="Current">Current Brand</option>';
            foreach($Brands as $brand){
                $atts.='<option value="'.MensioEncodeUUID($brand->uuid).'">'.$brand->name.'</option>';
            }
            $atts.='</select>';
        }
    }
    if($att=='margins'){
        if($what=='title'){
            $atts= 'Margin Top
                <input type="range" name="margin-top"><br />
                Margin Bottom
                <input type="range" name="margin-bottom">';
        }
        if($what=='atts'){
            $atts= '';
        }
    }
    if($att=='share'){
        if($what=='title'){
            $atts= 'Share';
        }
        if($what=='atts'){
            $atts= '<select class="share"><option>No</option><option>Yes</option></select>';
        }
    }
    if($att=='text-color'){
        if($what=="title"){
            $atts= 'Text Color';
        }
        if($what=="atts"){
        $atts='
            ';
        }
    }
    if($att=='text-size'){
        if($what=='title'){
            $atts="Text Align";
        }
        if($what=="atts"){
            $GLOBALS['MensioPressFontSize']="1";
                if(get_option("MensioPressGlobalFontSize")){
                    $GLOBALS['MensioPressFontSize']=get_option("MensioPressGlobalFontSize");
                }
            $atts='
                <div class="select">
                    <select name="text-align" id="edit-text-align">
                        <option></option>
                        <option id="edit-text-align-left" value="left">Left</option>
                        <option id="edit-text-align-center" value="center">Center</option>
                        <option id="edit-text-align-right" value="right">Right</option>
                    </select>
                </div>
                <hr />
                Text Size
                <div class="settings-cell">
                <div class="select">
                    <select name="text-size" id="edit-text-size" font-size="'.$GLOBALS['MensioPressFontSize'].'">';
                        for($i=1;$i<=3.10;$i=$i+0.1){
                        $atts.='
                        <option value="'.str_replace(".","-",$i).'">'.$i.'</option>';
                        }
                    $atts.='
                    </select>
                </div>
                </div>
                <Hr />
                Text Color
                <div class="settings-cell">
                    <div class="my-color-field-wrapper">
                        <input type="text"  name="text-color" class="my-color-field" data-default-color="#000000" value="" />
                    </div>
                </div>
                <hr />
                <div class="settings-cell"><strong>Active Link Color</strong></div>
                <div class="settings-cell">
                    <div class="my-color-field-wrapper">
                        <input type="text"  name="active-link-color" class="my-color-field" data-default-color="#000000" value="" />
                    </div>
                </div>
                ';
        }
    }
    if($att=='bg-color'){
        if($what=='title'){
            $atts="Block Background Color";
        }
        if($what=="atts"){
            $tt=time().rand();
            $atts='
                <div class="my-color-field-wrapper">
                    <input type="text"  name="background-color" class="my-color-field" data-default-color="#000000" value="" />
                </div>
                ';
        }
    }
    if($att=='border'){
        if($what=='title'){
            $atts="Block Border Width";
        }
        if($what=="atts"){
            $atts='
                <div class="select">
                <select name="border-w" id="border-w">
                    <option selected="selected" value="0">0</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="20">20</option>
                </select>
                </div>
                <hr />                
                Block Border Radius
                <div class="select">
                <select name="border-r" id="border-r">
                    <option selected="selected" value="0">0</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="20">20</option>
                </select>
                </div>
                <hr />                
                Block Border Color<br />
                <div class="my-color-field-wrapper">
                    <input type="text"  name="border-c" id="border-c'.rand(10000,99999).'" class="my-color-field" data-default-color="#000000" value="" />
                </div>
                ';
        }
    }
    return $atts;
}
function mensio_ajax_Open_Object_Settings_ModalForm(){
    $MdlForm = '
    <div id="MensioProperties">
        <div id="mns_settings">';
            foreach(mnsObjects() as $obj){
            $MdlForm.= '
                <div class="mnsObjectProperties" id="edit-'.$obj['id'].'">
                ';
                foreach($obj['atts'] as $att) {
                $MdlForm.= '
                <div class="settings-row">
                    <div class="settings-cell">'.mensio_front_end_settings_atts($att,"title").'</div>
                    <div class="settings-cell">
                        '.mensio_front_end_settings_atts($att,"atts").'
                    </div>
                </div>';
                }
                $MdlForm.= '
                    <br /><br /><br /><br /><br /><br />
                    <br /><br /><br /><br /><br /><br />
            </div>';
            }
            $MdlForm.= '
        </div>
        <div id="mns_styles">
        </div>
    </div>
        ';
return $MdlForm;
die;
}
function MensioCheckSlug(){
    $check=explode("::",$_POST['Security']);
    $check=$check[0];
    if(empty($_POST['Security']) || wp_verify_nonce($check,"Active_Page_PageDesigner")==false){
        die;
    }
    $slug=filter_var($_REQUEST['mensioSlug']);
    global $wpdb;
    $prfx = $wpdb->prefix;
    $Result=array();
    $Query = 'SELECT `ID` from `'.$prfx.'posts` where `post_name`="'.$slug.'"';
    $Result = $wpdb->get_results($Query);
    $cc=count($Result);
    $Query = 'SELECT `uuid` from `'.$prfx.'mns_store_slugs` where `slug`="'.$slug.'"';
    $Result = $wpdb->get_results($Query);
    $cc=$cc+count($Result);
    echo $cc;
    die;
}
function mns_OpenModalBox(){
    $check=explode("::",$_POST['Security']);
    $check=$check[0];
    if(empty($_POST['Security']) || wp_verify_nonce($check,"Active_Page_PageDesigner")==false){
        die;
    }
    $args = array(
        'wpautop' => true,
        'media_buttons' => true,
        'buttons' => true,
        'editor_class' => 'frontend',
        'textarea_rows' => 5,
        'tabindex' => 1,
        'toolbar1'  => 'bold,italic,underline,separator,alignleft,aligncenter,alignright,separator,link,unlink,undo,redo',
    );     
    $CustomHTML=false;
    echo "
    <div class='ModalContent'><input type='button' class='closeModal' value='x'>";
        $editor_id = 'MensioHTMLEDITOR';
	$settings = array( 
		'wpautop' => true, 
		'media_buttons' => true, 
		'quicktags' => array(
				'buttons' => 'strong,em,del,ul,ol,li,block,close'
			),
                'textarea_name' => 'my_options[textareafield]'
	);
        wp_editor( '', $editor_id, $settings );
    echo "
        <input type='button' value='Update' class='updateHTML'>".
    "</div>";
    die;
}
function MensioGetActiveReviewType(){
  global $wpdb;
  $prfx = $wpdb->prefix;
    $Result=false;
      $Query = 'SELECT * from `'.$prfx.'mns_ratings_types` where `active`=1';
      $Result = $wpdb->get_results($Query);
      foreach($Result as $res){
          $Result['id']=$res->uuid;
          $Result['max']=$res->max;
          $Result['min']=$res->min;
          $Result['step']=$res->step;
          $Result['name']=$res->name;
          $Result['icon']=$res->icon;
      }
    return $Result;
}
if(!empty($_GET['post']) && !empty($_GET['action']) && $_GET['action']=='edit' &&
        count(get_post_meta($_GET['post'],"mensio_page_function"))>0){
    $post=get_post($_GET['post']);
}
function mensiopress_MensioOpenPage(){
    $check=explode("::",$_POST['Security']);
    $check=$check[0];
    if(empty($_POST['Security']) || wp_verify_nonce($check,"Active_Page_PageDesigner")==false){
    }
    $tbl=new MensioPressPages();
    echo $tbl->GetDataTable(
            filter_var($_REQUEST['Page']),
            0,
            '',
            '',
            ''
        );
    die;
}
function mensiopress_MensioUpdateHomePage(){
    $check=explode("::",$_POST['Security']);
    $check=$check[0];
    if(empty($_POST['Security']) || wp_verify_nonce($check,"Active_Page_PageDesigner")==false){
        die;
    }
    $currentHome= get_option("page_on_front");
    if(!empty($currentHome)){
        wp_delete_post( $currentHome);
    }
    $newHomePageID=filter_var($_REQUEST['OptionValue']);
    $newHomePage= get_post($newHomePageID);
    $newFakeHomePage = array(
        'post_type'     => 'page',
        'post_name'     => $newHomePage->post_name,
        'post_content'  => "[mensiopresshomepage page='".filter_var($_REQUEST['OptionValue'])."']",
        'post_status'   => "publish",
        'post_title'   => $newHomePage->post_title
    );
    $newFakeHomePageID=wp_insert_post($newFakeHomePage);
    update_option("show_on_front", "page");
    update_option("page_on_front", $newFakeHomePageID);
    die;
}
