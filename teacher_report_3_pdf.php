<?php
require('fpdf186/fpdf.php');

$selectedCourse = isset($_GET['course']) ? $_GET['course'] : '';
$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : date('Y-m-d');
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : date('Y-m-d');
$absent = isset($_GET['absent']) ? $_GET['absent'] : '';
$cause = isset($_GET['cause']) ? $_GET['cause'] : '';

require_once 'connect.php';
$teacherId = isset($_SESSION['id']) ? $_SESSION['id'] : (isset($_GET['teacherId']) ? $_GET['teacherId'] : '');
$sql = "SELECT s.tb_student_tname, s.tb_student_name, s.tb_student_sname, s.tb_student_sex, s.tb_student_degree, c.absent, c.courses, c.time, c.course_name, c.cause, COUNT(c.absent) as count 
FROM ck_checking c
JOIN ck_students s ON c.absent = s.tb_student_code
WHERE 1=1 ";

if ($cause) {
    $sql .= " AND c.cause = :cause";
}

if ($selectedCourse) {
    $sql .= " AND c.courses = :courseCode";
}

if ($startDate && $endDate) {
    $sql .= " AND DATE(c.time) BETWEEN :startDate AND :endDate";
}

if ($absent) {
    $sql .= " AND c.absent = :absent";
}

$sql .= " ORDER BY
            s.tb_student_degree ASC,
            s.tb_student_sex ASC,
            CASE c.cause
                WHEN 'หนีเรียน' THEN 1
                WHEN 'ขาดเรียน' THEN 2
                WHEN 'ลาป่วย' THEN 3
                WHEN 'ลากิจ' THEN 4
                WHEN 'อื่นๆ' THEN 5
                WHEN 'ขออนุญาตเวลาเรียน' THEN 6
            END";


$stmt = $conn->prepare($sql);

if ($cause) {
    $stmt->bindParam(':cause', $cause);
}

if ($selectedCourse) {
    $stmt->bindParam(':courseCode', $selectedCourse);
}

if ($startDate && $endDate) {
    $stmt->bindParam(':startDate', $startDate);
    $stmt->bindParam(':endDate', $endDate);
}

if ($absent) {
    $stmt->bindParam(':absent', $absent);
}

$stmt->execute();
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

$roomMapping = [
    1 => 'ม.1/1',
    2 => 'ม.1/2',
    3 => 'ม.1/3',
    4 => 'ม.2/1',
    5 => 'ม.2/2',
    6 => 'ม.2/3',
    7 => 'ม.3/1',
    8 => 'ม.3/2',
    9 => 'ม.3/3',
    10 => 'ม.4/1',
    11 => 'ม.4/2',
    12 => 'ม.4/3',
    13 => 'ม.5/1',
    14 => 'ม.5/2',
    15 => 'ม.5/3',
    16 => 'ม.6/1',
    17 => 'ม.6/2',
    18 => 'ม.6/3',
];;

$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();
$pdf->AddFont('THSarabunPSK', '', 'THSarabunPSK.php');
$pdf->AddFont('THSarabunBoldPSK', '', 'THSarabunBoldPSK.php');
ob_start();
if (count($students) > 0) {
    $pdf->SetFont('THSarabunPSK', '', '16');
    $pdf->Cell(0, 1, iconv('utf-8', 'cp874', 'SAC - 2'), 0, 1, 'R');
    $pdf->SetFont('THSarabunBoldPSK', '', '18');
    $pdf->Cell(0, 7, iconv('utf-8', 'cp874', 'รายงานรายบุคคล'), 0, 1, 'C');
    function formatDateThai($date)
    {
        $dateTime = new DateTime($date);
        $thaiMonths = array(
            'มกราคม', 'กุมภาพันธ์', 'มีนาคม',
            'เมษายน', 'พฤษภาคม', 'มิถุนายน',
            'กรกฎาคม', 'สิงหาคม', 'กันยายน',
            'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
        );
        $formattedDateThai = $dateTime->format('d') . ' ' . $thaiMonths[$dateTime->format('m') - 1] . ' ' . ($dateTime->format('Y') + 543);
        return $formattedDateThai;
    }
    $startDateFormattedThai = formatDateThai($startDate);
    $endDateFormattedThai = formatDateThai($endDate);
    $pdf->Cell(0, 7, iconv('utf-8', 'cp874', 'ระหว่างวันที่: ' . $startDateFormattedThai . '  ถึงวันที่: ' . $endDateFormattedThai), 0, 1, 'C');
    if (isset($_GET['absent'])) {
        $absent = $_GET['absent'];
        $sqlStudent = "SELECT tb_student_tname, tb_student_name, tb_student_sname, tb_student_degree 
                       FROM ck_students 
                       WHERE tb_student_code = :absent
                       ORDER BY tb_student_sex ASC";
        $stmtStudent = $conn->prepare($sqlStudent);
        $stmtStudent->bindParam(':absent', $absent);
        $stmtStudent->execute();
        $studentData = $stmtStudent->fetch(PDO::FETCH_ASSOC);

        if ($studentData) {
            $studentName = $studentData['tb_student_tname'] . ' ' . $studentData['tb_student_name'] . ' ' . $studentData['tb_student_sname'];
            $pdf->Cell(0, 7, iconv('utf-8', 'cp874', 'ชื่อ-นามสกุล: ' . $studentName . ' ' . 'รหัสนักเรียน: ' . $absent . ' ' . 'ชั้น: ' . $roomMapping[$studentData['tb_student_degree']]), 0, 1, 'C');
        }
    }

    $pdf->Cell(0, 7, iconv('utf-8', 'cp874', ''), 0, 1, 'C');

    $pdf->SetFont('THSarabunBoldPSK', '', 16);
    $pdf->Cell(10, 8, iconv('utf-8', 'cp874', 'ลำดับ'), 1, 0, 'C');
    $pdf->Cell(25, 8, iconv('utf-8', 'cp874', 'วันที่'), 1, 0, 'C');
    $pdf->Cell(50, 8, iconv('utf-8', 'cp874', 'ชื่อ-นามสกุล'), 1, 0, 'C');
    $pdf->Cell(55, 8, iconv('utf-8', 'cp874', 'วิชา'), 1, 0, 'C');
    $pdf->Cell(15, 8, iconv('utf-8', 'cp874', 'ระดับชั้น'), 1, 0, 'C');
    $pdf->Cell(20, 8, iconv('utf-8', 'cp874', 'จำนวนคาบ'), 1, 0, 'C');
    $pdf->Cell(20, 8, iconv('utf-8', 'cp874', 'สาเหตุ'), 1, 1, 'C');

    $pdf->SetFont('THSarabunPSK', '', 16);
    $counter = 1;
    $processedStudents = array();
    $totalCount = 0;

    foreach ($students as $student) {
        $pdf->Cell(10, 8, iconv('utf-8', 'cp874', $counter), 1, 0, 'C');
        $yearFormatted = date('Y', strtotime($student['time'])) + 543;
        $pdf->Cell(25, 8, iconv('utf-8', 'cp874', date('d/m/', strtotime($student['time'])) . $yearFormatted), 1, 0, 'L');
        $pdf->Cell(50, 8, iconv('utf-8', 'cp874', $student['tb_student_tname'] . ' ' . $student['tb_student_name'] . ' ' . $student['tb_student_sname']), 1, 0, 'L');
        $pdf->Cell(55, 8, iconv('utf-8', 'cp874', $student['course_name']), 1, 0, 'L');
        $pdf->Cell(15, 8, iconv('utf-8', 'cp874', $roomMapping[$student['tb_student_degree']]), 1, 0, 'C');
        $periodNumbers = explode(',', $student['period']);
        $numberCount = count($periodNumbers);
        $totalNumberCount += $numberCount;
        $pdf->Cell(20, 8, iconv('utf-8', 'cp874', $numberCount), 1, 0, 'C');
        $pdf->multiCell(20, 8, iconv('utf-8', 'cp874', $student['cause']), 1, 'L');

        $counter++;
    }
} else {
    $pdf->Cell(0, 10, iconv('utf-8', 'cp874', 'ไม่มีข้อมูลนักเรียนที่ขาด'), 0, 1, 'C');
}
$pdf->Cell(155, 8, iconv('utf-8', 'cp874', 'รวม' . ' '), 1, 0, 'R');
$pdf->Cell(20, 8, iconv('utf-8', 'cp874', '' . ' ' . $totalNumberCount), 1, 0, 'C');
$pdf->Cell(20, 8, iconv('utf-8', 'cp874', '' . ' '), 1, 0, 'C');
$pdf->Cell(0, 30, iconv('utf-8', 'cp874', ''), 0, 1, 'C');


$pdf->Cell(65, 7, iconv('utf-8', 'cp874', 'ลงชื่อ .................................................'), 0, 0, 'C');
$pdf->Cell(65, 7, iconv('utf-8', 'cp874', 'ลงชื่อ .................................................'), 0, 0, 'C');
$pdf->Cell(65, 7, iconv('utf-8', 'cp874', 'ลงชื่อ .................................................'), 0, 1, 'C');

$id = 2001;
$sql = "SELECT * FROM ck_users WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$courseData = $stmt->fetch(PDO::FETCH_ASSOC);

if ($courseData) {
    $name = $courseData['name'];
    $pdf->Cell(65, 7, iconv('utf-8', 'cp874', '(' . $name . ')'), 0, 0, 'C');
}
$id = 2002;
$sql = "SELECT * FROM ck_users WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$courseData = $stmt->fetch(PDO::FETCH_ASSOC);

if ($courseData) {
    $name = $courseData['name'];
    $pdf->Cell(65, 7, iconv('utf-8', 'cp874', '(' . $name . ')'), 0, 0, 'C');
}
$id = 2003;
$sql = "SELECT * FROM ck_users WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$courseData = $stmt->fetch(PDO::FETCH_ASSOC);

if ($courseData) {
    $name = $courseData['name'];
    $pdf->Cell(65, 7, iconv('utf-8', 'cp874', '(' . $name . ')'), 0, 1, 'C');
}

$pdf->Cell(65, 7, iconv('utf-8', 'cp874', 'ผู้ช่วยรองผู้อำนวยการฝ่ายวิชาการ'), 0, 0, 'C');
$pdf->Cell(65, 7, iconv('utf-8', 'cp874', 'รองผู้อำนวยการโรงเรียนถ้ำปินวิทยาคม'), 0, 0, 'C');
$pdf->Cell(0, 7, iconv('utf-8', 'cp874', 'ผู้อำนวยการโรงเรียนถ้ำปินวิทยาคม'), 0, 1, 'C');
ob_end_clean();
$filename = "report_" . date('Y-m-d') . ".pdf";

header("Content-Type: application/pdf");
header("Content-Disposition: attachment; filename=\"$filename\"");
$pdf->Output();
