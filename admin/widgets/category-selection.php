<?php
function MensioCategoriesInstallWidget() {
    register_widget( 'MensioCategoriesWidget' );
}
add_action( 'widgets_init', 'MensioCategoriesInstallWidget' );
class MensioCategoriesWidget extends WP_Widget {
    function __construct() {
        parent::__construct(
        'MensioCategoriesWidget', 
        __('MensioPress Product Categories', 'wpb_widget_domain'), 
        array( 'description' => __( "Print your Categories anywhere you want", 'wpb_widget_domain' ), ) 
        );
    }
    public function widget( $args, $instance ) {
        echo "<div class='MensioPressWidgetCategories'>";
        if ( isset( $instance[ 'title_'.$_SESSION['MensioThemeLangShortcode'] ] ) ) {
            $title = $instance[ 'title_'.$_SESSION['MensioThemeLangShortcode']];
            echo "<div class='mensioPressWidgetTitle'>".$title."</div>";
        }
        $getCategories=new mensio_products_categories();
        $getCategories->Set_Parent("TopLevel");
        $Categories=$getCategories->LoadProductCategoriesTreeDataSet();
        $ViewMensioCategories= $instance['ViewMensioCategories'];
        $Object=new mnsGetFrontEndLink();
        $translateCatName=new mensio_seller();
        echo "<ul>";
        foreach($Categories as $cat){
            if(!in_array($cat->category, $ViewMensioCategories)){
                continue;
            }
            $getCategories->Set_Parent($cat->category);
            $SubCategories=$getCategories->LoadProductCategoriesTreeDataSet();
            $getCategories->Set_UUID($cat->category);
            $categoryData=$getCategories->GetCategoryData();
            $translateCatName->Set_CategoryID($cat->category);
            $CategoryName=$translateCatName->TranslateCategory();
            if(!empty($CategoryName[0]->name)){
                $CategoryName=$CategoryName[0]->name;
            }
            elseif(empty($CategoryName)){
                $CategoryName=$cat->name;
            }
            echo "<li><a href='".$Object->CategoryLink($cat->category)."'>".$CategoryName."</a>";
            if(!empty($SubCategories)){
                echo "<ul>";
                foreach($SubCategories as $subcat){
                    if(!in_array($subcat->category, $ViewMensioCategories)){
                        continue;
                    }
                    $getCategories->Set_UUID($subcat->category);
                    $categoryData=$getCategories->GetCategoryData();
                    $translateCatName->Set_CategoryID($subcat->category);
                    if(!empty($CategoryName[0]->name)){
                        $CategoryName=$translateCatName->TranslateCategory();
                        $CategoryName=$CategoryName[0]->name;
                    }
                    elseif(empty($CategoryName[0]->name)){
                        $CategoryName=$subcat->name;
                    }
                    echo "<li><a href='".$Object->CategoryLink($subcat->category)."'>".$subcat->name."</a></li>";
                }
                echo "</ul>";
            }
            echo "</li>";
        }
        echo "</ul>";
        echo "<br />";
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
        <strong>Categories</strong><br />
        <?php
        $ViewMensioCategories=array();
        if(!empty($instance['ViewMensioCategories'])){
            $ViewMensioCategories= $instance['ViewMensioCategories'];
        }
        $getCategories=new mensio_products_categories();
        $getCategories->Set_Parent("TopLevel");
        $Categories=$getCategories->LoadProductCategoriesTreeDataSet();
        foreach($Categories as $cat){
            $getCategories->Set_Parent($cat->category);
            $SubCategories=$getCategories->LoadProductCategoriesTreeDataSet();
                ?>
            <br />
            <label for="<?php echo $this->get_field_id($cat->category); ?>">
                <input class="" id="<?php echo $this->get_field_id($cat->category); ?>" name="<?php echo $this->get_field_name('ViewMensioCategories'); ?>[]" type="checkbox" value="<?php echo $cat->category ?>"
                    <?php if( in_array($cat->category, $ViewMensioCategories)){ echo 'checked="checked"'; } ?> />
                <?php _e($cat->name.':'); ?>
            </label><br />
            <?php
            if(!empty($SubCategories)){
                foreach($SubCategories as $subcat){
                ?>
            <label for="<?php echo $this->get_field_id($subcat->category); ?>">
                &nbsp;
                <input class="" id="<?php echo $this->get_field_id($subcat->category); ?>" name="<?php echo $this->get_field_name('ViewMensioCategories'); ?>[]" type="checkbox" value="<?php echo $subcat->category ?>"
                    <?php if( in_array($subcat->category, $ViewMensioCategories)){ echo 'checked="checked"'; } ?> />
                <?php _e($subcat->name.':'); ?>
            </label><br>
                <?php
                }
            }
        }
        echo '
        <br /><br/>';
    }
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['ViewMensioCategories'] = $new_instance['ViewMensioCategories'];
        $langs=new mensio_languages();
        $langs=$langs->GetActiveLanguages();
        foreach($langs as $lang){
            $instance['title_'.$lang->code] = ( ! empty( $new_instance['title_'.$lang->code] ) ) ? strip_tags( $new_instance['title_'.$lang->code] ) : '';
        }
        return $instance;
    }
}