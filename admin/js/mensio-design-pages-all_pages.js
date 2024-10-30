
    function ChangeHome(val){
        var $sec = jQuery('#_wpnonce').val()+'::'+jQuery('input[name=_wp_http_referer]').val();
        var OptionValue=val;
        $.ajax({
            type: 'post',
            url: ajaxurl,
            data: { 'Security': $sec,
                'action': 'mensiopress_MensioUpdateHomePage',
                'OptionValue' : OptionValue
            }
        });
    }
jQuery("document").ready(function(){
    var $sec = jQuery('#_wpnonce').val()+'::'+jQuery('input[name=_wp_http_referer]').val();
    jQuery("#pageSettingsButton").click(function(){
        jQuery("#PageSettings").show();
        jQuery("#newMensioPage").hide().height("auto");
        jQuery("#ModalBox").show();
    });
    jQuery("#exitModalBox").click(function(){
        jQuery("#ModalBox").hide();
    });
    jQuery("#newMensioPage-button").click(function(){
        jQuery("#PageSettings").hide();
        jQuery("#ModalBox").show();
        jQuery("#newMensioPage").show().height("auto");
    });
    jQuery(".exitModalBox").click(function(){
        jQuery("#newMensioPage").hide();
        jQuery("#ModalBox").hide();
    });
    jQuery("#newMensioPage #BTN_Save").click(function(){
        jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            data: { 'Security': $sec,
                'action': 'mensiopress_newMensioPage',
                'NewMensioPageTitle' : jQuery("#NewMensioPageTitle").val(),
                'NewMensioPageFunction' : jQuery("#NewMensioPageFunction").val()
            },
            success:function(data) {
                if(data){
                    window.location.href="?post="+data+"&page=mns-html-edit";
                }
            }
        });
    });
    jQuery("button.button.BTN_BulkActions").click(function(){
        var postID=false;
        if(jQuery("select.Bulk_Selector").val()=="DEL"){
            var conf=confirm("Are you sure you want to delete this Page?");
            if (conf==true){
                jQuery(".Mns_Tbl_Body_Table_Ctrl_Check:checked").each(function(){
                    postID=jQuery(this).attr("id").replace("Pages_","");
                    postID=postID.replace("_CheckBox","");
                    var ThisRow=jQuery(this).closest("tr");
                    jQuery.ajax({
                        type: 'post',
                        url: ajaxurl,
                        data: {
                            'Security': $sec,
                            'action': 'mensiopress_delMensioPage',
                            'MensioPageToDel' : postID
                        },
                        success:function(data) {
                            ThisRow.remove();
                        }
                    });
                });
            }
        }
    });
    jQuery(".MnsCustomSelect").mouseover(function(){
        jQuery(".MnsCustomSelect > .category").show().css("display","block");
    });
    jQuery(".MnsCustomSelect").mouseout(function(){
        jQuery(".MnsCustomSelect .category.inactive").css("display","none");
    });
    jQuery(".MnsCustomSelect .category").click(function(){
        jQuery(".MnsCustomSelect .category").removeClass("inactive").hide().css("display","none");
        jQuery(this).removeClass("inactive").addClass("active").show().css("display","block");
        var id=jQuery(this).attr("id").replace("MnsOption-","");
        jQuery("#NewMensioPageFunction option[value="+id+"]").attr('selected',true);
    });
    jQuery(".Mns_Subline > span").click(function(){
        var theID=jQuery(this).parent().attr("id").replace("EdOpt_Pages_","");
        if(jQuery(this).attr("id")=="EdOpt_Pages_Edit_"+theID){
            window.location.href="?post="+theID+"&page=mns-html-edit";
        }
        if(jQuery(this).attr("id")=="EdOpt_Pages_Delete_"+theID){
            var postID=jQuery(this).attr("id").replace("del-","");
            var conf=confirm("Are you sure you want to delete this Page?");
            if (conf==true){
                jQuery.ajax({
                    type: 'post',
                    url: ajaxurl,
                    data: { 'Security': $sec,
                        'action': 'mensiopress_delMensioPage',
                        'MensioPageToDel' : theID
                    },
                    success:function(data) {
                        jQuery("#EdOpt_Pages_Delete_"+theID).parent().parent().parent().parent().remove();
                    }
                });
            }
        }
    });
    jQuery("#Pages_PageSelector").attr("page","1")
    jQuery(".Mns_Tbl_Head_Sorter").click(function(){
        jQuery(".Mns_Tbl_Head_Sorter").removeClass("thisSorter");
        jQuery(this).addClass("thisSorter");
    });
    jQuery('.TBL_DataTable_Wrapper').on('click', '.Mns_Subline_EditOption',function() {
    var $EditOption = jQuery(this).attr('id');
    $EditOption = $EditOption.split('_');
    var theID=jQuery(this).parent().attr("id").replace("EdOpt_Pages_","");
    switch ($EditOption[2]) {
      case 'Edit':
          window.location.href="?post="+theID+"&page=mns-html-edit";
        break;
      case 'Delete':
        if(jQuery(this).attr("id")=="EdOpt_Pages_Edit_"+theID){
            window.location.href="?post="+theID+"&page=mns-html-edit";
        }
        if(jQuery(this).attr("id")=="EdOpt_Pages_Delete_"+theID){
            var postID=jQuery(this).attr("id").replace("del-","");
            var conf=confirm("Are you sure you want to delete this Page?");
            if (conf==true){
                jQuery.ajax({
                    type: 'post',
                    url: ajaxurl,
                    data: { 'Security': $sec,
                        'action': 'mensiopress_delMensioPage',
                        'MensioPageToDel' : theID
                    },
                    success:function(data) {
                        jQuery("#EdOpt_Pages_Delete_"+theID).parent().parent().parent().parent().remove();
                    }
                });
            }
        }
        break;
    }
  });
    jQuery("#Pages-Head input[type=checkbox],#Pages-Footer input[type=checkbox]").change(function(){
        if(jQuery(this).prop("checked")==true){
            jQuery("#Pages-Body .Mns_Tbl_Body_Table_Ctrl_Check").prop("checked",true);
            jQuery("#Pages-Head input[type=checkbox],#Pages-Footer input[type=checkbox]").prop("checked",true);
        }
        if(jQuery(this).prop("checked")==false){
            jQuery("#Pages-Body .Mns_Tbl_Body_Table_Ctrl_Check").prop("checked",false);
            jQuery("#Pages-Head input[type=checkbox],#Pages-Footer input[type=checkbox]").prop("checked",false);
        }
    });
    jQuery("#Pages .Mns_Tbl_Body_Table tr").each(function(){
        if(jQuery(this).find("td:nth-child(5)").text()!=""){
        }
    });
    jQuery("#Pages-Body input[name=mensiopage_on_front]").change(function(){
        var OptionValue=jQuery(this).val();
        $.ajax({
            type: 'post',
            url: ajaxurl,
            data: { 'Security': $sec,
                'action': 'mensiopress_MensioUpdateHomePage',
                'OptionValue' : OptionValue
            }
        });
    });
    jQuery("#PageSettings form").submit(function(){
        if(jQuery("input[name=MensioCategorySlug]").val()=="category"){
            jQuery("input[name=MensioCategorySlug]").addClass("wrongText");
            return false;
        }
    });
    jQuery("input[name=MensioCategorySlug]").on("keyup change",function(){
        if(jQuery(this).val()=="category"){
            jQuery("input[name=MensioCategorySlug]").addClass("wrongText");
        }
        else{
            jQuery("input[name=MensioCategorySlug]").removeClass("wrongText");
        }
    });
});