<?php
$pageTitle = 'Home';
require __DIR__ . '/includes/header.php';

$species  = load_data('wildlife');
$packages = load_data('packages');
$business = load_data('listings');
$latest   = array_slice(array_reverse($packages), 0, 3);
?>
<section class="hero">
    <h2>Discover Naujan Lake</h2>
    <p>An information system for eco-tourism and wildlife in Naujan, Oriental Mindoro. Browse wildlife records, tour packages and local tourism businesses.</p>
</section>

<div class="tiles">
    <div class="tile"><b><?= count($species) ?></b>Wildlife Records</div>
    <div class="tile"><b><?= count($packages) ?></b>Tour Packages</div>
    <div class="tile"><b><?= count($business) ?></b>Local Businesses</div>
</div>

<h3>Latest Tour Packages</h3>
<?php if (!$latest): ?>
    <div class="panel">No tour packages published yet.</div>
<?php else: ?>
    <div class="grid">
        <?php foreach ($latest as $p): ?>
            <div class="card">
                <h3><?= h($p['name']) ?></h3>
                <p class="muted"><?= (int) $p['days'] ?> day(s) &middot; up to <?= (int) $p['guests'] ?> guests</p>
                <strong><?= peso((float) $p['price']) ?></strong>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
