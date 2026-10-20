<?php
require "connection.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$id = filter_var($_POST["id"] ?? "", FILTER_VALIDATE_INT);

if ($id === false) {
    header("Location: index.php?status=error&msg=" . urlencode("Pesan yang akan dihapus tidak valid."));
    exit;
}

$stmt = $pdo->prepare("DELETE FROM Buku_Tamu WHERE ID = :id");
$stmt->execute([":id" => $id]);

if ($stmt->rowCount() === 0) {
    header("Location: index.php?status=error&msg=" . urlencode("Pesan tidak ditemukan atau sudah dihapus."));
    exit;
}

header("Location: index.php?status=hapus");
exit;