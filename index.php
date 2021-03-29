<!DOCTYPE html>
<html>

<head>
    <title>Import Data From Excel or CSV File to Mysql using PHPSpreadsheet</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
</head>

<body>
    <div class="container">
        <br />
        <h3 align="center">Movimientos Bancarios</h3>
        <br />
        <div class="panel panel-default">
            <div class="panel-heading">Todos mis bancos y cajas</div>
            <div class="panel-body">
                <div class="table-responsive">
                    <span id="message"></span>
                    <form method="post" id="import" enctype="multipart/form-data">
                        <table class="table">
                            <tr>
                                <td width="25%" align="right">
                                    <select id="bank">
                                        <option value="bbva">BBVA</option>
                                        <option value="caixa">LaCaixa</option>
                                        <option value="santander">Santander</option>
                                    </select>
                                </td>
                                <td width="50%"><input type="file" name="import_excel" /></td>
                                <td width="25%"><input type="submit" id="import-btn" class="btn btn-primary"
                                        value="Import" /></td>
                            </tr>
                        </table>
                    </form>
                    <form method="get" id="movimientos" enctype="multipart/form-data">
                        <table class="table">
                            <tr>
                                <td width="25%"><input type="submit" id="cargar-btn" class="btn btn-primary"
                                        value="Movimientos" /></td>
                                <td width="25%">Saldo: </span ><span id="saldo">0 </span><span> €</span></td>
                            </tr>
                        </table>
                    </form>
                    <span id="datos"></span>
                    <br />
                </div>
            </div>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
</body>

</html>
<script>
$(document).ready(function() {
    $('#movimientos').on('submit', function(event) {
        event.preventDefault();
        $.ajax({
            url: "carga.php",
            method: "GET",
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function() {
                $('#cargar-btn').attr('disabled', 'disabled');
                $('#cargar-btn').val('Cargando...');
            },
            success: function(data) {
                console.log(data);
                $('#datos').html(data);
                $('#cargar-btn').attr('disabled', false);
                $('#cargar-btn').val('Movimientos');
            }
        })
    });
    $('#import').on('submit', function(event) {
        event.preventDefault();
        var banco = $('#bank').val();
        $.ajax({
            url: "import.php?b=" + banco,
            method: "POST",
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function() {
                $('#import-btn').attr('disabled', 'disabled');
                $('#import-btn').val('Importing...');
            },
            success: function(data) {
                $('#message').html(data);
                $('#import')[0].reset();
                $('#import-btn').attr('disabled', false);
                $('#import-btn').val('Import');
            }
        })
    });
});
</script>