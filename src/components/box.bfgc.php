<?php
/**
 * BFG Box Component
 *
 * A container wrapper component. Use with <bfg-box.header> and <bfg-box.section> for structured content.
 *
 * Source (.bfg.php):
 *   <bfg-box class="custom-class">
 *     <bfg-box.header title="Boxes & Containers" description="Primary container component"></bfg-box.header>
 *     <bfg-box.section>
 *       <p>Content here</p>
 *     </bfg-box.section>
 *   </bfg-box>
 *
 * Compiled output (.php):
 *   <div class="bfg-box custom-class">
 *     <div class="bfg-box__header">
 *       <h2><?php esc_html_e('Boxes & Containers', 'bring-fraktguiden-for-woocommerce'); ?></h2>
 *       <p><?php esc_html_e('Primary container component', 'bring-fraktguiden-for-woocommerce'); ?></p>
 *     </div>
 *     <div class="bfg-box__section">
 *       <p>Content here</p>
 *     </div>
 *   </div>
 *
 * Unmatched attributes (class, data-*, etc.) are automatically passed through to root element.
 */
?>

<div class="bfg-box">
    <slot />
</div>