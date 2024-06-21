<?php
if (
    isset($_POST['time'])
    && isset($_POST['period'])
    && isset($_POST['courses'])
    && isset($_POST['course_name'])
    && isset($_POST['rooms'])
    && isset($_POST['teacher_id'])
    && isset($_POST['name'])
    && isset($_POST['surname'])
) {
    require_once 'connect.php'; // เชื่อมต่อฐานข้อมูล

    $time = $_POST['time'];
    $period = $_POST['period'];
    $courses = $_POST['courses'];
    $course_name = $_POST['course_name'];
    $rooms = $_POST['rooms'];
    $teacher_id = $_POST['teacher_id'];
    $name = $_POST['name'];
    $surname = $_POST['surname'];

    $period = implode(", ", $period);

    $stmt = $conn->prepare("INSERT INTO ck_main (`time`, `period`, `course_name`, `courses`, `rooms`, `teacher_id` , `name`, `surname`)
    VALUES (:time, :period, :course_name, :courses, :rooms , :teacher_id, :name, :surname)");
    $stmt->bindParam(':time', $time, PDO::PARAM_STR);
    $stmt->bindParam(':period', $period, PDO::PARAM_STR);
    $stmt->bindParam(':course_name', $course_name, PDO::PARAM_STR);
    $stmt->bindParam(':courses', $courses, PDO::PARAM_STR);
    $stmt->bindParam(':rooms', $rooms, PDO::PARAM_INT);
    $stmt->bindParam(':teacher_id', $teacher_id, PDO::PARAM_INT);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':surname', $surname, PDO::PARAM_STR);


    if ($stmt->execute()) {
        $lastInsertedId = $conn->lastInsertId(); // รับค่า ID ที่เพิ่งถูกเพิ่มล่าสุด

        echo '<script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>';
        echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>';
        echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">';
        echo '<script>
        swal({
            title: "กำลังแสดงผลข้อมูลนักเรียน", 
            text: "กรุณารอสักครู่",
            type: "success", 
            timer: 1000, 
            showConfirmButton: false 
        }, function(){
            window.location.href = "data.php?id=" + ' . $lastInsertedId . ';
        });
        </script>';
    } else {
        echo '<script>
        swal({
            title: "เกิดข้อผิดพลาดในการบันทึกข้อมูล",
            type: "error"
        }, function() {
            window.location = "index.php";
        });
        </script>';
    }
}
