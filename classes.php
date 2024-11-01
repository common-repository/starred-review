<?php

/**********************************************************
***********************************************************
Class: sr_info_cat
***********************************************************
**********************************************************/

/*
Starred Review category
*/
class sr_info_cat{

	var $name;
	var $id;
	var $count;

	function sr_info_cat($db_result=null){
		global $srdb;
		if (!isset($srdb)) $srdb = new SRDB();
		if ($db_result != null)$arr = $db_result;
		else $arr = $_POST;

		$this->name = $this->xhtml_safe($arr['cat_name']);
		$this->id = $arr['cat_id'];
		$this->count = (!empty($this->id)) ? $srdb->count_reviews($this->id): 0 ;

 	} /* end constructor */

	function xhtml_safe($str){
		$str = str_replace('"', '&quot;', $str);
		return $str;
	} /* end funtion: xhtml_safe*/

}/* end class: sr_info_cat */

/**********************************************************
***********************************************************
Class: sr_info_review
***********************************************************
**********************************************************/

/*
Starred Review review
*/
class sr_info_review{

	var $id;
	var $title;
	var $rating;
	var $category;
	var $date;
	var $link;

	function sr_info_review($db_result=null){
		if ($db_result != null)$arr = $db_result;
		else $arr = $_POST;

      	$this->id = $arr['rev_id'];
		$this->title = $this->xhtml_safe($arr['rev_title']);
		$this->rating = $arr['rev_rating'];
		$this->category = $arr['rev_cat'];
		$this->link = $arr['rev_link'];
		$this->prod_link = $arr['rev_prod_link'];

		if ($db_result != null) {
            $this->date = $arr['rev_date'];
		}else{
			$aa = $_POST['rev_year'];
			$mm = $_POST['rev_month'];
			$jj = $_POST['rev_day'];
			$hh = $_POST['rev_hour'];
			$mn = $_POST['rev_minute'];
			$ss = $_POST['rev_second'];
			$jj = ($jj > 31) ? 31 : $jj;
			$hh = ($hh > 23) ? $hh - 24 : $hh;
			$mn = ($mn > 59) ? $mn - 60 : $mn;
			$ss = ($ss > 59) ? $ss - 60 : $ss;
			$this->date = "$aa-$mm-$jj $hh:$mn:$ss";
		}

	} /* end constructor */

	function xhtml_safe($str){
		$str = str_replace('"', '&quot;', $str);
		return $str;
	} /* end funtion: xhtml_safe*/

}/* end class: sr_info_review */


/**********************************************************
***********************************************************
Class: SRDB
***********************************************************
**********************************************************/

/*
Starred Review database handler
*/
class SRDB{

	var $reviews_tbl;
	var $categories_tbl;

	function SRDB(){
		$options = $this->get_options();
		$this->reviews_tbl = $options['table_prefix'].'reviews';
		$this->categories_tbl = $options['table_prefix'].'categories';
	} /* end constructor */


	/****************************
	review table functions
	****************************/

	function count_reviews($cat_id='all'){
        global $wpdb;
		$param = ($cat_id!='all') ? "WHERE `rev_cat` =$cat_id" : '';
		$sql = "SELECT COUNT( * ) AS count FROM `$this->reviews_tbl` $param";
		$result = $wpdb->get_row($sql);
		return $result->count;
 	} /* end function: count_reviews */

	/*
	this will update the review for the sr_info_review class passed, if it doesn't exist it will created
	*/
	function update_review($reviewinfo){
        global $wpdb;

		if(!empty($reviewinfo->id)){
			$sql = "SELECT rev_title FROM $this->reviews_tbl WHERE rev_id=$reviewinfo->id LIMIT 1";
			$result = $wpdb->get_row($sql, ARRAY_A);
		}else
		    $result=null;

		if ($result==null)
		    $sql2 = "INSERT INTO `$this->reviews_tbl` "
				."( `rev_id` , `rev_title` , `rev_rating` , `rev_cat` , `rev_date`, `rev_link`, `rev_prod_link`) VALUES "
				."('', '$reviewinfo->title', '$reviewinfo->rating', '$reviewinfo->category', '$reviewinfo->date', '$reviewinfo->link', '$reviewinfo->prod_link')";
		else
		    $sql2 = "UPDATE `$this->reviews_tbl` SET "
				."`rev_title` = '$reviewinfo->title', "
				."`rev_cat` = '$reviewinfo->category', "
				."`rev_rating` = '$reviewinfo->rating', "
				."`rev_date` = '$reviewinfo->date', "
				."`rev_link` = '$reviewinfo->link', "
				."`rev_prod_link` = '$reviewinfo->prod_link' "
				."WHERE `rev_id` =$reviewinfo->id LIMIT 1";
        $wpdb->query($sql2);

	} /* end function: update_review */

	/*
	this will return all the reviews in the form of sr_info_review, you can specify from which category, if not, will return them all
	*/
	function get_reviews($cat_id=-1, $rating=-1, $sort_by='rev_date DESC'){
        global $wpdb;
        //echo "before:$rating ";
        $cat_id = ($cat_id == '') ? -1 : $cat_id;
        $rating = (empty($rating) && $rating != '0') ? -1 : $rating;
        //echo "after:$rating ";

        if ($cat_id >= 0 && $rating >= 0)
            $criteria = "WHERE `rev_cat`=$cat_id AND `rev_rating`=$rating";
        elseif ($cat_id >= 0)
        	$criteria = "WHERE `rev_cat`=$cat_id";
        elseif ($rating >= 0)
        	$criteria = "WHERE `rev_rating`=$rating";
		else
		    $cirteria = '';

		$sql = "SELECT $this->reviews_tbl.*, $this->categories_tbl.cat_name FROM $this->categories_tbl INNER JOIN $this->reviews_tbl ON $this->categories_tbl.cat_id = $this->reviews_tbl.rev_cat $criteria ORDER BY $sort_by";

		//$sql = "SELECT * FROM $this->reviews_tbl $criteria ORDER BY rev_$sort_by";
	    $results = $wpdb->get_results($sql, ARRAY_A);
	    //echo "$sql<br>".count($results)."<br>";
	    $revs = array();
		if(!empty($results)){
		    foreach ($results as $result)
				$revs[] = new sr_info_review($result);
		}//end if not empty
	    return $revs;
	} /* end function: get_reviews */

	/*
	this will return the review of the given ID in the form of sr_info_review
	*/
	function get_review($rev_id){
        global $wpdb;
		$sql = "SELECT * FROM $this->reviews_tbl WHERE `rev_id`=$rev_id";
		return (new sr_info_review($wpdb->get_row($sql, ARRAY_A)));
	} /* end function: get_review */

	/*
	will delete the review of the given ID
	*/
	function delete_review($rev_id){
		global $wpdb;
		$sql = "DELETE FROM $this->reviews_tbl WHERE `rev_id`=$rev_id LIMIT 1";
		$wpdb->query($sql);
	} /* end function: delete_review */


	/****************************
	category table functions
	****************************/

	/*
	this will update the category for the sr_info_cat class passed, if it doesn't exist it will created
	*/
	function update_category($catinfo){
		global $wpdb;

		if(!empty($catinfo->id)){
			$sql = "SELECT cat_name FROM $this->categories_tbl WHERE cat_id=$catinfo->id LIMIT 1";
			$result = $wpdb->get_row($sql, ARRAY_A);
		}else
		    $result=null;

		if ($result==null)
		    $sql2 = "INSERT INTO $this->categories_tbl ( `cat_id` , `cat_name`) VALUES ( '', '$catinfo->name')";
		else
		    $sql2 = "UPDATE $this->categories_tbl SET `cat_name` = '$catinfo->name' WHERE cat_id=$catinfo->id LIMIT 1 ;";
		$wpdb->query($sql2);

 	} /* end function: update_category */

 	/*
	this will return all the categories in the form of sr_info_cat
	*/
 	function get_categories($sort_by='id'){
		global $wpdb;
		$sql = "SELECT * FROM $this->categories_tbl ORDER BY cat_$sort_by";
	    $results = $wpdb->get_results($sql, ARRAY_A);
	    $cats_temp = array();
	    foreach ($results as $result)
	        $cats_temp[] = new sr_info_cat($result);

		$cats = array();

		foreach($cats_temp as $cat)
		    $cats[$cat->$sort_by] = $cat;

		ksort($cats);
	    return $cats;
  	}

 	/*
	this will return the category in the form of sr_info_cat
	*/
 	function get_category($cat_id){
		global $wpdb;
		$sql = "SELECT * FROM $this->categories_tbl WHERE `cat_id`=$cat_id";
		return (new sr_info_cat($wpdb->get_row($sql, ARRAY_A)));
  	} /* end function: get_category */

 	/*
	this will delete all the categories
	*/
 	function delete_category($cat_id){
		global $wpdb;
		$sql = "DELETE FROM $this->categories_tbl WHERE `cat_id`=$cat_id LIMIT 1";
		$wpdb->query($sql);
		$reviews = $this->get_reviews($cat_id);
		if(!empty($reviews)){
			foreach($reviews as $review){
				$review->category = 1;
				$this->update_review($review);
			}
		}//end if have reviews
  	} /* end function: delete_category */

	/****************************
	option table functions
	****************************/

	/*
	sets the option value from the SR option menu, if it doesn't exist it will created
	*/
	function update_option($option, $value=''){
		return update_option($option, $value);
	} /* end get_option */

	/*
	gets the option value from the SR option menu, if it doesn't exist it returns null
	*/
	function get_options(){
		return get_option('starred_review_settings');
	} /* end get_option */

} /* end class: SRDB */

class sr_functions{

	var $srdb;

	function sr_functions($srdb=null){
		$this->srdb = ($srdb != null) ? $srdb: new SRDB();
 	}

	function get_date_part($get, $date){
		switch ($get){
			case 'year':
			    return substr($date, 0, 4);
			    break;
			case 'month':
			    return substr($date, 5, 2);
			    break;
			case 'day':
			    return substr($date, 8, 2);
			    break;
			case 'hour':
			    return substr($date, 11, 2);
			    break;
			case 'minute':
			    return substr($date, 14, 2);
			    break;
			case 'second':
			    return substr($date, 17, 2);
			    break;
		}
	}  /* end get_date_part */

	function get_month_name($num){
		switch ($num){
			case '01':
				return 'January';
				break;
			case '02':
				return 'February';
				break;
			case '03':
				return 'March';
				break;
			case '04':
				return 'April';
				break;
			case '05':
				return 'May';
				break;
			case '06':
				return 'June';
				break;
			case '07':
				return 'July';
				break;
			case '08':
				return 'August';
				break;
			case '09':
				return 'September';
				break;
			case '10':
				return 'October';
				break;
			case '11':
				return 'November';
				break;
			case '12':
				return 'December';
				break;
  		}
 	}  /* end get_month_name */

	function recalculate_ratings($old_scale, $new_scale, $new_interval){
		global $srdb;
		$reviews = $srdb->get_reviews();
		foreach($reviews as $review){
			$percentage = $review->rating / $old_scale;
			$rating = round (($percentage * $new_scale), 2);
			$decimal = abs($new_rating - (int) $new_rating);
			if ($new_interval == '0.5'){
				if ($decimal < 0.25)
				    $new_rating = floor($rating);
				elseif ($decimal >= 0.75)
				    $new_rating = ceil($rating);
				else
					$new_rating = ((int) $rating) + 0.5;
			}else $new_rating = round($rating);
			$review->rating = $new_rating;
			$srdb->update_review($review);
		}//end foreach

	}  /* end recalculate_ratings */
	
	function make_section_title($str){
		$str = strtolower($str);
		$str = str_replace(' ', '-', $str);
		$remove = array(
		                '"',
		                "'",
		                '!',
		                '?',
		                '@',
		                '#',
		                '$',
		                '%',
		                ';',
		                ':',
		                ',',
		                '.',
		                '/',
		                '\\'
						);
		foreach($remove as $r)
			$str = str_replace($r, '', $str);
		
		return $str;
 	}

	function get_badge($limit, $category){

		$limit_param = ($limit == 'all') ? 'rev_date DESC' : "rev_date DESC LIMIT $limit";
		$reviews = ($category == 'all') ?  $this->srdb->get_reviews('', '', $limit_param) : $this->srdb->get_reviews($category, '', $limit_param);
		$badge = '<ul>';
		if (count($reviews) > 0){
			foreach($reviews as $i => $review){
				$alt = ($i%2 == 1) ? 'alt': '';
				$title = (strlen($review->prod_link) > 0) ? "<a href=\"$review->prod_link\" class=\"sr-title-link\">$review->title</a>": $review->title;
				$title .= (strlen($review->link) > 0) ? " <span class=\"sr-badge-read\">(<a href=\"$review->link\" title=\"read '$review->title' review\">read</a>)</span>": '';
				$badge .= ""
						."\n\n<li>\n"
						.$this->rating2stars($review->rating)
						.""
						."<br/>"
						.$title
						.""
						."\n</li>";
			}//end foreach review
		}//end if count >0
		return $badge."\n</ul>";
	}  /* end function: get_badge */

	function review_page(){

		$options = $this->srdb->get_options();

		$link['rating'] = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REDIRECT_URL'].'?sort=rating';
		$link['category'] = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REDIRECT_URL'].'?sort=category';
		$link['title'] = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REDIRECT_URL'].'?sort=title';
		$link['date'] = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REDIRECT_URL'].'?sort=date';
		$link[$options['default_sort']] = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REDIRECT_URL'];
		

		$review_page = '<div class="starred-review">'."\n".'<div class="sr-menu">Sort list by: ';
		if (!isset($_GET['sort'])) $_GET['sort'] = $options['default_sort'];

		switch ($_GET['sort']){
			case 'rating':
			    $sort = 'rev_rating DESC, rev_title ASC';
			    $section = 'rating';
			    $review_page .= '<a href="'.$link['date'].'">date</a>'
						.' | <a href="'.$link['category'].'">category</a>'
						.' | <a href="'.$link['title'].'">title</a>'
						.' | <span class="sr-menu-selected">rating</span>'
						."\n";
			    break;
			case 'category':
			    $section = 'cat';
			    $sort = 'cat_name ASC, rev_title ASC';
			    $review_page .= '<a href="'.$link['date'].'">date</a>'
						.' | <span class="sr-menu-selected">category</span>'
						.' | <a href="'.$link['title'].'">title</a>'
						.' | <a href="'.$link['rating'].'">rating</a>'
						."\n";
			    break;
			case 'title':
			    $section = 'title';
			    $sort = 'rev_title ASC';
			    $review_page .= '<a href="'.$link['date'].'">date</a>'
						.' | <a href="'.$link['category'].'">category</a>'
						.' | <span class="sr-menu-selected">title</span>'
						.' | <a href="'.$link['rating'].'">rating</a>'
						."\n";
			    break;
			case 'date':
			    $section = 'date';
			    $sort = 'rev_date DESC';
			    $review_page .= '<span class="sr-menu-selected">date</span>'
						.' | <a href="'.$link['category'].'">category</a>'
						.' | <a href="'.$link['title'].'">title</a>'
						.' | <a href="'.$link['rating'].'">rating</a>'
						."\n";
			    break;
        }// end: switch $_GET['sort']
        
		$cats = $this->srdb->get_categories();
		$reviews = $this->srdb->get_reviews('','',$sort);
		$cur_title = '';

		$review_page .= "</div>\n";

		if (count($reviews) > 0){
			$i = -1;
			foreach($reviews as $review){
				$i++;
				
				if ($options['display_titles'] == '1'){
					switch ($section){
						case 'rating':
						    $title = $review->rating;
						    break;
						case 'cat':
						    $title = $cats[$review->category]->name;
						    break;
						case 'title':
						    $title = substr($review->title, 0, 1);
						    break;
						case 'date':
						    $title = $this->get_month_name($this->get_date_part('month', $review->date)).', '.$this->get_date_part('year', $review->date);
						    break;
					} //end switch

					if ($cur_title != $title && $title != ''){
							$review_page .= "<h3 class=\"sr-section-title\"><a name=\"".$this->make_section_title($title)."\"></a>$title</h3>\n";
							$cur_title = $title;
							$i = 0;
					}
				} //end display titles

				$alt = ($i%2 == 1) ? 'alt': '';
				$title = (strlen($review->prod_link) > 0) ? "<a href=\"$review->prod_link\" class=\"sr-title-link\">$review->title</a>": $review->title;
				$title .= (strlen($review->link) > 0) ? " <span class=\"sr-review-read\">(<a href=\"$review->link\" title=\"read '$review->title' review\">read</a>)</span>": '';
				$review_page .= "<div class=\"sr-review $alt\">"
						."<div class=\"sr-review-rating\">"
						.$this->rating2stars($review->rating)
						."</div>"
						."<div class=\"sr-review-category\">"
						.$cats[$review->category]->name
				        ."</div>"
						."<div class=\"sr-review-title\">"
						.$title
						."</div>"
						."</div>\n";
			}//end foreach review
		}//end if count >0

		return $review_page.'</div><!-- starred-review -->';

	} /* end function: review_page */

	function rating2stars($rating){

		$options = $this->srdb->get_options();
		$parts = explode('**', $options['image_location']);		
		$dir = $parts[0];
		$ext = $parts[1];		
		
		$img = '';
		$img_path = get_option('siteurl').'/wp-content/plugins/starred-review/images/'.$dir;
		$decimal = abs($rating - ((int) $rating));

		for($i=1;$i<=((int) $rating);$i++)
			$img .= "<img src=\"$img_path/1.0.$ext\" alt=\"\" class=\"sr-star\" /> \n";

		if ($decimal > 0)
		    $img .= "<img src=\"$img_path/$decimal.$ext\" alt=\"\" class=\"sr-star\" /> \n";

		for($i=ceil($rating)+1;$i<=$options['rating_scale'];$i++)
			$img .= "<img src=\"$img_path/0.0.$ext\" alt=\"\" class=\"sr-star\" /> \n";

		return $img;
	}  /* end function: rating2stars */

} /* end class: sr_functions */

?>
