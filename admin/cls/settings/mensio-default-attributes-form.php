<?php
class Mensio_Admin_Products_Default_Attributes_Form extends mensio_core_form {
  public function __construct() {
    if (!defined('WPINC')) { die(); }
    $this->Set_MainTemplate();
    $this->ActivePage = 'Global_Attributes';
  }
  public function Load_Page_CSS() {
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_style(
     MENSIO_PLGTITLE.'-dfltattr',
     plugin_dir_url( __FILE__ ) . '../../css/mensio-products-default-attributes.css',
     array(),
     MENSIO_VERSION,
     'all'
    );
  }
  public function Load_Page_JS() {
    wp_enqueue_script( 'wp-color-picker' );
    wp_enqueue_script(
     MENSIO_PLGTITLE.'-dfltattr',
     plugin_dir_url( __FILE__ ) . '../../js/mensio-products-default-attributes.js',
     array( 'jquery' ),
     MENSIO_VERSION,
     false
    );
  }
  private function LoadDefaultAttributesDataSet($InSearch,$InSorter) {
    $Error = false;
    $DataSet = array();
    $DefAttr = new mensio_products_default_attributes();
    if ($InSearch !== '') {
      if (!$DefAttr->Set_SearchString($InSearch)) { $Error = true; }
    }
    if (!$DefAttr->Set_Sorter($InSorter)) { $Error = true; }
    if (!$Error) {
      $DataSet = $DefAttr->LoadDefaultAttributesDataSet();
    }
    unset($DefAttr);
    return $DataSet;
  }
  public function GetDataTable($InPage=1,$InRows=0,$InSorter='name',$InSearch='') {
    $RtrnTable = '';
    if (($InRows === 0) || ($InRows === '')) { $InRows = $this->LoadTableDefaultRows(); }
    $tbl = new mensio_datatable();
    $tbl->Set_Sorter($InSorter);
    $DataSet = $this->LoadDefaultAttributesDataSet($InSearch,$InSorter);
    $tbl->Set_ActivePage($InPage);
    $tbl->Set_ActiveRows($InRows);
    $tbl->Set_BulkActions(array(
     'VIS'=>'Toggle Visible'
    ));
    $tbl->Set_EditColumn('name');
    $tbl->Set_EditOptionsSubline(array(
      'Visible','Edit','Translate'
    ));
    $tbl->Set_Columns(array(
      'uuid:uuid:plain-text',
      'category:category:hidden',
      'name:Attribute:plain-text',
      'visibility:Visible:input-checkbox'
    ));
    $RtrnTable = $tbl->CreateTable(
      'DefaultAttributes',
      $DataSet,
      array('uuid','category','name','visibility')
    );
    unset($tbl);    
    return $RtrnTable;
  }
  public function LoadAttributeData($AttributeID) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Attribute'=>'','Name'=>'',
        'Visibility'=>'','InputControl'=>'','ValueList'=>'');
    $NoteType = '';
    $Attributes = new mensio_products_default_attributes();
    if (!$Attributes->Set_UUID($AttributeID)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Attribute code was not correct<br>';
    } else {
      $DataSet = $Attributes->LoadAttributeData();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        foreach ($DataSet as $Row) {
          $RtrnData['Attribute'] = $Row->uuid;
          $RtrnData['Name'] = $Row->name;
          $RtrnData['Type'] = $this->GetGLAttrType($Row->name);
          $RtrnData['Visibility'] = $Row->visibility;
          $ValList = $this->LoadAttributeValueList($AttributeID);
          $RtrnData['InputControl'] = $this->LoadTypeInputControl($RtrnData['Name'],$RtrnData['Type']);
          $RtrnData['ValueList'] = $ValList['ValueList'];
        }
      }
    }
    unset($Attributes);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;
  }
  private function GetGLAttrType($Attr) {
    $Type = '';
    $Name = '';
    $Metric = '';
    $Store = new mensio_store();
    $Data = $Store->LoadStoreData();
    if ((is_array($Data)) && (!empty($Data[0]))) {
      foreach ($Data as $Row) {
        $Metric = $Row->metrics;
      }
      $Metric = explode(';',$Metric);
      foreach ($Metric as $Row) {
        $Row = explode(':',$Row);
        if ($Attr === $Row[0]) { $Type = $Row[1]; }
      }
      switch ($Type) {
        case 'TXT':
          $Name = 'Text';
          break;
        case 'HEX':
          $Name = 'Color';
          break;
        case 'RGB':
          $Name = 'Color';
          break;
        case 'IMG':
          $Name = 'Image';
          break;
        case 'NUM':
          $Name = 'Number';
          break;
        case 'GAL':
          $Name = 'Gallon';
          break;
        case 'LTR':
          $Name = 'Litre';
          break;
        case 'CMT':
          $Name = 'Cubic Meter';
          break;
        case 'CFT':
          $Name = 'Cubic Foot';
          break;
        case 'MMT':
          $Name = 'Millimeter';
          break;
        case 'CMT':
          $Name = 'Centimeter';
          break;
        case 'MTR':
          $Name = 'Meter';
          break;
        case 'INC':
          $Name = 'Inch';
          break;
        case 'FOT':
          $Name = 'Foot';
          break;
        case 'YRD':
          $Name = 'Yard';
          break;
        case 'KLG':
          $Name = 'Kilogram';
          break;
        case 'GRM':
          $Name = 'Gram';
          break;
      }
    }
    unset($Store);
    return $Name;
  }
  public function LoadTypeInputControl($Name,$Type) {
    $InCtrl = $Name.' '.$Type;
    switch ($Type) {
      case 'Text':
        $InCtrl = '<label class="label_symbol">Values</label><br>
              <input type="text" id="FLD_AttrValue" value="" class="form-control">';
        break;
      case 'Color':
        $InCtrl = '<label class="label_symbol">Color Name</label><br>
              <input type="text" id="FLD_ColorName" value="" class="form-control">
              <label class="label_symbol">Color Selection</label><br>
              <div class="RGBDiv">
                <label class="label_symbol">R: <span id="R" class="RGBNum"></span></label>
                <label class="label_symbol">G: <span id="G" class="RGBNum"></span></label>
                <label class="label_symbol">B: <span id="B" class="RGBNum"></span></label>
              </div>
              <input id="FLD_Color_Hex">';
        break;
      case 'Image':
        $InCtrl = '<label class="label_symbol">Color Name</label><br>
              <input type="text" id="FLD_ColorName" value="" class="form-control">
              <div class="DIV_Cur_Img">
                <label class="label_symbol">Color Image</label>
                <div class="Mns_Img_Container">
                  <img id="DispImg" class="selectIm" src="">
                </div>
                <div class="">
                  <button id="Btn_OpenMediaModal" class="button Mns_Img_Btn" title="Open Image Selector">
                    <i class="fa fa-picture-o" aria-hidden="true"></i>
                  </button>
                  <button id="Btn_ClearImg" class="button Mns_Img_Btn" title="Clear Image">
                    <i class="fa fa-trash" aria-hidden="true"></i>
                  </button>
                  <input id="FLD_Logo" class="StoreBasicData" value="" type="hidden">
                </div>
              </div>';
        break;
      default:
        $InCtrl = '
              <div id="SingleDiv">
                <label class="label_symbol">Single Value Entry</label><br>
                <input type="number" id="FLD_AttrValue" value="" class="form-control">
              </div>
              <div id="MultipleDiv">
                <label class="label_symbol">Multiple Value Entry</label><br>
                <hr>
                <label class="label_symbol">Min Value</label><br>
                <input type="number" id="FLD_MinVal" class="form-control" min="0">
                <label class="label_symbol">Max Value</label><br>
                <input type="number" id="FLD_MaxVal" class="form-control" min="1">
                <label class="label_symbol">Step</label><br>
                <input type="number" id="FLD_Step" class="form-control" min="0.000001">
              </div>
              <div class="NumInputType">
                <input type="hidden" id="ActiveEntryType" value="Single">
                <button id="BTN_SingleVal" class="CatLstBtn">Single</button>
                <button id="BTN_MultipleVal" class="CatLstBtn">Multiple</button>
                <div class="DivResizer"></div>
              </div>';
        break;
    }
    $InCtrl .= '<div class="DivResizer"></div>';
    return $InCtrl;
  }
  public function LoadAttributeValueList($AttributeID) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'', 'ValueList'=>'');
    $NoteType = '';
    $Attributes = new mensio_products_default_attributes();
    if ($Attributes->Set_UUID($AttributeID)) {
      $Data = $Attributes->GetAttributeValues();
      if ((is_array($Data)) && (!empty($Data[0]))) {
        foreach ($Data as $Row) {
          if (substr($Row->value, 0, 5) === 'Name:') {
            $ClrData = explode(';',$Row->value);
            switch (count($ClrData)) {
              case 5:
                $ClrData[0] = str_replace('Name:', '', $ClrData[0]);
                $ClrData[1] = str_replace('Hex:', '', $ClrData[1]);
                $ClrData[2] = str_replace('R:', '', $ClrData[2]);
                $ClrData[3] = str_replace('G:', '', $ClrData[3]);
                $ClrData[4] = str_replace('B:', '', $ClrData[4]);
                $idval = $ClrData[0].';'.$ClrData[1].';'.$ClrData[2].';'.$ClrData[3].';'.$ClrData[4];
                $RtrnData['ValueList'] .= '
                <div class="RgnTypeBtn ClrBtn">
                  <div class="ColorDspl" style="background:'.$ClrData[1].';"></div>
                  <div class="ColorData">
                    <span id="'.$Row->uuid.'">'.$ClrData[0].'</span><br>
                    HEX: '.$ClrData[1].' // RGB: '.$ClrData[2].','.$ClrData[3].','.$ClrData[4].'
                  </div>
                  <input type="hidden" id="CLR_'.$Row->uuid.'" value="'.$idval.'">
                  <div id="DEL_'.$Row->uuid.'" class="RgTpBtns AttrValDelete" title="Remove">
                    <i class="fa fa-times" aria-hidden="true"></i>
                  </div>
                  <div id="EDT_'.$Row->uuid.'" class="RgTpBtns AttrValEdit" title="Edit">
                    <i class="fa fa-pencil" aria-hidden="true"></i>
                  </div>
                <div class="DivResizer"></div>
                </div>';
                break;
              case 2:
                $ClrData[0] = str_replace('Name:', '', $ClrData[0]);
                $ClrData[1] = str_replace('Img:', '', $ClrData[1]);
                $RtrnData['ValueList'] .= '
                <div class="RgnTypeBtn imgRgnTypeBtn">
                  <div class="ColorImgDspl">
                    <img src="'.get_site_url().'/'.$ClrData[1].'">
                  </div>
                  <span id="'.$Row->uuid.'">'.$ClrData[0].'</span>
                  <div id="DEL_'.$Row->uuid.'" class="RgTpBtns AttrValDelete" title="Remove">
                    <i class="fa fa-times" aria-hidden="true"></i>
                  </div>
                  <div id="EDT_'.$Row->uuid.'" class="RgTpBtns AttrValEdit" title="Edit">
                    <i class="fa fa-pencil" aria-hidden="true"></i>
                  </div>
                <div class="DivResizer"></div>
                </div>';
                break;
            }
          } else {
            $RtrnData['ValueList'] .= '
                <div class="RgnTypeBtn">
                  <span id="'.$Row->uuid.'">'.$Row->value.'</span>
                  <div id="DEL_'.$Row->uuid.'" class="RgTpBtns AttrValDelete" title="Remove">
                    <i class="fa fa-times" aria-hidden="true"></i>
                  </div>
                  <div id="EDT_'.$Row->uuid.'" class="RgTpBtns AttrValEdit" title="Edit">
                    <i class="fa fa-pencil" aria-hidden="true"></i>
                  </div>
                <div class="DivResizer"></div>
                </div>';
          }
        }
      }
    } else {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Attribute id not correct<br>';
    }
    unset($Attributes);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    }
    return $RtrnData;
  }
  public function SaveAttributeData($AttributeID,$Name,$Visibility) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'', '$Attribute'=>'');
    $NoteType = '';
    $Attributes = new mensio_products_default_attributes();
    if (!$Attributes->Set_UUID($AttributeID)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Attribute id not correct<br>';
    }
    $Attributes->Set_Visibility($Visibility);
    if ($RtrnData['ERROR'] === 'FALSE') {
      if (!$Attributes->UpdateGlobalAttribute()) {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] .= 'Global Attribute could not be updated<br>';
      }
    }
    unset($Attributes);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $NoteType = 'Success';
      $RtrnData['Message'] = 'Global Attribute saved successfully<br>';
      $RtrnData['Attribute'] = $AttributeID;
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    return $RtrnData;
  }
  public function SaveGlobalAttributeValue($AttributeID,$Name,$Type,$ValID,$Value) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $NewEntry = false;
    $Attributes = new mensio_products_default_attributes();
    if ($ValID === 'NewValue') {
      $ValID = $Attributes->GetNewAttributeID();
      $NewEntry = true;
    }
    if (!$Attributes->Set_UUID($AttributeID)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Attribute id not correct<br>';
    }
    if (!$Attributes->Set_ValueID($ValID)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Value id not correct<br>';
    }
    if ($Type === 'Text') {
      if (!$Attributes->Set_Value($Value)) {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] .= 'Value "'.$Value.'" not correct<br>';
      }
    } else {
      switch ($Name) {
        case 'Color':
          if (!$Attributes->Set_ColorValue($Value)) {
            $RtrnData['ERROR'] = 'TRUE';
            $NoteType = 'Alert';
            $RtrnData['Message'] .= 'Color Values "'.$Value.'" not correct<br>';
          }
          break;
        default:
          if (!$Attributes->Set_NumericValue($Value)) {
            $RtrnData['ERROR'] = 'TRUE';
            $NoteType = 'Alert';
            $RtrnData['Message'] .= 'Values "'.$Value.'" not correct<br>';
          }
          break;
      }
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      if ($NewEntry) {
        if (!$Attributes->AddNewValue()) {
          $RtrnData['ERROR'] = 'TRUE';
          $NoteType = 'Alert';
          $RtrnData['Message'] .= 'Value "'.$Value.'" could not be created<br>';
        }
      } else {
        if (!$Attributes->UpdateValue()) {
          $RtrnData['ERROR'] = 'TRUE';
          $NoteType = 'Alert';
          $RtrnData['Message'] .= 'Value "'.$Value.'" could not be Updated<br>';
        }
      }
    }
    unset($Attributes);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $RtrnData = $this->LoadAttributeValueList($AttributeID);
      $NoteType = 'Success';
      $RtrnData['Message'] = 'Global Attribute value "'.$Value.'" saved successfully<br>';
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;
  }
  public function RemoveGlobalAttributeValue($AttributeID,$ValueID) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $Attributes = new mensio_products_default_attributes();
    if (!$Attributes->Set_ValueID($ValueID)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Value id not correct<br>';
    } else {
      if (!$Attributes->CheckValueUsage()) {
        if (!$Attributes->RemoveGlobalValue()) {
          $RtrnData['ERROR'] = 'TRUE';
          $NoteType = 'Alert';
          $RtrnData['Message'] .= 'Value could not be removed<br>';
        }
      } else {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Info';
        $RtrnData['Message'] .= 'Value is been used by product(s).<br>Could not be removed<br>';
      }
    }
    unset($Attributes);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $RtrnData = $this->LoadAttributeValueList($AttributeID);
      $NoteType = 'Success';
      $RtrnData['Message'] = 'Global value removes successfully<br>';
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;
  }
  public function ToggleGlobalAttributeVisiblity($AttributeID) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $Attributes = new mensio_products_default_attributes();
    if (!$Attributes->Set_UUID($AttributeID)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Attribute id not correct<br>';
    } else {
      if (!$Attributes->UpdateGlobalAttributeVisibility()) {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] .= 'Visibility could not be updated<br>';
      }
    }
    unset($Attributes);
    if ($RtrnData['ERROR'] === 'TRUE') {
      $this->SetNotification($NoteType,$RtrnData['Message']);
      $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    }
    return $RtrnData;
  }
  public function HexToRGB($hex) {
   $rgb = array('r'=>'', 'g'=>'', 'b'=>'');
   $hex = str_replace('#', '', $hex);
   if(strlen($hex) == 3) {
      $rgb['r'] = hexdec(substr($hex,0,1).substr($hex,0,1));
      $rgb['g'] = hexdec(substr($hex,1,1).substr($hex,1,1));
      $rgb['b'] = hexdec(substr($hex,2,1).substr($hex,2,1));
   } else {
      $rgb['r'] = hexdec(substr($hex,0,2));
      $rgb['g'] = hexdec(substr($hex,2,2));
      $rgb['b'] = hexdec(substr($hex,4,2));
   }
   return $rgb;
  }
  public function LoadGlobalAttributeTranslation($AttributeID) {
    $RtrnData = '';
    $Categories = new mensio_products_categories();
    if ($Categories->Set_Attribute($AttributeID) ) {
      $Languages = new mensio_languages();
      $LangData = $Languages->LoadLanguagesData();
      unset($Languages);
      $TransFlds ='';
      if ((is_array($LangData)) && (!empty($LangData[0]))) {
        foreach ($LangData as $Row) {
          if ($Row->active) {
            if ($Categories->Set_Language($Row->uuid)) {
              $TransName = $Categories->GetAttributeTranslation();
              $TransFlds .= '<label class="label_symbol">'.$Row->name.'</label>
                <input type="text" id="'.$Row->uuid.'" class="form-control TransFlds" value="'.$TransName.'">';
            }
          }
        }
      }
      $ModalBody = $TransFlds.'
          <input type="hidden" id="MDL_TransAttribute" value="'.$AttributeID.'">
          <button id="BTN_AttrTransSave" class="button" title="Save">
            <i class="fa fa-floppy-o" aria-hidden="true"></i>
          </button>';
      $RtrnData = $this->CreateModalWindow('Attribute Translations',$ModalBody);
    }
    unset($Categories);
    return $RtrnData;
  }
  public function UpdateGlobalAttributeTranslations($AttributeID,$Data) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','Modal'=>'','Translations'=>'');
    $NoteType = '';
    $Categories = new mensio_products_categories();
    if ($Categories->Set_Attribute($AttributeID)) {
      $Data = stripslashes($Data);
      $Data = json_decode($Data, true);
      if (is_array($Data)) {
        foreach ($Data as $Row) {
          if (!$Categories->Set_Language($Row['Field'])) {
            $RtrnData['ERROR'] = 'TRUE';
            $NoteType = 'Alert';
            $RtrnData['Message'] .= 'Language id not correct<br>';
          }
          if (!$Categories->Set_Name($Row['Value'])) {
            $RtrnData['ERROR'] = 'TRUE';
            $NoteType = 'Alert';
            $RtrnData['Message'] .= 'Language translation not correct<br>';
          }
          if ($RtrnData['ERROR'] === 'FALSE') {
            if (!$Categories->UpdateAttributeTranslation()) {
              $RtrnData['ERROR'] = 'TRUE';
              $NoteType = 'Alert';
              $RtrnData['Message'] .= 'Translation could not be updated<br>';
            }
          }
        }
      }
    }
    unset($Categories);
    if ($RtrnData['ERROR'] === 'FALSE' ) {
      $NoteType = 'Success';
      $RtrnData['Message'] .= 'Translations updated Successfully<br>';
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;
  }
  public function LoadGlobalAttributeTypeForm() {
    $Page = new Mensio_Admin_Settings_Store();
    $DataSet = $Page->LoadStoreSettingsData();
    unset($Page);
    $Color = '<option value="TXT">Text</option><option value="HEX">Hexadecimal</option><option value="RGB">RGB</option><option value="IMG">Image (.png, .jpg, .gif, .svg)</option>';
    $Color = str_replace('value="'.$DataSet['Color'].'"', 'value="'.$DataSet['Color'].'" selected', $Color);
    $Height = '<option value="TXT">Text</option><option value="MMT">Millimeter</option><option value="CMT">Centimeter</option><option value="MTR">Meter</option><option value="INC">Inch</option><option value="FOT">Foot</option><option value="YRD">Yard</option>';
    $Height = str_replace('value="'.$DataSet['Height'].'"', 'value="'.$DataSet['Height'].'" selected', $Height);
    $Length = '<option value="TXT">Text</option><option value="MMT">Millimeter</option><option value="CMT">Centimeter</option><option value="MTR">Meter</option><option value="INC">Inch</option><option value="FOT">Foot</option><option value="YRD">Yard</option>';
    $Length = str_replace('value="'.$DataSet['Length'].'"', 'value="'.$DataSet['Length'].'" selected', $Length);
    $Size = '<option value="TXT">Text</option><option value="NUM">Number</option>';
    $Size = str_replace('value="'.$DataSet['Size'].'"', 'value="'.$DataSet['Size'].'" selected', $Size);
    $Volume = '<option value="TXT">Text</option><option value="GAL">Gallon</option><option value="LTR">Litre</option><option value="CMT">Cubic Meter</option><option value="CFT">Cubic Foot</option>';
    $Volume = str_replace('value="'.$DataSet['Volume'].'"', 'value="'.$DataSet['Volume'].'" selected', $Volume);
    $Weight = '<option value="TXT">Text</option><option value="KLG">Kilogram</option><option value="GRM">Gram</option><option value="GAL">Gallon</option><option value="LTR">Litre</option>';
    $Weight = str_replace('value="'.$DataSet['Weight'].'"', 'value="'.$DataSet['Weight'].'" selected', $Weight);
    $Width = '<option value="TXT">Text</option><option value="MMT">Millimeter</option><option value="CMT">Centimeter</option><option value="MTR">Meter</option><option value="INC">Inch</option><option value="FOT">Foot</option><option value="YRD">Yard</option>';
    $Width = str_replace('value="'.$DataSet['Width'].'"', 'value="'.$DataSet['Width'].'" selected', $Width);
    $MdlBody = '
            <div class="TypesWrapper">
              <input type="hidden" id="MDL_Store" value="'.$DataSet['uuid'].'">
              <label class="label_symbol">Color</label>
              <select id="MDL_Color" class="form-control">
                '.$Color.'
              </select>
              <label class="label_symbol">Height</label>
              <select id="MDL_Height" class="form-control">
                '.$Height.'
              </select>
              <label class="label_symbol">Length</label>
              <select id="MDL_Length" class="form-control">
                '.$Length.'
              </select>
              <label class="label_symbol">Size</label>
              <select id="MDL_Size" class="form-control">
                '.$Size.'
              </select>
              <label class="label_symbol">Volume</label>
              <select id="MDL_Volume" class="form-control">
                '.$Volume.'
              </select>
              <label class="label_symbol">Weight</label>
              <select id="MDL_Weight" class="form-control">
                '.$Weight.'
              </select>
              <label class="label_symbol">Width</label>
              <select id="MDL_Width" class="form-control">
                '.$Width.'
              </select>
              <div class="button_row">
                <button id="BTN_SaveMetrics" class="button BtnGreen" title="Save Types">
                  <i class="fa fa-floppy-o" aria-hidden="true"></i>
                </button>
              </div>
            </div>';
    return $this->CreateModalWindow('Attribute Types',$MdlBody);
  }
  public function UpdateStoreProductMetrics($StoreID,$Color,$Height,$Length,$Size,$Volume,$Weight,$Width) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $Metrics = '';
    $Store = new mensio_store();
    if (!$Store->Set_UUID($StoreID)) {
      $RtrnData['ERROR'] = 'TRUE';
      $NoteType = 'Alert';
      $RtrnData['Message'] .= 'Problem with Store ID<br>';
    } else {
      if ($this->CheckMetric('Color',$Color)) { $Metrics .= 'Color:'.$Color.';';}
      else {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] .= 'Color value not correct<br>';
      }
      if ($this->CheckMetric('Height',$Height)) { $Metrics .= 'Height:'.$Height.';';}
      else {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] .= 'Height value not correct<br>';
      } 
      if ($this->CheckMetric('Length',$Length)) { $Metrics .= 'Length:'.$Length.';';}
      else {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] .= 'Length value not correct<br>';
      } 
      if ($this->CheckMetric('Size',$Size)) { $Metrics .= 'Size:'.$Size.';';}
      else {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] .= 'Size value not correct<br>';
      }
      if ($this->CheckMetric('Volume',$Volume)) { $Metrics .= 'Volume:'.$Volume.';';}
      else {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] .= 'Volume value not correct<br>';
      }
      if ($this->CheckMetric('Weight',$Weight)) { $Metrics .= 'Weight:'.$Weight.';';}
      else {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] .= 'Weight value not correct<br>';
      }
      if ($this->CheckMetric('Width',$Width)) { $Metrics .= 'Width:'.$Width;}
      else {
        $RtrnData['ERROR'] = 'TRUE';
        $NoteType = 'Alert';
        $RtrnData['Message'] .= 'Width value not correct<br>';
      }
      if ($RtrnData['ERROR'] === 'FALSE') {
        if ($Store->Set_Metrics($Metrics)) {
          if (!$Store->UpdateStoreData()) {
            $RtrnData['ERROR'] = 'TRUE';
            $NoteType = 'Alert';
            $RtrnData['Message'] .= 'Metrics could not be updated<br>';
          }
        }
      }
    }
    unset($Store);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $NoteType = 'Success';
      $RtrnData['Message'] = 'Data Saved Successfully';
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']);
    return $RtrnData;
  }
  private function CheckMetric($Metric,$Value) {
    $Correct = false;
    switch ($Metric) {
      case 'Color':
        if (($Value ==='TXT') || ($Value ==='HEX') || ($Value ==='RGB') || ($Value ==='IMG')) {
          $Correct = true;
        }
        break;
      case 'Size':
        if (($Value ==='TXT') || ($Value ==='NUM')) { $Correct = true; }
        break;
      case 'Volume':
        if (($Value ==='TXT') || ($Value ==='GAL') || ($Value ==='LTR') ||
            ($Value ==='CMT') || ($Value ==='CFT')) { $Correct = true; }
        break;
      case 'Length':
      case 'Height':
      case 'Width':
        if (($Value ==='TXT') || ($Value ==='MMT') || ($Value ==='CMT') ||
            ($Value ==='MTR') || ($Value ==='INC') || ($Value ==='FOT') ||
            ($Value ==='YRD')) { $Correct = true; }
        break;
      case 'Weight':
        if (($Value ==='TXT') || ($Value ==='KLG') || ($Value ==='GRM') ||
            ($Value ==='GAL') || ($Value ==='LTR')) { $Correct = true; }
        break;
    }
    return $Correct;
  }
}