/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!****************************************************!*\
  !*** ./resources/js/bring-fraktguiden-checkout.js ***!
  \****************************************************/
jQuery(function ($) {
  var unblock_options = function unblock_options() {
    $('.bring-fraktguiden-date-options').unblock();
  };
  var block_options = function block_options() {
    $('.bring-fraktguiden-date-options').block({
      message: null,
      overlayCSS: {
        background: '#fff',
        opacity: 0.6
      }
    });
  };
  $(document).on('update_checkout', block_options);
  var busy = false;
  var select_time_slot = function select_time_slot() {
    if (busy) {
      return;
    }
    busy = true;
    var elem = $(this);
    elem.addClass('alternative-date-item--chosen alternative-date-item--selected').siblings().removeClass('alternative-date-item--chosen alternative-date-item--selected');
    block_options();
    $.post(_fraktguiden_checkout.ajaxurl, {
      action: 'bring_select_time_slot',
      time_slot: elem.data('time_slot')
    }, function () {
      busy = false;
      unblock_options();
    });
  };
  var bind_buttons = function bind_buttons() {
    $('.bring-fraktguiden-date-options .alternative-date-item--choice').on('click', select_time_slot);
  };
  $(document).on('updated_checkout', bind_buttons);
  bind_buttons();
});
/******/ })()
;