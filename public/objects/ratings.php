<?php
add_shortcode( 'mns_ratings', 'mensiopress_product_ratings' );
function mensiopress_product_ratings($atts){
    if(empty($atts['titlesize']) && !empty($GLOBALS['MensioPressFontSize'])){
        $atts['titlesize']=$GLOBALS['MensioPressFontSize'];
    }
    if(empty($atts['fontsize']) && !empty($GLOBALS['MensioPressFontSize'])){
        $atts['fontsize']=$GLOBALS['MensioPressFontSize'];
    }
    if(!empty($_GET['product'])){
        $prodID=$_GET['product'];
    }
    elseif(!empty($GLOBALS['UUID'])){
        $prodID= MensioEncodeUUID($GLOBALS['UUID']);
    }
    else{
        $prodID=0;
    }
    if(empty($atts) && empty($atts['reviewType'])){
    }
    $Type=MensioGetActiveReviewType();
    $maxStars=0;
    if(!empty($Type['max'])){
        $maxStars=$Type['max'];
    }
    $minStars=0;
    if(!empty($Type['min'])){
        $minStars=$Type['min'];
    }
    $stepStars=0;
    if(!empty($Type['step'])){
        $stepStars=$Type['step'];
    }
    $starIcon=0;
    if(!empty($Type['icon'])){
        $starIcon=$Type['icon'];
    }
    $title="Product Ratings";
    if(!empty($atts['title'])){
        $title=$atts['title'];
    }
    $fontSize="1";
    if(!empty($atts['titlesize'])){
        $fontSize=str_replace("-",".",$atts['titlesize']);
    }
    $html="<h2 style='font-size:".$fontSize."rem;' class='mensioObjectTitle'>".$title."</h2><hr class='titleLine' />";
                $CustomStyle="
                    .good{
                        height:30px;
                        width:30px;
                        background-image:url('". site_url()."/".$starIcon."') !important;
                        background-size: 100% 100%;
                        margin-left: 5px;
                        display:inline-block;
                    }
                    .not-good{
                        height:30px;
                        width:30px;
                        background-image:url('". site_url()."/".$starIcon."') !important;
                        opacity:0.5;
                        background-size: 100% 100%;
                        margin-left: 5px;
                        display:inline-block;
                    }
                    ";
                    wp_enqueue_style(
                        'MensioPressCustomListStyle',
                        plugin_dir_url(__FILE__) . '../css/mensio-public.css'
                    );
                    wp_add_inline_style( 'MensioPressCustomListStyle', $CustomStyle);
                    $seller=new mensio_seller();
                    $seller->Set_ProductID(MensioDecodeUUID($prodID));
                    $allReviews=$seller->AllProductReviews();
                    if($allReviews){
                        rsort($allReviews);
                        foreach($allReviews as $review){
                            $html.="
                                <br />
                            <div class='rating'>
                                <div class='rater' style='font-size:".$atts['fontsize']."rem;'>".$review['CustomerFirstName']." ".$review['CustomerLastName']."</div>
                                <div class='rating-title' style='font-size:".$atts['fontsize']."rem;'>".$review['title']."</div>
                                <div class='rating-text' style='font-size:".$atts['fontsize']."rem;'>".$review['text']."</div>
                                <div class='rating-stars' title='Reviews: ".$review['value']."'>";
                                for($o=1;$o<=$review['value'];$o++){
                                    $html.="
                                    <div class='good'></div>";
                                }
                                $html.="
                                </div>
                                <div class='whenPosted'>".$review['when']."</div>
                            </div>";
                        }
                    }
                    if(!empty($_SESSION['mnsUser']['Credential'])){
                    $html.="
                    <div class='MensioRateProduct' productid='".$prodID."'>
                        Title: <input type='text' name='ratingTitle' class='MensioNewRatingTitle' title='MensioNewRatingTitle'><br />
                        Text: <textarea class='MensioNewRating'></textarea><br />
                        <input type='button' value='Rate Now' class='MensioRateNow'>
                        <input type='range' class='rating-stars'>
                        <div style='' class='Ratings-Div'>
                            <div class='Ratings-range' maxprice='".$maxStars."' minprice='".$minStars."' step='".$stepStars."'></div>
                            </div>";
                        $CustomStyle="
                        div.mns-html-content .mns-ratings .Ratings-Div span.irs-slider.single {
                            background-image:url(". site_url()."/".$starIcon.") !important;
                            background-color:#fff;
                            background-size:100%;
                            background-repeat:no-repeat;
                            border-radius:100%;
                        }";
                        wp_enqueue_style(
                            'MensioPressCustomListStyle',
                            plugin_dir_url(__FILE__) . 'css/MensioPressCustomListStyle.css'
                        );
                        wp_add_inline_style( 'MensioPressCustomListStyle', $CustomStyle);
                        $CustomScript="
                        jQuery('.Ratings-range').ionRangeSlider({
                            min: ".$minStars.",
                            max: ".$maxStars.",
                            step: ".$stepStars.",
                            from: ".($maxStars/2).",
                            onChange: function (data) {
                                jQuery('input[type=range].rating-stars').val(data.from)
                            }
                        });
                        ";
                        $tt=rand(1,1000);
                        wp_enqueue_script("MensioPressPublicJS".$tt,plugin_dir_url(dirname(__FILE__))."js/empty.js");
                        wp_add_inline_script( "MensioPressPublicJS".$tt,
                        $CustomScript
                        );
                    $html.="
                    </div>
                    ";
                    }
                    else{
                        $Message="Please Login to post a rating";
                        if(!empty(get_option("MensioPress_TextLoginToRate_".$_SESSION['MensioThemeLangShortcode']))){
                            $Message=get_option("MensioPress_TextLoginToRate_".$_SESSION['MensioThemeLangShortcode']);
                        }
                        $LoginPage=false;
                        $Object=new mnsGetFrontEndLink();
                        $LoginPage=$Object->LoginLink();
                        $html.="
                        <div class='MensioRateProduct' productid='".$prodID."'>
                            <a href='".$LoginPage."'>
                                <i>".$Message."</i>
                            </a>
                        </div>";
                    }
                    if(empty($Type['max']) && empty($Type['min']) && empty($_GET['action'])){
                        $html=false;
                    }
    return $html;
}
add_action('wp_ajax_mensiopress_NewRating','mensiopress_NewRating' );
add_action('wp_ajax_nopriv_mensiopress_NewRating','mensiopress_NewRating' );
function mensiopress_NewRating(){
    if(!empty($_POST)){
        $url     = wp_get_referer();
        $url     = str_replace($_POST['mns_lang']."/","",$url);
        $ref     = url_to_postid( $url ); 
        $seller=new mensio_seller();
        $verification=$seller->VerifyPageIntegrity(filter_var($_POST['mns_sec']), "MensioPressFrontEnd-".$ref);
        if($verification==false){
            echo "0";
            die;
        }
    }
    if(!empty($_POST['mns_RatingStars']) && !empty($_POST['mns_Rating'])   ){
        $ratingStars=filter_var($_REQUEST['mns_RatingStars'])+1;
        $ratingText= stripslashes_deep(strip_tags(filter_var($_REQUEST['mns_Rating'])));
        $ratingType=filter_var($_REQUEST['mns_RatingType']);
        $ratingTitle=stripslashes_deep(filter_var($_REQUEST['mns_RatingTitle']));
        $product= MensioDecodeUUID(filter_var($_REQUEST['MensioProduct']));
        $reviewType=MensioGetActiveReviewType();
        $reviewType=$reviewType['id'];
            $getName=new mensio_seller();
            $name=$getName->GetCustomerData($_SESSION['mnsUser']['Credential'],"firstname");
            $getName=new mensio_seller();
            $name.=" ".$getName->GetCustomerData($_SESSION['mnsUser']['Credential'],"lastname");
        $newReview=new mensio_seller();
        $newReview->Set_ProductID($product);
        $newReview->Set_Customer($_SESSION['mnsUser']['Credential']);
        $newReview->Set_ReviewType($reviewType);
        $newReview->Set_ReviewTitle($ratingTitle);
        $newReview->Set_ReviewText($ratingText);
        $newReview->Set_ReviewValue($ratingStars);
        $newReview=$newReview->NewReview();
        if($newReview){
            $Results=array(
                "text"=>$ratingText,
                "title"=>$ratingTitle,
                "stars"=>$ratingStars,
                "when"=>date("Y-m-d H:i:s"),
                "name"=>$name,
            );
            echo json_encode($Results);
        }
        else{
            echo "0";
        }
    }
    die;
}
