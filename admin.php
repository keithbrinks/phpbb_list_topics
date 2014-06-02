<?php

// Add menu the item
add_action('admin_menu','plt_admin_menu');

/**
 * Create the menu item in the admin panel
 */
function plt_admin_menu() {

	add_options_page('phpBB List Topics', 'phpBB List Topics', 1, 'phpbblisttopics', 'plt_admin_page');

}

// Add options
add_action('admin_init','plt_options');

/**
 * Create user configurable options
 */
function plt_options() {

	register_setting('plt_options','plt_phpbb_path');
	register_setting('plt_options','plt_phpbb_url');
	
}

/**
 * Create the admin page
 */
function plt_admin_page() {

	echo '<div class="wrap">
		<h2>phpBB Latest Topics Settings</h2>';
		
		echo '<form method="post" action="options.php">';
		
			settings_fields('plt_options');
			
			echo '<table class="form-table">
				<tr>
					<th>
						<label for="plt_phpbb_path">phpBB Path</label>
					</th>
					<td>
						<input type="text" id="plt_phpbb_path" name="plt_phpbb_path" value="'. get_option('plt_phpbb_path') .'" />
						<p class="description">Enter relative path to phpBB from WordPress.</p>
					</td>
				</tr>
				<tr>
					<th>
						<label for="plt_phpbb_url">phpBB URL</label>
					</th>
					<td>
						<input type="text" id="plt_phpbb_url" name="plt_phpbb_url" value="'. get_option('plt_phpbb_url') .'" />
					</td>
				</tr>
			</table>
			<p class="submit">
				<input type="submit" class="button-primary" value="Save Changes" />
			</p>
		</form>
	</div>';
	
}
