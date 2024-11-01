<?php
/**
 * Plugin Name: Recent posts by language(WPML)
 * Description: A widget that displays recent posts by language via WPML.
 * Version: 0.1
 * Author: mschuddings
 */


add_action( 'widgets_init', 'recent_posts_wpml' );


function recent_posts_wpml() {
	register_widget( 'WP_recent_posts_by_language' );
}

class WP_recent_posts_by_language extends WP_Widget {

	function WP_recent_posts_by_language() {
		$widget_ops = array( 'classname' => 'wp_recent_posts_by_language', 'description' => __('A widget that displays the recents posts by language (WPML) ', 'wp_recent_posts_by_language') );

		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'widget-recent-posts-language-wpml' );

		$this->WP_Widget( 'widget-recent-posts-language-wpml', __('WP - Recent post per language widget', 'wp_recent_posts_by_language'), $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {
		extract( $args );

		//Our variables from the widget settings.
		$title = apply_filters('widget_title', $instance['title'] );
		$number_posts = $instance['number_posts'];

		echo $before_widget;

		// Display the widget title
		if ( $title )
			echo $before_title . $title . $after_title;
	    if ( !$number_posts )  {
			$number_posts = 3;
		}

		// Get the recent posts.
		global $wpdb;
		$querystr = "
		  SELECT wposts.*
		  FROM $wpdb->posts wposts, wp_icl_translations icl_translations
		  WHERE wposts.ID = icl_translations.element_id
		  AND icl_translations.language_code = '".ICL_LANGUAGE_CODE."'
		  AND wposts.post_status = 'publish'
		  AND wposts.post_type = 'post'
		  ORDER BY wposts.post_date DESC LIMIT ". intval($number_posts) ."
		";
		$recent_posts = $wpdb->get_results($querystr, OBJECT);

		// post_blip_sidebar";
		$bgclass = " post_blip_general";
	    $imgclass = " thickimgborder";
		$dim = 85;
		$titleclass = " pw_title";

		foreach ($recent_posts as $key => $post):
		 echo '<div class="post_blip '.$bgclass.'">';
		 echo '<div class="post_blip_img '.$imgclass.'">';
		if(has_post_thumbnail($post->ID))
			echo pwork_get_img(wp_get_attachment_url(get_post_thumbnail_id($post->ID)), $dim, $dim, $post->post_title );
		else
		  echo '<img src="'.TEMPLATEURI.'/assets/skin_'. PW_PRESET .'/images/pwthumb_default.png" width="'.$dim.'" height="'.$dim.'" alt="'.$post->post_title.'" />';
		  echo '</div>';
		  echo '<div class="post_blip_txt_wrap"><div class="post_blip_txt">';
		  echo '<div class="'.$titleclass.'"><a href="'.get_permalink($post->ID).'">'.$post->post_title.'</a></div>';
		  echo '<div class="smalltext">'.get_the_time('j M', $post->ID).' - '.($post->comment_count).' Comments</div>';
		  echo '</div></div>';
          echo '<div class="clear"></div>';
          echo '</div> <!-- /post_blip -->';
		endforeach;

		echo $after_widget;
	}

	//Update the widget
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		//Strip tags from title and name to remove HTML
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['number_posts'] = strip_tags( $new_instance['number_posts'] );

		return $instance;
	}


	function form( $instance ) {

		//Set up some default widget settings.
		$defaults = array( 'title' => __('wp_recent_posts_by_language', 'wp_recent_posts_by_language'), 'number_posts' => __('3', 'wp_recent_posts_by_language'), 'show_info' => true );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'Michael blog'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'number_posts' ); ?>"><?php _e('Number of posts:', '3'); ?></label>
			<input id="<?php echo $this->get_field_id( 'number_posts' ); ?>" name="<?php echo $this->get_field_name( 'number_posts' ); ?>" value="<?php echo $instance['number_posts']; ?>" style="width:100%;" />
		</p>

	<?php
	}
}

?>