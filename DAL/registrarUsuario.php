<?php
function recoge($var, $m = "")
{
    if (!isset($_REQUEST[$var])) {
        $tmp = (is_array($m)) ? [] : "";
    } elseif (!is_array($_REQUEST[$var])) {
        $tmp = trim(htmlspecialchars($_REQUEST[$var], ENT_QUOTES, "UTF-8"));
    } else {
        $tmp = $_REQUEST[$var];
        array_walk_recursive($tmp, function (&$valor) {
            $valor = trim(htmlspecialchars($valor, ENT_QUOTES, "UTF-8"));
        });
    }
    return $tmp;
}

$username = recoge('username');
$password = recoge('password');
$nombre = recoge('nombre');
$primer_apellido = recoge('primerApellido');
$segundo_apellido = recoge('segundoApellido');
$correo = recoge('correo');
$telefono = recoge('telefono');

$errores = [];

// Validación de datos
if (empty($username)) {
    $errores[] = "No anotó un nombre de usuario válido";
}

if (empty($password)) {
    $errores[] = "No anotó una contraseña";
}

if (empty($nombre)) {
    $errores[] = "No ingresó el nombre";
}

if (empty($primer_apellido)) {
    $errores[] = "No ingresó el primer apellido";
}

if (empty($segundo_apellido)) {
    $errores[] = "No ingresó el segundo apellido";
}

// Si hay errores, imprimirlos y salir
if (!empty($errores)) {
    foreach ($errores as $error) {
        echo "<p class='aviso'>$error</p>";
    }
    exit;
}

// Hash de la contraseña
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Conectar a la base de datos
$conexion = new mysqli("localhost", "root", "", "sucato");

// Verificar la conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
    
}

// Preparar la consulta SQL para insertar los datos en la tabla
$consulta = $conexion->prepare("INSERT INTO usuario (username, password, nombre, primer_apellido, segundo_apellido, correo, telefono, activo) VALUES (?, ?, ?, ?, ?, ?, ?, 1)");

// Vincular parámetros y ejecutar la consulta
$consulta->bind_param("sssssss", $username, $hashed_password, $nombre, $primer_apellido, $segundo_apellido, $correo, $telefono);
$consulta->execute();

// Verificar si se insertaron los datos correctamente
if ($consulta->affected_rows > 0) {
    echo "Registro exitoso.";
} else {
    echo "Error al registrar el usuario.";
}

// Cerrar la conexión y liberar los recursos
$consulta->close();
$conexion->close();
?>
