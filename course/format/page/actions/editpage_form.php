<?php // $Id$
/**
 * Page editing form
 *
 * @author Mark Nielsen
 * @version $Id$
 * @package format_page
 **/

require_once($CFG->libdir.'/formslib.php');

class format_page_editpage_form extends moodleform {

    function definition() {
        $mform =& $this->_form;

        $mform->addElement('hidden', 'action', 'editpage');
        $mform->setType('action', PARAM_ALPHA);

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'page', 0);
        $mform->setType('page', PARAM_INT);

        $mform->addElement('hidden', 'returnaction');
        $mform->setType('returnaction', PARAM_ALPHA);

        $mform->addElement('header', 'editpagesettings', get_string('editpagesettings', 'format_page'));

        $mform->addElement('text', 'nameone', get_string('pagenameone', 'format_page'), array('size'=>'20'));
        $mform->setType('nameone', PARAM_TEXT);
        $mform->addRule('nameone', null, 'required', null, 'client');

        $mform->addElement('text', 'nametwo', get_string('pagenametwo', 'format_page'), array('size'=>'20'));
        $mform->setType('nametwo', PARAM_TEXT);

        $mform->addElement('selectyesno', 'publish', get_string('publish', 'format_page'));
        $mform->setDefault('publish', 0);

        $options            = array();
        $options[0]         = get_string('no');
        $options[DISP_MENU] = get_string('yes');

        $mform->addElement('select', 'dispmenu', get_string('displaymenu', 'format_page'), $options);
        $mform->setDefault('dispmenu', 0);

        $options             = array();
        $options[0]          = get_string('no');
        $options[DISP_THEME] = get_string('yes');

        $mform->addElement('select', 'disptheme', get_string('displaytheme', 'format_page'), $options);
        $mform->setDefault('disptheme', 0);

        $mform->addElement('text', 'prefleftwidth', get_string('preferredleftcolumnwidth', 'format_page'), array('size'=>'5'));
        $mform->setType('prefleftwidth', PARAM_INT);
        $mform->setDefault('prefleftwidth', 200);
        $mform->addRule('prefleftwidth', null, 'numeric', null, 'client');

        $mform->addElement('text', 'prefcenterwidth', get_string('preferredcentercolumnwidth', 'format_page'), array('size'=>'5'));
        $mform->setType('prefcenterwidth', PARAM_INT);
        $mform->setDefault('prefcenterwidth', 400);
        $mform->addRule('prefcenterwidth', null, 'numeric', null, 'client');

        $mform->addElement('text', 'prefrightwidth', get_string('preferredrightcolumnwidth', 'format_page'), array('size'=>'5'));
        $mform->setType('prefrightwidth', PARAM_INT);
        $mform->setDefault('prefrightwidth', 200);
        $mform->addRule('prefrightwidth', null, 'numeric', null, 'client');

        $options              = array();
        $options[0]           = get_string('noprevnextbuttons', 'format_page');
        $options[BUTTON_PREV] = get_string('prevonlybutton', 'format_page');
        $options[BUTTON_NEXT] = get_string('nextonlybutton', 'format_page');
        $options[BUTTON_BOTH] = get_string('bothbuttons', 'format_page');

        $mform->addElement('select', 'showbuttons', get_string('showbuttons', 'format_page'), $options);
        $mform->setDefault('showbuttons', 0);

        $mform->addElement('selectyesno', 'template', get_string('useasdefault', 'format_page'));
        $mform->setDefault('template', 0);

        if (!empty($this->_customdata)) {
            $mform->addElement('select', 'parent', get_string('parent', 'format_page'), $this->_customdata);
            $mform->setDefault('parent', 0);
        } else {
            $mform->addElement('static', 'noparents', get_string('parent', 'format_page'), get_string('noparents', 'format_page'));
            $mform->addElement('hidden', 'parent', 0);
            $mform->setType('parent', PARAM_INT);
        }

        $this->add_action_buttons();
    }
}
?>