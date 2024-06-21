<?php
echo '
<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">';
//เช็คว่ามีตัวแปร session อะไรบ้าง
//print_r($_SESSION);
//exit();
//สร้างเงื่อนไขตรวจสอบสิทธิ์การเข้าใช้งานจาก session
if (empty($_SESSION['id']) && empty($_SESSION['name']) && empty($_SESSION['surname']) && empty($_SESSION['status'])) {
    echo '<script>
                setTimeout(function() {
                swal({
                title: "Please login again",
                type: "error"
                }, function() {
                window.location = "login.php"; //หน้าที่ต้องการให้กระโดดไป
                });
                }, 1000);
                </script>';
    exit();
}
?>
<header id="header" class="header">
    <div class="top-left">
        <div class="navbar-header">
            <a class="navbar-brand" style="margin-top: 7px;" href="./"><img src="images/logo.png" alt="Logo"></a>
            <a class="navbar-brand hidden" href="./"><img src="images/logo2.png" alt="Logo"></a>
            <a id="menuToggle" class="menutoggle"><i class="fa fa-bars"></i></a>

        </div>
    </div>
    <div class="top-right">
        <div class="header-menu">
            <div class="user-area dropdown float-right">
                <a href="#" class="dropdown-toggle active" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <p style="margin-top : 15px;"><?= $_SESSION['name_title'] . ' ' . $_SESSION['name'] . ' ' . $_SESSION['surname']; ?></p> &nbsp;&nbsp;&nbsp;
                    <img class="user-avatar rounded-circle" src="images/admin.png" alt="User Avatar">
                </a>
                <div class="user-menu dropdown-menu">
                    <!-- <a class="nav-link" href="#"><i class="fa fa- user"></i><?= $_SESSION['name'] . ' ' . $_SESSION['surname']; ?></a> -->
                    <a class="nav-link" href="logout.php"><i class="fa fa-power -off"></i>ออกจากระบบ</a>
                </div>
            </div>

        </div>
    </div>
</header>