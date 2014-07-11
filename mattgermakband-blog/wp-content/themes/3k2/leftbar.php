<div class="leftbar">
    <br />
    <br />
    <ul class="menu">
        <li class="<?php if (((is_home()) && !(is_paged())) or (is_archive()) or (is_single()) or (is_paged()) or (is_search())) { ?>current_page_item<?php } else { ?>page_item<?php } ?>"><a href="<?php echo get_settings('home'); ?>"><?php _e('news','k2_domain'); ?></a></li>
        <?php wp_list_pages('sort_column=menu_order&depth=1&title_li='); ?>
    </ul>
    <br />
    <br />
</div>
