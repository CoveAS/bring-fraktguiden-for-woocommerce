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

		<!-- Indicator Dots -->
		<bfg-box>
			<bfg-box.header title="Indicator Dots"
				description="Simple colored dots for health/status indication"></bfg-box.header>
			<bfg-box.section>
				<div class="bfgu:flex bfgu:items-center bfgu:gap-4 bfgu:mb-5">
					<div class="bfgu:flex bfgu:items-center bfgu:gap-2">
						<bfg-indicator-dot class="bfg-indicator-dot--healthy"></bfg-indicator-dot>
						<span>Healthy</span>
					</div>
					<div class="bfgu:flex bfgu:items-center bfgu:gap-2">
						<bfg-indicator-dot class="bfg-indicator-dot--warning"></bfg-indicator-dot>
						<span>Warning</span>
					</div>
					<div class="bfgu:flex bfgu:items-center bfgu:gap-2">
						<bfg-indicator-dot class="bfg-indicator-dot--error"></bfg-indicator-dot>
						<span>Error</span>
					</div>
				</div>
				<p class="bfg-description"><strong>Usage:</strong>
					<code>&lt;bfg-indicator-dot class="bfg-indicator-dot--healthy"&gt;&lt;/bfg-indicator-dot&gt;</code>
				</p>
				<p class="bfg-description"><strong>Modifiers:</strong> <code>.bfg-indicator-dot--healthy</code>,
					<code>.bfg-indicator-dot--warning</code>, <code>.bfg-indicator-dot--error</code>
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
						<bfg-t>Add shipping method</bfg-t>
						<bfg-step-desc><bfg-t>Add the Bring method to your shipping zone</bfg-t></bfg-step-desc>
						<bfg-badge.completed><t>Done</t></bfg-badge.completed>
					</bfg-step.completed>

					<bfg-step.completed href="#">
						<bfg-t>Select Shipping services</bfg-t>
						<bfg-step-desc><bfg-t>Choose your Bring services to offer</bfg-t></bfg-step-desc>
						<bfg-badge.completed><t>Done</t></bfg-badge.completed>
					</bfg-step.completed>

					<bfg-step.in-progress number="3">
						<bfg-t>API conversion</bfg-t>
						<bfg-step-desc><bfg-t>Connect your Bring API credentials</bfg-t></bfg-step-desc>
						<button class="bfg-btn bfg-btn--primary"><bfg-t>Connect API</bfg-t></button>
						<bfg-badge.in-progress><t>In progress</t></bfg-badge.in-progress>
					</bfg-step.in-progress>

					<bfg-step.pending href="#" number="4">
						<bfg-t>Set fallback rates</bfg-t>
						<bfg-step-desc><bfg-t>Configure backup shipping rates</bfg-t></bfg-step-desc>
					</bfg-step.pending>

					<bfg-step.pending href="#" number="5">
						<bfg-t>Test shipping</bfg-t>
						<bfg-step-desc><bfg-t>Test with a sample product</bfg-t></bfg-step-desc>
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
			<bfg-box.header title="Status Cards" description="Status display for Pro licenses and trials"></bfg-box.header>
			<bfg-box.section>
				<bfg-status-card>
					<bfg-status-item.lock label="LICENSE STATUS" value="Active" detail="License: PRO-2026-XXXX"></bfg-status-item.lock>
					<bfg-status-item.calendar label="VALID UNTIL" value="March 13, 2027" detail="365 days remaining"></bfg-status-item.calendar>
				</bfg-status-card>

				<bfg-status-card>
					<bfg-status-item.lock label="STATUS" value="Trial" detail=""></bfg-status-item.lock>
					<bfg-status-item.calendar label="DAYS REMAINING" value="7" detail="Expires April 1, 2026"></bfg-status-item.calendar>
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