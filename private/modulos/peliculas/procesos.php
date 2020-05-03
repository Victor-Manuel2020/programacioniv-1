<?php 
include('../../config/config.php');
$pelicula = new pelicula($conexion);

$proceso = '';
if( isset($_GET['proceso']) && strlen($_GET['proceso'])>0 ){
	$proceso = $_GET['proceso'];
}
$pelicula->$proceso( $_GET['pelicula'] );
print_r(json_encode($pelicula->respuesta));

class pelicula{
    private $datos = array(), $db;
    public $respuesta = ['msg'=>'correcto'];
    
    public function __construct($db){
        $this->db=$db;
    }
    public function recibirDatos($pelicula){
        $this->datos = json_decode($pelicula, true);
        $this->validar_datos();
    }
    private function validar_datos(){
        if( empty($this->datos['titulo']) ){
            $this->respuesta['msg'] = 'por favor ingrese el titulo de la pelicula';
        }
        if( empty($this->datos['descripcion']) ){
            $this->respuesta['msg'] = 'por favor ingrese el descripcion de la pelicula';
        }
        if( empty($this->datos['sinopsis']) ){
            $this->respuesta['msg'] = 'por favor ingrese el sinopsis de la pelicula';
        }
        if( empty($this->datos['genero']) ){
            $this->respuesta['msg'] = 'por favor ingrese la genero de la pelicula';
        }
        if( empty($this->datos['duracion']) ){
            $this->respuesta['msg'] = 'por favor ingrese el duracion de la pelicula';
        }
        $this->almacenar_pelicula();
    }
    private function almacenar_pelicula(){
        if( $this->respuesta['msg']==='correcto' ){
            if( $this->datos['accion']==='nuevo' ){
                $this->db->consultas('
                    INSERT INTO peliculas (titulo,descripcion,sinopsis,genero,duracion) VALUES(
                        "'. $this->datos['titulo'] .'",
                        "'. $this->datos['descripcion'] .'",
                        "'. $this->datos['sinopsis'] .'",
                        "'. $this->datos['genero'] .'",
                        "'. $this->datos['duracion'] .'"
                    )
                ');
                $this->respuesta['msg'] = 'Registro insertado correctamente';
            } else if( $this->datos['accion']==='modificar' ){
                $this->db->consultas('
                   UPDATE peliculas SET
                        titulo     = "'. $this->datos['titulo'] .'",
                        descripcion     = "'. $this->datos['descripcion'] .'",
                        sinopsis        = "'. $this->datos['sinopsis'] .'",
                        genero  = "'. $this->datos['genero'] .'",
                        duracion   = "'. $this->datos['duracion'] .'"
                    WHERE idPelicula = "'. $this->datos['idPelicula'] .'"
                ');
                $this->respuesta['msg'] = 'Registro actualizado correctamente';
            }
        }
    }
    public function buscarPelicula($valor=''){
        $this->db->consultas('
            select peliculas.idPelicula, peliculas.titulo, peliculas.descripcion, peliculas.sinopsis, peliculas.genero, peliculas.duracion
            from peliculas
            where peliculas.titulo like "%'.$valor.'%" or peliculas.sinopsis like "%'.$valor.'%" or peliculas.genero like "%'.$valor.'%"
        ');
        return $this->respuesta = $this->db->obtener_datos();
    }
    public function eliminarPelicula($idPelicula=''){
        $this->db->consultas('
            delete peliculas
            from peliculas
            where peliculas.idPelicula = "'.$idPelicula.'"
        ');
        $this->respuesta['msg'] = 'Registro eliminado correctamente';
    }
}
?>