<?php

include_once 'constantes.php';

function getConexionPDO()
{
	
}



function getConexionMySQLi()
{
    
}
function getConexionMySQLi_sin_bbdd()
{
    
}


function crearBBDD_MySQLi($basedatos){
    $conexion = getConexionMySQLi_sin_bbdd();
    $sql="select schema_name from information_schema.schemata where schema_name='$basedatos'";
    $stm=$conexion->prepare($sql);
    $stm->execute();
    
    $stm->bind_result($nombre_db);
    $existe=$stm->fetch();
    $stm->close();
   // $existe=0;
    if(!$existe){
        //crear la base de datos
        if ($conexion->query("CREATE DATABASE $basedatos") === true) { //ejecutando query
            
            echo "Base de datos $basedatos creada en MySQL por Objetos ";
            echo "<br>";
            
            
        } else {
            
            echo "Error al ejecutar consulta " . $this->conexion->error . " ";
             $existe=1;  
        }
        print_r ("Estoy aqui0");
        
    }
    $conexion->close();
    return $existe;
   
    
}

function crearTablas_MySQLi($basedatos){
    ini_set("display_errors",true);
    $conexion = getConexionMySQLi_sin_bbdd();
    $conexion->select_db($basedatos); 
  
    $existe_l=0;
    $libros2="
        CREATE TABLE libros (
        titulo varchar(50) NOT NULL,
        anyo_edicion int(11) NOT NULL,
        precio float(10,2) NOT NULL,
        fecha_adquisicion date NOT NULL,
        numero_ejemplar int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY 
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";
    
    // Pasamos la variable $strInsert para ejecurar el query
    //print_r ("Estoy aqui1");
    if ($conexion->query($libros2) === true) { //ejecutando query para la creaci�n de una tabla en MySQL
        
        echo "Tabla libros creada en MYSQL";
        echo "<br>";
        $existe_l=1;
        
    } else {
        
        echo "Error al crear tabla libros2 en MySQL " . $conexion->error . " ";
    }
   
    $existe_lg=0;
    $logins2="
        CREATE TABLE logins (
        usuario varchar(50) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL PRIMARY KEY,
        passwd char(32) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL 
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";
    
    if ($conexion->query($logins2) === true) { //ejecutando query para la creaci�n de una tabla en MySQL
        
        echo "Tabla logins creada en MYSQL";
        echo "<br>";
        $existe_lg=1;
        
    } else {
        
        echo "Error al crear tabla logins2 en MySQL " . $conexion->error . " ";
    }
    
    $conexion->close();
    if (($existe_l==1) && ($existe_lg==1)) return 1;
    
}

function crearBBDD($basedatos){
    try {
        $conexion = getConexionPDO();
        
        $sql = "SELECT schema_name FROM information_schema.schemata WHERE schema_name = :basedatos";
        $stm = $conexion->prepare($sql);
        $stm->bindParam(':basedatos', $basedatos);
        $stm->execute();
        
        $existe = $stm->fetch(PDO::FETCH_ASSOC);
        
        if (!$existe) {
            // Crear la base de datos
            $sql = "CREATE DATABASE $basedatos";
            if ($conexion->exec($sql) !== false) {
                echo "Base de datos $basedatos creada en MySQL por Objetos ";
                echo "<br>";
            } else {
                echo "Error al ejecutar consulta: " . $conexion->errorInfo();
                $existe = 1;
            }
            print_r("Estoy aqui0");
        }
        
        return $existe;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return 1;
    }
}


function crearTablas($basedatos){
    ini_set("display_errors", true);
    try {
        $conexion = getConexionPDO();
        $existe_l = 0;
        $libros2 = "
            CREATE TABLE libros (
            titulo varchar(50) NOT NULL,
            anyo_edicion int(11) NOT NULL,
            precio float(10,2) NOT NULL,
            fecha_adquisicion date NOT NULL,
            numero_ejemplar int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";
        
        if ($conexion->exec($libros2) !== false) {
            echo "Tabla libros creada en MySQL";
            echo "<br>";
            $existe_l = 1;
        } else {
            echo "Error al crear tabla libros en MySQL: " . $conexion->errorInfo();
        }
        
        $existe_lg = 0;
        $logins2 = "
            CREATE TABLE logins (
            usuario varchar(50) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL PRIMARY KEY,
            passwd char(32) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";
        
        if ($conexion->exec($logins2) !== false) {
            echo "Tabla logins creada en MySQL";
            echo "<br>";
            $existe_lg = 1;
        } else {
            echo "Error al crear tabla logins en MySQL: " . $conexion->errorInfo();
        }
        
        if ($existe_l == 1 && $existe_lg == 1) return 1;
        
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return 0;
    }
}



function usuarioCorrecto_MySQLi($usuario, $password)
{
    $conexion = getConexionMySQLi();
    
    $r=false;
    $sql="select usuario from logins where usuario=? and passwd=?";
    $stm=$conexion->prepare($sql);
    $p=md5($password);
    $stm->bind_param("ss",$usuario,$p);
    $stm->execute();
    $stm->bind_result($u);
    if($stm->fetch()){
        $r=true;
    }
    $stm->close();
    return $r;
}

function usuarioCorrecto($usuario, $password)
{
    try {
        $conexion = getConexionPDO();
        
        $r = false;
        $sql = "SELECT usuario FROM logins WHERE usuario = :usuario AND passwd = :passwd";
        $stm = $conexion->prepare($sql);
        $p = md5($password);
        $stm->bindParam(':usuario', $usuario);
        $stm->bindParam(':passwd', $p);
        $stm->execute();
        
        if ($stm->fetch(PDO::FETCH_ASSOC)) {
            $r = true;
        }
        
        return $r;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}



function registrarUsuario_MySQLi($usuario, $password)
{
    $correcto = false;
    
    $conexion  = getConexionMySQLi();
    //$password = md5($password); //Se puede encriptar mediante php o mediante MySQL
    $sql      = "INSERT INTO logins (usuario,passwd) VALUES (?,md5(?))";
    $consulta = $conexion->prepare($sql);
    $consulta->bind_param("ss",$usuario,$password);
    
    $consulta->execute();
    $consulta->close();
    $conexion->close();       
        
}

function registrarUsuario($usuario, $password)
{
    try {
        $conexion = getConexionPDO(); 
        $sql = "INSERT INTO logins (usuario, passwd) VALUES (:usuario, MD5(:passwd))";
        $consulta = $conexion->prepare($sql);
        $consulta->bindParam(':usuario', $usuario);
        $consulta->bindParam(':passwd', $password);
        
        $consulta->execute();
        $conexion = null; // Cerrar la conexi�n
        
        echo "Usuario registrado correctamente.";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

function insertarLibro_MySQLi($titulo, $anyo, $precio, $fechaAdquisicion)
{
    $conexion  = getConexionMySQLi();
    
    $conexion->autocommit(false);
   
    $consultaInsert = $conexion->stmt_init();
    $sqlInsert      = "insert into libros (titulo, anyo_edicion, precio, fecha_adquisicion)  values(?,?,?,?)";
    $consultaInsert->prepare($sqlInsert);
    $consultaInsert->bind_param('sids', $titulo, $anyo, $precio, $fechaAdquisicion); //int, int, string
    $consultaInsert->execute();
    $filasAfectadasInsert = $consultaInsert->affected_rows;
    $consultaInsert->close();
    
    if ($filasAfectadasInsert == 1)
    {
        $conexion->commit();
        return true;
    }
    else
    {
        $conexion->rollback();
        return false;
    }
}


function insertarLibro($titulo, $anyo, $precio, $fechaAdquisicion)
{
    $conexion = getConexionPDO();
    $sql = "INSERT into libros (titulo, anyo_edicion, precio, fecha_adquisicion) values (?,?,?,?)";
    $sentencia = $conexion->prepare($sql);
    
    $sentencia->bindParam(1, $titulo);
    $sentencia->bindParam(2, $anyo);
    $sentencia->bindParam(3, $precio);
    $sentencia->bindParam(4, $fechaAdquisicion);
    $numero = $sentencia->execute();
    unset($sentencia);
    
    unset($conexion);
    
    if($numero==1)
        return true;
        return false;
}




function getLibros_MySQLi()
{
	$conexion = getConexionMySQLi();
    $consulta = "select * from libros";
    $libros=[];
    if ($resultado = $conexion->query($consulta))
    {
        while ($libro = $resultado->fetch_object())
        {
            $libros[] = $libro;
        }
        $resultado->close();
    } 
    $conexion->close();
    return $libros;
}

function getLibros()
{
    try {
        $conexion = getConexionPDO();        
        $consulta = "SELECT * FROM libros";
        $stm = $conexion->prepare($consulta);
        $stm->execute();
        
        $libros = [];
        while ($libro = $stm->fetch(PDO::FETCH_OBJ)) {
            $libros[] = $libro;
        }
        
        return $libros;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return [];
    }
}

function getLibrosTitulo_MySQLi()
{
    /*La tabla libros compuesta por los campos:
     * titulo, ciudad, conferencia y divisi�n
     * */
    $mysqli = getConexionMySQLi();
    $consulta = "select titulo from libros";
    
    if ($resultado = $mysqli->query($consulta))
    {
        
        /* obtener el array de objetos */
        while ($libro = $resultado->fetch_object())
        {
            $libros[] = $libro->titulo;
        }
        
        /* liberar el conjunto de resultados */
        $resultado->close();
    }
    $mysqli->close();
    echo $libros;
    return $libros;
    
}


function getLibrosTitulo()
{
    try {
        $conexion = getConexionPDO();        
        $consulta = "SELECT titulo FROM libros";
        $stm = $conexion->prepare($consulta);
        $stm->execute();
        
        $libros = [];
        while ($libro = $stm->fetch(PDO::FETCH_OBJ)) {
            $libros[] = $libro->titulo;
        }
        
        echo implode(", ", $libros);
        return $libros;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return [];
    }
}



function borrarLibro($numeroEjemplar)
{
    try {
        $conexion = getConexionPDO();
        $conexion->beginTransaction();
        $precio = 0;
        
        // Consulta para obtener el precio del libro
        $consulta = "SELECT precio FROM libros WHERE numero_ejemplar = :numero_ejemplar";
        $stm = $conexion->prepare($consulta);
        $stm->bindParam(':numero_ejemplar', $numeroEjemplar);
        $stm->execute();
        
        if ($libro = $stm->fetch(PDO::FETCH_ASSOC)) {
            $precio = $libro['precio'];
        }
        
        // Consulta para borrar el libro
        $sql = "DELETE FROM libros WHERE numero_ejemplar = :numero_ejemplar";
        $stm = $conexion->prepare($sql);
        $stm->bindParam(':numero_ejemplar', $numeroEjemplar);
        $stm->execute();
        
        $conexion->commit();
        return $precio;
    } catch (PDOException $e) {
        if ($conexion->inTransaction()) {
            $conexion->rollBack();
        }
        echo "Error: " . $e->getMessage();
        return 0;
    } finally {
        $conexion = null; // Cerrar la conexi�n
    }
}

function borrarLibro_MySQLi($numeroEjemplar)
{
  
    
    $conexion = getConexionMySQLi();
    $precio = 0;
    
    $todo_bien = true;            // Definimos una variable para comprobar la ejecución
    $conexion->autocommit(false); // Deshabilitamos el modo transaccional automático
    
    
    $consulta = "select precio from libros WHERE numero_ejemplar = $numeroEjemplar";
    
    $resultado = $conexion->query($consulta);
    if ($resultado)
    {
        if ($libro = $resultado->fetch_array())
        {
            $precio = $libro['precio'];
        }
        $resultado->close();
    }
    
  

    
    $consultaDelete = $conexion->stmt_init();
    $sql = "DELETE FROM libros WHERE numero_ejemplar =  $numeroEjemplar";
   
    
    $consultaDelete->prepare($sql);
    
    if (!$consultaDelete->execute())
    {
        $todo_bien = false;
    }
    $consultaDelete->close();
    
    // Si todo fue bien, confirmamos los cambios y en caso contrario los deshacemos
    if ($todo_bien == true)
    {
        $conexion->commit();
    }
    else
    {
        $conexion->rollback();
    }
    
    $conexion->close();
    return $precio;    
}


function modificarLibroAnyo_MySQLi($numero_ejemplar,$anyo_edicion)
{
    $conexion  = getConexionMySQLi();
    
    $conexion->autocommit(false);
    
    
    
    $consultaInsert = $conexion->stmt_init();
    $sqlInsert      = "update libros set anyo_edicion=? where numero_ejemplar=?";
    $consultaInsert->prepare($sqlInsert);
    
    $consultaInsert->bind_param("ss", $anyo_edicion[0], $numero_ejemplar[0]);
    $consultaInsert->execute();
    //print_r($conexion);
    
    $filasAfectadasInsert = $consultaInsert->affected_rows;
    
    
    
    
    
    $consultaInsert->close();
    
    
    if ($filasAfectadasInsert == 1)
    {
        $conexion->commit();
       
        return true;
    }
    else
    {
        $conexion->rollback();
        
        return false;
    }
}

function arrayFlotante($array) {
    // Verificar que el array no est� vac�o
    if (empty($array)) {
        return 0.0;
    }
    
    // Concatenar todos los elementos del array en una sola cadena
    $cadena = implode('', $array);
    
    // Verificar que la cadena resultante sea un n�mero v�lido
    if (is_numeric($cadena)) {
        // Convertir la cadena a un n�mero flotante
        $flotante = (float)$cadena;
        return $flotante;
    } else {
        echo "Error: La cadena resultante no es un n�mero v�lido.";
        return 0.0;
    }
}


function modificarLibroAnyo($numero_ejemplar, $anyo_edicion)
{
    
    
    try {
        $conexion = getConexionPDO();
        $conexion->beginTransaction();
        $numero_ejemplar= $numero_ejemplar[0];
        $anyo_edicion = $anyo_edicion[0];
        $sqlUpdate = "UPDATE libros SET anyo_edicion = :anyo_edicion WHERE numero_ejemplar = :numero_ejemplar";
        $consultaUpdate = $conexion->prepare($sqlUpdate);
        $consultaUpdate->bindParam(":numero_ejemplar", $numero_ejemplar, PDO::PARAM_INT);
        $consultaUpdate->bindParam(":anyo_edicion", $anyo_edicion,PDO::PARAM_INT);
        
        $consultaUpdate->execute();
        
        $filasAfectadasUpdate = $consultaUpdate->rowCount();
        
        //echo "Estoy aqu�".$filasAfectadasUpdate;
        if ($filasAfectadasUpdate == 1) {
            $conexion->commit();
            //echo "Estoy aqu�2";
            return true;
        } else {
            $conexion->rollBack();
            //echo "Estoy aqu�3";
            return false;
        }
    } catch (PDOException $e) {
        if ($conexion->inTransaction()) {
            $conexion->rollBack();
        }
        echo "Error: " . $e->getMessage();
        return false;
    } finally {
        $conexion = null; // Cerrar la conexi�n
    }
}


function getLibrosAnyo_MySQLi($libro)
{
    /*La tabla libros     * */
    $mysqli = getConexionMySQLi();
    $consulta = "select numero_ejemplar, titulo,anyo_edicion, precio from libros where titulo = '$libro'";
    
    if ($resultado = $mysqli->query($consulta))
    {
        
        while ($libro = $resultado->fetch_array())
        {
            $libros[] = array("numero_ejemplar" => $libro["numero_ejemplar"], "titulo" => $libro["titulo"], "anyo_edicion" => $libro["anyo_edicion"] ,"precio" => $libro["precio"]);
        }
        
        $resultado->close();
    }
    $mysqli->close();
    return $libros;
}




function getLibrosAnyo($libro)
{
    try {
        $conexion = getConexionPDO();
        $consulta = "SELECT numero_ejemplar, titulo, anyo_edicion, precio FROM libros WHERE titulo = :titulo";
        $stm = $conexion->prepare($consulta);
        $stm->bindParam(':titulo', $libro);
        $stm->execute();
        
        $libros = [];
        while ($libro = $stm->fetch(PDO::FETCH_ASSOC)) {
            $libros[] = array(
                "numero_ejemplar" => $libro["numero_ejemplar"],
                "titulo" => $libro["titulo"],
                "anyo_edicion" => $libro["anyo_edicion"],
                "precio" => $libro["precio"]
            );
        }
        
        return $libros;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return [];
    }
}


?>
