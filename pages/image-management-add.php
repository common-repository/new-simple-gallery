<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<div class="wrap">
<?php
$nsg_errors = array();
$nsg_success = '';
$nsg_error_found = FALSE;

// Preset the form fields
$form = array(
	'nsg_path' 		=> '',
	'nsg_link' 		=> '',
	'nsg_target' 	=> '',
	'nsg_title' 	=> '',
	'nsg_order' 	=> '',
	'nsg_status' 	=> '',
	'nsg_type' 		=> ''
);

// Form submitted, check the data
if (isset($_POST['nsg_form_submit']) && $_POST['nsg_form_submit'] == 'yes')
{
	//	Just security thingy that wordpress offers us
	check_admin_referer('nsg_form_add');
	
	$form['nsg_path'] = isset($_POST['nsg_path']) ? esc_url_raw($_POST['nsg_path']) : '';
	if ($form['nsg_path'] == '')
	{
		$nsg_errors[] = __('Please enter the image path.', 'new-simple-gallery');
		$nsg_error_found = TRUE;
	}

	$form['nsg_link'] = isset($_POST['nsg_link']) ? esc_url_raw($_POST['nsg_link']) : '';
	if ($form['nsg_link'] == '')
	{
		$nsg_errors[] = __('Please enter the target link.', 'new-simple-gallery');
		$nsg_error_found = TRUE;
	}
	
	$form['nsg_target'] = isset($_POST['nsg_target']) ? sanitize_text_field($_POST['nsg_target']) : '';
	if($form['nsg_target']!= "_blank" && $form['nsg_target'] != "_parent" && $form['nsg_target'] != "_self" && $form['nsg_target'] != "_new")
	{
		$form['nsg_target'] = "_blank";
	}
	
	$form['nsg_title'] 	= isset($_POST['nsg_title']) ? sanitize_text_field($_POST['nsg_title']) : '';
	$form['nsg_order'] 	= isset($_POST['nsg_order']) ? intval($_POST['nsg_order']) : '';
	$form['nsg_status'] = isset($_POST['nsg_status']) ? sanitize_text_field($_POST['nsg_status']) : '';
	
	if($form['nsg_status'] != "YES" && $form['nsg_status'] != "NO")
	{
		$form['nsg_status'] = "YES";
	}
	
	$form['nsg_type'] = isset($_POST['nsg_type']) ? sanitize_text_field($_POST['nsg_type']) : '';

	//	No errors found, we can add this Group to the table
	if ($nsg_error_found == FALSE)
	{
		$sql = $wpdb->prepare(
			"INSERT INTO `".WP_NSG_TABLE."`
			(`nsg_path`, `nsg_link`, `nsg_target`, `nsg_title`, `nsg_order`, `nsg_status`, `nsg_type`)
			VALUES(%s, %s, %s, %s, %d, %s, %s)",
			array($form['nsg_path'], $form['nsg_link'], $form['nsg_target'], $form['nsg_title'], $form['nsg_order'], $form['nsg_status'], $form['nsg_type'])
		);
		$wpdb->query($sql);
		
		$nsg_success = __('New image details was successfully added.', 'new-simple-gallery');
		
		// Reset the form fields
		$form = array(
			'nsg_path' 		=> '',
			'nsg_link' 		=> '',
			'nsg_target' 	=> '',
			'nsg_title' 	=> '',
			'nsg_order' 	=> '',
			'nsg_status' 	=> '',
			'nsg_type' 		=> ''
		);
	}
}

if ($nsg_error_found == TRUE && isset($nsg_errors[0]) == TRUE)
{
	?>
	<div class="error fade">
		<p><strong><?php echo $nsg_errors[0]; ?></strong></p>
	</div>
	<?php
}
if ($nsg_error_found == FALSE && strlen($nsg_success) > 0)
{
	?>
	  <div class="updated fade">
		<p><strong><?php echo $nsg_success; ?> <a href="<?php echo WP_NSG_ADMIN_URL; ?>"><?php _e('Click here', 'new-simple-gallery'); ?></a> <?php _e('to view the details', 'new-simple-gallery'); ?></strong></p>
	  </div>
	  <?php
	}
?>
<script type="text/javascript">
jQuery(document).ready(function($){
    $('#upload-btn').click(function(e) {
        e.preventDefault();
        var image = wp.media({ 
            title: 'Upload Image',
            // mutiple: true if you want to upload multiple files at once
            multiple: false
        }).open()
        .on('select', function(e){
            // This will return the selected image from the Media Uploader, the result is an object
            var uploaded_image = image.state().get('selection').first();
            // We convert uploaded_image to a JSON object to make accessing it easier
            // Output to the console uploaded_image
            console.log(uploaded_image);
            var img_imageurl = uploaded_image.toJSON().url;
			var img_imagetitle = uploaded_image.toJSON().title;
            // Let's assign the url value to the input field
            $('#nsg_path').val(img_imageurl);
			$('#nsg_title').val(img_imagetitle);
        });
    });
});
</script>
<?php
wp_enqueue_script('jquery'); // jQuery
wp_enqueue_media(); // This will enqueue the Media Uploader script
?>
<div class="form-wrap">
	<div id="icon-edit" class="icon32 icon32-posts-post"><br></div>
	<h2><?php _e('New Simple Gallery', 'new-simple-gallery'); ?></h2>
	<form name="nsg_form" method="post" action="#" onsubmit="return nsg_submit()"  >
      <h3><?php _e('Add new image details', 'new-simple-gallery'); ?></h3>
      <label for="tag-image"><?php _e('Enter image path (URL)', 'new-simple-gallery'); ?></label>
      <input name="nsg_path" type="text" id="nsg_path" value="" size="90" />
	  <input type="button" name="upload-btn" id="upload-btn" class="button-secondary" value="Upload Image">
      <p><?php _e('Select and upload your image.', 'new-simple-gallery'); ?> (ex: http://www.gopiplus.com/work/wp-content/uploads/pluginimages/250x167/250x167_2.jpg)</p>
      <label for="tag-link"><?php _e('Enter target link', 'new-simple-gallery'); ?></label>
      <input name="nsg_link" type="text" id="nsg_link" value="#" size="90" />
      <p><?php _e('When someone clicks on the picture, where do you want to send them', 'new-simple-gallery'); ?></p>
      <label for="tag-target"><?php _e('Select target option', 'new-simple-gallery'); ?></label>
      <select name="nsg_target" id="nsg_target">
        <option value='_blank'>_blank</option>
        <option value='_parent'>_parent</option>
        <option value='_self'>_self</option>
        <option value='_new'>_new</option>
      </select>
      <p><?php _e('Do you want to open link in new window?', 'new-simple-gallery'); ?></p>
      <label for="tag-title"><?php _e('Enter image title', 'new-simple-gallery'); ?></label>
      <input name="nsg_title" type="text" id="nsg_title" value="" size="90" />
      <p><?php _e('Enter image title. This will be the description of your image.', 'new-simple-gallery'); ?></p>
      <label for="tag-select-gallery-group"><?php _e('Select gallery group', 'new-simple-gallery'); ?></label>
		<select name="nsg_type" id="nsg_type">
			<option value='Group1'>Group1</option>
			<option value='Group2'>Group2</option>
			<option value='Group3'>Group3</option>
			<option value='Group4'>Group4</option>
			<option value='Group5'>Group5</option>
			<option value='Group6'>Group6</option>
			<option value='Group7'>Group7</option>
			<option value='Group8'>Group8</option>
			<option value='Group9'>Group9</option>
		</select>
      <p><?php _e('This is to group the images. Select your slideshow group.', 'new-simple-gallery'); ?></p>
      <label for="tag-display-status"><?php _e('Display status', 'new-simple-gallery'); ?></label>
      <select name="nsg_status" id="nsg_status">
        <option value='YES'>Yes</option>
        <option value='NO'>No</option>
      </select>
      <p><?php _e('Do you want the picture to show in your galler?', 'new-simple-gallery'); ?></p>
      <label for="tag-display-order"><?php _e('Display order', 'new-simple-gallery'); ?></label>
      <input name="nsg_order" type="text" id="nsg_order" size="10" value="1" maxlength="3" />
      <p><?php _e('What order should the picture be played in. should it come 1st, 2nd, 3rd, etc.', 'new-simple-gallery'); ?></p>
      <input name="nsg_id" id="nsg_id" type="hidden" value="">
      <input type="hidden" name="nsg_form_submit" value="yes"/>
      <p class="submit">
        <input name="publish" lang="publish" class="button action" value="<?php _e('Submit', 'new-simple-gallery'); ?>" type="submit" />
        <input name="publish" lang="publish" class="button action" onclick="nsg_redirect()" value="<?php _e('Cancel', 'new-simple-gallery'); ?>" type="button" />
        <input name="Help" lang="publish" class="button action" onclick="nsg_help()" value="<?php _e('Help', 'new-simple-gallery'); ?>" type="button" />
      </p>
	  <?php wp_nonce_field('nsg_form_add'); ?>
    </form>
</div>
</div>