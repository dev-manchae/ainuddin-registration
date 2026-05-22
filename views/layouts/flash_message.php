<?php if (isset($_SESSION['success'])): ?>

    <div style="
        background-color: #dcfce7;
        color: #166534;
        padding: 12px;
        margin-bottom: 20px;
        border-radius: 5px;
    ">

        <?= htmlspecialchars($_SESSION['success']); ?>

    </div>

    <?php unset($_SESSION['success']); ?>

<?php endif; ?>


<?php if (isset($_SESSION['error'])): ?>

    <div style="
        background-color: #fee2e2;
        color: #991b1b;
        padding: 12px;
        margin-bottom: 20px;
        border-radius: 5px;
    ">

        <?= htmlspecialchars($_SESSION['error']); ?>

    </div>

    <?php unset($_SESSION['error']); ?>

<?php endif; ?>