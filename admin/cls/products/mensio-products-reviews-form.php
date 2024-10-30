<?php
class Mensio_Admin_Products_Reviews_Form extends mensio_core_form {
  public function __construct() {
    if (!defined('WPINC')) { die(); }
    $this->Set_MainTemplate();
  }
  public function Load_Page_CSS() {
    wp_enqueue_style(
     MENSIO_PLGTITLE.'-productreviews',
     plugin_dir_url( __FILE__ ) . '../../css/mensio-products-reviews.css',
     array(),
     MENSIO_VERSION,
     'all'
    );
  }
  public function Load_Page_JS() {
    wp_enqueue_script(
     MENSIO_PLGTITLE.'-productreviews',
     plugin_dir_url( __FILE__ ) . '../../js/mensio-products-reviews.js',
     array( 'jquery' ),
     MENSIO_VERSION,
     false
    );
  }
  private function DisplayRating($RatingID,$Value) {
    $DsplRating = '';
    $Ratings = new mensio_products_ratings();
    if ($Ratings->Set_UUID($RatingID)) {
      $Data = $Ratings->GetRatingSystemData();
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) {
          if ( $Value <= 5) {
            for ($i = 1; $i <= $Value; ++$i) {
              $DsplRating .= '
                <div class="TBL_RatIcons">
                  <img src="'.$Row->icon.'">
                </div>';
            }
          } else {
            $DsplRating .= '
              <div class="TBL_RatIcons">
                <img src="'.$Row->icon.'">
              </div> <span class="TBL_RatTxt">... X'.$Value;
          }
        }
      }
    }
    unset($Ratings);
    return $DsplRating;
  }
  private function LoadReviewsDataSet($ProductID) {
    $Data = array();
    $DataSet = array();
    $Reviews = new mensio_products_reviews();
    if ($Reviews->Set_Product($ProductID)) {
      $Data = $Reviews->LoadProductReviewsDataSet();
    }
    unset($Reviews);
    if ((is_array($Data)) && (!empty($Data[0]))) {
      $i = 0;
      foreach ($Data as $Row) {
        $DataSet[$i]['uuid'] = $Row->uuid;
        $DataSet[$i]['Title'] = $Row->title;
        $DataSet[$i]['Product'] = $Row->ProdName;
        $DataSet[$i]['Customer'] = $Row->username;
        $DataSet[$i]['Rating'] = $this->DisplayRating($Row->rtype,$Row->rvalue);
        $DataSet[$i]['Created'] =  date("d/m/Y", strtotime($Row->created));
        $DataSet[$i]['Edited'] =  date("d/m/Y", strtotime($Row->changed));
        ++$i;
      }
    }
    return $DataSet;
  }
  private function GetModalDataTable($ProductID) {
    $DataSet = $this->LoadReviewsDataSet($ProductID);
    $RtrnTable = '<table id="TblProductReviews" class="ModalTable">
                    <thead>
                      <tr>
                        <th class="BtnCol"></th>
                        <th class="">Title</th>
                        <th class="CstrCol">Customer</th>
                        <th class="RtnCol">Rating</th> 
                        <th class="DateCol">Created</th> 
                        <th class="DateCol">Edited</th>
                      </tr>
                    </thead>
                    <tbody>';
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) {
        $RtrnTable .= '<tr>
                        <td class="BtnCol">
                          <div id="Del_'.$Row['uuid'].'" class="ESBtns BtnRed MdlDelBtn" title="Delete">
                            <i class="fa fa-times" aria-hidden="true"></i>
                          </div>
                          <div id="View_'.$Row['uuid'].'" class="ESBtns MdlViewBtn" title="View">
                            <i class="fa fa-eye" aria-hidden="true"></i>
                          </div>
                        </td>
                        <td class="">'.$Row['Title'].'</td>
                        <td class="CstrCol">'.$Row['Customer'].'</td>
                        <td class="RtnCol">'.$Row['Rating'].'</td> 
                        <td class="DateCol">'.$Row['Created'].'</td> 
                        <td class="DateCol">'.$Row['Edited'].'</td>
                      </tr>';
      }
    }
    $RtrnTable .= '</tbody></table>';
    return $RtrnTable;
  }
  public function LoadProductReviews($ProductID) {
    $MdlForm = '<div id="ReviewTableDiv" class="ReviewModalDivs">
                '.$this->GetModalDataTable($ProductID).'
              </div>
              <div id="ReviewViewDiv" class="ReviewModalDivs"></div>';
    return $this->CreateModalWindow('Product Reviews', $MdlForm);
  }
  public function LoadReviewData($RevID) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Review'=>'');
    $NoteType = '';
    $ProdData = '';
    $Reviews = new mensio_products_reviews();
    if (!$Reviews->Set_UUID($RevID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Review code not corrent<br>';
    } else {
      $Data = $Reviews->LoadReviewsData();
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) {
          $ProdData = $this->CreateProductDisplay($Row->product);
          $ReplyData = $this->CreateReplyDisplay($Row->uuid);
          $RevData = '<div class="ReviewDataDiv">
          '.$Row->username.'
          <div class="ReviewDataIcons">
            '.$this->DisplayRating($Row->rtype,$Row->rvalue).'
          </div>
          <div class="ReviewDataEdited">
            '.$Row->changed.'
          </div>
          <div class="DivResizer"></div>
        </div>
        <hr>
        <div>
          <h4>'.$Row->title.'</h4>
          '.$Row->notes.'
        </div>';
        }
        $RtrnData['Review'] = '
          <div class="DIV_ProductDataWrapper">
            '.$ProdData.'
          </div>
          <div id="DIV_ReviewsWrapper">
            <div class="DIV_Review">
              '.$RevData.'
            <div class="DivResizer"></div>
            </div>
            '.$ReplyData.'
          </div>
          <div class="button_row">
            <button id="BTN_Back" class="button" title="Back">
              <i class="fa fa-arrow-left"></i>
            </button> 
          </div>';
      }
    }
    unset($Reviews);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;
  }
  private function CreateProductDisplay($ProductID) {
    $ProdData = '';
    $Reviews = new mensio_products_reviews();
    if ($Reviews->Set_Product($ProductID)) {
      $Data = $Reviews->LoadReviewProductStats();
      $ProdData = '<div class="ProductName">
          '.$Data['Name'].'
        </div>
        <div class="DataSpansDivs">
          <span class="DataLbls"># of Reviews:</span><span class="DataSums">'.($Data['NoReviews'] + 0).'</span>
        <div class="DivResizer"></div>
        </div>
        <div class="DataSpansDivs">
          <span class="DataLbls">Average Rating:</span><span class="DataSums">'.($Data['AvgRating'] + 0).'</span>
        <div class="DivResizer"></div>
        </div>';
    }
    unset($Reviews);
    return $ProdData;
  }
  private function CreateReplyDisplay($RevID) {
    $Replies = '';
    $Reviews = new mensio_products_reviews();
    if ($Reviews->Set_UUID($RevID)) {
      $Data = $Reviews->LoadReviewReplies();
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) {
          $Replies .= '
            <div class="DIV_ReplayWrapper">
              <div class="ReviewDataDiv">
                '.$Row->username.'
                <div class="ReviewDataEdited">
                  '.$Row->changed.'
                </div>
              </div>
              <hr>
              <div>
                '.$Row->notes.'
              </div>
            <div class="DivResizer"></div>
            </div>';
        }
      }
    }
    unset($Reviews);
    return $Replies;
  }
  public function RemoveReviewData($RevID) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $Reviews = new mensio_products_reviews();
    if (!$Reviews->Set_UUID($RevID)) {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Review code not corrent<br>';
    } else {
      if ($Reviews->DeleteReviewRecord()) {
        $NoteType = 'Success';
        $RtrnData['ERROR'] = 'FALSE';
        $RtrnData['Message'] .= 'Review deleted successfully<br>';
      } else {
        $NoteType = 'Alert';
        $RtrnData['ERROR'] = 'TRUE';
        $RtrnData['Message'] .= 'Review could not be deleted<br>';
      }
    }
    unset($Reviews);
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    return $RtrnData;
  }
}