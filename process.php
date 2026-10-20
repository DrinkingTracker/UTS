<?php
require "connection.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$nama   = trim($_POST["nama"] ?? "");
$no_hp  = preg_replace("/[\s\-]/", "", trim($_POST["no_hp"] ?? ""));
$alamat = trim($_POST["alamat"] ?? "");
$pesan  = trim($_POST["pesan"] ?? "");

$errors = [];

if ($nama === "") {
    $errors[] = "Nama wajib diisi.";
} elseif (mb_strlen($nama) > 100) {
    $errors[] = "Nama maksimal 100 karakter.";
}

if ($no_hp === "") {
    $errors[] = "No. HP wajib diisi.";
} elseif (!preg_match('/^(\+62|62|0)[0-9]{8,13}$/', $no_hp)) {
    $errors[] = "Format No. HP tidak valid (contoh: 081234567890).";
}

if ($alamat === "") {
    $errors[] = "Alamat wajib diisi.";
} elseif (mb_strlen($alamat) > 255) {
    $errors[] = "Alamat maksimal 255 karakter.";
}

if ($pesan === "") {
    $errors[] = "Pesan wajib diisi.";
}

if ($errors) {
    header("Location: index.php?status=error&msg=" . urlencode(implode(" ", $errors)));
    exit;
}

$stmt = $pdo->prepare(
    "INSERT INTO Buku_Tamu (nama, pesan, no_hp, alamat) VALUES (:nama, :pesan, :no_hp, :alamat)"
);
$stmt->execute([
    ":nama"   => $nama,
    ":pesan"  => $pesan,
    ":no_hp"  => $no_hp,
    ":alamat" => $alamat,
]);

header("Location: index.php?status=sukses");
exit;