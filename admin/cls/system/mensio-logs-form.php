<?php
class Mensio_Admin_System_Logs extends mensio_core_form {
  private $DataSet;
  public function __construct() {
    if (!defined('WPINC')) { die(); }
    $this->Set_MainTemplate();
    $this->LoadLogsDataSet();
    $this->ActivePage = 'Mensio_Logs';
  }
  public function Load_Page_CSS() {
    wp_enqueue_style(
     MENSIO_PLGTITLE.'-mnslogs',
     plugin_dir_url( __FILE__ ) . '../../css/mensio-admin-settings-logs.css',
     array(),
     MENSIO_VERSION,
     'all'
    );
  }
  public function Load_Page_JS() {
    wp_enqueue_script(
     MENSIO_PLGTITLE.'-mnsLogs',
     plugin_dir_url( __FILE__ ) . '../../js/mensio-admin-settings-logs.js',
     array( 'jquery' ),
     MENSIO_VERSION,
     false
    );
  }
  private function LoadLogsDataSet($InSorter='') {
    $this->DataSet = array();
    $Logs = new mensio_logs();
    $Logs->Set_Sorter($InSorter);
    $Data = $Logs->LoadLogs();
    if ((is_array($Data)) && (!empty($Data[0]))) {
      $i = 0;
      foreach ($Data as $Row) {
        $this->DataSet[$i]['code'] = $Row->code;
        $this->DataSet[$i]['logstore'] = $Row->log_store;
        $this->DataSet[$i]['logtype'] = $Row->log_type;
        $this->DataSet[$i]['logdate'] = $Row->log_date;
        $this->DataSet[$i]['log'] = $Row->log;
        ++$i;
      }
    }
    unset($Logs);
  }
  public function GetLogsDataTable($InPage=1,$InRows=0) {
    $RtrnTable = '';
    $this->LoadLogsDataSet();
    $TableData = $this->DataSet;
    if ($InRows === 0) { $InRows = $this->LoadTableDefaultRows(); }
    $tbl = new mensio_datatable();
    $tbl->Set_Searchable(false);
    $tbl->Set_ActivePage($InPage);
    $tbl->Set_ActiveRows($InRows);
    $tbl->Set_BulkActions(array(
     '0'=>'Export',
     '1'=>'Delete'
    ));
    $tbl->Set_Columns(array(
        'code:code:hidden',
        'logstore:store:hidden',
        'logtype:Type:small',
        'logdate:Date:small',
        'log:Logs:plain-text'
    ));
    $RtrnTable = $tbl->CreateTable(
            'Logs',
            $TableData,
            array('code','logstore','logdate','logtype','log',),
            'code'
    );
    unset($tbl,$Data);
    return $RtrnTable;
  }
  public function DeleteLogsData() {
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'','DataSet'=>'');
    $Logs = new mensio_logs();
    if (!$Logs->DeleteLogs()) {
          $RtrnData['ERROR'] = 'TRUE';
          $RtrnData['Message'] = 'Notification could not be deleted';
        }else{
          $Logs->LoadLogsDataSet();
          $LogsData->GetLogsDataTable(1,10); 
          $RtrnData['ERROR'] === 'FALSE'; 
          $RtrnData['Message'] = 'Notifications deleted successfully';
          $RtrnData['DataSet'] = $LogsData;
    }
    $this->LoadLogsDataSet();
    $RtrnData = $this->GetLogsDataTable(1,10);
    return $RtrnData;
  }
  public function DeleteMultiLogsData($MultCodes){
    $RtrnData = array('ERROR'=>'FALSE','Message'=>'');   
    $MultLogs = new mensio_logs();
    $MyTable = explode(";",$MultCodes);
    foreach($MyTable as $Row){
       if ($Row !== ''){
        if ($MultLogs->Set_Code($Row)){
            if ($MultLogs->DeleteMultiLogs()){          
                $RtrnData['ERROR'] === 'FALSE'; 
                $RtrnData['Message'] = 'Logs deleted successfully';
            }else{
                $RtrnData['ERROR'] = 'TRUE';
                $RtrnData['Message'] = 'Logs could not be deleted';
            }
        }
       }
    }
    $this->LoadLogsDataSet();
    $RtrnData = $this->GetLogsDataTable(1,10);
    return $RtrnData;
  }
}
