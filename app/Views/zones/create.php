<?php
ob_start();
?>
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <i class="fa-solid fa-plus me-2"></i> Create New Zone
            </div>
            <div class="card-body">
                <form method="POST" action="/zones">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label class="form-label">Zone Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" 
                               value="<?= e(old('name')) ?>" 
                               placeholder="example.com" required
                               pattern="[a-zA-Z0-9][a-zA-Z0-9\-\.]*[a-zA-Z0-9]">
                        <div class="form-text">Contoh: example.com, internal.lan</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">A Record (Apex) — opsional</label>
                        <input type="text" name="ip" class="form-control" 
                               value="<?= e(old('ip')) ?>" 
                               placeholder="192.168.1.10">
                        <div class="form-text">IP address untuk record @ (apex)</div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Primary NS</label>
                            <input type="text" name="ns1" class="form-control" 
                                   value="<?= e(old('ns1', config('bind.default_ns1'))) ?>" 
                                   placeholder="ns1.example.com.">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Secondary NS</label>
                            <input type="text" name="ns2" class="form-control" 
                                   value="<?= e(old('ns2', config('bind.default_ns2'))) ?>" 
                                   placeholder="ns2.example.com.">
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-check me-1"></i> Create Zone
                        </button>
                        <a href="/zones" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
echo view('layouts.main', compact('title', 'content'));
