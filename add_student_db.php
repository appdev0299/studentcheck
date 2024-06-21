<?php
if (
    isset($_POST['courses'])
    && isset($_POST['course_name'])
    && isset($_POST['rooms'])
    && isset($_POST['room_name'])
    && isset($_POST['teacher_id'])
    && isset($_POST['name'])
    && isset($_POST['surname'])
) {
    require_once 'connect.php';

    $courses = $_POST['courses'];
    $course_name = $_POST['course_name'];
    $rooms = $_POST['rooms'];
    $room_name = $_POST['room_name'];
    $teacherId = $_POST['teacher_id'];
    $name = $_POST['name'];
    $surname = $_POST['surname'];

    $stmt = $conn->prepare("INSERT INTO ck_reg_courses (`courses`, `course_name`, `rooms`,`room_name`,`teacher_id`, `name`, `surname`)
                            VALUES (:courses, :course_name, :rooms, :room_name, :teacher_id, :name, :surname)");
    $stmt->bindParam(':courses', $courses, PDO::PARAM_STR);
    $stmt->bindParam(':course_name', $course_name, PDO::PARAM_STR);
    $stmt->bindParam(':rooms', $rooms, PDO::PARAM_INT);
    $stmt->bindParam(':room_name', $room_name, PDO::PARAM_STR);
    $stmt->bindParam(':teacher_id', $teacherId, PDO::PARAM_INT);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':surname', $surname, PDO::PARAM_STR);

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
            window.location.href = "index.php";
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
