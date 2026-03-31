<?php
/**
 * BFG Box Component
 *
 * A container wrapper component. Use with <bfg-section.header> and <bfg-section.section> for structured content.
 *
 * Source (.bfg.php):
 *   <bfg-section class="custom-class">
 *     <bfg-section.header title="Boxes & Containers" description="Primary container component"></bfg-section.header>
 *     <bfg-section.section>
 *       <p>Content here</p>
 *     </bfg-section.section>
 *   </bfg-section>
 *
 * Compiled output (.php):
 *   <div class="bfg-section custom-class">
 *     <div class="bfg-section__header">
 *       <h2><?php esc_html_e('Boxes & Containers', 'bring-fraktguiden-for-woocommerce'); ?></h2>
 *       <p><?php esc_html_e('Primary container component', 'bring-fraktguiden-for-woocommerce'); ?></p>
 *     </div>
 *     <div class="bfg-section__section">
 *       <p>Content here</p>
 *     </div>
 *   </div>
 *
 * Unmatched attributes (class, data-*, etc.) are automatically passed through to root element.
 */
?>

<div class="bfg-section">
    <slot />
</div>