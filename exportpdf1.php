<?php
require('fpdf186/fpdf.php');

$selectedCourse = isset($_GET['course']) ? $_GET['course'] : '';
$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : date('Y-m-d');
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : date('Y-m-d');
$cause1 = 'ขาดเรียน';
$cause2 = 'ลาป่วย';
$cause3 = 'ลากิจ';
$cause4 = 'หนีเรียน';

require_once 'connect.php';

$sql = "SELECT s.tb_student_tname, s.tb_student_name, s.tb_student_sname, s.tb_student_sex, s.tb_student_degree, s.tb_student_code,
        SUM(CASE WHEN c.cause = :cause1 THEN 1 ELSE 0 END) AS count_absent,
        SUM(CASE WHEN c.cause = :cause2 THEN 1 ELSE 0 END) AS count_sick_leave,
        SUM(CASE WHEN c.cause = :cause3 THEN 1 ELSE 0 END) AS count_leave,
        SUM(CASE WHEN c.cause = :cause4 THEN 1 ELSE 0 END) AS count_skip
FROM ck_checking c
JOIN ck_students s ON c.absent = s.tb_student_code
WHERE 1=1 ";

if ($selectedCourse) {
    $sql .= " AND c.courses = :courseCode";
}

if ($startDate && $endDate) {
    $sql .= " AND DATE(c.time) BETWEEN :startDate AND :endDate";
}

$sql .= " GROUP BY c.absent ORDER BY 
          s.tb_student_degree ASC, 
          s.tb_student_sex ASC, 
          c.absent ASC";

$stmt = $conn->prepare($sql);

$stmt->bindParam(':cause1', $cause1);
$stmt->bindParam(':cause2', $cause2);
$stmt->bindParam(':cause3', $cause3);
$stmt->bindParam(':cause4', $cause4);

if ($selectedCourse) {
    $stmt->bindParam(':courseCode', $selectedCourse);
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


$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();
$pdf->AddFont('THSarabunPSK', '', 'THSarabunPSK.php');
$pdf->AddFont('THSarabunBoldPSK', '', 'THSarabunBoldPSK.php');
ob_start();

if (count($students) > 0) {
    $pdf->SetFont('THSarabunPSK', '', '16');
    $pdf->Cell(0, 1, iconv('utf-8', 'cp874', 'SAC - 1'), 0, 1, 'R');
    $pdf->SetFont('THSarabunBoldPSK', '', '18');
    $pdf->Cell(0, 7, iconv('utf-8', 'cp874', 'รายงานการขาดเรียน'), 0, 1, 'C');

    function formatDateThai($date)
    {
        $dateTime = new DateTime($date);
        $thaiMonths = array(
            'มกราคม',
            'กุมภาพันธ์',
            'มีนาคม',
            'เมษายน',
            'พฤษภาคม',
            'มิถุนายน',
            'กรกฎาคม',
            'สิงหาคม',
            'กันยายน',
            'ตุลาคม',
            'พฤศจิกายน',
            'ธันวาคม'
        );
        $formattedDateThai = $dateTime->format('d') . ' ' . $thaiMonths[$dateTime->format('m') - 1] . ' ' . ($dateTime->format('Y') + 543);
        return $formattedDateThai;
    }

    $startDateFormattedThai = formatDateThai($startDate);
    $endDateFormattedThai = formatDateThai($endDate);
    $pdf->Cell(0, 7, iconv('utf-8', 'cp874', 'ระหว่างวันที่: ' . $startDateFormattedThai . ' ถึงวันที่: ' . $endDateFormattedThai), 0, 1, 'C');
    $pdf->Cell(0, 7, iconv('utf-8', 'cp874', ''), 0, 1, 'C');

    $pdf->SetFont('THSarabunBoldPSK', '', 16);
    $pdf->Cell(15, 8, iconv('utf-8', 'cp874', 'ลำดับ'), 1, 0, 'C');
    $pdf->Cell(30, 8, iconv('utf-8', 'cp874', 'รหัสนักเรียน'), 1, 0, 'C');
    $pdf->Cell(75, 8, iconv('utf-8', 'cp874', 'ชื่อ-นามสกุล'), 1, 0, 'C');
    $pdf->Cell(25, 8, iconv('utf-8', 'cp874', 'ระดับชั้น'), 1, 0, 'C');
    $pdf->Cell(25, 8, iconv('utf-8', 'cp874', 'ลาป่วย'), 1, 0, 'C');
    $pdf->Cell(25, 8, iconv('utf-8', 'cp874', 'ลากิจ'), 1, 0, 'C');
    $pdf->Cell(25, 8, iconv('utf-8', 'cp874', 'ขาดเรียน'), 1, 0, 'C');
    $pdf->Cell(25, 8, iconv('utf-8', 'cp874', 'หนีเรียน'), 1, 0, 'C');
    $pdf->Cell(25, 8, iconv('utf-8', 'cp874', 'รวม'), 1, 1, 'C');

    $pdf->SetFont('THSarabunPSK', '', 16);
    $counter = 1;
    $totalSickLeaveCount = 0;
    $totalLeaveCount = 0;
    $totalAbsentCount = 0;
    $totalSkipCount = 0;

    foreach ($students as $student) {
        $pdf->Cell(15, 8, iconv('utf-8', 'cp874', $counter), 1, 0, 'C');
        $pdf->Cell(30, 8, iconv('utf-8', 'cp874', $student['tb_student_code']), 1, 0, 'C');
        $pdf->Cell(75, 8, iconv('utf-8', 'cp874', $student['tb_student_tname'] . ' ' . $student['tb_student_name'] . ' ' . $student['tb_student_sname']), 1, 0, 'L');
        $degree = isset($roomMapping[$student['tb_student_degree']]) ? $roomMapping[$student['tb_student_degree']] : 'ไม่ระบุ';
        $pdf->Cell(25, 8, iconv('utf-8', 'cp874', $degree), 1, 0, 'C');

        // ใช้เครื่องหมาย - แทนค่าศูนย์
        $sickLeave = $student['count_sick_leave'] == 0 ? '-' : $student['count_sick_leave'];
        $leave = $student['count_leave'] == 0 ? '-' : $student['count_leave'];
        $absent = $student['count_absent'] == 0 ? '-' : $student['count_absent'];
        $skip = $student['count_skip'] == 0 ? '-' : $student['count_skip'];

        $pdf->Cell(25, 8, iconv('utf-8', 'cp874', $sickLeave), 1, 0, 'C');
        $pdf->Cell(25, 8, iconv('utf-8', 'cp874', $leave), 1, 0, 'C');
        $pdf->Cell(25, 8, iconv('utf-8', 'cp874', $absent), 1, 0, 'C');
        $pdf->Cell(25, 8, iconv('utf-8', 'cp874', $skip), 1, 0, 'C');

        $total = $student['count_sick_leave'] + $student['count_leave'] + $student['count_absent'] + $student['count_skip'];
        $total = $total == 0 ? '-' : $total;
        $pdf->Cell(25, 8, iconv('utf-8', 'cp874', $total), 1, 1, 'C');

        // บวกค่าเพื่อรวม
        $totalSickLeaveCount += $student['count_sick_leave'];
        $totalLeaveCount += $student['count_leave'];
        $totalAbsentCount += $student['count_absent'];
        $totalSkipCount += $student['count_skip'];

        $counter++;
    }


    $pdf->Cell(245, 8, iconv('utf-8', 'cp874', 'รวมทั้งหมด'), 1, 0, 'C');
    $finalTotal = $totalSickLeaveCount + $totalLeaveCount + $totalAbsentCount + $totalSkipCount;
    $finalTotal = $finalTotal == 0 ? '-' : $finalTotal;
    $pdf->Cell(25, 8, iconv('utf-8', 'cp874', $finalTotal), 1, 1, 'C');
    $pdf->Cell(0, 30, iconv('utf-8', 'cp874', ''), 0, 1, 'C');
} else {
    $pdf->SetFont('THSarabunPSK', '', '16');
    $pdf->Cell(0, 7, iconv('utf-8', 'cp874', 'ไม่พบข้อมูล'), 0, 1, 'C');
}

$pdf->Cell(65, 8, iconv('utf-8', 'cp874', 'ลงชื่อ .................................................'), 0, 0, 'C');
$pdf->Cell(65, 8, iconv('utf-8', 'cp874', 'ลงชื่อ .................................................'), 0, 0, 'C');
$pdf->Cell(65, 8, iconv('utf-8', 'cp874', 'ลงชื่อ .................................................'), 0, 1, 'C');

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

$pdf->Cell(65, 7, iconv('utf-8', 'cp874', 'หัวหน้าฝ่ายบริหารวิชาการ'), 0, 0, 'C');
$pdf->Cell(65, 7, iconv('utf-8', 'cp874', 'รองผู้อำนวยการโรงเรียนถ้ำปินวิทยาคม'), 0, 0, 'C');
$pdf->Cell(65, 7, iconv('utf-8', 'cp874', 'ผู้อำนวยการโรงเรียนถ้ำปินวิทยาคม'), 0, 1, 'C');
ob_end_clean();
$filename = "report_" . date('Y-m-d') . ".pdf";

header("Content-Type: application/pdf");
header("Content-Disposition: attachment; filename=\"$filename\"");
$pdf->Output();
