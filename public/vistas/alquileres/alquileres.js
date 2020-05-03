Vue.component('v-select', VueSelect.VueSelect);

var appalquileres = new Vue({
    el:'#frm-alquileres',
    data:{
        alquiler:{
            idAlquiler : 0,
            accion    : 'nuevo',
            cliente   : {
                idCliente : 0,
                nombre   : ''
            },
            pelicula    : {
                idPelicula : 0,
                titulo   : ''
            },
            fechaPrestamo     : '',
            fechaDevolucion   : '',
            valor   : '',
            msg       : ''
        },
        clientes : {},
        peliculas  : {}
    },
    methods:{
        guardarAlquileres(){
            fetch(`private/Modulos/alquileres/procesos.php?proceso=recibirDatos&alquiler=${JSON.stringify(this.alquiler)}`).then( resp=>resp.json() ).then(resp=>{
                this.alquiler.msg = resp.msg;
            });
        },
        limpiarAlquileres(){
            this.alquiler.idAlquiler=0;
            this.alquiler.accion="nuevo";
            this.alquiler.msg="";
        }
    },
    created(){
        fetch(`private/Modulos/alquileres/procesos.php?proceso=traer_clientes_peliculas&alquiler=''`).then( resp=>resp.json() ).then(resp=>{
            this.clientes = resp.clientes;
            this.peliculas = resp.peliculas;
        });
    }
});