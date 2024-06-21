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
if (empty($_SESSION['id']) && empty($_SESSION['name']) && empty($_SESSION['surname'])&& empty($_SESSION['status'])) {
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
                                            <h3 class="text-center">ลงทะเบียนนักเรียน</h3>
                                        </div>
                                        <hr>
                                        <form action="#" method="post" novalidate="novalidate">
                                            <div class="form-group">
                                                <label for="courses" class="control-label mb-1">รายวิชา</label>
                                                <select name="courses" id="courses" class="form-control" onchange="updateCourseName()" required>
                                                    <option value="">เลือกวิชา</option>
                                                    <?php
                                                    require_once 'connect.php';

                                                    // ตรวจสอบว่ามีการเข้าสู่ระบบแล้วด้วย $_SESSION
                                                    if (isset($_SESSION['id'])) {
                                                        $teacherId = $_SESSION['id'];

                                                        $sql = "SELECT * FROM ck_courses WHERE tb_teacher_id = :teacherId";
                                                        $stmt = $conn->prepare($sql);
                                                        $stmt->bindParam(':teacherId', $teacherId);
                                                        $stmt->execute();

                                                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                            $courseCode = $row['tb_course_code'];
                                                            $courseName = $row['tb_course_name'];
                                                            echo "<option value='$courseCode'>$courseCode - $courseName</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                                <input type="hidden" name="course_name" id="course_name" class="form-control">
                                                <script>
                                                    function updateCourseName() {
                                                        var selectElement = document.getElementById("courses");
                                                        var selectedOption = selectElement.options[selectElement.selectedIndex];

                                                        var courseName = selectedOption.text.split(" - ")[1].trim();
                                                        document.getElementById("course_name").value = courseName;
                                                    }
                                                </script>
                                            </div>

                                            <div class="form-group has-success">
                                                <label for="rooms" class="control-label mb-1">ระดับชั้นเรียน</label>
                                                <select name="rooms" class="form-control" required onchange="updateRoomName(this)">
                                                    <option value="">เลือกระดับชั้น</option>
                                                    <?php
                                                    require_once 'connect.php';

                                                    $sql = "SELECT * FROM ck_rooms";
                                                    $stmt = $conn->prepare($sql);
                                                    $stmt->execute();
                                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                        $roomId = $row['tb_room_id'];
                                                        $roomName = $row['tb_room_name'];
                                                        echo "<option value='$roomId'>$roomName</option>";
                                                    }
                                                    ?>
                                                </select>
                                                <input type="hidden" name="room_name" id="tb_room_name" class="form-control">

                                                <script>
                                                    function updateRoomName(selectElement) {
                                                        var roomNameField = document.getElementById("tb_room_name");
                                                        var selectedOption = selectElement.options[selectElement.selectedIndex];
                                                        var roomName = selectedOption.text;
                                                        roomNameField.value = roomName;
                                                    }
                                                </script>
                                            </div>
                                            <div class="form-group">
                                                <input type="hidden" name="teacher_id" class="form-control" value="<?php echo $_SESSION['id']; ?>">
                                                <input type="hidden" name="name" class="form-control" value="<?php echo $_SESSION['name']; ?>">
                                                <input type="hidden" name="surname" class="form-control" value="<?php echo $_SESSION['surname']; ?>">
                                            </div>
                                            <div>
                                                <?php
                                                require_once 'add_student_db.php';
                                                // if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                                //     echo '<pre>';
                                                //     print_r($_POST);
                                                //     echo '</pre>';
                                                // }
                                                ?>
                                                <button type="submit" class="btn btn-info">
                                                    <span>บันทึก</span>
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
    <script src="https://cdn.jsdelivr.net/npm/jquery@2.2.4/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.4/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-match-height@0.7.2/dist/jquery.matchHeight.min.js"></script>
    <script src="assets/js/main.js"></script>

    <!--  Chart js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.7.3/dist/Chart.bundle.min.js"></script>

    <!--Chartist Chart-->
    <script src="https://cdn.jsdelivr.net/npm/chartist@0.11.0/dist/chartist.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartist-plugin-legend@0.6.2/chartist-plugin-legend.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/jquery.flot@0.8.3/jquery.flot.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flot-pie@1.0.0/src/jquery.flot.pie.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flot-spline@0.0.1/js/jquery.flot.spline.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/simpleweather@3.1.0/jquery.simpleWeather.min.js"></script>
    <script src="assets/js/init/weather-init.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/moment@2.22.2/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@3.9.0/dist/fullcalendar.min.js"></script>
    <script src="assets/js/init/fullcalendar-init.js"></script>
</body>

</html>