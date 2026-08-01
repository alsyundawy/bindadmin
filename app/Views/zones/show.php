<?php
ob_start();
$editableRecords = array_values(array_filter($records, fn($r) => $r['type'] !== 'SOA'));
$title = 'Zone: ' . e($zone['name']);
?>
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-1"><?= e($zone['name']) ?></h4>
        <span class="text-muted small">
            <?= e(strtoupper($zone['type'] ?? 'master')) ?> &middot;
            Serial <?= e((string)($zone['serial'] ?? '-')) ?> &
            <?= count($records) ?> records
            <?php if (!empty($demoMode)): ?>
                <span class="badge bg-warning text-dark ms-1">DEMO</span>
            <?php endif; ?>
        </span>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= e(url('/zones')) ?>" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Back
        </a>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addRecordModal">
            <i class="fa-solid fa-plus me-1"></i> Add Record
        </button>
    </div>
</div>

<?php if (!empty($_SESSION['flash_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= e($_SESSION['flash_success']) ?>
        <?php unset($_SESSION['flash_success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if (!empty($_SESSION['flash_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= e($_SESSION['flash_error']) ?>
        <?php unset($_SESSION['flash_error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Records</span>
        <input type="search" id="recordSearch" class="form-control form-control-sm" style="max-width:220px" placeholder="Search records...">
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="recordsTable">
                <thead>
                    <tr>
                        <th class="ps-3">Name</th>
                        <th>Type</th>
                        <th>TTL</th>
                        <th>Priority</th>
                        <th>Content</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($records)): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">No records.</td></tr>
                    <?php else: ?>
                        <?php foreach ($records as $idx => $rec): ?>
                            <tr>
                                <td class="ps-3 font-monospace small"><?= e($rec['name']) ?></td>
                                <td><span class="badge bg-info text-dark"><?= e($rec['type']) ?></span></td>
                                <td><?= e((string)$rec['ttl']) ?></td>
                                <td><?= e((string)($rec['priority'] ?? '-')) ?></td>
                                <td class="font-monospace small text-break" style="max-width:320px"><?= e($rec['content']) ?></td>
                                <td class="text-end pe-3">
                                    <?php if ($rec['type'] !== 'SOA'): ?>
                                        <button class="btn btn-sm btn-outline-primary me-1" onclick='editRecord(<?= (int)$idx ?>, <?= json_encode($rec, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'>
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <form method="post" action="<?= e(url('/zones/' . rawurlencode($zone['name']) . '/records/' . $idx . '/delete')) ?>" class="d-inline" onsubmit="return confirm('Delete this record?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted small">SOA</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Record Modal -->
<div class="modal fade" id="addRecordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="<?= e(url('/zones/' . rawurlencode($zone['name']) . '/records')) ?>">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Add Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" placeholder="@ or subdomain" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select class="form-select" name="type" id="addType" required>
                            <?php foreach (['A','AAAA','CNAME','MX','TXT','NS','PTR','SRV','CAA'] as $t): ?>
                                <option value="<?= $t ?>"><?= $t ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">TTL</label>
                        <input type="number" class="form-control" name="ttl" value="3600" min="60" required>
                    </div>
                    <div class="mb-3" id="addPriorityWrap" style="display:none">
                        <label class="form-label">Priority</label>
                        <input type="number" class="form-control" name="priority" value="10" min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Content</label>
                        <input type="text" class="form-control" name="content" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Record Modal -->
<div class="modal fade" id="editRecordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" id="editRecordForm">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Edit Record</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" id="editName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select class="form-select" name="type" id="editType" required>
                            <?php foreach (['A','AAAA','CNAME','MX','TXT','NS','PTR','SRV','CAA'] as $t): ?>
                                <option value="<?= $t ?>"><?= $t ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">TTL</label>
                        <input type="number" class="form-control" name="ttl" id="editTtl" min="60" required>
                    </div>
                    <div class="mb-3" id="editPriorityWrap" style="display:none">
                        <label class="form-label">Priority</label>
                        <input type="number" class="form-control" name="priority" id="editPriority" min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Content</label>
                        <input type="text" class="form-control" name="content" id="editContent" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function togglePriority(selectId, wrapId) {
    const t = document.getElementById(selectId).value;
    document.getElementById(wrapId).style.display = (t === 'MX' || t === 'SRV') ? 'block' : 'none';
}
document.getElementById('addType').addEventListener('change', () => togglePriority('addType', 'addPriorityWrap'));
document.getElementById('editType').addEventListener('change', () => togglePriority('editType', 'editPriorityWrap'));

function editRecord(idx, rec) {
    $('#editRecordForm').attr('action', '/zones/<?= urlencode($zone['name']) ?>/records/' + idx);
    $('#editName').val(rec.name);
    $('#editType').val(rec.type).trigger('change');
    $('#editTtl').val(rec.ttl);
    $('#editPriority').val(rec.priority || 10);
    $('#editContent').val(rec.content);
    new bootstrap.Modal('#editRecordModal').show();
}

$('#recordSearch').on('keyup', function() {
    const q = $(this).val().toLowerCase();
    $('#recordsTable tbody tr').each(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(q) > -1);
    });
});
</script>
<?php
$content = ob_get_clean();
$scripts = '';
echo view('layouts.main', compact('title', 'content', 'scripts'));
