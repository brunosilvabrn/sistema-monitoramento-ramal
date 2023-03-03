
const BASE_URL = 'http://localhost:8000';

const ENDPOINT_BUSCA_RAMAL          = BASE_URL + '/src/buscaRamais.php';
const ENDPOINT_STATUS_RAMAL         = BASE_URL + '/src/statusRamais.php';
const ENDPOINT_GERAR_CSV_RAMAL      = BASE_URL + '/src/gerarCsv.php';
const ENDPOINT_ATUALIZAR_LOGS_RAMAL = BASE_URL + '/src/logs.php';
const ENDPOINT_CSV_LOGS_RAMAL       = BASE_URL + '/src/logsCsv.php';

let timer;
let endpointAtual = ENDPOINT_BUSCA_RAMAL;

$(document).ready(function(){
    atualizar();
});

function getChamadaAjax(url, successCallback, errorCallback) {
    $.ajax({
        url: url,
        type: "GET",
        success: successCallback,
        error: errorCallback
    });
}

function atualizarCartoes(data) {
    let html = "";
    let cartoes = $("#cartoes");
    cartoes.empty();

    for (let i in data) {
        let status = data[i].status;
        let nome = data[i].nome;
        let agente = data[i].agente;

        let cartao = $("<div>", {class: "cartao"});
        $("<div>").text(nome).appendTo(cartao);
        $("<span>", {class: status + " icone-posicao"}).appendTo(cartao);
        $("<span>", {class: "mt-3 float-right"}).text(agente).appendTo(cartao);

        html += cartao.prop('outerHTML');
    }

    cartoes.append(html);
}

function atualizarStatusRamaisNumber()
{
    getChamadaAjax(ENDPOINT_STATUS_RAMAL, function(data) {
        let quantidades = data;

        atualizarLog();

        $('#quantIndisponivel').text(data.indisponivel);
        $('#quantDisponivel').text(quantidades.disponivel);
        $('#quantChamando').text(quantidades.chamando);
        $('#quantPausado').text(quantidades.pausado);
        $('#quantOcupado').text(quantidades.ocupado);

    }, function(){
            console.log("Erro ao atualizar quantidade STATUS ramais");
    });
}

function atualizar() {

    getChamadaAjax(endpointAtual, function(data) {
        atualizarCartoes(data);
        atualizarStatusRamaisNumber();
        indisponivelBackgroundCard();
    }, function() {
        console.log("Erro ao atualizar os dados dos ramais");
    });

    timer = setTimeout(atualizar, 10000);
}

function atualizarEndpoint(endpoint, busca = false) {
    clearTimeout(timer);

    if (busca && endpoint !== '') {
        endpointAtual = ENDPOINT_BUSCA_RAMAL + '?specificSearch=' + endpoint;
    } else {
        endpointAtual = ENDPOINT_BUSCA_RAMAL + '?search=' + endpoint;
    }

    atualizar();

    $("#btnMostrarTudo").toggleClass('d-none', endpoint === 'all' || endpoint === '');

}

function atualizarLog() {

    let svg = `<svg xmlns="http://www.w3.org/2000/svg" fill="none" width="20px" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="w-6 h-6">
                <path strokeLinecap="round" strokeLinejoin="round" d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3" />
              </svg>`;

    getChamadaAjax(ENDPOINT_ATUALIZAR_LOGS_RAMAL, function(data) {

            $("#log-ramais").empty();

            $.each(data, function(i, item) {
                let row = $("<tr>");
                row.append($("<td>").text(item.data));
                row.append($("<td>").text(item.ramal));
                row.append($("<td>").text(item.status_antigo));
                row.append($("<td>").html(svg));
                row.append($("<td>").text(item.status_novo));
                $("#log-ramais").append(row);
            });
    }, function() {
        console.log("Erro ao atualizar os LOGS ramais");
    });

}

let inputBusca = $("#buscaRamal");
inputBusca.on('input', function () {
    let parametro = inputBusca.val();
    atualizarEndpoint(parametro, true);
});

$(".btn-status").click(function () {
    var status = $(this).attr('id').replace('btn', '').toLowerCase();
    atualizarEndpoint(status);
});

$("#btnMostrarTudo").click(function () {
    $("#buscaRamal").val("");
    atualizarEndpoint('all');
});

function indisponivelBackgroundCard() {
    $('.indisponivel').parent().css('background-color', '#b3b3b3');
}

function downloadCSV() {
    window.location.href = ENDPOINT_GERAR_CSV_RAMAL;
}

function downloadLogs() {
    window.location.href = ENDPOINT_CSV_LOGS_RAMAL;
}
