
<tr>
  <td class="fraktguiden-services-table-col-enabled">
    <label for="<?php echo $service->id; ?>"
           style="display:inline-block; width: 100%">
      <input type="checkbox"
             id="<?php echo $service->id; ?>"
             name="<?php echo $field_key; ?>[]"
             value="<?php echo $service->key; ?>" <?php echo( $service->enabled ? 'checked="checked"' : '' ); ?> />
    </label>
  </td>
  <td class="fraktguiden-services-table-col-name">
    <span data-tip="<?php echo $service->service_data['HelpText']; ?>"
          class="woocommerce-help-tip"></span>
    <label class="fraktguiden-service"
           for="<?php echo $service->id; ?>"
           data-ProductName="<?php echo $service->service_data['ProductName']; ?>"
           data-DisplayName="<?php echo $service->service_data['DisplayName']; ?>">
      <?php echo $service->service_data[$this->shipping_method->service_name]; ?>
    </label>
  </td>
  <?php if ( Fraktguiden_Helper::pro_activated() || Fraktguiden_Helper::pro_test_mode() ) : ?>
  <td class="fraktguiden-services-table-col-custom-price">
    <input type="text"
           placeholder="<?= __( '...', 'bring-fraktguiden' );?>"
           name="<?php echo $service->custom_price_id; ?>"
           value="<?php echo $service->custom_price; ?>"
    />
  </td>
  <td class="fraktguiden-services-table-col-free-shipping">
    <label style="display:inline-block; width: 100%">
      <input type="checkbox"
             name="<?php echo $service->free_shipping_id; ?>"
          <?php echo $service->free_shipping ? 'checked="checked"' : ''; ?>>
    </label>
  </td>
  <td class="fraktguiden-services-table-col-free-shipping-threshold">
    <input type="text"
           placeholder="<?= __( '...', 'bring-fraktguiden' );?>"
           name="<?php echo $service->free_shipping_threshold_id; ?>"
           value="<?php echo $service->free_shipping_threshold; ?>"
           placeholder="0"
    />
  </td>
  <?php endif; ?>
</tr>