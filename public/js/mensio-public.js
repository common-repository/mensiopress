
function CollectVisitorData(){
        var unknown = '-';
        var screenSize = '';
        if (screen.width) {
            width = (screen.width) ? screen.width : '';
            height = (screen.height) ? screen.height : '';
            screenSize += '' + width + " x " + height;
        }
        var nVer = navigator.appVersion;
        var nAgt = navigator.userAgent;
        var browser = navigator.appName;
        var version = '' + parseFloat(navigator.appVersion);
        var majorVersion = parseInt(navigator.appVersion, 10);
        var nameOffset, verOffset, ix;
        if ((verOffset = nAgt.indexOf('Opera')) != -1) {
            browser = 'Opera';
            version = nAgt.substring(verOffset + 6);
            if ((verOffset = nAgt.indexOf('Version')) != -1) {
                version = nAgt.substring(verOffset + 8);
            }
        }
        if ((verOffset = nAgt.indexOf('OPR')) != -1) {
            browser = 'Opera';
            version = nAgt.substring(verOffset + 4);
        }
        else if ((verOffset = nAgt.indexOf('Edge')) != -1) {
            browser = 'Microsoft Edge';
            version = nAgt.substring(verOffset + 5);
        }
        else if ((verOffset = nAgt.indexOf('MSIE')) != -1) {
            browser = 'Microsoft Internet Explorer';
            version = nAgt.substring(verOffset + 5);
        }
        else if ((verOffset = nAgt.indexOf('Chrome')) != -1) {
            browser = 'Chrome';
            version = nAgt.substring(verOffset + 7);
        }
        else if ((verOffset = nAgt.indexOf('Safari')) != -1) {
            browser = 'Safari';
            version = nAgt.substring(verOffset + 7);
            if ((verOffset = nAgt.indexOf('Version')) != -1) {
                version = nAgt.substring(verOffset + 8);
            }
        }
        else if ((verOffset = nAgt.indexOf('Firefox')) != -1) {
            browser = 'Firefox';
            version = nAgt.substring(verOffset + 8);
        }
        else if (nAgt.indexOf('Trident/') != -1) {
            browser = 'Microsoft Internet Explorer';
            version = nAgt.substring(nAgt.indexOf('rv:') + 3);
        }
        else if ((nameOffset = nAgt.lastIndexOf(' ') + 1) < (verOffset = nAgt.lastIndexOf('/'))) {
            browser = nAgt.substring(nameOffset, verOffset);
            version = nAgt.substring(verOffset + 1);
            if (browser.toLowerCase() == browser.toUpperCase()) {
                browser = navigator.appName;
            }
        }
        if ((ix = version.indexOf(';')) != -1) version = version.substring(0, ix);
        if ((ix = version.indexOf(' ')) != -1) version = version.substring(0, ix);
        if ((ix = version.indexOf(')')) != -1) version = version.substring(0, ix);
        majorVersion = parseInt('' + version, 10);
        if (isNaN(majorVersion)) {
            version = '' + parseFloat(navigator.appVersion);
            majorVersion = parseInt(navigator.appVersion, 10);
        }
        var mobile = /Mobile|mini|Fennec|Android|iP(ad|od|hone)/.test(nVer);
        var cookieEnabled = (navigator.cookieEnabled) ? true : false;
        if (typeof navigator.cookieEnabled == 'undefined' && !cookieEnabled) {
            document.cookie = 'testcookie';
            cookieEnabled = (document.cookie.indexOf('testcookie') != -1) ? true : false;
        }
        var os = unknown;
        var clientStrings = [
            {s:'Windows 10', r:/(Windows 10.0|Windows NT 10.0)/},
            {s:'Windows 8.1', r:/(Windows 8.1|Windows NT 6.3)/},
            {s:'Windows 8', r:/(Windows 8|Windows NT 6.2)/},
            {s:'Windows 7', r:/(Windows 7|Windows NT 6.1)/},
            {s:'Windows Vista', r:/Windows NT 6.0/},
            {s:'Windows Server 2003', r:/Windows NT 5.2/},
            {s:'Windows XP', r:/(Windows NT 5.1|Windows XP)/},
            {s:'Windows 2000', r:/(Windows NT 5.0|Windows 2000)/},
            {s:'Windows ME', r:/(Win 9x 4.90|Windows ME)/},
            {s:'Windows 98', r:/(Windows 98|Win98)/},
            {s:'Windows 95', r:/(Windows 95|Win95|Windows_95)/},
            {s:'Windows NT 4.0', r:/(Windows NT 4.0|WinNT4.0|WinNT|Windows NT)/},
            {s:'Windows CE', r:/Windows CE/},
            {s:'Windows 3.11', r:/Win16/},
            {s:'Android', r:/Android/},
            {s:'Open BSD', r:/OpenBSD/},
            {s:'Sun OS', r:/SunOS/},
            {s:'Linux', r:/(Linux|X11)/},
            {s:'iOS', r:/(iPhone|iPad|iPod)/},
            {s:'Mac OS X', r:/Mac OS X/},
            {s:'Mac OS', r:/(MacPPC|MacIntel|Mac_PowerPC|Macintosh)/},
            {s:'QNX', r:/QNX/},
            {s:'UNIX', r:/UNIX/},
            {s:'BeOS', r:/BeOS/},
            {s:'OS/2', r:/OS\/2/},
            {s:'Search Bot', r:/(nuhk|Googlebot|Yammybot|Openbot|Slurp|MSNBot|Ask Jeeves\/Teoma|ia_archiver)/}
        ];
        for (var id in clientStrings) {
            var cs = clientStrings[id];
            if (cs.r.test(nAgt)) {
                os = cs.s;
                break;
            }
        }
        var osVersion = unknown;
        if (/Windows/.test(os)) {
            osVersion = /Windows (.*)/.exec(os)[1];
            os = 'Windows';
        }
        switch (os) {
            case 'Mac OS X':
                osVersion = /Mac OS X (10[\.\_\d]+)/.exec(nAgt)[1];
                break;
            case 'Android':
                osVersion = /Android ([\.\_\d]+)/.exec(nAgt)[1];
                break;
            case 'iOS':
                osVersion = /OS (\d+)_(\d+)_?(\d+)?/.exec(nVer);
                osVersion = osVersion[1] + '.' + osVersion[2] + '.' + (osVersion[3] | 0);
                break;
        }
        var flashVersion = 'no check';
        if (typeof swfobject != 'undefined') {
            var fv = swfobject.getFlashPlayerVersion();
            if (fv.major > 0) {
                flashVersion = fv.major + '.' + fv.minor + ' r' + fv.release;
            }
            else  {
                flashVersion = unknown;
            }
        }
    window.jscd = {
        screen: screenSize,
        browser: browser,
        browserVersion: version,
        browserMajorVersion: majorVersion,
        mobile: mobile,
        os: os,
        osVersion: osVersion,
        cookies: cookieEnabled,
        flashVersion: flashVersion
    };
    return jscd;
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
function login(Object){
    var Username=Object.closest(".login-form").find("input[name=username]").val();
    var Password=Object.closest(".login-form").find("input[name=password]").val();
        var $sec = jQuery('#MensioPressNonce').val();
        jQuery.ajax({
         type: 'post',
         url: ajaxurl,
         data: {
           'action': 'mensiopress_login_post',
           'mns_sec': $sec,'mns_lang': jQuery("#MensioPressLang").val(),
           'mns_username': Username,
           'mns_password': Password
         },
         success:function(data) {
             if(data==false){
                 jQuery(".login-error").show();
             }
             else{
                 var dd = JSON.parse(data);
                 if(dd.TOS!=null){
                 }
                 if(dd.last_seen_product==null){
                     window.location.href="";
                 }
                 else{
                     var conf=confirm("Welcome "+dd.welcome+". Would you like to go back to the product?");
                     if(conf==false){
                         window.location.href="/";
                     }
                     else{
                         window.location.href=dd.last_seen_product;
                     }
                 }
             }
         },
         error: function(errorThrown){
           jQuery(".mns-login .form-error").css("display","block").animate({height:"100px"},500);
         }
     });
}
function chooseSHippingCompany(){
    var $sec = jQuery('#MensioPressNonce').val();
    jQuery(".mensio-checkout-2 .shipping-company").change(function(){
        var ShippingCompany=jQuery(this).val();
        jQuery.ajax({
             type: 'post',
             url: ajaxurl,
             data: {
               'action': 'mensiopress_NewShippingCompany',
               'mns_sec': $sec,'mns_lang': jQuery("#MensioPressLang").val(),
               'mns_ShippingCompany': ShippingCompany
             },
             success:function(data) {
                 if(typeof MensioPressShowCoupons !== "undefined"){
                     MensioPressShowCoupons();
                 }
             }
         });
        var finalcost= Number(jQuery(".mns-FinalCost").attr("cost")) + Number(jQuery(this).attr("cost"));
        jQuery(".MensioPayButton .Total").html( finalcost.toFixed(2) );
        jQuery(".MensioPayButton").attr("totalcost", finalcost.toFixed(2) );
        if(jQuery("#pay-delivery-"+jQuery(this).val()).length!=0){
            var thisID=jQuery("#pay-delivery-"+jQuery(this).val()).attr("id");
            jQuery("#"+thisID.replace("pay-with-","")).css("display","inline-block");
        }
    });
}
function MensioPressPaginationControl(){
}
function MensioAddToCart(){
    var $sec = jQuery('#MensioPressNonce').val();
    jQuery(".add-to-cart").click(function(){
        var prod=jQuery(this).attr("id").replace("product-","");
        var quant=jQuery(".add-to-cart-quant input").val();
        if(quant==null){
            quant=1;
        }
        var CartStyle=jQuery(".MensioPressWidgetCart").attr("style");
        var PageText=jQuery(".MensioPressWidgetCart .Widget-row:last .cell a").html();
        var CartTitle=jQuery(".MensioPressWidgetCart .MensioWidgetTitle").html();
        jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            data: {
              'action': 'mensiopress_AddtoCart',
              'mns_sec':$sec,'mns_lang': jQuery("#MensioPressLang").val(),
              'mns_prod': prod,
              'mns_quant': quant
            },
            success:function(data) {
                var obj=JSON.parse(data);
                MensioMessage(obj.Message);
                jQuery(".MensioPressWidgetCart").before(obj.Cart).remove();
                jQuery(".MensioPressWidgetCart #MensioCartQuantities").html(obj.Quants);
                jQuery(".MensioPressWidgetCart").attr("style",CartStyle);
                jQuery(".MensioPressWidgetCart .MensioWidgetTitle").html(CartTitle);
                jQuery(".MensioPressWidgetFavoritesList .Widget-row:last .cell a").html(PageText);
            },
            error: function(errorThrown){
            }
        }); 
    });
}
function MensioPressAlert(message){
    message = "<div class='message'>"+message.replace(/\\/g, '')+"</div>";
    message+="<i class='fa fa-close close'></i>";
    jQuery('#MNSAlertText').html(message);
    jQuery('#MNSAlert').show();
    jQuery('#MNSAlert .close').click(function(){
        jQuery(this).parent().parent().hide();
    });
}
function MensioMessage(message){
if(message==false){
    return false;
}
  var Duration=jQuery("#MNSMessage").attr("data-duration");
  jQuery('#MNSMessage').html(message);
  jQuery('#MNSMessage').slideDown('slow');
  setTimeout (function() {
      jQuery('#MNSMessage').fadeOut('slow',function (){
      });
    },
    Duration
  );
}
function MensioFormPagination(){
}
function imageExists(image_url){
    var http = new XMLHttpRequest();
    http.open('HEAD', image_url, false);
    http.send();
    return http.status != 404;
}
function PasswordChangeButton(){
    jQuery(".mns-block.mns-user .changePassword input").css("border","");
    var $sec = jQuery('#MensioPressNonce').val();
    var thisParent=jQuery('.changePassword').parent();
    jQuery('.changePassword').parent().html("<div class='changePassword'>Current Password: <input type='password' name='CurrentPassword'>New Password: <input type='password' name='NewPassword'>Repeat New Password: <input type='password' name='RepeatNewPassword'><br /><input type='button' value='Change Password'></div>");
    jQuery('.changePassword input[type=button]').on('click',function(){
        if(jQuery(".changePassword input[name=NewPassword]").val()!=jQuery(".changePassword input[name=RepeatNewPassword]").val() ){
            MensioMessage("Passwords do not match");
            jQuery(".changePassword input[name=NewPassword],.changePassword input[name=RepeatNewPassword]").css("border","1px solid red");
            return false;
        }
        jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            data: {
              'action': 'mensiopress_changePassword',
              'mns_sec':$sec,'mns_lang': jQuery("#MensioPressLang").val(),
              'CurrentPassword': jQuery(".changePassword input[name=CurrentPassword]").val(),
              'NewPassword': jQuery(".changePassword input[name=NewPassword]").val()
            },
            success:function(data) {
                var obj=JSON.parse(data);
                MensioMessage(obj.Message);
                if(obj.Code=="2"){
                    jQuery(".changePassword input[name=CurrentPassword]").css("border","1px solid red");
                }
                if(obj.Code=="1"){
                    thisParent.html('************************ <a href="#" class="changePassword">(Change)</a></div>');
                    jQuery(".changePassword").click(function(){
                        PasswordChangeButton();
                        return false;
                    });
                }
            }
        });
    });
}
function MensioPressFiltering(Object){
        var attributes=new Array();
        var filters=new Array();
        var categories=new Array();
        var brands=new Array();
        var $sec = jQuery('#MensioPressNonce').val();
        var ProductFilters=jQuery(this).closest(".mns-product-filters");
        ProductFilters.css("opacity","0.5");
        jQuery(".mns-category-products.mns-list").css("opacity","0.3");
        if(jQuery(this).attr("type")=="checkbox"){
            jQuery(this).closest(".mns-block").find(".mns-category-products.mns-list").attr("page","0");
        }
        if(jQuery(".mns-category-products.mns-list").attr("page") < 0){
            jQuery(".mns-category-products.mns-list").attr("page","0");
            return false;
        }
        categories=jQuery(".mns-block.mns-category").find(".mns-category-products.mns-list").attr("categoryid");
        jQuery(".mns-block.mns-category").find(".filter.check input[type=checkbox]")
                .parent().parent().removeClass("selected");
        jQuery(".mns-block.mns-category").find(".filter.check:not(.brand-sel) input[type=checkbox]:checked")
            .each(function(){
            if(jQuery(this).val()){
                jQuery(this).parent().parent().addClass("selected");
                attributes.push( jQuery(this).val() );
            }
        });
        jQuery(".mns-block.mns-category").find(".filter.check:not(.brand-sel) input[type=checkbox]:checked")
            .each(function(){
            if(jQuery(this).val()){
                jQuery(this).parent().parent().addClass("selected");
                filters.push( jQuery(this).parent().parent().attr("filter") );
            }
        });
        jQuery(".mns-block.mns-category").find(".filter.brand-sel input[type=checkbox]:checked").each(function(){
            if(jQuery(this).val()){
                jQuery(this).parent().parent().addClass("selected");
                brands.push( jQuery(this).val() );
            }
        });
        var mnsAtts=jQuery(".mns-block.mns-category .mns-category-products.mns-list").attr("atts");
        var ResultPage="";
        if(!jQuery(this).closest(".mns-block").find(".mns-category-products.mns-list").attr("page")){
            jQuery(this).closest(".mns-block").find(".mns-category-products.mns-list").attr("page","1");
            ResultPage=1;
        }
        else{
            ResultPage=jQuery(this).closest(".mns-block").find(".mns-category-products.mns-list").attr("page");
        }
        var MinPrice=jQuery(".mns-block.mns-category").find(".pricesrange input[name=price-min]").val();
        var MaxPrice=jQuery(".mns-block.mns-category").find(".pricesrange input[name=price-max]").val();
        var OrderVal=jQuery("select[name=products-list-sort]").val();
        var Page=jQuery(".mns-block.mns-category").find(".mns-list").attr("page");
        var Items=jQuery(".mns-block.mns-category").find(".mns-list").attr("items");
        var TotalPages=jQuery(".mns-block.mns-category").find(".mns-list").attr("totalpages");
        if(Page==false){
            Page=1;
        }
        jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            data: {
                'action': 'mensiopress_FilterSearch',
                'mns_sec':$sec,'mns_lang': jQuery("#MensioPressLang").val(),
                'mns_keyword': jQuery("#mnsSearch-text").val(),
                'mns_Attributes':attributes,
                'mns_Filters':filters,
                'mns_Cats':categories,
                'mns_Brands':brands,
                'mns_Page':ResultPage,
                'SearchAtts':mnsAtts,
                'MinPrice':MinPrice,
                'MaxPrice':MaxPrice,
                'Order':OrderVal,
                'Items':Items,
                'Page':Page,
                'TotalPages':TotalPages
            },
            success:function(data) {
                data=jQuery.parseJSON(data);
                ProductFilters.css("opacity","1");
                jQuery('div.FilterAttributesCell label[filter]').css('opacity','0.2');
                jQuery('div.FilterAttributesCell label[brand]').css('opacity','0.2');
                jQuery('div.FilterAttributesCell label[filter] input[type=checkbox]').prop('disabled',true);
                jQuery('div.FilterAttributesCell label[brand] input[type=checkbox]').prop('disabled',true);
                jQuery(".mns-category-products.mns-list").css("opacity","1").html(data.Products);
                jQuery('div.FilterAttributesCell label').css('opacity','0.2')
                        .find("input[type=checkbox]").prop("disabled",true);
                if(data.AllowAtts.length>0){
                    var AllowAtts=jQuery.parseJSON(data.AllowAtts);
                    jQuery(AllowAtts).each(function(index,att){
                        jQuery('div.FilterAttributesCell label[filter='+att+']').css('opacity','1')
                            .find("input[type=checkbox]").prop("disabled",false);
                    });
                }
                if(data.AllowBrands.length>0){
                    var AllowBrands=jQuery.parseJSON(data.AllowBrands);
                    jQuery(AllowBrands).each(function(index,brand){
                        jQuery('div.FilterAttributesCell label[filter='+brand+']').css('opacity','1')
                            .find("input[type=checkbox]").prop("disabled",false);
                    });
                }
                jQuery(".mns-block.mns-category .next-page-result").click(function(){
                    jQuery(".mns-category-products.mns-list").attr("page",(parseInt(jQuery(".mns-category-products.mns-list").attr("page"))+1));
                    jQuery(".mns-block.mns-category input[name=search]").click();
                });
                jQuery(".mns-block.mns-category .prev-page-result").click(function(){
                    if(jQuery(".mns-category-products.mns-list").attr("page")!="0"){
                        jQuery(".mns-category-products.mns-list").attr("page",(parseInt(jQuery(".mns-category-products.mns-list").attr("page"))-1));
                        jQuery(".mns-block.mns-category input[name=search]").click();
                    }
                });
                jQuery(".mns-category-products.mns-list span.NavPage[value="+ jQuery(".mns-category-products.mns-list").attr("page")+"]" ).addClass("selected");
                jQuery(".mns-category-products.mns-list span.NavPage").click(function(){
                    jQuery(".mns-category-products.mns-list").attr("page",jQuery(this).attr("value"));
                    jQuery(".mns-block.mns-category input[name=search]").click();
                });
                jQuery(".ItemShow").click(function(){
                    jQuery(this).closest(".mns-category-products").attr("items",parseInt(jQuery(this).html()))
                    jQuery(this).closest(".mns-block").find("select[name=products-list-sort]").trigger("change");
                });
                var AllPages=parseInt(jQuery(".mns-category-products.mns-list .Pagination li:last").prev().html());
                var newPage=0;
                jQuery(".mns-category-products.mns-list .Pagination li:not(:last):not(:first)").click(function(){
                    jQuery(".mns-category-products.mns-list").attr("page", jQuery(this).html() );
                    jQuery(this).closest(".mns-block").find("select[name=products-list-sort]").trigger("change");
                });
                jQuery(".mns-category-products.mns-list .Pagination li:last").click(function(){
                    newPage=parseInt(jQuery(".mns-category-products.mns-list").attr("page"))+1;
                    if(newPage>AllPages){
                        newPage=AllPages;
                    }
                    jQuery(".mns-category-products.mns-list").attr("page", newPage );
                    jQuery(this).closest(".mns-block").find("select[name=products-list-sort]").trigger("change");
                });
                jQuery(".mns-category-products.mns-list .Pagination li:first").click(function(){
                    newPage=parseInt(jQuery(".mns-category-products.mns-list").attr("page"))-1;
                    if(newPage<1){
                        newPage=1;
                    }
                    jQuery(".mns-category-products.mns-list").attr("page", newPage );
                    jQuery(this).closest(".mns-block").find("select[name=products-list-sort]").trigger("change");
                });
                if(jQuery(".mns-block.mns-category ul.pagination li").length>7){
                jQuery(".mns-block.mns-category ul.pagination li")
                        .removeClass("showPagination")
                        .parent().find(".active").addClass("showPagination");
                        if(jQuery(".mns-block.mns-category ul.pagination li.active").prev().length==1){
                            jQuery(".mns-block.mns-category ul.pagination li.active")
                                    .prev()
                                    .addClass("showPagination");
                        }
                        if(jQuery(".mns-block.mns-category ul.pagination li.active").prev().prev().length==1){
                            jQuery(".mns-block.mns-category ul.pagination li.active")
                                    .prev()
                                    .prev()
                                    .addClass("showPagination");
                        }
                        if(jQuery(".mns-block.mns-category ul.pagination li.active").next().length==1){
                            jQuery(".mns-block.mns-category ul.pagination li.active")
                                    .next()
                                    .addClass("showPagination");
                        }
                        if(jQuery(".mns-block.mns-category ul.pagination li.active").next().next().length==1){
                            jQuery(".mns-block.mns-category ul.pagination li.active")
                                    .next()
                                    .next()
                                    .addClass("showPagination");
                        }
                }
                else{
                    jQuery(".mns-block.mns-category ul.pagination li").addClass("showPagination");
                }
                if(jQuery(".FilterAttributesCell.firstChecked").length==1 && jQuery(".FilterAttributesCell.firstChecked input:checked").length==0){
                    jQuery(".FilterAttributesCell.firstChecked").removeClass("firstChecked");
                    jQuery("label[filter] input:checked:first")
                            .closest(".FilterAttributesCell").addClass("firstChecked")
                            .find("*").css("opacity","1")
                            .find("input").prop("disabled",false);
                }
                if(jQuery(".FilterAttributesCell.firstChecked").length==0){
                    Object.closest(".FilterAttributesCell").addClass("firstChecked");
                }
                jQuery(".FilterAttributesCell.firstChecked")
                        .find("label[filter]").css("opacity","1")
                        .find("input").prop("disabled",false);
                MensioAddToCart();
                MensioAddToFavorites();
                MensioAddToCompareList();
                return false;
            }
        });
}
jQuery(document).ready(function() {
    var $sec = jQuery('#MensioPressNonce').val();
    var collectables=CollectVisitorData();
    var browser=collectables.browser+" - "+collectables.browserMajorVersion;
    var os=collectables.os;
    if(collectables.osVersion == true && collectables.osVersion != "-"){
        os+=" - "+collectables.osVersion;
    }
    var screen=collectables.screen;
    jQuery.ajax({
        type: 'post',
        url: ajaxurl,
        data: {
          'action': 'mensiopress_CollectVisitorData',
          'mns_sec':$sec,'mns_lang': jQuery("#MensioPressLang").val(),
          'mns_browser': browser,
          'mns_os': os,
          'mns_screen': screen
        },
        error:function(value,error,val){
            jQuery("#mensio_msg").html(val);
        }
    });
    jQuery(".mns-signup input[name=mns-email]").keyup(function(){
        var Input=jQuery(this);
        var Username=jQuery(this).val();
        jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            data: {
              'action': 'mensiopress_checkSignupUsername',
              'mns_sec':$sec,'mns_lang': jQuery("#MensioPressLang").val(),
              'UsernameToCheck': Username
            },
            success:function(data){
                if(data){
                    Input.css("border","1px solid red");
                }
                else{
                    Input.css("border","");
                }
            }
        });
    });
    jQuery(".mns-signup .next-form-button").click(function(){
        var empty="";
        jQuery(this).closest(".signup-form").find("input,select").each(function() {
            if(jQuery(this).parent().parent().attr("id")!="company-data"){
                if(jQuery(this).attr("type")!="password"){
                    jQuery(this).css("border","");
                }
                if(jQuery(this).val()=="" || jQuery(this).attr("data")=="wrong"){
                    jQuery(this).css("border","1px solid red");
                    empty='1';
                }
            }
        });
        jQuery(".mns-block.mns-signup .signup-data").html("");
        jQuery(this).closest(".mns-block.mns-signup").find("input[type=text],select").each(function(){
            if(jQuery(this).parent().parent().attr("id")!="company-data"){
                jQuery(".mns-block.mns-signup .signup-data").
                    append("<div><span>"+ jQuery(this).parent().find("span").html()
                        +":</span> <strong>"+ jQuery(this).val()+"</strong></div>");
            }
        });
        if(empty.length>0) {
            return false;
        }
        jQuery(this).parent().parent().animate({marginLeft:"-200px",opacity:0},750,function(){
            jQuery(this).css("display","none");
            if(jQuery("#mns-user-type").val()!='Company'){
                jQuery("#company-data").hide();
            }
            else{
                jQuery("#company-data").show();
            }
            jQuery(this).next(".signup-form").css("display","inline-block").animate({
                marginLeft:'0px',
                opacity:1
            },750);
        });
        return false;
    });
    if(jQuery(".mns-block select[name=mns-country]").attr("current-country")==true){
        jQuery(".mns-block select[name=mns-country] option[value="+jQuery(".mns-block select[name=mns-country]").attr("current-country")+"]").prop("selected",true);
    }
    jQuery(".mns-signup .back-form-button").click(function(){
        jQuery(this).parent().parent().animate({marginLeft:"-200px",opacity:0},750,function(){
            jQuery(this).css("display","none").css("margin","").css("margin-left","");
            jQuery(this).prev().css("display","block").animate({
                marginLeft: "0px" ,
                opacity:1
            },750);
        });
        return false;
    });
    jQuery(".mns-signup input[name=mns-repeat-password]").blur(function(){
        jQuery(this).css("border","");
        jQuery(".mns-signup input[name=mns-password]").css("border","").attr("data","");
        jQuery(".mns-signup input[name=mns-repeat-password]").css("border","").attr("data","");
        if(jQuery(this).val()!=jQuery(".mns-signup input[name=mns-password]").val()){
            jQuery(this).css("border","1px solid red").attr("data","wrong");
            jQuery(".mns-signup input[name=mns-password]").css("border","1px solid red");
        }
    });
    jQuery(".mns-signup .submit-button").click(function(){
        var FBID;
        if(jQuery(".mns-block.mns-signup input[name=mns-fbid]").length==true){
            FBID=jQuery(".mns-block.mns-signup input[name=mns-fbid]").val();
        }
        else{
            FBID=false;
        }
        jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            data: {
              'action': 'mensiopress_signup',
              'mns_sec':$sec,'mns_lang': jQuery("#MensioPressLang").val(),
              'mns_user_type': jQuery("select[name=mns-user-type]").val(),
              'mns_user_title': jQuery("select[name=mns-user-title]").val(),
              'mns_password': jQuery("input[name=mns-password]").val(),
              'mns_email': jQuery("input[name=mns-email]").val(),
              'mns_firstname': jQuery("input[name=mns-firstname]").val(),
              'mns_lastname': jQuery("input[name=mns-lastname]").val(),
              'mns_company_name': jQuery("input[name=mns-company-name]").val(),
              'mns_company_sector': jQuery("select[name=mns-company-sector]").val(),
              'mns_company_tin': jQuery("input[name=mns-company-tin]").val(),
              'mns_website': jQuery("input[name=mns-website]").val(),
              'mns_company_email': jQuery("input[name=mns-company-email]").val(),
              'mns_country': jQuery("select[name=mns-country]").val(),
              'mns_region': jQuery("select[name=mns-region]").val(),
              'mns_city': jQuery("input[name=mns-city]").val(),
              'mns_address': jQuery("input[name=mns-address]").val(),
              'mns_zip_code': jQuery("input[name=mns-zipcode]").val(),
              'mns_phone': jQuery("input[name=mns-phone]").val(),
              'mns_tos': jQuery("input[name=mns-terms_of_use]").prop("checked"),
              'fbid': FBID
            },
            success:function(data) {
                if(data==1){
                    MensioMessage("Check your email. You have to verify your account");
                    window.location.href="/";
                }
                else if(data==2){
                    MensioMessage("email already exists");
                }
                else if(data==3){
                    MensioMessage("Some of the fields are blank");
                }
                else if(data==4){
                    MensioMessage("You must agree to the terms of service");
                }
                else{
                    MensioMessage(data);
                }
            },
            error: function(errorThrown){
                MensioMessage(errorThrown);
                jQuery(".mns-signup .form-error").css("display","block").animate({height:"100px"},500);
            }
        });
    });
    jQuery(".mns-block.mns-checkout input[type=radio]").click(function(){
        return false;
    });
    jQuery("select[name=mns-country]").change(function(){
        var thisValue=jQuery(this).val();
        var thisObject=jQuery(this);
        if(jQuery(this).closest(".mns-block").hasClass("mns-checkout")){
            thisValue=jQuery(this).find("option:selected").attr("data-default");
        }
        var Language=false;
        if(jQuery(this).attr("language")){
            Language=jQuery(this).attr("language");
        }
        jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            data: {
              'action': 'mensiopress_choose_Region',
              'mns_sec':$sec,'mns_lang': jQuery("#MensioPressLang").val(),
              'mns_country': thisValue,
              'Language':Language
            },
            success:function(data) {
                var regions=JSON.parse(data);
                if(thisObject.closest(".mns-block").hasClass("mns-checkout")){
                    jQuery("select[name=mns-region]").html("").prop("disabled",true);
                    if(regions.length>0){
                        jQuery("select[name=mns-region]").prop("disabled",false).parent().show();
                        for(var i=0;i<=regions.length;i++){
                            if(regions[i]==null){
                                break;
                            }
                            jQuery("select[name=mns-region]").append("<option>"+regions[i]+"</option>");
                        }
                        jQuery(this).attr("country",jQuery(this).val());
                    }
                }
                if(thisObject.closest(".mns-block").hasClass("mns-signup")){
                    jQuery("select[name=mns-region]").html("").prop("disabled",true);
                    if(regions.length>0){
                        jQuery("select[name=mns-region]").prop("disabled",false).parent().show();
                        for(var i=0;i<=regions.length;i++){
                            if(regions[i]==null){
                                break;
                            }
                            jQuery("select[name=mns-region]").append("<option>"+regions[i]+"</option>");
                        }
                        jQuery(this).attr("country",jQuery(this).val());
                    }
                    if(jQuery.isEmptyObject(regions)){
                        jQuery("select[name=mns-region]").append("<option>NO-REGION</option>").parent().hide();
                    }
                }
                else if(thisObject.closest(".mns-block").hasClass("mns-user")){
                    thisObject.parent().parent().next().find("select[name=mns-region]").html("").prop("disabled",true);
                    if(regions.length>0){
                        thisObject.parent().parent().next().find("select[name=mns-region]").prop("disabled",false).parent().show();
                        for(var i=0;i<=regions.length;i++){
                            if(regions[i]==null){
                                break;
                            }
                            thisObject.parent().parent().next().find("select[name=mns-region]").append("<option>"+regions[i]+"</option>");
                        }
                        jQuery(this).attr("country",jQuery(this).val());
                    }
                    if(jQuery.isEmptyObject(regions)){
                        thisObject.parent().parent().next().find("select[name=mns-region]").append("<option>NO-REGION</option>").parent().hide();
                    }
                }
            }
        });
    });
    jQuery(".mns-login .login-form > .form-input > .submit-button,.MensioPressUserQuickMenu .login-form > .form-input > .submit-button").click(function(){
        login(jQuery(this));
    });
    jQuery(".mns-login .login-form > .form-input > input,.MensioPressUserQuickMenu .login-form > .form-input > input").keypress(function(e){
        if(e.which==13){
           login(jQuery(this));
        }
    });
    jQuery(".MensioPressLogout").click(function(){
        var Message=jQuery(this).attr("data-message");
        var conf=confirm(Message);
            if(conf==true){
            jQuery.ajax({
                type: 'post',
                url: ajaxurl,
                data: {
                  'action': 'mensiopress_logout',
                  'mns_sec':$sec,'mns_lang': jQuery("#MensioPressLang").val()
                },
                success:function(data) {
                    window.location.href='';
                }
            });
        }
        return false;
    });
    jQuery(".changePassword").click(function(){
        PasswordChangeButton();
            return false;
    });
    jQuery(".mns-login .login-form > .form-input > a.forgot-pass,\n\
            .MensioPressUserQuickMenu .form-input > a.forgot-pass").click(function(){
        if(jQuery(this).closest(".mns-block").length>0){
            jQuery(this).closest(".mns-block").find(".login-form").hide();
            jQuery(this).closest(".mns-block").find(".forgot-pass-form").show();
        }
        else if(jQuery(this).closest(".MensioPressUserQuickMenu").length>0){
            jQuery(this).closest(".MensioPressUserQuickMenu").find(".login-form").hide();
            jQuery(this).closest(".MensioPressUserQuickMenu").find(".forgot-pass-form").show().find("input[type=text]").focus();
        }
        return false;
    });
    jQuery(".MensioPressUserQuickMenu .login-form > .form-input > a.forgot-pass").click(function(){
        jQuery(this).closest(".MensioPressUserQuickMenu").find(".login-form").hide();
        jQuery(this).closest(".MensioPressUserQuickMenu").find(".forgot-pass-form").show();
        return false;
    });
    jQuery(".mns-block a.forgot-pass").click(function(){
        jQuery(this).closest(".mns-block").find(".login-form").hide();
        jQuery(this).closest(".mns-block").find(".forgot-pass-form").show();
        return false;
    });
    jQuery(".MensioPressUserQuickMenu .forgot-pass-form .submit-button,.mns-login .forgot-pass-form .submit-button").click(function(){
        var Username=jQuery(this).closest(".forgot-pass-form").find("input[name=username-forgot]").val();
        jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            data: {
              'action': 'mensiopress_ForgotPass',
              'mns_sec':$sec,'mns_lang': jQuery("#MensioPressLang").val(),
              'mns_username': Username
            },
            success:function(data) {
                if(data==0){
                    jQuery(".forgot-pass-form .forgot-pass-fail").show();
                    jQuery(".forgot-pass-form .forgot-pass-success").hide();
                }
                else{
                    jQuery(".forgot-pass-form .forgot-pass-fail").hide();
                    jQuery(".forgot-pass-form .forgot-pass-success").show();
                }
            }
        });
    });
    jQuery(".mns-block.mns-contact .submit-button").click(function(){
        var vals=Array();
        jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            data: {
              'action': 'mensiopress_contact_post',
              'mns_sec':$sec,'mns_lang': jQuery("#MensioPressLang").val(),
              'mns_name': jQuery("input[name=name]").val(),
              'mns_lastname': jQuery("input[name=lastname]").val(),
              'mns_email': jQuery("input[name=email]").val(),
              'mns_phone': jQuery("input[name=phone]").val(),
              'mns_cellphone': jQuery("input[name=cellphone]").val(),
              'mns_message': jQuery("textarea[name=message]").val()
            },
            success:function(data) {
                if(data!='ok'){
                    jQuery('.contact-form input[type=text],.contact-form textarea').val('');
                   MensioMessage(data);
                }
                else{
                    jQuery(".contact-form input[type=text],.contact-form textarea").val("");
                    jQuery(".contact-result").css("display","block").html("Message was sent");
                }
            },
            error: function(errorThrown){
            }
        }); 
    });
    jQuery(".mns-html-content .product-view .more-images img").click(function(){
        jQuery("#main-img > img").attr('src', jQuery(this).attr('src'))
            .attr('data-zoom-image', jQuery(this).attr('data-zoom-image'))
            .data('high-res-src', jQuery(this).attr('data-high-res-src'));
        jQuery(".zoomContainer > .zoomWindowContainer > div").css('background-image', "url("+jQuery(this).attr('data-high-res-src')+")");
    });
    jQuery("#product-details .quant-plus").click(function(){
        if(jQuery("#product-details .mns-quant").val()>=1){
            jQuery("#product-details .mns-quant").val( +jQuery("#product-details .mns-quant").val() + 1 );
        }
        else{
            jQuery("#product-details .mns-quant").val( 1 );
        }
    });
    jQuery("#product-details .quant-minus").click(function(){
        if(jQuery("#product-details .mns-quant").val()>=2){
            jQuery("#product-details .mns-quant").val( +jQuery("#product-details .mns-quant").val()-1 );
        }
        else{
            jQuery("#product-details .mns-quant").val( 1 );
        }
    });
    jQuery(".MensioProductDetails > div").click(function(){
        jQuery(this).parent().find("div").css("background","#ececec");
        jQuery(this).css("background","#ffffff");
        var Tab=jQuery(this).attr("id");
        jQuery("#Product-Tabs > div").css("background","transparent").fadeOut("100");
        jQuery("#Product-Tabs #"+ Tab +"-Tab").fadeIn("100");
    });
    MensioAddToCart();
    var total=1;
    jQuery(".mns-cart .change-quant").on('keyup change',function(){
        var cost=jQuery(this).parent().parent().find('.product-cost').attr('cost');
        var total_price=(cost * jQuery(this).val()).toFixed(2);
        jQuery(this).parent().parent().find('.cart-product-cost').html( total_price ).attr("cost",total_price);
        var grand_total=0;
        jQuery(".mns-cart .cart-product-cost").each(function(){
            cost=parseFloat(jQuery(this).text());
            grand_total+= cost;
        });
        jQuery(".cart-grand-total").html( grand_total.toFixed(2) );
    });
    jQuery(".mns-cart .checkout-button").click(function(){
        jQuery("body").animate({scrollTop: jQuery(".mns-cart").position().top },500);
        jQuery(".mns-cart .mns-list").css("overflow","hidden").animate({height:"0"},500);
        jQuery(".mns-cart .mns-checkout").css("padding","0").css("overflow","hidden").animate({height:"580px"},500);
    });
    jQuery("#mns-filters-expand").click(function(){
        var filters=jQuery(this).parent();
        if(filters.hasClass("mns-filters-closed")){
            jQuery(this).animate({right:"10px"},500);
            filters.animate({left:"0"},500);
            filters.removeClass("mns-filters-closed").addClass("mns-filters-open");
        }
        else if(filters.hasClass("mns-filters-open")){
            jQuery(this).animate({right:"-50px"},500);
            filters.animate({left:"-50%"},500);
            filters.removeClass("mns-filters-open").addClass("mns-filters-closed");
        }
    });
    var price_range=jQuery(".mns-product-filters #price-max");
    price_range.change(function(){
        jQuery("#max-price-to-show").html("Max Price: <span class='mensioPrice'>"+ jQuery(this).val()+"</span>") ;
    });
    jQuery(".MensioTopRightCart .remove-from-cart").click(function(){
        var buttonIndex=jQuery(".MensioTopRightCart .remove-from-cart").index(this);
        var CartRow=jQuery(this).parent().parent().parent().parent();
        var CartPrice=jQuery(this).parent().parent().parent().parent().find(".cart-product-cost").attr("cost");
        var Cart=jQuery(this).parent().parent().parent().parent();
        jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            data: {
                'action': 'mensiopress_RemoveFromCart',
                'mns_sec':$sec,'mns_lang': jQuery("#MensioPressLang").val(),
                'mnsProdRemove': buttonIndex
            },
            success:function(data) {
                var obj=JSON.parse(data);
                MensioMessage(obj.Message);
                jQuery(".MensioTopRightCart > #MensioCartQuantities").html(obj.Quants);
                jQuery(".MensioTopRightCartDIV > #MensioCart").html(obj.Cart);
                CartRow.remove();
                if(Cart.find(".remove-from-cart").length==0){
                    jQuery(".cart-grand-total").html("");
                }
                else{
                    jQuery(".cart-grand-total").html(jQuery(".cart-grand-total").html()-CartPrice);
                }
                if(obj.Quants!=0){
                    jQuery(".MensioTopRightCartDIV > #MensioCart").removeClass("Empty");
                }
                else{
                    jQuery(".MensioTopRightCartDIV > #MensioCart").addClass("Empty");
                }
            },
            error: function(errorThrown){
            }
        });
    });
    jQuery(".mns-block.mns-cart .remove-from-cart").click(function(){
        var buttonIndex=jQuery(".mns-block.mns-cart .remove-from-cart").index(this);
        var CartRow=jQuery(this).parent().parent();
        var CartPrice=jQuery(this).parent().parent().find(".cart-product-cost").attr("cost");
        jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            data: {
                'action': 'mensiopress_RemoveFromCart',
                'mns_sec':$sec,'mns_lang': jQuery("#MensioPressLang").val(),
                'mnsProdRemove': buttonIndex
            },
            success:function(data) {
                var obj=JSON.parse(data);
                MensioMessage(obj.Message);
                jQuery(".MensioTopRightCart > #MensioCartQuantities").html(obj.Quants);
                jQuery(".MensioTopRightCartDIV > #MensioCart").html(obj.Cart);
                CartRow.remove();
                if(CartPrice==false){
                    jQuery(".cart-grand-total").html("");
                }
                else{
                    var grand_total=0;
                    jQuery(".mns-cart .cart-product-cost").each(function(){
                        cost=parseFloat(jQuery(this).text());
                        grand_total+= cost;
                    });
                    jQuery(".cart-grand-total").html( grand_total.toFixed(2) );
                }
                if(obj.Quants!=0){
                    jQuery(".MensioTopRightCartDIV > #MensioCart").removeClass("Empty");
                }
                else{
                    jQuery(".MensioTopRightCartDIV > #MensioCart").addClass("Empty");
                }
                if(jQuery(".mns-block.mns-cart button.remove-from-cart").length==0){
                    jQuery(".mns-block.mns-cart .mns-list ").hide();
                    jQuery(".mns-block.mns-cart .noprodsfound").show();
                }
            },
            error: function(errorThrown){
            }
        });
    });
    jQuery(".mns-block.mns-cart .change-quant").on("change keyup",function(){
        if(jQuery(this).val()==0){
            jQuery(this).val("1").change();
            return false;
        }
        var ProductIndex=jQuery(".mns-block.mns-cart .change-quant").index(this);
        var ProductNewQuant=jQuery(this).val();
        jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            data: {
                'action': 'mensiopress_UpdateCartQuant',
                'mns_sec':$sec,'mns_lang': jQuery("#MensioPressLang").val(),
                'mnsProd': ProductIndex,
                'mnsProdNewQuant': ProductNewQuant
            },
            success:function(data) {
                var obj=JSON.parse(data);
                MensioMessage(obj.Message);
                jQuery(".MensioTopRightCart > #MensioCartQuantities").html(obj.Quants);
                jQuery(".MensioTopRightCartDIV > #MensioCart").html(obj.Cart);
            },
        });
    });
    var resetfilters=1;
    jQuery(".mns-product-filters .MensioOpenFilters-wrapper .MensioOpenFilters").click(function(){
        if(resetfilters==1){
            jQuery(".resetFilters").click();
            resetfilters=2;
        }
        if(jQuery(this).closest(".mns-product-filters").hasClass("mns-filters-closed")){
            jQuery(this).closest(".mns-product-filters").removeClass("mns-filters-closed");
            jQuery(".mns-product-filters .owl-stage-outer").width( jQuery(".mns-product-filters #mnsSearch").width() );
            jQuery(".mns-block.mns-category div.mns-content").css("margin-top","0px");
        }
        else{
            jQuery(this).closest(".mns-product-filters").addClass("mns-filters-closed");
            jQuery(".MensioOpenFilters-wrapper").show();
            jQuery(".mns-block.mns-category div.mns-content").css("margin-top","65px");
        }
        return false;
    });
    jQuery(".mns-products-filters .filter.check").click(function(){
        jQuery(this).find("input[type=checkbox]").prop("checked",true);
    });
    jQuery(".MensioSorting .fa").click(function(){
        jQuery(".MensioSorting .fa").removeClass("checked");
        jQuery(this).addClass("checked");
        jQuery(".MensioSorting select[name=products-list-sort]").val( jQuery(this).attr("sort") ).trigger("change");
        var Sort=false;
        if(jQuery(this).attr("sort")){
            Sort=jQuery(this).attr("sort");
        }
        jQuery.ajax({
             type: 'post',
             url: ajaxurl,
             data: {
               'action': 'mensiopress_SaveOrderingWay',
               'mns_sec':$sec,'mns_lang': jQuery("#MensioPressLang").val(),
               'DefaultSort':Sort
             }
         });
    });
    jQuery(".resetFilters").click(function(){
        var priceRange=jQuery(".price-range").data("ionRangeSlider");
        jQuery(".mns-block.mns-category").find(".pricesrange input[name=price-min]").val(MinPrice);
        jQuery(".mns-block.mns-category").find(".pricesrange input[name=price-max]").val(MaxPrice);
        jQuery(this).closest(".mns-product-filters").find("input[type=checkbox]").prop("checked",false);
        jQuery(this).closest(".mns-product-filters").find("input[type=text]").val("");
        jQuery(this).closest(".mns-product-filters").find(".filter.check").removeClass("selected").show();
        jQuery(this).closest(".mns-product-filters").find(".NotFound").hide().next().show();
        jQuery(this).closest(".mns-product-filters").find("#mnsSearch-text").val("");
        jQuery(".MensioSorting select[name=products-list-sort]").val( jQuery(this).attr("sort") ).trigger("change");
    });
    if(jQuery(".mns-block.mns-category").length>0){
        jQuery(".filter-title.resetFilters").click();
    }
    jQuery(".mns-block.mns-category input[name=search]").on('change keyup',function(){
        MensioPressFiltering(jQuery(this));
    });
    jQuery(".mns-block.mns-category select, .mns-block.mns-category input, .mns-block.mns-category input[name=search]").on('change keyup click',function(){
        MensioPressFiltering(jQuery(this));
    });
    if(jQuery(".mns-block.mns-category").length>0){
        jQuery(".mns-block .MensioOpenFilters").click(function(){
            jQuery(".mns-product-filters:not(.mns-filters-closed) .MensioOpenFilters-wrapper .resetFilters").click();
        });
    }
                jQuery(".mns-category-products.mns-list .Pagination li:first").click(function(){
                    jQuery(this).closest(".mns-category-products").attr("page",parseInt(
                        jQuery(this).closest(".mns-category-products").attr("page")
                            )-1);
                    jQuery(this).closest(".mns-block").find("select[name=products-list-sort]").trigger("change");                    
                });
                jQuery(".mns-category-products.mns-list .Pagination li:last").click(function(){
                    jQuery(this).closest(".mns-category-products").attr("page",parseInt(
                        jQuery(this).closest(".mns-category-products").attr("page")
                            )+1);
                    jQuery(this).closest(".mns-block").find("select[name=products-list-sort]").trigger("change");                    
                });
                jQuery(".mns-category-products.mns-list .Pagination li:not(:last):not(:first)").click(function(){
                    jQuery(this).closest(".mns-category-products").attr("page",parseInt(jQuery(this).html()))
                    jQuery(this).closest(".mns-block").find("select[name=products-list-sort]").trigger("change");
                });
                jQuery(".ItemShow").click(function(){
                    jQuery(this).closest(".mns-category-products").attr("items",parseInt(jQuery(this).html()))
                    jQuery(this).closest(".mns-block").find("select[name=products-list-sort]").trigger("change");
                });
    if(jQuery( ".price-range" ).length>0){
        var MinPrice=parseInt(jQuery(".price-range").attr("minprice"));
        var MaxPrice=parseInt(jQuery(".price-range").attr("maxprice"));
        jQuery(".price-range").ionRangeSlider({
            type: "double",
            grid: false,
            force_edges:true,
            min: MinPrice,
            max: MaxPrice,
            prefix: "<span class='mensioPrice'>",
            postfix: "</span>",
            onFinish: function (data) {
                jQuery("input[name=price-min]").val( data.from );
                jQuery("input[name=price-max]").val( data.to );
                jQuery("input[name=price-min]").change();
            },
            onChange: function (data) {
                jQuery("span.irs").addClass("changed");
                if((data.min==data.from) && (data.max==data.to)){
                    jQuery("span.irs").removeClass("changed");
                }
            },
        });
    }
    if(jQuery("#price-max").length){
        document.getElementById("price-max").addEventListener("input", function() {
            document.getElementById("max-price-to-show").innerHTML= "Max Price: "+document.getElementById("price-max").value+" EUR";
        }, false);
    }
    jQuery("#mnsFilterClearButton").click(function(){
        var filters_page=window.location.href;
        filters_page=filters_page.split('&filter')[0];
        jQuery("div.mns-html-content .mns-product-filters .filter input").each(function(){
            jQuery(this).prop('checked',false);
        });
        window.location.href=filters_page;
    });
    jQuery('.mns-block.mns-search #mnsSearch-button').click(function(){
        if(jQuery(this).closest("form").find("input[type=text]").val()==""){
            MensioPressAlert("Search is empty");
            return false;
        }
    });
    jQuery(".mns-block.mns-search.mns-ajax #mnsSearch-text").on('keypress click',function(e){
        if(e.which == 13) {
        }
        else if( jQuery(this).attr("id")!="searchButton" ){
            return true;
        }
        if(jQuery("#mnsSearch-text").val()==false){
            jQuery("#mnsSearch-error").html("Please fill in the form");
            return false;
        }
        jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            data: {
              'action': 'mensiopress_Search',
              'mns_sec':$sec,'mns_lang': jQuery("#MensioPressLang").val(),
              'mns_search': jQuery("#mnsSearch-text").val()
            },
            success:function(data) {
                if(data=="0"){
                    jQuery("#mnsSearch-error").html("Nothing found!");
                }
                else{
                    jQuery("#mnsSearchResultsBox").css("display","block");
                    jQuery("#mnsSearchResults").html(data);
                }
                MensioAddToCart();
            },
            error: function(errorThrown){
            }
        }); 
    });
    jQuery(".mns-block.mns-category #mnsSearch-text").keypress(function(e){
        var this_button=jQuery(this);
        var atrs=Array();
        jQuery(".filter input[name=filter]:checked").each(function(){
            atrs.push(jQuery(this).val());
        });
        var brands=Array();
        jQuery(".filter input[name=brand]:checked").each(function(){
            brands.push(jQuery(this).val());
        });
        var price_max=jQuery("#price-max").val();
        this_button.parent().parent().parent().find(".mns-category-products.mns-list").html("<img src='https://upload.wikimedia.org/wikipedia/commons/b/b1/Loading_icon.gif' width='100%'>");
        jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            data: {
              'action': 'mensiopress_CategorySearch',
              'mns_sec':$sec,'mns_lang': jQuery("#MensioPressLang").val(),
              'mns_search': jQuery("#mnsSearch-text").val(),
              'mns_brands':brands,
              'mns_atrs':atrs,
              'mns_max_price':price_max
            },
            success:function(data) {
                this_button.parent().parent().parent().find(".mns-category-products.mns-list").html(data);
                MensioAddToCart();
            },
            error: function(errorThrown){
                this_button.parent().parent().parent().find(".mns-category-products.mns-list").html("Error on search");
            }
        });
    });
    jQuery("#closeResults").click(function(){
        jQuery("#mnsSearchResultsBox").css("display","none");
    });
    jQuery(".mns-block.mns-user .user-data legend > div").click(function(){
        jQuery("div.mns-html-content .user-data legend div").removeClass("current-tab");
        var tab=jQuery(this).attr("class");
        jQuery(".field,.field-label").hide();
        jQuery(".field."+tab+",.field-label."+tab).not('.replies.field').show();
        jQuery(this).addClass("current-tab");
    });
    jQuery(".mns-block.mns-user .resend-verification").click(function(){
        jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            data: {
              'action': 'mensiopress_ResendVerification',
              'mns_sec':$sec
            },
            success:function(data) {
                MensioMessage("Verification Email succesfully sent");
            }
        });
    });
    jQuery(".field.tickets.is-Row.replies.newReply input[type=button]").click(function(){
        var TicketID=jQuery(this).parent().attr("class").replace("field tickets is-Row newReply replies ticket-","");
        var TicketReply=jQuery(".ticket-"+TicketID+" textarea").val();
        var TicketTitle=jQuery(".ticketLabel#"+TicketID+" .given").html();
        jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            data: {
              'action': 'mensiopress_PostNewTicketReply',
              'mns_sec':$sec,'mns_lang': jQuery("#MensioPressLang").val(),
              'mns_TicketID': TicketID,
              'mns_TicketReply': TicketReply,
              'mns_TicketTitle': TicketTitle
            },
            success:function(data) {
                var obj=JSON.parse(data);
                MensioMessage(obj.Message);
                jQuery("#postNewTicket").parent().find("textarea,input[type=text]").val("");
                jQuery(".ticket-"+TicketID+" textarea").val("").parent().before('<div class="field tickets replies"><div>'+Firstname+' '+Lastname+'</div><div>&nbsp;('+obj.date+')</div></div>'+
                        '<div class="field tickets is-Row replies" style="display: block;">'+obj.Reply+'</div>');
            }
        });
    });
    jQuery(".mns-block.mns-user .mns-update-user").click(function(){
        var Language=false;
        if(jQuery(this).length>0){
            Language=jQuery(this).attr("language");
        }
        jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            data: {
              'action': 'mensiopress_UpdateUser',
              'mns_sec':$sec,'mns_lang': jQuery("#MensioPressLang").val(),
              'mns_address_type': jQuery(".new-address select[name=mns-address_type]").val(),
              'mns_country': jQuery(".new-address select[name=mns-country]").val(),
              'mns_region': jQuery(".new-address select[name=mns-region]").val(),
              'mns_city': jQuery(".new-address input[name=mns-city]").val(),
              'mns_NewAddress': jQuery(".new-address input[name=mns-new-address]").val(),
              'mns_zip_code': jQuery(".new-address input[name=mns-zipcode]").val(),
              'mns_phone': jQuery(".new-address input[name=mns-phone]").val(),
              'Language':Language,
              'mns_newContactType': jQuery(".new-contacts select[name=mns-contactType]").val(),
              'mns_newContactValue': jQuery(".new-contacts input[name=mns-contactValue]").val()
            },
            success:function(data) {
               if(data=="aye"){
                   window.location.href='';
               }
            },
            error: function(errorThrown,data){
                MensioMessage("not posted"+errorThrown+data);
            }
        });
    });
    jQuery(".mns-block.mns-user .field.contacts .deleteContact").click(function(){
        var ID=jQuery(this);
        jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            data: {
              'action': 'mensiopress_DeleteUserContact',
              'mns_user':$sec,
              'ContactID':ID.attr("id")
            },
            success:function(data) {
                ID.parent().parent().remove();
            }
      });
    });
    jQuery(".mns-block.mns-user input[name=submitEdit]").click(function(){
        var ThisParent=jQuery(this).parent();
        jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            data: {
              'action': 'mensiopress_UpdateAddress',
              'mns_sec':$sec,'mns_lang': jQuery("#MensioPressLang").val(),
              'address':ThisParent.find("input[name=editAdress]").val(),
              'Type':ThisParent.find("select[name=updType]").val(),
              'FullName':ThisParent.find("input[name=updFullname]").val(),
              'Country':ThisParent.find("select[name=mns-country]").val(),
              'Region':ThisParent.find("select[name=mns-region]").val(),
              'City':ThisParent.find("input[name=updCity]").val(),
              'Street':ThisParent.find("input[name=updStreet]").val(),
              'ZipCode':ThisParent.find("input[name=updZipCode]").val(),
              'Phone':ThisParent.find("input[name=updPhone]").val()
            },
            success:function(data) {
                ThisParent.find("select[name=updType]").parent().find("span").html(ThisParent.find("select[name=updType] option:selected").html());
                ThisParent.find("input[name=updType]").parent().find("span").html(ThisParent.find("input[name=updType]").val());
                ThisParent.find("input[name=updFullname]").parent().find("span").html(ThisParent.find("input[name=updFullname]").val());
                ThisParent.find("select[name=mns-country]").parent().find("span").html(ThisParent.find("select[name=mns-country] option:selected").html());
                ThisParent.find("select[name=mns-region]").parent().find("span").html(ThisParent.find("select[name=mns-region] option:selected").html());
                ThisParent.find("input[name=updCity]").parent().find("span").html(ThisParent.find("input[name=updCity]").val());
                ThisParent.find("input[name=updStreet]").parent().find("span").html(ThisParent.find("input[name=updStreet]").val());
                ThisParent.find("input[name=updZipCode]").parent().find("span").html(ThisParent.find("input[name=updZipCode]").val());
                ThisParent.find("input[name=updPhone]").parent().find("span").html(ThisParent.find("input[name=updPhone]").val());
                ThisParent.find("input,select").hide();
                ThisParent.find("span").show();
            }
        });
    });
    if(jQuery(".mns-block.mns-user").length>0){
        jQuery(".mns-block.mns-user .user-data input[name=MensioPressOrdersFrom],\n\
                .mns-block.mns-user .user-data input[name=MensioPressOrdersTo]").datepicker({dateFormat: 'yy-mm-dd'}).change(function(){
    jQuery(".mns-block.mns-user .loadMoreHistory").attr("loadings","5");
            jQuery(".mns-block.mns-user fieldset > .HistoryOrders")
                    .html("");
            jQuery(".mns-block.mns-user .loadMoreHistory").click();
        });
    }
    jQuery(".mns-block.mns-user .loadMoreHistory").click(function(){
        var DateFrom=jQuery(".mns-block.mns-user .user-data input[name=MensioPressOrdersFrom]").val();
        var DateTo=jQuery(".mns-block.mns-user .user-data input[name=MensioPressOrdersTo]").val();
        var from=jQuery(this).attr("loadings");
        jQuery(this).attr("loadings",Number(from)+5);
        jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            data: {
              'action': 'mensiopress_LoadMoreHistory',
              'mns_sec':$sec,'mns_lang': jQuery("#MensioPressLang").val(),
              'from': from,
              'DateFrom': DateFrom,
              'DateTo': DateTo,
            },
            success:function(data) {
                jQuery(".mns-block.mns-user fieldset > .HistoryOrders")
                        .accordion("destroy")
                        .append(data)
                        .accordion({
                            autoheight:false,
                            heightStyle: "content",
                            collapsible: true,
                            active: false
                        });
                return false;
            }
        });
    });
    if(jQuery(".mns-block.mns-user  .date-range").length>0){
        jQuery(".mns-block.mns-user .date-range").ionRangeSlider({
            type:'double',
            min: +moment().subtract( jQuery(".mns-block.mns-user .date-range").attr("mindate")  , "days").format("X"),
            max: +moment().format("X"),
            from: +moment().subtract(jQuery(".mns-block.mns-user .date-range").attr("mindate"), "days").format("X"),
            to: +moment().format("X"),
            grid: true,
            force_edges: true,
            prettify: function (num) {
                var m = moment(num, "X").locale("ru");
                return m.format("DD-MM-YYYY");
            },
            onChange: function(){
                jQuery("span.irs").addClass("changed");
            },
            onFinish: function (data) {
                jQuery("input[name=MensioPressOrdersFrom]").val(data.from_pretty);
                jQuery("input[name=MensioPressOrdersTo]").val(data.to_pretty);
                jQuery(".mns-block.mns-user fieldset > .HistoryOrders")
                        .accordion("destroy")
                        .html("<i class='fa fa-spinner'></i>");
                    jQuery.ajax({
                        type: 'post',
                        url: ajaxurl,
                        data: {
                          'action': 'mensiopress_LoadMoreHistory',
                          'mns_sec':$sec,'mns_lang': jQuery("#MensioPressLang").val(),
                          'from': 0,
                          'DateFrom': jQuery("input[name=MensioPressOrdersFrom]").val(),
                          'DateTo': jQuery("input[name=MensioPressOrdersTo]").val(),
                        },
                        success:function(data) {
                                jQuery(".mns-block.mns-user fieldset > .HistoryOrders")
                                        .html(data)
                                        .accordion({
                                            autoheight:false,
                                            heightStyle: "content",
                                            collapsible: true,
                                            active: false
                                        });
                                jQuery(".mns-block.mns-user fieldset > .HistoryOrders .MensioHistoryOrder").show();
                            return false;
                        }
                    });
            }
        });
    }
    jQuery(".mns-block.mns-user .addresses .deleteAddress").click(function(){
        var conf=confirm("Are you sure you want to delete your address?");
        var This=jQuery(this);
        if(conf==true){
                jQuery.ajax({
                    type: 'post',
                    url: ajaxurl,
                    data: {
                      'action': 'mensiopress_DeleteAddress',
                      'mns_sec':$sec,'mns_lang': jQuery("#MensioPressLang").val(),
                      'Address': This.attr("id").replace("rem-","")
                    },
                    success:function(data) {
                        This.parent().next().remove();
                        This.parent().remove();
                    }
                });
        }
        else{
            return false;
        }
    });
    jQuery(".mns-block.mns-user .addresses .editAddress").click(function(){
        if(!jQuery(this).closest('.ui-accordion-header').hasClass("ui-accordion-header-active")){
            jQuery(this).closest('.ui-accordion-header').click();
        }
        if(!jQuery(this).hasClass("editing")){
            jQuery(this).addClass("editing");
            jQuery(this).closest(".ui-accordion-header").next().find("span").hide();
            jQuery(this).closest(".ui-accordion-header").next().find("input,select").show();
        }
        else{
            jQuery(this).removeClass("editing");
            jQuery(this).closest(".ui-accordion-header").next().find("span").show();
            jQuery(this).closest(".ui-accordion-header").next().find("input,select").hide();
        }
        return false;
    });
    jQuery(".mensio-checkout-tab .next-tab,.mensio-checkout-tab .prev-tab").click(function(){
        var thisButton=jQuery(this);
        if(thisButton.parent().parent().hasClass('mensio-checkout-1')){
            if(jQuery("body").hasClass("mensio-is-logged-in")==true){
                if(jQuery(".mensio-checkout-1 input[name=address]:checked").length==0){
                    return false;
                }
            }
            if(jQuery("body").hasClass("mensio-is-not-logged-in")==true){
                if(jQuery(".mensio-checkout-1 input[name=mns-fullname]").val()==false || jQuery(".mensio-checkout-1 input[name=mns-fullname]").val()==""){
                    jQuery(".mensio-checkout-1 input[name=mns-fullname]").addClass("falseInput");
                }
                else{
                    jQuery(".mensio-checkout-1 input[name=mns-fullname]").removeClass("falseInput");
                }
                if(jQuery(".mensio-checkout-1 input[name=mns-email]").val()==false || jQuery(".mensio-checkout-1 input[name=mns-email]").val()==""){
                    jQuery(".mensio-checkout-1 input[name=mns-email]").addClass("falseInput");
                }
                else{
                    jQuery(".mensio-checkout-1 input[name=mns-email]").removeClass("falseInput");
                }
                if(jQuery(".mensio-checkout-1 select[name=mns-country]").val()==false || jQuery(".mensio-checkout-1 select[name=mns-country]").val()==""){
                    jQuery(".mensio-checkout-1 select[name=mns-country]").addClass("falseInput");
                }
                else{
                    jQuery(".mensio-checkout-1 select[name=mns-country]").removeClass("falseInput");
                }
                if(jQuery(".mensio-checkout-1 select[name=mns-region]").val()==false || jQuery(".mensio-checkout-1 select[name=mns-region]").val()==""){
                    jQuery(".mensio-checkout-1 select[name=mns-region]").addClass("falseInput");
                }
                else{
                    jQuery(".mensio-checkout-1 select[name=mns-region]").removeClass("falseInput");
                }
                if(jQuery(".mensio-checkout-1 input[name=mns-city]").val()==false || jQuery(".mensio-checkout-1 input[name=mns-city]").val()==""){
                    jQuery(".mensio-checkout-1 input[name=mns-city]").addClass("falseInput");
                }
                else{
                    jQuery(".mensio-checkout-1 input[name=mns-city]").removeClass("falseInput");
                }
                if(jQuery(".mensio-checkout-1 input[name=mns-address]").val()==false || jQuery(".mensio-checkout-1 input[name=mns-address]").val()==""){
                    jQuery(".mensio-checkout-1 input[name=mns-address]").addClass("falseInput");
                }
                else{
                    jQuery(".mensio-checkout-1 input[name=mns-address]").removeClass("falseInput");
                }
                if(jQuery(".mensio-checkout-1 input[name=mns-zipcode]").val()==false || jQuery(".mensio-checkout-1 input[name=mns-zipcode]").val()==""){
                    jQuery(".mensio-checkout-1 input[name=mns-zipcode]").addClass("falseInput");
                }
                else{
                    jQuery(".mensio-checkout-1 input[name=mns-zipcode]").removeClass("falseInput");
                }
                if(jQuery(".mensio-checkout-1 input[name=mns-phone]").val()==false || jQuery(".mensio-checkout-1 input[name=mns-phone]").val()==""){
                    jQuery(".mensio-checkout-1 input[name=mns-phone]").addClass("falseInput");
                }
                else{
                    jQuery(".mensio-checkout-1 input[name=mns-phone]").removeClass("falseInput");
                }
                if(jQuery(".mensio-checkout-1 input[name=mns-fullname]").val()==false ||
                   jQuery(".mensio-checkout-1 input[name=mns-email]").val()==false ||
                   jQuery(".mensio-checkout-1 select[name=mns-country]").val()==false ||
                   jQuery(".mensio-checkout-1 select[name=mns-region]").val()==false ||
                   jQuery(".mensio-checkout-1 input[name=mns-city]").val()==false ||
                   jQuery(".mensio-checkout-1 input[name=mns-address]").val()==false ||
                   jQuery(".mensio-checkout-1 input[name=mns-zipcode]").val()==false ||
                   jQuery(".mensio-checkout-1 input[name=mns-phone]").val()==false){
                }
            }
        }
        if(thisButton.parent().parent().hasClass('mensio-checkout-2') && thisButton.hasClass('next-tab')==true){
            if(jQuery(".mensio-checkout-2 .mns-ShippingChoose input[type=radio]:checked").length==0){
                return false;
            }
        }
        if(thisButton.parent().parent().hasClass('mensio-checkout-3') && thisButton.hasClass('next-tab')==true){
            if(jQuery(".mensio-checkout-3 #mensio-agree-to-terms").prop('checked')==false){
                return false;
            }
        }
        thisButton.parent().parent().animate({marginLeft:"-200px",marginRight:"200px",opacity:0},500,function(){
            thisButton.parent().parent().hide().css("margin","0").css("opacity","1");
            if(thisButton.hasClass("next-tab")){
                thisButton.parent().parent().next().css("margin-left","-200px").css("margin-right","200px").show().animate({marginLeft:"0px",marginRight:"0px",opacity:1},500)
                jQuery(".current-step").removeClass("current-step").next().addClass("current-step");
            }
            if(thisButton.hasClass("prev-tab")){
                thisButton.parent().parent().prev().css("margin-left","-200px").css("margin-right","200px").show().animate({marginLeft:"0px",marginRight:"0px",opacity:1},500)
                jQuery(".current-step").removeClass("current-step").prev().addClass("current-step");
            }
        });
    });
    jQuery(".mensio-slide > label > input[type=radio]").change(function(){
        jQuery(this).parent().parent().parent().find(".mensio-slide-details").animate({height:"0"},500);
        jQuery(this).parent().parent().find(".mensio-slide-details").animate({height:"100%"},500);
    });
    jQuery(".mensio-checkout-3 #mensio-agree-to-terms").change(function(){
        if(jQuery(this).prop("checked")==true){
            jQuery(".mensio-checkout-3 .next-tab").prop("disabled",false);
        }
        else{
            jQuery(".mensio-checkout-3 .next-tab").prop("disabled",true);
        }
    });
    jQuery("body.mensio-is-logged-in .mns-checkout .ChooseBillingAddress .billing-address").click(function(){
        return false;
    });
    jQuery("body.mensio-is-not-logged-in .mns-checkout select[name=mns-country],\n\
                body.mensio-is-logged-in .mns-checkout .ChooseAddress .address,\n\
                body.mensio-is-logged-in .mns-checkout .ChooseShippingAddress .address")
                .change(function(){
        var country=jQuery(this).val();
        if(jQuery("body").hasClass("mensio-is-logged-in")==true){
            country=jQuery(this).attr("country");
        }
        if(jQuery(this).attr("name")=="mns-country"){
            country=jQuery(this).find("option:selected").attr('data-default');
        }
        jQuery(".mensio-checkout-1 .next-tab").prop("disabled",false);
        jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            data: {
              'action': 'mensiopress_CountryShippingMethods',
              'mns_sec':$sec,'mns_lang': jQuery("#MensioPressLang").val(),
              'mns_country': country
            },
            success:function(data) {
                var obj=jQuery.parseJSON(data);
                jQuery(".mns-block.mns-checkout .mensio-checkout-tab.mensio-checkout-2").show();
                jQuery(".mns-ShippingMethods").css("opacity","1").show();
                if(jQuery.isEmptyObject(obj)){
                    jQuery(".mns-checkout .mns-noShipping").show();
                    jQuery(".mns-checkout .mns-ShippingCompanies,.mns-checkout .mns-ShippingMethods-finalrow.mns-FinalCost").hide();
                }
                else{
                    jQuery(".mns-checkout .mns-ShippingCompanies").show();
                    jQuery(".mns-ShippingMethods .mns-ShippingCompanies").html("");
                    jQuery(".mensio-checkout-tab.mensio-checkout-2").show();
                    jQuery(".mns-checkout .mns-noShipping, .mns-FinalCost").hide();
                    var objs=obj.length;
                    var i=1;
                    jQuery.each( obj, function( i, val ) {
                        jQuery(".mns-checkout .mns-ShippingMethods .mns-ShippingCompanies").append("<label class='header'><input type='radio' name='choose' class='shipping-company' cost='"+val.price+"' value='"+val.ID+"'>"+val.name+"</label><div class='shippingCompany'><div>"+val.speed+" <span class='mensioPrice'>"+val.price+"</span><br /></div>   </div>");
                        if(i==2){
                        }
                        i++;
                    });
                    if(jQuery(".mns-checkout .mns-ShippingMethods .mns-ShippingCompanies").hasClass("ui-accordion")){
                        jQuery(".mns-checkout .mns-ShippingMethods .mns-ShippingCompanies").accordion("destroy");
                    }
                    jQuery(".mns-checkout .mns-ShippingMethods .mns-ShippingCompanies").accordion({
                        autoheight:true,
                        heightStyle: "content",
                        collapsible: true,
                        active: false
                    });
                    jQuery(".mns-checkout .mns-ShippingMethods .mns-ShippingCompanies .header").click(function(){
                        jQuery(this).find("input[type=radio]").prop("checked",true).change();
                    });
                }
                chooseSHippingCompany();
                    jQuery(".mns-block.mns-checkout input[type=radio]").click(function(){
                        return false;
                    });            }
        });
    });
    if(jQuery(".mns-block.mns-checkout").length>0 && jQuery(".mns-block.mns-checkout .mensio-checkout-tab").length>0){
        jQuery("#choosePayment,.ChooseAddress,.ChooseBillingAddress,.ChooseShippingAddress")
                .accordion({ collapsible: true, heightStyle: "content",active: false });
    }
    if(jQuery(".mns-block.mns-user").length>0){
        jQuery(".mns-block.mns-user fieldset > .addresses").accordion({ collapsible: true,heightStyle: "content", active: false });
        jQuery(".mns-block.mns-user fieldset > .history").accordion({ collapsible: true, heightStyle: "content",active: false });
        jQuery(".mns-block.mns-user fieldset > .Tickets").accordion({ collapsible: true,heightStyle: "content",active: false });
    }
    jQuery(".ChooseAddress .ui-accordion-header,.ChooseBillingAddress .ui-accordion-header,.ChooseShippingAddress .ui-accordion-header").click(function(){
        jQuery(this).find("input[type=radio]").prop("checked",true).change();
    });
    jQuery("#choosePayment .ui-accordion-header").click(function(){
        jQuery(this).find("input[type=radio]").prop("checked",true);
        jQuery(this).next().find("input[type=radio]").prop("checked",true);
    });
    jQuery(".mns-block.mns-checkout .payment-method").click(function(){
        var thisID=jQuery(this).attr("id");
        jQuery(".mns-block.mns-checkout .payment-method").removeClass("current-payment-method");
        jQuery(this).addClass("current-payment-method");
        jQuery(".payment-method-panel").animate({height:"0px"},500,function(){
            jQuery(this).hide();
            jQuery("#pay-with-"+thisID).show().animate({height:"450px"},500);
        });
    });
    jQuery(".mns-block .mensio-checkout-4 .MensioPayButton").click(function(){
        var PayMethod=jQuery(this).attr('id').replace("pay-with-","");
        var ThisParent=jQuery(this).parent();
        if(jQuery(this).closest(".PayMethod").hasClass("MensioPayWithVivaWallet")){
            ThisParent.append("<div class='checkout-Window' id='MensioPayWithVivaWallet'><div></div></div>");
            jQuery("#MensioPayWithVivaWallet > div").load("?PayWith=VivaWallet");
            return false;
        }
        if(ThisParent.hasClass("MensioPayWithPayPal")){
            ThisParent.append("<div class='checkout-Window' id='MensioPayWithPayPal'><iframe src='?PayWith=PayPal' class='checkout-form'></iframe></div>");
            return false;
        }
        var empty=0;
        jQuery(this).closest(".mns-block").find(".mensio-checkout-1 input").each(function(){
            if(jQuery(this).val()==false){
                empty=1;
            }
        });
        if(empty==true || jQuery(this).closest(".mns-block").find(".mensio-checkout-2 input:checked").length==0){
            MensioMessage("Some fields are empty");
            return false;
        }
        jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            data: {
              'action': 'mensiopress_CheckingOut',
              'mns_sec':$sec,'mns_lang': jQuery("#MensioPressLang").val(),
              'FullName': jQuery("input[name=mns-fullname]").val(),
              'email': jQuery("input[name=mns-email]").val(),
              'country': jQuery("select[name=mns-country]").val(),
              'region': jQuery("select[name=mns-region]").val(),
              'city': jQuery("input[name=mns-city]").val(),
              'street': jQuery("input[name=mns-address]").val(),
              'zipcode': jQuery("input[name=mns-zipcode]").val(),
              'phone': jQuery("input[name=mns-phone]").val(),
              'address': jQuery("input[name=shipping-address]").val(),
              'billingaddress': jQuery("input[name=billing-address]").val(),
              'ShippingCompany': jQuery(".mns-ShippingMethods input[name=choose]:checked").val(),
              'payMethod': PayMethod
            },
            success:function(data) {
                if(data=='Sorry'){
                    MensioMessage(data);
                }
                else if(data==false){
                    window.location.href='';
                }
                else{
                    if(ThisParent.closest(".PayMethod").hasClass("MensioPayWithPayPal")){
                        window.location.href=ThisParent.closest(".PayMethod").attr("link");
                        return true;
                    }
                    var obj = JSON.parse(data);
                    if(obj.orderNo!=""){
                        if(ThisParent.hasClass("Deposit") || ThisParent.hasClass("Delivery")){
                            window.location.href='';
                        }
                        else if(ThisParent.hasClass("Gateway")){
                            jQuery("input[name=orderid]").val(obj.orderNo);
                            jQuery("input[name=orderAmount]").val(obj.GrandTotal);
                            jQuery("input[name=digest]").val(obj.Digest);
                            ThisParent.find("form").submit();
                        }
                        else{
                            window.location.href='';
                        }
                    }
                }
            }
        });
    });
    jQuery("#postNewTicket").click(function(){
        var TicketText=jQuery(this).parent().find('textarea').val();
        var TicketTitle=jQuery(this).parent().find('input[name=title]').val();
        jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            data: {
              'action': 'mensiopress_NewTicket',
              'mns_sec':$sec,'mns_lang': jQuery("#MensioPressLang").val(),
              'TicketText': TicketText,
              'ticketTitle': TicketTitle
            },
            success:function(data) {
                var obj=JSON.parse(data);
                MensioMessage(obj.Message);
                jQuery("fieldset.user-data div.Tickets")
                        .accordion("destroy")
                        .append("<div class='field tickets ticketLabel'><div class='given'>"+TicketTitle+"</div></div>\n\
                        <div class='ticketContent'><div class='field tickets replies ticket-"+obj.TicketID+"'>\n\
                            <div>"+obj.FullName+"</div>\n\
                            <div>"+obj.Date+"</div>\n\
                        </div>\n\
                        <div class='field tickets is-Row replies ticket-"+obj.TicketID+"'>"+TicketText+"</div>\n\
                        <div class='field tickets is-Row newReply replies ticket-"+obj.TicketID+" form-group'><textarea class='form-control'></textarea><hr><input type='button' value='Post'></div>\n\
                    </div>")
                        .accordion({
                            autoheight:false,
                            heightStyle: "content",
                            collapsible: true,
                            active: false
                        });
            }
        });
    });
    jQuery("#AddNewCompanyUser").click(function(){
        var companyEmail=jQuery(this).parent().find('input[name=companyEmail]').val();
        var companyFirstname=jQuery(this).parent().find('input[name=companyFirstname]').val();
        var companyLastname=jQuery(this).parent().find('input[name=companyLastname]').val();
        var companyTitle=jQuery(this).parent().find('input[name=companyTitle]').val();
        var companyPassword=jQuery(this).parent().find('input[name=companyPassword]').val();
        jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            data: {
              'action': 'mensiopress_NewCompanyUser',
              'mns_sec':$sec,'mns_lang': jQuery("#MensioPressLang").val(),
              'Email': companyEmail,
              'Title': companyTitle,
              'Password': companyPassword,
              'Firstname': companyFirstname,
              'Lastname': companyLastname
            },
            success:function(data) {
                MensioMessage(data);
            }
        });
    });
    jQuery(".MensioRateProduct .rating-stars span").click(function(){
        jQuery(this).parent().addClass("rated").attr("value", jQuery(".MensioRateProduct .rating-stars span.good:last").index() );
    });
    jQuery(".MensioRateProduct .rating-stars span").mouseover(function(){
        var curStar=jQuery(this).index();
        var first=jQuery(this).parent().find("span:first").index();
        jQuery(".MensioRateProduct .rating-stars span").each(function(){
            if( jQuery(this).index() <= curStar){
                jQuery(this).removeClass("not-good").addClass("good");
            }
            else{
                jQuery(this).removeClass("good").addClass("not-good");
            }
        });
    });
    jQuery(".MensioRateProduct .rating-stars").mouseout(function(){
        var value=jQuery(this).attr("value");
        if(jQuery(this).hasClass("rated")){
            jQuery(".MensioRateProduct .rating-stars span").each(function(){
                if( jQuery(this).index() <= value){
                    jQuery(this).removeClass("not-good").addClass("good");
                }
                else{
                    jQuery(this).removeClass("good").addClass("not-good");
                }
            });
        }
        else{
            jQuery(".MensioRateProduct .rating-stars span").removeClass("not-good").addClass("good");
        }
    });
    jQuery(".mns-html-content .mns-ratings .MensioRateNow").click(function(){
        var button=jQuery(this);
        var gap='';
        var ratingTitle=jQuery(this).parent().parent().find(".MensioNewRatingTitle").val();
        var rating=jQuery(this).parent().parent().find(".MensioNewRating").val();
        var ratingStars=jQuery(this).parent().parent().find("input[type=range].rating-stars").val();
        var ProductID=jQuery(this).parent().attr("productid");
       jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            data: {
              'action': 'mensiopress_NewRating',
              'mns_sec':$sec,'mns_lang': jQuery("#MensioPressLang").val(),
              'mns_RatingTitle': ratingTitle,
              'mns_Rating': rating,
              'mns_RatingStars': ratingStars,
              'MensioProduct': ProductID
            },
            success:function(data) {
                if(data!='0'){
                    var arr=JSON.parse(data);
                    var stars=arr.stars;
                    var nameLastname=arr.name;
                    var Text=arr.text;
                    var Title=arr.title;
                    var divStars='';
                    for(i=1;i<=arr.stars;i++){
                        divStars=divStars+"<div class='good'></div>";
                    }
                    button.parent().before("<div class='rating'>"+gap
                                +"<div class='rater'>"+nameLastname+"</div>"+gap
                                +"<div class='rating-title'>"+Title+"</div>"+gap
                                +"<div class='rating-text'>"+Text+"</div>"+gap
                                +"<div class='rating-stars' title='Review: "+stars+"'>"+divStars+"</div>"+gap
                                +"<div class='whenPosted'>Now</div>"+gap
                                +"</div>");
                    button.parent().find("textarea").val('');
                    button.parent().find("input[type=range]").val('');
                }
            }
        });
    });
    jQuery(".searchByCat input[type=checkbox]").change(function(){
        if(jQuery(this).prop("checked")==true){
            jQuery(this).parent().next('.SearchAttributes').show();
        }
        else{
            jQuery(this).parent().next('.SearchAttributes').hide();
        jQuery(this).parent().next('.SearchAttributes').find('select option:selected').removeAttr("selected");
        }
    });
    jQuery(".mns-html-content img").error(function(){
        if(eshopLogo){
        }
    });
    jQuery(".MensioGoToParentCat").click(function(){
        window.location.href=jQuery(this).attr("href");
    });
    jQuery("#MensioLangs #MensioChangeLanguage").change(function(){
        var Link=jQuery(this).find("option:selected").attr("link")
        window.location.href=Link;
    });
    jQuery(".change-view").click(function(){
        if(jQuery(this).closest(".mns-block").hasClass("show-prods-in-rows")){
            jQuery(this).closest(".mns-block").removeClass("show-prods-in-rows");
        }
        else{
            jQuery(this).closest(".mns-block").addClass("show-prods-in-rows");
        }
        var view=false;
        if(jQuery(this).hasClass("fa-list")){
            view="list";
        }
        if(jQuery(this).hasClass("fa-th")){
            view="table";
        }
        jQuery.ajax({
             type: 'post',
             url: ajaxurl,
             data: {
               'action': 'mensiopress_SaveViewWay',
               'mns_sec':$sec,'mns_lang': jQuery("#MensioPressLang").val(),
               'view':view
             }
         });
    });
    jQuery("nav li a").click(function(){
        var dt;
        jQuery.ajax({
             type: 'post',
             url: ajaxurl,
             data: {
               'action': 'mensiopress_KillNavSessions',
               'mns_sec':$sec,'mns_lang': jQuery("#MensioPressLang").val(),
               'var1':'fe'
             }
         });
    });
    jQuery("div[class^=MensioPressWidget]")
        .attr("bodyw", jQuery("body").width() )
        .on("mouseover",function(event){
            if(jQuery(this).find(".WidgetContent").length>0){
                jQuery(this).find(".WidgetContent").mouseover(function(){
                    return false;
                });
            }
            var screen=(jQuery("body").width())/2;
            jQuery("div[class^=MensioPressWidget]").removeClass("sendRight, sendLeft, sendUp, sendDown");
            if(event.pageX < screen){
                jQuery("div[class^=MensioPressWidget]").addClass("sendRight");
            }
            else if(event.pageX > screen){
                jQuery("div[class^=MensioPressWidget]").addClass("sendLeft");
            }
            var CartH=jQuery(this).find(".WidgetContent").height();
            var bodyH=jQuery(window).height();
            var screenH=event.originalEvent.clientY;
            if((bodyH-CartH) < screenH){
                jQuery("div[class^=MensioPressWidget]").removeClass("sendDown");
                jQuery("div[class^=MensioPressWidget]").addClass("sendUp");
            }
            else{
                jQuery("div[class^=MensioPressWidget]").removeClass("sendUp");
                jQuery("div[class^=MensioPressWidget]").addClass("sendDown");
            }
    });
    jQuery(".NewTerms .userHandlers .disagree").click(function(){
        jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            data: {
              'action': 'mensiopress_logout',
              'mns_sec':$sec
            },
            success:function(data) {
                window.location.href='';
            }
        });
    });
    jQuery(".MensioPressModalBox .NewTerms .userHandlers .agree").click(function(){
       jQuery.ajax({
           type: 'post',
           url: ajaxurl,
           data: {
             'action': 'mensiopress_AgreeWithNewTOS',
              'mns_sec':$sec
           },
           success:function(data) {
               jQuery(".MensioPressModalBox").remove();
           }
       });
    });
    jQuery("table.filtersTable tr td div input[type=checkbox]").change(function(){
        if(jQuery(this).prop('checked')==true){
            jQuery(this).closest("label").parent().addClass("selected");
        }
        else{
            jQuery(this).closest("label").parent().removeClass("selected");
        }
    })
    jQuery("body.mensio-is-not-logged-in .mns-checkout select[name=mns-country][current-country]").trigger("change");
    if(jQuery(".mns-html-content .SimpleDisplay img").length>0 && jQuery("body").hasClass("wp-admin")==false){
        jQuery(".mns-html-content .SimpleDisplay .mns-image").each(function(){
            var w=jQuery(this).find("img").width();
            jQuery(this).find("a img").height(w);
            jQuery(this).find("a img").width(w);
        });
    }
});