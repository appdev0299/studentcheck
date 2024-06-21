<?php
require('fpdf186/fpdf.php');

$selectedCourse = isset($_GET['course']) ? $_GET['course'] : '';
$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : date('Y-m-d');
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : date('Y-m-d');
$studentCode = isset($_GET['studentCode']) ? $_GET['studentCode'] : '';
$cause = isset($_GET['cause']) ? $_GET['cause'] : '';
$teacherId = isset($_GET['teacherId']) ? $_GET['teacherId'] : '';


require_once 'connect.php';

$sql = "SELECT c.*, s.tb_student_tname, s.tb_student_name, s.tb_student_sname FROM ck_checking c 
JOIN ck_students s ON c.absent = s.tb_student_code
WHERE 1=1";

// เพิ่มเงื่อนไขใน SQL ให้กรองตาม teacher_id
if ($teacherId) {
    $sql .= " AND c.teacher_id = :teacherId";
}

if ($selectedCourse) {
    $sql .= " AND c.courses = :courseCode";
}

if ($startDate && $endDate) {
    $sql .= " AND DATE(c.time) BETWEEN :startDate AND :endDate";
}

if ($studentCode) {
    $sql .= " AND c.absent = :studentCode";
}

if ($cause) {
    $sql .= " AND c.cause = :cause";
}

// คริวรีข้อมูลด้วยคำสั่ง SQL
$stmt = $conn->prepare($sql);

// ตรวจสอบว่ามีค่า teacher_id ถูกส่งมาหรือไม่ ถ้ามีให้ bind parameter ให้กับตัวแปร $teacherId
if ($teacherId) {
    $stmt->bindParam(':teacherId', $teacherId);
}

if ($selectedCourse) {
    $stmt->bindParam(':courseCode', $selectedCourse);
}

if ($startDate && $endDate) {
    $stmt->bindParam(':startDate', $startDate);
    $stmt->bindParam(':endDate', $endDate);
}

if ($studentCode) {
    $stmt->bindParam(':studentCode', $studentCode);
}

if ($cause) {
    $stmt->bindParam(':cause', $cause);
}

$stmt->execute();
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();
$pdf->AddFont('THSarabunNew', '', 'THSarabunNew.php');
$pdf->AddFont('THSarabunNewBold', '', 'THSarabunNewBold.php');
$pdf->SetFont('THSarabunNew', '', '12');

ob_start();

if (count($students) > 0) {
    $pdf->SetFont('THSarabunNewBold', '', '18');
    $pdf->Cell(0, 7, iconv('utf-8', 'cp874', 'รายงานการขาดเรียน'), 0, 1, 'C');
    $pdf->SetFont('THSarabunNewBold', '', '16');
    require_once 'connect.php';
    if (isset($_GET['course'])) {
        $selectedCourse = $_GET['course'];

        $sqlCourse = "SELECT tb_course_name FROM ck_courses WHERE tb_course_code = :selectedCourse";
        $stmtCourse = $conn->prepare($sqlCourse);
        $stmtCourse->bindParam(':selectedCourse', $selectedCourse);
        $stmtCourse->execute();
        $courseData = $stmtCourse->fetch(PDO::FETCH_ASSOC);

        if ($courseData) {
            $tb_course_name = $courseData['tb_course_name'];
            $pdf->Cell(0, 7, iconv('utf-8', 'cp874', 'รายวิชา: ' . $tb_course_name), 0, 1, 'C');
        }
    }

    if (isset($_GET['studentCode'])) {
        $studentCode = $_GET['studentCode'];
        $sqlStudent = "SELECT tb_student_tname, tb_student_name, tb_student_sname, tb_student_degree FROM ck_students WHERE tb_student_code = :studentCode";
        $stmtStudent = $conn->prepare($sqlStudent);
        $stmtStudent->bindParam(':studentCode', $studentCode);
        $stmtStudent->execute();
        $studentData = $stmtStudent->fetch(PDO::FETCH_ASSOC);
        if ($studentData) {
            $studentrooms = $studentData['tb_student_degree'];
            $studentName = $studentData['tb_student_tname'] . ' ' . $studentData['tb_student_name'] . ' ' . $studentData['tb_student_sname'];
            switch ($studentrooms) {
                case 1:
                    $roomDisplay = 'ม.1/1';
                    break;
                case 2:
                    $roomDisplay = 'ม.1/2';
                    break;
                case 3:
                    $roomDisplay = 'ม.1/3';
                    break;
                case 4:
                    $roomDisplay = 'ม.2/1';
                    break;
                case 5:
                    $roomDisplay = 'ม.2/2';
                    break;
                case 6:
                    $roomDisplay = 'ม.2/3';
                    break;
                case 7:
                    $roomDisplay = 'ม.3/1';
                    break;
                case 8:
                    $roomDisplay = 'ม.3/2';
                    break;
                case 9:
                    $roomDisplay = 'ม.3/3';
                    break;
                case 10:
                    $roomDisplay = 'ม.4/1';
                    break;
                case 11:
                    $roomDisplay = 'ม.4/2';
                    break;
                case 12:
                    $roomDisplay = 'ม.4/3';
                    break;
                case 13:
                    $roomDisplay = 'ม.5/1';
                    break;
                case 14:
                    $roomDisplay = 'ม.5/2';
                    break;
                case 15:
                    $roomDisplay = 'ม.5/3';
                    break;
                case 16:
                    $roomDisplay = 'ม.6/1';
                    break;
                case 17:
                    $roomDisplay = 'ม.6/2';
                    break;
                case 18:
                    $roomDisplay = 'ม.6/3';
                    break;
                default:
                    $roomDisplay = 'ไม่ทราบ';
                    break;
            }
            $pdf->Cell(0, 7, iconv('utf-8', 'cp874', 'ชื่อนักเรียน: ' . $studentName . ' ' . 'รหัสนักเรียน: ' . $studentCode . ' ' . 'ชั้น: ' . $roomDisplay), 0, 1, 'C');
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

    $pdf->SetFont('THSarabunNewBold', '', 12);
    $pdf->Cell(20, 10, iconv('utf-8', 'cp874', 'วันที่'), 1, 0, 'C');
    $pdf->Cell(60, 10, iconv('utf-8', 'cp874', 'ครูผู้สอน'), 1, 0, 'C');
    $pdf->Cell(20, 10, iconv('utf-8', 'cp874', 'จำนวนคาบ'), 1, 0, 'C');
    $pdf->Cell(70, 10, iconv('utf-8', 'cp874', 'สาเหตุ'), 1, 1, 'C');

    $pdf->SetFont('THSarabunNew', '', 12);
    function compareStudents($a, $b)
    {

        $startDiff = strtotime($b['time']) - strtotime($a['time']);
        if ($startDiff !== 0) {
            return $startDiff;
        }
        return $a['rooms'] - $b['rooms'];
    }
    usort($students, 'compareStudents');
    foreach ($students as $student) {
        $roomNumber = is_numeric($student['rooms']) ? $student['rooms'] : 0;

        switch ($roomNumber) {
            case 1:
                $roomDisplay = 'ม.1/1';
                break;
            case 2:
                $roomDisplay = 'ม.1/2';
                break;
            case 3:
                $roomDisplay = 'ม.1/3';
                break;
            case 4:
                $roomDisplay = 'ม.2/1';
                break;
            case 5:
                $roomDisplay = 'ม.2/2';
                break;
            case 6:
                $roomDisplay = 'ม.2/3';
                break;
            case 7:
                $roomDisplay = 'ม.3/1';
                break;
            case 8:
                $roomDisplay = 'ม.3/2';
                break;
            case 9:
                $roomDisplay = 'ม.3/3';
                break;
            case 10:
                $roomDisplay = 'ม.4/1';
                break;
            case 11:
                $roomDisplay = 'ม.4/2';
                break;
            case 12:
                $roomDisplay = 'ม.4/3';
                break;
            case 13:
                $roomDisplay = 'ม.5/1';
                break;
            case 14:
                $roomDisplay = 'ม.5/2';
                break;
            case 15:
                $roomDisplay = 'ม.5/3';
                break;
            case 16:
                $roomDisplay = 'ม.6/1';
                break;
            case 17:
                $roomDisplay = 'ม.6/2';
                break;
            case 18:
                $roomDisplay = 'ม.6/3';
                break;
            default:
                $roomDisplay = 'ไม่ทราบ';
                break;
        }

        $yearFormatted = date('Y', strtotime($student['time'])) + 543;
        $pdf->Cell(20, 10, iconv('utf-8', 'cp874', date('d/m/', strtotime($student['time'])) . $yearFormatted), 1, 0, 'L');
        $pdf->Cell(60, 10, iconv('utf-8', 'cp874', $student['name_title'] . ' ' . $student['name'] . ' ' . $student['surname']), 1, 0, 'L');
        $periodNumbers = explode(',', $student['period']);
        $numberCount = count($periodNumbers);
        $totalNumberCount += $numberCount;
        $pdf->Cell(20, 10, iconv('utf-8', 'cp874', $numberCount), 1, 0, 'C');
        $pdf->Cell(70, 10, iconv('utf-8', 'cp874', $student['cause'] . '  ' . ($student['custom_cause'] ? '* ' . $student['custom_cause'] : '')), 1, 1, 'L');
    }
} else {
    $pdf->Cell(0, 10, iconv('utf-8', 'cp874', 'ไม่มีข้อมูลนักเรียนที่ขาด'), 0, 1, 'C');
}

$pdf->SetFont('THSarabunNewBold', '', 16);
$pdf->Cell(80, 10, iconv('utf-8', 'cp874', 'รวม' . ' '), 1, 0, 'R');
$pdf->Cell(20, 10, iconv('utf-8', 'cp874', '' . ' ' . $totalNumberCount), 1, 0, 'C');
$pdf->Cell(70, 10, iconv('utf-8', 'cp874', '' . ' '), 1, 0, 'R');

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
    }
}
$pdf->SetFont('THSarabunNew', '', '16');
$pdf->Cell(0, 30, iconv('utf-8', 'cp874', ''), 0, 1, 'C');
$pdf->Cell(0, 10, iconv('utf-8', 'cp874', 'ลงชื่อ .................................................'), 0, 1, 'C');
$pdf->Cell(0, 7, iconv('utf-8', 'cp874', '' . $name_title . ' ' . $name . ' ' . $surname), 0, 1, 'C');
ob_end_clean();
$filename = "report_" . date('Y-m-d') . ".pdf";

header("Content-Type: application/pdf");
header("Content-Disposition: attachment; filename=\"$filename\"");
$pdf->Output();
