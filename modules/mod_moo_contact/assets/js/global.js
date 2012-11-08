(function($) {
    'use strict';
    /**
     * Takes all form elements width data-placeholder attributes
     * and adds it as a placeholder to that element
     */
    var initPlaceholders = function () {
            var els = $('[data-placeholder]'),
                el;

            els.each(function () {
                el = $(this);

                function blurPlaceholder () {
                    el = $(this);

                    var dataPlaceholderStr = el.attr('data-placeholder'),
                        elValStr = el.val();

                    if ($.trim(elValStr) === '' ||
                        $.trim(elValStr) === dataPlaceholderStr
                    ) {
                        el.val(el.attr('data-placeholder'));
                        el.addClass('empty');
                    } else {
                        el.removeClass('empty');
                    }
                }

                function focusPlaceholder () {
                    el = $(this);

                    var dataPlaceholderStr = el.attr('data-placeholder'),
                        elValStr = el.val();

                    if ($.trim(elValStr) === '' ||
                        $.trim(elValStr) === dataPlaceholderStr
                    ) {
                        el.val('');
                        el.removeClass('empty');
                    } else {
                        el.removeClass('empty');
                    }
                }

                el.blur(blurPlaceholder).focus(focusPlaceholder);
                blurPlaceholder.call(el);
            });
        };

    $(document).ready(function () {
        initPlaceholders();
    });
}(jQuery));