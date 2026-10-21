<?php
$pageTitle = 'Local Businesses';
require __DIR__ . '/includes/header.php';

const TIERS = ['Basic', 'Featured', 'Premium'];

$problems = [];
$notice   = '';
$form     = ['name' => '', 'type' => 'Resort', 'contact' => '', 'address' => '', 'tier' => 'Basic'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rows = load_data('listings');

    if (($_POST['action'] ?? '') === 'delete') {
        $rows = array_filter($rows, fn($b) => $b['id'] !== ($_POST['id'] ?? ''));
        save_data('listings', $rows);
        header('Location: listings.php');
        exit;
    }

    foreach ($form as $key => $default) {
        $form[$key] = trim($_POST[$key] ?? $default);
    }
    $accredited = isset($_POST['accredited']);
    $phone      = preg_replace('/[\s-]/', '', $form['contact']);

    if (mb_strlen($form['name']) < 3 || mb_strlen($form['name']) > 100) {
        $problems[] = 'Business name must be 3 to 100 characters.';
    }
    if (!in_array($form['type'], BUSINESS_TYPES, true)) {
        $problems[] = 'Invalid business type.';
    }
    if (!preg_match('/^(09\d{9}|\+639\d{9})$/', $phone)) {
        $problems[] = 'Contact must be a valid PH mobile number (09XXXXXXXXX or +639XXXXXXXXX).';
    }
    if (mb_strlen($form['address']) < 3 || mb_strlen($form['address']) > 150) {
        $problems[] = 'Address must be 3 to 150 characters.';
    }
    if (!in_array($form['tier'], TIERS, true)) {
        $problems[] = 'Invalid promotion tier.';
    }
    if ($form['tier'] !== 'Basic' && !$accredited) {
        $problems[] = 'Only accredited businesses can use Featured or Premium promotion.';
    }

    if (!$problems) {
        $rows[] = [
            'id'         => uniqid('bz_'),
            'name'       => $form['name'],
            'type'       => $form['type'],
            'contact'    => $phone,
            'address'    => $form['address'],
            'accredited' => $accredited,
            'tier'       => $form['tier'],
            'added'      => date('Y-m-d'),
        ];
        save_data('listings', $rows);
        $notice = 'Business listing added successfully.';
        $form   = ['name' => '', 'type' => 'Resort', 'contact' => '', 'address' => '', 'tier' => 'Basic'];
    }
}

$fType    = $_GET['ftype'] ?? '';
$listings = array_filter(array_reverse(load_data('listings')), fn($b) => $fType === '' || $b['type'] === $fType);

// Premium first, then Featured, then Basic
usort($listings, fn($a, $b) => array_search($b['tier'] ?? 'Basic', TIERS) <=> array_search($a['tier'] ?? 'Basic', TIERS));
?>
<h2>Local Tourism Businesses</h2>
<?php if ($notice): ?><div class="alert good"><?= h($notice) ?></div><?php endif; ?>
<?php foreach ($problems as $p): ?><div class="alert bad"><?= h($p) ?></div><?php endforeach; ?>

<form method="post" class="panel">
    <div class="row">
        <div><label for="name">Business name</label><input type="text" id="name" name="name" maxlength="100" value="<?= h($form['name']) ?>" required></div>
        <div>
            <label for="type">Type</label>
            <select id="type" name="type">
                <?php foreach (BUSINESS_TYPES as $t): ?><option <?= $form['type'] === $t ? 'selected' : '' ?>><?= $t ?></option><?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="row">
        <div><label for="contact">Contact number</label><input type="text" id="contact" name="contact" placeholder="09XXXXXXXXX" value="<?= h($form['contact']) ?>" required></div>
        <div><label for="address">Address / barangay</label><input type="text" id="address" name="address" maxlength="150" value="<?= h($form['address']) ?>" required></div>
        <div>
            <label for="tier">Promotion tier</label>
            <select id="tier" name="tier">
                <?php foreach (TIERS as $t): ?><option <?= $form['tier'] === $t ? 'selected' : '' ?>><?= $t ?></option><?php endforeach; ?>
            </select>
        </div>
    </div>
    <label><input type="checkbox" name="accredited" value="1"> Accredited business</label>
    <p><button type="submit">Add Listing</button></p>
</form>

<form method="get" class="panel">
    <div class="row">
        <div>
            <label for="ftype">Filter by type</label>
            <select id="ftype" name="ftype">
                <option value="">All</option>
                <?php foreach (BUSINESS_TYPES as $t): ?><option <?= $fType === $t ? 'selected' : '' ?>><?= $t ?></option><?php endforeach; ?>
            </select>
        </div>
        <div><button type="submit">Filter</button></div>
    </div>
</form>

<?php if (!$listings): ?>
    <div class="panel">No business listings found.</div>
<?php else: ?>
    <div class="grid">
        <?php foreach ($listings as $b): ?>
            <div class="card">
                <h3><?= h($b['name']) ?></h3>
                <span class="badge"><?= h($b['type']) ?></span>
                <?php if (!empty($b['accredited'])): ?><span class="badge gold">Accredited</span><?php endif; ?>
                <?php if (($b['tier'] ?? 'Basic') !== 'Basic'): ?><span class="badge warn"><?= h($b['tier']) ?></span><?php endif; ?>
                <p class="muted"><?= h($b['address']) ?><br><?= h($b['contact']) ?></p>
                <form method="post" class="inline" onsubmit="return confirm('Delete this listing?');">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= h($b['id']) ?>">
                    <button class="danger" type="submit">Delete</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
