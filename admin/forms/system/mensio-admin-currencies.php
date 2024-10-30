<?php
if ( ! defined( 'WPINC' ) ) {die;}
add_action('wp_ajax_mensio_ajax_Table_Currencies', 'mensio_ajax_Table_Currencies');
add_action('wp_ajax_mensio_ajax_Update_Currency_LeftPos', 'mensio_ajax_Update_Currency_LeftPos');
add_action('wp_ajax_mensio_ajax_Load_Currency_Data', 'mensio_ajax_Load_Currency_Data');
add_action('wp_ajax_mensio_ajax_Update_Currencies_Data', 'mensio_ajax_Update_Currencies_Data');
add_action('wp_ajax_mensio_ajax_Load_Currencies_ClearTransFields', 'mensio_ajax_Load_Currencies_ClearTransFields');
add_action('wp_ajax_mensio_ajax_Currency_AddNew', 'mensio_ajax_Currency_AddNew');
add_action('wp_ajax_mensio_ajax_Bulk_Update_Currency_LeftPos', 'mensio_ajax_Bulk_Update_Currency_LeftPos');
function Mensio_Admin_Settings_Currencies() {
  $CompPage = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_Settings_Currencies();
    $Page->Load_Page_CSS();
    $Page->Load_Page_JS();
    $Page->Set_MainMenuItems($Page->GetPageSubPages('System'));
    $Page->Set_CustomMenuItems('
              <div class="menu_button_row"> 
                <button id="BTN_Save_Header" class="button BtnGreen" title="Save">
                  <i class="fa fa-floppy-o" aria-hidden="true"></i>
                </button>
                <button id="BTN_Back_Header" class="button" title="Back">
                  <i class="fa fa-arrow-left"></i>
                </button>
              </div>');
    $Page->Set_MainPlaceHolder('<h1 class="Mns_Page_HeadLine">Currencies<span class="Mns_Page_Breadcrumb">Table Mode</span></h1>
      <div id="ButtonArea">
        <button id="BTN_AddNew" title="Add New Currency">
          <i class="fa fa-plus action-icon" aria-hidden="true"></i>
          Add New
        </button>
      </div>
      '.wp_nonce_field('Active_Page_Currency').'
      <div class="PageInfo">'.MENSIO_PAGEINFO_Currency.'</div>
      <div class="DivResizer"></div>
      <hr>
      <div id="DIV_Table">
        <div id="TBL_Currencies_Wrapper" class="TBL_DataTable_Wrapper">
          '.$Page->GetCurrencyDataTable().'
        </div>
      <div class="DivResizer"></div>
      </div>
      <div id="DIV_Edit">
        <div class="Mns_Tab_Wrapper">
          <div id="tabs">
            <ul>
              <li><a href="#tabs-1">Currency Info</a></li>
              <li><a href="#tabs-2">Translations</a></li>
            </ul>
            <div id="tabs-1" class="Mns_Tab_Container">
              <input type="hidden" id="FLD_Currency" class="form-control" value="">
              <label class="label_symbol">Code</label>
              <br>
              <input type="text" id="FLD_Code" class="form-control">
              <label class="label_symbol">Symbol</label>
              <br>
              <select id="FLD_Symbol" class="form-control">
                <option value="Lek">Lek</option>
                <option value="؋">؋</option>
                <option value="$">$</option>
                <option value="ƒ">ƒ</option>
                <option value="ман">ман</option>
                <option value="Br">Br</option>
                <option value="BZ$">BZ$</option>
                <option value="$b">$b</option>
                <option value="KM">KM</option>
                <option value="P">P</option>
                <option value="лв">лв</option>
                <option value="R$">R$</option>
                <option value="៛">៛</option>
                <option value="¥">¥</option>
                <option value="₡">₡</option>
                <option value="kn">kn</option>
                <option value="₱">₱</option>
                <option value="Kč">Kč</option>
                <option value="kr">kr</option>
                <option value="RD$">RD$</option>
                <option value="£">£</option>
                <option value="€">€</option>
                <option value="¢">¢</option>
                <option value="Q">Q</option>
                <option value="L">L</option>
                <option value="Ft">Ft</option>
                <option value="INR">INR</option>
                <option value="Rp">Rp</option>
                <option value="﷼">﷼</option>
                <option value="₪">₪</option>
                <option value="J$">J$</option>
                <option value="₩">₩</option>
                <option value="₭">₭</option>
                <option value="ден">ден</option>
                <option value="RM">RM</option>
                <option value="₨">₨</option>
                <option value="₮">₮</option>
                <option value="MT">MT</option>
                <option value="C$">C$</option>
                <option value="₦">₦</option>
                <option value="B/.">B/.</option>
                <option value="Gs">Gs</option>
                <option value="S/.">S/.</option>
                <option value="zł">zł</option>
                <option value="lei">lei</option>
                <option value="руб">руб</option>
                <option value="Дин">Дин</option>
                <option value="S">S</option>
                <option value="R">R</option>
                <option value="CHF">CHF</option>
                <option value="NT$">NT$</option>
                <option value="฿">฿</option>
                <option value="TT$">TT$</option>
                <option value="TRY">TRY</option>
                <option value="₴">₴</option>
                <option value="$U">$U</option>
                <option value="Bs">Bs</option>
                <option value="₫">₫</option>
                <option value="Z$">Z$</option>
              </select>
              <label class="label_symbol">Display Left or Right</label>
              <br>
              <select id="FLD_Left" class="form-control">
                <option value="0">Right</option>
                <option value="1">Left</option>
              </select>
              <!-- <label class="label_symbol">Font Awsome Icon</label>
              <br> -->
              <input type="hidden" id="FLD_Icon" class="form-control">
            <div class="DivResizer"></div>
            </div>
            <div id="tabs-2" class="Mns_Tab_Container">
              <div id="DIV_CurrTrans"></div>
            <div class="DivResizer"></div>
            </div>
          </div>
          <div class="button_row">
            <button id="BTN_Save" class="button BtnGreen" title="Save">
              <i class="fa fa-floppy-o" aria-hidden="true"></i>
            </button>
            <button id="BTN_Back" class="button" title="Back">
              <i class="fa fa-arrow-left"></i>
            </button> 
          </div>
        <div class="DivResizer"></div>
        </div>
      <div class="DivResizer"></div>
      </div>'
    );
    $Page->UpdatePage();
    $Page->SetActiveSubPage('System','Currency');
    $CompPage = $Page->GetPage();
    unset($Page);
  }
  echo $CompPage;
}
function mensio_ajax_Table_Currencies() {
  $RtrnTable = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $InPage = filter_var($_REQUEST['Page'],FILTER_SANITIZE_STRING);
    $InRows = filter_var($_REQUEST['Rows'],FILTER_SANITIZE_STRING);
    $InSearch = filter_var($_REQUEST['Search'],FILTER_SANITIZE_STRING);
    $InSorter = filter_var($_REQUEST['Sorter'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Settings_Currencies();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      if ($InSearch == '') {
        $RtrnTable = $Page->GetCurrencyDataTable($InPage,$InRows,$InSorter);
      } else {
        $RtrnTable = $Page->LoadSearchResults($InPage, $InRows, $InSearch, $InSorter);
      }
    }
    unset($Page);
  }
  echo $RtrnTable;
  die();
}
function mensio_ajax_Update_Currency_LeftPos() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Currency = filter_var($_REQUEST['Currency'],FILTER_SANITIZE_STRING);
    $LeftPos = filter_var($_REQUEST['LeftPos'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Settings_Currencies();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateCurrencyLeftPos($Currency,$LeftPos);
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Load_Currency_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Currency = filter_var($_REQUEST['Currency'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Settings_Currencies();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->LoadCurrencyData($Currency);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Update_Currencies_Data() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Data = $_REQUEST['Data'];
    $Page = new Mensio_Admin_Settings_Currencies();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateCurrencyData($Data);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Load_Currencies_ClearTransFields() {
  $Btn = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_Settings_Currencies();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $Btn = $Page->GetNewTranslations();
    }
    unset($Page);
  }
  echo $Btn;
  die();  
}
function mensio_ajax_Currency_AddNew() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Code = filter_var($_REQUEST['code'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Settings_Currencies();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->AddNewCurrencyData($Code);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}
function mensio_ajax_Bulk_Update_Currency_LeftPos() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $CurCodes = filter_var($_REQUEST['Currency'],FILTER_SANITIZE_STRING);
    $LeftPos = filter_var($_REQUEST['LeftPos'],FILTER_SANITIZE_STRING);
    $Page = new Mensio_Admin_Settings_Currencies();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $Currency = explode(';',$CurCodes);
      if (is_array($Currency)) {
        $RtrnData = '';
        foreach ($Currency as $Row) {
          if ($Row !== '') {
            if ($RtrnData === 'OK') { $RtrnData = ''; }
            $RtrnData .= $Page->UpdateCurrencyLeftPos($Row,$LeftPos);
          }
        }
      }
    }
    unset($Page);
  }
  echo $RtrnData;
  die();
}