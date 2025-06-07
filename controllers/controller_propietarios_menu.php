<?php
class PropietariosMenuController
{


    /* CONTAR CAFETERIAS DE PROPIETARIO */

    static public function cafeteriasPropietarioController()
    {
        return PropietariosMenuModel::cafeteriasPropietarioModel($_SESSION['id']);
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
                $data .= '
                <div class="form-check form-switch">
                    <div class="row align-items-center">
                        <div class="col-10">
                            <label class="form-check-label" for="' . $subcategoria['id'] . '">' . $subcategoria['nombre'] . '</label>
                        </div>
                        <div class="col-2">
                            <input class="form-check-input check_subcategoria" type="checkbox" id="' . $subcategoria['id'] . '" estado="' . $subcategoria['estado'] . '" idRegistro="' . $subcategoria['id_registro'] . '" ' . $checked . '>
                        </div>
                    </div>
                </div>
                ';
            }

            // Obtener subcategorias_extra
            $subcategorias_extra = PropietariosMenuModel::obtenerSubcategoriasExtraModel($categoria['id'], $id_propietario);

            foreach ($subcategorias_extra as $subcategoria) {
                $checked = '';
                if ($subcategoria['estado'] == 1) {
                    $checked = 'checked';
                }
                $data .= '
                <div class="form-check form-switch">
                    <div class="row align-items-center">
                        <div class="col-10">
                            <a class="me-1 eliminarRegistro" style="color:red; cursor:pointer" tabla="propietarios_menu_subcategorias_extra" idRegistro="' . $subcategoria['id'] . '">x</a>
                            <label class="form-check-label" for="' . $subcategoria['id'] . '">' . $subcategoria['nombre'] . '</label>
                        </div>
                        <div class="col-2">
                            <input class="form-check-input check_subcategoria_extra" type="checkbox" id="' . $subcategoria['id'] . '" estado="' . $subcategoria['estado'] . '" ' . $checked . '>
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


    /* INSERTAR REGISTRO DE SUBCATEGORIAS EXTRA */

    static public function agregarSubcategoriaExtraController($datos)
    {

        $datos['estado'] = ($datos['estado'] == 1) ? 0 : 1;
        PropietariosMenuModel::cambiarEstadoSubcategoriaExtraModel($datos);
    }

    /* INSERTAR REGISTRO DE SUBCATEGORIAS EXTRA */


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

        return json_encode(PropietariosMenuModel::buscarProductoModel($datos, $datos['cafeteria'], $datos['campo']));
    }

    /* BUSCAR REGISTRO DE PRODUCTOS */


    /* OBTENER MENU POR PROPIETARIO */

    static public function obtenerMenuPropietarioController($productosActivos, $cafeterias, $extras, $ExtrasActivas = true)
    {
        $id_propietario = $_SESSION['id'];

        $url = TemplateController::obtenerUrlController();
        $categorias = '<li><a href="todos"class="menu-link active">Todos</a></li>';

        foreach (PropietariosMenuModel::obtenerCategoriasPropietarioModel($id_propietario, $extras) as $categoria) {
            $categorias .= '<li><a href="' . $categoria['id'] . '" class="menu-link">' . $categoria['nombre'] . '</a></li>';
        }

        $subcategorias = '';


        // Subcategorías extra ---->>>>

        if ($extras) {

            foreach (PropietariosMenuModel::obtenerSubcategoriasExtraModel(false, $id_propietario, $ExtrasActivas) as $subcategoria) {

                $productos_data = '';
                $hidden = '';
                $hidden = ($subcategoria['id_categoria'] == 1 ||
                    $subcategoria['id_categoria'] == 2 ||
                    $subcategoria['id_categoria'] == 3 ||
                    $subcategoria['id_categoria'] == 4)
                    ? '' : 'hidden';

                $productos = PropietariosMenuModel::obtenerProductosExtraModel($subcategoria['id'], $id_propietario, 'id_subcategoria_extra', false, $cafeterias);

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
                    $productos_data .= '<div class="product-item">
                                            <img src="' . $url . $producto['imagen'] . '" alt="" class="product-image">
                                            <div class="product-info">
                                                <span class="product-name">' . $producto['nombre'] . '</span>
                                                <span class="product-price"></span>
                                            </div>
                                            <div class="form-check form-switch">
                                                <div class="row align-items-center">
                                                    <div class="col">
                                                        <button type="button" class="btn btn-icono btn-categorias btn_editar_tamanos" campo="id_producto_extra" idProducto="' . $producto['id_producto_extra'] . '" ' . $hidden . '></button>
                                                        <input class="form-check-input check_productos mt-3" type="checkbox" campo="id_producto_extra" id="' . $producto['id'] . '" idRegistro="' . $producto['id_registro'] . '" estado="' . $producto['estado'] . '" ' . $checked . '>
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
        }


        foreach (PropietariosMenuModel::obtenerSubcategoriasPropietarioModel($id_propietario, 'activas') as $subcategoria) {

            $productos_data = '';
            $hidden = '';
            $hidden = ($subcategoria['id_categoria'] == 1 ||
                $subcategoria['id_categoria'] == 2 ||
                $subcategoria['id_categoria'] == 3 ||
                $subcategoria['id_categoria'] == 4)
                ? '' : 'hidden';

            $productos = PropietariosMenuModel::obtenerProductosModel($subcategoria['id'], $id_propietario, $productosActivos, $cafeterias, $extras);
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
                $campo = 'id_producto';
                $idProducto = $producto['id'];
                if (isset($producto['id_producto_extra'])) {
                    $campo = 'id_producto_extra';
                    $idProducto = $producto['id_producto_extra'];
                }
                $productos_data .= '<div class="product-item">
                                        <img src="' . $url . $producto['imagen'] . '" alt="" class="product-image">
                                        <div class="product-info">
                                            <span class="product-name">' . $producto['nombre'] . '</span>
                                            <span class="product-price"></span>
                                        </div>
                                        <div class="form-check form-switch">
                                            <div class="row align-items-center">
                                                <div class="col">
                                                    <button type="button" class="btn btn-icono btn-categorias btn_editar_tamanos" campo="' . $campo . '" idProducto="' . $idProducto . '" ' . $hidden . '></button>
                                                    <input class="form-check-input check_productos mt-3" type="checkbox" campo="' . $campo . '" id="' . $producto['id'] . '" estado="' . $producto['estado'] . '" idRegistro="' . $producto['id_registro'] . '" ' . $checked . '>
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

        if ($datos['actualizar'] == 'precios') {
            foreach (PropietariosMenuModel::buscarCafeteriasUsuarioModel($id_propietario) as $cafeterias) {
                foreach (PropietariosMenuModel::buscarMenuPropietarioModel($id_propietario) as $item) {
                    PropietariosMenuModel::actualizarPrecioSucursaleModel($item, $cafeterias['id']);
                }
            }
        } else {
            PropietariosMenuModel::eliminarMenuSucursalModel($id_propietario);
            foreach (PropietariosMenuModel::buscarCafeteriasUsuarioModel($id_propietario) as $cafeterias) {
                foreach (PropietariosMenuModel::buscarMenuPropietarioModel($id_propietario) as $item) {
                    PropietariosMenuModel::actualizarMenuSucursalModel($item, $cafeterias['id']);
                }
            }
        }
    }

    /* ACTUALIZAR MENU */


    /* AGREGAR SUBCATEGORIAS */

    static public function registrarSubcategoriaController($datos)
    {
        $datos['id_propietario'] = $_SESSION['id'];

        $validacion_nombre = GeneralModel::validarCampoModel($datos['nombre'], "nombre", "menu_subcategorias");
        //en caso que el correo ya se encuentre registrado por otra cuenta retornamos el error y terminamos la ejecución
        if ($validacion_nombre) return "error_validacion_nombre";

        $datos['id'] = PropietariosMenuModel::registrarSubcategoriaModel($datos);

        return "success";
    }

    /* AGREGAR SUBCATEGORIAS */


    /* OBTENER SUBCATEGORIAS */

    static public function obtenerSubcategoriasExtraController()
    {
        $id_propietario = $_SESSION['id'];
        return PropietariosMenuModel::obtenerSubcategoriasExtraModel(false, $id_propietario);
    }

    /* OBTENER SUBCATEGORIAS */


    /* AGREGAR PRODUCTOS EXTRA */

    static public function agregarProductosExtraController($datos)
    {
        $datos['id_propietario'] = $_SESSION['id'];
        $datos['fecha_alta'] = date("Y-m-d H:i:s");

        $validacion_nombre = GeneralModel::validarCampoModel($datos['nombre'], "nombre", "propietarios_menu_productos_extra");

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
}
