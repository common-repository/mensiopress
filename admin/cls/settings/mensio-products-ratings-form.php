<?php
class Mensio_Admin_Products_Ratings_Form extends mensio_core_form {
  public function __construct() {
    if (!defined('WPINC')) { die(); }
    $this->Set_MainTemplate();
    $this->ActivePage = 'Review_Ratings';
  }
  public function Load_Page_CSS($Deleted=false) {
    wp_enqueue_style(
     MENSIO_PLGTITLE.'-productratings',
     plugin_dir_url( __FILE__ ) . '../../css/mensio-products-brands.css',
     array(),
     MENSIO_VERSION,
     'all'
    );
  }
  public function Load_Page_JS() {
    wp_enqueue_script(
     MENSIO_PLGTITLE.'-productratings',
     plugin_dir_url( __FILE__ ) . '../../js/mensio-products-ratings.js',
     array( 'jquery' ),
     MENSIO_VERSION,
     false
    );
  }
  private function LoadRatingsDataSet($InSearch,$InSorter) {
    $Error = false;
    $DataSet = array();
    $Ratings = new mensio_products_ratings();
    if ($InSearch !== '') {
      if (!$Ratings->Set_Name($InSearch)) { $Error = true; }
    }
    if (!$Ratings->Set_Sorter($InSorter)) { $Error = true; }
    if (!$Error) {
      $DataSet = $Ratings->LoadProductRatingsDataSet();
    }
    unset($Ratings);
    return $DataSet;
  }
  public function GetDataTable($InPage=1,$InRows=0,$InSorter='name',$InSearch='') {
    $RtrnTable = '';
    if (($InRows === 0) || ($InRows === '')) { $InRows = $this->LoadTableDefaultRows(); }
    $tbl = new mensio_datatable();
    $tbl->Set_Sorter($InSorter);
    $DataSet = $this->LoadRatingsDataSet($InSearch,$InSorter);
    $tbl->Set_ActivePage($InPage);
    $tbl->Set_ActiveRows($InRows);
    $tbl->Set_BulkActions(array(
     'DEL'=>'Delete'
    ));
    $tbl->Set_EditColumn('name');
    $tbl->Set_EditOptionsSubline(array(
      'Edit','Delete'
    ));
    $tbl->Set_Columns(array(
      'uuid:uuid:plain-text',
      'name:Name:plain-text',
      'min:Min:small',
      'max:Max:small',
      'step:Step:small',
      'start:Start:small',
      'icon:Icon:img',
      'active:Active:input-checkbox'
    ));
    $RtrnTable = $tbl->CreateTable(
      'Ratings',
      $DataSet,
      array('uuid','name','min','max','start','step','icon','active')
    );
    unset($tbl);    
    return $RtrnTable;
  }
  public function LoadProductRatingSystemData($RatingID) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Rating'=>'','Name'=>'',
        'MinVal'=>'','MaxVal'=>'','Step'=>'','Start'=>'','Icon'=>'','Active'=>'');
    $NoteType = '';
    $Ratings = new mensio_products_ratings();
    if (!$Ratings->Set_UUID($RatingID)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] = 'Rating Code was not correct<br>';
    } else {
      $DataSet = $Ratings->GetRatingSystemData();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $RtrnData['Rating'] = $Row->uuid;
          $RtrnData['Name'] = $Row->name;
          $RtrnData['MinVal'] = $Row->min;
          $RtrnData['MaxVal'] = $Row->max;
          $RtrnData['Step'] = $Row->step;
          $RtrnData['Start'] = $Row->start;
          $RtrnData['Icon'] =  get_site_url().'/'.$Row->icon;
          $RtrnData['Active'] = $Row->active;
        }
      }
    }
    unset($Ratings);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;    
  }
  public function UpdateProductRatingSystemData($RatingID,$Data) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Rating'=>'');
    $NewRating = false;
    $NoteType = '';
    $Ratings = new mensio_products_ratings();
    if ($RatingID === 'NewRating') {
      $RatingID = $Ratings->GetNewRatingID();
      $NewRating = true;
    }
    if ($Ratings->Set_UUID($RatingID)) {
      $Data = stripslashes($Data);
      $Data = json_decode($Data, true);
      if (is_array($Data)) {
        foreach ($Data as $DataRow) {
          if (substr($DataRow['Field'],0,4) === 'FLD_') {
            $SetValue = $this->FindSetFun($DataRow['Field']);
            if ($SetValue !== '') {
              if (!$Ratings->$SetValue($DataRow['Value'])) {
                $RtrnData['ERROR'] = 'TRUE';
                $NoteType = 'Alert';
                $Lbl = str_replace('FLD_','',$DataRow['Field']);
                $RtrnData['Message'] .= 'Value "'.$DataRow['Value'].'" of the field '.$Lbl.' is not correct<br>';
              }
            }
          }
        }
        if ($RtrnData['ERROR'] === 'FALSE') {
          if ($Ratings->CheckMinMaxValues()) {
            if ($NewRating) {
              if (!$Ratings->InsertRatingSystem()) {
                $RtrnData['ERROR'] = 'TRUE';
                $NoteType = 'Alert';
                $RtrnData['Message'] .= 'Rating could not be saved<br>';
              }
            } else {
              if (!$Ratings->UpdateRatingSystem()) {
                $RtrnData['ERROR'] = 'TRUE';
                $NoteType = 'Alert';
                $RtrnData['Message'] .= 'Rating could not be updated<br>';
              }
            }
          } else {
            $RtrnData['ERROR'] = 'TRUE';
            $NoteType = 'Alert';
            $RtrnData['Message'] .= 'Min and Max values not correct<br>';
          }
        }
      }
    } else {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Problem with Rating ID<br>';
    }
    unset($Ratings);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $NoteType = 'Success';
      $RtrnData['Message'] = 'Data Saved Successfully';
      $RtrnData['Rating'] = $RatingID;
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;
  } 
  private function FindSetFun($Field) {
    $SetFun = '';
    switch ($Field) {
      case 'FLD_Name':
        $SetFun = 'Set_Name';
        break;
      case 'FLD_Min':
        $SetFun = 'Set_Min';
        break;
      case 'FLD_Max':
        $SetFun = 'Set_Max';
        break;
      case 'FLD_Step':
        $SetFun = 'Set_Step';
        break;
      case 'FLD_Start':
        $SetFun = 'Set_Start';
        break;
      case 'FLD_Icon':
        $SetFun = 'Set_Icon';
        break;
      case 'FLD_Active':
        $SetFun = 'Set_Active';
        break;
    }
    return $SetFun;
  }
  public function DeleteProductRatingSystemData($RatingID,$Notif=true) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NewRating = false;
    $NoteType = '';
    $Ratings = new mensio_products_ratings();
    if ($Ratings->Set_UUID($RatingID)) {
      if ($Ratings->RatingIsNiUse()) {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Info';
        $RtrnData['Message'] .= 'The rating system is in use. Can not delete<br>';
      } else {
        if (!$Ratings->DeleteRatingSystem()) {
          $RtrnData['ERROR'] = 'TRUE';
          $NoteType = 'Alert';
          $RtrnData['Message'] .= 'Rating could not be deleted<br>';
        }
      }
    } else {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Problem with Rating ID<br>';
    }
    unset($Ratings);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $NoteType = 'Success';
      $RtrnData['Message'] = 'Data deleted Successfully';
    }
    if ($Notif) {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    }
    return $RtrnData;
  }
  public function DeleteProductRatingSelections($Selections) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');    
    $NoteType = 'Success';
    $Selections = explode(';',$Selections);
    if (is_array($Selections)) {
      foreach ($Selections as $Row) {
        if ($Row !== '') {
          $Data = $this->LoadProductRatingSystemData($Row);
          $Answer = $this->DeleteProductRatingSystemData($Row,false);
          $RtrnData['ERROR'] = $Answer['ERROR'];
          $RtrnData['Message'] .= str_replace('Rating',$Data['Name'],$Answer['Message']).'<br>';
        }
      }
    }
    if ($RtrnData['ERROR'] !== 'FALSE') { $NoteType = 'Info'; }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;
  }
  public function UpdateActiveRatingSystem($CourierID,$Active) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $Ratings = new mensio_products_ratings();
    if (!$Ratings->Set_UUID($CourierID)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Problem with Rating ID<br>';
    } else {
      $Ratings->Set_Active($Active);
      if (!$Ratings->UpdateActiveRating()) {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] .= 'Active Rating Could Not Be Updated<br>';
      }
    }
    unset($Ratings);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $NoteType = 'Success';
      $RtrnData['Message'] .= 'Active Rating Updated Successfully<br>';
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;
  }
}