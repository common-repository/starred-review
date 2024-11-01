<?php

 	if (isset($_POST['submitted'])){
		$post_values = $_POST;
		unset($post_values['submitted']);
		unset($post_values['Submit']);
		if ($post_values != $sr_values ){
            if ($post_values['rating_scale'] != $sr_values['rating_scale'] || $post_values['rating_interval'] != $sr_values['rating_interval']){
				$srfunc->recalculate_ratings($sr_values['rating_scale'], $post_values['rating_scale'], $post_values['rating_interval']);
  			}
		    $srdb->update_option('starred_review_settings', $post_values);
		    $sr_values = $srdb->get_options();
		}

 	    _e('<div class="updated"><p><strong>Settings updated.</strong></p></div>');
	}

?>
<?php
/*
get the list of directories in the 'images' folder that contain rating images
*/
$t = explode('/', dirname(__FILE__));
$stars_path = implode('/', $t).'/images';
	if(is_dir($stars_path)){
	  	$dir = opendir($stars_path);
	  	$image_dirs = array();
		while ($img_dir = readdir($dir)){
			if ($img_dir != '.' && $img_dir != '..'){
			  	if (is_dir("$stars_path/$img_dir")) {
			  	  	$exts = array('jpg', 'jpeg', 'gif', 'png');
			  	  	foreach ($exts as $ext){
				  	  	if(file_exists("$stars_path/$img_dir/0.0.".$ext) && file_exists("$stars_path/$img_dir/0.5.".$ext) && file_exists("$stars_path/$img_dir/1.0.".$ext)){
							$image_dirs[$img_dir] = array('dir' => $img_dir, 'ext' =>$ext);
							break;
						}
					}//end for each ext
				}//end if is dir
			}//end if is no . or ..
		}//end while
		ksort($image_dirs);
	}//end if
?>



<div class="wrap" align="center">

<form action="<?php echo $_SERVER['PHP_SELF']; ?>?page=starred-review/starred-review.php&amp;srpage=settings" method="post">

<table width="100%" cellspacing="2" cellpadding="5" class="editform" summary="starred review settings">
<tr valign="top">
	<th scope="row" width="33%"><label for="default_sort">Default Sort for Main Page:</label></th>
	<td><select name="default_sort">
		<?php $selected = array($sr_values['default_sort'] => ' selected');?>
		<option value="date" <?php echo $selected['date']; ?>>Date</option>
		<option value="category" <?php echo $selected['category']; ?>>Category</option>
		<option value="title" <?php echo $selected['title']; ?>>Alphabetical</option>
		<option value="rating" <?php echo $selected['rating']; ?>>Rating</option>
	</select>
	<br />The order the reviews appear in when <code>&lt;?php starred_review(); ?&gt;</code> is called from a page template.</td>
</tr>
<tr valign="top">
	<th scope="row" width="33%"><label for="display_titles">Display Titles:</label></th>
	<td><select name="display_titles">
		<?php $selected = array($sr_values['display_titles'] => ' selected'); ?>
		<option value="1" <?php echo $selected['1']; ?>>YES</option>
		<option value="0" <?php echo $selected['0']; ?>>NO</option>
	</select>
	<br />When the <code>&lt;?php starred_review(); ?&gt;</code> it will split the reviews into sections, by category, date, etc, to you want a title for each section of the list?.</td>
</tr>
<tr valign="top">
	<th scope="row" width="33%"><label for="image_location">Image Location:</label></th>
	<td><select name="image_location">
		<?php 
		$selected = array($sr_values['image_location'] => ' selected'); 
		foreach ($image_dirs as $dir){
			_e('<option value="' . $dir["dir"] . '**' . $dir["ext"] .'"' . $selected[$dir["dir"] . '**' . $dir["ext"]] . '>' . $dir['dir'] . '</option>' . "\n");
			//print_r($dir);
		}
		?>
	</select>
	<br />The location of the images to be used to show the review's rating.</td><?php //'?>
</tr>
</table>

<fieldset class="options">
<legend>Rating Scale</legend>
<table width="100%" cellspacing="2" cellpadding="5" class="editform" summary="starred review settings">
<tr valign="top">
	<td colspan="2">
		It is not recomended that you change after you have initially set it, this is because changing it will affect the values of your current reviews.
	</td>
</tr>
<tr valign="top">
	<th scope="row" width="33%"><label for="rating_scale">Rating Scale:</label></th>
	<td><select name="rating_scale">
		<?php $selected = array($sr_values['rating_scale'] => ' selected');
		for($i=3;$i<=10;$i++)
            _e("<option value=\"$i\" ".$selected[$i].">$i</option>");
		?>
	</select>
	<br />The rating scale will be from zero to this number.</td>
</tr>
<tr valign="top">
	<th scope="row" width="33%"><label for="rating_interval">Rating Interval:</label></th>
	<td><select name="rating_interval">
		<?php $selected = array($sr_values['rating_interval'] => ' selected'); ?>
		<option value="0.5" <?php echo $selected['0.5']; ?>>0.5</option>
		<option value="1.0" <?php echo $selected['1.0']; ?>>1.0</option>
	</select>
	<br />The rating scale will go increase by this amount each time.</td>
	<br />
	
	<p class="submit">
	<input type="hidden" name="submitted" />
	<input type="hidden" name="table_prefix" value="<?php echo $sr_values['table_prefix']; ?>"/>
	<input type="hidden" name="version" value="<?php echo $sr_values['version']; ?>"/>
	<input type="submit" name="Submit" value="Update Settings &raquo;" />
	</p>

</tr>
</table>
</fieldset>
</form>
</div>