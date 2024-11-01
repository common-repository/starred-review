<?php

	if(isset($_POST['submitted']) && $_POST['rev_title'] != ''){
		$t = new sr_info_review();
		$srdb->update_review($t);
		if (strtolower($_POST['rev_action']) == 'edit')
			$msg = 'Review successfully editted.';
		elseif (strtolower($_POST['rev_action']) == 'add')
			$msg = 'Review successfully added.';
	}
	elseif($_GET['action'] == 'delete'){
        $sr_rev = $srdb->get_review($_GET['rev_id']);
		$msg = "'$sr_rev->title' review successfully deleted.";
		$srdb->delete_review($sr_rev->id);
		unset($sr_rev);
 	}//end pre load actions
 	
 	if (isset($msg))
 	    _e("<div class=\"updated\"><p><strong>$msg</strong></p></div>");

	$sorted_cats = array();
	$unsorted_cats = $srdb->get_categories();
	foreach($unsorted_cats as $cat)
        $sorted_cats[$cat->id] = $cat->name;
	ksort($sorted_cats);

 	switch ($_POST['order_by']){
		case 'date-asc':
		    $revs = $srdb->get_reviews($_POST['cat_id']);
		    $revs = array_reverse($revs);
		    break;
		case 'title':
		    $revs = $srdb->get_reviews($_POST['cat_id'],'','rev_title');
		    break;
		case 'rating-desc':
			$revs = $srdb->get_reviews($_POST['cat_id'],'','rev_rating');
			$revs = array_reverse($revs);
		    break;
		case 'rating-asc':
   			$revs = $srdb->get_reviews($_POST['cat_id'],'','rev_rating');
		    break;
		default:
		    $revs = $srdb->get_reviews($_POST['cat_id']);
			break;
 	}//end switch

?>

<div class="wrap" style="padding-bottom: 20px;">

    <form name="cats" method="post" action="<?php $_SERVER[PHP_SELF];?>?page=starred-review/starred-review.php&amp;srpage=review-man">
    <div align="center">

		 <!--<td align="center">
			<strong>Show</strong> reviews in category:<br />
		</td>-->

			<strong>Order</strong> by:

		    <?php
		    $order = array(
		                'date-desc' => ' Date (newest first)',
		                'date-asc' => ' Date (oldest first)',
		                'title' => ' Alphabetical',
		                'rating-desc' => ' Rating (highest first)',
		                'rating-asc' => ' Rating (lowst first)'
						);
		    ?>

		<!-- <tr><td>
			<select name="cat_id">
				<option value=""> All</option>
			    <?php
				foreach($unsorted_cats as $cat){
					$selected = ($_POST['cat_id'] == $cat->id) ? ' selected': '';
                    _e("<option value=\"$cat->id\"$selected> $cat->name</option>");
				}//for each value in the the rating
				?>
			</select>
		</td>-->

			<select name="order_by">
				<?php
				foreach($order as $k => $o_item){
					$selected = ($k == $_POST['order_by']) ? 'selected': '';
			        _e("<option value=\"$k\" $selected>$o_item</option>\n");
				}
				?>
			</select>
			
			<span class="submit"><input type="submit" name="action" value="Show" /></span>

    </div>
    </form>

</div><!--wrap-->

<div class="wrap" align="center">
	<form method="post" action="<?php $_SERVER[PHP_SELF];?>?page=starred-review/starred-review.php&amp;srpage=review-man">
		<input type="hidden" name="rev_id" value="" />
		<input type="hidden" name="action" value="" />
		<input type="hidden" name="order_by" value="" />
		<input type="hidden" name="cat_id" value="" />

		<table width="auto" cellpadding="10" cellspacing="5">
	    <tr>
			<!-- <th scope="col">ID</th> -->
			<!-- <th scope="col">Date</th> -->
			<td scope="col" class="sr-title"><strong>Title</strong></td>
			<td scope="col" align="center" width="auto"><strong>Rating</strong></td>
			<td scope="col" align="center" width="150px"><strong>Category</strong></td>
			<!-- <th scope="col">Review</th> -->
			<!-- <th scope="col">Product Link</th> -->
			<td scope="col"></td>
			<td scope="col"><strong>Action</strong></td>
		</tr>
		<?php
		if (!empty($revs)){
			$i=0;
  			foreach ($revs as $rev){
				$alternate = ($i % 2 == 0) ? 'class="sr-alternate"' : '';
				$i++;
				$cat = $sorted_cats[$rev->category];
				$has_review = (empty($rev->link)) ? '': '<a href="'.$rev->link.'" class="edit">view</a>';
				$has_prod_link = (empty($rev->prod_link)) ? '': '<a href="'.$rev->prod_link.'" class="edit">view</a>';
			    _e("\n<tr width=\"auto\" align=\"right\" $alternate>"
					/* ."<td>$rev->id</td>" */
					/* .'<td>'.str_replace(' ', '<br />', $rev->date).'</td>' */
					."<td class=\"sr-review-title\">$rev->title</td>\n"
					.'<td>'.$srfunc->rating2stars($rev->rating)."</td>\n" //"<td>$rev->rating</td>"
					."<td  align=\"center\">$cat</td>\n"
					/* ."<td>$has_review</td>" */
					/* ."<td>$has_prod_link</td>" */
					."<td><a href=\"$_SERVER[PHP_SELF]?page=starred-review/starred-review.php&amp;srpage=review-action&amp;action=edit&amp;rev_id=$rev->id\" class=\"edit\">edit</a></td> \n"
					."<td><a href=\"$_SERVER[PHP_SELF]?page=starred-review/starred-review.php&amp;srpage=review-man&amp;action=delete&amp;rev_id=$rev->id\" class=\"delete\">delete</a></td></tr> \n \n");
			}
		}//end if $revs not empty
		?>
		</table>
	</form>
</div><!--wrap-->

