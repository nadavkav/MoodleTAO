<script type="text/javascript">
//<![CDATA[

YAHOO.namespace("certification");

YAHOO.certification = new function() {

    return {
        init: function() {
            // Hide all course overview elements
            var detail = YAHOO.util.Dom.getElementsByClassName('taousercontent', null, 'middle-column');

            for (var i in detail) {
                detail[i].style.overflow = 'hidden';
                detail[i].style.height = 0;
            }

            // Find all course items
            var courses = YAHOO.util.Dom.getElementsByClassName('taousercomplete', null, 'middle-column');

            for(var i in courses) {
                if (detail[i].children.length) {
                    var more = document.createElement('a');
                    more.id = 'mymoodle_course_'+i;
                    more.href='#';
                    more.innerHTML =  '<?php print_string('moredetail', 'local');?>';

                    var clear = document.createElement('div');
                    clear.style.clear = 'both';

                    YAHOO.util.Event.addListener(more.id, 'click', YAHOO.certification.expand);
                    courses[i].appendChild(more);
                    courses[i].appendChild(clear);

                    YAHOO.util.Dom.addClass(more.id, 'mymoodle_more');
                }

            }
            
        },

        expand: function(e) {
            YAHOO.util.Event.preventDefault(e);

            var link = YAHOO.util.Event.getTarget(e);

            YAHOO.util.Event.removeListener(link, 'click');
            YAHOO.util.Event.addListener(link, 'click', YAHOO.certification.contract);
            link.innerHTML = '<?php print_string('lessdetail', 'local');?>';

            var container = YAHOO.util.Dom.getElementsByClassName('taousercontent', 'div', link.parentNode)[0];

            var attributes = {
                height: { to: container.scrollHeight}
            };
            var anim = new YAHOO.util.Anim(container, attributes, 1, YAHOO.util.Easing.easeOutStrong);
            anim.animate();

            return(true);
        },

        contract: function(e) {
            YAHOO.util.Event.preventDefault(e);

            var link = YAHOO.util.Event.getTarget(e);

            YAHOO.util.Event.removeListener(link, 'click');
            YAHOO.util.Event.addListener(link, 'click', YAHOO.certification.expand);
            link.innerHTML = '<?php print_string('moredetail', 'local');?>';

            var container = YAHOO.util.Dom.getElementsByClassName('taousercontent', 'div', link.parentNode)[0];

            var attributes = {
                height: { to: 0}
            };
            var anim = new YAHOO.util.Anim(container, attributes, 1, YAHOO.util.Easing.easeOutStrong);
            anim.animate();

            return(true);

        }
    }
}
YAHOO.util.Event.onDOMReady(YAHOO.certification.init, YAHOO.certification.init, true);
// ]]>
</script>