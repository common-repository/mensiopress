<?php
class Mensio_Admin_Products_Categories_Tree extends mensio_core_form {
  public function __construct() {
    if (!defined('WPINC')) { die(); }
    $this->Set_MainTemplate();
    $this->ActivePage = 'Categories_Tree';
  }
  public function Load_Page_CSS() {
    wp_enqueue_style(
     MENSIO_PLGTITLE.'-category-tree',
     plugin_dir_url( __FILE__ ) . '../../css/mensio-products-categories-tree.css',
     array(),
     MENSIO_VERSION,
     'all'
    );    
  }
  public function Load_Page_JS() {
    wp_enqueue_script(
     MENSIO_PLGTITLE.'-nestable',
     plugin_dir_url( __FILE__ ) . '../../js/jqnestable.js',
     array( 'jquery' ),
     MENSIO_VERSION,
     false
    );
    wp_enqueue_script(
     MENSIO_PLGTITLE.'-category-tree',
     plugin_dir_url( __FILE__ ) . '../../js/mensio-products-categories-tree.js',
     array( 'jquery' ),
     MENSIO_VERSION,
     false
    );
  }
  public function LoadCategoriesTree($Parent='TopLevel') {
    $CatTree = '';
    $Categories = new mensio_products_categories();
    if ($Categories->Set_Parent($Parent)) {
      $DataSet = $Categories->LoadProductCategoriesTreeDataSet();
      if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
        $CatTree .= '
          <ol class="dd-list">';
        foreach ($DataSet as $Row) {
          $CatTree .= '
              <li class="dd-item" data-id="'.$Row->category.'">
                  <div class="dd-handle">'.$Row->name.' ('.$Row->translation.')</div>';
          $CatTree .= $this->LoadCategoriesTree($Row->category);
          $CatTree .= '</li>';
        }
        $CatTree .= '
          </ol>';
      }
    }
    unset($Categories);
    return $CatTree;
  }
  private function UpdateCategoriesChildren($Data,$Parent) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $ParentID = '';
    $MaxOrder = 1;
    if (is_array($Data)) {
      $Categories = new mensio_products_categories();
      foreach ($Data as $Row) {
        if (!$Categories->Set_UUID($Row['id'])) {
          $NoteType = 'Alert';
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] .= 'Problem at category id<br>';
        }
        if (!$Categories->Set_Parent($Parent)) {
          $NoteType = 'Alert';
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] .= 'Tree level not correct<br>';
        }
        if (!$Categories->Set_COrder($MaxOrder)) {
          $NoteType = 'Alert';
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] .= 'Tree level not correct<br>';
        }
        if ($RtrnData['ERROR'] === 'FALSE') {
          if (!$Categories->UpdateCategoryTree()) {
            $NoteType = 'Alert';
            $RtrnData['ERROR'] = 'TRUE';
            $RtrnData['Message'] .= 'Problem while updating tree<br>';
          } else {
            $ParentID = $Row['id'];
            if (!empty($Row['children'])) {
              $SubData = $this->UpdateCategoriesChildren($Row['children'],$ParentID);
              $RtrnData['ERROR'] = $SubData['ERROR'];
              $RtrnData['Message'] = $SubData['Message'];
              if ($RtrnData['ERROR'] === 'TRUE') { $NoteType = 'Alert'; }
            }
          }
        }
        ++$MaxOrder;
      }
      unset($Categories);
    } else {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Not correct input format<br>';
    }
    return $RtrnData;
  }
  public function UpdateCategoriesTree($Tree) {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');
    $NoteType = '';
    $Parent = '';
    $MaxOrder = 1;
    $Data = json_decode(stripslashes($Tree), true);
    if (is_array($Data)) {
      $Categories = new mensio_products_categories();
      $Categories->ClearCategoriesTree();
      foreach ($Data as $Row) {
        if (!$Categories->Set_UUID($Row['id'])) {
          $NoteType = 'Alert';
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] .= 'Problem at category id<br>';
        }
        if (!$Categories->Set_Parent('TopLevel')) {
          $NoteType = 'Alert';
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] .= 'Tree level not correct<br>';
        }
        if (!$Categories->Set_COrder($MaxOrder)) {
          $NoteType = 'Alert';
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] .= 'Tree level not correct<br>';
        }
        if ($RtrnData['ERROR'] === 'FALSE') {
          if (!$Categories->UpdateCategoryTree()) {
            $NoteType = 'Alert';
            $RtrnData['ERROR'] = 'TRUE';
            $RtrnData['Message'] .= 'Problem while updating tree<br>';
          } else {
            $Parent = $Row['id'];
            if (!empty($Row['children'])) {
              $SubData = $this->UpdateCategoriesChildren($Row['children'],$Parent);
              $RtrnData['ERROR'] = $SubData['ERROR'];
              $RtrnData['Message'] = $SubData['Message'];
              if ($RtrnData['ERROR'] === 'TRUE') { $NoteType = 'Alert'; }
            }
          }
        }
        ++$MaxOrder;
      }
      unset($Categories);
    } else {
      $NoteType = 'Alert';
      $RtrnData['ERROR'] = 'TRUE';
      $RtrnData['Message'] .= 'Not correct input format<br>';
    }
    if ($RtrnData['ERROR'] === 'FALSE') {
      $NoteType = 'Success';
      $RtrnData['Message'] .= 'Save Successfull<br>';
    }
    $this->SetNotification($NoteType,$RtrnData['Message']);
    $RtrnData['Message'] = $this->CreateNotificationDiv($RtrnData['Message']); 
    return $RtrnData;
  }
}