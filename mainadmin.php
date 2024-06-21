<?php
header('Content-Type: text/html; charset=utf-8');
session_start();
echo '
<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">';
// print_r($_SESSION);
// exit();
// ตรวจสอบว่ามีตัวแปร session ที่เกี่ยวข้องถูกตั้งค่าหรือไม่
if (empty($_SESSION['id']) || empty($_SESSION['name']) || empty($_SESSION['surname']) || empty($_SESSION['status']) || $_SESSION['status'] != 1) {
    echo '<script>
        setTimeout(function() {
            swal({
                title: "Please login again",
                type: "error"
            }, function() {
                window.location = "login.php"; // หน้าที่ต้องการให้กระโดดไป
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
                    <?php
                    require_once 'connect.php';
                    $stmt = $conn->prepare("SELECT COUNT(*) AS students FROM ck_students");
                    $stmt->execute();
                    $result = $stmt->fetch();
                    ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="stat-widget-five">
                                    <div class="stat-icon dib flat-color-1">
                                        <i class="pe-7s-users"></i>
                                    </div>
                                    <div class="stat-content">
                                        <div class="text-left dib">
                                            <div class="stat-heading">จำนวนนักเรียน</div>
                                            <div class="stat-text"><span class="count"><?php echo $result['students']; ?></span> คน </div>
                                            <a href="#" data-toggle="modal" data-target="#studentsModal" class="small-box-footer">
                                                รายละเอียด <i class="fa fa-arrow-circle-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <?php
                    require_once 'connect.php';
                    $stmt = $conn->prepare("SELECT COUNT(*) AS teachers FROM ck_users");
                    $stmt->execute();
                    $result = $stmt->fetch();
                    ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="stat-widget-five">
                                    <div class="stat-icon dib flat-color-2">
                                        <i class="pe-7s-users"></i>
                                    </div>
                                    <div class="stat-content">
                                        <div class="text-left dib">
                                            <div class="stat-heading">จำนวนคุณครู</div>
                                            <div class="stat-text"><span class="count"><?php echo $result['teachers']; ?></span> คน </div>
                                            <a href="#" data-toggle="modal" data-target="#teachersModal" class="small-box-footer">
                                                รายละเอียด <i class="fa fa-arrow-circle-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    require_once 'connect.php';
                    $stmt = $conn->prepare("SELECT COUNT(*) AS courses FROM ck_courses");
                    $stmt->execute();
                    $result = $stmt->fetch();
                    ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="stat-widget-five">
                                    <div class="stat-icon dib flat-color-3">
                                        <i class="pe-7s-browser"></i>
                                    </div>
                                    <div class="stat-content">
                                        <div class="text-left dib">
                                            <div class="stat-heading">จำนวนวิชา</div>
                                            <div class="stat-text"><span class="count"><?php echo $result['courses']; ?></span> วิชา </div>
                                                         </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    require_once 'connect.php';
                    $stmt = $conn->prepare("SELECT COUNT(*) AS studentscheck FROM ck_checking");
                    $stmt->execute();
                    $result = $stmt->fetch();
                    ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <div class="stat-widget-five">
                                    <div class="stat-icon dib flat-color-1">
                                        <i class="pe-7s-users"></i>
                                    </div>
                                    <div class="stat-content">
                                        <div class="text-left dib">
                                            <div class="stat-heading">จำนวนนักเรียนที่ ขาดเรียน ฯลฯ</div>
                                            <div class="stat-text"><span class="count"><?php echo $result['studentscheck']; ?></span> คน </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal fade" id="studentsModal" tabindex="-1" role="dialog" aria-labelledby="studentsModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="studentsModalLabel">รายชื่อนักเรียน</h5>
                            </div>
                            <div class="modal-body">
                                <table id="bootstrap-data-table" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>รหัสนักเรียน</th>
                                            <th>ชื่อ-สกุล</th>
                                            <th>ระดับชั้น</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        require_once 'connect.php';
                                        // กำหนดฟังก์ชัน getRoomLabel
                                        function getRoomLabel($roomNumber)
                                        {
                                            $class = ($roomNumber - 1) % 3 + 1;
                                            $year = floor(($roomNumber - 1) / 3) + 1;
                                            return 'ม.' . $year . '/' . $class;
                                        }

                                        $stmt = $conn->prepare("SELECT * FROM ck_students");
                                        $stmt->execute();
                                        $result = $stmt->fetchAll();
                                        $countrow = 1;
                                        foreach ($result as $t1) {
                                        ?>
                                            <tr>
                                                <td><?= $countrow ?></td>
                                                <td><?= $t1['tb_student_code']; ?></td>
                                                <td><?= $t1['tb_student_name']; ?><?= $t1['tb_student_sname']; ?></td>
                                                <td><?= getRoomLabel($t1['tb_student_degree']); ?></td><!-- เรียกใช้ฟังก์ชันเพื่อแปลงค่าในคอลัมน์ tb_student_degree -->
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
                </div>
                <div class="modal fade" id="teachersModal" tabindex="-1" role="dialog" aria-labelledby="teachersModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="teachersModalLabel">รายชื่อนักเรียน</h5>
                            </div>
                            <div class="modal-body">
                                <table id="bootstrap-data-table1" class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>ชื่อ-สกุล</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        require_once 'connect.php';
                                        $stmt = $conn->prepare("SELECT * FROM ck_users");
                                        $stmt->execute();
                                        $result = $stmt->fetchAll();
                                        $countrow = 1;
                                        foreach ($result as $t1) {
                                        ?>
                                            <tr>
                                                <td><?= $countrow ?></td>
                                                <td><?= $t1['name_title']; ?> <?= $t1['name']; ?> <?= $t1['surname']; ?></td>
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
                $('#bootstrap-data-table-export').DataTable();
            });
        </script>
        <script type="text/javascript">
            $(document).ready(function() {
                $('#bootstrap-data-table1-export').DataTable();
            });
        </script>
</body>

</html>