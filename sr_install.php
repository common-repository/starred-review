<div class="wrap">
	<h2>Install Starred Review</h2>
	<br />

	<?php

	if(isset($_POST['submitted'])){
		$sql = array();
		$sql[] = "CREATE TABLE `".$_POST['sr_table_prefix']."categories` ("
				."`cat_id` bigint( 20 ) NOT NULL AUTO_INCREMENT ,"
				."`cat_name` varchar( 64 ) NOT NULL default '',"
				."PRIMARY KEY ( `cat_id` )"
				.") TYPE = MYISAM";
		$sql[] = "CREATE TABLE `".$_POST['sr_table_prefix']."reviews` ("
				."`rev_id` bigint( 20 ) NOT NULL AUTO_INCREMENT ,"
				."`rev_title` varchar( 64 ) NOT NULL default '',"
				."`rev_rating` varchar( 10 ) NOT NULL default '0',"
				."`rev_cat` bigint( 20 ) NOT NULL default '1',"
				."`rev_date` datetime NOT NULL default '0000-00-00 00:00:00',"
				."`rev_link` longtext NOT NULL ,"
				."`rev_prod_link` longtext NOT NULL ,"
				."PRIMARY KEY ( `rev_id` )"
				.") TYPE = MYISAM";
		$sql[] = "INSERT INTO `".$_POST['sr_table_prefix']."categories` ( "
				."`cat_id` , `cat_name` "
				.") VALUES ("
				." '1', 'General'"
				.")";

		global $wpdb;
		foreach($sql as $s)
			$wpdb->query($s);
			
		update_option('starred_review_settings',array(
		    'default_sort' => 'date',
			'rating_scale' => '5',
			'rating_interval' => '0.5',
			'table_prefix' => $_POST['sr_table_prefix'],
			'version' => '0.2b',
			'display_titles' => '1', 
			'image_location' => 'stars-black-png**png',
			));
		?>
		
		<p>Congrats you have installed <a href="http://www.metacomment.com/starred-review/">Starred Review</a>!</p>
		<p>Before you add any reviews, you must <a href="<?php echo $SERVER['PHP_SELF']; ?>?page=starred-review/starred-review.php&srpage=install">Install</a> this plugin. You can then <a href="<?php echo $SERVER['PHP_SELF']; ?>?page=starred-review/starred-review.php&srpage=settings">Configure</a> set the rating scale and interval for your reviews.  It is advised you do this at the start and DO NOT change it after.  Even though the old reviews will have their ratings updated to deal with the new rating scale it may skew pre-existing reviews.</p>
		<p>To begin adding reviews and such, you add/remove/edit reviews go to the <a href="<?php echo $SERVER['PHP_SELF']; ?>?page=starred-review/starred-review.php">Starred Review</a> submenu under Options.</p>
	
		<?php
 	}else{

		if ($user_level >= 0){
		?>
		
			<form action="<?php echo $SERVER['PHP_SELF']; ?>?page=starred-review/starred-review.php&amp;srpage=install" method="post">
			<div align="center">
			<table width="600px" cellspacing="2" cellpadding="5" class="editform" summary="Install Settings">
				<tr valign="top">
					<th scope="row" width="200px"><label for="sr_table_prefix">Table Prefix</label></th>
					<td><input type="text" name="sr_table_prefix" value="sr_" class="code"/>
		            <br /><br />Starred Review is about to create a couple tables in the same database that your installation of WordPress is in, please specify a tablename prefix so that the tables contain a unique name.  In most instances '<code>sr_</code>' should be fine.

					<br /><br/><strong>DO NOT EDIT THIS AFTER INITIAL INSTALLATION</strong>
					
					<br /><p class="submit"><input type="hidden" name="submitted" /><input type="submit" name="submit" value="Install Starred Review &raquo;" /></p>

		            </td>
				</tr>
			</table>
			</form>
			</div>

		<?php
  		}else
		     _e('Sorry, but you don\'t have a high enough user level to install this plugin, you need to have a minimum level of 8 to install Starred Review.');
	}//end else not insalling
	?>
</div>
