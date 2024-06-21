<?php
if (
    isset($_POST['time'])
    && isset($_POST['period'])
    && isset($_POST['courses'])
    && isset($_POST['course_name'])
    && isset($_POST['rooms'])
    && isset($_POST['teacher_id'])
    && isset($_POST['name_title'])
    && isset($_POST['name'])
    && isset($_POST['surname'])
    && isset($_POST['absent'])
) {
    require_once 'connect.php'; // Connect to the database

    $time = $_POST['time'];
    $period = $_POST['period'];
    $courses = $_POST['courses'];
    $course_name = $_POST['course_name'];
    $rooms = $_POST['rooms'];
    $teacher_id = $_POST['teacher_id'];
    $name_title = $_POST['name_title'];
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $absent = $_POST['absent'];
    $cause = isset($_POST['cause']) ? $_POST['cause'] : array();
    $custom_cause = isset($_POST['custom_cause']) ? $_POST['custom_cause'] : array();

    if (is_array($absent) && is_array($cause) && is_array($custom_cause)) {
        $numRows = count($absent);

        for ($i = 0; $i < $numRows; $i++) {
            $currentAbsent = $absent[$i];
            $currentCause = isset($cause[$i]) ? $cause[$i] : '';
            $currentCustomCause = !empty($custom_cause[$i]) ? $custom_cause[$i] : '';
            $stmt = $conn->prepare("INSERT INTO ck_checking (`time`, `period`, `courses`, `course_name`, `rooms`, `teacher_id`, `name_title`, `name`, `surname`, `absent`, `cause`, `custom_cause`)
            VALUES (:time, :period, :courses, :course_name, :rooms, :teacher_id, :name_title, :name, :surname, :absent, :cause, :custom_cause)");
            $stmt->bindParam(':time', $time, PDO::PARAM_STR);
            $stmt->bindParam(':period', $period, PDO::PARAM_STR);
            $stmt->bindParam(':courses', $courses, PDO::PARAM_STR);
            $stmt->bindParam(':course_name', $course_name, PDO::PARAM_STR);
            $stmt->bindParam(':rooms', $rooms, PDO::PARAM_INT);
            $stmt->bindParam(':teacher_id', $teacher_id, PDO::PARAM_INT);
            $stmt->bindParam(':name_title', $name_title, PDO::PARAM_STR);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':surname', $surname, PDO::PARAM_STR);
            $stmt->bindParam(':absent', $currentAbsent, PDO::PARAM_STR);
            $stmt->bindParam(':cause', $currentCause, PDO::PARAM_STR);
            $stmt->bindParam(':custom_cause', $currentCustomCause, PDO::PARAM_STR);

            $stmt->execute();
        }
    }
    if ($stmt->rowCount() > 0) {
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
