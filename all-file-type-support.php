<?php
/**
 * Plugin Name: All File Type Support
 * Plugin URI:  https://www.primisdigital.com/wordpress-plugins/
 * Description: All File Type Support is a wordpress plugin to upload svg,webp,psd,xml,json,webp type file uploading in wordpress.
 * Version:     1.0
 * Author:      Primis Digital
 * Author URI:  https://www.primisdigital.com/
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: 
 * Domain Path: 
 */

// Some constant defintion
define("ITS_DIR", plugin_dir_path(__FILE__), FALSE);
define("ITS_DIR_URL", plugin_dir_url(__FILE__), FALSE);
//Plugin Activation code
register_activation_hook(__FILE__, 'afts_activation');
function afts_activation()
{
    // Setting option for posts types , that used featured video
	if(get_option('ist_img_types')){
	}
    else {
		$ist_post_type ='';
		$ist_post_type = sanitize_text_field($ist_post_type); // sanitize data
		add_option('ist_img_types', $ist_post_type);
    }
	define( 'ALLOW_UNFILTERED_UPLOADS', true );

}

// Register Deactivation Hook here
register_deactivation_hook(__FILE__, 'afts_deactivation');
function afts_deactivation()
{
    define( 'ALLOW_UNFILTERED_UPLOADS', false );

}

// Register Uninstall Hook here
register_uninstall_hook(__FILE__, 'afts_uninstall');

function afts_uninstall()
{
    define( 'ALLOW_UNFILTERED_UPLOADS', false );
}

/* Plugin Menu Creation Starts Here */
add_action('admin_menu', 'afts_menus');
	
// Menu creation under General Settings with name "Simple Featured Video"
function afts_menus(){
add_submenu_page('options-general.php','All File Type Support', 'All File Type Support', 'administrator', 'afts_menu_page','afts_menu_page_function','', '');
}
function afts_menu_page_function(){
	?>
	<div class="wrap">
	<h1>All File Type Support For Wordpress</h1>
	<?php 
		if(isset($_POST['ist_posts_submit'])){
			if (!isset($_POST['ist_settings_non'])) { 
				die('<br><br>NO CSRF For you'); 
			}
			if (!wp_verify_nonce($_POST['ist_settings_non'],'ist_settings_non_num')) 
			{
			die('<br><br>NO CSRF For you'); 
			}
			// saving value in option variable into option table , after sanitizing
			$ist_post_type_temp =$_POST['ist_image_type'];
			foreach($ist_post_type_temp as $keys => $element){
					$ist_post_type[] = sanitize_text_field( $element );
				}

			if(!empty($ist_post_type) && is_array($ist_post_type)) {  
				$ist_post_type = implode(",",$ist_post_type);
			}else $ist_post_type ='';  // if empty
			$ist_post_type = sanitize_text_field($ist_post_type); // sanitize data
			update_option('ist_img_types',$ist_post_type);
			
			// Success message	
			echo sprintf(__( '<div id="setting-error-page_for_privacy_policy" class="updated settings-error notice is-dismissible">  
			<p><strong>updated successfully.</strong></p>
			<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
			</div>' ));
		}
	?>
	<h2>Select file type:</h2>
	<?php $ist_img_types = get_option('ist_img_types');	
	$ist_img_types_ar = explode(",",$ist_img_types);
	?>
	<form action="" name="ist_posts_form" method="post">
		<div>
			<p>
				<input type="checkbox" value="svg" name="ist_image_type[]" <?php if(in_array("svg",$ist_img_types_ar)){ echo "checked";}?> >
				<label for="svg"><?php echo esc_html("SVG"); ?></label>
			</p>
			<p>
				<input type="checkbox" value="webp" name="ist_image_type[]" <?php if(in_array("webp",$ist_img_types_ar)){ echo "checked";}?>>
				<label for="webp"><?php echo esc_html("Webp"); ?></label>
			</p>
			<p>
				<input type="checkbox" value="json" name="ist_image_type[]" <?php if(in_array("json",$ist_img_types_ar)){ echo "checked";}?>>
				<label for="json"><?php echo esc_html("json"); ?></label>
			</p>
			<p>
				<input type="checkbox" value="xml" name="ist_image_type[]" <?php if(in_array("xml",$ist_img_types_ar)){ echo "checked";}?>>
				<label for="xml"><?php echo esc_html("xml"); ?></label>
			</p>
			<p>
				<input type="checkbox" value="psd" name="ist_image_type[]" <?php if(in_array("psd",$ist_img_types_ar)){ echo "checked";}?>>
				<label for="psd"><?php echo esc_html("psd"); ?></label>
			</p>
			<input name="ist_settings_non" type="hidden" value="<?php echo wp_create_nonce('ist_settings_non_num'); ?>" />
			
		</div> 
		<?php echo sprintf(__( '<p class="submit"><input type="submit" name="ist_posts_submit" id="ist_posts_submit" class="button button-primary" value="Save Changes"></p>')); ?>
	</form> 
</div> 
<?php
}
// upload mimes
add_filter('upload_mimes', 'afts_add_sv_type');
function afts_add_sv_type($file_types){
	$ist_img_types = get_option('ist_img_types');	
	$ist_img_types_ar = explode(",",$ist_img_types);
	foreach($ist_img_types_ar as $type):
		$file_types[$type] = 'image/'.$type;
	endforeach;
	return $file_types;
}

