<?php
session_start();
require 'env.php';
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
        <div class="p-4 border-2 border-primary border-dashed rounded-lg dark:border-gray-700 mt-14">
            <div class="flex justify-between">
                <div>
                    <p class="text-gray-400 text-base dark:text-gray-400"><?php echo date('d F Y'); ?></p>
                    <!-- <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Selamat Datang, <?= $nama_lengkap; ?></h1> -->
                </div>
                <!-- <p class="text-gray-600 text-sm dark:text-gray-400">Anda login sebagai <?= $tipe_user; ?>.</p> -->
            </div>
            <div class="bg-gray-100 p-4 rounded-lg mt-8 flex justify-around flex-wrap gap-8">
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
</body>

</html>