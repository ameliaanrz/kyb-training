<!-- include chartjs and bootstrap -->
<?php include('partials/_header.php'); ?>
<script type="text/javascript" src="node_modules/jquery/dist/jquery.min.js"></script>
<script type="text/javascript" src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div style="height: 400px; width: 800px;">
    <canvas id="myChart"></canvas>
</div>

<script type="text/javascript">
    const timeline_month = 9;
    const timeline_year = new Date().getFullYear();
    const days_in_month = new Date(timeline_year, timeline_month, 0).getDate();
    const first_day_in_month = new Date(timeline_year, timeline_month - 1, 1);
    const last_day_in_month = new Date(timeline_year, timeline_month, 0);
    
    var events_data = {time_ranges: [], labels: [], datas : []};

    $.get(`includes/events.inc.php?month=${timeline_month}`).done(function(a, b, xhr) {
        if (xhr.responseJSON) {
          // get responses
          const events = xhr.responseJSON['events'];
          console.log(events);
          if (events) {
            events.forEach(event => {
                const start_date = ((new Date(event['START_DATE']) < first_day_in_month) ? first_day_in_month : new Date(event['START_DATE']));
                const end_date = ((new Date(event['END_DATE']) > last_day_in_month) ? last_day_in_month : new Date(event['END_DATE']));
                events_data.time_ranges.push([
                    (new Date(start_date)).getDate(),
                    (new Date(end_date)).getDate()
                ]);
                events_data.labels.push(event['TRAINING']);
                events_data.datas.push({
                    event_id: event['EVT_ID'],
                    training: event['TRAINING'],
                    training_id: event['T_ID'],
                    organizer: event['ORGANIZER'],
                    real_start: event['START_DATE'],
                    real_start_t: event['START_TIME'],
                    real_end: event['END_DATE'],
                    real_end_t: event['END_TIME']
                });
            });
          }
        }
        const data = {
        labels: events_data.labels,
        datasets: [
            {
            label: 'Dataset 1',
            data: events_data.time_ranges,
            backgroundColor: events_data.labels.map((currElement, index) => { return "hsl(" + (index * (360 / events_data.labels.length)) + ",70%,70%)"; })
            }
        ]
        };
        const config = {
            type: 'bar',
            data: data,
            options: {
                indexAxis: "y",
                responsive: true,
                plugins: {
                    legend: {
                        display: false,
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Chart.js Floating Bar Chart'
                    },
                    tooltip: {
                        callbacks: {
                            beforeFooter: function(t, d) { return `Start: ${events_data.datas[t[0].datasetIndex].real_start_t} ${events_data.datas[t[0].datasetIndex].real_start}`; },
                            footer: function(t, d) { return `End: ${events_data.datas[t[0].datasetIndex].real_end_t} ${events_data.datas[t[0].datasetIndex].real_end}`; },
                            label: function(t, d) { return ``; },
                            beforeBody: function(t, d) { return `Organizer: ${events_data.datas[t[0].datasetIndex].organizer}`; }
                        }
                    }
                },
                scales: {
                    x: {
                        min: 1,
                        max: days_in_month,
                        ticks: {
                            stepSize: 1,
                            callback: function(val, index) { return val; },
                            color: 'red',
                        }
                    }
                }
            }
        };

        var myChart = new Chart(
            document.getElementById('myChart'),
            config
        );
        
        const canvas = document.getElementById('myChart');    

        canvas.onclick = (evt) => {
        const res = myChart.getElementsAtEventForMode(
            evt,
            'nearest',
            { intersect: true },
            true
        );
        // If didn't click on a bar, `res` will be an empty array
        if (res.length === 0) {
            return;
        }
        // Alerts "You clicked on A" if you click the "A" chart
        alert('You clicked on ' + myChart.data.labels[res[0].index]);
        };
    });

    console.log(events_data);
</script>
<?php include('partials/_footer.php'); ?>