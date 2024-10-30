<?php
if(MENSIO_FLAVOR=='STD'){
function MensioFavoritesInstallWidget() {
    register_widget( 'MensioFavoritesWidget' );
}
add_action( 'widgets_init', 'MensioFavoritesInstallWidget' );
class MensioFavoritesWidget extends WP_Widget {
    function __construct() {
        parent::__construct(
        'MensioFavoritesWidget', 
        __('MensioPress Favorites List', 'wpb_widget_domain'), 
        array( 'description' => __( "Print your users' cart anywhere you want", 'wpb_widget_domain' ), ) 
        );
    }
    public function widget( $args, $instance ) {
        $get=new mnsFrontEndObject();
        $array=array();
        if(!empty($_SESSION['MensioFavorites'])){
            $getFavorites=new MensioFlavored();
            $array=$getFavorites->mnsFrontEndFavorites(false);
        }
        else{
            $array=array();
        }
        if(!empty($instance['icon-color'])){
            $instance['Icon']="Favorites".ucfirst($instance['icon-color'])."Icon";
        }
        else{
            $instance['Icon']="FavoritesWhiteIcon";
        }
        $get=new mnsGetFrontEndLink();
        $Link=$get->FavoritesPage();
        $Total=false;
        if(empty($instance['title_'.$_SESSION['MensioThemeLangShortcode']])){
            $instance['title']="Favorites List";
        }
        else{
            $instance['title']=$instance['title_'.$_SESSION['MensioThemeLangShortcode']];
        }
            if(get_option('MensioPress_TextComparisonPage_'.$_SESSION['MensioThemeLangShortcode'])){
                $LinkText=ucfirst(get_option('MensioPress_TextComparisonPage_'.$_SESSION['MensioThemeLangShortcode']));
            }
            else{
                $LinkText="Favorites Page";
            }
            $NoProdsText= get_option("MensioPress_TextNoProdsinFavoritesList_".$_SESSION['MensioThemeLangShortcode']);
            if(empty($NoProdsText)){
                $NoProdsText="No Products in Favorites List";
            }
        echo MensioPressWidgetListProducts("FavoritesList",$array,$instance,$Link,$LinkText,$NoProdsText);
    }
    public function form( $instance ) {
        if(empty($instance['compare-color'])){
            $FavoritesColor="black";
        }
        else{
            $FavoritesColor=$instance['compare-color'];
        }
        ?>
        <br />
        <strong>Title:</strong><br />
        <table>
        <?php
        $langs=new mensio_languages();
        $langs=$langs->GetActiveLanguages();
        foreach($langs as $lang){
            if ( isset( $instance[ 'title_'.$lang->code ] ) ) {
                $title = $instance[ 'title_'.$lang->code ];
            }
            else {
                $title = __( 'Favorites List', 'wpb_widget_domain' );
            }
            ?>
            <tr>
                <td>
                    <img src="<?php echo plugin_dir_url(__FILE__); ?>../../admin/icons/flags/<?php echo $lang->icon; ?>.png" width="30" />
                </td>
                <td>
                    <label for="<?php echo $this->get_field_id( 'title_'.$lang->code ); ?>"></label> 
                    <input placeholder="Favorites List" class="widefat" id="<?php echo $this->get_field_id( 'title_'.$lang->code ); ?>" name="<?php echo $this->get_field_name( 'title_'.$lang->code ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
                </td>
            </tr>
        <?php } ?>
        </table>
        <br />
        <strong>Icon Color:</strong><br/>
        <label for="<?php echo $this->get_field_id('icon-color'); ?>">
            <input class="" id="<?php echo $this->get_field_id('black'); ?>" name="<?php echo $this->get_field_name('icon-color'); ?>" type="radio" value="black" <?php  if((!empty($instance['icon-color']) && $instance['icon-color']=='black')){ echo 'checked="checked"'; } ?> />
            <?php _e('Black:'); ?>
        </label><br />
        <label for="<?php echo $this->get_field_id('icon-color'); ?>">
            <input class="" id="<?php echo $this->get_field_id('white'); ?>" name="<?php echo $this->get_field_name('icon-color'); ?>" type="radio" value="white" <?php if((!empty($instance['icon-color']) && $instance['icon-color']=='white') || empty($instance['icon-color'])){ echo 'checked="checked"'; } ?> />
            <?php _e('White:'); ?>
        </label><br><br />
        <strong>Options:</strong><br/>
        <label for="<?php echo $this->get_field_id('show-quantities'); ?>">
            <input class="" id="<?php echo $this->get_field_id('yes'); ?>" name="<?php echo $this->get_field_name('show-quantities'); ?>" type="checkbox" value="yes" <?php if(!empty($instance['show-quantities']) && $instance['show-quantities'] === 'yes'){ echo 'checked="checked"'; } ?> />
            <?php _e('Show Quantities:'); ?>
        </label>
        <br /><br />
        <strong>Background Gradient:</strong><br />
        <?php
        $grad1="ff0000";
        if(!empty($instance['gradient_1'])){
            $grad1=$instance['gradient_1'];
        }
        ?>
        <label for="<?php echo $this->get_field_id('gradient_1'); ?>"><?php //_e( 'Background Grad 1:' ); ?></label>
        <input type="text"  name="<?php echo $this->get_field_name('gradient_1'); ?>" class="my-color-field" data-default-color="#ff0000" value="<?php echo $grad1; ?>" />
          <br />
        <?php
        $grad2="ff0000";
        if(!empty($instance['gradient_2'])){
            $grad2=$instance['gradient_2'];
        }
        ?>
        <label for="<?php echo $this->get_field_id('gradient_2'); ?>"><?php// _e( 'Background Grad 2:' ); ?></label>
        <input type="text"  name="<?php echo $this->get_field_name('gradient_2'); ?>" class="my-color-field" data-default-color="#ff0000" value="<?php echo $grad2; ?>" />
        <br /><br/>
    <?php    
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wp-color-picker' );
    $CustomScript='
        jQuery("document").ready(function(){
            jQuery(".my-color-field").wpColorPicker({
                        change: function (event, ui) {
                            var element = event.target;
                            var color = ui.color.toString();
                            jQuery(this).closest("form").find("input[type=submit]").prop("disabled",false);
                        }
                    });
        });';
    wp_enqueue_script(
           'MensioPressFavoritesColorPicker',
           plugin_dir_url( __FILE__ ) . '../js/custom.js',
           array(),
           '1.0' );
   wp_add_inline_script( 'MensioPressFavoritesColorPicker',
           $CustomScript
           );
    }
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        if($new_instance['show-quantities']==false){
            $new_instance['show-quantities']="no";
        }
        $langs=new mensio_languages();
        $langs=$langs->GetActiveLanguages();
        foreach($langs as $lang){
            $instance['title_'.$lang->code] = ( ! empty( $new_instance['title_'.$lang->code] ) ) ? strip_tags( $new_instance['title_'.$lang->code] ) : '';
        }
        $instance['icon-color'] = ( ! empty( $new_instance['icon-color'] ) ) ? strip_tags( $new_instance['icon-color'] ) : '';
        $instance['show-quantities'] = ( ! empty( $new_instance['show-quantities'] ) ) ? strip_tags( $new_instance['show-quantities'] ) : '';
        for($i=1;$i<=2;$i++){
            $instance['gradient_'.$i] = ( ! empty( $new_instance['gradient_'.$i] ) ) ? strip_tags( $new_instance['gradient_'.$i] ) : '';
        }
        return $instance;
    }
}
}