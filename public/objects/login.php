<?php
add_shortcode( 'mns_login', 'mensiopress_login_form' );
function mensiopress_login_form($atts){
    $html=false;
    if(empty($atts)){
        $atts=array();
    }
        if(empty($atts['titlesize'])){
            $atts['titlesize']=$GLOBALS['MensioPressFontSize'];
        }
        if(empty($atts['fontsize'])){
            $atts['fontsize']=$GLOBALS['MensioPressFontSize'];
        }
        if(empty($atts['position'])){
            $atts['position']="page";
        }
        if(!empty($atts['title'])){
            $title=$atts['title'];
        }
        else{
            $title="Login";
        }
    $Text= get_option("MensioPress_TextWrongCreds_".$_SESSION['MensioThemeLangShortcode']);
    if(empty($Text)){
        $Text="Wrong Credentials";
    }
    $html.="<div style='font-size:".$atts['fontsize']."rem;'>";
    $html.="<div class='login-form login-error' style='display:none;'>
                <div>".$Text."</div>";
    $html.="</div>";
    if(!empty($_GET['forgot-password']) && $atts['position']=="page"){
        $getForgotOption= get_option("MensioPress-ForgotPassword-".urlencode($_GET['forgot-password']));
        $check=new mensio_seller();
        if(!empty($getForgotOption) && $check->GetUserIDByUsername(stripslashes_deep(filter_var($_REQUEST['forgot-password'])))){
            $getForgotOption=json_decode($getForgotOption,true);
            $data=$check->GetUserIDByUsername(stripslashes_deep(filter_var($_REQUEST['forgot-password'])));
            if($data->uuid==$getForgotOption['UUID'] &&
                    time()<=$getForgotOption['Until']){
                if(
                        !empty($_POST['MensioPressForgotPasswordPass']) &&
                        password_verify($_POST['MensioPressForgotPasswordPass'],$getForgotOption['Pass']) == true &&
                        !empty($_POST['MensioPressForgotPasswordCode']) &&
                        $_POST['MensioPressForgotPasswordCode']==$getForgotOption['Code']
                        ){
                    $html.="Password Changed";
                    $upd=new mensio_customers();
                    $upd->Set_UUID($data->uuid);
                    $upd->Set_Password(filter_var($_POST['MensioPressForgotPasswordPass']));
                    $newPasswordPassHashed=$upd->Return_Set_Password();
                    $upd=$upd->UpdateCustomerRecord();
                    delete_option("MensioPress-ForgotPassword-".urlencode($_GET['forgot-password']));
                }
                else{
                $html.="<form method='post'>
                        Email: <input type='text' name='MensioPressForgotPasswordEmail' value='".urldecode(filter_var($_GET['forgot-password']))."'><br />
                        Password: <input type='password' name='MensioPressForgotPasswordPass'><br />
                        Code: <input type='password' name='MensioPressForgotPasswordCode'><br />
                        <input type='submit' value='Reset Password'>
                    </form>";
                }
                return $html;
            }
        }
    }
    if(!empty($_SESSION['mnsUser']) && $atts['position']=='page'){
        $html.="<div class='login-form'>
                    <div class='mns-already-logged-in'>Welcome ".$_SESSION['mnsUser']['FirstName'];
        $html.="</div>";
        $html.="</div>";
    }
    elseif(!empty($_SESSION['mnsUser']) && $atts['position']=='widget'){
        $getLink=new mnsGetFrontEndLink();
        $UserLink=$getLink->UserPage();
        $FavoritesLink=$getLink->FavoritesPage();
        if(empty($LogoutMessage= get_option("MensioPress_TextLogoutMessage_".$_SESSION['MensioThemeLangShortcode']))){
            $LogoutMessage="Are you sure you want to exit?";
        }
        $html.="<div class='login-form'>
                  <div class='mns-already-logged-in'>
                    <div class='mensioPressWidgetTitle'>Welcome, ".$_SESSION['mnsUser']['FirstName']."</div>";
                    $html.="<ul class='user-menu'>";
                    if ( get_option('permalink_structure') ) {
                        $html.="<li><a href='".$UserLink."?subpage=general'>General</a></li>";
                        $html.="<li><a href='".$UserLink."?subpage=addresses'>Addresses</a></li>";
                        $html.="<li><a href='".$UserLink."?subpage=contacts'>Contacts</a></li>";
                        $html.="<li><a href='".$UserLink."?subpage=history'>Orders</a></li>";
                        $html.="<li><a href='".$UserLink."?subpage=tickets'>Support</a></li>";
                        if(MENSIO_FLAVOR=='STD'){
                            $html.="<li><a href='".$FavoritesLink."'>Wishlist</a></li>";
                        }
                    }
                    else{
                        $html.="<li><a href='".$UserLink."&subpage=general'>General</a></li>";
                        $html.="<li><a href='".$UserLink."&subpage=addresses'>Addresses</a></li>";
                        $html.="<li><a href='".$UserLink."&subpage=contacts'>Contacts</a></li>";
                        $html.="<li><a href='".$UserLink."&subpage=history'>Orders</a></li>";
                        $html.="<li><a href='".$UserLink."&subpage=tickets'>Support</a></li>";
                        if(MENSIO_FLAVOR=='STD'){
                            $html.="<li><a href='".$FavoritesLink."'>Wishlist</a></li>";
                        }
                    }
                        $html.="<li><a href='#' class='MensioPressLogout' data-message='".$LogoutMessage."'>Logout</a></li>";
                    $html.="</ul>";
            $html.="</div>";
        $html.="</div>";
    }
    elseif(empty($_SESSION['mnsUser'])){
        if(!empty($atts['titlesize'])){
            $fontSize=str_replace("-",".",$atts['titlesize']);
        }
        else{
            $fontSize="1";
        }
        $html.="<div class='newTOS'><input type='button' value='Agree' class='agree-to-new-terms'><input type='button' value='I disagree' onClick='?page=logout' class='disagree-to-new-terms'></div>";
        if(empty($atts['position']) || (!empty($atts['position']) && $atts['position']!="widget")){
            $html.="<h2 style='font-size:".$fontSize."rem;' class='mensioObjectTitle'>".$title."</h2><hr class='titleLine' />";
        }
        if(!empty($atts['position']) && $atts['position']=="widget"){
            $html.="<div class='mensioPressWidgetTitle'>Welcome </div>";
        }
                        if(get_option('MensioPress_TextUsername_'.$_SESSION['MensioThemeLangShortcode'])){
                            $UsernameKey=ucfirst(get_option('MensioPress_TextUsername_'.$_SESSION['MensioThemeLangShortcode']));
                        }
                        else{
                            $UsernameKey="Username";
                        }
        $html.="
            <div class='login-form'>
                <div class='form-input'>
                    <input type='text' value='' name='username' placeholder='".$UsernameKey."'>
                </div>";
                        if(get_option('MensioPress_TextPassword_'.$_SESSION['MensioThemeLangShortcode'])){
                            $PasswordKey=ucfirst(get_option('MensioPress_TextPassword_'.$_SESSION['MensioThemeLangShortcode']));
                        }
                        else{
                            $PasswordKey="Password";
                        }
                $html.="
                <div class='form-input'>
                    <input type='password' name='password' placeholder='".$PasswordKey."'>
                </div>";
                        if(get_option('MensioPress_TextLogin_'.$_SESSION['MensioThemeLangShortcode'])){
                            $LoginSubmitKey=ucfirst(get_option('MensioPress_TextLogin_'.$_SESSION['MensioThemeLangShortcode']));
                        }
                        else{
                            $LoginSubmitKey="Login";
                        }
                    $ForgotPasswordText=get_option("MensioPress_TextForgotPassword_".$_SESSION['MensioThemeLangShortcode']);
                    if(empty($ForgotPasswordText)){
                        $ForgotPasswordText="Forgot Password";
                    }
                    $ForgotPasswordTextSuccess=get_option("MensioPress_TextForgotPasswordSuccess_".$_SESSION['MensioThemeLangShortcode']);
                    if(empty($ForgotPasswordTextSuccess)){
                        $ForgotPasswordTextSuccess="Your password has been reset";
                    }
                    $ForgotPasswordTextFail=get_option("MensioPress_TextForgotPasswordFail_".$_SESSION['MensioThemeLangShortcode']);
                    if(empty($ForgotPasswordTextFail)){
                        $ForgotPasswordTextFail="Username not valid";
                    }
                $html.="
                <div class='form-input submitButton'>
                    <input type='button' value='".$LoginSubmitKey."' class='submit-button'>
                </div>
            </div>
            <div class='forgot-pass-form' style='display:none;width:70%;margin:0 auto;'>
                <div class='forgot-pass-success'
                    style='display:none;width:100%;background:green;color:#fff;margin:0 auto;text-align:center;'>
                        ".$ForgotPasswordTextSuccess."
                </div>
                <div class='forgot-pass-fail'
                    style='display:none;width:100%;background:red;color:#fff;margin:0 auto;text-align:center;'>
                        ".$ForgotPasswordTextFail."
                </div>";
                if(get_option('MensioPress_TextSend_'.$_SESSION['MensioThemeLangShortcode'])){
                    $key=ucfirst(get_option('MensioPress_TextSend_'.$_SESSION['MensioThemeLangShortcode']));
                }
                else{
                    $key="Send";
                }
                $html.="
                <input type='text' placeholder='".$UsernameKey."' name='username-forgot'><br />
                <input type='button' value='".$key."' class='submit-button'>
            </div>
            <div class='login-result'></div>";
            if((!empty(get_option("MensioPressFBAppID")) && empty($atts['hide-fb']) )
                    || (!empty(get_option("MensioPressFBAppID")) && !empty($atts['hide-fb']) && $atts['hide-fb']=="no")){
                $getSignupPage= new mnsGetFrontEndLink();
                $signupPage=$getSignupPage->SignupPage();
                $html.='
                    <div id="fb-root"></div>';
                $MensioPressScript=false;
                wp_enqueue_script("MensioPress-FBLoginScript", "//connect.facebook.net/en_US/all.js");
                $MensioPressScript.='
                        window.fbAsyncInit = function() {
                            FB.init({
                                appId      : "'. get_option("MensioPressFBAppID").'",
                                channelUrl : "http://'.$_SERVER['HTTP_HOST'].'",
                                status     : false,
                                cookie     : true,
                                xfbml      : true
                            });
                            FB.Event.subscribe("auth.authResponseChange", function(response) {
                                if (response.status === "connected") {
                                    testAPI();
                                } else if (response.status === "not_authorized") {
                                    FB.login();
                                } else {
                                    FB.login();
                                }
                            });
                        };
                        (function(d){
                            var js, id = "facebook-jssdk", ref = d.getElementsByTagName("script")[0];
                            if (d.getElementById(id)) {return;}
                            js = d.createElement("script"); js.id = id; js.async = true;
                            js.src = "//connect.facebook.net/en_US/all.js";
                            ref.parentNode.insertBefore(js, ref);
                        }(document));
                        function testAPI(status=false) {
                            if(status=="loading"){
                                FB.api("/me?fields=name", function(response) {
                                });
                                return false;
                            }
                            FB.api("/me", function(response) {
                            ';
                            if(empty($_SESSION['mnsUser'])){
                                $MensioPressScript.='
                                    jQuery("input[name=mns-fbid]").val(  );
                                    jQuery.ajax({
                                         type: "post",
                                         url: ajaxurl,
                                         data: {
                                            "action": "mns_fblogin",
                                            "mns_fbid": response.id
                                         },
                                         success:function(data) {
                                            if(data){
                                                window.location.href="";
                                            }
                                            else{
                                                window.location.href="?page_id='.$signupPage.'&action=fb";
                                            }
                                         }
                                });
                                ';
                            }
                                $MensioPressScript.='
                            });
                        }
                        ';
                wp_add_inline_script("MensioPress-FBLoginScript", $MensioPressScript);
                $fbSize="large";
                    if($atts['position']=='widget'){
                        $fbSize="small";
                    }
                    else{
                        $fbSize="large";
                    }
                    $html.="
                    <!--
                      Below we include the Login Button social plugin. This button uses
                      the JavaScript SDK to present a graphical Login button that triggers
                      the FB.login() function when clicked.
                    -->
                    <div style='text-align:center;margin:0 auto;width:auto;'>";
                    if($atts['position']=='widget'){
                        $html.="
                         <a href='#' class='continue-with-fb' onclick='FB.login();return false;'>
                            <button><i class='fa fa-facebook'></i> Continue with Facebook</button>
                         </a>";
                    }
                    else{
                        $html.="
                        <div class='fb-login-button'
                            data-max-rows='1'
                            data-size='".$fbSize."'
                            data-button-type='continue_with'
                            data-show-faces='false'
                            data-auto-logout-link='false'
                            data-use-continue-as='true'
                                onlogin='checkLoginState();'>
                        </div>";
                    }
                    $html.="
                    </div>";
            }
                $html.="
                <div class='form-input'>";
                    $html.="<a href='#' class='forgot-pass'>".$ForgotPasswordText."</a>";
                    if(!empty($atts['position']) && $atts['position']=="widget"){
                        $getLink=new mnsGetFrontEndLink();
                        $SignupText=get_option("MensioPress_TextSignup_".$_SESSION['MensioThemeLangShortcode']);
                        if(empty($SignupText)){
                            $SignupText="Signup";
                        }
                        $eshopSlug=get_option("MensioPageSlug");
                        if(!$eshopSlug){
                            $eshopSlug="action";
                        }
                        $html.="<a href='".$getLink->SignupPageLink()."'>".$SignupText."</a>";
                    }
                $html.="
                </div>";
    }
    $html.="</div>";
    return $html;
}
add_action('wp_ajax_mensiopress_login_post','mensiopress_login_post');
add_action('wp_ajax_nopriv_mensiopress_login_post','mensiopress_login_post');
function mensiopress_login_post(){
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
    $login=new mensio_seller();
    $login->Set_UserName(stripslashes_deep(filter_var($_REQUEST['mns_username'])));
    $login->Set_Password(stripslashes_deep(filter_var($_REQUEST['mns_password'])));
    $login->Set_IPAddress($_SERVER['REMOTE_ADDR']);
    $login=$login->CheckLoginCredentials();
    if($login['Error']==true){
        return false;
    }
    else{
        if(isset($_SESSION['mnsLastSeenProduct'])){
            $link_to_prod=new mnsGetFrontEndLink();
            $link_to_prod=$link_to_prod->ProductLink($_SESSION['mnsLastSeenProduct']);
        }
        $_SESSION['mnsUser']=$login['Data'];
        echo json_encode(
                array(
                    "welcome"=>$login['Data']['Title']." ".$login['Data']['FirstName']." ".$login['Data']['Lastname'],
                    "last_seen_product"=>$link_to_prod,
                    "TOS"=>$login['Data']['TermsCheck']
                )
            );
        if(!empty($login['Data']['TermsCheck'])){
            $_SESSION['NewTerms']=$login['Data']['TermsCheck'];
        }
    }
    die;
}
add_action('wp_ajax_mensiopress_logout','mensiopress_logout');
add_action('wp_ajax_nopriv_mensiopress_logout','mensiopress_logout');
function mensiopress_logout(){
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
    unset($_SESSION['mnsUser']);
    unset($_SESSION['NewTerms']);
    die;
}
add_action('wp_ajax_mensiopress_AgreeWithNewTOS','mensiopress_AgreeWithNewTOS');
add_action('wp_ajax_nopriv_mensiopress_AgreeWithNewTOS','mensiopress_AgreeWithNewTOS');
function mensiopress_AgreeWithNewTOS(){
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
    $seller=new mensio_seller();
    $seller->UpdateUserCredentials($_SESSION['mnsUser']['Credential'],"termsnotice",date("Y-m-d H:i:s"));
    unset($_SESSION['NewTerms']);
    die;
}
add_action('wp_ajax_mensiopress_fblogin','mensiopress_fblogin');
add_action('wp_ajax_nopriv_mensiopress_fblogin','mensiopress_fblogin');
function mensiopress_FBlogin(){
    $Res=false;
    if(!empty($_REQUEST['mns_fbid'])){
        $Res=get_option("MNSFBUser-".filter_var($_REQUEST['mns_fbid']));
        if(!empty($Res)){
            $seller=new mensio_seller();
            $Row=$seller->GetUserIDByUsername($Res);
            $RtrnData=array();
            $RtrnData['Error'] = false;
            $Data=array();
            $Data['Customer'] = $Row->customer;
            $Data['Credential'] = $Row->uuid;
            $Data['UserName'] = $Row->username;
            $Data['Title'] = $Row->title;
            $Data['FirstName'] = $Row->firstname;
            $Data['LastName'] = $Row->lastname;
            $RtrnData['Data'] = $Data;
            $_SESSION['mnsUser']=$RtrnData['Data'];
            $Res="ok";
        }
    }
    echo $Res;
    die;
}
add_action('wp_ajax_mensiopress_ForgotPass','mensiopress_ForgotPass');
add_action('wp_ajax_nopriv_mensiopress_ForgotPass','mensiopress_ForgotPass');
function mensiopress_ForgotPass(){
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
    $check=new mensio_seller();
    if(filter_var($_REQUEST['mns_username'])){
        $getLoginLink=new mnsGetFrontEndLink();
        $ForgotPasswordLink=$getLoginLink->LoginLink()."&forgot-password=". urlencode(filter_var($_REQUEST['mns_username']));
        $Result=$ForgotPasswordLink;
        $newPasswordPass    =wp_generate_password("8",false,false);
        $newPasswordCode    =wp_generate_password("8",false,false);
        if($check->GetUserIDByUsername(stripslashes_deep(filter_var($_REQUEST['mns_username'])))){
            $data=$check->GetUserIDByUsername(stripslashes_deep(filter_var($_REQUEST['mns_username'])));
            $upd=new mensio_customers();
            $upd->Set_UUID($data->uuid);
            $upd->Set_Password($newPasswordPass);
            $newPasswordPassHashed=$upd->Return_Set_Password();
            $upd=$upd->UpdateCustomerRecord();
            delete_option("MensioPress-ForgotPassword-".urlencode(filter_var($_REQUEST['mns_username'])));
            add_option("MensioPress-ForgotPassword-".urlencode(filter_var($_REQUEST['mns_username'])),
                        json_encode(
                            array(
                                "UUID"=>$data->uuid,
                                "Pass"=> $newPasswordPassHashed,
                                "Code"=>$newPasswordCode,
                                "Until"=>time()+3600
                            )
                        )
                    ,false);
            $Store=new mnsFrontEndObject();
            $Store=$Store->mnsFrontEndStoreData();
            $logo=get_site_url()."/".$Store['logo'];
            $from=$Store['email'];
            $logo='<img src="'.$logo.'" class="StoreLogo">';
            $ar=array(
                "STORELOGO"=>$logo,
                "STORENAME"=>$Store['name'],
                "STOREMAIL"=>$Store['email'],
                "FORGOTPASSWORDCODE"=>$newPasswordCode,
                "FORGOTPASSWORDPASS"=>$newPasswordPass,
                "FORGOTPASSWORDLINK"=>$ForgotPasswordLink,
            );
            $seller=new mensio_seller();
            if($seller->getMailTemplate("PswdConfirm",$ar)){
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= 'From: '.get_bloginfo().' <'.$from.'>' . "\r\n";
                $message= stripslashes_deep($seller->getMailTemplate("PswdConfirm",$ar));
                wp_mail(filter_var($_REQUEST['mns_username']), "Password Change", $message,$headers);
            }
        }
        echo $Result;
    }
    die;
}