<?php
require_once 'connect.php';

if (isset($_GET['course'])) {
    $selectedCourse = $_GET['course'];

    $sql = "SELECT ck_rooms.tb_room_id, ck_rooms.tb_room_name
    FROM ck_reg_courses
    INNER JOIN ck_rooms ON ck_reg_courses.rooms = ck_rooms.tb_room_id
    WHERE ck_reg_courses.courses = :selectedCourse";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':selectedCourse', $selectedCourse);
    $stmt->execute();

    $rooms = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $roomId = $row['tb_room_id'];
        $roomName = $row['tb_room_name'];
        $rooms[] = array('id' => $roomId, 'name' => $roomName);
    }

    header('Content-Type: application/json');
    echo json_encode($rooms);
}
