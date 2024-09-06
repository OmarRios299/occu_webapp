<!-- Sidebar  -->
<nav id="sidebar">
    <div id="dismiss">
        <i class="material-icons">arrow_back_ios</i>
    </div>

    <div class="sidebar-header">
        <h3>OCCU</h3>
    </div>

    <ul class="list-unstyled components">
        <li class="nav-item">
            <a class="nav-link" href="<?= $url.'dashboard'; ?>" ><span class="material-icons">grid_view </span><span>Dashboard</span></a>
            
        </li>

        <!-- Administración -->
        <li class="nav-item">
            <a class="nav-link" href="#nav1" data-toggle="collapse" aria-expanded="false"><span class="material-icons">manage_accounts</span><span>Administración</span></a>
            <ul class="collapse list-unstyled" id="nav1">
                <li>
                    <a class="nav-link" href="<?= $url.'admin_usuarios'; ?>"><span>Usuarios</span></a>
                </li>
                <li>
                    <a class="nav-link" href="<?= $url.'admin_cafeterias'; ?>"><span>Cafeterías</span></a>
                </li>
                <li>
                    <a class="nav-link" href="<?= $url.'admin_paises'; ?>"><span>Países</span></a>
                </li>
            </ul>
        </li>

        <!-- Cafeterías -->
        <li class="nav-item">
            <a class="nav-link" href="#nav2" data-toggle="collapse" aria-expanded="false"><span class="material-icons">store</span><span>Cafeterías</span></a>
            <ul class="collapse list-unstyled" id="nav2">
                <li>
                    <a class="nav-link" href="<?= $url.'cafeterias_ubicaciones'; ?>"><span>Ubicaciones</span></a>
                </li>
                <li>
                    <a class="nav-link" href="<?= $url.'cafeterias_lista'; ?>"><span>Lista de cafeterías</span></a>
                </li>
            </ul>
        </li>

    <ul class="list-unstyled CTAs">
        <li>
            <a href="" class="download">Botón 1</a>
        </li>
        <li>
            <a href="" class="article">Botón 2</a>
        </li>
    </ul>
</nav>