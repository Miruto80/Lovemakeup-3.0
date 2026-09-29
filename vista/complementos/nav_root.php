<!-- NAV PLANEL ROOT -->
    <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
        <div class="ms-md-auto pe-md-3 d-flex align-items-center">

            <div class="nombre-usuario d-none d-md-block card-m1">
               <i class="fa-solid fa-circle-user me-2 texto-rosa"></i>  <?php echo $_SESSION['nombre'] . " " . $_SESSION['apellido']; ?>
            </div>

        </div>
  

          <ul class="navbar-nav  justify-content-end">
            <li class="nav-item d-flex align-items-center">
                
              <a href="#" class="nav-link text-white font-weight-bold px-0" data-bs-toggle="modal" data-bs-target="#cerrar">
                <span class="d-sm-inline d-none"> <i class="fa-solid fa-right-to-bracket me-sm-1"></i>  Cerrar sesión</span>
              </a>
                 
            </li>
          </ul>


          <ul class="navbar-nav  justify-content-end">
            <li class="nav-item d-xl-none  d-flex align-items-center">
                <a href="#" class="font-weight-bold text-white" data-bs-toggle="modal" data-bs-target="#cerrar">
                <span class="d-block d-md-none"> <i class="fa-solid fa-right-to-bracket me-2"></i> Salir</span> 
              </a>
            </li>
            <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                <a href="javascript:;" class="nav-link text-white p-0" id="iconNavbarSidenav">
                    <div class="sidenav-toggler-inner">
                    <i class="sidenav-toggler-line bg-white"></i>
                    <i class="sidenav-toggler-line bg-white"></i>
                    <i class="sidenav-toggler-line bg-white"></i>
                    </div>
                </a>
            </li>
        </ul>
        </div>
      </div>
    </nav>

    <!--|||||||||||||||||||||||||||||||||| End Navbar||||||||||||||||||||||||||||||||| -->