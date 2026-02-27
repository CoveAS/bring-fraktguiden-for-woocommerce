<?php
/**
 * Conditional Field Group Component
 *
 * Renders a group of fields that can be enabled/disabled based on a trigger checkbox.
 *
 * @var array $args {
 *     @type string $id           Required. Unique identifier for the group.
 *     @type string $trigger_name Required. Name attribute of the trigger checkbox.
 *     @type string $content      Inner HTML content (fields to conditionally show).
 *     @type bool   $invert       Invert the logic (hide when checked). Default false.
 * }
 */

$args = wp_parse_args($args ?? [], [
	'id' => '',
	'trigger_name' => '',
	'content' => '',
	'invert' => false,
]);

if (!$args['id'] || !$args['trigger_name']) {
	return;
}

$invert_attr = $args['invert'] ? 'true' : 'false';
?>
<div class="bfg-conditional-group" id="<?php echo esc_attr($args['id']); ?>" data-trigger="<?php echo esc_attr($args['trigger_name']); ?>" data-invert="<?php echo esc_attr($invert_attr); ?>">
	<?php echo $args['content']; ?>
</div>

<script>
(function() {
	const group = document.getElementById('<?php echo esc_js($args['id']); ?>');
	if (!group) return;

	const triggerName = group.dataset.trigger;
	const invert = group.dataset.invert === 'true';
	const trigger = document.querySelector('input[name="' + triggerName + '"]');

	if (!trigger) return;

	function updateState() {
		const isEnabled = invert ? !trigger.checked : trigger.checked;
		const inputs = group.querySelectorAll('input, select, textarea');

		group.style.opacity = isEnabled ? '1' : '0.5';
		group.style.pointerEvents = isEnabled ? 'auto' : 'none';
		inputs.forEach(function(input) {
			input.disabled = !isEnabled;
		});
	}

	updateState();
	trigger.addEventListener('change', updateState);
})();
</script>
