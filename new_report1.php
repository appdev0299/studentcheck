<?php
session_start();
echo '
<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">';
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
    <?php require_once 'aside.php'; ?>
    <div id="right-panel" class="right-panel">
        <?php require_once 'header.php'; ?>
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
                                            <h3 class="text-center">รายงานการขาดเรียน</h3>
                                        </div>
                                        <hr>
                                        <form method="post" novalidate="novalidate">
                                            <div class="row">
                                                <?php
                                                require_once 'connect.php';
                                                $sql = "SELECT DISTINCT courses, course_name FROM ck_checking ";
                                                $stmt = $conn->prepare($sql);
                                                $stmt->execute();
                                                $checkings = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                                $startDate = isset($_POST['startDate']) ? $_POST['startDate'] : date('Y-m-d');
                                                $endDate = isset($_POST['endDate']) ? $_POST['endDate'] : date('Y-m-d');
                                                $cause = isset($_POST['cause']) ? $_POST['cause'] : 'ขาดเรียน';
                                                $startDateObj = new DateTime($startDate);
                                                $endDateObj = new DateTime($endDate);
                                                $startDate = $startDateObj->format('Y-m-d');
                                                $studentCode = isset($_POST['studentCode']) ? $_POST['studentCode'] : '';
                                                ?>
                                                <div class="form-group col-6">
                                                    <label for="startDate" class="control-label mb-1">วันที่เริ่มต้น</label>
                                                    <input type="date" name="startDate" id="startDate" class="form-control" value="<?= $startDate ?>">
                                                </div>

                                                <div class="form-group col-6">
                                                    <label for="endDate" class="control-label mb-1">วันที่สิ้นสุด</label>
                                                    <input type="date" name="endDate" id="endDate" class="form-control" value="<?= $endDate ?>">
                                                </div>
                                                <div class="form-group col-6">
                                                    <button type="submit" class="btn btn-info">
                                                        <span><i class="menu-icon fa fa-search"></i> แสดงรายชื่อ</span>
                                                    </button>
                                                    <button type="button" id="export_data" class="btn btn-success">ออกรายงาน</button>
                                                    <script>
                                                        document.getElementById('export_data').addEventListener('click', function() {
                                                            var startDate = document.querySelector('#startDate').value;
                                                            var endDate = document.querySelector('#endDate').value;
                                                            if (!startDate || !endDate) {
                                                                alert('Please select both start and end dates.');
                                                                return;
                                                            }
                                                            var url = `exportpdf1.php?startDate=${startDate}&endDate=${endDate}`;
                                                            url += `&timestamp=${Date.now()}`;
                                                            window.open(url, '_blank');
                                                        });
                                                    </script>

                                                </div>
                                                <?php
                                                if (isset($_POST['startDate']) || isset($_POST['endDate'])) {
                                                    $startDate = isset($_POST['startDate']) ? $_POST['startDate'] : date('Y-m-d');
                                                    $endDate = isset($_POST['endDate']) ? $_POST['endDate'] : date('Y-m-d');
                                                    $cause1 = isset($_POST['cause']) ? $_POST['cause'] : 'ขาดเรียน';
                                                    $cause2 = isset($_POST['cause']) ? $_POST['cause'] : 'ลาป่วย';
                                                    $cause3 = isset($_POST['cause']) ? $_POST['cause'] : 'ลากิจ';
                                                    $cause4 = isset($_POST['cause']) ? $_POST['cause'] : 'หนีเรียน';

                                                    require_once 'connect.php';

                                                    $sql = "SELECT s.tb_student_tname, s.tb_student_name, s.tb_student_sname,s.tb_student_sex,s.tb_student_degree, c.absent, COUNT(c.absent) as count 
                                                    FROM ck_checking c
                                                    JOIN ck_students s ON c.absent = s.tb_student_code
                                                    WHERE 1=1 ";

                                                    if ($startDate && $endDate) {
                                                        $sql .= " AND DATE(c.time) BETWEEN :startDate AND :endDate";
                                                    }

                                                    $sql .= " AND (c.cause = :cause1 OR c.cause = :cause2 OR c.cause = :cause3 OR c.cause = :cause4)";

                                                    $sql .= " GROUP BY c.absent ORDER BY 
                                                    s.tb_student_degree ASC, 
                                                    s.tb_student_sex ASC, 
                                                    c.absent ASC";

                                                    $stmt = $conn->prepare($sql);

                                                    if ($startDate && $endDate) {
                                                        $stmt->bindParam(':startDate', $startDate);
                                                        $stmt->bindParam(':endDate', $endDate);
                                                    }

                                                    // Bind each cause to its respective parameter
                                                    $stmt->bindParam(':cause1', $cause1);
                                                    $stmt->bindParam(':cause2', $cause2);
                                                    $stmt->bindParam(':cause3', $cause3);
                                                    $stmt->bindParam(':cause4', $cause4);

                                                    $stmt->execute();

                                                    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                                    $roomMapping = [
                                                        1 => 'ม.1/1',
                                                        2 => 'ม.1/2',
                                                        3 => 'ม.1/3',
                                                        4 => 'ม.2/1',
                                                        5 => 'ม.2/2',
                                                        6 => 'ม.2/3',
                                                        7 => 'ม.3/1',
                                                        8 => 'ม.3/2',
                                                        9 => 'ม.3/3',
                                                        10 => 'ม.4/1',
                                                        11 => 'ม.4/2',
                                                        12 => 'ม.4/3',
                                                        13 => 'ม.5/1',
                                                        14 => 'ม.5/2',
                                                        15 => 'ม.5/3',
                                                        16 => 'ม.6/1',
                                                        17 => 'ม.6/2',
                                                        18 => 'ม.6/3',
                                                    ];
                                                ?>

                                                    <table id="bootstrap-data-table" class="table table-striped table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>ลำดับ</th>
                                                                <th>รหัสนักเรียน</th>
                                                                <th>ชื่อ-นามสกุล</th>
                                                                <th>ระดับชั้น</th>
                                                                <th>จำนวน</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $counter = 1;
                                                            foreach ($students as $student) {
                                                            ?>
                                                                <tr>
                                                                    <td><?= $counter ?></td>
                                                                    <td><?= $student['absent'] ?></td>
                                                                    <td><?= $student['tb_student_tname'] . ' ' . $student['tb_student_name'] . ' ' . $student['tb_student_sname'] ?></td>
                                                                    <td><?= $roomMapping[$student['tb_student_degree']] ?></td>
                                                                    <td><?= $student['count'] ?></td>
                                                                </tr>

                                                            <?php
                                                                $counter++;
                                                            }
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                <?php
                                                    if (count($students) == 0) {
                                                        echo 'ไม่มีข้อมูลนักเรียนที่ขาด.';
                                                    }
                                                    $conn = null;
                                                }
                                                ?>

                                            </div>
                                            <hr>
                                        </form>

                                    </div>
                                </div>
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
</body>

</html>