<?php
/*
 * This file is part of pluck, the easy content management system
 * Copyright (c) somp (www.somp.nl)
 * http://www.pluck-cms.org

 * Pluck is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * See docs/COPYING for the complete license.
*/

function blog_page_site_list() {
	if (isset($_GET['post']) && file_exists('data/settings/modules/blog/posts/'.$_GET['post'])) {
		include ('data/settings/modules/blog/posts/'.$_GET['post']);
		$module_page_admin[] = array(
			'func'  => 'viewpost',
			'title' => $post_title
		);
	}
	return $module_page_admin;
}

//---------------
// Theme: main
//---------------
function blog_theme_main() {
	global $lang_blog14, $lang_blog23;

	//Display existing posts, but only if post-index file exists.
	if (file_exists('data/settings/modules/blog/post_index.dat')) {
		$handle = fopen('data/settings/modules/blog/post_index.dat', 'r');
		while (!feof($handle)) {
			$file = fgets($handle, 4096);
			//Filter out line breaks.
			$file = str_replace ("\n",'', $file);
			//Check if post exists.
			if (file_exists('data/settings/modules/blog/posts/'.$file) && is_file('data/settings/modules/blog/posts/'.$file)) {
				//Include post information.
				include_once ('data/settings/modules/blog/posts/'.$file);
				?>
					<div class="blogpost">
						<span class="posttitle">
							<a href="?module=blog&amp;page=viewpost&amp;post=<?php echo $file; ?>&amp;pageback=<?php echo CURRENT_PAGE_FILENAME; ?>" title="post <?php echo $post_title; ?>"><?php echo $post_title; ?></a>
						</span>
						<br />
						<span class="postinfo">
							<?php echo $lang_blog14; ?> <span style="font-weight: bold;"><?php echo $post_category; ?></span> - <?php echo $post_month; ?>-<?php echo $post_day; ?>-<?php echo $post_year; ?>, <?php echo $post_time; ?>
						</span>
						<br /><br />
						<?php echo $post_content; ?>
						<p>
							<a href="?module=blog&amp;page=viewpost&amp;post=<?php echo $file; ?>&amp;pageback=<?php echo CURRENT_PAGE_FILENAME; ?>" title="<?php echo $lang_blog23; ?>">&raquo; <?php echo $lang_blog23; ?></a>
						</p>
					</div>
				<?php
			}
		}
		//Close module-dir.
		fclose($handle);
	}
}

//---------------
// Page: viewpost
//---------------
function blog_page_site_viewpost() {
	//Global language variables
	global $lang, $lang_blog14, $lang_blog16, $lang_blog17, $lang_blog18, $lang_contact3, $lang_contact5, $lang_contact6, $lang_contact10;

	//Load blogpost
	if (isset($_GET['post']) && file_exists('data/settings/modules/blog/posts/'.$_GET['post']))
		include ('data/settings/modules/blog/posts/'.$_GET['post']);
	?>
		<div class="blogpost">
			<span class="postinfo">
				<?php echo $lang_blog14; ?> <span style="font-weight: bold;"><?php echo $post_category; ?></span> - <?php echo $post_month; ?>-<?php echo $post_day; ?>-<?php echo $post_year; ?>, <?php echo $post_time; ?>
			</span><br /><br />
			<?php echo $post_content; ?>
		</div>
		<div style="margin-top: 10px;">
			<span style="font-size: 19px"><?php echo $lang_blog16; ?></span>
			<?php
				//Then show the reactions.
				//Check if there are reactions.
				if (isset($post_reaction_title)) {
					foreach ($post_reaction_title as $key => $value) {
					?>
						<div class="blogpost_reaction">
							<span class="posttitle">
								<?php echo $post_reaction_title[$key]; ?>
							</span>
							<br />
							<span class="postinfo">
								<?php echo $lang_blog18; ?> <span style="font-weight: bold;"><?php echo $post_reaction_name[$key]; ?></span> -  <?php echo $post_reaction_month[$key]; ?>-<?php echo $post_reaction_day[$key]; ?>-<?php echo $post_reaction_year[$key]; ?>, <?php echo $post_reaction_time[$key]; ?>
							</span>
							<br />
							<?php
								//Change linebreaks in html-breaks
								$post_reaction_content_br = str_replace("\n", '<br />', $post_reaction_content[$key]);
								//Display post
								echo $post_reaction_content_br;
							?>
						</div>
					<?php
				}
				unset($key);
			}
		//Show a form to post new reactions
	?>
		<form method="post" action="" style="margin-top: 5px; margin-bottom: 15px;">
			<div>
				<label><?php echo $lang_blog17; ?></label>
				<br />
				<input name="title" type="text" />
				<br />
				<label><?php echo $lang_contact3; ?></label>
				<br />
				<input name="name" type="text" />
				<br />
				<label><?php echo $lang_contact5; ?></label>
				<br />
				<textarea name="message" rows="7" cols="45"></textarea>
				<br />
				<input type="submit" name="Submit" value="<?php echo $lang_contact10; ?>" />
			</div>
		</form>
	</div>

<?php
	//If form is posted...
	if (isset($_POST['Submit'])) {

		//Get post filename
		$post = $_GET['post'];

		//Check if everything has been filled in.
		if (empty($_POST['title']) || empty($_POST['name']) || empty($_POST['message']))
			echo '<span style="color: red;">'.$lang_contact6.'</span>';

		else {
			//Then fetch our posted variables
			$title = $_POST['title'];
			$name = $_POST['name'];
			$message = $_POST['message'];

			//Check for HTML, and eventually block it
			//FIXME: Replace ereg with strpos.
			if (ereg('<', $title) || ereg('>', $title) || ereg('<', $name) || ereg('>', $name) || ereg('<', $message) || ereg('>', $message))
				echo '<span style="color: red;">'.$lang_blog22.'</span>';

			//If no HTML is present
			else {
				//Delete unwanted characters
				$title = sanitize($title);
				$name = sanitize($name);
				$message = sanitize($message);
				$post_title = sanitize($post_title);
				$post_category = sanitize($post_category);
				$post_content = sanitize($post_content, false);

				//Determine the date
				$day = date('d');
				$month = date('m');
				$year = date('Y');
				$time = date('H:i');

				//Then, save existing post information
				$file = fopen('data/settings/modules/blog/posts/'.$post, 'w');
				fputs($file, '<?php'."\n"
				.'$post_title = \''.$post_title.'\';'."\n"
				.'$post_category = \''.$post_category.'\';'."\n"
				.'$post_content = \''.$post_content.'\';'."\n"
				.'$post_day = \''.$post_day.'\';'."\n"
				.'$post_month = \''.$post_month.'\';'."\n"
				.'$post_year = \''.$post_year.'\';'."\n"
				.'$post_time = \''.$post_time.'\';'."\n");

				//Check if there already are other reactions
				if (isset($post_reaction_title)) {
					foreach ($post_reaction_title as $reaction_key => $value) {
						//Set key
						$key = $reaction_key + 1;

						//Sanitize reaction variables
						$post_reaction_title[$reaction_key] = sanitize($post_reaction_title[$reaction_key]);
						$post_reaction_name[$reaction_key] = sanitize($post_reaction_name[$reaction_key]);
						$post_reaction_content[$reaction_key] = sanitize($post_reaction_content[$reaction_key]);

						//And save the existing reaction
						fputs($file, '$post_reaction_title['.$reaction_key.'] = \''.$post_reaction_title[$reaction_key].'\';'."\n"
						.'$post_reaction_name['.$reaction_key.'] = \''.$post_reaction_name[$reaction_key].'\';'."\n"
						.'$post_reaction_content['.$reaction_key.'] = \''.$post_reaction_content[$reaction_key].'\';'."\n"
						.'$post_reaction_day['.$reaction_key.'] = \''.$post_reaction_day[$reaction_key].'\';'."\n"
						.'$post_reaction_month['.$reaction_key.'] = \''.$post_reaction_month[$reaction_key].'\';'."\n"
						.'$post_reaction_year['.$reaction_key.'] = \''.$post_reaction_year[$reaction_key].'\';'."\n"
						.'$post_reaction_time['.$reaction_key.'] = \''.$post_reaction_time[$reaction_key].'\';'."\n");
					}
					unset($reaction_key);
				}

				//If this is the first reaction, use key '0'
				else
					$key = 0;

				//Then, save reaction
				fputs($file, '$post_reaction_title['.$key.'] = \''.$title.'\';'."\n"
				.'$post_reaction_name['.$key.'] = \''.$name.'\';'."\n"
				.'$post_reaction_content['.$key.'] = \''.$message.'\';'."\n"
				.'$post_reaction_day['.$key.'] = \''.$day.'\';'."\n"
				.'$post_reaction_month['.$key.'] = \''.$month.'\';'."\n"
				.'$post_reaction_year['.$key.'] = \''.$year.'\';'."\n"
				.'$post_reaction_time['.$key.'] = \''.$time.'\';'."\n"
				.'?>');
				fclose($file);
				chmod('data/settings/modules/blog/posts/'.$post, 0777);

				//Redirect user
				redirect('?module=blog&page=viewpost&post='.$post.'&pageback='.$_GET['pageback'], 0);
			}
		}
	}

	//Show back link
	if (isset($_GET['pageback'])) { ?>
	<p>
		<a href="?file=<?php echo $_GET['pageback']; ?>" title="<?php echo $lang['general']['back']; ?>">&lt;&lt;&lt; <?php echo $lang['general']['back']; ?></a>
	</p>
<?php
	}
}
?>