
<?php 

class coche{
    private $id;	
    private $matricula;
    private $marca;
    private $modelo;	
    private $combustible;	
    private $titular;
    private $dni;
    private $telefono;	
    private $fecha_cita;	
    private $imagen;

    
public function __construct( $id, $matricula, $marca, $modelo, $combustible ,$titular, $dni, $telefono, $fecha_cita, $imagen){

    $this->id = $id;	
    $this->matricula = $matricula;
    $this->marca = $marca;
    $this->modelo = $modelo;	
    $this->combustible = $combustible;	
    $this->titular = $titular;
    $this->dni = $dni;
    $this->telefono = $telefono;	
    $this->fecha_cita = $fecha_cita;	
    $this->imagen = $imagen;

}
//Getter
function getId(){
    return $this->id;
}
function getMatricula(){
    return $this->matricula;
}
function getMarca(){
    return $this->marca;
}
function getModelo(){
    return $this->modelo;
}
function getCombustible(){
    return $this->combustible;
}
function getTitular(){
    return $this->titular;
}
function getDni(){
    return $this->dni;
}
function getTelefono(){
    return $this->telefono;
}
function getFecha_cita(){
    return $this->fecha_cita;
}
function getImagen(){
    return $this->imagen;
}

//Setter
function setId($id){
    $this->id = $id;
}
function setMatricula($matricula){
    $this->matricula= $matricula;
}
function setMarca($marca){
    $this->marca = $marca;
}
function setModelo($modelo){
    $this->modelo = $modelo;
}
function setCombustible($combustible){
    $this->combustible = $combustible;
}
function setTitular($titular){
    $this->titular = $titular;
}
function setDni($dni){
    $this->dni = $dni;
}
function setTelefono($telefono){
    $this->telefono = $telefono;
}
function setFecha_cita($fecha_cita){
    $this->fecha_cita = $fecha_cita;
}
function setImagen($imagen){
    $this->imagen = $imagen;
}


}

?>