<?php
$pageTitle = 'Tour Packages';
require __DIR__ . '/includes/header.php';

$notice = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rows   = load_data('packages');
    $rows[] = [
        'id'          => uniqid('pk_'),
        'name'        => trim($_POST['name'] ?? ''),
        'price'       => (float) ($_POST['price'] ?? 0),
        'days'        => (int) ($_POST['days'] ?? 1),
        'guests'      => (int) ($_POST['guests'] ?? 1),
        'description' => trim($_POST['description'] ?? ''),
        'featured'    => false,
        'added'       => date('Y-m-d'),
    ];
    save_data('packages', $rows);
    $notice = 'Tour package added.';
}

$packages = array_reverse(load_data('packages'));
?>
<h2>Eco-Tourism Packages</h2>
<?php if ($notice): ?><div class="alert good"><?= h($notice) ?></div><?php endif; ?>

<form method="post" class="panel">
    <label for="name">Package name</label>
    <input type="text" id="name" name="name">
    <div class="row">
        <div><label for="price">Price per head (PHP)</label><input type="number" step="0.01" id="price" name="price"></div>
        <div><label for="days">Duration (days)</label><input type="number" id="days" name="days" value="1"></div>
        <div><label for="guests">Max guests</label><input type="number" id="guests" name="guests" value="10"></div>
    </div>
    <label for="description">Description</label>
    <textarea id="description" name="description" rows="3"></textarea>
    <p><button type="submit">Add Package</button></p>
</form>

<?php if (!$packages): ?>
    <div class="panel">No tour packages yet.</div>
<?php else: ?>
    <div class="grid">
        <?php foreach ($packages as $p): ?>
            <div class="card">
                <h3><?= h($p['name']) ?></h3>
                <p class="muted"><?= (int) $p['days'] ?> day(s) &middot; up to <?= (int) $p['guests'] ?> guests</p>
                <p><?= h($p['description']) ?></p>
                <strong><?= peso((float) $p['price']) ?></strong> <span class="muted">per head</span>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
