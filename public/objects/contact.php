<?php
add_shortcode( 'mns_contact', 'mensiopress_contact' );
function mensiopress_contact($atts){
    global $post;
    $MensioPressScript= '
        var ajaxurl="'.admin_url('admin-ajax.php').'";
        var post_id="'.$post->ID.'";';
    $tt=rand(1,1000);
    wp_enqueue_script("MensioPressPublicJS".$tt,plugin_dir_url(dirname(__FILE__))."js/empty.js");
    wp_add_inline_script("MensioPressPublicJS".$tt, $MensioPressScript);
    $html=false;
    if(empty($atts['titlesize']) && !empty($GLOBALS['MensioPressFontSize'])){
        $atts['titlesize']=$GLOBALS['MensioPressFontSize'];
    }
    if(empty($atts['fontsize']) && !empty($GLOBALS['MensioPressFontSize'])){
        $atts['fontsize']=$GLOBALS['MensioPressFontSize'];
    }
    if(!empty($atts['title'])){
        $title=$atts['title'];
    }
    else{
        $title="Contact";
    }
    if(!empty($atts['titlesize'])){
        $fontSize=str_replace("-",".",$atts['titlesize']);
    }
    else{
        $fontSize="1";
    }
        $atts['fontsize']=str_replace("-",".",$atts['fontsize']);
    $html.="<div style='font-size:".$atts['fontsize']."rem;'>";
    $html.="<h2 style='font-size:".$fontSize."rem;' class='mensioObjectTitle'>".$title."</h2><hr class='titleLine' />";
    $store= new mnsFrontEndObject();
    $store= $store->mnsFrontEndStoreData();
    $html.= '<div class="article-body">';
    $html.=$store['map'];
    $html.='
    <div class="contact-result"></div>
            <div class="contact-table">
                <div class="contact-form contact-inputs">
                    <div class="form-error">Please fill in all the required fields</div>
                    ';
                    if(!empty($atts['contact_inputs'])){
                        $contact_inputs=explode(",",$atts['contact_inputs']);
                        if(count($contact_inputs)>0){
                            foreach($contact_inputs as $key){
                                $inputName=$key;
                                if(($key=='message') || ($key==" ") || ($key==false)){
                                    continue;
                                }
                                if($key=="email"){$has_email=1;}
                                        if(get_option('MensioPress_Text'.ucfirst($key).'_'.$_SESSION['MensioThemeLangShortcode'])){
                                            $key=ucfirst(get_option('MensioPress_Text'.ucfirst($key).'_'.$_SESSION['MensioThemeLangShortcode']));
                                        }
                                        else{
                                            $key=ucfirst($key);
                                        }
                                $html.='
                            <div class="form-input">'.ucfirst($key).'</div>
                            <div class="form-input"><input type="text" name="'.$inputName.'" placeholder="'.ucfirst($key).'"></div>';
                            }
                        }
                    }
                    $key=ucfirst(get_option('MensioPress_TextMessage_'.$_SESSION['MensioThemeLangShortcode']));
                    if(empty($key)){
                        $key="Message";
                    }
                    $html.='
                    <div class="form-input">'.$key.'</div>
                    <div class="form-input"><textarea placeholder="'.$key.'" name="message"></textarea></div>';
                    if(!empty($has_email)){
                        if(get_option('MensioPress_TextSendCopy_'.$_SESSION['MensioThemeLangShortcode'])){
                            $key=ucfirst(get_option('MensioPress_TextSendCopy_'.$_SESSION['MensioThemeLangShortcode']));
                        }
                        else{
                            $key="Send Copy to myself";
                        }
                        $html.='
                        <div class="form-input"><label><input type="checkbox" name="mns-send-copy" value="1"> '.$key.'</label></div>';
                    }
                        if(get_option('MensioPress_TextSend_'.$_SESSION['MensioThemeLangShortcode'])){
                            $key=ucfirst(get_option('MensioPress_TextSend_'.$_SESSION['MensioThemeLangShortcode']));
                        }
                        else{
                            $key="Send";
                        }
                    $html.= '
                    <div class="form-input"><input type="button" value="'.$key.'" class="submit-button"></div>  
                </div>
                <div class="contact-form">
                            <div class="contact-info" style="font-size:'.$atts['fontsize'].'rem;">
                                '.$store['address'].' '.$store['number'].', '.$store['city'].' '.$store['country'].'<br />';
                                if($store['phone']){$html.='<div><i class="fa fa-phone" title="Phone: '.$store['phone'].'"></i> <span><a href="tel:'.$store['phone'].'">'.$store['phone'].'</a></span></div>';}
                                if($store['fax']){$html.='<div><i class="fa fa-fax" title="Fax: '.$store['fax'].'"></i> <span>'.$store['fax'].'</span></div>';}
                                if($store['email']){$html.='<div><i class="fa fa-at" title="email: '.$store['email'].'"></i> <span><a href="mailto:'.$store['email'].'">'.$store['email'].'</a></span></div>';}
                                $html.='
                            </div>
                    </div>
                </div>
            </div>';
    $html.="</div>";
    return $html;
}
add_action('wp_ajax_mensiopress_contact_post','mensiopress_contact_post' );
add_action('wp_ajax_nopriv_mensiopress_contact_post','mensiopress_contact_post' );
function mensiopress_contact_post(){
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
    $subject = "Contact Email";
    $Store=new mnsFrontEndObject();
    $Store=$Store->mnsFrontEndStoreData();
    $to = $Store['email'];
    if(!empty($_REQUEST['mns-send-copy']) && $_REQUEST['mns-send-copy']==1){
        $to.=",".filter_var($_REQUEST['mns_email']);
    }
    $message = "
    <html>
    <head>
    <title>Contact email</title>
    </head>
    <body>
        <table align='center'>
            <tr>
                <td colspan='2'><img src='".$Store['logo']."' height='150'></td>
            </tr>";
        if(isset($_REQUEST['mns_name'])){
        $message.="
            <tr>
                <td>Name:</td>
                <td>".stripslashes_deep(filter_var($_REQUEST['mns_name']))."</td>
            </tr>";
        }
        if(isset($_REQUEST['mns_lastname'])){
        $message.="
            <tr>
                <td>Lastname:</td>
                <td>".stripslashes_deep(filter_var($_REQUEST['mns_lastname']))."</td>
            </tr>";
        }
        if(isset($_REQUEST['mns_email'])){
        $message.="
            <tr>
                <td>Email:</td>
                <td>".stripslashes_deep(filter_var($_REQUEST['mns_email']))."</td>
            </tr>";
        }
        if(isset($_REQUEST['mns_phone'])){
        $message.="
            <tr>
                <td>Phone:</td>
                <td>".stripslashes_deep(filter_var($_REQUEST['mns_phone']))."</td>
            </tr>";
        }
        if(isset($_REQUEST['mns_cellphone'])){
        $message.="
            <tr>
                <td>Cellphone:</td>
                <td>".stripslashes_deep(filter_var($_REQUEST['mns_cellphone']))."</td>
            </tr>";
        }
        if(isset($_REQUEST['mns_message'])){
        $message.="
            <tr>
                <td>Message:</td>
                <td>".stripslashes_deep(filter_var($_REQUEST['mns_message']))."</td>
            </tr>";
        }
            $message.= "
        </table>
    </body>
    </html>
    ";
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    if(empty($_POST)){
        $result=false;
    }
    else{
        $result=wp_mail( $to, $subject, $message, $headers);
        $result="Message Sent";
    }
    echo $result;
    die;
}
