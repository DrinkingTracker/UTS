CREATE TABLE IF NOT EXISTS Buku_Tamu(
    ID      INTEGER PRIMARY KEY AUTOINCREMENT,
    nama    TEXT NOT NULL,
    pesan   TEXT NOT NULL,
    no_hp   TEXT NOT NULL,
    alamat  TEXT NOT NULL,
    waktu   TEXT NOT NULL DEFAULT (datetime('now', 'localtime'))
);