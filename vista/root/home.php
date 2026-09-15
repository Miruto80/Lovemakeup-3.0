<!DOCTYPE html>
<html lang="es">

<head>
  <!-- php barra de navegacion-->
  <?php include 'vista/complementos/head_root.php' ?>
  <title> Panel de Inicio  </title> 
</head>

<body class="g-sidenav-show bg-gray-100 dic">
  
<!-- php barra de navegacion-->
<?php include 'vista/complementos/sidebar_root.php' ?>

<main class="main-content position-relative border-radius-lg ">
<!-- ||| Navbar ||-->
<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl " id="navbarBlur" data-scroll="false">
  <div class="container-fluid py-1 px-3">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-white" href="#">Bienvenid@</a></li>
        <li class="breadcrumb-item text-sm text-white active" aria-current="page">Inicio</li>
      </ol>
      <h6 class="font-weight-bolder text-white mb-0">Dashboard</h6>
    </nav>

<!-- php barra de navegacion-->    
<?php include 'vista/complementos/nav_root.php' ?>

<!-- |||||||||||||||| LOADER ||||||||||||||||||||-->
  <div class="preloader-wrapper">
    <div class="preloader">
    </div>
  </div> 
<!-- |||||||||||||||| LOADER ||||||||||||||||||||-->


<div class="container-fluid py-4"> <!-- DIV CONTENIDO-->
 
     <div class="row">

   
          <!-- CARD 1: CPU del Servidor -->
          <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card div-principal shadow-sm border-0">
              <div class="card-body p-3">
                <div class="row">
                  <div class="col-8">
                    <div class="numbers">
                      <p class="text-sm mb-0 text-uppercase font-weight-bold">CPU del Servidor</p>
                      <h5 class="font-weight-bolder mb-0 text-primary" id="cpu-porcentaje-header">
                        0%
                      </h5>
                      <p class="text-xs text-white mt-1 mb-0">Uso actual en tiempo real</p>
                    </div>
                  </div>
                  <div class="col-4 d-flex justify-content-end align-items-start">
                    <div class="icon icon-shape bg-primary shadow-primary rounded-circle d-inline-flex align-items-center justify-content-center p-0" style="width: 48px; height: 48px; min-width: 48px;">
                      <i class="fa-solid fa-microchip text-lg opacity-10 text-white m-0 p-0" style="top: 0; line-height: 0;"></i>
                    </div>
                  </div>
                </div>
                <!-- Barra de Progreso CPU -->
                <div class="mt-3">
                  <div class="d-flex justify-content-between align-items-center text-xs mb-1">
                    <span class="text-white">Consumo</span>
                    <span class="font-weight-bold" id="cpu-texto-detalle">0% / 100%</span>
                  </div>
                  <div class="progress progress-s">
                    <div id="barra-cpu" class="progress-bar bg-gradient-primary" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- CARD 2: Memoria del Servidor -->
          <div class="col-xl-3 col-sm-6">
            <div class="card div-principal shadow-sm border-0">
              <div class="card-body p-3">
                <div class="row">
                  <div class="col-8">
                    <div class="numbers">
                      <p class="text-sm mb-0 text-uppercase font-weight-bold">Memoria RAM</p>
                      <h5 class="font-weight-bolder text-info mb-0" id="ram-usada-header">
                        0 MB
                      </h5>
                      <p class="text-xs text-white mt-1 mb-0">RAM en uso del sistema</p>
                    </div>
                  </div>
                  <div class="col-4 d-flex justify-content-end align-items-start">
                    <div class="icon icon-shape bg-info shadow-primary rounded-circle d-inline-flex align-items-center justify-content-center p-0" style="width: 48px; height: 48px; min-width: 48px;">
                      <i class="fa-solid fa-memory text-lg opacity-10 text-dark m-0 p-0" style="top: 0; line-height: 0;"></i>
                    </div>
                  </div>
                </div>
                <!-- Barra de Progreso RAM -->
                <div class="mt-3">
                  <div class="d-flex justify-content-between align-items-center text-xs mb-1">
                    <span class="text-white">Uso de RAM</span>
                    <span class="font-weight-bold" id="ram-texto-detalle">0 MB / 0 MB</span>
                  </div>
                  <div class="progress progress-s">
                    <div id="barra-ram" class="progress-bar bg-gradient-info" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- CARD 3: Disco del Servidor -->
          <div class="col-xl-3 col-sm-6">
            <div class="card div-principal shadow-sm border-0">
              <div class="card-body p-3">
                <div class="row">
                  <div class="col-8">
                    <div class="numbers">
                      <p class="text-sm mb-0 text-uppercase font-weight-bold">Disco Almacenamiento</p>
                      <h5 class="font-weight-bolder text-warning mb-0" id="disco-usado-header">
                        0 GB
                      </h5>
                      <p class="text-xs text-white mt-1 mb-0">Espacio en SSD principal</p>
                    </div>
                  </div>
                  <div class="col-4 d-flex justify-content-end align-items-start">
                    <div class="icon icon-shape bg-warning shadow-warning rounded-circle d-inline-flex align-items-center justify-content-center p-0" style="width: 48px; height: 48px; min-width: 48px;">
                      <i class="fa-solid fa-hard-drive text-lg opacity-10 text-white m-0 p-0" style="top: 0; line-height: 0;"></i>
                    </div>
                  </div>
                </div>
                <!-- Barra de Progreso Disco -->
                <div class="mt-3">
                  <div class="d-flex justify-content-between align-items-center text-xs mb-1">
                    <span class="text-white">Ocupado</span>
                    <span class="font-weight-bold" id="disco-texto-detalle">0 GB / 0 GB</span>
                  </div>
                  <div class="progress progress-s">
                    <div id="barra-disco" class="progress-bar bg-gradient-warning" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>

        <!-- CARD 4: Último Backup -->
        <div class="col-xl-3 col-sm-6">
          <div class="card div-principal shadow-sm border-0">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-8">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-uppercase font-weight-bold">Último Backup</p>
                    <h5 class="font-weight-bolder mb-0 text-truncate text-success" title="backup_db_2026_09_14.sql">
                      backup_db_2026.sql
                    </h5>
                    <p class="text-xs text-white mt-1 mb-0">Generado con éxito</p>
                  </div>
                </div>
                <div class="col-4 d-flex justify-content-end align-items-start">
                  <div class="icon icon-shape bg-success shadow-success rounded-circle d-inline-flex align-items-center justify-content-center p-0" style="width: 48px; height: 48px; min-width: 48px;">
                    <i class="fa-solid fa-floppy-disk text-lg opacity-10 text-dark m-0 p-0"  style="top: 0; line-height: 0;"></i>
                  </div>
                </div>
              </div>
              <!-- Información de archivo (Sin barra de progreso) -->
              <div class="mt-3 border-top pt-2">
                <p class="text-xs text-white mb-0 d-flex justify-content-between align-items-center">
                  <span><i class="ni ni-archive-2 me-1"></i> Tamaño:</span>
                  <strong class="text-success">245.8 MB</strong>
                </p>
              </div>
            </div>
          </div>
        </div>

         <!-- FIN -->
      </div>
<!-- FIN  -->

    <div class="row mt-4">

        <!-- BASE DE DATOS 1  -->
        <div class="col-lg-6 mb-lg-0 mb-4">
          <div class="card shadow-sm border-0 div-principal">
            <div class="card-body p-4">
             
              <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                  <span class="badge badge-sm bg-success text-dark mb-2">
                    <i class="fas fa-check-circle me-1"></i> Activa
                  </span>
                  <h4 class="font-weight-bolder mb-0 text-white">Base de Datos - Negocio</h4>
                  <p class="text-xs text-white mb-0">lovemakeupbd1</p>
                </div>
                  <div class="icon icon-shape bg-primary shadow-primary rounded-circle d-inline-flex align-items-center justify-content-center p-0" style="width: 52px; height: 52px; min-width: 52px;">
                      <i class="fa-solid fa-basket-shopping text-lg opacity-10 text-white m-0 p-0" style="top: 0; line-height: 0;" aria-hidden="true"></i>
                  </div>
              </div>

              <hr class="horizontal dark my-3">

              <div class="row text-center my-3">
                <div class="col-6 border-end">
                  <p class="text-xs text-uppercase text-white font-weight-bold mb-1">Tamaño Total</p>
                  <h5 class="font-weight-bolder mb-0 text-primary">
                    <i class="fa-solid fa-folder text-primary me-1"></i>  14.8 GB
                  </h5>
                
                </div>
                <div class="col-6">
                  <p class="text-xs text-uppercase text-white font-weight-bold mb-1">Tiempo de Respuesta</p>
                  <h5 class="font-weight-bolder mb-0 text-success">
                    <i class="fa-solid fa-signal text-success me-1"></i>
                     24 ms
                  </h5>
                
                </div>
              </div>
            
            </div>
          </div>
        </div>

        <!-- BASE DE DATOS SEGURIDAD -->
        <div class="col-lg-6">
          <div class="card shadow-sm border-0 div-principal">
            <div class="card-body p-4 ">
              
              <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                 
                  <span class="badge badge-sm bg-success text-dark mb-2">
                    <i class="fas fa-check-circle me-1"></i> Activa
                  </span>
                  <h4 class="font-weight-bolder mb-0 text-white">Base de Datos - Seguridad</h4>
                  <p class="text-xs text-white mb-0">lovemakeupbds2</p>
                </div>
                <div class="icon icon-shape bg-primary shadow-primary rounded-circle d-inline-flex align-items-center justify-content-center p-0" style="width: 52px; height: 52px; min-width: 52px;">
                      <i class="fa-solid fa-key text-lg opacity-10 text-white m-0 p-0" style="top: 0; line-height: 0;" aria-hidden="true"></i>
                  </div>
              </div>
             

              <hr class="horizontal white my-3">

              
              <div class="row text-center my-3">
                <div class="col-6 border-end">
                  <p class="text-xs text-uppercase text-white font-weight-bold mb-1">Tamaño</p>
                  <h5 class="font-weight-bolder mb-0 text-primary">
                    <i class="fa-solid fa-folder text-primary me-1"></i> 8.2 GB
                  </h5>
                </div>
                <div class="col-6">
                  <p class="text-xs text-uppercase text-white font-weight-bold mb-1">Respuesta</p>
                  <h5 class="font-weight-bolder mb-0 text-success">
                    <i class="fa-solid fa-signal text-success me-1"></i>
                     24 ms
                  </h5>
                </div>
              </div>

          
            </div>
          </div>
        </div>
   
      <!-- FIN  -->
    </div>
<!-- FIN  -->

</div><!-- FIN CARD PRINCIPAL-->  


 

<!-- php Footer-->
<?php include 'vista/complementos/footer_root.php' ?>

<script>
$(document).ready(function() {

    //  convertir Bytes a KB MB  GB con formato legible
    function formatearUnidades(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const unidades = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + unidades[i];
    }

    // AJAX para consultar el controlador
    function cargarMetricasServidor() {
        $.ajax({
            async: true,
            url: '',
            type: 'POST',
            data: {
              accion: 'obtener_metricas'
            },
            dataType: 'json',
            success: function(respuesta) {
                const metricas = respuesta.metricas;

                // ---  TARJETA CPU ---
                $('#cpu-porcentaje-header').text(metricas.uso_cpu + '%');
                $('#cpu-texto-detalle').text(metricas.uso_cpu + '% / 100%');
                $('#barra-cpu')
                    .css('width', metricas.uso_cpu + '%')
                    .attr('aria-valuenow', metricas.uso_cpu);

                // --- TARJETA RAM ---
                const ramUsada = formatearUnidades(metricas.ram_usada);
                const ramTotal = formatearUnidades(metricas.ram_total);

                $('#ram-usada-header').text(ramUsada);
                $('#ram-texto-detalle').text(ramUsada + ' / ' + ramTotal);
                $('#barra-ram')
                    .css('width', metricas.porcentaje_ram + '%')
                    .attr('aria-valuenow', metricas.porcentaje_ram);

                // ---  TARJETA DISCO ---
                const discoUsado = formatearUnidades(metricas.disco_usado);
                const discoTotal = formatearUnidades(metricas.disco_total);

                $('#disco-usado-header').text(discoUsado);
                $('#disco-texto-detalle').text(discoUsado + ' / ' + discoTotal);
                $('#barra-disco')
                    .css('width', metricas.porcentaje_disco + '%')
                    .attr('aria-valuenow', metricas.porcentaje_disco);

                // --- ADVERTENCIA SI ESTÁ EN HOSTING RESTRENGIDO ---
                if (metricas.mensaje_advertencia) {
                    if ($('#alerta-hosting').length === 0) {
                        $('.row').first().before(`
                            <div id="alerta-hosting" class="alert alert-warning text-white alert-dismissible fade show" role="alert">
                                <span class="alert-icon"><i class="fa-solid fa-triangle-exclamation"></i></span>
                                <span class="alert-text"><strong>Modo Hosting:</strong> ${metricas.mensaje_advertencia}</span>
                            </div>
                        `);
                    }
                }
            },
            error: function(xhr, estado, error) {
                console.error("Error al obtener los datos del servidor:", error);
            }
        });
    }

   
    cargarMetricasServidor();

    setInterval(cargarMetricasServidor, 3000);
});
</script>
</body>
</html>