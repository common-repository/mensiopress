<?php
    $MensioSkinColors=array();
    $MensioSkinColorTitles[1]="Default";
    $MensioSkinColors[1]['DivsBG']="F1F1F1";            //BACKGROUND OR THE MAIN DIVS
    $MensioSkinColors[1]['DivsCL']="5A5353";            //TEXT COLOR
    $MensioSkinColors[1]['DivsInactiveCL']="AAAAAA";    //INACTIVE DIVS
    $MensioSkinColors[1]['DivsBorder']="916953";        //DIVS BORDER COLOR
    $MensioSkinColors[1]['RangeBullet']="AA0000";       //INPUT RANGE (LIMIT) BULLET COLOR
    $MensioSkinColors[1]['RangeTrack']="444444";        //INPUT RANGE (LIMIT) TRACKER COLOR
    $MensioSkinColors[1]['BlocksHover']="960000";       //WHEN USER HOVERS DIV OR A BLOCK
    $MensioSkinColors[1]['TitlesCL']="960000";          //TEXT COLOR OR THE TITLE IN THE RIGHT SIDE WINDOF
    $MensioSkinColorTitles[2]="Black";
    $MensioSkinColors[2]['DivsBG']="000000";
    $MensioSkinColors[2]['DivsCL']="ffffff";
    $MensioSkinColors[2]['DivsInactiveCL']="3f4042";
    $MensioSkinColors[2]['DivsBorder']="c9cacc";
    $MensioSkinColors[2]['RangeBullet']="f1f1f1";
    $MensioSkinColors[2]['RangeTrack']="ffffff";
    $MensioSkinColors[2]['BlocksHover']="721515";
    $MensioSkinColors[2]['TitlesCL']="ffffff";
    $MensioSkinColorTitles[3]="Red";
    $MensioSkinColors[3]['DivsBG']="b52f2f";
    $MensioSkinColors[3]['DivsCL']="ffffff";
    $MensioSkinColors[3]['DivsInactiveCL']="dd8585";
    $MensioSkinColors[3]['DivsBorder']="f1f1f1";
    $MensioSkinColors[3]['RangeBullet']="dd8585";
    $MensioSkinColors[3]['RangeTrack']="ffffff";
    $MensioSkinColors[3]['BlocksHover']="ce5454";
    $MensioSkinColors[3]['TitlesCL']="ffffff";
    $MensioBottomButtons=array();
    $MensioBottomButtons['OpenCloseSettings']="This will minify the properties column above.";
    $MensioBottomButtons['OpenCloseEdits']="This will minify the properties column above.";
    $MensioBottomButtons['Settings']="This will open the Global Builder settings.";
    $MensioBottomButtons['SaveButton']="Selecting here will store all current changes. New page content will be immediately visible to all website visitors.";
    $MensioBottomButtons['CloseSettings']="Selecting here, will exit the Mensiopress Page Builder. Note: All unsaved changes will be lost!";
    $MensioBottomButtons['CloseProperties']="When active, you may click here in order to return to the main page properties column.";
    $MensioBottomButtons['Revert']="Selecting here will revert the current page. All unsaved changes will be lost!";
if(!empty($_GET['css'])){
    header("Content-type: text/css; charset: UTF-8");
    for($i=1;$i<=3;$i++){
            echo    "
            html.skin-".$i." #theSettings .mns-element-to-create,
            html.skin-".$i." #theEdits .backToEdits,
            html.skin-".$i." .current-blocks{
                color: #".$MensioSkinColors[$i]['DivsCL']." !important;
                border: 1px solid #".$MensioSkinColors[$i]['DivsBorder']." !important;
                border-top: 10px solid #".$MensioSkinColors[$i]['DivsBorder']." !important;
                background:#".$MensioSkinColors[$i]['DivsBG']." !important;
            }
            html.skin-".$i." .MnsCustomSelect{
                color: #".$MensioSkinColors[$i]['DivsCL']." !important;
                border: 1px solid #".$MensioSkinColors[$i]['DivsBorder']." !important;
                background:#".$MensioSkinColors[$i]['DivsBG']." !important;
            }
            html.skin-".$i." #theEdits label{
                color: #".$MensioSkinColors[$i]['DivsCL']." !important;
            }
            html.skin-".$i." .mns-html-content > .mns-block{
                border-top:none !important;
                transition:box-shadow .3s;
            }
            html.skin-".$i." #theSettings .mns-element-to-create.disabled{
                border: 1px solid #".$MensioSkinColors[$i]['DivsInactiveCL']." !important;
                border-top: 10px solid #".$MensioSkinColors[$i]['DivsInactiveCL']." !important;
            }
            html.skin-".$i." .mns-block .block-tool-bar{
                background:#".$MensioSkinColors[$i]['DivsInactiveCL']." !important;
            }
            html.skin-".$i." .mns-block.currentBlockEdit .block-tool-bar{
                background:#".$MensioSkinColors[$i]['BlocksHover']." !important;
                opacity:1 !important;
            }
            #currentBlocks{
                color: #".$MensioSkinColors[$i]['DivsCL'].";
                border-color:#".$MensioSkinColors[$i]['DivsCL'].";
            }
            html.skin-".$i." #theEdits,
            html.skin-".$i." #theSettings,
            html.skin-".$i." .bottom-buttons,
            html.skin-".$i." .center-bottom-buttons{
                background: #".$MensioSkinColors[$i]['DivsBG']." !important;
                color: #".$MensioSkinColors[$i]['DivsCL']." !important;
            }
            html.skin-".$i." #theEdits input[type=range]::-webkit-slider-thumb {
                background: #".$MensioSkinColors[$i]['RangeBullet']." !important;
            }
            html.skin-".$i." #theEdits input[type=range]::-moz-range-thumb {
              box-shadow: 1px 1px 1px #".$MensioSkinColors[$i]['RangeBullet'].", 0px 0px 1px #".$MensioSkinColors[$i]['RangeBullet']." !important;
              border: 1px solid #".$MensioSkinColors[$i]['RangeBullet']." !important;
              background: #".$MensioSkinColors[$i]['RangeBullet']." !important;
            }
            html.skin-".$i." #theEdits input[type=range]::-ms-thumb {
              box-shadow: 1px 1px 1px #".$MensioSkinColors[$i]['RangeBullet'].", 0px 0px 1px #".$MensioSkinColors[$i]['RangeBullet']." !important;
              border: 1px solid #".$MensioSkinColors[$i]['RangeBullet']." !important;
              background: #".$MensioSkinColors[$i]['RangeBullet']." !important;
            }
            html.skin-".$i." #theEdits input[type=range]::-webkit-slider-runnable-track {
              background: #".$MensioSkinColors[$i]['RangeTrack']." !important;
              border: 0.2px solid #".$MensioSkinColors[$i]['RangeTrack']." !important;
            }
            html.skin-".$i." .ps-scrollbar-y-rail{
                background-color: #".$MensioSkinColors[$i]['DivsBorder']." !important;
                width: 5px !important;
                height:100% !important;
                right:0 !important;
                opacity:1 !important;
                display:block !important;
            }
            html.skin-".$i." .ps-scrollbar-y{
                width: 5px !important;
                height:400px !important;
                right:0 !important;
                opacity:1 !important;
                background-color: #".$MensioSkinColors[$i]['DivsInactiveCL']." !important;
            }
            html.skin-".$i." #currentObjects h2,
            html.skin-".$i." #mnsEditsTitle,html.skin-".$i." #theEdits #pg_function, html.skin-".$i." #theEdits #page_title_div
            {
                color: #".$MensioSkinColors[$i]['DivsCL'].";
                border: 1px solid #".$MensioSkinColors[$i]['DivsCL']." !important;
            }
            html.skin-".$i." #currentBlocks{
                border: 1px solid #".$MensioSkinColors[$i]['DivsCL'].";
                color: #".$MensioSkinColors[$i]['DivsCL']." !important;
            }
            html.skin-".$i." #mnsPageEditorLogo,
            html.skin-".$i." #theSettings .bottom-buttons{
                border-right: 5px solid #".$MensioSkinColors[$i]['DivsBorder']." !important;
            }
            html.skin-".$i." #theEdits,
            html.skin-".$i." #theEdits .bottom-buttons{
                border-left:5px solid #".$MensioSkinColors[$i]['DivsBorder']." !important;
            }
            html.skin-".$i." #settings .ps__rail-y{
                background:#".$MensioSkinColors[$i]['DivsBorder']." !important;
                width:5px !important;
                opacity:1 !important;
            }
            html.skin-".$i." .current-blocks:hover,
            html.skin-".$i." #mnsEditsTitle{
                border:1px solid #".$MensioSkinColors[$i]['BlocksHover']." !important;
                border-top:10px solid #".$MensioSkinColors[$i]['BlocksHover']." !important;
            }
            html.skin-".$i." .clonedDIV{
                background:#".$MensioSkinColors[$i]['BlocksHover']." !important;
                color:#fff !important;
            }
            html.skin-".$i." .mns-html-content > div.mns-block:hover:not(.cusBorder),
            html.skin-".$i." .mns-html-content  div.mns-block.currentBlockEdit:not(.cusBorder){
                box-shadow:0 0 15px #000;
            }
            html.skin-".$i." #mnsEditsTitle{
                color:#".$MensioSkinColors[$i]['TitlesCL']." !important;
            }
            html.skin-".$i." .MnsCustomSelect .category:hover{
                background: #".$MensioSkinColors[$i]['DivsBorder']." !important;
                color: #".$MensioSkinColors[$i]['DivsBG']." !important;
            }
            html.skin-".$i." #theEdits .select::after,
            html.skin-".$i." .center-bottom-buttons .select::after,
                background: #".$MensioSkinColors[$i]['DivsBorder']." !important;
            }
            html.skin-".$i." #theEdits  .select:hover::after,
            html.skin-".$i." .center-bottom-buttons .select:hover::after {
                color: #".$MensioSkinColors[$i]['DivsBG']." !important;
            }
            html.skin-".$i."{
                margin: 0 !important;
            }
        ";
    }
}
?>