<?php
$db_host = 'localhost';
$db_name = 'ecommercephp';
$db_user = 'root';
$db_pass = 'Cuccutthan1@'; 

try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Lỗi kết nối CSDL: " . $e->getMessage());
}

if (!function_exists('setFlash')) {
    function setFlash($type, $msg) {
        $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
    }
}

if (!function_exists('getFlash')) {
    function getFlash() {
        if (isset($_SESSION['flash'])) {
            $f = $_SESSION['flash'];
            $cls = ($f['type'] == 'success') ? 'alert-success' : 'alert-danger';
            echo "<div class='alert $cls alert-dismissible fade show' role='alert'>
                    {$f['msg']}
                    <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                  </div>";
            unset($_SESSION['flash']);
        }
    }
}
?>