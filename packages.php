<?php
$pageTitle = 'Tour Packages';
require __DIR__ . '/includes/header.php';

$problems = [];
$notice   = '';
$form     = ['name' => '', 'price' => '', 'days' => '1', 'guests' => '10', 'description' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rows = load_data('packages');

    if (($_POST['action'] ?? '') === 'delete') {
        $rows = array_filter($rows, fn($p) => $p['id'] !== ($_POST['id'] ?? ''));
        save_data('packages', $rows);
        header('Location: packages.php');
        exit;
    }

    foreach ($form as $key => $default) {
        $form[$key] = trim($_POST[$key] ?? $default);
    }
    $featured = isset($_POST['featured']);

    if (mb_strlen($form['name']) < 3 || mb_strlen($form['name']) > 100) {
        $problems[] = 'Package name must be 3 to 100 characters.';
    }
    if (!is_numeric($form['price']) || $form['price'] <= 0 || $form['price'] > 100000) {
        $problems[] = 'Price must be a number between 1 and 100,000.';
    }
    if (!ctype_digit($form['days']) || $form['days'] < 1 || $form['days'] > 14) {
        $problems[] = 'Duration must be 1 to 14 days.';
    }
    if (!ctype_digit($form['guests']) || $form['guests'] < 1 || $form['guests'] > 100) {
        $problems[] = 'Max guests must be 1 to 100.';
    }
    if (mb_strlen($form['description']) > 400) {
        $problems[] = 'Description must not exceed 400 characters.';
    }

    if (!$problems) {
        $rows[] = [
            'id'          => uniqid('pk_'),
            'name'        => $form['name'],
            'price'       => (float) $form['price'],
            'days'        => (int) $form['days'],
            'guests'      => (int) $form['guests'],
            'description' => $form['description'],
            'featured'    => $featured,
            'added'       => date('Y-m-d'),
        ];
        save_data('packages', $rows);
        $notice = 'Tour package added successfully.';
        $form   = ['name' => '', 'price' => '', 'days' => '1', 'guests' => '10', 'description' => ''];
    }
}

// Featured packages first, then newest
$packages = array_reverse(load_data('packages'));
usort($packages, fn($a, $b) => (int) !empty($b['featured']) <=> (int) !empty($a['featured']));
?>
<h2>Eco-Tourism Packages</h2>
<?php if ($notice): ?><div class="alert good"><?= h($notice) ?></div><?php endif; ?>
<?php foreach ($problems as $p): ?><div class="alert bad"><?= h($p) ?></div><?php endforeach; ?>

<form method="post" class="panel">
    <label for="name">Package name</label>
    <input type="text" id="name" name="name" maxlength="100" value="<?= h($form['name']) ?>" required>
    <div class="row">
        <div><label for="price">Price per head (PHP)</label><input type="number" step="0.01" min="1" max="100000" id="price" name="price" value="<?= h($form['price']) ?>" required></div>
        <div><label for="days">Duration (days)</label><input type="number" min="1" max="14" id="days" name="days" value="<?= h($form['days']) ?>" required></div>
        <div><label for="guests">Max guests</label><input type="number" min="1" max="100" id="guests" name="guests" value="<?= h($form['guests']) ?>" required></div>
    </div>
    <label for="description">Description (max 400)</label>
    <textarea id="description" name="description" rows="3" maxlength="400"><?= h($form['description']) ?></textarea>
    <label><input type="checkbox" name="featured" value="1"> Feature this package (paid promotion)</label>
    <p><button type="submit">Add Package</button></p>
</form>

<?php if (!$packages): ?>
    <div class="panel">No tour packages yet.</div>
<?php else: ?>
    <div class="grid">
        <?php foreach ($packages as $p): ?>
            <div class="card">
                <h3><?= h($p['name']) ?> <?php if (!empty($p['featured'])): ?><span class="badge gold">Featured</span><?php endif; ?></h3>
                <p class="muted"><?= (int) $p['days'] ?> day(s) &middot; up to <?= (int) $p['guests'] ?> guests</p>
                <p><?= h($p['description']) ?></p>
                <strong><?= peso((float) $p['price']) ?></strong> <span class="muted">per head</span>
                <form method="post" class="inline" onsubmit="return confirm('Delete this package?');">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= h($p['id']) ?>">
                    <button class="danger" type="submit">Delete</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
