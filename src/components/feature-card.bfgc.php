<?php
/**
 * BFG Feature Card
 *
 * Card for presenting Pro features across different page states.
 * Gray icon by default, blue when active. Renders as <a> when href is provided.
 *
 * Sub-components:
 * - <bfg-feature-card.icon>     — icon wrapper (inherits gray/blue from parent state)
 * - <bfg-feature-card.benefits> — benefits list with divider and checkmarks
 *
 * @usage (default/teaser state)
 *   <bfg-feature-card>
 *     <bfg-feature-card.icon><svg>...</svg></bfg-feature-card.icon>
 *     <strong class="bfg-feature-card__title">Title</strong>
 *     <span class="bfg-feature-card__desc">Description</span>
 *     <bfg-feature-card.benefits>
 *       <li>Benefit 1</li>
 *     </bfg-feature-card.benefits>
 *   </bfg-feature-card>
 *
 * @usage (active/linked state)
 *   <bfg-feature-card href="..." active>
 *     <bfg-feature-card.icon><svg>...</svg></bfg-feature-card.icon>
 *     <strong class="bfg-feature-card__title">Title</strong>
 *     <span class="bfg-feature-card__desc">Description</span>
 *   </bfg-feature-card>
 *
 * @param string $href    Optional URL — renders card as a link with hover effect
 * @param bool   $active  Blue icon state (Pro license active)
 * @param bool   $expired Orange icon state (Pro license expired)
 *
 * Note: <else> must be a sibling of <if> (placed after </if>, not inside it).
 */
?>

<if :href>
<if :active>
<a href=":href" class="bfg-feature-card bfg-feature-card--link bfg-feature-card--active"><slot></slot></a>
</if>
<else>
<a href=":href" class="bfg-feature-card bfg-feature-card--link"><slot></slot></a>
</else>
</if>
<else>
<if :active>
<div class="bfg-feature-card bfg-feature-card--active"><slot></slot></div>
</if>
<else>
<if :expired>
<div class="bfg-feature-card bfg-feature-card--expired"><slot></slot></div>
</if>
<else>
<div class="bfg-feature-card"><slot></slot></div>
</else>
</else>
</else>
