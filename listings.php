<?php
$pageTitle = 'Local Businesses';
require __DIR__ . '/includes/header.php';

$notice = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rows   = load_data('listings');
    $rows[] = [
        'id'         => uniqid('bz_'),
        'name'       => trim($_POST['name'] ?? ''),
        'type'       => $_POST['type'] ?? 'Other',
        'contact'    => trim($_POST['contact'] ?? ''),
        'address'    => trim($_POST['address'] ?? ''),
        'accredited' => isset($_POST['accredited']),
        'tier'       => 'Basic',
        'added'      => date('Y-m-d'),
    ];
    save_data('listings', $rows);
    $notice = 'Business listing added.';
}

$listings = array_reverse(load_data('listings'));
?>
<h2>Local Tourism Businesses</h2>
<?php if ($notice): ?><div class="alert good"><?= h($notice) ?></div><?php endif; ?>

<form method="post" class="panel">
    <div class="row">
        <div><label for="name">Business name</label><input type="text" id="name" name="name"></div>
        <div>
            <label for="type">Type</label>
            <select id="type" name="type">
                <?php foreach (BUSINESS_TYPES as $t): ?><option><?= $t ?></option><?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="row">
        <div><label for="contact">Contact number</label><input type="text" id="contact" name="contact"></div>
        <div><label for="address">Address / barangay</label><input type="text" id="address" name="address"></div>
    </div>
    <label><input type="checkbox" name="accredited" value="1"> Accredited business</label>
    <p><button type="submit">Add Listing</button></p>
</form>

<?php if (!$listings): ?>
    <div class="panel">No business listings yet.</div>
<?php else: ?>
    <div class="grid">
        <?php foreach ($listings as $b): ?>
            <div class="card">
                <h3><?= h($b['name']) ?></h3>
                <span class="badge"><?= h($b['type']) ?></span>
                <?php if ($b['accredited']): ?><span class="badge gold">Accredited</span><?php endif; ?>
                <p class="muted"><?= h($b['address']) ?><br><?= h($b['contact']) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
