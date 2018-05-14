<?php if ( 0 ): ?>
<pre>
  <?php print_r( $waybill_data ); ?>
<!-- {
  "data":{
    "attributes":{
      "id":16856,
      "customerName":"DRIVDIGITAL AS",
      "customerOrganizationNumber":"916020473",
      "customerNumber":"PARCELS_NORWAY-10030342439",
      "senderName":"Test",
      "streetAddress":"Test",
      "postalCode":"4844",
      "postalPlace":"ARENDAL",
      "email":"landa@drivdigital.no",
      "reference":"113",
      "packages":[{"priority":"",
      "rfid":true,
      "recipientName":"1267890",
      "streetAddress":"Blødekjær 20",
      "postalCode":"4838",
      "postalPlace":"ARENDAL",
      "phoneNumber":"+47 90103720",
      "email":"test@example.com",
      "weight":1080,
    }
  }
} -->
</pre>
<?php endif; ?>
<style type="text/css">
  .mailbox-waybill-errors {
    width: 100%;
  }
  .mailbox-waybill-errors__title {
    margin-bottom: 0.5rem;
    color: #C00;
  }
  .mailbox-waybill-errors__message {
    padding: 0.25rem;
    color: #900;
    background-color: #FFEEEE;
    margin-top: 0;
  }
</style>

<?php if ( ! empty( $errors ) ): ?>
  <div class="mailbox-waybill-errors">
    <?php foreach ( $errors as $customer_number => $error_messages ): ?>
      <h3 class="mailbox-waybill-errors__title"><?php echo $customer_number; ?></h3>
      <ul class="mailbox-waybill-errors__messages">
        <?php foreach ( $error_messages as $error ): ?>
          <li class="mailbox-waybill-errors__message"><?php echo $error; ?></li>
        <?php endforeach; ?>
      </ul>
    <?php endforeach; ?>
  </div>
<?php endif; ?>