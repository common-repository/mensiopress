<?php
class Mensio_Admin_System_Notifications extends mensio_core_form {
    private $DataSet;
  public function __construct() {
    if (!defined('WPINC')) { die(); }
    $this->Set_MainTemplate();
    $this->LoadNotificationDataSet();
    $this->ActivePage = 'Mensio_Notifications';
  }
  public function Load_Page_CSS() {
    wp_enqueue_style(
     MENSIO_PLGTITLE.'-logs',
     plugin_dir_url( __FILE__ ) . '../../css/mensio-admin-settings-logs.css',
     array(),
     MENSIO_VERSION,
     'all'
    );
  }
  public function Load_Page_JS() {
    wp_enqueue_script(
     MENSIO_PLGTITLE.'-Notifications',
     plugin_dir_url( __FILE__ ) . '../../js/mensio-admin-settings-notifications.js',
     array( 'jquery' ),
     MENSIO_VERSION,
     false
    );
  }
  private function LoadNotificationDataSet($InSorter='') {
    $Data = '';
    $Notification = new mensio_notifications();
    if ($Notification->Set_Sorter($InSorter)) {
      $this->DataSet = $Notification->LoadNotifications();
    }  
    unset($Notification);
  }
  public function GetNotificationDataTable($InPage=1,$InRows=0) {
    $RtrnTable = '';
    $TableData = $this->DataSet;
    if ($InRows === 0) { $InRows = $this->LoadTableDefaultRows(); }
    $tbl = new mensio_datatable();
    $NewDataSet =array();  
      foreach ($TableData as $Row) {
        $CLSLoad = new mensio_notifications();
        $TableData = $CLSLoad->LoadNotifications(); 
        if ($Row->informed == 0){
          $Row->informed = 'NO';
        } else {
          $Row->informed = 'YES';
        }
        $NewDataSet[] = $Row;
      }
      $tbl->Set_Searchable(false);
      $tbl->Set_ActivePage($InPage);
      $tbl->Set_ActiveRows($InRows);
      $tbl->Set_BulkActions(array(
       '0'=>'Export',
       '1'=>'Delete'
      ));
      $tbl->Set_Columns(array(
        'code:Code:plain-text',
        'note_store:Note_Store:hidden',
        'note_type:Type:small',
        'note_date:Date:small',
        'note_user:User:hidden',
        'notification:Notifications:plain-text',
        'informed:Informed:small',
        'display_name:User:small',
      ));
      $RtrnTable = $tbl->CreateTable(
        'Notifications',
        $NewDataSet,
        array('code','note_date','notification','display_name','note_type','note_store','informed','note_user'),
        'code'
      );
      unset($tbl,$Data);
      return $RtrnTable;
  } 
  public function DeleteNotificationData() {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','DataSet'=>'');
    $Notification = new mensio_notifications();
    if (!$Notification->DeleteNotifications()) {
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] = 'Notification could not be deleted';
        }else{
          $Notification->LoadNotificationDataSet();
          $NotificationData->GetNotificationDataTable($InPage=0,$InRows=10); 
          $RtrnData['ERROR'] === 'FALSE'; 
          $RtrnData['Message'] = 'Notifications deleted successfully';
          $RtrnData['DataSet'] = $NotificationData;
    }
    $this->LoadNotificationDataSet();
    $RtrnData = $this->GetNotificationDataTable($InPage=1,$InRows=10);
    return $RtrnData;
  }
  public function DeleteMultiNotificationData($MultCodes){
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');   
    $MultNotification = new mensio_notifications();
    $MyTable = explode(";",$MultCodes);
    foreach($MyTable as $Row){
       if ($Row !== ''){
        if ($MultNotification->Set_Code($Row)){
            if ($MultNotification->DeleteMultiNotifications()){          
                $RtrnData['ERROR'] === 'FALSE'; 
                $RtrnData['Message'] = 'Notifications deleted successfully';
            }else{
                $RtrnData['ERROR'] = 'TRUE';
                $RtrnData['Message'] = 'Notification could not be deleted';
            }
        }
       }
    }
    $this->LoadNotificationDataSet();
    $RtrnData = $this->GetNotificationDataTable();
    return $RtrnData;
  }
  public function ExportToPDF() {
    $pdf = new tFPDF();
    $pdf->AddPage();
    $pdf->AddFont('DejaVu','','DejaVuSansCondensed.ttf',true);
    $pdf->SetFont('DejaVu','',14);
    $txt = 'This is a text';
    $pdf->Write(8,$txt);
    $pdf->SetFont('Arial','',14);
    $pdf->Ln(10);
    $pdf->Write(5,'The file size of this PDF is only 12 KB.');
    return $pdf->Output('test','D');
  }
}