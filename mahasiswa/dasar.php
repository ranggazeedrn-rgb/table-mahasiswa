<?php
require 'functions.php';
$mahasiswa = query("SELECT * FROM mahasiswa");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Admin — Daftar Mahasiswa</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:        #f0f4ff;
            --surface:   #ffffff;
            --card:      #ffffff;
            --border:    #dde3f5;
            --accent:    #4f46e5;
            --accent2:   #0ea5e9;
            --accent3:   #f59e0b;
            --danger:    #ef4444;
            --success:   #10b981;
            --text:      #1e293b;
            --muted:     #64748b;
            --heading:   'Plus Jakarta Sans', sans-serif;
            --body:      'Plus Jakarta Sans', sans-serif;
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: var(--body);
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 28px 80px;
        }

        /* ── HEADER (overlaps banner) ── */
        .page-header {
            position: relative;
            z-index: 10;
            margin-top: -80px;
            margin-bottom: 32px;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
        }

        .header-card {
            background: #fff;
            border-radius: 20px;
            padding: 24px 32px;
            box-shadow: 0 8px 32px rgba(79,70,229,.12), 0 2px 8px rgba(0,0,0,.06);
            flex: 1;
            min-width: 260px;
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
            font-family: var(--heading);
            font-size: clamp(1.7rem, 4vw, 2.6rem);
            font-weight: 800;
            color: var(--text);
            line-height: 1.1;
        }
        h1 span {
            background: linear-gradient(120deg, #4f46e5, #0ea5e9);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .btn-tambah {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 26px;
            background: linear-gradient(135deg, #4f46e5, #0ea5e9);
            color: #fff;
            font-family: var(--heading);
            font-size: 14px;
            font-weight: 700;
            border: none;
            border-radius: 14px;
            cursor: pointer;
            text-decoration: none;
            box-shadow: 0 4px 16px rgba(79,70,229,.35);
            transition: transform .15s, box-shadow .15s;
            white-space: nowrap;
            align-self: center;
        }
        .btn-tambah:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(79,70,229,.45);
        }

        /* ── STATS ── */
        .stats {
            display: flex;
            gap: 16px;
            margin-bottom: 28px;
            flex-wrap: wrap;
        }
        .stat-card {
            background: #fff;
            border-radius: 16px;
            padding: 18px 24px;
            flex: 1;
            min-width: 140px;
            box-shadow: 0 2px 12px rgba(79,70,229,.08);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 16px;
            animation: fadeUp .5s ease both;
        }
        .stat-icon {
            width: 48px; height: 48px;
            border-radius: 14px;
            background: linear-gradient(135deg, #ede9fe, #dbeafe);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .stat-icon svg { color: var(--accent); }
        .stat-num {
            font-family: var(--heading);
            font-size: 1.9rem;
            font-weight: 800;
            color: var(--text);
            line-height: 1;
        }
        .stat-label {
            font-size: 12px;
            color: var(--muted);
            margin-top: 3px;
            font-weight: 500;
        }

        /* ── TOOLBAR ── */
        .toolbar {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .search-box {
            position: relative;
            flex: 1;
            min-width: 200px;
        }
        .search-box svg {
            position: absolute;
            left: 14px; top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            pointer-events: none;
        }
        .search-box input {
            width: 100%;
            background: #fff;
            border: 1.5px solid var(--border);
            border-radius: 12px;
            padding: 11px 16px 11px 42px;
            color: var(--text);
            font-family: var(--body);
            font-size: 14px;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
            box-shadow: 0 1px 4px rgba(0,0,0,.05);
        }
        .search-box input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(79,70,229,.12);
        }
        .search-box input::placeholder { color: #b0b8d0; }

        /* ── TABLE CARD ── */
        .table-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(79,70,229,.07);
            animation: fadeUp .5s ease .08s both;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead tr {
            background: linear-gradient(90deg, #f5f3ff 0%, #eff6ff 100%);
            border-bottom: 2px solid #e0e7ff;
        }

        th {
            font-family: var(--heading);
            font-size: 11px;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--accent);
            font-weight: 700;
            padding: 14px 20px;
            text-align: left;
            white-space: nowrap;
        }
        th:first-child { padding-left: 28px; }
        th:last-child  { padding-right: 28px; }

        tbody tr {
            border-bottom: 1px solid #f1f5fd;
            transition: background .15s, transform .1s;
        }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover {
            background: #f8f7ff;
        }

        td {
            padding: 15px 20px;
            font-size: 14px;
            vertical-align: middle;
        }
        td:first-child { padding-left: 28px; }
        td:last-child  { padding-right: 28px; }

        /* row number */
        .row-num {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px; height: 30px;
            background: linear-gradient(135deg, #ede9fe, #dbeafe);
            border-radius: 9px;
            font-family: var(--heading);
            font-size: 12px;
            font-weight: 700;
            color: var(--accent);
        }

        /* avatar */
        .avatar-wrap {
            width: 46px; height: 46px;
            border-radius: 14px;
            overflow: hidden;
            border: 2px solid #e0e7ff;
            background: #f0f4ff;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(79,70,229,.1);
        }
        .avatar-wrap img {
            width: 100%; height: 100%;
            object-fit: cover;
            display: block;
        }

        .identity { display: flex; align-items: center; gap: 12px; }
        .identity-info .name { font-weight: 600; color: var(--text); }

        /* nrp kolom terpisah */
        .nrp-code {
            font-family: 'Fira Code', monospace;
            font-size: 13px;
            font-weight: 500;
            color: var(--accent);
            background: linear-gradient(120deg, #ede9fe, #dbeafe);
            border: 1px solid #c7d2fe;
            padding: 4px 10px;
            border-radius: 8px;
            white-space: nowrap;
            display: inline-block;
        }

        /* jurusan pill */
        .pill {
            display: inline-block;
            padding: 5px 13px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            background: linear-gradient(120deg, #ede9fe, #dbeafe);
            color: var(--accent);
            border: 1px solid #c7d2fe;
            white-space: nowrap;
        }

        /* email */
        .email-link {
            color: var(--accent2);
            font-size: 13px;
            text-decoration: none;
            font-weight: 500;
            transition: color .15s;
        }
        .email-link:hover { color: var(--accent); text-decoration: underline; }

        /* action buttons */
        .actions { display: flex; gap: 8px; }
        .btn-ubah, .btn-hapus {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 7px 14px;
            border-radius: 9px;
            font-size: 12px;
            font-weight: 700;
            font-family: var(--heading);
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: transform .12s, box-shadow .12s;
        }
        .btn-ubah {
            background: linear-gradient(135deg, #ede9fe, #dbeafe);
            color: var(--accent);
            border: 1px solid #c7d2fe;
        }
        .btn-ubah:hover {
            background: linear-gradient(135deg, #c7d2fe, #bfdbfe);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(79,70,229,.2);
        }
        .btn-hapus {
            background: #fff1f2;
            color: var(--danger);
            border: 1px solid #fecdd3;
        }
        .btn-hapus:hover {
            background: #ffe4e6;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(239,68,68,.15);
        }

        /* empty state */
        .empty {
            text-align: center;
            padding: 64px 24px;
            color: var(--muted);
        }
        .empty svg { margin: 0 auto 16px; display: block; opacity: .35; }
        .empty p { font-size: 14px; }

        /* ── ANIMATIONS ── */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(18px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        tbody tr { animation: fadeUp .4s ease both; }
        <?php $i = 1; foreach($mahasiswa as $row): ?>
        tbody tr:nth-child(<?= $i ?>) { animation-delay: <?= $i * 0.05 ?>s; }
        <?php $i++; endforeach; ?>

        /* ── RESPONSIVE ── */
        @media (max-width: 768px) {
            .top-banner { height: 140px; }
            .page-header { margin-top: -50px; }
            .header-card { padding: 18px 20px; }
            .table-card { border-radius: 14px; }
            th, td { padding: 12px 12px; }
            th:first-child, td:first-child { padding-left: 16px; }
            th:last-child, td:last-child   { padding-right: 16px; }
            .hide-mobile { display: none; }
            h1 { font-size: 1.5rem; }
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
            <h1>Daftar <span>Mahasiswa</span></h1>
        </div>
        <a href="tambah.php" class="btn-tambah">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Tambah Mahasiswa
        </a>
    </div>

    <!-- STATS -->
    <div class="stats">
        <div class="stat-card" style="animation-delay:.05s">
            <div class="stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div>
                <div class="stat-num"><?= count($mahasiswa) ?></div>
                <div class="stat-label">Total Mahasiswa</div>
            </div>
        </div>
    </div>

    <!-- SEARCH -->
    <div class="toolbar">
        <div class="search-box">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" id="searchInput" placeholder="Cari nama, NRP, atau jurusan…" oninput="filterTable()">
        </div>
    </div>

    <!-- TABLE -->
    <div class="table-card">
        <table id="mahasiswaTable">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Mahasiswa</th>
                    <th class="hide-mobile">NRP</th>
                    <th class="hide-mobile">Email</th>
                    <th class="hide-mobile">Jurusan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($mahasiswa)): ?>
                <tr>
                    <td colspan="6">
                        <div class="empty">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            <p>Belum ada data mahasiswa.</p>
                        </div>
                    </td>
                </tr>
                <?php else: ?>
                <?php $i = 1; foreach ($mahasiswa as $row): ?>
                <tr>
                    <td><span class="row-num"><?= $i ?></span></td>
                    <td>
                        <div class="identity">
                            <div class="avatar-wrap">
                                <img src="img/<?= htmlspecialchars($row['gambar']) ?>"
                                     alt="<?= htmlspecialchars($row['nama']) ?>"
                                     onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($row['nama']) ?>&background=ede9fe&color=4f46e5&size=88'">
                            </div>
                            <div class="identity-info">
                                <div class="name"><?= htmlspecialchars($row['nama']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td class="hide-mobile">
                        <span class="nrp-code"><?= htmlspecialchars($row['nrp']) ?></span>
                    </td>
                    <td class="hide-mobile">
                        <a href="mailto:<?= htmlspecialchars($row['email']) ?>" class="email-link">
                            <?= htmlspecialchars($row['email']) ?>
                        </a>
                    </td>
                    <td class="hide-mobile">
                        <span class="pill"><?= htmlspecialchars($row['jurusan']) ?></span>
                    </td>
                    <td>
                        <div class="actions">
                            <a href="ubah.php?nrp=<?= urlencode($row['nrp']) ?>" class="btn-ubah">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                Ubah
                            </a>
                            <a href="hapus.php?nrp=<?= urlencode($row['nrp']) ?>"
                               class="btn-hapus"
                               onclick="return confirm('Hapus mahasiswa <?= addslashes(htmlspecialchars($row['nama'])) ?>?')">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                Hapus
                            </a>
                        </div>
                    </td>
                </tr>
                <?php $i++; endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<script>
function filterTable() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#mahasiswaTable tbody tr');
    rows.forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}
</script>
</body>
</html>