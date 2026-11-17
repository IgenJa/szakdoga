if (typeof Chart !== 'undefined') {
    Chart.defaults.color = '#9c968c';
    Chart.defaults.borderColor = '#3c3935';
}

const chartDataElement = document.querySelector('#chart-data');

if (chartDataElement) {
    const chartData = JSON.parse(chartDataElement.textContent);

    drawBarChart('#speaking-time-chart', chartData.speakerNames, chartData.speakingSeconds, chartData.colors, 'Másodperc');
    drawDoughnutChart('#distribution-chart', chartData);
    drawBarChart('#turns-chart', chartData.speakerNames, chartData.turnCounts, chartData.colors, 'Megszólalás');
    drawWordChart('#words-chart', chartData);
    drawActivityChart('#activity-chart', chartData);
}

function drawBarChart(selector, labels, values, colors, axisLabel) {
    const canvas = document.querySelector(selector);

    if (!canvas) {
        return;
    }

    new Chart(canvas, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: colors,
            }],
        },
        options: {
            plugins: {
                legend: {
                    display: false,
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: axisLabel,
                    },
                },
            },
        },
    });
}

function drawDoughnutChart(selector, chartData) {
    const canvas = document.querySelector(selector);

    if (!canvas) {
        return;
    }

    new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels: chartData.speakerNames,
            datasets: [{
                data: chartData.speakingSeconds,
                backgroundColor: chartData.colors,
            }],
        },
    });
}

function drawWordChart(selector, chartData) {
    const canvas = document.querySelector(selector);

    if (!canvas || chartData.wordLabels.length === 0) {
        return;
    }

    new Chart(canvas, {
        type: 'bar',
        data: {
            labels: chartData.wordLabels,
            datasets: [{
                data: chartData.wordCounts,
                backgroundColor: '#c4a882',
            }],
        },
        options: {
            indexAxis: 'y',
            plugins: {
                legend: {
                    display: false,
                },
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                    },
                },
            },
        },
    });
}

function drawActivityChart(selector, chartData) {
    const canvas = document.querySelector(selector);

    if (!canvas) {
        return;
    }

    const datasets = chartData.speakerNames.map(function (name, index) {
        return {
            label: name,
            data: chartData.activitySeries[index],
            backgroundColor: chartData.colors[index],
        };
    });

    new Chart(canvas, {
        type: 'bar',
        data: {
            labels: chartData.activityLabels,
            datasets: datasets,
        },
        options: {
            scales: {
                x: {
                    stacked: true,
                    ticks: {
                        maxTicksLimit: 8,
                    },
                },
                y: {
                    stacked: true,
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Másodperc',
                    },
                },
            },
        },
    });
}
