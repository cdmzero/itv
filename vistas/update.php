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
$imagenAnterior ="";
$Infoimagen="";



if (isset($_POST["id"]) && !empty($_POST["id"])) {
    $id = $_POST["id"];

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
   $matriculanterior = $_POST['matriculaAnterior'];
   
   // Hasta aqui llegue en 165 minutos

    // Procesamos Marca
    $Valmarca = filtrado($_POST["marca"]);
    if (empty($Valmarca)) {
        $Errmarca = "Debe elegir al menos una marca";
        $errores[]= $Errmarca ;
    }elseif(!preg_match("/^([A-Za-zÑñ]+[áéíóú]?[A-Za-z]*){2,18}\s?([A-Za-zÑñ]+[áéíóú]?[A-Za-z]*){0,36}$/iu",$Valmarca)){
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
           $Errmodelo = "Por favor introduzca un modelo valido";
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
    $Errfecha_cita = "Debe elegir al menos un telefono" ;
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

        $fechaAnterior=filtrado($_POST['fechaAnterior']);
       if ($fechaAnterior != $fecha_cita){
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
}
    //imagen
    if ($_FILES['imagen']['size'] > 0 && count($errores) == 0) {
        $propiedades = explode("/", $_FILES['imagen']['type']);
        $extension = $propiedades[1];
        $tam_max = 1000000; // 1MB 
        $tam = $_FILES['imagen']['size'];
        $mod = true;

        if ($extension != "jpg" && $extension != "jpeg") {
            $mod = false;
            $imagenErr = "Formato debe ser jpg/jpeg";
        }

        if ($tam > $tam_max) {
            $mod = false;
            $imagenErr = "Tamaño superior al limite de: " . ($tam_max / 1000) . " KBytes";
        }

        if ($mod) {
            // guardar
            $imagen = md5($_FILES['imagen']['tmp_name'] . $_FILES['imagen']['name'] . time()) . "." . $extension;
            $controlador = ControladorImagen::getControlador();
            if (!$controlador->salvarImagen($imagen)) {
                $Errimagen = "Error al procesar la imagen y subirla al servidor";
                $errores[] = $Errimagen;
            }

            // Borrar
            $imagenAnterior = trim($_POST["imagenAnterior"]);
            if ($imagenAnterior != $imagen) {
                if (!$controlador->eliminarImagen($imagenAnterior)) {
                    $Infoimagen = "Error al borrar la antigua imagen en el servidor";
                }
            }
        } else {
            // Si no la hemos modificado
            $imagen = trim($_POST["imagenAnterior"]);
        }
    } else {
        $imagen = trim($_POST["imagenAnterior"]);
    }

    if (empty($errores)){
        $controlador = ControladorCoche::getControlador();
        $estado = $controlador->actualizarCoche($id,$matricula , $marca , $modelo , $combustible , $titular , $dni , $telefono , $fecha_cita, $imagen );
        if ($estado) {
            alerta("Se ha creado correctamente","../index.php");
            exit();
        } else {
            alerta("Ha fallado la modificacion");
            exit();
        }
    } else {
        alerta("Hay errores al procesar el formulario revise los errores");
    }
}



if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    $id =  decode($_GET["id"]);
    $controlador = ControladorCoche::getControlador();
    $coche= $controlador->buscarCocheid($id);
    if (!is_null($coche)) {
        $matricula = $coche->getMatricula();
        $matriculaAnterior = $matricula;
        $marca = $coche->getMarca();
        $modelo = $coche->getModelo();
        $combustible = $coche->getCombustible();
        $titular = $coche->getTitular();
        $dni = $coche->getDni();
        $telefono = $coche->getTelefono();
        $fecha_cita = $coche->getFecha_cita();
        $fechaAnterior=$fecha_cita;
        $imagen = $coche->getimagen();
        $imagenAnterior = $imagen;
    } else {
        header("location: error.php");
        exit();
    }
} else {
    header("location: error.php");
    exit();
}

?>

<?php require_once VIEW_PATH . "navbar.php"; ?>
<head>
<style type="text/css">

.banner-section{background-image:url("../imagenes/update.jpg"); background-size:1500px 350px ; height: 380px; left: 0; position: absolute; top: 0; background-position:0; background-repeat: no-repeat; }
#cabecera {
    font-weight: bold;
  font-size: 68px;
  font-family: "Arial";
  text-shadow: 0 1px 0 #ccc, 
               0 2px 0 #c9c9c9,
               0 3px 0 #bbb,
               0 4px 0 #b9b9b9,
               0 5px 0 #aaa,
               0 6px 1px rgba(0,0,0,.1),
               0 0 5px rgba(0,0,0,.1),
               0 1px 3px rgba(0,0,0,.3),
               0 3px 5px rgba(0,0,0,.2),
               0 5px 10px rgba(0,0,0,.25),
               0 10px 10px rgba(0,0,0,.2),
               0 20px 20px rgba(0,0,0,.15);
  color: #FFF;
  text-align: center;}
  #menu {
  font-weight: bold;
  font-size: 20px;
  font-family: "Arial";
  text-shadow: 0 0.5px 0 #ccc, 
               0 1px 0 #c9c9c9,
               0 1.5px 0 #bbb,
               0 2px 0 #b9b9b9,
               0 2.5px 0 #aaa,
               0 3px 0.5px rgba(0,0,0,.1),
               0 0 2.4px rgba(0,0,0,.1),
               0 0.5px 1.5px rgba(0,0,0,.3),
               0 1.5px 2.5px rgba(0,0,0,.2),
               0 2.5px 5px rgba(0,0,0,.25),
               0 5px 5px rgba(0,0,0,.2),
               0 10px 10px rgba(0,0,0,.1);
  color: #FFF;}
</style>
</head>
<section class="post-content-section">
    <div class="container">
        <div class="row" >
            <div class="col-lg-12 col-md-12 col-sm-12 post-title-block">
            <div id='cabecera'> <h1 class="display-1 text-center">Modificar Vehiculo</h1> </div>
            <div id="menu">    
            <ul class="list-inline text-center">
               
                    <li>Jose F |</li>
                    <li>CRUD ITV |</li>
                    <li>Modificar</li>
                
                </ul>
                </div>
            </div>
  </div>



<div class="list-group">
    <a class="list-group-item active"> 
    <h2 class="list-group-item-heading">Formulario de Modificacion </h2>
    <p class="list-group-item-text">Edita los campos para actualizar la ficha.</p>
    </a>
</div>
<div class="well">
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel-content">     

<div class="lead">
<form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post" enctype="multipart/form-data">
    <table>
        <tr>
            <td class="col-xs-11" class="align-left">
            <div class="lead">
            <div class="form-group <?php echo (!empty($Errmatricula)) ? 'error: ' : ''; ?>">
            <label>Matricula</label>
            <input type="text" required name="matricula" pattern="([0-9]{4}[a-zA-Z]{3})" maxlength="7" title="El matricula no puede contener números" value="<?php echo $matricula; ?>">
            <span class="help-block"><?php echo $Errmatricula;?></span> 
                    </td>
            <!-- Fotografía -->
            <td class="col-xs-11" class="align-right">
                <label>Fotografía</label><br>
                <img src='<?php echo "../imagenes/fotos/" . $coche->getimagen() ?>' class='rounded' class='img-thumbnail' width='150' height='auto'>
            </td>
 <!-- matricula-->
 </td>
</tr>
</table>
        <!-- marca-->
        <label>Marca</label>
    <input type="text" required name="marca" maxlength="25" pattern="^([A-Za-zÑñ]+[áéíóú]?[A-Za-z]*){3,18}\s?([A-Za-zÑñ]+[áéíóú]?[A-Za-z]*){0,36}$" title="la marca no puede contener números" value="<?php echo $marca; ?>">
    <span class="help-block"><?php echo $Errmarca;?></span> 
</div>
    <!-- modelo-->
    <div class="form-group <?php echo (!empty($Errmodelo)) ? 'error: ' : ''; ?>">
    <label>Modelo</label>
    <input type="text" required name="modelo" maxlength="25" pattern="^([A-Za-zÑñ]+[áéíóú]?[A-Za-z]*){1,18}?\s?([A-Za-zÑñ]+[áéíóú]?[A-Za-z]*){0,36}?[0-9]{0,5}$" title="El modelo no puede contener números intercalados" value="<?php echo $modelo; ?>">
    <span class="help-block"><?php echo $Errmodelo;?></span> 
</div>

<!-- combustible -->
<div class="form-group  <?php echo (!empty($Errcombustible)) ? 'error: ' : ''; ?>">
    <label>Combustible</label>
    <input type="radio" name="combustible" required value="Gasolina" <?php echo (strstr($combustible, 'Gasolina')) ? 'checked' : ''; ?>>Gasolina</input>
    <input type="radio" name="combustible" required value="Gas" <?php echo (strstr($combustible, 'Gas')) ? 'checked' : ''; ?>>Gas</input>
    <input type="radio" name="combustible" required value="Propano" <?php echo (strstr($combustible, 'Propano')) ? 'checked' : ''; ?>>Propano</input>
    <input type="radio" name="combustible" required value="Vapor" <?php echo (strstr($combustible, 'Vapor')) ? 'checked' : ''; ?>>Vapor</input>
    <input type="radio" name="combustible" required value="Electrico" <?php echo (strstr($combustible, 'Electrico')) ? 'checked' : ''; ?>>Electrico</input>
    <input type="radio" name="combustible" required value="Hibrido" <?php echo (strstr($combustible, 'Hibrido')) ? 'checked' : ''; ?>>Hibrido</input>
    <input type="radio" name="combustible" required value="Hidrogeno" <?php echo (strstr($combustible, 'Hidrogeno')) ? 'checked' : ''; ?>>Hidrogeno</input>
    <span class="help-block"><?php echo $Errcombustible; ?></span> 
</div>

<!-- Titular -->
<div class="form-group <?php echo (!empty($Errtitular)) ? 'error: ' : ''; ?>">
    <label>Titular</label>
    <input type="text" required name="titular" maxlength="25" pattern="^([A-Za-zÑñ]+[áéíóú]?[A-Za-z]*){3,18}\s?([A-Za-zÑñ]+[áéíóú]?[A-Za-z]*){0,36}$" title="El titular no puede contener números" value="<?php echo $titular; ?>">
    <span class="help-block"><?php echo $Errtitular;?></span> 
</div>
<!-- Dni -->
<div class="form-group <?php echo (!empty($Errdni)) ? 'error: ' : ''; ?>">
    <label>Dni</label>
    <input type="text" required name="dni" maxlength="25" pattern="^[0|5][0-9]{7}[a-zA-Z]$" title="El formato del DNi no es valido" value="<?php echo $dni; ?>">
    <span class="help-block"><?php echo $Errdni;?></span> 
</div>
<!-- Telefono -->
<div class="form-group <?php echo (!empty($Errtelefono)) ? 'error: ' : ''; ?>">
    <label>Telefono</label>
    <input type="text" required name="telefono" maxlength="25" pattern="^[6|7|8|9][0-9]{8}$"  title="El telefono no puede contener números" value="<?php echo $telefono; ?>">
    <span class="help-block"><?php echo $Errtelefono;?></span> 
</div>
<label>Fecha para la cita</label>
<input type="date" required name="fecha_cita" value="<?php echo date('Y-m-d', strtotime(str_replace('/', '-', $fecha_cita)));?>"></input><div>
                        <span class="help-block"><?php echo $Errfecha_cita;?></span>
                    </div>


<!-- Foto-->
<div class="form-group <?php echo (!empty($imagenErr)) ? 'error: ' : ''; ?>">
    <label>Fotografía</label>
    <input type="file" name="imagen" id="imagen" accept="image/jpeg">
    <span class="help-block"><?php echo $imagenErr; ?></span> 
</div>
    </div> 
        </div>
            </div>
                </div>
                    </div> 
                        </div>
                            </div>                             
        <input type="hidden" name="id" value="<?php echo $id; ?>" />
        <input type="hidden" name="imagenAnterior" value="<?php echo $imagenAnterior; ?>" />
        <input type="hidden" name="fechaAnterior" value="<?php echo $fechaAnterior; ?>" />
        </div>
        </div> 
            </div>
                </div>
                    </div>
                        </div> 
                            </div>
                                                          
    <span class="list-group-item text-center">
     <a onclick="history.back()" class="btn btn-primary"><span class="glyphicon glyphicon-chevron-left"></span> Volver</a>
     <button type="submit" name= "aceptar" value="aceptar" class=" btn btn-success" ><span class="glyphicon glyphicon-ok"></span><strong> Modificar</h5></strong>  </button>


     </span>
    </div>
    </div>  
</form>

<br>
<br>
<br>
</section>

<?php require_once VIEW_PATH . "footer.php" ?>
