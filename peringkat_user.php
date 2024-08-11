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

    // ambil hasil dari tabel hasil dan nama alternatif yang id_alternatif nya ada di tabel hasil
    $sql = "SELECT * FROM hasil JOIN alternatif ON hasil.id_alternatif = alternatif.id_alternatif ORDER BY hasil.nilai_hasil DESC";

    // buang data yang nama_alternatifnya duplikat
    $result = mysqli_query($conn, $sql);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    $data = array_map("unserialize", array_unique(array_map("serialize", $data)));
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
    <title>Peringkat | SPK Aras</title>
</head>

<body class="font-['Inter']">
    <header>
        <?php include 'navbar_user.php'; ?>
    </header>

    <aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700" aria-label="Sidebar">
        <?php include 'sidebar_user.php'; ?>
    </aside>

    <div class="p-4 sm:ml-64">
        <div class="p-4 border border-primary shadow-lg rounded-lg dark:border-gray-700 mt-14">
            <div class="flex justify-between items-center">
                <p class="text-gray-400 text-base dark:text-gray-400"><?php echo date('d F Y'); ?></p>
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Data Peringkat</h1>
                </div>
                <div class="p-4 text-sm rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400" role="alert">
                    <p class="font-medium"><span class="text-rose-800"><?= $nama_lengkap; ?></span></p>
                </div>
            </div>
            <div class="bg-primary p-4 rounded-lg mt-8 flex flex-col justify-around flex-wrap gap-4">
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
                                <th scope="col" class="px-6 py-3">
                                    Nilai Utilitas (K)
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Peringkat
                                </th>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                            <?php
                            $no = 1;
                            foreach ($data as $row) {
                                echo '
                                <tr class="bg-white dark:bg-gray-800">
                                    <td class="px-6 py-4 whitespace-nowrap border-r border-quinary">
                                        <div class="text-sm text-gray-900 dark:text-gray-400">' . $no . '</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap border-r border-quinary">
                                        <div class="text-sm text-gray-900 dark:text-gray-400">' . $row['nama_alternatif'] . '</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap border-r border-quinary">
                                        <div class="text-sm text-gray-900 dark:text-gray-400">' . $row['nilai_hasil'] . '</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-400">' . $no . '</div>
                                    </td>
                                </tr>
                                ';
                                $no++;
                            }
                            ?>
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