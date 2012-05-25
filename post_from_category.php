<?php
/*
Contributors: 		shashidharkumar
Plugin Name:       	Posts from Single Category Widget
Plugin URI:        	http://www.shashionline.in/post-from-single-category-widget-wordpress/
Author URI:        	http://www.shashionline.in/
Author:            	Shashidhar Kumar
Donate link: 		http://www.shashionline.in/
Tags: 				plugin, posts, posts from category, multiple posts from category, widget, Wordpress
Requires at least: 	3.0
Tested up to: 		3.3.2
Stable tag: 		trunk
Version:           	1.0
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
   $this->WP_Widget('postsfromcat-widget', 'Posts from Category', $widget_ops, $control_ops );
  }

  function form ($instance) {
    /* Set up some default widget settings. */
    $defaults = array('numberposts' => '5','catid'=>'1','title'=>'','rss'=>'');
    $instance = wp_parse_args( (array) $instance, $defaults ); ?>

  <p>
    <label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
    <input type="text" name="<?php echo $this->get_field_name('title') ?>" id="<?php echo $this->get_field_id('title') ?> " value="<?php echo $instance['title'] ?>" size="20">
  </p>

  <p>
   <label for="<?php echo $this->get_field_id('catid'); ?>">Category ID:</label>
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
  $instance['rss'] = $new_instance['rss'];

  return $instance;
}

function widget ($args,$instance) {
   extract($args);

  $title = $instance['title'];
  $catid = $instance['catid'];
  $numberposts = $instance['numberposts'];
  $rss = $instance['rss'];

  // retrieve posts information from database
  global $wpdb;
  $posts = get_posts('numberposts='.$numberposts.'&category='.$catid);
  $out = '<ul>';
  foreach($posts as $post) {
  $out .= '<li><a href="'.get_permalink($post->ID).'">'.$post->post_title.'</a></li>';
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

function post_load_widgets() {
  register_widget('Posts_From_Category');
}

add_action('widgets_init', 'post_load_widgets');
?>