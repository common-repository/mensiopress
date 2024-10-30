<?php
add_action('wp_ajax_mensio_ajax_Update_Store_Sales_Settings', 'mensio_ajax_Update_Store_Sales_Settings');
function Mensio_Admin_Settings_SalesSettings() {
  $CompPage = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_Settings_Store();
    $Page->Load_Page_CSS('mensio-admin-settings-store');
    $Page->Load_Page_JS('mensio-admin-settings-sales');
    $Page->Set_MainMenuItems($Page->GetPageSubPages('Settings'));
    $Page->Set_CustomMenuItems('
      <div class="menu_button_row"> 
        <button id="BTN_Save_Header" class="button BtnGreen" title="Save">
          <i class="fa fa-floppy-o" aria-hidden="true"></i>
        </button>
      </div>');
    $DataSet = $Page->LoadStoreSettingsData();
    $OrderTmpltSlcts = '
      <input type="radio" name="gender" class="OrderTmplSelector" id="Date" value="YYYYMMDD-nnnnnn"> Date + Six Digit Number (YYYYMMDD + nnnnnn)
      <br><br>
      <input type="radio" name="gender" class="OrderTmplSelector" id="YearMonth" value="YYYYMM-nnnnnn"> Year + month + Six Digit Number (YYYYMM + nnnnnn)
      <br><br>
      <input type="radio" name="gender" class="OrderTmplSelector" id="Year" value="YYYY-nnnnnn"> Year + Six Digit Number (YYYY + nnnnnn)
      <br><br>
      <input type="radio" name="gender" class="OrderTmplSelector" id="SixDigitNumber" value="nnnnnn"> Six Digit Number (nnnnnn)
      <br><br>
      <input type="radio" name="gender" class="OrderTmplSelector" id="Custom" value="Custom"> Custom<br>';
    $disabled = 'disabled';
    if (strpos($OrderTmpltSlcts, 'checked="checked"') === false) {
      switch ($DataSet['OrderSerial']) {
        case 'YYYYMMDD-nnnnnn':
          $OrderTmpltSlcts = str_replace('value="YYYYMMDD-nnnnnn"', 'value="YYYYMMDD-nnnnnn" checked="checked"', $OrderTmpltSlcts);
          break;
        case 'YYYYMM-nnnnnn':
          $OrderTmpltSlcts = str_replace('value="YYYYMM-nnnnnn"', 'value="YYYYMM-nnnnnn" checked="checked"', $OrderTmpltSlcts);
          break;
        case 'YYYY-nnnnnn':
          $OrderTmpltSlcts = str_replace('value="YYYY-nnnnnn"', 'value="YYYY-nnnnnn" checked="checked"', $OrderTmpltSlcts);
          break;
        case 'nnnnnn':
          $OrderTmpltSlcts = str_replace('value="nnnnnn"', 'value="nnnnnn" checked="checked"', $OrderTmpltSlcts);
          break;
        default:
          $OrderTmpltSlcts = str_replace('value="Custom"', 'value="Custom" checked="checked"', $OrderTmpltSlcts);
          $disabled = '';
          break;
      }
    }
    $Page->Set_MainPlaceHolder('<h1 class="Mns_Page_HeadLine">Commerce Settings</h1>
      '.wp_nonce_field('Active_Page_Store_Settings').'
      <div class="PageInfo">'.MENSIO_PAGEINFO_Settings.'</div>
      <hr>
      <div id="DIV_Edit">
        <div id="TabOrders" class="ProductTab">
          <input type="hidden" id="FLD_Store" value="'.$DataSet['uuid'].'">
          <label class="label_symbol">Main Store Currency</label>
          <select id="FLD_Currency" class="form-control">
            '.$Page->LoadCurrencyOptions($DataSet['Currency']).'
          </select>
          <label class="label_symbol">Sales Serial Template Type</label>
          <div class="OrderTmplSelectorDiv">
          '.$OrderTmpltSlcts.'
          </div>
          <div id="CstmOrderTempl">
            <label class="label_symbol">Sales Serial Template</label>
            <input id="FLD_OrderSerial" type="text" class="form-control" value ="'.$DataSet['OrderSerial'].'" '.$disabled.'>
          </div>
          <div class="DivResizer"></div>
          <div class="button_row">
            <button id="BTN_Save" class="button BtnGreen" title="Save">
              <i class="fa fa-floppy-o" aria-hidden="true"></i>
            </button>
          </div>
        <div class="DivResizer"></div>
        </div>
      <div class="DivResizer"></div>
      </div>'
    );
    $Page->UpdatePage();
    $Page->SetActiveSubPage('Settings','SalesSettings');
    $CompPage = $Page->GetPage();
    unset($Page);
  }
  echo $CompPage;
}
function mensio_ajax_Update_Store_Sales_Settings() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Store = filter_var($_REQUEST['Store'],FILTER_SANITIZE_STRING);
    $Currency = filter_var($_REQUEST['Currency'],FILTER_SANITIZE_STRING);
    $OrderSerial = filter_var($_REQUEST['OrderSerial'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Settings_Store();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateStoreDefaultCurrency($Store,$Currency);
      if ($RtrnData['ERROR'] === 'FALSE') {
        $RtrnData = $Page->UpdateStoreOrderSettings($Store,$OrderSerial);
      }
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}