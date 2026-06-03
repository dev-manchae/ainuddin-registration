</div> <!-- close main-container -->

<footer class="main-footer">
    <div class="footer-content">
        <div class="footer-brand">Tahfiz Ainuddin</div>
        <p class="footer-text">
            Membentuk generasi Al-Quran yang berkualiti dan berakhlak mulia.
        </p>
        <p>
            <a href="https://www.ainuddingroupofficial.com/" target="_blank" class="footer-link">ainuddingroupofficial.com</a>
        </p>
        <p class="footer-copy">&copy; <?= date('Y'); ?> Ainuddin Group. All rights reserved.</p>
    </div>
</footer>

<!-- Session Timeout Modal -->
<?php if (isset($_SESSION['id_pengguna'])): ?>
<div id="session-timeout-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 9999; justify-content: center; align-items: center; padding: 20px;">
    <div style="background: white; padding: 30px; border-radius: 16px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04); max-width: 450px; width: 100%; text-align: center; border: 1px solid #e2e8f0; font-family: inherit;">
        <div style="font-size: 40px; margin-bottom: 15px;">⏳</div>
        <h3 style="margin: 0 0 10px 0; font-size: 18px; font-weight: 700; color: #1e293b;">Sesi Anda Hampir Tamat</h3>
        <p style="margin: 0 0 24px 0; font-size: 14px; line-height: 1.5; color: #64748b;">
            Oleh kerana tiada aktiviti dikesan, sesi anda akan tamat secara automatik dalam masa <strong id="session-countdown" style="color: #ef4444; font-size: 16px;">3:00</strong>. Sila klik butang di bawah untuk kekal log masuk.
        </p>
        <div style="display: flex; gap: 12px; justify-content: center;">
            <button id="session-keep-btn" class="btn btn-teal" style="padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; border: none; height: 42px;">Kekalkan Sesi</button>
            <button id="session-logout-btn" class="btn btn-secondary" style="padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; border: none; background: #f1f5f9; color: #475569; height: 42px;">Log Keluar</button>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- External Scripts -->
<script src="public/assets/js/main.js?v=<?= time(); ?>"></script>

</body>
</html>