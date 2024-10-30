<?php
class mensio_test_class {
  function mensio_test_display() {
    global $wpdb;
    $prfx = $wpdb->prefix;
    return 'This is a test with prefix : '.$prfx;
  }
}
add_action('wp_ajax_mensiopress_choose_Region','mensiopress_choose_Region');
add_action('wp_ajax_nopriv_mensiopress_choose_Region','mensiopress_choose_Region');
add_action('wp_ajax_mensiopress_CollectVisitorData','mensiopress_CollectVisitorData' );
add_action('wp_ajax_nopriv_mensiopress_CollectVisitorData','mensiopress_CollectVisitorData' );
add_action('wp_ajax_mensiopress_KillNavSessions','mensiopress_KillNavSessions' );
add_action('wp_ajax_nopriv_mensiopress_KillNavSessions','mensiopress_KillNavSessions' );
add_action('wp_ajax_mensiopress_getUsersCountry','mensiopress_getUsersCountry' );
add_action('wp_ajax_nopriv_mensiopress_getUsersCountry','mensiopress_getUsersCountry' );
function mensiopress_KillNavSessions(){
    if(!empty($_POST)){
        $url     = wp_get_referer();
        $ref     = url_to_postid( $url ); 
        $seller=new mensio_seller();
        $verification=$seller->VerifyPageIntegrity(filter_var($_POST['mns_sec']), "MensioPressFrontEnd-".$ref);
        if($verification==false){
            echo "0";
            die;
        }
    }
    unset($_SESSION['mnsCurrentBrand']);
    die;
}
if(!is_admin()){
    function MensioCallAjaxUrl() {
        $tt=rand(1,1000);
        wp_enqueue_script("MensioPressPublicJS".$tt,plugin_dir_url( dirname( __FILE__ ) )."js/empty.js");
        $CustomScript='var ajaxurl="'.admin_url('admin-ajax.php').'";';
        wp_add_inline_script( "MensioPressPublicJS".$tt,
               $CustomScript
               );
    }
    add_action( 'wp_head', 'MensioCallAjaxUrl' );
}
class mnsGetFrontEndLink {
    public function SignupPage(){
        global $wpdb;
        $prfx = $wpdb->prefix;
        $results = $wpdb->get_results( "select post_id, meta_key from $wpdb->postmeta,".$prfx."posts where meta_value = 'signup_page' and ".$prfx."posts.ID=".$prfx."postmeta.post_id order by `meta_id` DESC", ARRAY_A );
            $post=get_post($results[0]['post_id']);
            return $post->ID;
    }
    public function SignupPageLink(){
        if($this->SignupPage()){
            $slug_2="";
            $get=get_post($this->SignupPage());
            $slug=$get->post_name;
            if ( get_option('permalink_structure') ){
                $eshopSlug=get_option("MensioPagesSlug");
                if(!$eshopSlug){
                    $eshopSlug="action";
                }
                $lang="/";
                if($_SESSION['MensioThemeLangShortcode']!=$_SESSION['MensioDefaultLangShortcode']){
                    $lang="/".$_SESSION['MensioThemeLangShortcode']."/";
                }
                $Link=site_url().$lang.$eshopSlug."/".$slug."/";
            }
            else{
                $Link= site_url()."?mensio_page=".$slug."&language=".$_SESSION['MensioThemeLangShortcode'];
            }
            return $Link;
        }
        else{
            return "?page=eshop&category=".$id;
        }
    }
    public function LoginPage(){
        global $wpdb;
        $prfx = $wpdb->prefix;
        $results = $wpdb->get_results( "select post_id, meta_key from $wpdb->postmeta,".$prfx."posts where meta_value = 'login_page' and ".$prfx."posts.ID=".$prfx."postmeta.post_id order by `meta_id` DESC", ARRAY_A );
        $post=get_post($results[0]['post_id']);
        return $post->ID;
    }
    public function LoginLink(){
        global $wpdb;
        $prfx = $wpdb->prefix;
        $results = $wpdb->get_results( "select post_id, meta_key from $wpdb->postmeta,".$prfx."posts where meta_value = 'login_page' and ".$prfx."posts.ID=".$prfx."postmeta.post_id order by `meta_id` DESC", ARRAY_A );
        $post=get_post($results[0]['post_id']);
        $Link=site_url()."?page_id=".$this->LoginPage();
        return $Link;
    }
    public function BrandPage(){
        global $wpdb;
        $results = $wpdb->get_results( "select post_id, meta_key from $wpdb->postmeta where meta_value = 'brand_page'", ARRAY_A );
        foreach($results as $res){
            if(get_post_status($res['post_id'])=="publish"){
                return $res['post_id'];
            }
        }
        return false;
    }
    public function BrandLink($id){
        if($this->BrandPage()){
            $slug_2="";
            $get=new mensio_seller();
            $get=$get->MensioSearchForSlug($id);
            if(count($get)>0){
                $slug=$get[0]->slug;
            }
            if ( get_option('permalink_structure') == true ){
                $eshopSlug=get_option("MensioBrandSlug");
                if(!$eshopSlug){
                    $eshopSlug="mensio-brand";
                }
                $lang="/";
                if($_SESSION['MensioThemeLangShortcode']!=$_SESSION['MensioDefaultLangShortcode']){
                    $lang="/".$_SESSION['MensioThemeLangShortcode']."/";
                }
                $Link=site_url().$lang.$eshopSlug."/".$slug."/";
            }
            else{
                $Link="?page_id=".$this->BrandPage()."&language=".$_SESSION['MensioThemeLangShortcode']."&brand=".MensioEncodeUUID($id);
            }
            return $Link;
        }
        else{
            return "?page=eshop&brand=".$id;
        }
    }
    public function CategoryPage(){
        global $wpdb;
        $results = $wpdb->get_results( "select post_id, meta_key from $wpdb->postmeta where meta_value = 'category_page'", ARRAY_A );
        foreach($results as $res){
            if(get_post_status($res['post_id'])=="publish"){
                return $res['post_id'];
            }
        }
        return false;
    }
    public function CategoryLink($id){
        if($this->CategoryPage()){
            $slug_2="";
            $get=new mensio_seller();
            $get=$get->MensioSearchForSlug($id);
            if(count($get)>0){
                $slug=$get[0]->slug;
            }
            if ( get_option('permalink_structure') ){
                $eshopSlug=get_option("MensioCategorySlug");
                if(!$eshopSlug){
                    $eshopSlug="mensio-category";
                }
                $lang="/";
                if($_SESSION['MensioThemeLangShortcode']!=$_SESSION['MensioDefaultLangShortcode']){
                    $lang="/".$_SESSION['MensioThemeLangShortcode']."/";
                }
                $Link=site_url().$lang.$eshopSlug."/".$slug."/";
            }
            else{
                $Link="?page_id=".$this->CategoryPage()."&language=".$_SESSION['MensioThemeLangShortcode']."&category=".MensioEncodeUUID($id);
            }
            return $Link;
        }
        else{
            return "?page=eshop&category=".$id;
        }
    }
    public function ProductPage(){
        global $wpdb;
        $results = $wpdb->get_results( "select post_id, meta_key from $wpdb->postmeta where meta_value = 'product_page'", ARRAY_A );
        foreach($results as $res){
            if(get_post_status($res['post_id'])=="publish"){
                return $res['post_id'];
            }
        }
        return false;
    }
    public function ProductLink($id){
        if($this->ProductPage()){
            $slug="";
            $get=new mensio_seller();
            $get=$get->MensioSearchForSlug($id);
            if(count($get)>0){
                $slug=$get[0]->slug;
            }
            if ( get_option('permalink_structure') ){
                $eshopSlug=get_option("MensioProductSlug");
                if(!$eshopSlug){
                    $eshopSlug="mensio-product";
                }
                $lang="/";
                if($_SESSION['MensioThemeLangShortcode']!=$_SESSION['MensioDefaultLangShortcode']){
                    $lang="/".$_SESSION['MensioThemeLangShortcode']."/";
                }
                if($slug==true){
                    $slug=$slug."/";
                }
                $Link=site_url().$lang.$eshopSlug."/".$slug;
            }
            else{
                $Link="?page_id=".$this->ProductPage()."&language=".$_SESSION['MensioThemeLangShortcode']."&product=".MensioEncodeUUID($id);
            }
            return $Link;
        }
        else{
            return "?page=eshop&product=".$id;
        }
    }
    public function CheckoutPage(){
        global $wpdb;
        $link=false;
        $results = $wpdb->get_results( "select post_id, meta_key from $wpdb->postmeta where meta_value = 'checkout_page'", ARRAY_A );
        if($results){
            if (! get_option('permalink_structure') ){
                $link="?page_id=".$results[0]['post_id'];
                if($_SESSION['MensioThemeLangShortcode']!=$_SESSION['MensioDefaultLangShortcode']){
                    $link.="&language=".$_SESSION['MensioThemeLangShortcode'];
                }
            }
            else{
                $post= get_post($results[0]['post_id']);
                $langlink=false;
                if($_SESSION['MensioThemeLangShortcode']!=$_SESSION['MensioDefaultLangShortcode']){
                    $langlink="/".$_SESSION['MensioThemeLangShortcode'];
                }
                $link=get_site_url().$langlink."/".get_option("MensioPageSlug")."/".$post->post_name;
            }
        }
        return $link;
    }
    public function CartPage(){
        global $wpdb;
        $link=false;
        $results = $wpdb->get_results( "select post_id, meta_key from $wpdb->postmeta where meta_value = 'cart_page'", ARRAY_A );
        if($results){
            if (! get_option('permalink_structure') ){
                $link="?page_id=".$results[0]['post_id']."&language=".$_SESSION['MensioThemeLangShortcode'];
            }
            else{
                $post= get_post($results[0]['post_id']);
                $langLink=false;
                if($_SESSION['MensioThemeLangShortcode']!=$_SESSION['MensioDefaultLangShortcode']){
                    $langLink="/".$_SESSION['MensioThemeLangShortcode'];
                }
                $link=get_site_url().$langLink."/".get_option("MensioPageSlug")."/".$post->post_name;
            }
        }
        return $link;
    }
    public function CartPageID(){
        global $wpdb;
        $link=false;
        $results = $wpdb->get_results( "select post_id, meta_key from $wpdb->postmeta where meta_value = 'cart_page'", ARRAY_A );
        return $results[0]['post_id'];
    }
    public function TOSPage(){
        global $wpdb;
        $link=false;
        $results = $wpdb->get_results( "select post_id, meta_key from $wpdb->postmeta where meta_value = 'tos_page'", ARRAY_A );
        if($results){
            if (! get_option('permalink_structure') ){
                $link="?page_id=".$results[0]['post_id'];
            }
            else{
                $post= get_post($results[0]['post_id']);
                $link=get_site_url()."/".get_option("MensioPageSlug")."/".$post->post_name;
            }
        }
        return $link;
    }
    public function UserPage(){
        global $wpdb;
        $link=false;
        $prfx = $wpdb->prefix;
        $Query="SELECT
                    `".$prfx."posts`.`post_name`,
                    `".$prfx."posts`.`ID` as `post_id`
                FROM
                    `".$prfx."posts`,
                    `".$prfx."postmeta`
                WHERE
                    `".$prfx."posts`.`ID`=`".$prfx."postmeta`.`post_id` AND
                    `".$prfx."postmeta`.`meta_key`='mensio_page_function' AND
                    `".$prfx."postmeta`.`meta_value`='user_page' AND
                    `".$prfx."posts`.`post_status`='publish'";
        $results=$wpdb->get_results($Query,ARRAY_A );
        if($results){
            if (! get_option('permalink_structure') ){
                $link="?page_id=".$results[0]['post_id'];
            }
            else{
                $post= get_post($results[0]['post_id']);
                $link=get_site_url()."/".get_option("MensioPageSlug")."/".$post->post_name;
            }
        }
        return $link;
    }
    public function FavoritesPage(){
        global $wpdb;
        $link=false;
        $results = $wpdb->get_results( "select post_id, meta_key from $wpdb->postmeta where meta_value = 'product_favorites_page'", ARRAY_A );
        if($results){
            if (! get_option('permalink_structure') ){
                $link="?page_id=".$results[0]['post_id']."&language=".$_SESSION['MensioThemeLangShortcode'];
            }
            else{
                $langLink=false;
                $post= get_post($results[0]['post_id']);
                if($_SESSION['MensioThemeLangShortcode']!=$_SESSION['MensioDefaultLangShortcode']){
                    $langLink="/".$_SESSION['MensioThemeLangShortcode'];
                }
                $link=get_site_url().$langLink."/".get_option("MensioPageSlug")."/".$post->post_name;
            }
        }
        return $link;
    }
    public function SearchPage($What=false){
        global $wpdb;
        $link=false;
        $results = $wpdb->get_results( "select post_id, meta_key from $wpdb->postmeta where meta_value = 'search_results_page'", ARRAY_A );
        if($results){
            if (! get_option('permalink_structure') ){
                $link="?page_id=".$results[0]['post_id'];
            }
            else{
                $post= get_post($results[0]['post_id']);
                $link=get_site_url()."/".get_option("MensioPageSlug")."/".$post->post_name;
            }
            if($What=='theID'){
                $link= $results[0]['post_id'];
            }
        }
        return $link;
    }
}
class mensio_product_filtering {
  final public function ProductSelectionFiltering($PrdName='',$PrdCategories=array(),$PriceType="",$MinPrice='',$MaxPrice='',$PrdValues=array(),$ListOrder=array(),$atts=array()) {
    $DataSet = array('Data'=>false, 'MinPrice'=>'', 'MaxPrice'=>'');
    $NameIDs = '';
    if ($PrdName !== '') { $NameIDs = $this->GetIDsFromName($PrdName); }
    global $wpdb;
    $prfx = $wpdb->prefix;
    $Query = 'SELECT '.$prfx.'mns_products.* FROM '.$prfx.'mns_products WHERE '.$prfx.'mns_products.visibility = TRUE ';
    if ($NameIDs !== '') {
      $Query .= ' AND '.$prfx.'mns_products.uuid IN ('.$NameIDs.')';
    }
    if (($MinPrice !== '') && ($MaxPrice !== '')) {
      if ($PriceType === 'btb') {
        $Query .= ' AND '.$prfx.'mns_products.btbprice BETWEEN "'.$MinPrice.'" AND "'.$MaxPrice.'"';
      } else {
        $Query .= ' AND '.$prfx.'mns_products.price BETWEEN "'.$MinPrice.'" AND "'.$MaxPrice.'"';
      }
    }
    $FltrValue = '';
    if ((is_array($PrdValues)) && (!empty($PrdValues[0]))) {
      foreach ($PrdValues as $Value) {
        $Value = '"'.str_replace('::', '","', $Value).'"';
        if ($FltrValue === '') {
          $FltrValue .= '(SELECT product FROM '.$prfx.'mns_products_attributes WHERE attribute_value IN ('.$Value.')';
        } else {
          $FltrValue .= ' AND product IN (SELECT product FROM '.$prfx.'mns_products_attributes WHERE attribute_value IN ('.$Value.'))';
        }
      }
      $FltrValue .= ' )';
    } //
    $FltrCtgr = '';
    if ((is_array($PrdCategories)) && (!empty($PrdCategories[0]))) {
      foreach ($PrdCategories as $Category) {
        if ($FltrCtgr === '') {
          $FltrCtgr .= '(
            SELECT product FROM '.$prfx.'mns_products_categories
            WHERE category = "'.$Category.'"';
        } else {
          $FltrCtgr .= ' OR category = "'.$Category.'"';
        }
      }
      if ($FltrValue !== '') { $FltrCtgr .= ' AND product IN '.$FltrValue; }
      $FltrCtgr .= ')';
    }
    if ($FltrCtgr === '') {
      if ($FltrValue !== '') { $Query .= ' AND '.$prfx.'mns_products.uuid IN '.$FltrValue; }
    } else {
      $Query .= ' AND '.$prfx.'mns_products.uuid IN '.$FltrCtgr;
    }
    if ((is_array($ListOrder)) && (!empty($ListOrder[0]))) {
      $OrderBy = '';
      foreach ($ListOrder as $Column) {
        if ($OrderBy === '') { $OrderBy .= $Column; }
          else { $OrderBy .= ', '.$Column; }
      }
      $Query .= ' ORDER BY '.$OrderBy;
    }
    $Records = $wpdb->get_results($Query);
    if ((is_array($Records)) && (!empty($Records[0]))) {
      $MinPrice = array();
      $MaxPrice = array();
      $i = 0;
      foreach ($Records as $Row) {
        $DataSet['Data'][$i]['uuid'] = $Row->uuid;
        $DataSet['Data'][$i]['guuid'] = $Row->guuid;
        $DataSet['Data'][$i]['code'] = $Row->code;
        $DataSet['Data'][$i]['brand'] = $Row->brand;
        $DataSet['Data'][$i]['btbprice'] = $Row->btbprice;
        $DataSet['Data'][$i]['btbtax'] = $Row->btbtax;
        $DataSet['Data'][$i]['price'] = $Row->price;
        $DataSet['Data'][$i]['tax'] = $Row->tax;
        $DataSet['Data'][$i]['discount'] = $Row->discount;
        $DataSet['Data'][$i]['created'] = $Row->created;
        $DataSet['Data'][$i]['available'] = $Row->available;
        $DataSet['Data'][$i]['changed'] = $Row->changed;
        $DataSet['Data'][$i]['stock'] = $Row->stock;
        $DataSet['Data'][$i]['minstock'] = $Row->minstock;
        $DataSet['Data'][$i]['image'] = $this->GetProductMainImage($Row->uuid);
        $InfoData = $this->GetProductName($Row->uuid);
        $DataSet['Data'][$i]['name'] = $InfoData['name'];
        $DataSet['Data'][$i]['description'] = $InfoData['description'];
        $DataSet['Data'][$i]['reviews'] = $this->GetProductReviewsValueSum($Row->uuid);
        $StatusData = $this->GetProductStatusDescription($Row->uuid,$Row->status,$Row->stock);
        $DataSet['Data'][$i]['status_name'] = $StatusData['name'];
        $DataSet['Data'][$i]['status_color'] = $StatusData['color'];
        $DataSet['Data'][$i]['status_icon'] = $StatusData['icon'];
        if(!empty($atts) && $atts['show-price-with-tax']=='yes'){
            $final_price=$Row->price-($Row->price*($Row->discount/100));
            $final_price=$final_price+($final_price*($Row->tax/100));
            $final_price= number_format($final_price,2);
            $Prices[$i] = $final_price;
        }
        else{
            $Prices[$i] = $Row->price;
        }
        $i++;
      }
      sort($Prices);
      $DataSet['MinPrice'] = floor($Prices[0]);
      $DataSet['MaxPrice'] = ceil($Prices[(count($Prices)-1)]);
    }
    return $DataSet;
  }
  private function GetIDsFromName($PrdName) {
    $PrdIDs = '';
    global $wpdb;
    $prfx = $wpdb->prefix;
    $prfx = $wpdb->prefix;
    $Query = '
    SELECT * FROM '.$prfx.'mns_products WHERE uuid IN (
      SELECT product FROM '.$prfx.'mns_products_descriptions
      WHERE name LIKE "%'.$PrdName.'%"
      AND language = "53ac0d27-ac0b-11e6-b57b-4ccc6a4aa826")
    OR uuid IN (
      SELECT variation FROM '.$prfx.'mns_products_variations
      WHERE product IN (
        SELECT product FROM '.$prfx.'mns_products_descriptions
        WHERE name LIKE "%'.$PrdName.'%"
        AND language = "'.$_SESSION['MensioThemeLang'].'"
      )
    )'; // ΑΛΛΑΓΗ ΣΤΟΝ ΚΩΔΙΚΟ ΤΗΣ ΓΛΩΣΣΑΣ
    $Records = $wpdb->get_results($Query);
    if ((is_array($Records)) && (!empty($Records[0]))) {
      foreach ($Records as $Row) {
        if ($PrdIDs === '') { $PrdIDs .= '"'.$Row->uuid.'"'; }
          else { $PrdIDs .= ',"'.$Row->uuid.'"'; }
      }
    }
    return $PrdIDs;
  }
  private function GetProductName($PrdID) {
    $DataSet = array('name'=>'', 'description'=>'');
    global $wpdb;
    $prfx = $wpdb->prefix;
    $Query = 'SELECT * FROM '.$prfx.'mns_products_descriptions
      WHERE product = "'.$PrdID.'"
      AND language = "'.$_SESSION['MensioThemeLang'].'"'; // ΑΛΛΑΓΗ ΣΤΟΝ ΚΩΔΙΚΟ ΤΗΣ ΓΛΩΣΣΑΣ
    $Records = $wpdb->get_results($Query);
    if ((is_array($Records)) && (!empty($Records[0]))) {
      foreach ($Records as $Row) {
        $DataSet['name'] = $Row->name;
        $DataSet['description'] = $Row->description;
      }
    }
    if (MENSIO_FLAVOR !== 'FREE') {
      if ($Name === '') {
        $Query = 'SELECT * FROM '.$prfx.'mns_products_descriptions
          WHERE language = "53ac0d27-ac0b-11e6-b57b-4ccc6a4aa826"
          AND product IN (SELECT product FROM '.$prfx.'mns_products_variations
            WHERE variation = "'.$PrdID.'")';
        $Records = $wpdb->get_results($Query);
        if ((is_array($Records)) && (!empty($Records[0]))) {
          foreach ($Records as $Row) {
            $DataSet['name'] = $Row->name;
            $DataSet['description'] = $Row->description;
          }
        }
      }
    }
    return $DataSet;
  }
  private function GetProductStatusDescription($ProductID,$StatusID,$stock) {
    $DataSet = array();
    global $wpdb;
    $prfx = $wpdb->prefix;
    $Order = '';
    if ($StatusID === 'StockRelated') {
      $StatusTable = $prfx.'mns_products_stock_status';
      $Where = 'WHERE '.$StatusTable.'.product = "'.$ProductID.'" '
        . 'AND '.$StatusTable.'.stock <= "'.$stock.'" ';
      $Order = 'ORDER BY '.$StatusTable.'.stock DESC LIMIT 1';
      $Descr = 'stock_status';
    } else {
      $StatusTable = $prfx.'mns_products_status';
      $Where = 'WHERE '.$StatusTable.'.uuid = "'.$StatusID.'" ';
      $Descr = 'status';
    }
    $Query = 'SELECT '.$StatusTable.'.*, '.$StatusTable.'_descriptions.name '
      .'FROM '.$StatusTable.', '.$StatusTable.'_descriptions '
      .$Where.' AND '.$StatusTable.'.uuid = '.$StatusTable.'_descriptions.'.$Descr
      .' AND '.$StatusTable.'_descriptions.language = "'.$_SESSION['MensioThemeLang'].'" '.$Order;
    $Records = $wpdb->get_results($Query);
    if ((is_array($Records)) && (!empty($Records[0]))) {
      foreach ($Records as $Row) {
        $DataSet['name'] = $Row->name;
        $DataSet['icon'] = $Row->icon;
        $DataSet['color'] = $Row->color;
      }
    }
    return $DataSet;
  }
  private function GetProductMainImage($PrdID) {
    $Image = '';
    global $wpdb;
    $prfx = $wpdb->prefix;
    $Query = 'SELECT * FROM '.$prfx.'mns_products_images WHERE product = "'.$PrdID.'" AND main = TRUE';
    $Records = $wpdb->get_results($Query);
    if ((is_array($Records)) && (!empty($Records[0]))) {
      foreach ($Records as $Row) {
        $Image = $Row->file;
      }
    }
    return $Image;
  }
  private function GetProductReviewsValueSum($PrdID) {
    $Sum = '';
    global $wpdb;
    $prfx = $wpdb->prefix;
    $Query = 'SELECT SUM(rvalue) AS ValueSum FROM '.$prfx.'mns_reviews WHERE product = "'.$PrdID.'"';
    $Records = $wpdb->get_results($Query);
    if ((is_array($Records)) && (!empty($Records[0]))) {
      foreach ($Records as $Row) {
        $Sum = $Row->ValueSum;
      }
    }
    return $Sum;
  }
  final public function GetVariationInfo($ProdID) {
    $DataSet = array();
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT * FROM '.$prfx.'products_descriptions
      WHERE product = "'.$ProdID.'" AND language = "'.$_SESSION['MensioThemeLang'].'"';
    $DataSet = $wpdb->get_results($Query);
    if ((!is_array($DataSet)) || (empty($DataSet[0]))) {
      $Query = 'SELECT * FROM '.$prfx.'products_descriptions
        WHERE language = "'.$_SESSION['MensioThemeLang'].'" AND product IN (
          SELECT product FROM '.$prfx.'products_variations WHERE variation = "'.$ProdID.'"
        )';
      $DataSet = $wpdb->get_results($Query);
    }
    return $DataSet;
  }
}
class mnsFrontEndObject {
    public function mnsFrontEndBrands($ordering){
        if(empty($ordering)){
            $ordering="A-Z";
        }
        global $wpdb;
        $prfx = $wpdb->prefix;
            switch ($ordering) {
                case "A-Z":
                    $brands=new mensio_products_brands();
                    $brands->Set_Sorter("name ASC");
                    $brands=$brands->LoadProductBrandsDataSet();
                    break;
                case "Z-A":
                    $brands=new mensio_products_brands();
                    $brands->Set_Sorter("name DESC");
                    $brands=$brands->LoadProductBrandsDataSet();
                    break;
                case "Random":
                    $brands=new mensio_products_brands();
                    $brands->Set_Sorter("name");
                    $brands=$brands->LoadProductBrandsDataSet();
                    shuffle($brands);
                    break;
                case "MostProducts":
                    $brands=new mensio_seller();
                    $brands=$brands->LoadBrandsAndProducts("DESC");
                    break;
                case "FewerProducts":
                    $brands=new mensio_seller();
                    $brands=$brands->LoadBrandsAndProducts("ASC");
                    break;
            }
            $arr=array();
            $i=0;
            foreach($brands as $brand){
                if(!$brand->visible){
                    continue;
                }
                $Brand=explode("-",$brand->uuid);
                $arr[$i]['id']=$Brand[2].$Brand[1].$Brand[0];
                $arr[$i]['uuid']=$brand->uuid;
                $arr[$i]['name']=$brand->name;
                $arr[$i]['image']=$this->MensioGetThumb(array(300,300),$brand->logo);
                    $link= new mnsGetFrontEndLink();
                    $link=$link->BrandLink($brand->uuid);
                $arr[$i]['link']=$link;
                $arr[$i]['BorderColor']=$brand->color;
                $i++;
            }
            return $arr;
        if(empty($_GET['action'])){
        }
        else{
            $i=0;
            $dir= plugin_dir_path(__FILE__)."../icons/offline-objects/brands/";
            $files = scandir($dir);
            foreach($files as $file){
                if($file=='.' || $file=='..'){continue;}
                $arr[$i]['id']=$i;
                $arr[$i]['name']="Brand ".$i;
                $arr[$i]['image']= plugin_dir_url(__FILE__)."../icons/offline-objects/brands/".$file;
                $arr[$i]['link']="";
                $i++;
            }
        }
        return $arr;
    }
    public function mnsFrontEndTopBrands(){
        $brands=new mensio_products_brands();
        $brands=$brands->LoadProductBrandsDataSet();
        $arr=array();
        $i=0;
        foreach($brands as $brand){
            $arr[$i]['name']=$brand->name;
            $arr[$i]['image']=$this->MensioGetThumb(array(300,300),$brand->logo);
                $link= new mnsGetFrontEndLink();
                $link=$link->BrandLink($brand->uuid);
            $arr[$i]['link']=$link;
            $i++;
        }
        return $arr;
    }
    public function mnsFrontEndCategories($ordering){
        $cats=new mensio_products_categories();
        $cats=$cats->LoadProductCategoriesDataSet();
            switch ($ordering) {
                case "A-Z":
                case "":
                    $cats=new mensio_products_categories();
                    $cats->Set_Sorter("name ASC");
                    $cats=$cats->LoadProductCategoriesDataSet();
                    break;
                case "Z-A":
                    $cats=new mensio_products_categories();
                    $cats->Set_Sorter("name DESC");
                    $cats=$cats->LoadProductCategoriesDataSet();
                    break;
                case "Random":
                    $cats=new mensio_products_categories();
                    $cats->Set_Sorter("name ASC");
                    $cats=$cats->LoadProductCategoriesDataSet();
                    shuffle($cats);
                    break;
                case "MostProducts":
                    $cats=new mensio_seller();
                    $cats=$cats->LoadCategoriesAndProducts(",uuid DESC");
                    break;
                case "FewerProducts":
                    $cats=new mensio_seller();
                    $cats=$cats->LoadCategoriesAndProducts(",uuid ASC");
                    break;
            }
            $i=0;
            $categories=Array();
            foreach($this->mnsFrontEndBrands($ordering) as $brand){
                $brandID= $brand['uuid'];
                if(!empty($cats['categories'])){
                    foreach($cats['categories'] as $key=>$val){
                        $i=$key;
                        $translate=new mensio_products_categories();
                        $translate->Set_Language($_SESSION['MensioThemeLang']);
                        $translate->Set_UUID($key);
                        $translate=$translate->GetCategoryTranslation();
                        if($translate){
                            $categories[$i]['name']=$translate;
                        }
                        else{
                            $categories[$i]['name']=$val['name'];
                        }
                        $categories[$i]['id']=$key;
                        $categories[$i]['image']=$val['image'];
                            $link= new mnsGetFrontEndLink();
                            $link=$link->CategoryLink($key);
                        $categories[$i]['link']=$link;
                        $i++;
                    }
                }
            }
            return $categories;
    }
    public function mnsFrontEndTopCategories(){
        $cats=new mensio_products_categories();
        $cats=$cats->LoadProductCategoriesDataSet();
        $arr=array();
        $i=0;
        foreach($cats as $cat){
            $arr[$i]['name']=$cat->name;
            $arr[$i]['image']=$this->MensioGetThumb(array(300,300),$cat->image);
                $link= new mnsGetFrontEndLink();
                $link=$link->CategoryLink($cat->uuid);
            $arr[$i]['link']=$link;
            $i++;
        }
        return $arr;
    }
    public function mnsFrontEndBrandProducts($brand_id){
        $Products = new mensio_products();
        if(!$Products->Set_Brand($brand_id)){
            $arr=array();
            return $arr;
        }
        else{
            $arr=array();
            $i=0;
            global $wpdb;
            $prfx = $wpdb->prefix;
            $Data=new mensio_seller();
            $Data=$Data->LoadTopLevelCategories($brand_id);
            if ((is_array($Data)) && (!empty($Data[0]))) {
              foreach($Data as $cats){
                    $translate=new mensio_products_categories();
                    $translate->Set_Language($_SESSION['MensioThemeLang']);
                    $translate->Set_UUID($cats['uuid']);
                    $translate=$translate->GetCategoryTranslation();
                    if($translate){
                        $arr[$i]['name']=$translate;
                        $arr['categories'][$cats['uuid']]['name']=$translate;
                    }
                    else{
                        $arr['categories'][$cats['uuid']]['name']=$cats['name'];
                    }
                  $arr['categories'][$cats['uuid']]['image']=$this->MensioGetThumb(array(300,300),$cats['image']);
                      $link= new mnsGetFrontEndLink();
                      $link=$link->CategoryLink($cats['uuid']);
                  $arr['categories'][$cats['uuid']]['link']=$link;
              }
            }
            $brand_data=new mensio_products_brands();
            $brand_data->Set_UUID($brand_id);
            $Data=$brand_data->GetBrandData();
            $arr['current_brand_webpage']=$Data[0]->webpage;
            $arr['current_brand_name']=$Data[0]->name;
            $arr['BorderColor']=$Data[0]->color;
            $arr['current_brand_image']=site_url()."/".$Data[0]->logo;
            $arr['current_brand_imageThumb']=$this->MensioGetThumb(array(300,300),$Data[0]->logo);
            $getBrandDescr=$brand_data->GetBrandTranslations($_SESSION['MensioThemeLang']);
            if(count($getBrandDescr)=='0'){
                $getBrandDescr=$brand_data->GetBrandTranslations();
            }
            $arr['current_brand_description']="";
            foreach($getBrandDescr as $dt){
                $arr['current_brand_description']=$dt->notes;
            }
            $Data = $Products->LoadBrandsProductsList();
            $i=0;
            $arr['products'][$i]['name']="";
            $arr['products'][$i]['image']="";
            $arr['products'][$i]['description']="";
            $arr['products'][$i]['link']="";
            foreach($Data as $prod){
                $arr['products'][$i]['name']=$prod->name;
                $arr['products'][$i]['image']=$this->MensioGetThumb(array(300,300),$prod->file);
                $arr['products'][$i]['description']=$prod->description;
                    $link= new mnsGetFrontEndLink();
                    $link=$link->ProductLink($prod->uuid);
                $arr['products'][$i]['link']=$link;
                $i++;
            }
            return $arr;
        }
    }
    public function mnsFrontProductOffers(){
        if(constant("MENSIO_FLAVOR")=="STD"){
            $arr=new MensioFlavored();
            $arr=$arr->mnsFrontProductOffers();
            return $arr;
        }
        $prodData=new mnsFrontEndObject();
        $Products = new mensio_seller();
        $arr=array();
        $Data = $Products->LoadProductOffers(true);
        $i=0;
        krsort($Data);
        foreach($Data as $prod){
            if($prod->visibility==0){
                continue;
            }
            $arr[$i]['id']=$prod->uuid;
            $Prod=$prodData->mnsFrontEndProduct($prod->uuid);
            $arr[$i]['visibility']=false;
            if(!empty($Prod['visibility'])){
                $arr[$i]['visibility']=$Prod['visibility'];
            }
            $arr[$i]['name']=$Prod['name'];
            $arr[$i]['description']=$Prod['description'];
            $arr[$i]['brand']=$prod->brand;
            $Brand=$prodData->mnsFrontEndBrandProducts($prod->brand);
            $arr[$i]['BorderColor']=$Brand['BorderColor'];
            $arr[$i]['sku']=$prod->code;
            $arr[$i]['reviews']=$prodData->GetAverageRating($prod->uuid);
            $getCat=new mensio_products();
            $getCat->Set_UUID($prod->uuid);
            $Cats=$getCat->LoadProductCategories();
            foreach($Cats as $cat){
                $arr[$i]['categories'][]=$cat->category;
            }
            $arr[$i]['image']=$prodData->MensioGetThumb(array(300,300),$prod->file);
            $arr[$i]['created']=$prod->created;
            $arr[$i]['discount']=$prod->discount;
            $arr[$i]['averageRating']=$prodData->GetAverageRating($prod->uuid);
            $arr[$i]['tax']= $prod->tax;
            $arr[$i]['btbtax']= $prod->btbtax;
            $arr[$i]['price']= $prod->price;
            $arr[$i]['btbprice']= $prod->btbprice;
            $arr[$i]['FinalPrice']=number_format( $arr[$i]['price'] - ($arr[$i]['price']*($prod->discount/100)) ,2);
                $link= new mnsGetFrontEndLink();
                $link=$link->ProductLink($prod->uuid);
            $arr[$i]['link']=$link;
            $getBarcodes=new mensio_products();
            $getBarcodes->Set_UUID($prod->uuid);
            $arr[$i]['barcodes']=array();
            foreach($getBarcodes->LoadProductBarcodeList() as $barcode){
                $arr[$i]['barcodes'][]=$barcode->barcode;
            }
            $i++;
        }
        return $arr;
    }
    public function GetAverageRating($prodID){
        $allReviews=new mensio_seller();
        $allReviews->Set_ProductID($prodID);
        $allReviews=$allReviews->AllProductReviews();
        $Average=0;
        if($allReviews){
            $i=0;
            $AllRatings=0;
            foreach($allReviews as $review){
                $AllRatings=$AllRatings+$review['value'];
                $i++;
            }
            $Average=$AllRatings/$i;
            $Average=$Average/5;
        }
        return $Average;
    }
    public function mnsFrontEndCategoryProducts($cat_id,$atts=false,$limit=false){
        $hide_this_prod='';
        if(!empty($_GET['action']) && empty($cat_id)){
            $cats=$this->mnsFrontEndCategories("");
            foreach($cats as $cat){
                $cat_id=$cat['id'];
            }
        }
        if((!empty($atts['filters']) && ($atts['filters']==true))){
            $filters=$atts['filters'];
        }
        else{
            $filters=array();
        }
        if((!empty($atts['max_price']) && ($atts['max_price']==true))){
            $max_price=$atts['max_price'];
        }
        else{
            $max_price=array();
        }
        $max_price=0;
        $Products = new mensio_seller();
        if(!$Products->Set_CategoryID($cat_id)){
            return false;
        }
        else{
            $arr=array();
            $cat_data=new mensio_products_categories();
            $cat_data->Set_UUID($cat_id);
            $Data=$cat_data->GetCategoryData();
            $arr['current_cat_name']=$Data['name'];
            $Products->Set_CategoryID($cat_id);
            $catName=$Products->TranslateCategory();
            if(!empty($catName)){
                $arr['current_cat_name']=$catName[0]->name;
            }
            $arr['current_cat_image']=site_url()."/".$Data['image'];
            $ordering=false;
            if(!empty($atts['ordering'])){
                $ordering=$atts['ordering'];
            }
            $Data = $Products->LoadCategoryProductsList($ordering,$limit);
            $i=0;
            foreach($Data as $prod){
                    $product=new mensio_products();
                    $product->Set_UUID($prod->uuid);
                    $atts=$product->LoadProductAttributeValues();
                    $arr['products'][$i]['atts']=$atts;
                        foreach($atts as $att){
                            if(!isset($arr['filters'][$att->name])){
                                $arr['filters'][$att->name]=array();
                            }
                            if(!in_array($att->value,$arr['filters'][$att->name])){
                                $arr['filters'][$att->name][$att->attribute_value]=$att->value;
                            }
                            else{
                            }
                        }
                        $i=$prod->uuid;
                        $arr['products'][$i]['brand']=$prod->brand;
                        $arr['products'][$i]['price']=$prod->price;
                        $arr['products'][$i]['tax']=$prod->tax;
                        $arr['products'][$i]['discount']=$prod->discount;
                        $arr['products'][$i]['final_price']=($prod->price+($prod->price * $prod->tax/100));
                        if($prod->discount>0){
                            $arr['products'][$i]['final_price']=($prod->price-($prod->price*($prod->discount/100)));
                            $arr['products'][$i]['final_price']=number_format($arr['products'][$i]['final_price']+($arr['products'][$i]['final_price'] * $prod->tax/100 ),2);
                        }
                        $arr['products'][$i]['name']=$prod->name;
                        $arr['products'][$i]['image']=$this->MensioGetThumb(array(300,300),$prod->file);
                        $arr['products'][$i]['description']=$prod->description;
                        $arr['products'][$i]['id']=$prod->uuid;
                            $link= new mnsGetFrontEndLink();
                            $link=$link->ProductLink($prod->uuid);
                        $arr['products'][$i]['link']=$link;
                $i++;
            }
            $seller=new mensio_seller();
            $children=$seller->LoadChildCategories($cat_id);
            $i=0;
            foreach($children as $cat){
                $arr['childcategories'][$i]['id']=$cat->category;
                $seller->Set_CategoryID($cat->category);
                $name=$seller->TranslateCategory();
                if(!empty($name)){
                    $arr['childcategories'][$i]['name']=$name[0]->name;
                }
                else{
                    $arr['childcategories'][$i]['name']=$cat->name;
                }
                $arr['childcategories'][$i]['image']= site_url()."/".$cat->image;
                $arr['childcategories'][$i]['image']=$this->MensioGetThumb(array(300,300),$cat->image);
                    $link= new mnsGetFrontEndLink();
                    $link=$link->CategoryLink($cat->uuid);
                $arr['childcategories'][$i]['link']=$link;
                $i++;
            }
            return $arr;
        }
    }
    public function mnsFrontEndProduct($prod_id){
        $Products = new mensio_seller();
        if (!$Products->Set_UUID($prod_id)) {
          $NoteType = 'Alert';
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] = 'Product value not acceptable';
          return false;
        } else {
        $arr=array();
        if(constant("MENSIO_FLAVOR")=="STD"){
            $arr=new MensioFlavored();
            $arr=$arr->mnsFrontEndProduct($prod_id);
            return $arr;
        }
            $object=new mnsFrontEndObject();
            $DataSet = $Products->LoadProductRecordData();
            foreach($DataSet as $prod){
                $getLink= new mnsGetFrontEndLink();
                $link=$getLink->ProductLink($prod->uuid);
                $arr['link']=$link;
                $arr['id']=$prod->uuid;
                $arr['gid']=$prod->guuid;
                $arr['price']= number_format($prod->price,2);
                $arr['tax']= $prod->tax;
                $arr['btbprice']= number_format($prod->btbprice,2);
                $arr['btbtax']= $prod->btbtax;
                $arr['discount']= $prod->discount;
                $arr['Weight']= $Products->LoadProductWeight($prod_id);
                $seller=new mensio_seller();
                $Status=$seller->Status($prod->status);
                $arr['availability']= false;
                $arr['availability-color']= false;
                $arr['availability-icon']= false;
                if(!empty($Status->name)){
                    $arr['availability']= $Status->name;
                    $arr['availability-color']= $Status->color;
                    $arr['availability-icon']= site_url()."/".$Status->icon;
                }
                $arr['BorderColor']="";
                $arr['reviews']=$object->GetAverageRating($prod->uuid);;
                $arr['FinalPrice']="-";
                $Brand=$object->mnsFrontEndBrandProducts($prod->brand);
                $arr['BorderColor']=$Brand['BorderColor'];
                $arr['stock']= $prod->stock;
                $arr['sku']= $prod->code;
                $arr['brand']= $prod->brand;
                $arr['status']= $prod->status;
                $price_tax= (($prod->price* ($prod->tax/100))+$prod->price);
                $final_price=$price_tax;
                $prod->discount=$prod->discount;
                $arr['final_price']=($prod->price+($prod->price * $prod->tax/100));
                if($prod->discount>0){
                    $arr['final_price']=($prod->price-($prod->price*($prod->discount/100)));
                    $arr['final_price']=number_format($arr['final_price']+($arr['final_price'] * $prod->tax/100 ),2);
                }
                $i=0;
                $product=new mensio_products();
                $product=new mensio_seller();
                $product->Set_UUID($prod_id);
                $atts=$product->LoadProductAttributeValues();
                if(!empty($atts)){
                    foreach($atts as $att){
                        $arr['filterIDS'][]=$att->attribute_value;
                        $ProdCategories=new mensio_products_categories();
                        $ProdCategories->Set_Language($_SESSION['MensioThemeLang']);
                        $ProdCategories->Set_Attribute($att->attribute_uuid);
                        $AttName=$ProdCategories->GetAttributeTranslation();
                        if(!$AttName){
                            $AttName=$att->name;
                        }
                        $arr['filterIDS'][]=$att->attribute_value;
                        $arr['filters'][$i]['FrontName']=$AttName;
                        $arr['filters'][$i]['name']=$att->name;
                        $arr['filters'][$i]['value']=$att->value;
                        $i++;
                    }
                }
                $arr['name']=false;
                $arr['description']=false;
                $arr['longDescription']=false;
                if(!empty($prod->name)){
                    $arr['name']=$prod->name;
                    $arr['description']=$prod->description;
                    $arr['longDescription']=$prod->notes;
                }
                else{
                }
            }
            $arr['tags']=$Products->LoadProductTags();
            $arr['images']=array();
            $i=0;
            if(!empty($VarProd_id)){
                $Products->Set_UUID($VarProd_id);
                $images=$Products->LoadProductRecordImages();
                if(count($images)>0){
                    foreach($images as $img){
                        if(!empty($img->main) && $img->main > 0){
                            $arr['images'][$i]['image']=get_site_url()."/".$img->file;
                            $arr['images'][$i]['thumb']=get_site_url()."/".$object->MensioGetThumb(array(300,300),$img->file);
                            $i++;
                        }
                    }
                }
                $Products->Set_UUID($MasterProduct);
            }
            $images=$Products->LoadProductRecordImages();
            $o=1;
            foreach($images as $img){
                if($object->MensioGetThumb(array(300,300),$img->file)){
                    $arr['images'][$i]['image']= get_site_url()."/".$img->file;
                    $arr['images'][$i]['thumb']=$object->MensioGetThumb(array(300,300),$img->file);
                    $i++;
                }
                if($img->main == '1' && !empty($img->file)){
                    $arr['image']=site_url()."/".$img->file;
                    $arr['image-thumb']=$object->MensioGetThumb(array(300,300),$img->file);
                }
            }
            if(count($images)==0){
                $arr['image']= plugin_dir_url(__FILE__)."/../../icons/mensiopress-noimage.png";
            }
            return $arr;
        }
    }
    public function mnsFrontEndRandomProduct(){
        $prods=$this->mnsFrontEndNewProducts(10);
        return $prods[0]['id'];
    }
    public function mnsFrontEndNewProducts($Limit=false){
        $arr=array();
        $GetLink= new mnsGetFrontEndLink();
        $prodData=new mnsFrontEndObject();
        $Products=new mensio_products();
        $Products=$Products->LoadLatestProductsList($Limit, "", $_SESSION['MensioThemeLang']);
        $i=0;
        foreach($Products as $key=>$prod){
            $arr[$i]['id']=$prod['uuid'];
            $arr[$i]['gid']=$prod['guuid'];
            $arr[$i]['name']=$prod['name'];
            $arr[$i]['sku']=$prod['code'];
            $arr[$i]['description']=$prod['description'];
            $arr[$i]['brand']=$prod['brand'];
            $arr[$i]['BorderColor']=$prod['border_color'];
            $arr[$i]['discount']=$prod['discount'];
            $arr[$i]['price']=$prod['price'];
            $arr[$i]['tax']=$prod['tax'];
            $arr[$i]['btbprice']=$prod['btbprice'];
            $arr[$i]['btbtax']=$prod['btbtax'];
            $arr[$i]['reviews']=$prodData->GetAverageRating($prod['uuid']);
            $arr[$i]['link']=$GetLink->ProductLink($prod['uuid']);
            $arr[$i]['image']=$prodData->MensioGetThumb(array(300,300),$prod['image']);
            $arr[$i]['FinalPrice']=false;
            if(MENSIO_FLAVOR=='STD'){
                $barcodes=explode("::",$prod['barcodes']);
                $arr[$i]['barcodes']=array();
                foreach($barcodes as $barcode){
                    $arr[$i]['barcodes'][]=$barcode;
                }
            }
            $i++;
        }
        return $arr;
        global $wpdb;
        $prfx = $wpdb->prefix.'mns_';
        $Products = new mensio_products();
        $arr=array();
        $Products->Set_Sorter(" `".$prfx."products`.`created` LIMIT 0,".$Limit);
        $Data = $Products->LoadProductsDataSet(true);
        $i=0;
        $k=1;
        krsort($Data);
        foreach($Data as $prod){
            if($prod->visibility==0){
                continue;
            }
            $Prod=$prodData->mnsFrontEndProduct($prod->uuid);
            if($k==$Limit){
                break;
            }
            $arr[$i]['id']=$prod->uuid;
            $arr[$i]['name']=$Prod['name'];
            $arr[$i]['description']=$Prod['description'];
            $arr[$i]['brand']=$prod->brand;
            $Brand=$prodData->mnsFrontEndBrandProducts($prod->brand);
            $arr[$i]['BorderColor']=$Brand['BorderColor'];
            $arr[$i]['sku']=$prod->code;
            $arr[$i]['created']=$prod->created;
            $arr[$i]['discount']=$prod->discount;
            $arr[$i]['averageRating']=$prodData->GetAverageRating($prod->uuid);
            $arr[$i]['tax']= $prod->tax;
            $arr[$i]['btbtax']= $prod->btbtax;
            $arr[$i]['price']= $prod->price;
            $arr[$i]['btbprice']= $prod->btbprice;
            $arr[$i]['FinalPrice']=number_format( $arr[$i]['price'] - ($arr[$i]['price']*($prod->discount/100)) ,2);
            $getBarcodes=new mensio_products();
            $getBarcodes->Set_UUID($prod->uuid);
            $arr[$i]['barcodes']=array();
            foreach($getBarcodes->LoadProductBarcodeList() as $barcode){
                $arr[$i]['barcodes'][]=$barcode->barcode;
            }
            $i++;
            $k++;
        }
        return $arr;
    }
    public function mnsGetProductCategories($prodID){
        $i=0;
        $arr=array();
        $getCat=new mensio_products();
        $getCat->Set_UUID($prodID);
        $Cats=$getCat->LoadProductCategories();
        foreach($Cats as $cat){
            $arr[]=$cat->category;
            $i++;
        }
        return $arr;
    }
    public function mnsFrontEndStoreData(){
        $store=new mensio_store();
        $store=$store->LoadStoreData();
        $Data=array();
        foreach($store as $dt){
            $Data['id']=$dt->uuid;
            $Data['name']=$dt->name;
            $Data['city']=$dt->city;
            $Data['address']=$dt->street;
            $Data['number']=$dt->number;
            $Data['phone']=$dt->phone;
            $Data['fax']=$dt->fax;
            $Data['map']=$dt->gglmap;
            $Data['ganalytics']=$dt->gglstats;
            $Data['email']=$dt->email;
            $Data['logo']= $dt->logo;
            $Data['metrics']=$dt->metrics;
            $Data['languages']=$dt->thmactivelang;
                $get_country=new mensio_countries();
                if (!$get_country->Set_UUID($dt->country)) {
                    return false;
                } else {
                    $country=$get_country->GetCountryName();
                    $Data['country']=$country;
                }
        }
        return $Data;
    }
    public function mnsNewCustomer(){
        $new=new mensio_customers();
            $sign=new mensio_customers();
            $inputs=$sign->LoadSelectorTypes("contacts");
            foreach($inputs as $key){
            }
        $new->Username=$_REQUEST['mns_email'];
        $new->Password=$_REQUEST['mns_password'];
        $new->Email=$_REQUEST['mns_email'];
        $new->Firstname=$_REQUEST['mns_name'];
        $new->Lastname=$_REQUEST['mns_lastname'];
        $new->Address=$_REQUEST['mns_address'];
        $new->Phone=$_REQUEST['mns_phone'];
        $new->Type='3a0654a6-0246-11e7-b56a-4ccc6a4aa826';
        $new->Source='FrE';
        $new->IPAddress=$_SERVER['REMOTE_ADDR'];
        $new->InsertNewCustomer();
        return $new;
    }
    public function mnsFrontEndCart($Admin=false){
        $arr=array();
        if(constant("MENSIO_FLAVOR")=="STD"){
            $arr=new MensioFlavored();
            $arr=$arr->mnsFrontEndCart();
            return $arr;
        }
        $totalCost=false;
        if($Admin==false){
            if (!empty($_SESSION['MensioCart'])){
                $i=0;
                foreach($_SESSION['MensioCart'] as $cart){
                    if(empty($cart['ID'])){
                        continue;
                    }
                    $Prod=$this->mnsFrontEndProduct($cart['ID']);
                    $arr[$i]['id']=$cart['ID'];
                    $arr[$i]['MainImage']=$Prod['images'][0]['thumb'];
                    $arr[$i]['MainImage']=$Prod['image'];
                    $arr[$i]['Name']=$Prod['name'];
                    $arr[$i]['Description']=$Prod['description'];
                    $arr[$i]['Quant']=$cart['Quant'];
                    $arr[$i]['tax']=$Prod['tax'];
                    $arr[$i]['discount']=$Prod['discount'];
                    $arr[$i]['price']=$Prod['price'];
                    $arr[$i]['Price']=$Prod['final_price'];
                    $arr[$i]['Cost']=$Prod['final_price']*$cart['Quant'];
                    $arr[$i]['link']=$Prod['link'];
                    $arr[$i]['Weight']=$Prod['Weight'];
                    $arr[$i]['TotalWeight']=$Prod['Weight']*$cart['Quant'];
                    if(is_numeric($Prod['final_price']) && is_numeric($cart['Quant'])){
                        $totalCost=$totalCost+($Prod['final_price']*$cart['Quant']);
                    }
                    $i++;
                }
            }
        }
        return $arr;
    }
    public function mnsFrontEndTOS(){
        $Data="<i>Terms of Service are unavailable</i>";
        $tos=new mensio_store();
            $getStoreID=$tos->LoadStoreData();
            $uuid=$getStoreID[0]->uuid;
        $tos->Set_UUID($uuid);
        $terms=$tos->LoadStoreTermsOfUse();
        $tos=new mensio_seller();
        $terms=$tos->GetActiveTermsNotice();
        if(!empty($terms)){
            $Data=$terms[count($terms)-1]->useterms;
        }
        return $Data;
    }
    public function mnsFrontEndCheckout(){
        return "Checkout";
    }
    public function MensioGetThumb($dims,$imageFile){
        if(!$dims){
            $dims=array("300","300");
        }
        global $wpdb;
        $prfx = $wpdb->prefix;
        $Query = 'SELECT `ID`  FROM `'.$prfx.'posts` WHERE `guid` LIKE "%'.$imageFile.'%"';
        $Result = $wpdb->get_results($Query);
        if($Result){
            $Result= wp_get_attachment_image_src($Result[0]->ID,$dims,false);
            $Result=$Result[0];
        }
        else{
            $Result=false;
        }
        return $Result;
    }
}
function mensiopress_choose_Region(){
    if(!empty($_POST)){
        $url     = wp_get_referer();
        $ref     = url_to_postid( $url ); 
        $seller=new mensio_seller();
        $verification=$seller->VerifyPageIntegrity(filter_var($_POST['mns_sec']), "MensioPressFrontEnd-".$ref);
        if($verification==false){
            echo "Unauthorized";
            die;
        }
    }
    $country_post=filter_var($_REQUEST['mns_country']);
    if(!empty(($_REQUEST['Language']))){
        $_SESSION['MensioThemeLang']= MensioDecodeUUID(filter_var($_REQUEST['Language']));
    }
    $Result=json_encode(array(""));
    $countries=new mensio_seller();
    $countries=$countries->GetCountryCodes();
    foreach($countries as $country){
        if($country->originalName==$country_post || $country->originalID== MensioDecodeUUID($country_post)){
            $Result=$country->uuid;
            $regions=array();
            $get_regions=new mensio_seller();
            $get_regions->Set_Country($country->originalID);
            foreach($get_regions->GetCountryRegions() as $region){
                $regions[]=$region['name'];
            }
            $Result=json_encode($regions);
            break;
        }
    }
    echo $Result;
    die;
}
add_action('init', 'mnsStartSession', 1);
function mnsStartSession() {
    if(!session_id()) {
        session_start();
    }
}
function mensiopress_CollectVisitorData(){
    if(!empty($_POST)){
        $url     = wp_get_referer();
        $ref     = url_to_postid( $url ); 
        $seller=new mensio_seller();
        $verification=$seller->VerifyPageIntegrity(filter_var($_POST['mns_sec']), "MensioPressFrontEnd-".$ref);
        if($verification==false){
            echo "Unauthorized";
            die;
        }
    }
  $Data=array();
  $Data['browser']=trim(filter_var($_REQUEST['mns_browser']));
  $Data['os']=trim(filter_var($_REQUEST['mns_os']));
  $Data['screen']=trim(filter_var($_REQUEST['mns_screen']));
  $Data['ip']=trim(filter_var($_SERVER['REMOTE_ADDR']));
  if(!isset($_SESSION['MensioThemeLang'])){
      $get=new mensio_seller();
      $get=$get->GetDefaultThemeLanguage();
      $_SESSION['MensioThemeLang']=$get['Data'];
  }
  if(!isset($_SESSION['mnsVisitID']) & !empty($Data)){
    $seller=new mensio_seller();
    $seller->Set_IPAddress($Data['ip']);
    if(!empty($Data['os'])){
        $seller->Set_OpSystem($Data['os']);
    }
    $seller->Set_Browser($Data['browser']);
    $seller->Set_ScreenSize($Data['screen']);
    $IsCustomer = true;
    $Customer = $seller->CheckVisitorFromIPAddress();
    if(!$Customer) {
      $Customer='Guest-'.$Data['ip'];
      $IsCustomer = false;
    }
    $seller->Set_Customer($Customer);
    $_SESSION['mnsVisitor'] = array(
      'CustID'=> 'Guest',
      'CredID'=> $Customer,
      'Type'=> 'Guest',
      'LastLogin'=> date("Y-m-d H:i:s"),
      'TermsNotice'=>'2000-01-01 01:00:00'
    );
    $guest=$seller->AddIPAddressToHistory();
    if (!$guest['Error']) {
      $_SESSION['mnsVisitID'] = $guest['Data'];
      if ($IsCustomer) {
        $VisitorInfo = $seller->GetVisitorInfo();
        if (!$VisitorInfo['Error']) {
          $_SESSION['mnsVisitor'] = $VisitorInfo['Data'];
        }
      }
    }
    unset($seller);
  }
  die;
}
function MensioGetUUIDByPostName(){
    $page=get_post( get_the_ID() );
    $UUID="";
    $actualLink = explode("/","http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
    $getUUID=new mensio_seller();
    $getPage=$getUUID->MensioSearchSlug($actualLink[count($actualLink)-2]);
    if(count($getPage)){
        $postID=new mnsGetFrontEndLink();
        $pageType=$getPage[0]->type;
        if($pageType == "Product"){
            $postID=$postID->ProductPage();
        }
        elseif($pageType == "Category"){
            $postID=$postID->CategoryPage();
        }
        elseif($pageType == "Brand"){
            $postID=$postID->BrandPage();
        }
        $GLOBALS['UUID']=$getPage[0]->uuid;
        $UUID=$getPage[0]->uuid;
    }
    else{
        $postID= get_the_ID();
    }
    $page=get_post($postID);
    return array("post"=>$page,"uuid"=>$UUID);
}
add_action( 'init', 'MensioDefineLang' );
function MensioDefineLang() {
    $getThemeLang=new mensio_seller();
    $ThemeLang=$getThemeLang->GetDefaultThemeLanguage();
    $_SESSION['MensioDefaultLang']=$ThemeLang['Data']['uuid'];
    $_SESSION['MensioDefaultLangShortcode']=$ThemeLang['Data']['code'];
    $currentURL = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $langs=new mensio_seller();
        $langs=$langs->GetActiveThemeLanguages();
        $_SESSION['MensioThemeLanguages']=array();
        foreach($langs['Data'] as $lang){
            $_SESSION['MensioThemeLanguages'][]=$lang->code;
        }
    $findLang=explode("/",$currentURL);
    if(empty($_GET['language']) && count($findLang)>=2){
        $langCode=$findLang[1];
    }
    else{
        $langCode=$_GET['language'];
    }
    if(empty($langCode)){
        $_SESSION['MensioThemeLang']=$_SESSION['MensioDefaultLang'];
        $_SESSION['MensioThemeLangShortcode']=$_SESSION['MensioDefaultLangShortcode'];
    }
    else{
        foreach($langs['Data'] as $lang){
            if($lang->code==$langCode){
                $_SESSION['MensioThemeLang']=$lang->uuid;
                $_SESSION['MensioThemeLangShortcode']=filter_var($lang->code);
                $found=1;
            }
        }
        if(empty($found)){
            $_SESSION['MensioThemeLang']=$_SESSION['MensioDefaultLang'];
            $_SESSION['MensioThemeLangShortcode']=$_SESSION['MensioDefaultLangShortcode'];
        }
    }
}
        function MensioPages() {
            $eshopSlug=get_option("MensioPageSlug");
            if(!$eshopSlug){
                $eshopSlug="action";
            }
            if(!empty($_SESSION['MensioThemeLang']) && $_SESSION['MensioThemeLang']!=$_SESSION['MensioDefaultLang']){
                $slug=$_SESSION['MensioThemeLangShortcode']."/".$eshopSlug;
            }
            else{
                $slug=$eshopSlug;
            }
            $args = array(
                'label'  => 'Mensio Page',
                'labels'=>array(
                    "edit_item"=>true
                    ),
                'public' => true,
                'rewrite' => array(
                    'slug' => $slug,
                    'with_front'=>false
                    ),
                'capability_type' => 'page'
            );
            register_post_type( 'mensio_page', $args );
            flush_rewrite_rules();
            if(is_admin()){
                wp_enqueue_style("HideMensioPagesMenuFromAdminMainMenu", plugin_dir_url("public/css/empty.css"));
                wp_add_inline_style("HideMensioPagesMenuFromAdminMainMenu",
                        "#menu-posts-mensio_page,
                            #toplevel_page_mns-html-edit{
                            display:none;
                        }
                        body.post-type-mensio_page .wp-heading-inline{
                            opacity:0;
                        }"
                        );
            }
        }
        add_action( 'init','MensioPages');
        function change_link( $permalink, $post ) {
            if(get_option("page_on_front")==$post->ID){
                if($_SESSION['MensioThemeLang']!=$_SESSION['MensioDefaultLang']){
                    $permalink = get_home_url() ."/".$_SESSION['MensioThemeLangShortcode']."/";
                }
            }
            return $permalink;
        }
        add_filter('post_type_link',"change_link",true,true);
    function MensioBrands() {
        $eshopSlug=get_option("MensioBrandSlug");
        if(!$eshopSlug){
            $eshopSlug="mensio-brand";
        }
        if(!empty($_SESSION['MensioThemeLang']) && $_SESSION['MensioThemeLang']!=$_SESSION['MensioDefaultLang']){
            $slug=$_SESSION['MensioThemeLangShortcode']."/".$eshopSlug;
        }
        else{
            $slug=$eshopSlug;
        }
        $args = array(
            'label'  => 'Mensio Brand',
            'public' => true,
            'rewrite' => array(
                'slug' => $slug,
                'with_front'=>false
                ),
            'capability_type' => false
        );
        register_post_type( 'mensio_brand', $args );
        flush_rewrite_rules();
    }
    add_action( 'init', 'MensioBrands' );
    function MensioCategories() {
        $eshopSlug=get_option("MensioCategorySlug");
        if(!$eshopSlug){
            $eshopSlug="mensio-category";
        }
        if(!empty($_SESSION['MensioThemeLang']) && $_SESSION['MensioThemeLang']!=$_SESSION['MensioDefaultLang']){
            $slug=$_SESSION['MensioThemeLangShortcode']."/".$eshopSlug;
        }
        else{
            $slug=$eshopSlug;
        }
        $args = array(
            'label'  => 'Mensio Category',
            'public' => true,
            'rewrite' => array(
                'slug' => $slug,
                'with_front'=>false
                ),
            'capability_type' => false
        );
        register_post_type( 'mensio_category', $args );
        flush_rewrite_rules();
    }
    add_action( 'init', 'MensioCategories' );
    function MensioProducts() {
        $eshopSlug=get_option("MensioProductSlug");
        if(!$eshopSlug){
            $eshopSlug="mensio-product";
        }
        if(!empty($_SESSION['MensioThemeLang']) && $_SESSION['MensioThemeLang']!=$_SESSION['MensioDefaultLang']){
            $slug=$_SESSION['MensioThemeLangShortcode']."/".$eshopSlug;
        }
        else{
            $slug=$eshopSlug;
        }
        $args = array(
            'public' => true,
            'rewrite' => array(
                'slug' => $slug,
                'with_front'=>false
                ),
            'capability_type' => false
        );
        register_post_type( 'mensio_product', $args );
        flush_rewrite_rules();
    }
    add_action( 'init', 'MensioProducts' );
function mensiopress_getUsersCountry(){
    if(!empty($_POST['CountryCode'])){
        $countries=new mensio_countries();
        foreach($countries->GetCountriesDataSet() as $country){
            $countryCode=explode("-",$country->iso);
            $countryCode=$countryCode[0];
            if($countryCode==filter_var($_POST['CountryCode'])){
                $_SESSION['UserInCountry']=$country->uuid;
            }
        }
    }
    die;
}
