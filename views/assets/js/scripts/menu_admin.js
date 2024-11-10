
  // Función para cambiar entre categorías
  $(".menu-link").click(function (e) {
      e.preventDefault();

      // Remover la clase 'active' de todas las pestañas y categorías
      $(".menu-link").removeClass("active");
      $(".category").removeClass("active");

      // Añadir la clase 'active' a la pestaña y categoría seleccionada
      $(this).addClass("active");
      const target = $(this).attr("href"); 
      (target=='todos') ? $(".category").addClass("active") : '';
      $('.' + target).addClass("active");

      // Centrando la categoría seleccionada en el menú de navegación
      const $navMenu = $('.nav-menu'); 
      const $selectedItem = $(this);

      // Obtener la posición actual del elemento seleccionado en relación al contenedor del menú
      const itemLeftPosition = $selectedItem.offset().left; 
      const navMenuLeftPosition = $navMenu.offset().left; 
      const itemWidth = $selectedItem.outerWidth(); 
      const navMenuWidth = $navMenu.width(); 

      // Calcular el desplazamiento ideal para centrar el elemento
      let scrollToPosition = $navMenu.scrollLeft() + (itemLeftPosition - navMenuLeftPosition) - (navMenuWidth / 2) + (itemWidth / 2);

      // Asegurarse de que el desplazamiento no sea menor que cero y que no exceda el límite derecho
      const maxScroll = $navMenu[0].scrollWidth - navMenuWidth;
      scrollToPosition = Math.max(0, Math.min(scrollToPosition, maxScroll));

      // Hacer scroll horizontal para centrar la categoría seleccionada
      $navMenu.animate({
          scrollLeft: scrollToPosition
      }, 500);
  });

  // Mostrar solo la primera categoría por defecto
  //$(".category").first().addClass("active");
