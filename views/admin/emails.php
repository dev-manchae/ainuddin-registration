<?php
require_once "app/helpers/EmailSimulator.php";
$emails = EmailSimulator::getEmails();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
    <div>
        <h2 style="margin-bottom: 5px;">Simulasi Penghantaran Emel</h2>
        <p style="color: #64748b; font-size: 14px;">Rekod emel sistem pendaftaran pelajar yang dihantar kepada penjaga/pemohon</p>
    </div>
</div>

<div class="card">
    <?php if (empty($emails)): ?>
        <div style="text-align: center; padding: 40px; color: #64748b;">
            <p>Tiada rekod penghantaran emel simulasi ditemui.</p>
        </div>
    <?php else: ?>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="width: 180px;">Tarikh & Masa</th>
                    <th>Penerima</th>
                    <th>Subjek</th>
                    <th>Nama Pelajar (No Rujukan)</th>
                    <th style="width: 120px; text-align: center;">Tindakan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($emails as $email): ?>
                    <tr>
                        <td style="font-size: 13px; color: #475569;">
                            <?= date('d/m/Y H:i:s', strtotime($email['tarikh_hantar'])); ?>
                        </td>
                        <td style="font-weight: 600; color: #334155;">
                            <?= htmlspecialchars($email['penerima']); ?>
                        </td>
                        <td style="color: #475569;">
                            <?= htmlspecialchars($email['subjek']); ?>
                        </td>
                        <td style="font-size: 13px; color: #64748b;">
                            <?= htmlspecialchars($email['nama_pelajar'] ?? '-'); ?> 
                            <span style="font-size: 11px; background: #f1f5f9; padding: 2px 6px; border-radius: 4px; margin-left: 4px;">
                                <?= htmlspecialchars($email['no_rujukan'] ?? 'Draf'); ?>
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <button onclick="viewEmail(<?= $email['id_emel']; ?>)" class="btn btn-teal" style="padding: 6px 12px; font-size: 12px; border-radius: 6px;">
                                Lihat Emel
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- EMAIL PREVIEW MODAL -->
<div id="emailModal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5); align-items: center; justify-content: center; backdrop-filter: blur(2px);">
    <div style="background-color: #f8fafc; border-radius: 12px; width: 90%; max-width: 650px; box-shadow: var(--shadow-lg); overflow: hidden; display: flex; flex-direction: column; height: 85vh; border: 1px solid #e2e8f0; animation: modalFadeIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);">
        <!-- Modal Header -->
        <div style="background: white; padding: 15px 20px; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
            <div style="text-align: left;">
                <h3 id="modalSubject" style="margin: 0; font-size: 16px; font-weight: 700; color: #1e293b;">Subjek Emel</h3>
                <p style="margin: 3px 0 0; font-size: 12px; color: #64748b;">Kepada: <span id="modalRecipient" style="font-weight: 600; color: #475569;">penerima@mail.com</span></p>
            </div>
            <button onclick="closeModal()" style="background: #f1f5f9; border: none; font-size: 20px; color: #64748b; font-weight: bold; cursor: pointer; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">&times;</button>
        </div>
        <!-- Modal Body (Iframe for styled encapsulation) -->
        <div style="flex: 1; padding: 0; background: #f8fafc; overflow: hidden; position: relative;">
            <iframe id="emailFrame" src="" style="width: 100%; height: 100%; border: none; background: #f8fafc; display: block;"></iframe>
        </div>
    </div>
</div>

<style>
@keyframes modalFadeIn {
    from { opacity: 0; transform: scale(0.96) translateY(10px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}
#emailModal button:hover {
    background: #e2e8f0 !important;
    color: #0f172a !important;
}
</style>

<script>
function viewEmail(id) {
    const modal = document.getElementById('emailModal');
    const iframe = document.getElementById('emailFrame');
    const subject = document.getElementById('modalSubject');
    const recipient = document.getElementById('modalRecipient');
    
    // Fetch details dynamically
    fetch('?page=admin_json_emel&id=' + id)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }
            subject.textContent = data.subjek;
            recipient.textContent = data.penerima;
            
            // Set src of iframe to the read action
            iframe.src = '?page=admin_lihat_emel&id=' + id;
            
            modal.style.display = 'flex';
        })
        .catch(err => {
            console.error(err);
            alert('Gagal mendapatkan maklumat emel.');
        });
}

function closeModal() {
    const modal = document.getElementById('emailModal');
    const iframe = document.getElementById('emailFrame');
    modal.style.display = 'none';
    iframe.src = ''; // reset src
}

// Close when clicking outside of modal content wrapper
window.onclick = function(event) {
    const modal = document.getElementById('emailModal');
    if (event.target === modal) {
        closeModal();
    }
}
</script>
