<style>
  /* Estilo del menú de navegación superior */
  .nav-menu {
    display: flex;
    justify-content: center;
    background-color: #ffffff;
    border-bottom: 2px solid #ddd;
    padding: 10px 0;
    position: sticky;
    top: 0;
    z-index: 1000;
    overflow-x: auto;
    /* Asegurarse de que el contenido sea desplazable */
    scrollbar-width: thin;
    /* Para navegadores como Firefox */
  }

  /* Estilo del contenedor de lista */
  .nav-menu ul {
    list-style: none;
    display: flex;
    padding: 0;
    margin: 0;
    width: max-content;
    /* Asegurarse de que el ul tenga un ancho suficiente */
  }

  .nav-menu li {
    margin: 0 10px;
  }

  /* Enlaces del menú */
  .menu-link {
    text-decoration: none;
    color: #333;
    padding: 5px 10px;
    font-weight: bold;
  }

  .menu-link.active,
  .menu-link:hover {
    color: #ff956b;
    border-bottom: 2px solid #ff956b;
  }

  /* Diseño para dispositivos móviles */
  @media (max-width: 768px) {
    .nav-menu {
      justify-content: flex-start !important;
      /* Para asegurarse de que empieza desde la izquierda */
      padding: 10px;
      overflow-x: auto;
      /* Permitir desplazamiento horizontal si no caben todas las opciones */
      white-space: nowrap;
      /* Mantener los elementos en una sola línea */
      -webkit-overflow-scrolling: touch;
      /* Para desplazamiento suave en dispositivos táctiles */
    }

    .nav-menu ul {
      display: inline-flex;
      width: max-content;
      /* Para evitar que el ul se colapse si hay muchas opciones */
    }
  }

  /* Ocultar todas las categorías por defecto en dispositivos móviles */
  .category {
    display: none;
  }

  /* Mostrar la categoría activa */
  .category.active {
    display: block;
  }





  .product-list {
    width: 100%;
    max-width: 600px;
    /* Ajusta el ancho máximo según tus necesidades */
    margin: 0 auto;
    /* Centrar la lista */
    padding: 20px;
    background-color: #f9f9f9;
    /* Color de fondo */
    border-radius: 10px;
    /* Bordes redondeados para darle un toque moderno */
  }

  .product-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
    padding: 10px;
    background-color: #ffffff;
    /* Fondo blanco para cada elemento */
    border-radius: 10px;
    box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
    /* Sombra para darle profundidad */
  }

  .product-image {
    width: 50px;
    /* Ajusta el tamaño de la imagen */
    height: 50px;
    border-radius: 50%;
    /* Hacer la imagen redonda */
    object-fit: cover;
    /* Ajustar la imagen sin perder la proporción */
    margin-right: 15px;
  }

  .product-info {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-grow: 1;
  }

  .product-name {
    font-size: 1em;
    color: #333;
    flex-grow: 1;
    /* Ocupa el espacio disponible */
  }

  .product-price {
    font-size: 1em;
    font-weight: bold;
    color: #333;
  }
</style>
<?php
$menu = AdminMenuController::obtenerMenuController();
?>

<div class="menu-cafeterias ">
  <!-- Menú de Navegación -->
  <div class="nav-menu">
    <ul>
      <?php echo $menu['categorias'] ?>
    </ul>
  </div>
  <div class="product-list">
    <!-- Secciones de Categorías -->
    <div class="cafe-menu">
      <?php echo $menu['subcategorias'] ?>

    </div>
  </div>
</div>
