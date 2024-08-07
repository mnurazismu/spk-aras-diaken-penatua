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
        $query_matriks = "DELETE FROM nilai_matriks WHERE id_kriteria = '$id'";
        $result_matriks = mysqli_query($conn, $query_matriks);
        $query_subkriteria = "DELETE FROM subkriteria WHERE id_kriteria = '$id'";
        $result_subkriteria = mysqli_query($conn, $query_subkriteria);
        $query_kriteria = "DELETE FROM kriteria WHERE id_kriteria = '$id'";
        $result_kriteria = mysqli_query($conn, $query_kriteria);

        if ($result_kriteria) {
            echo '
            <script src="src/jquery-3.6.3.min.js"></script>
            <script src="src/sweetalert2.all.min.js"></script>
            <script>
            $(document).ready(function() {
                Swal.fire({
                    position: "top-center",
                    icon: "success",
                    title: "Berhasil Menghapus Kriteria!",
                    showConfirmButton: false,
                    timer: 2000
                })
                setTimeout(myFunction, 2000);
            });
            function myFunction() {
                document.location.href = "kriteria_admin.php";
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
                    title: "Gagal Menghapus Kriteria!",
                    showConfirmButton: false,
                    timer: 2000
                })
                setTimeout(myFunction, 2000);
            });
            function myFunction() {
                document.location.href = "kriteria_admin.php";
            }
            </script>
            ';
        }
    }
}
