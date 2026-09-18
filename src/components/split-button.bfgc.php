<?php
/**
 * BFG Split Button
 *
 * A link button with a caret at the right edge. The caret opens a menu of
 * other links. The menu is a native <details> element, so it needs no script.
 *
 * Put one link per menu item in the slot.
 *
 * @usage
 *   <bfg-split-button :href="$both_url" label="Print the labels">
 *     <a :href="$labels_url"><t>Shipping label only</t></a>
 *     <a :href="$return_labels_url"><t>Return label only</t></a>
 *   </bfg-split-button>
 *
 * The main link opens a new tab, because the menu prints files.
 *
 * @param string $href  Address of the main action (required)
 * @param string $label Text of the main action (required)
 */
?>

<div class="bfg-split-button">
    <a href=":href" class="bfg-btn bfg-btn--sm bfg-split-button__main" target="_blank" rel="noreferrer"><t>label</t></a>
    <details class="bfg-split-button__menu">
        <summary class="bfg-btn bfg-btn--sm bfg-split-button__toggle" aria-label="<?php esc_attr_e('More options', 'bring-fraktguiden-for-woocommerce'); ?>">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
        </summary>
        <div class="bfg-split-button__list">
            <slot></slot>
        </div>
    </details>
</div>
