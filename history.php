
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event History</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        .container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            padding: 20px;
        }
        .event-card {
            padding: 20px;
            border-radius: 8px;
            background-color: #f8f9fa; /* Soft background color */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            color: #333;
            text-align: center;
        }
        .event-card .card-title {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .event-card .card-text {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Example Event Cards -->
    <div class="event-card">
        <div class="card-title">${data['EVENT_NAME_1']}</div>
        <div class="card-content">
            <p class="card-text">Status: ${data['STATUS_1']}</p>
            <button class="btn btn-primary" data-toggle="modal" data-target="#eventModal" onclick="showEventDetails('${data['EVENT_ID_1']}')">View Details</button>
        </div>
    </div>
    <div class="event-card">
        <div class="card-title">${data['EVENT_NAME_2']}</div>
        <div class="card-content">
            <p class="card-text">Status: ${data['STATUS_2']}</p>
            <button class="btn btn-primary" data-toggle="modal" data-target="#eventModal" onclick="showEventDetails('${data['EVENT_ID_2']}')">View Details</button>
        </div>
    </div>
    <div class="event-card">
        <div class="card-title">${data['EVENT_NAME_3']}</div>
        <div class="card-content">
            <p class="card-text">Status: ${data['STATUS_3']}</p>
            <button class="btn btn-primary" data-toggle="modal" data-target="#eventModal" onclick="showEventDetails('${data['EVENT_ID_3']}')">View Details</button>
        </div>
    </div>
    <div class="event-card">
        <div class="card-title">${data['EVENT_NAME_4']}</div>
        <div class="card-content">
            <p class="card-text">Status: ${data['STATUS_4']}</p>
            <button class="btn btn-primary" data-toggle="modal" data-target="#eventModal" onclick="showEventDetails('${data['EVENT_ID_4']}')">View Details</button>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-labelledby="eventModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalLabel">Event Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Table to show event details -->
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Column 1</th>
                            <th>Column 2</th>
                            <th>Column 3</th>
                        </tr>
                    </thead>
                    <tbody id="eventDetailsBody">
                        <!-- Event details will be dynamically inserted here -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script>
    function showEventDetails(eventId) {
        // Example: Fetch event details based on eventId
        // Here you would make an AJAX call to get the event details from the server
        // For now, we'll just use some dummy data

        const eventDetails = [
            { column1: 'Data 1', column2: 'Data 2', column3: 'Data 3' },
            { column1: 'Data A', column2: 'Data B', column3: 'Data C' }
        ];

        const eventDetailsBody = document.getElementById('eventDetailsBody');
        eventDetailsBody.innerHTML = '';

        eventDetails.forEach(detail => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${detail.column1}</td>
                <td>${detail.column2}</td>
                <td>${detail.column3}</td>
            `;
            eventDetailsBody.appendChild(row);
        });
    }
</script>

</body>
</html>
