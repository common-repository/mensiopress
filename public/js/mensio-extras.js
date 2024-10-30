
    function MensioPressShowCoupons(){
        jQuery("#mns-coupon").show().prev().show().prev().show();
    }
function MensioAddToFavorites(){
    var $sec = jQuery('#MensioPressNonce').val();
    var WidgetTitle=jQuery(".MensioPressWidgetFavoritesList .MensioWidgetTitle").html();
    var WidgetStyle=jQuery(".MensioPressWidgetFavoritesList").attr("style");
    var PageText=jQuery(".MensioPressWidgetFavoritesList .Widget-row:last .cell a").html();
    jQuery(".add-to-wish-list").click(function(){
        var prod=jQuery(this).attr("id").replace("product-","");
        jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            data: {
              'action': 'mensiopress_AddtoFavorites',
              'mns_sec':$sec,
              'mns_prod': prod
            },
            success:function(data) {
                var obj=JSON.parse(data);
                MensioMessage(obj.Message);
                jQuery(".MensioPressWidgetFavoritesList").before( obj.FavoritesPages ).remove();
                jQuery(".MensioPressWidgetFavoritesList .MensioWidgetTitle").html(WidgetTitle);
                jQuery(".MensioPressWidgetFavoritesList #MensioCartQuantities").html(obj.FavoriteProducts);
                jQuery(".MensioPressWidgetFavoritesList").attr("style",WidgetStyle);
                jQuery(".MensioPressWidgetFavoritesList .Widget-row:last .cell a").html(PageText);
            },
            error: function(errorThrown){
            }
        }); 
    });
}
function RemoveFromComparisonList(){
    var $sec = jQuery('#MensioPressNonce').val();
    jQuery(".MensioRemoveFromComparison").click(function(){
        var prod=jQuery(this).attr("id").replace("mensioRemove-","");
        jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            data: {
              'action': 'mensiopress_RemoveComparison',
              'mns_sec':$sec,
              'Prod': prod
            },
            success:function(data) {
                var obj = JSON.parse(data);
                MensioMessage(obj.Message);
                jQuery(".MensioProductCompare-"+prod).remove();
                jQuery(".MensioComparisonQuantities").html(obj.Quantities);
                if(jQuery(".mns-block.mns-product_comparison .comparisonName").length==0){
                    jQuery(".mns-block.mns-product_comparison #MensioComparison").hide();
                    jQuery(".mns-block.mns-product_comparison .NoProdsFound").show();
                }
            },
            error: function(errorThrown){
            }
        });
    });
}
function MensioAddToCompareList(){
    var $sec = jQuery('#MensioPressNonce').val();
    jQuery(".MensiocompareProduct").click(function(){
        if(jQuery(".MensioTopRightComparisonDIV .fa.fa-compress").hasClass("black")){
            var Color="black";
        }
        else{
            var Color="white";
        }
        var compQuants=jQuery(".MensioComparisonQuantities").length;
        var WidgetComparisonStyle=jQuery(".MensioPressWidgetComparisonList").attr("style");
        var WidgetComparisonTitle=jQuery(".MensioPressWidgetComparisonList .MensioWidgetTitle").html();
        var prod=jQuery(this).attr('id').replace('product-','');
        jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            data: {
              'action': 'mensiopress_AddtoComparison',
              'mns_sec':$sec,
              'mns_prod': prod
            },
            success:function(data) {
                var obj=JSON.parse(data);
                MensioMessage(obj.Message);
                var Prods=obj.Prods;
                jQuery(".MensioPressWidgetComparisonList").before(Prods).remove();
                jQuery(".MensioPressWidgetComparisonList #MensioCartQuantities").html(obj.Quantities);
                jQuery(".MensioPressWidgetComparisonList .MensioWidgetTitle").html(WidgetComparisonTitle);
                jQuery(".MensioPressWidgetComparisonList").attr("style",WidgetComparisonStyle);
                RemoveFromComparisonList();
            },
            error: function(errorThrown){
            }
        });
    });
}
jQuery("document").ready(function(){
    var $sec = jQuery('#MensioPressNonce').val();
    jQuery(".MensioProductDetails > #mensioProductFiles").click(function(){
        jQuery(".MensioProductDetails > div").css("background","#ececec");
        jQuery(this).css("background","white");
        jQuery(".product-view-text").css("display","none");
        jQuery(".mnsBundleProducts").css("display","none");
        jQuery(".mnsAttributes").css("display","none");
        jQuery(".mensioProductFiles").css("display","block");
    });
    jQuery(".MensioProductDetails > #mensioBundleProducts").click(function(){
        jQuery(".MensioProductDetails > div").css("background","#ececec");
        jQuery(this).css("background","#ffffff");
        jQuery(".product-view-text").css("display","none");
        jQuery(".mnsAttributes").css("display","none");
        jQuery(".mnsBundleProducts").css("display","block");
        jQuery(".mensioProductFiles").css("display","none");
    });
    MensioAddToFavorites();
    jQuery(".mns-block.mns-favorites .remove-from-favorites").click(function(){
        var buttonIndex=jQuery(".mns-block.mns-cart .remove-from-favorites").index(this);
        var ProdID=jQuery(this).attr("id");
        var CartRow=jQuery(this).parent().parent();
        jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            data: {
              'action': 'mensiopress_RemoveFromFavorites',
              'mns_sec':$sec,
              'mnsProdRemove': ProdID
            },
            success:function(data){
                CartRow.remove();
                var obj=JSON.parse(data);
                MensioMessage(obj.Message);
                jQuery(".mns-list.Widget").html( obj.FavoritesPages );
                jQuery(".MensioPressTopRightFavorites").find("#MensioPressFavoritesQuantity")
                        .html( obj.FavoriteProducts );
                if(jQuery(".mns-block.mns-favorites .favorite-item").length==0){
                    jQuery(".mns-block.mns-favorites .noprodsfound").show();
                }
            },
            error: function(errorThrown){
            }
        });
    });
    jQuery("#pay-with-credit_card_button").click(function(){
        jQuery("#pay-with-credit_card").css("display","block");
    });
    jQuery(".mns-block.mns-advanced_search select, .mns-block.mns-advanced_search input, .mns-block.mns-advanced_search input[name=search]").on('change keyup click',mnsStartFromLastKeyPress(function(){
        var attributes=new Array();
        var filters=new Array();
        var categories=new Array();
        var brands=new Array();
        var ProductFilters=jQuery(this).closest(".mns-product-filters");
        ProductFilters.css("opacity","0.5");
        jQuery(".mns-advanced_search-products.mns-list").css("opacity","0.3");
        if(jQuery(this).attr("type")=="checkbox"){
        }
        if(jQuery(".mns-advanced_search-products.mns-list").attr("page") < 0){
            jQuery(".mns-advanced_search-products.mns-list").attr("page","0");
            return false;
        }
        jQuery(".mns-block.mns-advanced_search").find(".filter.check input[type=checkbox]")
                .parent().parent().removeClass("selected");
        jQuery(".mns-block.mns-advanced_search").find(".filter.check:not(.brand-sel) input[type=checkbox]:checked")
            .each(function(){
            if(jQuery(this).val()){
                jQuery(this).parent().parent().addClass("selected");
                attributes.push( jQuery(this).val() );
            }
        });
        jQuery(".mns-block.mns-advanced_search").find(".filter.check:not(.brand-sel) input[type=checkbox]:checked")
            .each(function(){
            if(jQuery(this).val()){
                jQuery(this).parent().parent().addClass("selected");
                filters.push( jQuery(this).parent().parent().attr("filter") );
            }
        });
        jQuery(".mns-block.mns-advanced_search").find(".filter.brand-sel input[type=checkbox]:checked").each(function(){
            if(jQuery(this).val()){
                jQuery(this).parent().parent().addClass("selected");
                brands.push( jQuery(this).val() );
            }
        });
        jQuery(".mns-block.mns-advanced_search").find(".filter.cat-sel input[type=checkbox]:checked").each(function(){
            if(jQuery(this).val()){
                jQuery(this).parent().parent().addClass("selected");
                categories.push( jQuery(this).val() );
            }
        });
        var mnsAtts=jQuery(".mns-block.mns-advanced_search .mns-advanced_search-products.mns-list").attr("atts");
        var ResultPage="";
        if(!jQuery(this).closest(".mns-block").find(".mns-advanced_search-products.mns-list").attr("page")){
            jQuery(this).closest(".mns-block").find(".mns-advanced_search-products.mns-list").attr("page","1");
            ResultPage=1;
        }
        else{
            ResultPage=jQuery(this).closest(".mns-block").find(".mns-advanced_search-products.mns-list").attr("page");
        }
        var MinPrice=jQuery(this).closest(".mns-block").find(".pricesrange input[name=price-min]").val();
        var MaxPrice=jQuery(this).closest(".mns-block").find(".pricesrange input[name=price-max]").val();
        var OrderVal=jQuery("select[name=products-list-sort]").val();
        var Page=jQuery(this).closest(".mns-block").find(".mns-list").attr("page");
        var Items=jQuery(this).closest(".mns-block").find(".mns-list").attr("items");
        var Object=jQuery(this);
        jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            data: {
              'action': 'mensiopress_FilterSearch',
              'mns_sec':$sec,
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
              'Page':Page
            },
            success:function(data) {
                ProductFilters.css("opacity","1");
                jQuery(".mns-advanced_search-products.mns-list").html(data);
                jQuery(".mns-block.mns-advanced_search .next-page-result").click(function(){
                    jQuery(".mns-advanced_search-products.mns-list").attr("page",(parseInt(jQuery(".mns-advanced_search-products.mns-list").attr("page"))+1));
                    jQuery(".mns-block.mns-advanced_search input[name=search]").click();
                });
                jQuery(".mns-block.mns-advanced_search .prev-page-result").click(function(){
                    if(jQuery(".mns-advanced_search-products.mns-list").attr("page")!="0"){
                        jQuery(".mns-advanced_search-products.mns-list").attr("page",(parseInt(jQuery(".mns-advanced_search-products.mns-list").attr("page"))-1));
                        jQuery(".mns-block.mns-advanced_search input[name=search]").click();
                    }
                });
                jQuery(".ItemShow").click(function(){
                    jQuery(this).closest(".mns-advanced_search-products").attr("items",parseInt(jQuery(this).html()))
                    jQuery(this).closest(".mns-block").find("select[name=products-list-sort]").trigger("change");
                });
                var AllPages=parseInt(jQuery(".mns-advanced_search-products.mns-list .Pagination li:last").prev().html());
                var newPage=0;
                jQuery(".mns-advanced_search-products.mns-list .Pagination li:not(:last):not(:first)").click(function(){
                    jQuery(".mns-advanced_search-products.mns-list").attr("page", jQuery(this).html() );
                    jQuery(this).closest(".mns-block").find("select[name=products-list-sort]").trigger("change");
                });
                jQuery(".mns-advanced_search-products.mns-list .Pagination li:last").click(function(){
                    newPage=parseInt(jQuery(".mns-advanced_search-products.mns-list").attr("page"))+1;
                    if(newPage>AllPages){
                        newPage=AllPages;
                    }
                    jQuery(".mns-advanced_search-products.mns-list").attr("page", newPage );
                    jQuery(this).closest(".mns-block").find("select[name=products-list-sort]").trigger("change");
                });
                jQuery(".mns-advanced_search-products.mns-list .Pagination li:first").click(function(){
                    newPage=parseInt(jQuery(".mns-advanced_search-products.mns-list").attr("page"))-1;
                    if(newPage<1){
                        newPage=1;
                    }
                    jQuery(".mns-advanced_search-products.mns-list").attr("page", newPage );
                    jQuery(this).closest(".mns-block").find("select[name=products-list-sort]").trigger("change");
                });
                jQuery(".mns-advanced_search-products .ItemShow").click(function(){
                    jQuery(this).closest(".mns-advanced_search-products ").attr("items",parseInt(jQuery(this).html()))
                    jQuery(this).closest(".mns-block").find("select[name=products-list-sort]").trigger("change");
                });
                if(jQuery(".mns-block.mns-advanced_search ul.pagination li").length>7){
                jQuery(".mns-block.mns-advanced_search ul.pagination li")
                        .removeClass("showPagination")
                        .parent().find(".active").addClass("showPagination");
                        if(jQuery(".mns-block.mns-advanced_search ul.pagination li.active").prev().length==1){
                            jQuery(".mns-block.mns-advanced_search ul.pagination li.active")
                                    .prev()
                                    .addClass("showPagination");
                        }
                        if(jQuery(".mns-block.mns-advanced_search ul.pagination li.active").prev().prev().length==1){
                            jQuery(".mns-block.mns-advanced_search ul.pagination li.active")
                                    .prev()
                                    .prev()
                                    .addClass("showPagination");
                        }
                        if(jQuery(".mns-block.mns-advanced_search ul.pagination li.active").next().length==1){
                            jQuery(".mns-block.mns-advanced_search ul.pagination li.active")
                                    .next()
                                    .addClass("showPagination");
                        }
                        if(jQuery(".mns-block.mns-advanced_search ul.pagination li.active").next().next().length==1){
                            jQuery(".mns-block.mns-advanced_search ul.pagination li.active")
                                    .next()
                                    .next()
                                    .addClass("showPagination");
                        }
                }
                else{
                    jQuery(".mns-block.mns-advanced_search ul.pagination li").addClass("showPagination");
                }
                jQuery(".mns-advanced_search-products.mns-list").css("opacity","1");
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
            },
            error:function(){
                ProductFilters.css("opacity","1");
            }
        });
    }));
    jQuery(".MensioAdvancedSearchResult span.NavPage").click(function(){
        jQuery(".MensioAdvancedSearchResult").attr("page",jQuery(this).attr("value"));
        jQuery(".mns-block.mns-advanced_search input[name=search]").click();
    });
    jQuery(".mns-block.mns-advanced_search .next-page-result").click(function(){
        jQuery(".MensioAdvancedSearchResult").attr("page",(parseInt(jQuery(".MensioAdvancedSearchResult").attr("page"))+1));
        jQuery(".mns-block.mns-advanced_search input[name=search]").click();
    });
    jQuery(".mns-block.mns-advanced_search .prev-page-result").click(function(){
        if(jQuery(".MensioAdvancedSearchResult").attr("page")!="0"){
            jQuery(".MensioAdvancedSearchResult").attr("page",(parseInt(jQuery(".MensioAdvancedSearchResult").attr("page"))-1));
            jQuery(".mns-block.mns-advanced_search input[name=search]").click();
        }
    });
    jQuery("div.mns-html-content .user-data .field > div .disableMnsUser").click(function(){
        var parentDiv=jQuery(this).parent().parent();
        var user=parentDiv.attr("class").replace("field company-users user-","");
        jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            data: {
              'action': 'mensiopress_DisableUser',
              'mns_sec':$sec,
              'mns_User': user
            },
            success:function(data) {
                MensioMessage(data);
                parentDiv.remove();
            }
        });
    });
    jQuery("#mns-coupon").hide().prev().hide().prev().hide();
    jQuery("#mns-coupon").blur(function(){
        jQuery("h2.FinalPriceWithCoupon").remove();
        jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            data: {
              'action': 'mensiopress_CheckCoupon',
              'mns_sec':$sec,
              'mns_Coupon': jQuery("#mns-coupon").val()
            },
            success:function(data) {
                if(data==false){
                    jQuery("#mns-coupon").css("border","1px solid red");
                    jQuery(".MensioPayButton .Total").html( 
                            jQuery(".MensioPayButton").attr("totalcost")
                            );
                    return false;
                }
                jQuery("#mns-coupon").css("border","1px solid green");
                var obj=JSON.parse(data);
                var finalCost=jQuery(".mns-FinalCost").attr("cost");
                if(obj.Message!=""){
                    jQuery("#mns-coupon").after("<h3>"+obj.Message+"</h3>");
                }
                if(obj.DiscountPercent>0){
                    finalCost=finalCost-(finalCost*(obj.DiscountPercent/100));
                    finalCost=finalCost+parseInt(jQuery("input.shipping-company:checked").attr("cost"));
                    jQuery(".mns-FinalCost").wrap("<s></s>");
                    if(jQuery(".FinalPriceWithCoupon").length==0){
                        jQuery("#mns-coupon").after("<h2 class='FinalPriceWithCoupon'>-"+obj.DiscountPercent+"% = <span class='mensioPrice'>"+finalCost+"</span></h2>");
                        jQuery("input[name=orderAmount]").val(finalCost);
                    }
                    else{
                        jQuery(".FinalPriceWithCoupon").html("-"+obj.DiscountPercent+"% = <span class='mensioPrice'>"+finalCost+"</div>");
                    }
                    jQuery(".FinalPriceWithCoupon").html(obj.CouponName);
                    jQuery(".MensioPayButton .Total").html(finalCost.toFixed(2));
                }
            }
        });
    });
    jQuery(".mns-checkout .mns-ShippingMethods .mns-ShippingChoose input[type=radio]").change(function(){
        var finalcost= Number(jQuery(".mns-FinalCost").attr("cost")) + Number(jQuery(this).parent().prev().html());
        jQuery(".Gateway.PayMethod input[name=orderAmount]").val(finalcost);
    });
    MensioAddToCompareList();
    jQuery(".MensioComparisonListRemoveAll").click(function(){
        var prod=jQuery(this).closest(".product-view").attr("prodid");
        jQuery.ajax({
            type: 'post',
            url: ajaxurl,
            data: {
              'action': 'mensiopress_RemoveComparison',
              'mns_sec':$sec,
              'Prod':'All'
            },
            success:function(data) {
                window.location.href='';
            },
            error: function(errorThrown){
            }
        });
    });
    RemoveFromComparisonList();
    jQuery(".clear-all-filters input[type=button]").click(function(){
        jQuery(".product-view-variation input").prop("disabled",false).prop("checked",false).parent().parent().removeClass("VariationChecked").css("opacity","1");
        jQuery(".product-view-variation input").prop("disabled",false).prop("checked",false).parent().parent().removeClass("VariationChecked").css("opacity","1");
    });    
    jQuery(".product-view-variation")
        .attr("bodyw", jQuery("body").width() )
        .one("mouseover",function(event){
            var screen=(jQuery("body").width())/2;
            if(event.pageX < screen){
                jQuery(this).addClass("sendRight");
            }
            else if(event.pageX > screen){
                jQuery(this).addClass("sendLeft");
            }
    });
    jQuery(".MensioTopRightComparisonDIV,.MensioTopRightFavoritesDIV")
        .attr("bodyw", jQuery("body").width() )
        .on("mouseover",function(event){
            if(jQuery(this).find(".mns-list.Widget").length>0){
                jQuery(this).find(".mns-list.Widget").mouseover(function(){
                    return false;
                });
            }
            var screen=(jQuery("body").width())/2;
            jQuery(".MensioTopRightComparisonDIV,.MensioTopRightFavoritesDIV").removeClass("sendRight, sendLeft");
            if(event.pageX < screen){
                jQuery(".MensioTopRightComparisonDIV,.MensioTopRightFavoritesDIV").addClass("sendRight");
            }
            else if(event.pageX > screen){
                jQuery(".MensioTopRightComparisonDIV,.MensioTopRightFavoritesDIV").addClass("sendLeft");
            }
            var CartH=jQuery(this).find(".mns-list.Widget").height();
            var bodyH=jQuery(window).height();
            var screenH=event.originalEvent.clientY;
            if((bodyH-CartH) < screenH){
                jQuery(".MensioTopRightComparisonDIV, .MensioTopRightFavoritesDIV").removeClass("sendDown");
                jQuery(".MensioTopRightComparisonDIV, .MensioTopRightFavoritesDIV").addClass("sendUp");
            }
            else{
                jQuery(".MensioTopRightComparisonDIV, .MensioTopRightFavoritesDIV").removeClass("sendUp");
                jQuery(".MensioTopRightComparisonDIV, .MensioTopRightFavoritesDIV").addClass("sendDown");
            }
    });
});