<?php
add_action( 'admin_enqueue_scripts', 'MensioPickColorForCart' );
function MensioPickColorForCart( $hook_suffix ) {
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wp-color-picker' );
}
function MensioCartInstallWidget() {
    register_widget( 'MensioCartWidget' );
}
add_action( 'widgets_init', 'MensioCartInstallWidget' );
class MensioCartWidget extends WP_Widget {
    function __construct() {
        parent::__construct(
        'MensioCartWidget', 
        __('MensioPress Cart', 'wpb_widget_domain'), 
        array( 'description' => __( "Print your users' cart anywhere you want", 'wpb_widget_domain' ), ) 
        );
    }
    public function widget( $args, $instance ) {
        $get=new mnsFrontEndObject();
        $array=$get->mnsFrontEndCart();
        $Link=new mnsGetFrontEndLink();
        $Link=$Link->CheckoutPage();
        $Total=false;
        if(empty($instance['title_'.$_SESSION['MensioThemeLangShortcode']])){
            $instance['title']="Shopping Cart";
        }
        else{
            $instance['title']=$instance['title_'.$_SESSION['MensioThemeLangShortcode']];
        }
        if(!empty($instance['cart-color'])){
            $instance['Icon']="Cart".ucfirst($instance['cart-color'])."Icon";
        }
        else{
            $instance['Icon']="CartWhiteIcon";
        }
            if(get_option('MensioPress_TextCart_'.$_SESSION['MensioThemeLangShortcode'])){
                $LinkText=ucfirst(get_option('MensioPress_TextCart_'.$_SESSION['MensioThemeLangShortcode']));
            }
            else{
                $LinkText="Cart";
            }
                        if(get_option('MensioPress_TextNoProdsInCart_'.$_SESSION['MensioThemeLangShortcode'])){
                            $NoProdsText=ucfirst(get_option('MensioPress_TextNoProdsInCart_'.$_SESSION['MensioThemeLangShortcode']));
                        }
                        else{
                            $NoProdsText="No Products found in your cart";
                        }
        echo MensioPressWidgetListProducts("Cart",$array,$instance,$Link,$LinkText,$NoProdsText);
    }
    public function form( $instance ) {
        if(empty($instance['cart-color'])){
            $CartColor="black";
        }
        else{
            $CartColor=$instance['cart-color'];
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
                $title = __( 'Shopping Cart', 'wpb_widget_domain' );
            }
            ?>
            <tr>
                <td>
                    <img src="<?php echo plugin_dir_url(__FILE__); ?>../../admin/icons/flags/<?php echo $lang->icon; ?>.png" width="30" />
                </td>
                <td>
                    <label for="<?php echo $this->get_field_id( 'title_'.$lang->code ); ?>"></label> 
                    <input placeholder="Shopping Cart" class="widefat" id="<?php echo $this->get_field_id( 'title_'.$lang->code ); ?>" name="<?php echo $this->get_field_name( 'title_'.$lang->code ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
                </td>
            </tr>
        <?php } ?>
        </table>
        <br />
        <strong>Cart Color:</strong><br/>
        <label for="<?php echo $this->get_field_id('cart-color'); ?>">
            <input class="" id="<?php echo $this->get_field_id('black'); ?>" name="<?php echo $this->get_field_name('cart-color'); ?>" type="radio" value="black" <?php if($CartColor === 'black'){ echo 'checked="checked"'; } ?> />
            <?php _e('Black:'); ?>
        </label><br />
        <label for="<?php echo $this->get_field_id('cart-color'); ?>">
            <input class="" id="<?php echo $this->get_field_id('white'); ?>" name="<?php echo $this->get_field_name('cart-color'); ?>" type="radio" value="white" <?php if($CartColor === 'white'){ echo 'checked="checked"'; } ?> />
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
    $MensioScript='
        $(".my-color-field").wpColorPicker({
          change: function(event, ui) {
            $(".my-color-field").css( "color", ui.color.toString());
            $(".my-color-field").css( "background", ui.color.toString());
          }
        });
    ';
    wp_enqueue_script(
           'MensioPressCartColorPicker',
           plugin_dir_url( __FILE__ ) . '../js/custom.js',
           array(),
           '1.0' );
   wp_add_inline_script( 'MensioPressCartColorPicker',
           $MensioScript
           );
    }
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $langs=new mensio_languages();
        $langs=$langs->GetActiveLanguages();
        foreach($langs as $lang){
            $instance['title_'.$lang->code] = ( ! empty( $new_instance['title_'.$lang->code] ) ) ? strip_tags( $new_instance['title_'.$lang->code] ) : '';
        }
        if($new_instance['show-quantities']==false){
            $new_instance['show-quantities']="no";
        }
        $instance['cart-color'] = ( ! empty( $new_instance['cart-color'] ) ) ? strip_tags( $new_instance['cart-color'] ) : '';
        $instance['show-quantities'] = ( ! empty( $new_instance['show-quantities'] ) ) ? strip_tags( $new_instance['show-quantities'] ) : '';
        for($i=1;$i<=2;$i++){
            $instance['gradient_'.$i] = ( ! empty( $new_instance['gradient_'.$i] ) ) ? strip_tags( $new_instance['gradient_'.$i] ) : '';
        }
        return $instance;
    }
}
?>