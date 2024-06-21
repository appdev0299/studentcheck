
<?php
if (
    isset($_POST['id'])
    && isset($_POST['email'])    && isset($_POST['password'])
    && isset($_POST['name_title'])
    && isset($_POST['name'])
    && isset($_POST['surname'])
    && isset($_POST['position'])
    && isset($_POST['groups'])
    && isset($_POST['status'])
) {
    require_once 'connect.php';

    $id = $_POST['id'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $name_title = $_POST['name_title'];
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $position = $_POST['position'];
    $groups = $_POST['groups'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("INSERT INTO ck_users (`id`, `email`, `password`,`name_title`,`name`, `surname`, `position`, `groups`, `status`)
                            VALUES (:id, :email, :password, :name_title, :name, :surname, :position, :groups, :status)");
    $stmt->bindParam(':id', $id, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':password', $password, PDO::PARAM_INT);
    $stmt->bindParam(':name_title', $name_title, PDO::PARAM_STR);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':surname', $surname, PDO::PARAM_STR);
    $stmt->bindParam(':position', $position, PDO::PARAM_STR);
    $stmt->bindParam(':groups', $groups, PDO::PARAM_STR);
    $stmt->bindParam(':status', $status, PDO::PARAM_STR);

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
            window.location.href = "import_users.php";
        });
        </script>';
    } else {
        echo '<script>
        swal({
            title: "เกิดข้อผิดพลาดในการบันทึกข้อมูล",
            type: "error"
        }, function() {
            window.location = "import_users.php";
        });
        </script>';
    }
}
