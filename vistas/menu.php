<?php
error_reporting(E_ALL & ~(E_STRICT|E_NOTICE));
session_start();
if (!isset($_SESSION['email']) || empty($_SESSION['email'])) {
    header("location: /itv/vistas/login.php");
    exit();
}
require_once $_SERVER['DOCUMENT_ROOT'] . "/itv/dirs.php";
?>


<style type="text/css">
.banner-section{background-image:url("imagenes/lista.jpg"); background-size:1500px 500px ; height: 380px; left: 0; position: absolute; top: 0; background-position:0; background-repeat: no-repeat; }
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
<body>
    

<section class="post-content-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 post-title-block">
            <div id='cabecera'> <h1 class="display-1 text-center">Lista de Coches</h1></div>
            <div id="menu">    
            <ul class="list-inline text-center">
               
                    <li>Jose F |</li>
                    <li>CRUD ITV |</li>
                    <li>Lista</li>
                
                </ul>
                </div>
            </div>

           
    <!-- Botones-->
  <div class="btn-group btn-group-justified">
  <a href="javascript:window.print()"  class="btn btn-primary"> Imprimir</a>
  <a href="utilidades/descargar.php?opcion=TXT" class="btn btn-primary">TXT</a>
  <a href="utilidades/descargar.php?opcion=PDF" target="_blank" class="btn btn-primary">PDF</a>
  <a href="utilidades/descargar.php?opcion=XML" target="_blank"class="btn btn-primary">XML</a>
  <a href="utilidades/descargar.php?opcion=JSON" target="_blank"class="btn btn-primary">JSON</a>
   <?php if($_SESSION['tipo'] =='admin'){ ?>
  <a href="vistas/create.php" class="btn btn-success"> Añadir Cita</a>
  <?php }?> 
</div>    
    <br>
    <br>
    <br>
<?php
require_once CONTROLLER_PATH . "ControladorCoche.php";
require_once CONTROLLER_PATH . "Paginador.php";
require_once UTILITY_PATH . "funciones.php";



if (!isset($_POST["coche"])) {
    $marca = "";
} else {
    $marca = filtrado($_POST["coche"]);
}

$controlador = ControladorCoche::getControlador();

//Paginador
$pagina = (isset($_GET['page'])) ? $_GET['page'] : 1;
$enlaces = (isset($_GET['enlaces'])) ? $_GET['enlaces'] : 10;

// Consulta 
$consulta = "SELECT * FROM coches WHERE marca LIKE :marca order by marca ";
$parametros = array(':marca' => "%" . $marca . "%");
$limite = 10; // Limite del paginador
$paginador  = new Paginador($consulta, $parametros, $limite);
$resultados = $paginador->getDatos($pagina);


if (count($resultados->datos)> 0) {
    echo "<table class='table table-bordered table-striped'>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>Matricula</th>";
    echo "<th>Marca</th>";
    echo "<th>Modelo</th>";
    echo "<th>Combustible</th>";
    echo "<th>Titular</th>";
    echo "<th>Dni</th>";
    echo "<th>Telefono</th>";
    echo "<th>Fecha_cita</th>";
    echo "<th>Imagen</th>";
    echo "<th>Accion</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
  
    foreach ($resultados->datos as $a) {

     
        $coche = new coche($a->id, $a->matricula, $a->marca, $a->modelo, $a->combustible, $a->titular, $a->dni, $a->telefono ,$a->fecha_cita ,$a->imagen);
        

        echo "<tr>";
            echo "<td>" . $coche->getMatricula() . "</td>";
            echo "<td>" . $coche->getMarca() . "</td>";
            echo "<td>" . $coche->getModelo() . "</td>";
            echo "<td>" . $coche->getCombustible() . "</td>";
            echo "<td>" . $coche->getTitular() . "</td>";
            echo "<td>" . $coche->getDni() . "</td>";
            echo "<td>" . $coche->getTelefono() . "</td>";
            echo "<td>" . $coche->getFecha_cita() . "</td>";
            echo "<td><img src='imagenes/fotos/" . $coche->getimagen() . "' width='48px' height='48px'></td>";
            echo "<td>";
                echo "<a href='vistas/read.php?id=" . encode($coche->getId()) . "' title='Ver ' data-toggle='tooltip'><span class='glyphicon glyphicon-eye-open'></span></a>";
                 if($_SESSION['tipo'] =='admin'){
                    echo "<a href='vistas/update.php?id=" . encode($coche->getId()) . "' title='Actualizar ' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>";
                    echo "<a href='vistas/delete.php?id=" . encode($coche->getId()) . "' title='Borrar ' data-toggle='tooltip'><span class='glyphicon glyphicon-trash'></span></a>";
                 }
            echo "</td>";
        echo "</tr>";
        
    }
    
   
    echo "</tbody>";
    echo "</table>";
    echo "<ul class='pager' id='no_imprimir'>"; 
    echo $paginador->crearLinks($enlaces);
    echo "</ul>";
} else {
    echo "<p><em><h2>No se ha encontrado datos de coches.</h2></em></p>";
}

?>

<?php



?>

<div id="no_imprimir">
    <?php if(isset($_SESSION['email'])){
        if (isset($_COOKIE['CONTADOR'])) {
            echo $contador;
            echo $acceso;
        } else {
            echo "Es tu primera visita hoy";
        }
    }
    ?>
</div>
</div>
</div>

</body>
</html>

<?php require_once VIEW_PATH . "footer.php" ?>