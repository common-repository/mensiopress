var $sec = jQuery('#_wpnonce').val()+'::'+jQuery('input[name=_wp_http_referer]').val();
    function removeBlocks(){
        $(".mns-obj-to-rem").click(function(){
            var aa=confirm("Are you sure you want to remove this object?");
            if(aa===true){
                $("#theEdits .current-blocks[link-with="+$(this).parent().attr("mns-elem")+"]").remove();
                $(this).parent().remove();
                if($("div.mns-html-content").html()==""){
                    $("div.mns-html-content").html("<p class='mns-block mns-object mns-del-div'>&nbsp;</p>");
                }
            }
            else{
                return false;
            }
        });
    }
    function Mensio_View_Settings(tab,type,elem) {
        var block=type.replace("mns-","");
      $.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
          'action': 'mensio_ajax_Open_Object_Settings_Modal',
          'pg_setting' : tab
        },
        success:function(data) {
            if($("#theEdits #mns_settings").length == 0){
                $("#theEdits").append(data);
            }
            editSettings( elem );
            edits(elem);
            $("#theEdits .current-blocks").hide();
            $("#theEditsProperties").show();
            $("#mns_settings").show();
            $("#mns_styles").show();
            $("html,body").scrollTop($(".mns-block.currentBlockEdit").offset().top);
            $(".backToEdits").click(function(){
                $(".mns-html-content .mns-block.currentBlockEdit").removeClass("currentBlockEdit");
                $("#theEditsProperties").hide();
                $("#mns_settings").hide();
                $("#mns_styles").hide();
                $(".current-blocks").show();
            });
        },
        error: function(errorThrown){
            alert("NOOOO");
        }
      });
    }
    function rgbToHex(red, green, blue) {
        var rgb = blue | (green << 8) | (red << 16);
        return '#' + (0x1000000 + rgb).toString(16).slice(1)
    }
    function allBlocks(){
        $(".mns-block").each(function(){
        });
    }
    function edit_objects(){
        var i=1;
        $(".mns-html-content *").each(function(){
            i++;
        });
        var i=1;
        var k=$(".mns-html-content > .mns-block").length/2;
        $("#theEdits .current-blocks").remove();
        $(".mns-html-content > .mns-block").each(function(){
            $(this).addClass('mns-object').attr("mns-elem",i);
            if((!$(this).hasClass("mns-del-div")) || ($(this).find(".mns-edit-obj").length==0)){
                $(this).prepend("<div class='mns-edit-obj'>Edit</div>");
            }
            var itsClass=$(this).attr("class").split(" ");
            itsClass=itsClass[1];
            $("#"+itsClass.replace("mns-","")).clone().appendTo("#theEdits").attr("link-with",$(this).attr("mns-elem")).attr("class","current-blocks");
            if(i>=k){
                return false;
            }
            i++;
        });
        $(".mns-edit-obj").click(function(){
            if($("#close-settings").attr("status")==="closed"){
                $("#close-settings").click();
            }
        });
        $("#theSettings #edit-object").val();
        $("div.mns-block").click(function(){
            $(this).addClass("currentBlockEdit");
            var element;
            element=$(this).attr('mns-elem');
            $("#edit-object").val( $(this).attr('mns-elem') );
            var mnsType=$(this).attr("class").split(' ');
            Mensio_View_Settings("edits",mnsType[1], element );
            if($("#close-edits").attr("status")=="closed"){
                $("#close-edits").click();
            }
            $(".mns-html-content .mns-block.currentBlockEdit").removeClass("currentBlockEdit");
            $(".backToEdits").click(function(){
                $(".mns-html-content .mns-block.currentBlockEdit").removeClass("currentBlockEdit");
                $("#theEditsProperties").hide();
                $("#mns_settings").hide();
                $("#mns_styles").hide();
                $(".current-blocks").show();
                $("#mnsEditsTitle").hide();
            });
            $("#styles").css('display','block');
            $("#mns_styles").css('display','block');
            $("#mnsEditsTitle").css('display','block');
            $("#edit-object").val(element);
            element=$("#edit-object").val();
        });
        $(".current-blocks,.mns-block").click(function(){
            $("#theEditsProperties").show();
            $(".mns-html-content .mns-block.currentBlockEdit").removeClass("currentBlockEdit");
            $(".mns-html-content .mns-block[mns-elem="+ $(this).attr("link-with") +"]").click().addClass("currentBlockEdit");
            if($("#mnsEditsTitle").length>0){
                if($(this).hasClass("mns-block")){
                    var cls=$(this).attr("class").split(" ");
                    var cl=cls[1].replace("mns-","");
                    var text=$("#"+cl).text();
                }
                else{
                    var text=$(this).text();
                }
                $("#mnsEditsTitle").html( text );
            }
            else{
                if($(this).hasClass("mns-block")){
                    var cls=$(this).attr("class").split(" ");
                    var cl=cls[1].replace("mns-","");
                    var text=$("#"+cl).text();
                }
                else{
                    var text=$(this).text();
                }
                $("#theEdits").append('<div id="theEditsProperties"><input type="button" value="<< Back" class="backToEdits"><div id="mnsEditsTitle">'+text+'</div></div>')
            }
        });
        $("#mns_styles").hide();
        $("#mns_settings").hide();
        $("#theEditsProperties").hide();
    }
    function edits(element){
            $("#mns_styles #link-with-next").change(function(){
                if((element===$("#edit-object").val()) && ($(this).prop("checked"))){
                    $(".mns-block[mns-elem="+element+"]").css("width","50%").css("display","table-cell");
                    $(".mns-block[mns-elem="+element+"]").next().css("width","50%").css("display","table-cell");
                }
                else if((element===$("#edit-object").val()) && (!$(this).prop("checked"))){
                    $(".mns-block[mns-elem="+element+"]").css("width","100%").css("display","block");
                    $(".mns-block[mns-elem="+element+"]").next().css("width","100%").css("display","block");
                }
            });
            $("#mns_styles #width-50").change(function(){
                if(((element===$("#edit-object").val()) && ($(this).prop("checked")))){
                    $(".mns-block[mns-elem="+element+"]").css("width","50%").css("display","table-cell");
                }
                else if((element===$("#edit-object").val()) && (!$(this).prop("checked"))){
                    $(".mns-block[mns-elem="+element+"]").css("width","100%").css("display","block");
                }
            });
            var clr=$(".mns-block[mns-elem="+element+"]").css("color").replace(" ","").replace("rgb(","").replace(")","").split(",");
            clr=rgbToHex(clr[0], clr[1], clr[2]);
            var clr="#000000";
            $("#mns_styles #text-color").val(clr).change(function(){
                if(element===$("#edit-object").val()){
                    $(".mns-block[mns-elem="+element+"]").css("color",$(this).val());
                }
            });
            clr=$(".mns-block[mns-elem="+element+"]").css("background-color").replace(" ","").replace("rgb(","").replace(")","").split(",");
            clr=rgbToHex(clr[0], clr[1], clr[2]);
            var clr="#000000";
            $("#mns_styles #background-color").val(clr).change(function(){
                if(element===$("#edit-object").val()){
                    $(".mns-block[mns-elem="+element+"]").css("background-color",$(this).val());
                }
            });
            var align=$(".mns-block[mns-elem="+element+"]").css("text-align");
            $("#mns_styles-align-"+align).prop("checked","true");
            $("#mns_styles select[name=text-align]").change(function(){
                if(element===$("#edit-object").val()){
                    $(".mns-block[mns-elem="+element+"]").css("text-align",$(this).val());
                }
            });
            var margin=$(".mns-block[mns-elem="+element+"]").css("margin-top");
            $("#mns_styles input[name=margin-v]").change(function(){
                if(element===$("#edit-object").val()){
                    $(".mns-block[mns-elem="+element+"]").css("margin",$(this).val()+" 0");
                }
            });
            var padding=$(".mns-block[mns-elem="+element+"]").css("padding-left");
            $("#mns_styles input[name=padding-h]").change(function(){
                if(element===$("#edit-object").val()){
                    $(".mns-block[mns-elem="+element+"]").css("padding-left",$(this).val()+"px").css("padding-right",$(this).val()+"px");
                }
            });
    }
    function mnsSelectImages(elem_num){
        var mediaUploader;
        if (mediaUploader) {
          mediaUploader.open();
          return;
        }
        mediaUploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
          button: {
          text: 'Choose Image'
        }, multiple: true });
            mediaUploader.on('select', function() {
            var urls=Array();
            var attachments = mediaUploader.state().get('selection').toJSON();
            $('div.mns-block[mns-elem='+elem_num+']').html('');
            for(var i=0; i< attachments.length ; i++ ){
                urls.push(attachments[i]['url']);
                $('div.mns-block[mns-elem='+elem_num+']').append("<img src='"+attachments[i]['url']+"' width='25%' />");
            }
        });
        mediaUploader.open();
    }
    function newDroppableOptions(mnsElem){
        return {
            over: function (event,ui){
            },
            drop: function( event, ui ) {
                $.ajax({
                    type: 'post',
                    url: ajaxurl,
                    data: { 'Security': $sec,
                      'action': 'mnsPreviewShortCodes',
                      'previewShortCode' : $("#"+ui.draggable.prop('id')).attr("shortcode")
                    },
                    success:function(data) {
                        var newObj=$(".mns-html-content .mns-block").length;
                        if(mnsElem==true){
                            $(".mns-block[mns-elem="+mnsElem+"]").before("<div class='mns-block mns-"+ui.draggable.prop('id')+" mns-object' mns-elem='"+newObj+"'>"+data+"</div>");
                        }
                        else{
                            $(".mns-block").before("<div class='mns-block mns-"+ui.draggable.prop('id')+" mns-object' mns-elem='"+newObj+"'>"+data+"</div>");
                        }
                        edit_objects();
                        allBlocks();
                    },
                    error: function(errorThrown){
                        alert(errorThrown);
                    }
                });
            },
            out: function (event,ui){
            },
            deactivate: function(){
            },
            tolerance:'pointer',
            helper:'clone',
            accept: ".mns-element-to-create",
            greedy: true
        }
    }
    function reloadMnsObjects(){
        $("#theSettings #mns-objects").load(AdminSiteurl+" #mns-objects>*",function(){
            var elements=$(this).html();
            $("#mns-objects .mns-element-to-create").remove();
            $("#mns-objects").append(elements);
            $("#mns-objects .ps__rail-x:last").remove();
            $("#mns-objects .ps__rail-y:last").remove();
            $("#mns-objects .drag-drop").draggable({
                    revert:false,
                    helper: function() {
                      return $( "<div style='background: #916953;width:200px;padding: 10px 5px;vertical-align: middle;text-align: center;margin: 6px 5px;position: relative;line-height: 14px;font-size: 15px;color: #fff;display: inline-block;color: #fff;border-radius: 5px;box-shadow: -3px 3px 5px rgba(0,0,0,.5);float: none !important;'>"+$(this).html()+"</div>" );
                    },
                    cursor: "pointer",
                    zIndex:999999,
                    cursorAt: { top: -5, left: -5,position:'fixed' },
                    appendTo:"body",
                    cancel: ".disabled"
            });
            $( "#mns-objects").scrollTop(1);
        });
    }
(function($){
    var post_id = $("code#mns_html_page_content").attr('post-id');
    $("#plus-group").click(function(){
        $(this).parent().next().html("<input type='text'  id='new-group'/>");
    });
    $(".mns-edit-obj").click(function(){
        Mensio_View_Settings( "edits" );
    });
    $(".back-button").click(function(){
        window.location.href='edit.php?post_type='+$(this).attr('post_type')+'&page=mnsObjPrintAllPages';
    });
    $(".save-settings").mouseout(function(){
       $(this).removeClass("no-preview"); 
    });
    $(".save-settings").mouseover(function(){
        $(this).addClass("no-preview");
    });
    $(".save-settings").click(function(){
        $("#theEdits .current-blocks").remove();
        edit_objects();
        $(".mns-block").removeClass("currentBlockEdit");
        $(".mns-block").removeClass("disabled-mns-block");
        $(".current-blocks").show();
        $("#mns_styles").hide();
        $("#mns_settings").hide();
        $("#theEditsProperties").hide();
        var contact_inputs=Array();
        $("#theSettings .contact-inputs:checked").each(function(){
            contact_inputs.push({title:$(this).parent().text(), name:$(this).val() });
        });
        replace_content_with_shortcodes();
        if($(this).hasClass("no-preview")){
            $("*").removeClass("mns-preview");
        }
        var old_content = $("div.mns-html-content").html();
        $(".mns-obj-to-rem").remove();
        $(".mns-edit-obj").remove();
        var new_content = "<div class='mns-html-content'>"+$("div.mns-html-content").html()+"</div>";
        $("code#mns_html_page_content").val(new_content.replace("ui-sortable",""));
        $("body").prepend("<div id='saving-settings'></div>");
        $("div.mns-html-content").html(old_content);
        var pg_group;
        if($("#new-group").val()){
            pg_group=$("#new-group").val();
        }
        if($("#pg_group").val()){
            pg_group=$("#pg_group").val();
        }
        $.ajax({
            type: 'post',
            url: ajaxurl,
            data: { 'Security': $sec,
                'action': 'mns_update',
                'post_title' : $("#page_title").val(),
                'content' : new_content,
                'contact_inputs' : contact_inputs,
                'page_function' : $("#pg_function select").val(),
                'page_group' : pg_group,
                'post_id' : post_id
            },
            success:function(data) {
                $("#thePage div.mns-html-content").load(siteurl+" div.mns-html-content>*",function(){
                    var droppableOptions = newDroppableOptions("0");
                    $( ".mns-block" ).droppable(droppableOptions );
                    edit_objects();
                    $("div.mns-html-content" ).sortable();
                    if($(this).find('.mns-obj-to-rem').length==0){
                        $("div.mns-block:not(mns-del-div)").prepend("<div class='mns-obj-to-rem'>x</div>");
                    }
                    removeBlocks();
                    if($(this).find('.mns-edit-obj').length==0){
                        $("div.mns-block:not(mns-del-div)").prepend("<div class='mns-edit-obj'>edit</div>");
                    }
                    var i=1;
                    $(".mns-html-content section,.mns-html-content div,.mns-html-content p").each(function(){
                        $(this).attr('mns-elem',i);
                        i++;
                    });
                    var itsClass;
                    $("#thePage div.mns-block").each(function(){
                        itsClass=$(this).attr("class").split(" ");
                        itsClass=itsClass[1];
                    });
                    $(".current-blocks").click(function(){
                        $(".mns-html-content .mns-block.currentBlockEdit").removeClass("currentBlockEdit");
                        $(".mns-html-content .mns-block[mns-elem="+$(this).attr("link-with")+"]").click().addClass("currentBlockEdit");
                        $("#mnsEditsTitle").html( $(this).html() );
                    });
                    $(".mns-html-content div.mns-block").each(function(){
                        var cls=$(this).attr("class").split(" ");
                        var cl=cls[1].replace("mns-","");
                        if($("#"+cl).hasClass("disabled")){
                            $(".mns-html-content .mns-block.mns-"+cl).addClass("disabled-mns-block");
                        }
                    });
                    $("div.mns-block").click(function(){
                        $(this).addClass("currentBlockEdit");
                    });
                    $("#theEdits .current-blocks").remove();
                    edit_objects();
                });
                $("#saving-settings").remove();
                reloadMnsObjects();
                edit_objects();
                $(".mns-html-content div.mns-block").each(function(){
                    var cls=$(this).attr("class").split(" ");
                    var cl=cls[1].replace("mns-","");
                    if($("#"+cl).hasClass("disabled")){
                        $(".mns-html-content .mns-block.mns-"+cl).css("opacity","0.4");
                    }
                });
            },
            error: function(errorThrown){
              alert(errorThrown);
            } 
        });
    });
    $("#thePage").droppable;
    var i=1;
    $(".mns-html-content section,.mns-html-content div,.mns-html-content p").each(function(){
        $(this).attr('mns-elem',i);
        i++;
    });
    var i=1;
    $(".ui-droppable").each(function(){
        $(this).attr('mns-element',''+i);
        i++;
    });
    $("document").ready(function(){
    var elements=$("#mns-objects").html();
    $("#thePage").load(siteurl,function(){
        $("#thePage a").click(function(){return false;});
        $("#wpadminbar").remove();
        $( ".mns-block" ).disableSelection();
        if($('div.mns-html-content').html()==false){
            $('div.mns-html-content').html("<p class='mns-block mns-object mns-del-div'>&nbsp;</p>");
        }
        var textarea_content = $("code#mns_html_page_content").html();
        if($('div.mns-html-content').length===0){
            $.ajax({
                type: 'post',
                url: ajaxurl,
                data: { 'Security': $sec,
                  'action': 'mns_update',
                  'content' : "<div class='mns-html-content'><p></p>"+textarea_content+"</div>",
                  'post_id' : post_id
                },
                success:function(data) {
                    $("code#mns_html_page_content").val("<div class='mns-html-content'>"+textarea_content+"</div>");
                    window.location.href='';
                },
                error: function(errorThrown){
                  alert(errorThrown);
                } 
            });
        }
        $(".mns-html-content div.mns-block").each(function(){
            var cls=$(this).attr("class").split(" ");
            var cl=cls[1].replace("mns-","");
            if($("#"+cl).hasClass("disabled")){
                $(".mns-html-content .mns-block.mns-"+cl).addClass("disabled-mns-block");
            }
        });
        $(".mns-html-content section,.mns-html-content > div,.mns-html-content > p").droppable({
            over: function (event,ui){
                $(this).addClass("BlockIsSelected");
            },
            drop: function( event, ui ) {
                $(this).css('border','none');
                $(".ui-draggable").data("uiDraggable").originalPosition;
                AfterDrop(elements);
               var currentBlock=$(this).attr("mns-elem");
                $.ajax({
                    type: 'post',
                    url: ajaxurl,
                    data: { 'Security': $sec,
                      'action': 'mnsPreviewShortCodes',
                      'previewShortCode' : $("#"+ui.draggable.prop('id')).attr("shortcode")
                    },
                    success:function(data) {
                        var newObj=$(".mns-html-content .mns-block").length;
                        $(".mns-block[mns-elem="+currentBlock+"]").before("<div class='mns-block mns-"+ui.draggable.prop('id')+" mns-object' mns-elem='"+newObj+"'><div class='mns-edit-obj'>Edit</div><div class='mns-obj-to-rem'>x</div>"+data+"</div>");
                        var droppableOptions = newDroppableOptions(newObj);
                        $( ".mns-block[mns-elem="+newObj+"]" ).droppable(droppableOptions );
                        removeBlocks();
                        edit_objects();
                        allBlocks();
                    },
                    error: function(errorThrown){
                        alert(errorThrown);
                    }
                });
            },
            out: function (event,ui){
            },
            deactivate: function(){
                AfterDrop(elements);
            },
            tolerance:'pointer',
            helper:'clone',
            accept: ".mns-element-to-create",
            greedy: true
        });
        $("div.mns-html-content" ).sortable();
        $("div.mns-block:not(mns-del-div)").prepend("<div class='mns-obj-to-rem'>x</div>");
        removeBlocks();
        edit_objects();
        var itsClass;
        $("#thePage div.mns-block").each(function(){
            itsClass=$(this).attr("class").split(" ");
            itsClass=itsClass[1];
        });
        $("div.mns-block").click(function(){
            $(this).addClass("currentBlockEdit");
        });
    });
    $("#close-settings").click(function(){
        if($(this).attr('status')==='open'){
            $(this).attr('status','closed');
            $("#theSettings").animate({left: "-25%"}, 500);
            $(this).animate({left: "0%"}, 500);
            $(this).html(">");
        }
        else if($(this).attr('status')==='closed'){
            $(this).attr('status','open');
            $("#theSettings").animate({left: "0%"}, 500);
            $(this).animate({left: "145px"}, 500);
            $(this).html("<");
        }
        if($("#close-edits").attr("status")=='open' && $("#close-settings").attr("status")=='open'){
        }
    });
    $("#close-edits").click(function(){
        if($(this).attr('status')==='open'){
            $(this).attr('status','closed');
            $("#theEdits").animate({right: "-25%"}, 500);
            $(this).animate({right: "0%"}, 500);
            $(this).html('>');
        }
        else if($(this).attr('status')==='closed'){
            $(this).attr('status','open');
            $("#theEdits").animate({right: "0%"}, 500);
            $(this).animate({right: "20px"}, 500);
            $(this).html('<');
        }
        if($("#close-edits").attr("status")=='open' && $("#close-settings").attr("status")=='open'){
        }
    });
    function AfterDrop(elements){
        $("#mns-objects .mns-element-to-create").remove();
        $("#mns-objects").append(elements);
        $("#mns-objects .ps__rail-x:last").remove();
        $("#mns-objects .ps__rail-y:last").remove();
        $("#mns-objects .drag-drop").draggable({
                revert:false,
                helper: function() {
                  return $( "<div style='background: #916953;width:200px;padding: 10px 5px;vertical-align: middle;text-align: center;margin: 6px 5px;position: relative;line-height: 14px;font-size: 15px;color: #fff;display: inline-block;color: #fff;border-radius: 5px;box-shadow: -3px 3px 5px rgba(0,0,0,.5);float: none !important;'>"+$(this).html()+"</div>" );
                },
                cursor: "pointer",
                zIndex:999999,
                cursorAt: { top: -5, left: -5,position:'fixed' },
                appendTo:"body",
                cancel: ".disabled"
        });
    }
    AfterDrop(elements);
    $( "#mns-objects").scrollTop(1);
    $("#theEditsProperties").hide();
    $("#pg_function select").change(function(){
        $("#mns-objects > .mns-element-to-create").removeClass('disabled');
        $("#mns-objects > #related_products,#mns-objects > #product,#mns-objects > #category,#mns-objects > #brand").addClass('disabled');
        if($(this).val()=="product_page"){
            $("#mns-objects #brand,#mns-objects #category").addClass('disabled');
            $("#mns-objects #product,#mns-objects #related_products").removeClass('disabled');
        }
        if($(this).val()=="brand_page"){
            $("#mns-objects #product,#mns-objects #related_products,#mns-objects #category").addClass('disabled');
            $("#mns-objects #brand").removeClass('disabled');
        }
        if($(this).val()=="category_page"){
            $("#mns-objects #category,#mns-objects #related_products,#mns-objects #brand").addClass('disabled');
            $("#mns-objects #category").removeClass('disabled');
        }
    });
    });
})(jQuery);