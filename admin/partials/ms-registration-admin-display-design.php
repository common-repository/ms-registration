<?php
function ms_get_attachment_id_by_url($url) {
    $parsed_url  = explode(parse_url(WP_CONTENT_URL, PHP_URL_PATH), $url);
    $this_host = str_ireplace('www.', '', parse_url( home_url(), PHP_URL_HOST));
    $file_host = str_ireplace('www.', '', parse_url( $url, PHP_URL_HOST));
    if ( ! isset( $parsed_url[1] ) || empty( $parsed_url[1] ) || ( $this_host != $file_host ) ) {
        return;
    }
    global $wpdb;
    $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM {$wpdb->prefix}posts WHERE guid RLIKE %s;", $parsed_url[1]));
    return $attachment[0];
}

function ms_registration_design_options() {
    add_thickbox();
	wp_enqueue_media();
	$registration_design = get_option('ms_registration_form_design', array());
    ?>
    <div class="wrap ms-design-wrap">
        <h2><?php echo __('Customize WP login and registration design.', 'ms-registration') ?></h2>
        <hr>
        <div id="post-body" class="metabox-holder columns-2">
            <div id="post-body-content">
				<form id="ms-design">
					<input type="hidden" name="action" value="ms_registration_submit_design_data">
					<p>
						<img src="<?php echo ((isset($registration_design['logo']) && @$registration_design['logo'] != '') ? wp_get_attachment_image_src(ms_get_attachment_id_by_url($registration_design['logo']))[0] : '') ?>"><br>
						<input type="text" name="logo_image"<?php echo ((isset($registration_design['logo']) && @$registration_design['logo'] != '') ? ' value="' . $registration_design['logo'] . '"' : '') ?> readonly>
						<button class="button button-primary" data-image-picker="1" data-title="Select an image for the logo" data-select-button="Send to Logo">Select Logo</button>
					</p>
					<p>
						<img src="<?php echo ((isset($registration_design['bg']) && @$registration_design['bg'] != '') ? wp_get_attachment_image_src(ms_get_attachment_id_by_url($registration_design['bg']))[0] : '') ?>"><br>
						<input type="text" name="bg_image"<?php echo ((isset($registration_design['bg']) && @$registration_design['bg'] != '') ? ' value="' . $registration_design['bg'] . '"' : '') ?> readonly>
						<button class="button button-primary" data-image-picker="1" data-title="Select an image for the background" data-select-button="Send to Background">Select Background</button>
					</p>
					<?php submit_button(); ?>
				</form>
            </div>
        </div>
        <br class="clear">
    </div>
	<script>
		jQuery('[data-image-picker="1"]').on('click', function(e) {
			e.preventDefault();
			var element = $(this);
			var title = element.data('title');
			var select_button = element.data('select-button');			
			var file_frame = wp.media.frames.file_frame = wp.media({
				title: title,
				button: {
					text: select_button,
				},
				multiple: false
			});
			
			file_frame.on('select', function() {
				attachment = file_frame.state().get('selection').first().toJSON();
				var p_element = element.parent();
				p_element.find('input').val(attachment.url);
				p_element.find('img').attr('src', attachment.sizes.thumbnail.url);
			});
			
			file_frame.open();
		});
		jQuery('#ms-design').on('submit', function(e) {
                e.preventDefault();
                jQuery.post('admin-ajax.php', jQuery(this).serialize(), function(data) {
                    jQuery('#ms_submit_norif').remove();
                    jQuery('div.wrap').prepend('<div id="ms_submit_norif" class="notice ' + (data.msg === 'ok' ? 'notice-success' : 'notice-error') + ' is-dismissible"><p>' + data._msg + '</p></div>');
                });
            });
	</script>
    <?php
}
?>