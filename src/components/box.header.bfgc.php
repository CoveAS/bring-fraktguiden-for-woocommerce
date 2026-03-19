<?php
/**
 * BFG Box Header Component
 *
 * Header section for a box component with title and optional description.
 *
 * Source (.bfg.php):
 *   <bfg-box.header title="Page Title" description="Optional description"></bfg-box.header>
 *
 * Compiled output (.php):
 *   <div class="bfg-box__header">
 *     <h2><?php esc_html_e('Page Title', 'bring-fraktguiden-for-woocommerce'); ?></h2>
 *     <p><?php esc_html_e('Optional description', 'bring-fraktguiden-for-woocommerce'); ?></p>
 *   </div>
 *
 * Note: Self-closing tags (/) are not supported by the compiler. Always use closing tags.
 *
 * Compiler replaces:
 *   <t>title</t>       -> <?php esc_html_e('Title', 'bring-fraktguiden-for-woocommerce'); ?>
 *   <t>description</t> -> <?php esc_html_e('Description', 'bring-fraktguiden-for-woocommerce'); ?>
 *   <if :description>  -> conditionally renders if description attribute is present
 */
?>

<div class="bfg-box__header">
    <h2>
        <t>title</t>
    </h2>
    <if :description>
        <p>
            <t>description</t>
        </p>
    </if>
</div>