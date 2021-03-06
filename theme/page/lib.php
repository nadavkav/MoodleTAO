<?php // $Id$
/**
 * Theme Library
 *
 * @author Mark Nielsen
 * @version $Id$
 * @package page
 **/

/**
 * Prints the theme tabs
 *
 * @return void
 **/
function page_theme_print_tabs() {
    global $COURSE, $CFG, $ME;

    if ($COURSE->format == 'page') {
        require_once($CFG->libdir.'/blocklib.php');
        require_once($CFG->dirroot.'/course/format/page/lib.php');

        $tabs = $row = $inactive = array();
        $selected = '';

        if (page_theme_config('page_menutab') and $tabmenus = page_theme_get_tab_menu()) {
            foreach ($tabmenus as $tabmenu) {
                if ($tabmenu->active) {
                    if (empty($selected)) {
                        $selected = $tabmenu->id;
                    }
                } else if ($tabmenu->id == 'menutree0' and !empty($tabmenu->menu)) {
                /// This code is a HACK and it is legacy - so only check for first menu tree

                /// Nothing in the menu is selected, so check out white list areas
                /// To do this, run through a series of URL tests
                    $locations = array('/blocks/task_list/',
                                       '/blocks/announcement/',
                                       '/blocks/teo_schedule/',
                                       '/mod/pairandshare/index.php',
                                       '/course/format/page/managemenu.php',
                                       '/mod/assess/index.php',
                                       '/blocks/certify/');

                    foreach ($locations as $location) {
                        $testurl = $CFG->wwwroot.$location;
                        if (strpos($ME, $testurl) !== false) {
                            foreach ($tabmenu->menu as $menuitem) {
                                if (isset($menuitem->data->url) and strpos($menuitem->data->url, $testurl) !== false) {
                                    $selected = $tabmenu->id;
                                    break;
                                }
                            }
                            break;
                        }
                    }
                }
                 // HACKING in id and onclick attributes
                $row[] = new tabobject($tabmenu->id, $tabmenu->tablink, $tabmenu->name, $tabmenu->name.'" onclick="this.target=\'_top\'" id="tab'.$tabmenu->id, true);
            }
        }

    /// Master pages are added as tabs
        $secondpage = '';
        if ($pages = page_get_theme_pages($COURSE->id)) {
            // Different URL depending on if this is the front page or not
            if ($COURSE->id == SITEID) {
                $baseurl = $CFG->wwwroot.'/index.php?page=';
            } else {
                $baseurl = "$CFG->wwwroot/course/view.php?id=$COURSE->id&amp;page=";
            }

            $i = 1;
            foreach ($pages as $page) {
                $name = format_string($page->nameone, true);

                // HACKING in an onclick attribute
                $row[] = new tabobject("page$page->id", $baseurl.$page->id, $name, $name.'" onclick="this.target=\'_top\'', true);
                if ($i == 2) {
                    // Get the second page's tabojbect name - might be used later
                    $secondpage = "page$page->id";
                }
                $i++;
            }
        }

        if (empty($selected)) {
            // Didn't find a manage page - look for a course format page to select
            if ($currentpage = page_get_current_page($COURSE->id, false)) {
            /// First check to see if this current page parent is a tab
                $masterpage = page_get_toplevel_parent($currentpage->id, $COURSE->id);
                if (!empty($masterpage->id) and $masterpage->display & DISP_THEME) {
                    $selected = "page$masterpage->id";
            /// Next, check if this page is in the menu
                } else if ($currentpage->display & DISP_MENU) {
                    // In the course menu, default to second master page tab
                    $selected = $secondpage;
                }
            }
        }

    /// Logout tab if needed
        if (page_theme_config('page_signouttab')) {
            $row[]  = new tabobject('logout', "$CFG->wwwroot/login/logout.php?sesskey=".sesskey(), get_string('signout', 'theme_page'));
        }

        $tabs[] = $row;

        echo '<div id="header-tabs" class="header-tabs">';
        print_tabs($tabs, $selected, $inactive);
        if (page_theme_config('page_menutab') and $tabmenus) {
            foreach ($tabmenus as $tabmenu) {
                if (!empty($tabmenu->menuhtml)) {
                    $class = 'menutreeroot';
                } else {
                    $class = 'nomenurender';
                }
                echo "<div id=\"$tabmenu->id\" class=\"$class\">\n$tabmenu->menuhtml\n</div>\n";
            }
        }
        echo '</div>';
    }
}

/**
 * Prints YUI library files for the tab menu
 *
 * @return void
 **/
function page_theme_include_yui_js() {
    global $COURSE, $CFG;

    // Only adding these libraries when we are in a course format page with a manage tab menu
    if ($COURSE->format == 'page' and page_theme_get_tab_menu()) {
        $yuis = array('yahoo-dom-event/yahoo-dom-event.js',
                      'container/container_core-min.js',
                      'menu/menu-min.js',
                      'yahoo/yahoo-min.js',
                      'dom/dom-min.js');
        $jslibs = array();
        foreach ($yuis as $yui) {
            $jslibs[] = "$CFG->wwwroot/lib/yui/$yui";
        }
        echo '<link rel="stylesheet" type="text/css" href="'.$CFG->wwwroot.'/lib/yui/menu/assets/menu.css">';
        require_js($jslibs);
    }
}

/**
 * Includes YUI JavaScript and optionally includes
 * the current and parent theme's javascript.js file.
 *
 * @return void
 **/
function page_theme_include_js() {
    global $CFG, $THEME;

    page_theme_include_yui_js();

    if (page_theme_config('page_javascriptinclude')) {
        if (file_exists($CFG->themedir.'/'.current_theme().'/javascript.js')) {
            echo '<script type="text/javascript" src="'.$CFG->themewww.'/'.current_theme().'/javascript.js"></script>';
        }
    }
    if (page_theme_config('page_parentjavascriptinclude')) {
        if (file_exists($CFG->themedir.'/'.$THEME->parent.'/javascript.js')) {
            echo '<script type="text/javascript" src="'.$CFG->themewww.'/'.$THEME->parent.'/javascript.js"></script>';
        }
    }
}

/**
 * Prints the "Back to X" where is is the name
 * of a page format page.
 *
 * @return void
 **/
function page_theme_print_backto_button() {
    global $CFG, $SESSION, $COURSE;

    if (page_theme_config('page_backtobutton')) {

        if (isset($COURSE->format) and $COURSE->format == 'page') {
            $url = qualified_me();
            $url = strip_querystring($url);

            // URLs where the format could be displayed
            $locations = array($CFG->wwwroot,
                               $CFG->wwwroot.'/',
                               $CFG->wwwroot.'/index.php',
                               $CFG->wwwroot.'/course/view.php',
                               $CFG->wwwroot.'/course/format/page/format.php');

            // See if we aren't on a course format page already
            if (!in_array($url, $locations)) {
                require_once($CFG->dirroot.'/course/format/page/lib.php');

                // Make sure we have a page to go to
                if ($page = page_get_current_page($COURSE->id)) {
                    echo '<p><span class="button"><a href="'.$CFG->wwwroot.'/course/view.php?id='.$page->courseid.'&amp;page='.$page->id.'">'.get_string('backtopage', 'theme_page', page_get_name($page)).'</a></span></p>';
                }
            }
        }
    }
}

/**
 * Gets the tab menu from the course_menu block
 *
 * @return mixed
 **/
function page_theme_get_tab_menu() {
    global $CFG, $COURSE;
    static $tabmenus;

    if (!isset($tabmenus)) {
        $tabmenus = array();

        if ($pagemenus = get_records_sql("SELECT c.id, c.instance, c.visible, p.name
                                            FROM {$CFG->prefix}pagemenu p,
                                                 {$CFG->prefix}course_modules c,
                                                 {$CFG->prefix}modules m
                                           WHERE c.instance = p.id
                                             AND m.id = c.module
                                             AND m.name = 'pagemenu'
                                             AND p.course = $COURSE->id
                                             AND p.useastab = 1
                                        ORDER BY p.taborder, p.name")) {

            require_once($CFG->dirroot.'/mod/pagemenu/locallib.php');

            if ($menuinfos = pagemenu_build_menus($pagemenus, true, true, $COURSE->id)) {
                $count = 0;
                foreach ($menuinfos as $cmid => $menuinfo) {
                    if (isset($menuinfo->menuitems[0])) {
                        $tablink = $menuinfo->menuitems[0]->url;
                    } else {
                        $tablink = '#';
                    }

                    $tabmenu           = new stdClass;
                    $tabmenu->active   = $menuinfo->active;
                    $tabmenu->id       = 'menutree'.$count;
                    $tabmenu->name     = format_string($pagemenus[$cmid]->name);
                    $tabmenu->tablink  = $tablink;
                    $tabmenu->menuhtml = $menuinfo->html;
                    $tabmenus[] = $tabmenu;
                    $count++;
                }
            }
        } else {
            // Legacy
            require_once($CFG->libdir.'/blocklib.php');
            require_once($CFG->dirroot.'/course/format/page/lib.php');

            if ($tabmenu = block_method_result('course_menu', 'get_tab_menu', 'menutree')) {
                $tabmenu->id       = 'menutree0';
                $tabmenu->tablink  = '#';
                $tabmenu->menuhtml = "<div class=\"bd\">\n$tabmenu->menuhtml</div>\n";
                $tabmenus[] = $tabmenu;
            }
        }
    }
    return $tabmenus;
}

/**
 * Prints the onload attribute for the body tag.
 *
 * @param boolean $focus Add setfocus() method to the onload attribute
 * @return void
 **/
function page_theme_print_onload($focus) {
    global $THEME;

    if ($focus) {
        if (empty($THEME->page_onload)) {
            $THEME->page_onload = 'setfocus()';
        } else {
            $THEME->page_onload .= ' setfocus();';
        }
    }
    if (!empty($THEME->page_onload)) {
        echo " onload=\"$THEME->page_onload\"";
    }
}

/**
 * Return a theme config setting.  Returns true
 * if setting is not set since it is assumed
 * that if it is missing then it is on.
 *
 * @param string $config Name of the theme config variable
 * @return void
 **/
function page_theme_config($config) {
    global $THEME;

    if (isset($THEME->$config)) {
        return $THEME->$config;
    } else {
        return true;
    }
}

/**
 * Enforce browser white-list rules
 *
 * @return void
 **/
function page_theme_browser_whitelist(&$THEME) {
    $whitelist = array('Firefox' => 2, 'MSIE' => 6);

    $supported = false;
    foreach ($whitelist as $browser => $version) {
        if (check_browser_version($browser, $version)) {
            $supported = true;
            break;
        }
    }
    // Modify $THEME for unsupported browsers
    if (!$supported) {
        // Remove rounded buttons JavaScript
        $THEME->page_onload = trim(str_replace('pageThemeRoundButtons();', '', $THEME->page_onload));

        // Remove Button Style Sheet from theme sheets
        $key = array_search('styles_buttons', $THEME->sheets);
        if ($key !== false) {
            unset($THEME->sheets[$key]);
        }
        // Remove Button Style Sheet from parent sheets
        $key = array_search('styles_buttons', $THEME->parentsheets);
        if ($key !== false) {
            unset($THEME->parentsheets[$key]);
        }
    }
}

/**
 * Similar to {@link check_browser_version()} but
 * adds an extra check for clients system.
 *
 * @param string $browser Client browser name
 * @param int $version Client browser version
 * @param string $system Client system
 * @return boolean
 * @todo Add support for other systems
 **/
function page_check_client($browser = 'MSIE', $version = 5.5, $system = '') {
    if (check_browser_version($browser, $version)) {
        $agent = $_SERVER['HTTP_USER_AGENT'];

        switch ($system) {
            case 'Macintosh':
                // Best check first
                if (preg_match("/\(Macintosh;/i", $agent, $match)) {
                    return true;
                }
                // Check for operating system
                if (preg_match("/Mac OS X;/i", $agent, $match)) {
                    return true;
                }
                break;
        }
    }

    return false;
}

/**
 * Fixes the realitive urls on the (css files ONLY) passed in from a
 * given theme. Replace Relative urls with ../themename/pix/
 *
 * @param string $css CSS to operate on
 * @param string themename
 * @return string
 **/

function page_theme_fix_relative_urls($css, $themename) {
    return str_replace('url(pix/', "url(../$themename/pix/", $css);
}

/**
 * Comebine styles but perform no string replace
 *
 * @param array of css sheets from theme configs
 * @param string themename
 * @param boolean $fixurls Replace relative urls with ../themename/pix/
 * @return string
 **/
function page_theme_combine_css($css_sheets, $themename, $fixurls = false) {
    global $CFG;

    $style_out = '';
    foreach ($css_sheets as $sheet) {
        $style_out .= "/***** $sheet start *****/\n\n";
        $style_out .= file_get_contents("$CFG->themedir/$themename/$sheet.css");
        $style_out .= "/***** $sheet end *****/\n\n";
    }

    if ($fixurls) {
        $style_out = page_theme_fix_relative_urls($style_out, $themename);
    }

    return $style_out;
}

/**
 * Collects all styles that would be included in calls to styles.php
 * from the various themes (standard, parent, current, etc).
 *
 * @return void
 **/
function page_theme_process_styles($lifetime=300, $forceconfig='', $lang='') {
    global $CFG, $THEME;

    if (!empty($forceconfig)) {
        $current = $forceconfig;
    } else {
        $current = current_theme();
    }

    // Determine if we need to Refresh the client cached stylesheet
    $headers = apache_request_headers();
    $lastmodified = filemtime($CFG->themedir.'/.'); // themes change means styles are modified
    $timestamp = time();
    $ETag = ''.($timestamp - $timestamp%3600); // Rounded to the hour
        
    if (( isset($headers['If-None-Match']) and isset($headers['If-Modified-Since']) ) and
        (strpos($headers['If-None-Match'], "$ETag")) and
        (gmdate("D, d M Y H:i:s", $lastmodified) == $headers['If-Modified-Since']) ) {
            
        // Send Not Modified headers with the same info as in style_sheet_setup
        header('HTTP/1.1 304 Not Modified');
        header('Last-Modified: ' . gmdate("D, d M Y H:i:s", $lastmodified) . ' GMT');
        header('Expires: ' . gmdate("D, d M Y H:i:s", time() + $lifetime) . ' GMT');
        header('Cache-Control: max-age='. $lifetime);
        header('Pragma: ');
        header('Content-type: text/css');  // Correct MIME type
        header('ETag: "'.$ETag.'"');
        exit;

    } else { // Refresh the contents

    /// Process Style Sheets --------------------------------------------------
        ob_start();
        style_sheet_setup(time(), $lifetime, 'standard', $forceconfig, $lang);
        $standard = ob_get_contents();
        ob_end_clean();
        // Replace relative strings in standard
        $css = page_theme_fix_relative_urls($standard, 'standard');

        theme_setup($current);

        $css .= "/**************************************\n";
        $css .= " * THEME NAME: $current and parents   *\n";
        $css .= " **************************************/\n\n";

        $parentsheets = page_theme_get_parent_styles($current);
        foreach($parentsheets as $themename => $styles ) {
            if ($themename == 'page') {
                $fixurls = false;
            } else {
                $fixurls = true;
            }
            $css .= page_theme_combine_css($styles, $themename, $fixurls);
        }

        $css .= page_theme_combine_css($THEME->sheets, $current, ($current != 'page'));
    /// END Process Style Sheets ----------------------------------------------

        // Append Necessary Headers for proper caching
        header('Content-length: '.strlen($css));
        header('Last-Modified: '.gmdate("D, d M Y H:i:s", $lastmodified));
        header('ETag: "'.$ETag.'"');

        echo $css; // Now that header information is done send contents
    }
}

/**
 * Does any processing to $meta before
 * sending it to the browser.
 *
 * @param string $meta Theme meta content
 * @return void
 **/
function page_theme_print_meta($meta) {
    global $CFG;

    $matches = array();
    preg_match_all('/'.preg_quote("<link rel=\"stylesheet\" type=\"text/css\" href=\"$CFG->themewww/", '/').'\w+\/styles\.php(.*)" \/\>/', $meta, $matches);

    // Remove all found styles sheets
    $meta = str_replace($matches[0], '', $meta);

    // Build query string using the query string from the found styles if set
    if (!empty($matches[1][0])) {
        $querystr = $matches[1][0].'&amp;';
    } else {
        $querystr = '?';
    }

    echo "\n<link rel=\"stylesheet\" type=\"text/css\" href=\"$CFG->themewww/page/styles.php{$querystr}single=1\" />\n".$meta;
}

/**
 * Return arrays of parent sheets
 *
 * @param string $theme_name
 * @return array parent and parents parent style sheets
 **/
function page_theme_get_parent_styles($themename) {
    global $CFG;

    $parentsheets = array();
    while (true) {
        // Sets up $THEME
        require("$CFG->dirroot/theme/$themename/config.php");

        // Get parent sheets
        if (!empty($THEME->parentsheets)) {
            $parentsheets[$THEME->parent] = $THEME->parentsheets;
        }

        // Update themename
        $themename = $THEME->parent;
        if (empty($themename)) {
            break;
        }
    }

    return array_reverse($parentsheets, true);
}

?>
