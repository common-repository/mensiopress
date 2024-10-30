<?php
function MensioUserInstallWidget() {
    register_widget( 'MensioUserWidget' );
}
add_action( 'widgets_init', 'MensioUserInstallWidget' );
class MensioUserWidget extends WP_Widget {
    function __construct() {
        parent::__construct(
        'MensioUserWidget', 
        __('MensioPress User', 'wpb_widget_domain'), 
        array( 'description' => __( "Print User's data", 'wpb_widget_domain' ), ) 
        );
    }
    public function widget( $args, $instance ) {
        echo "<div class='MensioPressWidgetUser MensioPressUserQuickMenu'>";
            echo mensiopress_login_form(array("hide-fb"=>"no","position"=>"widget"));
        echo "</div>";
    }
    public function form( $instance ) {
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
                $title = __( 'New title', 'wpb_widget_domain' );
            }
            ?>
            <tr>
                <td>
                    <img src="<?php echo plugin_dir_url(__FILE__); ?>../../admin/icons/flags/<?php echo $lang->icon; ?>.png" width="30" />
                </td>
                <td>
                    <label for="<?php echo $this->get_field_id( 'title_'.$lang->code ); ?>"></label> 
                    <input class="widefat" id="<?php echo $this->get_field_id( 'title_'.$lang->code ); ?>" name="<?php echo $this->get_field_name( 'title_'.$lang->code ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
                </td>
            </tr>
        <?php } ?>
        </table>
        <?php
    }
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $langs=new mensio_languages();
        $langs=$langs->GetActiveLanguages();
        foreach($langs as $lang){
            $instance['title_'.$lang->code] = ( ! empty( $new_instance['title_'.$lang->code] ) ) ? strip_tags( $new_instance['title_'.$lang->code] ) : '';
        }
        $instance['ViewMensioBrands'] = $new_instance['ViewMensioBrands'];
        return $instance;
    }
}