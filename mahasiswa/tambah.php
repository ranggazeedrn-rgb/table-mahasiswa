<?php
require 'functions.php';

$errors  = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Ambil & bersihkan input
    $nrp     = trim($_POST['nrp']     ?? '');
    $nama    = trim($_POST['nama']    ?? '');
    $email   = trim($_POST['email']   ?? '');
    $jurusan = trim($_POST['jurusan'] ?? '');

    // Validasi
    if ($nrp     === '') $errors[] = 'NRP wajib diisi.';
    if ($nama    === '') $errors[] = 'Nama wajib diisi.';
    if ($email   === '') $errors[] = 'Email wajib diisi.';
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Format email tidak valid.';
    if ($jurusan === '') $errors[] = 'Jurusan wajib diisi.';

    // Cek NRP duplikat
    if ($nrp !== '') {
        global $conn;
        $nrp_safe = mysqli_real_escape_string($conn, $nrp);
        $cek = query("SELECT nrp FROM mahasiswa WHERE nrp = '$nrp_safe'");
        if (!empty($cek)) $errors[] = 'NRP sudah terdaftar.';
    }

    // Upload gambar
    $nama_file = 'default.png';
    if (!empty($_FILES['gambar']['name'])) {
        $ext_allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $ext_allowed)) {
            $errors[] = 'Format gambar tidak didukung (jpg, jpeg, png, webp, gif).';
        } elseif ($_FILES['gambar']['size'] > 2 * 1024 * 1024) {
            $errors[] = 'Ukuran gambar maksimal 2 MB.';
        } else {
            $nama_file = uniqid('mhs_') . '.' . $ext;
            if (!is_dir('img')) mkdir('img', 0755, true);
            move_uploaded_file($_FILES['gambar']['tmp_name'], 'img/' . $nama_file);
        }
    }

    // Simpan ke database jika tidak ada error
    if (empty($errors)) {
        global $conn;
        $nrp_s     = mysqli_real_escape_string($conn, $nrp);
        $nama_s    = mysqli_real_escape_string($conn, $nama);
        $email_s   = mysqli_real_escape_string($conn, $email);
        $jurusan_s = mysqli_real_escape_string($conn, $jurusan);
        $gambar_s  = mysqli_real_escape_string($conn, $nama_file);

        $sql = "INSERT INTO mahasiswa (nrp, nama, email, jurusan, gambar)
                VALUES ('$nrp_s', '$nama_s', '$email_s', '$jurusan_s', '$gambar_s')";

        if (mysqli_query($conn, $sql)) {
            $success = true;
        } else {
            $errors[] = 'Gagal menyimpan data: ' . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Mahasiswa — Panel Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:      #f0f4ff;
            --card:    #ffffff;
            --border:  #dde3f5;
            --accent:  #4f46e5;
            --accent2: #0ea5e9;
            --danger:  #ef4444;
            --success: #10b981;
            --text:    #1e293b;
            --muted:   #64748b;
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ── TOP BANNER ── */
        .top-banner {
            background: linear-gradient(120deg, #4f46e5 0%, #0ea5e9 60%, #06b6d4 100%);
            height: 200px;
            position: relative;
            overflow: hidden;
        }
        .top-banner::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 50% 80% at 80% 50%, rgba(255,255,255,.18) 0%, transparent 60%),
                radial-gradient(ellipse 30% 60% at 10% 80%, rgba(245,158,11,.25) 0%, transparent 55%);
        }
        .top-banner .circle1 {
            position: absolute;
            width: 220px; height: 220px;
            border-radius: 50%;
            border: 40px solid rgba(255,255,255,.1);
            top: -60px; right: 120px;
        }
        .top-banner .circle2 {
            position: absolute;
            width: 120px; height: 120px;
            border-radius: 50%;
            background: rgba(255,255,255,.08);
            bottom: -30px; left: 60%;
        }

        .wrapper {
            max-width: 760px;
            margin: 0 auto;
            padding: 0 24px 80px;
        }

        /* ── PAGE HEADER ── */
        .page-header {
            position: relative;
            z-index: 10;
            margin-top: -80px;
            margin-bottom: 32px;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }
        .header-card {
            background: #fff;
            border-radius: 20px;
            padding: 24px 32px;
            box-shadow: 0 8px 32px rgba(79,70,229,.12), 0 2px 8px rgba(0,0,0,.06);
            flex: 1;
            min-width: 240px;
        }
        .label {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .16em;
            text-transform: uppercase;
            color: var(--accent);
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .label-dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: var(--accent);
            display: inline-block;
        }
        h1 {
            font-size: clamp(1.5rem, 4vw, 2.2rem);
            font-weight: 800;
            line-height: 1.1;
            color: var(--text);
        }
        h1 span {
            background: linear-gradient(120deg, #4f46e5, #0ea5e9);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 22px;
            background: #fff;
            color: var(--accent);
            font-weight: 700;
            font-size: 13px;
            border: 1.5px solid #c7d2fe;
            border-radius: 12px;
            text-decoration: none;
            transition: background .15s, box-shadow .15s;
            align-self: center;
            white-space: nowrap;
        }
        .btn-back:hover {
            background: #ede9fe;
            box-shadow: 0 2px 10px rgba(79,70,229,.15);
        }

        /* ── ALERT ── */
        .alert {
            border-radius: 14px;
            padding: 16px 20px;
            margin-bottom: 24px;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            animation: fadeUp .4s ease both;
        }
        .alert-error {
            background: #fff1f2;
            border: 1px solid #fecdd3;
            color: #be123c;
        }
        .alert-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #15803d;
        }
        .alert ul { padding-left: 18px; margin-top: 4px; }
        .alert li { margin-top: 3px; }

        /* ── FORM CARD ── */
        .form-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 36px 40px;
            box-shadow: 0 4px 24px rgba(79,70,229,.07);
            animation: fadeUp .5s ease .05s both;
        }

        .form-section-title {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--accent);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1.5px solid #e0e7ff;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        .form-group { display: flex; flex-direction: column; gap: 6px; }
        .form-group.full { grid-column: 1 / -1; }

        label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
        }
        label .req { color: var(--danger); margin-left: 2px; }

        input[type="text"],
        input[type="email"],
        select {
            background: #f8faff;
            border: 1.5px solid var(--border);
            border-radius: 11px;
            padding: 11px 14px;
            font-family: inherit;
            font-size: 14px;
            color: var(--text);
            outline: none;
            transition: border-color .2s, box-shadow .2s, background .2s;
            width: 100%;
        }
        input:focus, select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(79,70,229,.1);
            background: #fff;
        }
        input::placeholder { color: #b0b8d0; }

        /* ── UPLOAD AREA ── */
        .upload-area {
            border: 2px dashed #c7d2fe;
            border-radius: 14px;
            padding: 28px;
            text-align: center;
            cursor: pointer;
            transition: border-color .2s, background .2s;
            background: #f8faff;
            position: relative;
        }
        .upload-area:hover, .upload-area.drag-over {
            border-color: var(--accent);
            background: #ede9fe22;
        }
        .upload-area input[type="file"] {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }
        .upload-icon {
            width: 52px; height: 52px;
            background: linear-gradient(135deg, #ede9fe, #dbeafe);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 12px;
        }
        .upload-icon svg { color: var(--accent); }
        .upload-text { font-weight: 600; font-size: 14px; color: var(--text); }
        .upload-hint { font-size: 12px; color: var(--muted); margin-top: 4px; }
        .upload-preview {
            margin-top: 14px;
            display: none;
        }
        .upload-preview img {
            width: 80px; height: 80px;
            object-fit: cover;
            border-radius: 14px;
            border: 2px solid #c7d2fe;
            box-shadow: 0 2px 8px rgba(79,70,229,.1);
        }
        .upload-preview .file-name {
            font-size: 12px;
            color: var(--muted);
            margin-top: 6px;
            font-family: 'Fira Code', monospace;
        }

        /* ── SUBMIT BUTTON ── */
        .form-actions {
            margin-top: 28px;
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            flex-wrap: wrap;
        }
        .btn-submit {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 13px 30px;
            background: linear-gradient(135deg, #4f46e5, #0ea5e9);
            color: #fff;
            font-family: inherit;
            font-size: 14px;
            font-weight: 700;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            box-shadow: 0 4px 16px rgba(79,70,229,.35);
            transition: transform .15s, box-shadow .15s;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(79,70,229,.4);
        }
        .btn-reset {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 13px 22px;
            background: #fff;
            color: var(--muted);
            font-family: inherit;
            font-size: 14px;
            font-weight: 600;
            border: 1.5px solid var(--border);
            border-radius: 12px;
            cursor: pointer;
            transition: background .15s;
        }
        .btn-reset:hover { background: #f1f5fd; }

        /* ── ANIMATIONS ── */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 600px) {
            .top-banner { height: 140px; }
            .page-header { margin-top: -50px; }
            .header-card { padding: 18px 20px; }
            .form-card { padding: 24px 20px; }
            .form-grid { grid-template-columns: 1fr; }
            .form-group.full { grid-column: 1; }
        }
    </style>
</head>
<body>

<!-- TOP BANNER -->
<div class="top-banner">
    <div class="circle1"></div>
    <div class="circle2"></div>
</div>

<div class="wrapper">

    <!-- HEADER -->
    <div class="page-header">
        <div class="header-card">
            <div class="label"><span class="label-dot"></span> Panel Admin</div>
            <h1>Tambah <span>Mahasiswa</span></h1>
        </div>
        <a href="dasar.php" class="btn-back">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
            Kembali
        </a>
    </div>

    <?php if ($success): ?>
    <div class="alert alert-success">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" style="flex-shrink:0"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        <div>
            <strong>Data berhasil disimpan!</strong>
            <div style="margin-top:6px;font-size:13px;">
                <a href="dasar.php" style="color:inherit;font-weight:700;">← Kembali ke daftar</a>
                &nbsp;·&nbsp;
                <a href="tambah.php" style="color:inherit;font-weight:700;">Tambah lagi</a>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" style="flex-shrink:0"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <div>
            <strong>Terdapat kesalahan:</strong>
            <ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
        </div>
    </div>
    <?php endif; ?>

    <!-- FORM -->
    <div class="form-card">
        <div class="form-section-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Data Mahasiswa
        </div>

        <form method="POST" enctype="multipart/form-data">

            <div class="form-grid">
                <div class="form-group">
                    <label for="nrp">NRP <span class="req">*</span></label>
                    <input type="text" id="nrp" name="nrp"
                           placeholder="Contoh: 5025211001"
                           value="<?= htmlspecialchars($_POST['nrp'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="nama">Nama Lengkap <span class="req">*</span></label>
                    <input type="text" id="nama" name="nama"
                           placeholder="Contoh: Budi Santoso"
                           value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="email">Email <span class="req">*</span></label>
                    <input type="email" id="email" name="email"
                           placeholder="Contoh: budi@email.com"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="jurusan">Jurusan <span class="req">*</span></label>
                    <select id="jurusan" name="jurusan">
                        <option value="" disabled <?= empty($_POST['jurusan']) ? 'selected' : '' ?>>— Pilih Jurusan —</option>
                        <?php
                        $jurusan_list = [
                            'Teknik Informatika',
                            'Sistem Informasi',
                            'Teknik Komputer',
                            'Teknik Elektro',
                            'Teknik Mesin',
                            'Teknik Sipil',
                            'Manajemen Bisnis',
                            'Desain Komunikasi Visual',
                        ];
                        foreach ($jurusan_list as $j):
                            $selected = (($_POST['jurusan'] ?? '') === $j) ? 'selected' : '';
                        ?>
                        <option value="<?= $j ?>" <?= $selected ?>><?= $j ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group full">
                    <label>Foto Mahasiswa</label>
                    <div class="upload-area" id="uploadArea">
                        <input type="file" name="gambar" id="gambarInput"
                               accept="image/jpg,image/jpeg,image/png,image/webp,image/gif"
                               onchange="previewImage(event)">
                        <div class="upload-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                        </div>
                        <div class="upload-text">Klik atau seret gambar ke sini</div>
                        <div class="upload-hint">JPG, PNG, WEBP — maks. 2 MB</div>
                        <div class="upload-preview" id="uploadPreview">
                            <img id="previewImg" src="" alt="Preview">
                            <div class="file-name" id="fileName"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="reset" class="btn-reset" onclick="resetPreview()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-3.5"/></svg>
                    Reset
                </button>
                <button type="submit" class="btn-submit">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                    Simpan Mahasiswa
                </button>
            </div>

        </form>
    </div>

</div>

<script>
function previewImage(event) {
    const file = event.target.files[0];
    if (!file) return;
    const preview = document.getElementById('uploadPreview');
    const img     = document.getElementById('previewImg');
    const name    = document.getElementById('fileName');
    const reader  = new FileReader();
    reader.onload = e => {
        img.src = e.target.result;
        name.textContent = file.name;
        preview.style.display = 'block';
    };
    reader.readAsDataURL(file);
}

function resetPreview() {
    document.getElementById('uploadPreview').style.display = 'none';
    document.getElementById('previewImg').src = '';
    document.getElementById('fileName').textContent = '';
}

// Drag & drop style
const area = document.getElementById('uploadArea');
area.addEventListener('dragover', e => { e.preventDefault(); area.classList.add('drag-over'); });
area.addEventListener('dragleave', () => area.classList.remove('drag-over'));
area.addEventListener('drop', e => { e.preventDefault(); area.classList.remove('drag-over'); });
</script>
</body>
</html>