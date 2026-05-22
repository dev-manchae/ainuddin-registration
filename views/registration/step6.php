<h2>Hantar Permohonan</h2>

<div style="
    border:1px solid #ddd;
    padding:25px;
    border-radius:10px;
">

    <p>
        Sila pastikan semua maklumat adalah tepat
        sebelum menghantar permohonan.
    </p>

    <br>

    <form method="POST" action="?page=submit_permohonan">
        <?= csrfField(); ?>

        <div style="
            background:#fff8d6;
            border:1px solid #f0d96c;
            padding:15px;
            border-radius:8px;
            margin-bottom:20px;
        ">

            <strong>Perakuan:</strong><br><br>

            Saya mengesahkan bahawa semua maklumat
            yang diberikan adalah benar dan tepat.

        </div>

        <label>
            <input type="checkbox" name="perakuan" required>
            Saya bersetuju dan memahami perakuan ini.
        </label>

        <br><br>

        <button type="submit" class="btn btn-teal" style="width:100%; padding: 14px 24px; font-size: 16px;">
            Hantar Permohonan
        </button>

    </form>

</div>