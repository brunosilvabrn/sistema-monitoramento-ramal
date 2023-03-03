function getStatusRamaisNumber() {
    return new Promise(function(resolve, reject) {
        $.ajax({
            url: 'http://localhost:8000/src/statusRamais.php',
            type: "GET",
            success: function(data) {
                resolve(data);
            },
            error: function() {
                reject(Error("Erro ao atualizar quantidade STATUS ramais"));
            }
        });
    });
}

$(document).ready(function() {
    getStatusRamaisNumber().then(function (data) {
        let dataStatus = [data.indisponivel, data.disponivel, data.chamando, data.pausado, data.ocupado];
        console.log(dataStatus);

        let chartData = {
            labels: ['Indisponível', 'Disponível', 'Chamando', 'Pausado', 'Ocupado'],
            datasets: [{
                label: 'Status dos Ramais',
                data: dataStatus,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.5)',
                    'rgba(75, 192, 192, 0.5)',
                    'rgba(54, 162, 235, 0.5)',
                    'rgba(255, 206, 86, 0.5)',

                    'rgba(153, 102, 255, 0.5)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',

                    'rgba(153, 102, 255, 1)'
                ],
                borderWidth: 1
            }]
        };

        let chartOptions = {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        stepSize: 1
                    }
                }]
            }
        };

        let ctx = document.getElementById('myChart').getContext('2d');
        let myChart = new Chart(ctx, {
            type: 'bar',
            data: chartData,
            options: chartOptions
        });

        function updateChart() {
            getStatusRamaisNumber().then(function (data) {
                let dataStatus = [data.indisponivel, data.disponivel, data.chamando, data.pausado, data.ocupado];

                // Atualiza os dados do gráfico
                myChart.data.datasets[0].data = dataStatus;
                myChart.update();
                console.log("foi");
            });
        }

        setInterval(updateChart, 10000);
    }).catch(function (error) {
        console.log(error);
    });
});
