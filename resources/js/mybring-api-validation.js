import TextValidator from './components/text-validator.vue';
import {createApp} from 'vue';

const validate_email = function (email) {
	const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	return re.test(String(email).toLowerCase());
}
const wrap = el => {
	const wrapper = jQuery(el).wrap('<div>').parent();
	return wrapper.get(0);
}
const i18n = bring_fraktguiden_settings.i18n;

const api_uid = createApp(
	TextValidator,
	{
		original_el: window.woocommerce_bring_fraktguiden_mybring_api_uid,
		validator: function (value) {
			const error_messages = [];
			if (value.match(/\s/)) {
				error_messages.push(i18n.error_spaces + ' ' + i18n.api_email);
				return error_messages;
			}
			if (!validate_email(value)) {
				error_messages.push(i18n.error_api_uid);
			}
			return error_messages;
		}
	}
);
api_uid.mount(wrap('#woocommerce_bring_fraktguiden_mybring_api_uid'));

const api_key = createApp(
	TextValidator,
	{
		original_el: window.woocommerce_bring_fraktguiden_mybring_api_key,
		validator: function (value) {
			const error_messages = [];
			if (value.match(/\s/)) {
				error_messages.push(i18n.error_spaces + ' ' + i18n.api_key);
				return error_messages;
			}
			if (!value.match(/^[A-Za-z\-\d]*$/)) {
				error_messages.push(i18n.error_api_key);
			}
			return error_messages;
		}
	}
);
api_key.mount(wrap('#woocommerce_bring_fraktguiden_mybring_api_key'));

const api_customer_number = createApp(
	TextValidator,
	{
		original_el: window.woocommerce_bring_fraktguiden_mybring_customer_number,
		validator: function (value) {
			const error_messages = [];
			if (value.match(/\s/)) {
				error_messages.push(i18n.error_spaces + ' ' + i18n.customer_number);
				return error_messages;
			}
			if (!value.match(/^[A-Za-z_]+\-\d+$/) && !value.match(/^\d{6,}$/)) {
				error_messages.push(i18n.error_customer_number);
			}
			return error_messages;
		}
	}
);
api_customer_number.mount(wrap('#woocommerce_bring_fraktguiden_mybring_customer_number'));
