<?php

	if(isset($_POST['submitted']) && $_POST['cat_name'] != ''){
		$srdb->update_category(new sr_info_cat());
		if (strtolower($_POST['cat_action']) == 'edit')
			$msg = 'Category successfully editted.';
		elseif (strtolower($_POST['cat_action']) == 'add')
			$msg = 'Category successfully added.';
	}
	elseif($_GET['action'] == 'edit'){
        $sr_cat = $srdb->get_category($_GET['cat_id']);
	}
	elseif($_GET['action'] == 'delete'){
        $sr_cat = $srdb->get_category($_GET['cat_id']);
		if ($_GET['cat_id'] == 1)
		    $msg = "Sorry, but the '$sr_cat->name' can't be deleted, it is the default category.";
		else{
			$msg = "'$sr_cat->name' successfully deleted.";
			$srdb->delete_category($sr_cat->id);
		}
		unset($sr_cat);
 	}//end pre load actions
 	
 	if (isset($msg))
 	    _e("<div class=\"updated\"><p><strong>$msg</strong></p></div>");
?>

<?php if ($_GET['action'] != 'edit'){
	$edit_cat = FALSE;
	?>
	<div class="wrap" align="center">

		<table width="auto" cellpadding="10" cellspacing="5">
			<tr>
				<th valign="bottom">Name</th>
				<th valign="bottom">ID</th>
				<th valign="bottom">Items</th>
				<th valign="bottom">&nbsp;</th>
				<th valign="bottom">Edit</th>
			</tr>
		
		<?php
		$cats = $srdb->get_categories();
		foreach ($cats as $i => $cat){
			$alternate = ($i % 2 == 0) ? ' class="sr-alternate"' : '';
		    _e("<tr $alternate>"
				."<td class=\"sr-title\">$cat->name</td>"
				."<td>$cat->id</td>"
				."<td class=\"sr-title\">$cat->count</td>"
				."<td><a href=\"$_SERVER[PHP_SELF]?page=starred-review/starred-review.php&amp;srpage=review-cat&amp;action=edit&amp;cat_id=$cat->id\" class=\"edit\">edit</a></td>\n"
				."<td><a href=\"$_SERVER[PHP_SELF]?page=starred-review/starred-review.php&amp;srpage=review-cat&amp;action=delete&amp;cat_id=$cat->id\" class=\"delete\">delete</a></td></tr>\n\n");
		}
		?>
		</table>

	</div>

<?php }

else $edit_cat = TRUE;?>

<div class="wrap" align="center">
	
		<form name="add-category" action="<?php $_SERVER[PHP_SELF];?>?page=starred-review/starred-review.php&amp;srpage=review-cat" method="post">
		<input type="hidden" name="cat_id" value="<?php if(isset($sr_cat))echo $sr_cat->id; ?>" />
		<input type="hidden" name="cat_action" value="<?php echo $cat_action; ?>" />
		
	New category: 

	<input name="cat_name" type="text" size="20" value="<?php if(isset($sr_cat))echo $sr_cat->name; ?>" class="code"/>
	
	<span class="submit">
	<input type="hidden" name="submitted" />
	<input type="submit" name="Submit" value="<?php _e($cat_action);?> Add &raquo;" />
	</span>
	</form>

</div>