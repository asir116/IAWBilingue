<?php
define ("HOST", "localhost");
define ("USERNAME", "root");
define ("PASSWORD", "");
define ("DATABASE", "libros");
function conectar() {
$conexion  = new mysqli(HOST, USERNAME, PASSWORD, DATABASE);
$conexion->set_charset("utf8");
$error = $conexion->connect_errno;
// error conexión
if ($error != null)
{
    print "<p>Se ha producido el error: $conexion->connect_error.</p>";
    exit();
}
return $conexion;
}

function explorerData(){
    $conexion = conectar();
    $sql= "SELECT * FROM libros";
    $result = $conexion->query($sql);
    // comprobar numero de filas de la respuesta
    if ($result->num_rows > 0)
    {
        while ($fila = $result->fetch_object()) {
        }
        echo "ID: " . $fila->id . "Tïtulo: " . $fils->titulo . "<br>"; 
    }
}
function borrarData(){
    $conexion = conectar();
    $sql= "DELETE * FROM Libros";
    $result = $conexion->query($sql);
    if ($result === TRUE){
        echo "Todos los registros borrados";
    } else{
        echo "Error al borrar los registros";
    }
}
function insertartData(){
    $conexion = conectar();
    $sql= "INSERT INTO libros (titulo, autor) VALUES (Biblia, Dios)";
    $result = $conexion->query($sql);
    if ($result === TRUE) {
        echo "Se han insertado los datos con éxito";
    } else {
        echo "Hubo un error al insertar los datos";
    }
}
function updatearData(){
    $conexion = conectar();
    $sql= "UPDATE libros SET titulo = Biblia 2, autor = JordiWild";
    $result = $conexion->query($sql);
    if ($result === TRUE) {
        echo "Se han actualizado los datos con éxito";
    } else {
        echo "Hubo un error al actualizado los datos";
    }
}

$conexion->close();

   