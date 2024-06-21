<?php
session_start();
echo '
<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">';
//เช็คว่ามีตัวแปร session อะไรบ้าง
// print_r($_SESSION);
// exit();
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


<!doctype html>
<html class="no-js" lang="">

<?php require_once 'head.php'; ?>

<body>
    <!-- Left Panel -->
    <?php require_once 'aside.php'; ?>
    <!-- /#left-panel -->
    <!-- Right Panel -->
    <div id="right-panel" class="right-panel">
        <!-- Header-->
        <?php require_once 'header.php'; ?>
        <!-- /#header -->

        <!-- Content -->
        <div class="content">
            <!-- Animated -->
            <div class="animated fadeIn">
                <!-- Widgets  -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                        <div class="card-title">
                                            <h3 class="text-center">นำเข้าข้อมูล</h3>
                                        </div>
                                        <hr>
                                        <form action="#" method="post" novalidate="novalidate" enctype="multipart/form-data">
                                            <div class="row">
                                                <div class="form-group col-lg-6 col-md-3 col-12">
                                                    <div class="col col-md-3"><label for="excel" class=" form-control-label">เพิ่มไฟล์</label></div>
                                                    <div class="col-12 col-md-9"><input type="file" id="excel" name="excel" class="form-control-file" required></div>
                                                </div>
                                            </div>
                                            <div class="form-group col-lg-6 col-md-3 col-12">
                                                <button name="import" type="submit" class="btn btn-success">
                                                    <i class="fa fa-upload"></i> <span>อัปโหลด</span>
                                                </button>
                                            </div>
                                        </form>
                                        <?php
                                        require 'config.php';
                                        if (isset($_POST["import"])) {
                                            $fileName = $_FILES["excel"]["name"];
                                            $fileExtension = explode('.', $fileName);
                                            $fileExtension = strtolower(end($fileExtension));
                                            $newFileName = date("Y.m.d") . " - " . date("h.i.sa") . "." . $fileExtension;

                                            $targetDirectory = "uploads/" . $newFileName;
                                            move_uploaded_file($_FILES['excel']['tmp_name'], $targetDirectory);

                                            error_reporting(0);
                                            ini_set('display_errors', 0);

                                            require 'excelReader/excel_reader2.php';
                                            require 'excelReader/SpreadsheetReader.php';

                                            $reader = new SpreadsheetReader($targetDirectory);
                                            foreach ($reader as $key => $row) {
                                                $tb_course_id = $row[0];
                                                $tb_course_code = $row[1];
                                                $tb_course_name = $row[2];
                                                $tb_teacher_id = $row[3];
                                                mysqli_query($conn, "INSERT INTO ck_courses VALUES('$tb_course_id','$tb_course_code', '$tb_course_name', '$tb_teacher_id')");
                                            }
                                            echo '<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>';
                                            echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>';
                                            echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">';
                                            echo '<script>
                                            $(document).ready(function(){
                                                swal({
                                                    title: "นำเข้าข้อมูลสำเร็จ",
                                                    text: "กรุณารอสักครู่",
                                                    type: "success",
                                                    timer: 2000,
                                                    showConfirmButton: false
                                                }, function(){
                                                    window.location.href = "import_courses.php";
                                                });
                                            });
                                        </script>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- .card -->
                    </div>
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                        <form action="#" method="post" novalidate="novalidate" enctype="multipart/form-data">
                                            <div class="form-group col-lg-6 col-md-3 col-12">
                                                <button type="button" class="btn btn-info ml-auto" data-toggle="modal" data-target="#teachersModal">
                                                    <i class="fa fa-plus-square-o"></i> เพิ่ม 1 วิชา
                                                </button>
                                            </div>
                                        </form>
                                        <hr>
                                        <table id="bootstrap-data-table2" class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <td>ลำดับ</td>
                                                    <td>รหัสวิชา-ชื่อวิชา</td>
                                                    <td>ครูผู้สอน</td>
                                                    <td>กลุ่มสาระการเรียนรู้</td>
                                                    <td></td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                require_once 'connect.php';
                                                $stmt = $conn->prepare("SELECT * FROM ck_courses a LEFT JOIN ck_users b ON a.tb_teacher_id=b.id LEFT JOIN ck_departments c on c.tb_department_id=b.groups");
                                                $stmt->execute();
                                                $result = $stmt->fetchAll();
                                                $countrow = 1;
                                                foreach ($result as $t1) {
                                                ?>
                                                    <tr>
                                                        <td><?= $countrow ?></td>
                                                        <td> <?php echo $t1["tb_course_code"]; ?> - <?php echo $t1["tb_course_name"]; ?> </td>
                                                        <td> <?php echo $t1["name_title"]; ?> <?php echo $t1["name"]; ?> <?php echo $t1["surname"]; ?> </td>
                                                        <td> <?php echo $t1["tb_department_name"]; ?> </td>
                                                        <td> <a href="del_course.php?tb_course_id=<?= $t1['tb_course_id']; ?>" class="btn btn-danger"><i class="fa fa-trash-o"></i> ลบ</a>
                                                        </td>
                                                    </tr>
                                                <?php
                                                    $countrow++;
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- .card -->
                    </div>
                </div>
                <div class="modal fade" id="teachersModal" tabindex="-1" role="dialog" aria-labelledby="teachersModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="teachersModalLabel">เพิ่มรายชื่อนักเรียน</h5>
                            </div>
                            <div class="modal-body">
                                <form method="post" enctype="multipart/form-data">
                                    <div class="row">
                                        <div class="form-group col-lg-6 col-md-3 col-12">
                                            <label for="tb_course_code" class="control-label mb-1">รหัสวิชา</label>
                                            <input type="text" name="tb_course_code" id="tb_course_code" class="form-control" required>
                                        </div>
                                        <div class="form-group col-lg-6 col-md-3 col-12">
                                            <label for="tb_course_name" class="control-label mb-1">ชื่อวิชา</label>
                                            <input type="text" name="tb_course_name" id="tb_course_name" class="form-control" required>
                                        </div>
                                        <div class="form-group col-lg-12 col-md-3 col-12">
                                            <label for="tb_teacher_id" class="control-label mb-1">รหัสผู้สอน</label>
                                            <input type="text" name="tb_teacher_id" id="tb_teacher_id" class="form-control" required>
                                        </div>
                                    </div>
                                    <div>
                                        <?php
                                        require_once 'import_courses_db.php';
                                        // if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                        //     echo '<pre>';
                                        //     print_r($_POST);
                                        //     echo '</pre>';
                                        // }
                                        ?>
                                        <button type="submit" class="btn btn-info">ยืนยัน</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.content -->
        <div class="clearfix"></div>
        <!-- Footer -->
        <?php require_once 'footer.php'; ?>
        <!-- /.site-footer -->
    </div>
    <!-- /#right-panel -->
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@2.2.4/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.4/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-match-height@0.7.2/dist/jquery.matchHeight.min.js"></script>
    <script src="assets/js/main.js"></script>


    <script src="assets/js/lib/data-table/datatables.min.js"></script>
    <script src="assets/js/lib/data-table/dataTables.bootstrap.min.js"></script>
    <script src="assets/js/lib/data-table/dataTables.buttons.min.js"></script>
    <script src="assets/js/lib/data-table/buttons.bootstrap.min.js"></script>
    <script src="assets/js/lib/data-table/jszip.min.js"></script>
    <script src="assets/js/lib/data-table/vfs_fonts.js"></script>
    <script src="assets/js/lib/data-table/buttons.html5.min.js"></script>
    <script src="assets/js/lib/data-table/buttons.print.min.js"></script>
    <script src="assets/js/lib/data-table/buttons.colVis.min.js"></script>
    <script src="assets/js/init/datatables-init.js"></script>


    <script type="text/javascript">
        $(document).ready(function() {
            $('#bootstrap-data-table2-export').DataTable();
        });
    </script>


</body>

</html>