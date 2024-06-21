<?php session_start(); ?>
<!doctype html>
<html class="no-js" lang=""> <!--<![endif]-->
<?php require_once 'head.php'; ?>

<body class="bg-gradient-primary">

    <div class="sufee-login d-flex align-content-center flex-wrap">
        <div class="container">
            <div class="login-content">

                <div class="login-form">
                    <div class="login-logo">
                        <img class="align-content" src="images/logo.png" alt="">
                    </div>
                    <form action="" method="post">
                        <div class="form-group">
                            <label>เข้าสู่ระบบ</label>
                            <input type="text" name="email" class="form-control login" required minlength="3" placeholder="Username....">
                        </div>
                        <div class="form-group">
                            <input type="password" name="password" class="form-control login" required minlength="3" placeholder="Enter Password...">
                        </div>
                        <input type="hidden" name="status" class="form-control " value="0">
                        <button type="submit" class="btn btn-primary btn-flat m-b-30 m-t-30">เข้าสู่ระบบ</button>
                    </form>
                    <?php
                    // print_r($_POST); // ตรวจสอบมี input อะไรบ้างและส่งอะไรมาบ้าง
                    // ถ้ามีค่าส่งมาจากฟอร์ม
                    if (isset($_POST['email']) && isset($_POST['password']) && isset($_POST['status'])) {
                        // sweet alert
                        echo '
                        <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
                        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">
                        <link rel="preconnect" href="https://fonts.googleapis.com">
                        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
                        <link href="https://fonts.googleapis.com/css2?family=Bai+Jamjuree&display=swap" rel="stylesheet">';

                        // ไฟล์เชื่อมต่อฐานข้อมูล
                        require_once 'connect.php';
                        // ประกาศตัวแปรรับค่าจากฟอร์ม
                        $email = $_POST['email'];
                        $password = $_POST['password'];
                        $status = $_POST['status']; // เก็บรหัสผ่านในรูปแบบ sha1 

                        // check email & password
                        $stmt = $conn->prepare("SELECT id, name_title, name, surname, position, groups, status FROM ck_users WHERE email = :email AND password = :password");
                        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
                        $stmt->execute();

                        // กรอก email & password ถูกต้อง
                        if ($stmt->rowCount() == 1) {
                            // fetch เพื่อเรียกคอลัมน์ที่ต้องการไปสร้างตัวแปร session
                            $row = $stmt->fetch(PDO::FETCH_ASSOC);

                            // ตรวจสอบสถานะผู้ใช้งาน
                            if ($row['status'] == 0) {
                                // เช็คหน้า mainadmin.php
                                if (basename($_SERVER['PHP_SELF']) == 'mainadmin.php') {
                                    echo '
                                    <script>
                                        setTimeout(function() {
                                            swal({
                                                title: "คุณไม่มีสิทธิ์ใช้งาน",
                                                text: "กรุณาติดต่อผู้ดูแลระบบ",
                                                type: "error"
                                            }, function() {
                                                window.location = "login.php"; // หน้าที่ต้องการให้กระโดดไป
                                            });
                                        }, 1000);
                                    </script>';
                                    $conn = null; // ปิดการเชื่อมต่อฐานข้อมูล
                                    exit();
                                } else {

                                    echo '<script>window.location.href = "index.php";</script>';
                                }
                            }
                            // สถานะเป็น 1
                            if ($row['status'] == 1) {
                                echo '<script>window.location.href = "mainadmin.php";</script>';
                            }

                            // สร้างตัวแปร session
                            $_SESSION['id'] = $row['id'];
                            $_SESSION['name_title'] = $row['name_title'];
                            $_SESSION['name'] = $row['name'];
                            $_SESSION['surname'] = $row['surname'];
                            $_SESSION['position'] = $row['position'];
                            $_SESSION['groups'] = $row['groups'];
                            $_SESSION['status'] = $row['status'];

                            echo '
                            <script>
                                swal({
                                    title: "เข้าสู่ระบบสำเร็จ!",
                                    text: "กรุณารอสักครู่",
                                    type: "success",
                                    timer: 2000,
                                    showConfirmButton: false
                                }, function(){
                                    window.location.href = "index.php";
                                });
                            </script>';
                        } else {
                            echo '
                            <script>
                                setTimeout(function() {
                                    swal({
                                        title: "เกิดข้อผิดพลาด",
                                        text: "Username หรือ Password ไม่ถูกต้อง ลองใหม่อีกครั้ง",
                                        type: "warning"
                                    }, function() {
                                        window.location = "login.php"; // หน้าที่ต้องการให้กระโดดไป
                                    });
                                }, 1000);
                            </script>';
                            $conn = null;
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@2.2.4/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.4/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-match-height@0.7.2/dist/jquery.matchHeight.min.js"></script>
    <script src="assets/js/main.js"></script>

</body>

</html>