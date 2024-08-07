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

    $queryKriteria = "SELECT * FROM kriteria";
    $resultKriteria = mysqli_query($conn, $queryKriteria);
    $kriteria = [];
    while ($row = mysqli_fetch_assoc($resultKriteria)) {
        $kriteria[] = $row;
    }

    $queryAlternatif = "SELECT * FROM alternatif";
    $resultAlternatif = mysqli_query($conn, $queryAlternatif);
    $alternatif = [];
    while ($row = mysqli_fetch_assoc($resultAlternatif)) {
        $alternatif[] = $row;
    }

    if (isset($_POST['hitung'])) {
        header('Location: perhitungan_aras_admin.php');
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
    <title>Data Alternatif | SPK Aras</title>
</head>

<body class="font-['Inter']">
    <header>
        <?php include 'navbar_admin.php'; ?>
    </header>

    <aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700" aria-label="Sidebar">
        <?php include 'sidebar_admin.php'; ?>
    </aside>

    <div class="p-4 sm:ml-64">
        <div class="p-4 border border-primary shadow-lg rounded-lg dark:border-gray-700 mt-14">
            <div class="flex justify-between items-center">
                <p class="text-gray-400 text-base dark:text-gray-400"><?php echo date('d F Y'); ?></p>
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Data Perhitungan</h1>
                </div>
                <div class="p-4 text-sm rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400" role="alert">
                    <p class="font-medium"><span class="text-rose-800"><?= $nama_lengkap; ?></span></p>
                </div>
            </div>
            <div class="bg-primary p-4 rounded-lg mt-8 flex flex-col justify-around flex-wrap gap-4">
                <div class="flex justify-center items-center">

                    <div class="flex justify-end">
                        <a href="perhitungan_aras_admin.php"><button type="button" class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center flex items-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 15.75V18m-7.5-6.75h.008v.008H8.25v-.008Zm0 2.25h.008v.008H8.25V13.5Zm0 2.25h.008v.008H8.25v-.008Zm0 2.25h.008v.008H8.25V18Zm2.498-6.75h.007v.008h-.007v-.008Zm0 2.25h.007v.008h-.007V13.5Zm0 2.25h.007v.008h-.007v-.008Zm0 2.25h.007v.008h-.007V18Zm2.504-6.75h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V13.5Zm0 2.25h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V18Zm2.498-6.75h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V13.5ZM8.25 6h7.5v2.25h-7.5V6ZM12 2.25c-1.892 0-3.758.11-5.593.322C5.307 2.7 4.5 3.65 4.5 4.757V19.5a2.25 2.25 0 0 0 2.25 2.25h10.5a2.25 2.25 0 0 0 2.25-2.25V4.757c0-1.108-.806-2.057-1.907-2.185A48.507 48.507 0 0 0 12 2.25Z" />
                                </svg>
                                Hitung ARAS
                            </button></a>
                    </div>
                </div>
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                    <table class="w-full text-sm text-center rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-sm text-white uppercase bg-quinary dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">
                                    No
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Nama Alternatif
                                </th>
                                <?php foreach ($kriteria as $key => $value) : ?>
                                    <th scope="col" class="px-6 py-3">
                                        C<?= $key + 1 ?>
                                    </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                            <?php foreach ($alternatif as $key => $value) : ?>
                                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                    <th class="px-6 py-4 border-r border-quinary">
                                        <?= $key + 1 ?>
                                    </th>
                                    <?php
                                    $id_alternatif = $value['id_alternatif'];
                                    $query_matriks = "SELECT * FROM nilai_matriks WHERE id_alternatif = '$id_alternatif'";
                                    $result_matriks = mysqli_query($conn, $query_matriks);
                                    $nilai_matriks = [];
                                    while ($row = mysqli_fetch_assoc($result_matriks)) {
                                        $nilai_matriks[] = $row;
                                    }
                                    ?>
                                    <td class="px-6 py-4 border-r border-quinary">
                                        <?= $value['nama_alternatif'] ?>
                                    </td>
                                    <?php foreach ($nilai_matriks as $key => $value) : ?>
                                        <td class="px-6 py-4 border-r border-quinary">
                                            <?= $value['nilai_matriks'] ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Konfirmasi Logout -->
    <div id="confirmationModalLogout" class="fixed inset-0 items-center justify-center bg-gray-800 bg-opacity-50 z-50 hidden">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <button onclick="logout(false)" type="button" class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
                <div class="p-4 md:p-5 text-center">
                    <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Apakah anda yakin ingin logout?</h3>
                    <button onclick="logout(true)" type="button" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                        Yakin
                    </button>
                    <button onclick="logout(false)" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Batal</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <script type="text/javascript">
        // JavaScript function to handle confirmation dialog
        function showConfirmationModalLogout() {
            document.getElementById("confirmationModalLogout").style.display = "flex";
        }

        // Function to logout
        function logout(confirmDelete) {
            if (confirmDelete) {
                window.location.href = "./logout_admin.php";
            } else {
                document.getElementById("confirmationModalLogout").style.display = "none";
            }
        }
    </script>
</body>

</html>