<?php
ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Activity Log</h4>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($logs)): ?>
            <p class="text-muted text-center py-5 mb-0">Belum ada aktivitas</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-3">Time</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Zone</th>
                            <th>IP</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                        <tr>
                            <td class="ps-3 small text-muted"><?= e($log['created_at']) ?></td>
                            <td class="fw-medium"><?= e($log['username'] ?? 'system') ?></td>
                            <td><code class="small"><?= e($log['action']) ?></code></td>
                            <td>
                                <?php if ($log['zone_name']): ?>
                                    <a href="/zones/<?= urlencode($log['zone_name']) ?>"><?= e($log['zone_name']) ?></a>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                            <td class="small text-muted"><?= e($log['ip_address'] ?? '—') ?></td>
                            <td class="small text-muted text-truncate" style="max-width:200px;">
                                <?php
                                $d = $log['details'];
                                if (is_array($d)) {
                                    echo e(json_encode($d));
                                } else {
                                    echo e($d ?? '');
                                }
                                ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php
$content = ob_get_clean();
echo view('layouts.main', compact('title', 'content'));
