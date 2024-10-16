<nav class="navbar navbar-expand-lg fixed-top navbar-dark bg-plantilla" aria-label="Main navigation">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">OCCU</a>
        <button class="navbar-toggler p-0 border-0" type="button" id="navbarSideCollapse" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="navbar-collapse offcanvas-collapse" id="navbarsExampleDefault">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link nav-item" aria-current="page" item='nav1' id='nav1' href="<?= $url . 'inicio'; ?>">Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-item" aria-current="page" item='nav2' id='nav2' href="<?= $url . 'registrarme'; ?>">Registrarme</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle item-color nav-item" id='nav3' data-bs-toggle="dropdown" aria-expanded="false">Cafeterías</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item item-color nav-item" item='nav3' href="<?= $url . 'cafeterias_mapa'; ?>"><span>Ubicaciones</span></a></li>
                        <li><a class="dropdown-item item-color nav-item" item='nav3' href="<?= $url . 'cafeterias_lista'; ?>"><span>Lista de cafeterías</span></a></li>
                    </ul>
                </li>

            </ul>
            <div class="row">
                <div class="col-md-8 mt-2 ">
                    <p class="letras-blancas">Hola, bienvenido<i class="bi bi-house"></i><span></span></p>
                </div>
                <div class="col-md-4">
                    <a class="btn btn-iniciar-sesion" href="<?php echo $url ?>login"> Iniciar sesion </a>
                </div>

            </div>
        </div>
    </div>
</nav>