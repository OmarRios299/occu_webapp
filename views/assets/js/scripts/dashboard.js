$(document).ready(function () {

    if (moduloActual == 'dashboard') {
        if (grafica_ciudades) {

            // Destruimos la gráfica si ya existe para evitar conflictos
            if (grafica_ciudades && grafica_ciudades.destroy) {
                grafica_ciudades.destroy();
            }

            // Creamos la nueva gráfica
            grafica_ciudades = new Chart(document.getElementById('grafica_ciudades').getContext('2d'), {
                type: 'line',
                data: {
                    labels: grafica_ciudades.map(ciudad => ciudad.nombre),
                    datasets: [
                        {
                            label: 'Cafeterías',
                            data: grafica_ciudades.map(ciudad => ciudad.total_cafeterias),
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',  // Puedes ajustar los colores
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1,
                            fill: false
                        },
                        {
                            label: 'Propietarios',
                            data: grafica_ciudades.map(ciudad => ciudad.total_propietarios),
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1,
                            fill: false
                        },
                        {
                            label: 'Baristas',
                            data: grafica_ciudades.map(ciudad => ciudad.total_baristas),
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1,
                            fill: false
                        },
                        {
                            label: 'Consumidores',
                            data: grafica_ciudades.map(ciudad => ciudad.total_consumidores),
                            backgroundColor: 'rgba(153, 102, 255, 0.2)',
                            borderColor: 'rgba(153, 102, 255, 1)',
                            borderWidth: 1,
                            fill: false
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: "Gráfica comparativa"
                        },
                        legend: {
                            display: true
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true // Asegura que el eje y comience en 0
                        }
                    }
                }
            });
        }
    }

});

function cargarMetricosDashboard() {
    var datos = new FormData();

    datos.append("cargarMetricos", true);
    datos.append('pais', $("#id_ciudad").val());
    $.ajax({
        url: url + 'views/ajax/ajax_dashboard.php',
        method: 'POST',
        data: datos,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: cargaSistema(true),
        success: function (respuesta) {
            respuesta = JSON.parse(respuesta)
            console.log(respuesta);
            $("#total_cafeterias").html(respuesta.contadores.total_cafeterias);
            $("#total_ciudades").html(respuesta.contadores.total_ciudades);
            cargarGraficaDiasOTs(respuesta.data);
            cargaSistema(false);
        }
    });
}
