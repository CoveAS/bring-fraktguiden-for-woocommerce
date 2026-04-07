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
    <a href=":href" class="bfg-btn bfg-btn--outline bfg-btn--sm bfg-btn--icon-only">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="5" y1="12" x2="19" y2="12"></line>
            <polyline points="12 5 19 12 12 19"></polyline>
        </svg>
    </a>
</div>
