<?php
if (
    isset($_POST['tb_course_code'])
    && isset($_POST['tb_course_name'])
    && isset($_POST['tb_teacher_id'])
) {
    // ตรวจสอบและนำเข้าไฟล์ connect.php ที่ใช้ในการเชื่อมต่อฐานข้อมูล
    require_once 'connect.php';

    // นำเข้าข้อมูลจากฟอร์มไปเก็บในตัวแปร
    $tb_course_code = $_POST['tb_course_code'];
    $tb_course_name = $_POST['tb_course_name'];
    $tb_teacher_id = $_POST['tb_teacher_id'];

    // ใช้ try-catch เพื่อจับข้อผิดพลาดในการเชื่อมต่อและดำเนินการเก็บข้อมูล
    try {
        // สร้างคำสั่ง SQL ในการเพิ่มข้อมูลลงในตาราง ck_courses
        $sql = "INSERT INTO ck_courses (`tb_course_code`, `tb_teacher_id`, `tb_course_name`)
                VALUES (:tb_course_code, :tb_teacher_id, :tb_course_name)";

        // เตรียมคำสั่ง SQL และ bind ค่าที่ต้องการเพิ่มเข้าไปในคำสั่ง SQL
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':tb_course_code', $tb_course_code, PDO::PARAM_STR);
        $stmt->bindParam(':tb_course_name', $tb_course_name, PDO::PARAM_STR);
        $stmt->bindParam(':tb_teacher_id', $tb_teacher_id, PDO::PARAM_INT); // ใช้ PARAM_INT หาก tb_teacher_id เป็นข้อมูลประเภท integer

        // ดำเนินการเพิ่มข้อมูล
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
                window.location.href = "import_courses.php";
            });
            </script>';
        } else {
            echo '<script>
            swal({
                title: "เกิดข้อผิดพลาดในการบันทึกข้อมูล",
                type: "error"
            }, function() {
                window.location = "import_courses.php";
            });
            </script>';
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
