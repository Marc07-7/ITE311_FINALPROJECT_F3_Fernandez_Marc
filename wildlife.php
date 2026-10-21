<?php
$pageTitle = 'Wildlife Information';
require __DIR__ . '/includes/header.php';

$notice = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rows   = load_data('wildlife');
    $rows[] = [
        'id'          => uniqid('wl_'),
        'name'        => trim($_POST['name'] ?? ''),
        'scientific'  => trim($_POST['scientific'] ?? ''),
        'category'    => $_POST['category'] ?? 'Other',
        'status'      => $_POST['status'] ?? 'Not Evaluated',
        'description' => trim($_POST['description'] ?? ''),
        'added'       => date('Y-m-d'),
    ];
    save_data('wildlife', $rows);
    $notice = 'Wildlife record added.';
}

$species = array_reverse(load_data('wildlife'));
?>
<h2>Wildlife Information</h2>
<?php if ($notice): ?><div class="alert good"><?= h($notice) ?></div><?php endif; ?>

<form method="post" class="panel">
    <div class="row">
        <div><label for="name">Common name</label><input type="text" id="name" name="name"></div>
        <div><label for="scientific">Scientific name</label><input type="text" id="scientific" name="scientific"></div>
    </div>
    <div class="row">
        <div>
            <label for="category">Category</label>
            <select id="category" name="category">
                <?php foreach (WILDLIFE_CATEGORIES as $c): ?><option><?= $c ?></option><?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="status">Conservation status</label>
            <select id="status" name="status">
                <?php foreach (CONSERVATION_STATUS as $s): ?><option><?= $s ?></option><?php endforeach; ?>
            </select>
        </div>
    </div>
    <label for="description">Description / habitat notes</label>
    <textarea id="description" name="description" rows="3"></textarea>
    <p><button type="submit">Add Record</button></p>
</form>

<?php if (!$species): ?>
    <div class="panel">No wildlife records yet.</div>
<?php else: ?>
    <div class="grid">
        <?php foreach ($species as $s): ?>
            <div class="card">
                <h3><?= h($s['name']) ?></h3>
                <p class="muted"><em><?= h($s['scientific'] ?: 'Scientific name not recorded') ?></em></p>
                <span class="badge"><?= h($s['category']) ?></span>
                <span class="badge gray"><?= h($s['status']) ?></span>
                <p><?= h($s['description']) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
