<?php
// Sertakan file konfigurasi dan mulai sesi jika belum dimulai
session_start();
require 'env.php';

// Periksa apakah kriteria_id dikirim melalui permintaan GET
if (isset($_GET['id_kriteria'])) {
    $kriteriaId = $_GET['id_kriteria'];

    // Buat query untuk mengambil data subkriteria berdasarkan id_kriteria
    $query = "SELECT * FROM subkriteria WHERE id_kriteria = '$kriteriaId'";
    $result = mysqli_query($conn, $query);

    // Buat array untuk menyimpan data subkriteria
    $subcriteria = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $subcriteria[] = $row;
    }

    // Mengembalikan data subkriteria dalam format JSON
    echo json_encode($subcriteria);
} else {
    // Jika id_kriteria tidak tersedia dalam permintaan GET, kirim respons kode status 400
    http_response_code(400);
}
