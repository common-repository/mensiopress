<?php
function mensiopress_get_top_brands($atts){
    $brands=new mnsFrontEndObject();
    $brands=$brands->mnsFrontEndTopBrands();
    $list=MensioList($brands,$atts,"Top Brands",false);
    return $list;
}
function mensiopress_blank(){
    echo "";
}
function mnsTextBlock($atts){
    $html=false;
    if(!$atts && !empty($_GET['action'])){
            $html="Your Text";
    }
    else{
        $html= do_shortcode(urldecode($atts['mnstext_'.$_SESSION['MensioThemeLangShortcode']]));
    }
    return "<div class='mnsText'>".$html."</div>";
}
function mnsCustomHTML($atts){
    $titleStyle='';
    if(!empty($atts['titlecolor'])){
        $titleStyle.="color:#".$atts['titlecolor'];
    }
    if(!empty($atts['titlesize'])){
        $tag=$atts['titlesize'];
    }
    else{
        $tag="h3";
    }
    if(empty($atts['text'])){
        $atts['text']="Example";
    }
    $html=false;
    if(!empty($_GET['action']) && $_GET['action']=='mns-html-edit'){
        $content=$atts['text'];
        $editor_id="444";
        $settings =   array(
            'wpautop' => false, // use wpautop?
            'media_buttons' => false, // show insert/upload button(s)
            "drag_drop_upload" => true,
            'textarea_name' => $editor_id, // set the textarea name to something different, square brackets [] can be used here
            'textarea_rows' => 10, // rows="..."
            'tabindex' => '',
            'editor_css' => '', // intended for extra styles for both visual and HTML editors buttons, needs to include the <style> tags, can use "scoped".
            'editor_class' => '', // add extra class(es) to the editor textarea
            'teeny' => false, // output the minimal editor config used in Press This
            'dfw' => false, // replace the default fullscreen with DFW (supported on the front-end in WordPress 3.4)
            'tinymce' => true, // load TinyMCE, can be used to pass settings directly to TinyMCE using an array()
            'quicktags' => true // load Quicktags, can be used to pass settings directly to Quicktags using an array()
        );
        ob_start();
        wp_editor(urldecode(strip_shortcodes($content)), $editor_id, $settings );
        $editor = ob_get_clean();
        $html.=$editor;
        $html.="<button class='update'>Update</button>";
        $CustomScript="
            jQuery('#thePage').ready(function(){
                jQuery('#wp-".$editor_id."-wrap').ready(function(){
                    jQuery('#insert-media-button').click();
                    jQuery('.media-modal').parent().hide();
                    jQuery('.wp-editor-tabs button').prop('disabled',false);
                });
                jQuery('.update').prop('disabled',false);
                jQuery('.update').click(function(){
                    jQuery('.wp-switch-editor.switch-tmce').click();
                    var editor = tinyMCE.get('".$editor_id."');
                    var content = editor.getContent();
                    jQuery(this).closest('.mns-block')
                        .attr('customhtml',encodeURI(content))
                        .removeClass('currentBlockEdit')
                        .find('.CustomHTML').html(content);
                });
            });
            ";       
        $tt=rand(1,1000);
        wp_enqueue_script("MensioPressPublicJS".$tt,plugin_dir_url(dirname(__FILE__))."js/empty.js");
        $CustomScript='var ajaxurl="'.admin_url('admin-ajax.php').'";';
        wp_add_inline_script( "MensioPressPublicJS".$tt,
               $CustomScript
               );
        $html.="
        <div class='CustomHTML'>".urldecode(strip_shortcodes($atts['text']))."</div>";
    }
    else{
        if(!empty($atts['editing'])){
            return false;
        }
        $html= do_shortcode(urldecode($atts['text']));
    }
        return $html;
    return "<div class='CustomHTML'>".urldecode($html)."</div>";
}
function MensioObject($att){
    if(empty($att)){
       die; 
    }
    elseif(!empty($att['uuid'])){
        $UUID=$att['uuid'];
        global $post;
        $slug=$post->post_name;
        $getUUID=new mensio_seller();
        $getObjectProperties=$getUUID->MensioSearchSlug($slug);
        if(empty($getObjectProperties)){
            $change=new mensio_seller();
            $change=$change->MensioSearchForSlug($att['uuid']);
            $newSlug=$change[0]->slug;
        }
        elseif($getObjectProperties[0]->type=="Brand"){
            $brandID=$att['uuid'];
            $getBrandPage=new mnsGetFrontEndLink();
            $BrandPage=$getBrandPage->BrandPage();
            $GLOBALS['brand_id']=$att['uuid'];
            $post= get_post($BrandPage);
            echo do_shortcode($post->post_content);
        }
        elseif($getObjectProperties[0]->type=="Category"){
            $categoryID=$att['uuid'];
            $getCategoryPage=new mnsGetFrontEndLink();
            $CategoryPage=$getCategoryPage->BrandPage();
            $GLOBALS['category_id']=$att['uuid'];
            $post= get_post($CategoryPage);
            echo do_shortcode($post->post_content);
        }
        elseif($getObjectProperties[0]->type=="Product"){
            $productID=$att['uuid'];
            $getProductPage=new mnsGetFrontEndLink();
            $ProductPage=$getProductPage->ProductPage();
            if(empty($ProductPage) || !empty($_GET['page']) && ($_GET['page']=="eshop")){
                echo"<div class='mns-html-content'>";
                echo"<div class='mns-block mns-product'>";
                echo mns_show_Product(array("prod_id",$productID));
                echo "</div></div>";
            }
            else{
                $GLOBALS['product_id']=$att['uuid'];
                $post= get_post($ProductPage);
                echo do_shortcode($post->post_content);
            }
        }
    }
}
function MensioLanguages($instance=false){
    echo "<div class='MensioPressChangeLanguage'>";
    if(empty($instance['position'])){
        $instance['position']=false;
    }
    $title='Languages';
    if(empty($instance['position']) && $instance['position']!='widget'){
        echo "<div class='mensioPressWidgetTitle'>".$title."</div>";
    }
    $langs=new mensio_seller();
    $langs=$langs->GetActiveThemeLanguages();
    global $wp;
    $currentURL = str_replace("//","/",$_SERVER['REQUEST_URI']);
    $cur=explode("/",$currentURL);
    $currentLang = $cur[1];
    $currentURL=str_replace($currentLang."/","",$currentURL);
    if(!in_array($currentLang, $_SESSION['MensioThemeLanguages'])){
        $currentURL=$_SERVER['REQUEST_URI'];
    }
   global $post;
   if(is_404()){
       $post= get_post(get_option("page_on_front") );
   }
    $languages=array();
    $i=0;
    foreach($langs['Data'] as $lang){
        if(strpos($lang->icon, "http://") !== false){
            $icon=$lang->icon;
        }
        else{
            $icon= plugin_dir_url(__FILE__)."../../admin/icons/flags/".$lang->icon.".png";
        }
        if ( get_option('permalink_structure') == true ){
            $link="/".$lang->code."/".$currentURL;
            $link=str_replace("/".$_SESSION['MensioDefaultLangShortcode']."/","",$link);
            $link="/".str_replace("//","/",$link);
            $link=str_replace("//","/",$link);
        }
        else{
            $link="".str_replace(
                    "=".$_SESSION['MensioThemeLangShortcode'],
                    "=".$lang->code,
                    $currentURL
                );
        }
        if(empty($post->ID)){
            return false;
        }
        if(get_option("page_on_front")==$post->ID && (!empty($post->ID)) && $lang->uuid!=$_SESSION['MensioDefaultLang']){
            $link= get_home_url()."/".$lang->code."/action/".$post->post_name;
        }
        $languages[$i]['name']=$lang->name;
        $languages[$i]['code']=$lang->code;
        $languages[$i]['icon']=$icon;
        $languages[$i]['link']=$link;
        $i++;
        $link="";
    }
    $imgs="";
    foreach($languages as $lang){
        $imgs.= "<a href='".$lang['link']."'><img src='".$lang['icon']."' alt='Change to Language ".$lang['code']."' title='Change to Language ".$lang['code']."' /></a> &nbsp;";
    }
    $select= "<select id='MensioChangeLanguage'>";
    foreach($languages as $lang){
        $sel='';
        if($_SESSION['MensioThemeLangShortcode']==$lang['code']){
            $sel=" selected";
        }
        $tt=time().rand(999,1000);
        $select.=  "<option style='background:url(".$lang['icon'].");background-size:100% 100%;' id='".$tt."' value='".$lang['code']."'".$sel." link='".$lang['link']."'>".$lang['name']."</option>";
    }
    $select.=  "</select>";
    if(!get_option("MensioLanguagesButtons")){
        $MensioLanguagesButtons="flags";
    }
    else{
        $MensioLanguagesButtons= get_option("MensioLanguagesButtons");
    }
    if($instance['ViewMensioLangs']=="Flags"){
        $MensioLanguagesButtons="flags";
    }
    elseif($instance['ViewMensioLangs']=="Dropdown"){
        $MensioLanguagesButtons="select";
    }
    echo "<div id='MensioLangs' class='".$MensioLanguagesButtons."'>"
            ."<div class='flag-langs'>".$imgs."</div>"
            ."<div class='select-langs'>".$select."</div>";
    foreach($languages as $lang){
    }
    echo"</div>";
    echo"</div>";
}
function MensioHomepage($att){
    $Home=get_post($att['page']);
    if(!empty($Home->post_content)){
        if(empty($att['foradmin'])){
            echo do_shortcode($Home->post_content);
        }
        else{
            return $att['page'];
        }
    }
    else{
        echo "Not Found";
    }
}
