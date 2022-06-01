<?php
/**
 * Vendor List Template
 *
 * This template can be overridden by copying it to yourtheme/wc-vendors/front/vendors-list.php
 *
 * @author        Jamie Madden, WC Vendors
 * @package       WCVendors/Templates/Emails/HTML
 * @version       2.0.0
 *
 *    Template Variables available
 *  $shop_name : pv_shop_name
 *  $shop_description : pv_shop_description (completely sanitized)
 *  $shop_link : the vendor shop link
 *  $vendor_id  : current vendor id for customization
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


$store_icon_src   = wp_get_attachment_image_src(
    get_user_meta( $vendor_id, '_wcv_store_icon_id', true ),
    array( 150, 150 )
);
$store_icon       = '<img src="' . get_avatar_url( $vendor_id, array( 'size' => 150 ) ) . '" alt="" class="store-icon" />';
if ( is_array( $store_icon_src ) ) {
    $store_icon = '<img src="' . $store_icon_src[0] . '" alt="" class="store-icon" />';
}

?>
<div class="vendor_list" style="display:inline-block; margin-right:10%;">
	<center>
		<a href="<?php echo $shop_link; ?>"><?php echo $store_icon; ?></a><br/>
		<a href="<?php echo $shop_link; ?>" class="button"><?php echo $shop_name; ?></a>
		<br/><br/>
	</center>
</div>
