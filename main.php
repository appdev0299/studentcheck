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
                                        <form action="#" method="post" novalidate="novalidate">
                                            <div class="row">
                                                <div class="form-group col-lg-6 col-md-3 col-12">
                                                    <label for="date" class="control-label mb-1">วันที่</label>
                                                    <input type="date" name="time" id="time" class="form-control" required>
                                                    <script>
                                                        var currentDateInput = document.getElementById('time');
                                                        var currentDate = new Date();
                                                        var year = currentDate.getFullYear();
                                                        var month = ("0" + (currentDate.getMonth() + 1)).slice(-2);
                                                        var day = ("0" + currentDate.getDate()).slice(-2);
                                                        currentDateInput.value = year + "-" + month + "-" + day;
                                                    </script>
                                                </div>

                                                <div class="form-group col-lg-6 col-md-3 col-12">
                                                    <label for="cc-name" class="control-label mb-1">คาบเรียน</label>
                                                    <div class="form-group has-success">
                                                        <input type="checkbox" class="checkbox" name="period[]" value="1" id="period1">
                                                        <label for="period1">คาบเรียนที่ 1 |</label>

                                                        <input type="checkbox" class="checkbox" name="period[]" value="2" id="period2">
                                                        <label for="period2">คาบเรียนที่ 2 |</label>

                                                        <input type="checkbox" class="checkbox" name="period[]" value="3" id="period3">
                                                        <label for="period3">คาบเรียนที่ 3 |</label>

                                                        <input type="checkbox" class="checkbox" name="period[]" value="4" id="period4">
                                                        <label for="period4">คาบเรียนที่ 4 |</label>

                                                        <input type="checkbox" class="checkbox" name="period[]" value="5" id="period5">
                                                        <label for="period5">คาบเรียนที่ 5 |</label>

                                                        <input type="checkbox" class="checkbox" name="period[]" value="6" id="period6">
                                                        <label for="period6">คาบเรียนที่ 6 |</label>

                                                        <input type="checkbox" class="checkbox" name="period[]" value="7" id="period7">
                                                        <label for="period7">คาบเรียนที่ 7 |</label>

                                                        <input type="checkbox" class="checkbox" name="period[]" value="8" id="period8">
                                                        <label for="period8">คาบเรียนที่ 8</label>
                                                    </div>
                                                    <script>
                                                        const checkboxes = document.querySelectorAll('input[type="checkbox"][name="period[]"]');
                                                        const maxChecked = 2;

                                                        checkboxes.forEach(checkbox => {
                                                            checkbox.addEventListener('change', function() {
                                                                const checkedCount = document.querySelectorAll('input[type="checkbox"][name="period[]"]:checked').length;

                                                                if (checkedCount > maxChecked) {
                                                                    this.checked = false;
                                                                }
                                                            });
                                                        });
                                                    </script>
                                                    <?php
                                                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                                        if (isset($_POST['period'])) {
                                                            $selectedPeriods = $_POST['period'];
                                                            if (count($selectedPeriods) >= 1 && count($selectedPeriods) <= 2) {
                                                                // ตรวจสอบเลือกคาบเรียนที่ถูกต้อง
                                                                // ดำเนินการต่อหรือเก็บข้อมูลที่ได้รับ
                                                            } else {
                                                                echo '<span style="color: red;">กรุณาเลือกคาบเรียนอย่างน้อย 1 คาบเรียน และไม่เกิน 2 คาบเรียน</span>';
                                                            }
                                                        } else {
                                                            echo '<span style="color: red;">กรุณาเลือกคาบเรียนอย่างน้อย 1 คาบเรียน และไม่เกิน 2 คาบเรียน</span>';
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-lg-6 col-md-3 col-12">
                                                    <label for="courses" class="control-label mb-1">วิชา</label>
                                                    <select name="courses" class="form-control">
                                                        <option value="">เลือกวิชา</option>
                                                        <?php
                                                        require_once 'connect.php';

                                                        $selectCoursesQuery = "SELECT DISTINCT tb_course_code, tb_course_name FROM ck_courses WHERE tb_teacher_id = :teacherId";
                                                        $stmt = $conn->prepare($selectCoursesQuery);
                                                        $stmt->bindParam(':teacherId', $_SESSION['id']);
                                                        $stmt->execute();

                                                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                            $course = $row['tb_course_code'];
                                                            $courseName = $row['tb_course_name'];

                                                            echo "<option value='$course' data-course-name='$courseName'>$course - $courseName</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <input type="hidden" name="course_name" class="form-control" id="courseNameInput">
                                                <script>
                                                    var coursesDropdown = document.querySelector('select[name="courses"]');
                                                    var courseNameInput = document.getElementById('courseNameInput');

                                                    coursesDropdown.addEventListener('change', function() {
                                                        var selectedOption = this.options[this.selectedIndex];
                                                        var courseName = selectedOption.dataset.courseName;
                                                        courseNameInput.value = courseName;
                                                    });
                                                </script>
                                                <div class="form-group col-lg-6 col-md-3 col-12">
                                                    <label for="rooms" class="control-label mb-1">ระดับชั้น</label>
                                                    <select name="rooms" required class="form-control">
                                                        <option value="">เลือกระดับชั้น</option>
                                                        <?php
                                                        require_once 'connect.php';

                                                        $selectRoomsQuery = "SELECT tb_room_id, tb_room_name FROM ck_rooms";
                                                        $stmt = $conn->prepare($selectRoomsQuery);
                                                        $stmt->execute();

                                                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                            $roomId = $row['tb_room_id'];
                                                            $roomName = $row['tb_room_name'];

                                                            echo "<option value='$roomId' data-roomname='$roomName'>$roomName</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <input type="hidden" name="room_name" class="form-control" id="roomNameInput">
                                                <script>
                                                    var roomsDropdown = document.querySelector('select[name="rooms"]');
                                                    var roomNameInput = document.getElementById('roomNameInput');

                                                    roomsDropdown.addEventListener('change', function() {
                                                        var selectedOption = this.options[this.selectedIndex];
                                                        var roomName = selectedOption.getAttribute('data-roomname');
                                                        roomNameInput.value = roomName;
                                                    });
                                                </script>
                                            </div>
                                            <div class="col-6">
                                                <input type="hidden" name="teacher_id" class="form-control" value="<?php echo $_SESSION['id']; ?>">
                                                <input type="hidden" name="name" class="form-control" value="<?php echo $_SESSION['name']; ?>">
                                                <input type="hidden" name="surname" class="form-control" value="<?php echo $_SESSION['surname']; ?>">
                                            </div>
                                            <hr>
                                            <div>
                                                <?php
                                                require_once 'add_main_db.php';
                                                // if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                                //     echo '<pre>';
                                                //     print_r($_POST);
                                                //     echo '</pre>';
                                                // }
                                                ?>
                                                <button id="payment-button" type="submit" class="btn btn-info">
                                                    <span>แสดงรายชื่อ</span>
                                                </button>
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