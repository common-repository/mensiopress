<?php
add_shortcode( 'mns_checkout', 'mnsCheckout' );
if(!function_exists("mnsCheckout")){
function mnsCheckout($atts){
    if(empty($atts)){
        $atts=array();
    }
    if(empty($atts['titlesize']) && !empty($GLOBALS['MensioPressFontSize'])){
        $atts['titlesize']=$GLOBALS['MensioPressFontSize'];
    }
    if(empty($atts['fontsize']) && !empty($GLOBALS['MensioPressFontSize'])){
        $atts['fontsize']=$GLOBALS['MensioPressFontSize'];
    }
    $get=new mnsFrontEndObject();
    if(count($get->mnsFrontEndCart())==0){
        return "Please add Products to your cart";
    }
    $cart=new mnsFrontEndObject();
    $cart=$cart->mnsFrontEndCart();
    $TotalWeight=0;
    foreach($cart as $crt){
        $TotalWeight=$TotalWeight+$crt['TotalWeight'];
    }
    $html='';
    if(empty($atts['title'])){$title="Checkout";}
    else{$title=$atts['title'];}
    if(!empty($_POST) &&
            !empty($_POST['status']) &&
            $_POST['status']=="CAPTURED" &&
            !empty($_GET['result']) &&
            $_GET['result']=='success' &&
            MENSIO_FLAVOR=='STD'
            ){
        $go=new mensioSellerExtend();
        $ar=array();
        foreach($_POST as $post=>$value){
            if($post=='digest'){
                continue;
            }
            $ar[]=$value;
        }
        $ar[]=$go->GetGatewayPaymentParameters("03 Digest Key");
        $myDigest=base64_encode(sha1(implode("",$ar),true));
        $postDigest=$_POST['digest'];
        if($myDigest==$postDigest){
            $comple=new mensio_seller();
            $comple->Set_NewOrderID(MensioDecodeUUID(filter_var($_POST['orderid'])));
            $comple->GiverOrderStatus("Complete");
            $comple->UpdateOrderToComplete();
            SendOrderToCustomer(MensioDecodeUUID(filter_var($_POST['orderid'])));
            $MensioPressScript="window.location.href='".$go->GetGatewayPaymentParameters("06 Return Success Page")."';";
            $tt=rand(1,1000);
            wp_enqueue_script("MensioPressPublicJS".$tt,plugin_dir_url(dirname(__FILE__))."js/empty.js");
            wp_add_inline_script("MensioPressPublicJS".$tt, $MensioPressScript);
        }
        unset($_SESSION['MensioCart']);
        die;
    }
    if(!empty($_POST) &&
            !empty($_POST['status']) &&
            $_POST['status']=="CANCELED" &&
            !empty($_GET['result']) &&
            $_GET['result']=='fail' && 
            MENSIO_FLAVOR=='STD'
            ){
        $comple=new mensio_seller();
        $comple->Set_NewOrderID(MensioDecodeUUID(filter_var($_POST['orderid'])));
        $comple->GiverOrderStatus("Canceled");
        $comple->UpdateOrderToComplete();
        $go=new mensioSellerExtend();
        $MensioPressScript="window.location.href='".$go->GetGatewayPaymentParameters("07 Return Failed Page")."';";
        $tt=rand(1,1000);
        wp_enqueue_script("MensioPressPublicJS".$tt,plugin_dir_url(dirname(__FILE__))."js/empty.js");
        wp_add_inline_script("MensioPressPublicJS".$tt, $MensioPressScript);
        die;
    }
    if(!empty($_POST) &&
            !empty($_POST['status']) &&
            $_POST['status']=="FAILED" &&
            !empty($_GET['result']) &&
            $_GET['result']=='fail' &&
            MENSIO_FLAVOR=='STD'
            ){
        $comple=new mensio_seller();
        $comple->Set_NewOrderID(MensioDecodeUUID(filter_var($_POST['orderid'])));
        $comple->GiverOrderStatus("Failed");
        $comple->UpdateOrderToComplete();
        $go=new mensioSellerExtend();
        $MensioPressScript="window.location.href='".$go->GetGatewayPaymentParameters("07 Return Failed Page")."';";
        $tt=rand(1,1000);
        wp_enqueue_script("MensioPressPublicJS".$tt,plugin_dir_url(dirname(__FILE__))."js/empty.js");
        wp_add_inline_script("MensioPressPublicJS".$tt, $MensioPressScript);
        die;
    }
    if(!empty($_GET['paywith']) && $_GET['paywith']=='PayPal'
            && count($_SESSION['MensioCart'])>0 && !empty($_SESSION['mnsPayPalOrder'])
                && !empty($_SESSION['mnsPayPalSerial']) ){
        $getCart=new mnsFrontEndObject();
        $Cart=$getCart->mnsFrontEndCart();
        $cart=new MensioFlavored();
        $cart=$cart->mnsFrontEndCart();
        $Total=0;
        foreach($cart as $crt){
            $Total=$Total+$crt['Cost'];
        }
        $Total=number_format($Total,2);
        if(!empty($_SESSION['MensioCart']['Shipping'])){
            $seller=new mensio_seller();
            $shipping=$seller->GetShippingData($_SESSION['MensioCart']['Shipping']);
            $shippingCost=number_format($shipping['Data'][0]->price,2);
        }
        if(!empty($shippingCost)){
            $Total=$Total+$shippingCost;
        }
        $PayPalLiveID=$_SESSION['PayPalLiveID'];
        $PayPalSandbox=$_SESSION['PayPalSandboxID'];
        if($_SESSION['PayPalSandboxMode']=="Y"){
            $env="sandbox";
        }
        else{
            $env='production';
        }
        wp_enqueue_script("MensioPressPayPal", "https://www.paypalobjects.com/api/checkout.js");
        $html='
        <div id="paypal-button-container"></div>
        <input type="hidden" id="payPalAnswers" value="'.md5($_SESSION['mnsPayPalSerial']."/".$_SESSION['mnsPayPalOrder']).'" />
        ';
        $MensioPressScript='
            paypal.Button.render({
                env: "'.$env.'", // sandbox | production
                style: {
                  size: "responsive"
                },
                client: {
                    sandbox:    "'.$PayPalSandbox.'",
                    production: "'.$PayPalLiveID.'"
                },
                commit: true,
                payment: function(data, actions) {
                return actions.payment.create({
                    payment: {
                        transactions: [
                            {
                                amount: { total: "'.$Total.'", currency: "EUR" },
                                custom: jQuery("#payPalAnswers").val()
                            }
                        ]
                    }
                });
               },
               onAuthorize: function(data, actions) {
                   return actions.payment.execute().then(function(dd) {
                        var aa=dd;
                        jQuery.ajax({
                            type: "post",
                            url: ajaxurl,
                            data: {
                              "action": "mns_NewPayPalPayment",
                              "mnsPayPal": aa,
                              "mnsPayPalOrder": "'.MensioEncodeUUID($_SESSION['mnsPayPalOrder']).'"
                            },
                            success:function(data) {
                                MensioMessage("Payment Complete!");
                                window.location.href="";
                            }
                        });
                   });
               }
           }, "#paypal-button-container");
        ';
        wp_add_inline_script("https://www.paypalobjects.com/api/checkout.js", $MensioPressScript);
        return $html;
    }
    $link=new mnsGetFrontEndLink();
    if(!empty($atts['title'])){
        $title=$atts['title'];
    }
    else{
        $title="Checkout";
    }
    if(!empty($atts['titlesize'])){
        $fontSize=str_replace("-",".",$atts['titlesize']);
    }
    else{
        $fontSize="1";
    }
    $html.="<h2 style='font-size:".$fontSize."rem;' class='mensioObjectTitle'>".$title."</h2><hr />";
    $html.="<div class='CartItems'>";
        $Object=new mnsFrontEndObject();
        $Cart=$Object->mnsFrontEndCart();
        $Prods=array();
        $Total=0;
        foreach($Cart as $cart){
            $Prod=$Object->mnsFrontEndProduct($cart['id']);
            $Prod['FinalPrice']= number_format($Prod['price'],2);
            if(!empty($Prod['discount'])){
                $Prod['FinalPrice']=number_Format($Prod['price']-($Prod['price']*($Prod['discount']/100)),2);
            }
            $Prod['FinalPrice']=number_Format($Prod['FinalPrice']+($Prod['FinalPrice']*($Prod['tax']/100)),2);
            $Prod['FinalPrice']=number_format($Prod['FinalPrice'],2);
            $Total=$Total+$Prod['FinalPrice'];
            $Prods[]=$Prod;
        }
        wp_enqueue_script("jquery-ui-accordion");
        $html.= "<div class='mns-block mns-cart show-prices' style='text-align:center;'>".MensioList($Prods, array(
            "display"=>"carousel",
            "showprices"=>"yes",
            "carouselautoplay"=>false,
            "carousel-loop"=>"no",
            "show-price-with-tax"=>"yes"
            ), false, false)."</div>";
    $html.="</div>";
    $html.="<div class='CheckoutTotal'><span class='mensioPrice'>".number_format($Total,2)."</span></div>";
    if(empty($_SESSION['MensioCart']) ){
        return $html."Please add a product in your Cart";
    }
    unset($_SESSION['MensioCart']['Shipping']);
    $html.='
        <center class="mensio-steps">
            <span class="mensio-step current-step">1</span>
            <span class="mensio-step">2</span>
            <span class="mensio-step">3</span>
            <span class="mensio-step">4</span>
        </center>
        <div class="mensio-checkout-tab mensio-checkout-1">
            <div class="mensio-tab-controls">
                <input type="button" value="Next" class="next-tab">
            </div>
            <!--Billing Address:<br/><br/>-->
            <div>
                <div class="mensio-give-data">
            '.MensioCheckoutStep_1().'
                </div>
            </div>
            <div class="mensio-tab-controls">
                <input type="button" value="Next" class="next-tab">
            </div>
        </div>
        ';
        $html.='
        <div class="mensio-checkout-tab mensio-checkout-2">
            <div class="mensio-tab-controls">
                <input type="button" value="Previous" class="prev-tab">
                <input type="button" value="Next" class="next-tab">
            </div>
            '.MensioCheckoutStep_2().'
            <div class="mensio-tab-controls">
                <input type="button" value="Previous" class="prev-tab">
                <input type="button" value="Next" class="next-tab">
            </div>
        </div>
        <div class="mensio-checkout-tab mensio-checkout-3">
            <div class="mensio-tab-controls">
                <input type="button" value="Previous" class="prev-tab">
                <input type="button" value="Next" class="next-tab">
            </div>
            <div class="mensio-tab-controls">
                <input type="button" value="Previous" class="prev-tab">
                <input type="button" value="Next" class="next-tab">
            </div>
        </div>
        <div class="mensio-checkout-tab mensio-checkout-4">
            <div class="mensio-tab-controls">
                <input type="button" value="Previous" class="prev-tab">
            </div>
            <br /><strong>Payment method</strong>
            '.MensioCheckoutStep_4().'
            <div class="mensio-tab-controls">
                <input type="button" value="Previous" class="prev-tab">
            </div>
            <!--
            <label>
                <input type="checkbox" value="agree" id="mensio-agree-to-terms">
                I agree to the <a href="'.$link->TOSPage().'">Terms of Service</a>
            </label>
            -->
        </div>
    ';
    return $html;
}
}
function MensioCheckoutStep_1(){
    $html='';
    $countries=new mensio_seller();
    $countries=$countries->GetCountryCodes();
    $countryText=false;
    foreach($countries as $count){
        if(!empty($_SESSION['UserInCountry']) && $count->uuid==$_SESSION['UserInCountry']){
            $countryText=$count->originalName;
            $getRegions=new mensio_seller();
            $getRegions->Set_Country($count->originalID);
            $regions=$getRegions->GetCountryRegions();
        }
    }
    if(empty($_SESSION['mnsUser']['Credential'])){
        $html.='
    <div class="form-input">
    <strong>Fullname:</strong>
        <input type="text" name="mns-fullname" placeholder="Firstname Lastname" autocomplete="off">
    </div>
    <div class="form-input">
    <strong>Email:</strong>
        <input type="text" name="mns-email" placeholder="email" autocomplete="off">
    </div>
    <div class="form-input">
    <strong>Phone:</strong>
        <input type="text" name="mns-phone" placeholder="Phone" autocomplete="off">
    </div>
    <div class="form-input">
    <strong>Country:</strong>
        <select name="mns-country" current-country="'.$countryText.'" language="'. MensioEncodeUUID($_SESSION['MensioThemeLang']).'"><option></option>';
        foreach($countries as $country){
            $html.='<option value="'.$country->originalName.'" data-default="'.$country->originalName.'">'.$country->name.'</option>';
        }
    $html.='
        </select>
    </div>
    <div class="form-input">
    <strong>Region:</strong>
        <select name="mns-region">';
            if(!empty($countryText)){
                $html.="<option></option>";
                foreach($regions as $region){
                    $html.="<option>".$region['name']."</option>";
                }
            }
            else{
                $html.='
                <option></option>';
            }
        $html.='
        </select>
    </div>
    <div class="form-input">
    <strong>Street:</strong>
        <input type="text" name="mns-address" placeholder="Street" autocomplete="off">
    </div>
    <div class="form-input">
    <strong>Zip Code:</strong>
        <input type="text" name="mns-zipcode" placeholder="Zip Code" autocomplete="off">
    </div>
    <div class="form-input">
    <strong>City:</strong>
        <input type="text" name="mns-city" placeholder="City" autocomplete="off">
    </div>
    ';
    }
    else{
        $user_uuid=$_SESSION['mnsUser']['Credential'];
        $address=new mensio_customers();
        $address->Set_UUID($user_uuid);
        $addresses=$address->LoadCustomerAddress();
        $html.="<strong>Choose Billing Address</strong>
                <div class='ChooseBillingAddress'>";
        $Type=false;
        $checked="checked";
        foreach($addresses as $address){
            if($address->deleted=='1'){
                continue;
            }
                        if($address->name!="Billing"
                                && $address->name!="Billing / Shipping"){
                            continue;
                        }
                        if($address->name!=$Type){
                            $Type=$address->name;
                        }
                        foreach($countries as $count){
                            if($count->uuid==$address->country){
                                $country=$count->name;       
                                $get_regions=new mensio_seller();
                                $get_regions->Set_Country($count->uuid);
                                if(is_array($get_regions->GetCountryRegions())){
                                    foreach($get_regions->GetCountryRegions() as $reg){
                                        if($reg['uuid']==$address->region){
                                            $region=$reg['name'];
                                        }
                                    }
                                }
                                else{
                                    $region="No Region";
                                }
                                break;
                            }
                        }
                        if(empty($region)){
                            $region="No Region";
                        }
                        $checked=false;
                        $html.='
                        <!--<div class="mensio-slide">-->
                            <label>
                                <input type="radio" name="billing-address" class="billing-address" value="'.$address->uuid.'" country="'.$country.'" '.$checked.'>
                               '.$address->street.',
                               '.$address->city.' - 
                               '.$country.'
                            </label>
                            <!--<div class="mensio-slide-details">-->
                            <div class="Address">
                                <div class="form-input">
                                    <strong>Fullname:</strong>
                                    <div class="form-value">
                                        '.$address->fullname.'
                                    </div>
                                </div>
                                <div class="form-input">
                                    <strong>Email:</strong>
                                    <div class="form-value">
                                        '.$_SESSION['mnsUser']['UserName'].'
                                    </div>
                                </div>
                                <div class="form-input">
                                    <strong>Phone:</strong>
                                    <div class="form-value">
                                        '.$address->phone.'
                                    </div>
                                </div>
                                <div class="form-input">
                                    <strong>Country:</strong>
                                    <div class="form-value">
                                        '.$country.'
                                    </div>
                                </div>
                                <div class="form-input">
                                    <strong>Region:</strong>
                                    <div class="form-value">
                                        '.$region.'
                                    </div>
                                </div>
                                <div class="form-input">
                                    <strong>City:</strong>
                                    <div class="form-value">
                                        '.$address->city.'
                                    </div>
                                </div>
                                <div class="form-input">
                                    <strong>Zip Code:</strong>
                                    <div class="form-value">
                                        '.$address->zipcode.'
                                    </div>
                                </div>
                                <div class="form-input">
                                    <strong>Street:</strong>
                                    <div class="form-value">
                                        '.$address->street.'
                                    </div>
                                </div>
                            </div>
                        <!--</div>
                        <hr />-->
                            ';
                        $checked=false;
        }
            $html.="</div>";
       $html.="<strong>Choose Shipping Address</strong>
               <div class='ChooseShippingAddress'>";
        $Type=false;
        $checked="checked";
        foreach($addresses as $address){
            if($address->deleted=='1'){
                continue;
            }
                        if($address->name!="Shipping"
                                && $address->name!="Billing / Shipping"){
                            continue;
                        }
                        if($address->name!=$Type){
                            $Type=$address->name;
                        }
                        foreach($countries as $count){
                            if($count->uuid==$address->country){
                                $country=$count->name;       
                                $country=$count->originalName;
                                $countryShow=$count->name;
                                $get_regions=new mensio_seller();
                                $get_regions->Set_Country($count->uuid);
                                if(is_array($get_regions->GetCountryRegions())){
                                    foreach($get_regions->GetCountryRegions() as $reg){
                                        if($reg['uuid']==$address->region){
                                            $region=$reg['name'];
                                        }
                                    }
                                }
                                else{
                                    $region="No Region";
                                }
                                break;
                            }
                        }
                        $checked=false;
                        $html.='
                        <!--<div class="mensio-slide">-->
                            <label>
                                <input type="radio" name="shipping-address" class="address" value="'.$address->uuid.'" country="'.$country.'" '.$checked.'>
                               '.$address->street.',
                               '.$address->city.' - 
                               '.$countryShow.'
                            </label>
                            <!--<div class="mensio-slide-details">-->
                            <div class="Address">
                                <div class="form-input">
                                    <strong>Fullname:</strong>
                                    <div class="form-value">
                                        '.$address->fullname.'
                                    </div>
                                </div>
                                <div class="form-input">
                                    <strong>Email:</strong>
                                    <div class="form-value">
                                        '.$_SESSION['mnsUser']['UserName'].'
                                    </div>
                                </div>
                                <div class="form-input">
                                    <strong>Phone:</strong>
                                    <div class="form-value">
                                        '.$address->phone.'
                                    </div>
                                </div>
                                <div class="form-input">
                                    <strong>Country:</strong>
                                    <div class="form-value">
                                        '.$country.'
                                    </div>
                                </div>
                                <div class="form-input">
                                    <strong>Region:</strong>
                                    <div class="form-value">
                                        '.$region.'
                                    </div>
                                </div>
                                <div class="form-input">
                                    <strong>City:</strong>
                                    <div class="form-value">
                                        '.$address->city.'
                                    </div>
                                </div>
                                <div class="form-input">
                                    <strong>Zip Code:</strong>
                                    <div class="form-value">
                                        '.$address->zipcode.'
                                    </div>
                                </div>
                                <div class="form-input">
                                    <strong>Street:</strong>
                                    <div class="form-value">
                                        '.$address->street.'
                                    </div>
                                </div>
                            </div>
                        <!--</div>
                        <hr />-->
                            ';
                        $checked=false;
        }
            $html.="</div>";
    }
    return $html;
}
function MensioCheckoutStep_2(){
    $NoShippingMsg=get_option("MensioPress_TextNoShipping_".$_SESSION['MensioThemeLangShortcode']);
    if(empty($NoShippingMsg)){
        $NoShippingMsg='No Shipping found for your country';
    }
    $total=MensioGetCartTotal();
    $html='
    <div class="mns-ShippingMethods">
    <strong>Shipping Methods</strong>
                <div class="mns-ShippingCompanies"></div>
                <!--
                <div class="mns-ShippingMethods-row mns-header-label">
                    Shipping Method
                </div>
                <div class="mns-ShippingMethods-row mns-headers">
                    <div class="mns-header">Service Name</div>
                    <div class="mns-header">Speed</div>
                    <div class="mns-header">Bill Type</div>
                    <div class="mns-header">Cost</div>
                    <div class="mns-header"></div>
                </div>
                <div class="mns-ShippingMethods-row mns-first-row">
                    <div class="mns-ShippingService"></div>
                    <div class="mns-ShippingSpeed"></div>
                    <div class="mns-ShippingBillType"></div>
                    <div class="mns-ShippingCost mensioPrice"></div>
                    <div class="mns-ShippingChoose"><input type="radio" name="choose" class="shipping-company" value=""></div>
                </div>
                -->
                <div class="mns-noShipping">
                    '. $NoShippingMsg.'
                </div>
                <div class="mns-ShippingMethods-finalrow mns-FinalCost mensioPrice" cost="'.number_format($total,2).'">'.number_format($total,2).'</div>
            </div>';
    return $html;    
}
function MensioCheckoutStep_3(){
    $tos=new mensio_seller();
    $terms=$tos->GetActiveTermsNotice();
    return "";
}
if(!function_exists("MensioCheckoutStep_4")){
    function MensioCheckoutStep_4(){
    $paymentMethods=new mensio_seller_gateways();
    $paymentMethods=$paymentMethods->GetActivePaymentMethods();
    $html='';
    $html.="<div id='choosePayment'>";
    $k=1;
    $checked="checked";
    if(!empty($paymentMethods['Data'])){
        foreach($paymentMethods['Data'] as $method){
        if(
            $method['Type']=='On Delivery' ||
            $method['Type']=='Bank Deposit' ||
            $method['Type']=='PayPal'
            ){
        $extraClass='';
        $paymethod=new mensio_seller_gateways();
        $payment=$paymethod->GetPaymentMethodData($method['Payment'],"Gateway");
        if($method['Type']=='On Delivery'){
            $payment=$paymethod->GetPaymentMethodData($method['Payment'],"Delivery");
        }
        elseif($method['Type']=='Bank Deposit'){
            $payment=$paymethod->GetPaymentMethodData($method['Payment'],"Deposit");
        }
        else{
            $payment=$paymethod->GetPaymentMethodData($method['Payment'],"Gateway",$method['Type']);
        }
        if($method['Type']=="Bank Deposit"){
            $PayMethod = new mensio_payment_methods();
            if ($PayMethod->Set_UUID($method['Payment'])) {
              $Data = $PayMethod->LoadBankDepositData(false);
              if ((is_array($Data)) && (!empty($Data[0]))) {
                foreach ($Data as $Row) {
                  $RtrnData['Description'] = $Row->description;
                  $RtrnData['Instructions'] = $Row->instructions;
                }
              }
              $RtrnData['Options'] = $PayMethod->LoadBankAccountList();
            }
        }
        if(!empty($payment['Data']) && $method['Type']=='Eurobank'){
            $extraClass=" Gateway";
        }
        $link="";
        if($method['Type']=="PayPal"){
            if ( get_option('permalink_structure') ){
                $link="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."?paywith=PayPal";
            }
            else{
                $link="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."&paywith=PayPal";
            }
        }
        $html.= '<label>';
        $html.='<input type="radio" name="choosePaymentType" id="'.$method['Payment'].'">';
        $html.=$method['Type'].'</label>';
        $html.= "<div class='MensioPayWith".$method['Type'].$extraClass." PayMethod' link='".$link."' id='pay-with-".MensioEncodeUUID($method['Payment'])."'>";
        if(str_replace(" ","",$method['Type'])=="OnDelivery"){
            $img=str_replace(" ","",$method['Type']);
        }
        elseif(str_replace(" ","",$method['Type'])=="PayPal"){
            $img="PaymentGateways";
            $img="PayPal";
            foreach($payment['Data']['Options'] as $data){
                if($data->parameter=="01 Client ID Live"){
                    $_SESSION['PayPalLiveID']=$data->value;
                }
                if($data->parameter=="02 Client ID Sandbox"){
                    $_SESSION['PayPalSandboxID']=$data->value;
                }
                if($data->parameter=="00 Active Sandbox Mode"){
                    $_SESSION['PayPalSandboxMode']=$data->value;
                }
            }
        }
        else{
            $img=str_replace(" ","",$method['Type']);
        }
        $html.="<label class='choosePayment' for='".$method['Payment']."'>";
        $paymentType=str_replace(array(" ","On","Bank"),"",$method['Type']);
        $html.="</label>";
        $checked=false;
        if(!empty($payment['Data']) && $method['Type']=='Eurobank'){
        }
        elseif($method['Type']!='Eurobank'){
            $html.='<form method="post">
                    <input type="hidden" name="MensioPostOrder" value="'.$method['Payment'].'">
                    </form>';
        }
        $html.="<div class='PaySection1'>";
            if(!empty($payment['Data']['Description'])){
                $html.="<div class='checkout-description'><i class='fa fa-info'></i>"
                        . "<span>".$payment['Data']['Description']."</span>"
                    . "</div>";
            }
            if(!empty($payment['Data']['Instructions'])){
                $html.="<div class='checkout-instructions'>".$payment['Data']['Instructions']."</i></div>";
            }
            if($method['Type']=="Bank Deposit"){
                foreach($RtrnData['Options'] as $ac){
                    $html.="<br /><img src='".site_url()."/".$ac->account_icon."' style='display: inline;max-width: 75px;'><br />".
                            $ac->account_bank."<hr />"
                            . "Account Number: ".$ac->account_number."<br />"
                            . "Account Name: ".$ac->account_name."<br />"
                            . "IBAN: ".$ac->account_iban."<br />"
                            . "Routing: ".$ac->account_routing."<br />"
                            . "SWIFT: ".$ac->account_swift."<br />";
                }
            }
            $image=plugin_dir_url(__FILE__)."../../admin/icons/payment/".$img.".svg";
            if(!empty($payment['Data']['Options'])){
                $Options=$payment['Data']['Options'];
                foreach($Options as $opt){
                    if(!empty($opt->parameter) && $opt->parameter=="Icon" && !empty($opt->value)){
                        $image=site_url()."/".$opt->value;
                    }
                }
            }
        $html.="<br /><br /></div>";
        $html.= "
                <div class='PaySection2'>
                    <img src='".$image."'><br/>
                    <!--<input type='button' value='Pay' class='MensioPayButton'>-->
                    <button class='MensioPayButton' id='pay-with-".MensioEncodeUUID($method['Payment'])."' style='background:#000;color:#fff;padding:10px 20px;'>
                        Pay <span class='Total mensioPrice'></span>
                    </button>
                </div>";
        $html.="
            </div>";
            }
    }
    }
    $html.="</div>";
    return $html;
}
}
add_action('wp_ajax_mensiopress_NewShippingCompany','mensiopress_NewShippingCompany' );
add_action('wp_ajax_nopriv_mensiopress_NewShippingCompany','mensiopress_NewShippingCompany' );
function mensiopress_NewShippingCompany(){
    if(!empty($_POST)){
        $url     = wp_get_referer();
        $url     = str_replace($_POST['mns_lang']."/","",$url);
        $ref     = url_to_postid( $url ); 
        $seller=new mensio_seller();
        $verification=$seller->VerifyPageIntegrity(filter_var($_POST['mns_sec']), "MensioPressFrontEnd-".$ref);
        if($verification==false){
            echo "Unauthorized";
            die;
        }
    }
    $shipping=filter_var($_REQUEST['mns_ShippingCompany']);
    $_SESSION['MensioCart']['Shipping']= MensioDecodeUUID($shipping);
    die;
}
add_action('wp_ajax_mensiopress_CheckingOut','mensiopress_CheckingOut');
add_action('wp_ajax_nopriv_mensiopress_CheckingOut','mensiopress_CheckingOut' );
if(!function_exists("mensiopress_CheckingOut")){
function mensiopress_CheckingOut(){
    if(!empty($_POST)){
        $url     = wp_get_referer();
        $url     = str_replace($_POST['mns_lang']."/","",$url);
        $ref     = url_to_postid( $url ); 
        $seller=new mensio_seller();
        $verification=$seller->VerifyPageIntegrity(filter_var($_POST['mns_sec']), "MensioPressFrontEnd-".$ref);
        if($verification==false){
            echo "Unauthorized";
            die;
        }
    }
    $get=new mnsFrontEndObject();
    if(count($get->mnsFrontEndCart())==0){
        die;
    }
    $CartTotal=MensioGetCartTotal();
    $get=new mensio_seller();
    $shippingMethods=$get->GetShippingMethod($_SESSION['MensioCart']['Shipping']);
    $cart=new mnsFrontEndObject();
    $cart=$cart->mnsFrontEndCart();
    foreach($cart as $cart=>$val){
        if($val['Weight']){
            $allWeight=$allWeight+$val['Weight'];
        }
    }
    foreach($shippingMethods['Data'] as $ship){
        if($ship->weight<=$allWeight || $allWeight==false){
            $ShippingWeight=$ship->uuid;
            $ShippingCost=$ship->price;
            break;
        }
    }
    $GrandTotal=$ShippingCost+$CartTotal;
    $result=array();
    $StoreOrderSerial=new mensio_seller();
    $StoreOrderSerial=$StoreOrderSerial->getOrderSerial();
    if(empty($_SESSION['mnsUser'])){
        $Customer = $_SESSION['mnsVisitor']['CustID'];
        $address=array();
        $fullname=explode(" ",filter_var($_REQUEST['FullName']));
            $firstname=$fullname[0];
            $lastname=$fullname[1];
        $phone=filter_var($_REQUEST['phone']);
        $country_post=filter_var($_REQUEST['country']);
        $region_post=filter_var($_REQUEST['region']);
        $city=filter_var($_REQUEST['city']);
        $street=filter_var($_REQUEST['street']);
        $zipcode=filter_var($_REQUEST['zipcode']);
        $email=filter_var($_REQUEST['email']);
        $Result="not-found";
        $countries=new mensio_seller();
        $countries=$countries->GetCountryCodes();
        foreach($countries as $country){
            if($country->originalName == $country_post || $country->originalID == MensioDecodeUUID($country_post)){
                $country_id=$country->uuid;
                $get_regions=new mensio_seller();
                $get_regions->Set_Country($country->originalID);
                foreach($get_regions->GetCountryRegions() as $region){
                    if($region['name']==filter_var($_REQUEST['region'])){
                        $region_id=$region['uuid'];
                    }
                }
            }
        }
        $country=$country_id;
        $arr=array(
            "user_type"=>"Guest",
            "title"=>"Mr",
            "firstname"=>$firstname,
            "lastname"=>$lastname,
            "company_name"=>false,
            "company_sector"=>false,
            "company_tin"=>false,
            "website"=>false,
            "company_email"=>false,
            "email"=>$email,
            "password"=>rand(1000,99999),
            "country"=>$country_id,
            "region"=>$region_id,
            "city"=>$city,
            "address"=>$street,
            "zip_code"=>$zipcode,
            "phone"=>$phone,
        );
        $signup_message="0";
        $signup=new mensio_seller();
        $signup->Set_NewCustomerData(json_encode($arr));
        $check=$signup->CheckNewCustomerData();
        if(!$check['Error']){
            if (!$signup->CheckIfUserNameExists()) {
              $signupData=$signup->SignUpNewCustomer();
              $signup_message="1";
              if(!$signupData['Error']){
                  $Customer=$signupData['Data']['Customer'];
                  $signup_message="1";
                    $get=new mensio_seller();
                    $data=$get->GetUserIDsByMail($email);
                    $CustID=$data->customer;
                    $Customer=$data->uuid;
                    $CredID=$data->uuid;
                        $newAddress=new mensio_seller();
                        $data=array(
                            "email"=>$email,
                            "lastname"=>$lastname,
                            "firstname"=>$firstname,
                            "country"=>$country_id,
                            "region"=>$region_id,
                            "city"=>$city,
                            "address"=>$street,
                            "zip_code"=>$zipcode,
                            "phone"=>$phone
                            );
                        if($newAddress->Set_NewCustomerData(json_encode($data))){
                            $newAddress=$newAddress->AddNewCustomerAddress(
                                    $CustID,
                                    $CredID,
                                    false);
                            $BillingAddressID=$newAddress;
                            $ShippingAddressID=$newAddress;
                        }
              }
              else{
                $signup_message="2";
              }
            }
            else{
                $signup_message="2";
                    $get=new mensio_seller();
                    $data=$get->GetUserIDsByMail($email);
                    $CustID  =$data->customer;
                    $Customer=$data->uuid;
                    $CredID=$data->uuid;
                        $newAddress=new mensio_seller();
                        $data=array(
                            "email"=>$email,
                            "lastname"=>$lastname,
                            "firstname"=>$firstname,
                            "country"=>$country_id,
                            "region"=>$region_id,
                            "city"=>$city,
                            "address"=>$street,
                            "zip_code"=>$zipcode,
                            "phone"=>$phone
                            );
                        if($newAddress->Set_NewCustomerData(json_encode($data))){
                            $newAddress=$newAddress->AddNewCustomerAddress(
                                    $CustID,
                                    $CredID,
                                    false);
                            $BillingAddressID=$newAddress;
                            $ShippingAddressID=$newAddress;
                        }
            }
        }
        else{
            $signup_message="3";
        }
    }
    else{
        $Customer = $_SESSION['mnsUser']['Credential'];
        $address  = array(stripslashes_deep(filter_var($_REQUEST['address'])));
        $Addr=new mensio_seller_gateways();
        $Addr->Set_CustomerAddress($address);
        $getCountry=new mensio_seller();
        $country=$getCountry->FindAddressCountry($_POST['address']);
        $BillingAddressID=stripslashes_deep(filter_var($_REQUEST['billingaddress']));
        $ShippingAddressID=stripslashes_deep(filter_var($_REQUEST['address']));
    }
    $Shipping= MensioDecodeUUID(filter_var($_REQUEST['ShippingCompany']));
    $BillingType= MensioDecodeUUID(filter_var($_REQUEST['payMethod']));
    $DiscountIDs='';
    $Order=array(
        "CustID"=>$Customer,
        "BillingAddressID"=>$BillingAddressID,
        "ShippingAddressID"=>$ShippingAddressID,
        "Shipping"=>$Shipping,
        "BillingType"=>$BillingType,
        "DiscountIDs"=>$DiscountIDs,
    );
    $cart=new mnsFrontEndObject();
    $cart=$cart->mnsFrontEndCart();
    $i=0;
    foreach($cart as $cart){
        $Order['Products'][$i]['ProductID']=$cart['id'];
        $Order['Products'][$i]['Amount']=$cart['Quant'];
        $Order['Products'][$i]['discount']=$cart['discount'];
        $Order['Products'][$i]['tax']=$cart['tax'];
        $Order['Products'][$i]['price']=$cart['price'];
        $Order['Products'][$i]['Price']=$cart['Price'];
        $Order['Products'][$i]['Weight']=$cart['Weight'];
        $TotalWeight=$TotalWeight+$cart['TotalWeight'];
        $UserType=false;
        $cart['FinalPrice']=$cart['price'];
        if(!empty($_SESSION['mnsUser']['Customer']) && MENSIO_FLAVOR=='STD'){
            $get=new mensioSellerExtend();
            $UserType=$get->getUserType($_SESSION['mnsUser']['Customer']);
            if(($UserType=="Company" || $UserType=="Organization") && !empty($cart['btbprice'])){
                $cart['price']=$cart['btbprice'];
                $cart['tax']=$cart['btbtax'];
                $cart['FinalPrice']=$cart['btbprice'];
            }
            $cart['price']=$cart['price']+($cart['price']*($cart['tax']/100));
            $cart['FinalPrice']=$cart['price'];
            if(!empty($cart['discount'])){
                $cart['price']=$cart['price'];
                $cart['FinalPrice']=$cart['price']-($cart['price']*($cart['discount']/100));
            }
        }
        $i++;
    }
    $newShipping=new mensio_seller();
    $newShipping=$newShipping->FindNewOrderShipping($country, $TotalWeight);
    if(!empty($_REQUEST['ShippingCompany'])){
        $newShipping=$Shipping;
    }
    $newOrder=new mensio_seller();
    $newOrder->Set_OrderCustomer($Customer);
    $newOrder->Set_Serial();
    $newOrder->Set_BlngAddress($BillingAddressID);
    $newOrder->Set_SendAddress($ShippingAddressID);
    $newOrder->Set_Shipping($newShipping);
    $newOrder=$newOrder->InsertNewOrderData();
        if(!$newOrder){
            $result= "Sorry";
        }
        else{
            $orderID=$newOrder['orderID'];
            $orderSerial=$newOrder['Serial'];
            $orderRefNum=$newOrder['refNumber'];
            if(!empty($_SESSION['MensioCouponDiscount'])){
                $AddCoupon=new mensio_seller();
                $AddCoupon=$AddCoupon->AddCouponToOrder(
                        $orderID,
                        $_SESSION['MensioCouponDiscount']['CustomerCouponID'],
                        $_SESSION['MensioCouponDiscount']['CouponID']
                        );
            }
            $TotalCost=0;
            foreach($Order['Products'] as $prod){
                $addProduct=new mensio_seller();
                $addProduct->Set_NewOrderID($orderID);
                $addProduct->Set_NewOrderProduct($prod['ProductID']);
                $addProduct->Set_NewOrderAmount($prod['Amount']);
                    $getProd=new mnsFrontEndObject();
                    $Prod=$getProd->mnsFrontEndProduct($prod['ProductID']);
                $addProduct->Set_NewOrderPrice($prod['price']);
                $addProduct->Set_NewOrderTax($prod['tax']);
                $addProduct->Set_NewOrderDiscount($prod['discount']);
                $addProduct->AddOrderProduct( ($prod['Price']*$prod['Amount']) );
                $TotalCost=$TotalCost+$prod['final_price'];
            }
            $paymentMethods=new mensio_seller_gateways();
            $paymentMethods=$paymentMethods->GetActivePaymentMethods();
            foreach($paymentMethods['Data'] as $method){
                if(!empty($BillingType) && ($BillingType==$method['Payment']) && ($method['Type']=="PayPal")){
                    $orderPayment=new mensio_seller();
                    $orderPayment->Set_NewOrderID($orderID);
                    $orderPayment=$orderPayment->AddPaymentMethodToOrder($method['Payment'],'StartingConnection');
                    $orderCompl=new mensio_seller();
                    $orderCompl->Set_NewOrderID($orderID);
                    $orderCompl=$orderCompl->GiverOrderStatus("Submitted");
                    $orderCompl=new mensio_seller();
                    $orderCompl->Set_NewOrderID($orderID);
                    $orderCompl=$orderCompl->GiverOrderStatus("Pending");
                    $_SESSION['mnsPayPalOrder']=$orderID;
                    $_SESSION['mnsPayPalSerial']=$orderRefNum;
                    echo md5($orderRefNum."/".$orderID);
                    die;
                }
                elseif($method['Payment']==$BillingType  && ($method['Type']=="On Delivery" || $method['Type']=="Bank Deposit")){
                    $orderCompl=new mensio_seller();
                    $orderCompl->Set_NewOrderID($orderID);
                    $orderCompl=$orderCompl->GiverOrderStatus("Submitted");
                    $orderCompl=new mensio_seller();
                    $orderCompl->Set_NewOrderID($orderID);
                    $orderCompl=$orderCompl->GiverOrderStatus("Pending");
                    $orderPayment=new mensio_seller();
                    $orderPayment->Set_NewOrderID($orderID);
                    $orderPayment=$orderPayment->AddPaymentMethodToOrder($method['Payment'],'Pending');
                    SendOrderToCustomer($orderID);
                    $result=array(
                        "orderNo"=>$orderID
                        );
                    unset($_SESSION['MensioCart']);
                }
                elseif($method['Payment']==$BillingType && $method['Type']!="on Delivery" && $method['Type']!="Bank Deposit"){
                    $paymethod=new mensio_seller_gateways();
                    $payment=$paymethod->GetPaymentMethodData($method['Payment'],"Gateway");
                    foreach($payment['Data']['Options'] as $opt){
                        $optName=str_replace(" ","",preg_replace('/[0-9]+/', '', $opt->parameter));
                        if($optName=="Actionurl"){$ActionUrl=$opt->value;continue;}
                        if($optName=="MerchantID"){$mid=$opt->value;}
                        if($optName=="DigestKey"){$digest=$opt->value;}
                        if($optName=="RejectdsU"){$reject3dsU=$opt->value;}
                        if($optName=="ReturnSuccessPage"){
                            $confirmUrl= get_site_url()."/";
                            $get=new mnsGetFrontEndLink();
                            if (! get_option('permalink_structure') ){
                                $confirmUrl=$get->CheckoutPage()."&result=success";
                            }
                            else{
                                $confirmUrl=$get->CheckoutPage()."?result=success";
                            }
                        }
                        if($optName=="ReturnFailedPage"){
                            $cancelUrl=get_site_url()."/".$opt->value;
                            $cancelUrl= get_site_url()."/";
                            $get=new mnsGetFrontEndLink();
                            if (! get_option('permalink_structure') ){
                                $cancelUrl=$get->CheckoutPage()."&result=fail";
                            }
                            else{
                                $cancelUrl=$get->CheckoutPage()."?result=fail";
                            }
                        }
                        $i++;
                    }
                    $orderDesc="";
                    $currency="EUR";
                    $payerEmail="";
                    $payerPhone="6900000000";
                    $cart=new mnsFrontEndObject();
                    $cart=$cart->mnsFrontEndCart();
                    $orderAmount=0;
                    foreach($cart as $product){
                        $get=new mnsFrontEndObject();
                        $prod=$get->mnsFrontEndProduct($product['id']);
                        $UserType=false;
                        $prod['FinalPrice']=$prod['price'];
                        if(!empty($_SESSION['mnsUser']['Customer'])){
                            $get=new mensioSellerExtend();
                            $UserType=$get->getUserType($_SESSION['mnsUser']['Customer']);
                            $FinalPrice=$prod['final_btbprice'];
                        }
                        else{
                            $FinalPrice=$prod['final_price'];
                        }
                        $Price=($FinalPrice*$product['Quant']);
                        $orderAmount=$orderAmount+$Price;
                    }
                    $get=new mensio_seller();
                    $shipping=$get->GetShippingData($newShipping);
                    $shippingCost=$shipping['Data'][0]->price;
                            if(!empty($_SESSION['MensioCart']['Shipping'])){
                                $seller=new mensio_seller();
                                $shipping=$seller->GetShippingData($_SESSION['MensioCart']['Shipping']);
                                $shippingCost=number_format($shipping['Data'][0]->price,2);
                            }
                            $cart=new MensioFlavored();
                            $cart=$cart->mnsFrontEndCart();
                            $TotalCost=0;
                            foreach($cart as $crt){
                                $TotalCost=$TotalCost+$crt['Cost'];
                            }
                            $TotalCost=number_format($TotalCost,2);
                            if(!empty($shippingCost)){
                                $TotalCost=$TotalCost+$shippingCost;
                            }
                    $orderAmount=$TotalCost;
                    if(!empty($_SESSION['MensioCouponDiscount']['DiscountPercent'])){
                        $orderAmount=$orderAmount-($orderAmount*$_SESSION['MensioCouponDiscount']['DiscountPercent']/100);
                    }
                    $form_data_array=array();
                    $form_mid = $mid;                                   $form_data_array[0] = $mid;				//Required Parameter
                    $form_order_id = MensioEncodeUUID($orderID);	$form_data_array[1] = $form_order_id;				//Required Parameter
                    $form_order_desc = $orderDesc;			$form_data_array[2] = $orderDesc;			//Optional Parameter
                    $form_order_amount = $orderAmount;                  $form_data_array[3] = $orderAmount;			//Required Parameter
                    $form_currency = $currency;                         $form_data_array[4] = $currency;			//Required Parameter
                    $form_email = $payerEmail;                          $form_data_array[5] = $payerEmail;                      //Required Parameter
                    $form_reject3dsU = $reject3dsU;			$form_data_array[6] = $reject3dsU;			//Optional Parameter
                    $form_confirm_url = $confirmUrl;                    $form_data_array[7] = $confirmUrl;			//Required Parameter
                    $form_cancel_url = $cancelUrl;			$form_data_array[8] = $cancelUrl;			//Required Parameter
                    $form_secret = $digest;				$form_data_array[9] = $digest;				//Required Parameter
                    $form_data = implode("", $form_data_array);
                    $Digest = base64_encode(sha1($form_data,true));
                    $result=array(
                        "orderNo"=>MensioEncodeUUID($orderID),
                        "Digest"=> stripslashes($Digest),
                        "GrandTotal"=>$orderAmount,
                        "Implode"=>$form_data
                        );
                    $orderCompl=new mensio_seller();
                    $orderCompl->Set_NewOrderID($orderID);
                    $orderCompl=$orderCompl->GiverOrderStatus("Submitted");
                    $orderCompl=new mensio_seller();
                    $orderCompl->Set_NewOrderID($orderID);
                    $orderCompl=$orderCompl->GiverOrderStatus("Pending");
                    $orderPayment=new mensio_seller();
                    $orderPayment->Set_NewOrderID($orderID);
                    $orderPayment=$orderPayment->AddPaymentMethodToOrder($method['Payment'],'StartingConnection');
                    break;
                }
            }
        }
        if(!empty($_SESSION['mnsUser']['UserName'])){
            $CustomerEmail=$_SESSION['mnsUser']['UserName'];
        }
        else{
            $CustomerEmail=stripslashes_deep(filter_var($_REQUEST['email']));
        }
    $Data=array(
        "RefNum"=>$orderRefNum,
        "Order"=>$orderSerial,
        "Date"=>date("Y-m-d H:i:s"),
        "Customer"=>$CustomerEmail
    );
    $notify=new mensio_seller();
    $notify=$notify->AddNewOrderNotification($Data);
    echo json_encode($result);
    die;
}
}
add_action('wp_ajax_mensiopress_CountryShippingMethods','mensiopress_CountryShippingMethods' );
add_action('wp_ajax_nopriv_mensiopress_CountryShippingMethods','mensiopress_CountryShippingMethods' );
function mensiopress_CountryShippingMethods(){
    if(!empty($_POST)){
        $url     = wp_get_referer();
        $url     = str_replace($_POST['mns_lang']."/","",$url);
        $ref     = url_to_postid( $url ); 
        $seller=new mensio_seller();
        $verification=$seller->VerifyPageIntegrity(filter_var($_POST['mns_sec']), "MensioPressFrontEnd-".$ref);
        if($verification==false){
            echo "Unauthorized";
            die;
        }
    }
    $shippingMethods=false;
    $countryID=false;
    $country_post=filter_var($_REQUEST['mns_country']);
    $countries=new mensio_seller();
    $countries=$countries->GetCountryCodes();
    foreach($countries as $country){
        if($country->originalName == $country_post){
            $countryID=$country->originalID;
        }
    }
    $cart=new mnsFrontEndObject();
    $cart=$cart->mnsFrontEndCart();
    $TotalWeight=0;
    foreach($cart as $crt){
        $TotalWeight=$TotalWeight+$crt['TotalWeight'];
    }
    $getMethods=new mensio_seller();
    $getMethods->Set_Country($countryID);
    $getMethods->Set_TtlWeight($TotalWeight);
    $methods=$getMethods->GetCountryShippingMethods($TotalWeight);
    $i=0;
    $ShipMethod = '';
    if(!empty($methods['Data'])){
        foreach($methods['Data'] as $method){
            if ($method->active) {
              if ($ShipMethod !== $method->name) {
                $shippingMethods[$i]['active']=$method->active;
                $shippingMethods[$i]['name']=$method->name;
                $shippingMethods[$i]['speed']=$method->delivery_speed;
                $shippingMethods[$i]['billType']=$method->billing_type;
                $shippingMethods[$i]['price']=$method->price+0;
                $shippingMethods[$i]['ID']= MensioEncodeUUID($method->shipping);
                $ShipMethod = $method->name;
              }
            }
            $i++;
        }
        if(is_array($shippingMethods)){
            $shippingMethods=json_encode($shippingMethods);
        }
    }
    echo $shippingMethods;
    die;
}
if(!function_exists("SendOrderToCustomer")){
    function SendOrderToCustomer($orderID){
    $get=new mensio_seller();
    $html='';
    $orderData=$get->getOrderData($orderID);
    $orderSerial=$orderData[0]->serial;
    $Store=new mnsFrontEndObject();
    $Store=$Store->mnsFrontEndStoreData();
    $from=$Store['email'];
    $logo=get_site_url()."/".$Store['logo'];
    $Subject= get_bloginfo( false, 'display' )." ".$orderSerial;
    $Total=0;
    $OrderProducts=$get->getOrdersProducts($orderID);
    $PaymentData=$get->getOrderPaymentData($orderID);
    $paymentMethod=$PaymentData[0]->payment;
    $paymentMethods=new mensio_seller_gateways();
    $paymentMethods=$paymentMethods->GetActivePaymentMethods();
    $paymentHTML=false;
    foreach($paymentMethods['Data'] as $method){
        if($method['Payment']==$paymentMethod){
            $paymentHTML.=$method['Description']."<br />";
            $paymentHTML.="<i>".$method['Instructions']."</i><br />";
            if($method['Type']=="Bank Deposit"){
                $PayMethod = new mensio_payment_methods();
                if ($PayMethod->Set_UUID($method['Payment'])) {
                $Data = $PayMethod->LoadBankDepositData(false);
                    if ((is_array($Data)) && (!empty($Data[0]))) {
                        foreach ($Data as $Row) {
                          $RtrnData['Description'] = $Row->description;
                          $RtrnData['Instructions'] = $Row->instructions;
                        }
                    }
                    $RtrnData['Options'] = $PayMethod->LoadBankAccountList();
                    foreach($RtrnData['Options'] as $ac){
                        $paymentHTML.="<br /><img src='".site_url()."/".$ac->account_icon."' style='display: inline;max-width: 75px;'><br />".
                                $ac->account_bank."<hr />"
                                . "<strong>Account Number:</strong> ".$ac->account_number."<br />"
                                . "<strong>Account Name:</strong>".$ac->account_name."<br />"
                                . "<strong>IBAN:</strong> ".$ac->account_iban."<br />"
                                . "<strong>Routing:</strong> ".$ac->account_routing."<br />"
                                . "<strong>SWIFT:</strong> ".$ac->account_swift."<br />";
                    }
                }
            }
        }
    }
    $OrderData=$get->getOrdersData($orderID);
    $CustomerID=$OrderData[0]->customer;
    $usr=new mensio_customers();
    $usr->Set_UUID($CustomerID);
    $user=$usr->LoadCustomerData();
    $user=$user[0];
    if(!empty($user->username)){
        $to=$user->username;
    }
    $address=new mensio_customers();
    $address->Set_UUID($CustomerID);
    $addresses=$address->LoadCustomerAddress();
    $contact=new mensio_customers();
    $contact->Set_UUID($CustomerID);
    $contacts=$contact->LoadCustomerContact();
    foreach($addresses as $addr){
        if($addr->uuid==$orderData[0]->sendingaddr){
            $address=$addr;
            break;
        }
    }
    $countries=$get->GetCountryCodes();
    foreach($countries as $count){
        if($address->country==$count->uuid){
            $countryName=$count->name;
            break;
        }
    }
    $logo='<img src="'.$logo.'" class="StoreLogo">';
    $OrderList=false;
    $OrderList.='<table cellpadding="20">';
    foreach($OrderProducts as $product){
        $getProd=new mnsFrontEndObject();
        $prod=$getProd->mnsFrontEndProduct($product->product);
        if(!empty($product->master_product)){
            $getProdLink=new mnsGetFrontEndLink();
            $prodLink=$getProdLink->ProductLink($product->master_product);
        }
        else{
            $getProdLink=new mnsGetFrontEndLink();
            $prodLink=$getProdLink->ProductLink($product->product);
        }
        $image=$prod['images'][0]['image'];
        $ProdName=$prod['name'];
        $finalPrice=$product->price+($product->price*($product->taxes/100));
        $OrderList.= "<tr>";
        $OrderList.= "<td><a href='".$prodLink."'><img src='".$image."' class='ProductImage' style='max-width:100px;max-height:100px;'></a></td>";
        $OrderList.= "<td><a href='".$prodLink."' class='ProductName'>".$ProdName."</a></td>";
        $OrderList.= "<td><span class='mensioPrice ProductPrice'>".number_format($finalPrice,2)."</span></td>";
        if(!empty($product->discount) && $product->discount>0){
            $OrderList.= "<td class='ProductDiscount'>";
            $OrderList.= "-".number_format($product->discount,0)."%";
            $OrderList.= "</td>";
            $OrderList.= "<td class='ProductQuantity'>x".number_format($product->amount,0)."</td>";
            $OrderList.= "<td><span class='mensioPrice class='ProductTotal''>".number_format(($finalPrice-($finalPrice*($product->discount/100)))*$product->amount,2)."</span></td>";
            $Total=$Total+(($finalPrice-($finalPrice*($product->discount/100)))*$product->amount);
        }
        else{
            $OrderList.= "<td></td>";
            $OrderList.= "<td class='ProductQuantity'>x".number_format($product->amount,0)."</td>";
            $OrderList.= "<td><span class='mensioPrice ProductPrice'>".number_format($finalPrice*$product->amount,2)."</span>"."</span></td>";
            $Total=$Total+($finalPrice*$product->amount);
        }
        $OrderList.= "</tr>";
    }
    $Discount=false;
    $get=new mensio_seller();
    $shippingData=$get->GetShippingData($orderData[0]->shipping);
    $shippingCompanyData=$get->GetShippingData($orderData[0]->shipping);
    $Address=$get->GetAddress($orderData[0]->sendingaddr);
    $BillingAddress=$get->GetAddress($orderData[0]->billingaddr);
    $ShippingCompany=false;
    $get->Set_Country($Address['country']);
    $ShippingCompanies=$get->GetCountryShippingMethods();
    foreach($ShippingCompanies['Data'] as $method){
        if($method->shipping==$orderData[0]->shipping){
            $ShippingCompany=$method->name;
        }
    }
    $shippingCost=$shippingData['Data'][0]->price;
    $Total=$Total+$shippingCost;
    $OrderList.='<tr>
                <td></td>
                <td>
                    Shipping Cost<br />
                    '.$ShippingCompany.'
                </td>
                <td colspan="3"></td>
                <td class="mensioPrice">'.number_format($shippingCost,2).'</td>
            </tr>';
    $OrderList.='<tr>
                <td colspan="6"><hr /></td>
            </tr>
                <td colspan="5" align="right">'.$Discount.'</td>
                <td><span class="mensioPrice OrderTotal">'.number_format($Total,2).'</span></td>
            </tr>
            <tr>
                <td colspan="6">
                    <strong>Shipping Address:</strong>
                    <table>
                        <tr>
                            <td>FullName:</td>
                            <td>'.$Address['fullname'].'</td>
                        </tr>
                        <tr>
                            <td>Country:</td>
                            <td>'.$Address['countryText'].'</td>
                        </tr>
                        <tr>
                            <td>City:</td>
                            <td>'.$Address['city'].'</td>
                        </tr>
                        <tr>
                            <td>Address:</td>
                            <td>'.$Address['street'].'</td>
                        </tr>
                    </table>
                    <strong>Billing Address:</strong>
                    <table>
                        <tr>
                            <td>FullName:</td>
                            <td>'.$BillingAddress['fullname'].'</td>
                        </tr>
                        <tr>
                            <td>Country:</td>
                            <td>'.$BillingAddress['countryText'].'</td>
                        </tr>
                        <tr>
                            <td>City:</td>
                            <td>'.$BillingAddress['city'].'</td>
                        </tr>
                        <tr>
                            <td>Address:</td>
                            <td>'.$BillingAddress['street'].'</td>
                        </tr>
                    </table>
                </td>
            </tr>
            </table>';
    if(!empty($paymentHTML)){
        $OrderList.=$paymentHTML;
    }
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: '.get_bloginfo().' <'.$from.'>' . "\r\n";
        $ar=array(
            "STORELOGO"=>$logo,
            "STORENAME"=>$Store['name'],
            "STOREMAIL"=>$Store['email'],
            "ORDERNUMBER"=>$orderSerial,
            "ORDERLIST"=>$OrderList
            );
        $seller=new mensio_seller();
        if($seller->getMailTemplate("Sales",$ar)){
            $message= stripslashes_deep($seller->getMailTemplate("Sales",$ar));
        }
        wp_mail($to,$Subject,$message,$headers);
    }
}
add_action('wp_ajax_mensiopress_NewPayPalPayment','mensiopress_NewPayPalPayment' );
add_action('wp_ajax_nopriv_mensiopress_NewPayPalPayment','mensiopress_NewPayPalPayment' );
function mensiopress_NewPayPalPayment(){
    if(!empty($_POST['mnsPayPal'])){
        $PayPal= $_POST['mnsPayPal'];
        $Status=$PayPal['payer']['status'];
        if($Status=="VERIFIED"){
            $orderCompl=new mensio_seller();
            $orderCompl->Set_NewOrderID(MensioDecodeUUID($_POST['mnsPayPalOrder']));
            $orderCompl=$orderCompl->GiverOrderStatus("Complete");
            echo "VERIFIED";
        }
        else{
            $orderPayment=new mensio_seller();
            $orderPayment->Set_NewOrderID(MensioDecodeUUID($_POST['mnsPayPalOrder']));
            $orderPayment=$orderPayment->UpdatePaymentMethod("Failed");
            $orderCompl=new mensio_seller();
            $orderCompl->Set_NewOrderID(MensioDecodeUUID($_POST['mnsPayPalOrder']));
            $orderCompl=$orderCompl->GiverOrderStatus("Failed");
            echo "FAILED";
        }
    }
    die;
}
