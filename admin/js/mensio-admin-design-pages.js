var $sec = $('#mns_sec').val()+'::'+$('input[name=_wp_http_referer]').val();
function mnsLoadPage(){
    $("#thePage")
        .html("<div class='mns-block wait-please' style='height:100%;width:100%;'></div>")
            .load(siteurl+"",function(data){
                $("#thePage").removeClass("wait-please");
                $("#thePage a").click(
                        function(){
                            return false;
                        }
                    );
                $("#wpadminbar").remove();
                $(".ui-droppable").removeClass("ui-droppable")
                        .removeClass("ui-sortable-handle")
                        .removeClass("ui-droppable");
                AllBlocks();
                MensioDroppableObjects();
                removeBlocks();
                $("#thePage .mns-block button,#thePage .mns-block input[type=button]").prop("disabled",true);
                $("html").removeClass("saving");
                $("#seeAsBrand").hide();
                if($("#NewMensioPageFunction").val()=="brand_page"){
                    $("#seeAsBrand").show().find("select").change(function(){
                        window.location.href=AdminSiteurl+"&brand="+$(this).val();
                    });
                }
                $("#seeAsCategory").hide();
                if($("#NewMensioPageFunction").val()=="category_page"){
                    $("#seeAsCategory").show().find("select").change(function(){
                        window.location.href=AdminSiteurl+"&category="+$(this).val();
                    });
                }
                $(".wp-picker-container").each(function(){
                    if($(this).find("input[type=text]").val()==false){
                        $(this).find(".wp-color-result").addClass("undefinedColor").html("Undefined");
                    }
                });
                $(".wp-picker-container").each(function(){
                    if($(this).find("input[type=text]").val()==false){
                        $(this).find(".wp-color-result").addClass("undefinedColor").html("Undefined");
                    }
                    else{
                        $(this).find(".wp-color-result").removeClass("undefinedColor").html($(this).find("input[type=text]").val());
                    }
                });
                var line1 = data.search("<body ");
                var found =data.substr(line1,1000);
                var line2 = found.search(">");
                var found =found.substr(found,line2);
                var line3 = found.search('class="');
                var found =found.substr(line3,1000).replace('class="',"");
                var line4 = found.search('"');
                var found =found.substr(0,line4).replace('"',"");
                $("body").addClass(found);
                $("#thePage .custom-header").css("margin-bottom","0");
                $("#currentObjects").css("right","0").show();
    });
}
function isFloat(n){
    return Number(n) === n && n % 1 !== 0;
}
function mnsStartFromLastKeyPress(f, delay){
    var timer = null;
    return function(){
        var context = this, args = arguments;
        clearTimeout(timer);
        timer = window.setTimeout(function(){
            f.apply(context, args);
        },
        delay || 500);
    };
}
function changePgFunction(func){
    $.ajax({
            type: 'post',
            url: ajaxurl,
            data: { 'Security': $sec,
              'action': 'mensiopress_MensioTheObjects',
              'tempPgFunction' : func
            },
            success:function(data) {
                $("#mns-objects").html( data );
                MensioDraggableObjects();
                AllBlocks();
                MensioDroppableObjects();
                $('#mns-objects').perfectScrollbar()
                        .append('<div class="ps-scrollbar-y-rail" style="top: 0px; height: 1122px; right: 0px;"><div class="ps-scrollbar-y" tabindex="0" style="top: 0px; height: 996px;"></div></div>')
                        .scrollTop(1);
                $('#MensioProperties').perfectScrollbar()
                        .append('<div class="ps-scrollbar-y-rail" style="top: 0px; height: 1122px; left: -15px;"><div class="ps-scrollbar-y" tabindex="0" style="top: 0px; height: 996px;"></div></div>')
                        .scrollTop(1);
                $('#theEditsProperties').perfectScrollbar();
                $("#seeAsBrand").hide();
                if($("#NewMensioPageFunction").val()=="brand_page"){
                    $("#seeAsBrand").show().find("select").change(function(){
                        window.location.href=window.location.href+"&brand="+$(this).val();
                    });
                }
            },
            error: function(errorThrown){
                alert(errorThrown);
            }
    });
}
function removeBlocks(){
  $(".mns-obj-to-rem").click(function(){
      var thisBlock=$(this);
    $("#MensioConfirmation,#MensioMessage").show();
    $("#MensioConfirmation #MensioMessage").animate({left:"0%"},250);
    $("#MensioMessage > #question").html("Are you sure you want to remove this object?");
    $("#answer-YES,#answer-NO").show();
    $("#answer-YES").click(function(){
        $("#MensioMessage").hide().animate({left:"-100%"},250);
        $("#theEdits .current-blocks[link-with-mns-elem="+thisBlock.parent().parent().attr("mns-elem")+"]").remove();
        if(thisBlock.parent().parent().prev().hasClass("mns-Dropzone")){
            thisBlock.parent().parent().prev().remove();    
        }
        thisBlock.parent().parent().remove();
        if($("div.mns-html-content .mns-block").length==0){
            $("div.mns-html-content").html("<div class='mns-Dropzone' mns-elem='blank'></div>");
        }
            MensioDroppableObjects();
        $("#theEdits #theEditsProperties").hide();
        $("#theEdits #currentObjects").show().animate({right:0},250);
        $("#MensioConfirmation").hide();
    });
    $("#answer-NO").click(function(){
        $("#MensioMessage").hide().animate({left:"-100%"},250);
        $("#MensioSettings").hide();
        $("#MensioConfirmation").hide();
    });
  });
}
function AllBlocks(){
    $(".mns-Dropzone").remove();
    $("#theEdits #currentBlocks > *").not("#page_title_div").not("#pg_function").not("h2").remove();
    $(".disabled-mns-block").removeClass("disabled-mns-block");
    var i=1;
    $("#thePage .mns-html-content:first > div.mns-block:not(.mns-del-div)").each(function(){
var classes=$(this).attr("class").split(" ");
if(classes.length==1){
    if($("#NewMensioPageFunction").val()=="signup_page"){
        $(this).addClass("mns-signup");
    }
    if($("#NewMensioPageFunction").val()=="user_page"){
        $(this).addClass("mns-user");
    }
    if($("#NewMensioPageFunction").val()=="product_page"){
        $(this).addClass("mns-product");
    }
    if($("#NewMensioPageFunction").val()=="brands_page"){
        $(this).addClass("mns-brands");
    }
    if($("#NewMensioPageFunction").val()=="categories_page"){
        $(this).addClass("mns-categories");
    }
    if($("#NewMensioPageFunction").val()=="category_page"){
        $(this).addClass("mns-category");
    }
    if($("#NewMensioPageFunction").val()=="login_page"){
        $(this).addClass("mns-login");
    }
    if($("#NewMensioPageFunction").val()=="signup_page"){
        $(this).addClass("mns-signup");
    }
    if($("#NewMensioPageFunction").val()=="contact_page"){
        $(this).addClass("mns-contact");
    }
    if($("#NewMensioPageFunction").val()=="cart_page"){
        $(this).addClass("mns-cart");
    }
    if($("#NewMensioPageFunction").val()=="tos_page"){
        $(this).addClass("mns-tos");
    }
    if($("#NewMensioPageFunction").val()=="checkout_page"){
        $(this).addClass("mns-checkout");
    }
    if($("#NewMensioPageFunction").val()=="product_comparison_page"){
        $(this).addClass("mns-product_comparison");
    }
    if($("#NewMensioPageFunction").val()=="product_favorites_page"){
        $(this).addClass("mns-favorites");
    }
    if($("#NewMensioPageFunction").val()=="search_results_page"){
        $(this).addClass("mns-search");
    }
}
        if(!$(".mns-Dropzone[mns-elem-dropzone="+i+"]").length ){
            $(this).before('<div class="mns-Dropzone" mns-elem-dropzone="'+i+'"></div>');
        }
        if($(this).find(".block-tool-bar").length){
            $(this).find(".mns-edit-obj").attr("link-with-mns-elem",i);
        }
        var cls=$(this).attr("class").split(" ");
        var cl=cls[1].replace("mns-","");
        $("#theEdits > #currentObjects > #currentBlocks").disableSelection().sortable({
            update:function(){
                $(this).find('.current-blocks').each(function(){
                    var mnsElem=$("#thePage .mns-html-content:first .mns-block[mns-elem="+$(this).attr("link-with-mns-elem")+"]");
                    var mnsElemDropzone=$("#thePage .mns-html-content:first .mns-Dropzone[mns-elem-dropzone="+$(this).attr("link-with-mns-elem")+"]");
                    mnsElemDropzone.clone().appendTo("#tempHTML");
                    mnsElem.clone().appendTo("#tempHTML");
                });
                $("#thePage .mns-html-content:first").html( $("#tempHTML").html() );
                var k=1;
                $("#tempHTML .mns-block").each(function(){
                    k++;
                });
                $("#tempHTML").html("");
                AllBlocks();
                removeBlocks();
                MensioDroppableObjects();
            }
        }).append("<div class='current-blocks' link-with-mns-elem='"+i+"' title='"+$("#"+cl).html()+"'>"+$("#"+cl).html()+"</div>");
        if(!$(this).find(".block-tool-bar").length){
            $(this).prepend("<div class='block-tool-bar'><div class='title'>"+$("#"+cl).html()+"</div><div class='mns-obj-to-rem'><i class='fa fa-close'></i></div><div class='mns-edit-obj' link-with-mns-elem='"+i+"' title='"+$("#"+cl).html()+"'><i class='fa fa-cog'></i></div>");
        }
        $(this).attr("mns-elem",i);
        if($("#"+cl).hasClass("disabled")){
            $(".mns-html-content .mns-block.mns-"+cl).addClass("disabled-mns-block");
        }
        i++;
    });
    $(".mns-html-content").append('<div class="mns-Dropzone" mns-elem-dropzone="'+i+'"></div>');
    $(".current-blocks,.mns-edit-obj").click(function(){
        $(".close-properties").prop("disabled",false).removeClass("inactive");
        $(".MnsCustomSelect .category").removeClass("active").css("display","none");
        $(".currentBlockEdit").removeClass("currentBlockEdit");
        $("#theEditsProperties").css("right","-400px").css("position","absolute");
        $("#currentObjects").css("position","absolute").animate({right:"-400px"},250,function(){
            $("#currentObjects").hide();
            $("#theEditsProperties").show().css("position","relative").animate({right:0},250);
        });
        $("#theEditsProperties #mnsEditsTitle").html( $(this).attr("title"));
        $("div.mns-block[mns-elem="+$(this).attr("link-with-mns-elem")+"]").addClass("currentBlockEdit");
        var cls=$(".mns-block[mns-elem="+$(this).attr("link-with-mns-elem")+"]").attr("class").split(" ");
        var cl=cls[1].replace("mns-","");
        $("#edit-"+$("#"+cl).attr("id")).show();
        $("html,body").animate({scrollTop:$(".mns-block.currentBlockEdit").offset().top},500);
        $("#edit-object").val( $(this).attr("link-with-mns-elem") );
        if($("#close-edits").attr("status")=="closed"){
            $("#close-edits").click();
        }
        editSettings($(this).attr("link-with-mns-elem"));
        edits($(this).attr("link-with-mns-elem"));
        $("#theEdits input,#theEdits select").change(function(){
            if($(".center-bottom-buttons input[name=has-change]").val()=="0"){
                $(".center-bottom-buttons input[name=has-change]").val("1");
            }
        });
    });
    $("#theEditsProperties").hide();
    $("#close-properties").prop("disabled",true).click(function(){
        $(".MnsCustomSelect").each(function(){
            $(this).find(".category").removeClass("active");
        });
        $("#theEditsProperties").animate({right:"-400px"},250,function(){
            $(this).hide();
            $("#currentObjects").show().css("position","relative").animate({right:0},250);
        });
        $(".currentBlockEdit").removeClass("currentBlockEdit");
        $(this).addClass("inactive");
        $(this).prop("disabled",true);
        $(".bubbleDescr[for=close-properties]").hide();
    });
    $("div.mns-block").each(function(){
        if($(this).attr("ordering")){
            $(this).find("div[ordering]").hide();
            $(this).find("div[ordering="+$(this).attr("ordering")+"]").show();
        }
        else{
            $(this).find("div[ordering]").hide();
            $(this).find("div[ordering]").first().show();
        }
    });
}
function rgb2hex(rgb){
 rgb = rgb.match(/^rgba?[\s+]?\([\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?/i);
 return (rgb && rgb.length === 4) ? "#" +
  ("0" + parseInt(rgb[1],10).toString(16)).slice(-2) +
  ("0" + parseInt(rgb[2],10).toString(16)).slice(-2) +
  ("0" + parseInt(rgb[3],10).toString(16)).slice(-2) : '';
}
function edits(element){
        $(".jsColorVal").on("change keyup",function(){
            $(this).next().val( $(this).val() ).css("background-color","#"+$(this).val()).change().css("color","transparent");
        });
        $(".jsColor").css("color","transparent").on("change keyup",function(){
            $(this).css("color","transparent").prev().val( $(this).val() );
        });
        $("#mns_styles #width-50").change(function(){
            if((element===$("#edit-object").val()) && ($(this).prop("checked"))){
                $(".mns-block.currentBlockEdit").addClass("width-50");
                $(".mns-block.currentBlockEdit").css("width","49%").css("display","inline-block");
                $(".mns-block.currentBlockEdit").next(".mns-Dropzone").hide();
            }
            else if((element===$("#edit-object").val()) && (!$(this).prop("checked"))){
                $(".mns-block.currentBlockEdit").removeClass("width-50");
                $(".mns-block.currentBlockEdit").css("width","100%").css("display","block");
                $(".mns-block.currentBlockEdit").next(".mns-Dropzone").show();
            }
        });
        $("#mns_styles #category-box-border-color").val($('.mns-block[mns-elem='+element+']').attr('category-box-border-color')).change(function(){
            if(element===$("#edit-object").val()){
                $(".mns-block.currentBlockEdit").attr("category-box-border-color",$(this).val());
            }
        });
        if($(".mns-block.currentBlockEdit").attr("text-color")){
            var clr=$(".mns-block.currentBlockEdit").attr("text-color");
            $("input[name=text-color]").val(clr);
        }
        if($(".mns-block.currentBlockEdit").attr("active-link-color")){
            var clr=$(".mns-block.currentBlockEdit").attr("active-link-color");
            $("input[name=active-link-color]").val(clr);
        }
        if($(".mns-block.currentBlockEdit").attr("titlecolor")){
            var clr=$(".mns-block.currentBlockEdit").attr("titlecolor");
            $("#theEdits input[name=titlecolor]").val(clr).closest(".my-color-field-wrapper")
                    .find(".button.wp-color-result").html(clr).css("background-color",clr).removeClass("undefinedColor");
        }
        if(!$(".mns-block.currentBlockEdit").attr("background-color")){
            $("#theEdits .transparentBackground").prop('checked',true);
            $("#theEdits input[name=background-color]").css("background-color","none").attr("istransparent","yes");
            $("#theEdits input[name=background-color-val]").val("");
        }
        else{
            clr=$(".mns-block.currentBlockEdit").attr("background-color");
            $("#theEdits input[name=background-color]").attr("value",clr).css("background-color", clr );
        }
        $("#theEdits input[name=background-color]").on("change keyup blur",function(){
            $(".mns-block.currentBlockEdit").css("background-color","#"+$(this).val()).attr("BGcolor", $(this).val());
        });
        $(".transparentBackground").on("change",function(){
            if($(this).prop("checked")==true){
                $("#theEdits input[name=background-color]").attr("istransparent","yes");
                $(".mns-block.currentBlockEdit").css("background","none").attr("bgcolor","");
                $("#theEdits input[name=background-color-val]").val("");
            }
            else{
                $("#theEdits input[name=background-color]").attr("istransparent","");
            }
        });
        $("select[name=text-align]").change(function(){
            if(element===$("#edit-object").val()){
                $(".mns-block.currentBlockEdit").css("text-align", $(this).val() ).attr("textAlign",$(this).val());
            }
        });
        var marginTop=$(".mns-block.currentBlockEdit").css("margin-top");
        $("#theEdits input[name=margin-top]").val(marginTop.replace("px","")).change(function(){
            if(element===$("#edit-object").val()){
                $(".mns-block.currentBlockEdit").css("margin-top",$(this).val()+"px");
            }
        });
        var marginBottom=$(".mns-block.currentBlockEdit").css("margin-bottom");
        $("#theEdits input[name=margin-bottom]").val(marginBottom.replace("px","")).change(function(){
            if(element===$("#edit-object").val()){
                $(".mns-block.currentBlockEdit").css("margin-bottom",$(this).val()+"px");
            }
        });
        var padding=$(".mns-block.currentBlockEdit").css("padding-left");
        $("#mns_styles input[name=padding-h]").val(padding.replace("px",""));
        $("#mns_styles input[name=padding-h]").change(function(){
            if(element===$("#edit-object").val()){
                $(".mns-block.currentBlockEdit").css("padding-left",$(this).val()+"px").css("padding-right",$(this).val()+"px");
            }
        });
        var border_w=$(".mns-block.currentBlockEdit").css("border-width");
        $("select[name=border-w]").change(function(){
            if(element===$("#edit-object").val()){
                $(".mns-block.currentBlockEdit").addClass("cusBorder").css("border-style","solid").css("border-width",$(this).val()+"px");
                if($(this).val()==0){
                    $(".mns-block.currentBlockEdit").removeClass("cusBorder")
                            .css("border-style","")
                            .css("border-width","")
                            .css("border-color","")
                            .css("border-radius","");
                }
            }
        });
        var border_r=$(".mns-block.currentBlockEdit").css("border-radius");
        $("select[name=border-r]").change(function(){
            if(element===$("#edit-object").val()){
                $(".mns-block.currentBlockEdit").addClass("cusBorder").css("border-radius",$(this).val()+"px");
            }
        });
        clr=$(".mns-block.currentBlockEdit").attr("border-c");
        if($(".mns-block.currentBlockEdit").attr("border-c")){
            $("input[name=border-c]").attr("value",clr).css("background-color",clr);
        }
        if($(".mns-block.currentBlockEdit").attr("navigationnextlabel")){
            var navigationnextlabel=decodeURI($(".mns-block.currentBlockEdit").attr("navigationnextlabel"));
        }
        else{
            var navigationnextlabel="";
        }
        $(".carousel-navigation-next").val( navigationnextlabel ).on("keyup change",function(){
           $(".mns-block.currentBlockEdit").attr("navigationnextlabel",encodeURI($(this).val()));
        });
        if($(".mns-block.currentBlockEdit").attr("navigationpreviouslabel")){
            var navigationpreviouslabel=decodeURI($(".mns-block.currentBlockEdit").attr("navigationpreviouslabel"));
        }
        else{
            var navigationpreviouslabel="";
        }
        $(".carousel-navigation-previous").val( navigationpreviouslabel).on("keyup change",function(){
           $(".mns-block.currentBlockEdit").attr("navigationpreviouslabel",encodeURI($(this).val()));
        });
        $("select[name=text-size]").change(function(){
            $(".mns-block.currentBlockEdit").css("font-size",$(this).val()+"rem").attr("fontSize", $(this).val() );
            $(".mns-block.currentBlockEdit *").not(".mensioObjectTitle, .block-tool-bar, .block-tool-bar *").css("font-size",$(this).val().replace("-",".")+"rem");
        });
        $("select[name=TitleSize]").change(function(){
           var wr=$(this).val();
           $(".mns-block.currentBlockEdit").attr("titleSize",wr);
        });
        $("textarea[name=mnsText]").on('change keyup',function(){
            $(".mns-block.currentBlockEdit .mnsText").html( $(this).val() );
        });
        $("#mns_settings .HowToDisplay").change(function(){
            $(".mns-block.currentBlockEdit").attr("display","simple");
            if(element===$("#edit-object").val()){
                if($(this).val()=="carousel"){
                    $(".mns-block.currentBlockEdit").attr("display","carousel");
                    $(".CarouselOptions").show();
                }
                else{
                    $(".mns-block.currentBlockEdit").attr("display","simple");
                }
            }
        });
        if(!$(".mns-block.currentBlockEdit").attr("discount-background-color")){
            $("#theEdits input[name=discount-background-color]").css("background-color","FF0000");
            $("#theEdits input[name=discount-background-color-val]").val("FF0000");
        }
        else{
            $("#theEdits input[name=discount-background-color]").css("background-color", $(".mns-block.currentBlockEdit").attr("discount-background-color") ).val($(".mns-block.currentBlockEdit").attr("discount-background-color"));
            $("#theEdits input[name=discount-background-color-val]").val($(".mns-block.currentBlockEdit").attr("discount-background-color").replace("#","") );
        }
        if($(".mns-block.currentBlockEdit").attr("discount-text-color")){
            $("#theEdits input[name=discount-text-color]").css("background-color",$(".mns-block.currentBlockEdit").attr("discount-text-color"));
            $("#theEdits input[name=discount-text-color]").val($(".mns-block.currentBlockEdit").attr("discount-text-color"));
        }
        else{
            $("#theEdits input[name=discount-text-color]").css("background-color","ffffff");
            $("#theEdits input[name=discount-text-color]").val("ffffff");
        }
        $("#theEdits .discount-bold").change(function(){
            if($(this).prop("checked")==true){
                $(".mns-block.currentBlockEdit").attr("discountbold","yes");
            }
            else{
                $(".mns-block.currentBlockEdit").attr("discountbold","");
            }
        });
        var CurBrand=$(".mns-block.currentBlockEdit").attr("of-brand");
        $("#theEditsProperties select[name=of_brand]").val(CurBrand);
        $("#mns_settings .carousel-autoplay").change(function(){
            if($(this).prop("checked")==true){
                $(".mns-block.currentBlockEdit").addClass("carousel-autoplay");
            }
            else{
                $(".mns-block.currentBlockEdit").removeClass("carousel-autoplay");
            }
        });
        $("#mns_settings .autoplayTime").on('keyup change',function(){
            $(".mns-block.currentBlockEdit").attr("carouselautoplaytime",$(this).val());
        });
        $("#mns_settings .carousel-HoverPause").change(function(){
            if($(this).prop("checked")==true){
                $(".mns-block.currentBlockEdit").addClass("carousel-HoverPause");
            }
            else{
                $(".mns-block.currentBlockEdit").removeClass("carousel-HoverPause");
            }
        });
        $(".hide-tags").change(function(){
            if ($(this).prop('checked')==true){
                $(".mns-block.currentBlockEdit").find('.product-tags').css('display','none');
            }
            else{
                $(".mns-block.currentBlockEdit").find('.product-tags').css('display','block');
            }
        });
        $(".hide-title").change(function(){
            if ($(this).prop('checked')==true){
                $(".mns-block.currentBlockEdit").find('.mensioObjectTitle').css('display','none').next("hr").css("display","none");
            }
            else{
                $(".mns-block.currentBlockEdit").find('.mensioObjectTitle').css('display','block').next("hr").css("display","block");
            }
        });
        var shortcode="";
        var BlockType="";
        $("#mns_settings input,#mns_settings select,#preview-shortcode").on("keyup change",mnsStartFromLastKeyPress(function(event){
            if(     ($(this).attr("name")=="round-images") ||
                    ($(this).attr("name")=="background-color") ||
                    ($(this).attr("name")=="hide-attributes") ||
                    ($(this).attr("name")=="hide-availability") ||
                    ($(this).attr("name")=="border-c") ||
                    ($(this).attr("name")=="border-w") ||
                    ($(this).attr("name")=="border-r") ||
                    ($(this).attr("name")=="text-align")
                )
                {
                return false;
            }
            var getBlockType=$(".mns-block[mns-elem="+$("#edit-object").val()+"]").attr("class").split(" ");
            BlockType=getBlockType[1].replace("mns-");
            var mnsElem=$("#edit-object").val();
            replace_content_with_shortcodes("Preview",mnsElem);
            shortcode=$("#preview-shortcode").val();
            var cls=$(".mns-block[mns-elem="+mnsElem+"]").attr("class").split(" ");
            var cl=cls[1].replace("mns-","");
            var extraURL="";
            if($("#edit-"+BlockType+" select[name=of_brand]").length==0){
                extraURL="&brand="+CurrentBrand;
            }
            else{
                var extraURL="&brand="+$("#edit-"+BlockType+" select[name=of_brand]").val();
                if($("#edit-"+BlockType+" select[name=of_brand]").val()=="Current"){
                    extraURL="&brand="+CurrentBrand;
                }
            }
            if($("#edit-"+BlockType+" select[name=of_category]").length==0){
                extraURL="&category="+CurrentCategory;
            }
            else{
                var extraURL="&category="+$("#edit-"+BlockType+" select[name=of_category]").val();
                if($("#edit-"+BlockType+" select[name=of_category]").val()=="Current"){
                    extraURL="&category="+CurrentCategory;
                }
            }
            $(".mns-block[mns-elem="+mnsElem+"]").html("").addClass("wait-please");
            $.ajax({
                type: 'post',
                url: ajaxurl+"?page=mns-html-edit"+extraURL,
                data: { 'Security': $sec,
                  'action': 'mensiopress_mnsPreviewShortCodes',
                  'previewShortCode' : shortcode
                },
                success:function(data) {
                    $(".mns-block[mns-elem="+mnsElem+"]").removeClass("wait-please");
                    $(".mns-block[mns-elem="+mnsElem+"]").html(data);
                    $("#preview-shortcode").val("");
                    $("#theEditsProperties").show();
                    if(!$(".mns-block[mns-elem="+mnsElem+"]").find(".block-tool-bar").length){
                        $(".mns-block[mns-elem="+mnsElem+"]").prepend("<div class='block-tool-bar'><div class='title'>"+$("#"+cl).html()+"</div><div class='mns-obj-to-rem'><i class='fa fa-close'></i></div><div class='mns-edit-obj' link-with-mns-elem='"+mnsElem+"' title='"+$("#"+cl).html()+"'><i class='fa fa-cog'></i></div>");
                        $(".mns-edit-obj").click(function(){
                            $(".current-blocks[link-with-mns-elem="+ $(this).attr("link-with-mns-elem") +"]").click();
                        });
                    }
                    $(".mns-block[mns-elem="+mnsElem+"][display=carousel] .CarouselDisplay").show();
                    return false;
                },
                error: function(errorThrown){
                    console.log("error");
                }
            });
            return false;
        }));
}
function MensioDraggableObjects(){
    $("#mns-objects .drag-drop").draggable({
            revert:false,
            helper: function() {
              return $( "<div class='clonedDIV' style=''>"+$(this).html()+"</div>" );
            },
            zIndex:999999,
            cursor: "pointer",
            cursorAt: { top: 10, left: 20,position:'fixed' },
            appendTo:"body",
            cancel: ".disabled"
    });
}
function MensioDroppableObjects(){
    MensioDraggableObjects();
    var newObj=$(".mns-html-content .mns-block").length;
    newObj++;
    $(".mns-html-content .mns-Dropzone").droppable({
            tolerance:"pointer",
            over:function(){
                $(this).addClass("BlockIsSelected");
            },
            out: function (event,ui){
                $(".BlockIsSelected").removeClass("BlockIsSelected");
            }
            ,
            drop: function (event,ui){
                var dropzone=$(this);
                jQuery.ajax({
                    type: 'post',
                    url: ajaxurl+"?page=mns-html-edit",
                    data: {
                      'Security': $sec,
                      'action': 'mensiopress_mnsPreviewShortCodes',
                      'previewShortCode' : $("#"+ui.draggable.prop('id')).attr("shortcode")
                    },
                    success:function(data) {
                        if(!ui.draggable.prop('id')){
                            return false;
                        }
                        dropzone.before("<div class='mns-Dropzone' mns-elem-dropzone='"+newObj+"'></div><div class='mns-block mns-"+ui.draggable.prop('id')+" mns-object' mns-elem='"+newObj+"'>"+data+"</div>");
                        AllBlocks();
                        MensioDraggableObjects();
                        MensioDroppableObjects();
                        removeBlocks();
                        $("#theEdits .close-properties").click();
                        $(".center-bottom-buttons input[name=has-change]").val("1");
                    },
                    error: function(errorThrown){
                        alert(errorThrown);
                    }
                });
                $(".BlockIsSelected").removeClass("BlockIsSelected");
            }
    });
}
function SaveMnsPage(){
    $(".center-bottom-buttons input[name=has-change]").val("0");
    $(".mns-block").removeClass("wait-please");
    $(".currentBlockEdit").removeClass("currentBlockEdit");
    $(".disabled-mns-block").removeClass("disabled-mns-block");
    $(".mns-Dropzone").remove();
    replace_content_with_shortcodes("onPage");
    $("body").prepend("<div id='saving-settings'></div>");
    $("#theEdits .close-properties").click();
    $("html").addClass("saving");
    var PageSlug=$("#page_slug").val();
    if($("#page_slug").hasClass("slug-unavailable")){
        PageSlug="";
        $("#page_slug").val($("#page_slug").attr("data-slug") );
    }
    var MainTitle;
    $(".PageTitle input").each(function(){
        MainTitle=$(this).val();
        return false;
    });
    var PostTitles=Array();
    var obj={};
    $(".PageTitle input[name=page_title]").each(function(){
        obj[$(this).attr("lang")]=$(this).val();
    });
    PostTitles.push(obj);
    PostTitles=JSON.stringify(PostTitles);
    var PostDescriptions=Array();
    var DescrObj={};
    $("#page_title_div textarea[name=page_description]").each(function(){
        DescrObj[$(this).attr("lang")]=$(this).val();
    });
    PostDescriptions.push(DescrObj);
    PostDescriptions=JSON.stringify(PostDescriptions);
    var MainDescription;
    $("#page_title_div textarea[name=page_description]").each(function(){
        MainDescription=$(this).val();
        return false;
    });
    $.ajax({
        type: 'post',
        url: ajaxurl,
        data: { 'Security': $sec,
            'action': 'mensiopress_mns_update',
            'post_title' : MainTitle,
            'post_excerpt' : MainDescription,
            'post_slug' : PageSlug,
            'content' : "<div class='mns-html-content'>"+$("#thePage .mns-html-content:first").html()+"</div>",
            'page_function' : $("#pg_function select").val(),
            'post_id' : $(".save-settings").attr("postID"),
            'post_titles':JSON.stringify(PostTitles),
            'post_descriptions':JSON.stringify(PostDescriptions)
        },
        success:function(data) {
            $("#saving-settings").remove();
            mnsLoadPage();
            MensioDraggableObjects();
            MensioDroppableObjects();
        },
        error:function(){
            $("#saving-settings").remove();
        }
    });
}
$("document").ready(function(){
    if($("input[name=skin]:checked")){
        if(!$("input[name=skin]:checked").val()){
            $("input[name=skin]:first").prop("checked",true);
        }
        $("html").addClass("skin-"+$("input[name=skin]:checked").val());
    }
        $("input[name=skin]").change(function(){
            $("html").removeClass("skin-1").
                      removeClass("skin-2").
                      removeClass("skin-3").addClass("skin-"+$(this).val());
        });
    mnsLoadPage();
    MensioDraggableObjects();
    $("body").mouseover(function(){
        $(".mns-html-content:first .SimpleDisplay .mns-image").each(function(){
            var w=$(this).width();
            var h=$(this).height();
            $(this).find("img").height(w);
            $(this).find("img").width(w);
        });
        console.log("hello");
    });
    $(".save-settings").click(function(){
       SaveMnsPage(); 
    });
    if($("#edit-object").val()){
        editSettings($(this).attr("link-with-mns-elem"));
    }
    $("#close-settings").click(function(){
        return false;
        if($(this).attr('status')==='open'){
            $(this).attr('status','closed');
            $("#theSettings").animate({left: "-25%"}, 500);
            $(this).animate({left: "0%"}, 500);
            $(this).find("i").removeClass('fa-minus');
            $(this).find("i").addClass('fa-plus');
        }
        else if($(this).attr('status')==='closed'){
            $(this).attr('status','open');
            $("#theSettings").animate({left: "0%"}, 500);
            $(this).animate({left: "135px"}, 500);
            $(this).find("i").removeClass('fa-plus');
            $(this).find("i").addClass('fa-minus');
        }
        if($("#close-edits").attr("status")=='open' && $("#close-settings").attr("status")=='open'){
        }
        return true;
    });
    $("#close-edits").click(function(){
        return false;
        if($(this).attr('status')==='open'){
            $(this).attr('status','closed');
            $("#theEdits").animate({right: "-25%"}, 500);
            $(this).animate({right: "0"}, 500);
            $(this).find("i").removeClass('fa-minus');
            $(this).find("i").addClass('fa-plus');
        }
        else if($(this).attr('status')==='closed'){
            $(this).attr('status','open');
            $("#theEdits").animate({right: "0%"}, 500);
            $(this).animate({right: "90px"}, 500);
            $(this).find("i").removeClass('fa-plus');
            $(this).find("i").addClass('fa-minus');
        }
        if($("#close-edits").attr("status")=='open' && $("#close-settings").attr("status")=='open'){
        }
        return true;
    });
    $(".back-button").click(function(){
        $("#MensioConfirmation,#MensioMessage").show();
        $("#MensioConfirmation #MensioMessage").animate({left:"0%"},250);
        $("#MensioMessage > #question").html("Are you sure you want to exit the MensioPress Page Builder?");
        $("#answer-YES,#answer-NO").show();
        $("#answer-YES").click(function(){
            window.location.href='admin.php?page=mnsObjPrintAllPages';
        });
        $("#answer-NO").click(function(){
            $("#MensioMessage").hide().animate({left:"-100%"},250);
            $("#MensioConfirmation").hide();
        });
    });
    $("#pg_function select").on('change',function(){
        changePgFunction($(this).val());
    });
    $(".open-settings").click(function(){
        $("#MensioConfirmation").show();
        $("#MensioSettings").show().animate({left:"0%"},250);
    });
    $(".closeConfirmation").click(function(){
        $("#MensioSettings,#MensioMessage").hide().animate({left:"-100%"},250);
        $("#MensioConfirmation").hide();
    });
    $("#MensioConfirmation").not("#MensioSettings").on('click',function(e){
        if(e.target !== this){
            return;
        }
        $("#MensioSettings,#MensioMessage").hide().animate({left:"-100%"},250);
        $("#MensioConfirmation").hide();
    });
    $("#MensioSettings input[name=skin]").change(function(){
        $.ajax({
                type: 'post',
                url: ajaxurl,
                data: { 'Security': $sec,
                  'action': 'mensiopress_mns_quickUpdate',
                  'MensioSkin' : $(this).val()
                },
                success:function(data) {
                },
                error: function(errorThrown){
                    alert(errorThrown);
                }
            });
    });
    $(".MnsCustomSelect span").click(function(){
        if($(this).attr("status")=="closed"){
            $(this).attr("status","open");
            $(this).parent().find(".category").show().css("display","block");
        }
        else if( $(this).attr("status")=="open" ){
            $(this).attr("status","closed");
            if($(this).parent().find(".category.inactive").length>0){
                $(this).parent().find(".category.inactive").css("display","none");
            }
            else{
                $(this).parent().find(".category.active").removeClass("inactive").css("display","block");
            }
        }
    });
    $(".MnsCustomSelect .category").click(function(){
        $(this).parent().find(".category").removeClass("inactive").removeClass("active").hide().css("display","none");
        $(this).removeClass("inactive").addClass("active").show().css("display","block");
        var id=$(this).attr("opt");
        $(this).parent().parent().find("option").attr('selected',false);
        $(this).parent().parent().find("option[value="+id+"]").attr('selected',true);
        $(this).parent().parent().find("select").trigger("change");
    });
    $(".MnsCustomSelect > .category.active").show().css("display","block");
    $(".MnsCustomSelect.NewMensioPageFunction .category").click(function(){
        $(".MnsCustomSelect.NewMensioPageFunction .category").removeClass("inactive").removeClass("active").hide().css("display","none");
        $(this).removeClass("inactive").addClass("active").show().css("display","block");
        var id=$(this).attr("id").replace("MnsOption-","");
        $("#NewMensioPageFunction option").attr('selected',false);
        $("#NewMensioPageFunction option[value="+id+"]").attr('selected',true);
        changePgFunction( id );
    });
    $("button").mouseover(function(){
    });
    $("button").mouseout(function(){
    });
    $("#theEditsProperties").height( $("body").height()-50+"px" );
    $(".bottom-buttons button,.center-bottom-buttons button,#close-settings,#close-edits").mouseover(function(){
    });
    $(".open-editor").click(function(){
        $.ajax({
                type: 'post',
                url: ajaxurl,
                data: { 'Security': $sec,
                  'action': 'mensiopress_mns_OpenModalBox',
                  'OpenWhat' : "HTMLEditor"
                },
                success:function(data) {
                    if($(".openModalBox #wp-MensioHTMLEDITOR-wrap").length==0){
                        $(".openModalBox").show().html(data);
                    }
                    else{
                        $("#MensioHTMLEDITOR-html").click();
                        $(".openModalBox").show();
                        return false;
                    }
                    if($(".mns-block[mns-elem="+$("#edit-object").val()+"]").attr("customhtml")){
                        var customHTML=decodeURI($(".mns-block[mns-elem="+$("#edit-object").val()+"]").attr("customhtml") );
                    }
                    else{
                        var customHTML='';
                    }
                        $(".openModalBox .wp-editor-area").val( customHTML ).on('keyup change',function(){
                            $(".open-editor").parent().find("textarea.CustomHTMLContent").html( $(this).val() );
                            $(".mns-block[mns-elem="+ $("#edit-object").val()+"]").attr("CustomHTML", encodeURI($(this).val()) );
                            $(".mns-block[mns-elem="+ $("#edit-object").val()+"] .CustomHTML").html( $(this).val() );
                        });
                    $(".closeModal,.updateHTML").click(function(){
                        $(".mns-block[mns-elem="+$("#edit-object").val()+"]").attr("customhtml",encodeURI($("#MensioHTMLEDITOR_ifr").html()) );
                        $(this).parent().parent().hide();
                    });
                    $("#MensioHTMLEDITOR-html").click();
                },
                error: function(errorThrown){
                    alert(errorThrown);
                }
            });
    });
    $("#MensioProperties").css("height", $("#theEdits").height() - 100 );
    $("#page_slug").on('keyup change',function(){
        var thisSlug=$(this).val();
        $.ajax({
                type: 'post',
                url: ajaxurl,
                data: { 'Security': $sec,
                  'action': 'mensiopress_MensioCheckSlug',
                  'mensioSlug' : thisSlug
                },
                success:function(data) {
                    if(data>0){
                        $("#slugStatus").html("Slug Already Exists");
                        $("#page_slug").addClass("slug-unavailable");
                    }
                    else{
                        $("#slugStatus").html("Slug is ok");
                        $("#page_slug").removeClass("slug-unavailable");
                    }
                },
                error: function(errorThrown){
                    alert(errorThrown);
                }
            });
    });
    $(".carousel-autoplay").click(function(){
        if($(this).prop("checked")){
            $(this).parent().find(".carousel-autoplay-options").removeClass("closed").addClass("open");
        }
        else{
            $(this).parent().find(".carousel-autoplay-options").removeClass("open").addClass("closed");
        }
    });
    $(".center-bottom-buttons .revert").click(function(){
        if($(".center-bottom-buttons input[name=has-change]").val()!="0"){
            $("#MensioConfirmation,#MensioMessage").show();
            $("#MensioConfirmation #MensioMessage").animate({left:"0%"},250);
            $("#MensioMessage > #question").html("You have unsaved changes! Are you sure you want to exit?");
            $("#answer-YES").click(function(){
                window.location.href="?post="+$("select[name=fast-go-to-edit]").val()+"&page=mns-html-edit";
            });
            $("#answer-NO").click(function(){
                $("#MensioMessage").hide().animate({left:"-100%"},250);
                $("#MensioSettings").hide();
                $("#MensioConfirmation").hide();
            });
        }
        else{
           window.location.href="?post="+$("select[name=fast-go-to-edit]").val()+"&page=mns-html-edit";
       }
    });
    $("select[name=fast-go-to-edit]").change(function(){
        if($(".center-bottom-buttons input[name=has-change]").val()!="0"){
            $("#MensioConfirmation,#MensioMessage").show();
            $("#MensioConfirmation #MensioMessage").animate({left:"0%"},250);
            $("#MensioMessage > #question").html("You has unsaved changes! Are you sure you want to exit?");
            $("#answer-YES").click(function(){
                window.location.href="?post="+$("select[name=fast-go-to-edit]").val()+"&page=mns-html-edit";
            });
            $("#answer-NO").click(function(){
                $("#MensioMessage").hide().animate({left:"-100%"},250);
                $("#MensioSettings").hide();
                $("#MensioConfirmation").hide();
                $("select[name=fast-go-to-edit]").val($("select[name=fast-go-to-edit]").attr("defaultvalue"));
            });
        }
        else{
           window.location.href="?post="+$("select[name=fast-go-to-edit]").val()+"&page=mns-html-edit";
       }
    });
    $("#theEdits .addMetaDescr").click(function(event){
        if(event.target.nodeName=="TEXTAREA"){
            return false;
        }
        if(!$(this).hasClass("active")){
            $(this).animate({height:300},
                function(){
                        $(this).find("textarea[name=page_description]")
                            .show()
                            .focus()
                });
            $(this).addClass("active");
        }
        else{
            $(this).animate({"height":25},
                    function(){
                    $(this).find("textarea[name=page_description]")
                        .hide()
                    });
            $(this).removeClass("active");
        }
    });
    $("#theEdits").mouseover(function(){
    });
});
