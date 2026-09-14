<div class="min-height-300 sidedar-color-root position-absolute w-100">  </div>
  <aside class="sidebar  sidenav sidebar_root navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4 " id="sidenav-main">
  <div class="sidenav-header">
      <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand m-0" href="?pagina=root_home">
          <img src="assets/img/icono.png" width="30px" height="30px" class="navbar-brand-img h-100" alt="main_logo">
          <span class="ms-1 font-weight-bold texto-negro">Love Makeup C.A</span>
        </a>
      <p class="text-center text-white m-0" style="font-size: 14px;">Panel ROOT </p>


    </div>
    
    <hr class="horizontal dark mt-0 texto-hr">

   <div class="collapse navbar-collapse sidebar" id="sidenav-collapse-main">
      

    <ul class="navbar-nav texto-negro">
        
        <li class="nav-item ">
           <a class="nav-link <?= $pagina_actual == 'root_home' ? 'bg-activo' : '' ?>" href="?pagina=root_home">
            <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
              <i class="fa-solid fa-house text-sm texto-negro <?= $pagina_actual == 'home' ? 'text-white' : 'text-dark' ?>"></i>
            </div>
            <span class="nav-link-text ms-1 texto-negro">Inicio</span>
          </a>
        </li>

         <li class="nav-item ">
           <a class="nav-link <?= $pagina_actual == 'home' ? 'bg-activo' : '' ?>" href="?pagina=home">
            <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
              <i class="fa-solid fa-house text-sm texto-negro <?= $pagina_actual == 'home' ? 'text-white' : 'text-dark' ?>"></i>
            </div>
            <span class="nav-link-text ms-1 texto-negro">Backups</span>
          </a>
        </li>

         <li class="nav-item ">
           <a class="nav-link <?= $pagina_actual == 'home' ? 'bg-activo' : '' ?>" href="?pagina=home">
            <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
              <i class="fa-solid fa-house text-sm texto-negro <?= $pagina_actual == 'home' ? 'text-white' : 'text-dark' ?>"></i>
            </div>
            <span class="nav-link-text ms-1 texto-negro">Base de datos</span>
          </a>
        </li>

         <li class="nav-item ">
           <a class="nav-link <?= $pagina_actual == 'home' ? 'bg-activo' : '' ?>" href="?pagina=home">
            <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
              <i class="fa-solid fa-house text-sm texto-negro <?= $pagina_actual == 'home' ? 'text-white' : 'text-dark' ?>"></i>
            </div>
            <span class="nav-link-text ms-1 texto-negro">Usuarios</span>
          </a>
        </li>

         <li class="nav-item mt-3">
          <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder texto-rosa">Visualizar</h6>
        </li>

        <li class="nav-item ">
           <a class="nav-link <?= $pagina_actual == 'home' ? 'bg-activo' : '' ?>" href="?pagina=home">
            <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
              <i class="fa-solid fa-house text-sm texto-negro <?= $pagina_actual == 'home' ? 'text-white' : 'text-dark' ?>"></i>
            </div>
            <span class="nav-link-text ms-1 texto-negro">Modo Admin</span>
          </a>
        </li>

         <li class="nav-item ">
           <a class="nav-link <?= $pagina_actual == 'home' ? 'bg-activo' : '' ?>" href="?pagina=home">
            <div class="icon icon-shape icon-sm border-radius-md text-center me-2 d-flex align-items-center justify-content-center">
              <i class="fa-solid fa-house text-sm texto-negro <?= $pagina_actual == 'home' ? 'text-white' : 'text-dark' ?>"></i>
            </div>
            <span class="nav-link-text ms-1 texto-negro">Modo Tienda</span>
          </a>
        </li>
        

        
      
    </ul>
    </div> 

    <div class="sidenav-footer mx-3 ">
      <div class="card card-plain shadow-none" id="sidenavCard">
        <img class="w-50 mx-auto" src="assets/img/root.png" alt="sidebar_illustration">
        <div class="card-body text-center p-3 w-100 pt-0">
          <div class="docs-info">
            <h6 class="mb-0 texto-negro">Visualizar Modo</h6>
          </div>
        </div>
      </div>
      <a href="?pagina=catalogo" class="btn btn-primary btn-sm w-100 mb-3">TIENDA</a>
      <a class="btn btn-primary btn-sm mb-0 w-100" href="?pagina=home">ADMINISTRATIVO</a>
    </div>

  </aside>
