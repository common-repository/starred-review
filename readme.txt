=== Starred Review ===
Contributors: Callum Alden
Donate link: http://www.metacomment.com/blog/
Tags: reviews,review,reviewing,movies,cinema,art,radio,report,evaluation
Tested up to: 2.8.4
Requires at least: 2.0
Stable tag: 1.4.2

A small plugin that allows you to add reviews to you website.


== Description ==
A WordPress plugin that will allow you to post reviews to your website. The plugin is inspired 
by Kottke's movie reviews. It allows you to rate different things and place them in different categories. 
This is a basic plugin and won't hold actual written reviews, but will allow you to link to reviews you 
may have posted and link to product pages. It's the plugin I use for my reviews section/page.

The [changelog](http://svn.wp-plugins.org/starred-review/trunk/Changelog.txt) is a good place to start if you want to know what has changed since you last downloaded the plugin.


== Installation ==
1. Download the latest version. 
2. Unzip it and upload the 'starred-review' folder and 'starred-review.php' to your WordPress plugin 
directory. 
3. Activate the plugin on the plugins page. 
4. Under 'Settings' click on "Starred Review". 
5. You'll need to "Install" and then Configure your plugin if this is the first install.


== Frequently Asked Questions ==

= I've added a few reviews but they don't show =

Go to your Wordpress 'Settings' panel, where you'll find the option to access the Starred Review options page, within that page you'll need to hit the 'Install' button, top right.


== Screenshots ==

1. With no CSS adjustments, Starred Review in the Barecity theme.


= Using =

Starred Review can be configured through the Wordpress Widgets page.

Alternately there are two functions available, both of which can be placed in your template files: 

<?php starred_review(); ?> 
This function has no parameters and will display the actual review page that lists all your reviews.

<?php starred_review_badge('limit', 'category'); ?>
This function will display you most recent reviews, meant to be a teaser for you front page. Look at the bottom right of my main page, you'll see me using it. It has two OPTIONAL parameters. The first one  (limit) is the number of reviews you want to display in your badge, the default is 5. The second parameter is the number of the category you want to display, just incase you only want to show your latest movie reviews instead of all your latest reviews. The default is 'all'. 

= Customising =

The plugin comes with mutliple variations of the images that you can use in both gif and png format. But if you don't like to use the star available you can either create your own images, place them in their own 
directory within the images folder and label them '1.0.ext', '0.5.ext' or '0.0.ext' (where 'ext' is either, jpg, jpeg, png or gif). If these files don't exist in the directory then you won't be able to select that 
directory from the settings panel in the admin menu. If you don't like the colours of the stars that are provided you can always download the PSD file (from metacomment.com/starred-review) and create your own colour combnination. 


== Troubleshooting==
If things don't work when you installed the plugin you can always contact me via my blog.


== Links ==
[Starred Review Homepage](http://www.metacomment.com/starred-review/) is the official homepage for this plugin.
[John Bedard](http://jbfabrications.com/) fixed a major issue with version 1.3.


== Updates ==
Updates to the plugin will be posted here, to my blog '[Meta Comment](http://www.metacomment.com/blog/)' and the [Wordpress Plugin Repository](http://wordpress.org/extend/plugins/starred-review/) will always link to the newest version.


== Thanks ==
Thanks to Marc Hodges, who created the original plugin and to Peter for his invaluable [Widgetizing Tutorial](http://apartmentonesix.com/2009/03/widgetizing-a-wordpress-plugin-example-widget-code/).