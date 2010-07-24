<?php // $Id: block_cmsnavigation.php,v 1.7.10.1 2008/03/23 09:36:05 julmis Exp $

class block_cmsnavigation extends block_base {
    function init() {
        $this->title = get_string('cmsnavigation','cms');
        $this->content_type = BLOCK_TYPE_TEXT;
        $this->version = 2005020301;
        $this->navidata = NULL;
    }

    function specialization() {
        // We allow empty titles
        $this->title = isset($this->config->title) ? $this->config->title : '';
    }

    function instance_allow_multiple() {
        return true;
    }

    function is_login_required ( $menuid ) {
        return get_field("cmsnavi", "requirelogin", "id", $menuid);
    }

    function is_guest_allowed ( $menuid ) {
        return get_field("cmsnavi", "allowguest", "id", $menuid);
    }

    function preferred_width() {
        // The preferred value is in pixels
        return 240;
    }

    function hide_header() {
        return empty($this->title);
    }

    function get_css_classname (&$path, &$navi) {
        global $CFG;

        $frontpagelayout = (isloggedin() && !empty($CFG->frontpageloggedin))
                            ? $CFG->frontpageloggedin
                            : $CFG->frontpage;

        $frontpagecheck = false;
        if ( $frontpagelayout == FRONTPAGECMS ) {
            if ( end($path) == 0 && $navi->isfp != 0 && empty($GLOBALS['pagename']) ) {
                $frontpagecheck = true;
            }
        }

        if ( ($navi->pageid == end($path)) or $frontpagecheck ) {
            $class = 'class="cms-active-1';
        } else if ( in_array($navi->pageid, $path) ) {
            $class = 'class="cms-active-2';
        } else {
            $class = 'class="cms-inactive';
        }
        return $class;
    }

    function construct_tree_menu ($parentid, $path, $menuid, $level=1) {

        global $CFG, $USER, $SITE;
        static $top;

        if ( empty($top) ) {
            $top = array();
        }

        if (! empty($this->navidata) ) {

            $this->content->text .= '<ul class="cms-navi-list">' . "\n";

            foreach($this->navidata as $key => $navi) {

                if ( $navi->parentid == $parentid ) {
                    array_push($top, $navi->pageid);

                    $class = $this->get_css_classname($path, $navi);
                    $class .= ' level'.$level.'on"';

                    if ( empty($navi->url) ) {
                        // If the admin has hacked the core Moodle code then he will want the links
                        // to point to the site index.php page for site pages
                        if (empty($navi->pagename)) {
                            $baseurl = $CFG->wwwroot .'/?pid='. (empty($navi->parentid)? '' : $navi->parentid . ',') . $navi->pageid;
                        } else {
                            $tempurl = (!$CFG->slasharguments)
                                        ? $CFG->wwwroot.'/index.php?page='.$navi->pagename
                                        : $CFG->wwwroot.'/index.php/'. $navi->pagename;
                        $baseurl = ($navi->course == SITEID)
                                    ? ( $tempurl )
                                    // TODO: should the URL below be rendered with slasharguments? 
                                : $CFG->wwwroot.'/course/view.php?id='. $navi->course .'&amp;page='.$navi->pagename;
                        }

                    } else {
                        $baseurl = $navi->url;
                    }

                    $target = (!empty($navi->url) && !empty($navi->target)) ? ' target="'. $navi->target .'"' : '';

                    $this->content->text .= '<li '. $class .'><a '.
                                            ' href="'. $baseurl . '"'. $target .
                                            '>' . stripslashes($navi->title) .
                                        '</a>' . "\n";
                    if ( in_array($navi->pageid, $path) or (empty($path) && $navi->isfp) ) {
                        $this->construct_tree_menu($navi->pageid, $path, $menuid, $level+1);
                    }

                    $this->content->text .= '</li>' . "\n";

                    array_pop($top);

                }
            }
            $this->content->text .= '</ul>' . "\n";
        }

    }

    /**
     * Check if page have parent id.
     *
     * As navidata array already exists we can use it as searcable
     * index because of its structure ( array of objects where pageid
     * is also an array key ).
     * @param int $pageid
     * @param bool $returnid
     * @return mixed
     */
    function has_parent ( $pageid, $returnid=FALSE) {

        $pageid = intval($pageid);

        if ( !empty($this->navidata[$pageid]) ) {
            $page = $this->navidata[$pageid];
                if ( $page->parentid != 0 ) {
                if ( !$returnid ) {
                    return true;
                } else {
                    // return first item.
                    return (int) $page->parentid;
                }
            }
        }
        return false;
    }

    /**
     * Create a id path from requested page all away to root level.
     *
     * @param int $pageid;
     * @return array
     */
    function get_path($pageid) {

        $pagearray = array();
        array_push($pagearray, $pageid);
        while ( $pageid = $this->has_parent($pageid, true) ) {
            $pagearray[$pageid] = $pageid;
        }
        return array_reverse($pagearray);

    }

    function get_content() {

        global $CFG, $USER, $SITE, $COURSE;

        if ($this->content !== NULL) {
            return $this->content;
        }

        $pagename = optional_param('page', '', PARAM_FILE);
        $pageid   = optional_param('pid', 0, PARAM_INT);
        if ( defined('SITEID') && SITEID == $this->instance->pageid && $CFG->slasharguments ) {
            // Support sitelevel slasharguments
            // in form /index.php/<pagename>
            $relativepath = get_file_argument(basename($_SERVER['SCRIPT_FILENAME']));
            if ( preg_match("/^(\/[a-z0-9\_\-]+)/i", $relativepath) ) {
                $args = explode("/", $relativepath);
                $pagename = clean_param($args[1], PARAM_FILE);
            }
            unset($args, $relativepath);
        }

        // set menuid according to block configuration or if no menu has been configured yet use
        // the first available menu if it exists
        if (!empty($this->config->menu)) {
            $menuid = intval($this->config->menu);
        } else if ($menus = get_records('cmsnavi', 'course', $this->instance->pageid, 'id ASC')) {
            $menu = array_pop($menus);
            $menuid = $menu->id;
            $this->config->menu = $menuid;
            $this->instance_config_commit();
        } else {
            $menuid = 0;
        }

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';

        $menurequirelogin = $this->is_login_required($menuid);
        $menuallowguest   = $this->is_guest_allowed($menuid);

        if (($menurequirelogin && empty($USER->loggedin)) or
            ($menurequirelogin && isguest() && !$menuallowguest)) {
              return $this->content;
        }

        $this->navidata = get_records_sql("SELECT n.pageid, n.parentid, n.title, n.isfp, n.pagename,
                                            n.url, n.target, p.publish, cn.requirelogin, cn.course
                                           FROM {$CFG->prefix}cmsnavi_data n,
                                                {$CFG->prefix}cmspages p,
                                                {$CFG->prefix}cmsnavi cn
                                           WHERE n.pageid = p.id AND p.publish = 1
                                           AND n.naviid = '$menuid'
                                           AND (cn.id = '$menuid')
                                           AND n.showinmenu = '1'
                                           ORDER BY sortorder");

        if ( empty($pageid) && !empty($pagename) ) {
            $pageid = get_field('cmsnavi_data', 'pageid', 'pagename', $pagename, 'naviid', $menuid);
        }

        $path = $this->get_path($pageid);

        // Wrap it inside div element which width you can control
        // with CSS styles in styles.php file.
        $this->content->text .= "\n" . '<div class="cms-navi">' . "\n";
        $this->construct_tree_menu(0, $path, $menuid);
        $this->content->text .= '</div>'."\n";

        if (!empty($USER->editing) and !empty($pageid)) {
            $toolbar = '';

            $stradd     = get_string('add');
            $addlink = $CFG->wwwroot .'/cms/pageadd.php?id='. $pageid .'&amp;'.
                  'sesskey='. $USER->sesskey .'&amp;parentid=0&amp;course=' . $COURSE->id .'';
            $addicon = $CFG->wwwroot .'/cms/pix/add.gif';
            $toolbar .=  '<a href="'. $addlink .'" target="reorder"><img src="'. $addicon .'"'
                         .  ' width="11" height="11" alt="'. $stradd .'"'
                         . ' title="'. $stradd .'" /></a>';

            $strreorder = get_string('reorder', 'cms');
            $reorderlink = $CFG->wwwroot .'/cms/reorder.php?source='. $pageid .
                '&amp;sesskey='. $USER->sesskey;
            $reordericon = $CFG->wwwroot .'/pix/t/move.gif';

            $toolbar .=  ' <a href="'. $reorderlink .'" target="reorder"><img src="'. $reordericon .'"'
                         .  ' width="11" height="11" alt="'. $strreorder .'"'
                         . ' title="'. $strreorder .'" /></a>';
            $this->content->footer = $toolbar;
        }

        return $this->content;

    }
}

?>