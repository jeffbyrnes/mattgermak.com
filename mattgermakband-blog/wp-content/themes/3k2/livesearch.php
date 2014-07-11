<?php
	require("../../../wp-blog-header.php");
	/* The results count is disabled be default as it proved very taxing for my sql server. Try at your own risk!
	$posts = query_posts('posts_per_page=-1&s='.$s.'&what_to_show=posts');
	$countthem = 0;

	if ($posts) { foreach ($posts as $post) { start_wp(); {
		$countthem++;
	} } }


	PS: For some reason it works best if this page is outputted in a single line, with no carriage returns.
	*/

$posts = query_posts('posts_per_page=10&s='.$s.'&what_to_show=posts'); ?><div class="LSRes"><?php if ($posts) { foreach ($posts as $post) { start_wp(); ?><div class="LSRow" onclick="location.href='<?php echo get_permalink() ?>';" style="cursor: pointer;"><a href="<?php echo get_permalink() ?>" rel="bookmark" title='<?php printf(__('Permanent Link to "%s"','k2_domain'), get_the_title()) ?>'><?php the_title(); ?></a> &nbsp;<span class="metalink"><a href="<?php comments_link(); ?>" title="<?php _e('Go the the comments for this entry','k2_domain'); ?>"><?php comments_number('0', '1', '%'); ?></a></span><br /><small><?php /* If 'Dunstan's Time Since' plugin is installed use it; else use default. */ if (function_exists('time_since')) { printf(__('%s ago.','k2_domain'), time_since(abs(strtotime($post->post_date_gmt . " GMT")), time()));	} else { the_time(__('F jS, Y','k2_domain')); } ?> <?php edit_post_link(__('e','k2_domain'),'',''); ?></span></small></div><?php if("comment" == $oddcomment) {$oddcomment="";} else { $oddcomment="comment"; } } ?><?php } else { ?><div class="LSRow" style="text-align: center;"><?php _e('Sorry, no results.','k2_domain'); ?></div><?php }
