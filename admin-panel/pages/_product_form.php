<?php
/**
 * Ortak ürün ekle/düzenle form parçası. product_add.php ve product_edit.php tarafından include edilir.
 * Beklenen değişkenler: $mode ('add'|'edit'), $product (edit modunda dolu dizi, add modunda null), $types.
 */

$isEdit = ($mode === 'edit');
$p = $product ?? [];

$existingSeperate = !empty($p['applySeperate']);
$existingRows = [];
if ($isEdit) {
    $rawTypes  = array_map('trim', explode(',', (string)($p['applyType'] ?? '')));
    $rawValues = array_map('trim', explode(',', (string)($p['apply'] ?? '')));
    foreach ($rawTypes as $i => $t) {
        $val = $rawValues[$i] ?? '';
        if ($t === '' && $val === '') {
            continue;
        }
        $existingRows[] = [
            'type'      => $t,
            'value'     => $existingSeperate ? '' : $val,
            'subValues' => $existingSeperate ? array_values(array_filter(array_map('trim', explode('-', $val)), fn($v) => $v !== '')) : [],
        ];
    }
}
?>
<form method="POST" enctype="multipart/form-data" id="productForm">
    <?php csrf_field(); ?>

    <!-- Temel Bilgiler -->
    <div class="card mb-4">
        <div class="card-header bg-light"><strong>Temel Bilgiler</strong></div>
        <div class="card-body row g-3">
            <div class="col-md-6">
                <label class="form-label">Ürün Adı <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($p['name'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Kategori <span class="text-danger">*</span></label>
                <select name="type" class="form-select" required>
                    <option value="">Seçiniz</option>
                    <?php foreach ($types as $t): ?>
                        <option value="<?= htmlspecialchars($t['name']) ?>" <?= (($p['type'] ?? null) === $t['name']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($t['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Ambalaj</label>
                <input type="text" name="pack" class="form-control" value="<?= htmlspecialchars($p['pack'] ?? '') ?>">
            </div>
        </div>
    </div>

    <!-- Açıklama & İçerik -->
    <div class="card mb-4">
        <div class="card-header bg-light"><strong>Açıklama &amp; İçerik</strong></div>
        <div class="card-body row g-3">
            <div class="col-12">
                <label class="form-label">Açıklama</label>
                <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($p['description'] ?? '') ?></textarea>
            </div>
            <div class="col-12">
                <label class="form-label">İçerikler <small class="text-muted">(virgülle ayırın)</small></label>
                <textarea name="contents" class="form-control" rows="3"><?= htmlspecialchars($p['contents'] ?? '') ?></textarea>
            </div>
        </div>
    </div>

    <!-- Uygulama / Doz Bilgisi -->
    <div class="card mb-4">
        <div class="card-header bg-light"><strong>Uygulama / Doz Bilgisi</strong></div>
        <div class="card-body">
            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" name="applySeperate" id="applySeperateToggle" value="1" <?= $existingSeperate ? 'checked' : '' ?>>
                <label class="form-check-label" for="applySeperateToggle">
                    <strong>Ayrı Uygulama Var mı?</strong>
                    <small class="text-muted d-block">Açıkken, her uygulama türü için (örn. farklı bitkiler/dönemler için) birden fazla doz girebilirsiniz.</small>
                </label>
            </div>

            <div class="alert alert-light border small mb-3" id="dosePreviewBox">Önizleme: henüz bir uygulama türü eklenmedi.</div>

            <div id="doseRows"></div>
            <button type="button" class="btn btn-outline-success btn-sm mb-3" id="addDoseRow">+ Uygulama Türü Ekle</button>

            <hr>

            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" id="rawModeToggle">
                <label class="form-check-label" for="rawModeToggle">Gelişmiş mod (ham metin olarak düzenle)</label>
            </div>
            <div id="rawModeWrap" style="display:none;">
                <div class="mb-2">
                    <label class="form-label small">Uygulama Türü (virgülle ayır)</label>
                    <input type="text" class="form-control form-control-sm" id="applyTypeRaw" placeholder="Yapraktan, Damla sulama ile">
                </div>
                <div class="mb-2">
                    <label class="form-label small">Uygulama (virgülle ayır; ayrı modda &quot;-&quot; ile alt dozlar)</label>
                    <textarea class="form-control form-control-sm" id="applyRaw" rows="2"></textarea>
                </div>
            </div>

            <input type="hidden" name="applyType" id="applyTypeHidden">
            <input type="hidden" name="apply" id="applyHidden">
        </div>
    </div>

    <!-- Görsel -->
    <div class="card mb-4">
        <div class="card-header bg-light"><strong>Görsel</strong></div>
        <div class="card-body">
            <?php if ($isEdit && !empty($p['imgPath'])): ?>
                <div class="mb-2">
                    <img src="../../<?= htmlspecialchars($p['imgPath']) ?>" alt="Mevcut Görsel" style="max-height:120px; border-radius:6px; border:1px solid #dee2e6;">
                </div>
            <?php endif; ?>
            <input type="file" name="imgPath" class="form-control" accept="image/*" onchange="previewImage(this, 'imgPreview')">
            <div id="imgPreview" class="mt-2" style="display:none;">
                <img src="" alt="Önizleme" style="max-height:150px; border-radius:8px; border:1px solid #dee2e6;">
            </div>
            <small class="text-muted">JPG, PNG, GIF, WebP. Maks. 5MB.</small>
        </div>
    </div>

    <div class="mb-4">
        <button type="submit" class="btn btn-success"><i class="bi bi-save me-1"></i> Kaydet</button>
        <a href="products.php" class="btn btn-outline-secondary">Geri Dön</a>
    </div>
</form>

<script>
function previewImage(input, previewId) {
    const wrap = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            wrap.style.display = 'block';
            wrap.querySelector('img').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

(function() {
    const initialRows = <?= json_encode($existingRows, JSON_UNESCAPED_UNICODE) ?>;
    const initialSeperate = <?= $existingSeperate ? 'true' : 'false' ?>;

    const doseRows       = document.getElementById('doseRows');
    const addDoseRowBtn  = document.getElementById('addDoseRow');
    const seperateToggle = document.getElementById('applySeperateToggle');
    const previewBox     = document.getElementById('dosePreviewBox');
    const rawModeToggle  = document.getElementById('rawModeToggle');
    const rawModeWrap    = document.getElementById('rawModeWrap');
    const applyTypeRaw   = document.getElementById('applyTypeRaw');
    const applyRaw       = document.getElementById('applyRaw');
    const applyTypeHidden = document.getElementById('applyTypeHidden');
    const applyHidden     = document.getElementById('applyHidden');
    const form            = document.getElementById('productForm');

    function makeSubItem(value) {
        const wrap = document.createElement('div');
        wrap.className = 'input-group input-group-sm mb-1 dose-sub-item';
        wrap.innerHTML = `
            <input type="text" class="form-control dose-sub-input" placeholder="örn: Bağ 25 cc / 100 lt su" value="${escapeAttr(value || '')}">
            <button type="button" class="btn btn-outline-danger remove-dose-sub">&times;</button>
        `;
        wrap.querySelector('.remove-dose-sub').addEventListener('click', () => {
            wrap.remove();
            updateAll();
        });
        return wrap;
    }

    function makeRow(row) {
        row = row || {type: '', value: '', subValues: []};
        const el = document.createElement('div');
        el.className = 'dose-row border rounded p-3 mb-2';
        el.innerHTML = `
            <div class="d-flex gap-2 align-items-start">
                <div class="flex-grow-1">
                    <label class="form-label small fw-semibold mb-1">Uygulama Türü</label>
                    <input type="text" class="form-control form-control-sm dose-type-input" placeholder="örn: Yapraktan" value="${escapeAttr(row.type || '')}">
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger remove-dose-row mt-4"><i class="bi bi-trash"></i></button>
            </div>
            <div class="dose-single-wrap mt-2">
                <label class="form-label small fw-semibold mb-1">Doz</label>
                <input type="text" class="form-control form-control-sm dose-single-input" placeholder="örn: 100lt suya 30-50gr" value="${escapeAttr(row.value || '')}">
            </div>
            <div class="dose-multi-wrap mt-2">
                <label class="form-label small fw-semibold mb-1">Dozlar (bitki/uygulama bazında)</label>
                <div class="dose-sub-list"></div>
                <button type="button" class="btn btn-sm btn-outline-success add-dose-sub mt-1">+ Doz Ekle</button>
            </div>
        `;
        const subList = el.querySelector('.dose-sub-list');
        (row.subValues && row.subValues.length ? row.subValues : ['']).forEach(v => subList.appendChild(makeSubItem(v)));

        el.querySelector('.remove-dose-row').addEventListener('click', () => {
            el.remove();
            updateAll();
        });
        el.querySelector('.add-dose-sub').addEventListener('click', () => {
            subList.appendChild(makeSubItem(''));
            updateAll();
        });
        el.addEventListener('input', updateAll);
        return el;
    }

    function escapeAttr(str) {
        return String(str).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;');
    }

    function applyModeVisibility() {
        const seperate = seperateToggle.checked;
        doseRows.querySelectorAll('.dose-row').forEach(row => {
            row.querySelector('.dose-single-wrap').style.display = seperate ? 'none' : '';
            row.querySelector('.dose-multi-wrap').style.display   = seperate ? '' : 'none';
        });
    }

    function serialize() {
        const seperate = seperateToggle.checked;
        const types = [];
        const values = [];
        doseRows.querySelectorAll('.dose-row').forEach(row => {
            const type = row.querySelector('.dose-type-input').value.trim();
            let value;
            if (seperate) {
                const subs = [...row.querySelectorAll('.dose-sub-input')].map(i => i.value.trim()).filter(v => v !== '');
                value = subs.join(' - ');
            } else {
                value = row.querySelector('.dose-single-input').value.trim();
            }
            if (type === '' && value === '') return;
            types.push(type);
            values.push(value);
        });
        return { applyType: types.join(', '), apply: values.join(', ') };
    }

    function updatePreview() {
        const { applyType, apply } = serialize();
        if (!applyType) {
            previewBox.textContent = 'Önizleme: henüz bir uygulama türü eklenmedi.';
            return;
        }
        const types = applyType.split(',').map(s => s.trim());
        const values = apply.split(',').map(s => s.trim());
        const parts = types.map((t, i) => `${t}: ${values[i] || ''}`);
        previewBox.textContent = 'Önizleme: ' + parts.join(' | ');
    }

    function updateAll() {
        applyModeVisibility();
        updatePreview();
    }

    seperateToggle.addEventListener('change', updateAll);

    addDoseRowBtn.addEventListener('click', () => {
        doseRows.appendChild(makeRow(null));
        applyModeVisibility();
        updatePreview();
    });

    rawModeToggle.addEventListener('change', () => {
        if (rawModeToggle.checked) {
            const { applyType, apply } = serialize();
            applyTypeRaw.value = applyType;
            applyRaw.value = apply;
            rawModeWrap.style.display = '';
            doseRows.style.display = 'none';
            addDoseRowBtn.style.display = 'none';
        } else {
            rawModeWrap.style.display = 'none';
            doseRows.style.display = '';
            addDoseRowBtn.style.display = '';
        }
    });

    form.addEventListener('submit', () => {
        if (rawModeToggle.checked) {
            applyTypeHidden.value = applyTypeRaw.value.trim();
            applyHidden.value = applyRaw.value.trim();
        } else {
            const { applyType, apply } = serialize();
            applyTypeHidden.value = applyType;
            applyHidden.value = apply;
        }
    });

    // İlk yükleme
    if (initialRows.length) {
        initialRows.forEach(r => doseRows.appendChild(makeRow(r)));
    } else {
        doseRows.appendChild(makeRow(null));
    }
    seperateToggle.checked = initialSeperate;
    applyModeVisibility();
    updatePreview();
})();
</script>
