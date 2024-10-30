<?php
add_shortcode( 'mns_user', 'mensiopress_user' );
function mensiopress_user($atts){
    wp_enqueue_script("jquery-ui-datepicker");
    wp_enqueue_script("jquery-ui-accordion");
    if(empty($atts['titlesize']) && !empty($GLOBALS['MensioPressFontSize'])){
        $atts['titlesize']=$GLOBALS['MensioPressFontSize'];
    }
    if(empty($atts['fontsize']) && !empty($GLOBALS['MensioPressFontSize'])){
        $atts['fontsize']=$GLOBALS['MensioPressFontSize'];
    }
    if((!empty($_SESSION['mnsUser']) && empty($_GET['action']) && (substr($_SESSION['mnsUser']['Credential'],0,6)!='Guest-'))){
    $user_uuid=$_SESSION['mnsUser']['Credential'];
    $usr=new mensio_customers();
    $usr->Set_UUID($user_uuid);
    $user=$usr->LoadCustomerData();
    $user=$user[0];
    $address=new mensio_customers();
    $address->Set_UUID($user_uuid);
    $addresses=$address->LoadCustomerAddress();
    $contact=new mensio_customers();
    $contact->Set_UUID($user_uuid);
    $contacts=$contact->LoadCustomerContact();
    $contactTypes=new mensio_customers();
    $contactTypes=$contactTypes->LoadSelectorTypes("contacts");
    $userType=$user->name;
    $check=new mensio_customers();
    $check->Set_UUID($_SESSION['mnsUser']['Credential']);
    $MensioPressScript='var username="'.$_SESSION['mnsUser']['UserName'].'";var Firstname="'.$_SESSION['mnsUser']['FirstName'].'";var Lastname="'.$_SESSION['mnsUser']['LastName'].'";';
    $tt=rand(1,1000);
                        wp_enqueue_script("MensioPressPublicJS".$tt,plugin_dir_url(dirname(__FILE__))."js/empty.js");
                        wp_add_inline_script( "MensioPressPublicJS".$tt,
                            $MensioPressScript
                        );
    $html='
        <div>';
            $html.='<fieldset class="user-data" style="font-size:'.$atts['fontsize'].'rem;">';
            if(!empty($_GET['subpage']) && $_GET['subpage']=='general'){
                $html.='
                <div class="field-label general" style="font-size:'.str_replace("-",".",$atts['titlesize']).'rem;">Account info</div>
                <div class="field general">
                    <div class="asked">Created:</div>
                    <div class="given">'.$user->created.'</div>
                </div>
                <div class="field general">
                    <div class="asked">Type:</div>
                    <div class="given">'.$userType.'</div>
                </div>
                <div class="field general">
                    <div class="asked">Title: </div>
                    <div class="given">'.$user->title.'</div>
                </div>
                <div class="field general">
                    <div class="asked">First Name:</div>
                    <div class="given">'.$user->firstname.'</div>
                </div>
                <div class="field general">
                    <div class="asked">Last Name: </div>
                    <div class="given">'.$user->lastname.'</div>
                </div>
                <div class="field general">
                    <div class="asked">Username:</div>
                    <div class="given">'.$user->username.'</div>
                </div>
                <div class="field general">
                    <div class="asked">Password:</div>
                    <div class="given">************************ <a href="#" class="changePassword notes">(Change)</a></div>
                </div>
                 <div class="field-label general">Activity Report</div>
                <div class="field general">
                    <div class="asked">Last Login: </div>
                    <div class="given">'.$user->lastlogin.'</div>
                </div>
                <div class="field general">
                    <div class="asked">IP Address: </div>
                    <div class="given">'.$user->ipaddress.'</div>
                </div>
                <div class="field general">
                    <div class="asked">Account Status: </div>
                    <div class="given">';
                        if($user->active){
                            $html.="<strong style='color:green;'>Verified</strong>";
                        }
                        else{
                            $html.="<strong style='color:red;'>Not Verified <a href='#' class='resend-verification notes'>(Resend Verification Email)</a></strong>";
                        }
                    $html.='</div>
                </div>';
            }
            if(!empty($_GET['subpage']) && $_GET['subpage']=='addresses'){
            $html.='
            <!--Addresses Tab -->
            <div class="field-label" style="font-size:'.$atts['titlesize'].'rem;">Address info</div>
            <div class=" addresses">';
            $getCountry=new mensio_countries();
            $countries=$getCountry->GetCountriesDataSet();
            $contactTypes=new mensio_customers();
            $AddressTypes=$contactTypes->LoadSelectorTypes("addresses");
            foreach($addresses as $address){
                if($address->deleted=="1"){
                    continue;
                }
                        $html.='<div>
                            <div class="editAddress">
                                <i class="fa fa-pencil" style="float:right;"></i>
                            </div>
                            <div class="deleteAddress" id="rem-'. MensioEncodeUUID($address->uuid).'">
                                <i class="fa fa-trash" style="float:right;"></i>
                            </div>
                            '.$address->street.'
                                </div>';
                        $html.='<div>';
                            $getCountry=new mensio_countries();
                            $getCountry->Set_UUID($address->country);
                            $country=false;
                            $mycountries=new mensio_seller();
                            $mycountries=$mycountries->GetCountryCodes();
                            foreach($mycountries as $countr){
                                if($countr->originalID==$address->country){
                                    $country=$countr->name;
                                }
                            }
                            $getRegion=new mensio_regions();
                            $getRegion->Set_UUID($address->region);
                            $regionName=$getRegion->LoadRegionName();
                            $html.='
                            <div class="field addresses">
                                <div class="asked">Type: </div>
                                <div class="given">';
                                    $html.="<span>".$address->name."</span>";
                                    $html.="<select style='display:none;' name='updType'>";
                                        $checked=false;
                                        foreach($AddressTypes as $adr){
                                            if($adr->name==$address->name){
                                                $checked=" selected";
                                            }
                                            $html.="<option value='".MensioEncodeUUID($adr->uuid)."'".$checked.">".$adr->name."</option>";
                                            $checked=false;
                                        }
                                    $html.="</select>";
                                $html.='</div>
                            </div>
                            <div class="field addresses">
                                <div class="asked">Receiver: </div>
                                <div class="given">';
                                    $html.="<span>".$user->lastname.' '.$user->firstname."</span>";
                                    $html.='<input type="text" name="updFullname" value="'.$user->lastname.' '.$user->firstname.'" style="display:none;"/>';
                                $html.='</div>
                            </div>
                            <div class="field addresses">
                                <div class="asked">Country: </div>
                                <div class="given">';
                                    $html.="<span>".$country."</span>";
                                    $html.='
                                    <select name="mns-country" style="display:none;" language="'. MensioEncodeUUID($_SESSION['MensioThemeLang']).'">';
                                        $checked=false;
                                        $getmycountries=new mensio_seller();
                                        $mycountries=$getmycountries->GetCountryCodes();
                                        foreach($mycountries as $countr){
                                            if($countr->name==$country){
                                                $checked=" selected";
                                                $get_regions=new mensio_seller();
                                                $get_regions->Set_Country($countr->originalID);
                                                if(is_array($get_regions->GetCountryRegions())){
                                                    $regions=$get_regions->GetCountryRegions();
                                                }
                                            }
                                            $html.='<option value="'.MensioEncodeUUID($countr->originalID).'" '.$checked.'>'.$countr->name.'</option>';
                                            $checked=false;
                                        }
                                    $html.='
                                    </select>';
                                $html.='</div>
                            </div>
                            <div class="field addresses">
                                <div class="asked">Region: </div>
                                <div class="given">';
                                    $html.="<span>".$regionName."</span>";
                                    $html.='
                                    <select name="mns-region" style="display:none;">';
                                    if(!empty($regions)){
                                        foreach($regions as $region){
                                            $html.="<option value='".MensioEncodeUUID($region['uuid'])."'>".$region['name']."</option>";
                                        }
                                    }
                                    $html.='
                                    </select>';
                                $html.='</div>
                            </div>
                            <div class="field addresses">
                                <div class="asked">City: </div>
                                <div class="given">';
                                    $html.="<span>".$address->city."</span>";
                                    $html.='<input type="text" name="updCity" value="'.$address->city.'" style="display:none;"/>';
                                $html.='</div>
                            </div>
                            <div class="field addresses">
                                <div class="asked">Zip Code: </div>
                                <div class="given">';
                                    $html.="<span>".$address->zipcode."</span>";
                                    $html.='<input type="text" name="updZipCode" value="'.$address->zipcode.'" style="display:none;"/>';
                                $html.='</div>
                            </div>
                            <div class="field addresses">
                                <div class="asked">Street: </div>
                                <div class="given">';
                                    $html.="<span>".$address->street."</span>";
                                    $html.='<input type="text" name="updStreet" value="'.$address->street.'" style="display:none;"/>';
                                $html.='</div>
                            </div>
                            <div class="field addresses">
                                <div class="asked">Phone:</div>
                                <div class="given">';
                                    $html.="<span>".$address->phone."</span>";
                                    $html.='<input type="text" name="updPhone" value="'.$address->phone.'" style="display:none;"/>';
                                $html.='</div>
                            </div>';
                            if($address->notes!='none'){
                            $html.='
                            <div class="field addresses">
                                <div class="asked">Notes: </div>
                                <div class="given">'.$address->notes.'</div>
                            </div>';
                            }
                            $html.='
                            <div class="field addresses">
                                <div class="asked"></div>
                                <div class="given"></div>
                            </div>
                            <input type="hidden" name="editAdress" value="'. MensioEncodeUUID($address->uuid).'">
                            <input type="button" name="submitEdit" value="Submit Edit" style="display:none;">
                        </div>';
            }
            $getmycountries=new mensio_seller();
            $mycountries=$getmycountries->GetCountryCodes();
            $html.= '
        </div>
        <br />
        <div class="field-label"><strong>+ New address</strong></div>
        <div style="display:table;border-spacing:10px;">
                <div class="field new-address">
                    <div class="asked">Type:</div>
                    <div class="given">
                        <select name="mns-address_type">';
                            foreach($AddressTypes as $adr){
                                $html.="<option value='".MensioEncodeUUID($adr->uuid)."'>".$adr->name."</option>";
                            }
                        $html.='
                        </select>
                    </div>
                </div>
                <div class="field new-address">
                    <div class="asked">Street:</div>
                    <div class="given"><input type="text" value="" name="mns-new-address"></div>
                </div>
                <div class="field new-address">
                    <div class="asked">Country:</div>
                    <div class="given">
                        <select name="mns-country" language="'.MensioEncodeUUID($_SESSION['MensioThemeLang']).'">';
                            foreach($mycountries as $country){
                                $sel=false;
                                if(!empty($_SESSION['UserInCountry']) && $_SESSION['UserInCountry']==$country->uuid){
                                    $sel=" selected";
                                }
                                $html.='<option value="'.MensioEncodeUUID($country->originalID).'" '.$sel.'>'.$country->name.'</option>';
                            }
                        $html.='
                        </select>
                    </div>
                </div>
                <div class="field new-address">
                    <div class="asked">Region:</div>
                    <div class="given">';
                        if(!empty($_SESSION['UserInCountry'])){
                            $html.='
                            <select name="mns-region">';
                            $get_regions=new mensio_seller();
                            $get_regions->Set_Country($_SESSION['UserInCountry']);
                            $regions=$get_regions->GetCountryRegions();
                            foreach($regions as $region){
                                $html.='<option>'.$region['name'].'</option>';
                            }
                        }
                        else{
                            $html.='<select name="mns-region" disabled>';
                        }
                        $html.='</select>
                    </div>
                </div>
                <div class="field new-address">
                    <div class="asked">City:</div>
                    <div class="given"><input type="text" value="" name="mns-city"></div>
                </div>
                <div class="field new-address">
                    <div class="asked">Zip Code:</div>
                    <div class="given"><input type="text" name="mns-zipcode" value=""></div>
                </div>
                <div class="field new-address">
                    <div class="asked">Phone:</div>
                    <div class="given"><input type="text" value="" name="mns-phone"></div>
                </div>
                <div class="field new-address">
                    <div class="asked"></div>
                    <div class="given"><br />
                        <input type="button" value="Save Address" class="mns-update-user" language="'. MensioEncodeUUID($_SESSION['MensioThemeLang']).'">
                    </div>
                </div>
        </div>
        ';
            }
            if(!empty($_GET['subpage']) && $_GET['subpage']=='contacts'){
            $html.='
            <!--Contact Tab -->
            <div class="field-label contacts" style="font-size:'.$atts['titlesize'].'rem;">Contact info</div>
            <div style="display:table;border-spacing:0 10px;">
                 ';
                 foreach($contacts as $contact){
                     if($contact->deleted=="1"){
                         continue;
                     }
                $html.='
                <div class="field contacts">
                    <div class="asked">'.$contact->name.': </div>
                    <div class="given">'.$contact->value.'</div>
                    <div class="given"><i class="fa fa-trash deleteContact" id="'.MensioEncodeUUID($contact->uuid).'"></i></div>
                </div>';
                 }
                $html.= '
            </div>
                <div class="field-label contacts">
                    <div class="asked new-address">+ New Contact</div>
                </div>
                <div class="field contacts new-contacts">
                    <div class="asked">Type:</div>
                    <div class="given">
                        <select name="mns-contactType">';
                            foreach($contactTypes as $type){
                            $html.='<option>'.$type->name.'</option>';
                            }
                        $html.='
                        </select>
                    </div>
                </div>
                <div class="field contacts new-contacts">
                    <div class="asked">Value:</div>
                    <div class="given"><input type="text" value="" name="mns-contactValue"></div>
                </div>
                <div class="field contacts new-contacts">
                    <div class="asked"></div>
                    <div class="given"><br /><br/>
                        <input type="button" value="Update Data" class="mns-update-user">
                    </div>
                </div>
            ';
            }
            if(!empty($_GET['subpage']) && $_GET['subpage']=='history'){
                wp_enqueue_script("MENSIOPRESSmomentsJS", plugin_dir_url(__FILE__)."../../public/js/moment.js");
                $toDate=$user->created;
                $seller=new mensio_seller();
                $seller->Set_Customer($_SESSION['mnsUser']['Credential']);
                $historyOrders=$seller->LoadAllCustomerOrders(false);
                $historyOrders2=$historyOrders;
                asort($historyOrders2);
                foreach($historyOrders2 as $order=>$product){
                    $toDate=$historyOrders[$order]['created'];
                    break;
                }
                $postdate = date("d-m-Y",strtotime($toDate));
                $today = date('Y-m-d'); // today date
                $diff = strtotime($today) - strtotime($postdate);
                $days = (int)$diff/(60*60*24);
                $html.='
            <!--History Tab -->
            <div class="field-label history" style="font-size:'.$atts['titlesize'].'rem;">Orders</div>';
                $html.="<input type='text' name='MensioPressOrdersFrom' value='".date("Y-m-d",strtotime($user->created))."'>";
                $html.="<input type='text' name='MensioPressOrdersTo' value='".date("Y-m-d")."'>";
                $html.="<div class='MensioPressHistoryOrderschooseDate'>
                        <div class='date-range' mindate='".$days."' maxdate='".date("Y-m-d")."'></div>
                        </div>";
            $html.='
            <div class="history HistoryOrders">
                 ';
            $html.=showOrders($historyOrders);
            $html.='</div>';
            }
            if(!empty($_GET['subpage']) && $_GET['subpage']=='tickets'){
                 $html.='
<!--Tickets Tab -->
                <div class="field-label tickets" style="font-size:'.$atts['titlesize'].'rem;">Support Requests</div>
                <div class="Tickets">';
                 $getTickets=new mensio_seller();
                 $getTickets->Set_Customer($_SESSION['mnsUser']['Credential']);
                 $Tickets=$getTickets->LoadUserTickets();
                 foreach($Tickets as $ticket){
                    $html.='
                    <div class="field tickets ticketLabel" id="'. MensioEncodeUUID($ticket->uuid).'">
                       <div class="given">'.$ticket->title.'</div>
                    </div>
                    ';
                    if($ticket->content){
                        $html.=
                        '<div class="ticketContent">'
                            . '<div class="field tickets replies ticket-'.MensioEncodeUUID($ticket->uuid).'">'
                                .'<div>'.$ticket->firstname.' '.$ticket->lastname.'</div>'
                                . '<div>&nbsp;('.$ticket->dateadded.')</div>'
                            .'</div>'
                            .'<div class="field tickets is-Row replies ticket-'.MensioEncodeUUID($ticket->uuid).'">'
                                .$ticket->content
                            .'</div>';
                    }
                    $getReplies=new mensio_seller();
                    $getReplies->Set_Customer($_SESSION['mnsUser']['Credential']);
                    $getReplies->Set_TicketID($ticket->uuid);
                    $Replies=$getReplies->LoadUserTicketReplies();
                    if(!empty($Replies)){
                        foreach($Replies as $reply){
                            $replyAuthor=$reply->replyauthor;
                            if($replyAuthor==$_SESSION['mnsUser']['Credential']){
                                $replyAuthor=$_SESSION['mnsUser']['FirstName']." ".$_SESSION['mnsUser']['LastName'];
                            }
                            $html.=  '<div class="field tickets replies ticket-'.MensioEncodeUUID($ticket->uuid).'">'
                                        .'<div>'.$replyAuthor.'</div>'
                                        . '<div>&nbsp;('.$reply->replydate.')</div>'
                                    .'</div>'
                                    .'<div class="field tickets is-Row replies ticket-'.MensioEncodeUUID($ticket->uuid).'">'
                                        .$reply->replytext
                                    .'</div>';
                        }
                    }
                    $html.='<div class="field tickets is-Row newReply replies ticket-'.MensioEncodeUUID($ticket->uuid).'">'
                                    .'<textarea></textarea>'
                                    .'<hr /><input type="button" value="Post">'
                            .'</div>'
                        .'</div>';
                 }
                 $html.="</div>";
                    if(empty($_GET['ticketTitle'])){
                        $TicketTitle=false;
                    }
                    else{
                        $TicketTitle=$_GET['ticketTitle'];
                    }
                    $html.='<br />
                    <div class="field tickets ticketLabel newTicket">
                       <div class="field-label tickets"><Strong>New Support Request:</strong></div>
                       <div class="given">
                            Title:<input type="text" name="title" value="'.$TicketTitle.'"><br>
                            Text:<textarea id="newTicket" name="newTicket"></textarea><br><input type="button" id="postNewTicket" value="Post New">
                       </div>
                    </div>
                    ';
            }
            if($userType!="Individual" && $check->CheckIfMain()){
                    $html.='
<!--Company Users Tab -->
                <div class="field-label company-users" style="font-size:'.$atts['titlesize'].'rem;">Users</div>';
                $usr=new mensio_seller();
                $users=$usr->LoadCompanyCredentials($_SESSION['mnsUser']['Customer']);
                foreach($users as $user){
                    $html.= 
                    '<div class="field company-users user-'.MensioEncodeUUID($user->uuid).'">'
                        .'<div class="asked">'.$user->firstname." ".$user->lastname.'</div>'
                        . '<div>/</div>'
                        . '<div class="given">'.$user->username;
                        $userCheck=new mensio_customers();
                        $userCheck->Set_UUID($user->uuid);
                        if(!$userCheck->CheckIfMain()){
                            $html.='<input type="button" value="x" class="disableMnsUser">';
                        }
                            $html.='</div>'
                    .'</div>';
                }
                $html.='
                    <div class="field company-users newUser">
                       <div class="asked">New User: </div>
                       <div></div>
                       <div class="given">
                            Email:<input type="text" name="companyEmail"><br>
                            Title:
                            <label>Mr <input type="radio" value="Mr" name="companyTitle"></label>
                            <label>Mrs <input type="radio" value="Mrs" name="companyTitle"></label><br>
                            Firstname:<input type="text" name="companyFirstname" placeholder="Firstname"><br>
                            Lastname:<input type="text" name="companyLastname" placeholder="Lastname"><br>
                            Password:<input type="password" name="companyPassword" placeholder="Password"><br>
                            <input type="button" value="Add User" id="AddNewCompanyUser">
                       </div>
                    </div>';
}
            $html.='</fieldset>';
        $html.='</div>';
    }
    else{
        $html='
            <center>
                <strong>You are not yet logged in!</strong>
            </center>
            ';
    }
    return $html;
}
add_action('wp_ajax_mensiopress_UpdateUser','mensiopress_UpdateUser');
add_action('wp_ajax_nopriv_mensiopress_UpdateUser','mensiopress_UpdateUser');
function mensiopress_UpdateUser(){
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
    if(!empty($_SESSION['mnsUser'])){
        if(!empty($_REQUEST['Language'])){
            $_SESSION['MensioThemeLang']= MensioDecodeUUID(filter_var($_REQUEST['Language']));
        }
        $result=array();
        $userID=$_SESSION['mnsUser']['Customer'];
        if(filter_var($_REQUEST['mns_NewAddress'])){
            $country_post=filter_var($_REQUEST['mns_country']);
            $countries=new mensio_seller();
            $countries=$countries->GetCountryCodes();
            foreach($countries as $country){
                if($country->name == $country_post || $country->originalID==MensioDecodeUUID(filter_var($_POST['mns_country']))){
                    $country_id=$country->uuid;
                    $get_regions=new mensio_seller();
                    $get_regions->Set_Country($country->uuid);
                    foreach($get_regions->GetCountryRegions() as $region){
                        if($region['name']==filter_var($_REQUEST['mns_region'])){
                            $region_id=$region['uuid'];
                        }
                    }
                }
            }
            $newAddress=new mensio_seller();
            $data=array(
                "email"=>$_SESSION['mnsUser']['UserName'],
                "lastname"=>$_SESSION['mnsUser']['LastName'],
                "firstname"=>$_SESSION['mnsUser']['FirstName'],
                "country"=>$country_id,
                "region"=>$region_id,
                "city"=>stripslashes_deep(filter_var($_REQUEST['mns_city'])),
                "address"=>stripslashes_deep(filter_var($_REQUEST['mns_NewAddress'])),
                "zip_code"=>stripslashes_deep(filter_var($_REQUEST['mns_zip_code'])),
                "phone"=>stripslashes_deep(filter_var($_REQUEST['mns_phone']))
                );
            if($newAddress->Set_NewCustomerData(json_encode($data))){
                $newAddress->AddNewCustomerAddress(
                        $_SESSION['mnsUser']['Customer'],
                        $_SESSION['mnsUser']['Credential'],
                        MensioDecodeUUID($_POST['mns_address_type']));
                $data['newAddressAdded']='yes';
                $result['NewAddress']=$data;
                echo "aye";
            }
            else{
                echo "no";
            }
            die;
        }
        if(filter_var($_REQUEST['mns_newContactType']) && filter_var($_REQUEST['mns_newContactValue'])){
            $newContact=new mensio_seller();
            $newContact->AddCustomerContact(
                    $_SESSION['mnsUser']['Credential'],
                    stripslashes_deep(filter_var($_REQUEST['mns_newContactType'])),
                    stripslashes_deep(filter_var($_REQUEST['mns_newContactValue']))
                );
            $data=array(
                "newContactAdded"=>'yes',
                'type'=>stripslashes_deep(filter_var($_REQUEST['mns_newContactType'])),
                'value'=>stripslashes_deep(filter_var($_REQUEST['mns_newContactValue']))
            );
            $result['NewContact']=$data;
            echo "aye";
            die;
        }
        echo json_encode($result);
    }
    else{
        echo "no";
    }
    die;
}
function showOrders($historyOrders=array()){
    $seller=new mensio_seller();
    $html=false;
    $i=0;
    foreach($historyOrders as $order=>$product){
         $i++;
         if($i==6){
         }
        $TicketLink='
        ';
         $total=0;
         $html.='<div class="MensioHistoryOrder history">#'.$historyOrders[$order]['refnumber'].' ('.$historyOrders[$order]['serial'].') / '.$historyOrders[$order]['created'].' / '.$historyOrders[$order]['status'].' <i class="fa fa-question"></i></div>'
                 . '<div class="MensioHistoryOrderProducts history">';
        $ShippingCost=0;
        if(!empty($product['shipping'])){
            $ShippingData=$seller->GetShippingData($product['shipping']);
            $shippingAddressText=false;
            if(!empty($ShippingData['Data'][0]->price)){
                $ShippingCost=$ShippingData['Data'][0]->price;
                $shippingAddress=$seller->GetAddress($product['shippingaddress']);
                if(!empty($shippingAddress)){
                    $shippingAddressText=$shippingAddress['street']."<br />";
                    $shippingAddressText.=$shippingAddress['zipcode']." - ";
                    $shippingAddressText.=$shippingAddress['city']."<br />";
                    if(!empty($shippingAddress['regionText'])){
                        $shippingAddressText.=$shippingAddress['regionText'].", ";
                    }
                    if(!empty($shippingAddress['countryText'])){
                        $shippingAddressText.=$shippingAddress['countryText'];
                    }
                }
            }
            $billingAddressText=false;
            if(!empty($BillingData['Data'][0]->price)){
                $BillingCost=$BillingData['Data'][0]->price;
                $BillingAddress=$seller->GetAddress($product['Billingaddress']);
                $BillingAddressText=$BillingAddress['street'].", ";
                $BillingAddressText.=$BillingAddress['city'].", ";
                $BillingAddressText.=$BillingAddress['zipcode'].", ";
                if(!empty($BillingAddress['regionText'])){
                    $BillingAddressText.=$BillingAddress['regionText'].", ";
                }
                if(!empty($BillingAddress['countryText'])){
                    $BillingAddressText.=$BillingAddress['countryText'];
                }
            }
        }
          $html.=
          '<div class="MensioHistoryOrderShipping">'
                          . '<div></div>'
              . '<div><strong>Shipping Address:</strong><br />'.
              $shippingAddressText
          .'</div>'
          .'</div>';
         $html.="<div class='MensioPressOrderHistoryTable'>";
                        foreach($product['Products'] as $prod){
                            $getProd=new mnsFrontEndObject();
                            $Prod=$getProd->mnsFrontEndProduct($prod['product']);
                            if(empty($Prod['name'])){
                                continue;
                            }
                            $total=$total+$Prod['final_price'];
                            $html.=
                                '<div class="MensioHistoryOrderProduct">'
                                    .'<div class="ProductImage"><!--<img src="'.$Prod['images'][0]['thumb'].'" alt="'.$Prod['name'].'"/>--></div>'
                                    .'<div class="ProductTitle"><strong>'.$Prod['name'].'</strong></div>'
                                    .'<div class="ProductAmount">x'.($prod['amount']+0).'</div>'
                                    .'<div class="ProductCost"><span class="mensioPrice">'.number_format(($Prod['final_price']+0),2).'</span></div>'
                                .'</div>';
                        }
                        $total=$total+$ShippingCost;
                            $html.=
                                '<div class="MensioHistoryOrderProduct">'
                                    .'<div class="ProductImage"></div>'
                                    .'<div class="ProductTitle"><strong>Shipping Cost</strong></div>'
                                    .'<div class="ProductAmount"></div>'
                                    .'<div class="ProductCost"><span class="mensioPrice">'.number_format($ShippingCost,2).'</span></div>'
                                .'</div>'
                                    . ''
                                    . '';
                            $html.=
                                    '<div class="MensioHistoryOrderProduct">'
                                    .'<div class="ProductImage">
                                    </div>'
                                    .'<div class="ProductTitle"><strong>Total Cost</strong></div>'
                                    .'<div class="ProductAmount"></div>'
                                    .'<div class="ProductCost"><span class="mensioPrice">'.number_format($total,2).'</span></div>'
                                .'</div>'
                    .'</div>';
                    $html.='
                        <a href="';
                            if ( get_option('permalink_structure') ) {
                                $html.='?subpage=tickets&ticketTitle='.urlencode('#'.$historyOrders[$order]['refnumber'].' ('.$historyOrders[$order]['serial'].')');
                            }
                            else{
                                $html.='?mensio_page='.$_GET['mensio_page'].'&subpage=tickets&ticketTitle='. urlencode('#'.$historyOrders[$order]['refnumber'].' ('.$historyOrders[$order]['serial'].')');
                            }
                        $html.='">
                            <div class="MensioPressOrderHistoryFunctionsBar">
                                Support
                            </div></a>';
                 $html.= '</div>';
     }
    return $html;
}
add_action('wp_ajax_mensiopress_LoadMoreHistory','mensiopress_LoadMoreHistory' );
add_action('wp_ajax_nopriv_mensiopress_LoadMoreHistory','mensiopress_LoadMoreHistory' );
function mensiopress_LoadMoreHistory(){
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
    if(!empty($_POST['from']) ){
        die;
    }
    else{
        $From=filter_var($_POST['from']);
        $seller=new mensio_seller();
        $seller->Set_Customer($_SESSION['mnsUser']['Credential']);
        $PostDateFrom=date("Y-m-d",strtotime($_POST['DateFrom']));
        $PostDateTo=date("Y-m-d",strtotime($_POST['DateTo']));
        $historyOrders=$seller->LoadAllCustomerOrders(false,
                array(
                    "DateFrom"=>$PostDateFrom,
                    "DateTo"=>$PostDateTo)
                );
        $html=false;
        $i=0;
        $html.= showOrders($historyOrders);
        if($html==false){
            $html="No Orders Found";
        }
        echo $html;
    }
    die;
}
add_action('wp_ajax_mensiopress_UpdateAddress','mensiopress_UpdateAddress' );
add_action('wp_ajax_nopriv_mensiopress_UpdateAddress','mensiopress_UpdateAddress' );
function mensiopress_UpdateAddress(){
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
    if(!empty($_POST['address'])){
        $address=stripslashes_deep(filter_var($_POST['address']));
        $Res= "updated";
    }
    if(!empty($_POST['Type'])){
        $updType=$_POST['Type'];
        $Res= "updated";
    }
    if(!empty($_POST['Type'])){
        $updType=MensioDecodeUUID($_POST['Type']);
        $Result=$seller->UpdateAddress("type",$updType, MensioDecodeUUID($address));
        $Res= "updated";
    }
    if(!empty($_POST['FullName'])){
        $updFullname=$_POST['FullName'];
        $Result=$seller->UpdateAddress("fullname",$updFullname, MensioDecodeUUID($address));
        $Res= "updated";
    }
    if(!empty($_POST['Country'])){
        $updCountry= MensioDecodeUUID($_POST['Country']);
        $Result=$seller->UpdateAddress("country",$updCountry, MensioDecodeUUID($address));
        $Res= "updated";
    }
    if(!empty($_POST['Region'])){
        $updRegion= MensioDecodeUUID($_POST['Region']);
        $Result=$seller->UpdateAddress("region",$updRegion, MensioDecodeUUID($address));
        $Res= "updated";
    }
    if(!empty($_POST['City'])){
        $updCity= $_POST['City'];
        $Result=$seller->UpdateAddress("city",$updCity, MensioDecodeUUID($address));
        $Res= "updated";
    }
    if(!empty($_POST['Street'])){
        $updStreet=$_POST['Street'];
        $Result=$seller->UpdateAddress("street",$updStreet, MensioDecodeUUID($address));
        $Res= "updated";
    }
    if(!empty($_POST['ZipCode'])){
        $updZipCode=$_POST['ZipCode'];
        $Result=$seller->UpdateAddress("zipcode",$updZipCode, MensioDecodeUUID($address));
        $Res= "updated";
    }
    if(!empty($_POST['Phone'])){
        $updPhone=$_POST['Phone'];
        $Result=$seller->UpdateAddress("phone",$updPhone, MensioDecodeUUID($address));
        $Res= "updated";
    }
    if(!empty($Result) && $Result=="deleted"){
            $seller=new mensio_seller();
                $updFullname=explode(" ",$updFullname);
                $data=array(
                    "email"=>$_SESSION['mnsUser']['UserName'],
                    "lastname"=>$updFullname[0],
                    "firstname"=>$updFullname[1],
                    "country"=>$updCountry,
                    "region"=>$updRegion,
                    "city"=>$updCity,
                    "address"=>$updStreet,
                    "zip_code"=>$updZipCode,
                    "phone"=>$updPhone
                    );
                if($seller->Set_NewCustomerData(json_encode($data))){
                    $added=$seller->AddNewCustomerAddress(
                            $_SESSION['mnsUser']['Customer'],
                            $_SESSION['mnsUser']['Credential'],$updType);
                    $data['newAddressAdded']='yes';
                    $result['NewAddress']=$data;
                    $Res= "added";
                }
    }
    echo $Res;
    die;
}
add_action('wp_ajax_mensiopress_DeleteAddress','mensiopress_DeleteAddress' );
add_action('wp_ajax_nopriv_mensiopress_DeleteAddress','mensiopress_DeleteAddress' );
function mensiopress_DeleteAddress(){
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
    $Res="false";
    if(!empty($_SESSION['mnsUser']['Credential']) && !empty($_POST['Address'])){
        $Address=stripslashes_deep(filter_var($_POST['Address']));
        $seller=new mensio_seller();
        $seller->RemoveAddress(MensioDecodeUUID($Address),$_SESSION['mnsUser']['Credential']);
        $Res="true";
    }
    echo $Res;
    die;
}
add_action('wp_ajax_mensiopress_DeleteUserContact','mensiopress_DeleteUserContact' );
add_action('wp_ajax_nopriv_mensiopress_DeleteUserContact','mensiopress_DeleteUserContact' );
function mensiopress_DeleteUserContact(){
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
    $Res="false";
    if(!empty($_SESSION['mnsUser']['Credential']) && !empty($_POST['ContactID'])){
        $Contact=stripslashes_deep(filter_var($_POST['ContactID']));
        $seller=new mensio_seller();
        $seller->RemoveContact(MensioDecodeUUID($Contact),$_SESSION['mnsUser']['Credential']);
        $Res="true";
    }
    echo $Res;
}
add_action('wp_ajax_mensiopress_NewTicket','mensiopress_NewTicket' );
add_action('wp_ajax_nopriv_mensiopress_NewTicket','mensiopress_NewTicket' );
function mensiopress_NewTicket(){
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
    if(empty($_POST['TicketText'])){
       die; 
    }
    $ticketText=stripslashes_deep(filter_var($_POST['TicketText']));
    $TicketTitle=stripslashes_deep(filter_var($_POST['ticketTitle']));
    $newTicket=new mensio_seller();
    $newTicket->Set_Customer($_SESSION['mnsUser']['Credential']);
    $newTicket->Set_TicketText($ticketText);
    $newTicketCode=$newTicket->InsertNewText($TicketTitle);
    if(!empty($newTicketCode)){
        $Data=array(
            "Title"=>$TicketTitle,
            "Code"=>$newTicketCode['code'],
            "Date"=>date("Y-m-d H:i:s"),
            "User"=>$_SESSION['mnsUser']['UserName']
        );
        $notify=new mensio_seller();
        $notify=$notify->AddNewTicketNotification($Data);
        echo json_encode(
                array(
                    "Message"=>"Your Ticket has been posted. We'll come back to you soon!",
                    "FullName"=>$_SESSION['mnsUser']['FirstName']." ".$_SESSION['mnsUser']['LastName'],
                    "Date"=>date("Y-m-d H:i:s"),
                    "TicketID"=>$newTicketCode['ID']
                )
            );
    }
    die;
}
add_action('wp_ajax_mensiopress_DisableUser','mensiopress_DisableUser' );
add_action('wp_ajax_nopriv_mensiopress_DisableUser','mensiopress_DisableUser' );
function mensiopress_DisableUser(){
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
    $userUUID= MensioDecodeUUID(filter_var($_REQUEST['mns_User']));
    $del=new mensio_seller();
    $del=$del->DisableUser($userUUID);
    if($del){
        echo "User Disabled";
    }
    else{
        echo "User not disabled";
    }
    die;
}
add_action('wp_ajax_mensiopress_NewCompanyUser','mensiopress_NewCompanyUser' );
add_action('wp_ajax_nopriv_mensiopress_NewCompanyUser','mensiopress_NewCompanyUser' );
function mensiopress_NewCompanyUser(){
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
    $title=stripslashes_deep(filter_var($_POST['Title']));
    $firstname=stripslashes_deep(filter_var($_POST['Firstname']));
    $lastname=stripslashes_deep(filter_var($_POST['Lastname']));
    $email=stripslashes_deep(filter_var($_POST['Email']));
    $password=stripslashes_deep(filter_var($_POST['Password']));
    $Data=array(
        'email'=>$email,
        'lastname'=>$lastname,
        'firstname'=>$firstname,
        'title'=>$title,
        'password'=>$password
    );
    $add=new mensio_seller();
    $add->Set_NewCustomerData(json_encode($Data));
    $add=$add->AddNewCompanyCredentials($_SESSION['mnsUser']['Customer']);
    if($add){
        echo "User added";
    }
    else{
        echo "User not added ";
    }
    die;
}
add_action('wp_ajax_mensiopress_PostNewTicketReply','mensiopress_PostNewTicketReply' );
add_action('wp_ajax_nopriv_mensiopress_PostNewTicketReply','mensiopress_PostNewTicketReply' );
function mensiopress_PostNewTicketReply(){
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
    if(!empty($_REQUEST['mns_TicketReply'])){
        $ticketID= MensioDecodeUUID(filter_var($_REQUEST['mns_TicketID']));
        $ticketReply=hebrevc(stripslashes_deep(filter_var($_REQUEST['mns_TicketReply'])));
        $insert=new mensio_seller();
        $insert->Set_Customer($_SESSION['mnsUser']['Credential']);
        $insert->Set_TicketID($ticketID);
        $insert->Set_TicketReply($ticketReply);
        $insert=$insert->InsertNewReply();
        if($insert==true){
            $arr=array(
                "Message"=>"Your reply has been posted",
                "Reply"=>$ticketReply,
                "date"=>date("Y-m-d H:i:s")
                );
            echo json_encode($arr);
        }
    }
    die;
}
add_action('wp_ajax_mensiopress_changePassword','mensiopress_changePassword' );
add_action('wp_ajax_nopriv_mensiopress_changePassword','mensiopress_changePassword' );
function mensiopress_changePassword(){
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
    $Result=array();
    $newPassword=false;
    if(!empty($_POST['NewPassword'])){
        $newPassword=$_POST['NewPassword'];
    }
    $check=new mensio_seller();
    $check->Set_UserName(stripslashes_deep($_SESSION['mnsUser']['UserName']));
    $check->Set_Password(stripslashes_deep(filter_var($_POST['CurrentPassword'])));
    $check->Set_IPAddress($_SERVER['REMOTE_ADDR']);
    $check=$check->CheckLoginCredentials();
    if(empty($_POST['CurrentPassword'])){
        if(empty($Result['Message']= get_option("MensioPress_TextPasswordChangeWrongCreds_".$_SESSION['MensioThemeLangShortcode']))){
            $Result['Message']="Wrong Credentials";
        }
        $Result['Code']="2";
    }
    elseif($check['Error']){
        if(empty($Result['Message']= get_option("MensioPress_TextPasswordChangeWrongCreds_".$_SESSION['MensioThemeLangShortcode']))){
            $Result['Message']="Wrong Credentials";
        }
        $Result['Code']="2";
    }
    else{
        $user_uuid=$_SESSION['mnsUser']['Credential'];
        $upd=new mensio_customers();
        $upd->Set_UUID($user_uuid);
        $upd->Set_Password($newPassword);
        if($upd->UpdateCustomerRecord()){
            if(empty($Result['Message']=get_option("MensioPress_TextPasswordChanged_".$_SESSION['MensioThemeLangShortcode']))){
                $Result['Message']= get_option("MensioPress_TextPasswordChanged_".$_SESSION['MensioThemeLangShortcode']);
            }
            $Result['Code']="1";
        }
        $Store=new mnsFrontEndObject();
        $Store=$Store->mnsFrontEndStoreData();
        $logo=get_site_url()."/".$Store['logo'];
        $from=$Store['email'];
        $logo='<img src="'.$logo.'" class="StoreLogo">';
        if(!$PasswordChangedText=get_option("MensioPress_TextPasswordChanged_".$_POST['mns_lang'])){
            $PasswordChangedText="Your Password has been reset";
        }
        $ar=array(
            "STORELOGO"=>$logo,
            "STORENAME"=>$Store['name'],
            "STOREMAIL"=>$Store['email'],
            "GENERALMAIL" =>$PasswordChangedText
        );
        $seller=new mensio_seller();
        if($seller->getMailTemplate("GeneralMail",$ar)){
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= 'From: '.get_bloginfo().' <'.$from.'>' . "\r\n";
            $message= stripslashes_deep($seller->getMailTemplate("GeneralMail",$ar));
            wp_mail(filter_var($_SESSION['mnsUser']['UserName']), "Password Change", $message,$headers);
        }
    }
    echo json_encode($Result);
    die;
}
