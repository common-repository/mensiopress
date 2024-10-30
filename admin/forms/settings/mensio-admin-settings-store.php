<?php
add_action('wp_ajax_mensio_ajax_Update_Store_Basic_Data', 'mensio_ajax_Update_Store_Basic_Data');
add_action('wp_ajax_mensio_ajax_Load_Store_Google_Analytics_Help', 'mensio_ajax_Load_Store_Google_Analytics_Help');
function Mensio_Admin_Settings_Store() {
  $CompPage = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_Settings_Store();
    $Page->Load_Page_CSS('mensio-admin-settings-store');
    $Page->Load_Page_JS('mensio-admin-settings-store');
    $Page->Set_MainMenuItems($Page->GetPageSubPages('Settings'));
    $Page->Set_CustomMenuItems('
      <div class="menu_button_row"> 
        <button id="BTN_StoreInfo_Header" class="button BtnGreen BTN_Save Btn-ui-id Btn-ui-id-1" title="Save">
          <i class="fa fa-floppy-o" aria-hidden="true"></i>
        </button>
      </div>');
    $DataSet = $Page->LoadStoreSettingsData();
    if ($DataSet['CurrUpdate']) { $CurrUpdtOptions = '<option value="0">No</option><option value="1" selected>Yes</option>';}
      else { $CurrUpdtOptions = '<option value="0" selected>No</option><option value="1">Yes</option>'; }
    $Page->Set_MainPlaceHolder('<h1 class="Mns_Page_HeadLine">General</h1>
      '.wp_nonce_field('Active_Page_Store_Settings').'
      <div class="PageInfo">'.MENSIO_PAGEINFO_Settings.'</div>
      <div class="DivResizer"></div>
      <hr>
      <div id="DIV_Edit">
        <div id="TabStore" class="ProductTab">
          <input type="hidden" id="FLD_Store" value="'.$DataSet['uuid'].'">
            <div class="DIV_Cur_Img">
              <label class="label_symbol">Store Logo</label>
              <div class="Mns_Img_Container">
                <img id="DispImg" class="selectIm" src="'.$DataSet['Logo'].'" alt="logo_image">
              </div>
              <div class="">
                <button id="Btn_OpenMediaModal" class="button Mns_Img_Btn" title="Open Image Selector">
                  <i class="fa fa-picture-o" aria-hidden="true"></i>
                </button>
                <button id="Btn_ClearImg" class="button Mns_Img_Btn" title="Clear Image">
                  <i class="fa fa-trash" aria-hidden="true"></i>
                </button>
                <input id="FLD_Logo" class="StoreBasicData" type="hidden" value="'.$DataSet['Logo'].'"/>
              </div>
            </div>
            <div class="StoreInfoFldsDiv">
              <label class="label_symbol">Name</label>
              <input id = "FLD_Name" type="text" class="form-control StoreBasicData" value ="'.$DataSet['Name'].'">
              <label class="label_symbol">Country</label>
              <select id="FLD_Country" class="form-control StoreBasicData">
                '.$Page->LoadCountryOptions($DataSet['Country']).'
              </select>
              <label class="label_symbol">Time Zone</label>
              <select id="FLD_TZone" class="form-control StoreBasicData">
                '.$Page->LoadTimezonesOptions($DataSet['TZone']).'
              </select>
              <label class="label_symbol">City</label>
              <input id = "FLD_City" type="text" class="form-control StoreBasicData" value ="'.$DataSet['City'].'">
              <label class="label_symbol">Street</label>
              <input id="FLD_Street" type="text" name="street" class="form-control StoreBasicData" value ="'.$DataSet['Street'].'">
              <label class="label_symbol">Number</label>
              <input id="FLD_Number" type="text" name="number" class="form-control StoreBasicData" value ="'.$DataSet['Number'].'">
              <label class="label_symbol">Phone</label>
              <input id="FLD_Phone" type="text" name="Phone" class="form-control StoreBasicData" value ="'.$DataSet['Phone'].'">
              <label class="label_symbol">Fax</label>
              <input id="FLD_Fax" type="text" name="fax" class="form-control StoreBasicData" value ="'.$DataSet['Fax'].'">
              <label class="label_symbol">Email</label>
              <input id="FLD_Email" type="text" name="email" class="form-control StoreBasicData" value = "'.$DataSet['EMail'].'">
              <label class="label_symbol">Google Analytics
                <div class="HelpButtonDivSmall">
                  <div id="Btn_TempInfo" class="HelpButtonSmall" title="Template variable explanation">
                    <i class="fa fa-question-circle fa-lg" aria-hidden="true"></i>
                  </div>
                </div>
              </label>
              <textarea id="FLD_GglAnalytics" class="form-control StoreBasicData">'.$DataSet['GglAnalytics'].'</textarea>
              <label class="label_symbol">Google Map</label>
              <textarea id="FLD_GglMap" class="form-control StoreBasicData">'.$DataSet['GglMap'].'</textarea>
            </div>
            <div class="button_row">
              <button id="BTN_StoreInfo" class="button BtnGreen BTN_Save" title="Save">
                <i class="fa fa-floppy-o" aria-hidden="true"></i>
              </button>
            </div>
          <div class="DivResizer"></div>
        </div>
      <div class="DivResizer"></div>
      </div>'
    );
    $Page->UpdatePage();
    $Page->SetActiveSubPage('Settings','Store');
    $CompPage = $Page->GetPage();
    unset($Page);
  }
  echo $CompPage;
}
function mensio_ajax_Update_Store_Basic_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Store = filter_var($_REQUEST['Store'],FILTER_SANITIZE_STRING);
    $Data = $_REQUEST['Data'];
    $Page = new Mensio_Admin_Settings_Store();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateStoreBasicData($Store,$Data);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Load_Store_Google_Analytics_Help() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Data = $_REQUEST['Data'];
    $Page = new Mensio_Admin_Settings_Store();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadStoreGoogleAnalyticsHelp();
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}