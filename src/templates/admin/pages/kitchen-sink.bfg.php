<?php
/**
 * Kitchen Sink - Component Library & Design System Reference
 *
 * This page demonstrates all available UI components and CSS classes
 * used throughout the Bring Fraktguiden admin interface.
 *
 * Only visible when BRING_ENVIRONMENT === 'local'
 */
?>

<div class="wrap bfg bfg-admin-page bfg-admin-page__kitchen-sink">
	<div class="bfg-page__header">
		<h1><bfg-t>Kitchen Sink - Component Library</bfg-t></h1>
	</div>

	<div class="bfg-page__main">
		<div class="bfg-notices">
			<div class="wp-header-end"><!-- Notices appear after this div --></div>
		</div>

		<?php require_once dirname(__FILE__, 5) . '/build/templates/admin/pages/kitchen-sink/boxes-containers.php'; ?>
		<?php require_once dirname(__FILE__, 5) . '/build/templates/admin/pages/kitchen-sink/typography.php'; ?>
		<?php require_once dirname(__FILE__, 5) . '/build/templates/admin/pages/kitchen-sink/title-hierarchy.php'; ?>
		<?php require_once dirname(__FILE__, 5) . '/build/templates/admin/pages/kitchen-sink/notice-banners.php'; ?>
		<?php require_once dirname(__FILE__, 5) . '/build/templates/admin/pages/kitchen-sink/buttons.php'; ?>
		<?php require_once dirname(__FILE__, 5) . '/build/templates/admin/pages/kitchen-sink/badges.php'; ?>
		<?php require_once dirname(__FILE__, 5) . '/build/templates/admin/pages/kitchen-sink/form-fields.php'; ?>
		<?php require_once dirname(__FILE__, 5) . '/build/templates/admin/pages/kitchen-sink/step-rows.php'; ?>
		<?php require_once dirname(__FILE__, 5) . '/build/templates/admin/pages/kitchen-sink/access-links.php'; ?>
		<?php require_once dirname(__FILE__, 5) . '/build/templates/admin/pages/kitchen-sink/license-card.php'; ?>
		<?php require_once dirname(__FILE__, 5) . '/build/templates/admin/pages/kitchen-sink/feature-cards.php'; ?>
		<?php require_once dirname(__FILE__, 5) . '/build/templates/admin/pages/kitchen-sink/css-reference.php'; ?>
	</div>
</div>
