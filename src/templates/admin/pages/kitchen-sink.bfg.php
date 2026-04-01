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
	<div class="bfg-page__header">
		<h1><bfg-t>Kitchen Sink - Component Library</bfg-t></h1>
	</div>

	<div class="bfg-page__main">
		<div class="bfg-notices">
			<div class="wp-header-end"><!-- Notices appear after this div --></div>
		</div>

		<!-- Boxes & Containers -->
		<bfg-section>
			<bfg-section.header title="Boxes & Containers"
				description="Primary container component used throughout the admin"></bfg-section.header>
			<bfg-section.section>
				<p><strong>Classes:</strong> <code>.bfg-section</code>, <code>.bfg-section__header</code>,
					<code>.bfg-section__section</code>
				</p>
				<p><bfg-t>Use .bfg-section for main content sections. Contains header with title/description and section for
						content.</bfg-t>
				</p>
			</bfg-section.section>
		</bfg-section>

		<!-- Typography -->
		<bfg-section>
			<bfg-section.header title="Typography" description="8pt scaling system for consistent visual rhythm"></bfg-section.header>
			<bfg-section.section>
				<h3 class="bfg-field-group-title">
					<bfg-t>Type Scale</bfg-t>
				</h3>
				<table class="bfg-type-scale-table" style="width: 100%; border-collapse: collapse; margin-bottom: 32px;">
					<thead>
						<tr style="text-align: left; border-bottom: 1px solid var(--bfg-border);">
							<th style="padding: 8px 16px 8px 0; font-size: 13px; font-weight: 500; color: var(--bfg-text-muted);">Size</th>
							<th style="padding: 8px 16px; font-size: 13px; font-weight: 500; color: var(--bfg-text-muted);">Line Height</th>
							<th style="padding: 8px 16px; font-size: 13px; font-weight: 500; color: var(--bfg-text-muted);">Weight</th>
							<th style="padding: 8px 0 8px 16px; font-size: 13px; font-weight: 500; color: var(--bfg-text-muted);">Example</th>
						</tr>
					</thead>
					<tbody>
						<tr style="border-bottom: 1px solid var(--bfg-border);">
							<td style="padding: 16px 16px 16px 0;"><code>48px</code></td>
							<td style="padding: 16px;"><code>56px</code></td>
							<td style="padding: 16px;"><code>600</code> Semibold</td>
							<td style="padding: 16px 0 16px 16px; font-size: 48px; line-height: 56px; font-weight: 600;">Display</td>
						</tr>
						<tr style="border-bottom: 1px solid var(--bfg-border);">
							<td style="padding: 16px 16px 16px 0;"><code>32px</code></td>
							<td style="padding: 16px;"><code>40px</code></td>
							<td style="padding: 16px;"><code>600</code> Semibold</td>
							<td style="padding: 16px 0 16px 16px; font-size: 32px; line-height: 40px; font-weight: 600;">Heading XL</td>
						</tr>
						<tr style="border-bottom: 1px solid var(--bfg-border);">
							<td style="padding: 16px 16px 16px 0;"><code>28px</code></td>
							<td style="padding: 16px;"><code>36px</code></td>
							<td style="padding: 16px;"><code>500</code> Medium</td>
							<td style="padding: 16px 0 16px 16px; font-size: 28px; line-height: 36px; font-weight: 500;">Heading L</td>
						</tr>
						<tr style="border-bottom: 1px solid var(--bfg-border);">
							<td style="padding: 16px 16px 16px 0;"><code>24px</code></td>
							<td style="padding: 16px;"><code>32px</code></td>
							<td style="padding: 16px;"><code>500</code> Medium</td>
							<td style="padding: 16px 0 16px 16px; font-size: 24px; line-height: 32px; font-weight: 500;">Heading M</td>
						</tr>
						<tr style="border-bottom: 1px solid var(--bfg-border);">
							<td style="padding: 16px 16px 16px 0;"><code>20px</code></td>
							<td style="padding: 16px;"><code>28px</code></td>
							<td style="padding: 16px;"><code>500</code> Medium</td>
							<td style="padding: 16px 0 16px 16px; font-size: 20px; line-height: 28px; font-weight: 500;">Heading S</td>
						</tr>
						<tr style="border-bottom: 1px solid var(--bfg-border);">
							<td style="padding: 16px 16px 16px 0;"><code>16px</code></td>
							<td style="padding: 16px;"><code>24px</code></td>
							<td style="padding: 16px;"><code>400</code> Regular</td>
							<td style="padding: 16px 0 16px 16px; font-size: 16px; line-height: 24px; font-weight: 400;">Body / Base</td>
						</tr>
						<tr style="border-bottom: 1px solid var(--bfg-border);">
							<td style="padding: 16px 16px 16px 0;"><code>15px</code></td>
							<td style="padding: 16px;"><code>24px</code></td>
							<td style="padding: 16px;"><code>500</code> Medium</td>
							<td style="padding: 16px 0 16px 16px; font-size: 15px; line-height: 24px; font-weight: 500;">Label</td>
						</tr>
						<tr style="border-bottom: 1px solid var(--bfg-border);">
							<td style="padding: 16px 16px 16px 0;"><code>14px</code></td>
							<td style="padding: 16px;"><code>20px</code></td>
							<td style="padding: 16px;"><code>400</code> Regular</td>
							<td style="padding: 16px 0 16px 16px; font-size: 14px; line-height: 20px; font-weight: 400;">Small / Caption</td>
						</tr>
						<tr>
							<td style="padding: 16px 16px 16px 0;"><code>13px</code></td>
							<td style="padding: 16px;"><code>20px</code></td>
							<td style="padding: 16px;"><code>400</code> Regular</td>
							<td style="padding: 16px 0 16px 16px; font-size: 13px; line-height: 20px; font-weight: 400;">Extra Small</td>
						</tr>
					</tbody>
				</table>

				<h3 class="bfg-field-group-title">
					<bfg-t>Font Weights</bfg-t>
				</h3>
				<div class="bfgu:flex bfgu:flex-col bfgu:gap-3 bfgu:mb-8">
					<div class="bfgu:flex bfgu:items-center bfgu:gap-4">
						<code class="bfgu:w-12 bfgu:shrink-0">400</code>
						<span style="font-size: 20px; font-weight: 400;">Regular — Body text, descriptions, captions</span>
					</div>
					<div class="bfgu:flex bfgu:items-center bfgu:gap-4">
						<code class="bfgu:w-12 bfgu:shrink-0">500</code>
						<span style="font-size: 20px; font-weight: 500;">Medium — Labels, headings, buttons</span>
					</div>
					<div class="bfgu:flex bfgu:items-center bfgu:gap-4">
						<code class="bfgu:w-12 bfgu:shrink-0">600</code>
						<span style="font-size: 20px; font-weight: 600;">Semibold — Display headings, field group titles</span>
					</div>
					<div class="bfgu:flex bfgu:items-center bfgu:gap-4">
						<code class="bfgu:w-12 bfgu:shrink-0">700</code>
						<span style="font-size: 20px; font-weight: 700;">Bold — Strong emphasis</span>
					</div>
				</div>

				<h3 class="bfg-field-group-title">
					<bfg-t>Medium Weight Sizes</bfg-t>
				</h3>
				<div class="bfgu:flex bfgu:flex-col bfgu:gap-3 bfgu:mb-8">
					<div class="bfgu:flex bfgu:items-center bfgu:gap-4">
						<code class="bfgu:w-12 bfgu:shrink-0">14px</code>
						<span style="font-size: 14px; font-weight: 500;">Medium 14 — Small labels, compact UI</span>
					</div>
					<div class="bfgu:flex bfgu:items-center bfgu:gap-4">
						<code class="bfgu:w-12 bfgu:shrink-0">16px</code>
						<span style="font-size: 16px; font-weight: 500;">Medium 16 — Default labels, buttons</span>
					</div>
					<div class="bfgu:flex bfgu:items-center bfgu:gap-4">
						<code class="bfgu:w-12 bfgu:shrink-0">18px</code>
						<span style="font-size: 18px; font-weight: 500;">Medium 18 — Larger labels, subheadings</span>
					</div>
				</div>

				<h3 class="bfg-field-group-title">
					<bfg-t>Text Formatting</bfg-t>
				</h3>
				<p>Regular paragraph text with <strong>bold text</strong> and <em>italic text</em>.</p>
				<p>Links look <a href="#">like this</a> and inline <code>code snippets</code> use monospace.</p>
			</bfg-section.section>
		</bfg-section>

		<!-- Notice Banners -->
		<bfg-section>
			<bfg-section.header title="Notice Banners"></bfg-section.header>
			<bfg-section.section>
				<div class="bfgu:flex bfgu:flex-col bfgu:gap-4">
					<bfg-notice type="warning">This is a warning notice banner</bfg-notice>
					<bfg-notice type="info">This is an info notice banner</bfg-notice>
					<bfg-notice type="success">This is a success notice banner</bfg-notice>
					<bfg-notice type="error">This is an error notice banner</bfg-notice>
				</div>
			</bfg-section.section>
		</bfg-section>

		<!-- Buttons & Links -->
		<bfg-section>
			<bfg-section.header title="Buttons"></bfg-section.header>
			<bfg-section.section>
				<h3 class="bfg-field-group-title">
					<bfg-t>Small</bfg-t>
				</h3>
				<div class="bfgu:flex bfgu:flex-wrap bfgu:items-center bfgu:gap-4 bfgu:mb-8">
					<button class="bfg-btn bfg-btn--primary bfg-btn--sm"><bfg-t>Primary</bfg-t></button>
					<button class="bfg-btn bfg-btn--secondary bfg-btn--sm"><bfg-t>Secondary</bfg-t></button>
					<button class="bfg-btn bfg-btn--outline bfg-btn--sm"><bfg-t>Outline</bfg-t></button>
					<button class="bfg-btn bfg-btn--ghost bfg-btn--sm"><bfg-t>Ghost</bfg-t></button>
				</div>

				<h3 class="bfg-field-group-title">
					<bfg-t>Medium (default)</bfg-t>
				</h3>
				<div class="bfgu:flex bfgu:flex-wrap bfgu:items-center bfgu:gap-4 bfgu:mb-8">
					<button class="bfg-btn bfg-btn--primary"><bfg-t>Primary</bfg-t></button>
					<button class="bfg-btn bfg-btn--secondary"><bfg-t>Secondary</bfg-t></button>
					<button class="bfg-btn bfg-btn--outline"><bfg-t>Outline</bfg-t></button>
					<button class="bfg-btn bfg-btn--ghost"><bfg-t>Ghost</bfg-t></button>
				</div>

				<h3 class="bfg-field-group-title">
					<bfg-t>Large</bfg-t>
				</h3>
				<div class="bfgu:flex bfgu:flex-wrap bfgu:items-center bfgu:gap-4 bfgu:mb-8">
					<button class="bfg-btn bfg-btn--primary bfg-btn--lg"><bfg-t>Primary</bfg-t></button>
					<button class="bfg-btn bfg-btn--secondary bfg-btn--lg"><bfg-t>Secondary</bfg-t></button>
					<button class="bfg-btn bfg-btn--outline bfg-btn--lg"><bfg-t>Outline</bfg-t></button>
					<button class="bfg-btn bfg-btn--ghost bfg-btn--lg"><bfg-t>Ghost</bfg-t></button>
				</div>

				<h3 class="bfg-field-group-title">
					<bfg-t>Full Width</bfg-t>
				</h3>
				<div class="bfgu:flex bfgu:gap-4">
					<button class="bfg-btn bfg-btn--primary bfg-btn--full-width">
						<bfg-t>Full Width Button</bfg-t>
					</button>
				</div>
			</bfg-section.section>
		</bfg-section>

		<!-- Badges -->
		<bfg-section>
			<bfg-section.header title="Badges"></bfg-section.header>
			<bfg-section.section>
				<div class="bfgu:flex bfgu:gap-4 bfgu:mb-5">
					<bfg-badge.completed>Done</bfg-badge.completed>
					<bfg-badge.in-progress>In progress</bfg-badge.in-progress>
				</div>
				<p class="bfg-description"><strong>Classes:</strong> <code>.bfg-badge</code>,
					<code>.bfg-badge--completed</code>, <code>.bfg-badge--in-progress</code>
				</p>
			</bfg-section.section>
		</bfg-section>

		<!-- Form Fields -->
		<bfg-section>
			<bfg-section.header title="Form Fields"></bfg-section.header>
			<bfg-section.section>
				<h3 class="bfg-field-group-title">
					<bfg-t>Text Inputs</bfg-t>
				</h3>
				<div class="bfgu:mb-8">
					<bfg-field.text id="demo-text" name="demo-text" label="Text Input" placeholder="Placeholder text"
						description="Help text goes here"></bfg-field.text>

					<bfg-field.number id="demo-number" name="demo-number" label="Number Input with Suffix" value="100"
						suffix="NOK"></bfg-field.number>

					<bfg-field.number id="demo-number-lg" name="demo-number-lg" label="Number Input with Large Suffix"
						value="250" suffix-lg="NOK"></bfg-field.number>
				</div>

				<h3 class="bfg-field-group-title">
					<bfg-t>Select Dropdowns</bfg-t>
				</h3>
				<div class="bfgu:mb-8">
					<bfg-field.select id="demo-select" name="demo-select" label="Custom Select" :options="[
						'option1' => __('Option 1', 'bring-fraktguiden-for-woocommerce'),
						'option2' => __('Option 2', 'bring-fraktguiden-for-woocommerce'),
						'option3' => __('Option 3', 'bring-fraktguiden-for-woocommerce'),
					]" value="option1" placeholder="Select an option"></bfg-field.select>
				</div>

				<h3 class="bfg-field-group-title">
					<bfg-t>Checkboxes</bfg-t>
				</h3>
				<div class="bfgu:mb-8">
					<bfg-field.checkbox name="demo-checkbox-1" value="1" title="Checkbox with description"
						description="Description text appears below the title"></bfg-field.checkbox>

					<bfg-field.checkbox name="demo-checkbox-2" value="1" title="Checkbox without description" checked></bfg-field.checkbox>
				</div>

				<h3 class="bfg-field-group-title">
					<bfg-t>3-Column Grid</bfg-t>
				</h3>
				<div class="bfgu:flex bfgu:flex-row bfgu:gap-4 bfgu:mb-8">
					<div class="bfgu:flex-1">
						<label class="bfg-label">Length</label>
						<div class="bfg-input bfg-input--number">
							<input type="number" value="120" class="bfg-input__field">
							<span class="bfg-suffix">cm</span>
						</div>
					</div>
					<div class="bfgu:flex-1">
						<label class="bfg-label">Width</label>
						<div class="bfg-input bfg-input--number">
							<input type="number" value="80" class="bfg-input__field">
							<span class="bfg-suffix">cm</span>
						</div>
					</div>
					<div class="bfgu:flex-1">
						<label class="bfg-label">Height</label>
						<div class="bfg-input bfg-input--number">
							<input type="number" value="60" class="bfg-input__field">
							<span class="bfg-suffix">cm</span>
						</div>
					</div>
				</div>

				<h3 class="bfg-field-group-title">
					<bfg-t>2-Column Grid</bfg-t>
				</h3>
				<div class="bfgu:flex bfgu:flex-row bfgu:gap-4">
					<div class="bfgu:flex-1">
						<label class="bfg-label">Rate name</label>
						<input type="text" value="Standard Shipping" class="bfg-input">
					</div>
					<div class="bfgu:flex-1">
						<label class="bfg-label">Price</label>
						<div class="bfg-input bfg-input--number">
							<input type="number" value="99" class="bfg-input__field">
							<span class="bfg-suffix-lg">NOK</span>
						</div>
					</div>
				</div>
			</bfg-section.section>
		</bfg-section>

		<!-- Step Row -->
		<bfg-section>
			<bfg-section.header title="Step Rows"></bfg-section.header>
			<bfg-section.section>
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
						<button class="bfg-btn bfg-btn--primary bfg-btn--sm"><bfg-t>Connect API</bfg-t></button>
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
			</bfg-section.section>
		</bfg-section>

		<!-- Access Links -->
		<bfg-section>
			<bfg-section.header title="Access Links"
				description="Clickable rows for navigation without status tracking"></bfg-section.header>
			<bfg-section.section>
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
			</bfg-section.section>
		</bfg-section>

		<!-- Subscription Status -->
		<bfg-section>
			<bfg-section.header title="Subscription Status" description="Displays license and subscription details"></bfg-section.header>
			<bfg-section.section>
				<h3 class="bfg-field-group-title">
					<bfg-t>Active License</bfg-t>
				</h3>
				<bfg-subscription-info class="bfgu:mb-0">
					<bfg-subscription-item.lock class="bfgu:p-0" label="LICENSE STATUS" value="Active" detail="License: PRO-2026-XXXX"></bfg-subscription-item.lock>
					<bfg-subscription-item.calendar class="bfgu:p-0" label="VALID UNTIL" value="March 13, 2027" detail="365 days remaining"></bfg-subscription-item.calendar>
				</bfg-subscription-info>

				<h3 class="bfg-field-group-title">
					<bfg-t>Trial License</bfg-t>
				</h3>
				<bfg-subscription-info class="bfgu:mb-0">
					<bfg-subscription-item.lock class="bfgu:p-0" label="LICENSE STATUS" value="Trial" detail="Activated: March 25, 2026"></bfg-subscription-item.lock>
					<bfg-subscription-item.calendar class="bfgu:p-0" label="VALID UNTIL" value="April 1, 2026" detail="7 days remaining"></bfg-subscription-item.calendar>
				</bfg-subscription-info>
			</bfg-section.section>
		</bfg-section>

		<!-- Feature List -->
		<bfg-section>
			<bfg-section.header title="Feature Lists"></bfg-section.header>
			<bfg-section.section>
				<h3 class="bfg-field-group-title">
					<bfg-t>Regular Feature List</bfg-t>
				</h3>
				<div class="bfgu:mb-8">
					<bfg-feature-list>
						<li>MyBring Booking</li>
						<li>Fixed shipping prices</li>
						<li>Free shipping threshold</li>
						<li>Pick-up points</li>
					</bfg-feature-list>
				</div>

				<h3 class="bfg-field-group-title">
					<bfg-t>Compact Feature List</bfg-t>
				</h3>
				<bfg-feature-list compact>
					<li>MyBring Booking</li>
					<li>Fixed shipping prices</li>
					<li>Free shipping threshold</li>
				</bfg-feature-list>
			</bfg-section.section>
		</bfg-section>


		<!-- Color & Style Reference -->
		<bfg-section>
			<bfg-section.header title="Component CSS Reference"></bfg-section.header>
			<bfg-section.section>
				<h3 class="bfg-field-group-title">
					<bfg-t>Main Classes</bfg-t>
				</h3>
				<ul>
					<li><code>.bfg-admin-page</code> - Main page wrapper</li>
					<li><code>.bfg-page__main</code> - Main content area</li>
					<li><code>.bfg-page__header</code> - Page header section</li>
					<li><code>.bfg-page__header-row</code> - Header row with title and badge</li>
					<li><code>.bfg-section</code> - Card/box container</li>
					<li><code>.bfg-section__header</code> - Box header with title</li>
					<li><code>.bfg-section__section</code> - Box content section</li>
				</ul>

				<h3 class="bfg-field-group-title bfgu:mt-8">
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

				<h3 class="bfg-field-group-title bfgu:mt-8">
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
			</bfg-section.section>
		</bfg-section>
	</div>
</div>