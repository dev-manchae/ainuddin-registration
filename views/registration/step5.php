<form id="deleteDocForm" method="POST" action="?page=delete_dokumen" style="display:none;">
    <?= csrfField(); ?>
    <input type="hidden" name="id_dokumen" id="deleteDocId">
</form>

<h2 style="margin-bottom:20px;">Muat Naik Dokumen</h2>
<form method="POST" action="?page=save_step5" enctype="multipart/form-data" id="stepForm">
    <?= csrfField(); ?>

    <!-- Salinan IC -->
    <div style="border:1px solid var(--border); padding:20px; border-radius:12px; margin-bottom:20px;">
        <label style="font-weight:600;">Salinan IC/Sijil Lahir Pelajar <span style="color: var(--danger);">*</span></label>
        <input type="file" name="ic_pelajar" accept=".pdf,.jpg,.jpeg,.png" style="margin-top:10px; display:block;" onchange="previewFile(this)">
        
        <div class="file-preview-wrapper" style="margin-top:12px;" id="preview_ic_pelajar">
            <?php if (!empty($dokumen['IC Pelajar']) && !empty($dokumen['IC Pelajar'][0])): 
                $doc = $dokumen['IC Pelajar'][0];
                $filePath = 'public/uploads/pelajar_ic/' . $doc['nama_fail'];
                $ext = strtolower(pathinfo($doc['nama_asal'], PATHINFO_EXTENSION));
                $isImage = in_array($ext, ['jpg', 'jpeg', 'png']);
            ?>
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 15px; border: 1px solid var(--border); padding: 12px; border-radius: 8px; background: #fafafa;">
                    <div style="flex-grow: 1; min-width: 0;">
                        <?php if ($isImage): ?>
                            <a href="<?= $filePath; ?>" target="_blank" class="img-preview-anchor">
                                <img src="<?= $filePath; ?>" class="img-preview-direct" alt="Salinan IC/Sijil Lahir Pelajar">
                            </a>
                        <?php else: ?>
                            <a href="<?= $filePath; ?>" target="_blank" class="doc-preview-card" style="margin: 0;">
                                <div class="doc-preview-icon pdf">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                </div>
                                <div class="doc-preview-info">
                                    <span class="doc-preview-name" title="<?= htmlspecialchars($doc['nama_asal']); ?>"><?= htmlspecialchars($doc['nama_asal']); ?></span>
                                    <span class="doc-preview-action">Klik untuk buka PDF</span>
                                </div>
                            </a>
                        <?php endif; ?>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDeleteDoc(<?= $doc['id_dokumen']; ?>)" style="flex-shrink: 0; padding: 6px 12px; font-size: 13px; border-radius: 6px;">Hapus</button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Gambar Pelajar -->
    <div style="border:1px solid var(--border); padding:20px; border-radius:12px; margin-bottom:20px;">
        <label style="font-weight:600;">Gambar Pelajar <span style="color: var(--danger);">*</span></label>
        <input type="file" name="gambar_pelajar" accept=".jpg,.jpeg,.png" style="margin-top:10px; display:block;" onchange="previewFile(this)">
        
        <div class="file-preview-wrapper" style="margin-top:12px;" id="preview_gambar_pelajar">
            <?php if (!empty($dokumen['Gambar Pelajar']) && !empty($dokumen['Gambar Pelajar'][0])): 
                $doc = $dokumen['Gambar Pelajar'][0];
                $filePath = 'public/uploads/gambar/' . $doc['nama_fail'];
                $ext = strtolower(pathinfo($doc['nama_asal'], PATHINFO_EXTENSION));
                $isImage = in_array($ext, ['jpg', 'jpeg', 'png']);
            ?>
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 15px; border: 1px solid var(--border); padding: 12px; border-radius: 8px; background: #fafafa;">
                    <div style="flex-grow: 1; min-width: 0;">
                        <?php if ($isImage): ?>
                            <a href="<?= $filePath; ?>" target="_blank" class="img-preview-anchor">
                                <img src="<?= $filePath; ?>" class="img-preview-direct" alt="Gambar Pelajar">
                            </a>
                        <?php else: ?>
                            <a href="<?= $filePath; ?>" target="_blank" class="doc-preview-card" style="margin: 0;">
                                <div class="doc-preview-icon pdf">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                </div>
                                <div class="doc-preview-info">
                                    <span class="doc-preview-name" title="<?= htmlspecialchars($doc['nama_asal']); ?>"><?= htmlspecialchars($doc['nama_asal']); ?></span>
                                    <span class="doc-preview-action">Klik untuk buka PDF</span>
                                </div>
                            </a>
                        <?php endif; ?>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDeleteDoc(<?= $doc['id_dokumen']; ?>)" style="flex-shrink: 0; padding: 6px 12px; font-size: 13px; border-radius: 6px;">Hapus</button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Sijil Pelajar -->
    <div style="border:1px solid var(--border); padding:20px; border-radius:12px; margin-bottom:20px;">
        <label style="font-weight:600; display: block; margin-bottom: 8px;">Sijil Akademik / Hafazan <span style="color: var(--danger);">*</span></label>
        
        <!-- Senarai Sijil Sedia Ada -->
        <?php if (!empty($dokumen['Sijil Pelajar'])): ?>
            <div style="margin-bottom: 20px; display: flex; flex-direction: column; gap: 12px;">
                <span style="font-size: 13px; font-weight: 600; color: var(--text-muted);">Sijil yang telah dimuat naik:</span>
                <?php foreach ($dokumen['Sijil Pelajar'] as $doc): 
                    $filePath = 'public/uploads/sijil/' . $doc['nama_fail'];
                    $ext = strtolower(pathinfo($doc['nama_asal'], PATHINFO_EXTENSION));
                    $isImage = in_array($ext, ['jpg', 'jpeg', 'png']);
                ?>
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 15px; border: 1px solid var(--border); padding: 12px; border-radius: 8px; background: #fafafa;">
                        <div style="flex-grow: 1; min-width: 0;">
                            <?php if ($isImage): ?>
                                <a href="<?= $filePath; ?>" target="_blank" class="img-preview-anchor">
                                    <img src="<?= $filePath; ?>" class="img-preview-direct" alt="Sijil Akademik / Hafazan">
                                </a>
                            <?php else: ?>
                                <a href="<?= $filePath; ?>" target="_blank" class="doc-preview-card" style="margin: 0;">
                                    <div class="doc-preview-icon pdf">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                    </div>
                                    <div class="doc-preview-info">
                                        <span class="doc-preview-name" title="<?= htmlspecialchars($doc['nama_asal']); ?>"><?= htmlspecialchars($doc['nama_asal']); ?></span>
                                        <span class="doc-preview-action">Klik untuk buka PDF</span>
                                    </div>
                                </a>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDeleteDoc(<?= $doc['id_dokumen']; ?>)" style="flex-shrink: 0; padding: 6px 12px; font-size: 13px; border-radius: 6px;">Hapus</button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Container Input Fail Baru -->
        <div id="sijil_inputs_container">
            <div id="sijil_row_0">
                <label style="font-size: 13px; color: var(--text-muted); display: block; margin-top: 10px;">Muat naik sijil:</label>
                <input type="file" name="sijil_pelajar[]" accept=".pdf,.jpg,.jpeg,.png" style="margin-top:5px; display:block;" onchange="previewMultipleFile(this, 0)">
                <div class="file-preview-wrapper" style="margin-top:10px;" id="preview_sijil_0"></div>
            </div>
        </div>

        <button type="button" class="btn btn-outline btn-sm" id="btn_add_sijil" style="margin-top: 15px; padding: 6px 12px; font-size: 13px; border-radius: 6px; display: inline-flex; align-items: center; gap: 6px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            Tambah Sijil Lain
        </button>
    </div>

    <div style="display:none;"><button type="submit">Simpan</button></div>
</form>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Capture original HTML structure for fallback restoration
    document.querySelectorAll('input[type="file"]').forEach(function(input) {
        const wrapper = document.getElementById('preview_' + input.name);
        if (wrapper) {
            wrapper.dataset.originalHtml = wrapper.innerHTML;
        }
    });

    // Add click event for adding more sijil inputs
    const btnAddSijil = document.getElementById('btn_add_sijil');
    if (btnAddSijil) {
        let sijilIndex = 1;
        btnAddSijil.addEventListener('click', function() {
            const container = document.getElementById('sijil_inputs_container');
            const newRow = document.createElement('div');
            newRow.id = 'sijil_row_' + sijilIndex;
            newRow.style.borderTop = '1px dashed var(--border)';
            newRow.style.paddingTop = '15px';
            newRow.style.marginTop = '15px';
            newRow.style.position = 'relative';
            
            newRow.innerHTML = `
                <button type="button" class="btn btn-sm btn-outline-danger" style="position: absolute; right: 0; top: 15px; padding: 4px 8px; font-size: 12px;" onclick="removeSijilRow(${sijilIndex})">Buang</button>
                <label style="font-size: 13px; color: var(--text-muted); display: block;">Muat naik sijil:</label>
                <input type="file" name="sijil_pelajar[]" accept=".pdf,.jpg,.jpeg,.png" style="margin-top:5px; display:block;" onchange="previewMultipleFile(this, ${sijilIndex})">
                <div class="file-preview-wrapper" style="margin-top:10px;" id="preview_sijil_${sijilIndex}"></div>
            `;
            container.appendChild(newRow);
            sijilIndex++;
        });
    }
});

// Keep track of active blob URLs to revoke them and prevent memory leaks
const activeBlobUrls = {};

function previewFile(input) {
    const wrapper = document.getElementById('preview_' + input.name);
    if (!wrapper) return;
    
    // Revoke previous blob URL if any exists
    if (activeBlobUrls[input.name]) {
        URL.revokeObjectURL(activeBlobUrls[input.name]);
        delete activeBlobUrls[input.name];
    }
    
    const file = input.files[0];
    if (!file) {
        // Restore original server-side HTML if selection is cleared
        wrapper.innerHTML = wrapper.dataset.originalHtml || '';
        return;
    }

    // Client-side Validation: Size (Max 2MB)
    const maxSize = 2 * 1024 * 1024; // 2MB
    if (file.size > maxSize) {
        alert("Saiz fail '" + file.name + "' melebihi had 2MB. Sila muat naik fail yang lebih kecil.");
        input.value = '';
        wrapper.innerHTML = wrapper.dataset.originalHtml || '';
        return;
    }

    // Client-side Validation: Allowed Formats (from input accept attribute)
    const acceptAttr = input.getAttribute('accept');
    if (acceptAttr) {
        const allowedExtensions = acceptAttr.split(',').map(ext => ext.trim().toLowerCase().replace('.', ''));
        const fileExt = file.name.split('.').pop().toLowerCase();
        if (!allowedExtensions.includes(fileExt)) {
            alert("Format fail '" + file.name + "' tidak sah. Sila pilih fail berformat: " + acceptAttr);
            input.value = '';
            wrapper.innerHTML = wrapper.dataset.originalHtml || '';
            return;
        }
    }
    
    const fileType = file.type;
    const fileName = file.name;
    const objectUrl = URL.createObjectURL(file);
    activeBlobUrls[input.name] = objectUrl;
    
    let previewHtml = '';
    if (fileType.startsWith('image/')) {
        previewHtml = `
            <a href="${objectUrl}" target="_blank" class="img-preview-anchor" style="margin: 0;">
                <img src="${objectUrl}" class="img-preview-direct" alt="${escapeHtml(fileName)}">
            </a>
        `;
    } else if (fileType === 'application/pdf') {
        previewHtml = `
            <a href="${objectUrl}" target="_blank" class="doc-preview-card" style="margin: 0;">
                <div class="doc-preview-icon pdf">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="doc-preview-info">
                    <span class="doc-preview-name" title="${escapeHtml(fileName)}">${escapeHtml(fileName)}</span>
                    <span class="doc-preview-action">Klik untuk buka PDF (Pra-lihat)</span>
                </div>
            </a>
        `;
    } else {
        wrapper.innerHTML = `<span style="color:var(--danger); font-size: 13px;">Format fail tidak disokong untuk pra-lihat.</span>`;
        return;
    }

    wrapper.innerHTML = `
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 15px; border: 1px solid var(--border); padding: 12px; border-radius: 8px; background: #fafafa;">
            <div style="flex-grow: 1; min-width: 0;">
                ${previewHtml}
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearLocalSelection('${input.name}')" style="flex-shrink: 0; padding: 6px 12px; font-size: 13px; border-radius: 6px;">Hapus</button>
        </div>
    `;
}

function previewMultipleFile(input, id) {
    const wrapper = document.getElementById('preview_sijil_' + id);
    if (!wrapper) return;
    
    const key = 'sijil_' + id;
    if (activeBlobUrls[key]) {
        URL.revokeObjectURL(activeBlobUrls[key]);
        delete activeBlobUrls[key];
    }
    
    const file = input.files[0];
    if (!file) {
        wrapper.innerHTML = '';
        return;
    }

    // Client-side Validation: Size (Max 2MB)
    const maxSize = 2 * 1024 * 1024; // 2MB
    if (file.size > maxSize) {
        alert("Saiz fail '" + file.name + "' melebihi had 2MB. Sila muat naik fail yang lebih kecil.");
        input.value = '';
        wrapper.innerHTML = '';
        return;
    }

    // Client-side Validation: Allowed Formats (from input accept attribute)
    const acceptAttr = input.getAttribute('accept');
    if (acceptAttr) {
        const allowedExtensions = acceptAttr.split(',').map(ext => ext.trim().toLowerCase().replace('.', ''));
        const fileExt = file.name.split('.').pop().toLowerCase();
        if (!allowedExtensions.includes(fileExt)) {
            alert("Format fail '" + file.name + "' tidak sah. Sila pilih fail berformat: " + acceptAttr);
            input.value = '';
            wrapper.innerHTML = '';
            return;
        }
    }
    
    const fileType = file.type;
    const fileName = file.name;
    const objectUrl = URL.createObjectURL(file);
    activeBlobUrls[key] = objectUrl;
    
    let previewHtml = '';
    if (fileType.startsWith('image/')) {
        previewHtml = `
            <a href="${objectUrl}" target="_blank" class="img-preview-anchor" style="margin: 0;">
                <img src="${objectUrl}" class="img-preview-direct" alt="${escapeHtml(fileName)}">
            </a>
        `;
    } else if (fileType === 'application/pdf') {
        previewHtml = `
            <a href="${objectUrl}" target="_blank" class="doc-preview-card" style="margin: 0;">
                <div class="doc-preview-icon pdf">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="doc-preview-info">
                    <span class="doc-preview-name" title="${escapeHtml(fileName)}">${escapeHtml(fileName)}</span>
                    <span class="doc-preview-action">Klik untuk buka PDF (Pra-lihat)</span>
                </div>
            </a>
        `;
    } else {
        wrapper.innerHTML = `<span style="color:var(--danger); font-size: 13px;">Format fail tidak disokong untuk pra-lihat.</span>`;
        return;
    }

    wrapper.innerHTML = `
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 15px; border: 1px solid var(--border); padding: 12px; border-radius: 8px; background: #fafafa;">
            <div style="flex-grow: 1; min-width: 0;">
                ${previewHtml}
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearMultipleLocalSelection(${id})" style="flex-shrink: 0; padding: 6px 12px; font-size: 13px; border-radius: 6px;">Hapus</button>
        </div>
    `;
}

function removeSijilRow(id) {
    const row = document.getElementById('sijil_row_' + id);
    if (row) {
        const key = 'sijil_' + id;
        if (activeBlobUrls[key]) {
            URL.revokeObjectURL(activeBlobUrls[key]);
            delete activeBlobUrls[key];
        }
        row.remove();
    }
}

function clearLocalSelection(inputName) {
    const input = document.querySelector(`input[name="${inputName}"]`);
    if (input) {
        input.value = '';
        previewFile(input);
    }
}

function clearMultipleLocalSelection(id) {
    const row = document.getElementById('sijil_row_' + id);
    if (row) {
        const input = row.querySelector('input[type="file"]');
        if (input) {
            input.value = '';
            previewMultipleFile(input, id);
        }
    }
}

function confirmDeleteDoc(id_dokumen) {
    if (confirm("Adakah anda pasti mahu memadam dokumen ini secara kekal?")) {
        document.getElementById('deleteDocId').value = id_dokumen;
        document.getElementById('deleteDocForm').submit();
    }
}

function escapeHtml(string) {
    return String(string).replace(/[&<>"']/g, function (s) {
        return {
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': "&quot;",
            "'": "&#039;"
        }[s];
    });
}
</script>