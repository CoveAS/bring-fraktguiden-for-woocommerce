<?php

use BringFraktguiden\Admin\FieldRenderer;

/**
 * @var string $title
 * @var string $type
 * @var string $label
 * @var string $description
 * @var string $desc_tip
 * @var string $default
 * @var string $placeholder
 * @var string $css
 * @var array $custom_attributes
 * @var array $options
 */
?>

<p><?php echo wp_kses_post($description); ?></p>
