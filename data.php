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
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <?php
                                require_once 'connect.php';

                                if (isset($_SESSION['id'])) {
                                    $teacherId = $_SESSION['id'];

                                    if (isset($_GET['id'])) {
                                        $id = $_GET['id'];

                                        $stmt = $conn->prepare("SELECT * FROM ck_main WHERE teacher_id = :teacherId AND id = :id");
                                        $stmt->bindParam(':teacherId', $teacherId, PDO::PARAM_INT);
                                        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                                        $stmt->execute();

                                        if ($stmt->rowCount() > 0) {
                                            $row = $stmt->fetch(PDO::FETCH_ASSOC);
                                            $teacherName = $row['name'];
                                        }
                                    }
                                }
                                ?>
                                <div class="card-title">
                                    <h3 class="text-center">รายชื่อนักเรียน</h3>
                                    <br>
                                    <h4 class="text-center"> <b>วิชา : </b> <?= $row['courses']; ?> <?= $row['course_name']; ?> <b>ระดับชั้น :</b> <?= getRoomLabel($row['rooms']); ?></h4>
                                    <h4 class="text-center"> <b>เวลา : </b> <?= $row['time']; ?> <b>คาบเรียนที่</b> <?= $row['period']; ?></h4>
                                    <h4 class="text-center"> <b>ครูประจำวิชา</b> <?= $_SESSION['name_title']; ?> <?= $row['name']; ?> <?= $row['surname']; ?></h4>
                                </div>
                                <?php
                                function getRoomLabel($roomNumber)
                                {
                                    $class = ($roomNumber - 1) % 3 + 1;
                                    $year = floor(($roomNumber - 1) / 3) + 1;
                                    return 'ม.' . $year . '/' . $class;
                                }
                                ?>

                                <form method="post">
                                    <?php
                                    require_once 'connect.php';

                                    if (isset($_SESSION['id']) && isset($_GET['id'])) {
                                        $teacherId = $_SESSION['id'];
                                        $id = $_GET['id'];

                                        $stmt = $conn->prepare("SELECT * FROM ck_main WHERE teacher_id = :teacherId AND id = :id");
                                        $stmt->bindParam(':teacherId', $teacherId, PDO::PARAM_INT);
                                        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                                        $stmt->execute();

                                        if ($stmt->rowCount() > 0) {
                                            $row = $stmt->fetch(PDO::FETCH_ASSOC);
                                            $rooms = $row['rooms'];
                                            $stmt2 = $conn->prepare("SELECT *
                                            FROM ck_students
                                            WHERE tb_student_degree = :rooms
                                            ORDER BY 
                                                CASE 
                                                    WHEN tb_student_sex = 1 THEN 0
                                                    WHEN tb_student_sex = 2 THEN 1
                                                    ELSE 2
                                                END ASC,
                                                tb_student_code ASC;
                                            ");
                                            $stmt2->bindParam(':rooms', $rooms, PDO::PARAM_INT);
                                            $stmt2->execute();

                                            if ($stmt2->rowCount() > 0) {
                                                $countrow = 1;
                                                if (empty($_POST['absent'])) {
                                                    $_POST['absent'] = [0];
                                                }

                                                if (empty($_POST['cause'])) {
                                                    $_POST['cause'] = array_fill(0, $stmt2->rowCount(), "");
                                                }
                                    ?>
                                                <table id="bootstrap-data-table" class="table table-striped table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>ลำดับ</th>
                                                            <th>รหัสนักเรียน</th>
                                                            <th>รายชื่อ-นามสกุล</th>
                                                            <th>ขาดเรียน</th>
                                                            <th>สาเหตุ</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $index = 1; ?>
                                                        <?php while ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) { ?>
                                                            <tr>
                                                                <td><?= $index ?></td>
                                                                <td><?= $row2['tb_student_code']; ?></td>
                                                                <td><?= $row2['tb_student_tname']; ?> <?= $row2['tb_student_name']; ?> <?= $row2['tb_student_sname']; ?></td>
                                                                <td>
                                                                    <input type="checkbox" class="form-control" name="absent[]" value="<?= $row2['tb_student_code']; ?>" onchange="handleCheckbox(this)">
                                                                </td>
                                                                <td>
                                                                    <select required class="form-control" name="cause[]" disabled onchange="handleCauseSelect(this, <?= $index ?>)">
                                                                        <option value="">โปรดเลือก</option>
                                                                        <option value="ขาดเรียน" <?php if (isset($_POST['cause'][$index]) && $_POST['cause'][$index] === 'ขาดเรียน') echo 'selected'; ?>>ขาดเรียน</option>
                                                                        <option value="ลาป่วย" <?php if (isset($_POST['cause'][$index]) && $_POST['cause'][$index] === 'ลาป่วย') echo 'selected'; ?>>ลาป่วย</option>
                                                                        <option value="ลากิจ" <?php if (isset($_POST['cause'][$index]) && $_POST['cause'][$index] === 'ลากิจ') echo 'selected'; ?>>ลากิจ</option>
                                                                        <option value="หนีเรียน" <?php if (isset($_POST['cause'][$index]) && $_POST['cause'][$index] === 'หนีเรียน') echo 'selected'; ?>>หนีเรียน</option>
                                                                        <option value="ขออนุญาตเวลาเรียน" <?php if (isset($_POST['cause'][$index]) && $_POST['cause'][$index] === 'ขออนุญาตเวลาเรียน') echo 'selected'; ?>>ขออนุญาตเวลาเรียน</option>
                                                                        <option value="อื่นๆ" <?php if (isset($_POST['cause'][$index]) && $_POST['cause'][$index] === 'อื่นๆ') echo 'selected'; ?>>อื่นๆ</option>
                                                                    </select>
                                                                    <input type="text" class="form-control" name="custom_cause[]" style="display: none;">
                                                                </td>
                                                            </tr>
                                                            <script>
                                                                function handleCheckbox(checkbox) {
                                                                    var selectCause = checkbox.parentNode.nextElementSibling.querySelector('select[name="cause[]"]');
                                                                    var inputText = checkbox.parentNode.nextElementSibling.querySelector('input[name="custom_cause[]"]');
                                                                    if (checkbox.checked) {
                                                                        selectCause.disabled = false;
                                                                        handleCauseSelect(selectCause, <?= $index ?>);
                                                                    } else {
                                                                        selectCause.disabled = true;
                                                                        selectCause.value = '';
                                                                        inputText.style.display = 'none';
                                                                        inputText.value = '';
                                                                    }
                                                                }

                                                                function handleCauseSelect(selectCause, index) {
                                                                    var inputText = selectCause.nextElementSibling;
                                                                    if (selectCause.value === 'อื่นๆ') {
                                                                        inputText.style.display = 'block';
                                                                        inputText.disabled = false;
                                                                    } else {
                                                                        inputText.style.display = 'none';
                                                                        inputText.value = '';
                                                                    }
                                                                }
                                                            </script>
                                                        <?php $index++;
                                                        } ?>
                                                    </tbody>

                                                </table>
                                    <?php } else {
                                                echo 'No student data found.';
                                            }
                                        } else {
                                            echo 'No data found.';
                                        }
                                    }
                                    ?>
                                    <div>
                                        <input type="hidden" name="time" class="form-control" value="<?php echo $row['time']; ?>">
                                        <input type="hidden" name="period" class="form-control" value="<?php echo $row['period']; ?>">
                                        <input type="hidden" name="courses" class="form-control" value="<?php echo $row['courses']; ?>">
                                        <input type="hidden" name="course_name" class="form-control" value="<?php echo $row['course_name']; ?>">
                                        <input type="hidden" name="rooms" class="form-control" value="<?php echo $row['rooms']; ?>">
                                        <input type="hidden" name="teacher_id" class="form-control" value="<?php echo $row['teacher_id']; ?>">
                                        <input type="hidden" name="name_title" class="form-control" value="<?php echo $_SESSION['name_title']; ?>">
                                        <input type="hidden" name="name" class="form-control" value="<?php echo $row['name']; ?>">
                                        <input type="hidden" name="surname" class="form-control" value="<?php echo $row['surname']; ?>">
                                    </div>
                                    <div>
                                        <?php
                                        require_once 'add_checking_db.php';
                                        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                                            if (empty($_POST['absent'])) {
                                                $_POST['absent'] = [0];
                                            }

                                            if (empty($_POST['cause'])) {
                                                $_POST['cause'] = array_fill(0, $stmt2->rowCount(), "");
                                            }

                                            // echo '<pre>';
                                            // print_r($_POST);
                                            // echo '</pre>';
                                        }
                                        ?>
                                        <button type="submit" class="btn btn-info">
                                            <span>บันทึก</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
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