(function ($) {

"use strict";

$.fn.autoCompleteFix = function(opt) {
    var ro = 'readonly', settings = $.extend({
        attribute : 'autocomplete',
        trigger : {
            disable : ["off"],
            enable : ["on"]
        },
        focus : function() {
            $(this).removeAttr(ro);
        },
        force : false
    }, opt);

    $(this).each(function(i, el) {
        el = $(el);

        if(el.is('form')) {
            var force = (-1 !== $.inArray(el.attr(settings.attribute), settings.trigger.disable))
            el.find('input').autoCompleteFix({force:force});
        } else {
            var disabled = -1 !== $.inArray(el.attr(settings.attribute), settings.trigger.disable);
            var enabled = -1 !== $.inArray(el.attr(settings.attribute), settings.trigger.enable);
            if (settings.force && !enabled || disabled)
                el.attr(ro, ro).focus(settings.focus).val("");
        }
    });
};
})(jQuery);