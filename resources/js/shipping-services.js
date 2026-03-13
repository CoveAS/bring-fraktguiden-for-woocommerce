(function($) {
    'use strict';

    // Main controller object
    const ShippingServicesController = {
        // State
        selectedServices: [],
        servicesData: {},
        allServices: {},
        proActivated: false,
        i18n: {},
        serviceStates: {},

        // Initialize
        init: function() {
            // Load data from window.bring_fraktguiden_settings
            this.loadData();

            // Initialize Select2
            this.initSelect2();

            // Bind events
            this.bindEvents();

            // Render initial state
            this.render();
        },

        loadData: function() {
            const settings = window.bring_fraktguiden_settings;
            if (!settings) {
                console.error('bring_fraktguiden_settings not found');
                return;
            }

            this.selectedServices = [...settings.services_enabled];
            this.servicesData = settings.services_data;
            this.allServices = settings.services;
            this.proActivated = settings.pro_activated === '1' || settings.pro_activated === true;
            this.i18n = settings.i18n;
        },

        initSelect2: function() {
            const self = this;
            $('#shipping_services .select2')
                .select2()
                .on('change select2:clear', function(e) {
                    const values = $(this).val() || [];
                    self.selectedServices = [...values];
                    self.render();
                });
        },

        bindEvents: function() {
            const container = document.getElementById('shipping_services');

            // Delegate events for dynamic content
            container.addEventListener('change', (e) => {
                if (e.target.matches('.bring-toggle-checkbox')) {
                    this.handleToggleChange(e.target);
                } else if (e.target.matches('.vas-checkbox')) {
                    this.handleVasChange(e.target);
                } else if (e.target.matches('.customer-number-input')) {
                    this.validateCustomerNumber(e.target);
                }
            });

            container.addEventListener('input', (e) => {
                if (e.target.matches('.customer-number-input')) {
                    this.validateCustomerNumber(e.target);
                }
            });
        },

        render: function() {
            const cardsContainer = document.getElementById('service-cards-container');
            if (!cardsContainer) return;

            // Clear existing cards
            cardsContainer.innerHTML = '';

            // Render each selected service
            this.selectedServices.forEach(serviceId => {
                const service = this.allServices[serviceId];
                if (service) {
                    const card = this.createServiceCard(service);
                    cardsContainer.appendChild(card);
                }
            });
        },

        createServiceCard: function(service) {
            const div = document.createElement('div');
            div.className = 'fraktguiden-product';
            if (service.service_data.class) {
                div.classList.add(service.service_data.class);
            }

            div.innerHTML = this.getServiceCardHTML(service);

            // Initialize state for this service
            this.initServiceState(service.bring_product);

            return div;
        },

        getServiceCardHTML: function(service) {
            return `
                <header class="${service.service_data.class || ''}">
                    <h3>
                        <span class="fraktguiden-product__name">${service.service_data.productName}</span>
                        <span class="fraktguiden-product__id">${service.bring_product}</span>
                    </h3>
                    ${service.service_data.warning ? `<p class="warning">${service.service_data.warning}</p>` : ''}
                    ${service.service_data.description ? `<p>${service.service_data.description}</p>` : ''}
                </header>

                <div class="fraktguiden-product__fields">
                    ${this.getCustomNameFieldHTML(service)}
                    ${this.getOverrideFieldHTML(service, 'custom_price', this.i18n.fixed_price_override, 'number')}
                    ${this.getOverrideFieldHTML(service, 'customer_number', this.i18n.alternative_customer_number, 'text', true)}
                    ${this.getOverrideFieldHTML(service, 'free_shipping', this.i18n.free_shipping_activated_at, 'number')}
                    ${this.getOverrideFieldHTML(service, 'additional_fee', this.i18n.additional_fee, 'number')}
                    ${service.service_data.pickuppoint ? this.getOverrideFieldHTML(service, 'pickup_point', this.i18n.pickup_point, 'number', false, '1') : ''}
                </div>

                ${service.vas && service.vas.length > 0 ? this.getVasHTML(service) : ''}

                <footer>
                    <ul class="validation-errors" data-service-id="${service.bring_product}" style="display: none;"></ul>
                </footer>
            `;
        },

        getCustomNameFieldHTML: function(service) {
            return `
                <label>
                    <span>${this.i18n.shipping_name}</span>
                    <input
                        type="text"
                        name="${service.option_key}[${service.bring_product}][custom_name]"
                        value="${this.escapeHtml(service.settings.custom_name || '')}"
                        placeholder="${service.service_data.productName}"
                        ${!this.proActivated ? 'readonly' : ''}
                    >
                </label>
            `;
        },

        getOverrideFieldHTML: function(service, fieldId, label, inputType, withValidation = false, step = '0.01') {
            const fieldValue = service.settings[fieldId] || '';
            const checkboxValue = service.settings[fieldId + '_cb'] === 'on';
            const namePrefix = `${service.option_key}[${service.bring_product}]`;
            const validationClass = withValidation ? 'customer-number-input' : '';

            return `
                <label class="override-toggle-label">
                    <span>${label}</span>
                    <div class="togglererer">
                        <input
                            type="checkbox"
                            class="bring-toggle-checkbox"
                            name="${namePrefix}[${fieldId}_cb]"
                            ${checkboxValue ? 'checked' : ''}
                            ${!this.proActivated ? 'readonly' : ''}
                            data-target-field="${fieldId}"
                            data-service-id="${service.bring_product}"
                        >
                        <em class="bring-toggle-alt"></em>
                        <input
                            type="${inputType}"
                            name="${namePrefix}[${fieldId}]"
                            value="${this.escapeHtml(fieldValue)}"
                            ${inputType === 'number' ? `step="${step}" min="0"` : ''}
                            ${!checkboxValue || !this.proActivated ? 'readonly' : ''}
                            data-field-id="${fieldId}"
                            data-service-id="${service.bring_product}"
                            class="${validationClass}"
                        >
                    </div>
                </label>
            `;
        },

        getVasHTML: function(service) {
            const vasList = service.vas.map(vas => {
                const namePrefix = `${service.option_key}[${service.bring_product}]`;
                return `
                    <label class="checkbox">
                        <input
                            type="checkbox"
                            class="vas-checkbox bring-checkbox"
                            name="${namePrefix}[vas_${vas.code}]"
                            ${vas.value ? 'checked' : ''}
                        >
                        <span>${vas.name}</span>
                    </label>
                `;
            }).join('');

            return `
                <div class="fraktguiden-product__vas">
                    <h4>${this.i18n.value_added_services}</h4>
                    <div class="vas-checkboxes">
                        ${vasList}
                    </div>
                </div>
            `;
        },

        initServiceState: function(serviceId) {
            if (!this.serviceStates[serviceId]) {
                this.serviceStates[serviceId] = {
                    validation_errors: []
                };
            }
        },

        handleToggleChange: function(checkbox) {
            const serviceId = checkbox.dataset.serviceId;
            const fieldId = checkbox.dataset.targetField;
            const isChecked = checkbox.checked;

            // Find the corresponding input field
            const input = checkbox.parentElement.querySelector(`input[data-field-id="${fieldId}"]`);
            if (input) {
                input.readOnly = !isChecked || !this.proActivated;
                input.required = isChecked;

                // Trigger validation if customer_number field
                if (fieldId === 'customer_number') {
                    this.validateCustomerNumber(input);
                }
            }
        },

        handleVasChange: function(checkbox) {
            // VAS checkboxes just need to submit their value
            // No additional handling needed
        },

        validateCustomerNumber: function(input) {
            const serviceId = input.dataset.serviceId;
            const value = input.value.trim();
            const checkbox = input.parentElement.querySelector('.bring-toggle-checkbox');
            const isEnabled = checkbox && checkbox.checked;

            // Clear existing errors for this field
            this.clearValidationError(serviceId, 'customer_number');

            // Only validate if checkbox is enabled and there's a value
            if (!isEnabled || !value) {
                return true;
            }

            // Validation pattern: letters/underscore + dash + numbers OR 6+ digits
            const pattern1 = /^[A-Za-z_]+-\d+$/;
            const pattern2 = /^\d{6,}$/;
            const isValid = pattern1.test(value) || pattern2.test(value);

            if (!isValid) {
                this.addValidationError(serviceId, 'customer_number', this.i18n.error_customer_number);
                input.classList.add('validation-error');
            } else {
                input.classList.remove('validation-error');
            }

            return isValid;
        },

        addValidationError: function(serviceId, errorId, message) {
            const state = this.serviceStates[serviceId];
            if (!state) return;

            // Check if error already exists
            const exists = state.validation_errors.some(err => err.id === errorId);
            if (!exists) {
                state.validation_errors.push({ id: errorId, message: message });
            }

            this.renderValidationErrors(serviceId);
        },

        clearValidationError: function(serviceId, errorId) {
            const state = this.serviceStates[serviceId];
            if (!state) return;

            state.validation_errors = state.validation_errors.filter(err => err.id !== errorId);
            this.renderValidationErrors(serviceId);
        },

        renderValidationErrors: function(serviceId) {
            const state = this.serviceStates[serviceId];
            const errorList = document.querySelector(`ul.validation-errors[data-service-id="${serviceId}"]`);
            if (!errorList) return;

            if (state.validation_errors.length === 0) {
                errorList.style.display = 'none';
                errorList.innerHTML = '';
            } else {
                errorList.style.display = 'block';
                errorList.innerHTML = state.validation_errors.map(err =>
                    `<li class="validation-errors__error">${err.message}</li>`
                ).join('');
            }
        },

        escapeHtml: function(unsafe) {
            return (unsafe || '')
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        if (window.bring_fraktguiden_settings) {
            ShippingServicesController.init();
        }
    });

})(jQuery);
