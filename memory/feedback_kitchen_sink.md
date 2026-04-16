---
name: Always reference kitchen sink for styling
description: When adding or changing styles, always check the kitchen sink page first for the correct values to use
type: feedback
---

Always refer to the kitchen sink page (`src/templates/admin/pages/kitchen-sink.bfg.php`) for styling — font sizes, colors, spacing, component classes — before writing any new CSS or picking values. This ensures consistency across all admin pages.

**Why:** Ad-hoc values (e.g. 14px vs 15px descriptions) cause visual inconsistency across pages. The kitchen sink is the single source of truth for the design system.

**How to apply:** Before choosing a font size, color, border radius, or spacing value, read the relevant section in the kitchen sink first. Prefer existing classes (e.g. `bfg-complete-card__title`, `bfg-complete-card__desc`) over new ones where possible.
