<?php
ob_start();
?>
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="icon bg-primary bg-opacity-25 text-primary">
                    <i class="fa-solid fa-globe"></i>
                </div>
                <div>
                    <div class="text-muted small">Zones</div>
                    <div class="fs-4 fw-bold"><?= $zoneCount ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="icon bg-success bg-opacity-25 text-success">
                    <i class="fa-solid fa-list"></i>
                </div>
                <div>
                    <div class="text-muted small">Records</div>
                    <div class="fs-4 fw-bold"><?= $totalRecords ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="icon bg-info bg-opacity-25 text-info">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <div class="text-muted small">Users</div>
                    <div class="fs-4 fw-bold"><?= $userCount ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="icon bg-warning bg-opacity-25 text-warning">
                    <i class="fa-solid fa-server"></i>
                </div>
                <div>
                    <div class="text-muted small">Status</div>
                    <div class="fs-6 fw-bold text-truncate"><?= e($status['status'] ?? 'unknown') ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-globe me-2"></i> Zones</span>
                <a href="/zones/create" class="btn btn-sm btn-primary">
                    <i class="fa-solid fa-plus me-1"></i> Add Zone
                </a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($zones)): ?>
                    <div class="text-center text-muted py-5">
                        <i class="fa-solid fa-folder-open fa-3x mb-3 opacity-50"></i>
                        <p>Belum ada zone. Buat zone pertama Anda.</p>
                        <a href="/zones/create" class="btn btn-primary btn-sm">Create Zone</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-3">Zone</th>
                                    <th>Type</th>
                                    <th>Serial</th>
                                    <th>Records</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($zones, 0, 10) as $z): ?>
                                <tr>
                                    <td class="ps-3">
                                        <a href="/zones/<?= urlencode($z['name']) ?>" class="text-decoration-none fw-medium">
                                            <?= e($z['name']) ?>
                                        </a>
                                    </td>
                                    <td><span class="badge bg-secondary"><?= e($z['type'] ?? 'master') ?></span></td>
                                    <td class="text-muted small"><?= e($z['serial'] ?? '-') ?></td>
                                    <td><?= $z['records_count'] ?? 0 ?></td>
                                    <td class="text-end pe-3">
                                        <a href="/zones/<?= urlencode($z['name']) ?>" class="btn btn-sm btn-outline-secondary">
                                            <i class="fa-solid fa-arrow-right"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <i class="fa-solid fa-clock-rotate-left me-2"></i> Recent Activity
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentLogs)): ?>
                    <p class="text-muted text-center py-4 mb-0 small">Belum ada aktivitas</p>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentLogs as $log): ?>
                        <div class="list-group-item bg-transparent border-secondary px-3 py-2">
                            <div class="d-flex justify-content-between">
                                <span class="small fw-medium"><?= e($log['username'] ?? 'system') ?></span>
                                <span class="text-muted" style="font-size:0.7rem;"><?= e($log['created_at']) ?></span>
                            </div>
                            <div class="small text-muted">
                                <?= e($log['action']) ?>
                                <?php if ($log['zone_name']): ?>
                                    <span class="text-primary"><?= e($log['zone_name']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if ($demoMode): ?>
        <div class="alert alert-warning mt-3 small">
            <i class="fa-solid fa-flask me-1"></i>
            <strong>Demo Mode</strong> aktif. Zone file disimpan di <code>storage/zones/</code>. 
            Ubah <code>BIND_DEMO_MODE=false</code> di .env untuk production.
        </div>
        <?php endif; ?>
    </div>
</div>
<?php
$content = ob_get_clean();
echo view('layouts.main', compact('title', 'content', 'demoMode') + ['demoMode' => $demoMode ?? false]);
