<?php
if (
    isset($_POST['tb_student_code'])
    && isset($_POST['tb_student_name'])
    && isset($_POST['tb_student_tname'])
    && isset($_POST['tb_student_sex'])
    && isset($_POST['tb_student_sname'])
    && isset($_POST['tb_student_degree'])
) {
    require_once 'connect.php';

    $tb_student_code = $_POST['tb_student_code'];
    $tb_student_tname = $_POST['tb_student_tname'];
    $tb_student_sex = $_POST['tb_student_sex'];
    $tb_student_name = $_POST['tb_student_name'];
    $tb_student_sname = $_POST['tb_student_sname'];
    $tb_student_degree = $_POST['tb_student_degree'];

    $stmt = $conn->prepare("INSERT INTO ck_students (`tb_student_code`, `tb_student_name`, `tb_student_tname`, `tb_student_sex`, `tb_student_sname`, `tb_student_degree`)
                            VALUES (:tb_student_code, :tb_student_name, :tb_student_tname, :tb_student_sex, :tb_student_sname, :tb_student_degree)");
    $stmt->bindParam(':tb_student_code', $tb_student_code, PDO::PARAM_STR);
    $stmt->bindParam(':tb_student_name', $tb_student_name, PDO::PARAM_STR);
    $stmt->bindParam(':tb_student_tname', $tb_student_tname, PDO::PARAM_STR);
    $stmt->bindParam(':tb_student_sex', $tb_student_sex, PDO::PARAM_STR);
    $stmt->bindParam(':tb_student_sname', $tb_student_sname, PDO::PARAM_STR);
    $stmt->bindParam(':tb_student_degree', $tb_student_degree, PDO::PARAM_STR);

    if ($stmt->execute()) {
        echo '<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>';
        echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>';
        echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">';
        echo '<script>
        swal({
            title: "บันทึกข้อมูลสำเร็จ", 
            text: "กรุณารอสักครู่",
            type: "success", 
            timer: 2000, 
            showConfirmButton: false 
        }, function(){
            window.location.href = "import_student.php";
        });
        </script>';
    } else {
        echo '<script>
        swal({
            title: "เกิดข้อผิดพลาดในการบันทึกข้อมูล",
            type: "error"
        }, function() {
            window.location = "import_student.php";
        });
        </script>';
    }
}
