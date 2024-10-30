<?php
add_shortcode( 'mns_cart', 'mensiopress_cart' );
if(!function_exists("mensiopress_cart")){
function mensiopress_cart($atts,$Location){
    if($Location!="topRight"){
        if(empty($atts['titlesize']) && !empty($GLOBALS['MensioPressFontSize'])){
            $atts['titlesize']=$GLOBALS['MensioPressFontSize'];
        }
        if(empty($atts['fontsize']) && !empty($GLOBALS['MensioPressFontSize'])){
            $atts['fontsize']=$GLOBALS['MensioPressFontSize'];
        }
    }
    if(!empty($atts['fontsize'])){
        $atts['fontsize']=str_replace("-",".",$atts['fontsize']);
    }
    $html=false;
    $title="Cart";
    global $post;
    $quant=0;
    $total=0;
    if(!empty($atts['title-'.$_SESSION['MensioThemeLangShortcode']])){
        $title=$atts['title-'.$_SESSION['MensioThemeLangShortcode']];
    }
    else{
        $title="Cart";
    }
    if(!empty($atts['titlesize'])){
        $fontSize=str_replace("-",".",$atts['titlesize']);
    }
    else{
        $fontSize="1";
    }
    if($Location!="topRight"){
        $html="<h2 style='font-size:".$fontSize."rem;' class='mensioObjectTitle'>".$title."</h2><hr class='titleLine' />";
    }
    if(!empty($_GET['action']) && $_GET['action']=="mns-html-edit"){
        $Admin=false;
    }
    else{
        $Admin=false;
    }
    $getCart=new mnsFrontEndObject();
    $cart=$getCart->mnsFrontEndCart($Admin);
    $ExtraClass=false;
    if(count($cart)==0){
        $ExtraClass=" Empty";
    }
    $html.="<div class='mns-list ".$Location.$ExtraClass."'>";
                        if(get_option('MensioPress_TextNoProdsInCart_'.$_SESSION['MensioThemeLangShortcode'])){
                            $NoprodsFoundText=ucfirst(get_option('MensioPress_TextNoProdsInCart_'.$_SESSION['MensioThemeLangShortcode']));
                        }
                        else{
                            $NoprodsFoundText="No Products found in your cart";
                        }
    if(count($cart)>0){
    $total=0;
    foreach($cart as $prod){
        $UserType=false;
        $ar['FinalPrice']=$prod['price'];
        if(($UserType=="Company" || $UserType=="Organization") && !empty($prod['btbprice'])){
            $prod['price']=$prod['btbprice'];
            $prod['tax']=$prod['btbtax'];
            $prod['FinalPrice']=$prod['btbprice'];
        }
        $prod['price']=$prod['price']+($prod['price']*($prod['tax']/100));
        $prod['FinalPrice']=$prod['price'];
        if(!empty($prod['discount'])){
            $prod['price']=$prod['price'];
            $prod['FinalPrice']=$prod['price']-($prod['price']*($prod['discount']/100));
        }
        if($Location=='topRight'){
            $html.='
            <div class="cart-item">
                <div class="cart-data">
                    <a href="'.$prod['link'].'">
                        <img src="'.$prod['MainImage'].'" alt="'.$prod['Name'].'">
                    </a>
                </div>
                <div class="cart-data">
                    <div class="cart-data">';
                        $html.=$prod['Name'];
                        $html.="<div class='cartProductDescription'>".$prod['Description']."</div>";
                    $html.='</div>';
                    $html.='
                    <div class="cart-data cart-prices">
                        <div class="cart-data product-cost mensioPrice" cost="'.$prod['FinalPrice'].'">
                                '.number_format($prod['FinalPrice'],2).'
                        </div>
                        <div class="cart-data">';
                            $html.='x'.$prod['Quant'];
                        $html.='</div>
                        <div class="cart-data cart-product-cost mensioPrice" cost="'.number_format(($prod['FinalPrice']*$prod['Quant']),2).'">'.number_format(($prod['FinalPrice']*$prod['Quant']),2).'</div>';
                $html.='
                    </div>
                </div>
            </div>
            ';
        }
        if($Location!='topRight'){
            $html.='
            <div class="cart-item ">
                <div class="cart-data">
                    <a href="'.$prod['link'].'">
                        <img src="'.$prod['MainImage'].'" alt="'.$prod['Name'].'">
                    </a>
                </div>
                <div class="cart-data" style="font-size:'.$atts['fontsize'].'rem;">';
                if($Location!='Location'){
                    $html.=$prod['Name'];
                }
                else{
                    $html.=$prod['Name'];
                }
                $html.='</div>
                <div style="font-size:'.$atts['fontsize'].'rem;" class="cart-data product-cost mensioPrice" cost="'.$prod['FinalPrice'].'">'.number_format($prod['FinalPrice'],2).'</div>
                <div style="font-size:'.$atts['fontsize'].'rem;" class="cart-data">
                    x
                </div>
                <div style="font-size:'.$atts['fontsize'].'rem;" class="cart-data">';
                $html.='<input type="tel" class="change-quant" min="1" value="'.$prod['Quant'].'">';
                $html.='</div>
                <div style="font-size:'.$atts['fontsize'].'rem;" class="cart-data">
                    =
                </div>
                <div style="font-size:'.$atts['fontsize'].'rem;" class="cart-data cart-product-cost mensioPrice" cost="'.number_format(($prod['FinalPrice']*$prod['Quant']),2).'">'.number_format(($prod['FinalPrice']*$prod['Quant']),2).'</div>';
                if($Location!='topRight'){
                $html.=
                '<div style="font-size:'.$atts['fontsize'].'rem;" class="cart-data">
                    <button  class="remove-from-cart">
                        <i class="fa fa-trash-o" aria-hidden="true"></i>
                    </button>
                </div>';
                }
                $html.='
            </div>
            ';
        }
        $total=$total+($prod['FinalPrice']*$prod['Quant']);
    }
}
if($Location!='topRight' && $total>0){
    $html.='
    <div class="cart-item " style="text-align:right;padding-top:0;">
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>';
            $link=new mnsGetFrontEndLink();
            $link=$link->CheckoutPage();
            $html.='
        <div style="text-align:center;">
            <div style="font-size:'.$atts['fontsize'].'rem;" class="cart-grand-total mensioPrice">'.number_format($total,2).'</div>
            <a type="button" href="'.$link.'"><input type="button" value="Checkout" style="font-size:'.$atts['fontsize'].'rem;"></a>
        </div>
    </div>';
}
elseif($Location=='topRight'){
    $html.='
    <div class="">
        <div class="cart-data cart-grand-total mensioPrice">'.number_format($total,2).'</div>';
                        if(get_option('MensioPress_TextCart_'.$_SESSION['MensioThemeLangShortcode'])){
                            $key=ucfirst(get_option('MensioPress_TextCart_'.$_SESSION['MensioThemeLangShortcode']));
                        }
                        else{
                            $key="Cart";
                        }
            $link=new mnsGetFrontEndLink();
            $link=$link->CartPage();
            $html.='
            <div class="cart-data checkout-link"><a href="'.$link.'">'.$key.'</a></div>
    </div>';
}
    $html.='
</div>
';
    $display="none";
if(count($cart)==0){
    $display="block";
}
$html.='
<div class="noprodsfound" style="display:'.$display.';">'.$NoprodsFoundText.'</div>
';
    return $html;
}
}
if(!empty($_SESSION['UserInCountry'])){
    $countryShipping=new mensio_seller();
    $countryShipping=$countryShipping->FindNewOrderShipping($_SESSION['UserInCountry'], false);
    if(empty($countryShipping)){
        function MensioPressNoShippingFound() {
            $NoShippingMsg=get_option("MensioPress_TextNoShipping_".$_SESSION['MensioThemeLangShortcode']);
            if(empty($NoShipping)){
                $NoShippingMsg='No Shipping found for your country';
            }
            echo "<div class='FixedNoShippingFound'>".$NoShippingMsg."</div>";
        }
        add_action( 'wp_footer', 'MensioPressNoShippingFound' );
    }
}
add_action('wp_ajax_mensiopress_AddtoCart','mensiopress_AddtoCart' );
add_action('wp_ajax_nopriv_mensiopress_AddtoCart','mensiopress_AddtoCart' );
function mensiopress_AddtoCart(){
    if(!empty($_POST['mns_sec'])){
        $url     = wp_get_referer();
        $url     = str_replace($_POST['mns_lang']."/","",$url);
        $ref     = url_to_postid( $url ); 
        $seller=new mensio_seller();
        $verification=$seller->VerifyPageIntegrity(filter_var($_POST['mns_sec']), "MensioPressFrontEnd-".$ref);
        if($verification==false){
            echo json_encode(array(
                "Message"=>"Unauthorized---".$ref,
                "Cart"=>false,
                "Quants"=>0
                    ));
            die;
        }
    $prods=new mnsFrontEndObject();
    $prods=$prods->mnsFrontEndProduct(MensioDecodeUUID(filter_var($_REQUEST['mns_prod'])));
    if(!empty($prods['Weight'])){
        $weight=$prods['Weight'];
    }
    else{
        $weight=0;
    }
    if($prods){
        if(empty($_SESSION['MensioCart'])){
            $_SESSION['MensioCart']=array();
        }
        if(!empty(!$_SESSION['mnsUser']) && !empty($_SESSION['mnsUser']['Active'])){
            $Result="You must validate your account before buying";
        }
        elseif($prods['stock']<=0 && $prods['overstock']==0){
            $Result="Product is unavailable";
        }
        elseif($prods['stock']<$_REQUEST['mns_quant'] && $prods['overstock']==0){
            $Result="Product is unavailable";
        }
        else{
            if(isset($_SESSION['MensioCart'])){
                if(!$_SESSION['MensioCart'][filter_var($_REQUEST['mns_prod'])]){
                    $_SESSION['MensioCart'][filter_var($_REQUEST['mns_prod'])]=array(
                        'ID'=> MensioDecodeUUID(filter_var($_REQUEST['mns_prod'])),
                        'Quant'=>filter_var($_REQUEST['mns_quant']),
                        'Weight'=> $weight
                        );
                    $Result= "Added to Cart";
                }
                else{
                    $moreQuant=$_SESSION['MensioCart'][filter_var($_REQUEST['mns_prod'])]['Quant']+filter_var($_REQUEST['mns_quant']);
                    $moreWeight=$_SESSION['MensioCart'][filter_var($_REQUEST['mns_prod'])]['Weight']+$weight;
                    $_SESSION['MensioCart'][filter_var($_REQUEST['mns_prod'])]=array(
                        'ID'=>MensioDecodeUUID(filter_var($_REQUEST['mns_prod'])),
                        'Quant'=>$moreQuant,
                        'Weight'=>$moreWeight
                        );
                    $Result= "Added more to Cart";
                }
            }
            else{
                $_SESSION['MensioCart']=array(
                    filter_var($_REQUEST['mns_prod'])=>array(
                        'ID'=>MensioDecodeUUID(filter_var($_REQUEST['mns_prod'])),
                        'Quant'=>filter_var($_REQUEST['mns_quant']),
                        'Weight'=>$weight*filter_var($_REQUEST['mns_quant'])
                        )
                );
                $Result= "Added to Cart";
            }
        }
    }
    $Quants=0;
    $getCart=new mnsFrontEndObject();
    foreach($getCart->mnsFrontEndCart(false) as $cart){
        $Quants=$Quants+$cart['Quant'];
    }
    $html=mensiopress_cart("","topRight");
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
            $instance['Icon']="CartWhiteIcon";
        }
        else{
            $instance['Icon']="Cart".$instace['cart-color']."Icon";
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
        $html= MensioPressWidgetListProducts("Cart",$array,$instance,$Link,$LinkText,$NoProdsText);
    }
    echo json_encode(array(
        "Message"=>$Result,
        "Cart"=>$html,
        "Quants"=>$Quants
            ));
    die;
}
add_action('wp_ajax_mensiopress_RemoveFromCart','mensiopress_RemoveFromCart' );
add_action('wp_ajax_nopriv_mensiopress_RemoveFromCart','mensiopress_RemoveFromCart' );
function mensiopress_RemoveFromCart(){
    if(empty($_POST['mns_sec'])){
        die;
    }
    $url     = wp_get_referer();
    $url     = str_replace($_POST['mns_lang']."/","",$url);
    $ref     = url_to_postid( $url ); 
    $seller=new mensio_seller();
    $verification=$seller->VerifyPageIntegrity(filter_var($_POST['mns_sec']), "MensioPressFrontEnd-".$ref);
    if($verification==false){
        echo json_encode(array(
            "Message"=>"Unauthorized",
            "Cart"=>false,
            "Quants"=>0
                ));
        die;
    }
    $i=0;
    foreach($_SESSION['MensioCart'] as $prodID=>$items){
        if($i==filter_var($_REQUEST['mnsProdRemove'])){
            $toDelete=$prodID;
            break;
        }
        $i++;
    }
    $Message="notDeleted";
    if($toDelete){
        unset($_SESSION['MensioCart'][$toDelete]);
        if(!$Message=get_option("MensioPress_TextRemovedFromCart_".$_SESSION['MensioThemeLangShortcode'])){
            $Message= "Deleted";
        }
    }
    $Quants=0;
    $getCart=new mnsFrontEndObject();
    foreach($getCart->mnsFrontEndCart(false) as $cart){
        $Quants=$Quants+$cart['Quant'];
    }
    $Result= json_encode(array(
        "Message"=>$Message,
        "Cart"=>mensiopress_cart("","topRight"),
        "Quants"=>$Quants
            ));
    echo $Result;
    die;
}
add_action('wp_ajax_mensiopress_UpdateCartQuant','mensiopress_UpdateCartQuant' );
add_action('wp_ajax_nopriv_mensiopress_UpdateCartQuant','mensiopress_UpdateCartQuant' );
function mensiopress_UpdateCartQuant(){
    $url     = wp_get_referer();
    $url     = str_replace($_POST['mns_lang']."/","",$url);
    $ref     = url_to_postid( $url ); 
    $seller=new mensio_seller();
    $verification=$seller->VerifyPageIntegrity(filter_var($_POST['mns_sec']), "MensioPressFrontEnd-".$ref);
    if($verification==false){
        echo json_encode(array(
            "Message"=>"Unauthorized",
            "Cart"=>false,
            "Quants"=>0
                ));
        die;
    }
    $i=0;
    foreach($_SESSION['MensioCart'] as $prodID=>$items){
        if($i==filter_var($_REQUEST['mnsProd'])){
            $toUpdate=$prodID;
            break;
        }
        $i++;
    }
    if($toUpdate){
        $_SESSION['MensioCart'][$toUpdate]['Quant']=filter_var($_REQUEST['mnsProdNewQuant']);
        $Message= "Updated";
    }
    else{
        $Message="not Updated";
    }
    $Quants=0;
    $getCart=new mnsFrontEndObject();
    foreach($getCart->mnsFrontEndCart(false) as $cart){
        $Quants=$Quants+$cart['Quant'];
    }
    $Result= json_encode(array(
        "Message"=>$Message,
        "Cart"=>mensiopress_cart("","topRight"),
        "Quants"=>$Quants
            ));
    echo $Result;
    die;
}
function MensioTopRightCart($atts=false){
    $getCart=new mnsFrontEndObject();
    $cart=$getCart->mnsFrontEndCart(false);
    $MensioQuants=0;
    $ExtraClass=false;
    foreach($cart as $cart){
        $MensioQuants=$MensioQuants+$cart['Quant'];
    }
    if(empty($atts['cart-color'])){
        $CartColor=get_option( "MensiotopRightCartColor");
    }
    else{
        $CartColor=$atts['cart-color'];
    }
    if(count($cart)==0){
        $ExtraClass='Empty';
    }
                echo '<div id="MensioCart" class="'.$ExtraClass.'">';
                echo mns_cart("","topRight");
                echo'</div>';
    echo '<div class="MensioTopRightCart Mensio'.$CartColor.'Cart">';
            if(!empty($atts['show-quantities']) && $atts['show-quantities']=='yes'){
                echo '<div id="MensioCartQuantities">'.$MensioQuants.'</div>';
            }
                echo '
                </div>';
        $title="Cart";
        if(!empty($atts['title_'.$_SESSION['MensioThemeLangShortcode']])){
            $title=$atts['title_'.$_SESSION['MensioThemeLangShortcode']];
        }
        echo "<span class='MensioTopRightCartTitle'><strong>".$title."</strong></span>";
}
function MensioGetCartTotal(){
        if(!empty($_SESSION['MensioCart'])){
        $getCart=new mnsFrontEndObject();
        $cart=$getCart->mnsFrontEndCart(false);
        $total=0;
        foreach($cart as $prod){
            $UserType=false;
            $prod['FinalPrice']=$prod['price'];
            $prod['price']=$prod['price']+($prod['price']*($prod['tax']/100));
            $prod['FinalPrice']=$prod['price'];
            if(!empty($prod['discount'])){
                $prod['price']=$prod['price'];
                $prod['FinalPrice']=$prod['price']-($prod['price']*($prod['discount']/100));
            }
            $total=$total+($prod['FinalPrice']*$prod['Quant']);
        }   
    }
    else{
        $total=0;
    }
    return $total;
}