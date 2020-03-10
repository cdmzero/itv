<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/itv/dirs.php";
require_once CONTROLLER_PATH . "ControladorCoche.php";
require_once MODEL_PATH . "coche.php";
require_once VENDOR_PATH . "autoload.php";
use Spipu\Html2Pdf\HTML2PDF;

class ControladorDescarga
{
    private $fichero;
    static private $instancia = null;

    private function __construct()
    {
        //echo "Conector creado";
    }

    /**
     * Patrón Singleton. Ontiene una instancia del Controlador de Descargas
     * @return instancia de conexion
     */

    public static function getControlador()
    {
        if (self::$instancia == null) {
            self::$instancia = new ControladorDescarga();
        }
        return self::$instancia;
    }
//----------------------------------------------------------------------------------------------------------
    public function descargarTXT()
    {
        $this->fichero = "lista.txt";
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=" . $this->fichero . "");

        $controlador = ControladorCoche::getControlador();
        $lista = $controlador->listarCoches("", "");



        if (!is_null($lista) && count($lista) > 0) {
            foreach ($lista as &$coche) {
                echo " -- Matricula: " . $coche->getMatricula() . "  -- Combustible: " . $coche->getCombustible() .
                    " -- Marca: " . $coche->getMarca() . " -- Titular: " . $coche->getTitular() . " -- Telefono: " . $coche->getTelefono() .
                    " -- Modelo: " . $coche->getModelo() . " -- DNI: " . $coche->getDni(). " -- Fecha de Cita: " . $coche->getFecha_cita() ."\r\n";
            }
        } else {
            echo "No se ha encontrado datos de Coches";
        }
    }
//---------------------------------------------------------------------------------------------------------
    public function descargarJSON()
    {
        $this->fichero = "lista.json";
        header("Content-Type: application/octet-stream");
        header('Content-type: application/json');
        //header("Content-Disposition: attachment; filename=" . $this->fichero . ""); //archivo de salida

        $controlador = ControladorCoche::getControlador();
        $lista = $controlador->listarCoches("", "");
        $sal = [];
        foreach ($lista as $al) {
            $sal[] = $this->json_encode_private($al);
        }
        echo json_encode($sal);
    }

    private function json_encode_private($object)
    {
        $public = [];
        $reflection = new ReflectionClass($object);
        foreach ($reflection->getProperties() as $property) {
            $property->setAccessible(true);
            $public[$property->getName()] = $property->getValue($object);
        }
        return json_encode($public);
    }
//-----------------------------------------------------------------------------------------------------------
    public function descargarXML()
    {
        $this->fichero = "listado.xml";
        $nombre= "listado.xml";
        $lista = $controlador = ControladorCoche::getControlador();
        $lista = $controlador->listarCoches("", "");
        $doc = new DOMDocument('1.0', 'UTF-8');
        $coches = $doc->createElement('coches');

        foreach ($lista as $a) {
            $coche = $doc->createElement('coche');
            $coche->appendChild($doc->createElement('Matricula', $a->getMatricula()));
            $coche->appendChild($doc->createElement('Marca', $a->getMarca()));
            $coche->appendChild($doc->createElement('Modelo', $a->getModelo()));
            $coche->appendChild($doc->createElement('Combustible', $a->getCombustible()));
            $coche->appendChild($doc->createElement('Titular', $a->getTitular()));
            $coche->appendChild($doc->createElement('Dni', $a->getDni()));
            $coche->appendChild($doc->createElement('Telefono', $a->getTelefono()));
            $coche->appendChild($doc->createElement('Fecha', $a->getFecha_cita()));
            $coche->appendChild($doc->createElement('Imagen', $a->getimagen()));

            $coches->appendChild($coche);
        }

        $doc->appendChild($coches);
        header('Content-type: application/xml');
        header("Content-Disposition: attachment; filename=" . $nombre . ""); //archivo de salida
        echo $doc->saveXML();

        exit;
    }
//-------------------------------------------------------------------------------------------------------------
    public function descargarPDF(){
        ob_end_clean(); // Para limpiar de polvo paja y alpiste, muy util de hecho voy a invitar a un cafe a quien me dio la solucion
        $sal ='<h2 class="pull-left">Fichas de Luchadores</h2>';
        $lista = $controlador = ControladorCoche::getControlador();
        $lista = $controlador->listarCoches("", "");
        if (!is_null($lista) && count($lista) > 0) {
            $sal.="<table class='table table-bordered table-striped'>";
            $sal.="<thead>";
            $sal.="<tr>";
            $sal.="<th>Matricula</th>";
            $sal.="<th>Marca</th>";
            $sal.="<th>Modelo</th>";
            $sal.="<th>Tipo</th>";
            $sal.="<th>Disponible</th>";
            $sal.="<th>Precio</th>";
            $sal.="<th>Imagen</th>";
            $sal.="<th>Accion</th>";
            $sal.="</tr>";
            $sal.="</thead>";
            $sal.="<tbody>";

            foreach ($lista as $a) {
                $sal.="<tr>";
                $sal.="<td>" . $a->getMatricula() . "</td>";
                $sal.="<td>" . $a->getMarca() . "</td>";
                $sal.="<td>" . $a->getModelo() . "</td>";
                $sal.="<td>" . $a->getCombustible() . "</td>";
                $sal.="<td>" . $a->getTitular() . "</td>";
                $sal.="<td>" . $a->getDni(). "</td>";
                $sal.="<td>" . $a->getTelefono(). "</td>";
                $sal.="<td>" . $a->getFecha_cita(). "</td>";
                $sal.="<td><img src='".$_SERVER['DOCUMENT_ROOT'] . "/itv/imagenes/fotos/" . $a->getimagen()."'  style='max-width: 12mm; max-height: 12mm'></td>";
                $sal.="</tr>";
            }
            $sal.="</tbody>";
            $sal.="</table>";
        } else {
            $sal.="<p class='lead'><em>No se ha encontrado datos de coches.</em></p>";
        }
        $pdf=new HTML2PDF('L','A4','es','false','UTF-8');
        $pdf->writeHTML($sal);
        $pdf->output('coches.pdf');

    }
}
