/**
 * Theme: Dastone - Responsive Bootstrap 5 Admin Dashboard
 * Author: Mannatthemes
 * Form Repeater
 */

$(document).ready(function () {
    'use strict';

    $('.repeater-default').repeater();

    $('.repeater-custom-show-hide').repeater({
        show: function () {
            $(this).slideDown();
        },
        hide: function (remove) {
            if (confirm('Are you sure you want to remove this item?')) {
                $(this).slideUp(remove);
            }
        }
    });
});


// #############################################################################
// كود يعمل مع   select2 الكود التالى 


/**
 * Theme: Dastone - Responsive Bootstrap 5 Admin Dashboard
 * Author: Mannatthemes
 * Form Repeater
 */

// $(document).ready(function () {
//     'use strict';

//     $('.repeater-default').repeater({
//         show: function () {
//             $(this).slideDown();
//             $(this).find('.select2-container').remove();
//             $(this).find('.select2').select2({
//                 dropdownAutoWidth: true,
//                 width: '100%'
//             });
//         },
//         hide: function (remove) {
//             if (confirm('Are you sure you want to remove this item?')) {
//                 $(this).slideUp(remove);
//             }
//         }
//     });

//     $('.repeater-custom-show-hide').repeater({
//         show: function () {
//             $(this).slideDown();
//             $(this).find('.select2-container').remove();
//             $(this).find('.select2').select2({
//                 dropdownAutoWidth: true,
//                 width: '100%'
//             });
//         },
//         hide: function (remove) {
//             if (confirm('Are you sure you want to remove this item?')) {
//                 $(this).slideUp(remove);
//             }
//         }
//     });

//     // Initialize select2 for existing elements
//     $('.select2').select2({
//         dropdownAutoWidth: true,
//         width: '100%'
//     });
// });
