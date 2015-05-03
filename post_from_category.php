<?php
/*
Contributors: 		shashidharkumar
Plugin Name:       	Posts from Single Category Widget
Plugin URI:        	http://www.shashidharkumar.com/post-from-single-category-widget-wordpress/
Description: 		This plugin is a widget that displays a list of posts from single category on your sidebar. You can also assign how may words will be display for each category content. You can customize your Read more text as well.
Author URI:        	http://www.shashidharkumar.com/
Author:            	Shashidhar Kumar
Donate link: 		http://www.shashidharkumar.com/donate/
Tags: 				plugin, posts, posts from category, multiple posts from category, widget, Wordpress
Requires at least: 	4.0
Tested up to: 		4.2.1
Stable tag: 		trunk
Version:           	3.0
License: 			GPLv2 or later
License URI: 		http://www.gnu.org/licenses/gpl-2.0.html
*/

class Posts_From_Category extends WP_Widget {
  function Posts_From_Category() {
     /* Widget settings. */
    $widget_ops = array(
      'classname' => 'postsfromcat',
      'description' => 'You can add a widget for number of most recent posts from a category.');

     /* Widget control settings. */
    $control_ops = array(
       'width' => 250,
       'height' => 250,
       'id_base' => 'postsfromcat-widget');

    /* Create the widget. */
   $this->WP_Widget('postsfromcat-widget', 'Posts from Single Category', $widget_ops, $control_ops );
  }

  function form ($instance) {
    /* Set up some default widget settings. */
    $defaults = array('numberposts' => '5','catid'=>'1','title'=>'Post from Single Category','rss'=>'','clength'=>'15','readmore'=>'read more');
    $instance = wp_parse_args( (array) $instance, $defaults ); ?>

  <p>
    <label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
    <input type="text" name="<?php echo $this->get_field_name('title') ?>" id="<?php echo $this->get_field_id('title') ?> " value="<?php echo $instance['title'] ?>" size="20">
  </p>

  <p>
   <label for="<?php echo $this->get_field_id('catid'); ?>">Category:</label>
   <?php wp_dropdown_categories('hide_empty=0&hierarchical=1&id='.$this->get_field_id('catid').'&name='.$this->get_field_name('catid').'&selected='.$instance['catid']); ?>
  </p>
  
  <p>
   <label for="<?php echo $this->get_field_id('numberposts'); ?>">Number of posts:</label>
   <select id="<?php echo $this->get_field_id('numberposts'); ?>" name="<?php echo $this->get_field_name('numberposts'); ?>">
   <?php for ($i=1;$i<=20;$i++) {
         echo '<option value="'.$i.'"';
         if ($i==$instance['numberposts']) echo ' selected="selected"';
         echo '>'.$i.'</option>';
        } ?>
       </select>
  </p>
  
  <p>
    <label for="<?php echo $this->get_field_id('clength'); ?>">Content Length:</label>
    <input type="text" name="<?php echo $this->get_field_name('clength') ?>" id="<?php echo $this->get_field_id('clength') ?>" value="<?php echo $instance['clength'] ?>" size="2">&nbsp;Number of Words
  </p>
  
  <p>
    <label for="<?php echo $this->get_field_id('readmore'); ?>">Read More Text:</label>
    <input type="text" name="<?php echo $this->get_field_name('readmore') ?>" id="<?php echo $this->get_field_id('readmore') ?>" value="<?php echo $instance['readmore'] ?>" size="15">
  </p>

  <p>
   <input type="checkbox" id="<?php echo $this->get_field_id('rss'); ?>" name="<?php echo $this->get_field_name('rss'); ?>" <?php if ($instance['rss']) echo 'checked="checked"' ?> />
   <label for="<?php echo $this->get_field_id('rss'); ?>">Show RSS feed link?</label>
  </p>

  <?php
}

function update ($new_instance, $old_instance) {
  $instance = $old_instance;

  $instance['catid'] = $new_instance['catid'];
  $instance['numberposts'] = $new_instance['numberposts'];
  $instance['title'] = $new_instance['title'];
  $instance['clength'] = $new_instance['clength'];
  $instance['readmore'] = $new_instance['readmore'];
  $instance['rss'] = $new_instance['rss'];

  return $instance;
}

function widget ($args,$instance) {
   extract($args);

  $title = $instance['title'];
  $catid = $instance['catid'];
  $numberposts = $instance['numberposts'];
  $clength = $instance['clength'];
  $readmore = $instance['readmore'];
  $rss = $instance['rss'];

  // retrieve posts information from database
  global $wpdb;
  $posts = get_posts('numberposts='.$numberposts.'&category='.$catid);
  $out = '<ul>';
  foreach($posts as $post) {
  $exrt = string_limit_contents($post->post_content,$clength);
  if($readmore != '')
  	{
	$readmore = $readmore;
	}
  else
  	{
	$readmore = 'read more';
	}	
  $out .= '<li style="list-style:none; float: left;"><b><a href="'.get_permalink($post->ID).'">'.$post->post_title.'</a></b><br>'.$exrt.'...<a href="'.get_permalink($post->ID).'">'.$readmore.'</a><hr></li>';
  }
  $imgURL = "<img src='".plugins_url('post-from-single-category/images/rss.png' , dirname(__FILE__) ). "' >";
  if ($rss) $out .= '<li style="list-style:none; margin-right: 10px; float: right;"><a href="'.get_category_link($catid).'feed/" class="rss">'.$imgURL.'</a></li>';
  $out .= '</ul>';

  //print the widget for the sidebar
  echo $before_widget;
  echo $before_title.$title.$after_title;
  echo $out;
  echo $after_widget;
 }
}
//Shorten content length
function string_limit_contents($string, $word_limit)
{
  $words = explode(' ', $string, ($word_limit + 1));
  if(count($words) > $word_limit)
  array_pop($words);
  return implode(' ', $words);
}

function post_load_widgets() {
  register_widget('Posts_From_Category');
}

add_action('widgets_init', 'post_load_widgets');
?>