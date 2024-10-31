<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<?php
// Form submitted, check the data
if (isset($_POST['frm_nsg_display']) && $_POST['frm_nsg_display'] == 'yes')
{
	$did = isset($_GET['did']) ? intval($_GET['did']) : '0';
	if(!is_numeric($did)) { die('<p>Are you sure you want to do this?</p>'); }
	
	$nsg_success = '';
	$nsg_success_msg = FALSE;
	
	// First check if ID exist with requested ID
	$sSql = $wpdb->prepare(
		"SELECT COUNT(*) AS `count` FROM ".WP_NSG_TABLE."
		WHERE `nsg_id` = %d",
		array($did)
	);
	$result = '0';
	$result = $wpdb->get_var($sSql);
	
	if ($result != '1')
	{
		?><div class="error fade"><p><strong><?php _e('Oops, selected details doesnt exist', 'new-simple-gallery'); ?></strong></p></div><?php
	}
	else
	{
		// Form submitted, check the action
		if (isset($_GET['ac']) && $_GET['ac'] == 'del' && isset($_GET['did']) && $_GET['did'] != '')
		{
			//	Just security thingy that wordpress offers us
			check_admin_referer('nsg_form_show');
			
			//	Delete selected record from the table
			$sSql = $wpdb->prepare("DELETE FROM `".WP_NSG_TABLE."`
					WHERE `nsg_id` = %d
					LIMIT 1", $did);
			$wpdb->query($sSql);
			
			//	Set success message
			$nsg_success_msg = TRUE;
			$nsg_success = __('Selected record was successfully deleted.', 'new-simple-gallery');
		}
	}
	
	if ($nsg_success_msg == TRUE)
	{
		?><div class="updated fade"><p><strong><?php echo $nsg_success; ?></strong></p></div><?php
	}
}
?>
<div class="wrap">
  <div id="icon-edit" class="icon32 icon32-posts-post"></div>
    <h2><?php _e('New Simple Gallery', 'new-simple-gallery'); ?><a class="add-new-h2" href="<?php echo WP_NSG_ADMIN_URL; ?>&amp;ac=add"><?php _e('Add New', 'new-simple-gallery'); ?></a></h2>
    <div class="tool-box">
	<?php
		$sSql = "SELECT * FROM `".WP_NSG_TABLE."` order by nsg_type, nsg_order";
		$myData = array();
		$myData = $wpdb->get_results($sSql, ARRAY_A);
		?>
		<form name="frm_nsg_display" method="post">
      <table width="100%" class="widefat" id="straymanage">
        <thead>
          <tr>
		  	<th scope="col"><?php _e('Title', 'new-simple-gallery'); ?></th>
			<th scope="col"><?php _e('Group', 'new-simple-gallery'); ?></th>
			<th scope="col"><?php _e('Image', 'new-simple-gallery'); ?></th>
            <th scope="col"><?php _e('URL', 'new-simple-gallery'); ?></th>
            <th scope="col"><?php _e('Order', 'new-simple-gallery'); ?></th>
            <th scope="col"><?php _e('Display', 'new-simple-gallery'); ?></th>
          </tr>
        </thead>
		<tfoot>
          <tr>
		    <th scope="col"><?php _e('Title', 'new-simple-gallery'); ?></th>
			<th scope="col"><?php _e('Group', 'new-simple-gallery'); ?></th>
			<th scope="col"><?php _e('Image', 'new-simple-gallery'); ?></th>
            <th scope="col"><?php _e('URL', 'new-simple-gallery'); ?></th>
            <th scope="col"><?php _e('Order', 'new-simple-gallery'); ?></th>
            <th scope="col"><?php _e('Display', 'new-simple-gallery'); ?></th>
          </tr>
        </tfoot>
		<tbody>
		<?php 
		$i = 0;
		if(count($myData) > 0 )
		{
			foreach ($myData as $data)
			{
				?>
				<tr class="<?php if ($i&1) { echo'alternate'; } else { echo ''; }?>">
					<td>
					<?php echo esc_html(stripslashes($data['nsg_title'])); ?>
					<div class="row-actions">
						<span class="edit"><a title="Edit" href="<?php echo WP_NSG_ADMIN_URL; ?>&ac=edit&amp;did=<?php echo $data['nsg_id']; ?>"><?php _e('Edit', 'new-simple-gallery'); ?></a> | </span>
						<span class="trash"><a onClick="javascript:nsg_delete('<?php echo $data['nsg_id']; ?>')" href="javascript:void(0);"><?php _e('Delete', 'new-simple-gallery'); ?></a></span> 
					</div>
					</td>
					<td><?php echo esc_html(stripslashes($data['nsg_type'])); ?></td>
					<td><a href="<?php echo esc_html($data['nsg_path']); ?>" target="_blank"><img src="<?php echo plugins_url( 'new-simple-gallery/inc/image-icon.png'); ?>"  /></a></td>
					<td>
					<?php if ($data['nsg_link'] <> '#' and $data['nsg_link'] <> '') { ?>
						<a href="<?php echo esc_html($data['nsg_link']); ?>" target="_blank"><img src="<?php echo plugins_url( 'new-simple-gallery/inc/link-icon.gif'); ?>"  /></a>
					<?php } else { ?>
						<img src="<?php echo plugins_url( 'new-simple-gallery/inc/link-icon.gif'); ?>"  />
					<?php } ?>
					</td>
					<td><?php echo esc_html(stripslashes($data['nsg_order'])); ?></td>
					<td><?php echo esc_html(stripslashes($data['nsg_status'])); ?></td>
				</tr>
				<?php 
				$i = $i+1; 
			} 
		}
		else
		{
			?><tr><td colspan="6" align="center"><?php _e('No records available', 'new-simple-gallery'); ?></td></tr><?php 
		}
		?>
		</tbody>
        </table>
		<?php wp_nonce_field('nsg_form_show'); ?>
		<input type="hidden" name="frm_nsg_display" value="yes"/>
      </form>	
	  <div class="tablenav bottom">
	  <a href="<?php echo WP_NSG_ADMIN_URL; ?>&amp;ac=add"><input class="button action" type="button" value="<?php _e('Add New', 'new-simple-gallery'); ?>" /></a>
	  <a target="_blank" href="http://www.gopiplus.com/work/2010/08/07/new-simple-gallery/"><input class="button action" type="button" value="<?php _e('Help', 'new-simple-gallery'); ?>" /></a>
	  <a target="_blank" href="http://www.gopiplus.com/work/2010/08/07/new-simple-gallery/"><input class="button button-primary" type="button" value="<?php _e('Short Code', 'new-simple-gallery'); ?>" /></a>
	  </div>
	</div>
</div>