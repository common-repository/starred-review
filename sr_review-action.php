<?php
	if(isset($_POST['submitted']) && $_POST['rev_title'] != ''){
		$t = new sr_info_review();
		$srdb->update_review($t);
		if (strtolower($_POST['rev_action']) == 'edit')
			$msg = 'Review successfully editted.';
		elseif (strtolower($_POST['rev_action']) == 'add')
		$msg = 'Review successfully added.';
	}
	if($_GET['action'] == 'edit'){
        $sr_rev = $srdb->get_review($_GET['rev_id']);
        $rev_action = 'Edit';
	}else
	    $rev_action = 'Add';
	    
	if (isset($msg))
		_e("<div class=\"updated\"><p><strong>$msg</strong></p></div>");
		
?>

<div class="wrap" align="center">
    <?php
	_e();
	$formpage = ($rev_action == 'Add') ? 'action': 'man';
	?>
	<form name="add-review" action="<?php $_SERVER[PHP_SELF];?>?page=starred-review/starred-review.php&amp;srpage=review-<?php echo $formpage; ?>" method="post">
	<input type="hidden" name="rev_id" value="<?php if(isset($sr_rev))echo $sr_rev->id; ?>" />
	<input type="hidden" name="rev_action" value="<?php echo strtolower($rev_action); ?>" />

	<table width="auto" cellspacing="2" cellpadding="5" class="editform" summary="add a new review">
		<tr valign="top">
			<th scope="row" width="125px" valign="middle" class="sr-title">Review Title:</th>
			<td><input name="rev_title" type="text" size="40" value="<?php if(isset($sr_rev))echo $sr_rev->title; ?>" class="code"/>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" width="125px" valign="middle" class="sr-title">Rating:</th>
			<td><select name="rev_rating">
			    <?php
			    $max = $sr_values['rating_scale'];
			    $interval = $sr_values['rating_interval'];
				for($i=0;$i<=$max;$i += $interval){
					$selected = ($sr_rev->rating == $i) ? ' selected': '';
                    _e("<option value=\"$i\"$selected>$i</option>\n");
				}//for each value in the the rating
				?>
			</select>
			
			<br /><br/>
			
			</td>
		<tr valign="top">
			<th scope="row" width="125px" valign="middle" class="sr-title">Category:</th>
			<td><select name="rev_cat">
			    <?php
       			$cats = $srdb->get_categories();
				foreach($cats as $cat){
					$selected = ($sr_rev->category == $cat->id) ? ' selected': '';
                    _e("<option value=\"$cat->id\"$selected>$cat->name</option>\n");
				}//for each value in the the rating
				?>
			</select>
			
			<br /><br/>
			
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" width="125px" valign="middle" class="sr-title">Edit time:</th>
			<td><select name="rev_month">
			
			    <?php
					if ($rev_action == 'Add') $date = date('Y-m-d H:i:s');
					else $date = $sr_rev->date;
			    	$months = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');
					foreach($months as $month){
						$selected = ($srfunc->get_date_part('month', $date) == $month) ? '  selected': '';
	                    _e("<option value=\"$month\"$selected>".$srfunc->get_month_name($month)."</option>\n");
					}//for each value in the the rating
				?>
				</select>
				<input type="text" name="rev_day" value="<?php echo $srfunc->get_date_part('day', $date); ?>" size="2" maxlength="2" />
				<input type="text" name="rev_year" value="<?php echo $srfunc->get_date_part('year', $date); ?>" size="4" maxlength="5" /> @
				<input type="text" name="rev_hour" value="<?php echo $srfunc->get_date_part('hour', $date); ?>" size="2" maxlength="2" /> :
				<input type="text" name="rev_minute" value="<?php echo $srfunc->get_date_part('minute', $date); ?>" size="2" maxlength="2" />
				<input type="hidden" name="rev_second" value="<?php echo $srfunc->get_date_part('second', $date); ?>" size="2" maxlength="2" />
				<?php if($rev_action == 'Edit'){?>
				<br />Existing timestamp:	<?php _e($srfunc->get_month_name($srfunc->get_date_part('month', $date)).' '.$srfunc->get_date_part('day', $date).', '.$srfunc->get_date_part('year', $date).' @ '.$srfunc->get_date_part('hour', $date).':'.$srfunc->get_date_part('minute', $date)); ?>
				<?php } ?>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" width="125px" valign="middle" class="sr-title">Review Link:</th>
			<td><input name="rev_link" type="text" size="40" value="<?php if(isset($sr_rev))echo $sr_rev->link; ?>" class="code"/>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row" width="125px" valign="top" class="sr-title">Product Link:		
			</th>
			<td><input name="rev_prod_link" type="text" size="40" value="<?php if(isset($sr_rev))echo $sr_rev->prod_link; ?>" class="code"/>
			<br />...could be <a href="http://www.amazon.com/">Amazon</a>, <a href="http://www.imdb.com/">IMDB</a>, <a href="http://www.rottentomatoes.com/">Rotten Tomatoes</a>, etc.
			
			<p class="submit"><input type="hidden" name="submitted" /><input type="submit" name="Submit" value="<?php _e($rev_action);?> Review &raquo;" /></p>
			
			</td>
		</tr>
		
	</table>
	</form>
</div><!--wrap-->
