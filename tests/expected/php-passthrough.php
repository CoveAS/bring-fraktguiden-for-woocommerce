<div class="wrap">
    <h1><?php echo esc_html($title); ?></h1>

    <div class="content">
        <?php foreach ($items as $item): ?>
            <p><?php echo esc_html($item); ?></p>
        <?php endforeach; ?>
    </div>

    <?php if (!empty($items)): ?>
        <p>Found <?php echo count($items); ?> items</p>
    <?php endif; ?>
</div>
