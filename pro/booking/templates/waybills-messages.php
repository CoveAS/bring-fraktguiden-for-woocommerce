
<?php if ( 422 == $response->status_code ): ?>
  <ul class="error-messages">
  <?php foreach ( $data['errors'] as $error ): ?>
    <?php var_dump( $error ); ?>

    <li class="error-message">
      <span class="error-code">
        <?php echo $error['code']; ?>
      </span>
      <span class="error-title">
        <?php echo $error['title']; ?>
      </span>

    </li>
  <?php endforeach; ?>
  </ul>
<?php elseif ( 201 == $response->status_code ): ?>
<pre>
  <?php print_r( $data ); ?>
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


<?php var_dump( $data ); ?>