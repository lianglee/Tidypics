<?php
/**
 * Tidypics basic uploader form
 *
 * This only handled uploading the images. Editing the titles and descriptions
 * are handled with the edit forms.
 *
 * @uses $vars['album']
 */

global $CONFIG;

$album = $vars['album'];
$access_id = $album->access_id;

$maxfilesize = (float) get_plugin_setting('maxfilesize','tidypics');
if (!$maxfilesize) {
	$maxfilesize = 5;
}

$quota = get_plugin_setting('quota','tidypics');
if ($quota) {
	$image_repo_size_md = get_metadata_byname($album->container_guid, "image_repo_size");
	$image_repo_size = (int)$image_repo_size_md->value;
	$image_repo_size = $image_repo_size / 1024 / 1024;
	$quote_percentage = round(100 * ($image_repo_size / $quota));
	// for small quotas, so one decimal place
	if ($quota < 10) {
		$image_repo_size = sprintf('%.1f', $image_repo_size);
	} else {
		$image_repo_size = round($image_repo_size);
	}
	if ($image_repo_size > $quota) {
		$image_repo_size = $quota;
	}
}

?>
<div id="tidypics_ref"></div>
<div class="contentWrapper">
	<?php
	ob_start();
	?>
	<p style="line-height:1.6em;">
		<label><?php echo elgg_echo("tidypics:uploader:upload"); ?></label><br />
		<i><?php echo sprintf(elgg_echo('tidypics:uploader:basic'), $maxfilesize); ?></i><br />
		<?php
		if ($quota) {
			?>
		<i><?php echo elgg_echo("tidypics:quota") . ' ' . $image_repo_size . '/' . $quota . " MB ({$quote_percentage}%)"; ?></i><br />
			<?php
		}
		?>
	<div class="tidypics_popup">
		<?php echo elgg_echo("tidypics:uploading:images"); ?><br />
		<div style="margin:20px 0px 20px 80px;"><img id="progress" alt="..." border="0" src="<?php echo $vars['url'].'mod/tidypics/graphics/loader.gif' ?>" /></div>
	</div>
	<ol id="tidypics_image_upload_list">
		<?php
		for ($x = 0; $x < 10; $x++) {
			echo '<li>' . elgg_view("input/file",array('internalname' => "upload_$x")) . '</li>';
		}
		?>
	</ol>
</p>
<p>
	<?php
	if ($album) {
		echo '<input type="hidden" name="album_guid" value="' . $album->guid . '" />';
	}
	if ($access_id) {
		echo '<input type="hidden" name="access_id" value="' . $access_id . '" />';
	}
	?>
	<input type="submit" value="<?php echo elgg_echo("save"); ?>" onclick="displayProgress();" />
</p>
<?php
$form_body = ob_get_clean();
echo $form_body;

?>
</div>
<script type="text/javascript">

	function displayProgress()
	{
		offsetY = 60;
		offsetX = 120;

		divWidth = $('#tidypics_ref').width();
		imgOffset = $('#tidypics_ref').offset();
		imgWidth  = $('#tidypics_ref').width();

		_top = imgOffset.top + offsetY;
		_left = imgOffset.left + offsetX;

		$('.tidypics_popup').show().css({
			"top": _top + "px",
			"left": _left + "px"
		});

		setTimeout('document.images["progress"].src = "<?php echo $vars['url'].'mod/tidypics/graphics/loader.gif' ?>"', 200);
	}
</script>