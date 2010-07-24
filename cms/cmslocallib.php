<?php // $Id: cmslocallib.php,v 1.12.10.1 2008/03/23 09:36:06 julmis Exp $
/**
 * This file contains necessary functions and class to administrate
 * cms menu and page content on site or course level.
 *
 * @author Janne Mikkonen
 * @version  $Id: cmslocallib.php,v 1.12.10.1 2008/03/23 09:36:06 julmis Exp $
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package CMS_plugin
 * @todo Check if there are deprecated functions.
 */

require_once('cmslib.php');

/**
* Print menu selection list.
*
* @param int $courseid
* @return void
*/
function cms_print_menus ($courseid=1) {

    cms_print_newmenulink($courseid);
    cms_print_allmenus($courseid);

}

/**
* Print all menus.
*
* @param int $courseid
* @return void
*/
function cms_print_allmenus ($courseid=1) {

    global $CFG, $USER;

    $menus = get_records("cmsnavi", "course", $courseid);
    $pixpath  = "$CFG->wwwroot/cms/pix";

    if (is_array($menus)) {
        include_once('html/printmenus.php');
    } else {
        echo "<div align=\"center\">";
        echo "<p>";
        print_string("nomenus","cms");
        echo "</p></div>";
    }

}

/**
* Print Add new link into menu administration page.
*
* @param int $courseid
* @return void
*/
function cms_print_newmenulink ($courseid=1) {

    global $USER;
    $straddnew = get_string('addnewmenu','cms');

    echo "<div align=\"center\"><p><a href=\"menuadd.php?course=$courseid&amp;";
    echo "sesskey=$USER->sesskey\">$straddnew</a></p></div>\n";

}

/**
* Print pages index page.
*
* @param int $menuid
* @param int $courseid
* @return void
*/
function cms_print_pages ($menuid=1, $courseid=1) {

    global $CFG, $USER, $context;

    $menuid = clean_param($menuid, PARAM_INT);
    if (!$menus  = get_records("cmsnavi", "course", $courseid)) {
        redirect('menus.php?course='.$courseid.'&amp;sesskey='.sesskey());
    }

    $menuids = array();

    foreach ($menus as $menu) {
        $menuids[] = (int) $menu->id;
    }

    if ( !in_array($menuid, $menuids) ) {
        $menuid = $menuids[0];
    }

    include_once('html/navimenu.php');
    include_once('html/pagesindex.php');

}

/**
* Print add new page link
*
* @global object $USER
* @param int $menuid
* @param int $courseid
* @return void
*/
function cms_print_addnewpage ($menuid, $courseid=1) {

    global $USER;

    $menuid = clean_param($menuid, PARAM_INT);

    echo "<p><a href=\"pageadd.php?id=$menuid&amp;sesskey=$USER->sesskey&amp;course=$courseid\">";
    print_string("addnewpage","cms");
    echo "</a></p>\n";

}

/**
* Get navigation data for page index.
*
* @param int $parentid
* @param int $menuid
* @return array
*/
function cms_get_navi ($parentid, $menuid=1) {

    global $CFG, $SITE;

    $menuid   = intval($menuid);
    $parentid = intval($parentid);

    $sql  = "SELECT n.*, p.publish, ";
    $sql .= "cn.requirelogin FROM {$CFG->prefix}cmsnavi_data AS n, ";
    $sql .= "{$CFG->prefix}cmspages AS p, ";
    $sql .= "{$CFG->prefix}cmsnavi AS cn ";
    $sql .= "WHERE n.pageid = p.id AND p.publish = 1 ";
    $sql .= "AND n.naviid= $menuid ";
    $sql .= "AND cn.id = $menuid ";
    $sql .= "AND n.parentid = $parentid";

    return get_records_sql($sql);

}

/**
* Get all page data.
*
* @global object $CFG
* @param int $pageid
* @return object
*/
function cms_get_pagedata ($pageid) {

    global $CFG;

    $pageid = clean_param($pageid, PARAM_INT);

    $sql  = "SELECT p.*, n.title, n.showinmenu, n.id AS nid, n.naviid, ";
    $sql .= "n.parentid, n.url, n.target, n.pagename, n.showblocks ";
    $sql .= "FROM {$CFG->prefix}cmspages AS p, ";
    $sql .= "{$CFG->prefix}cmsnavi_data AS n ";
    $sql .= "WHERE n.pageid = p.id AND n.pageid = ". $pageid ."";

    if ($data = get_record_sql($sql)) {
        $data->parentname = get_field('cmsnavi_data', 'pagename', 'pageid', $data->parentid);
    }

    return $data;

}

/**
* Resets menu order of selected menu.
*
* @global object $CFG
* @staticvar int $count
* @param int $parentid
* @param int $menuid
* @return bool
*/
function cms_reset_menu_order ($parentid, $menuid) {

    global $CFG;
    static $count;

    $parentid = intval($parentid);
    $menuid   = intval($menuid);

    if ( empty($count) ) {
        $count = 0;
    }

    $pages = get_records_sql("SELECT id, pageid, parentid
                              FROM {$CFG->prefix}cmsnavi_data
                              WHERE parentid = $parentid
                              AND naviid = $menuid");

    if (! empty($pages) ) {
        $count++;
        $pagecount = 1;
        foreach ($pages as $page) {

            $sortorder = (1000 * $count) + $pagecount;
            set_field("cmsnavi_data", "sortorder", $sortorder, "pageid", intval($page->pageid));

            cms_reset_menu_order($page->pageid, $menuid);
            $pagecount++;

        }
        $count--;
    }

    return true;

}

/**
* This class takes care of page index output almost completely.
*
* @package CMS_plugin
*/
class cms_pages_menu {

    /**
    * Array container for pages
    * @var array $pages
    */
    var $pages = NULL;
    /**
    * Menu id
    * @var int $menuid
    */
    var $menuid = NULL;
    /**
    * Course id
    * @var int $courseid
    */
    var $courseid = NULL;
    /**
    * String holder for image up on pages index.
    * @var string $imgup
    */
    var $imgup = NULL;
    /**
    * String holder for image down on pages index.
    * @var string $imgdown
    */
    var $imgdown;
    /**
    * String holder for image right on pages index.
    * @var string $imgright
    */
    var $imgright;
    /**
    * String holder for image left on pages index.
    * @var string $imgleft
    */
    var $imgleft;
    /**
    * String holder for publish image on pages index.
    * @var string $imgpub
    */
    var $imgpub;
    /**
    * String holder for unpublish image on pages index.
    * @var string $imgunpub
    */
    var $imgunpub;
    /**
    * String holder for blank image on pages index.
    * @var string $imgblank
    */
    var $imgblank;
    /**
    * Language string for default page.
    * @var string $strisdefault
    */
    var $strisdefault;
    /**
    * Language string for set as default page.
    * @var string $strsetasdefault
    */
    var $strsetasdefault;
    /**
    * Language string for published.
    * @var string $strpublished
    */
    var $strpublished;
    /**
    * Language string for unpublished.
    * @var string $strunpublished
    */
    var $strunpublished;
    /**
    * Site container.
    * @var object $site
    */
    var $siteid;
    /**
    * wwwroot for internal use.
    * @var string $wwwroot
    */
    var $wwwroot;

    /**
    * Constructor sets up needed variables and
    * fetch pages information from database.
    *
    * @param int $menuid
    * @param int $courseid
    */
    function cms_pages_menu ( $menuid, $courseid=1 ) {

        global $CFG, $USER, $context;

        $this->menuid = clean_param($menuid, PARAM_INT);
        $this->courseid = clean_param($courseid, PARAM_INT);
        // Get strings
        $this->strisdefault    = get_string('isdefaultpage', 'cms');
        $this->strsetasdefault = get_string('setdefault'   , 'cms');
        $this->strpublished    = get_string('published'    , 'cms');
        $this->strunpublished  = get_string('unpublished'  , 'cms');
        // Cache images. Pointless to initialize them in
        // methods every time.
        $pixpath = $CFG->wwwroot .'/cms/pix';
        $this->imgup   = '<img src="'. $pixpath .'/up.gif" width="11" height="11" alt="" border="0" />';
        $this->imgdown = '<img src="'. $pixpath .'/down.gif" width="11" height="11" alt="" border="0" />';
        $this->imgright = '<img src="'. $pixpath .'/right.gif" width="11" height="11" alt="" border="0" />';
        $this->imgleft  = '<img src="'. $pixpath .'/left.gif" width="11" height="11" alt="" border="0" />';
        $this->imgpub  = '<img src="'. $pixpath .'/yespublish.gif" alt="' .
                         stripslashes($this->strpublished) .'" title="' .
                         stripslashes($this->strpublished) .'" />';
        $this->imgunpub = '<img src="'. $pixpath .'/nopublish.gif" alt="' .
                          stripslashes($this->strunpublished) .'" title="' .
                          stripslashes($this->strunpublished) .'" />';
        $this->imgblank = '<img src="'. $pixpath .'/blank.gif" width="11" height="11" alt="" />';

        $sql  = "SELECT n.pageid AS id, n.naviid, n.pagename, ";
        $sql .= "n.title, n.isfp,n.parentid,n.url, ";
        $sql .= "n.target,p.publish, p.created, p.modified ";
        $sql .= "FROM {$CFG->prefix}cmsnavi_data AS n, ";
        $sql .= "{$CFG->prefix}cmspages AS p ";
        $sql .= "WHERE n.pageid = p.id AND ";
        $sql .= " n.naviid = ". $this->menuid ." ";
        $sql .= "ORDER BY n.sortorder";

        $this->pages = get_records_sql($sql);
        $this->siteid = SITEID;
        $this->wwwroot = $CFG->wwwroot;
        $this->path = array();
        $this->tmparray = array();
        if ($this->pages) {
            foreach ( $this->pages as $page ) {
                $this->tmparray[$page->parentid][] = $page->id;
            }
        }
    }

    /**
    * PHP5 styled constructor
    * @see cms_pages_menu()
    */
    function __construct ( $menuid, $courseid=1 ) {
        $this->cms_pages_menu($menuid, $courseid);
    }

    /**
    * Check if current page has parent page. For internal use only.
    * @param int $pageid
    * @param bool $returnid
    * @return mixed Returns parent page id if enable or true/false
    */
    function __hasParent ( $pageid, $returnid=FALSE) {

        $pageid = intval($pageid);

        if ( !empty($this->pages[$pageid]) ) {
            $page = $this->pages[$pageid];
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
    * Check if current page has child page. For internal use only.
    * @param int $pageid
    * @param bool $returnid
    * @return mixed Returns child page id if enable or true/false
    */
    function __hasChildren ( $pageid, $returnid=FALSE ) {

        $pageid = intval($pageid);

        if ( !empty($this->tmparray[$pageid]) ) {
            if ( !$returnid ) {
                return true;
            } else {
                // return first item in array.
                return (int) $this->tmparray[$pageid][0];
            }
        }

        return false;

    }

    /**
    * Check if current page has sibling page. For internal use only.
    * @param int $parentid
    * @return bool
    */
    function __hasSibling ( $parentid ) {

        $parentid = intval($parentid);
        if ( !empty($this->tmparray[$parentid]) ) {
                    return true;
                }

        return false;

    }

    /**
    * Check if current page is the first page in current level.
    * @param int $parentid
    * @param int $pageid
    * @return bool
    */
    function __firstAtLevel ( $parentid, $pageid ) {

        $pageid = intval($pageid);
        $parentid = intval($parentid);

        if ( !empty($this->tmparray[$parentid]) ) {
            $first = array_shift($this->tmparray[$parentid]);
            array_unshift($this->tmparray[$parentid], $first);
            if ( $first == $pageid ) {
                        return true;
                    }
                }

        return false;

    }

    /**
    * Check if current page is the last page in current level.
    * @param int $parentid
    * @param int $pageid
    * @return bool
    */
    function __lastAtLevel ( $parentid, $pageid ) {

        $pageid = intval($pageid);
        $parentid = intval($parentid);

        if ( !empty($this->tmparray[$parentid]) ) {
            if ( end($this->tmparray[$parentid]) == $pageid ) {
                        return true;
                    }
                }
        return false;

    }

    /**
    * Construct data for table class used in pagesindex page.
    * @global object $USER
    * @staticvar array $output
    * @staticvar int $count
    * @staticvar object $prevpage
    * @param int $parentid
    * @return array
    */
    function get_page_tree_rows ( $parentid ) {

        global $USER;
        static $output, $count, $prevpage;

        if ( empty($output) ) {
            $output = array();
        }
        if ( empty($count) ) {
            $count = 0;
        }

        if ( !empty($this->pages) ) {

            $count++;
            foreach ( $this->pages as $p ) {
                if ( $p->parentid == $parentid ) {
                    $row = array();

                    $row[] = '<input type="checkbox" name="id" value="'.
                         $p->id .'" />';

                    $hrefup = '<a href="pages.php?sesskey='. $USER->sesskey .
                          '&amp;sort=up&amp;menuid='. $p->naviid . '&amp;pid='. $p->id .
                          '&amp;mid='. $p->parentid .'&amp;course='. $this->courseid .'">'.
                          $this->imgup .'</a>';

                    $hrefdown = '<a href="pages.php?sesskey='. $USER->sesskey .
                            '&amp;sort=down&amp;menuid='. $p->naviid .'&amp;pid='. $p->id .
                            '&amp;mid='. $p->parentid .'&amp;course='. $this->courseid .'">'.
                            $this->imgdown .'</a>';


                    $hrefleft='';
                    if ( !empty($prevpage->id) or $this->__hasParent($p->id) ) {
                        $moveto = $this->__hasParent($p->parentid, true);
                        if ( empty($moveto) ) {
                            $moveto = '0';
                        }
                        $hrefleft = '<a href="pages.php?sesskey='. $USER->sesskey .
                                    '&amp;move='. $moveto .'&amp;pid='. $p->id .
                                    '&amp;menuid='. $p->naviid .'&amp;course='.
                                    $this->courseid .'" alt="">'. $this->imgleft .'</a>';
                    }

                    $hrefright = '';
                    if ( !empty($prevpage->id) ) {
                        $hrefright  = '<a href="pages.php?sesskey='. $USER->sesskey .
                                      '&amp;move='. $prevpage->id .'&amp;pid='. $p->id .
                                      '&amp;menuid='. $p->naviid .'&amp;course='.
                                      $this->courseid .'" alt="">'. $this->imgright .'</a>';
                    }

                    $moverow = '<table border="0" cellpadding="2"><tr>';

                    if ( $this->__firstAtLevel($p->parentid, $p->id) &&
                         $this->__hasSibling($p->parentid) ) {
                        $moverow .= '<td>'. $hrefdown .'</td><td>'. $this->imgblank .'</td>';
                    } else if ( $this->__lastAtLevel($p->parentid, $p->id) &&
                                $this->__hasSibling($p->parentid) ) {
                        $moverow .= '<td>'. $this->imgblank .'</td><td>'. $hrefup .'</td>';
                    } else if ( $this->__hasSibling($p->parentid) ) {
                        $moverow .= '<td>'.$hrefdown .'</td><td>'. $hrefup .'</td>';
                    } else {
                        $moverow .= '<td>'.$this->imgblank .'</td><td>'. $this->imgblank .'</td>';
                    }

                    // Add level changers.
                    if ( $this->__hasParent($p->id) ) {
                        $moverow .= '<td>'. $hrefleft .'</td>';
                    } else {
                        $moverow .= '<td>'. $this->imgblank .'</td>';
                    }
                    if ( $this->__hasSibling($p->parentid) && !$this->__firstAtLevel($p->parentid, $p->id) ) {
                        $moverow .= '<td>'. $hrefright .'</td>';
                    }

                    $row[] = $moverow .'</tr></table>';

                    $pageurl = '';
                    if ( !empty($this->siteid) ) {
                        $pageurl = ($this->courseid > $this->siteid) ?
                                   $this->wwwroot .'/course/view.php?id='. $this->courseid .
                                   '&amp;pid='. $this->__get_path($p->id) :
                                   $this->wwwroot .'/index.php?pid='. $this->__get_path($p->id);
                    }

                    // If link is a direct url to resource or webpage
                    if ( !empty($p->url) ) {
                        $pageurl = $p->url;
                    }

                    $p->title  = '<a href="'. $pageurl .'" target="_blank">'. s($p->title) .'</a>';
                    $pagetitle  = str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;", $count - 1);
                    $pagetitle .= !empty($p->isfp) ?
                                  '<strong>'. $p->title .'</strong>' :
                                  $p->title;
                    $row[] = $pagetitle;

                    $default = !empty($p->isfp) ? stripslashes($this->strisdefault) :
                               ((!empty($p->publish) && empty($p->parentid)) ?
                               '<a href="pages.php?course='. $this->courseid .'&amp;sesskey='. $USER->sesskey .
                               '&amp;setfp='. $p->id .'">'. stripslashes($this->strsetasdefault) .'</a>' : '');
                    $row[] = $default;

                    $publishurl = '<a href="pages.php?sesskey='. $USER->sesskey .
                                  '&amp;pid='. $p->id .'&amp;menuid='. $p->naviid .
                                  '&amp;course='. $this->courseid;
                    $publish = !empty($p->publish) ? $publishurl .'&amp;publish=no">'. $this->imgpub .'</a>'
                                                   : $publishurl .'&amp;publish=yes">'. $this->imgunpub .'</a>';
                    $row[] = $publish;

                    // Get version information.
                    $version = cms_get_page_version($p->id);
                    $historylink = '<a href="pagehistory.php?sesskey='. $USER->sesskey .'&amp;course='.
                                   $this->courseid .'&amp;menuid='. $p->naviid .'&amp;pageid='. $p->id .
                                   '">' . s($version) .'</a>';
                    $row[] = $historylink; //s($version);
                    $row[] = userdate($p->modified, "%x %X");

                    array_push($output, $row);
                    $this->get_page_tree_rows ($p->id);
                    $prevpage = $p;
                }
            }
            $count--;
        }

        return $output;
    }

    /**
    * Create path string from page ids like 2,3,4
    * @param int $pageid
    * @return string
    */
    function __get_path($pageid) {

        $pagearray = array();
        array_push($pagearray, $pageid);
        while ( $pageid = $this->__hasParent($pageid, true) ) {
            array_push($pagearray,$pageid);
        }
        return implode(",", array_reverse($pagearray));

    }

}

/**
* Get child page ids of selected page.
*
* @param int $parentid
* @return array An array of ids.
*/
function cms_get_children_ids ( $parentid ) {

    static $childrenids;

    $parentid = intval($parentid);

    if ( empty($childrenids) ) {
        $childrenids = array();
    }

    if ( $children = get_records("cmsnavi_data", "parentid", $parentid) ) {
        foreach ( $children as $child ) {
            array_push($childrenids, intval($child->pageid));
            cms_get_children_ids($child->pageid);
        }
    }

    return $childrenids;

}

/**
* Include necessary javascript scripts into editpage form
* @return void
*/
function include_webfx_scripts () {
    global $CFG;
    echo "\n";
    ?>
    <script type="text/javascript" src="<?php echo $CFG->wwwroot ?>/cms/js/tabpane/local/webfxlayout.js"></script>
    <link type="text/css" rel="stylesheet" href="<?php echo $CFG->wwwroot ?>/cms/js/tabpane/css/luna/tab.css" />
    <script type="text/javascript" src="<?php echo $CFG->wwwroot ?>/cms/js/tabpane/js/tabpane.js"></script>
    <?php
    echo "\n";
}

/**
* Get version information for selected page. Information
* can be a single string or an object.
*
* @param int $pageid
* @param bool $object Return data as a string or object.
* @return mixed Returns true/false or an object
*/
function cms_get_page_version ($pageid, $object=false) {
    global $CFG;
    $pageid = intval($pageid);
    // Possibly we should use SELECT max(version) FROM table WHERE pageid=$pageid
    // to avoid unecessary data? DB compatibility? Moodle restrictions?
    $version = get_record_sql("SELECT pageid, version
                              FROM {$CFG->prefix}cmspages_history
                              WHERE pageid = $pageid
                              ORDER BY id DESC", true);
    if ( !$object ) {
        if ( empty($version) ) {
            return '1.0';
        }
        return $version->version;
    } else {
        return $version;
    }
}
/**
* Reorder pages
* @param int $id Page id.
* @param int $parent Parent id.
* @param int $menuid Menu id.
* @param string $direction.
* @return bool
*/
function cms_reorder($id, $parent, $menuid, $direction) {

    global $CFG;

    $sql  = "SELECT id, pageid, parentid ";
    $sql .= "FROM ". $CFG->prefix ."cmsnavi_data ";
    $sql .= "WHERE parentid=". $parent ." AND naviid = ". $menuid ." ";
    $sql .= "ORDER BY sortorder";

    if (! ($results = get_records_sql($sql)) ) {
        return false;
    }

    $records = array();
    $tmp     = array();

    $i = 0;
    foreach ($results as $row) {
        $records[$i]['id'] = intval($row->pageid);
        $records[$i]['sortorder'] = intval($i + 1);
        array_push($tmp, $records[$i]);
        $i++;
    }
    unset($results, $i, $row);

    $rows = intval(count($records));

    for ($i = 0; $i < $rows; $i++) {

        if ( $tmp[$i]['id'] == $id ) {
            // Check direction and can we move up?
            switch (strtolower($direction)) {
                case 'up':
                if ($i != 0) {
                    $tmp[$i]['sortorder'] -= 1;
                    $tmp[$i - 1]['sortorder'] += 1;
                }
                break;
                case 'down':
                if ($i < ($rows - 1)) {
                    $tmp[$i]['sortorder'] += 1;
                    $tmp[$i + 1]['sortorder'] -= 1;
                }
                break;
            }

        }
    }

    // Update menu table

    foreach ($tmp as $record) {

        if (! set_field("cmsnavi_data", "sortorder", $record['sortorder'], "pageid", $record['id'])) {
            return false;
        }

    }
    return true;
}

/**
 * Check if given name already exists at same course level.
 *
 * @uses $CFG
 * @param string $pagename Page name.
 * @param int $naviid Menu id.
 * @param int $courseid Course or site id.
 * @return bool
 */
function cms_pagename_exists ($pagename, $courseid) {
    global $CFG;
    return record_exists_sql("SELECT nd.id, nd.pagename
                              FROM
                                {$CFG->prefix}cmsnavi_data nd
                              LEFT JOIN
                                {$CFG->prefix}cmsnavi c
                              ON nd.naviid = c.id
                              WHERE nd.pagename = '$pagename'
                              AND c.course = '$courseid'");
}
?>