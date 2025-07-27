<?php
class MenuIngredientesController
{



    static public function obtenerIngredientesController()
    {
        $url = TemplateController::obtenerUrlController();
        $data = [];
        $i = 0;
        foreach (MenuIngredientesModel::obtenerIngredientesModel() as $ingrediente) {
            $checked = ($ingrediente['estado'] == 0) ? "checked" : "";
            $botones = '<button class="btn btn-icono btn-eliminar eliminarRegistro" tabla="menu_ingredientes" idRegistro="' . $ingrediente['id'] . '"></button>    
                    <button class="btn btn-icono btn-editar btn_editar_ingrediente" idRegistro="' . $ingrediente['id'] . '" nombre="' . $ingrediente['nombre'] . '" categoria=' . $ingrediente['id_ingrediente_categoria'] . '></button>';
            $estado = '<div class="form-check form-switch">
                <input type="checkbox"
                class="form-check-input cambioEstado"
                id="switch' . $ingrediente['id'] . '"
                tabla="menu_ingredientes"
                idRegistro="' . $ingrediente['id'] . '"
                ' . $checked . '>
                <label class="custom-control-label" for="switch' . $ingrediente['id'] . '"></label>
            </div>';
            $data[] = [
                ++$i,
                $botones,
                $estado,
                $ingrediente['nombre'],
                $ingrediente['categoria'],
            ];
        }

        return json_encode(['data' => $data]);
    }


    static public function agregarIngredienteController($datos)
    {
        date_default_timezone_set("America/Tijuana");
        $datos['id_alta'] = $_SESSION['id'];
        $datos['fecha_alta'] = date("Y-m-d H:i:s");
        
        if (!$datos['id']) {

            $datos['id'] = MenuIngredientesModel::agregarIngredienteModel($datos);
        } else {
            MenuIngredientesModel::editarIngredienteModel($datos);
        }

        $ingrediente = MenuIngredientesModel::buscarIngredienteModel($datos['id']);


        return "success";
    }


    static public function obtenerCategoriasController()
    {
        return MenuIngredientesModel::obtenerCategoriasModel();
    }
}
