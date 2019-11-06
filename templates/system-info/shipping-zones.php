<div class="shippping-zone">
	<div class="shippping-zone__name">
		<strong>
			<span class="shippping-zone__name"><?php echo esc_html( $zone['zone_name'] ); ?></span> -
			<span class="shippping-zone__location-name"><?php echo esc_html( $zone['formatted_zone_location'] ); ?></span>
		</strong>
	</div>

	<div class="shippping-zone__locations">
		<strong class="shippping-zone__locations-title">Locations</strong>
		<ul class="shippping-zone__locations-list">
			<?php foreach ( $zone['zone_locations'] as $location ) : ?>
				<li>
					<dl>
						<dt><?php esc_html_e( 'Type' ); ?></dt>
						<dd class="location__type"><?php echo esc_html( $location->type ); ?></dd>
						<dt><?php esc_html_e( 'Code' ); ?></dt>
						<dd class="location__code"><?php echo esc_html( $location->code ); ?></dd>
					</dl>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<div class="shippping-zone__shipping-methods">
		<strong><?php esc_html_e( 'Shipping methods' ); ?></strong>
		<ul class="shippping-zone__shipping-methods-list">
			<?php foreach ( $zone['shipping_methods'] as $method ) : ?>
				<li>
					<?php echo esc_html( get_class( $method ) ); ?>
					<?php esc_html( $method->title ) ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
