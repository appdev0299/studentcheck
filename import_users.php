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
                                                    <div class="col-12 col-md-9"><input type="file" id="excel" name="excel" class="form-control-file"></div>
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
                                                $id = $row[0];
                                                $email = $row[1];
                                                $password = $row[2];
                                                $name_title = $row[3];
                                                $name = $row[4];
                                                $surname = $row[5];
                                                $position = $row[6];
                                                $groups = $row[7];
                                                $status = $row[8];
                                                mysqli_query($conn, "INSERT INTO ck_users VALUES('', '$id','$email', '$password','$name_title', '$name', '$surname', '$position', '$groups', '$status')");
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
                                                    window.location.href = "import_users.php";
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
                                                    <i class="fa fa-plus-square-o"></i> เพิ่ม 1 คน
                                                </button>
                                            </div>
                                        </form>
                                        <hr>
                                        <table id="bootstrap-data-table2" class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <td>ลำดับ</td>
                                                    <td>Username</td>
                                                    <td>Password</td>
                                                    <td>ชื่อ-สกุล</td>
                                                    <td>กลุ่มสาระการเรียนรู้</td>
                                                    <td></td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                require_once 'connect.php';
                                                $stmt = $conn->prepare("SELECT * FROM ck_users b
                                                LEFT JOIN ck_departments c ON b.groups = c.tb_department_id");
                                                $stmt->execute();
                                                $result = $stmt->fetchAll();
                                                $countrow = 1;
                                                foreach ($result as $t1) {
                                                ?>
                                                    <tr>
                                                        <td><?= $countrow ?></td>
                                                        <td> <?php echo $t1["email"]; ?> </td>
                                                        <td> <?php echo $t1["password"]; ?> </td>
                                                        <td> <?php echo $t1["name_title"]; ?> <?php echo $t1["name"]; ?> <?php echo $t1["surname"]; ?> </td>
                                                        <td> <?php echo $t1["tb_department_name"]; ?> </td>
                                                        <td> <a href="del_users.php?excel_id=<?= $t1['excel_id']; ?>" class="btn btn-danger"><i class="fa fa-trash-o"></i> ลบ</a>
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
                                <h5 class="modal-title" id="teachersModalLabel">เพิ่มรายชื่อครู</h5>
                            </div>
                            <div class="modal-body">
                                <form method="post" enctype="multipart/form-data">
                                    <div class="row">
                                        <div class="form-group col-lg-6 col-md-3 col-12">
                                            <label for="id" class="control-label mb-1">รหัสประจำตัว</label>
                                            <input type="text" name="id" id="id" class="form-control" required>
                                        </div>
                                        <div class="form-group col-lg-6 col-md-3 col-12">
                                            <label for="email" class="control-label mb-1">Username</label>
                                            <input type="text" name="email" id="email" class="form-control" required>
                                        </div>
                                        <div class="form-group col-lg-6 col-md-3 col-12">
                                            <label for="password" class="control-label mb-1">Password</label>
                                            <input type="text" name="password" id="password" class="form-control" required>
                                        </div>
                                        <div class="form-group col-lg-6 col-md-3 col-12">
                                            <label for="name_title" class="control-label mb-1">คำนำหน้า</label>
                                            <input type="text" name="name_title" id="name_title" class="form-control" required>
                                        </div>
                                        <div class="form-group col-lg-6 col-md-3 col-12">
                                            <label for="name" class="control-label mb-1">ชื่อ</label>
                                            <input type="text" name="name" id="name" class="form-control" required>
                                        </div>
                                        <div class="form-group col-lg-6 col-md-3 col-12">
                                            <label for="surname" class="control-label mb-1">สกุล</label>
                                            <input type="text" name="surname" id="surname" class="form-control" required>
                                        </div>
                                        <div class="form-group col-lg-6 col-md-3 col-12">
                                            <label for="position" class="control-label mb-1">ตำแหน่ง</label>
                                            <select name="position" id="position" class="form-control" required>
                                                <option value="0">กรุณาระบุตำแหน่ง</option>
                                                <option value="ครูผู้ช่วย">ครูผู้ช่วย</option>
                                                <option value="ครู">ครู</option>
                                                <option value="ครูชำนาญการพิเศษ">ครูชำนาญการพิเศษ</option>
                                                <option value="ครูเชี่ยวชาญ">ครูเชี่ยวชาญ</option>
                                                <option value="ครูอัตราจ้าง">ครูอัตราจ้าง</option>
                                                <option value="ผู้อำนวยการ">ผู้อำนวยการ</option>
                                                <option value="รองผู้อำนวยการ">รองผู้อำนวยการ</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-lg-6 col-md-3 col-12">
                                            <label for="groups" class="control-label mb-1">กลุ่มสาระการเรียนรู้</label>
                                            <select name="groups" id="groups" class="form-control" required>
                                                <option value="0">กรุณาระบุกลุ่มสาระการเรียนรู้</option>
                                                <option value="1">ผู้บริหารสถานศึกษา</option>
                                                <option value="2">เจ้าหน้าที่สำนักงานฯ</option>
                                                <option value="3">กิจกรรมพัฒนาผู้เรียน</option>
                                                <option value="4">กลุ่มสาระการเรียนรู้ศิลปะ</option>
                                                <option value="5">กลุ่มสาระการเรียนรู้สุขศึกษา และพลศึกษา</option>
                                                <option value="6">กลุ่มสาระการเรียนรู้การงานอาชีพและเทคโนโลยี</option>
                                                <option value="7">กลุ่มสาระการเรียนรู้ภาษาต่างประเทศ</option>
                                                <option value="8">กลุ่มสาระการเรียนรู้วิทยาศาสตร์</option>
                                                <option value="9">กลุ่มสาระการเรียนรู้คณิตศาสตร์</option>
                                                <option value="10">กลุ่มสาระการเรียนรู้สังคม ศาสนา และวัฒนธรรม</option>
                                                <option value="11">กลุ่มสาระการเรียนรู้ภาษาไทย</option>
                                                <option value="12">ลูกจ้างประจำ</option>
                                                <option value="13">พนักงานจ้างเหมา </option>
                                                <option value="14">แม่บ้าน คนสวน</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-lg-12 col-md-3 col-12">
                                            <label for="status" class="control-label mb-1">ตำแหน่ง</label>
                                            <select name="status" id="status" class="form-control" required>
                                                <option value="0">ผู้ใช้งาน</option>
                                                <option value="1">Admin</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div>
                                        <?php
                                        require_once 'import_users_db.php';
                                        // if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                        //     echo '<pre>';
                                        //     print_r($_POST);
                                        //     echo '</pre>';
                                        // }
                                        ?>
                                        <button type="submit" class="btn btn-info">ยันยืน</button>
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