<?php
/**
 * BFG Box Section Component
 *
 * Content section for a box component. Contains the main body content.
 *
 * Source (.bfg.php):
 *   <bfg-section.section>
 *     <p>Your content here</p>
 *   </bfg-section.section>
 *
 * Compiled output (.php):
 *   <div class="bfg-section__section">
 *     <p>Your content here</p>
 *   </div>
 *
 * Compiler replaces:
 *   <slot /> -> inner content (already processed)
 */
?>

<div class="bfg-section__section">
    <slot />
</div>