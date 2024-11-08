
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
}

.nav-menu ul {
  list-style: none;
  display: flex;
  padding: 0;
  margin: 0;
}

.nav-menu li {
  margin: 0 10px;
}

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
    overflow-x: scroll;
    white-space: nowrap;
    padding: 10px;
  }

  .nav-menu ul {
    display: inline-flex;
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


</style>
<!-- Menú de Navegación -->
<div class="nav-menu">
  <ul>
    <li><a href="#populares" class="menu-link active">Populares</a></li>
    <li><a href="#tacos" class="menu-link">Tacos</a></li>
    <li><a href="#tortas" class="menu-link">Tortas</a></li>
    <li><a href="#platos-fuertes" class="menu-link">Platos Fuertes</a></li>
    <li><a href="#antojitos" class="menu-link">Antojitos</a></li>
  </ul>
</div>

<!-- Secciones de Categorías -->
<div class="cafe-menu">
  <div id="populares" class="category">
    <h2>Populares</h2>
    <!-- Productos de Populares aquí -->
  </div>

  <div id="tacos" class="category">
    <h2>Tacos</h2>
    <!-- Productos de Tacos aquí -->
  </div>

  <div id="tortas" class="category">
    <h2>Tortas</h2>
    <!-- Productos de Tortas aquí -->
  </div>

  <div id="platos-fuertes" class="category">
    <h2>Platos Fuertes</h2>
    <!-- Productos de Platos Fuertes aquí -->
  </div>

  <div id="antojitos" class="category">
    <h2>Antojitos</h2>
    <!-- Productos de Antojitos aquí -->
  </div>
</div>

