<form method="post">
  <table>
    <thead>
      <tr>
        <th>Select all</th>
        <th>Customer number</th>
        <th>Consignment number</th>
        <th>Order id</th>
        <th>Date/time</th>
        <th>Download link</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ( $consignments as $consignment ): ?>
      <tr>
        <td>
          <input type="checkbox" name="consignment_numbers[<?php echo $consignment->get_customer_number(); ?>][<?php echo $consignment->get_consignment_number(); ?>]">
        </td>
        <td><?php echo $consignment->get_customer_number(); ?></td>
        <td><?php echo $consignment->get_consignment_number(); ?></td>
        <td><?php echo $consignment->order_id; ?></td>
        <td><?php echo $consignment->get_date_time(); ?></td>
        <td><?php echo $consignment->get_label_file()->get_download_link(); ?></td>
      </tr>
      <?php endforeach; ?>
      <tfoot>
        <tr>
          <td colspan="4"></td>
          <td colspan="2">
            <input type="submit" name="book" value="Book selected labels">
            <input type="submit" name="book_all" value="Book all labels">
          </td>
        </tr>
      </tfoot>
    </tbody>
  </table>
</form>