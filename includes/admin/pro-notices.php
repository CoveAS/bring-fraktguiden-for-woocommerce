<?php


?>
<div class="notice notice-<?php echo $type; ?> <?php if ( $dismissable ) { echo 'is-dismissible'; } ?> bring-notice bring-notice-<?php echo $key; ?>" data-notice_id="<?php echo $key; ?>">
  <p><?php echo $message; ?></p>
</div>