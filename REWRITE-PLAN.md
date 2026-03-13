# Plan: Rewrite Settings Selector with Vanilla JavaScript

## Context

The current shipping services settings selector uses Vue 3 with Vite, which has proven problematic:
- Template mounting issues (renders empty despite successful mount)
- Mixing Composition API (Refs) with Options API causing bugs
- Complex build system with ES modules and chunks
- Multiple debugging attempts haven't resolved core rendering issues

This rewrite will use **vanilla JavaScript** with zero external dependencies (except existing jQuery/Select2) to create a simpler, more maintainable solution.

## Why Vanilla JavaScript

**Advantages:**
- ✅ **Zero dependencies** - No framework library to load
- ✅ **No build step** - Write once, works immediately
- ✅ **Full control** - No framework magic or unexpected behavior
- ✅ **Lighter weight** - Smaller than any framework solution
- ✅ **Easier debugging** - Direct DOM manipulation, clear data flow
- ✅ **Future-proof** - No framework API changes to worry about

**Trade-offs:**
- More imperative code (~300 lines vs ~200 with Alpine.js)
- Manual DOM manipulation and event handling
- More verbose but more explicit

## Requirements to Preserve

All 10 critical features from current implementation:
1. Multi-select dropdown with service grouping (Select2)
2. Dynamic service card rendering based on selection
3. Per-service configuration fields (name, price, customer number, fees, pickup point)
4. Checkbox + input toggle pairs for optional fields
5. VAS checkboxes per service
6. Customer number validation with error display
7. Pro vs non-pro readonly states
8. Form submission with nested POST naming structure
9. Data persistence across form saves
10. Readonly state synchronization

## Implementation Plan

### Step 1: Create Core JavaScript Module

**File:** `/resources/js/shipping-services.js` (NEW)

**Structure:**
```javascript
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
```

### Step 2: Update Template

**File:** `/templates/service-field.php`

**Changes:**
- Remove all Vue directives (`v-model`, `v-for`, etc.)
- Add container for dynamically rendered service cards
- Simplify select element

```php
<?php
use Bring_Fraktguiden\Common\Fraktguiden_Helper;
?>
<tr valign="top">
    <th scope="row" class="titledesc">
        <label for="<?php echo esc_attr( $field_key ); ?>">
            <?php echo esc_html( $title ); ?>
        </label>
    </th>
    <td class="forminp">
        <div id="shipping_services" class="pro-<?php echo Fraktguiden_Helper::pro_activated() ? 'enabled' : 'disabled'; ?>">

            <!-- Multi-select dropdown -->
            <select
                class="select2"
                multiple="multiple"
                name="<?php echo esc_attr( $field_key ); ?>[]"
            >
                <?php foreach ( Fraktguiden_Helper::get_services_data() as $group_id => $group ) : ?>
                    <optgroup label="<?php echo esc_attr( $group['title'] ); ?>">
                        <?php foreach ( $group['services'] as $service_id => $service ) : ?>
                            <option value="<?php echo esc_attr( $service_id ); ?>">
                                <?php echo esc_html( $service['productName'] ); ?>
                            </option>
                        <?php endforeach; ?>
                    </optgroup>
                <?php endforeach; ?>
            </select>

            <!-- Service cards will be rendered here by JavaScript -->
            <div id="service-cards-container"></div>

        </div>
    </td>
</tr>
<tr>
    <td colspan="2">
        <script>
            jQuery(document).ready(function($) {
                $(document).on('change', '.bring-toggle-checkbox', function() {
                    $(this)
                        .closest('td')
                        .find('> input')
                        .prop('readonly', !this.checked)
                        .prop('required', this.checked);
                });
            });
        </script>
    </td>
</tr>
```

### Step 3: Extract and Create CSS File

**File:** `/resources/css/shipping-services.css` (NEW)

Extract styles from Vue components to standalone CSS:

```css
/* Service card container */
.fraktguiden-product {
    border: 1px solid #ddd;
    border-radius: 4px;
    margin-bottom: 1rem;
    background: #fff;
}

.fraktguiden-product header {
    padding: 1rem;
    background: #f7f7f7;
    border-bottom: 1px solid #ddd;
}

.fraktguiden-product header.warning {
    background: #fff8e5;
    border-color: #f0c36d;
}

.fraktguiden-product h3 {
    margin: 0 0 0.5rem;
    font-size: 1.1em;
}

.fraktguiden-product__name {
    font-weight: 600;
}

.fraktguiden-product__id {
    color: #666;
    font-size: 0.9em;
    margin-left: 0.5rem;
}

.fraktguiden-product p.warning {
    color: #856404;
    margin: 0.5rem 0 0;
}

/* Fields container */
.fraktguiden-product__fields {
    padding: 1rem;
}

.fraktguiden-product__fields label {
    display: block;
    margin-bottom: 1rem;
}

.fraktguiden-product__fields label > span {
    display: block;
    margin-bottom: 0.25rem;
    font-weight: 500;
}

/* Override toggle fields */
.override-toggle-label {
    position: relative;
}

.togglererer {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.bring-toggle-checkbox {
    margin: 0 !important;
}

.bring-toggle-alt {
    font-style: normal;
    width: 2rem;
    height: 1rem;
    background: #ccc;
    border-radius: 1rem;
    position: relative;
    display: inline-block;
    transition: background 0.3s;
}

.bring-toggle-alt::after {
    content: '';
    position: absolute;
    top: 2px;
    left: 2px;
    width: 0.75rem;
    height: 0.75rem;
    background: #fff;
    border-radius: 50%;
    transition: left 0.3s;
}

.bring-toggle-checkbox:checked + .bring-toggle-alt {
    background: #2271b1;
}

.bring-toggle-checkbox:checked + .bring-toggle-alt::after {
    left: calc(100% - 0.75rem - 2px);
}

.togglererer input[type="text"],
.togglererer input[type="number"] {
    flex: 1;
}

/* VAS section */
.fraktguiden-product__vas {
    padding: 0 1rem 1rem;
    border-top: 1px solid #eee;
}

.fraktguiden-product__vas h4 {
    margin: 1rem 0 0.5rem;
    font-size: 1em;
}

.vas-checkboxes {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.vas-checkboxes label.checkbox {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.vas-checkboxes .bring-checkbox {
    margin: 0;
}

/* Validation errors */
.validation-errors {
    list-style: none;
    margin: 0;
    padding: 0.5rem 1rem;
    background: #f8d7da;
    border-top: 1px solid #f5c6cb;
}

.validation-errors__error {
    color: #721c24;
    margin: 0.25rem 0;
}

input.validation-error {
    border-color: #dc3545;
    background-color: #fff5f5;
}

/* Pro feature gating */
.pro-disabled .override-toggle-label::after {
    content: 'Pro only';
    position: absolute;
    top: 0;
    right: 0;
    color: #C00;
    background-color: #fff;
    border-radius: 5px;
    padding: 0.25rem 0.5rem;
    font-size: 0.8em;
    opacity: 0.8;
}

.pro-disabled input[readonly] {
    opacity: 0.6;
    cursor: not-allowed;
}
```

### Step 4: Update Script Enqueuing

**File:** `/classes/ResourceManagement/Scripts.php`

**Changes:**
- Remove Vue runtime and settings script
- Add new vanilla JS script
- Add shipping services CSS
- Remove `add_type_module` filter

```php
public static function admin_enqueue_scripts( string $hook ): void
{
    if ( 'woocommerce_page_wc-settings' !== $hook ) {
        return;
    }
    $baseUrl = plugin_dir_url(dirname(__DIR__));

    // Remove old Vue scripts - DELETE THESE LINES:
    // wp_enqueue_script( 'bring-vue-runtime', ... );
    // wp_enqueue_script( 'bring-settings-js', ... );

    // Add new vanilla JS script
    wp_enqueue_script(
        'bring-shipping-services',
        $baseUrl . '/resources/js/shipping-services.js',
        ['jquery'],
        Bring_Fraktguiden::VERSION,
        true
    );

    wp_enqueue_script( 'bring-admin-js', $baseUrl . '/assets/js/bring-fraktguiden-admin.js', [], Bring_Fraktguiden::VERSION );
    wp_enqueue_script( 'mybring-admin-js', $baseUrl . '/assets/js/mybring-admin.js', ['jquery'], Bring_Fraktguiden::VERSION, true );

    // Localize script data (unchanged)
    wp_localize_script(
        'bring-admin-js',
        'bring_fraktguiden',
        [
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
        ]
    );

    wp_localize_script(
        'bring-shipping-services',  // Changed from 'bring-settings-js'
        'bring_fraktguiden_settings',
        [
            'services_data'    => Fraktguiden_Helper::get_services_data(),
            'services'         => Fraktguiden_Service::all( 'woocommerce_bring_fraktguiden_services' ),
            'services_enabled' => array_keys( Fraktguiden_Service::all( 'woocommerce_bring_fraktguiden_services', true ) ),
            'pro_activated'    => Fraktguiden_Helper::pro_activated(),
            'i18n'             => [
                'shipping_name'               => esc_html__( 'Service name:', 'bring-fraktguiden-for-woocommerce' ),
                'fixed_price_override'        => esc_html__( 'Fixed price override:', 'bring-fraktguiden-for-woocommerce' ),
                'alternative_customer_number' => esc_html__( 'Alternative customer number:', 'bring-fraktguiden-for-woocommerce' ),
                'free_shipping_activated_at'  => esc_html__( 'Free shipping activated at:', 'bring-fraktguiden-for-woocommerce' ),
                'additional_fee'              => esc_html__( 'Additional fee:', 'bring-fraktguiden-for-woocommerce' ),
                'value_added_services'        => esc_html__( 'Value added services', 'bring-fraktguiden-for-woocommerce' ),
                'pickup_point'                => esc_html__( 'Pickup points', 'bring-fraktguiden-for-woocommerce' ),
                'error_customer_number'       => esc_html__( 'Customer numbers should be letters (A-Z) and underscores followed by a dash and a number.', 'bring-fraktguiden-for-woocommerce' ),
            ],
        ]
    );

    // Add shipping services CSS
    wp_enqueue_style(
        'bring-shipping-services',
        $baseUrl . '/resources/css/shipping-services.css',
        [],
        Bring_Fraktguiden::VERSION
    );

    wp_enqueue_style( 'bring-fraktguiden-styles', $baseUrl . '/assets/css/bring-fraktguiden-admin.css', [], Bring_Fraktguiden::VERSION );
}

// Remove this method entirely - no longer needed:
// public static function add_type_module( ... ) { ... }

// Update setup() to remove the filter:
public static function setup(): void
{
    add_action( 'admin_enqueue_scripts', __CLASS__ . '::admin_enqueue_scripts' );
    // DELETE: add_filter( 'script_loader_tag', __CLASS__ . '::add_type_module', 10, 3 );
}
```

### Step 5: Clean Up Old Files

**After verifying the new implementation works**, delete these files:

- `/resources/js/bring-fraktguiden-settings.js`
- `/resources/js/components/shipping-product.vue`
- `/resources/js/components/override-toggle.vue`
- `/resources/js/components/checkbox.vue`
- `/resources/js/mybring-api-validation.js` (functionality moved into main script)

**Keep these Vue files** (used by other features):
- `/resources/js/components/text-validator.vue` (MyBring API validation)
- `/pro/resources/js/booking.js` (Pro booking feature)
- All `/pro/resources/js/components/Booking/` files

**Optional:** Remove Vite settings entry from `/vite.config.js` or keep for future use.

## Testing Strategy

### Phase 1: Initial Implementation
1. Create `/resources/js/shipping-services.js`
2. Create `/resources/css/shipping-services.css`
3. Don't modify other files yet
4. Load in browser console to test for syntax errors

### Phase 2: Integration Testing
1. Update `/templates/service-field.php`
2. Update `/classes/ResourceManagement/Scripts.php`
3. Test in WordPress admin:
   - Navigate to WooCommerce > Settings > Shipping > Bring
   - Open browser console, check for errors
   - Test Select2 dropdown
   - Select services, verify cards appear
   - Deselect services, verify cards disappear

### Phase 3: Feature Testing
1. **Custom name field** - Type value, verify it appears in card
2. **Toggle fields** - Check checkbox, verify input becomes editable
3. **Customer number validation** - Enter invalid format, verify error appears
4. **VAS checkboxes** - Check/uncheck, verify state changes
5. **Pro gating** - Test with pro enabled/disabled

### Phase 4: Form Submission Testing
1. Configure multiple services with various settings
2. Submit form
3. Check network tab for POST data structure
4. Verify data saves to database
5. Reload page, verify all settings persist

### Phase 5: Browser Compatibility
- Chrome/Edge (latest)
- Firefox (latest)
- Safari (latest)
- Mobile browsers (iOS Safari, Android Chrome)

## Rollback Plan

**Immediate Rollback:**
```bash
# Restore old Vue files from git
git checkout resources/js/bring-fraktguiden-settings.js
git checkout resources/js/components/
git checkout templates/service-field.php
git checkout classes/ResourceManagement/Scripts.php

# Remove new vanilla JS files
rm resources/js/shipping-services.js
rm resources/css/shipping-services.css

# Clear browser cache
```

**Backup Strategy:**
Before starting, create backup branch:
```bash
git checkout -b backup-vue-implementation
git checkout main
```

## Migration Timeline

**Day 1: Core Implementation**
- Create shipping-services.js
- Create shipping-services.css
- Test in isolation

**Day 2: Template Integration**
- Update service-field.php
- Update Scripts.php
- Basic functionality testing

**Day 3: Feature Implementation**
- Implement all 10 critical features
- Validation logic
- Pro gating logic

**Day 4: Testing & Refinement**
- Comprehensive testing
- Bug fixes
- Edge cases

**Day 5: Deployment**
- Deploy to staging
- Final testing
- Deploy to production
- Monitor for issues

**Day 6-7: Stabilization**
- Monitor for bug reports
- Quick fixes if needed
- Clean up old Vue files

## Success Criteria

- ✅ Multi-select dropdown works with Select2
- ✅ Service cards render/un-render dynamically
- ✅ All form fields functional
- ✅ Checkbox toggles enable/disable inputs correctly
- ✅ Customer number validation works
- ✅ Validation errors display inline
- ✅ VAS checkboxes work
- ✅ Pro feature gating works (visual + functional)
- ✅ Form submits with correct POST structure
- ✅ Data persists across page loads
- ✅ No JavaScript console errors
- ✅ No CSS layout issues
- ✅ Works in all major browsers

## Critical Files

1. **`/resources/js/shipping-services.js`** (NEW) - 300 lines of vanilla JS
2. **`/resources/css/shipping-services.css`** (NEW) - Extracted component styles
3. **`/templates/service-field.php`** - Simplified template without Vue directives
4. **`/classes/ResourceManagement/Scripts.php`** - Updated script enqueuing
5. **`/classes/common/class-fraktguiden-service.php`** (REFERENCE) - Form processing logic

## Advantages of This Approach

1. **Zero framework dependencies** - No Vue, Alpine, React, etc.
2. **No build step** - Write code, refresh page, see changes
3. **Smaller bundle** - ~10KB vs 70KB+ with Vue
4. **Easier debugging** - Direct DOM manipulation, clear call stack
5. **No mounting issues** - No template compilation or Ref/reactive bugs
6. **Future-proof** - No framework API changes or deprecations
7. **Full control** - No framework magic or unexpected behavior
8. **WordPress-friendly** - Fits WordPress development patterns

## Trade-offs Accepted

1. **More verbose** - ~300 lines vs ~200 with Alpine.js (~100 line increase)
2. **Manual DOM manipulation** - No automatic reactivity
3. **More imperative** - Explicit event handling and rendering
4. **HTML template strings** - Instead of declarative template syntax

These trade-offs are acceptable because:
- The code is still maintainable and readable
- No external dependencies means fewer potential issues
- Direct control makes debugging easier
- Simplicity aligns with "avoid complexity" requirement
