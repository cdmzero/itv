<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/itv/dirs.php";
require_once CONTROLLER_PATH . "ControladorCoche.php";
require_once CONTROLLER_PATH . "ControladorImagen.php";
require_once UTILITY_PATH . "funciones.php";

error_reporting(E_ALL & ~(E_STRICT|E_NOTICE));
session_start();
if (!isset($_SESSION['email']) || empty($_SESSION['email'])) {
    header("location: login.php");
    exit();
}elseif($_SESSION['tipo'] != 'admin'){
    header("location: error.php");
    exit();
}

// Variables a procesar
$matricula = $marca = $modelo = $combustible = $titular = $dni = $telefono = $fecha_cita = $imagen = "";
$Valmatricula = $Valmarca = $Valmodelo = $Valcombustible = $Valtitular = $Valdni = $Valtelefono = $Valfecha_cita = $Valimagen = "";
$Errmatricula = $Errmarca = $Errmodelo = $Errcombustible = $Errtitular = $Errdni = $Errtelefono = $Errfecha_cita = $Errimagen = "";
$errores = [];



if ($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["aceptar"]) {

    // Matricula
    $Valmatricula = filtrado(($_POST["matricula"]));
    if (empty($Valmatricula)) {
        $Errmatricula = "Por favor introduzca un matricula válido con carácteres alfanumericos.";
        $errores[] = $Errmatricula ;
    } elseif (!preg_match("/^[0-9]{4}[a-zA-Z]{3}$/iu", $Valmatricula)) {
        $Errmatricula = "Por favor introduzca un matricula válida con solo carácteres alfanumericos.";
        $errores[] = $Errmatricula ;
    } else {
        $matricula = $Valmatricula;
    }
    
 

    // Procesamos Marca
    $Valmarca = filtrado($_POST["marca"]);
    if (empty($Valmarca)) {
        $Errmarca = "Debe elegir al menos una marca";
        $errores[]= $Errmarca ;
    }elseif(!preg_match("/^([A-Za-zÑñ]+[áéíóú]?[A-Za-z]*){1,18}?\s?([A-Za-zÑñ]+[áéíóú]?[A-Za-z]*){0,36}$/iu",$Valmarca)){
        $Errmarca = "Por favor introduzca una marca valida";
        $errores[]= $Errmarca;
    } else {
        $marca = $Valmarca;
    }

       // Procesamos Modelo
       $Valmodelo = filtrado($_POST["modelo"]);
       if (empty($Valmodelo)) {
           $Errmodelo = "Debe elegir al menos un modelo";
           $errores[]= $Errmodelo ;
       }elseif(!preg_match("/^([A-Za-zÑñ]+[áéíóú]?[A-Za-z]*){1,18}\s?([A-Za-zÑñ]+[áéíóú]?[A-Za-z]*){0,36}?[0-9]{0,5}$/iu",$Valmodelo)){
           $Errmodelo = "Por favor introduzca un marca valida";
           $errores[]= $Errmodelo;
       } else {
           $modelo = $Valmodelo;
       }
        //Combustible
        $Valcombustible = $_POST["combustible"];
        if (empty($Valcombustible)) {
            $Errcombustible = "Debe elegir al menos una opcion";
            $errores[]= $Errcombustible ;
        } else {
            $combustible = $Valcombustible;
        }

        //Titular
        $Valtitular = $_POST["titular"];
        if (empty($Valtitular)) {
                $Errtitular = "Debe elegir al menos un titular";
                $errores[] = $Errtitular;
        } elseif(!preg_match("/^([A-Za-zÑñ]+[áéíóú]?[A-Za-z]*){2,18}\s?([A-Za-zÑñ]+[áéíóú]?[A-Za-z]*){0,36}$/iu",$Valtitular)){
            $Errtitular = "Por favor introduzca un titular valido";
            $errores[]= $Errtitular ;
        } else {
            $titular = $Valtitular;
        }

        //Dni
        $Valdni = filtrado($_POST["dni"]);
        if (empty($Valdni)) {
            $Errdni = "Debe elegir al menos un dni";
            $errores[]= $Errdni ;
        } elseif(!preg_match("/^([0-9]){8}+([A-Za-z]){1}$/",$Valdni)){
            $Errdni = "Por favor introduzca un DNI valido";
            $errores[]= $Errdni ;
        } else {
            $dni= $Valdni;
        }

        //Telefono
        $Valtelefono= filtrado($_POST["telefono"]);
        if (empty($Valtelefono)) {
            $Errtelefono = "Debe elegir al menos un telefono" ;
            $errores[]= $Errtelefono ;
        }elseif( !(preg_match('/^[6|7|8|9][0-9]{8}$/', $Valtelefono)) || strlen($Valtelefono) > 9){
            $Errtelefono = "Por favor introduzca 9 numeros válidos";
            $errores[]= $Errtelefono;
        } else {
            $telefono = $Valtelefono;
        }

        //Fecha Cita
        $Valfecha_cita= filtrado($_POST["fecha_cita"]);
        if (empty($Valfecha_cita)) {
            $Errfecha_cita = "Debe elegir al menos una fecha" ;
            $errores[]= $Errfecha_cita ;
        } else {
            $Valfecha_cita=date("d-m-Y", strtotime(filtrado($_POST["fecha_cita"])));
            $hoy = date("d-m-Y");

            $Valfecha=new DateTime($Valfecha_cita);
            $hoy=new DateTime($hoy);
            if($Valfecha <= $hoy){
                $Errfecha_cita= "La fecha no puede ser hoy o anterior";
                $errores[]=$Errfecha;
            }else{
                $fecha_cita=date("d/m/Y", strtotime(filtrado($_POST["fecha_cita"])));
            
                $controlador = ControladorCoche::getControlador();
                $cocheVal = $controlador->buscarCocheyFecha($matricula,$fecha_cita);
                if (isset($cocheVal)) {
                    $Errmatricula = "Ya existe una cita para esa fecha para esta matricula";
                    $errores[]= $Errmatricula ;
                } else {
                    $matricula = $Valmatricula;
                }
            
            }
        }

        //

    // Procesamos la foto
    $propiedades = explode("/", $_FILES['imagen']['type']);
    $extension = $propiedades[1];
    $tam_max = 1000000; // 1 Mb
    $tam = $_FILES['imagen']['size'];
    $mod = true; // para modificar

    // Si no coicide la extensión
    if ($extension != "jpg" && $extension != "jpeg") {
        $mod = false;
        $imagenErr = "Formato debe ser jpg/jpeg";
    }
    // si no tiene el tamaño
    if ($tam > $tam_max) {
        $mod = false;
        $Errimagen = "Tamaño superior al limite de 1MB";
        $errores[]=  $Errimagen;
    }

    if ($mod) {
        //guardar imagen
        $imagen = md5($_FILES['imagen']['tmp_name'] . $_FILES['imagen']['name'] . time()) . "." . $extension;
        $controlador = ControladorImagen::getControlador();
        if (!$controlador->salvarImagen($imagen)) {
            $Errimagen = "Error al procesar la imagen y subirla al servidor";
            $errores[] =  $Errimagen;
        }
    }

    if (empty($errores)) {
        $controlador = ControladorCoche::getControlador();
        $estado = $controlador->almacenarCoche($matricula , $marca , $modelo , $combustible , $titular , $dni , $telefono , $fecha_cita, $imagen );
        if ($estado) {
        alerta('Coche adherido correctamente','../index.php');
        } else {
            alerta('Coche no adherido correctamente, revise los errores');
        }
    } else {
        alerta("Hay errores al procesar el formulario revise los errores");
    }

}
?>

<?php require_once VIEW_PATH . "navbar.php"; ?>
<head>

<div class="list-group">
    <a class="list-group-item active"> 
    <h2 class="list-group-item-heading">Formulario Para añadir Coche </h2>
    <p class="list-group-item-text">Todos los campos son requeridos para completarse la creacion.</p>
    </a>
</div>
<div class="well">
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel-content">     

<div class="lead">
<!-- Formulario-->
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
    <!-- matricula-->
    <div class="form-group <?php echo (!empty($Errmatricula)) ? 'error: ' : ''; ?>">
    
        <label>Matricula</label>
        <input type="text" required name="matricula" pattern="([0-9]{4}[a-zA-Z]{3})" maxlength="7" title="El matricula no puede contener números" value="<?php echo $Valmatricula; ?>">
        <span class="help-block"><?php echo $Errmatricula;?></span> 
    </div>
            <!-- marca-->
            <label>Marca</label>
        <input type="text" required name="marca" maxlength="25" pattern="^([A-Za-zÑñ]+[áéíóú]?[A-Za-z]*){3,18}\s?([A-Za-zÑñ]+[áéíóú]?[A-Za-z]*){0,36}$" title="la marca no puede contener números" value="<?php echo $Valmarca; ?>">
        <span class="help-block"><?php echo $Errmarca;?></span> 
</div>
        <!-- modelo-->
        <div class="form-group <?php echo (!empty($Errmodelo)) ? 'error: ' : ''; ?>">
        <label>Modelo</label>
        <input type="text" required name="modelo" maxlength="25" title="El modelo no puede contener números" value="<?php echo $Valmodelo; ?>">
        <span class="help-block"><?php echo $Errmodelo;?></span> 
</div>

    <!-- combustible -->
    <div class="form-group  <?php echo (!empty($Errcombustible)) ? 'error: ' : ''; ?>">
        <label>Combustible</label>
        <input type="radio" name="combustible" required value="Gasolina" <?php echo (strstr($Valcombustible, 'Gasolina')) ? 'checked' : ''; ?>>Gasolina</input>
        <input type="radio" name="combustible" required value="Gas" <?php echo (strstr($Valcombustible, 'Gas')) ? 'checked' : ''; ?>>Gas</input>
        <input type="radio" name="combustible" required value="Propano" <?php echo (strstr($Valcombustible, 'Propano')) ? 'checked' : ''; ?>>Propano</input>
        <input type="radio" name="combustible" required value="Vapor" <?php echo (strstr($Valcombustible, 'Vapor')) ? 'checked' : ''; ?>>Vapor</input>
        <input type="radio" name="combustible" required value="Electrico" <?php echo (strstr($Valcombustible, 'Electrico')) ? 'checked' : ''; ?>>Electrico</input>
        <input type="radio" name="combustible" required value="Hibrido" <?php echo (strstr($Valcombustible, 'Hibrido')) ? 'checked' : ''; ?>>Hibrido</input>
        <input type="radio" name="combustible" required value="Hidrogeno" <?php echo (strstr($Valcombustible, 'Hidrogeno')) ? 'checked' : ''; ?>>Hidrogeno</input>
        <span class="help-block"><?php echo $Errcombustible; ?></span> 
    </div>

<!-- Titular -->
<div class="form-group <?php echo (!empty($Errtitular)) ? 'error: ' : ''; ?>">
        <label>Titular</label>
        <input type="text" required name="titular" maxlength="25" pattern="^([A-Za-zÑñ]+[áéíóú]?[A-Za-z]*){3,18}\s?([A-Za-zÑñ]+[áéíóú]?[A-Za-z]*){0,36}$" title="El titular no puede contener números" value="<?php echo $Valtitular; ?>">
        <span class="help-block"><?php echo $Errtitular;?></span> 
</div>
<!-- Dni -->
<div class="form-group <?php echo (!empty($Errdni)) ? 'error: ' : ''; ?>">
        <label>Dni</label>
        <input type="text" required name="dni" maxlength="25" pattern="^[0|5][0-9]{7}[a-zA-Z]$" title="El formato del DNi no es valido" value="<?php echo $Valdni; ?>">
        <span class="help-block"><?php echo $Errdni;?></span> 
</div>
    <!-- Telefono -->
    <div class="form-group <?php echo (!empty($Errtelefono)) ? 'error: ' : ''; ?>">
        <label>Telefono</label>
        <input type="text" required name="telefono" maxlength="25" pattern="^[6|7|8|9][0-9]{8}$"  title="El telefono no puede contener números" value="<?php echo $Valtelefono; ?>">
        <span class="help-block"><?php echo $Errtelefono;?></span> 
</div>
<div class="form-group <?php echo (!empty($Errtelefono)) ? 'error: ' : ''; ?>">
<label>Fecha para la cita</label>
 <input type="date" required name="fecha_cita" value="<?php echo date('Y-m-d', strtotime(str_replace('/', '-', $Valfecha_cita)));?>"></input><div>
                            <span class="help-block"><?php echo $Errfecha_cita;?></span>
                        </div>


    <!-- Foto-->
    <div class="form-group <?php echo (!empty($imagenErr)) ? 'error: ' : ''; ?>">
        <label>Fotografía</label>
        <input type="file" required name="imagen" id="imagen" accept="image/jpeg">
        <span class="help-block"><?php echo $imagenErr; ?></span> 
    </div>
        </div> 
            </div>
                </div>
                    </div>
                        </div> 
                            </div>
                                </div>                             
    <span class="list-group-item text-center">
     <a onclick="history.back()" class="btn btn-primary"><span class="glyphicon glyphicon-chevron-left"></span> Volver</a>
     <button type="reset" value="reset" class="btn btn-info"> <span class="glyphicon glyphicon-repeat"></span>  Limpiar</button> 
     <button type="submit" name= "aceptar" value="aceptar" class=" btn btn-success" ><span class="glyphicon glyphicon-ok"></span><strong> Crear</h5></strong>  </button>


     </span>
    </div>
</form>
<br>
<br>
<br>
</section>

<?php require_once VIEW_PATH . "footer.php" ?>

