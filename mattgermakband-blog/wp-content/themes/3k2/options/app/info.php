<?php

function k2info($show='') {
        $info = get_k2info($show);
        echo $info;
}

function get_k2info($show='') {
global $current;
    switch($show) {
        case 'version' :
                $output = 'Beta '. $current;
            break;
        case 'scheme' :
            $output = bloginfo('template_url') . '/styles/' . get_option('k2scheme');
            break;
    }
    return $output;
}


function k2_style_info()
{
    $style_info = get_option('k2styleinfo');

    echo stripslashes($style_info);
}

function k2styleinfo_update()
{
    $style_info = '';
    $data = k2styleinfo_parse( get_option('k2scheme') );

    if ('' != $data) {
        $style_info = get_option('k2styleinfo_format');
        $style_info = str_replace("%author%", $data['author'], $style_info);
        $style_info = str_replace("%site%", $data['site'], $style_info);
        $style_info = str_replace("%style%", $data['style'], $style_info);
        $style_info = str_replace("%stylelink%", $data['stylelink'], $style_info);
        $style_info = str_replace("%version%", $data['version'], $style_info);
        $style_info = str_replace("%comments%", $data['comments'], $style_info);
    }

    update_option('k2styleinfo', $style_info, '','');
}

function k2styleinfo_demo()
{
    $style_info = get_option('k2styleinfo_format');
    $data = k2styleinfo_parse( get_option('k2scheme') );

    if ('' != $data) {
        $style_info = str_replace("%style%", $data['style'], $style_info);
        $style_info = str_replace("%stylelink%", $data['stylelink'], $style_info);
        $style_info = str_replace("%author%", $data['author'], $style_info);
        $style_info = str_replace("%site%", $data['site'], $style_info);
        $style_info = str_replace("%version%", $data['version'], $style_info);
        $style_info = str_replace("%comments%", $data['comments'], $style_info);
    } else {
        $style_info = str_replace("%style%", __('Default'), $style_info);
        $style_info = str_replace("%author%", 'Michael Heilemann', $style_info);
        $style_info = str_replace("%site%", 'http://www.binarybonsai.com/', $style_info);
        $style_info = str_replace("%version%", '1.0', $style_info);
        $style_info = str_replace("%comments%", 'Loves you like a kitten.', $style_info);
        $style_info = str_replace("%stylelink%", 'http://getk2.com/', $style_info);
    }

    echo stripslashes($style_info);
}

function k2styleinfo_parse($style_file = '')
{
    // if no style selected, exit
    if ( '' == $style_file ) {
        return;
    }

    // Check whether host has allow_url_fopen enabled. If so,
    // use file() to get style info. If not, fall back to curl.
    if (ini_get(allow_url_fopen)) {
            // open the current style file into an array
            $style_data = implode('', file( get_bloginfo('template_url') . '/styles/' . $style_file ));
    } else if (function_exists(curl_init)) {
            $ch = curl_init();
            $timeout = 5; // set to zero for no timeout
            curl_setopt ($ch, CURLOPT_URL, get_bloginfo('template_url') . '/styles/' . $style_file );
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $style_data = curl_exec($ch);
            curl_close($ch);
    } else {
    printf( __('Looks like your server supports neither %1$s nor %2$s. So you won\'t be able to use the following feature.','k2_domain'), '<a href="http://us3.php.net/manual/en/ref.filesystem.php#ini.allow-url-fopen">url_fopen</a>', '<a href="http://us2.php.net/manual/en/ref.curl.php">cURL</a>' );
    echo '<br />';
    return;
    }

    // parse the data
    preg_match("|Author Name\s*:(.*)|i", $style_data, $author);
    preg_match("|Author Site\s*:(.*)|i", $style_data, $site);
    preg_match("|Style Name\s*:(.*)|i", $style_data, $style);
    preg_match("|Style URI\s*:(.*)|i", $style_data, $stylelink);
    preg_match("|Version\s*:(.*)|i", $style_data, $version);
    preg_match("|Comments\s*:(.*)|i", $style_data, $comments);

    return array('style' => trim($style[1]), 'stylelink' => trim($stylelink[1]), 'author' => trim($author[1]), 'site' => trim($site[1]), 'version' => trim($version[1]), 'comments' => trim($comments[1]));
}
