<?php
/*
Plugin Name: Starred Review
Plugin URI: http://www.metacomment.com/starred-review/
Description: A small plugin that allows you to add reviews to your blog. I'm indebted to <a href="http://jbfabrications.com/" target"_blank">John Bedard</a> for his help. You Need to <a href="./options-general.php?page=starred-review/starred-review.php&amp;srpage=install">Install & Configure</a> this plugin before you can start using it. 
Version: 1.4.2
License: GPL
Author: Callum Alden
Author URI: http://www.metacomment.com/
*/

function sr_options_page() {

    $sr_dir = dirname(__FILE__).'/';

	if (starredreview_installed())
		$sr_page = $sr_dir.'sr_install.php';
	else
	    $sr_page = (isset($_GET['srpage']) && file_exists($sr_dir.'sr_'.$_GET['srpage'].'.php')) ? $sr_dir.'sr_'.$_GET['srpage'].'.php' : $sr_dir.'sr_review-man.php';

	$srdb = new SRDB();
	$srfunc = new sr_functions();
	$sr_values = $srdb->get_options();

	if (!strstr($sr_page, 'install')){
		?>

	<div align="center" class="sr-heading">
		<a href="<?php echo $_SERVER[PHP_SELF];?>?page=starred-review/starred-review.php">Manage Reviews</a> | 
		<a href="<?php echo $_SERVER[PHP_SELF];?>?page=starred-review/starred-review.php&amp;srpage=review-action">Add Review</a> | 
		<a href="<?php echo $_SERVER[PHP_SELF];?>?page=starred-review/starred-review.php&amp;srpage=review-cat">Review Categories</a> | 
		<a href="<?php echo $_SERVER[PHP_SELF];?>?page=starred-review/starred-review.php&amp;srpage=settings">Configure</a> | 
		<a style="font-weight: bold;" href="<?php echo $_SERVER[PHP_SELF];?>?page=starred-review/starred-review.php&amp;srpage=install">Install</a>
	</div>

	<?php
	} //end if install
	require_once($sr_page);

}



	function starredreview_installed(){
        $srdb = new SRDB();
		$options = $srdb->get_options();
		if ($options['version'] != '1')
		    return FALSE;
		if (!empty($options)){
			$result = mysql_list_tables(DB_NAME);
			$tables = array();
			while ($row = mysql_fetch_row($result)) { $tables[] = $row[0]; }
			return (in_array($options['table_prefix'].'reviews', $tables) && in_array($options['table_prefix'].'categories', $tables));
		}else
		    return FALSE;

	}

	function starredreview_admin_menu(){
		add_options_page('Starred Review Options', 'Starred Review', 8, "starred-review/starred-review.php",'sr_options_page');
	} /* end fucntion: starredreview_admin_menu */

	function starredreview_require(){
        require_once(dirname(__FILE__).'/classes.php');
	} /* end fucntion: starredreview_require */

	function starred_review(){
		$srfunc = new sr_functions(new SRDB());
		_e($srfunc->review_page());
 	} /* end function: starred_review */

	function starred_review_badge($limit=5, $category='all'){
		$limit = ($limit == '') ? '5' : $limit;
		$category = ($category == '') ? 'all' : $category;
		$srfunc = new sr_functions(new SRDB());
		_e($srfunc->get_badge($limit, $category));
 	} /* end fucntion: starred_review_badge */

    add_action('options_page_starred-review', 'sr_options_page');    
    add_action('init', 'starredreview_require');
    add_action('admin_menu', 'starredreview_admin_menu');


	function sr_css(){
		echo '<link rel="stylesheet" href="' . plugins_url("/starred-review/style.css") .'" type="text/css" />';
		}

	add_action('admin_head', 'sr_css');

/*
FOR WIDGET:
*/

error_reporting(0);

function sr_widget_init()
{

register_sidebar_widget('Starred Review', 'sr_widget');
register_widget_control('Starred Review', 'sr_widget_control');
}

function sr_widget($args) {

extract($args);

$sr_widget_options = unserialize(get_option('sr_widget_options'));

?>

<?php
echo $before_widget;
?>

<?php  echo $before_title;?>
<?php echo $sr_widget_options['title']; ?>
<?php echo $after_title; ?>

<?php
		$limit = ($sr_widget_options['limit']);
		$category = ($sr_widget_options['category']);
		$srfunc = new sr_functions(new SRDB());
		_e($srfunc->get_badge($limit, $category));
?>

<?php echo $after_widget; ?>
<?php
}

function sr_widget_control() {

if(!get_option('sr_widget_options'))
{
add_option('sr_widget_options', serialize(array('title'=>'Starred Review', 'limit'=>'5', 'category'=>'all')));
}
$sr_widget_options = $sr_widget_newoptions = unserialize(get_option('sr_widget_options'));

if ($_POST['sr_widget_title']){
$sr_widget_newoptions['title'] = $_POST['sr_widget_title'];
}
if ($_POST['sr_widget_limit']){
$sr_widget_newoptions['limit'] = $_POST['sr_widget_limit'];
}
if ($_POST['sr_widget_category']){
$sr_widget_newoptions['category'] = $_POST['sr_widget_category'];
}

if($sr_widget_options != $sr_widget_newoptions){
$sr_widget_options = $sr_widget_newoptions;
update_option('sr_widget_options', serialize($sr_widget_options));
}

?>
<p>
<label for="sr_widget_title">Title:<br />
<input id="sr_widget_title" name="sr_widget_title" type="text" value="<?php echo $sr_widget_options['title']; ?>"/>
</label>
</p>
<p>
<label for="sr_widget_limit">Limit:<br />
<input id="sr_widget_limit" name="sr_widget_limit" type="text" value="<?php echo $sr_widget_options['limit']; ?>"/>
</label>
</p>
<p>
<label for="sr_widget_category">Category:<br />
<input id="sr_widget_category" name="sr_widget_category" type="text" value="<?php echo $sr_widget_options['category']; ?>"/>
</label>
</p>
<?php
}
add_action("widgets_init", "sr_widget_init");
?>