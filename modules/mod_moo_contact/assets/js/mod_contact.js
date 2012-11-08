(function($) {
   $(document).ready(function () {
       new com_pilla.Validator($('#contact-form'), {
           first_name: 'isNotEmpty',
           last_name: 'isNotEmpty',
           email: 'isEmail'
       }, {
           onInvalidSubmit: 'Please correct the highlighted fields below.'
       });
   });
}(jQuery));
