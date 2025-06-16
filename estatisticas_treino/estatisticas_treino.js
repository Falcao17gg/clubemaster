// Arquivo JavaScript para funcionalidades de estatísticas de treino

document.addEventListener('DOMContentLoaded', function() {
    // Função para mostrar/ocultar campos de data personalizada
    function toggleDataPersonalizada() {
        var periodo = document.getElementById('periodo').value;
        var dataPersonalizada = document.getElementById('data-personalizada');
        
        if(periodo === 'personalizado') {
            dataPersonalizada.style.display = 'flex';
        } else {
            dataPersonalizada.style.display = 'none';
        }
    }
    
    // Inicializar o estado do formulário
    toggleDataPersonalizada();
    
    // Adicionar evento de mudança ao select de período
    var periodoSelect = document.getElementById('periodo');
    if (periodoSelect) {
        periodoSelect.addEventListener('change', toggleDataPersonalizada);
    }
    
    // Inicializar gráficos se os elementos existirem
    inicializarGraficos();
});

// Função para inicializar os gráficos
function inicializarGraficos() {
    // Verificar se Chart.js está disponível
    if (typeof Chart === 'undefined') {
        console.error('Chart.js não está carregado');
        return;
    }
    
    // Gráfico de Presenças por Mês
    var ctxPresencas = document.getElementById('graficoPresencasMes');
    if (ctxPresencas) {
        var labelsData = JSON.parse(ctxPresencas.getAttribute('data-labels') || '[]');
        var presencasData = JSON.parse(ctxPresencas.getAttribute('data-presencas') || '[]');
        var ausenciasData = JSON.parse(ctxPresencas.getAttribute('data-ausencias') || '[]');
        
        new Chart(ctxPresencas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: labelsData,
                datasets: [{
                    label: 'Presenças',
                    data: presencasData,
                    backgroundColor: 'rgba(40, 167, 69, 0.7)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 1
                }, {
                    label: 'Ausências',
                    data: ausenciasData,
                    backgroundColor: 'rgba(220, 53, 69, 0.7)',
                    borderColor: 'rgba(220, 53, 69, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
    
    // Gráfico de Percentual de Presenças por Mês
    var ctxPercentual = document.getElementById('graficoPercentualMes');
    if (ctxPercentual) {
        var labelsData = JSON.parse(ctxPercentual.getAttribute('data-labels') || '[]');
        var percentuaisData = JSON.parse(ctxPercentual.getAttribute('data-percentuais') || '[]');
        
        new Chart(ctxPercentual.getContext('2d'), {
            type: 'line',
            data: {
                labels: labelsData,
                datasets: [{
                    label: '% de Presenças',
                    data: percentuaisData,
                    backgroundColor: 'rgba(23, 162, 184, 0.2)',
                    borderColor: 'rgba(23, 162, 184, 1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });
    }
    
    // Gráfico de estatísticas por escalão
    var ctxEscaloes = document.getElementById('graficoEscaloes');
    if (ctxEscaloes) {
        var escaloes = JSON.parse(ctxEscaloes.getAttribute('data-escaloes') || '[]');
        var presencasEscaloes = JSON.parse(ctxEscaloes.getAttribute('data-presencas') || '[]');
        var ausenciasEscaloes = JSON.parse(ctxEscaloes.getAttribute('data-ausencias') || '[]');
        
        new Chart(ctxEscaloes.getContext('2d'), {
            type: 'bar',
            data: {
                labels: escaloes,
                datasets: [{
                    label: 'Presenças',
                    data: presencasEscaloes,
                    backgroundColor: 'rgba(40, 167, 69, 0.7)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 1
                }, {
                    label: 'Ausências',
                    data: ausenciasEscaloes,
                    backgroundColor: 'rgba(220, 53, 69, 0.7)',
                    borderColor: 'rgba(220, 53, 69, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
}
