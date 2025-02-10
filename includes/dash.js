// Exemple d'utilisation des données dans dash.js
console.log(window.donneesGlobales);

// Par exemple, accéder aux données des interactions
const interactions = window.donneesGlobales;

console.log('Questions:', interactions.nb_question);
console.log('Réponses:', interactions.nb_comment);

document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('myPieChart').getContext('2d');
    const data = {
        labels: ['Questions', 'Réponses', 'Messages envoyés', 'Messages reçus', 'Likes', 'Dislikes'],
        datasets: [{
            data: [
                interactions.nb_question,
                interactions.nb_comment,
                interactions.nb_message_envoye,
                interactions.nb_message_recus,
                interactions.nb_like,
                interactions.nb_dislike
            ],
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        }]
    };

    const options = {
        responsive: true,
        plugins: {
            legend: {
                position: 'right',
                align: 'start',
            },
            tooltip: {
                callbacks: {
                    label: function(tooltipItem) {
                        return tooltipItem.label + ': ' + tooltipItem.raw.toFixed(2);
                    }
                }
            },
            title: {
                display: true,
                text: 'Diagramme Circulaire des Interactions'
            }
        },
        animation: {
            animateRotate: true,
            animateScale: true
        }
    };

    const myPieChart = new Chart(ctx, {
        type: 'pie',
        data: data,
        options: options,
        plugins: [{
            id: 'insideLabel',
            beforeDraw: (chart) => {
                const { ctx, chartArea } = chart;
                ctx.save();

                const { x, y, width, height } = chartArea;
                const fontSize = (height / 100).toFixed(2);
                ctx.font = fontSize + 'em Verdana';
                ctx.textBaseline = 'middle';

                const total = chart.data.datasets[0].data.reduce((acc, value) => acc + value, 0);
                const offset = Math.PI / 2; // rotation pour commencer à 12h

                chart.data.labels.forEach((label, index) => {
                    const value = chart.data.datasets[0].data[index];
                    const angle = (value / total) * 2 * Math.PI;
                    const midAngle = offset + angle / 2;

                    const xInside = x + width / 2 * Math.cos(midAngle);
                    const yInside = y + height / 2 * Math.sin(midAngle);

                    ctx.fillStyle = 'white';
                    ctx.fillText(value.toFixed(2), xInside, yInside);
                });

                ctx.restore();
            }
        }]
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const bars = document.querySelectorAll('.bar');

    bars.forEach(bar => {
        const value = bar.getAttribute('data-value');
        bar.style.height = `${value}%`;
    });
});
