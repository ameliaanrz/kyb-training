<?php
include_once __DIR__ . '/../partials/_header.php';

?>

<input type="text" id="text" placeholder="text">
    <input type="text" id="loop" placeholder="Loop">
    <button id="btnpost">POST</button>
    <script>
        $(document).ready(function() {
        $('#btnpost').click(function() {
        var text =$('#text').val();
        var loop = $('#loop').val(); 
        for (var i = 0; i < loop; i++) {
            $.ajax({
                url: 'https://api.fonnte.com/send',
                type: 'POST',
                data: {
                    apikey: 'N4w1GNPy16FYPd8CyQnY',
                    mtype: 'text',
                    receiver: '6281387038642',
                    text: text,
                },
                success: function(data) {
                    console.log('Request ' + (i + 1) + ' response:', data);
                },
                error: function(xhr, status, error) {
                    console.error('Request ' + (i + 1) + ' failed:', status, error);
                }
            });
        }
                  
        });

    });
    </script>


        <?php
include_once __DIR__ . '/../partials/_footer.php';
?>