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
                title: "Anda Login Sebagai Guru!",
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

    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $query_matriks = "DELETE FROM nilai_matriks WHERE id_alternatif = '$id'";
        $result = mysqli_query($conn, $query_matriks);
        // $query_hasil = "DELETE FROM hasil WHERE id_alternatif = '$id'";
        // $result_hasil = mysqli_query($conn, $query_hasil);
        $query_alternatif = "DELETE FROM alternatif WHERE id_alternatif = '$id'";
        $result_alternatif = mysqli_query($conn, $query_alternatif);
        if ($result_alternatif) {
            echo '
            <script src="src/jquery-3.6.3.min.js"></script>
            <script src="src/sweetalert2.all.min.js"></script>
            <script>
            $(document).ready(function() {
                Swal.fire({
                    position: "top-center",
                    icon: "success",
                    title: "Data Berhasil Dihapus!",
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
        } else {
            echo '
            <script src="src/jquery-3.6.3.min.js"></script>
            <script src="src/sweetalert2.all.min.js"></script>
            <script>
            $(document).ready(function() {
                Swal.fire({
                    position: "top-center",
                    icon: "error",
                    title: "Data Gagal Dihapus!",
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
        }
    }
}
