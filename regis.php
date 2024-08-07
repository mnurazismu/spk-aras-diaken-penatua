<?php
require 'env.php';

if (isset($_POST['daftar'])) {
    $nama_lengkap = $_POST['nama_lengkap'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $konfirmasi_password = $_POST['konfirmasi_password'];
    $tipe_user = 'User';

    if ($password != $konfirmasi_password) {
        echo '
            <script src="src/jquery-3.6.3.min.js"></script>
            <script src="src/sweetalert2.all.min.js"></script>
            <script>
            $(document).ready(function() {
                Swal.fire({
                    position: "top-center",
                    icon: "error",
                    title: "Daftar Akun Gagal!",
                    text: "Password tidak sama!",
                    showConfirmButton: false,
                    timer: 2000
                })
                setTimeout(myFunction, 2000);
            });
            function myFunction() {
                document.location.href = "regis.php";
            }
            </script>
            ';
    } else {
        $cek_email = mysqli_query($conn, "SELECT * FROM user WHERE email = '$email'");
        if (mysqli_num_rows($cek_email) == 0) {
            $password = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO user (nama_lengkap, email, password, tipe_user) VALUES ('$nama_lengkap', '$email', '$password', '$tipe_user')";
            if (mysqli_query($conn, $query)) {
                echo '
                    <script src="src/jquery-3.6.3.min.js"></script>
                    <script src="src/sweetalert2.all.min.js"></script>
                    <script>
                    $(document).ready(function() {
                        Swal.fire({
                            position: "top-center",
                            icon: "success",
                            title: "Daftar Akun Berhasil!",
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
            } else {
                echo '
                    <script src="src/jquery-3.6.3.min.js"></script>
                    <script src="src/sweetalert2.all.min.js"></script>
                    <script>
                    $(document).ready(function() {
                        Swal.fire({
                            position: "top-center",
                            icon: "error",
                            title: "Daftar Akun Gagal!",
                            text: "Silahkan Coba Lagi",
                            showConfirmButton: false,
                            timer: 2000
                        })
                        setTimeout(myFunction, 2000);
                    });
                    function myFunction() {
                        document.location.href = "regis.php";
                    }
                    </script>
                    ';
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
                        title: "Daftar Akun Gagal!",
                        text: "Email sudah terdaftar!",
                        showConfirmButton: false,
                        timer: 2000
                    })
                    setTimeout(myFunction, 2000);
                });
                function myFunction() {
                    document.location.href = "regis.php";
                }
                </script>
                ';
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
    <title>Register | SPK ARAS</title>
</head>

<body class="font-['Inter']">
    <main class="min-h-screen flex flex-col col-re lg:flex-row-reverse">
        <section class="cursor-pointer flex lg:w-1/2 dark:bg-gray-900 bg-gray-50">
            <a href="./index.php" class="py-5 px-6 min-h-96 bg-gradient-to-b from-primary to-secondary border border-gray-100 shadow rounded-lg m-auto flex flex-col gap-6 justify-center items-center">
                <h3 class="font-semibold text-lg text-quinary tracking-wider">Sistem Pendukung Keputusan</h3>
                <h3 class="font-bold text-2xl">Pemilihan Calon Diaken Penatua</h3>
                <h3 class="text-quaternary">GPIB IMMANUEL SAMARINDA SEKTOR KAISERA</h3>
            </a>
        </section>
        <section class="lg:w-1/2 flex flex-col items-center justify-center bg-gradient-to-b from-primary to-secondary">
            <div class="w-full max-w-sm p-4 bg-gray-50 border border-gray-200 rounded-lg shadow sm:p-6 md:p-8 dark:bg-gray-800 dark:border-gray-700">
                <form class="space-y-4" action="" method="post">
                    <h5 class="text-xl text-center font-medium bg-clip-text text-transparent bg-gradient-to-r from-tertiary to-quinary dark:text-white">Silahkan Registrasi</h5>
                    <div>
                        <label for="nama_lengkap" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Lengkap</label>
                        <div class="relative">
                            <input type="text" id="nama_lengkap" name="nama_lengkap" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-rose-800 focus:border-rose-800 block w-full p-2.5 pl-10 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Nama Lengkap" required autocomplete="off" />
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-gray-400">
                                    <path fill-rule="evenodd" d="M18.685 19.097A9.723 9.723 0 0 0 21.75 12c0-5.385-4.365-9.75-9.75-9.75S2.25 6.615 2.25 12a9.723 9.723 0 0 0 3.065 7.097A9.716 9.716 0 0 0 12 21.75a9.716 9.716 0 0 0 6.685-2.653Zm-12.54-1.285A7.486 7.486 0 0 1 12 15a7.486 7.486 0 0 1 5.855 2.812A8.224 8.224 0 0 1 12 20.25a8.224 8.224 0 0 1-5.855-2.438ZM15.75 9a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" clip-rule="evenodd" />
                                </svg>

                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
                        <div class="relative">
                            <input type="text" id="email" name="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-rose-700 focus:border-rose-700 block w-full p-2.5 pl-10 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Masukan email" required autocomplete="off" />
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-gray-400">
                                    <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password</label>
                        <div class="relative">
                            <input type="password" id="password" name="password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-rose-800 focus:border-rose-800 block w-full p-2.5 pl-10 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Masukan password" required autocomplete="off" />
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-gray-400">
                                    <path fill-rule="evenodd" d="M12 1.5a5.25 5.25 0 0 0-5.25 5.25v3a3 3 0 0 0-3 3v6.75a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3v-6.75a3 3 0 0 0-3-3v-3c0-2.9-2.35-5.25-5.25-5.25Zm3.75 8.25v-3a3.75 3.75 0 1 0-7.5 0v3h7.5Z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="konfirmasi_password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Konfirmasi Password</label>
                        <div class="relative">
                            <input type="password" id="konfirmasi_password" name="konfirmasi_password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-rose-800 focus:border-rose-800 block w-full p-2.5 pl-10 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Konfirmasi Password" required autocomplete="off" />
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-gray-400">
                                    <path fill-rule="evenodd" d="M12 1.5a5.25 5.25 0 0 0-5.25 5.25v3a3 3 0 0 0-3 3v6.75a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3v-6.75a3 3 0 0 0-3-3v-3c0-2.9-2.35-5.25-5.25-5.25Zm3.75 8.25v-3a3.75 3.75 0 1 0-7.5 0v3h7.5Z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="syarat" type="checkbox" value="" class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-rose-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-rose-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800" required />
                            </div>
                            <label for="syarat" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Saya menerima Syarat dan Ketentuan</label>
                        </div>
                        <a href="#" class="hidden ms-auto text-sm text-blue-700 hover:underline dark:text-blue-500">Lupa Password?</a>
                    </div>
                    <button type="submit" name="daftar" class="w-full text-white bg-gradient-to-br from-tertiary to-quinary hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Registrasi</button>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-300">
                        Sudah Punya Akun? <a href="index.php" class="text-blue-700 hover:underline dark:text-blue-500">Login</a>
                    </div>
                </form>
            </div>
        </section>
    </main>
</body>

</html>