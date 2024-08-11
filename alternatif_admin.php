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

    // make search work
    if (isset($_GET['search'])) {
        $search = $_GET['search'];
        $queryKriteria = "SELECT * FROM kriteria WHERE nama_kriteria LIKE '%$search%'";
        $resultKriteria = mysqli_query($conn, $queryKriteria);
        $kriteria = [];
        while ($row = mysqli_fetch_assoc($resultKriteria)) {
            $kriteria[] = $row;
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
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Data Alternatif</h1>
                </div>
                <div class="p-4 text-sm rounded-lg bg-blue-50 dark:bg-gray-800 dark:text-blue-400" role="alert">
                    <p class="font-medium"><span class="text-rose-800"><?= $nama_lengkap; ?></span></p>
                </div>
            </div>
            <div class="bg-primary p-4 rounded-lg mt-8 flex flex-col justify-around flex-wrap gap-4">
                <div class="flex justify-between items-center">

                    <form class="w-1/4">
                        <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only dark:text-white">Search</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                </svg>
                            </div>
                            <input type="search" id="default-search" class="block w-full p-4 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-green-500 dark:focus:border-green-500" placeholder="Search" required onkeyup="search()" />
                            <button type="submit" class="text-white absolute end-2.5 bottom-2.5 bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Search</button>
                        </div>
                    </form>
                    <div class="flex justify-end">
                        <a href="tambah_alternatif_admin.php"><button type="button" class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center flex items-center dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4 mr-1" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M12 3.75a.75.75 0 0 1 .75.75v6.75h6.75a.75.75 0 0 1 0 1.5h-6.75v6.75a.75.75 0 0 1-1.5 0v-6.75H4.5a.75.75 0 0 1 0-1.5h6.75V4.5a.75.75 0 0 1 .75-.75Z" clip-rule="evenodd" />
                                </svg>
                                Tambah
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
                                <th scope="col" class="px-6 py-3">
                                    Aksi
                                </th>
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
                                    <td class="px-6 py-4">
                                        <a href="./ubah_alternatif_admin.php?id_alternatif=<?= $value['id_alternatif'] ?>"><button type="button" class="text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm p-2 text-center inline-flex items-center me-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                                                    <path d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 0 0 0-3.712ZM19.513 8.199l-3.712-3.712-8.4 8.4a5.25 5.25 0 0 0-1.32 2.214l-.8 2.685a.75.75 0 0 0 .933.933l2.685-.8a5.25 5.25 0 0 0 2.214-1.32l8.4-8.4Z" />
                                                    <path d="M5.25 5.25a3 3 0 0 0-3 3v10.5a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3V13.5a.75.75 0 0 0-1.5 0v5.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5V8.25a1.5 1.5 0 0 1 1.5-1.5h5.25a.75.75 0 0 0 0-1.5H5.25Z" />
                                                </svg>
                                            </button></a>
                                        <a href="javascript:void(0)" onclick="showConfirmationModalDelete()"><button type="button" class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm p-2 text-center inline-flex items-center me-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                                                    <path fill-rule="evenodd" d="M16.5 4.478v.227a48.816 48.816 0 0 1 3.878.512.75.75 0 1 1-.256 1.478l-.209-.035-1.005 13.07a3 3 0 0 1-2.991 2.77H8.084a3 3 0 0 1-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 0 1-.256-1.478A48.567 48.567 0 0 1 7.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 52.662 0 0 1 3.369 0c1.603.051 2.815 1.387 2.815 2.951Zm-6.136-1.452a51.196 51.196 0 0 1 3.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 0 0-6 0v-.113c0-.794.609-1.428 1.364-1.452Zm-.355 5.945a.75.75 0 1 0-1.5.058l.347 9a.75.75 0 1 0 1.499-.058l-.346-9Zm5.48.058a.75.75 0 1 0-1.498-.058l-.347 9a.75.75 0 0 0 1.5.058l.345-9Z" clip-rule="evenodd" />
                                                </svg>
                                            </button></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div id="confirmationModalDelete" class="fixed top-0 left-0 w-full h-full bg-gray-800 bg-opacity-50 z-50 items-center justify-center hidden">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <button onclick="deleteItem(false)" type="button" class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="popup-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
                <div class="p-4 md:p-5 text-center">
                    <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Apakah anda yakin ingin menghapus alternatif ini?</h3>
                    <button onclick="deleteItem(true)" data-modal-hide="popup-modal" type="button" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                        Yakin
                    </button>
                    <button onclick="deleteItem(false)" data-modal-hide="popup-modal" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Batal</button>
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
        function search() {
            // Ambil nilai input pencarian
            var input = document.getElementById("default-search");
            var filter = input.value.toUpperCase();
            var table = document.getElementById("table-body");
            var tr = table.getElementsByTagName("tr");

            // Loop semua baris dan sembunyikan yang tidak cocok dengan filter
            for (var i = 0; i < tr.length; i++) {
                var tdNamaKriteria = tr[i].getElementsByTagName("td")[0]; // Ganti dengan indeks kolom yang sesuai untuk pencarian nama kriteria
                if (tdNamaKriteria) {
                    var textValue = tdNamaKriteria.textContent || tdNamaKriteria.innerText;
                    if (textValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }

        function closeModal() {
            document.getElementById("confirmationModal").style.display = "none";
        }

        // JavaScript function to handle confirmation dialog
        function showConfirmationModalDelete() {
            document.getElementById("confirmationModalDelete").style.display = "flex";
        }

        // Function to delete item based on user's choice
        function deleteItem(confirmDelete) {
            if (confirmDelete) {
                // Perform deletion action here
                // For example, redirect to delete page or submit a form
                // Replace 'deleteItem.php' with your actual delete action page
                window.location.href = "./hapus_alternatif.php?id=<?= $value['id_alternatif'] ?>";
            } else {
                // Hide confirmation modal if user cancels
                document.getElementById("confirmationModalDelete").style.display = "none";
            }
        }
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