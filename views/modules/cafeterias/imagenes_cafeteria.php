<div class="titulo-boton mt-4">
    <h1 class="titulo-modulo">Cafeterías</h1>
</div>

<div class="my-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-chevron p-3 bg-body-tertiary rounded-3">
            <li class="breadcrumb-item">
                <a class="link-body-emphasis" href="<?= $url . 'cafeterias' ?>">
                    <i class="fas fa-home" style="color: black;"></i>
                    <span class="visually-hidden">Home</span>
                </a>
            </li>
            <li class="breadcrumb-item">
                <a class="link-body-emphasis fw-semibold text-decoration-none" href="<?= $url .'cafeterias/'. $action[2] .'/editar' ?>">Mi cafetería</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                Agregando imgenes
            </li>
        </ol>
    </nav>
</div>