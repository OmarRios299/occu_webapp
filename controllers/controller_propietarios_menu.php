<?php
class PropietariosMenuController
{


    /* CONTAR CAFETERIAS DE PROPIETARIO */

    static public function cafeteriasPropietarioController()
    {
        return PropietariosMenuModel::cafeteriasPropietarioModel($_SESSION['id']);
    }

    static public function cafeteriasSucPropietarioController()
    {
        return PropietariosMenuModel::cafeteriasSucPropietarioModel($_SESSION['id']);
    }

    /* CONTAR CAFETERIAS DE PROPIETARIO */


    /* OBTENER CATEGORIAS */

    static public function obtenerCategoriasController()
    {
        $id_propietario = $_SESSION['id'];

        $data = '';
        foreach (PropietariosMenuModel::obtenerCategoriasModel() as $categoria) {
            $data .= '
            <div class="col-md-4">
                <h6 class=""><b>' . $categoria['nombre'] . '</b></h6>
                <div class="row mb-3">
            ';

            // verificar si la cafeteria ya cuenta con registros de subcategorias para la informacion que se mostrará
            $subcategorias = PropietariosMenuModel::obtenerSubcategoriasModel($categoria['id'], $id_propietario);

            foreach ($subcategorias as $subcategoria) {
                $checked = '';
                if ($subcategoria['estado'] == 1) {
                    $checked = 'checked';
                }
                $eliminar = ($subcategoria['registro_occu']==2) ? '<a class="me-1 eliminarRegistro" style="color:red; cursor:pointer" tabla="menu_subcategorias" idRegistro="' . $subcategoria['id'] . '">x</a>' : '';
                $data .= '
                <div class="form-check form-switch">
                    <div class="row align-items-center">
                        <div class="col-10">
                            '.$eliminar.'
                            <label class="form-check-label" for="' . $subcategoria['id'] . '">' . $subcategoria['nombre'] . '</label>
                        </div>
                        <div class="col-2">
                            <input class="form-check-input check_subcategoria" type="checkbox" id="' . $subcategoria['id'] . '" estado="' . $subcategoria['estado'] . '" idRegistro="' . $subcategoria['id_registro'] . '" ' . $checked . '>
                        </div>
                    </div>
                </div>
                ';
            }

            $data .= '</div></div>';
        }

        return json_encode($data);
    }

    /* OBTENER CATEGORIAS */


    /* INSERTAR REGISTRO DE SUBCATEGORIAS */

    static public function agregarSubcategoriaController($datos)
    {
        if ($datos['id_registro'] != 'No') {
            $datos['estado'] = ($datos['estado'] == 1) ? 0 : 1;
            PropietariosMenuModel::cambiarEstadoSubcategoriaModel($datos);
        } else {
            //$cafeterias = PropietariosMenuModel::buscarCafeteriasUsuarioModel($datos['id_usuario']);
            $datos['id_propietario'] = $_SESSION['id'];
            PropietariosMenuModel::agregarSubcategoriaModel($datos);
        }
    }

    /* INSERTAR REGISTRO DE SUBCATEGORIAS */


    /* INSERTAR REGISTRO DE PRODUCTOS */

    static public function agregarProductoController($datos)
    {
        if ($datos['cafeteria'] !== 'false') {
            $cafeteria = GeneralController::verificarCafeteriaContoller($datos['cafeteria'], $_SESSION['id']);
            if (!$cafeteria) {
                return 'error';
            }
        }
        if ($datos['id_registro'] != 'No') {
            $datos['estado'] = ($datos['estado'] == 1) ? 0 : 1;
            PropietariosMenuModel::cambiarEstadoProductoModel($datos, $datos['cafeteria']);
        } else {
            // $datos['id_usuario'] = $_SESSION['id'];
            //$cafeterias = PropietariosMenuModel::buscarCafeteriasUsuarioModel($datos['id_usuario']);
            $datos['id_propietario'] = $_SESSION['id'];
            PropietariosMenuModel::agregarProductoModel($datos);
        }
    }

    /* INSERTAR REGISTRO DE PRODUCTOS */


    /* ACTIVA TAMANO DE PRODUCTOS */

    static public function activarTamanoController($datos)
    {

        $datos['id_propietario'] = $_SESSION['id'];
        if ($datos['cafeteria'] !== 'false') {
            $cafeteria = GeneralController::verificarCafeteriaContoller($datos['cafeteria'], $_SESSION['id']);
            if (!$cafeteria) {
                return 'error';
            }
        }

        if ($datos['accion'] == 'desactivar') {
            PropietariosMenuModel::desactivarProductoModel($datos, $datos['cafeteria']);
        } else {
            PropietariosMenuModel::actualizarProductoModel($datos, $datos['cafeteria']);
        }
    }

    /* ACTIVA TAMANO DE PRODUCTOS */



    /* BUSCAR REGISTRO DE PRODUCTOS */

    static public function buscarProductoController($datos)
    {
        $datos['id_propietario'] = $_SESSION['id'];
        if ($datos['cafeteria'] !== 'false') {
            $cafeteria = GeneralController::verificarCafeteriaContoller($datos['cafeteria'], $_SESSION['id']);
            if (!$cafeteria) {
                return json_encode(['error' => 'Cafetería no válida']);
            }
        }

        $data = '';

        foreach (PropietariosMenuModel::obtenerCategoriasingredientesModel() as $categoria) {
            $data .= '
            <div class="col-md-4">
                <h5 class=""><b>' . $categoria['nombre'] . '</b></h5>
                <hr/>
            ';

            // verificar si la cafeteria ya cuenta con registros de ingrediente para la informacion que se mostrará
            $ingredientes = PropietariosMenuModel::obtenerIngredientesModel($categoria['id'], $datos, $datos['id_producto']);

            foreach ($ingredientes as $ingrediente) {
                $checked = '';
                if ($ingrediente['estado'] == 1) {
                    $checked = 'checked';
                }
                $checked_ex = '';
                $display = 'style="display:none"';
                if ($ingrediente['costo_extra'] == 'Si') {
                    $checked_ex = 'checked';
                    $display = '';
                }
                $eliminar = ($ingrediente['registro_occu']==2) ? '<a class="me-1 eliminarRegistro" style="color:red; cursor:pointer" tabla="menu_ingredientes" idRegistro="' . $ingrediente['id'] . '">x</a>' : '';
                $data .= '
                    <div class="row mb-3 divItemIngrediente">
                        <div class="col-5 ml-2">
                            '.$eliminar.'
                            <b><label class="form-check-label" for="' . $ingrediente['id'] . '">' . $ingrediente['nombre'] . '</label></b>
                        </div>
                         <div class="col-5">
                            <div class="form-check">
                                <input class="form-check-input checkIng_precio_extra" type="checkbox" ' . $checked_ex . ' costo_extra="' . $ingrediente['costo_extra'] . '" idRegistro="' . $ingrediente['id_registro'] . '">
                                <label class="form-check-label">
                                   Costo extra
                                </label>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="form-check form-switch">
                                <input class="form-check-input check_ingredientes" type="checkbox" id="' . $ingrediente['id'] . '" estado="' . $ingrediente['estado'] . '" idRegistro="' . $ingrediente['id_registro'] . '" ' . $checked . '>
                            </div>
                        </div>
                        
                        ';
                            $data.='
                                <div class="col-12 divExtra" '.$display.'>
                                    <div class="row d-flex justify-content-center ingredItem" idRegistroItem="' . $ingrediente['id_registro'] . '">
                                        <div class="col-5">
                                            <div class="form-group">
                                            <label>Cantidad gratis:</label>
                                            <input type="number" class="form-control ingred_cantidad_gratis" value="' . $ingrediente['cantidad_gratis'] . '">
                                        </div>
                                        </div>
                                        <div class="col-5">
                                            <div class="form-group">
                                                <label>Precio:</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="number" class="form-control ingred_precio_extra" placeholder="0.00" value="' . $ingrediente['precio'] . '">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>';
                        

                $data.='</div>';
            }

            $data .= '</div>';
        }

        return json_encode([
            'producto' => PropietariosMenuModel::buscarProductoModel($datos, $datos['cafeteria']),
            'ingredientes' => $data,
        ]);
    }

    /* BUSCAR REGISTRO DE PRODUCTOS */


    /* OBTENER MENU POR PROPIETARIO */

    static public function obtenerMenuPropietarioController($productosActivos, $cafeterias, $occu)
    {
        $id_propietario = $_SESSION['id'];

        $url = TemplateController::obtenerUrlController();
        $categorias = '<li><a href="todos"class="menu-link active">Todos</a></li>';

        foreach (PropietariosMenuModel::obtenerCategoriasPropietarioModel($id_propietario) as $categoria) {
            $categorias .= '<li><a href="' . $categoria['id'] . '" class="menu-link">' . $categoria['nombre'] . '</a></li>';
        }

        $subcategorias = '';

        foreach (PropietariosMenuModel::obtenerSubcategoriasPropietarioModel($id_propietario, 'activas') as $subcategoria) {

            $productos_data = '';
            $hidden = '';
            $hidden = ($subcategoria['id_categoria'] == 1 ||
                $subcategoria['id_categoria'] == 2 ||
                $subcategoria['id_categoria'] == 3 ||
                $subcategoria['id_categoria'] == 4)
                ? '' : 'hidden';

            $productos = PropietariosMenuModel::obtenerProductosModel($subcategoria['id'], $id_propietario, $productosActivos, $cafeterias,$occu);
            if (!$productos) {
                $productos_data = '                
                    <div class="product-item">
                        <div class="alert alert-warning w-100 m-0">
                            <span class="">Aún no activas ningún producto en esta categoría.</span>
                        </div>
                    </div>';
            }

            foreach ($productos as $producto) {
                $checked = '';
                if ($producto['estado'] == 1) {
                    $checked = 'checked';
                }
                $idProducto = $producto['id'];
                $productos_data .= '<div class="product-item">
                                        <img src="' . $url . $producto['imagen'] . '" alt="" class="product-image">
                                        <div class="product-info">
                                            <span class="product-name">' . $producto['nombre'] . '</span>
                                            <span class="product-price"></span>
                                        </div>
                                        <div class="form-check form-switch">
                                            <div class="row align-items-center">
                                                <div class="col">
                                                    <button type="button" class="btn btn-icono btn-ingredientes btn_editar_ingre" idProducto="' . $idProducto . '" ></button>
                                                    <button type="button" class="btn btn-icono btn-categorias btn_editar_tamanos" idProducto="' . $idProducto . '" ' . $hidden . '></button>
                                                    <input class="form-check-input check_productos mt-3" type="checkbox" id="' . $producto['id'] . '" estado="' . $producto['estado'] . '" idRegistro="' . $producto['id_registro'] . '" ' . $checked . '>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>';
            }

            $subcategorias .= '<div class="' . $subcategoria['id_categoria'] . ' category active">
                                    <h2>' . $subcategoria['nombre'] . '</h2>
                                    ' . $productos_data . '
                                    <hr>
                                </div>';
        }
        return array(
            'subcategorias' => $subcategorias,
            'categorias' => $categorias
        );
    }

    /* OBTENER MENU POR PROPIETARIO */


    /* ACTUALIZAR MENU */

    static public function actualizarMenuController($datos)
    {
        $id_propietario = $_SESSION['id'];

        if ($datos['actualizar'] == 'sucursalMenu') {
           if (!empty($datos['sucursal'])) {
                PropietariosMenuModel::eliminarMenuSucursalModel($datos['sucursal']);
                 PropietariosMenuModel::eliminarIngredientesSucursalModel($datos['sucursal']);
                foreach (PropietariosMenuModel::buscarMenuPropietarioModel($id_propietario) as $item) {
                    PropietariosMenuModel::actualizarMenuSucursalModel($item, $datos['sucursal']);
                }
                foreach (PropietariosMenuModel::buscarPropietarioIngredienteModel($id_propietario) as $ingre) {
                    PropietariosMenuModel::actualizarIngredientesSucursalModel($ingre, $datos['sucursal']);
                }
            }
        }else if ($datos['actualizar'] == 'precios') {
            foreach (PropietariosMenuModel::buscarCafeteriasUsuarioModel($id_propietario) as $cafeterias) {
                foreach (PropietariosMenuModel::buscarMenuPropietarioModel($id_propietario) as $item) {
                    PropietariosMenuModel::actualizarPrecioSucursaleModel($item, $cafeterias['id']);
                }
            }
        } else {
            foreach (PropietariosMenuModel::buscarCafeteriasUsuarioModel($id_propietario) as $cafeterias) {
                PropietariosMenuModel::eliminarMenuSucursalModel($cafeterias['id']);
                 PropietariosMenuModel::eliminarIngredientesSucursalModel($cafeterias['id']);
                foreach (PropietariosMenuModel::buscarMenuPropietarioModel($id_propietario) as $item) {
                    PropietariosMenuModel::actualizarMenuSucursalModel($item, $cafeterias['id']);
                }
                foreach (PropietariosMenuModel::buscarPropietarioIngredienteModel($id_propietario) as $ingre) {
                    PropietariosMenuModel::actualizarIngredientesSucursalModel($ingre, $cafeterias['id']);
                }
            }
        }
    }

    /* ACTUALIZAR MENU */


    /* AGREGAR SUBCATEGORIAS */

    static public function registrarSubcategoriaController($datos)
    {
        date_default_timezone_set("America/Tijuana");
        $datos['fecha_alta'] = date("Y-m-d H:i:s");
        $datos['id_propietario'] = $_SESSION['id'];

        $validacion_nombre = GeneralModel::validarCampoModel($datos['nombre'], "nombre", "menu_subcategorias");
        //en caso que el correo ya se encuentre registrado por otra cuenta retornamos el error y terminamos la ejecución
        if ($validacion_nombre) return "error_validacion_nombre";

        $datos['id'] = PropietariosMenuModel::registrarSubcategoriaModel($datos);

        return "success";
    }

    /* AGREGAR SUBCATEGORIAS */


    /* AGREGAR PRODUCTOS EXTRA */

    static public function agregarProductosExtraController($datos)
    {
        date_default_timezone_set("America/Tijuana");
        $datos['id_propietario'] = $_SESSION['id'];
        $datos['fecha_alta'] = date("Y-m-d H:i:s");

        $validacion_nombre = GeneralModel::validarCampoModel($datos['nombre'], "nombre", "menu_productos");

        //en caso que el correo ya se encuentre registrado por otra cuenta retornamos el error y terminamos la ejecución
        if ($validacion_nombre) return "error_validacion_nombre";

        $datos['id'] = PropietariosMenuModel::agregarProductoExtraModel($datos);

        $producto = PropietariosMenuModel::buscarProductoExtraModel($datos['id']);

        if ($datos['imagen_subir']) {

            if ($producto['imagen'] != "" && file_exists("../../" . $producto['imagen']) && $producto['imagen'] != "views/assets/img/cafeteria_default.png")
                unlink("../../" . $producto['imagen']);
            $nombre_imagen = "imagen_producto_" . $datos['id'];
            $datos['imagen'] = GeneralController::subirImagen($datos['imagen_subir'], "menu_productos", $nombre_imagen);
            PropietariosMenuModel::editarImagenModel($datos);
        }

        return "success";
    }

    /* AGREGAR PRODUCTOS EXTRA */



/* ----- FUNCIONAMIENTO DEL MODAL DE SELECCION DE INGREDIENTES ----- 
---------------------------------------------------------------------*/


    static public function agregarIngredienteController($datos)
    {
        if ($datos['id_registro'] != 'No') {
            $datos['estado'] = ($datos['estado'] == 1) ? 0 : 1;
            PropietariosMenuModel::cambiarEstadoIngredienteModel($datos);
        } else {
            //$cafeterias = PropietariosMenuModel::buscarCafeteriasUsuarioModel($datos['id_usuario']);
            $datos['id_propietario'] = $_SESSION['id'];
            $datos['precio'] =  PropietariosMenuModel::buscarPropietarioPrecioIngredienteModel($datos);
            PropietariosMenuModel::agregarIngredienteModel($datos);
        }
    }

    static public function actualizarIngredienteController($datos)
    {
        return PropietariosMenuModel::actualizarIngredienteModel($datos);   
    }

    static public function checkCostoExtraIngredienteController($datos)
    {
        $datos['estatus'] = ($datos['costo_extra']=="Si") ? "No" : "Si";
        return PropietariosMenuModel::checkCostoExtraIngredienteModel($datos);   
    }

    
    static public function obtenerCategoriasingredientesController(){
        return PropietariosMenuModel::obtenerCategoriasingredientesModel();
    }
    
    static public function agregarPropietarioIngredienteController($datos)
    {
        date_default_timezone_set("America/Tijuana");
        $datos['id_propietario'] = $_SESSION['id'];
        $datos['fecha_alta'] = date("Y-m-d H:i:s");
        PropietariosMenuModel::agregarPropietarioIngredienteModel($datos);
        
    }
}
