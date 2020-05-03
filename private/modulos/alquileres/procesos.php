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
        if( empty($this->datos['pelicula']['id']) ){
            $this->respuesta['msg'] = 'por favor ingrese el pelicula del alquiler';
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
                        "'. $this->datos['pelicula']['id'] .'",
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
                        idCliente     = "'. $this->datos['pelicula']['id'] .'",
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
    public function buscarAlquiler($valor = ''){
        if( substr_count($valor, '-')===2 ){
            $valor = implode('-', array_reverse(explode('-',$valor)));
        }
        $this->db->consultas('
            select alquileres.idAlquiler, alquileres.idPelicula, alquileres.idCliente, 
                date_format(alquileres.fechaPrestamo,"%d-%m-%Y") AS fechaP, alquileres.fechaPrestamo AS fp,
                date_format(alquileres.fechaDevolucion,"%d-%m-%Y") AS fechaD, alquileres.fechaDevolucion AS fd,
                alquileres.valor, 
                clientes.nombre, clientes.dui,
                peliculas.titulo, peliculas.genero
            from alquileres
                inner join clientes on(clientes.idCliente=alquileres.idCliente)
                inner join peliculas on(peliculas.idPelicula=alquileres.idPelicula)
            where clientes.nombre like "%'. $valor .'%" or 
                peliculas.titulo like "%'. $valor .'%" or 
                alquileres.fechaPrestamo like "%'. $valor .'%"

        ');
        $alquileres = $this->respuesta = $this->db->obtener_data();
        foreach ($alquileres as $key => $value) {
            $datos[] = [
                'idAlquiler' => $value['idAlquiler'],
                'cliente'      => [
                    'id'      => $value['idCliente'],
                    'label'   => $value['nombre']
                ],
                'pelicula'      => [
                    'id'      => $value['idPelicula'],
                    'label'   => $value['pelicula']
                ],
                'fechaP'       => $value['fp'],
                'fp'           => $value['fechaP']
                ,
                'fechaD'       => $value['fd'],
                'fd'           => $value['fechaD']

            ]; 
        }
        return $this->respuesta = $datos;
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