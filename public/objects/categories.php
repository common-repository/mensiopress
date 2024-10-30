<?php
add_shortcode( 'mns_categories', 'mensiopress_get_categories' );
function mensiopress_get_categories($atts){
    if(empty($atts['ordering'])){
        $ordering="A-Z";
    }
    else{
        $ordering=$atts['ordering'];
    }
    if(empty($atts['maxproducts'])){
        $atts['maxproducts']=-1;
    }
    $categories=new mensio_products_categories();
    $categories->Set_Parent("TopLevel");
    $TopCategories=$categories->LoadProductCategoriesTreeDataSet();
    $i=0;
    $list=array();
    foreach($TopCategories as $top){
        $categories->Set_Parent($top->category);
        $ChildCategories=$categories->LoadProductCategoriesTreeDataSet();
        $categories->Set_UUID($top->category);
        $TopCat=$categories->GetCategoryData();
        $Link=new mnsGetFrontEndLink();
        $Link=$Link->CategoryLink($top->category);
        $list[$i]['id']=$top->category;
        $list[$i]['name']=$top->translation;
        $list[$i]['link']=$Link;
        $list[$i]['image']= site_url()."/".$TopCat['image'];
        $i++;
    }
    $list=MensioList($list,$atts,"Categories",false);
    return $list;
}
add_shortcode( 'mns_top_categories', 'mensiopress_top_categories' );
function mensiopress_top_categories($atts){
    $cats=new mnsFrontEndObject();
    $cats=$cats->mnsFrontEndTopCategories();
    $list=MensioList($cats,$atts,"Top Categories",false);
    return $list;
}
