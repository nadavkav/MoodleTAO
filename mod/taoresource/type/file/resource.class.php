<?php
/**
 *
 * @author  Piers Harding  piers@catalyst.net.nz
 * @version 0.0.1
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License, mod/taoresource is a work derived from Moodle mod/resoruce
 * @package taoresource
 *
 */

/**
* Extend the base resource class for file resources
*/
class taoresource_file extends taoresource_base {

    function taoresource_file($cmid=0, $identifier=false) {
        parent::taoresource_base($cmid, $identifier);
    }

    var $parameters;
    var $maxparameters = 5;


    /**
    * Sets the parameters property of the extended class
    *
    * @param    USER  global object
    * @param    CFG   global object
    */
    function set_parameters() {
        global $USER, $CFG;

        $site = get_site();

        $littlecfg = new object;       // to avoid some notices later
        $littlecfg->wwwroot = $CFG->wwwroot;


        $this->parameters = array(
                'label2'          => array('langstr' => "",
                                           'value'   =>'/optgroup'),
                'label3'          => array('langstr' => get_string('course'),
                                           'value'   => 'optgroup'),

                'courseid'        => array('langstr' => 'id',
                                           'value'   => $this->course->id),
                'coursefullname'  => array('langstr' => get_string('fullname'),
                                           'value'   => $this->course->fullname),
                'courseshortname' => array('langstr' => get_string('shortname'),
                                           'value'   => $this->course->shortname),
                'courseidnumber'  => array('langstr' => get_string('idnumbercourse'),
                                           'value'   => $this->course->idnumber),
                'coursesummary'   => array('langstr' => get_string('summary'),
                                           'value'   => $this->course->summary),
                'courseformat'    => array('langstr' => get_string('format'),
                                           'value'   => $this->course->format),
                'courseteacher'   => array('langstr' => get_string('wordforteacher'),
                                           'value'   => $this->course->teacher),
                'courseteachers'  => array('langstr' => get_string('wordforteachers'),
                                           'value'   => $this->course->teachers),
                'coursestudent'   => array('langstr' => get_string('wordforstudent'),
                                           'value'   => $this->course->student),
                'coursestudents'  => array('langstr' => get_string('wordforstudents'),
                                           'value'   => $this->course->students),

                'label4'          => array('langstr' => "",
                                           'value'   =>'/optgroup'),
                'label5'          => array('langstr' => get_string('miscellaneous'),
                                           'value'   => 'optgroup'),

                'lang'            => array('langstr' => get_string('preferredlanguage'),
                                           'value'   => current_language()),
                'sitename'        => array('langstr' => get_string('fullsitename'),
                                           'value'   => format_string($site->fullname)),
                'serverurl'       => array('langstr' => get_string('serverurl', 'resource', $littlecfg),
                                           'value'   => $littlecfg->wwwroot),
                'currenttime'     => array('langstr' => get_string('time'),
                                           'value'   => time()),
//                'encryptedcode'   => array('langstr' => get_string('encryptedcode'),
//                                           'value'   => $this->set_encrypted_parameter()),

                'label6'          => array('langstr' => "",
                                           'value'   =>'/optgroup')
        );

        if (!empty($USER->id)) {

            $userparameters = array(

                'label1'          => array('langstr' => get_string('user'),
                                           'value'   => 'optgroup'),

                'userid'          => array('langstr' => 'id',
                                           'value'   => $USER->id),
                'userusername'    => array('langstr' => get_string('username'),
                                           'value'   => $USER->username),
                'useridnumber'    => array('langstr' => get_string('idnumber'),
                                           'value'   => $USER->idnumber),
                'userfirstname'   => array('langstr' => get_string('firstname'),
                                           'value'   => $USER->firstname),
                'userlastname'    => array('langstr' => get_string('lastname'),
                                           'value'   => $USER->lastname),
                'userfullname'    => array('langstr' => get_string('fullname'),
                                           'value'   => fullname($USER)),
                'useremail'       => array('langstr' => get_string('email'),
                                           'value'   => $USER->email),
                'usericq'         => array('langstr' => get_string('icqnumber'),
                                           'value'   => $USER->icq),
                'userphone1'      => array('langstr' => get_string('phone').' 1',
                                           'value'   => $USER->phone1),
                'userphone2'      => array('langstr' => get_string('phone2').' 2',
                                           'value'   => $USER->phone2),
                'userinstitution' => array('langstr' => get_string('institution'),
                                           'value'   => $USER->institution),
                'userdepartment'  => array('langstr' => get_string('department'),
                                           'value'   => $USER->department),
                'useraddress'     => array('langstr' => get_string('address'),
                                           'value'   => $USER->address),
                'usercity'        => array('langstr' => get_string('city'),
                                           'value'   => $USER->city),
                'usertimezone'    => array('langstr' => get_string('timezone'),
                                           'value'   => get_user_timezone_offset()),
                'userurl'         => array('langstr' => get_string('webpage'),
                                           'value'   => $USER->url)
             );

             $this->parameters = $userparameters + $this->parameters;
        }
    }

    function add_instance($resource) {
        $this->_postprocess($resource);
        return parent::add_instance($resource);
    }


    function update_instance($resource) {
        $this->_postprocess($resource);
        return parent::update_instance($resource);
    }


    function _postprocess(&$resource) {
        global $TAORESOURCE_WINDOW_OPTIONS;
        $alloptions = $TAORESOURCE_WINDOW_OPTIONS;

        if (!empty($resource->forcedownload)) {
            $resource->popup = '';
            $resource->options = 'forcedownload';

        } else if ($resource->windowpopup) {
            $optionlist = array();
            foreach ($alloptions as $option) {
                $optionlist[] = $option."=".$resource->$option;
                unset($resource->$option);
            }
            $resource->popup = implode(',', $optionlist);
            unset($resource->windowpopup);
            $resource->options = '';

        } else {
            if (empty($resource->framepage)) {
                $resource->options = '';
            } else {
                $resource->options = 'frame';
            }
            unset($resource->framepage);
            $resource->popup = '';
        }

        $optionlist = array();
        for ($i = 0; $i < $this->maxparameters; $i++) {
            $parametername = "parameter$i";
            $parsename = "parse$i";
            if (!empty($resource->$parsename) and $resource->$parametername != "-") {
                $optionlist[] = $resource->$parametername."=".$resource->$parsename;
            }
            unset($resource->$parsename);
            unset($resource->$parametername);
        }

        $resource->alltext = implode(',', $optionlist);
    }


    /**
    * Display the file resource
    *
    * Displays a file resource embedded, in a frame, or in a popup.
    * Output depends on type of file resource.
    *
    * @param    CFG     global object
    */
    function display() {
        global $CFG, $THEME, $USER;

    /// Set up generic stuff first, including checking for access
        parent::display();

    /// Set up some shorthand variables
        $cm = $this->cm;
        $course = $this->course;
        $resource = $this->taoresource;
        $taoresource_entry = taoresource_entry::get_by_identifier($resource->identifier);

        // if we dont get the resource then bail
        if (!$taoresource_entry) {
            taoresource_not_found($course->id);
        }
        $resource->reference = $taoresource_entry->file ? $taoresource_entry->file : $taoresource_entry->url;
        if (isset($resource->name)) {
            $resource->title = $resource->name;
        }

        $this->set_parameters(); // set the parameters array

        /// First, find out what sort of file we are dealing with.
        require_once($CFG->libdir.'/filelib.php');

        $querystring = '';
        $resourcetype = '';
        $embedded = false;
        $mimetype = mimeinfo("type", $resource->reference);
        $pagetitle = strip_tags($course->shortname.': '.format_string($resource->title));

        $formatoptions = new object();
        $formatoptions->noclean = true;

        if ($this->inpopup || (isset($resource->options) && $resource->options != "forcedownload")) { // TODO nicolasconnault 14-03-07: This option should be renamed "embed"
            if (in_array($mimetype, array('image/gif','image/jpeg','image/png'))) {  // It's an image
                $resourcetype = "image";
                $embedded = true;

            } else if ($mimetype == "audio/mp3") {    // It's an MP3 audio file
                $resourcetype = "mp3";
                $embedded = true;

            } else if ($mimetype == "video/x-flv") {    // It's a Flash video file
                $resourcetype = "flv";
                $embedded = true;

            } else if (substr($mimetype, 0, 10) == "video/x-ms") {   // It's a Media Player file
                $resourcetype = "mediaplayer";
                $embedded = true;

            } else if ($mimetype == "video/quicktime") {   // It's a Quicktime file
                $resourcetype = "quicktime";
                $embedded = true;

            } else if ($mimetype == "application/x-shockwave-flash") {   // It's a Flash file
                $resourcetype = "flash";
                $embedded = true;

            } else if ($mimetype == "video/mpeg") {   // It's a Mpeg file
                $resourcetype = "mpeg";
                $embedded = true;

            } else if ($mimetype == "text/html") {    // It's a web page
                $resourcetype = "html";

            } else if ($mimetype == "application/zip") {    // It's a zip archive
                $resourcetype = "zip";
                $embedded = true;

            } else if ($mimetype == 'application/pdf' || $mimetype == 'application/x-pdf') {
                $resourcetype = "pdf";
                $embedded = true;
            } else if ($mimetype == "audio/x-pn-realaudio") {   // It's a realmedia file
                $resourcetype = "rm";
                $embedded = true;
            }
        }

        $isteamspeak = (stripos($resource->reference, 'teamspeak://') === 0);

    /// Form the parse string
        $querys = array();
        if (!empty($resource->alltext)) {
            $parray = explode(',', $resource->alltext);
            foreach ($parray as $fieldstring) {
                list($moodleparam, $urlname) = explode('=', $fieldstring);
                $value = urlencode($this->parameters[$moodleparam]['value']);
                $querys[urlencode($urlname)] = $value;
                $querysbits[] = urlencode($urlname) . '=' . $value;
            }
            if ($isteamspeak) {
                $querystring = implode('?', $querysbits);
            } else {
                $querystring = implode('&amp;', $querysbits);
            }
        }


        /// Set up some variables

        $inpopup = optional_param('inpopup', 0, PARAM_BOOL);

        if (taoresource_is_url($resource->reference)) {
            $fullurl = $resource->reference;
            if (!empty($querystring)) {
                $urlpieces = parse_url($resource->reference);
                if (empty($urlpieces['query']) or $isteamspeak) {
                    $fullurl .= '?'.$querystring;
                } else {
                    $fullurl .= '&amp;'.$querystring;
                }
            }

        } else {   // Normal uploaded file
            $forcedownloadsep = '?';
            if (isset($resource->options) && $resource->options == 'forcedownload') {
                $querys['forcedownload'] = '1';
            }
            $fullurl = taoresource_get_file_url(TAORESOURCE_RESOURCEPATH.$resource->reference, $querys);
        }

        /// Check whether this is supposed to be a popup, but was called directly
        if (isset($resource->popup) && $resource->popup and !$inpopup) {    /// Make a page and a pop-up window
            $navigation = build_navigation($this->navlinks, $cm);
            print_header($pagetitle, $course->fullname, $navigation,
                    "", "", true, update_module_button($cm->id, $course->id, $this->strtaoresource), navmenu($course, $cm));

            echo "\n<script type=\"text/javascript\">";
            echo "\n<!--\n";
            echo "openpopup('/mod/taoresource/view.php?inpopup=true&id={$cm->id}','resource{$resource->id}','{$resource->popup}');\n";
            echo "\n-->\n";
            echo '</script>';

            if (trim(strip_tags($resource->summary))) {
                print_simple_box(format_text($resource->summary, FORMAT_MOODLE, $formatoptions), "center");
            }

            $link = "<a href=\"$CFG->wwwroot/mod/taoresource/view.php?inpopup=true&amp;id={$cm->id}\" "
                  . "onclick=\"this.target='resource{$resource->id}'; return openpopup('/mod/taoresource/view.php?inpopup=true&amp;id={$cm->id}', "
                  . "'resource{$resource->id}','{$resource->popup}');\">".format_string($resource->title,true)."</a>";

            echo '<div class="popupnotice">';
            print_string('popupresource', 'resource');
            echo '<br />';
            print_string('popupresourcelink', 'resource', $link);
            echo '</div>';
            print_footer($course);
            exit;
        }


        /// Now check whether we need to display a frameset
        $frameset = optional_param('frameset', '', PARAM_ALPHA);
        if (empty($frameset) and !$embedded and !$inpopup and (isset($resource->options) && $resource->options == "frame") and empty($USER->screenreader)) {
            @header('Content-Type: text/html; charset=utf-8');
            echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Frameset//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd\">\n";
            echo "<html dir=\"ltr\">\n";
            echo '<head>';
            echo '<meta http-equiv="content-type" content="text/html; charset=utf-8" />';
            echo "<title>" . format_string($course->shortname) . ": ".strip_tags(format_string($resource->title,true))."</title></head>\n";
            echo "<frameset rows=\"$CFG->taoresource_framesize,*\">";
            echo "<frame src=\"view.php?id={$cm->id}&amp;type={$resource->type}&amp;frameset=top\" title=\""
                 . get_string('modulename','resource')."\"/>";
            echo "<frame src=\"$fullurl\" title=\"".get_string('modulename','taoresource')."\"/>";
            echo "</frameset>";
            echo "</html>";
            exit;
        }

        /// We can only get here once per resource, so add an entry to the log
        add_to_log($course->id, "taoresource", "view", "view.php?identifier={$resource->identifier}", $resource->title);

        /// If we are in a frameset, just print the top of it
        if (!empty( $frameset ) and ($frameset == "top") ) {
            $navigation = build_navigation($this->navlinks, $cm);
            print_header($pagetitle, $course->fullname, $navigation,
                    "", "", true, update_module_button($cm->id, $course->id, $this->strresource), navmenu($course, $cm, "parent"));

            $options = new object();
            $options->para = false;
            echo '<div class="summary">'.format_text($resource->summary, FORMAT_HTML, $options).'</div>';
            print_footer('empty');
            exit;
        }

        /// Display the actual resource
        if ($embedded) {       // Display resource embedded in page
            $strdirectlink = get_string("directlink", "taoresource");

            if ($inpopup) {
                print_header($pagetitle);
            } else {
                $navigation = build_navigation($this->navlinks, $cm);
                print_header_simple($pagetitle, '', $navigation, "", "", true,
                    update_module_button($cm->id, $course->id, $this->strtaoresource), navmenu($course, $cm, "self"));

            }

            if ($resourcetype == "image") {
                echo '<div class="resourcecontent resourceimg">';
                echo "<img title=\"".strip_tags(format_string($resource->title,true))."\" class=\"resourceimage\" src=\"$fullurl\" alt=\"\" />";
                echo '</div>';

            } else if ($resourcetype == "mp3") {
                if (!empty($THEME->resource_mp3player_colors)) {
                    $c = $THEME->resource_mp3player_colors;   // You can set this up in your theme/xxx/config.php
                } else {
                    $c = 'bgColour=000000&btnColour=ffffff&btnBorderColour=cccccc&iconColour=000000&'.
                         'iconOverColour=00cc00&trackColour=cccccc&handleColour=ffffff&loaderColour=ffffff&'.
                         'font=Arial&fontColour=FF33FF&buffer=10&waitForPlay=no&autoPlay=yes';
                }
                $c .= '&volText='.get_string('vol', 'taoresource').'&panText='.get_string('pan','taoresource');
                $c = htmlentities($c);
                $id = 'filter_mp3_'.time(); //we need something unique because it might be stored in text cache
                $cleanurl = addslashes_js($fullurl);


                // If we have Javascript, use UFO to embed the MP3 player, otherwise depend on plugins

                echo '<div class="resourcecontent resourcemp3">';

                echo '<span class="mediaplugin mediaplugin_mp3" id="'.$id.'"></span>'.
                     '<script type="text/javascript">'."\n".
                     '//<![CDATA['."\n".
                       'var FO = { movie:"'.$CFG->wwwroot.'/lib/mp3player/mp3player.swf?src='.$cleanurl.'",'."\n".
                         'width:"600", height:"70", majorversion:"6", build:"40", flashvars:"'.$c.'", quality: "high" };'."\n".
                       'UFO.create(FO, "'.$id.'");'."\n".
                     '//]]>'."\n".
                     '</script>'."\n";

                echo '<noscript>';

                echo "<object type=\"audio/mpeg\" data=\"$fullurl\" width=\"600\" height=\"70\">";
                echo "<param name=\"src\" value=\"$fullurl\" />";
                echo '<param name="quality" value="high" />';
                echo '<param name="autoplay" value="true" />';
                echo '<param name="autostart" value="true" />';
                echo '</object>';
                echo '<p><a href="' . $fullurl . '">' . $fullurl . '</a></p>';

                echo '</noscript>';
                echo '</div>';

            } else if ($resourcetype == "flv") {
                $id = 'filter_flv_'.time(); //we need something unique because it might be stored in text cache
                $cleanurl = addslashes_js($fullurl);


                // If we have Javascript, use UFO to embed the FLV player, otherwise depend on plugins

                echo '<div class="resourcecontent resourceflv">';

                echo '<span class="mediaplugin mediaplugin_flv" id="'.$id.'"></span>'.
                     '<script type="text/javascript">'."\n".
                     '//<![CDATA['."\n".
                       'var FO = { movie:"'.$CFG->wwwroot.'/filter/mediaplugin/flvplayer.swf?file='.$cleanurl.'",'."\n".
                         'width:"600", height:"400", majorversion:"6", build:"40", allowscriptaccess:"never", quality: "high" };'."\n".
                       'UFO.create(FO, "'.$id.'");'."\n".
                     '//]]>'."\n".
                     '</script>'."\n";

                echo '<noscript>';

                echo "<object type=\"video/x-flv\" data=\"$fullurl\" width=\"600\" height=\"400\">";
                echo "<param name=\"src\" value=\"$fullurl\" />";
                echo '<param name="quality" value="high" />';
                echo '<param name="autoplay" value="true" />';
                echo '<param name="autostart" value="true" />';
                echo '</object>';
                echo '<p><a href="' . $fullurl . '">' . $fullurl . '</a></p>';

                echo '</noscript>';
                echo '</div>';

            } else if ($resourcetype == "mediaplayer") {
                echo '<div class="resourcecontent resourcewmv">';
                echo '<object type="video/x-ms-wmv" data="' . $fullurl . '">';
                echo '<param name="controller" value="true" />';
                echo '<param name="autostart" value="true" />';
                echo "<param name=\"src\" value=\"$fullurl\" />";
                echo '<param name="scale" value="noScale" />';
                echo "<a href=\"$fullurl\">$fullurl</a>";
                echo '</object>';
                echo '</div>';

            } else if ($resourcetype == "mpeg") {
                echo '<div class="resourcecontent resourcempeg">';
                echo '<object classid="CLSID:22d6f312-b0f6-11d0-94ab-0080c74c7e95"
                              codebase="http://activex.microsoft.com/activex/controls/mplayer/en/nsm p2inf.cab#Version=5,1,52,701"
                              type="application/x-oleobject">';
                echo "<param name=\"fileName\" value=\"$fullurl\" />";
                echo '<param name="autoStart" value="true" />';
                echo '<param name="animationatStart" value="true" />';
                echo '<param name="transparentatStart" value="true" />';
                echo '<param name="showControls" value="true" />';
                echo '<param name="Volume" value="-450" />';
                echo '<!--[if !IE]>-->';
                echo '<object type="video/mpeg" data="' . $fullurl . '">';
                echo '<param name="controller" value="true" />';
                echo '<param name="autostart" value="true" />';
                echo "<param name=\"src\" value=\"$fullurl\" />";
                echo "<a href=\"$fullurl\">$fullurl</a>";
                echo '<!--<![endif]-->';
                echo '<a href="' . $fullurl . '">' . $fullurl . '</a>';
                echo '<!--[if !IE]>-->';
                echo '</object>';
                echo '<!--<![endif]-->';
                echo '</object>';
                echo '</div>';
            } else if ($resourcetype == "rm") {

                echo '<div class="resourcecontent resourcerm">';
                echo '<object classid="clsid:CFCDAA03-8BE4-11cf-B84B-0020AFBBCCFA" width="320" height="240">';
                echo '<param name="src" value="' . $fullurl . '" />';
                echo '<param name="controls" value="All" />';
                echo '<!--[if !IE]>-->';
                echo '<object type="audio/x-pn-realaudio-plugin" data="' . $fullurl . '" width="320" height="240">';
                echo '<param name="controls" value="All" />';
                echo '<a href="' . $fullurl . '">' . $fullurl .'</a>';
                echo '</object>';
                echo '<!--<![endif]-->';
                echo '</object>';
                echo '</div>';

            } else if ($resourcetype == "quicktime") {
                echo '<div class="resourcecontent resourceqt">';

                echo '<object classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B"';
                echo '        codebase="http://www.apple.com/qtactivex/qtplugin.cab">';
                echo "<param name=\"src\" value=\"$fullurl\" />";
                echo '<param name="autoplay" value="true" />';
                echo '<param name="loop" value="true" />';
                echo '<param name="controller" value="true" />';
                echo '<param name="scale" value="aspect" />';

                echo '<!--[if !IE]>-->';
                echo "<object type=\"video/quicktime\" data=\"$fullurl\">";
                echo '<param name="controller" value="true" />';
                echo '<param name="autoplay" value="true" />';
                echo '<param name="loop" value="true" />';
                echo '<param name="scale" value="aspect" />';
                echo '<!--<![endif]-->';
                echo '<a href="' . $fullurl . '">' . $fullurl . '</a>';
                echo '<!--[if !IE]>-->';
                echo '</object>';
                echo '<!--<![endif]-->';
                echo '</object>';
                echo '</div>';
            }  else if ($resourcetype == "flash") {
                echo '<div class="resourcecontent resourceswf">';
                echo '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000">';
                echo "<param name=\"movie\" value=\"$fullurl\" />";
                echo '<param name="autoplay" value="true" />';
                echo '<param name="loop" value="true" />';
                echo '<param name="controller" value="true" />';
                echo '<param name="scale" value="aspect" />';
                echo '<!--[if !IE]>-->';
                echo "<object type=\"application/x-shockwave-flash\" data=\"$fullurl\">";
                echo '<param name="controller" value="true" />';
                echo '<param name="autoplay" value="true" />';
                echo '<param name="loop" value="true" />';
                echo '<param name="scale" value="aspect" />';
                echo '<!--<![endif]-->';
                echo '<a href="' . $fullurl . '">' . $fullurl . '</a>';
                echo '<!--[if !IE]>-->';
                echo '</object>';
                echo '<!--<![endif]-->';
                echo '</object>';
                echo '</div>';

            } elseif ($resourcetype == 'zip') {
                echo '<div class="resourcepdf">';
                echo get_string('clicktoopen', 'resource') . '<a href="' . $fullurl . '">' . format_string($resource->title) . '</a>';
                echo '</div>';

            } elseif ($resourcetype == 'pdf') {
                echo '<div class="resourcepdf">';
                echo '<object data="' . $fullurl . '" type="application/pdf">';
                echo get_string('clicktoopen', 'resource') . '<a href="' . $fullurl . '">' . format_string($resource->title) . '</a>';
                echo '</object>';
                echo '</div>';
            }

            if (trim($resource->summary)) {
                print_simple_box(format_text(stripslashes_safe($resource->summary), FORMAT_MOODLE, $formatoptions, $course->id), "center");
            }

            if ($inpopup) {
                // suppress the banner that gets cutoff with large images
                echo '<style> body.HAT-narrowbg {background:none};</style>';
                echo "<div class=\"popupnotice\">(<a href=\"$fullurl\">$strdirectlink</a>)</div>";
                echo "</div>"; // MDL-12098
                print_footer($course); // MDL-12098
            } else {
                print_spacer(20,20);
                print_footer($course);
            }

        } else {              // Display the resource on it's own
            redirect($fullurl);
        }

    }


    //backwards compatible with existing resources
    function set_encrypted_parameter() {
        global $CFG;

        if (!empty($this->resource->reference) && file_exists($CFG->dirroot ."/mod/taoresource/type/file/externserverfile.php")) {
            include $CFG->dirroot ."/mod/taoresource/type/file/externserverfile.php";
            if (function_exists(extern_server_file)) {
                return extern_server_file($this->resource->reference);
            }
        }
        return md5($_SERVER['REMOTE_ADDR'].$CFG->taoresource_secretphrase);
    }

    function setup_preprocessing(&$defaults){

        if (isset($defaults['options']) and $defaults['options'] === 'forcedownload') {
            $defaults['forcedownload'] = 1;
        }

        if (isset($defaults['forcedownload']) && $defaults['forcedownload'] == 1) {
            $defaults['windowpopup'] = 0;
        }
        if (!isset($defaults['popup'])) {
            // use form defaults

        } else if (!empty($defaults['popup'])) {
            $defaults['windowpopup'] = 1;
            if (array_key_exists('popup', $defaults)) {
                $rawoptions = explode(',', $defaults['popup']);
                foreach ($rawoptions as $rawoption) {
                    $option = explode('=', trim($rawoption));
                    $defaults[$option[0]] = $option[1];
                }
            }
        } else {
            $defaults['windowpopup'] = 0;
            if (array_key_exists('options', $defaults)) {
                $defaults['framepage'] = ($defaults['options']=='frame');
            }
        }
        /// load up any stored parameters
        if (!empty($defaults['alltext'])) {
            $parray = explode(',', $defaults['alltext']);
            $i=0;
            foreach ($parray as $rawpar) {
                list($param, $varname) = explode('=', $rawpar);
                $defaults["parse$i"] = $varname;
                $defaults["parameter$i"] = $param;
                $i++;
            }
        }
    }

    /**
     * TODO document
     */
    function setup_elements(&$mform) {
        global $CFG, $USER, $TAORESOURCE_WINDOW_OPTIONS;

        $taoresource_entry;

        $add     = optional_param('add', 0, PARAM_ALPHA);
        $update  = optional_param('update', 0, PARAM_INT);
        $return  = optional_param('return', 0, PARAM_BOOL); //return to course/view.php if false or mod/modname/view.php if true
        $type    = optional_param('type', '', PARAM_ALPHANUM);
        $section = optional_param('section', PARAM_INT);
        $course  = optional_param('course', PARAM_INT);
        if (!empty($add)) {
            $entry_id = optional_param('entry_id', false, PARAM_INT);
            // Have we selected a resource yet ?
            if (empty($entry_id)) {
                redirect($CFG->wwwroot.
                  "/mod/taoresource/search.php?course={$course}&section={$section}&type={$type}&add={$add}&return={$return}");
            }
            // we have our reference TAO resource
            else {
                if (!$taoresource_entry = taoresource_entry::read_by_id($entry_id)) {
                    error('Invalid taoresource_entry::id supplied: '.$entry_id);
                }
            }
        }
        else if (!empty($update)) {
            if (! $cm = get_coursemodule_from_id('taoresource', $update)) {
                error('Course Module ID was incorrect');
            }
            if (! $resource = get_record('taoresource', 'id', $cm->instance)) {
                error('Resource ID was incorrect');
            }
            if (!$taoresource_entry = taoresource_entry::read($resource->identifier)) {
                error('Invalid taoresource_entry::identifier supplied: '.$resource->identifier);
            }
        }

        $this->set_parameters(); // set the parameter array for the form

        $mform->addElement('hidden', 'entry_id', $taoresource_entry->id);
        $mform->addElement('hidden', 'identifier', $taoresource_entry->identifier);
        $mform->setDefault('name', stripslashes_safe($taoresource_entry->title));
        $mform->setDefault('description', stripslashes_safe($taoresource_entry->description));

        $location = $mform->addElement('static', 'origtitle', get_string('title', 'taoresource').': ', stripslashes_safe($taoresource_entry->title));
        $strpreview = get_string('preview','taoresource');
        $link =  "<a href=\"$CFG->wwwroot/mod/taoresource/view.php?identifier={$taoresource_entry->identifier}&amp;inpopup=true\" "
          . "onclick=\"this.target='resource{$taoresource_entry->id}'; return openpopup('/mod/taoresource/view.php?inpopup=true&amp;identifier={$taoresource_entry->identifier}', "
          . "'resource{$taoresource_entry->id}','resizable=1,scrollbars=1,directories=1,location=0,menubar=0,toolbar=0,status=1,width=800,height=600');\">(".$strpreview.")</a>";
        $location = $mform->addElement('static', 'url', get_string('location', 'taoresource').': ', $link);

        $searchbutton = $mform->addElement('submit', 'searchtaoresource', get_string('searchtaoresource', 'taoresource'));
        $buttonattributes = array('title'=> get_string('searchtaoresource', 'taoresource'), 'onclick'=>" window.location.href ='"
                          . $CFG->wwwroot."/mod/taoresource/search.php?course={$this->course->id}&section={$section}&type={$type}&add={$add}&return={$return}"."'; return false;");
        $searchbutton->updateAttributes($buttonattributes);

        $mform->addElement('header', 'displaysettings', get_string('display', 'resource'));
        $mform->addElement('checkbox', 'forcedownload', get_string('forcedownload', 'resource'));
        $mform->setHelpButton('forcedownload', array('forcedownload', get_string('forcedownload', 'resource'), 'resource'));
        $mform->disabledIf('forcedownload', 'windowpopup', 'eq', 1);

        $woptions = array(0 => get_string('pagewindow', 'resource'), 1 => get_string('newwindow', 'resource'));
        $mform->addElement('select', 'windowpopup', get_string('display', 'resource'), $woptions);
        $mform->setDefault('windowpopup', (empty($CFG->taoresource_popup) ? 1 : 0));
        $mform->disabledIf('windowpopup', 'forcedownload', 'checked');

        $mform->addElement('checkbox', 'framepage', get_string('keepnavigationvisible', 'resource'));

        $mform->setHelpButton('framepage', array('frameifpossible', get_string('keepnavigationvisible', 'resource'), 'resource'));
        $mform->setDefault('framepage', 0);
        $mform->disabledIf('framepage', 'windowpopup', 'eq', 1);
        $mform->disabledIf('framepage', 'forcedownload', 'checked');
        $mform->setAdvanced('framepage');

        foreach ($TAORESOURCE_WINDOW_OPTIONS as $option) {
            if ($option == 'height' or $option == 'width') {
                $mform->addElement('text', $option, get_string('new'.$option, 'resource'), array('size'=>'4'));
                $mform->setDefault($option, $CFG->{'taoresource_popup'.$option});
                $mform->disabledIf($option, 'windowpopup', 'eq', 0);
            } else {
                $mform->addElement('checkbox', $option, get_string('new'.$option, 'resource'));
                $mform->setDefault($option, $CFG->{'taoresource_popup'.$option});
                $mform->disabledIf($option, 'windowpopup', 'eq', 0);
            }
            $mform->setAdvanced($option);
        }

        $mform->addElement('header', 'parameters', get_string('parameters', 'resource'));

        $options = array();
        $options['-'] = get_string('chooseparameter', 'resource').'...';
        $optgroup = '';
        foreach ($this->parameters as $pname=>$param) {
            if ($param['value']=='/optgroup') {
                $optgroup = '';
                continue;
            }
            if ($param['value']=='optgroup') {
                $optgroup = $param['langstr'];
                continue;
            }
            $options[$pname] = $optgroup.' - '.$param['langstr'];
        }

        for ($i = 0; $i < $this->maxparameters; $i++) {
            $parametername = "parameter$i";
            $parsename = "parse$i";
            $group = array();
            $group[] =& $mform->createElement('text', $parsename, '', array('size'=>'12'));//TODO: accessiblity
            $group[] =& $mform->createElement('select', $parametername, '', $options);//TODO: accessiblity
            $mform->addGroup($group, 'pargroup'.$i, get_string('variablename', 'resource').'='.get_string('parameter', 'resource'), ' ', false);
            $mform->setAdvanced('pargroup'.$i);

            $mform->setDefault($parametername, '-');
        }
    }

}

?>