<?php
class MensioPressPages  extends mensio_core_form{
    private $DataSet;
  private function LoadPagesDataSet($InSorter='') {
    $pgTypes=mensio_page_types();
    $wpb_all_query= new WP_Query(array('post_type'=>'mensio_page', 'post_status'=>'publish', 'posts_per_page'=>-1));
    $i=0;
    $PagesList=array();
    if(get_option("page_on_front")){
    $home_page_id= get_option("page_on_front");
        $homePost= get_post($home_page_id);
        $is_home="<input type='radio' name='mensiopage_on_front' value='".$home_page_id."' checked>";
        $pg_func=get_post_meta($home_page_id,"mensio_page_function");
        if((count($pg_func)>0) && !empty(count($pg_func))){
            foreach($pgTypes as $Type){
                }
                if($pg_func[0]==$Type['id']){
                    $page_function=$Type['description'];
            }
        }
        $homePageID=get_option( 'page_on_front' );
        if(!empty($homePageID)){
            $Home=get_post($homePageID);
            $homepageShortcode=$Home->post_content;
            $homePageID=do_shortcode(str_replace("]"," foradmin='yes']",$homepageShortcode));
            $homeID=str_replace(
                    array(
                        "[mensiopresshomepage page='",
                        "']"
                        )
                    ,'',$homepageShortcode);
            $homePageID=$homeID;
        }
    }
    while ( $wpb_all_query->have_posts() ){
        $wpb_all_query->the_post();
        $pg_func=get_post_meta(get_the_ID(),"mensio_page_function");
        if(count($pg_func)>0){
            foreach($pgTypes as $Type){
                if($pg_func[0]==$Type['id']){
                    $page_function=$Type['description'];
                }
            }
        }
        else{$page_function='-';continue;}
        $post=get_post(get_the_ID());
        $args = array(
                'post_parent' => get_the_ID(),
                'numberposts' => 1
        );
        $children = get_children( $args );
        if(get_the_ID()==$homePageID){
            $is_home="<input type='radio' name='mensiopage_on_front' value='".get_the_ID()."' onClick='ChangeHome($(this).val())' checked='true' />";
        }
        else{
            $is_home="<input type='radio' name='mensiopage_on_front' value='".get_the_ID()."' onClick='ChangeHome($(this).val())' />";
        }
        $slug=$post->post_name;
        $PagesList[$i]['uuid']= get_the_ID();
        $PagesList[$i]['Title']=get_the_title();
        $PagesList[$i]['Slug']= $slug;
        $PagesList[$i]['Created']= get_the_date('Y-m-d');
        $PagesList[$i]['Notes']= $page_function;
        $PagesList[$i]['isHome']= $is_home;
        $i++;
    }
    $this->DataSet = array();
    if(!empty($PagesList)){
        $this->DataSet = $PagesList;
    }
    unset($PagesList);
  }
  public function GetDataTable($InPage=1,$InRows=0,$InSorter='',$InSearch='',$JSONData='') {
    $RtrnTable = '';
    if (($InRows === 0) || ($InRows === '')) { $InRows = $this->LoadTableDefaultRows(); }
    $tbl = new mensio_datatable();
    $this->LoadPagesDataSet();
    $tbl->Set_ActivePage($InPage);
    $tbl->Set_ActiveRows($InRows);
    $tbl->Set_BulkActions(array(
     'DEL'=>'Delete'
    ));
    $tbl->Set_EditColumn('Title');
    $tbl->Set_EditOptionsSubline(array(
      'Edit', 'Delete'
    ));
    $tbl->Set_Columns(array(
      'uuid:uuid:plain-text',
      'Title:Title:plain-text',
      'Slug:Slug:small',
      'Created:Created:small',
      'Notes:Notes:plain-text',
      'isHome:Front Page:small'
    ));
    $Table=array();
    if(!empty($this->DataSet)){
        $Table=$this->DataSet;
    }
    $RtrnTable = $tbl->CreateTable(
      'Pages',
      $Table,
      array('uuid','Title','Notes','Slug','Created','isHome')
    );
    unset($tbl,$Data);    
    return $RtrnTable;
  }
}
function Mensio_Admin_Pages($InPage=1,$InRows=0,$InSorter='',$InSearch='',$JSONData=''){
    $RtrnTable = '';
    $Page = new MensioPressPages();
    $RtrnTable = $Page->GetDataTable();
    $CompPage = '';
      $Page = new Mensio_Admin_Design_Pages_Form();
      $Page->Load_Page_CSS();
      $Page->Load_Page_JS();
      $Page->Set_MainMenuItems($Page->GetPageSubPages('Design'));
      $Page->Set_CustomMenuItems('') ;
      $Page->Set_MainPlaceHolder(
       '
        <h1 class="Mns_Page_HeadLine">Mensiopress Page Builder</h1>
        <div id="ButtonArea">
            <button id="newMensioPage-button" title="Add New Page">
                <i class="fa fa-plus action-icon" aria-hidden="true"></i>
                Add New
            </button>
        </div>
        '.wp_nonce_field('Active_Page_PageDesigner').'
        <div class="PageInfo">'.MENSIO_PAGEINFO_Design.'</div>
        <div class="DivResizer"></div>
        <hr>
        <div id="TBL_Pages_Wrapper" class="TBL_DataTable_Wrapper">'.$RtrnTable.'
        </div>
        '
      );
    $CustomScript='var siteurl="'. site_url().'";';
    wp_enqueue_script(
           'MensioPressSiteURL',
           plugin_dir_url( __FILE__ ) . '../../js/mensio-admin-design-pages.js',
           array(),
           '1.0' );
   wp_add_inline_script( 'MensioPressSiteURL',
           $CustomScript
           );
      $Page->UpdatePage();
      $Page->SetActiveSubPage('Design','Design');
      $CompPage = $Page->GetPage();
      unset($Page);
      $CompPage.= '
        <div id="ModalBox">
        <div id="newMensioPage" class="mensio-panel">
            <input type="button" class="exitModalBox" value="x">
            <div class="row">
                <span class="cell">Title:</i></span>
                <span class="cell"><input type="text" id="NewMensioPageTitle" name="NewMensioPageTitle" value="" /></span>
            </div>
            <div class="row">
                <span class="cell">Function: </span>
                <span class="cell">
                <div class="select">
                    <select id="NewMensioPageFunction" name="NewMensioPageFunction">';
                        $functions=mensio_page_types();
                        foreach($functions as $func){
                            $dis='';
                            global $wpdb;
                            $results = $wpdb->get_results( "select post_id, meta_key from $wpdb->postmeta where meta_value = '".$func['id']."'", ARRAY_A );
                            if(count($results)!=0){
                            }
                            $CompPage.= "<option value='".$func['id']."'".$dis.">".$func['description']."</option>";
                        }
                    $CompPage.= '
                    </select>
                </div>
                </span>
            </div>
            <div class="button_row">
              <button id="BTN_Save" class="button BtnGreen" title="Save">
                <i class="fa fa-floppy-o" aria-hidden="true"></i>
              </button>
            </div>
        </div>
        </div>
        ';
      echo $CompPage;
}
if(is_admin()){
    add_action( 'pre_get_posts' ,'MensioExcludeThisPage' );
    function MensioExcludeThisPage( $query ) {
            global $wpdb;
            $results = $wpdb->get_results(
                "
                SELECT post_id 
                FROM {$wpdb->prefix}postmeta 
                WHERE meta_key 
                LIKE 'mensio_page_function'
                ",
                ARRAY_N
            );
            $results = array_map(function($value){
                return (int) str_replace('like_status_', '', $value[0]);
            }, $results);
            foreach($results as $post_id){
                $pages[]=$post_id;
            }
            if( !is_admin() )
                    return $query;
            global $pagenow;
            if( 'edit.php' == $pagenow && ( get_query_var('post_type') && 'page' == get_query_var('post_type') ) )
                    $query->set( 'post__not_in',
                            $pages
                    ); // page id
            return $query;
    }
}
function Mensio_register_menu_metabox() {
	$custom_param = array( 0 => 'This param will be passed to my_render_menu_metabox' );
}
function Mensio_render_menu_metabox( $object, $args ) {
	global $nav_menu_selected_id;
        $o=0;
        $Pages[$o]=(object) array(
                    'ID' => 0,
                    'db_id' => 0,
                    'menu_item_parent' => 0,
                    'object_id' => "logout",
                    'post_parent' => 0,
                    'type' => 'custom',
                    'object' => "logout",
                    'type_label' => '',
                    'title' => "Logout",
                    'url' => home_url("?page=mensio-logout"),
                    'target' => '',
                    'attr_title' => '',
                    'description' => '',
                    'classes' => array("mensio-logout"),
                    'xfn' => '',
        );
        ?>
	<div id="my-plugin-div">
            <div id="tabs-panel-my-plugin-all" class="tabs-panel tabs-panel-active">
            <b>Pages</b>
            <ul id="my-plugin-checklist-pop" class="categorychecklist form-no-clear mensioAllBrands" >
                    <?php echo walk_nav_menu_tree( array_map( 'wp_setup_nav_menu_item', $Pages ), 0, (object) array( 'walker' => $walker ) ); ?>
            </ul>
            <p class="button-controls">
                <span class="list-controls">
                    <a href="<?php
                        echo esc_url(add_query_arg(
                            array(
                                'my-plugin-all' => 'all',
                                'selectall' => 1,
                            ),
                            remove_query_arg( $removed_args )
                        ));
                    ?>#my-menu-test-metabox" class="select-all"><?php _e( 'Select All' ); ?>
                    </a>
                </span>
                <span class="add-to-menu">
                    <input type="submit"<?php wp_nav_menu_disabled_check( $nav_menu_selected_id ); ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e( 'Add to Menu' ); ?>" name="add-my-plugin-menu-item" id="submit-my-plugin-div" />
                    <span class="spinner"></span>
                </span>
            </p>
            </div>
	</div>
	<?php
}
function MensioPressGetShortcodeAttributes( $tag, $text ){
    preg_match_all( '/' . get_shortcode_regex() . '/s', $text, $matches );
    $out = array();
    if( isset( $matches[2] ) )
    {
        foreach( (array) $matches[2] as $key => $value )
        {
            if( $tag === $value )
                $out[] = shortcode_parse_atts( $matches[3][$key] );  
        }
    }
    return $out;
}
function MensioPressCustom_toolbar_link($wp_admin_bar) {
    global $post;
    if(!empty($post)){
        $content=$post->post_content;
        if ( has_shortcode( $content, 'mensiopresshomepage' ) && !empty($post)) {
            $out = MensioPressGetShortcodeAttributes( 'mensiopresshomepage', $content );
            $pageID=$out[0]['page'];
            $post=get_post($pageID);
        }
    }
    if(!empty($post->ID) &&
            ($post->post_type=="mensio_category"
              || $post->post_type=="mensio_product"
                || $post->post_type=="mensio_brand"
                  || $post->post_type=="mensio_page")){
        $Link=site_url().'/wp-admin/admin.php?post='.$post->ID.'&page=mns-html-edit';
        if($post->post_type=="mensio_category"){
            $getCategory=new mnsGetFrontEndLink();
            $Category=$getCategory->CategoryPage();
            $post= get_post($Category);
            $Link=site_url().'/wp-admin/admin.php?post='.$post->ID.'&page=mns-html-edit';
            $Link.='&category='.MensioEncodeUUID($GLOBALS['UUID']);
        }
        elseif($post->post_type=="mensio_product"){
            $getProduct=new mnsGetFrontEndLink();
            $Product=$getProduct->ProductPage();
            $post= get_post($Product);
            $Link=site_url().'/wp-admin/admin.php?post='.$post->ID.'&page=mns-html-edit';
            if(!empty($GLOBALS['UUID'])){
                $Link.='&product='.MensioEncodeUUID($GLOBALS['UUID']);
            }
        }
        elseif($post->post_type=="mensio_brand"){
            $getBrand=new mnsGetFrontEndLink();
            $Brand=$getBrand->BrandPage();
            $post= get_post($Brand);
            $Link=site_url().'/wp-admin/admin.php?post='.$post->ID.'&page=mns-html-edit';
            $Link.='&brand='.MensioEncodeUUID($GLOBALS['UUID']);
        }
        $args = array(
            'id' => 'edit',
            'title' => 'Edit Page', 
            'href' => site_url()."/wp-admin/post.php?post=".$post->ID."&action=edit", 
            'meta' => array(
                'class' => 'MensioPress-Wordpress-Edit-Button', 
                'title' => 'Edit Page'
            )
        );
        $wp_admin_bar->add_node($args);
        $MensioPressLogo= false;
        $args = array(
            'id' => 'mnsEditButton',
            'title' => $MensioPressLogo.' Edit "'.$post->post_title.'"', 
            'href' => $Link, 
            'meta' => array(
                'class' => 'MensioPress-Edit-Button', 
                'title' => 'Edit "'.$post->post_title.'"'
            )
        );
        $wp_admin_bar->add_node($args);
    }
}
add_action('admin_bar_menu', 'MensioPressCustom_toolbar_link', 999);
    add_action( 'admin_menu', 'extra_post_info_menu' );
    function extra_post_info_menu(){
      $page_title = 'MensioPress FrontEnd';
      $menu_title = 'FrontEnd';
      $capability = 'manage_options';
      $menu_slug  = 'mns-html-edit';
      $function   = 'MensioPressFrontEndEditing';
      $icon_url   = 'dashicons-media-code';
      $position   = 100;
      add_menu_page( $page_title,
                     $menu_title, 
                     $capability, 
                     $menu_slug, 
                     $function, 
                     $icon_url, 
                     $position );
    }
function VerifyPageIntegrity($Passphrase) {
    $IsCorrect = false;
    $Passphrase = explode ('::',$Passphrase);
    $Passphrase = $Passphrase[0];
    if (wp_verify_nonce( $Passphrase,"Active_Page_PageDesigner")) {
        $IsCorrect = true;
    }
    return $IsCorrect;
}
function MensioPressFrontEndEditing(){
    $seller=new mensio_seller();
    if((!empty($_REQUEST['post'])) && ($_REQUEST['page']=='mns-html-edit') && is_admin()){
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );
        wp_enqueue_script( 'jquery-ui' );
        wp_enqueue_script( 'jquery-ui-draggable' );
        $post_id= filter_var($_REQUEST['post']);
        $post=get_post($post_id);
        if(get_option("MensioSkin")==false){
            add_option("MensioSkin", "1",'','no');
        }
        if(!empty($_COOKIE['MensioSkin'])){
            $skin=$_COOKIE['MensioSkin'];
        }
        else{
            $skin='1';
        }
$SkingsDIR=plugin_dir_path(__FILE__).'../../css/skins.php';
include($SkingsDIR);
        $Layout='
        <div class="openModalBox"></div>';
        if(!empty($_GET['brand'])){
            $extraurl="&brand=".$_GET['brand'];
        }
        elseif(!empty($_GET['category'])){
            $extraurl="&category=".$_GET['category'];
        }
        else{
            $extraurl="";
        }
        $Layout.='
        <input type="hidden" id="skin" value="'.$skin.'" />';
        wp_enqueue_script("MensioPressAdminDesignPages", plugin_dir_url( __FILE__ ).'../../js/mensio-admin-design-pages.js');
        $MensioScript=false;
            if(!empty($_GET['brand'])){
                $MensioScript.='var CurrentBrand= "'.$_GET['brand'].'";';
            }
            else{
                $MensioScript.='var CurrentBrand= "";';
            }
            if(!empty($_GET['category'])){
                $MensioScript.='var CurrentCategory= "'.$_GET['category'].'";';
            }
            else{
                $MensioScript.='var CurrentCategory= "";';
            }
            $MensioScript.='
            var extraURL= "'.$extraurl.'";
            var ajaxurl = "' . admin_url('admin-ajax.php') . '";
            var siteurl = "'.site_url()."/?p=".$post_id.'&page=mns-html-edit'.$extraurl.'";
            var AdminSiteurl = "'. admin_url()."edit.php?post=".$post_id.'&page=mns-html-edit";
            jQuery("document").ready(function(){
                jQuery("#MensioSettings form").accordion({
                            autoheight:false,
                            heightStyle: "content",
                            collapsible: true,
                            active: false,
                            header: "h2",
                            option: "div",
                            activate: function(event, ui) {
                                if(typeof ui.newHeader.context != "undefined"){
                                    jQuery("#MensioSettings").scrollTop(ui.newHeader.context.offsetTop);
                                }
                            }
                        });
                jQuery(".my-color-field").wpColorPicker({
                            change: function (event, ui) {
                                    var NewColor=ui.color.toString();
                                    jQuery(this).closest(".wp-picker-container").find(".wp-color-result").html( NewColor );
                                    jQuery(this).attr("value",NewColor);
                                    jQuery(this).closest(".wp-picker-container").find("button").removeClass("undefinedColor");
                                    if(jQuery("#edit-object").val()){
                                        jQuery(".mns-block[mns-elem="+jQuery("#edit-object").val()+"]").attr(jQuery(this).attr("name"),NewColor);
                                        if(jQuery(this).attr("name")=="border-c"){
                                            jQuery(".mns-block[mns-elem="+jQuery("#edit-object").val()+"]").css("border-color",NewColor);
                                        }
                                        if(jQuery(this).attr("name")=="background-color"){
                                            jQuery(".mns-block[mns-elem="+jQuery("#edit-object").val()+"]").css("background-color",NewColor);
                                        }
                                        if(jQuery(this).attr("name")=="text-color"){
                                            jQuery(".mns-block[mns-elem="+jQuery("#edit-object").val()+"]").css("color",NewColor);
                                        }
                                        if(jQuery(this).attr("name")=="titlecolor"){
                                            jQuery(".mns-block[mns-elem="+jQuery("#edit-object").val()+"] .mensioObjectTitle").css("color",NewColor);
                                        }
                                        if(jQuery(this).attr("name")=="discount-text-color"){
                                            jQuery(".mns-block[mns-elem="+jQuery("#edit-object").val()+"] .discount").css("color",NewColor);
                                        }
                                        if(jQuery(this).attr("name")=="discount-background-color"){
                                            jQuery(".mns-block[mns-elem="+jQuery("#edit-object").val()+"] .discount").css("background-color",NewColor);
                                            jQuery(".mns-block[mns-elem="+jQuery("#edit-object").val()+"]").attr("discount-background-color",NewColor);
                                        }
                                        if(jQuery(this).attr("name")=="priceColor"){
                                            jQuery(".mns-block[mns-elem="+jQuery("#edit-object").val()+"] .mensioPrice").css("color",NewColor);
                                        }
                                        if(jQuery(this).attr("name")=="box-border-color"){
                                            jQuery(".mns-block[mns-elem="+jQuery("#edit-object").val()+"] .mns-list-item-new").css("border-color",NewColor);
                                        }
                                        if(jQuery(this).attr("name")=="background-color"){
                                            jQuery(".mns-block[mns-elem="+jQuery("#edit-object").val()+"]").css("background-color",NewColor);
                                        }
                                        if(jQuery(this).attr("name")=="border-c"){
                                            jQuery(".mns-block[mns-elem="+jQuery("#edit-object").val()+"]").css("border-color",NewColor);
                                        }
                                        if(jQuery(this).attr("name")=="box-border-hover-color"){
                                            jQuery(".mns-block[mns-elem="+jQuery("#edit-object").val()+"] .mns-list-item-new:first").css("border-color",NewColor).css("border-width", jQuery(".mns-block[mns-elem="+jQuery("#edit-object").val()+"] .box-border-hover").val() );
                                        }
                                        if(jQuery(this).attr("name")=="box-border-image-hover-color"){
                                            var MyStyle=jQuery(".mns-block[mns-elem="+jQuery("#edit-object").val()+"] .mns-list-item-new:first .mns-image a img").css("border-color","").css("border-width","")
                                                    .attr("style");
                                            jQuery(".mns-block[mns-elem="+jQuery("#edit-object").val()+"] .mns-list-item-new:first .mns-image a img")
                                                    .attr("style",MyStyle+"border-color:"+NewColor+" !important;border-width:"+ jQuery(".mns-block[mns-elem="+jQuery("#edit-object").val()+"]").attr("box-border-image-hover")+"px !important;");
                                        }
                                    }
                            }
                        });
            });';
        wp_add_inline_script("MensioPressAdminDesignPages", $MensioScript);
        wp_enqueue_style("PerfectScrollbarCSS",plugin_dir_url( __FILE__ ).'../../css/perfect-scrollbar-4.9.5.css');
        wp_enqueue_script("PerfectScrollbarJS",plugin_dir_url( __FILE__ ).'../../js/perfect-scrollbar.jquery.js');
        wp_enqueue_style("MENSIOPRESS_SKINS",plugin_dir_url(__FILE__).'../../css/skins.php?css=1');
        wp_enqueue_script("MensioPressFrontEndLoad",plugin_dir_url( __FILE__ ).'../../js/perfect-scrollbar.jquery.js');
        wp_add_inline_script("MensioPressFrontEndLoad",'
        window.onload=function(){
            jQuery("#mns-objects")
            .height("90%")
            .width("200px")
            .css("position","fixed")
            .perfectScrollbar().scrollTop(1);
            jQuery("#MensioProperties").perfectScrollbar().scrollTop(1);
        }');
        wp_enqueue_style("MensioDesignPages",plugin_dir_url( __FILE__ ).'../../css/mensio-admin-design-pages.css');
        wp_enqueue_style("MensioPressFontAwesome",plugin_dir_url( __FILE__ ).'../../css/font-awesome.css');
        $Layout.='
            <div id="MensioConfirmation">
                <div id="MensioSettings">
                <!--<input type="button" class="closeConfirmation" value="x">-->
                <button class="closeConfirmation"><i class="fa fa-close"></i></button>
                    ';
                    if(!empty($_POST['hideentrytitle']) && wp_verify_nonce($_POST['mns_sec'],"Active_Page_PageDesigner")){
                        if(!update_option( "hideentrytitle", filter_var($_POST['hideentrytitle']))){
                            add_option("hideentrytitle",filter_var($_POST['hideentrytitle']),'','no');
                        }
                    }
                    if(!empty($_POST['MensioLanguagesButtons']) && wp_verify_nonce($_POST['mns_sec'],"Active_Page_PageDesigner")){
                        if(!update_option( "MensioLanguagesButtons", filter_var($_POST['MensioLanguagesButtons']))){
                            add_option("MensioLanguagesButtons",filter_var($_POST['MensioLanguagesButtons']),'','no');
                        }
                    }
                    if(!empty($_POST['Mensio-hide-nav-links']) && wp_verify_nonce($_POST['mns_sec'],"Active_Page_PageDesigner")){
                        if(!update_option( "Mensio-hide-nav-links", filter_var($_POST['Mensio-hide-nav-links']))){
                            add_option("Mensio-hide-nav-links",filter_var($_POST['Mensio-hide-nav-links']),'','no');
                        }
                    }
                    if(!empty($_POST['MensioBrandSlug']) && wp_verify_nonce($_POST['mns_sec'],"Active_Page_PageDesigner")){
                        if(!update_option( "MensioBrandSlug", filter_var($_POST['MensioBrandSlug']))){
                            add_option("MensioBrandSlug",filter_var($_POST['MensioBrandSlug']),'','no');
                        }
                    }
                    $eshopBrandSlug=get_option("MensioBrandSlug");
                    if(!$eshopBrandSlug){
                        $eshopBrandSlug="mensio-brand";
                    }
                    if(!empty($_POST['MensioCategorySlug']) && wp_verify_nonce($_POST['mns_sec'],"Active_Page_PageDesigner")){
                        if(!update_option( "MensioCategorySlug", filter_var($_POST['MensioCategorySlug']))){
                            add_option("MensioCategorySlug",filter_var($_POST['MensioCategorySlug']),'','no');
                        }
                    }
                    $eshopCategorySlug=get_option("MensioCategorySlug");
                    if(!$eshopCategorySlug){
                        $eshopCategorySlug="mensio-category";
                    }
                    if(!empty($_POST['MensioProductSlug']) && wp_verify_nonce($_POST['mns_sec'],"Active_Page_PageDesigner")){
                        if(!update_option( "MensioProductSlug", filter_var($_POST['MensioProductSlug']))){
                            add_option("MensioProductSlug",filter_var($_POST['MensioProductSlug']),'','no');
                        }
                    }
                    $eshopProductSlug=get_option("MensioProductSlug");
                    if(!$eshopProductSlug){
                        $eshopProductSlug="mensio-product";
                    }
                    if(!empty($_POST['MensioPageSlug']) && wp_verify_nonce($_POST['mns_sec'],"Active_Page_PageDesigner")){
                        if(!update_option( "MensioPageSlug", filter_var($_POST['MensioPageSlug']))){
                            add_option("MensioPageSlug",filter_var($_POST['MensioPageSlug']),'','no');
                        }
                    }
                    $MensioPageSlug=get_option("MensioPageSlug");
                    if(!$MensioPageSlug){
                        $MensioPageSlug="action";
                    }
        $Layout.='
        <i class="error"></i>
            <form method="post">
            ';
        $Layout.= wp_nonce_field('Active_Page_PageDesigner',"mns_sec");
            if(!empty($MensioSkinColors)){
                $Layout.='
                <h2>Skins</h2>
                <div id="Skins">';
                foreach($MensioSkinColors as $id => $attr){
                    $Layout.='<label><input type="radio" value="'.$id.'" name="skin"';if(!empty($_COOKIE['MensioSkin']) && $_COOKIE['MensioSkin']==$id){$Layout.=" checked";}$Layout.='>';
                    foreach($attr as $att=>$val){
                        $Layout.='<div class="MnsSkinColor" style="background:#'.$val.';"></div>';
                    }
                    $Layout.="<span class='attrTitle'>".$MensioSkinColorTitles[$id]."</span>";
                    $Layout.='</label>';
                }
                $Layout.='</div>';
            }
            $Layout.='
            <h2>Add To Cart Button</h2>
                ';
            $langs=new mensio_languages();
            $langs=$langs->GetActiveLanguages();
            foreach($langs as $lang){
                if(!empty($_REQUEST['MensioAddToCart_'.$lang->code])){
                    if(!update_option( 'MensioAddToCart_'.$lang->code, filter_var($_POST['MensioAddToCart_'.$lang->code]))){
                        add_option('MensioAddToCart_'.$lang->code,filter_var($_POST['MensioAddToCart_'.$lang->code]),'','no');
                    }
                }
            }
                $Layout.='
            <div>
            <div class="UsualSettings">
                    <div>
                        <div>Show Text:</div>
                    </div>
                    <div>';
            $i=1;
            $MasterValue="Add to Cart";
            foreach($langs as $lang){
                if($i!=1){
                    $Layout.='<div>';
                }
                if(get_option('MensioAddToCart_'.$lang->code)){
                    $Value=get_option('MensioAddToCart_'.$lang->code);
                }
                else{
                    $Value=$MasterValue;
                }
                $Layout.= '
                        <div style="display:inline-block;width:44px;max-height: 26px;">
                            <img src="'.plugin_dir_url(__FILE__).'../../icons/flags/'.$lang->icon.'.png" width="30" />
                        </div>';
                $Layout.='
                        <div style="display:inline-block;width:70%;">
                            <input type="text" name="MensioAddToCart_'.$lang->code.'" value="'.$Value.'">
                        </div>
                    </div>';
                $i++;
            }
            if(!empty($_REQUEST['MensioCartBackgroundColor'])){
                if(!update_option( 'MensioCartBackgroundColor', filter_var($_POST['MensioCartBackgroundColor']))){
                    add_option('MensioCartBackgroundColor',filter_var($_POST['MensioCartBackgroundColor']),'','no');
                }
            }
            if(!empty($_REQUEST['MensioCartTextColor'])){
                if(!update_option( 'MensioCartTextColor', filter_var($_POST['MensioCartTextColor']))){
                    add_option('MensioCartTextColor',filter_var($_POST['MensioCartTextColor']),'','no');
                }
            }
            if(!empty($_REQUEST['MensioHoverCartBackgroundColor'])){
                if(!update_option( 'MensioHoverCartBackgroundColor', filter_var($_POST['MensioHoverCartBackgroundColor']))){
                    add_option('MensioHoverCartBackgroundColor',filter_var($_POST['MensioHoverCartBackgroundColor']),'','no');
                }
            }
            if(!empty($_REQUEST['MensioHoverCartTextColor'])){
                if(!update_option( 'MensioHoverCartTextColor', filter_var($_POST['MensioHoverCartTextColor']))){
                    add_option('MensioHoverCartTextColor',filter_var($_POST['MensioHoverCartTextColor']),'','no');
                }
            }
            $CartBGcolor='#cccccc';
            $CartTextcolor='#000000';
            $HoverCartBGcolor='#cccccc';
            $HoverCartTextcolor='#000000';
            if(get_option("MensioCartBackgroundColor")){
                $CartBGcolor=get_option("MensioCartBackgroundColor");
            }
            if(get_option("MensioCartTextColor")){
                $CartTextcolor=get_option("MensioCartTextColor");
            }
            if(get_option("MensioHoverCartBackgroundColor")){
                $HoverCartBGcolor=get_option("MensioHoverCartBackgroundColor");
            }
            if(get_option("MensioHoverCartTextColor")){
                $HoverCartTextcolor=get_option("MensioHoverCartTextColor");
            }
                $Layout.='
                    <div class="ChooseColor">
                        <div>Background Color:</div>
                        <div>
                            <input type="text" class="my-color-field" data-default-color="#cecece" value="'.$CartBGcolor.'" name="MensioCartBackgroundColor">
                        </div>
                    </div>
                    <div class="ChooseColor">
                        <div>Text Color:</div>
                        <div><input type="text" class="my-color-field" data-default-color="#ffffff" value="'.$CartTextcolor.'" name="MensioCartTextColor"></div>
                    </div>
                    <div class="ChooseColor">
                        <div>Hover Background Color:</div>
                        <div><input type="text" class="my-color-field" data-default-color="#000000" value="'.$HoverCartBGcolor.'" name="MensioHoverCartBackgroundColor" style="width:100% !important;"></div>
                    </div>
                    <div class="ChooseColor">
                        <div colspan="2">Hover Text Color:</div>
                        <div><input type="text" class="my-color-field" data-default-color="#ffffff" value="'.$HoverCartTextcolor.'" name="MensioHoverCartTextColor" style="width:100% !important;"></div>
                    </div>
                </div>
                </div>';
            $Layout.='<h2>Product Tabs Translations</h2>';
            $Layout.='<div>';
            $Layout.='<div>';
            $Layout.='<div class="Input">
                        <div>Description</div>
                        <div></div>
                    </div>';
            foreach($langs as $lang){
                if(!empty($_REQUEST['MensioProductPageTab_Description_'.$lang->code])){
                    if(!update_option( 'MensioProductPageTab_Description_'.$lang->code, filter_var($_POST['MensioProductPageTab_Description_'.$lang->code]))){
                        add_option('MensioProductPageTab_Description_'.$lang->code,filter_var($_POST['MensioProductPageTab_Description_'.$lang->code]),'','no');
                    }
                }
                if(get_option('MensioProductPageTab_Description_'.$lang->code)){
                    $Value=get_option('MensioProductPageTab_Description_'.$lang->code);
                }
                else{
                    $Value="";
                }
                $Layout.='
                    <div>
                        <div><img src="'.plugin_dir_url(__FILE__).'../../icons/flags/'.$lang->icon.'.png" width="30" /></div>
                        <div><input type="text" name="MensioProductPageTab_Description_'.$lang->code.'" placeHolder="Description" value="'.$Value.'"></div>
                    </div>';
            }
            $Layout.='<div class="Input"><div>Attributes</div><div></div></div>';
            foreach($langs as $lang){
                if(!empty($_REQUEST['MensioProductPageTab_Attributes_'.$lang->code])){
                    if(!update_option( 'MensioProductPageTab_Attributes_'.$lang->code, filter_var($_POST['MensioProductPageTab_Attributes_'.$lang->code]))){
                        add_option('MensioProductPageTab_Attributes_'.$lang->code,filter_var($_POST['MensioProductPageTab_Attributes_'.$lang->code]),'','no');
                    }
                }
                if(get_option('MensioProductPageTab_Attributes_'.$lang->code)){
                    $Value=get_option('MensioProductPageTab_Attributes_'.$lang->code);
                }
                else{
                    $Value="";
                }
                $Layout.='
                    <div>
                        <div><img src="'.plugin_dir_url(__FILE__).'../../icons/flags/'.$lang->icon.'.png" width="30" /></div>
                        <div><input type="text" name="MensioProductPageTab_Attributes_'.$lang->code.'" placeHolder="Attributes" value="'.$Value.'"></div>
                    </div>';
            }
            $Layout.='<div class="Input"><div>Files</div><div></div></div>';
            foreach($langs as $lang){
                if(!empty($_REQUEST['MensioProductPageTab_Files_'.$lang->code])){
                    if(!update_option( 'MensioProductPageTab_Files_'.$lang->code, filter_var($_POST['MensioProductPageTab_Files_'.$lang->code]))){
                        add_option('MensioProductPageTab_Files_'.$lang->code,filter_var($_POST['MensioProductPageTab_Files_'.$lang->code]),'','no');
                    }
                }
                if(get_option('MensioProductPageTab_Files_'.$lang->code)){
                    $Value=get_option('MensioProductPageTab_Files_'.$lang->code);
                }
                else{
                    $Value="";
                }
                $Layout.='
                    <div>
                        <div><img src="'.plugin_dir_url(__FILE__).'../../icons/flags/'.$lang->icon.'.png" width="30" /></div>
                        <div><input type="text" name="MensioProductPageTab_Files_'.$lang->code.'" placeHolder="Files" value="'.$Value.'"></div>
                    </div>';
            }
            if(function_exists("MoreProductTabsTranslations")){
                $Layout.=MoreProductTabsTranslations($langs);
            }
            $Layout.='</div>';
            $Layout.='</div>';
            $Layout.='<h2>Contact Inputs</h2>';
            $Layout.='<div>';
            $Layout.='<div>';
            $ContactInputs=array(
                "Username","Name","LastName","email","Password",
                "Country","Region","City","Street","ZipCode",
                "Phone","CellPhone","Message",
                "ReadTos"=>"I have read and agree the Terms of Service",
                "SendCopy"=>"Send copy to my email address",
                "Send","Signup",
                "LogoutMessage"=>"Are you sure you want to exit?",
                "ForgotPassword"=>"Forgot Password",
                "ForgotPasswordSuccess"=>"Your Password has been reset",
                "ForgotPasswordFail"=>"Username not valid",
                "PasswordChanged"=>"Password Changed",
                "PasswordChangeWrongCreds"=>"Password not changed. Wrong Credentials",
                "Login","Back","Next");
            foreach($ContactInputs as $input=>$val){
                if(is_numeric($input)){
                    $input=$val;
                }
                    $Layout.='
                    <div class="Input">
                        <div>'.$val.'</div>
                        <div></div>
                    </div>
                    ';
                    foreach($langs as $lang){
                        if(!empty($_REQUEST['MensioPress_Text'.$input.'_'.$lang->code])){
                            if(!update_option( 'MensioPress_Text'.$input.'_'.$lang->code, stripslashes_deep(filter_var($_POST['MensioPress_Text'.$input.'_'.$lang->code])) ) ){
                                add_option('MensioPress_Text'.$input.'_'.$lang->code,stripslashes_deep(filter_var($_POST['MensioPress_Text'.$input.'_'.$lang->code])),'','no');
                            }
                        }
                        if(get_option('MensioPress_Text'.$input.'_'.$lang->code)){
                            $Value=get_option('MensioPress_Text'.$input.'_'.$lang->code);
                        }
                        else{
                            $Value="";
                        }
                        $Layout.='
                            <div>
                                <div><img src="'.plugin_dir_url(__FILE__).'../../icons/flags/'.$lang->icon.'.png" width="30" /></div>
                                <div><input type="text" name="MensioPress_Text'.$input.'_'.$lang->code.'" placeHolder="'.$val.'" value="'.$Value.'"></div>
                            </div>';
                    }
            }
            $Layout.='
            </div>
            </div>
            <h2>Other Translations</h2>
            <div>
                <div>
            ';
            if(function_exists("OtherTranslationsInputs")){
                $ContactInputs=OtherTranslationsInputs();
            }
            else{
            $ContactInputs=array(
                "Cart"=>"Cart",
                "NoProdsInCart"=>"No Products found in your cart",
                "RemoveAll"=>"Remove All",
                "NoShipping"=>"No Shipping found for your country",
                "WrongCreds"=>"Wrong Credentials",
                "AddedToCart"=>"Added To Cart",
                "AddedMoreToCart"=>"Added More To Cart",
                "RemovedFromCart"=>"Removed From Cart",
                "LoginToRate"=>"Please Login to post a rating"
                );
            }
            foreach($ContactInputs as $input=>$val){
                if(is_numeric($input)){
                    $input=$val;
                }
                $Layout.='
                <div class="Input">
                    <div>'.$val.'</div>
                    <div></div>
                </div>
                ';
                foreach($langs as $lang){
                    if(!empty($_REQUEST['MensioPress_Text'.$input.'_'.$lang->code])){
                        if(!update_option( 'MensioPress_Text'.$input.'_'.$lang->code,  stripslashes_deep(filter_var($_POST['MensioPress_Text'.$input.'_'.$lang->code])) ) ){
                            add_option('MensioPress_Text'.$input.'_'.$lang->code,filter_var($_POST['MensioPress_Text'.$input.'_'.$lang->code]),'','no');
                        }
                    }
                    if(get_option('MensioPress_Text'.$input.'_'.$lang->code)){
                        $Value=get_option('MensioPress_Text'.$input.'_'.$lang->code);
                    }
                    else{
                        $Value="";
                    }
                    $Layout.='
                        <div>
                            <div><img src="'.plugin_dir_url(__FILE__).'../../icons/flags/'.$lang->icon.'.png" width="30" /></div>
                            <div><input type="text" name="MensioPress_Text'.$input.'_'.$lang->code.'" placeHolder="'.$val.'" value="'.$Value.'"></div>
                        </div>';
                }
            }
            $Layout.='
                </div>
            </div>
                ';
            $Layout.='
            <h2>Slugs</h2>';
            $Layout.='
            <div>
            <div class="normalSettings">
                <div>
                    <div>
                        <h3>Eshop Brand Slug</h3>
                    </div>
                    <div>
                        <input type="text" name="MensioBrandSlug" value="'. $eshopBrandSlug.'">
                    </div>
                </div>
                <div>
                    <div>
                        <h3>Eshop Category Slug</h3>
                    </div>
                    <div>
                        <input type="text" name="MensioCategorySlug" value="'. $eshopCategorySlug.'">
                    </div>
                </div>
                <div>
                    <div>
                        <h3>Eshop Product Slug</h3>
                    </div>
                    <div>
                        <input type="text" name="MensioProductSlug" value="'. $eshopProductSlug.'">
                    </div>
                </div>
                <div>
                    <div>
                        <h3>Mensio Page Slug</h3>
                    </div>
                    <div>
                        <input type="text" name="MensioPageSlug" value="'. $MensioPageSlug.'">
                    </div>
                </div>
            </div>
            </div>
            ';
            if(get_option("Mensio-hide-nav-links") && get_option("Mensio-hide-nav-links")=="yes"){
                $Mensiohidenavlinks=" checked";
            }
            else{
                $Mensiohidenavlinks="";
            }
            $Layout.='
            <h2>Other Settings</h2>
            <div>
            <div class="normalSettings otherSettings">
                <div>
                    <div>
                        <h3>Hide Navigation Links</h3>
                        <label>Yes <input type="radio" name="Mensio-hide-nav-links" value="yes"'.$Mensiohidenavlinks.'></label>
                        ';
            if(!get_option("Mensio-hide-nav-links")){
                $Mensiohidenavlinks=" checked";
            }
            elseif(get_option("Mensio-hide-nav-links") && get_option("Mensio-hide-nav-links")=="flags"){
                $Mensiohidenavlinks=" checked";
            }
            else{
                $Mensiohidenavlinks="";
            }
            $Layout.='
                        <label>No  <input type="radio" name="Mensio-hide-nav-links" value="no"'.$Mensiohidenavlinks.'></label>
                    </div>
                </div>';
            if(get_option("hideentrytitle") && get_option("hideentrytitle")=="yes"){
                $hideentrytitle=" checked";
            }
            else{
                $hideentrytitle="";
            }
            $Layout.='
            <div>
                <div>
                    <h3>Hide Entry Title</h3>
                    <label>Yes <input type="radio" name="hideentrytitle" value="yes"'.$hideentrytitle.'></label>
                ';
            if(!get_option("hideentrytitle")){
                $hideentrytitle=" checked";
            }
            elseif(get_option("hideentrytitle") && get_option("hideentrytitle")=="no"){
                $hideentrytitle=" checked";
            }
            else{
                $hideentrytitle="";
            }
            $Layout.='
                    <label>No  <input type="radio" name="hideentrytitle" value="no"'.$hideentrytitle.'></label>
                </div>
            </div>';
            if(!empty($_REQUEST['MensioPressNotificationBGcolor'])){
                if(!update_option( 'MensioPressNotificationBGcolor',  stripslashes_deep(filter_var($_POST['MensioPressNotificationBGcolor'])) ) ){
                    add_option('MensioPressNotificationBGcolor',filter_var($_POST['MensioPressNotificationBGcolor']),'','no');
                }
            }
            if(!get_option("MensioPressNotificationBGcolor")){
                $NotificationBGcolor="#000000";
            }
            else{
                $NotificationBGcolor= get_option("MensioPressNotificationBGcolor");
            }
            if(!empty($_REQUEST['MensioPressNotificationTextcolor'])){
                if(!update_option( 'MensioPressNotificationTextcolor',  stripslashes_deep(filter_var($_POST['MensioPressNotificationTextcolor'])) ) ){
                    add_option('MensioPressNotificationTextcolor',filter_var($_POST['MensioPressNotificationTextcolor']),'','no');
                }
            }
            if(!get_option("MensioPressNotificationTextcolor")){
                $NotificationTextcolor="#FFFFFF";
            }
            else{
                $NotificationTextcolor= get_option("MensioPressNotificationTextcolor");
            }
            if(!empty($_REQUEST['MensioPressNotificationDuration'])){
                if(!update_option( 'MensioPressNotificationDuration',  stripslashes_deep(filter_var($_POST['MensioPressNotificationDuration'])) ) ){
                    add_option('MensioPressNotificationDuration',filter_var($_POST['MensioPressNotificationDuration']),'','no');
                }
            }
            if(!get_option("MensioPressNotificationDuration")){
                $NotificationDuration='500';
            }
            else{
                $NotificationDuration= get_option("MensioPressNotificationDuration");
            }
            if(!empty($_REQUEST['MensioPressFBAppID'])){
                if(!update_option( 'MensioPressFBAppID',  stripslashes_deep(filter_var($_POST['MensioPressFBAppID'])) ) ){
                    add_option('MensioPressFBAppID',filter_var($_POST['MensioPressFBAppID']),'','no');
                }
            }
            if(!get_option("MensioPressFBAppID")){
                $MensioPressFBAppID='0';
            }
            else{
                $MensioPressFBAppID= get_option("MensioPressFBAppID");
            }
            if(!empty($_REQUEST['MensioPressNotificationCornerRadius'])){
                if(!update_option( 'MensioPressNotificationCornerRadius',  stripslashes_deep(filter_var($_POST['MensioPressNotificationCornerRadius'])) ) ){
                    add_option('MensioPressNotificationCornerRadius',filter_var($_POST['MensioPressNotificationCornerRadius']),'','no');
                }
            }
            if(!get_option("MensioPressNotificationCornerRadius")){
                $NotificationCornerRadius='0';
            }
            else{
                $NotificationCornerRadius= get_option("MensioPressNotificationCornerRadius");
            }
            if(!empty($_REQUEST['MensioPressGlobalFontSize'])){
                if(!update_option( 'MensioPressGlobalFontSize',  stripslashes_deep(filter_var($_POST['MensioPressGlobalFontSize'])) ) ){
                    add_option('MensioPressGlobalFontSize',filter_var($_POST['MensioPressGlobalFontSize']),'','no');
                }
            }
            if(!get_option("MensioPressGlobalFontSize")){
                $GlobalFontSize="1";
            }
            else{
                $GlobalFontSize= get_option("MensioPressGlobalFontSize");
            }
            $Layout.='
            <div>
                <div><br />
                    <strong>Notification Settings</strong><br /><br />
                    Background Color<br />
                    <input type="text" class="my-color-field" data-default-color="'.$NotificationBGcolor.'" value="'.$NotificationBGcolor.'" name="MensioPressNotificationBGcolor"><br />
                    Text Color<br />
                    <input type="text" class="my-color-field" data-default-color="'.$NotificationTextcolor.'" value="'.$NotificationTextcolor.'" name="MensioPressNotificationTextcolor"><br />
                    Duration<br />
                    <input type="number" name="MensioPressNotificationDuration" value="'.$NotificationDuration.'"><br /><br />
                    <i class="fa fa-facebook-square"></i> Login App ID<br />
                    <input type="number" name="MensioPressFBAppID" value="'.$MensioPressFBAppID.'"><br /><br />
                    Corner Radius<br />
                    <input type="number" name="MensioPressNotificationCornerRadius" value="'.$NotificationCornerRadius.'"><br /><br />
                    Global Font Size on <br />MensioPress texts and Titles<br />
                    <div class="select">
                        <select name="MensioPressGlobalFontSize">';
                            for($i=0.5;$i<=3.1;$i=($i+0.1)){
                                $sel=false;
                                if(number_format($i,2)==number_format($GlobalFontSize,2)){
                                    $sel=" selected";
                                }
                            $Layout.='
                            <option value="'.$i.'"'.$sel.'>'.$i.'</option>';
                            }
                        $Layout.='
                        </select>
                    </div>
                </div>
            </div>';
            $Layout.='</div>';
            $Layout.='</div>';
                $Layout.='
                    <button class="form-save-settings" onClick="jQuery(this).parent().submit();">
                        <i class="fa fa-save"></i> Save
                    </button>
                    </form></div>
                <div id="MensioMessage">
                <!--<input type="button" class="closeConfirmation" value="x">-->
                <button class="closeConfirmation">
                    <i class="fa fa-close"></i>
                </button>
                    <div id="question"></div>
                    <div id="answer-YES">Yes</div>
                    <div id="answer-NO">No</div>
                </div>
                <div id="chooseReviewTypes">';
                $Layout.='
                </div>
            </div>';
                if(empty($MensioBottomButtons)){
                    $MensioBottomButtons=array();
                }
                    $Layout.='<div id="theSettings">
                            '.mensiopress_MensioTheObjects($MensioBottomButtons).'</div>';
    $Layout.='
<div class="bottom-buttons left">
    <button class="open-settings" class="button BtnGreen BTN_Save" title="Settings" postID="'.$post_id.'">
        <div class="bubbleDescr" for="open-settings">'.$MensioBottomButtons['Settings'].'</div>
        <i class="fa fa-cog" aria-hidden="true"></i>
    </button>
</div>
<div class="bottom-buttons right">
    <button class="close-properties inactive" id="close-properties" post_type="'.$post->post_type.'">
        <div class="bubbleDescr" for="close-properties">'.$MensioBottomButtons['CloseProperties'].'</div>
        <i class="fa fa-arrow-left" aria-hidden="true"></i>
    </button>
</div>
    <div class="center-bottom-buttons">
        <div class="select">
        <select name="fast-go-to-edit" defaultvalue="'.$_GET['post'].'">';
        global $wpdb;
        $prfx = $wpdb->prefix;
        $Query = '  SELECT
                        `ID`,
                        `post_title`
                    FROM
                        `'.$prfx.'posts`,
                        `'.$prfx.'postmeta`
                    WHERE
                            `'.$prfx.'posts`.`post_status` LIKE "publish" AND
                        `'.$prfx.'posts`.`post_type` LIKE "mensio_page" AND
                        `'.$prfx.'postmeta`.`post_id`=`'.$prfx.'posts`.`ID` AND
                        `'.$prfx.'postmeta`.`meta_key`="mensio_page_function"
                    ';
        $checked=false;
        foreach($wpdb->get_results($Query) as $page){
            if($page->ID==$_REQUEST['post']){
                $checked=" selected";
            }
            $Layout.="<option value='".$page->ID."'".$checked.">".$page->post_title."</option>";
            $checked=false;
        }
    $Layout.='
        </select>
        </div>
        <button class="back-button" post_type="'.$post->post_type.'">
            <div class="bubbleDescr" for="back-button">'.$MensioBottomButtons['CloseSettings'].'</div>
            <i class="fa fa-arrow-left" aria-hidden="true"></i>
        </button>
        <button style="" class="save-settings" class="button BtnGreen BTN_Save" title="Save" postID="'.$post_id.'">
            <div class="bubbleDescr" for="save-settings">'.$MensioBottomButtons['SaveButton'].'</div>
            <i class="fa fa-floppy-o" aria-hidden="true"></i>
        </button>
        <input type="hidden" value="0" name="has-change">
        <button style="" class="revert" class="button BTN_Revert" title="Revert" postID="'.$post_id.'">
            <div class="bubbleDescr" for="revert">'.$MensioBottomButtons['Revert'].'</div>
            <i class="fa fa-undo" aria-hidden="true"></i>
        </button>
    </div>
';
        $Layout.='<div id="theEdits">
                '.MensioTheEdits($MensioBottomButtons).'</div>
                    <!--
                    <button postid="1348" status="open" class="close-edits" id="close-edits">
                        <i class="fa fa-minus" aria-hidden="true"></i>
                    </button>
                    -->
        ';
        $Layout.='<div id="thePage" class="mns-block wait-please"></div>';
        $Layout.=
                '
                        <div class="bubbleDescr" for="close-edits">'.$MensioBottomButtons['OpenCloseEdits'].'</div>
                        <div class="bubbleDescr" for="close-settings">'.$MensioBottomButtons['OpenCloseSettings'].'</div>
                        <div class="bubbleDescr" for="close-properties">'.$MensioBottomButtons['CloseProperties'].'</div>
                        ';
        echo $Layout;
        $settings = array( 'media_buttons' => false );
    }
    else{
        wp_enqueue_script("MensioPressAdminDesignPages", plugin_dir_url( __FILE__ ).'../../js/mensio-admin-design-pages.js');
        $MensioScript="window.location.href='?page=mnsObjPrintAllPages';";
        wp_add_inline_script("MensioPressAdminDesignPages", $MensioScript);
    }
}
function mensio_ajax_Open_Object_Settings_Modal() {
    $modal=new Mensio_Admin_Design_Pages_Form();
    $form=$modal->mensio_ajax_Open_Object_Settings_ModalForm();
    echo $form;
    die;
}
function mensio_get_all_group_pages($key){
    global $wpdb;
    $r = $wpdb->get_col( $wpdb->prepare( "
        SELECT DISTINCT pm.meta_value
        FROM {$wpdb->posts} as p,
             {$wpdb->postmeta} as pm,
             {$wpdb->postmeta} as pm2
        WHERE p.ID = pm.post_id
              AND p.ID = pm2.post_id
              AND pm.meta_key = '%s'
              AND p.post_type = 'brand'
              AND p.post_status = 'publish'
              AND (pm2.meta_key = 'show_in_list' AND pm2.meta_value = '1')
    ", $key ) );
    return $r;
}
function newMensioPage(){
    $check=explode("::",$_POST['Security']);
    $check=$check[0];
    if(empty($_POST['Security']) || wp_verify_nonce($check,"Active_Page_PageDesigner")==false){
        die;
    }
    $content='<div class="mns-html-content"><p>&nbsp;</p></div>';
    $my_post = array(
      'post_title'    => filter_var($_POST['NewMensioPageTitle']),
      'post_content'  => $content,
      'post_status'   => 'publish',
      'post_type'     => 'mensio_page'
    );
    $post_id=wp_insert_post( $my_post );
    if(!empty(filter_var($_POST['NewMensioPageFunction']))){
        if ( ! add_post_meta( $post_id, 'mensio_page_function', filter_var($_POST['NewMensioPageFunction']), true ) ) { 
           update_post_meta ( $post_id, 'mensio_page_function', filter_var($_POST['NewMensioPageFunction']) );
        }
    }
    echo $post_id;
    die;
}
function mensiopress_delMensioPage(){
    $check=explode("::",$_POST['Security']);
    $check=$check[0];
    if(empty($_POST['Security']) || wp_verify_nonce($check,"Active_Page_PageDesigner")==false){
        die;
    }
    wp_trash_post( $post_id = filter_var($_POST['MensioPageToDel']) );
}
add_action( 'pre_get_posts' ,'exclude_this_page' );
function exclude_this_page( $query ) {
        if( !is_admin() )
                return $query;
        global $pagenow;
        $homePageID=get_option("page_on_front");
        $homePageID=false;
        if( 'edit.php' == $pagenow && ( get_query_var('post_type') && 'page' == get_query_var('post_type') ) )
                $query->set( 'post__not_in', array( $homePageID ) ); // array page ids
        return $query;
}
add_action( 'wp_ajax_mensiopress_mns_quickUpdate', 'mensiopress_mns_quickUpdate' );
add_action( 'wp_ajax_nopriv_mensiopress_mns_quickUpdate', 'mensiopress_mns_quickUpdate' );
function mensiopress_mns_quickUpdate(){
    $check=explode("::",$_POST['Security']);
    $check=$check[0];
    if(empty($_POST['Security']) || wp_verify_nonce($check,"Active_Page_PageDesigner")==false){
        die;
    }
    if(!empty(filter_var($_POST['MensioSkin']))){
        setcookie("MensioSkin", filter_var($_POST['MensioSkin']),time() + (86400 * 30));
        $_COOKIE['MensioSkin']=filter_var($_POST['MensioSkin']);
    }
    die;
}