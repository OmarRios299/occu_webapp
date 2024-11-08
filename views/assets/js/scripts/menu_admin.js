$(document).ready(function () {
  // Función para cambiar entre categorías
  $(".menu-link").click(function (e) {
    e.preventDefault();

    // Remover la clase 'active' de todas las pestañas y categorías
    $(".menu-link").removeClass("active");
    $(".category").removeClass("active");

    // Añadir la clase 'active' a la pestaña y categoría seleccionada
    $(this).addClass("active");
    const target = $(this).attr("href");
    $(target).addClass("active");

    // Hacer scroll hacia la categoría seleccionada (opcional para mejor UX)
    $('html, body').animate({
      scrollTop: $(target).offset().top - $(".nav-menu").height()
    }, 500);
  });

  // Mostrar la primera categoría por defecto
  $(".category").first().addClass("active");
});
