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
  function oneColumnShipping() {
    var tr = $('.woocommerce-shipping-totals');
    if (tr.children.length !== 2) {
      return;
    }
    var header = tr.find('th');
    var cell = tr.find('td');
    if (!header.length || !cell.length) {
      return;
    }
    // Move the header and give it colspan 2
    header.attr('colspan', 2);
    tr.before($('<tr>').append(header));
    // Give cell colspan 2
    cell.attr('colspan', 2);
  }
  if (_fraktguiden_checkout.one_column_shipping) {
    $(document).on('updated_checkout', oneColumnShipping);
    oneColumnShipping();
  }
  var busy = false;
  var select_time_slot = function select_time_slot() {
    if (busy) {
      return;
    }
    busy = true;
    var elem = $(this);
    $('.alternative-date-item--chosen').removeClass('alternative-date-item--chosen alternative-date-item--selected');
    elem.addClass('alternative-date-item--chosen alternative-date-item--selected');
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
    $('.bring-fraktguiden-logo, .bring-fraktguiden-description, .bring-fraktguiden-environmental, .bring-fraktguiden-eta').on('click', function () {
      $(this).closest('li').find('input').trigger('click');
    });
  };
  $(document).on('updated_checkout', bind_buttons);
  bind_buttons();
});
/******/ })()
;