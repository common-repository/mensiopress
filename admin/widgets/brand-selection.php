<?php
function MensioBrandsInstallWidget() {
    register_widget( 'MensioBrandsWidget' );
}
add_action( 'widgets_init', 'MensioBrandsInstallWidget' );
class MensioBrandsWidget extends WP_Widget {
    function __construct() {
        parent::__construct(
        'MensioBrandsWidget', 
        __('MensioPress Brands Selection', 'wpb_widget_domain'), 
        array( 'description' => __( "Print your Brands", 'wpb_widget_domain' ), ) 
        );
    }
    public function widget( $args, $instance ) {
        echo "<div class='MensioPressWidgetBrands'>";
        if ( isset( $instance[ 'title_'.$_SESSION['MensioThemeLangShortcode'] ] ) ) {
            $title = $instance[ 'title_'.$_SESSION['MensioThemeLangShortcode']];
            echo "<div class='mensioPressWidgetTitle'>".$title."</div>";
        }
        echo "<ul>";
        $getBrands=new mensio_products_brands();
        $allBrands=$getBrands->LoadProductBrandsDataSet();
        $Object=new mnsGetFrontEndLink();
        foreach($allBrands as $brand){
            if(in_array($brand->uuid,$instance['ViewMensioBrands'])){
                $Link=$Object->BrandLink($brand->uuid);
                echo "<li><a href='".$Link."'>".$brand->name."</a></li>";
            }
        }
        echo "</ul><br />";
        echo "</div>";
    }
    public function form( $instance ) {
        $langs=new mensio_languages();
        $langs=$langs->GetActiveLanguages();
        ?>
        <br />
        <strong>Title:</strong><br />
        <table>
        <?php
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
        <hr />
        <strong>Brands</strong><br />
        <?php
        $ViewMensioBrands= $instance['ViewMensioBrands'];
        $getBrands=new mensio_products_brands();
        $allBrands=$getBrands->LoadProductBrandsDataSet();
        foreach($allBrands as $brand){
            ?>
            <br />
            <label for="<?php echo $this->get_field_id($brand->uuid); ?>">
                <input class="" id="<?php echo $this->get_field_id($brand->uuid); ?>" name="<?php echo $this->get_field_name('ViewMensioBrands'); ?>[]" type="checkbox" value="<?php echo $brand->uuid; ?>"
                <?php if( in_array($brand->uuid, $ViewMensioBrands)){ echo 'checked="checked"'; } ?> />
                <?php _e($brand->name.':'); ?>
            </label>
            <?php
        }
        ?>
        <br /><br/>
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