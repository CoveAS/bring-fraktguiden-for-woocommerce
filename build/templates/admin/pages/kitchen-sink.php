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
				<div class="bfg-notice-banner">
					<span class="bfg-notice-icon">
						<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 13.3334V10.0001M10 6.66675H10.0083M18.3333 10.0001C18.3333 14.6025 14.6024 18.3334 10 18.3334C5.39765 18.3334 1.66669 14.6025 1.66669 10.0001C1.66669 5.39771 5.39765 1.66675 10 1.66675C14.6024 1.66675 18.3333 5.39771 18.3333 10.0001Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
					</span>
					<p><?php echo wp_kses_post('This is a warning notice banner'); ?></p>
				</div>
				<br>
				<div class="bfg-notice-banner">
					<span class="bfg-notice-icon">
						<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 13.3334V10.0001M10 6.66675H10.0083M18.3333 10.0001C18.3333 14.6025 14.6024 18.3334 10 18.3334C5.39765 18.3334 1.66669 14.6025 1.66669 10.0001C1.66669 5.39771 5.39765 1.66675 10 1.66675C14.6024 1.66675 18.3333 5.39771 18.3333 10.0001Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
					</span>
					<p><?php echo wp_kses_post('This is an info notice banner'); ?></p>
				</div>
				<br>
				<div class="bfg-notice-banner">
					<span class="bfg-notice-icon">
						<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M16.6666 5L7.49998 14.1667L3.33331 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
					</span>
					<p><?php echo wp_kses_post('This is a success notice banner'); ?></p>
				</div>
				<br>
				<div class="bfg-notice-banner">
					<span class="bfg-notice-icon">
						<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 13.3334V10.0001M10 6.66675H10.0083M18.3333 10.0001C18.3333 14.6025 14.6024 18.3334 10 18.3334C5.39765 18.3334 1.66669 14.6025 1.66669 10.0001C1.66669 5.39771 5.39765 1.66675 10 1.66675C14.6024 1.66675 18.3333 5.39771 18.3333 10.0001Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
					</span>
					<p><?php echo wp_kses_post('This is an error notice banner'); ?></p>
				</div>
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
					<?php $uniqueId = 'bfg-select-demo-select-' . wp_rand(); ?>
					<div class="bfg-input bfg-input--select">
						<div class="bfg-custom-select" id="<?php echo $uniqueId; ?>">
							<!-- Hidden native select for form submission -->
							<select
								name="demo-select"
								id="demo-select"
								class="bfg-custom-select__native"
								tabindex="-1"
								aria-hidden="true"
							>
								<option value="" disabled><?php echo esc_html(__('Select an option', 'bring-fraktguiden-for-woocommerce')); ?></option>
								<option value="option1" selected><?php echo esc_html(__('Option 1', 'bring-fraktguiden-for-woocommerce')); ?></option>
								<option value="option2"><?php echo esc_html(__('Option 2', 'bring-fraktguiden-for-woocommerce')); ?></option>
								<option value="option3"><?php echo esc_html(__('Option 3', 'bring-fraktguiden-for-woocommerce')); ?></option>
							</select>

							<!-- Custom visible select trigger -->
							<button type="button" class="bfg-custom-select__trigger" aria-haspopup="listbox" aria-expanded="false">
								<span class="bfg-custom-select__value"><?php echo esc_html(__('Option 1', 'bring-fraktguiden-for-woocommerce')); ?></span>
								<svg class="bfg-custom-select__arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<path d="M6 9l6 6 6-6"/>
								</svg>
							</button>

							<!-- Custom dropdown list -->
							<div class="bfg-custom-select__dropdown" role="listbox">
								<div
									class="bfg-custom-select__option is-selected"
									data-value="option1"
									role="option"
									aria-selected="true"
								>
									<span class="bfg-custom-select__option-text"><?php echo esc_html(__('Option 1', 'bring-fraktguiden-for-woocommerce')); ?></span>
									<svg class="bfg-custom-select__check" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
										<polyline points="20 6 9 17 4 12"></polyline>
									</svg>
								</div>
								<div
									class="bfg-custom-select__option"
									data-value="option2"
									role="option"
									aria-selected="false"
								>
									<span class="bfg-custom-select__option-text"><?php echo esc_html(__('Option 2', 'bring-fraktguiden-for-woocommerce')); ?></span>
								</div>
								<div
									class="bfg-custom-select__option"
									data-value="option3"
									role="option"
									aria-selected="false"
								>
									<span class="bfg-custom-select__option-text"><?php echo esc_html(__('Option 3', 'bring-fraktguiden-for-woocommerce')); ?></span>
								</div>
							</div>
						</div>
					</div>
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
				<div class="bfg-progress-container">
					<span class="bfg-progress-badge"><?php echo esc_html(__('3 of 5 completed', 'bring-fraktguiden-for-woocommerce')); ?></span>
					<div class="bfg-progress-bar-new">
						<div class="bfg-progress-bar-fill" style="width: 60%;"></div>
					</div>
				</div>
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
				<div class="bfg-pro-status-card">
					<div class="bfg-pro-status-card__item">
						<span class="bfg-pro-status-card__label"><?php echo esc_html(__('Status', 'bring-fraktguiden-for-woocommerce')); ?></span>
						<span class="bfg-pro-status-card__value bfg-pro-status-card__value--success"><?php echo esc_html(__('Active', 'bring-fraktguiden-for-woocommerce')); ?></span>
					</div>
					<div class="bfg-pro-status-card__item">
						<span class="bfg-pro-status-card__label"><?php echo esc_html(__('License Type', 'bring-fraktguiden-for-woocommerce')); ?></span>
						<span class="bfg-pro-status-card__value"><?php echo esc_html(__('PRO License', 'bring-fraktguiden-for-woocommerce')); ?></span>
					</div>
				</div>
				<br>
				<div class="bfg-pro-status-card bfg-pro-status-card--trial">
					<div class="bfg-pro-status-card__item">
						<span class="bfg-pro-status-card__label"><?php echo esc_html(__('Status', 'bring-fraktguiden-for-woocommerce')); ?></span>
						<span class="bfg-pro-status-card__value bfg-pro-status-card__value--trial"><?php echo esc_html(__('Trial', 'bring-fraktguiden-for-woocommerce')); ?></span>
					</div>
					<div class="bfg-pro-status-card__item">
						<span class="bfg-pro-status-card__label"><?php echo esc_html(__('Days Remaining', 'bring-fraktguiden-for-woocommerce')); ?></span>
						<span class="bfg-pro-status-card__value"><?php echo esc_html('7'); ?></span>
					</div>
				</div>
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
				<ul class="bfg-pro-features-grid">
					<li>
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
						<?php echo esc_html(__('MyBring Booking', 'bring-fraktguiden-for-woocommerce')); ?>
					</li>
					<li>
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
						<?php echo esc_html(__('Fixed shipping prices', 'bring-fraktguiden-for-woocommerce')); ?>
					</li>
					<li>
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
						<?php echo esc_html(__('Free shipping threshold', 'bring-fraktguiden-for-woocommerce')); ?>
					</li>
					<li>
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
						<?php echo esc_html(__('Pick-up points', 'bring-fraktguiden-for-woocommerce')); ?>
					</li>
				</ul>

				<h3 class="bfg-field-group-title">
					<?php esc_html_e('Compact Feature List', 'bring-fraktguiden-for-woocommerce'); ?></h3>
				<ul class="bfg-pro-features-grid bfg-pro-features-grid--compact">
					<li>
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
						<?php echo esc_html(__('MyBring Booking', 'bring-fraktguiden-for-woocommerce')); ?>
					</li>
					<li>
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
						<?php echo esc_html(__('Fixed shipping prices', 'bring-fraktguiden-for-woocommerce')); ?>
					</li>
					<li>
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M20 6L9 17L4 12" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
						<?php echo esc_html(__('Free shipping threshold', 'bring-fraktguiden-for-woocommerce')); ?>
					</li>
				</ul>
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