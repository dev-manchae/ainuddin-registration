<form id="deleteDocForm" method="POST" action="?page=delete_dokumen" style="display:none;">
    <?= csrfField(); ?>
    <input type="hidden" name="id_dokumen" id="deleteDocId">
</form>

<h2 style="margin-bottom:20px;">Muat Naik Dokumen</h2>
<form method="POST" action="?page=save_step5" enctype="multipart/form-data" id="stepForm">
    <?= csrfField(); ?>

    <!-- Salinan IC -->
    <div style="border:1px solid var(--border); padding:20px; border-radius:12px; margin-bottom:20px;">
        <label style="font-weight:600; display: block; margin-bottom: 8px;">Salinan IC/Sijil Lahir Pelajar <span style="color: var(--danger);">*</span></label>
        
        <!-- Senarai IC Sedia Ada -->
        <div id="uploaded_ic_container" style="margin-bottom: 20px;">
            <?php if (!empty($dokumen['IC Pelajar'])): ?>
                <span style="font-size: 13px; font-weight: 600; color: var(--text-muted); display:block; margin-bottom:10px;">Salinan IC/Sijil Lahir yang telah dimuat naik:</span>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <?php foreach ($dokumen['IC Pelajar'] as $doc): 
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
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Container Input Fail Baru -->
        <div id="ic_inputs_container">
            <div id="ic_row_0">
                <label style="font-size: 13px; color: var(--text-muted); display: block; margin-top: 10px; margin-bottom: 8px;">Muat naik salinan IC/Sijil Lahir:</label>
                <div class="upload-dropzone" id="dropzone_ic_0">
                    <div class="upload-icon">📂</div>
                    <div class="upload-text">Drag & drop fail di sini, atau <span>klik untuk pilih fail</span></div>
                    <div class="upload-note">Format: PDF, PNG, JPG (Maks 2MB)</div>
                    <input type="file" name="ic_pelajar[]" accept=".pdf,.jpg,.jpeg,.png" style="display:none;" onchange="previewMultipleIcFile(this, 0)">
                </div>
                <div class="file-preview-wrapper" style="margin-top:10px;" id="preview_ic_0"></div>
            </div>
        </div>

        <button type="button" class="btn btn-outline btn-sm" id="btn_add_ic" style="margin-top: 15px; padding: 6px 12px; font-size: 13px; border-radius: 6px; display: inline-flex; align-items: center; gap: 6px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            Tambah Salinan IC Lain
        </button>
    </div>

    <!-- Gambar Pelajar -->
    <div style="border:1px solid var(--border); padding:20px; border-radius:12px; margin-bottom:20px;">
        <label style="font-weight:600; display: block; margin-bottom: 12px;">Gambar Pelajar <span style="color: var(--danger);">*</span></label>
        
        <div class="upload-dropzone" id="dropzone_gambar_pelajar">
            <div class="upload-icon">🖼️</div>
            <div class="upload-text">Drag & drop gambar di sini, atau <span>klik untuk pilih gambar</span></div>
            <div class="upload-note">Format: PNG, JPG (Maks 2MB)</div>
            <input type="file" name="gambar_pelajar" accept=".jpg,.jpeg,.png" style="display:none;" onchange="previewFile(this)">
        </div>
        
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
        <div id="uploaded_sijil_container" style="margin-bottom: 20px;">
            <?php if (!empty($dokumen['Sijil Pelajar'])): ?>
                <span style="font-size: 13px; font-weight: 600; color: var(--text-muted); display:block; margin-bottom:10px;">Sijil yang telah dimuat naik:</span>
                <div style="display: flex; flex-direction: column; gap: 12px;">
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
        </div>

        <!-- Container Input Fail Baru -->
        <div id="sijil_inputs_container">
            <div id="sijil_row_0">
                <label style="font-size: 13px; color: var(--text-muted); display: block; margin-top: 10px; margin-bottom: 8px;">Muat naik sijil:</label>
                <div class="upload-dropzone" id="dropzone_sijil_0">
                    <div class="upload-icon">📜</div>
                    <div class="upload-text">Drag & drop sijil di sini, atau <span>klik untuk pilih fail</span></div>
                    <div class="upload-note">Format: PDF, PNG, JPG (Maks 2MB)</div>
                    <input type="file" name="sijil_pelajar[]" accept=".pdf,.jpg,.jpeg,.png" style="display:none;" onchange="previewMultipleFile(this, 0)">
                </div>
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
                <label style="font-size: 13px; color: var(--text-muted); display: block; margin-bottom: 8px; margin-top: 10px;">Muat naik sijil:</label>
                <div class="upload-dropzone" id="dropzone_sijil_${sijilIndex}">
                    <div class="upload-icon">📜</div>
                    <div class="upload-text">Drag & drop sijil di sini, atau <span>klik untuk pilih fail</span></div>
                    <div class="upload-note">Format: PDF, PNG, JPG (Maks 2MB)</div>
                    <input type="file" name="sijil_pelajar[]" accept=".pdf,.jpg,.jpeg,.png" style="display:none;" onchange="previewMultipleFile(this, sijilIndex)">
                </div>
                <div class="file-preview-wrapper" style="margin-top:10px;" id="preview_sijil_${sijilIndex}"></div>
            `;
            container.appendChild(newRow);
            sijilIndex++;
        });
    }

    // Add click event for adding more IC inputs
    const btnAddIc = document.getElementById('btn_add_ic');
    if (btnAddIc) {
        let icIndex = 1;
        btnAddIc.addEventListener('click', function() {
            const container = document.getElementById('ic_inputs_container');
            const newRow = document.createElement('div');
            newRow.id = 'ic_row_' + icIndex;
            newRow.style.borderTop = '1px dashed var(--border)';
            newRow.style.paddingTop = '15px';
            newRow.style.marginTop = '15px';
            newRow.style.position = 'relative';
            
            newRow.innerHTML = `
                <button type="button" class="btn btn-sm btn-outline-danger" style="position: absolute; right: 0; top: 15px; padding: 4px 8px; font-size: 12px;" onclick="removeIcRow(${icIndex})">Buang</button>
                <label style="font-size: 13px; color: var(--text-muted); display: block; margin-bottom: 8px; margin-top: 10px;">Muat naik salinan IC/Sijil Lahir:</label>
                <div class="upload-dropzone" id="dropzone_ic_${icIndex}">
                    <div class="upload-icon">📂</div>
                    <div class="upload-text">Drag & drop fail di sini, atau <span>klik untuk pilih fail</span></div>
                    <div class="upload-note">Format: PDF, PNG, JPG (Maks 2MB)</div>
                    <input type="file" name="ic_pelajar[]" accept=".pdf,.jpg,.jpeg,.png" style="display:none;" onchange="previewMultipleIcFile(this, icIndex)">
                </div>
                <div class="file-preview-wrapper" style="margin-top:10px;" id="preview_ic_${icIndex}"></div>
            `;
            container.appendChild(newRow);
            icIndex++;
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

function previewMultipleIcFile(input, id) {
    const wrapper = document.getElementById('preview_ic_' + id);
    if (!wrapper) return;
    
    const key = 'ic_' + id;
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

    // Client-side Validation: Allowed Formats
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
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearMultipleIcLocalSelection(${id})" style="flex-shrink: 0; padding: 6px 12px; font-size: 13px; border-radius: 6px;">Hapus</button>
        </div>
    `;
}

function removeIcRow(id) {
    const row = document.getElementById('ic_row_' + id);
    if (row) {
        const key = 'ic_' + id;
        if (activeBlobUrls[key]) {
            URL.revokeObjectURL(activeBlobUrls[key]);
            delete activeBlobUrls[key];
        }
        row.remove();
    }
}

function clearMultipleIcLocalSelection(id) {
    const row = document.getElementById('ic_row_' + id);
    if (row) {
        const input = row.querySelector('input[type="file"]');
        if (input) {
            input.value = '';
            previewMultipleIcFile(input, id);
        }
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

// Re-renders the uploaded documents lists and resets temporary inputs/rows
window.updateUploadedDocuments = function(docs) {
    // 1. Re-render IC Pelajar List
    const icContainer = document.getElementById('uploaded_ic_container');
    if (icContainer) {
        const icDocs = docs['IC Pelajar'] || [];
        if (icDocs.length > 0) {
            let html = '<span style="font-size: 13px; font-weight: 600; color: var(--text-muted); display:block; margin-bottom:10px;">Salinan IC/Sijil Lahir yang telah dimuat naik:</span>';
            html += '<div style="display: flex; flex-direction: column; gap: 12px;">';
            icDocs.forEach(function(doc) {
                const filePath = 'public/uploads/pelajar_ic/' + doc.nama_fail;
                const ext = doc.nama_asal.split('.').pop().toLowerCase();
                const isImage = ['jpg', 'jpeg', 'png'].includes(ext);
                
                html += `
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 15px; border: 1px solid var(--border); padding: 12px; border-radius: 8px; background: #fafafa;">
                        <div style="flex-grow: 1; min-width: 0;">
                            ${isImage ? `
                                <a href="${filePath}" target="_blank" class="img-preview-anchor">
                                    <img src="${filePath}" class="img-preview-direct" alt="Salinan IC/Sijil Lahir Pelajar">
                                </a>
                            ` : `
                                <a href="${filePath}" target="_blank" class="doc-preview-card" style="margin: 0;">
                                    <div class="doc-preview-icon pdf">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                    </div>
                                    <div class="doc-preview-info">
                                        <span class="doc-preview-name" title="${escapeHtml(doc.nama_asal)}">${escapeHtml(doc.nama_asal)}</span>
                                        <span class="doc-preview-action">Klik untuk buka PDF</span>
                                    </div>
                                </a>
                            `}
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDeleteDoc(${doc.id_dokumen})" style="flex-shrink: 0; padding: 6px 12px; font-size: 13px; border-radius: 6px;">Hapus</button>
                    </div>
                `;
            });
            html += '</div>';
            icContainer.innerHTML = html;
        } else {
            icContainer.innerHTML = '';
        }
    }

    // 2. Re-render Gambar Pelajar Preview
    const gambarContainer = document.getElementById('preview_gambar_pelajar');
    if (gambarContainer) {
        const gambarDocs = docs['Gambar Pelajar'] || [];
        if (gambarDocs.length > 0) {
            const doc = gambarDocs[0];
            const filePath = 'public/uploads/gambar/' + doc.nama_fail;
            const ext = doc.nama_asal.split('.').pop().toLowerCase();
            const isImage = ['jpg', 'jpeg', 'png'].includes(ext);
            
            gambarContainer.innerHTML = `
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 15px; border: 1px solid var(--border); padding: 12px; border-radius: 8px; background: #fafafa;">
                    <div style="flex-grow: 1; min-width: 0;">
                        ${isImage ? `
                            <a href="${filePath}" target="_blank" class="img-preview-anchor">
                                <img src="${filePath}" class="img-preview-direct" alt="Gambar Pelajar">
                            </a>
                        ` : `
                            <a href="${filePath}" target="_blank" class="doc-preview-card" style="margin: 0;">
                                <div class="doc-preview-icon pdf">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                </div>
                                <div class="doc-preview-info">
                                    <span class="doc-preview-name" title="${escapeHtml(doc.nama_asal)}">${escapeHtml(doc.nama_asal)}</span>
                                    <span class="doc-preview-action">Klik untuk buka PDF</span>
                                </div>
                            </a>
                        `}
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDeleteDoc(${doc.id_dokumen})" style="flex-shrink: 0; padding: 6px 12px; font-size: 13px; border-radius: 6px;">Hapus</button>
                </div>
            `;
            gambarContainer.dataset.originalHtml = gambarContainer.innerHTML;
        } else {
            gambarContainer.innerHTML = '';
            gambarContainer.dataset.originalHtml = '';
        }
    }

    // 3. Re-render Sijil Pelajar List
    const sijilContainer = document.getElementById('uploaded_sijil_container');
    if (sijilContainer) {
        const sijilDocs = docs['Sijil Pelajar'] || [];
        if (sijilDocs.length > 0) {
            let html = '<span style="font-size: 13px; font-weight: 600; color: var(--text-muted); display:block; margin-bottom:10px;">Sijil yang telah dimuat naik:</span>';
            html += '<div style="display: flex; flex-direction: column; gap: 12px;">';
            sijilDocs.forEach(function(doc) {
                const filePath = 'public/uploads/sijil/' + doc.nama_fail;
                const ext = doc.nama_asal.split('.').pop().toLowerCase();
                const isImage = ['jpg', 'jpeg', 'png'].includes(ext);
                
                html += `
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 15px; border: 1px solid var(--border); padding: 12px; border-radius: 8px; background: #fafafa;">
                        <div style="flex-grow: 1; min-width: 0;">
                            ${isImage ? `
                                <a href="${filePath}" target="_blank" class="img-preview-anchor">
                                    <img src="${filePath}" class="img-preview-direct" alt="Sijil Akademik / Hafazan">
                                </a>
                            ` : `
                                <a href="${filePath}" target="_blank" class="doc-preview-card" style="margin: 0;">
                                    <div class="doc-preview-icon pdf">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                    </div>
                                    <div class="doc-preview-info">
                                        <span class="doc-preview-name" title="${escapeHtml(doc.nama_asal)}">${escapeHtml(doc.nama_asal)}</span>
                                        <span class="doc-preview-action">Klik untuk buka PDF</span>
                                    </div>
                                </a>
                            `}
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmDeleteDoc(${doc.id_dokumen})" style="flex-shrink: 0; padding: 6px 12px; font-size: 13px; border-radius: 6px;">Hapus</button>
                    </div>
                `;
            });
            html += '</div>';
            sijilContainer.innerHTML = html;
        } else {
            sijilContainer.innerHTML = '';
        }
    }

    // 4. Clear all inputs and dynamic rows
    document.querySelectorAll('input[type="file"]').forEach(function(input) {
        input.value = '';
    });
    document.querySelectorAll('[id^="ic_row_"]').forEach(function(row) {
        if (row.id !== 'ic_row_0') {
            row.remove();
        }
    });
    document.querySelectorAll('[id^="sijil_row_"]').forEach(function(row) {
        if (row.id !== 'sijil_row_0') {
            row.remove();
        }
    });
    const previewIc0 = document.getElementById('preview_ic_0');
    if (previewIc0) previewIc0.innerHTML = '';
    const previewSijil0 = document.getElementById('preview_sijil_0');
    if (previewSijil0) previewSijil0.innerHTML = '';
};

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