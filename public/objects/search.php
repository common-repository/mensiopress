<?php
add_shortcode( 'mns_search', 'mnsSearchBox' );
function mnsSearchBox($atts){
    $html=false;
    if(empty($atts['titlesize']) && !empty($GLOBALS['MensioPressFontSize'])){
        $atts['titlesize']=$GLOBALS['MensioPressFontSize'];
    }
    if(empty($atts['fontsize']) && !empty($GLOBALS['MensioPressFontSize'])){
        $atts['fontsize']=$GLOBALS['MensioPressFontSize'];
    }
    $title='Search';
    if(!empty($atts['title-'.$_SESSION['MensioThemeLangShortcode']])){
        $title=$atts['title-'.$_SESSION['MensioThemeLangShortcode']];
    }
    $getLink=new mnsGetFrontEndLink();
    $SearchLink=$getLink->SearchPage();
    $SearchLinkID=$getLink->SearchPage("theID");
    $html.= '
    <form id="mnsSearch" action="'.$SearchLink.'" method="get">
        <div>
            <div id="mnsSearch-error"></div>
            <input type="text" name="mns_keyword" placeholder="'.$title.'" id="mnsSearch-text"';
            if(!empty($_REQUEST['mns_keyword'])){
                $html.=' value="'.$_REQUEST['mns_keyword'].'"';
            }
        $html.='>';
            if (! get_option('permalink_structure') ){
                $html.='
                <input type="hidden" name="page_id" value="'.$SearchLinkID.'">';
            }
            $html.='
            <input type="hidden" name="Page" value="1">
            <input type="hidden" name="Items" value="5">
            <input type="submit" value="'.$title.'" id="mnsSearch-button">
        </div>
    </form>
    ';
    if(!empty($_REQUEST['mns_keyword']) || (!empty($_GET['action']))){
        if(!empty($_GET['action'])){
            $keyword="*";
            $atts['maxproducts']=5;
            $Limit=" LIMIT 0,1";
        }
        else{
            $keyword=stripslashes_deep(filter_var($_REQUEST['mns_keyword']));
            $Limit=false;
        }
        $Items=5;
        if(!empty($_REQUEST['Items'])){
            $Items=$_REQUEST['Items'];
        }
        $atts["maxproducts"]=$Items;
        $SearchResults=mensiopress_Search($keyword,$Limit);
        $Results=MensioList($SearchResults, $atts, "", false);
        if(empty($Results)){
            $Results="<center>No Products Found</center>";
        }
        $pagination="
        <div class='Pagination'>";
                        $checked=false;
                        if(!empty($_REQUEST['Items']) && $_REQUEST['Items']=="5"){
                            $checked=" active";
                        }
                        $pagination.= "<a href='//".$_SERVER['HTTP_HOST']. preg_replace("#&Items=.*#", '', $_SERVER['REQUEST_URI'])."&Items=5'><span class='ItemShow".$checked."'>5</span></a>";
                        $checked=false;
                        if(empty($_REQUEST['Items']) || (!empty($_REQUEST['Items']) && $_REQUEST['Items']=="10")){
                            $checked=" active";
                        }
                        $pagination.= "<a href='//".$_SERVER['HTTP_HOST']. preg_replace("#&Items=.*#", '', $_SERVER['REQUEST_URI'])."&Items=10'><span class='ItemShow".$checked."'>10</span></a>";
                        $checked=false;
                        if(!empty($_REQUEST['Items']) && $_REQUEST['Items']=="20"){
                            $checked=" active";
                        }
                        $pagination.= "<a href='//".$_SERVER['HTTP_HOST']. preg_replace("#&Items=.*#", '', $_SERVER['REQUEST_URI'])."&Items=20'><span class='ItemShow".$checked."'>20</span></a>";
                        $checked=false;
                        $previousPage=1;
                        if(!empty($_REQUEST['Page']) && $_REQUEST['Page']>1){
                            $previousPage=$_REQUEST['Page']-1;
                        }
                        $nextPage=1;
                        if(!empty($_REQUEST['Page']) && $_REQUEST['Page']<count($SearchResults)){
                            $nextPage=$_REQUEST['Page']+1;
                        }
                        $pagination.= "<ul class='pagination'>"
                        . "<a href='//".$_SERVER['HTTP_HOST']. preg_replace("#&Page=.*#", '', $_SERVER['REQUEST_URI'])."&Page=".
                                $previousPage
                                ."'><li><i class='fas fa-angle-double-left'></i></li></a>";
                        $maxPages=ceil(count($SearchResults)/$Items);
                        $active=false;
                        for($i=1;$i<=$maxPages;$i++){
                            $style=false;
                            $checked=false;
                            if(empty($style) || $i>($active+5)){
                            }
                            if((!empty($_REQUEST['Page']) && $i==$_REQUEST['Page']) ||
                             (empty($_REQUEST['Page']) && $i==1)){
                                $checked=" class='active'";
                                $style=" style='display:inline-block;'";
                                $active=$i;
                                $start=(($i-1)*$Items);
                            }
                            if(!empty($_REQUEST['Page']) && $i>=($_REQUEST['Page']-2) && $active==false){
                                $style=" class='showPagination'";
                            }
                            elseif(!empty($_REQUEST['Page']) && $i<=($_REQUEST['Page']+2) && $active==true){
                                $style=" class='showPagination'";
                            }
                            $pagination.="<a href='//".$_SERVER['HTTP_HOST']. preg_replace("#&Page=.*#", '', $_SERVER['REQUEST_URI'])."&Page=".$i."'".$style."><li".$checked.$style.">".$i."</li></a>";
                        }
                    $pagination.="<a href='//".$_SERVER['HTTP_HOST']. preg_replace("#&Page=.*#", '', $_SERVER['REQUEST_URI'])."&Page=".
                                $nextPage
                                ."'><li><i class='fas fa-angle-double-right'></i></li></a>"
                            . "</ul>"
                            . "</div>";
        $html.='
        <div id="mnsSearchResultsBox">
            <div id="mnsSearchResults" class="mns-list mns-category-products">
            '.$Results.$pagination.'
            </div>
        </div>';
    }
    return $html;
}
add_action('wp_ajax_mensiopress_Search','mensiopress_Search' );
add_action('wp_ajax_nopriv_mensiopress_Search','mensiopress_Search' );
function mensiopress_Search($Keyword,$Limit=false){
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
    if(empty($Keyword)){
        die;
    }
    $html=false;
    $search=new mensio_seller();
    $search->Set_SearchString($Keyword);
    $results=$search->GetSearchResults($Limit);
    $i=0;
    if($results['Error']==false){
        $count=count($results['Data']['Products']);
        $count=$count+count($results['Data']['Tags']);
        $count=$count+count($results['Data']['Categories']);
        $count=$count+count($results['Data']['Brands']);
        $List=array();
        $html.=$count." results ";
        foreach($results['Data'] as $key=>$val){
            foreach($results['Data'][$key] as $product){
                $productFirstPrice=false;
                $productFinalPrice=false;
                $Product=new mnsFrontEndObject();
                $Product=$Product->mnsFrontEndProduct($product->uuid);
                $link=$Product['link'];
                $html.= '<a href="'.$link.'" class="search-result">'
                        . '<div class="result-image">'
                            . '<img src="'.get_site_url()."/".$product->MainImage.'"></div>'
                        . '<div class="result-title">'.$product->ProductName."</div></a>";
                            $List[$i]['id']=$Product['id'];
                            $List[$i]['name']=$Product['name'];
                            if(!empty($Product['description'])){
                                $List[$i]['description']=$Product['description'];
                            }
                            $List[$i]['sku']=$Product['sku'];
                            if(!empty($Product['advantages'][$_SESSION['MensioThemeLang']])){
                                $k=0;
                                $List[$i]['advantages']=array();
                                foreach($Product['advantages'][$_SESSION['MensioThemeLang']] as $adv){
                                    $List[$i]['advantages'][$k]=$adv;
                                    $k++;
                                }
                            }
                            $List[$i]['price']=$Product['price'];
                            $List[$i]['tax']=$Product['tax'];
                            $List[$i]['link']=$Product['link'];
                            $List[$i]['btbtax']=$Product['btbtax'];
                            $List[$i]['discount']=$Product['discount'];
                            foreach($Product['images'] as $key=>$img){
                                $List[$i]['image']=$img['thumb'];
                            }
                            if(!empty($Product['barcodes'])){
                                $List[$i]['barcodes']=$Product['barcodes'];
                            }
                            $List[$i]['brand']=$Product['brand'];
                            $List[$i]['reviews']=$Product['reviews'];
                            $List[$i]['availability']=$Product['availability'];
                            $List[$i]['availability-color']=$Product['availability-color'];
                            $List[$i]['availability-icon']=$Product['availability-icon'];
                    $i++;
                unset($cont,$matched);
            }
        }
    }
    return $List;
    return $html;
    die;
}