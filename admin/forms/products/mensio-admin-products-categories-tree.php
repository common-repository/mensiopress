<?php
add_action('wp_ajax_mensio_ajax_Update_Category_Tree', 'mensio_ajax_Update_Category_Tree');
function Mensio_Admin_Products_Categories_Tree() {
  $CompPage = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Page = new Mensio_Admin_Products_Categories_Tree();
    $Page->Load_Page_CSS();
    $Page->Load_Page_JS();
    $Page->Set_MainMenuItems($Page->GetPageSubPages('Products'));
    $Page->Set_CustomMenuItems('
              <div class="menu_button_row">
                <button id="BTN_Save_Header" class="button BtnGreen" title="Save">
                  <i class="fa fa-floppy-o" aria-hidden="true"></i>
                </button>
              </div>') ;
    $Page->Set_MainPlaceHolder('<h1 class="Mns_Page_HeadLine">Product Categories Tree</h1>
      '.wp_nonce_field('Active_Page_Categories_Tree').'
      <div class="PageInfo">'.MENSIO_PAGEINFO_CategoriesTree.'</div>
      <div class="DivResizer"></div>
      <hr>
      <div id="DIV_Edit">
        <div class="Mns_Edit_Wrapper">
          <div class="dd" id="nestable">
            <input type="hidden" id="OldTree" value="">
            <input type="hidden" id="Treelist" value="">
            '.$Page->LoadCategoriesTree().'
          </div>
        <div class="DivResizer"></div>
        </div>
        <div class="button_row">
          <button id="BTN_Save" class="button BtnGreen" title="Save">
            <i class="fa fa-floppy-o" aria-hidden="true"></i>
          </button>
        </div>      
      </div>');
    $Page->UpdatePage();
    $Page->SetActiveSubPage('Products','CategoriesTree');
    $CompPage = $Page->GetPage();
    unset($Page);
  }
  echo $CompPage;
}
function mensio_ajax_Update_Category_Tree() {
  $RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $Tree = $_REQUEST['Treelist'];
    $Page = new Mensio_Admin_Products_Categories_Tree();
    $Security = explode('::', filter_var($_REQUEST['Security'],FILTER_SANITIZE_STRING));
    if ($Page->VerifyPageIntegrity($Security[0],$Security[1])) {
      $RtrnData = $Page->UpdateCategoriesTree($Tree);
    }
    unset($Page);
    $RtrnData = json_encode($RtrnData);
  }
  echo $RtrnData;
  die();
}