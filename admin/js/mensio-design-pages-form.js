var $sec = jQuery('#_wpnonce').val()+'::'+jQuery('input[name=_wp_http_referer]').val();
jQuery("document").ready(function(){
    jQuery("#newMensioPage-button").click(function(){
        jQuery("#allPages").css("overflow","hidden").animate({height:0},500);
        jQuery("#newMensioPage").animate({height:"100%",paddingTop:"25px",paddingBottom:"25px"},500); 
    });
    jQuery("#BTN_Save").click(function(){
        if((jQuery("#NewMensioPageTitle").val()==false) || (jQuery("#NewMensioPageFunction").val()==false)){
            alert("Please fill in all the fields of the form");
            return false;
        }
        jQuery.ajax({
          type: "post",
          url: ajaxurl,
          data: { 'Security': $sec,
            "action": "newMensioPage",
            "NewMensioPageTitle": jQuery("#NewMensioPageTitle").val(),
            "NewMensioPageFunction" : jQuery("#NewMensioPageFunction").val()
          },
          success:function(data) {
            jQuery("#newMensioPage").prepend("<strong>Page Added Successfully!</strong>");
            window.location.href='';
          },
          error: function(errorThrown){
              alert("Oops! Something went wrong");
          }
        });
    });
    jQuery("#Pages-Body .delete-mensio-page").click(function(){
        var MensioPageID=jQuery(this).attr("id").replace("del-","");
        var MensioPage=jQuery(this);
        var conf=confirm("Are you sure you want to remove Page "+ MensioPage.attr("title") +"?")
        if(conf==true){
            jQuery.ajax({
              type: "post",
              url: ajaxurl,
              data: { 'Security': $sec,
                "action": "delMensioPage",
                "MensioPageToDel": MensioPageID
              },
              success:function(data) {
                MensioPage.parent().parent().remove();
              },
              error: function(errorThrown){
                  alert("Cannot delete Page");
              }
            });
        }
    });
});