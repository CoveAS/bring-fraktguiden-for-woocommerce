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
			<h1><bfg-t>Kitchen Sink - Component Library</bfg-t></h1>
			<p><bfg-t>Reference guide for all UI components and design patterns</bfg-t>
			</p>
		</div>

		<div class="bfg-notices">
			<div class="wp-header-end"><!-- Notices appear after this div --></div>
		</div>

		<!-- Boxes & Containers -->
		<bfg-box>
			<bfg-box.header title="Boxes & Containers"
				description="Primary container component used throughout the admin"></bfg-box.header>
			<bfg-box.section>
				<p><strong>Classes:</strong> <code>.bfg-box</code>, <code>.bfg-box__header</code>,
					<code>.bfg-box__section</code>
				</p>
				<p><bfg-t>Use .bfg-box for main content sections. Contains header with title/description and section for
						content.</bfg-t>
				</p>
			</bfg-box.section>
		</bfg-box>

		<!-- Typography -->
		<bfg-box>
			<bfg-box.header title="Typography"></bfg-box.header>
			<bfg-box.section>
				<h1>Heading 1</h1>
				<h2>Heading 2</h2>
				<h3 class="bfg-field-group-title">Field Group Title (h3.bfg-field-group-title)</h3>
				<h2 class="bfg-section-card-title">Section Card Title</h2>
				<p>Regular paragraph text with <strong>bold text</strong> and <em>italic text</em>.</p>
				<p class="bfg-description">Description text (.bfg-description) - Used for field help text</p>
				<p class="bfg-checkbox-desc">Checkbox description (.bfg-checkbox-desc)</p>
			</bfg-box.section>
		</bfg-box>

		<!-- Notice Banners -->
		<bfg-box>
			<bfg-box.header title="Notice Banners"></bfg-box.header>
			<bfg-box.section>
				<bfg-notice type="warning">This is a warning notice banner</bfg-notice>
				<br>
				<bfg-notice type="info">This is an info notice banner</bfg-notice>
				<br>
				<bfg-notice type="success">This is a success notice banner</bfg-notice>
				<br>
				<bfg-notice type="error">This is an error notice banner</bfg-notice>
			</bfg-box.section>
		</bfg-box>

		<!-- Buttons & Links -->
		<bfg-box>
			<bfg-box.header title="Buttons"></bfg-box.header>
			<bfg-box.section>
				<div class="bfgu:flex bfgu:flex-wrap bfgu:gap-4 bfgu:mb-5">
					<button class="bfg-btn bfg-btn--primary"><bfg-t>Primary</bfg-t></button>
					<button class="bfg-btn bfg-btn--secondary"><bfg-t>Secondary</bfg-t></button>
					<button class="bfg-btn bfg-btn--outline"><bfg-t>Outline</bfg-t></button>
					<button class="bfg-btn bfg-btn--ghost"><bfg-t>Ghost</bfg-t></button>
				</div>
				<div class="bfgu:flex bfgu:items-end bfgu:gap-4 bfgu:mb-5">
					<button class="bfg-btn bfg-btn--primary bfg-btn--sm"><bfg-t>Small Button</bfg-t></button>
					<button class="bfg-btn bfg-btn--primary"><bfg-t>Medium Button</bfg-t></button>
					<button class="bfg-btn bfg-btn--primary bfg-btn--lg"><bfg-t>Large Button</bfg-t></button>
				</div>
				<div class="bfgu:flex bfgu:gap-4">
					<button class="bfg-btn bfg-btn--primary bfg-btn--full-width">
						<bfg-t>Full Width Button</bfg-t>
					</button>
				</div>
				<p class="bfg-description bfgu:mt-4"><strong>Classes:</strong> <code>.bfg-btn</code>,
					<code>.bfg-btn--primary</code>, <code>.bfg-btn--secondary</code>, <code>.bfg-btn--outline</code>, <code>.bfg-btn--ghost</code>, <code>.bfg-btn--sm</code>, <code>.bfg-btn--lg</code>,
					<code>.bfg-btn--full-width</code>
				</p>
			</bfg-box.section>
		</bfg-box>

		<!-- Badges -->
		<bfg-box>
			<bfg-box.header title="Badges"></bfg-box.header>
			<bfg-box.section>
				<div class="bfgu:flex bfgu:gap-4 bfgu:mb-5">
					<bfg-badge.completed>Done</bfg-badge.completed>
					<bfg-badge.in-progress>In progress</bfg-badge.in-progress>
					<bfg-badge.progress>3 of 5 completed</bfg-badge.progress>
				</div>
				<p class="bfg-description"><strong>Classes:</strong> <code>.bfg-badge</code>,
					<code>.bfg-badge--completed</code>, <code>.bfg-badge--in-progress</code>,
					<code>.bfg-progress-badge</code>
				</p>
			</bfg-box.section>
		</bfg-box>

		<!-- Form Fields -->
		<bfg-box>
			<bfg-box.header title="Form Fields"></bfg-box.header>
			<bfg-box.section>
				<h3 class="bfg-field-group-title">
					<bfg-t>Text Inputs</bfg-t>
				</h3>

				<bfg-field.text id="demo-text" name="demo-text" label="Text Input" placeholder="Placeholder text"
					description="Help text goes here"></bfg-field.text>

				<bfg-field.number id="demo-number" name="demo-number" label="Number Input with Suffix" value="100"
					suffix="NOK"></bfg-field.number>

				<bfg-field.number id="demo-number-lg" name="demo-number-lg" label="Number Input with Large Suffix"
					value="250" suffix-lg="NOK"></bfg-field.number>

				<h3 class="bfg-field-group-title">
					<bfg-t>Select Dropdowns</bfg-t>
				</h3>

				<bfg-field.select id="demo-select" name="demo-select" label="Custom Select" :options="[
					'option1' => __('Option 1', 'bring-fraktguiden-for-woocommerce'),
					'option2' => __('Option 2', 'bring-fraktguiden-for-woocommerce'),
					'option3' => __('Option 3', 'bring-fraktguiden-for-woocommerce'),
				]" value="option1" placeholder="Select an option"></bfg-field.select>

				<h3 class="bfg-field-group-title">
					<bfg-t>Checkboxes</bfg-t>
				</h3>

				<bfg-field.checkbox name="demo-checkbox-1" value="1" title="Enable this feature"
					description="This is a checkbox with a description below the title"></bfg-field.checkbox>

				<bfg-field.checkbox name="demo-checkbox-2" value="1" title="Checked checkbox" checked></bfg-field.checkbox>

				<p class="bfg-description"><strong>Field Classes:</strong> <code>.bfg-field</code>,
					<code>.bfg-field--checkbox-box</code>, <code>.bfg-input</code>, <code>.bfg-input--number</code>,
					<code>.bfg-input--select</code>
				</p>
			</bfg-box.section>
		</bfg-box>

		<!-- Flex Layout Utilities -->
		<bfg-box>
			<bfg-box.header title="Flex Layout Utilities"></bfg-box.header>
			<bfg-box.section>
				<h3 class="bfg-field-group-title">
					<bfg-t>Flex Row with Gap</bfg-t>
				</h3>
				<div class="bfgu:flex bfgu:flex-row bfgu:gap-4">
					<div class="bfgu:flex-1" style="background: #f0f0f0; padding: 1rem;">Flex item 1</div>
					<div class="bfgu:flex-1" style="background: #e0e0e0; padding: 1rem;">Flex item 2</div>
					<div class="bfgu:flex-1" style="background: #d0d0d0; padding: 1rem;">Flex item 3</div>
				</div>
				<p class="bfg-description"><strong>Classes:</strong> <code>.bfgu:flex</code>,
					<code>.bfgu:flex-row</code>, <code>.bfgu:gap-4</code>, <code>.bfgu:flex-1</code>
				</p>

				<h3 class="bfg-field-group-title">
					<bfg-t>Spacing Utilities</bfg-t>
				</h3>
				<p class="bfg-description"><code>.bfgu:mt-8</code> - Margin top, <code>.bfgu:mb-5</code> - Margin bottom
				</p>
			</bfg-box.section>
		</bfg-box>

		<!-- Progress Bar -->
		<bfg-box>
			<bfg-box.header title="Progress Bar"></bfg-box.header>
			<bfg-box.section>
				<bfg-progress current="3" total="5" label="3 of 5 completed"></bfg-progress>
				<p class="bfg-description"><strong>Usage:</strong>
					<code>&lt;bfg-progress current="3" total="5" label="..." /&gt;</code>
				</p>
			</bfg-box.section>
		</bfg-box>

		<!-- Step Row -->
		<bfg-box>
			<bfg-box.header title="Step Rows"></bfg-box.header>
			<bfg-box.section>
				<div class="bfg-steps-list">
					<bfg-step.completed href="#">
						<bfg-t>Completed Step</bfg-t>
						<bfg-step-desc><bfg-t>This step has been completed</bfg-t></bfg-step-desc>
						<bfg-badge.completed>Completed</bfg-badge.completed>
					</bfg-step.completed>

					<bfg-step.in-progress href="#" number="2">
						<bfg-t>In Progress Step</bfg-t>
						<bfg-step-desc><bfg-t>Currently working on this step</bfg-t></bfg-step-desc>
						<bfg-badge.in-progress>In Progress</bfg-badge.in-progress>
					</bfg-step.in-progress>

					<bfg-step.pending href="#" number="3">
						<bfg-t>Pending Step</bfg-t>
						<bfg-step-desc><bfg-t>This step is not yet started</bfg-t></bfg-step-desc>
					</bfg-step.pending>
				</div>
			</bfg-box.section>
		</bfg-box>

		<!-- Access Links -->
		<bfg-box>
			<bfg-box.header title="Access Links"
				description="Clickable rows for navigation without status tracking"></bfg-box.header>
			<bfg-box.section>
				<div class="bfg-steps-list">
					<bfg-access-link href="#">
						<bfg-t>MyBring Booking</bfg-t>
						<bfg-step-desc><bfg-t>Create shipping labels directly in WooCommerce</bfg-t></bfg-step-desc>
					</bfg-access-link>

					<bfg-access-link href="#">
						<bfg-t>Pick-up Points</bfg-t>
						<bfg-step-desc><bfg-t>Let customers choose their preferred pickup location</bfg-t></bfg-step-desc>
					</bfg-access-link>

					<bfg-access-link href="#">
						<bfg-t>Free Shipping Threshold</bfg-t>
						<bfg-step-desc><bfg-t>Offer free shipping when cart exceeds a value</bfg-t></bfg-step-desc>
					</bfg-access-link>

					<bfg-access-link href="#">
						<bfg-t>Fixed Prices</bfg-t>
						<bfg-step-desc><bfg-t>Set your own shipping prices instead of Bring rates</bfg-t></bfg-step-desc>
					</bfg-access-link>
				</div>
				<p class="bfg-description bfgu:mt-4"><strong>Usage:</strong>
					<code>&lt;bfg-access-link href="#"&gt;Title&lt;bfg-step-desc&gt;Description&lt;/bfg-step-desc&gt;&lt;/bfg-access-link&gt;</code>
				</p>
			</bfg-box.section>
		</bfg-box>

		<!-- Status Cards -->
		<bfg-box>
			<bfg-box.header title="Status Cards"></bfg-box.header>
			<bfg-box.section>
				<bfg-status-card type="default">
					<bfg-status-item label="Status" value="Active" type="success"></bfg-status-item>
					<bfg-status-item label="License Type" value="PRO License" type="default"></bfg-status-item>
				</bfg-status-card>
				<br>
				<bfg-status-card type="trial">
					<bfg-status-item label="Status" value="Trial" type="trial"></bfg-status-item>
					<bfg-status-item label="Days Remaining" value="7" type="default"></bfg-status-item>
				</bfg-status-card>
			</bfg-box.section>
		</bfg-box>

		<!-- Feature List -->
		<bfg-box>
			<bfg-box.header title="Feature Lists"></bfg-box.header>
			<bfg-box.section>
				<h3 class="bfg-field-group-title">
					<bfg-t>Regular Feature List</bfg-t>
				</h3>
				<bfg-feature-list>
					<li>MyBring Booking</li>
					<li>Fixed shipping prices</li>
					<li>Free shipping threshold</li>
					<li>Pick-up points</li>
				</bfg-feature-list>

				<h3 class="bfg-field-group-title">
					<bfg-t>Compact Feature List</bfg-t>
				</h3>
				<bfg-feature-list compact>
					<li>MyBring Booking</li>
					<li>Fixed shipping prices</li>
					<li>Free shipping threshold</li>
				</bfg-feature-list>
			</bfg-box.section>
		</bfg-box>

		<!-- PRO Teaser Components -->
		<bfg-box>
			<bfg-box.header title="PRO Teaser Components"></bfg-box.header>
			<bfg-box.section>
				<div class="bfg-box bfg-pro-teaser-v2">
					<div class="bfg-pro-teaser__shield bfg-pro-teaser__shield--success">
						<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor"
							stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
							<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
							<polyline points="22 4 12 14.01 9 11.01"></polyline>
						</svg>
					</div>
					<h2 class="bfg-pro-teaser__title">
						<bfg-t>PRO Teaser Example</bfg-t>
					</h2>
					<p class="bfg-pro-teaser__subtitle">
						<bfg-t>Example subtitle text for PRO teaser boxes</bfg-t>
					</p>
				</div>
				<p class="bfg-description"><strong>Classes:</strong> <code>.bfg-pro-teaser-v2</code>,
					<code>.bfg-pro-teaser__shield</code>, <code>.bfg-pro-teaser__title</code>,
					<code>.bfg-pro-teaser__subtitle</code>
				</p>
			</bfg-box.section>
		</bfg-box>

		<!-- Active Step Card -->
		<bfg-box>
			<bfg-box.header title="Active Step Card"></bfg-box.header>
			<bfg-box.section>
				<div class="bfg-active-step-card">
					<div class="bfg-active-step__icon">1</div>
					<div class="bfg-active-step__content">
						<h3><bfg-t>Next Step Title</bfg-t></h3>
						<p><bfg-t>Description of the next step to complete</bfg-t>
						</p>
						<a class="bfg-btn bfg-btn--primary" href="#">
							<bfg-t>Take Action</bfg-t>
						</a>
					</div>
				</div>
			</bfg-box.section>
		</bfg-box>

		<!-- Color & Style Reference -->
		<bfg-box>
			<bfg-box.header title="Component CSS Reference"></bfg-box.header>
			<bfg-box.section>
				<h3 class="bfg-field-group-title">
					<bfg-t>Main Classes</bfg-t>
				</h3>
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
					<bfg-t>Form Classes</bfg-t>
				</h3>
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
					<bfg-t>Utility Classes</bfg-t>
				</h3>
				<ul>
					<li><code>.bfgu:flex</code> - Flex container</li>
					<li><code>.bfgu:flex-row</code> - Flex direction row</li>
					<li><code>.bfgu:flex-1</code> - Flex item (flex: 1)</li>
					<li><code>.bfgu:gap-4</code> - Gap between flex items</li>
					<li><code>.bfgu:mt-8</code> - Margin top</li>
					<li><code>.bfgu:mb-5</code> - Margin bottom</li>
				</ul>
			</bfg-box.section>
		</bfg-box>
	</div>
</div>