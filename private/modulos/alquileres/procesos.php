<?php 
include('../../Config/Config.php');
$alquiler = new alquiler($conexion);

$proceso = '';
if( isset($_GET['proceso']) && strlen($_GET['proceso'])>0 ){
	$proceso = $_GET['proceso'];
}
$alquiler->$proceso( $_GET['alquiler'] );
print_r(json_encode($alquiler->respuesta));

class alquiler{
    private $datos = array(), $db;
    public $respuesta = ['msg'=>'correcto'];
    
    public function __construct($db){
        $this->db=$db;
    }
    public function recibirDatos($alquiler){
        $this->datos = json_decode($alquiler, true);
        $this->validar_datos();
    }
    private function validar_datos(){
        if( empty($this->datos['periodo']['id']) ){
            $this->respuesta['msg'] = 'por favor ingrese el periodo del alquiler';
        }
        if( empty($this->datos['pelicula']['id']) ){
            $this->respuesta['msg'] = 'por favor ingrese la pelicula';
        }
        $this->almacenar_alquiler();
    }
    private function almacenar_alquiler(){
        if( $this->respuesta['msg']==='correcto' ){
            if( $this->datos['accion']==='nuevo' ){
                $this->db->consultas('
                    INSERT INTO alquileres (idCliente,idPelicula,fechaPrestamo,fechaDevolucion,valor) VALUES(
                        "'. $this->datos['periodo']['id'] .'",
                        "'. $this->datos['pelicula']['id'] .'",
                        "'. $this->datos['fechaPrestamo'] .'",
                        "'. $this->datos['fechaDevolucion'] .'",
                        "'. $this->datos['valor'] .'"
                    )
                ');
                $this->respuesta['msg'] = 'Registro insertado correctamente';
            } else if( $this->datos['accion']==='modificar' ){
                $this->db->consultas('
                    UPDATE alquileres SET
                        idCliente     = "'. $this->datos['periodo']['id'] .'",
                        idPelicula      = "'. $this->datos['pelicula']['id'] .'",
                        fechaPrestamo         = "'. $this->datos['fechaPrestamo'] .'",
                        fechaDevolucion         = "'. $this->datos['fechaDevolucion'] .'",
                        valor         = "'. $this->datos['valor'] .'"
                    WHERE idAlquiler = "'. $this->datos['idAlquiler'] .'"
                ');
                $this->respuesta['msg'] = 'Registro actualizado correctamente';
            }
        }
    }
    public function traer_clientes_peliculas(){
        $this->db->consultas('
            select clientes.nombre AS label, clientes.idCliente AS id
            from clientes
        ');
        $clientes = $this->db->obtener_data();
        $this->db->consultas('
            select peliculas.titulo AS label, peliculas.idPelicula AS id
            from peliculas
        ');
        $peliculas = $this->db->obtener_data();
        return $this->respuesta = ['clientes'=>$clientes, 'peliculas'=>$peliculas ];
    }
    public function eliminarAlquiler($idAlquiler = 0){
        $this->db->consultas('
            DELETE alquileres
            FROM alquileres
            WHERE alquileres.idAlquiler="'.$idAlquiler.'"
        ');
        return $this->respuesta['msg'] = 'Registro eliminado correctamente';;
    }
}
?>