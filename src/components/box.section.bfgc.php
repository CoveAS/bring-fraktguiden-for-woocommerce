<?php
/**
 * BFG Box Section Component
 *
 * Content section for a box component. Contains the main body content.
 *
 * Source (.bfg.php):
 *   <bfg-box.section>
 *     <p>Your content here</p>
 *   </bfg-box.section>
 *
 * Compiled output (.php):
 *   <div class="bfg-box__section">
 *     <p>Your content here</p>
 *   </div>
 *
 * Compiler replaces:
 *   <slot /> -> inner content (already processed)
 */
?>

<div class="bfg-box__section">
    <slot />
</div>