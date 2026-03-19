<?php
/**
 * BFG Feature List
 *
 * A styled list of features with checkmark icons.
 *
 * @usage <bfg-feature-list><li>Feature 1</li><li>Feature 2</li></bfg-feature-list>
 * @usage <bfg-feature-list compact><li>Feature 1</li></bfg-feature-list>
 *
 * @param bool $compact Use compact variant (optional)
 */
?>

<if :compact>
<ul class="bfg-feature-list bfg-feature-list--compact"><slot></slot></ul>
<else>
<ul class="bfg-feature-list"><slot></slot></ul>
</else>
</if>
