<?php
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">DNS Zones</h4>
        <p class="text-muted small mb-0"><?= count($zones) ?> zone terdaftar</p>
    </div>
    <a href="/zones/create" class="btn btn-primary">
        <i class="fa-solid fa-plus me-1"></i> Add Zone
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($zones)): ?>
            <div class="text-center text-muted py-5">
                <i class="fa-solid fa-globe fa-3x mb-3 opacity-25"></i>
                <p>Belum ada zone.</p>
                <a href="/zones/create" class="btn btn-primary">Create First Zone</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="zonesTable">
                    <thead>
                        <tr>
                            <th class="ps-3">Zone Name</th>
                            <th>Type</th>
                            <th>Serial</th>
                            <th>Records</th>
                            <th>Modified</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($zones as $z): ?>
                        <tr>
                            <td class="ps-3">
                                <a href="/zones/<?= urlencode($z['name']) ?>" class="fw-semibold text-decoration-none">
                                    <i class="fa-solid fa-globe me-2 text-primary opacity-75"></i>
                                    <?= e($z['name']) ?>
                                </a>
                            </td>
                            <td><span class="badge bg-secondary"><?= e(ucfirst($z['type'] ?? 'master')) ?></span></td>
                            <td class="text-muted small font-monospace"><?= e($z['serial'] ?? '—') ?></td>
                            <td><?= $z['records_count'] ?? 0 ?></td>
                            <td class="text-muted small"><?= e($z['modified'] ?? '—') ?></td>
                            <td class="text-end pe-3">
                                <div class="btn-group btn-group-sm">
                                    <a href="/zones/<?= urlencode($z['name']) ?>" class="btn btn-outline-primary" title="Manage">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <a href="/zones/<?= urlencode($z['name']) ?>/export" class="btn btn-outline-secondary" title="Export">
                                        <i class="fa-solid fa-download"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" title="Delete"
                                            onclick="confirmDelete('<?= e($z['name']) ?>')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<form id="deleteForm" method="POST" style="display:none;">
    <?= csrf_field() ?>
</form>

<script>
function confirmDelete(zone) {
    if (confirm('Hapus zone "' + zone + '"? Tindakan ini tidak dapat dibatalkan.')) {
        const form = document.getElementById('deleteForm');
        form.action = '/zones/' + encodeURIComponent(zone) + '/delete';
        form.submit();
    }
}
</script>
<?php
$content = ob_get_clean();
$scripts = '';
echo view('layouts.main', compact('title', 'content', 'scripts') + ['demoMode' => $demoMode ?? false]);
