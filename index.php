<?php
require "connection.php";
date_default_timezone_set("Asia/Jakarta");

function e($teks)
{
    return htmlspecialchars((string) $teks, ENT_QUOTES, "UTF-8");
}

$bulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli",
           "Agustus", "September", "Oktober", "November", "Desember"];

function tanggal_panjang($datetime)
{
    global $bulan;
    $t = strtotime($datetime);
    return date("j", $t) . " " . $bulan[(int) date("n", $t)] . " " . date("Y", $t);
}

$total = (int) $pdo->query("SELECT COUNT(*) FROM Buku_Tamu")->fetchColumn();

$daftar = $pdo->query(
    "SELECT ID, nama, pesan, no_hp, alamat, waktu
     FROM Buku_Tamu
     ORDER BY waktu DESC, ID DESC"
)->fetchAll();

$hariIni = (int) $pdo->query(
    "SELECT COUNT(*) FROM Buku_Tamu WHERE date(waktu) = date('now', 'localtime')"
)->fetchColumn();

$terakhir = $pdo->query("SELECT MAX(waktu) FROM Buku_Tamu")->fetchColumn();

$status = $_GET["status"] ?? "";
$pesanStatus = $_GET["msg"] ?? "";
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Buku tamu digital: tinggalkan nama dan pesanmu.">
  <title>Buku Tamu Digital</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Google+Sans+Flex:opsz,wght@6..144,1..1000&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>

  <svg width="0" height="0" style="position:absolute" aria-hidden="true">
    <symbol id="i-chart" viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></symbol>
    <symbol id="i-plus" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></symbol>
    <symbol id="i-search" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></symbol>
    <symbol id="i-close" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></symbol>
    <symbol id="i-menu" viewBox="0 0 24 24"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></symbol>
    <symbol id="i-message" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></symbol>
    <symbol id="i-calendar" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></symbol>
    <symbol id="i-clock" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></symbol>
    <symbol id="i-trash" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></symbol>
  </svg>

  <div class="dashboard-shell">
    <button class="sidebar-backdrop" id="sidebar-backdrop" type="button" aria-label="Tutup menu"></button>

    <aside class="app-sidebar" id="sidebar">
      <div class="sidebar-brand">
        <div class="brand-mark"><img src="assets/stuff/PP.webp" alt=""></div>
        <div><strong>Hansen Santoso</strong><span>252410103060</span></div>
        <button class="sidebar-close" id="sidebar-close" type="button" aria-label="Tutup menu"><svg class="icon"><use href="#i-close"/></svg></button>
      </div>

      <div class="sidebar-section-label">Workspace</div>
      <nav class="sidebar-nav" aria-label="Navigasi utama">
        <button class="nav-link is-active" type="button" data-target="#section-buku-tamu" aria-current="page">
          <svg class="icon"><use href="#i-message"/></svg><span>Buku Tamu</span>
        </button>
        <button class="nav-link" type="button" data-target="#section-statistik">
          <svg class="icon"><use href="#i-chart"/></svg><span>Statistik</span>
        </button>
      </nav>
    </aside>

    <div class="main-content">
      <header class="mobile-header navbar">
        <button class="mobile-menu-button" id="menu-toggle" type="button" aria-label="Buka menu"><svg class="icon"><use href="#i-menu"/></svg></button>
        <div class="mobile-brand"><span class="brand-mark"><img src="assets/stuff/PP.webp" alt=""></span><strong>Hansen Santoso</strong></div>
      </header>

      <main class="content-inner">

        <section class="page-section" id="section-buku-tamu">
          <header class="page-heading">
            <div>
              <p class="eyebrow"><?= e(date("j") . " " . $bulan[(int) date("n")] . " " . date("Y")) ?></p>
              <h1>Buku Tamu</h1>
            </div>
            <button class="btn primary-button" type="button" data-bs-toggle="modal" data-bs-target="#modal-pesan">
              <svg class="icon"><use href="#i-plus"/></svg>Tulis Pesan
            </button>
          </header>

          <div class="card content-card">
            <div class="card-toolbar">
              <h2>Semua Pesan <span id="total-pesan"><?= $total ?></span></h2>
              <div class="card-controls">
                <label class="search-box">
                  <svg class="icon"><use href="#i-search"/></svg>
                  <input id="search" type="search" placeholder="Cari nama, no. HP, alamat, atau pesan..." aria-label="Cari pesan" autocomplete="off" />
                </label>
              </div>
            </div>

            <?php if ($total > 0): ?>
              <div class="table-responsive table-scroll" id="tabel-wrapper">
                <table class="table table-guestbook align-middle" id="tabel-pesan">
                  <thead>
                    <tr>
                      <th scope="col" class="col-no">No</th>
                      <th scope="col" class="col-nama">Nama</th>
                      <th scope="col" class="col-pesan">Pesan</th>
                      <th scope="col" class="col-hp">No. HP</th>
                      <th scope="col" class="col-alamat">Alamat</th>
                      <th scope="col" class="col-waktu">Waktu Kirim</th>
                      <th scope="col" class="col-aksi"><span class="visually-hidden">Aksi</span></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($daftar as $i => $row): ?>
                      <tr tabindex="0"
                          data-id="<?= (int) $row["ID"] ?>"
                          data-nama="<?= e($row["nama"]) ?>"
                          data-hp="<?= e($row["no_hp"]) ?>"
                          data-alamat="<?= e($row["alamat"]) ?>"
                          data-pesan="<?= e($row["pesan"]) ?>">
                        <td data-label="No" class="col-no"><?= $i + 1 ?></td>
                        <td data-label="Nama" class="col-nama"><strong class="cell-clamp"><?= e($row["nama"]) ?></strong></td>
                        <td data-label="Pesan" class="col-pesan"><span class="cell-clamp"><?= e($row["pesan"]) ?></span></td>
                        <td data-label="No. HP" class="col-hp"><?= e($row["no_hp"]) ?></td>
                        <td data-label="Alamat" class="col-alamat"><span class="cell-clamp"><?= e($row["alamat"]) ?></span></td>
                        <td data-label="Waktu" class="col-waktu">
                          <?= e(tanggal_panjang($row["waktu"])) ?>
                          <small><?= e(date("H:i", strtotime($row["waktu"]))) ?> WIB</small>
                        </td>
                        <td data-label="Aksi" class="col-aksi">
                          <button type="button" class="btn-hapus" aria-label="Hapus pesan dari <?= e($row["nama"]) ?>">
                            <svg class="icon"><use href="#i-trash"/></svg>
                          </button>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>

              <div class="empty-state" id="no-result" style="display:none">
                <div class="empty-icon"><svg class="icon"><use href="#i-search"/></svg></div>
                <h3>Pesan tidak ditemukan</h3>
                <p>Coba kata kunci lain.</p>
              </div>
            <?php else: ?>
              <div class="empty-state">
                <div class="empty-icon"><svg class="icon"><use href="#i-message"/></svg></div>
                <h3>Belum ada pesan</h3>
                <p>Jadilah yang pertama menulis di buku tamu ini.</p>
                <button class="btn primary-button" type="button" data-bs-toggle="modal" data-bs-target="#modal-pesan">Tulis Pesan Pertama</button>
              </div>
            <?php endif; ?>
          </div>
        </section>

        <section class="page-section" id="section-statistik" style="display:none">
          <div class="stats-row">
            <article class="stat-card">
              <div class="stat-icon green"><svg class="icon"><use href="#i-message"/></svg></div>
              <div>
                <span>Total Pesan</span>
                <strong><?= $total ?></strong>
                <small>pesan masuk seluruhnya</small>
              </div>
            </article>
            <article class="stat-card">
              <div class="stat-icon orange"><svg class="icon"><use href="#i-calendar"/></svg></div>
              <div>
                <span>Pesan Hari Ini</span>
                <strong><?= $hariIni ?></strong>
                <small>sejak pukul 00:00</small>
              </div>
            </article>
            <article class="stat-card">
              <div class="stat-icon neutral"><svg class="icon"><use href="#i-clock"/></svg></div>
              <div>
                <span>Pesan Terakhir</span>
                <?php if ($terakhir): ?>
                  <strong><?= e(date("H:i", strtotime($terakhir))) ?> WIB</strong>
                  <small><?= e(tanggal_panjang($terakhir)) ?></small>
                <?php else: ?>
                  <strong>-</strong>
                  <small>belum ada pesan</small>
                <?php endif; ?>
              </div>
            </article>
          </div>
        </section>

      </main>

      <footer class="site-footer"></footer>
    </div>
  </div>

  <div class="modal fade" id="modal-pesan" tabindex="-1" aria-labelledby="modal-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <form class="modal-content modal-card" id="form-pesan" action="process.php" method="post" novalidate>
        <div class="modal-heading">
          <div>
            <span class="modal-kicker">PESAN BARU</span>
            <h2 id="modal-title">Tulis Pesan</h2>
            <p>Isi data di bawah, lalu kirim ke buku tamu.</p>
          </div>
          <button class="icon-close-button" type="button" data-bs-dismiss="modal" aria-label="Tutup"><svg class="icon"><use href="#i-close"/></svg></button>
        </div>

        <div class="form-grid">
          <label class="form-field form-field-wide">
            <span>Nama <b>*</b></span>
            <input type="text" name="nama" id="f-nama" maxlength="100" placeholder="Nama lengkap" autocomplete="name" />
          </label>
          <label class="form-field">
            <span>No. HP <b>*</b></span>
            <input type="tel" name="no_hp" id="f-hp" placeholder="081234567890" autocomplete="tel" />
          </label>
          <label class="form-field">
            <span>Alamat <b>*</b></span>
            <input type="text" name="alamat" id="f-alamat" maxlength="255" placeholder="Kota atau alamat" autocomplete="street-address" />
          </label>
          <label class="form-field form-field-wide">
            <span>Pesan <b>*</b></span>
            <textarea name="pesan" id="f-pesan" rows="5" placeholder="Tulis pesanmu..."></textarea>
          </label>
        </div>

        <p class="form-error" id="form-error" role="alert" style="display:none"></p>

        <div class="modal-actions">
          <button class="btn secondary-button" type="button" data-bs-dismiss="modal">Batal</button>
          <button class="btn primary-button" id="btn-kirim" type="submit">Kirim Pesan</button>
        </div>
      </form>
    </div>
  </div>

  <div class="modal fade" id="modal-preview" tabindex="-1" aria-label="Pesan lengkap" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content modal-card preview-card">
        <button class="icon-close-button preview-close" type="button" data-bs-dismiss="modal" aria-label="Tutup"><svg class="icon"><use href="#i-close"/></svg></button>
        <div class="modal-body preview-body">
          <dl class="preview-info">
            <div><dt>Nama pengirim</dt><dd>:</dd><dd id="pv-nama"></dd></div>
            <div><dt>No. HP</dt><dd>:</dd><dd id="pv-hp"></dd></div>
            <div><dt>Alamat</dt><dd>:</dd><dd id="pv-alamat"></dd></div>
          </dl>
          <hr>
          <p class="preview-pesan" id="pv-pesan"></p>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade modal-konfirmasi" id="modal-hapus" tabindex="-1" aria-labelledby="hapus-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <form class="modal-content modal-card delete-card" action="delete.php" method="post">
        <input type="hidden" name="id" id="hapus-id" value="">
        <div class="delete-icon"><svg class="icon"><use href="#i-trash"/></svg></div>
        <h2 id="hapus-title">Hapus pesan ini?</h2>
        <p>Apakah kamu yakin ingin menghapus pesan dari <strong id="hapus-nama"></strong>? Tindakan ini tidak dapat dibatalkan.</p>
        <div class="modal-actions">
          <button class="btn secondary-button" type="button" data-bs-dismiss="modal">Batal</button>
          <button class="btn danger-button" type="submit">Ya, Hapus Pesan</button>
        </div>
      </form>
    </div>
  </div>

  <div class="toast-stack" id="notifikasi" aria-live="polite">
    <?php if ($status === "sukses"): ?>
      <div class="alert alert-success alert-dismissible" role="alert">
        Pesan berhasil dikirim. Terima kasih!
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Tutup"></button>
      </div>
    <?php elseif ($status === "hapus"): ?>
      <div class="alert alert-success alert-dismissible" role="alert">
        Pesan berhasil dihapus.
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Tutup"></button>
      </div>
    <?php elseif ($status === "error"): ?>
      <div class="alert alert-danger alert-dismissible" role="alert">
        <?= e($pesanStatus !== "" ? $pesanStatus : "Terjadi kesalahan. Silakan coba lagi.") ?>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Tutup"></button>
      </div>
    <?php endif; ?>
  </div>

  <script src="assets/js/Jquery.js"></script>
  <script src="assets/js/bootstrap.bundle.min.js"></script>
  <script src="assets/js/script.js"></script>
</body>
</html>