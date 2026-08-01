<?php
ob_start();
?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <h4 class="mb-4">Settings</h4>
        
        <div class="card mb-4">
            <div class="card-header">Application</div>
            <div class="card-body">
                <form method="POST" action="/settings">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Site Name</label>
                        <input type="text" name="site_name" class="form-control" 
                               value="<?= e($settings['site_name'] ?? 'BindAdmin') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Records per page</label>
                        <input type="number" name="records_per_page" class="form-control" 
                               value="<?= e($settings['records_per_page'] ?? 50) ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </form>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">BIND9 Configuration (read-only from .env)</div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr>
                        <td class="text-muted" style="width:40%;">Demo Mode</td>
                        <td>
                            <?php if ($bindConfig['demo_mode']): ?>
                                <span class="badge bg-warning text-dark">ON (using storage/zones)</span>
                            <?php else: ?>
                                <span class="badge bg-success">OFF (production)</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Zone Path</td>
                        <td><code><?= e($bindConfig['zone_path']) ?></code></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Default TTL</td>
                        <td><?= e($bindConfig['default_ttl']) ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Default NS1</td>
                        <td><code><?= e($bindConfig['default_ns1']) ?></code></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Default NS2</td>
                        <td><code><?= e($bindConfig['default_ns2']) ?></code></td>
                    </tr>
                </table>
                <p class="text-muted small mt-3 mb-0">
                    Ubah konfigurasi BIND melalui file <code>.env</code> lalu restart PHP-FPM / web server.
                </p>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
echo view('layouts.main', compact('title', 'content'));
