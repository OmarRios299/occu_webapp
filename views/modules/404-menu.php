<!-- Sección: Menú no disponible -->
<div class="seccion__sin-menu d-flex align-items-center justify-content-center min-vh-100 bg-light">
    <div class="text-center px-3">
        <img src="<?= $url ?>\views\assets\img\utilidades\registro\404-menu.png" alt="Menú no disponible" class="img-fluid mb-4" style="max-width: 300px;">
        <h2 class="mb-3 text-dark">El menú aún no está disponible</h2>
        <?php
        if ($_SESSION['nivel'] == "Propietario" && $action[0] == "cafeterias_menu") {
            $cafeteria = GeneralController::verificarCafeteriaContoller($action[1], $_SESSION['id']);
            if ($cafeteria) {
        ?>
                <p class="text-muted mb-4">
                    Aún no has configurado tu menú.
                </p>
                <a href="<?= $url ?>propietarios_menu_categorias" class="btn btn-outline-primary">Configurar menú</a>
        <?php
                return;
            }
        }
        if ($_SESSION['nivel'] == "Propietario") {
        ?>
            <p class="text-muted mb-4">
                Aún no has agregado ninguna categorías.
            </p>
            <a href="<?= $url ?>propietarios_menu_categorias" class="btn btn-outline-primary">Agregar categorías</a>
        <?php
            return;
        }
        ?>
        <p class="text-muted mb-4">
            Esta cafetería todavía no ha configurado su menú. Pronto podrás ver sus productos disponibles aquí.
        </p>
        <a href="<?= $url ?>" class="btn btn-outline-primary">Volver al inicio</a>
    </div>
</div>