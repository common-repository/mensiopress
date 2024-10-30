<?php
add_shortcode( 'mns_signup', 'mensiopress_signup_form' );
if(!function_exists("mensiopress_signup_form")){
    function mensiopress_signup_form($atts){
    if(empty($atts['titlesize']) && !empty($GLOBALS['MensioPressFontSize'])){
        $atts['titlesize']=$GLOBALS['MensioPressFontSize'];
    }
    if(empty($atts['fontsize']) && !empty($GLOBALS['MensioPressFontSize'])){
        $atts['fontsize']=$GLOBALS['MensioPressFontSize'];
    }
    if(!empty($atts['titlesize'])){
        $atts['titlesize']=str_replace("-",".",$atts['titlesize']);
    }
    if(!empty($atts['fontsize'])){
        $atts['fontsize']=str_replace("-",".",$atts['fontsize']);
    }
                $html="";
                if(empty($atts['title'])){
                    $title="Signup";
                }
                else{
                    $title=$atts['title'];
                }
                $countries=new mensio_seller();
                $countries=$countries->GetCountryCodes();
                $currentCountry=false;
                if(!empty($_SESSION['UserInCountry'])){
                    foreach($countries as $country){
                        if($country->originalID==$_SESSION['UserInCountry']){
                            $currentCountry=$country->originalName;
                        }
                    }
                }
                $sectors=new mensio_seller();
                $sectors=$sectors->GetBusinessSectorTypes();
                if(!empty($_REQUEST['MensioVerifyUser'])){
                    $verify=new mensio_seller();
                    $verify=$verify->VerifyUser(MensioDecodeUUID($_REQUEST['MensioVerifyUser']));
                    if($verify==true){
                        $html.="<div class='account-verified'>Account has been verified</div>";
                    }
                    else{
                        $html.="<div class='account-not-verified'>Account has not been verified</div>";
                    }
                    return $html;
                }
                if(get_option('MensioPress_TextNext_'.$_SESSION['MensioThemeLangShortcode'])){
                    $NextButton=get_option('MensioPress_TextNext_'.$_SESSION['MensioThemeLangShortcode']);
                }
                else{
                    $NextButton="Next";
                }
                if(get_option('MensioPress_TextBack_'.$_SESSION['MensioThemeLangShortcode'])){
                    $BackButton=get_option('MensioPress_TextBack_'.$_SESSION['MensioThemeLangShortcode']);
                }
                else{
                    $BackButton="Back";
                }
                if(get_option('MensioPress_TextSignup_'.$_SESSION['MensioThemeLangShortcode'])){
                    $SignupButton=get_option('MensioPress_TextSignup_'.$_SESSION['MensioThemeLangShortcode']);
                }
                else{
                    $SignupButton="Signup";
                }
                if(!empty($_SESSION['mnsUser'])){
                    $html.="<div class='mns-already-logged-in'>Welcome ".$_SESSION['mnsUser']['FirstName']."</div>";
                }
                else{
                    $CustomScript="
                        var ajaxurl = '".admin_url('admin-ajax.php')."';";
                    $tt=rand(1,1000);
                        wp_enqueue_script("MensioPressPublicJS".$tt,plugin_dir_url(dirname(__FILE__))."js/empty.js");
                        wp_add_inline_script( "MensioPressPublicJS".$tt,
                            $CustomScript
                        );
                    if(!empty($atts['titlesize'])){
                        $fontSize=$atts['titlesize'];
                    }
                    else{
                        $fontSize="1";
                    }
                    $html.="<h2 style='font-size:".$fontSize."rem;' class='mensioObjectTitle'>".$title."</h2><hr class='titleLine' />";
                    $html.="
                    <div class='form-error'>
                        Please fill in all the required fields
                    </div>
                    <div class='signup-form form-1' style='font-size:".$atts['fontsize']."rem;'>";
                        if(MENSIO_FLAVOR=='STD' && function_exists("MensioPressChooseUserType")){
                            $html.=MensioPressChooseUserType();
                        }
                        else{
                        $html.="
                            <select name='mns-user-type' id='mns-user-type' style='display:none;'>
                                <option>Individual</option>
                            </select>";
                        }
                        $html.="
                        <div class='form-input'>
                        <span>Title</span>
                            <select name='mns-user-title'>
                                <option value='Mr'>Mr</option>
                                <option value='Mrs'>Mrs</option>
                            </select>
                        </div>
                        <div class='form-input'>
                        <span>Firstname</span>
                            <input type='text' name='mns-firstname' placeholder='Firstname' autocomplete='off'>
                        </div>
                        <div class='form-input'>
                        <span>Lastname</span>
                            <input type='text' name='mns-lastname' placeholder='Lastname' autocomplete='off'>
                        </div>
                        <div class='form-input'>
                        <span>Email</span>
                            <input type='text' name='mns-email' placeholder='email' autocomplete='off'>
                        </div>
                        <div class='form-input'>
                        <span>Password</span>
                            <input type='password' name='mns-password' placeholder='Password' autocomplete='off'>
                        </div>
                        <div class='form-input'>
                        <span>Repeat Password</span>
                            <input type='password' name='mns-repeat-password' placeholder='Password' autocomplete='off'>
                        </div>
                        <div class='form-input'>
                            <a href='#' class='next-form-button'>".$NextButton." <i class='fa fa-angle-double-right'></i></a>
                        </div>
                    </div>
                    <div class='signup-form form-2' style='font-size:".$atts['fontsize']."rem;'>
                        <div class='' id='company-data'>
                            <div class='form-input'>
                            <span>Company Name</span>
                                <input type='text' name='mns-company-name' placeholder='Company Name' autocomplete='off'>
                            </div>
                            <div class='form-input'>
                            <span>Company Sector:</span>
                                <select name='mns-company-sector'>";
                                foreach($sectors as $sectorID => $sectorName){
                                    $html.= "<option value='".MensioEncodeUUID($sectorID)."'>".$sectorName."</option>";
                                }
                                $html.= "
                                </select>
                            ";
                            $html.="
                            <!--    <input type='text' name='mns-company-type' placeholder='Company Type' autocomplete='off'>-->
                            </div>
                            <div class='form-input'>
                            <span>Company Tin</span>
                                <input type='text' name='mns-company-tin' placeholder='Company Tin' autocomplete='off'>
                            </div>
                            <div class='form-input'>
                            <span>Website</span>
                                <input type='text' name='mns-website' placeholder='Website' autocomplete='off'>
                            </div>
                            <div class='form-input'>
                            <span>Company email</span>
                                <input type='text' name='mns-company-email' placeholder='Company email' autocomplete='off'>
                            </div>
                        </div>
                        <div class='form-input'>
                        <span>Country:</span>
                            <select name='mns-country' current-country='".$currentCountry."' language='". MensioEncodeUUID($_SESSION['MensioThemeLang'])."'>";
                            foreach($countries as $country){
                                $sel=false;
                                if(!empty($_SESSION['UserInCountry']) && $country->originalID==$_SESSION['UserInCountry']){
                                    $sel.=" selected";
                                }
                                $html.="<option value='".$country->originalName."' ".$sel.">".$country->name."</option>";
                            }
                        $html.="
                            </select>
                        </div>
                        <div class='form-input'>
                        <span>Region</span>";
                            if(empty($currentCountry)){
                                $html.="
                                <select name='mns-region' disabled>
                                    <option></option>";
                            }
                            else{
                                $html.="
                                <select name='mns-region'>";
                                $regions=new mensio_seller();
                                $regions->Set_Country($_SESSION['UserInCountry']);
                                foreach($regions->GetCountryRegions() as $region){
                                    $html.="<option>".$region['name']."</option>";
                                }
                            }
                                $html.="
                            </select>
                        </div>
                        <div class='form-input'>
                        <span>City</span>
                            <input type='text' name='mns-city' placeholder='City' autocomplete='off'>
                        </div>
                        <div class='form-input'>
                        <span>Street</span>
                            <input type='text' name='mns-address' placeholder='Address' autocomplete='off'>
                        </div>
                        <div class='form-input'>
                        <span>Zip Code</span>
                            <input type='text' name='mns-zipcode' placeholder='Zip Code' autocomplete='off'>
                        </div>
                        <div class='form-input'>
                        <span>Phone</span>
                            <input type='text' name='mns-phone' placeholder='Phone' autocomplete='off'>
                        </div>
                        <div class='form-input'>
                            <a href='#' class='back-form-button'><i class='fa fa-angle-double-left'></i> ".$BackButton." </a>
                            <a href='#' class='next-form-button'>".$NextButton." <i class='fa fa-angle-double-right'></i></a>
                        </div>
                    </div>
                    <div class='signup-form form-3' style='font-size:".$atts['fontsize']."rem;'>";
                        $tos=new mensio_seller();
                        $terms=$tos->GetActiveTermsNotice();
                        $tos=new mnsFrontEndObject();
                        $terms=$tos->mnsFrontEndTOS();
                        $html.=$terms."
                            <div class='signup-data'>
                            </div>
                        <div class='form-input'><hr />
                            <label style='text-align:right;'>
                                <input type='checkbox' name='mns-terms_of_use' value='agree' />
                                I Agree to Terms of Use
                            </label>
                        </div>
                        <div class='form-input'>
                            <a href='#' class='back-form-button'><i class='fa fa-angle-double-left'></i> ".$BackButton." </a>
                            <input type='button' value='".$SignupButton."' class='submit-button'>
                        </div>
                    </div>
                    <div class='signup-result'></div>
                    ";
                }
                return $html;
        }
}
add_action('wp_ajax_mensiopress_checkSignupUsername','mensiopress_checkSignupUsername');
add_action('wp_ajax_nopriv_mensiopress_checkSignupUsername','mensiopress_checkSignupUsername');
function mensiopress_checkSignupUsername(){
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
    $check=false;
    if(!empty($_POST['UsernameToCheck'])){
        $seller=new mensio_seller();
        $seller->Set_UserName(filter_var($_POST['UsernameToCheck']));
        $check=$seller->CheckIfUserNameExists();
        if(!empty($check)){
            $data=$seller->GetUserIDsByMail(filter_var($_POST['UsernameToCheck']));
            $CredID=$data->customer;
            $UserType=$seller->GetCustomerType($CredID);
            if(!empty($UserType) && $UserType=="Guest"){
                $check=false;
            }
        }
        echo $check;
    }
    die;
}
add_action('wp_ajax_mensiopress_signup','mensiopress_signup');
add_action('wp_ajax_nopriv_mensiopress_signup','mensiopress_signup');
function mensiopress_signup(){
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
    if(empty($_REQUEST['mns_tos']) || $_REQUEST['mns_tos']=="false"){
        $signup_message="4";
        echo $signup_message;
        die;
    }
    $country_id=false;
    $region_id=false;
    $country_post=filter_var($_REQUEST['mns_country']);
    $Result="not-found";
    $countries=new mensio_seller();
    $countries=$countries->GetCountryCodes();
    foreach($countries as $country){
        if($country->originalName == $country_post){
            $country_id=$country->originalID;
            $get_regions=new mensio_seller();
            $get_regions->Set_Country($country->originalID);
            foreach($get_regions->GetCountryRegions() as $region){
                if($region['name']==filter_var($_REQUEST['mns_region'])){
                    $region_id=$region['uuid'];
                }
            }
        }
    }
    $user_type="Individual";
    if(!empty(filter_var($_REQUEST['mns_user_type']))){
        $user_type=stripslashes_deep(filter_var($_REQUEST['mns_user_type']));
    }
    $title=false;
    if(!empty(filter_var($_REQUEST['mns_user_title']))){
        $title=stripslashes_deep(filter_var($_REQUEST['mns_user_title']));
    }
    $email=false;
    if(!empty(filter_var($_REQUEST['mns_email']))){
        $email=stripslashes_deep(filter_var($_REQUEST['mns_email']));
    }
    $firstname=false;
    if(!empty(filter_var($_REQUEST['mns_firstname']))){
        $firstname=stripslashes_deep(filter_var($_REQUEST['mns_firstname']));
    }
    $lastname=false;
    if(!empty(filter_var($_REQUEST['mns_lastname']))){
        $lastname=stripslashes_deep(filter_var($_REQUEST['mns_lastname']));
    }
    $password=false;
    if(!empty(filter_var($_REQUEST['mns_password']))){
        $password=stripslashes_deep(filter_var($_REQUEST['mns_password']));
    }
    $city=false;
    if(!empty(filter_var($_REQUEST['mns_city']))){
        $city=stripslashes_deep(filter_var($_REQUEST['mns_city']));
    }
    $address=false;
    if(!empty(filter_var($_REQUEST['mns_address']))){
        $address=stripslashes_deep(filter_var($_REQUEST['mns_address']));
    }
    $zip_code=false;
    if(!empty(filter_var($_REQUEST['mns_zip_code']))){
        $zip_code=stripslashes_deep(filter_var($_REQUEST['mns_zip_code']));
    }
    $phone=false;
    if(!empty(filter_var($_REQUEST['mns_phone']))){
        $phone=stripslashes_deep(filter_var($_REQUEST['mns_phone']));
    }
    $tos=false;
    if(!empty(filter_var($_REQUEST['mns_tos']))){
        $tos=stripslashes_deep(filter_var($_REQUEST['mns_tos']));
    }
    $companySector=false;
    if(!empty($_REQUEST['mns_company_sector'])){
        $companySector=MensioDecodeUUID(filter_var($_REQUEST['mns_company_sector']));
    }
    $companyTin=false;
    if(!empty($_REQUEST['mns_company_tin'])){
        $companyTin=stripslashes_deep(filter_var($_REQUEST['mns_company_tin']));
    }
    $companyName=false;
    if(!empty($_REQUEST['mns_company_name'])){
        $companyName=stripslashes_deep(filter_var($_REQUEST['mns_company_name']));
    }
    $company_email=false;
    if(!empty($_REQUEST['mns_company_email'])){
        $company_email=stripslashes_deep(filter_var($_REQUEST['mns_company_email']));
    }
    $website=false;
    if(!empty($_REQUEST['mns_website'])){
        $website=stripslashes_deep(filter_var($_REQUEST['mns_website']));
    }
    $arr=array(
        "user_type"=>$user_type,
        "title"=>$title,
        "firstname"=>$firstname,
        "lastname"=>$lastname,
        "company_name"=>$companyName,
        "company_sector"=>$companySector,
        "company_tin"=>$companyTin,
        "website"=>$website,
        "company_email"=>$company_email,
        "email"=>$email,
        "password"=>$password,
        "country"=>$country_id,
        "region"=>$region_id,
        "city"=>$city,
        "address"=>$address,
        "zip_code"=>$zip_code,
        "phone"=>$phone,
        "tos"=>$tos
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
              $_SESSION['mnsUser']=$signupData['Data'];
              $signup->SendWelcomeMail($arr);
              $signup->WriteInLog("Customers",$arr['email'].' has signed up');
              $signup_message="1";
              if(!empty(get_option("MensioPressFBappID")) && !empty(filter_var($_POST['fbid']))){
                  add_option("MNSFBUser-".filter_var($_POST['fbid']),$arr['email'],'',"no");
              }
          }
          else{
            $signup_message="2";
          }
        }
        else{
            $signup_message="2";
        }
        if($signup_message=="2"){
            $seller=new mensio_seller();
            $data=$seller->GetUserIDsByMail($email);
            $CustID=$data->customer;
            $CredID=$data->uuid;
            $UserType=$seller->GetCustomerType($CustID);
            if(!empty($UserType) && $UserType=="Guest"){
                $updateCustomer=$signup->GuestToUser($CredID,$CustID,$user_type,$password,$firstname,$lastname,$title);
                    $newAddress=new mensio_seller();
                    $data=array(
                        "email"=>$email,
                        "lastname"=>$lastname,
                        "firstname"=>$firstname,
                        "country"=>$country_id,
                        "region"=>$region_id,
                        "city"=>$city,
                        "address"=>$address,
                        "zip_code"=>$zip_code,
                        "phone"=>$phone
                        );
                    if($newAddress->Set_NewCustomerData(json_encode($data))){
                        $newAddress=$newAddress->AddNewCustomerAddress(
                                $CustID,
                                $CredID,
                                false);
                        $signup_message="1";
                    }
            }
        }
    }
    else{
        $signup_message="3";
    }
    echo $signup_message;
    die;
}
add_action('wp_ajax_mensiopress_ResendVerification','mensiopress_ResendVerification');
add_action('wp_ajax_nopriv_mensiopress_ResendVerification','mensiopress_ResendVerification');
function mensiopress_ResendVerification(){
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
    $arr=array(
        "email"=>$_SESSION['mnsUser']['UserName'],
        "firstname"=>$_SESSION['mnsUser']['FirstName'],
        "lastname"=>$_SESSION['mnsUser']['LastName']
        );
    $signup=new mensio_seller();
    $signup->SendWelcomeMail($arr);
    die;
}