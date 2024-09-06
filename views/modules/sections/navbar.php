<!-- <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <button type="button" id="sidebarCollapse" class="btn btn-menu">
            <span class="material-icons">list</span>
        </button>
        <div class="nav navbar-nav ml-auto mt-2">
            <p>Hola, <span><?= $_SESSION['nombre_completo']; ?></span></p>
        </div>
        <div>
            <a class="btn btn-sesion" href="<?php echo $url ?>salir"> Cerrar sesion </a>
        </div>
        
    </div>
</nav> -->
<nav class="navbar navbar-expand-lg fixed-top navbar-dark bg-plantilla" aria-label="Main navigation">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">OCCU</a>
        <button class="navbar-toggler p-0 border-0" type="button" id="navbarSideCollapse" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="navbar-collapse offcanvas-collapse" id="navbarsExampleDefault">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link nav-item" aria-current="page" item='nav1' id='nav1' href="<?= $url . 'dashboard'; ?>">Dashboard</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle item-color nav-item" id='nav2' data-bs-toggle="dropdown" aria-expanded="false">Administración</a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item item-color nav-item" item='nav2' href="<?= $url . 'admin_usuarios'; ?>"><span>Usuarios</span></a>
                        </li>
                        <li>
                            <a class="dropdown-item item-color nav-item" item='nav2' href="<?= $url . 'cafeterias'; ?>"><span>Cafeterías</span></a>
                        </li>
                        <li>
                            <a class="dropdown-item item-color nav-item" item='nav2' href="<?= $url . 'cafeterias_productos_categorias'; ?>"><span>Cetegorías de productos</span></a>
                        </li>
                        <li>
                            <a class="dropdown-item item-color nav-item" item='nav2' href="<?= $url . ''; ?>"><span>Ingredientes</span></a>
                        </li>
                        <li>
                            <a class="dropdown-item item-color nav-item" item='nav2' href="<?= $url . 'admin_paises'; ?>"><span>Países</span></a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id='nav3' data-bs-toggle="dropdown" aria-expanded="false">Cafeterías</a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" item='nav3' href="<?= $url . 'cafeterias_mapa'; ?>"><span>Ubicaciones</span></a>
                        </li>
                        <li>
                            <a class="dropdown-item" item='nav3' href="<?= $url . 'cafeterias_lista'; ?>"><span>Lista de cafeterías</span></a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id='na4' data-bs-toggle="dropdown" aria-expanded="false">Catálogo</a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" item='nav4' href="<?= $url . ''; ?>"><span>Para baristas</span></a>
                        </li>
                        <li>
                            <a class="dropdown-item" item='nav4' href="<?= $url . ''; ?>"><span>Para cafeterías</span></a>
                        </li>
                    </ul>
                </li>
            </ul>
            <div class="row">
                <div class="col-md-8 mt-2 ">
                    <p class="letras-blancas">Hola, <i class="bi bi-house"></i><span><?= $_SESSION['nombre_completo']; ?></span></p>
                </div>
                <div class="col-md-4">
                    <a class="btn btn-sesion" href="<?php echo $url ?>salir"> Cerrar sesion </a>
                </div>

            </div>
        </div>
    </div>
</nav>

<!-- <div class="nav-scroller bg-body shadow-sm">
    <nav class="nav" aria-label="Secondary navigation">
        <a class="nav-link active" aria-current="page" href="#">Dashboard</a>
        <a class="nav-link" href="#">
            Friends
            <span class="badge text-bg-light rounded-pill align-text-bottom">27</span>
        </a>
        <a class="nav-link" href="#">Explore</a>
        <a class="nav-link" href="#">Suggestions</a>
     
    </nav>
</div> -->