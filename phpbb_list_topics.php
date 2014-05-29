<?php
/**
 * Plugin Name: phpBB List Topics
 * Plugin URI:
 * Description: Lists topics from phpBB
 * Version: 0.1
 * Author: Keith Brinks
 * Author URI: http://keithbrinks.com
 * License: GPL2
 */

// Define our plugin's directory
define('PLT_PLUGIN_DIR', dirname(__FILE__)); 
 
// Load admin file is user is an admin
if (is_admin()) {

	require_once(PLT_PLUGIN_DIR .'/admin.php');

}

/**
 * Main function to call
 */
function phpbb_list_topics($atts) {
	
	// Define our parameters
	extract(shortcode_atts(array(
		'include' => '',
		'exclude' => '',
		'limit' => '5',
		'title_only' => false
	), $atts));
	
	// Get user defined options for phpBB
	$phpbb_path = trailingslashit(get_option('plt_phpbb_path'));
	$phpbb_url = trailingslashit(get_option('plt_phpbb_url'));
	
	if (empty($phpbb_path) || empty($phpbb_url)) {
	
		echo 'Please fill out the phpBB List Topics settings in the admin panel.';
		
	} else if (!file_exists($phpbb_path .'/config.php')) {
		
			echo 'Cannot find phpBB config file';
			
	} else {
			
		require_once($phpbb_path .'/config.php');
			
		// Connect to phpBB's database
		$db = new mysqli($dbhost, $dbuser, $dbpasswd, $dbname);
		
		// Test our database connection
		if ($db->connect_errno) {
		
			echo 'phpBB database connection failed';
		
		} else {
			
			// Build SQL query	
			$sql = 'SELECT t.topic_id, t.forum_id, t.topic_title, t.topic_poster, t.topic_time, p.post_text, u.username
			
				FROM phpbb_topics t
				
				INNER JOIN phpbb_posts p ON p.post_id = t.topic_first_post_id
				INNER JOIN phpbb_users u ON u.user_id = t.topic_poster';
				
			if (!empty($include) && empty($exclude)) {
				
				$sql .= ' WHERE t.forum_id IN ('. $include .')';
				
			} else if (empty($include) && !empty($exclude)) {
				
				$sql .= ' WHERE t.forum_id NOT IN ('. $exclude .')';
				
			}

			$sql .= ' ORDER BY p.topic_id DESC LIMIT '. $limit;
			
			// Get results from database
			$result = $db->query($sql);
			
			echo '<ul class="plt_list">';
			
			if (!$title_only) {
			
				while ($topic = $result->fetch_array()) {
					
					echo '<li>
					
						<h2><a href="'. $phpbb_url .'viewtopic.php?f='. $topic['forum_id'] .'&t='. $topic['topic_id'] .'">'. $topic['topic_title'] .'</a></h2>
						<span class="plt_meta">Posted '. date('F jS, Y', $topic['topic_time']) .' by <a href="'. $phpbb_url .'memberlist.php?mode=viewprofile&u='. $topic['topic_poster'] .'">'. $topic['username'] .'</a></span>
					
						<div class="plt_topic">
							'. $topic['post_text'] .'
						</div>
					
					</li>';
					
				}
				
			} else {
				
				while($topic = $result->fetch_array()) {
					
					echo '<li><a href="'. $phpbb_url .'viewtopic.php?f='. $topic['forum_id'] .'&t='. $topic['topic_id'] .'">'. $topic['topic_title'] .'</a></li>';
					
				}
				
			}
			
			echo '</ul>';
		}
	
	}
	
}

// Add shortcode
add_action('init', 'plt_shortcode');

/**
 * Create shortcode
 */
function plt_shortcode() {
	add_shortcode('phpbb-list-topics', 'phpbb_list_topics');
}