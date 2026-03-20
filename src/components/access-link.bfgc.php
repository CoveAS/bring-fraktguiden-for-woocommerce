<?php
/**
 * BFG Access Link Component
 *
 * A row that links to another page with a configure button.
 * Used for feature navigation without status tracking.
 *
 * @usage <bfg-access-link href="/page">Title<bfg-step-desc>Description</bfg-step-desc></bfg-access-link>
 *
 * @param string $href Link URL (required)
 */
?>

<div class="bfg-access-link">
    <div class="bfg-access-link__content">
        <slot></slot>
    </div>
    <a href=":href" class="bfg-access-link__btn">Configure</a>
</div>
