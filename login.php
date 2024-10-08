<?php session_start();

// Hacemos la conexión a la base de datos
require 'conexion/conexion.php';
$errores = 0;

if (isset($_SESSION['usuarios'])) {
    $email = $_SESSION['usuarios'];
    $consultarROl = $conexion->prepare('SELECT id_roles FROM usuarios WHERE Correo_Electronico = :correo');
    $consultarROl->execute(array(':correo' => $email));
    $resultadoConsulta = $consultarROl->fetch();

    if ($resultadoConsulta['id_roles'] == 2) { // El cliente
        header('Location: dashboard.php');
        exit();
    } else if ($resultadoConsulta['id_roles'] == 1) { // Si es administrador
        header('Location: admin/dashboard.php');
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Correo = isset($_POST['usuario']) ? $_POST['usuario'] : null;
    $Contrasena = isset($_POST['Contrasena']) ? $_POST['Contrasena'] : null;

    // Validar que no estén vacíos
    if (empty($Correo) || empty($Contrasena)) {
        echo "vacio";
    } else {

        // Consultar si el usuario existe
        $q = $conexion->prepare("SELECT * FROM usuarios WHERE Correo_Electronico = :correo AND Contrasena = :contrasena");
        
        $q->execute(array(':correo' => $Correo, ':contrasena' => $Contrasena));
        
        $resultadoq = $q->fetchAll();

        //if ($resultadoq  && password_verify($Contrasena, $resultadoq['Contrasena'])){

            // Si hay resultados
            if (count($resultadoq) > 0) {
            $_SESSION['Correo_Electronico'] = $Correo;
            $usuario = $resultadoq[0]; // Obtén el primer usuario

                // Roles
                if ($usuario['id_roles'] == 2) { // El cliente
                    header('Location: dashboard.php');
                    exit();
                } else if ($usuario['id_roles'] == 1) { // Si es administrador
                    header('Location: admin/dashboard.php');
                    exit();
                }

            } else {
                echo '
                    <script>
                        alert("Datos incorrectos");
                        window.location = "signup.php";
                    </script>';
                exit();
            }
        //}
    }
}

require "views/login.view.php";
?>