'use strict';
var $sec = jQuery('#_wpnonce').val()+'::'+jQuery('input[name=_wp_http_referer]').val();
function Mensio_Save_Delivery_Data() {
  var $pod = jQuery('#FLD_PoD').val();
  var $active = jQuery('#FLD_DlvrActive').val();
  var $lang = jQuery('#FLD_lang_Delivery').val();
  var $dscr = jQuery('#FLD_DlvrDesc').val();
  var $notes = jQuery('#FLD_DlvrNotes').val();
  var $ShipOpt = '';
  var $Options = jQuery('.FLD_ShipOptn');
  for(var i = 0; i < $Options.length; i++){
    if ($ShipOpt === '') {
      $ShipOpt = jQuery($Options[i]).attr('id') + ':' + jQuery($Options[i]).val();
    } else {
      $ShipOpt = $ShipOpt + ';' + jQuery($Options[i]).attr('id') + ':' + jQuery($Options[i]).val();
    }
  }
  if (($dscr !=='') && ($notes !== '')) {
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Update_PayOnDelivery_Data',
        'PoD': $pod,
        'active' : $active,
        'Lang':$lang,
        'Descr':$dscr,
        'Notes':$notes,
        'ShipOpt':$ShipOpt
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        Mensio_Append_New_PopUp(data.Message);
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  }
}
function Mensio_Save_Bank_Data() {
  var $pay = jQuery('#FLD_BankDep').val();
  var $active = jQuery('#FLD_BankActive').val();
  var $lang = jQuery('#FLD_lang_Bank').val();
  var $dscr = jQuery('#FLD_BankDesc').val();
  var $notes = jQuery('#FLD_BankNotes').val();
  if (($dscr !=='') && ($notes !== '')) {
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Update_BankDeposit_Data',
        'Pay': $pay,
        'Active' : $active,
        'Lang':$lang,
        'Descr':$dscr,
        'Notes':$notes
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        Mensio_Append_New_PopUp(data.Message);
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  }
}
function Mensio_Save_Gateway_Data() {
  var $tab = jQuery('#ActiveTab').val();
  var $pay = jQuery('#FLD_'+$tab+'Dep').val();
  var $active = jQuery('#FLD_'+$tab+'Active').val();
  var $lang = jQuery('#FLD_lang_'+$tab).val();
  var $dscr = jQuery('#FLD_'+$tab+'Desc').val();
  var $notes = jQuery('#FLD_'+$tab+'Notes').val();
  var $Fields = jQuery('.'+$tab+'Flds');
  var $FldID = '';
  var $val = '';
  var $ValPckg = Array();
  var $Err = false;
  for (var $i=0; $i < $Fields.length; ++$i) {
    $FldID = $Fields[$i].id;
    $val = jQuery('#'+$FldID).val();
    $FldID = $FldID.replace('FLD_','');
    $FldID = $FldID.replace($tab,'');
    if ($val === '') { $Err = true; }
      else { $ValPckg.push({"Param":$FldID,"Value":$val}); }
  }
  if ($Err) {
    alert('One or more fields were empty');
  } else {
    var $Params = JSON.stringify($ValPckg);
    jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Update_Gateway_Data',
          'Gateway' : $tab,
          'Pay': $pay,
          'Active' : $active,
          'Lang': $lang,
          'Descr': $dscr,
          'Notes': $notes,
          'Params': $Params
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          Mensio_Append_New_PopUp(data.Message);
        },
        error: function(errorThrown){
          alert(errorThrown);
        }
    });
  }
}
jQuery(document).ready(function() {
  jQuery('#tabs').tabs(); // Creating tabs
  jQuery('.ui-tabs-anchor').click(function(){
    var tab = jQuery(this).attr('data-selector');
    jQuery('#ActiveTab').val(tab);
  });
  jQuery('.ActCheckbox').on('change', function() {
    if (jQuery(this).val() === '0') {
      jQuery(this).prop('checked', true);
      jQuery(this).val('1');
    } else {
      jQuery(this).prop('checked', false);
      jQuery(this).val('0');
    }
  });  
  jQuery('.FLD_ShipOptn').on('change', function() {
    if (jQuery(this).val() === '0') {
      jQuery(this).prop('checked', true);
      jQuery(this).val('1');
    } else {
      jQuery(this).prop('checked', false);
      jQuery(this).val('0');
    }
  });  
  jQuery('#DIV_Edit').on('click', '#BTN_Save', function() {
    var tab = jQuery('#ActiveTab').val();
    switch (tab) {
      case 'OnDelivery':
        Mensio_Save_Delivery_Data();
        break;
      case 'BankDeposit':
        Mensio_Save_Bank_Data();
        break;
      default:
        Mensio_Save_Gateway_Data();
        break;
    }
  });
  jQuery('#Mensio_HeadBar').on('click', '#BTN_Save_Header', function() {
    var tab = jQuery('#ActiveTab').val();
    switch (tab) {
      case 'OnDelivery':
        Mensio_Save_Delivery_Data();
        break;
      case 'BankDeposit':
        Mensio_Save_Bank_Data();
        break;
      default:
        Mensio_Save_Gateway_Data();
        break;
    }
  });
  jQuery('.BTN_Translations').on('click', function() {
    var $tab = jQuery('#ActiveTab').val();
    var $pay = '';
    switch ($tab) {
      case 'OnDelivery':
        $pay = jQuery('#FLD_PoD').val();
        break;
      case 'BankDeposit':
        $pay = jQuery('#FLD_BankDep').val();
        break;
      default:
        $pay = jQuery('#FLD_'+$tab+'Dep').val();
        break;
    }
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Load_Payment_Translations_Modal',
        'Pay': $pay
      },
      success:function(data) {
        jQuery('#MnsModal').html(data);
        jQuery('#MnsModal').toggle('slide');
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('#DIV_Edit').on('click', '#BTN_AddBankAccount', function() {
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Load_Bank_Account_Modal',
        'BnkAccnt': 'NewEntry'
      },
      success:function(data) {
        jQuery('#MnsModal').html(data);
        jQuery('#MnsModal').toggle('slide');
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('#DIV_Edit').on('click', '.EditAccBtn', function() {
    var $acc = jQuery(this).attr('id');
    $acc = $acc.replace('EditAcc_','');
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Load_Bank_Account_Modal',
        'BnkAccnt': $acc
      },
      success:function(data) {
        jQuery('#MnsModal').html(data);
        jQuery('#MnsModal').toggle('slide');
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('#DIV_Edit').on('click', '.DelAccBtn', function() {
    var answer = confirm('Are you sure you want to DELETE the entry?');
    if (answer === true) {
      var $Pay = jQuery('#FLD_BankDep').val();
      var $acc = jQuery(this).attr('id');
      $acc = $acc.replace('DelAcc_','');
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Remove_Bank_Account_Data',
          'Pay': $Pay,
          'BnkAccnt': $acc
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          if (data.ERROR === 'TRUE') {
            Mensio_Append_New_PopUp(data.Message);
          } else {
            jQuery('#BankAccountTable').html(data.AccountsTable);
          }
        },
        error: function(errorThrown){
          alert(errorThrown);
        }
      });
    }
  });
  jQuery('#MnsModal').on('blur', '.ModalFld', function() {
    var $Chgnd = false;
    var $id = '';
    var $val = '';
    var $old = '';
    var $Fields = jQuery('.ModalFld');
    for(var i = 0; i < $Fields.length; i++){
      $val = jQuery($Fields[i]).val();
      $id = jQuery($Fields[i]).attr('id');
      $id = $id.replace('MDL_FLD_','MDL_OLD_');
      $old = jQuery('#'+$id).val();
      if ($val !== $old) { $Chgnd = true; }
    }    
    if ($Chgnd) {
      jQuery('#MDL_Changes').val('1');
      jQuery('#MyMdlAlertBar').show('slow');
    } else {
      jQuery('#MDL_Changes').val('0');
      jQuery('#MyMdlAlertBar').hide('slow');
    }
  });
  jQuery('#MnsModal').on('click', '.MDL_LangSelector', function() {
    var $tab = jQuery('#ActiveTab').val();
    var $pay = '';
    var $lngfld = '';
    switch ($tab) {
      case 'OnDelivery':
        $pay = jQuery('#FLD_PoD').val();
        $lngfld = '_Delivery';
        break;
      case 'BankDeposit':
        $pay = jQuery('#FLD_BankDep').val();
        $lngfld = '_Bank';
        break;
      default:
        $pay = jQuery('#FLD_'+$tab+'Dep').val();
        $lngfld = '_'+$tab;
        break;
    }
    var $lang = jQuery(this).attr('id');
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Load_Payment_Language_Translations',
        'Payment': $pay,
        'Language': $lang
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        if (data.ERROR === 'TRUE') {
          Mensio_Append_New_PopUp(data.Message);
        } else {
          jQuery('#MDL_Changes').val('0');
          jQuery('#MyMdlAlertBar').hide('slow');
          jQuery('#MDL_FLD_lang').val(data.Language);
          jQuery('#MDL_OLD_DlvrDesc').val(data.Description);
          jQuery('#MDL_FLD_DlvrDesc').val(data.Description);
          jQuery('#MDL_OLD_DlvrNotes').val(data.Instructions);
          jQuery('#MDL_FLD_DlvrNotes').val(data.Instructions);
          jQuery('.MDL_LangSelector').removeClass('LangSelected');
          jQuery('#'+$lang).addClass('LangSelected');
       }
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('#MnsModal').on('click', '#BTN_ModalRestore', function() {
    var $id = '';
    var $val = '';
    var $Fields = jQuery('.ModalFld');
    for(var i = 0; i < $Fields.length; i++){
      $id = jQuery($Fields[i]).attr('id');
      $id = $id.replace('MDL_FLD_','MDL_OLD_');
      $val = jQuery('#'+$id).val();
      jQuery($Fields[i]).val($val);
    }    
    var $tab = jQuery('#ActiveTab').val();
    if ($tab === 'Bank') {
      var img = jQuery('#MDL_OLD_Icon').val();
      jQuery("#DispImg").attr("src",img);
    }
    jQuery('#MDL_Changes').val('0');
    jQuery('#MyMdlAlertBar').hide('slow');
  });
  jQuery('#MnsModal').on('click', '#BTN_ModalTransSave', function() {
    var $tab = jQuery('#ActiveTab').val();
    var $fld = '';
    var $pay = '';
    var $lngfld = '';
    switch ($tab) {
      case 'OnDelivery':
        $pay = jQuery('#FLD_PoD').val();
        $fld = 'Dlvr';
        $lngfld = '_Delivery';
        break;
      case 'BankDeposit':
        $pay = jQuery('#FLD_BankDep').val();
        $fld = 'Bank';
        $lngfld = '_Bank';
        break;
      default:
        $pay = jQuery('#FLD_'+$tab+'Dep').val();
        $lngfld = '_'+$tab;
        break;
    }
    var $lang = jQuery('#MDL_FLD_lang').val();
    var $desc = jQuery('#MDL_FLD_DlvrDesc').val();
    var $notes = jQuery('#MDL_FLD_DlvrNotes').val();
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Update_Payment_Language_Translations',
        'Payment': $pay,
        'Language': $lang,
        'Desc': $desc,
        'Notes': $notes
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        if (data.ERROR === 'TRUE') {
          Mensio_Append_New_PopUp(data.Message);
        } else {
          var $mainlang = jQuery('#FLD_lang'+$lngfld).val();
          if ($mainlang === $lang) {
            jQuery('#FLD_'+$fld+'Desc').val($desc);
            jQuery('#FLD_'+$fld+'Notes').val($notes);
          }
          jQuery('#MDL_Changes').val('0');
          jQuery('#MyMdlAlertBar').hide('slow');
          jQuery('#MDL_OLD_DlvrDesc').val($desc);
          jQuery('#MDL_OLD_DlvrNotes').val($notes);
       }
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('#MnsModal').on('click', '.Mns_OpenMediaModal', function() {
    if (this.window === undefined) {
      this.window = wp.media({
        title: 'Select Image',
        library: {type: 'image'},
        multiple: false,
        button: {text: 'Select'}
      });
      var self = this; // Needed to retrieve our variable in the anonymous function below
      this.window.on('select', function() {
        var $Image = self.window.state().get('selection').first().toJSON();
        jQuery('#MDL_FLD_Icon').val($Image.url);    
        jQuery("#DispImg").attr("src",$Image.url);
        jQuery('#NOSAVEWARN').show();
      });
    }
    this.window.open();
  });
  jQuery('#DIV_Edit').on('click', '.Mns_OpenMediaModal', function() {
    var $tab = jQuery('#ActiveTab').val();
    if (this.window === undefined) {
      this.window = wp.media({
        title: 'Select Image',
        library: {type: 'image'},
        multiple: false,
        button: {text: 'Select'}
      });
      var self = this; // Needed to retrieve our variable in the anonymous function below
      this.window.on('select', function() {
        var $Image = self.window.state().get('selection').first().toJSON();
        jQuery('#FLD_'+$tab+'Icon').val($Image.url);    
        jQuery('#'+$tab+'DsplIcon').attr("src",$Image.url);
        jQuery('#NOSAVEWARN').show();
      });
    }
    this.window.open();
  });
  jQuery('#DIV_Edit').on('click', '.Mns_ClearImg', function() {
    var $tab = jQuery('#ActiveTab').val();
    switch ($tab) {
      case 'OnDelivery':
      case 'BankDeposit':
        var img = jQuery('#DefImg').val();
        jQuery('#MDL_FLD_Icon').val(img);
        jQuery("#DispImg").attr("src",img);
        break;
      default:
        var img = jQuery('#DefImg_'+$tab).val();
        jQuery('#FLD_'+$tab+'Icon').val(img);
        jQuery('#'+$tab+'DsplIcon').attr("src",img);
        break;
    }
  });
  jQuery('#MnsModal').on('click', '#BTN_ModalAccountSave', function() {
    var $Pay = jQuery('#FLD_BankDep').val();
    var $Account = jQuery('#MDL_FLD_Account').val();
    var $Bank = jQuery('#MDL_FLD_Bank').val();
    var $Name = jQuery('#MDL_FLD_Name').val();
    var $Number = jQuery('#MDL_FLD_Number').val();
    var $Routing = jQuery('#MDL_FLD_Routing').val();
    var $IBAN = jQuery('#MDL_FLD_IBAN').val();
    var $Swift = jQuery('#MDL_FLD_Swift').val();
    var $Icon = jQuery('#MDL_FLD_Icon').val();
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Update_Bank_Account_Data',
        'Pay': $Pay,
        'Account': $Account,
        'Bank': $Bank,
        'Name': $Name,
        'Number': $Number,
        'Routing': $Routing,
        'IBAN': $IBAN,
        'Swift': $Swift,
        'Icon': $Icon
      },
      success:function(data) {
        data = jQuery.parseJSON(data);
        if (data.ERROR === 'TRUE') {
          Mensio_Append_New_PopUp(data.Message);
        } else {
          jQuery('#BankAccountTable').html(data.AccountsTable);
          jQuery('#MDL_Changes').val('0');
          jQuery('#MyMdlAlertBar').hide('slow');
          jQuery('#MnsModal').toggle('slide');
        }
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('#ButtonArea').on('click', '#BTN_DfltLndPages', function() {
    jQuery.ajax({
      type: 'post',
      url: ajaxurl,
      data: { 'Security': $sec,
        'action': 'mensio_ajax_Load_Default_Landing_Pages_Modal'
      },
      success:function(data) {
        jQuery('#MnsModal').html(data);
        jQuery('#MnsModal').toggle('slide');
      },
      error: function(errorThrown){
        alert(errorThrown);
      }
    });
  });
  jQuery('#MnsModal').on('click', '#BTN_UpdateLanding', function() {
    var $oldsuccess = jQuery('#MDL_OLD_Success').val();
    var $success = jQuery('#MDL_FLD_Success').val();
    var $oldfailed = jQuery('#MDL_OLD_Failed').val();
    var $failed = jQuery('#MDL_FLD_Failed').val();
    if (($success !=='') && ($failed !== '')) {
      jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Load_Update_Landing_Pages',
          'OldSuccess': $oldsuccess,
          'Success': $success,
          'OldFailed': $oldfailed,
          'Failed': $failed
        },
        success:function(data) {
          data = jQuery.parseJSON(data);
          if (data.ERROR === 'TRUE') { Mensio_Append_New_PopUp(data.Message); }
            else { location.reload(); }
        },
        error: function(errorThrown){
          alert(errorThrown);
        }
      });
    }
  });
});