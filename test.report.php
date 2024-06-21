<?php
require('fpdf186/fpdf.php');
$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : date('Y-m-d');
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : date('Y-m-d');
$teacherId = isset($_GET['teacherId']) ? $_GET['teacherId'] : '';
$selectedCourse = isset($_GET['course']) ? $_GET['course'] : '';


require_once 'connect.php';
$teacherId = isset($_SESSION['id']) ? $_SESSION['id'] : (isset($_GET['teacherId']) ? $_GET['teacherId'] : '');
$sql = "SELECT s.tb_student_tname, s.tb_student_name, s.tb_student_sname, s.tb_student_sex, s.tb_student_degree, c.absent, c.courses, c.course_name, c.cause,c.custom_cause, COUNT(c.absent) as count 
FROM ck_checking c
JOIN ck_students s ON c.absent = s.tb_student_code
WHERE 1=1 ";

if ($teacherId) {
    $sql .= " AND c.teacher_id = :teacherId";
}
if ($selectedCourse) {
    $sql .= " AND c.courses = :selectedCourse";
}
if ($startDate && $endDate) {
    $sql .= " AND DATE(c.time) BETWEEN :startDate AND :endDate";
}

$sql .= " GROUP BY c.absent, c.courses, c.cause ORDER BY 
s.tb_student_degree ASC, 
s.tb_student_sex ASC, 
c.absent ASC";
$stmt = $conn->prepare($sql);

if ($teacherId) {
    $stmt->bindParam(':teacherId', $teacherId);
}
if ($selectedCourse) {
    $stmt->bindParam(':selectedCourse', $selectedCourse);
}
if ($startDate && $endDate) {
    $stmt->bindParam(':startDate', $startDate);
    $stmt->bindParam(':endDate', $endDate);
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
];


$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();
$pdf->AddFont('THSarabunPSK', '', 'THSarabunPSK.php');
$pdf->AddFont('THSarabunBoldPSK', '', 'THSarabunBoldPSK.php');

ob_start();
if (count($students) > 0) {
    $pdf->SetFont('THSarabunPSK', '', '16');
    $pdf->Cell(0, 7, iconv('utf-8', 'cp874', 'SAC - 5'), 0, 1, 'R');
    $pdf->SetFont('THSarabunBoldPSK', '', '18');
    $pdf->Cell(0, 7, iconv('utf-8', 'cp874', 'รายงานการขาดเรียนรายวิชา'), 0, 1, 'C');
    if (isset($_GET['course'])) {
        $selectedCourse = $_GET['course'];
        $sqlCourse = "SELECT tb_course_name FROM ck_courses WHERE tb_course_code = :selectedCourse";
        $stmtCourse = $conn->prepare($sqlCourse);
        $stmtCourse->bindParam(':selectedCourse', $selectedCourse);
        $stmtCourse->execute();
        $courseData = $stmtCourse->fetch(PDO::FETCH_ASSOC);
        if ($courseData) {
            $tb_course_name = $courseData['tb_course_name'];
        }
        if (isset($_GET['teacherId'])) {
            $teacherId = $_GET['teacherId'];
            $sqlTeacher = "SELECT name_title, name, surname FROM ck_users WHERE id = :teacherId";
            $stmtTeacher = $conn->prepare($sqlTeacher);
            $stmtTeacher->bindParam(':teacherId', $teacherId);
            $stmtTeacher->execute();
            $teacherData = $stmtTeacher->fetch(PDO::FETCH_ASSOC);

            if ($teacherData) {
                $name_title = $teacherData['name_title'];
                $name = $teacherData['name'];
                $surname = $teacherData['surname'];
                $pdf->Cell(0, 7, iconv('utf-8', 'cp874', 'ครูผู้สอน: ' . $name_title . ' ' . $name . ' ' . $surname . ' ' . 'วิชา: ' . $tb_course_name), 0, 1, 'C');
            }
        }
    }
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

    $pdf->Cell(0, 7, iconv('utf-8', 'cp874', ''), 0, 1, 'C');

    $pdf->SetFont('THSarabunBoldPSK', '', 16);
    $pdf->Cell(15, 10, iconv('utf-8', 'cp874', 'ลำดับ'), 1, 0, 'C');
    $pdf->Cell(100, 10, iconv('utf-8', 'cp874', 'ชื่อ-นามสกุล'), 1, 0, 'C');
    $pdf->Cell(20, 10, iconv('utf-8', 'cp874', 'ระดับชั้น'), 1, 0, 'C');
    $pdf->Cell(25, 10, iconv('utf-8', 'cp874', 'จำนวนคาบ'), 1, 0, 'C');
    $pdf->Cell(30, 10, iconv('utf-8', 'cp874', 'สาเหตุ'), 1, 1, 'C');

    $pdf->SetFont('THSarabunPSK', '', 16);
    $counter = 1;
    $processedStudents = array();
    $totalCount = 0;
    foreach ($students as $student) {
        $pdf->Cell(15, 10, iconv('utf-8', 'cp874', $counter), 1, 0, 'C');
        $pdf->Cell(100, 10, iconv('utf-8', 'cp874', $student['tb_student_tname'] . ' ' . $student['tb_student_name'] . ' ' . $student['tb_student_sname']), 1, 0, 'L');
        $pdf->Cell(20, 10, iconv('utf-8', 'cp874', $roomMapping[$student['tb_student_degree']]), 1, 0, 'C');
        $pdf->Cell(25, 10, $student['count'], 1, 0, 'C');
        $totalCount += $student['count'];
        // $pdf->Cell(30, 10, iconv('utf-8', 'cp874', $student['cause'] . '  ' . $student['custom_cause']), 1, 1, 'L');
        $pdf->Cell(30, 10, iconv('utf-8', 'cp874', $student['cause']), 1, 1, 'L');

        $counter++;
    }
} else {
    $pdf->Cell(0, 10, iconv('utf-8', 'cp874', 'ไม่มีข้อมูลนักเรียนที่ขาด'), 0, 1, 'C');
}
$pdf->Cell(135, 10, iconv('utf-8', 'cp874', 'รวม' . ' '), 1, 0, 'R');
$pdf->Cell(25, 10, iconv('utf-8', 'cp874', '' . ' ' . $totalCount), 1, 0, 'C');
$pdf->Cell(30, 10, iconv('utf-8', 'cp874', '' . ' '), 1, 0, 'C');

$pdf->Cell(0, 30, iconv('utf-8', 'cp874', ''), 0, 1, 'C');
$pdf->Cell(0, 7, iconv('utf-8', 'cp874', 'ลงชื่อ .................................................'), 0, 1, 'C');
$pdf->Cell(0, 7, iconv('utf-8', 'cp874', '' . $name_title . ' ' . $name . ' ' . $surname), 0, 1, 'C');
ob_end_clean();
$filename = "report_" . date('Y-m-d') . ".pdf";

header("Content-Type: application/pdf");
header("Content-Disposition: attachment; filename=\"$filename\"");
$pdf->Output();
