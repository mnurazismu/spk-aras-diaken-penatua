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
        document.location.href = "index.php";
    }
    </script>
    ';
    exit;
} else {
    if ($_SESSION['tipe_user'] != 'User') {
        echo '
        <script src="src/jquery-3.6.3.min.js"></script>
        <script src="src/sweetalert2.all.min.js"></script>
        <script>
        $(document).ready(function() {
            Swal.fire({
                position: "top-center",
                icon: "error",
                title: "Anda Login Sebagai Admin!",
                showConfirmButton: false,
                timer: 2000
            })
            setTimeout(myFunction, 2000);
        });
        function myFunction() {
            document.location.href = "beranda_admin.php";
        }
        </script>
        ';
        exit;
    }

    $email = $_SESSION['email'];
    $nama_lengkap = $_SESSION['nama_lengkap'];
    $tipe_user = $_SESSION['tipe_user'];

    $query_kriteria = "SELECT * FROM kriteria";
    $result_kriteria = mysqli_query($conn, $query_kriteria);
    $jumlah_subkriteria = mysqli_num_rows($result_kriteria);
    $kriteria = [];
    while ($row_kriteria = mysqli_fetch_assoc($result_kriteria)) {
        $kriteria[] = $row_kriteria;
    }

    $nama_alternatif = isset($_SESSION['nama_alternatif']) ? $_SESSION['nama_alternatif'] : '';
    $tempat_lahir = isset($_SESSION['tempat_lahir']) ? $_SESSION['tempat_lahir'] : '';
    $tanggal_lahir = isset($_SESSION['tanggal_lahir']) ? $_SESSION['tanggal_lahir'] : '';
    $pendidikan_terakhir = isset($_SESSION['pendidikan_terakhir']) ? $_SESSION['pendidikan_terakhir'] : '';

    if (isset($_SESSION['id_alternatif'])) {
        $id = $_SESSION['id_alternatif'];
        $query = "SELECT * FROM alternatif WHERE id_alternatif = '$id'";
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result);

        $query_nilai = "SELECT * FROM nilai_matriks WHERE id_alternatif = '$id'";
        $result_nilai = mysqli_query($conn, $query_nilai);
        $nilai = [];
        while ($row_nilai = mysqli_fetch_assoc($result_nilai)) {
            $nilai[] = $row_nilai;
        }

        if (isset(($_POST['ubah']))) {
            $id = $_POST['id'];
            $query_kriteria = "SELECT id_kriteria FROM kriteria";
            $result_kriteria = mysqli_query($conn, $query_kriteria);
            $kriteria_id = [];
            while ($row_kriteria = mysqli_fetch_assoc($result_kriteria)) {
                $kriteria_id[] = $row_kriteria;
            }

            foreach ($kriteria_id as $k_id) {
                ${'c' . $k_id['id_kriteria']} = $_POST['c' . $k_id['id_kriteria']];
            }

            $formatted_date = date('Y-m-d', strtotime($tanggal_lahir));

            $query_alternatif = "UPDATE alternatif SET nama_alternatif = '$nama_alternatif', tempat_lahir = '$tempat_lahir', tanggal_lahir = '$formatted_date', pendidikan_terakhir = '$pendidikan_terakhir' WHERE id_alternatif = '$id'";
            $result_alternatif = mysqli_query($conn, $query_alternatif);

            foreach ($kriteria_id as $k_id) {
                $query_update = "UPDATE nilai_matriks SET nilai_matriks = '${'c' .$k_id['id_kriteria']}' WHERE id_alternatif = '$id' AND id_kriteria = '$k_id[id_kriteria]'";
                $result_update = mysqli_query($conn, $query_update);
            }

            if ($result_alternatif) {
                echo '
                <script src="src/jquery-3.6.3.min.js"></script>
                <script src="src/sweetalert2.all.min.js"></script>
                <script>
                $(document).ready(function() {
                    Swal.fire({
                        position: "top-center",
                        icon: "success",
                        title: "Berhasil Mengubah Alternatif!",
                        showConfirmButton: false,
                        timer: 2000
                    })
                    setTimeout(myFunction, 2000);
                });
                function myFunction() {
                    document.location.href = "alternatif_user.php";
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
                        title: "Gagal Mengubah Alternatif!",
                        showConfirmButton: false,
                        timer: 2000
                    })
                    setTimeout(myFunction, 2000);
                });
                function myFunction() {
                    document.location.href = "ubah_alternatif_user_2.php";
                }
                </script>
                ';
                exit;
            }
        }
    } else {
        echo '
        <script src="src/jquery-3.6.3.min.js"></script>
        <script src="src/sweetalert2.all.min.js"></script>
        <script>
        $(document).ready(function() {
            Swal.fire({
                position: "top-center",
                icon: "error",
                title: "ID Alternatif Tidak Ditemukan!",
                showConfirmButton: false,
                timer: 2000
            })
            setTimeout(myFunction, 2000);
        });
        function myFunction() {
            document.location.href = "alternatif_user.php";
        }
        </script>
        ';
        exit;
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
    <title>Ubah Alternatif</title>
</head>

<body class="font-['Inter'] bg-gradient-to-br from-primary via-secondary to-quinary flex min-h-screen justify-center items-center">
    <div class="w-1/2 shadow-md bg-gray-100 rounded-md py-12 my-12">
        <form class="max-w-2xl mx-auto lg:min-w-96" action="" method="post">
            <h1 class="text-2xl text-center font-extrabold tracking-tight leading-none text-quinary dark:text-white">Tambah Alternatif</h1>
            <hr>
            <hr>
            <h3 class="mb-8 mt-2 text-sm">Silahkan lengkapi form berikut ini untuk melanjutkan tambah alternatif.</h3>
            <input type="hidden" name="id" value=<?= $id ?>>
            <div class="mb-5">
                <label for="nama_alternatif" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Lengkap</label>
                <div class="relative">
                    <input disabled value="<?= $nama_alternatif ?>" type="text" id="nama_alternatif" name="nama_alternatif" class="bg-gray-50 border border-gray-300 text-gray-400 text-sm rounded-lg focus:ring-rose-800 focus:border-rose-800 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-rose-800 dark:focus:border-rose-800" placeholder="Nama Lengkap" required autocomplete="off" />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    </div>
                </div>
            </div>
            <div class="mb-5">
                <label for="tempat_lahir" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tempat Lahir</label>
                <div class="relative">
                    <input disabled value="<?= $tempat_lahir ?>" type="text" id="tempat_lahir" name="tempat_lahir" class="bg-gray-50 border border-gray-300 text-gray-400 text-sm rounded-lg focus:ring-rose-800 focus:border-rose-800 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-rose-800 dark:focus:border-rose-800" placeholder="Tempat Lahir" required autocomplete="off" />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    </div>
                </div>
            </div>
            <div class="mb-5">
                <label for="tanggal_lahir" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tanggal Lahir</label>
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3.5 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                        </svg>
                    </div>
                    <input disabled value="<?= $tanggal_lahir ?>" datepicker datepicker-format="dd-mm-yyyy" id="tanggal_lahir" name="tanggal_lahir" type="text" class="bg-gray-50 border border-gray-300 text-gray-400 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Pilih Tanggal" autocomplete="off">
                </div>
            </div>
            <div class="mb-5">
                <label for="pendidikan_terakhir" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pendidikan Terakhir</label>
                <div class="relative">
                    <input disabled value="<?= $pendidikan_terakhir ?>" type="text" id="pendidikan_terakhir" name="pendidikan_terakhir" class="bg-gray-50 border border-gray-300 text-gray-400 text-sm rounded-lg focus:ring-rose-800 focus:border-rose-800 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-rose-800 dark:focus:border-rose-800" placeholder="Pendidikan Terakhir" required autocomplete="off" />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    </div>
                </div>
            </div>
            <div class="mx-auto mb-4">
                <?php foreach ($kriteria as $key) : ?>
                    <div class="flex flex-col w-full">
                        <table>
                            <tr class="w-full">
                                <td class="w-1/2 text-left py-3">
                                    <label for="c<?= $key['id_kriteria'] ?>" class="font-semibold">
                                        <?= $key['nama_kriteria'] ?>
                                    </label>
                                </td>
                                <td class="w-1/2">
                                    <select name="c<?= $key['id_kriteria'] ?>" id="c<?= $key['id_kriteria'] ?>" class="bg-gray-50 font-semibold border border-gray-300 text-sm rounded-lg focus:outline-offset-4 focus:outline-secondary block w-full p-2.5 " required>
                                        <option value="" disabled selected class="text-center">Pilih</option>
                                        <?php
                                        $query_subkriteria = "SELECT * FROM subkriteria WHERE id_kriteria = " . $key['id_kriteria'];
                                        $result_subkriteria = mysqli_query($conn, $query_subkriteria);

                                        if (mysqli_num_rows($result_subkriteria) > 0) {
                                            while ($row_subkriteria = mysqli_fetch_assoc($result_subkriteria)) {
                                                echo '<option value="' . $row_subkriteria['nilai_sub'] . '"';

                                                // if (isset($nilai[$key['id_kriteria'] - 1]) && $nilai[$key['id_kriteria'] - 1]['nilai_matriks'] == $row_subkriteria['nilai_sub']) {
                                                //     echo ' selected';
                                                // }
                                                // cara di atas tidak bisa karena id_kriteria tidak berurutan
                                                foreach ($nilai as $n) {
                                                    if ($n['id_kriteria'] == $key['id_kriteria'] && $n['nilai_matriks'] == $row_subkriteria['nilai_sub']) {
                                                        echo ' selected';
                                                    }
                                                }
                                                echo '>' . $row_subkriteria['nama_sub'] . '</option>';
                                            }
                                        } else {
                                            echo '<option value="" disabled>Tidak Ada Subkriteria</option>';
                                        }
                                        ?>

                                    </select>
                                </td>
                            </tr>
                        </table>
                    </div>
                <?php endforeach; ?>
            </div>
            <div>
                <button type="submit" name="ubah" class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Ubah</button>
                <a href="./ubah_alternatif_user.php"><button type="button" class="text-black bg-primary hover:bg-secondary focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">Kembali</button></a>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.4.1/dist/flowbite.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const datepickerEl = document.getElementById('default-datepicker');
            new Flowbite.datepicker(datepickerEl);
        });
    </script>

</body>

</html>