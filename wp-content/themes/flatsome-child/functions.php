<?php
// Add custom Theme Functions here
//Copy từng phần và bỏ vào file functions.php của theme:
//xoa mã bưu điện thanh toán
add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields' );
function custom_override_checkout_fields( $fields ) {
     unset($fields['billing']['billing_postcode']);
     unset($fields['billing']['billing_country']);
     unset($fields['billing']['billing_address_2']);
     unset($fields['billing']['billing_company']);
     return $fields;
}
add_filter('woocommerce_empty_price_html', 'custom_call_for_price');
function custom_call_for_price() {
return '<span class="lien-he-price">Liên hệ</span>';
}
function register_my_menu() {
  register_nav_menu('product-menu',__( 'Menu Danh mục' ));
}
add_action( 'init', 'register_my_menu' );
//Doan code thay chữ giảm giá bằng % sale
//* Add stock status to archive pages
add_filter( 'woocommerce_get_availability', 'custom_override_get_availability', 1, 2);
 
// The hook in function $availability is passed via the filter!
function custom_override_get_availability( $availability, $_product ) {
if ( $_product->is_in_stock() ) $availability['availability'] = __('Còn hàng', 'woocommerce');
return $availability;
}
// Enqueue Scripts and Styles.
add_action( 'wp_enqueue_scripts', 'flatsome_enqueue_scripts_styles' );
function flatsome_enqueue_scripts_styles() {
wp_enqueue_style( 'dashicons' );
wp_enqueue_style( 'flatsome-ionicons', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' );
}
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart');
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
// Code luu anh
class Auto_Save_Images{
 
    function __construct(){     
        
        add_filter( 'content_save_pre',array($this,'post_save_images') ); 
    }
    
    function post_save_images( $content ){
        if( ($_POST['save'] || $_POST['publish'] )){
            set_time_limit(240);
            global $post;
            $post_id=$post->ID;
            $preg=preg_match_all('/<img.*?src="(.*?)"/',stripslashes($content),$matches);
            if($preg){
                foreach($matches[1] as $image_url){
                    if(empty($image_url)) continue;
                    $pos=strpos($image_url,$_SERVER['HTTP_HOST']);
                    if($pos===false){
                        $res=$this->save_images($image_url,$post_id);
                        $replace=$res['url'];
                        $content=str_replace($image_url,$replace,$content);
                    }
                }
            }
        }
        remove_filter( 'content_save_pre', array( $this, 'post_save_images' ) );
        return $content;
    }
    
    function save_images($image_url,$post_id){
        $file=file_get_contents($image_url);
        $post = get_post($post_id);
        $posttitle = $post->post_title;
        $postname = sanitize_title($posttitle);
        $im_name = "$postname-$post_id.jpg";
        $res=wp_upload_bits($im_name,'',$file);
        $this->insert_attachment($res['file'],$post_id);
        return $res;
    }
    
    function insert_attachment($file,$id){
        $dirs=wp_upload_dir();
        $filetype=wp_check_filetype($file);
        $attachment=array(
            'guid'=>$dirs['baseurl'].'/'._wp_relative_upload_path($file),
            'post_mime_type'=>$filetype['type'],
            'post_title'=>preg_replace('/\.[^.]+$/','',basename($file)),
            'post_content'=>'',
            'post_status'=>'inherit'
        );
        $attach_id=wp_insert_attachment($attachment,$file,$id);
        $attach_data=wp_generate_attachment_metadata($attach_id,$file);
        wp_update_attachment_metadata($attach_id,$attach_data);
        return $attach_id;
    }
}
new Auto_Save_Images();
// end code luu anh

//bỏ đánh giá
add_filter( 'woocommerce_product_tabs', 'wcs_woo_remove_reviews_tab', 98 );
    function wcs_woo_remove_reviews_tab($tabs) {
    unset($tabs['reviews']);
    return $tabs;
}