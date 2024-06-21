<?php
// คำสั่งเชื่อมต่อฐานข้อมูล
$conn = mysqli_connect("localhost", "root", "", "studentcheck");

// ตรวจสอบการเชื่อมต่อ
if (!$conn) {
    die("การเชื่อมต่อฐานข้อมูลล้มเหลว: " . mysqli_connect_error());
}

// ตั้งค่าการเชื่อมต่อให้รองรับภาษา UTF-8
mysqli_set_charset($conn, "utf8");
