Wordpress Cupcake
=================

Add Facebook "like" functionality to wordpress post. Only registered user can use the like function.

Installation
------------

* Download zip file or clone the repo
* Extract and copy into wp-content/plugins
* Enable the plugin

How to use
----------

<pre>
	<?php do_action('cupcake_like_button', get_the_ID(), 'class_name'); ?>
</pre>