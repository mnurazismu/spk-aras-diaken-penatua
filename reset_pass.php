<?php
session_start();
require 'env.php';

$resetToken = isset($_SESSION['reset_token']) ? $_SESSION['reset_token'] : null;

if (isset($_POST['reset'])) {
    $newPassword = $_POST['newpassword'];
    $confirmPassword = $_POST['confirmpassword'];

    if ($newPassword == $confirmPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $email = $_SESSION['email'];
        $query = "SELECT * FROM user WHERE email = '$email'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            $resetTokenDb = $row['reset_token'];
            $resetTokenExpiration = $row['reset_token_expiration'];

            if ($resetToken == $resetTokenDb && $resetTokenExpiration > date('Y-m-d H:i:s')) {
                $updateQuery = "UPDATE user SET password = '$hashedPassword', reset_token = NULL, reset_token_expiration = NULL WHERE email = '$email'";
                mysqli_query($conn, $updateQuery);
                echo '
                <script src="src/jquery-3.6.3.min.js"></script>
                <script src="src/sweetalert2.all.min.js"></script>
                <script>
                $(document).ready(function() {
                    Swal.fire({
                        position: "top-center",
                        icon: "success",
                        title: "Ubah Password Berhasil!",
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
                echo '
                <script src="src/jquery-3.6.3.min.js"></script>
                <script src="src/sweetalert2.all.min.js"></script>
                <script>
                $(document).ready(function() {
                    Swal.fire({
                        position: "top-center",
                        icon: "error",
                        title: "Ubah Password Gagal!",
                        text: "Token Tidak Valid!",
                        showConfirmButton: false,
                        timer: 2000
                    })
                    setTimeout(myFunction, 2000);
                });
                function myFunction() {
                    document.location.href = "reset_pass.php";
                }
                </script>
                ';
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
                    title: "Ubah Password Gagal!",
                    text: "Password Tidak Sama!",
                    showConfirmButton: false,
                    timer: 2000
                })
                setTimeout(myFunction, 2000);
            });
            function myFunction() {
                document.location.href = "reset_pass.php";
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
    <title>Reset Password | SPK ARAS</title>
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
                    <h5 class="text-xl text-center font-medium bg-clip-text text-transparent bg-gradient-to-r from-tertiary to-quinary dark:text-white">Reset Password</h5>
                    <h3 class="text-md">Silahkan masukkan password baru anda.</h3>
                    <div>
                        <label for="newpassword" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password</label>
                        <div class="relative">
                            <input type="password" id="newpassword" name="newpassword" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-rose-800 focus:border-rose-800 block w-full p-2.5 pl-10 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Masukan password" required autocomplete="off" />
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-gray-400">
                                    <path fill-rule="evenodd" d="M12 1.5a5.25 5.25 0 0 0-5.25 5.25v3a3 3 0 0 0-3 3v6.75a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3v-6.75a3 3 0 0 0-3-3v-3c0-2.9-2.35-5.25-5.25-5.25Zm3.75 8.25v-3a3.75 3.75 0 1 0-7.5 0v3h7.5Z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="confirmpassword" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Konfirmasi Password</label>
                        <div class="relative">
                            <input type="password" id="confirmpassword" name="confirmpassword" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-rose-800 focus:border-rose-800 block w-full p-2.5 pl-10 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Konfirmasi Password" required autocomplete="off" />
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-gray-400">
                                    <path fill-rule="evenodd" d="M12 1.5a5.25 5.25 0 0 0-5.25 5.25v3a3 3 0 0 0-3 3v6.75a3 3 0 0 0 3 3h10.5a3 3 0 0 0 3-3v-6.75a3 3 0 0 0-3-3v-3c0-2.9-2.35-5.25-5.25-5.25Zm3.75 8.25v-3a3.75 3.75 0 1 0-7.5 0v3h7.5Z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                    </div>
                    <button type="submit" name="reset" class="w-full text-white bg-gradient-to-br from-tertiary to-quinary hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Submit</button>
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