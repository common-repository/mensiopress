<?php
class mensio_datatable {
  private $Error;
  private $ErrMsg;
  private $TblID;
  private $DataSet;
  private $RecKey;
  private $CTypes;
  private $Columns;
  private $ActivePage;
  private $ActiveRows;
  private $RowSelector;
  private $PageSelectOptions;
  private $RowSelectOptions;
  private $RowSelectActive;
  private $TblFldSelector;
  private $TblFldSelectOptions;
  private $MultiSelect;
  private $Editable;
  private $Searchable;
  private $TblHead;
  private $TblBody;
  private $TblFooter;
  private $RowInfo;
  private $Sorter;
  private $DisplayOrder;
  private $BulkActions;
  private $ExtraHeaderActions;
  private $ExtraFooterActions;
  private $EditOptionsSubline;
  private $EditColumn;
  function __construct () {
    if (!defined('WPINC')) { die(); }
    $this->Error = false;
    $this->ErrMsg = '';
    $this->CTypes = array(
      'plain-text',
      'input-checkbox',
      'input-select',
      'input-text',
      'input-button',
      'img',
      'fa-icons',
      'hidden',
      'small'
    );
    $this->TblID = '';
    $this->DataSet = '';
    $this->RecKey = '';
    $this->Columns = array();
    $this->ActivePage = 1;
    $this->ActiveRows = $this->LoadTableDefaultRows();
    switch ($this->ActiveRows) {
      case '10': case '25': case '50': case '100':
        $this->RowSelector = array('10','25','50','100');
        break;
      default:
        $this->RowSelector = array($this->ActiveRows,'10','25','50','100');
        break;
    }
    $this->RowSelectActive = true;
    $this->TblFldSelector = '';
    $this->MultiSelect = true;
    $this->Editable = false;
    $this->Searchable = true;
    $this->RowSelectOptions = '';
    $this->PageSelectOptions = '';
    $this->TblFldSelectOptions = '';
    $this->TblHead = '';
    $this->TblBody = '';
    $this->TblFooter = '';
    $this->RowInfo = '';
    $this->Sorter = '';
    $this->DisplayOrder = '';
    $this->BulkActions = '';
    $this->ExtraHeaderActions = '';
    $this->ExtraFooterActions = '';
    $this->EditOptionsSubline = '';
    $this->EditColumn = '';
  }
  private function LoadTableDefaultRows() {
    $Rows = 10;
    global $wpdb;
    $prfx = $wpdb->prefix.'mns_';
    $Query = 'SELECT * FROM '.$prfx.'store';
    $Data = $wpdb->get_results($Query);
    if ((is_array($Data)) && (!empty($Data[0]))) {
      foreach ($Data as $Row) { $Rows = $Row->tblrows; }
    }
    return $Rows;
  }
  protected function ClearValue($Value,$Type='AN',$SpCh='NONE') {
    switch($Type) {
      case 'TX':
        $Patern = '[^A-Za-zΑ-Ωα-ωΆ-Ώά-ώ]';
        break;
      case 'EN':
        $Patern = '[^A-Za-z0-9]';
        break;
      case 'NM':
        $Patern = '[^0-9]';
        break;
      default:
        $Patern = '[^A-Za-z0-9Α-Ωα-ωΆ-Ώά-ώ]';
        break;
    }
    if ($SpCh != 'NONE') {
      $Patern = str_replace(']','\\'.$SpCh.']', $Patern);
    }
    $Value = mb_ereg_replace($Patern, '', $Value);
    return $Value;
  }
  final public function Set_ActivePage($Value) {
    $ValueSet = false;
    $ClrVal = $this->ClearValue($Value,'NM');
    if (mb_strlen($ClrVal) == mb_strlen($Value)) {
      $ValueSet = true;
      $this->ActivePage = $ClrVal;
    }
    return $ValueSet;
  }
  final public function Set_ActiveRows($Value) {
    $ValueSet = false;
    $ClrVal = $this->ClearValue($Value,'NM');
    if (mb_strlen($ClrVal) == mb_strlen($Value)) {
      $ValueSet = true;
      $this->ActiveRows = $ClrVal;
    }
    return $ValueSet;
  }
  final public function Set_RowSelector($Value) {
    $ValueSet = false;
    $Error = false;
    if (is_array($Value)) {
      foreach ($Value as $Option) {
        if (!is_numeric($Option)) { $Error = true; }
      }
      if (!$Error) {
        $ValueSet = true;
        $this->RowSelector = $Value;
        $this->CreateRowSelectOptions();
      }
    }
    return $ValueSet;
  }
  final public function Set_RowSelectActive($Value) {
    if ($Value === true) {
      $this->RowSelectActive = true;
    } else {
      $this->RowSelectActive = false;
    }
  }
  final public function Set_MultiSelect($Value) {
    if ($Value === true) {
      $this->MultiSelect = true;
    } else {
      $this->MultiSelect = false;
    }
  }
  final public function Set_Editable($Value) {
    if ($Value === true) {
      $this->Editable = true;
    } else {
      $this->Editable = false;
    }
  }
  final public function Set_Searchable($Value) {
    if ($Value === true) {
      $this->Searchable = true;
    } else {
      $this->Searchable = false;
    }
  }
  final public function Set_Columns($Value) {
    if (is_array($Value)) {
      $ColNum = 0;
      foreach ($Value as $Column) {
        $ColData = explode(':',$Column);
        if ((is_array($ColData)) && (count($ColData) == 3)) {
          $this->Columns[$ColNum] = $Column;
          ++$ColNum;
        }
      }
    }
  }
  final public function Set_TblFldSelector($Value) {
    $this->TblFldSelector = '';
    if (is_array($Value)) {
      $this->TblFldSelector = $Value;
    }
  }
  final public function Set_Sorter($Value) {
    $ValueSet = false;
    $ClrVal = $this->ClearValue($Value,'EN', ' ');
    if (mb_strlen($ClrVal) == mb_strlen($Value)) {
      $ValueSet = true;
      $this->Sorter = $ClrVal;
    }
    return $ValueSet;
  }
  private function Set_DisplayOrder($Value) {
    $this->DisplayOrder = '';
    if (is_array($Value)) {
      $DSOrder = $this->GetDataSetOrder();
      if (count($Value) == count($DSOrder)) {
        $this->DisplayOrder = array();
        foreach ($Value as $Column) {
          foreach ($DSOrder as $Field) {
            if ($Column == $Field) {
              $this->DisplayOrder[] = $Field;
            }
          }
        }
      }
    }
    return $this->DisplayOrder;
  }
  final public function Set_BulkActions($Value) {
    $this->BulkActions = '';
    if (is_array($Value)) {
      $this->BulkActions = '
        <select id="FLD_[---TBLID---]_BASelector_[---AREA---]" class="Bulk_Selector">
          <option value="" selected>Bulk Actions</option>';
      foreach ($Value as $Key => $Description) {
        $this->BulkActions .= '<option value="'.$Key.'">'.$Description.'</option>';
      }
      $this->BulkActions .= '
        </select>
        <button id="BTN_[---TBLID---]_BulkActions_[---AREA---]" class="button BTN_BulkActions">
          Apply
        </button>';
    }
  }
  final public function Set_EditColumn($Value) {
    $ValueSet = false;
    $ClrVal = $this->ClearValue($Value,'EN');
    if (mb_strlen($ClrVal) == mb_strlen($Value)) {
      $ValueSet = true;
      $this->EditColumn = $ClrVal;
    }
    return $ValueSet;
  }
  final public function Set_EditOptionsSubline($Value) {
    $this->EditOptionsSubline = '';
    if (is_array($Value)) {
      $this->EditOptionsSubline = '<div id="EdOpt_[---TBLID---]_[---KEY---]" class="Mns_Subline">';
      foreach ($Value as $Option) {
        $ClrVal = $this->ClearValue($Option,'EN',' ');
        if (mb_strlen($ClrVal) == mb_strlen($Option)) {
          $this->EditOptionsSubline .= '<span id="EdOpt_[---TBLID---]_'.$ClrVal.'_[---KEY---]"
                class="Mns_Subline_EditOption">'.$ClrVal.'</span> | ';
        }
      }
      $this->EditOptionsSubline .= '</div>';
      $this->EditOptionsSubline = str_replace(' | </div>', '</div>', $this->EditOptionsSubline);
    }
  }
  final public function Set_ExtraActions($Options,$Area='both') {
    $Selects = '';
    if ((is_array($Options)) && (!empty($Options[0]))) {
      foreach ($Options as &$Option) {
        $Selects .= '
          <select id="FLD_[---TBLID---]_XTRSelector_[---AREA---]_'.$Option['name'].'" class="Extra_Selector">
            '.$Option['options'].'
          </select>';
      }
    }
    switch ($Area) {
      case 'both':
        $this->ExtraHeaderActions = $Selects;
        $this->ExtraFooterActions = $Selects;
        break;
      case 'header':
        $this->ExtraHeaderActions = $Selects;
        break;
      case 'footer':
        $this->ExtraFooterActions = $Selects;
        break;
    }
  }
  private function AddBulkActions() {
    $this->BulkActions = str_replace('[---TBLID---]', $this->TblID, $this->BulkActions);
    $this->ExtraHeaderActions = str_replace('[---TBLID---]', $this->TblID, $this->ExtraHeaderActions);
    $this->ExtraFooterActions = str_replace('[---TBLID---]', $this->TblID, $this->ExtraFooterActions);
  }
  private function GetDataSetOrder() {
    $DSOrder = array();
    if (is_array($this->DataSet)) {
      foreach ($this->DataSet as $ROW) {
        foreach ($ROW as $field => $value) {
          $DSOrder[] = $field;
        }
        break;
      }
    }
    return $DSOrder;
  }
  final public function Get_Sorter() {
    return $this->Sorter;
  }
  private function DrawRowSelectOptions() {
    $this->RowSelectOptions = '';
    foreach ($this->RowSelector as $Option) {
     $this->RowSelectOptions .= '<option value="'.$Option.'">'.$Option.'</option>';
    }
    $this->RowSelectOptions = str_replace(
     'value="'.$this->ActiveRows.'"',
     'value="'.$this->ActiveRows.'" selected',
     $this->RowSelectOptions
    );
  }
  private function DrawPageSelectOptions() {
    $this->PageSelectOptions = '';
    if (is_array($this->DataSet)) {
      $Pages = ceil(count($this->DataSet) / $this->ActiveRows);
      for ($i=1;$i<=$Pages;$i++) {
        $this->PageSelectOptions .='<option value="'.$i.'">Page '.$i.' of '.$Pages.'</option>';
      }
      $this->PageSelectOptions = str_replace(
        'value="'.$this->ActivePage.'"',
        'value="'.$this->ActivePage.'" selected',
        $this->PageSelectOptions
      );
    } else {
      $this->PageSelectOptions .='<option value="1">Page 1 of 1</option>';
    }
  }
  private function DrawTblTh() {
    $ThCols = '';
    $Check = true;
    $Multi = '';
    if ($this->MultiSelect) {
      $Multi = '
        <th class="Mns_Tbl_Body_Table_Ctrl_Col Mns_Tbl_Head_Check_Col">
          <input type="checkbox" id="HeadCheck_'.$this->TblID.'" class="Mns_Tbl_Head_Check">
        </th>';
    }
    $Edit = '';
    if ($this->Editable) {
      $Edit = '<th class="Mns_Tbl_Body_Table_Ctrl_Col"></th>';
    }
    if ($this->CheckIfCorrectColumns()) {
      if ($this->DisplayOrder != '') {
        foreach ($this->DisplayOrder as $DspOrder) {
          foreach ($this->Columns as $Column) {
            $Column = explode(':',$Column);
            if ((is_array($Column)) && (count($Column) == 3)) {
              if (($this->RecKey != $Column[0]) && ($DspOrder == $Column[0])) {
                $ThCols .= $this->DrawTblHeadColumn($Column[0],$Column[2],$Column[1]);
              }
            } else {
              $Check = false;
            }
          }
        }
      } else {
        foreach ($this->Columns as $Column) {
          $Column = explode(':',$Column);
          if ((is_array($Column)) && (count($Column) == 3)) {
            if ($this->RecKey != $Column[0]) {
              $ThCols .= $this->DrawTblHeadColumn($Column[0],$Column[2],$Column[1]);
            }
          } else {
            $Check = false;
          }
        }
      }
      $ThCols = '<tr>'.$Multi.$ThCols.$Edit.'</tr>';
    } else {
      $Check = false;
    }
    if (!$Check) {
      foreach ($this->DataSet as $ROW) {
        if ($this->DisplayOrder != '') {
          foreach ($this->DisplayOrder as $DspOrder) {
            foreach ($ROW as $field => $value) {
              if (($this->RecKey != $field) && ($DspOrder == $field)) {
                $ThCols .= $this->DrawTblHeadColumn($this->CTypes[0],$this->CTypes[0],$field);
              }
            }
          }
        } else {
          foreach ($ROW as $field => $value) {
            if ($this->RecKey != $field) {
              $ThCols .= $this->DrawTblHeadColumn($this->CTypes[0],$this->CTypes[0],$field);
            }
          }
        }
        break;
      }
      $ThCols = '<tr>'.$Multi.$ThCols.$Edit.'</tr>';
    }
    return $ThCols;
  }
  private function CheckIfCorrectColumns() {
    $EqualRows = false;
    $DSCols = 0;
    foreach ($this->DataSet as $ROW) {
      foreach ($ROW as $field => $value) { ++$DSCols; }
      break;
    }
    if ($DSCols == count($this->Columns)) { $EqualRows = true; }
    return $EqualRows;
  }
  private function DrawTblHeadColumn($TblCol, $Type,$Name) {
    $ColHead = $Name;
    $SorterCls = '';
    if ($this->Sorter == $TblCol) {
      $SorterCls = 'Mns_Tbl_Head_Active_Sorter';
    }
    switch ($Type) {
      case 'hidden':
        $Class = 'Mns_Tbl_Body_Table_Hdn_Col_Head';
        break;
      case 'input-button':
      case 'img':
        $Class = 'Mns_Tbl_Body_Table_Img_Col_Head';
        break;
      case 'input-checkbox':
      case 'fa-icons':
        $Class = 'Mns_Tbl_Body_Table_Ctrl_Col_Head';
        $ColHead = '<div id="'.$this->TblID.'_'.$TblCol.'_Header"
          class="Mns_Tbl_Head_Sorter '.$SorterCls.'">'.$Name.'</div>';
        break;
      case 'input-text':
      case 'input-select':
        $Class = 'Mns_Tbl_Body_Table_Column_Head';
        $ColHead = '<div id="'.$this->TblID.'_'.$TblCol.'_Header"
          class="Mns_Tbl_Head_Sorter '.$SorterCls.'">'.$Name.'</div>';
        break;
      case 'small':
        $Class = 'Mns_Tbl_Body_Table_Column_Head_small';
        $ColHead = '<div id="'.$this->TblID.'_'.$TblCol.'_Header"
          class="Mns_Tbl_Head_Sorter '.$SorterCls.'">'.$Name.'</div>';
        break;
      default:
        $Class = 'Mns_Tbl_Body_Table_Column_Head';
        $ColHead = '<div id="'.$this->TblID.'_'.$TblCol.'_Header"
          class="Mns_Tbl_Head_Sorter '.$SorterCls.'">'.$Name.'</div>';
        break;
    }
    return '<th class="'.$Class.'">'.$ColHead.'</th>';
  }
  private function DrawTblTd() {
    $TdLines = '';
    $Max = $this->ActivePage * $this->ActiveRows;
    $Min = $Max - $this->ActiveRows;
    $Line = 1;
    $LineType = '1';
    $DspMin = 0;
    $DspMax = 0;
    foreach ($this->DataSet as $ROW) {
      if (($Line > $Min) && ($Line <= $Max)) {
        if ($DspMin == 0) { $DspMin = $Line; }
        $CodeKey = $this->GetCodeKey($ROW);
        $Options = $this->CheckColOptions($CodeKey);
        if ($this->DisplayOrder == '' ) {
          $TdCols = $this->DrawTblTdRaw($ROW,$CodeKey);
        } else {
          $TdCols = $this->DrawTblTdOrder($ROW,$CodeKey);
        }
        $TdLines .= '
          <tr class="Mns_Tbl_Body_Table_Line_'.$LineType.'">
            '.$Options['Multi'].$TdCols.$Options['Edit'].'
          </tr>';
        if ($LineType == '1') { $LineType = '2'; }
          else { $LineType = '1'; }
        $DspMax = $Line;
      }
        ++$Line;
    }
    $this->RowInfo = '
      <span>
        '.$DspMin.' to '.$DspMax.' of '.count($this->DataSet).' items
      </span>';
    return $TdLines;
  }
  private function DrawTblTdRaw($ROW,$CodeKey) {
    $TdCols = '';
    foreach ($ROW as $field => $value) {
      if ($this->RecKey != $field) {
        $Type = $this->GetTdColType($field);
        $TdCols .= $this->DrawTblColumn($CodeKey,$field,$Type,$value);
      }
    }
    return $TdCols;
  }
  private function DrawTblTdOrder($ROW,$CodeKey) {
    $TdCols = '';
    foreach ($this->DisplayOrder as $Col) {
      if ($this->RecKey != $Col) {
        foreach ($ROW as $field => $value) {
          if ($Col == $field) {
           $Type = $this->GetTdColType($field);
           $TdCols .= $this->DrawTblColumn($CodeKey,$field,$Type,$value);
          }
        }
      }
    }
    return $TdCols;
  }
  private function GetCodeKey($ROW) {
    $CodeKey = '';
    foreach ($ROW as $field => $value) {
      if ($this->RecKey == $field) { $CodeKey = $value; }
    }
    return $CodeKey;
  }
  private function CheckColOptions($CodeKey) {
    $Options = array('Multi'=>'','Edit'=>'');
    if ($this->MultiSelect) {
      $Options['Multi'] = '
        <td class="Mns_Tbl_Body_Table_Ctrl_Col Mns_Tbl_Head_Check_Col">
          <input id="'.$this->TblID.'_'.$CodeKey.'_CheckBox" type="checkbox"
            class="Mns_Tbl_Body_Table_Ctrl_Check" value="">
        </td>';
    }
    if ($this->Editable) {
      $Options['Edit'] = '
        <td class="Mns_Tbl_Body_Table_Ctrl_Col">
          <button id="'.$this->TblID.'_'.$CodeKey.'_BtnEdit"
            class="btn Mns_Tbl_Btn"
            data-toggle="tooltip" title="Edit Record">
            <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
          </button>
        </td>';
    }
    return $Options;
  }
  private function GetTdColType($field) {
    $Type = $this->CTypes[0];
    if (is_array($this->Columns)) {
      foreach ($this->Columns as $Row) {
        $Column = explode(':',$Row);
        if ($field == $Column[0]) { $Type = $Column[2]; }
      }
    }
    return $Type;
  }
  private function GetFldSlctOptions($field,$value) {
    $this->TblFldSelectOptions = '';
    if (is_array($this->TblFldSelector)) {
      foreach ($this->TblFldSelector[$field] as $Key => $Display) {
        $this->TblFldSelectOptions .= '
          <option value="'.$Key.'">
            '.$Display.'
          </option>';
      }
    }
    $this->TblFldSelectOptions = str_replace(
      'value="'.$value.'"',
      'value="'.$value.'" selected',
      $this->TblFldSelectOptions
    );
    if ($this->TblFldSelectOptions == '') {
      $this->TblFldSelectOptions = '<option value="" selected>No Options</option>';
    }
  }
  private function DrawTblColumn($codeKey,$field,$type,$value) {
    $Column = '';
    switch ($type) {
      case 'hidden':
        $Column = '<td class="Mns_Tbl_Body_Table_Hdn_Col">'.$value.'</td>';
        break;
      case 'input-checkbox':
        $Checked = '';
        if ($value != '0') { $Checked = 'checked'; }
        $Column = '
          <td class="Mns_Tbl_Body_Table_Ctrl_Col">
            <input id="'.$this->TblID.'_'.$field.'_'.$codeKey.'"
              type="checkbox" class="Mns_Tbl_Body_Table_Fld_Check"
              value="'.$value.'" '.$Checked.'>
          </td>';
        break;
      case 'input-button':
        $Column = '
          <td class="Mns_Tbl_Body_Table_Ctrl_Col">
            <input id="'.$this->TblID.'_'.$field.'_'.$codeKey.'"
              type="submit" class="btn Mns_Tbl_Fld_Btn"
              value="'.$value.'">
          </td>';
        break;
      case 'img':
        $Img = $value;
        if ($value != 'No Image') {
          $Img = '<div class="Mns_Tbl_Fld_Img">
              <img src="'.$value.'" alt="'.$value.'">
            </div>';
        }
        $Column = '
          <td class="Mns_Tbl_Body_Table_Img_Col">
            '.$Img.'
          </td>';
        break;
      case 'fa-icons':
        $Column = '
          <td class="Mns_Tbl_Body_Table_Ctrl_Col">
            <i class="fa '.$value.'" aria-hidden="true"></i>
          </td>';
        break;
      case 'input-text':
        $Column = '
          <td class="Mns_Tbl_Body_Table_Column">
            <input id="'.$this->TblID.'_'.$field.'_'.$codeKey.'"
              type="checkbox" class="Mns_Tbl_Body_Table_Fld_Text"
              value="'.$value.'">
          </td>';
        break;
      case 'input-select':
        $this->GetFldSlctOptions($field,$value);
        $Column = '
          <td class="Mns_Tbl_Body_Table_Column">
              <select id="'.$this->TblID.'_'.$field.'_'.$codeKey.'" class="Mns_Tbl_Body_Table_Fld_Select">
              '.$this->TblFldSelectOptions.'
            </select>
          </td>';
        break;
      case 'small':
        $Column = '<td class="Mns_Tbl_Body_Table_Column_small">'.$value.'</td>';
        break;
      default:
        if ($this->EditColumn === $field) {
          $OptionsLine = str_replace('[---TBLID---]', $this->TblID, $this->EditOptionsSubline);
          $OptionsLine = str_replace('[---KEY---]', $codeKey, $OptionsLine);
          $Column = '
            <td id="'.$this->TblID.'_'.$field.'_'.$codeKey.'" class="Mns_Tbl_Body_Table_Column Mns_Editable_Column">
                <span class="Mns_Editable_Column_Text">'.$value.'</span>
                <div class="Mns_Subline_Wrapper">'.$OptionsLine.'</div>
            </td>';
        } else {
          $Column = '<td class="Mns_Tbl_Body_Table_Column">'.$value.'</td>';
        }
        break;
    }
    return $Column;
  }
  private function DrawTableHead() {
    $ShowRS = '';
    $Resize = '';
    if (!$this->RowSelectActive) {
      $ShowRS = 'Mns_Invisible';
      $Resize = 'Mns_Half';
    }
    $BulkActions = str_replace('[---AREA---]','Header',$this->BulkActions);
    $ExtraHeaderActions = str_replace('[---AREA---]','Header',$this->ExtraHeaderActions);
    $SearchDiv = '';
    if ($this->Searchable) {
      $SearchDiv = '<div class="Mns_Fld_Search_Div">
              <input id="'.$this->TblID.'_SearchFld" type="text" class="Mns_Fld_Search" placeholder="Search ...">
              <div id="'.$this->TblID.'_BtnSearch" class="button Mns_Btn_Search">Search '.$this->TblID.'</div>
            </div>';
    }
    $this->TblHead = '
          <div class="Mns_Tbl_Header">
            '.$SearchDiv.'
            <div class="Mns_Tbl_DivResizer"></div>
            <div id="#Mns_Table_'.$this->TblID.'_Header_ActionsBar" class="Mns_Table_ActionsBar Mns_Header_ActionsBar">
              <div class="Mns_Bulk_Selector_Div">
                '.$BulkActions.'
              </div>
              '.$ExtraHeaderActions.'
              <div class="Mns_Tbl_Footer_Pagination_Div '.$Resize.'">
                <div id="'.$this->TblID.'_Info_Header" class="Mns_Tbl_Footer_Info_Div '.$Resize.'">
                  '.$this->RowInfo.'
                </div>
                <div class="Mns_Row_Selector_Div '.$ShowRS.'">
                  <select id="'.$this->TblID.'_RowSelector_Header" class="Mns_Row_Selector">
                    '.$this->RowSelectOptions.'
                  </select>
                </div>
                <div class="Mns_Tbl_Footer_Pagination_Wrapper">
                  <div id="'.$this->TblID.'_BtnBack_Header" class="Mns_Tbl_Footer_Pagination Mns_Tbl_Footer_Pagination_Previous">
                    &#60;
                  </div>
                  <div class="Mns_Tbl_Footer_Pagination_PageSelector_Div">
                    <select id="'.$this->TblID.'_PageSelector_Header" class="Mns_Tbl_Footer_Pagination_PageSelector">
                      '.$this->PageSelectOptions.'
                    </select>
                  </div>
                  <div id="'.$this->TblID.'_BtnNext_Header" class="Mns_Tbl_Footer_Pagination Mns_Tbl_Footer_Pagination_Next">
                    &#62;
                  </div>
                </div>
              </div>
              <div class="Mns_Tbl_DivResizer"></div>
            </div>
            <div class="Mns_Tbl_DivResizer"></div>
          </div>';
  }
  private function DrawTableBody() {
    if ($this->TblBody !== 'No Records Found') {
      $TblHead = $this->DrawTblTh();
      $TblFoot = str_replace('id="HeadCheck_','id="FootCheck_',$TblHead);
      $TblFoot = str_replace('_Header"','_Footer"',$TblFoot);
      $this->TblBody = '
            <div class="Mns_Tbl_Body">
              <input id="'.$this->TblID.'_MultiSelectTblIDs" type="hidden" value="">
              <input id="'.$this->TblID.'_SorterCol" type="hidden" value="'.$this->Sorter.'">
              <table class="Mns_Tbl_Body_Table">
                <thead id="'.$this->TblID.'-Head">
                  '.$TblHead.'
                </thead>
                <tbody id="'.$this->TblID.'-Body">
                  '.$this->DrawTblTd().'
                </tbody>
                <tfoot id="'.$this->TblID.'-Foot">
                  '.$TblFoot.'
                </tfoot>
              </table>
              <div class="Mns_Tbl_DivResizer"></div>
            </div>';
    } else {
      $this->TblBody = '
            <div class="Mns_Tbl_Body">
              <input id="'.$this->TblID.'_MultiSelectTblIDs" type="hidden" value="">
              <input id="'.$this->TblID.'_SorterCol" type="hidden" value="'.$this->Sorter.'">
                <div class="NoRecors">'.$this->TblBody.'</div>
              <div class="Mns_Tbl_DivResizer"></div>
            </div>';
    }
  }
  private function DrawTableFooter() {
    $ShowRS = '';
    $Resize = '';
    if (!$this->RowSelectActive) {
      $ShowRS = 'Mns_Invisible';
      $Resize = 'Mns_Half';
    }
    $BulkActions = str_replace('[---AREA---]','Footer',$this->BulkActions);
    $ExtraFooterActions = str_replace('[---AREA---]','Footer',$this->ExtraFooterActions);
    $this->TblFooter = '
          <div class="Mns_Tbl_Footer">
            <div id="#Mns_Table_'.$this->TblID.'_Footer_ActionsBar" class="Mns_Table_ActionsBar Mns_Footer_ActionsBar">
              <div class="Mns_Bulk_Selector_Div">
                '.$BulkActions.'
              </div>
              '.$ExtraFooterActions.'
              <div class="Mns_Tbl_Footer_Pagination_Div '.$Resize.'">
                <div id="'.$this->TblID.'_Info_Footer" class="Mns_Tbl_Footer_Info_Div '.$Resize.'">
                  '.$this->RowInfo.'
                </div>
                <div class="Mns_Row_Selector_Div '.$ShowRS.'">
                  <select id="'.$this->TblID.'_RowSelector_Footer" class="Mns_Row_Selector">
                    '.$this->RowSelectOptions.'
                  </select>
                </div>
                <div class="Mns_Tbl_Footer_Pagination_Wrapper">
                  <div id="'.$this->TblID.'_BtnBack_Footer" class="Mns_Tbl_Footer_Pagination Mns_Tbl_Footer_Pagination_Previous">
                    &#60;
                  </div>
                  <div class="Mns_Tbl_Footer_Pagination_PageSelector_Div">
                    <select id="'.$this->TblID.'_PageSelector_Footer" class="Mns_Tbl_Footer_Pagination_PageSelector">
                      '.$this->PageSelectOptions.'
                    </select>
                  </div>
                  <div id="'.$this->TblID.'_BtnNext_Footer" class="Mns_Tbl_Footer_Pagination Mns_Tbl_Footer_Pagination_Next">
                    &#62;
                  </div>
                </div>
              </div>
            </div>
            <div class="Mns_Tbl_DivResizer"></div>
          </div>';
  }
  private function DrawTable() {
    $DataTable = '';
    if (!$this->Error) {
      $this->DrawTableBody();
      $this->DrawTableHead();
      $this->DrawTableFooter();
      $DataTable = '
      <div id="'.$this->TblID.'" class="Mns_Tbl_wrapper">
        '.$this->TblHead.'
        '.$this->TblBody.'
        '.$this->TblFooter.'
      </div>';
    } else {
      $DataTable = $this->ErrMsg;
    }
    return $DataTable;
  }
  function CreateTable( $TblID, $TblDataSet, $DspOrder='', $TblKey='uuid') {
    $ClrVal = $this->ClearValue($TblID,'EN');
    if (mb_strlen($ClrVal) == mb_strlen($TblID)) {
      $this->TblID = $ClrVal;
    } else {
     $this->Error = true;
     $this->ErrMsg .= 'Not correct id value<br>';
    }
    if (is_array($TblDataSet)) {
      if (empty($TblDataSet[0])) {
        $this->TblBody = 'No Records Found';
      } else {
        $this->DataSet = $TblDataSet;
      }
    } else {
        $this->TblBody = 'No Records Found';
    }
    $this->Set_DisplayOrder($DspOrder);
    $ClrVal = $this->ClearValue($TblKey,'EN');
    $ClrVal = $this->ClearValue($ClrVal,'TX');
    if (mb_strlen($ClrVal) == mb_strlen($TblKey)) {
      $this->RecKey = $ClrVal;
    } else {
      $this->Error = true;
      $this->ErrMsg .= 'Not correct key<br>';
    }
    $this->DrawRowSelectOptions();
    $this->DrawPageSelectOptions();
    $this->AddBulkActions();
    return $this->DrawTable();
  }
}
