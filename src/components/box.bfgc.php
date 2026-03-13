<?php
/**
 * BFG Box Component
 *
 * Transforms <bfg-box> tags into proper HTML structure.
 *
 * Source (.bfg.php):
 *   <bfg-box title="Boxes & Containers" description="Primary container component">
 *     <p>Content here</p>
 *   </bfg-box>
 *
 * Compiled output (.php):
 *   <div class="bfg-box">
 *     <div class="bfg-box__header">
 *       <h2><?php esc_html_e('Boxes & Containers', 'bring-fraktguiden-for-woocommerce'); ?></h2>
 *       <p><?php esc_html_e('Primary container component', 'bring-fraktguiden-for-woocommerce'); ?></p>
 *     </div>
 *     <div class="bfg-box__section">
 *       <p>Content here</p>
 *     </div>
 *   </div>
 *
 * Compiler replaces:
 *   <t>title</t>       -> <?php esc_html_e('Title', 'bring-fraktguiden-for-woocommerce'); ?>
 *   <t>description</t> -> <?php esc_html_e('Description', 'bring-fraktguiden-for-woocommerce'); ?>
 *   <slot/>            -> inner content (already processed)
 *
 * Unmatched attributes (class, data-*, etc.) are automatically passed through to root element.
 */
?>

<div class="bfg-box">
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
	<div class="bfg-box__section">
		<slot />
	</div>
</div>