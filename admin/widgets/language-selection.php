<?php
function MensioLangsInstallWidget() {
    register_widget( 'MensioLangsWidget' );
}
add_action( 'widgets_init', 'MensioLangsInstallWidget' );
class MensioLangsWidget extends WP_Widget {
    function __construct() {
        parent::__construct(
        'MensioLangsWidget', 
        __('MensioPress Languages Selection', 'wpb_widget_domain'), 
        array( 'description' => __( "Print your languages so your members can switch language anytime", 'wpb_widget_domain' ), ) 
        );
    }
    public function widget( $args, $instance ) {
        $instance['position']='widget';
        echo "<div class='MensioPressWidgetLanguages'>";
        echo MensioLanguages($instance);
        echo "<br />";
        echo "</div>";
    }
    public function form( $instance ) {
        if ( isset( $instance[ 'title' ] ) ) {
        $title = $instance[ 'title' ];
        }
        else {
        $title = __( 'New title', 'wpb_widget_domain' );
        }
        $ViewMensioLangs= false;
        if(!empty($instance['ViewMensioLangs'])){
            $ViewMensioLangs= esc_attr($instance['ViewMensioLangs']);
        }
        ?><br />
        <strong>Options</strong>
        <br />
        <label for="<?php echo $this->get_field_id('ViewMensioLangs'); ?>">
            <input class="" id="<?php echo $this->get_field_id('Flags'); ?>" name="<?php echo $this->get_field_name('ViewMensioLangs'); ?>" type="radio" value="Flags" <?php if($ViewMensioLangs === 'Flags'){ echo 'checked="checked"'; } ?> />
            <?php _e('Flags'); ?>
        </label><br>
        <label for="<?php echo $this->get_field_id('ViewMensioLangs'); ?>">
            <input class="" id="<?php echo $this->get_field_id('Dropdown'); ?>" name="<?php echo $this->get_field_name('ViewMensioLangs'); ?>" type="radio" value="Dropdown" <?php if($ViewMensioLangs === 'Dropdown'){ echo 'checked="checked"'; } ?> />
            <?php _e('DropDown'); ?>
        </label>
        <br /><br/>
    <?php 
    }
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['ViewMensioLangs'] = ( ! empty( $new_instance['ViewMensioLangs'] ) ) ? strip_tags( $new_instance['ViewMensioLangs'] ) : '';
        return $instance;
    }
}