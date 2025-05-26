<div class="titulo-boton">
    <h1 class="titulo-modulo">Dashboard</h1>
</div>
<?php
if ($_SESSION['nivel'] == 'Administrador') { ?>
    <div class="row">
        <div class="col-md-4">
            <?php $contador = DashboardController::obtenerDatosContadoresController(); ?>
            <div class="row h-100">
                <!-- <div class="col-md-12">
                <div class="card card-metricos solido estilo-principal h-100">
                    <div class="card-body">
                        <h5 class="card-title">10</h5>
                        <p class="card-text">Total de cafeterías</p>
                    </div>
                </div>
            </div> -->
                <div class="col-md-6">
                    <div class="card card-metricos h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?= $contador['total_cafeterias'] ?></h5>
                            <p class="card-text">Cafeterías</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card card-metricos h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?= $contador['total_propietarios'] ?></h5>
                            <p class="card-text">Propietarios</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card card-metricos h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?= $contador['total_baristas'] ?></h5>
                            <p class="card-text">Baristas</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card card-metricos h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?= $contador['total_consumidores'] ?></h5>
                            <p class="card-text">Consumidores</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="col-md-12">
                <div class="caja">
                    <div class="row">
                        <div class="col-md-12">
                            <?php $grafica_ciudades = DashboardController::cafeteriasPorCiudadController();
                            if ($grafica_ciudades == '') {
                            ?>
                                <script>
                                    grafica_ciudades = false;
                                </script>
                                <div class="alert alert-warning">No hay cafeterías regitradas.</div>
                            <?php } else { ?>
                                <script>
                                    grafica_ciudades = <?= json_encode($grafica_ciudades); ?>
                                </script>
                                <canvas id="grafica_ciudades"></canvas>
                            <?php }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
} else {
?>

<?php
}
?>