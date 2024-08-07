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
                title: "Anda Login Sebagai User!",
                showConfirmButton: false,
                timer: 2000
            })
            setTimeout(myFunction, 2000);
        });
        function myFunction() {
            document.location.href = "beranda_user.php";
        }
        </script>
        ';
        exit;
    }

    $email = $_SESSION['email'];
    $nama_lengkap = $_SESSION['nama_lengkap'];
    $tipe_user = $_SESSION['tipe_user'];

    $nama_kriteria = isset($_SESSION['nama_kriteria']) ? $_SESSION['nama_kriteria'] : '';
    $bobot_kriteria = isset($_SESSION['bobot_kriteria']) ? $_SESSION['bobot_kriteria'] : '';
    $jenis_kriteria = isset($_SESSION['jenis_kriteria']) ? $_SESSION['jenis_kriteria'] : '';
    $jumlah_subkriteria = isset($_SESSION['jumlah_subkriteria']) ? $_SESSION['jumlah_subkriteria'] : '';

    if (isset($_POST['tambah'])) {
        $nama_kriteria = $_SESSION['nama_kriteria'];
        $bobot_kriteria = $_SESSION['bobot_kriteria'];
        $jenis_kriteria = $_SESSION['jenis_kriteria'];
        $subkriteria = [];

        for ($i = 1; $i <= $jumlah_subkriteria; $i++) {
            $nilai_subkriteria = $_POST['nilai_subkriteria_' . $i];

            // buat validasi jika ada nilai subkriteria yang sama
            if (!is_numeric($nilai_subkriteria)) {
                echo '
                <script src="src/jquery-3.6.3.min.js"></script>
                <script src="src/sweetalert2.all.min.js"></script>
                <script>
                $(document).ready(function() {
                    Swal.fire({
                        position: "top-center",
                        icon: "error",
                        title: "Nilai Sub Kriteria Tidak Boleh Sama!",
                        showConfirmButton: false,
                        timer: 2000
                    })
                    setTimeout(myFunction, 2000);
                });
                function myFunction() {
                    document.location.href = "tambah_subkriteria.php";
                }
                </script>
                ';
                exit;
            }

            // buat validasi jika ada nilai subkriteria yang kosong
            if ($nilai_subkriteria === '') {
                echo '
                <script src="src/jquery-3.6.3.min.js"></script>
                <script src="src/sweetalert2.all.min.js"></script>
                <script>
                $(document).ready(function() {
                    Swal.fire({
                        position: "top-center",
                        icon: "error",
                        title: "Nilai Sub Kriteria Tidak Boleh Kosong!",
                        showConfirmButton: false,
                        timer: 2000
                    })
                    setTimeout(myFunction, 2000);
                });
                function myFunction() {
                    document.location.href = "tambah_subkriteria.php";
                }
                </script>
                ';
                exit;
            }

            $nama_subkriteria = $_POST['subkriteria_' . $i];
            $subkriteria[$i] = array(
                'nama_subkriteria' => $nama_subkriteria,
                'nilai_subkriteria' => $nilai_subkriteria
            );
        }

        $query = "INSERT INTO kriteria (nama_kriteria, bobot_kriteria, jenis_kriteria) VALUES ('$nama_kriteria', '$bobot_kriteria', '$jenis_kriteria')";
        $result = mysqli_query($conn, $query);

        $kriteria_id = mysqli_insert_id($conn);

        foreach ($subkriteria as $sub) {
            $nama_subkriteria = $sub['nama_subkriteria'];
            $nilai_subkriteria = $sub['nilai_subkriteria'];

            $query = "INSERT INTO subkriteria (id_kriteria, nama_sub, nilai_sub) VALUES ('$kriteria_id', '$nama_subkriteria', '$nilai_subkriteria')";
            $result = mysqli_query($conn, $query);
        }

        if ($result) {
            $select_alternatif = "SELECT * FROM alternatif";
            $result_alternatif = mysqli_query($conn, $select_alternatif);
            while ($row = mysqli_fetch_assoc($result_alternatif)) {
                $id_alternatif = $row['id_alternatif'];
                $query = "INSERT INTO nilai_matriks (id_alternatif, id_kriteria, nilai_matriks) VALUES ('$id_alternatif', '$kriteria_id', 0)";
                $result = mysqli_query($conn, $query);
            }
            $_SESSION['jumlah_subkriteria'] = null;

            echo '
            <script src="src/jquery-3.6.3.min.js"></script>
            <script src="src/sweetalert2.all.min.js"></script>
            <script>
            $(document).ready(function() {
                Swal.fire({
                    position: "top-center",
                    icon: "success",
                    title: "Data Berhasil Ditambahkan!",
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
        } else {
            echo '
            <script src="src/jquery-3.6.3.min.js"></script>
            <script src="src/sweetalert2.all.min.js"></script>
            <script>
            $(document).ready(function() {
                Swal.fire({
                    position: "top-center",
                    icon: "error",
                    title: "Data Gagal Ditambahkan!",
                    showConfirmButton: false,
                    timer: 2000
                })
                setTimeout(myFunction, 2000);
            });
            function myFunction() {
                document.location.href = "tambah_subkriteria.php";
            }
            </script>
            ';
            exit;
        }
    }
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
    <title>Tambah Sub Kriteria</title>
</head>

<body class="font-['Inter'] bg-gradient-to-br from-primary via-secondary to-quinary flex min-h-screen justify-center items-center">
    <div class="w-1/2 shadow-md bg-gray-100 rounded-md py-12 my-12">
        <form class="max-w-2xl mx-auto lg:min-w-full px-12" action="" method="post">
            <h1 class="text-2xl text-center font-extrabold tracking-tight leading-none text-quinary dark:text-white text-shadow">Form Tambah Sub Kriteria</h1>
            <hr>
            <hr>
            <h3 class="mb-8 mt-2 text-xm">Silahkan lengkapi form berikut ini untuk melanjutkan tambah kriteria dan sub kriteria.</h3>
            <div class="mb-5">
                <label for="nama_kriteria" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Kriteria</label>
                <div class="relative">
                    <input disabled value="<?= $nama_kriteria ?>" type="text" id="nama_kriteria" name="nama_kriteria" class="bg-gray-50 border border-gray-300 text-gray-400 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Nama Kriteria" required autocomplete="off" />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    </div>
                </div>
            </div>
            <div class="mb-5">
                <label for="bobot_kriteria" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Bobot Kriteria</label>
                <div class="relative">
                    <input disabled value="<?= $bobot_kriteria ?>" type="number" id="bobot_kriteria" name="bobot_kriteria" class="bg-gray-50 border border-gray-300 text-gray-400 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Bobot Kriteria" required autocomplete="off" />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    </div>
                </div>
            </div>
            <div class="mb-5">
                <label for="jenis_kriteria" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Jenis Kriteria</label>
                <div class="relative">
                    <input disabled value="<?= $jenis_kriteria ?>" type="text" id="jenis_kriteria" name="jenis_kriteria" class="bg-gray-50 border border-gray-300 text-gray-400 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Jenis Kriteria" required autocomplete="off" />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    </div>
                </div>
            </div>
            <div id="subkriteria-container" class="flex flex-col items-center">
                <?php
                // Generate input fields for subcriteria based on the session value
                for ($i = 1; $i <= $jumlah_subkriteria; $i++) {
                    echo '
                                    <div class="flex flex-col border border-green-400 px-4 py-2 rounded-lg items-center mb-4 w-full">
                                    <h3 class="text-center font-semibold mb-2">Sub Kriteria ' . $i . '</h3>
                                    <div class="flex items-center mb-4 w-full">
                                        <label for="subkriteria_' . $i . '" class="w-2/6 font-semibold">Nama Sub Kriteria ' . $i . ' :</label>
                                        <input name="subkriteria_' . $i . '" id="subkriteria_' . $i . '" placeholder="Sub Kriteria ' . $i . '" required type="text" autocomplete="off" class="w-4/6 px-4 py-2 h-11 border border-green-400 rounded-lg focus:outline-offset-4 focus:outline-secondary">
                                    </div>
                                    <div class="flex flex-col w-full">
                                        <table>
                                            <tr class="w-full">
                                                <td class="w-2/3 text-left py-3">
                                                    <label for="nilai_subkriteria_' . $i . '" class="font-semibold">Nilai Sub Kriteria ' . $i . ' :</label>
                                                </td>
                                                <td class="w-1/3">
                                                    <select name="nilai_subkriteria_' . $i . '" id="nilai_subkriteria_' . $i . '" class="bg-quarternary font-semibold border border-green-400 text-sm rounded-lg focus:outline-offset-4 focus:outline-secondary block w-full p-2.5 " required>
                                                    <option value="" disabled selected class="text-center">Pilih</option>';

                    // Generate options for the select based on the number of subcriteria
                    for ($j = 1; $j <= $jumlah_subkriteria; $j++) {
                        echo '<option value="' . $j . '" class="text-center"> ' . $j . '</option>';
                    }
                    echo '
                                                    </select>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    </div>
                                ';
                }
                ?>
            </div>
            <div>
                <button type="submit" name="tambah" class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Tambah</button>
                <a href="./tambah_kriteria.php"><button type="button" class="text-black bg-primary hover:bg-secondary focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">Kembali</button></a>
            </div>
        </form>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function() {
            // Disable selected options in other selects
            const selects = document.querySelectorAll('select');
            selects.forEach((select, index) => {
                select.addEventListener('change', function() {
                    const selectedValue = this.value;
                    for (let i = index + 1; i < selects.length; i++) {
                        const options = selects[i].querySelectorAll('option');
                        options.forEach(option => {
                            if (option.value === selectedValue) {
                                option.disabled = true;
                            }
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>