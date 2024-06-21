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
                                        <!-- เพิ่มโค้ดในส่วนของหน้าเว็บหรืออินเตอร์เฟซที่ต้องการแสดง dropdown วิชา และ input วันที่ -->
                                        <form method="post" novalidate="novalidate">
                                            <div class="row">
                                                <div class="form-group col-lg-12 col-md-3 col-12">
                                                    <fieldset class="form-row" id="Member">
                                                        <div class="radio-group col-lg-12 col-md-3 col-12">
                                                            <div class="row col-md-4">
                                                                <input type="radio" id="watch-me" value="yes" name="Member" class="tap-input">
                                                                <label for="watch-me">พิมพ์รายงานการขาดเรียนรายวิชา</label>
                                                            </div>
                                                            <div class="row col-md-8">
                                                                <input type="radio" id="watch-me-maybe" value="maybe" name="Member" class="tap-input">
                                                                <label for="watch-me-maybe">พิมพ์รายงานการขาดเรียนรายบุคคล</label>
                                                            </div>
                                                        </div>
                                                        <style>
                                                            .radio-group {
                                                                display: flex;
                                                                align-items: flex-start;
                                                            }

                                                            .radio-group input[type="radio"] {
                                                                margin-right: 15px;
                                                                margin-top: -7px;
                                                            }

                                                            #MemberNo {
                                                                display: none;
                                                            }
                                                        </style>
                                                        <div>
                                                            <input type="radio" id="MemberNo" value="no" name="Member" class="tap-input">
                                                        </div>


                                                        <div id="show-me" class="form-group col-lg-12 col-md-3 col-12">
                                                            <div class="row">
                                                                <?php
                                                                require_once 'connect.php';
                                                                $teacherId = $_SESSION['id'];
                                                                $sql = "SELECT DISTINCT courses, course_name FROM ck_checking WHERE teacher_id = :teacherId";
                                                                $stmt = $conn->prepare($sql);
                                                                $stmt->bindParam(':teacherId', $teacherId);
                                                                $stmt->execute();
                                                                $checkings = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                                echo '<div class="form-group col-12">';
                                                                echo '<label for="course" class="control-label mb-1">วิชา <span style="color:red;">*</span></label>';
                                                                echo '<select name="course" id="course" class="form-control">';
                                                                $selectedCourses = array();

                                                                foreach ($checkings as $checking) {
                                                                    $courseCode = $checking['courses'];
                                                                    $courseName = $checking['course_name'];
                                                                    if (!in_array($courseCode, $selectedCourses)) {
                                                                        $selected = ($courseCode == $_POST['course']) ? 'selected' : '';
                                                                        echo '<option value="' . $courseCode . '" ' . $selected . '>' . $courseCode . ' ' . $courseName . '</option>';
                                                                        $selectedCourses[] = $courseCode;
                                                                    }
                                                                }
                                                                echo '</select>';
                                                                echo '</div>';

                                                                $startDate = isset($_POST['startDate']) ? $_POST['startDate'] : date('Y-m-d');

                                                                $endDate = isset($_POST['endDate']) ? $_POST['endDate'] : date('Y-m-d');

                                                                $startDateObj = new DateTime($startDate);
                                                                $endDateObj = new DateTime($endDate);

                                                                $startDateObj->modify('-1 day');

                                                                $startDate = $startDateObj->format('Y-m-d');
                                                                $studentCode = isset($_POST['studentCode']) ? $_POST['studentCode'] : '';

                                                                echo '<div class="form-group col-lg-6 col-md-3 col-12">';
                                                                echo '<label for="startDate" class="control-label mb-1">วันที่เริ่มต้น <span style="color:red;">*</span></label>';
                                                                echo '<input type="date" name="startDate" id="startDate" class="form-control" value="' . $startDate . '">';
                                                                echo '</div>';
                                                                echo '<div class="form-group col-lg-6 col-md-3 col-12">';
                                                                echo '<label for="endDate" class="control-label mb-1">วันที่สิ้นสุด <span style="color:red;">*</span></label>';
                                                                echo '<input type="date" name="endDate" id="endDate" class="form-control" value="' . $endDate . '">';
                                                                echo '</div>';
                                                                ?>
                                                            </div>
                                                            <hr>
                                                            <div class="col-lg-12">
                                                                <div class="row">
                                                                    <div class="row" style="margin-left: 0px">
                                                                        <button class="btn btn-success" id="exportBtn1">
                                                                            <i class="menu-icon fa fa-file-pdf-o"></i><span> ส่งออก </span>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <script>
                                                                document.getElementById('exportBtn1').addEventListener('click', function() {
                                                                    var teacherId = <?php echo json_encode($_SESSION['id']); ?>;
                                                                    var course = document.querySelector('#show-me select[name="course"]').value;
                                                                    var startDate = document.querySelector('#show-me input[name="startDate"]').value;
                                                                    var endDate = document.querySelector('#show-me input[name="endDate"]').value;
                                                                    var url = `reportpdf1.php?teacherId=${teacherId}&course=${course}&startDate=${startDate}&endDate=${endDate}`;
                                                                    url += `&timestamp=${Date.now()}`;
                                                                    window.open(url, '_blank');
                                                                });
                                                            </script>
                                                        </div>

                                                        <div id="show-me-2" class="form-group col-lg-12 col-md-3 col-12">
                                                            <div class="row">
                                                                <?php
                                                                require_once 'connect.php';
                                                                $teacherId = $_SESSION['id'];
                                                                $sql = "SELECT DISTINCT courses, course_name FROM ck_checking WHERE teacher_id = :teacherId";
                                                                $stmt = $conn->prepare($sql);
                                                                $stmt->bindParam(':teacherId', $teacherId);
                                                                $stmt->execute();
                                                                $checkings = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                                                echo '<div class="form-group col-12">';
                                                                echo '<label for="course" class="control-label mb-1">วิชา <span style="color:red;">*</span></label>';
                                                                echo '<select name="course" id="course" class="form-control">';
                                                                $selectedCourses = array();
                                                                $latestCourse = '';

                                                                if (isset($_POST['course'])) {
                                                                    $selectedCourse = $_POST['course'];
                                                                }

                                                                foreach ($checkings as $checking) {
                                                                    $courseCode = $checking['courses'];
                                                                    $courseName = $checking['course_name'];
                                                                    if ($checking['teacher_id'] == $desiredTeacherId) {
                                                                        $latestCourse = $courseCode;
                                                                    }
                                                                    $selected = ($courseCode == $selectedCourse) ? 'selected' : '';
                                                                    if (!in_array($courseCode, $selectedCourses)) {
                                                                        echo '<option value="' . $courseCode . '" ' . $selected . '>' . $courseCode . ' ' . $courseName . '</option>';
                                                                        $selectedCourses[] = $courseCode;
                                                                    }
                                                                }

                                                                echo '</select>';
                                                                echo '</div>';

                                                                $startDate = isset($_POST['startDate']) ? $_POST['startDate'] : date('Y-m-d');

                                                                $endDate = isset($_POST['endDate']) ? $_POST['endDate'] : date('Y-m-d');

                                                                $startDateObj = new DateTime($startDate);
                                                                $endDateObj = new DateTime($endDate);

                                                                $startDateObj->modify('-1 day');

                                                                $startDate = $startDateObj->format('Y-m-d');

                                                                $studentCode = isset($_POST['studentCode']) ? $_POST['studentCode'] : '';

                                                                echo '<div class="form-group col-lg-6 col-md-3 col-12">';
                                                                echo '<label for="startDate" class="control-label mb-1">วันที่เริ่มต้น <span style="color:red;">*</span></label>';
                                                                echo '<input type="date" name="startDate" id="startDate" class="form-control" value="' . $startDate . '">';
                                                                echo '</div>';

                                                                echo '<div class="form-group col-lg-6 col-md-3 col-12">';
                                                                echo '<label for="endDate" class="control-label mb-1">วันที่สิ้นสุด <span style="color:red;">*</span></label>';
                                                                echo '<input type="date" name="endDate" id="endDate" class="form-control" value="' . $endDate . '">';
                                                                echo '</div>';

                                                                echo '<div class="form-group col-lg-12 col-md-3 col-12">';
                                                                echo '<label for="studentCode" class="control-label mb-1">รหัสนักเรียน <span style="color:red;">*</span></label>';
                                                                echo '<input type="text" required name="studentCode" id="studentCode" class="form-control" value="' . $studentCode . '">';
                                                                echo '</div>';
                                                                ?>
                                                            </div>
                                                            <hr>
                                                            <div class="col-lg-12">
                                                                <div class="row">
                                                                    <div class="row" style="margin-left: 0px">
                                                                        <button class="btn btn-success" id="exportBtn2">
                                                                            <i class="menu-icon fa fa-file-pdf-o"></i><span> ส่งออก </span>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <script>
                                                                document.getElementById('exportBtn2').addEventListener('click', function() {
                                                                    var teacherId = <?php echo $_SESSION['id']; ?>;
                                                                    var course = document.querySelector('#show-me-2 select[name="course"]').value;
                                                                    var startDate = document.querySelector('#show-me-2 input[name="startDate"]').value;
                                                                    var endDate = document.querySelector('#show-me-2 input[name="endDate"]').value;
                                                                    var studentCode = document.querySelector('#show-me-2 input[name="studentCode"]').value;
                                                                    if (studentCode.trim() === '') {
                                                                        alert('กรุณาระบุรหัสประจำตัวนักเรียน');
                                                                        window.location.reload();
                                                                    }
                                                                    var url = `reportpdf2.php?teacherId=${teacherId}&course=${course}&startDate=${startDate}&endDate=${endDate}&studentCode=${studentCode}`;
                                                                    url += `&timestamp=${Date.now()}`;
                                                                    window.open(url, '_blank');
                                                                });
                                                            </script>
                                                        </div>
                                                    </fieldset>
                                                </div>
                                                <script>
                                                    function showHide(input) {
                                                        var attrVal = $(input).attr('id');
                                                        switch (attrVal) {
                                                            case 'watch-me':
                                                                $('#show-me-2').hide();
                                                                $('#show-me').show();
                                                                break;
                                                            case "watch-me-maybe":
                                                                $('#show-me').hide();
                                                                $('#show-me-2').show();
                                                                break;
                                                            default:
                                                                $('#show-me-2').hide();
                                                                $('#show-me').hide();
                                                                break;
                                                        }
                                                    }
                                                    $(document).ready(function() {
                                                        $('input[type="radio"]').each(function() {
                                                            showHide(this);
                                                        });
                                                        $('input[type="radio"]').click(function() {
                                                            showHide(this);
                                                        });
                                                    });
                                                </script>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- .card -->
                    </div>
                </div>
                <!-- /Widgets -->
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
</body>

</html>