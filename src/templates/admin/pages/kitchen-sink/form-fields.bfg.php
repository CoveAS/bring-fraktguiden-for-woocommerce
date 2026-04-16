<?php /* Kitchen Sink: Form Fields */ ?>

<!-- Form Fields -->
<bfg-section>
	<bfg-section.header title="Form Fields" description="All input types with MD / LG size variants"></bfg-section.header>
	<bfg-section.section>

		<?php $size_label = 'width:28px;flex-shrink:0;font-size:10px;text-transform:uppercase;letter-spacing:.07em;color:var(--bfg-text-muted);display:flex;align-items:center'; ?>

		<!-- ── Text Input ──────────────────────────────────────────── -->
		<h3 class="bfg-field-group-title"><bfg-t>Text Input</bfg-t></h3>
		<div>
			<bfg-field.text id="demo-text" label="Label" description="Help text goes here">
				<input type="text" id="demo-text" name="demo-text" placeholder="Placeholder text">
			</bfg-field.text>
		</div>
		<div class="bfgu:flex bfgu:flex-col bfgu:gap-3 bfgu:mb-10" style="">
			<div class="bfgu:flex bfgu:items-center bfgu:gap-3">
				<span style="<?php echo $size_label; ?>">MD</span>
				<div class="bfg-field bfgu:flex-1" style="margin:0"><input type="text" placeholder="Medium — 40px" style="width:100%"></div>
			</div>
			<div class="bfgu:flex bfgu:items-center bfgu:gap-3">
				<span style="<?php echo $size_label; ?>">LG</span>
				<div class="bfg-field bfg-field--lg bfgu:flex-1" style="margin:0"><input type="text" placeholder="Large — 48px" style="width:100%"></div>
			</div>
		</div>

		<!-- ── Number Input ────────────────────────────────────────── -->
		<h3 class="bfg-field-group-title bfgu:mt-4"><bfg-t>Number Input</bfg-t></h3>
		<div>
			<bfg-field.number id="demo-number" name="demo-number" label="Amount (small suffix)" value="100" suffix="NOK"></bfg-field.number>
			<bfg-field.number id="demo-number-lg-s" name="demo-number-lg-s" label="Amount (large suffix)" value="250" suffix-lg="NOK"></bfg-field.number>
		</div>
		<div class="bfgu:flex bfgu:flex-col bfgu:gap-3 bfgu:mb-10" style="">
			<div class="bfgu:flex bfgu:items-center bfgu:gap-3">
				<span style="<?php echo $size_label; ?>">MD</span>
				<div class="bfg-field bfgu:flex-1" style="margin:0">
					<div class="bfg-input bfg-input--number"><input type="number" value="100"><span class="bfg-suffix">NOK</span></div>
				</div>
			</div>
			<div class="bfgu:flex bfgu:items-center bfgu:gap-3">
				<span style="<?php echo $size_label; ?>">LG</span>
				<div class="bfg-field bfg-field--lg bfgu:flex-1" style="margin:0">
					<div class="bfg-input bfg-input--number"><input type="number" value="100"><span class="bfg-suffix">NOK</span></div>
				</div>
			</div>
		</div>

		<!-- ── Select ──────────────────────────────────────────────── -->
		<h3 class="bfg-field-group-title bfgu:mt-4"><bfg-t>Select</bfg-t></h3>
		<div>
			<bfg-field.select id="demo-select" name="demo-select" label="Label" placeholder="Select an option">
				<option value="option1"><?php esc_html_e('Option 1', 'bring-fraktguiden-for-woocommerce'); ?></option>
				<option value="option2"><?php esc_html_e('Option 2', 'bring-fraktguiden-for-woocommerce'); ?></option>
				<option value="option3"><?php esc_html_e('Option 3', 'bring-fraktguiden-for-woocommerce'); ?></option>
			</bfg-field.select>
		</div>
		<div class="bfgu:flex bfgu:flex-col bfgu:gap-3 bfgu:mb-10" style="">
			<div class="bfgu:flex bfgu:items-center bfgu:gap-3">
				<span style="<?php echo $size_label; ?>">MD</span>
				<div class="bfg-field bfgu:flex-1" style="margin:0"><select style="width:100%"><option>Option 1</option><option>Option 2</option></select></div>
			</div>
			<div class="bfgu:flex bfgu:items-center bfgu:gap-3">
				<span style="<?php echo $size_label; ?>">LG</span>
				<div class="bfg-field bfg-field--lg bfgu:flex-1" style="margin:0"><select style="width:100%"><option>Option 1</option><option>Option 2</option></select></div>
			</div>
		</div>

		<!-- ── Checkbox ────────────────────────────────────────────── -->
		<h3 class="bfg-field-group-title bfgu:mt-4"><bfg-t>Checkbox</bfg-t></h3>
		<div class="bfgu:mb-10" style="">
			<bfg-field.checkbox name="demo-checkbox-1" value="1" title="With description"
				description="Description text appears below the title"></bfg-field.checkbox>
			<bfg-field.checkbox name="demo-checkbox-2" value="1" title="Checked state" checked></bfg-field.checkbox>
		</div>

		<!-- ── Grid Layouts ────────────────────────────────────────── -->
		<h3 class="bfg-field-group-title bfgu:mt-4"><bfg-t>3-Column Grid</bfg-t></h3>
		<div class="bfgu:flex bfgu:flex-col bfgu:gap-3 bfgu:mb-10">
			<div class="bfgu:flex bfgu:items-center bfgu:gap-3">
				<span style="<?php echo $size_label; ?>">MD</span>
				<div class="bfg-field bfgu:flex-1" style="margin:0">
					<div class="bfgu:flex bfgu:gap-3">
						<div class="bfgu:flex-1"><div class="bfg-input bfg-input--number"><input type="number" value="120"><span class="bfg-suffix">cm</span></div></div>
						<div class="bfgu:flex-1"><div class="bfg-input bfg-input--number"><input type="number" value="80"><span class="bfg-suffix">cm</span></div></div>
						<div class="bfgu:flex-1"><div class="bfg-input bfg-input--number"><input type="number" value="60"><span class="bfg-suffix">cm</span></div></div>
					</div>
				</div>
			</div>
			<div class="bfgu:flex bfgu:items-center bfgu:gap-3">
				<span style="<?php echo $size_label; ?>">LG</span>
				<div class="bfg-field bfg-field--lg bfgu:flex-1" style="margin:0">
					<div class="bfgu:flex bfgu:gap-3">
						<div class="bfgu:flex-1"><div class="bfg-input bfg-input--number"><input type="number" value="120"><span class="bfg-suffix">cm</span></div></div>
						<div class="bfgu:flex-1"><div class="bfg-input bfg-input--number"><input type="number" value="80"><span class="bfg-suffix">cm</span></div></div>
						<div class="bfgu:flex-1"><div class="bfg-input bfg-input--number"><input type="number" value="60"><span class="bfg-suffix">cm</span></div></div>
					</div>
				</div>
			</div>
		</div>

		<h3 class="bfg-field-group-title"><bfg-t>2-Column Grid</bfg-t></h3>
		<div class="bfgu:flex bfgu:flex-col bfgu:gap-3">
			<div class="bfgu:flex bfgu:items-center bfgu:gap-3">
				<span style="<?php echo $size_label; ?>">MD</span>
				<div class="bfg-field bfgu:flex-1" style="margin:0">
					<div class="bfgu:flex bfgu:gap-3">
						<div class="bfgu:flex-1"><input type="text" value="Standard Shipping" style="width:100%"></div>
						<div class="bfgu:flex-1"><div class="bfg-input bfg-input--number"><input type="number" value="99"><span class="bfg-suffix-lg">NOK</span></div></div>
					</div>
				</div>
			</div>
			<div class="bfgu:flex bfgu:items-center bfgu:gap-3">
				<span style="<?php echo $size_label; ?>">LG</span>
				<div class="bfg-field bfg-field--lg bfgu:flex-1" style="margin:0">
					<div class="bfgu:flex bfgu:gap-3">
						<div class="bfgu:flex-1"><input type="text" value="Standard Shipping" style="width:100%"></div>
						<div class="bfgu:flex-1"><div class="bfg-input bfg-input--number"><input type="number" value="99"><span class="bfg-suffix-lg">NOK</span></div></div>
					</div>
				</div>
			</div>
		</div>

	</bfg-section.section>
</bfg-section>
