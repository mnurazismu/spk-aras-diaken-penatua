<?php
session_start();
require 'env.php';

if (!isset($_SESSION['login'])) {
    echo '
    <script src="src/jquery-3.6.3.min.js"></script>
    <script src="src/sweetalert2.all.min.js"></script>
    <script>
    $(document).ready(function() {
        Swal.fire({
            position: "top-center",
            icon: "error",
            title: "Anda Belum Login!",
            text: "Silahkan Login Terlebih Dahulu!",
            showConfirmButton: false,
            timer: 2000
        })
        setTimeout(myFunction, 2000);
    });
    function myFunction() {
        document.location.href = "login.php";
    }
    </script>
    ';
    exit;
} else {
    if ($_SESSION['tipe_user'] != 'Admin') {
        echo '
        <script src="src/jquery-3.6.3.min.js"></script>
        <script src="src/sweetalert2.all.min.js"></script>
        <script>
        $(document).ready(function() {
            Swal.fire({
                position: "top-center",
                icon: "error",
                title: "Anda Login Sebagai Guru!",
                showConfirmButton: false,
                timer: 2000
            })
            setTimeout(myFunction, 2000);
        });
        function myFunction() {
            document.location.href = "beranda_guru.php";
        }
        </script>
        ';
        exit;
    }

    $email = $_SESSION['email'];
    $nama_lengkap = $_SESSION['nama_lengkap'];
    $tipe_user = $_SESSION['tipe_user'];

    $queryKriteria = "SELECT * FROM kriteria";
    $resultKriteria = mysqli_query($conn, $queryKriteria);
    $kriteria = [];
    while ($row = mysqli_fetch_assoc($resultKriteria)) {
        $kriteria[] = $row;
    }
    $jumlah_kriteria = count($kriteria);
    $jumlah_bobot_kriteria = 0;
    foreach ($kriteria as $key) {
        $jumlah_bobot_kriteria += $key['bobot_kriteria'];
    }

    if ($jumlah_bobot_kriteria != 100) {
        echo '
        <script src="src/jquery-3.6.3.min.js"></script>
        <script src="src/sweetalert2.all.min.js"></script>
        <script>
        $(document).ready(function() {
            Swal.fire({
                position: "top-center",
                icon: "error",
                title: "Jumlah Bobot Kriteria Tidak 100%!",
                text: "Silahkan Atur Bobot Kriteria Terlebih Dahulu!",
                showConfirmButton: false,
                timer: 2000
            })
            setTimeout(myFunction, 2000);
        });
        function myFunction() {
            document.location.href = "kriteria_admin.php";
        }
        </script>
        ';
        exit;
    }

    $query_alternatif = "SELECT * FROM alternatif";
    $result_alternatif = mysqli_query($conn, $query_alternatif);
    $alternatif = [];
    while ($row = mysqli_fetch_assoc($result_alternatif)) {
        $alternatif[] = $row;
    }

    $query_check = "SELECT COUNT(*) as count FROM nilai_matriks WHERE nilai_matriks = 0 AND id_alternatif IN (SELECT id_alternatif FROM alternatif)";
    $result_check = mysqli_query($conn, $query_check);
    $check = mysqli_fetch_assoc($result_check);
    $has_zero = $check['count'] > 0;

    if ($has_zero) {
        echo '
        <script src="src/jquery-3.6.3.min.js"></script>
        <script src="src/sweetalert2.all.min.js"></script>
        <script>
        $(document).ready(function() {
            Swal.fire({
                position: "top-center",
                icon: "error",
                title: "Nilai Matriks Ada Yang Bernilai 0!",
                text: "Silahkan Isi Nilai Matriks Terlebih Dahulu!",
                showConfirmButton: false,
                timer: 2000
            })
            setTimeout(myFunction, 2000);
        });
        function myFunction() {
            document.location.href = "alternatif_admin.php";
        }
        </script>
        ';
        exit;
    }

    // empty the table 'hasil' before inserting new values
    $query_delete = "DELETE FROM hasil";
    mysqli_query($conn, $query_delete);

    // Initialize an empty array to store nilai_matriks
    $nilai_matriks = [];

    // Iterate over each alternative to fetch corresponding nilai_matriks
    foreach ($alternatif as $key => $value) {
        $query_nilai_matriks = "SELECT * FROM nilai_matriks WHERE id_alternatif = " . $value['id_alternatif'];
        $result_nilai_matriks = mysqli_query($conn, $query_nilai_matriks);
        while ($row = mysqli_fetch_assoc($result_nilai_matriks)) {
            $nilai_matriks[] = $row;
        }
    }

    // Find the best value for each criteria; if criteria is cost, find the minimum value, otherwise find the maximum value
    $nilai_terbaik = [];
    foreach ($kriteria as $key => $criteria) { 
        $nilai = [];
        foreach ($nilai_matriks as $kunci => $matriks) { 
            // Check if criteria ID matches
            if ($criteria['id_kriteria'] == $matriks['id_kriteria']) {
                $nilai[] = $matriks['nilai_matriks'];
            }
        }

        // Determine the best value based on the criteria type
        if ($criteria['jenis_kriteria'] == 'Cost') {
            $nilai_terbaik[] = !empty($nilai) ? min($nilai) : null; 
        } else {
            $nilai_terbaik[] = !empty($nilai) ? max($nilai) : null; 
        }
    }

    // simpan nilai terbaik ke dalam array bersama dengan nama kriteria
    $nilai_terbaik_kriteria = [];
    foreach ($kriteria as $key => $criteria) {
        $nilai_terbaik_kriteria[] = [
            'nama_kriteria' => $criteria['nama_kriteria'],
            'nilai_terbaik' => $nilai_terbaik[$key]
        ];
    }

    // Initialize an empty array to store normalized matrix values
    $nilai_normalisasi = [];

    // Initialize arrays to store the sum of nilai_matriks and divisors
    $pembagi_per_kriteria = []; // Array to store divisors for each criterion
    $nilai_terbaik_normalisasi = [];

    // Iterate over each criterion to calculate divisors and normalized best values
    foreach ($kriteria as $key_kriteria => $criteria) {
        $jumlah_nilai_matriks = 0; // Initialize the sum of nilai_matriks for the current criterion
        $reciprocal_sum = 0; // Initialize the sum of reciprocals for criteria of type 'Cost'

        // Sum nilai_matriks and calculate reciprocal sum for the current criterion
        foreach ($nilai_matriks as $matriks) {
            if ($matriks['id_kriteria'] == $criteria['id_kriteria']) {
                $nilai = $matriks['nilai_matriks'];
                $jumlah_nilai_matriks += $nilai;
                if ($criteria['jenis_kriteria'] == 'Cost') {
                    $reciprocal_sum += 1 / $nilai;
                }
            }
        }

        // Determine the divisor (pembagi) for the current criterion
        if ($criteria['jenis_kriteria'] == 'Cost') {
            $pembagi = $reciprocal_sum + (1 / $nilai_terbaik[$key_kriteria]);
        } else {
            $pembagi = $jumlah_nilai_matriks + $nilai_terbaik[$key_kriteria];
        }

        // Store the divisor
        $pembagi_per_kriteria[$criteria['id_kriteria']] = $pembagi;
        
        // Normalize the best value
        if ($criteria['jenis_kriteria'] == 'Cost') {
            $nilai_terbaik_normalisasi[] = [
                'nama_kriteria' => $criteria['nama_kriteria'],
                'nilai_normalisasi' => number_format((1 / $nilai_terbaik[$key_kriteria]) / $pembagi, 4)
            ];
        } else {
            $nilai_terbaik_normalisasi[] = [
                'nama_kriteria' => $criteria['nama_kriteria'],
                'nilai_normalisasi' => number_format($nilai_terbaik[$key_kriteria] / $pembagi, 4)
            ];
        }
    }

    // Add the normalized best values to the main normalization array as the first element
    $nilai_normalisasi[] = [
        'id_alternatif' => 'A0', // Identifier for best values
        'nama_alternatif' => 'Alternatif Optimum',
        'normalisasi' => $nilai_terbaik_normalisasi
    ];

    // Iterate over each alternative
    foreach ($alternatif as $key => $value) {
        // Initialize an array to store normalized values for the current alternative
        $nilai_normalisasi_alternatif = [
            'id_alternatif' => $value['id_alternatif'],
            'nama_alternatif' => $value['nama_alternatif'],
            'normalisasi' => []  // Array to store normalized values for each criterion
        ];

        // Iterate over each criterion
        foreach ($kriteria as $key_kriteria => $criteria) {
            // Find the corresponding nilai_matriks for the current alternative and criterion
            $nilai_matriks_value = null;
            foreach ($nilai_matriks as $matriks) {
                if ($matriks['id_alternatif'] == $value['id_alternatif'] && $matriks['id_kriteria'] == $criteria['id_kriteria']) {
                    $nilai_matriks_value = $matriks['nilai_matriks'];
                    break;
                }
            }

            // Get the divisor for the current criterion
            $pembagi = $pembagi_per_kriteria[$criteria['id_kriteria']];

            // Calculate normalized value
            if ($criteria['jenis_kriteria'] == 'Cost') {
                $nilai_normalisasi_value = $nilai_matriks_value ? (1 / $nilai_matriks_value) / $pembagi : null;
            } else {
                $nilai_normalisasi_value = $nilai_matriks_value ? $nilai_matriks_value / $pembagi : null;
            }

            $nilai_normalisasi_value = number_format($nilai_normalisasi_value, 4);

            // Store normalized value with criterion name
            $nilai_normalisasi_alternatif['normalisasi'][] = [
                'nama_kriteria' => $criteria['nama_kriteria'],
                'nilai_normalisasi' => $nilai_normalisasi_value
            ];
        }

        // Add the normalized values for the current alternative to the main array
        $nilai_normalisasi[] = $nilai_normalisasi_alternatif;
    }

    // Display the normalized matrix including the normalized best values
    // echo "<pre>";
    // print_r($nilai_normalisasi);
    // echo "</pre>";
    
    // Calculate the weighted normalized matrix
    $nilai_normalisasi_terbobot = [];
    foreach ($nilai_normalisasi as $key => $value) {
        $nilai_normalisasi_terbobot_alternatif = [
            'id_alternatif' => $value['id_alternatif'],
            'nama_alternatif' => $value['nama_alternatif'],
            'normalisasi_terbobot' => []  // Array to store weighted normalized values for each criterion
        ];

        foreach ($value['normalisasi'] as $kunci => $nilai) {
            $nilai_normalisasi_terbobot_alternatif['normalisasi_terbobot'][] = [
                'nama_kriteria' => $nilai['nama_kriteria'],
                'nilai_normalisasi_terbobot' => number_format($nilai['nilai_normalisasi'] * $kriteria[$kunci]['bobot_kriteria'] / 100, 4)
            ];
        }

        $nilai_normalisasi_terbobot[] = $nilai_normalisasi_terbobot_alternatif;
    }

    // find max value for each criterion
    $max_value_per_kriteria = [];
    foreach ($kriteria as $key => $criteria) {
        $max_value = 0;
        foreach ($nilai_normalisasi_terbobot as $kunci => $nilai) {
            foreach ($nilai['normalisasi_terbobot'] as $kunci2 => $nilai2) {
                if ($criteria['nama_kriteria'] == $nilai2['nama_kriteria']) {
                    if ($nilai2['nilai_normalisasi_terbobot'] > $max_value) {
                        $max_value = $nilai2['nilai_normalisasi_terbobot'];
                    }
                }
            }
        }
        $max_value_per_kriteria[] = $max_value;
    }

    // Calculate the weighted sum for each alternative
    $nilai_normalisasi_terbobot_sum = [];
    foreach ($nilai_normalisasi_terbobot as $key => $value) {
        $sum = 0;
        foreach ($value['normalisasi_terbobot'] as $kunci => $nilai) {
            $sum += $nilai['nilai_normalisasi_terbobot'];
        }
        $nilai_normalisasi_terbobot_sum[] = [
            'id_alternatif' => $value['id_alternatif'],
            'nama_alternatif' => $value['nama_alternatif'],
            'nilai_normalisasi_terbobot_sum' => number_format($sum, 4)
        ];
    }

    // Display the weighted sum for each alternative
    // echo "<pre>";
    // print_r($nilai_normalisasi_terbobot_sum);
    // echo "</pre>";

    // Calculate the utility value by dividing the weighted sum by max value for the criteria
    $nilai_utilitas = [];
    foreach ($nilai_normalisasi_terbobot_sum as $key => $value) {
        $total_max_value = array_sum($max_value_per_kriteria); // Sum of all max values for criteria
        $nilai_utilitas_value = $total_max_value > 0 ? $value['nilai_normalisasi_terbobot_sum'] / $total_max_value : 0; // Avoid division by zero
        
        $nilai_utilitas[] = [
            'id_alternatif' => $value['id_alternatif'],
            'nama_alternatif' => $value['nama_alternatif'],
            'nilai_utilitas' => number_format($nilai_utilitas_value, 4)
        ];
    }

    // save utility values to database, except for the best value
    foreach ($nilai_utilitas as $key => $value) {
        if ($value['id_alternatif'] != 'A0') {
            $query = "INSERT INTO hasil (id_alternatif, nilai_hasil) VALUES ('" . $value['id_alternatif'] . "', " . $value['nilai_utilitas'] . ")";
            mysqli_query($conn, $query);
        }
    }

    // Display the utility values
    // echo "<pre>";
    // print_r($nilai_utilitas);
    // echo "</pre>";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="src/output.css">
    <title>Perhitungan ARAS</title>
</head>

<body class="font-['Inter'] bg-gray-100 flex flex-col min-h-screen">
    <h2 class="mx-5 self-center text-xl font-semibold my-5 shadow-xl tracking-widest border-b border-secondary text-center">
        Perhitungan Metode ARAS (Additive Ratio Assessment)</h2>
    <div class="w-11/12 shadow-md bg-white rounded-md pb-5 self-center mb-10">
        <h2 class="mx-auto my-5 tracking-widest border-b border-quinary text-center">Matriks Keputusan</h2>
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg mx-4">
            <table class="text-center w-full text-sm rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-sm text-white uppercase bg-quinary dark:bg-gray-700 dark:text-gray-400">
                    <tr class="">
                        <th class=""></th>
                        <th scope="col" colspan="<?= $jumlah_kriteria ?>" class="px-6 py-3 text-center">
                            Kriteria
                        </th>
                    </tr>
                    <tr>
                        <th scope="col" class="px-6 py-3 ">
                            Alternatif
                        </th>
                        <?php foreach ($kriteria as $key => $value) : ?>
                            <th scope="col" class="px-6 py-3">
                                <?= $value['nama_kriteria'] ?>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($alternatif as $key => $value) : ?>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <th class="px-6 py-4">
                                <?= $value['nama_alternatif'] ?>
                            </th>
                            <?php foreach ($nilai_matriks as $kunci => $nilai) : ?>
                                <?php if ($value['id_alternatif'] == $nilai['id_alternatif']) : ?>
                                    <td class="px-6 py-4">
                                        <?= $nilai['nilai_matriks'] ?>
                                    </td>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="w-11/12 shadow-md bg-white rounded-md pb-5 self-center mb-10">
        <h2 class="mx-auto my-5 tracking-widest border-b border-quinary text-center">Nilai Alternatif Optimum</h2>
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg mx-4">
            <table class="text-center w-full text-sm rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-sm text-white uppercase bg-quinary dark:bg-gray-700 dark:text-gray-400">
                    <tr class="">
                        <th class=""></th>
                        <th scope="col" colspan="<?= $jumlah_kriteria ?>" class="px-6 py-3 text-center">
                            Kriteria
                        </th>
                    </tr>
                    <tr>
                        <th scope="col" class="px-6 py-3 ">
                            Alternatif
                        </th>
                        <?php foreach ($nilai_terbaik_kriteria as $key => $value) : ?>
                            <th scope="col" class="px-6 py-3">
                                <?= $value['nama_kriteria'] ?>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <th class="px-6 py-4">
                            Alternatif Optimum
                        </th>
                        <?php foreach ($nilai_terbaik_kriteria as $key => $value) : ?>
                            <td class="px-6 py-4">
                                <?= $value['nilai_terbaik'] ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <!-- tampilkan matriks keputusan yaitu gabungan nilai terbaik kriteria bersama yang lain -->
    <div class="w-11/12 shadow-md bg-white rounded-md pb-5 self-center mb-10">
        <h2 class="mx-auto my-5 tracking-widest border-b border-quinary text-center">Matriks Keputusan (Dengan Alternatif Optimum)</h2>
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg mx-4">
            <table class="text-center w-full text-sm rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-sm text-white uppercase bg-quinary dark:bg-gray-700 dark:text-gray-400">
                    <tr class="">
                        <th class=""></th>
                        <th scope="col" colspan="<?= $jumlah_kriteria ?>" class="px-6 py-3 text-center">
                            Kriteria
                        </th>
                    </tr>
                    <tr>
                        <th scope="col" class="px-6 py-3 ">
                            Alternatif
                        </th>
                        <?php foreach ($kriteria as $key => $value) : ?>
                            <th scope="col" class="px-6 py-3">
                                <?= $value['nama_kriteria'] ?>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <th class="px-6 py-4">
                            Alternatif Optimum
                        </th>
                        <?php foreach ($nilai_terbaik_kriteria as $key => $value) : ?>
                            <td class="px-6 py-4">
                                <?= $value['nilai_terbaik'] ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                    <?php foreach ($alternatif as $key => $value) : ?>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <th class="px-6 py-4">
                                <?= $value['nama_alternatif'] ?>
                            </th>
                            <?php foreach ($nilai_matriks as $kunci => $nilai) : ?>
                                <?php if ($value['id_alternatif'] == $nilai['id_alternatif']) : ?>
                                    <td class="px-6 py-4">
                                        <?= $nilai['nilai_matriks'] ?>
                                    </td>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="w-11/12 shadow-md bg-white rounded-md pb-5 self-center mb-10">
        <h2 class="mx-auto my-5 tracking-widest border-b border-quinary text-center">Normalisasi Matriks</h2>
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg mx-4">
            <table class="text-center w-full text-sm rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-sm text-white uppercase bg-quinary dark:bg-gray-700 dark:text-gray-400">
                    <tr class="">
                        <th class=""></th>
                        <th scope="col" colspan="<?= $jumlah_kriteria ?>" class="px-6 py-3 text-center">
                            Kriteria
                        </th>
                    </tr>
                    <tr>
                        <th scope="col" class="px-6 py-3 ">
                            Alternatif
                        </th>
                        <?php foreach ($kriteria as $key => $value) : ?>
                            <th scope="col" class="px-6 py-3">
                                <?= $value['nama_kriteria'] ?>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($nilai_normalisasi as $key => $value) : ?>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <th class="px-6 py-4">
                                <?= $value['nama_alternatif'] ?>
                            </th>
                            <?php foreach ($value['normalisasi'] as $kunci => $nilai) : ?>
                                <td class="px-6 py-4">
                                    <?= $nilai['nilai_normalisasi'] ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="w-11/12 shadow-md bg-white rounded-md pb-5 self-center mb-10">
        <h2 class="mx-auto my-5 tracking-widest border-b border-quinary text-center">Normalisasi Terbobot Matriks</h2>
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg mx-4">
            <table class="text-center w-full text-sm rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-sm text-white uppercase bg-quinary dark:bg-gray-700 dark:text-gray-400">
                    <tr class="">
                        <th class=""></th>
                        <th scope="col" colspan="<?= $jumlah_kriteria + 1?>" class="px-6 py-3 text-center">
                            Kriteria
                        </th>
                    </tr>
                    <tr>
                        <th scope="col" class="px-6 py-3 ">
                            Alternatif
                        </th>
                        <?php foreach ($kriteria as $key => $value) : ?>
                            <th scope="col" class="px-6 py-3">
                                <?= $value['nama_kriteria'] ?>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($nilai_normalisasi_terbobot as $key => $value) : ?>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <th class="px-6 py-4">
                                <?= $value['nama_alternatif'] ?>
                            </th>
                            <?php foreach ($value['normalisasi_terbobot'] as $kunci => $nilai) : ?>
                                <td class="px-6 py-4">
                                    <?= $nilai['nilai_normalisasi_terbobot'] ?>
                                </td>
                            <?php endforeach; ?>
                            
                        </tr>
                    <?php endforeach; ?>
                    <!-- tampilkan bobot kriteria yang sudah dibagi 100 -->
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                        <th class="px-6 py-4">
                            Bobot Kriteria
                        </th>
                        <?php foreach ($kriteria as $key => $value) : ?>
                            <td class="px-6 py-4">
                                <?= $value['bobot_kriteria'] / 100 ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="w-11/12 shadow-md bg-white rounded-md pb-5 self-center mb-10">
        <h2 class="mx-auto my-5 tracking-widest border-b border-quinary text-center">Nilai Fungsi Optimum</h2>
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg mx-4">
            <table class="text-center w-full text-sm rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-sm text-white uppercase bg-quinary dark:bg-gray-700 dark:text-gray-400">
                    <tr class="">
                        <th class=""></th>
                        <th scope="col" colspan="<?= $jumlah_kriteria + 1?>" class="px-6 py-3 text-center">
                            Kriteria
                        </th>
                    </tr>
                    <tr>
                        <th scope="col" class="px-6 py-3 ">
                            Alternatif
                        </th>
                        <?php foreach ($kriteria as $key => $value) : ?>
                            <th scope="col" class="px-6 py-3">
                                <?= $value['nama_kriteria'] ?>
                            </th>
                        <?php endforeach; ?>
                        <th>Fungsi Optimum (S)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($nilai_normalisasi_terbobot as $key => $value) : ?>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <th class="px-6 py-4">
                                <?= $value['nama_alternatif'] ?>
                            </th>
                            <?php foreach ($value['normalisasi_terbobot'] as $kunci => $nilai) : ?>
                                <td class="px-6 py-4">
                                    <?= $nilai['nilai_normalisasi_terbobot'] ?>
                                </td>
                            <?php endforeach; ?>
                            <td class="px-6 py-4">
                                <?= $nilai_normalisasi_terbobot_sum[$key]['nilai_normalisasi_terbobot_sum'] ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="w-11/12 shadow-md bg-white rounded-md pb-5 self-center mb-10">
        <h2 class="mx-auto my-5 tracking-widest border-b border-quinary text-center">Nilai Utilitas</h2>
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg mx-4">
            <table class="text-center w-full text-sm rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-sm text-white uppercase bg-quinary dark:bg-gray-700 dark:text-gray-400">
                    <tr class="">
                        <th class=""></th>
                        <th scope="col" colspan="<?= $jumlah_kriteria + 2?>" class="px-6 py-3 text-center">
                            Kriteria
                        </th>
                    </tr>
                    <tr>
                        <th scope="col" class="px-6 py-3 ">
                            Alternatif
                        </th>
                        <?php foreach ($kriteria as $key => $value) : ?>
                            <th scope="col" class="px-6 py-3">
                                <?= $value['nama_kriteria'] ?>
                            </th>
                        <?php endforeach; ?>
                        <th>Fungi Optimum (S)</th>
                        <th>Nilai Utilitas (K)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($nilai_normalisasi_terbobot as $key => $value) : ?>
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <th class="px-6 py-4">
                                <?= $value['nama_alternatif'] ?>
                            </th>
                            <?php foreach ($value['normalisasi_terbobot'] as $kunci => $nilai) : ?>
                                <td class="px-6 py-4">
                                    <?= $nilai['nilai_normalisasi_terbobot'] ?>
                                </td>
                            <?php endforeach; ?>
                            <td class="px-6 py-4">
                                <?= $nilai_normalisasi_terbobot_sum[$key]['nilai_normalisasi_terbobot_sum'] ?>
                            </td>
                            <td class="px-6 py-4">
                                <?= $nilai_utilitas[$key]['nilai_utilitas'] ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <!-- tombol kembali -->
        <a href="./perhitungan_admin.php"><button type="button" class="text-white mx-4 mt-4 w-full bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm sm:w-auto px-5 py-2.5 text-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">Kembali</button></a>
        
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
</body>

</html>