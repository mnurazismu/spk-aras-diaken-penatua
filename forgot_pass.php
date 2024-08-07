<?php
session_start();
require 'env.php';

if (isset($_POST['cari'])) {
    $email = $_POST['email'];
    $query = "SELECT * FROM user WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['email'] = $email;

        $resetToken = bin2hex(random_bytes(32));
        $expirationTime = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $_SESSION['reset_token'] = $resetToken;

        $updateQuery = "UPDATE user SET reset_token = '$resetToken', reset_token_expiration = '$expirationTime' WHERE email = '$email'";
        mysqli_query($conn, $updateQuery);
        header('Location: reset_pass.php');
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
                    title: "Forgot Password Gagal!",
                    text: "Email Tidak Ditemukan!",
                    showConfirmButton: false,
                    timer: 2000
                })
                setTimeout(myFunction, 2000);
            });
            function myFunction() {
                document.location.href = "forgot_pass.php";
            }
            </script>
            ';
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
    <title>Forgot Password | SPK ARAS</title>
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
                <form class="space-y-6" action="" method="post">
                    <h5 class="text-xl text-center font-medium bg-clip-text text-transparent bg-gradient-to-r from-tertiary to-quinary dark:text-white">Lupa Password?</h5>
                    <h3 class="text-md">Masukkan email untuk reset password.</h3>
                    <div class="mb-5">
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
                    <button type="submit" name="cari" class="w-full text-white bg-gradient-to-br from-tertiary to-quinary hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Cari Email</button>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-300">
                        <a href="index.php" class="text-blue-700 hover:underline dark:text-blue-500">Back to Login</a>
                    </div>
                </form>
            </div>
        </section>
    </main>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
</body>

</html>