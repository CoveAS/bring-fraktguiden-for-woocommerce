<?php
/**
 * Kitchen Sink - Component Library & Design System Reference
 *
 * This page demonstrates all available UI components and CSS classes
 * used throughout the Bring Fraktguiden admin interface.
 *
 * Only visible when BRING_ENVIRONMENT === 'local'
 */

use BringFraktguiden\Admin\Component;
?>

<div class="wrap bfg-admin-page bfg-admin-page__kitchen-sink">
	<div class="bfg-page__main">
		<div class="bfg-page__header">
			<h1><?php esc_html_e('Kitchen Sink - Component Library', 'bring-fraktguiden-for-woocommerce'); ?></h1>
			<p><?php esc_html_e('Reference guide for all UI components and design patterns', 'bring-fraktguiden-for-woocommerce'); ?>
			</p>
		</div>

		<div class="bfg-notices">
			<div class="wp-header-end"><!-- Notices appear after this div --></div>
		</div>

		<!-- Boxes & Containers -->
		<div class="bfg-box">
			<div class="bfg-box__header">
				<h2><?php esc_html_e('Boxes & Containers', 'bring-fraktguiden-for-woocommerce'); ?></h2>
				<p><?php esc_html_e('Primary container component used throughout the admin', 'bring-fraktguiden-for-woocommerce'); ?>
				</p>
			</div>
			<div class="bfg-box__section">
				<p><strong>Classes:</strong> <code>.bfg-box</code>, <code>.bfg-box__header</code>,
					<code>.bfg-box__section</code></p>
				<p><?php esc_html_e('Use .bfg-box for main content sections. Contains header with title/description and section for content.', 'bring-fraktguiden-for-woocommerce'); ?>
				</p>
			</div>
		</div>

		<!-- Typography -->
		<div class="bfg-box">
			<div class="bfg-box__header">
				<h2><?php esc_html_e('Typography', 'bring-fraktguiden-for-woocommerce'); ?></h2>
			</div>
			<div class="bfg-box__section">
				<h1>Heading 1</h1>
				<h2>Heading 2</h2>
				<h3 class="bfg-field-group-title">Field Group Title (h3.bfg-field-group-title)</h3>
				<h2 class="bfg-section-card-title">Section Card Title</h2>
				<p>Regular paragraph text with <strong>bold text</strong> and <em>italic text</em>.</p>
				<p class="bfg-description">Description text (.bfg-description) - Used for field help text</p>
				<p class="bfg-checkbox-desc">Checkbox description (.bfg-checkbox-desc)</p>
			</div>
		</div>

		<!-- Notice Banners -->
		<div class="bfg-box">
			<div class="bfg-box__header">
				<h2><?php esc_html_e('Notice Banners', 'bring-fraktguiden-for-woocommerce'); ?></h2>
			</div>
			<div class="bfg-box__section">
				<?php echo Component::noticeBanner('This is a warning notice banner', 'warning'); ?>
				<br>
				<?php echo Component::noticeBanner('This is an info notice banner', 'info'); ?>
				<br>
				<?php echo Component::noticeBanner('This is a success notice banner', 'success'); ?>
				<br>
				<?php echo Component::noticeBanner('This is an error notice banner', 'error'); ?>
			</div>
		</div>

		<!-- Buttons & Links -->
		<div class="bfg-box">
			<div class="bfg-box__header">
				<h2><?php esc_html_e('Buttons', 'bring-fraktguiden-for-woocommerce'); ?></h2>
			</div>
			<div class="bfg-box__section">
				<div class="bfgu:flex bfgu:gap-4 bfgu:mb-5">
					<button
						class="bfg-btn bfg-btn--primary"><?php esc_html_e('Primary Button', 'bring-fraktguiden-for-woocommerce'); ?></button>
					<button
						class="bfg-btn bfg-btn--secondary"><?php esc_html_e('Secondary Button', 'bring-fraktguiden-for-woocommerce'); ?></button>
					<button
						class="bfg-btn bfg-btn--primary bfg-btn--lg"><?php esc_html_e('Large Primary', 'bring-fraktguiden-for-woocommerce'); ?></button>
				</div>
				<div class="bfgu:flex bfgu:gap-4">
					<button
						class="bfg-btn bfg-btn--primary bfg-btn--full-width"><?php esc_html_e('Full Width Button', 'bring-fraktguiden-for-woocommerce'); ?></button>
				</div>
				<p class="bfg-description"><strong>Classes:</strong> <code>.bfg-btn</code>,
					<code>.bfg-btn--primary</code>, <code>.bfg-btn--secondary</code>, <code>.bfg-btn--lg</code>,
					<code>.bfg-btn--full-width</code></p>
			</div>
		</div>

		<!-- Badges -->
		<div class="bfg-box">
			<div class="bfg-box__header">
				<h2><?php esc_html_e('Badges', 'bring-fraktguiden-for-woocommerce'); ?></h2>
			</div>
			<div class="bfg-box__section">
				<div class="bfgu:flex bfgu:gap-4 bfgu:mb-5">
					<span
						class="bfg-badge bfg-badge--completed"><?php esc_html_e('Completed', 'bring-fraktguiden-for-woocommerce'); ?></span>
					<span
						class="bfg-badge bfg-badge--in-progress"><?php esc_html_e('In Progress', 'bring-fraktguiden-for-woocommerce'); ?></span>
					<div class="bfg-progress-badge">
						<?php esc_html_e('3 of 5 completed', 'bring-fraktguiden-for-woocommerce'); ?></div>
				</div>
				<p class="bfg-description"><strong>Classes:</strong> <code>.bfg-badge</code>,
					<code>.bfg-badge--completed</code>, <code>.bfg-badge--in-progress</code>,
					<code>.bfg-progress-badge</code></p>
			</div>
		</div>

		<!-- Form Fields -->
		<div class="bfg-box">
			<div class="bfg-box__header">
				<h2><?php esc_html_e('Form Fields', 'bring-fraktguiden-for-woocommerce'); ?></h2>
			</div>
			<div class="bfg-box__section">
				<h3 class="bfg-field-group-title">
					<?php esc_html_e('Text Inputs', 'bring-fraktguiden-for-woocommerce'); ?></h3>

				<div class="bfg-field">
					<label
						for="demo-text"><?php esc_html_e('Text Input', 'bring-fraktguiden-for-woocommerce'); ?></label>
					<input type="text" id="demo-text" name="demo-text" value=""
						placeholder="<?php esc_attr_e('Placeholder text', 'bring-fraktguiden-for-woocommerce'); ?>">
					<p class="bfg-description">
						<?php esc_html_e('Help text goes here', 'bring-fraktguiden-for-woocommerce'); ?></p>
				</div>

				<div class="bfg-field">
					<label
						for="demo-number"><?php esc_html_e('Number Input with Suffix', 'bring-fraktguiden-for-woocommerce'); ?></label>
					<div class="bfg-input bfg-input--number">
						<input type="number" id="demo-number" name="demo-number" value="100">
						<span class="bfg-suffix">NOK</span>
					</div>
				</div>

				<div class="bfg-field">
					<label
						for="demo-number-lg"><?php esc_html_e('Number Input with Large Suffix', 'bring-fraktguiden-for-woocommerce'); ?></label>
					<div class="bfg-input bfg-input--number">
						<input type="number" id="demo-number-lg" name="demo-number-lg" value="250">
						<span class="bfg-suffix-lg">NOK</span>
					</div>
				</div>

				<h3 class="bfg-field-group-title">
					<?php esc_html_e('Select Dropdowns', 'bring-fraktguiden-for-woocommerce'); ?></h3>

				<div class="bfg-field">
					<label
						for="demo-select"><?php esc_html_e('Custom Select', 'bring-fraktguiden-for-woocommerce'); ?></label>
					<?php
					echo Component::customSelect(
						'demo-select',
						[
							'option1' => __('Option 1', 'bring-fraktguiden-for-woocommerce'),
							'option2' => __('Option 2', 'bring-fraktguiden-for-woocommerce'),
							'option3' => __('Option 3', 'bring-fraktguiden-for-woocommerce'),
						],
						'option1',
						__('Select an option', 'bring-fraktguiden-for-woocommerce')
					);
					?>
				</div>

				<h3 class="bfg-field-group-title">
					<?php esc_html_e('Checkboxes', 'bring-fraktguiden-for-woocommerce'); ?></h3>

				<div class="bfg-field bfg-field--checkbox-box">
					<label>
						<input type="checkbox" name="demo-checkbox-1" value="1">
						<div class="bfg-checkbox-content">
							<span
								class="bfg-checkbox-title"><?php esc_html_e('Enable this feature', 'bring-fraktguiden-for-woocommerce'); ?></span>
							<p class="bfg-checkbox-desc">
								<?php esc_html_e('This is a checkbox with a description below the title', 'bring-fraktguiden-for-woocommerce'); ?>
							</p>
						</div>
					</label>
				</div>

				<div class="bfg-field bfg-field--checkbox-box">
					<label>
						<input type="checkbox" name="demo-checkbox-2" value="1" checked>
						<div class="bfg-checkbox-content">
							<span
								class="bfg-checkbox-title"><?php esc_html_e('Checked checkbox', 'bring-fraktguiden-for-woocommerce'); ?></span>
						</div>
					</label>
				</div>

				<p class="bfg-description"><strong>Field Classes:</strong> <code>.bfg-field</code>,
					<code>.bfg-field--checkbox-box</code>, <code>.bfg-input</code>, <code>.bfg-input--number</code>,
					<code>.bfg-input--select</code></p>
			</div>
		</div>

		<!-- Flex Layout Utilities -->
		<div class="bfg-box">
			<div class="bfg-box__header">
				<h2><?php esc_html_e('Flex Layout Utilities', 'bring-fraktguiden-for-woocommerce'); ?></h2>
			</div>
			<div class="bfg-box__section">
				<h3 class="bfg-field-group-title">
					<?php esc_html_e('Flex Row with Gap', 'bring-fraktguiden-for-woocommerce'); ?></h3>
				<div class="bfgu:flex bfgu:flex-row bfgu:gap-4">
					<div class="bfgu:flex-1" style="background: #f0f0f0; padding: 1rem;">Flex item 1</div>
					<div class="bfgu:flex-1" style="background: #e0e0e0; padding: 1rem;">Flex item 2</div>
					<div class="bfgu:flex-1" style="background: #d0d0d0; padding: 1rem;">Flex item 3</div>
				</div>
				<p class="bfg-description"><strong>Classes:</strong> <code>.bfgu:flex</code>,
					<code>.bfgu:flex-row</code>, <code>.bfgu:gap-4</code>, <code>.bfgu:flex-1</code></p>

				<h3 class="bfg-field-group-title">
					<?php esc_html_e('Spacing Utilities', 'bring-fraktguiden-for-woocommerce'); ?></h3>
				<p class="bfg-description"><code>.bfgu:mt-8</code> - Margin top, <code>.bfgu:mb-5</code> - Margin bottom
				</p>
			</div>
		</div>

		<!-- Progress Bar -->
		<div class="bfg-box">
			<div class="bfg-box__header">
				<h2><?php esc_html_e('Progress Bar', 'bring-fraktguiden-for-woocommerce'); ?></h2>
			</div>
			<div class="bfg-box__section">
				<?php echo Component::progressBar(3, 5, __('3 of 5 completed', 'bring-fraktguiden-for-woocommerce')); ?>
				<p class="bfg-description"><strong>Usage:</strong>
					<code>Component::progressBar(3, 5, '3 of 5 completed')</code></p>
			</div>
		</div>

		<!-- Step Row -->
		<div class="bfg-box">
			<div class="bfg-box__header">
				<h2><?php esc_html_e('Step Rows', 'bring-fraktguiden-for-woocommerce'); ?></h2>
			</div>
			<div class="bfg-box__section">
				<div class="bfg-steps-list">
					<a href="#" class="bfg-step-row bfg-step--completed">
						<div class="bfg-step-row__indicator">
							<svg width="32" height="32" viewBox="0 0 32 32" fill="none"
								xmlns="http://www.w3.org/2000/svg">
								<circle cx="16" cy="16" r="16" fill="#dcfce7" />
								<path d="M10 16L14 20L22 12" stroke="#15803d" stroke-width="2" stroke-linecap="round"
									stroke-linejoin="round" />
							</svg>
						</div>
						<div class="bfg-step-row__content">
							<div class="bfg-step-row__label">
								<?php esc_html_e('Completed Step', 'bring-fraktguiden-for-woocommerce'); ?></div>
							<div class="bfg-step-row__description">
								<?php esc_html_e('This step has been completed', 'bring-fraktguiden-for-woocommerce'); ?>
							</div>
						</div>
						<div class="bfg-step-row__status">
							<span
								class="bfg-badge bfg-badge--completed"><?php esc_html_e('Completed', 'bring-fraktguiden-for-woocommerce'); ?></span>
						</div>
					</a>

					<a href="#" class="bfg-step-row bfg-step--in-progress">
						<div class="bfg-step-row__indicator">
							<div class="bfg-step-row__number">2</div>
						</div>
						<div class="bfg-step-row__content">
							<div class="bfg-step-row__label">
								<?php esc_html_e('In Progress Step', 'bring-fraktguiden-for-woocommerce'); ?></div>
							<div class="bfg-step-row__description">
								<?php esc_html_e('Currently working on this step', 'bring-fraktguiden-for-woocommerce'); ?>
							</div>
						</div>
						<div class="bfg-step-row__status">
							<span
								class="bfg-badge bfg-badge--in-progress"><?php esc_html_e('In Progress', 'bring-fraktguiden-for-woocommerce'); ?></span>
						</div>
					</a>

					<a href="#" class="bfg-step-row bfg-step--pending">
						<div class="bfg-step-row__indicator">
							<div class="bfg-step-row__number">3</div>
						</div>
						<div class="bfg-step-row__content">
							<div class="bfg-step-row__label">
								<?php esc_html_e('Pending Step', 'bring-fraktguiden-for-woocommerce'); ?></div>
							<div class="bfg-step-row__description">
								<?php esc_html_e('This step is not yet started', 'bring-fraktguiden-for-woocommerce'); ?>
							</div>
						</div>
					</a>
				</div>
			</div>
		</div>

		<!-- Status Cards -->
		<div class="bfg-box">
			<div class="bfg-box__header">
				<h2><?php esc_html_e('Status Cards', 'bring-fraktguiden-for-woocommerce'); ?></h2>
			</div>
			<div class="bfg-box__section">
				<?php
				echo Component::statusCard([
					['label' => __('Status', 'bring-fraktguiden-for-woocommerce'), 'value' => __('Active', 'bring-fraktguiden-for-woocommerce'), 'type' => 'success'],
					['label' => __('License Type', 'bring-fraktguiden-for-woocommerce'), 'value' => __('PRO License', 'bring-fraktguiden-for-woocommerce'), 'type' => 'default'],
				], 'default');
				?>
				<br>
				<?php
				echo Component::statusCard([
					['label' => __('Status', 'bring-fraktguiden-for-woocommerce'), 'value' => __('Trial', 'bring-fraktguiden-for-woocommerce'), 'type' => 'trial'],
					['label' => __('Days Remaining', 'bring-fraktguiden-for-woocommerce'), 'value' => '7', 'type' => 'default'],
				], 'trial');
				?>
			</div>
		</div>

		<!-- Feature List -->
		<div class="bfg-box">
			<div class="bfg-box__header">
				<h2><?php esc_html_e('Feature Lists', 'bring-fraktguiden-for-woocommerce'); ?></h2>
			</div>
			<div class="bfg-box__section">
				<h3 class="bfg-field-group-title">
					<?php esc_html_e('Regular Feature List', 'bring-fraktguiden-for-woocommerce'); ?></h3>
				<?php
				echo Component::featureList([
					__('MyBring Booking', 'bring-fraktguiden-for-woocommerce'),
					__('Fixed shipping prices', 'bring-fraktguiden-for-woocommerce'),
					__('Free shipping threshold', 'bring-fraktguiden-for-woocommerce'),
					__('Pick-up points', 'bring-fraktguiden-for-woocommerce'),
				], false);
				?>

				<h3 class="bfg-field-group-title">
					<?php esc_html_e('Compact Feature List', 'bring-fraktguiden-for-woocommerce'); ?></h3>
				<?php
				echo Component::featureList([
					__('MyBring Booking', 'bring-fraktguiden-for-woocommerce'),
					__('Fixed shipping prices', 'bring-fraktguiden-for-woocommerce'),
					__('Free shipping threshold', 'bring-fraktguiden-for-woocommerce'),
				], true);
				?>
			</div>
		</div>

		<!-- PRO Teaser Components -->
		<div class="bfg-box">
			<div class="bfg-box__header">
				<h2><?php esc_html_e('PRO Teaser Components', 'bring-fraktguiden-for-woocommerce'); ?></h2>
			</div>
			<div class="bfg-box__section">
				<div class="bfg-box bfg-pro-teaser-v2">
					<div class="bfg-pro-teaser__shield bfg-pro-teaser__shield--success">
						<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor"
							stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
							<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
							<polyline points="22 4 12 14.01 9 11.01"></polyline>
						</svg>
					</div>
					<h2 class="bfg-pro-teaser__title">
						<?php esc_html_e('PRO Teaser Example', 'bring-fraktguiden-for-woocommerce'); ?></h2>
					<p class="bfg-pro-teaser__subtitle">
						<?php esc_html_e('Example subtitle text for PRO teaser boxes', 'bring-fraktguiden-for-woocommerce'); ?>
					</p>
				</div>
				<p class="bfg-description"><strong>Classes:</strong> <code>.bfg-pro-teaser-v2</code>,
					<code>.bfg-pro-teaser__shield</code>, <code>.bfg-pro-teaser__title</code>,
					<code>.bfg-pro-teaser__subtitle</code></p>
			</div>
		</div>

		<!-- Active Step Card -->
		<div class="bfg-box">
			<div class="bfg-box__header">
				<h2><?php esc_html_e('Active Step Card', 'bring-fraktguiden-for-woocommerce'); ?></h2>
			</div>
			<div class="bfg-box__section">
				<div class="bfg-active-step-card">
					<div class="bfg-active-step__icon">1</div>
					<div class="bfg-active-step__content">
						<h3><?php esc_html_e('Next Step Title', 'bring-fraktguiden-for-woocommerce'); ?></h3>
						<p><?php esc_html_e('Description of the next step to complete', 'bring-fraktguiden-for-woocommerce'); ?>
						</p>
						<a class="bfg-btn bfg-btn--primary" href="#">
							<?php esc_html_e('Take Action', 'bring-fraktguiden-for-woocommerce'); ?>
						</a>
					</div>
				</div>
			</div>
		</div>

		<!-- Color & Style Reference -->
		<div class="bfg-box">
			<div class="bfg-box__header">
				<h2><?php esc_html_e('Component CSS Reference', 'bring-fraktguiden-for-woocommerce'); ?></h2>
			</div>
			<div class="bfg-box__section">
				<h3 class="bfg-field-group-title">
					<?php esc_html_e('Main Classes', 'bring-fraktguiden-for-woocommerce'); ?></h3>
				<ul>
					<li><code>.bfg-admin-page</code> - Main page wrapper</li>
					<li><code>.bfg-page__main</code> - Main content area</li>
					<li><code>.bfg-page__header</code> - Page header section</li>
					<li><code>.bfg-page__header-row</code> - Header row with title and badge</li>
					<li><code>.bfg-box</code> - Card/box container</li>
					<li><code>.bfg-box__header</code> - Box header with title</li>
					<li><code>.bfg-box__section</code> - Box content section</li>
				</ul>

				<h3 class="bfg-field-group-title">
					<?php esc_html_e('Form Classes', 'bring-fraktguiden-for-woocommerce'); ?></h3>
				<ul>
					<li><code>.bfg-field</code> - Form field wrapper</li>
					<li><code>.bfg-field--checkbox-box</code> - Checkbox field variant</li>
					<li><code>.bfg-input</code> - Input wrapper</li>
					<li><code>.bfg-input--number</code> - Number input with suffix</li>
					<li><code>.bfg-input--select</code> - Select input wrapper</li>
					<li><code>.bfg-custom-select</code> - Custom select component</li>
					<li><code>.bfg-suffix</code> - Input suffix (small)</li>
					<li><code>.bfg-suffix-lg</code> - Input suffix (large)</li>
				</ul>

				<h3 class="bfg-field-group-title">
					<?php esc_html_e('Utility Classes', 'bring-fraktguiden-for-woocommerce'); ?></h3>
				<ul>
					<li><code>.bfgu:flex</code> - Flex container</li>
					<li><code>.bfgu:flex-row</code> - Flex direction row</li>
					<li><code>.bfgu:flex-1</code> - Flex item (flex: 1)</li>
					<li><code>.bfgu:gap-4</code> - Gap between flex items</li>
					<li><code>.bfgu:mt-8</code> - Margin top</li>
					<li><code>.bfgu:mb-5</code> - Margin bottom</li>
				</ul>
			</div>
		</div>
	</div>
</div>