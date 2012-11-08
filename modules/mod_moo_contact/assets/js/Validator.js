/**
 * @package Client-side form validator
 * @author Dan Mooney
 * @version 9/19/12
 * @param form jQuery wrapped form element
 * @param config object with input ids as keys and validationTypes as values
 * @param instructions object with validation types as keys and user friendly translations as values
 */
var com_novellasrecycling = {};
com_novellasrecycling.Validator = function Validator (form, config, instructions) {
    'use strict';
    var obj,
        stopEvent = function(e) {
            if (typeof e.preventDefault === 'function') {
                e.preventDefault();
            }
            e.returnValue = false;
        },
        toString = Object.prototype.toString;
    $ = jQuery;
    obj = {
        /**
         * The form element itself, instantiated on document ready
         */
        form: {},

        /**
         * List of element ids as keys with validationType strings as value
         * @var {Object}
         */
        config: {},

        /**
         * User-friendly messages used to help fill out form if input is invalid
         * The keys should be the validationTypes.  In addition, these keys are also supported:
         *   - onInvalidSubmit: Will be prepended to form, used for general invalidation indication
         */
        instructions: {},

        /**
         * Repository for invalid elements inside a form
         */
        invalidEls: [],

        /**
         * The validation types that get performed on elements listed in config
         * @var {Object}
         */
        validationTypes: {
            isNotEmpty: function (value) {
                if ($.trim(value) === '') {
                    return false;
                }
                return true;
            },
            /**
             * Facade for isNotEmpty for radio buttons
             */
            isFilledOut: function (value, args, el) {
                var els = $('[name="' + el.attr('name') + '"]'),
                    i;
                for (i = 0; i < els.length; i += 1) {
                    if (els.eq(i).is(':checked')) {
                        return true;
                    }
                }
                return false;
            },
            isEmail:  function (value) {
                return /\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b/i.test(value);
            },
            isNumeric: function (value) {
                return /^\s+[0-9]\s+$/.test(value);
            },
            isZipCode: function (value) {
                return /^\d{5}(-\d{4})?$/.test(value);
            },
            isPhoneNumber: function (value) {
                return /^(?:(?:\+?1\s*(?:[.-]\s*)?)?(?:\(\s*([2-9]1[02-9]|[2-9][02-8]1|[2-9][02-8][02-9])\s*\)|([2-9]1[02-9]|[2-9][02-8]1|[2-9][02-8][02-9]))\s*(?:[.-]\s*)?)?([2-9]1[02-9]|[2-9][02-9]1|[2-9][02-9]{2})\s*(?:[.-]\s*)?([0-9]{4})(?:\s*(?:#|x\.?|ext\.?|extension)\s*(\d+))?$/.test(value);
            },
            isFile: function(value, fileTypes) {
                if (!this.isNotEmpty(value)
                    ||  value.indexOf('.') === -1) {
                    return false;
                }
                var fileExt = value.split('.')[1],
                    i = fileTypes.length;

                while (--i >= 0) {
                    if (fileTypes[i] === fileExt) {
                        return true;
                    }
                }
                return false;
            },
            isEqualTo: function(value, anotherValue) {
                anotherValue = anotherValue[0];
                return $.trim(value) === anotherValue;
            }
        },

        /**
         * Checks whether form is valid
         */
        isValid: function() {
            return this.invalidEls.length === 0;
        },

        removeFromInvalidEls: function (id) {
            var i = this.invalidEls.length;
            if (this.invalidEls.length === 0) {
                return;
            }
            while (--i >= 0) {
                if (this.invalidEls[i] === id) {
                    this.invalidEls.splice(i, 1);
                    return;
                }
            }
        },

        addToInvalidEls: function (id) {
            var i = this.invalidEls.length;
            if (this.invalidEls.length > 0) {
                while (--i >= 0) {
                    if (this.invalidEls[i] === id) { // if id already exists in invalidEls, return
                        return;
                    }
                }
            }
            // push onto invalidEls
            this.invalidEls.push(id);
        },

        /**
         * View for indicating to the user that the required element input is invalid
         */
        markAsInvalid: function (el) {
            var id = el.attr('id');
            el.addClass('invalid');

            if (el['isRadio']) {
                el.siblings('label').addClass('invalid');
                $('[name="' + name + '"]').siblings('label').addClass('invalid');
            }

            /**
             * FIXME - not very extensible here... this is for doppelganger file input elements
             */
            if (this.form.find('#' + id + '_text').length > 0) {
                this.form.find('#' + id + '_text').addClass('invalid');
            }
        },

        /**
         * View for indicating to user that the required elemnt input is now valid
         */
        markAsValid: function(el) {
            var id = el.attr('id'),
                name = el.attr('name');
            el.removeClass('invalid');

            if (el['isRadio']) {
                el.siblings('label').removeClass('invalid');
                $('[name="' + name + '"]').siblings('label').removeClass('invalid');
            }

            /**
             * FIXME - not very extensible here... this is for doppelganger file input elements
             */
            if (this.form.find('#' + id + '_text').length > 0) {
                this.form.find('#' + id + '_text').removeClass('invalid');
            }
        },

        validateEl: function (el, validatorType, additionalArgs) {
            var value = el.val();

            if (el.hasClass('empty')) {
                value = '';
            }

            return this.validationTypes[validatorType](value, additionalArgs, el);
        },

        /**
         * Checks for element existence and then validates it with validateEl, and adds/removes it from invalidEls array
         */
        checkEl: function (el) {
            if (!el ||
                el.length === 0
                ) {
                return;
            }

            if (!el.is(':visible')) {
                this.markAsValid(el);
                return;
            }

            var id = el.attr('name'),
                validatorType = this.config[id],
                args = [];

            if (toString.call(validatorType) === '[object Array]') {
                validatorType = this.config[id][0];
                args = this.config[id].slice(1);
            }

            if (typeof this.validationTypes[validatorType] === 'function') {
                if (el.attr('type') === 'radio' ||
                    el.attr('type') === 'checkbox'
                    ) {
                    el = $('[name="' + el.attr('name') + '"]');
                    el['isRadio'] = true;
                    validatorType = 'isFilledOut';
                }

                if (this.validateEl(el, validatorType, args)) { // valid
                    this.removeFromInvalidEls(id);
                    this.markAsValid(el);
                } else { // invalid
                    this.addToInvalidEls(id);
                    this.markAsInvalid(el);
                }
            }
        },

        /**
         * Runs through all fields in config and validates them
         */
        checkAllRequiredEls: function () {
            for (var id in this.config) {
                this.checkEl(this.form.find('[name="' + id + '"]'));
            }
        },

        /**
         * Event handler for form submission
         */
        checkBeforeSubmit: function (e) {

            this.checkAllRequiredEls();
            if (!this.isValid()) {
                stopEvent(e);
                this.invalidateSubmit();
                return false;
            }

            this.form.find('input, textarea').each(function () {
                if ($(this).hasClass('empty')) {
                    $(this).val('');
                }
            });

            this.form.submit();

            return true;
        },

        /**
         * Function for when submission is invalid
         */
        invalidateSubmit: function () {
            var formEl = $(this.instructions.onInvalidSubmitPosition).length > 0
                ?  $(this.instructions.onInvalidSubmitPosition)
                :  this.form;

            var invalidationMessages = formEl.find('.invalidate-message');

            if (invalidationMessages.length > 0) {
                invalidationMessages.fadeOut(200, function () {
                    $(this).fadeIn(200);
                });
                return;
            }

            if (typeof this.instructions.onInvalidSubmit === 'string') {
                if ($.scrollTo) {
                    $.scrollTo(this.form, 100);
                }
                $('<div>').attr({
                    'class': 'invalidate-message general',
                    'style': 'display:none'
                }).text(this.instructions.onInvalidSubmit)
                    .prependTo(formEl)
                    .slideDown('slow', function () {

                    });
            }
        },

        /**
         * Initialization function on document ready
         */
        init: function () {
            var self = this;

            if (!(this.form instanceof jQuery)) {
                this.form = $(this.form);
            }

            if (!this.form ||
                this.form.length === 0
                ) {
                // throw new Error('formId passed as argument could not be found.  Aborting...');
                return;
            }

            this.form.find('input, select, textarea').change(function () {
                self.checkEl($(this));
            });
            this.form.find('[type="submit"]').click(function (e) {
                self.checkBeforeSubmit(e);
            });
        }
    };

    obj.form = form;
    obj.config = config;
    obj.instructions = instructions || {};
    obj.init();
};