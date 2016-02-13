<link rel="stylesheet" href="<?php echo get_bloginfo('url'); ?>/wp-content/plugins/s3-video/css/style.css?ver=3.5.1" type="text/css" media="all" />

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
<script type='text/javascript' src="<?php echo get_bloginfo('url'); ?>/wp-content/plugins/s3-video/js/jquery.tablesorter.js?ver=1.0"></script>
<script type='text/javascript' src="<?php echo get_bloginfo('url'); ?>/wp-content/plugins/s3-video/js/jquery.paginator.js?ver=1.0"></script>



<script type="text/javascript">
$(function() {
	  var awsBucket = '<?php echo $pluginSettings['amazon_video_bucket']; ?>';
	  jQuery("#videoListTable").tablesorter();
	  jQuery("#videoListTable").paginateTable({ rowsPerPage: <?php echo $pluginSettings['s3_video_page_result_limit']; ?>});	  
	  	  
	  jQuery(".insertVideo").click(function() {
			var videoName = jQuery(this).attr("title");
			jQuery("#insertVideoName").val(videoName);
			jQuery("#insertVideoForm").submit();
	  });
	  	  
});
</script>

<div class="wrap">

<strong>S3 Videos</strong>

<?php
	if ((!empty($existingVideos)) && (count($existingVideos) > 0)) {
?>
		<table id="videoListTable" class="tablesorter" cellspacing="0" >
			<thead>
				<tr>
					<th>File Name</th>
					<th>File Size</th>
					<th>Created</th>				
					<th>Actions</th>								
				</tr>
			</thead>
			
			<tbody>	
				<?php
					foreach($existingVideos as $existingVideo) {
						$fileExtension = strtolower(pathinfo($existingVideo['name'], PATHINFO_EXTENSION));
						$videoExtensions = array('mp4', 'mov', 'avi', 'flv', 'mpeg', 'mpg', 'wmv', '3gp', 'ogm', 'mkv', 'm4v');
						if (in_array($fileExtension, $videoExtensions)) {
				?>
							<tr>
								<td>
									<?php echo $existingVideo['name']; ?> 
								</td>
								
								<td>
									<?php echo s3_humanReadableBytes($existingVideo['size']); ?>
								</td>
								
								<td>
									<?php echo date('j/n/Y', $existingVideo['time']); ?>
								</td>
													
								<td>
									<a href="#" title="<?php echo $existingVideo['name']; ?>" class="insertVideo">
										Insert Video
									</a>												
								</td>
							</tr>
				<?php	
						}
					}
				?>
			</tbody>
		</table>
		<?php if (count($existingVideos) > $pluginSettings['s3_video_page_result_limit']) { ?>
        		<div align="center">
        			<div class='pager'>
        		        <a href='#' alt='Previous' class='prevPage'>Prev</a> - 
        		         Page <span class='currentPage'></span> of <span class='totalPages'></span>
        		         - <a href='#' alt='Next' class='nextPage'>Next</a>
        		        <br>
        		       	<span class='pageNumbers'></span>
        	   		</div>
        		</div>
    <?php } ?>
<?php 	
	} else {
?>
		<p>No media found in this bucket.</p>
<?php		
	}
?>

<form method="POST" id="insertVideoForm">
	<input type="hidden" name="insertVideoName" id="insertVideoName" value="" />
</form>

</div>

