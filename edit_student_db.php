<?php

if (
  isset($_POST['id'])
  && isset($_POST['cause'])
  && isset($_POST['custom_cause'])
) {
  $id = $_POST['id'];
  $cause = $_POST['cause'];
  $custom_cause = $_POST['custom_cause'];

  // SQL update
  $stmt = $conn->prepare("UPDATE ck_checking SET cause=:cause, custom_cause=:custom_cause WHERE id=:id");
  $stmt->bindParam(':id', $id, PDO::PARAM_INT);
  $stmt->bindParam(':cause', $cause, PDO::PARAM_STR);
  $stmt->bindParam(':custom_cause', $custom_cause, PDO::PARAM_STR);
  $stmt->execute();
  echo '
    <script src="https://code.jquery.com/jquery-2.1.3.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert-dev.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">';
  echo '<script>
        swal({
          title: "แก้ไขข้อมูลสำเร็จ",
          type: "success",
          timer: 1500,
          showConfirmButton: false
        }, function(){
          window.location = "export_date.php";
        });
      </script>';
} else {
  echo '<script>
        swal({
          title: "แก้ไขข้อมูลไม่สำเร็จ",
          type: "fail",
          timer: 1500,
          showConfirmButton: false
        }, function(){
          window.location.href = "export_date.php";
        });
      </script>';
}
$conn = null; //close connect db
