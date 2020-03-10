<?php
require_once MODEL_PATH."coche.php";
require_once CONTROLLER_PATH."ControladorBD.php";
require_once UTILITY_PATH."funciones.php";

class ControladorCoche {

    static private $instancia = null;
    private function __construct() {
        //echo "Conector creado";
    }
    
    /**
     * Patrón Singleton. Ontiene una instancia del Manejador de la BD
     * @return instancia de conexion
     */
    public static function getControlador() {
        if (self::$instancia == null) {
            self::$instancia = new ControladorCoche();
        }
        return self::$instancia;
    }
    
    /**
     * Lista el coche según la matricula o la marca
     * @param type $matricula , $marca
     * 
     */
//----------------------------------------------------------------------------------------------------
    public function listarCoches($matricula, $marca){
        $lista=[];
        $bd = ControladorBD::getControlador();
        $bd->abrirBD();

        $consulta = "SELECT * FROM coches WHERE matricula LIKE :matricula OR marca LIKE :marca";
        $parametros = array(':matricula' => "%".$matricula."%", ':marca' => "%".$marca."%");

        $res = $bd->consultarBD($consulta,$parametros);
        $filas=$res->fetchAll(PDO::FETCH_OBJ);

        if (count($filas) > 0) {
            foreach ($filas as $a) {
                $coche = new coche($a->id, $a->matricula, $a->marca, $a->modelo, $a->combustible, $a->titular, $a->dni, $a->telefono ,$a->fecha_cita ,$a->imagen);
                $lista[] = $coche;
            }
            $bd->cerrarBD();
            return $lista;
        }else{
            return null;
        }    
    }
//----------------------------------------------------------------------------------------------------
    public function almacenarCoche($matricula, $marca, $modelo, $combustible, $titular, $dni, $telefono, $fecha_cita, $imagen){
        $bd = ControladorBD::getControlador();
        $bd->abrirBD();
        //muy importante respetar el orden de las columnas en el insert y corresponderse a la tabla de verdad!!
        // el resto se puede poner de orden que nos de la gana
        $consulta = "INSERT INTO coches (matricula,  marca, modelo, combustible, titular, dni, telefono, fecha_cita, imagen) VALUES ( :matricula,:marca,:modelo, :combustible, :titular, :dni, :telefono, :fecha_cita, :imagen)";
        
        $parametros=array(':matricula'=>$matricula, ':marca'=>$marca, ':modelo'=>$modelo, ':combustible'=>$combustible,  ':titular'=>$titular, ':dni'=>$dni,':telefono'=>$telefono, ':fecha_cita'=>$fecha_cita,
        ':imagen'=>$imagen);
        $estado = $bd->actualizarBD($consulta,$parametros);
        $bd->cerrarBD();
        return $estado;
    }
//----------------------------------------------------------------------------------------------------
    public function buscarCocheid($id){ 
        $bd = ControladorBD::getControlador();
        $bd->abrirBD();
        $consulta = "SELECT * FROM coches WHERE id = :id";
        $parametros = array(':id' => $id);
        
        $filas = $bd->consultarBD($consulta, $parametros);
        $res = $bd->consultarBD($consulta,$parametros);
        $filas=$res->fetchAll(PDO::FETCH_OBJ);
        
        if (count($filas) > 0) {
            foreach ($filas as $a) {
                $coche = new coche($a->id, $a->matricula, $a->marca, $a->modelo, $a->combustible, $a->titular, $a->dni, $a->telefono ,$a->fecha_cita ,$a->imagen);
            }
            $bd->cerrarBD();
            return $coche;
        }else{
            return null;
        }    
    }
//--------------------------------------------------------------------------------------------------
    public function buscarCoche($matricula){ 
        $bd = ControladorBD::getControlador();
        $bd->abrirBD();
        $consulta = "SELECT * FROM coches WHERE matricula = :matricula";
        $parametros = array(':matricula' => $matricula);
        $filas = $bd->consultarBD($consulta, $parametros);
        $res = $bd->consultarBD($consulta,$parametros);
        $filas=$res->fetchAll(PDO::FETCH_OBJ);
        if (count($filas) > 0) {
            foreach ($filas as $a) {
                $coche = new coche($a->id, $a->matricula, $a->marca, $a->modelo, $a->combustible, $a->titular, $a->dni, $a->telefono ,$a->fecha_cita ,$a->imagen);
            }
            $bd->cerrarBD();
            return $coche;
        }else{
            return null;
        }    
    }
//------------------------------------------------------------------------------------------------- 
// //--------------------------------------------------------------------------------------------------
public function buscarCocheyFecha($matricula,$fecha_cita){ 
    $bd = ControladorBD::getControlador();
    $bd->abrirBD();
    $consulta = "SELECT * FROM coches WHERE matricula = :matricula AND fecha_cita = :fecha_cita";
    $parametros = array(':matricula' => $matricula,':fecha_cita' => $fecha_cita);
    $filas = $bd->consultarBD($consulta, $parametros);
    $res = $bd->consultarBD($consulta,$parametros);
    $filas=$res->fetchAll(PDO::FETCH_OBJ);
    if (count($filas) > 0) {
        foreach ($filas as $a) {
            $cocheVal = new coche($a->id, $a->matricula, $a->marca, $a->modelo, $a->combustible, $a->titular, $a->dni, $a->telefono ,$a->fecha_cita ,$a->imagen);
        }
        $bd->cerrarBD();
        return $cocheVal;
    }else{
        return null;
    }    
}
//------------------------------------------------------------------------------------------------- 
    public function borrarCoche($id){ 
        $estado=false;
        $bd = ControladorBD::getControlador();
        $bd->abrirBD();
        $consulta = "DELETE FROM coches WHERE id = :id";
        $parametros = array(':id' => $id);
        $estado = $bd->actualizarBD($consulta,$parametros);
        $bd->cerrarBD();
        return $estado;
    }
//-------------------------------------------------------------------------------------------------  
    public function actualizarCoche($id,$matricula, $marca, $modelo, $combustible, $titular, $dni, $telefono, $fecha_cita, $imagen){
        $bd = ControladorBD::getControlador();
        $bd->abrirBD();
        $consulta = "UPDATE coches SET  matricula=:matricula, marca=:marca, modelo=:modelo, combustible=:combustible, titular=:titular, dni=:dni, telefono=:telefono,  fecha_cita=:fecha_cita,   
             imagen=:imagen 
            WHERE id=:id";
        $parametros= array(':id'=>$id ,':matricula'=>$matricula,  ':marca'=>$marca,':modelo'=>$modelo,':combustible'=>$combustible,':titular'=>$titular, ':dni'=>$dni, ':telefono'=>$telefono, ':fecha_cita'=>$fecha_cita,
       ':imagen'=>$imagen);
        $estado = $bd->actualizarBD($consulta,$parametros);
        $bd->cerrarBD();
        return $estado;
    }
//-------------------------------------------------------------------------------------------------  



}
