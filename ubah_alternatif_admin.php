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

    if (isset($_GET['id_alternatif'])) {
        $id = $_GET['id_alternatif'];
        $query = "SELECT * FROM alternatif WHERE id_alternatif = '$id'";
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result);
        $tanggal_lahir_formatted = date('d-m-Y', strtotime($row['tanggal_lahir']));

        if (isset($_POST['selanjutnya'])) {
            $nama_alternatif = $_POST['nama_alternatif'];
            $tempat_lahir = $_POST['tempat_lahir'];
            $tanggal_lahir = $_POST['tanggal_lahir'];
            $pendidikan_terakhir = $_POST['pendidikan_terakhir'];

            $_SESSION['id_alternatif'] = $id;
            $_SESSION['nama_alternatif'] = $nama_alternatif;
            $_SESSION['tempat_lahir'] = $tempat_lahir;
            $_SESSION['tanggal_lahir'] = $tanggal_lahir;
            $_SESSION['pendidikan_terakhir'] = $pendidikan_terakhir;
            header('Location: ubah_alternatif_admin_2.php');
            exit;
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
            document.location.href = "alternatif_admin.php";
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
    <div class="w-1/2 shadow-md bg-gray-100 rounded-md py-12">
        <form class="max-w-2xl mx-auto lg:min-w-96" action="" method="post">
            <h1 class="text-2xl text-center font-extrabold tracking-tight leading-none text-quinary dark:text-white">Tambah Alternatif</h1>
            <hr>
            <hr>
            <h3 class="mb-8 mt-2 text-sm">Silahkan lengkapi form berikut ini untuk melanjutkan tambah alternatif.</h3>
            <div class="mb-5">
                <label for="nama_alternatif" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Lengkap</label>
                <div class="relative">
                    <input value="<?= $row['nama_alternatif'] ?>" type="text" id="nama_alternatif" name="nama_alternatif" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-rose-800 focus:border-rose-800 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-rose-800 dark:focus:border-rose-800" placeholder="Nama Lengkap" required autocomplete="off" />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    </div>
                </div>
            </div>
            <div class="mb-5">
                <label for="tempat_lahir" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tempat Lahir</label>
                <div class="relative">
                    <input value="<?= $row['tempat_lahir'] ?>" type="text" id="tempat_lahir" name="tempat_lahir" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-rose-800 focus:border-rose-800 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-rose-800 dark:focus:border-rose-800" placeholder="Tempat Lahir" required autocomplete="off" />
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
                    <input value="<?= $tanggal_lahir_formatted ?>" datepicker datepicker-format="dd-mm-yyyy" id="tanggal_lahir" name="tanggal_lahir" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Pilih Tanggal" autocomplete="off">
                </div>
            </div>
            <div class="mb-5">
                <label for="pendidikan_terakhir" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pendidikan Terakhir</label>
                <div class="relative">
                    <input value="<?= $row['pendidikan_terakhir'] ?>" type="text" id="pendidikan_terakhir" name="pendidikan_terakhir" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-rose-800 focus:border-rose-800 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-rose-800 dark:focus:border-rose-800" placeholder="Pendidikan Terakhir" required autocomplete="off" />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    </div>
                </div>
            </div>
            <div>
                <button type="submit" name="selanjutnya" class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Selanjutnya</button>
                <a href="./alternatif_admin.php"><button type="button" class="text-black bg-primary hover:bg-secondary focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">Kembali</button></a>
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