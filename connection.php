<?php
try {
    $pdo = new PDO("sqlite:" . __DIR__ . "/buku_tamu.db");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    $pdo->exec("CREATE TABLE IF NOT EXISTS Buku_Tamu(
        ID      INTEGER PRIMARY KEY AUTOINCREMENT,
        nama    TEXT NOT NULL,
        pesan   TEXT NOT NULL,
        no_hp   TEXT NOT NULL,
        alamat  TEXT NOT NULL,
        waktu   TEXT NOT NULL DEFAULT (datetime('now', 'localtime'))
    )");
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
?>