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
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                        <div class="card-title">
                                            <h3 class="text-center"></h3>
                                        </div>
                                        <hr>

                                        <form method="POST" novalidate="novalidate">
                                            <div class="row">
                                                <?php
                                                if (isset($_GET['id'])) {
                                                    require_once 'connect.php';
                                                    $stmt = $conn->prepare("SELECT* FROM ck_checking WHERE id=?");
                                                    $stmt->execute([$_GET['id']]);
                                                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                                                    //ถ้าคิวรี่ผิดพลาดให้กลับไปหน้า index
                                                    if ($stmt->rowCount() < 1) {
                                                        header('Location: index.php');
                                                        exit();
                                                    }
                                                } //isset
                                                ?>
                                                <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                                <div class="form-group col-lg-6 col-md-3 col-12">
                                                    <label for="date" class="control-label mb-1">วิชา</label>
                                                    <input type="text" value="<?= $row['courses']; ?> <?= $row['course_name']; ?>" class="form-control" readonly>
                                                </div>
                                                <div class="form-group col-lg-6 col-md-3 col-12">
                                                    <label for="date" class="control-label mb-1">ครูผู้สอน</label>
                                                    <input type="text" value="<?= $row['name_title']; ?> <?= $row['name']; ?> <?= $row['surname']; ?>" class="form-control" readonly>
                                                </div>
                                                <div class="form-group col-lg-6 col-md-3 col-12">
                                                    <label for="date" class="control-label mb-1">รหัสนักเรียน</label>
                                                    <input type="text" value="<?= $row['absent']; ?>" class="form-control" readonly>
                                                </div>
                                                <div class="form-group col-lg-6 col-md-3 col-12">
                                                    <label for="cause" class="control-label mb-1">สาเหตุ</label>
                                                    <select name="cause" id="cause" class="form-control" required>
                                                        <option><?= $row['cause']; ?></option>
                                                        <option value="ขาดเรียน">ขาดเรียน</option>
                                                        <option value="หนีเรียน">หนีเรียน</option>
                                                        <option value="ลาป่วย">ลาป่วย</option>
                                                        <option value="ลากิจ">ลากิจ</option>
                                                        <option value="ขออนุญาตเวลาเรียน">ขออนุญาตเวลาเรียน</option>
                                                        <option value="อื่นๆ">อื่นๆ</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-lg-6 col-md-3 col-12" id="otherCauseGroup" style="display: none;">
                                                    <label for="custom_cause" class="control-label mb-1">สาเหตุอื่นๆ</label>
                                                    <input type="text" name="custom_cause" id="custom_cause" class="form-control">
                                                </div>
                                                <script>
                                                    document.getElementById('cause').addEventListener('change', function() {
                                                        var selectedValue = this.value;
                                                        var otherCauseGroup = document.getElementById('otherCauseGroup');

                                                        if (selectedValue === 'อื่นๆ') {
                                                            otherCauseGroup.style.display = 'block';
                                                        } else {
                                                            otherCauseGroup.style.display = 'none';
                                                        }
                                                    });
                                                </script>
                                            </div>
                                            <hr>
                                            <div>
                                                <?php
                                                require_once 'edit_student_db.php';
                                                // echo '<pre>';
                                                // print_r($_POST);
                                                // echo '</pre>';
                                                ?>
                                                <button type="submit" class="btn btn-info">
                                                    <span><i class="menu-icon fa fa-search"></i> อัพเดทข้อมูล</span>
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

</body>

</html>