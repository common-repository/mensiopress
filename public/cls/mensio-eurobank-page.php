<?php
class Mensio_Eurobank_Page {
	private $UUID;
  private $Active;
  private $Description;
  private $Instructions;
	private $ActionUrl;
  private $mid;
  private $deviceCategory;
  private $Orderid;
  private $orderDesc;
  private $orderAmount;
  private $currency;
  private $payerEmail;
  private $payerPhone;
  private $maxPayRetries;
  private $reject3dsU;
  private $extInstallmentoffset;
  private $extInstallmentperiod;
  private $extRecurringfrequency;
  private $extRecurringenddate;
  private $cssUrl;
  private $confirmUrl;
  private $cancelUrl;
  private $Digest;
  public function __construct() {
    $this->deviceCategory ='';
    $this->Orderid ='';
    $this->orderDesc ='';
    $this->orderAmount ='';
    $this->currency ='';
    $this->payerEmail ='';
    $this->payerPhone ='';
    $this->InitializeValues();
  }
	private function InitializeValues() {
    $PayMethods = new mensio_payment_methods();
    $DataSet = $PayMethods->LoadGatewayData('Eurobank');
    if ((is_array($DataSet)) && (!empty($DataSet[0]))) {
      foreach ($DataSet as $Row) {
        $this->UUID = $Row->uuid;
        $this->Active = $Row->active;
        $this->Description = $Row->description;
        $this->Instructions = $Row->instructions;
      }
    }
    if ($PayMethods->Set_UUID($this->UUID)) {
      $Parameters = $PayMethods->LoadGatewayParameters();
      if ((is_array($Parameters)) && (!empty($Parameters[0]))) {
        foreach ($Parameters as $Row) {
          switch ($Row->parameter) {
            case '01 Action url':
              $this->ActionUrl = $Row->value;
              break;
            case '02 Merchant ID':
              $this->mid = $Row->value;
              break;
            case '03 Digest Key':
              $this->Digest = $Row->value;
              break;
            case '04 Max Pay Retries':
              $this->maxPayRetries = $Row->value;
              break;
            case '05 Reject 3ds U':
              if ($Row->value === 'Yes') { $this->reject3dsU = 'Y'; }
                else { $this->reject3dsU = 'N'; }
              break;
            case '06 Return Success Page':
              if ($Row->value === '-') { $this->confirmUrl = home_url(); }
                else { $this->confirmUrl = $Row->value; }
              break;
            case '07 Return Failed Page':
              if ($Row->value === '-') { $this->cancelUrl = home_url(); }
                else { $this->cancelUrl = $Row->value; }
              break;
            case '08 CSS url':
              $this->cssUrl = '';
              if ($Row->value !== '-') { $this->cssUrl = $Row->value; }
              break;
            case '09a Installment Offset':
              $this->extInstallmentoffset = '';
              if ($Row->value !== '-') { $this->extInstallmentoffset = $Row->value; }
              break;
            case '09b Installment Period':
              $this->extInstallmentperiod = '';
              if ($Row->value !== '-') { $this->extInstallmentperiod = $Row->value; }
              break;
            case '09c Installment frequency':
              $this->extRecurringfrequency = '';
              if ($Row->value !== '-') { $this->extRecurringfrequency = $Row->value; }
              break;
            case '09d Recurring End Date':
              $this->extRecurringenddate = '';
              if ($Row->value !== '-') { $this->extRecurringenddate = $Row->value; }
              break;
          }
        }
      }
    }
    unset($PayMethods,$DataSet);    
	}
	private function ClearValue($Value,$Type='AN',$SpCh='NONE') {
    switch($Type) {
      case 'TX':
        $Patern = '[^\p{L}]';
        break;
      case 'EN':
        $Patern = '[^A-Za-z0-9]';
        break;
      case 'NM':
        $Patern = '[^0-9]';
        break;
      default:
        $Patern = '[^\p{L}\p{N}]';
        break;
    }
    if ($SpCh != 'NONE') {
      $Patern = str_replace(']','\\'.$SpCh.']', $Patern);
    }
    $Value = mb_ereg_replace($Patern, '', $Value);
    return $Value;
	}
	private function ClearUUID($Value) {
		$RtrnVal = false;
		if (mb_strlen($Value) == 36) {
			$ClrVal = $this->ClearValue($Value,'EN','-');
			if (mb_strlen($ClrVal) == 36) {
				$ValArray = explode('-',$ClrVal);
				if ((is_array($ValArray)) && (count($ValArray) == 5)) {
					$RtrnVal = $ClrVal;
				}
			}
		}
		return $RtrnVal;
	}
	public function LoadEurobankForm() {
    $FLD_Installm = '';
    $FLD_CSSurl = '';
    if ($this->extInstallmentoffset !== '') {
      $FLD_Installm = '<input type="hidden" name="extInstallmentoffset" value="'.$this->extInstallmentoffset.'"/>
			<input type="hidden" name="extInstallmentperiod" value="'.$this->extInstallmentperiod.'"/>
			<input type="hidden" name="extRecurringfrequency" value="'.$this->extRecurringfrequency.'"/>
			<input type="hidden" name="extRecurringenddate" value="'.$this->extRecurringenddate.'"/>';
    }
    if ($this->cssUrl !== '') {
      $FLD_CSSurl = '<input type="hidden" name="cssUrl" value="'.$this->cssUrl.'"/>';
    }
		$Form = '
		<form id="shopform1" name="demo" method="POST" action="'.$this->ActionUrl.'" accept-charset="UTF-8" >
			<input type="hidden" name="mid" value="'.$this->mid.'"/>
			<input type="hidden" name="lang" value="GR"/>
			<input type="hidden" name="deviceCategory" value="'.$this->deviceCategory.'"/>
			<input type="hidden" name="orderid" value="'.$this->Orderid.'"/>
			<input type="hidden" name="orderDesc" value="'.$this->orderDesc.'"/>
			<input type="hidden" name="orderAmount" value="'.$this->orderAmount.'"/>
			<input type="hidden" name="currency" value="'.$this->currency.'"/>
			<input type="hidden" name="payerEmail" value="'.$this->payerEmail.'"/>
			<input type="hidden" name="payerPhone" value="'.$this->payerPhone.'"/>
			<input type="hidden" name="maxPayRetries" value="'.$this->maxPayRetries.'"/>
			<input type="hidden" name="reject3dsU" value="'.$this->reject3dsU.'"/>
      '.$FLD_Installm.'
      '.$FLD_CSSurl.'
			<input type="hidden" name="confirmUrl" value="'.$this->confirmUrl.'"/>
			<input type="hidden" name="cancelUrl" value="'.$this->cancelUrl.'"/>
			<input type="hidden" name="digest" value="'.$this->Digest.'"/>
		</form>';
    return $Form;
	}
}
function mensiopress_OBJ_Eurobank() {
  $RtrnData = '';
  $EBGW = new Mensio_Eurobank_Page();
  $RtrnData = $EBGW->LoadEurobankForm();
  unset($EBGW);
  return $RtrnData;
}