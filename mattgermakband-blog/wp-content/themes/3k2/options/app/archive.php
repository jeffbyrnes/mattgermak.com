<?php
/* This class holds all the code for creating, deleting and setting up the archives, powered by Extended Live Archives: http://www.sonsofskadi.net/extended-live-archive/ */

class archive {
    function create_archive() {
        global $wpdb, $user_ID;
        get_currentuserinfo();
        $check = $wpdb->query("SELECT * from $wpdb->posts WHERE post_title = 'Archives'");
            if(!$check) {
                $message = "Do not edit this page";
                $title_message = 'Archives';
                $content = apply_filters('content_save_pre', $message);
                $post_title = apply_filters('title_save_pre', $title_message);
                $now = current_time('mysql');
                $now_gmt = current_time('mysql', 1);
                $post_author = $user_ID;
                $id_result = $wpdb->get_row("SHOW TABLE STATUS LIKE '$wpdb->posts'");
                $post_ID = $id_result->Auto_increment;
                $post_name = sanitize_title($post_title, $post_ID);
                $ping_status = get_option('default_ping_status');
                $comment_status = get_option('default_comment_status');

                $postquery ="INSERT INTO $wpdb->posts
            (ID, post_author, post_date, post_date_gmt, post_content, post_title, post_excerpt,  post_status, comment_status, ping_status, post_password, post_name, to_ping, post_modified, post_modified_gmt, post_parent, menu_order)
            VALUES
            ('$post_ID', '$post_author', '$now', '$now_gmt', '$content', '$post_title', '', 'static', '$comment_status', '$ping_status', '', '$post_name', '', '$now', '$now_gmt', '', '')";
            $result = $wpdb->query($postquery);
            $metaquery = "INSERT INTO $wpdb->postmeta(meta_id, post_id, meta_key, meta_value) VALUES('', '$post_ID', '_wp_page_template', 'page-archives.php')";
            $result2 = $wpdb->query($metaquery);
    }
}

function delete_archive() {
global $wpdb;
    $check = $wpdb->query("SELECT * from $wpdb->posts WHERE post_title = 'Archives'");
        if($check) {
    $burninate = $wpdb->query("DELETE from $wpdb->posts WHERE post_title = 'Archives' and post_status = 'static'");
    $result = $wpdb->query($burninate);
    }
}

function setup_archive() {
    global $wpdb;

    if (file_exists(ABSPATH . 'wp-content/plugins/UltimateTagWarrior/ultimate-tag-warrior-core.php') && in_array('UltimateTagWarrior/ultimate-tag-warrior.php', get_option('active_plugins'))) {
        $menu_order="chrono,tags,cats";
    } else {
        $menu_order="chrono,cats";
    }

    $initSettings = array(

    // we always set the character set from the blog settings
        'newest_first' => 0,
        'num_entries' => 1,
        'num_entries_tagged' => 0,
        'num_comments' => 1,
        'fade' => 1,
        'hide_pingbacks_and_trackbacks' => 1,
        'use_default_style' => 1,
        'paged_posts' => 1,
        'selected_text' => '',
        'selected_class' => 'selected',
        'comment_text' => '<span>%</span>',
        'number_text' => '<span>%</span>',
        'number_text_tagged' => '(%)',
        'closed_comment_text' => '<span>%</span>',
        'day_format' => 'jS',
        'error_class' => 'alert',
    // allow truncating of titles
        'truncate_title_length' => 0,
        'truncate_cat_length' => 25,
        'truncate_title_text' => '&#8230;',
        'truncate_title_at_space' => 1,
        'abbreviated_month' => 1,
        'tag_soup_cut' => 0,
        'tag_soup_X' => 0,
    // paged posts related stuff
        'paged_post_num' => 15,
        'paged_post_next' => 'next 15 posts &raquo;',
        'paged_post_prev' => '&laquo; previous 15 posts',
    // default text for the tab buttons
        'menu_order' => $menu_order,
        'menu_month' => 'Chronology',
        'menu_cat' => 'Taxonomy',
        'menu_tag' => 'Folksonomy',
        'before_child' => '&nbsp;&nbsp;&nbsp;',
        'after_child' => '',
        'loading_content' => '<img src="'.get_bloginfo('template_url').'/images/spinner.gif" class="elaload" alt="Spinner" />',
        'idle_content' => '',
        'excluded_categories' => '0');

    if (function_exists('af_ela_set_config')) {
        $ret = af_ela_set_config($initSettings);
    }

    return $ret;
    }

    function __call($method, $args) {
        if ($method == 'id' || $method == 'ID') {
            return $this->_id;
        } else if (in_array($method, $this->fields)) {
               return getset($this, $method, $args);
        } else {
               print "Invalid method \"$method\" called, in archive::__call.";
               exit();
        }
    }
}
