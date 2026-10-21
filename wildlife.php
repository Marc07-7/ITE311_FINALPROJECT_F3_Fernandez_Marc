<?php
$pageTitle = 'Wildlife Information';
require __DIR__ . '/includes/header.php';

$problems = [];
$notice   = '';
$form     = ['name' => '', 'scientific' => '', 'category' => 'Bird', 'status' => 'Not Evaluated', 'description' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($form as $key => $default) {
        $form[$key] = trim($_POST[$key] ?? $default);
    }
    $rows = load_data('wildlife');

    if (mb_strlen($form['name']) < 2 || mb_strlen($form['name']) > 80) {
        $problems[] = 'Common name must be 2 to 80 characters.';
    }
    if ($form['scientific'] !== '' && !preg_match('/^[A-Za-z .\-]{3,100}$/', $form['scientific'])) {
        $problems[] = 'Scientific name may only contain letters, spaces, periods and hyphens.';
    }
    if (!in_array($form['category'], WILDLIFE_CATEGORIES, true)) {
        $problems[] = 'Invalid category.';
    }
    if (!in_array($form['status'], CONSERVATION_STATUS, true)) {
        $problems[] = 'Invalid conservation status.';
    }
    if (mb_strlen($form['description']) > 500) {
        $problems[] = 'Description must not exceed 500 characters.';
    }
    foreach ($rows as $r) {
        if (strcasecmp($r['name'], $form['name']) === 0) {
            $problems[] = 'This species is already in the directory.';
            break;
        }
    }

    if (!$problems) {
        $rows[] = [
            'id'          => uniqid('wl_'),
            'name'        => $form['name'],
            'scientific'  => $form['scientific'],
            'category'    => $form['category'],
            'status'      => $form['status'],
            'description' => $form['description'],
            'added'       => date('Y-m-d'),
        ];
        save_data('wildlife', $rows);
        $notice = 'Wildlife record added successfully.';
        $form   = ['name' => '', 'scientific' => '', 'category' => 'Bird', 'status' => 'Not Evaluated', 'description' => ''];
    }
}

// Filters for the directory list
$q      = trim($_GET['q'] ?? '');
$fCat   = $_GET['fcat'] ?? '';
$fStat  = $_GET['fstat'] ?? '';

$species = array_filter(array_reverse(load_data('wildlife')), function ($s) use ($q, $fCat, $fStat) {
    if ($q !== '' && stripos($s['name'], $q) === false && stripos($s['scientific'], $q) === false) return false;
    if ($fCat !== '' && $s['category'] !== $fCat) return false;
    if ($fStat !== '' && $s['status'] !== $fStat) return false;
    return true;
});

function status_class(string $status): string
{
    if (in_array($status, ['Endangered', 'Critically Endangered'], true)) return 'red';
    if (in_array($status, ['Vulnerable', 'Near Threatened'], true)) return 'warn';
    return 'gray';
}
?>
<h2>Wildlife Information</h2>
<?php if ($notice): ?><div class="alert good"><?= h($notice) ?></div><?php endif; ?>
<?php foreach ($problems as $p): ?><div class="alert bad"><?= h($p) ?></div><?php endforeach; ?>

<form method="post" class="panel">
    <div class="row">
        <div><label for="name">Common name</label><input type="text" id="name" name="name" maxlength="80" value="<?= h($form['name']) ?>" required></div>
        <div><label for="scientific">Scientific name (optional)</label><input type="text" id="scientific" name="scientific" value="<?= h($form['scientific']) ?>"></div>
    </div>
    <div class="row">
        <div>
            <label for="category">Category</label>
            <select id="category" name="category">
                <?php foreach (WILDLIFE_CATEGORIES as $c): ?><option <?= $form['category'] === $c ? 'selected' : '' ?>><?= $c ?></option><?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="status">Conservation status</label>
            <select id="status" name="status">
                <?php foreach (CONSERVATION_STATUS as $s): ?><option <?= $form['status'] === $s ? 'selected' : '' ?>><?= $s ?></option><?php endforeach; ?>
            </select>
        </div>
    </div>
    <label for="description">Description / habitat notes (max 500)</label>
    <textarea id="description" name="description" rows="3" maxlength="500"><?= h($form['description']) ?></textarea>
    <p><button type="submit">Add Record</button></p>
</form>

<form method="get" class="panel">
    <div class="row">
        <div><label for="q">Search</label><input type="text" id="q" name="q" value="<?= h($q) ?>" placeholder="Common or scientific name"></div>
        <div>
            <label for="fcat">Category</label>
            <select id="fcat" name="fcat">
                <option value="">All</option>
                <?php foreach (WILDLIFE_CATEGORIES as $c): ?><option <?= $fCat === $c ? 'selected' : '' ?>><?= $c ?></option><?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="fstat">Status</label>
            <select id="fstat" name="fstat">
                <option value="">All</option>
                <?php foreach (CONSERVATION_STATUS as $s): ?><option <?= $fStat === $s ? 'selected' : '' ?>><?= $s ?></option><?php endforeach; ?>
            </select>
        </div>
        <div><button type="submit">Filter</button></div>
    </div>
</form>

<?php if (!$species): ?>
    <div class="panel">No wildlife records found.</div>
<?php else: ?>
    <p class="muted"><?= count($species) ?> record(s) shown.</p>
    <div class="grid">
        <?php foreach ($species as $s): ?>
            <div class="card">
                <h3><?= h($s['name']) ?></h3>
                <p class="muted"><em><?= h($s['scientific'] ?: 'Scientific name not recorded') ?></em></p>
                <span class="badge"><?= h($s['category']) ?></span>
                <span class="badge <?= status_class($s['status']) ?>"><?= h($s['status']) ?></span>
                <p><?= h($s['description']) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
