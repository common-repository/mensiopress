<?php
function mensio_Install_Page() {
	global $wpdb;
	$DsplForm = '
<div class="wrap">
<div id="MsgWrap"></div>
	<h1>'.ucfirst(strtolower(MENSIO_PLGTITLE)).' <small>Installation page</small></h1>
	<p>Installation Options Page</p>
	<hr>
	<div id="FormDiv" class="InstallForm">
			<div class="FormInfoDiv">
        <img class="InstallLogo" src="'.plugins_url('mensiopress/admin/icons/default/mensiopress-logo-landscape-500.png').'" alt="Mensio Image">
				<p>Welcome to the setup page of the '.ucfirst(strtolower(MENSIO_PLGTITLE)).' plugin. Here we are going to ask you few simple questions in order to configure your new E-Shop.</p>
          <p><span class="Bolder">PLEASE</span> before starting the installation procedure be sure the content folder (wp-content by default) has correct permissions for writing. For the installation please set them to 777, you can revert the permissions after finishing the procedure.</p>
			<div class="DivResizer"></div>
			</div>
			<div class="FormFieldDiv">
				<label>Data Base</label>
				<input type="text" id="dbname" class="large-text InputFld" value="'.DB_NAME.'">
				<label>Username</label>
				<input type="text" id="username" class="large-text InputFld" value="'.DB_USER.'">
        <div class="secret">
          <label>Password</label>
          <input type="text" id="password" class="large-text InputFld" value="'.DB_PASSWORD.'">
        </div>
				<label>Host</label>
				<input type="text" id="host" class="large-text InputFld" value="'.DB_HOST.'">
				<label>Prefix</label>
				<input type="text" id="prefix" class="large-text InputFld" value="'.$wpdb->prefix.'">
			<div class="DivResizer"></div>
			</div>
			<div class="BtnCtrlDiv">
				<input id="BtnSetDB" class="btn button-primary" type="submit" value="Start Installation"/>
			<div class="DivResizer"></div>
			</div>
	<div class="DivResizer"></div>
	</div>
	<div id="DisplayDiv" class="InstallDspl">
		<div id="instmsg" class="FormInfoDiv WaitDiv">
			<p>Please wait while we setup the necessary tables.</p>
		<div class="DivResizer"></div>
		</div>
		<div id="LdgDiv" class="InstInfo WaitDiv">
      <div id="cogsdiv">
        <div id="myProgress"> <div id="myBar"></div> </div>
				<div id="MessagesDisplay" class="MessageDiv"></div>
			</div>
			<div class="DivResizer"></div>
      <div id="RefBtnDiv">
        <div id="BtnReload" class="btn button-primary">!!!! Click Me to Continue !!!!</div>
      </div>
		</div>
	<div class="DivResizer"></div>
	</div>
<div class="DivResizer"></div>
</div>';
	echo $DsplForm;
}
add_action('wp_ajax_mensio_install_Settings', 'mensio_install_Settings');
add_action('wp_ajax_mensio_install_settings_values', 'mensio_install_settings_values');
add_action('wp_ajax_mensio_install_Customer', 'mensio_install_Customer');
add_action('wp_ajax_mensio_install_customer_values', 'mensio_install_customer_values');
add_action('wp_ajax_mensio_install_product', 'mensio_install_product');
add_action('wp_ajax_mensio_install_product_values', 'mensio_install_product_values');
add_action('wp_ajax_mensio_install_sales', 'mensio_install_sales');
add_action('wp_ajax_mensio_install_sales_values', 'mensio_install_sales_values');
add_action('wp_ajax_mensio_install_lock_complete', 'mensio_install_lock_complete');
function mensio_install_Settings() {
	$RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $DataString = $_REQUEST['DataString'];
    $MnsInstall = new mensio_Install_Functions();
    $RtrnData = $MnsInstall->CheckDataStringValues($DataString);
    if ($RtrnData['ERROR'] === 'FALSE') {
      $RtrnData = $MnsInstall->StartMnsInstallSettings();
    }
    unset($MnsInstall);
    $RtrnData = json_encode($RtrnData);
  }
	echo $RtrnData;
	die();
}
function mensio_install_settings_values() {
	$RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $MnsInstall = new mensio_Install_Functions();
    $RtrnData = $MnsInstall->InstallSettingsBasicValues();
    unset($MnsInstall);
    $RtrnData = json_encode($RtrnData);
  }
	echo $RtrnData;
	die();
}
function mensio_install_Customer() {
	$RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $MnsInstall = new mensio_Install_Functions();
    $RtrnData = $MnsInstall->StartMnsInstallCustomer();
    unset($MnsInstall);
    $RtrnData = json_encode($RtrnData);
  }
	echo $RtrnData;
	die();
}
function mensio_install_customer_values() {
	$RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $MnsInstall = new mensio_Install_Functions();
    $RtrnData = $MnsInstall->InstallCustomerBasicValues();
    unset($MnsInstall);
    $RtrnData = json_encode($RtrnData);
  }
	echo $RtrnData;
	die();
}
function mensio_install_product() {
	$RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $MnsInstall = new mensio_Install_Functions();
    $RtrnData = $MnsInstall->StartMnsInstallProduct();
    unset($MnsInstall);
    $RtrnData = json_encode($RtrnData);
  }
	echo $RtrnData;
	die();
}
function mensio_install_product_values() {
	$RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $MnsInstall = new mensio_Install_Functions();
    $RtrnData = $MnsInstall->InstallProductBasicValues();
    unset($MnsInstall);
    $RtrnData = json_encode($RtrnData);
  }
	echo $RtrnData;
	die();
}
function mensio_install_sales() {
	$RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $MnsInstall = new mensio_Install_Functions();
    $RtrnData = $MnsInstall->StartMnsInstallSales();
    unset($MnsInstall);
    $RtrnData = json_encode($RtrnData);
  }
	echo $RtrnData;
	die();
}
function mensio_install_sales_values() {
	$RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $MnsInstall = new mensio_Install_Functions();
    $RtrnData = $MnsInstall->InstallSalesBasicValues();
    unset($MnsInstall);
    $RtrnData = json_encode($RtrnData);
  }
	echo $RtrnData;
	die();
}
function mensio_install_lock_complete() {
	$RtrnData = '';
  if ((defined('WPINC')) && (current_user_can('manage_options'))) {
    $MnsInstall = new mensio_Install_Functions();
    $RtrnData = $MnsInstall->CompleteInstallation();
    unset($MnsInstall);
    $RtrnData = json_encode($RtrnData);
  }
	echo $RtrnData;
	die();
}
