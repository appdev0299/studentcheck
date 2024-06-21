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
                                                $startDateObj = new DateTime($startDate);
                                                $endDateObj = new DateTime($endDate);
                                                $startDate = $startDateObj->format('Y-m-d');
                                                $studentCode = isset($_POST['studentCode']) ? $_POST['studentCode'] : '';
                                                $absent = isset($_POST['absent']) ? $_POST['absent'] : '';

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
                                                    <label for="course" class="control-label mb-1">วิชา</label>
                                                    <select name="course" id="course" class="form-control">
                                                        <!-- <option value="">ทั้งหมด</option> -->
                                                        <?php
                                                        require_once 'connect.php';
                                                        $teacherId = $_SESSION['id'];
                                                        $sql = "SELECT DISTINCT courses, course_name FROM ck_checking WHERE teacher_id = :teacherId";
                                                        $stmt = $conn->prepare($sql);
                                                        $stmt->bindParam(':teacherId', $teacherId);
                                                        $stmt->execute();
                                                        $checkings = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                                        foreach ($checkings as $checking) {
                                                            $courseCode = $checking['courses'];
                                                            $courseName = $checking['course_name'];
                                                            if (!in_array($courseCode, $selectedCourses)) {
                                                                $selected = ($courseCode == $_POST['course']) ? 'selected' : '';
                                                                echo '<option value="' . $courseCode . '" ' . $selected . '>' . $courseCode . ' ' . $courseName . '</option>';
                                                                $selectedCourses[] = $courseCode;
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="form-group col-6">
                                                    <label for="absent" class="control-label mb-1">รหัสนักเรียน</label>
                                                    <input type="text" name="absent" id="absent" class="form-control" value="<?= $absent ?>">
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
                                                            var selectedCourse = document.querySelector('#course').value;
                                                            var teacherId = <?php echo json_encode($_SESSION['id']); ?>;
                                                            var absent = document.querySelector('#absent').value.trim();

                                                            if (!absent || absent.length === 0) {
                                                                alert('กรุณาระบุ รหัสนักเรียนเพื่อออกรายงาน');
                                                                return;
                                                            }

                                                            if (!startDate || !endDate) {
                                                                alert('Please select both start and end dates.');
                                                                return;
                                                            }

                                                            var url = `teacher_report_2_pdf.php?teacherId=${teacherId}&startDate=${startDate}&endDate=${endDate}&course=${selectedCourse}&absent=${absent}`;
                                                            url += `&timestamp=${Date.now()}`;
                                                            window.open(url, '_blank');
                                                        });
                                                    </script>

                                                </div>
                                                <?php
                                                if (isset($_POST['startDate']) || isset($_POST['endDate'])) {
                                                    $startDate = isset($_POST['startDate']) ? $_POST['startDate'] : date('Y-m-d');
                                                    $endDate = isset($_POST['endDate']) ? $_POST['endDate'] : date('Y-m-d');
                                                    $course = isset($_POST['course']) ? $_POST['course'] : '';
                                                    $absent = isset($_POST['absent']) ? $_POST['absent'] : '';
                                                    $teacherId = isset($_POST['teacherId']) ? $_POST['teacherId'] : '';
                                                    require_once 'connect.php';

                                                    $teacherId = $_SESSION['id'];
                                                    $sql = "SELECT s.tb_student_tname, s.tb_student_name, s.tb_student_sname, s.tb_student_sex, s.tb_student_degree, c.absent, c.courses, c.course_name, c.cause, COUNT(c.absent) as count 
                                                    FROM ck_checking c
                                                    JOIN ck_students s ON c.absent = s.tb_student_code
                                                    WHERE 1=1 ";

                                                    if ($startDate && $endDate) {
                                                        $sql .= " AND DATE(c.time) BETWEEN :startDate AND :endDate";
                                                    }

                                                    if ($course) {
                                                        $sql .= " AND c.courses = :courseCode";
                                                    }
                                                    if (!empty($absent)) {
                                                        $sql .= " AND c.absent = :absent";
                                                    }
                                                    $sql .= " AND c.teacher_id = :teacherId";

                                                    $sql .= " GROUP BY c.absent, c.courses, c.cause ORDER BY 
                                                    s.tb_student_degree ASC, 
                                                    s.tb_student_sex ASC, 
                                                    c.absent ASC";

                                                    $stmt = $conn->prepare($sql);

                                                    if ($startDate && $endDate) {
                                                        $stmt->bindParam(':startDate', $startDate);
                                                        $stmt->bindParam(':endDate', $endDate);
                                                    }

                                                    if ($course) {
                                                        $stmt->bindParam(':courseCode', $course);
                                                    }
                                                    if (!empty($absent)) {
                                                        $stmt->bindParam(':absent', $absent);
                                                    }
                                                    $stmt->bindParam(':teacherId', $teacherId);  // ใช้ $_SESSION['id'] แทนที่ $_POST['teacherId']

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
                                                                <th>วิชา</th>
                                                                <th>ระดับชั้น</th>
                                                                <th>จำนวนคาบ</th>
                                                                <th>สาเหตุ</th>
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
                                                                    <td><?= $student['courses'] . ' - ' . $student['course_name'] ?></td>
                                                                    <td><?= $roomMapping[$student['tb_student_degree']] ?></td>
                                                                    <td><?= $student['count'] ?></td>
                                                                    <td><?= $student['cause'] ?></td>
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