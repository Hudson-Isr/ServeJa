<?php
//include('PHP/Start.php');
include "../includes/boostrap.php";
include "navbar-client.php";
include "../PHP/Start.php";
$conn = new mysqli('localhost', 'root', '', 'serveja');

session_start();
$nome = $_SESSION['nome'];

if (isset($_POST['entrar_mesa'])) {
    try {
        $codigo = $_POST["cod_mesa"];
        $status = "Ocupado";

        #check email before insert
        $sql = "SELECT codigo FROM mesa where codigo='$codigo'";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 0) {
            header("location: ?error=mesa&pedido=false");
            exit;
        }

        $stat = "SELECT status FROM mesa where codigo='$codigo'";

        $result = $conn->query($stat);
        while ($row = $result->fetch_assoc()) {
            $statu = $row["status"];
        }

        if ($statu == 'Ocupado') {
            header("location: ?erro=ocupado&pedido=false");
            exit;
        } else {
            $mesa = "SELECT * FROM mesa WHERE codigo='$codigo'";
            $result = $conn->query($mesa);
            while ($row = $result->fetch_assoc()) {
                $id_mesa = $row["id"];
            }
            $query = "UPDATE mesa SET status = '$status', nome_cliente = '$nome' WHERE id=$id_mesa";
            $query_run = mysqli_query($conn, $query);
            exit(header("location: client-index-mesa.php?code=$codigo&pedido=false"));
        }
    } catch (mysqli_sql_exception $e) {
        exit($e->getMessage());
    }

    //Verifica se a query executou corretamente, caso não irá exibir o erro na tela.
    if (!$query_run) {
        $error = "Invalid query: " . $conn->error;
    }
}

?>

<?php
if (isset($_GET['error']) == "mesa") {
    $erro = "Número de mesa não encontrado!";
    echo "
    <div class='container position-absolute top-1 start-50 w-25 alert alert-warning alert-dismissible fade show' role='alert'>
        <strong>Error: </strong> $erro
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
    </div>
    ";
}
if (isset($_GET['erro']) == "ocupado") {
    $erro = "Esta mesa está ocupada no momento!";
    echo "
    <div class='container position-absolute top-1 start-50 w-25 alert alert-warning alert-dismissible fade show' role='alert'>
        <strong>Error: </strong> $erro
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
    </div>
    ";
}
?>
<script src="../node_modules/html5-qrcode/html5-qrcode.min.js"></script>
<div class='modal fade' id='verModal' tabindex='-1' aria-labelledby='exampleModalLabel' aria-hidden='true'>
    <div class='modal-dialog modal-dialog-scrollable'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h5 class='modal-title' id='exampleModalLabel'><b>Insira o código da mesa:</b></h5>
                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
            </div>
            <div class='modal-body col-md-5'>
                <form method='POST'>
                    <label for="cod_mesa" class="form-label">Código da mesa:</label>
                    <input required type="text" class="form-control input-sm" name="cod_mesa">
            </div>
            <div class='modal-footer'>
                <button type='submit' name='entrar_mesa' class='btn btn-primary' data-bs-dismiss='modal'>Entrar</button>
                <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Fechar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class='modal qr fade' id='scanQR' tabindex='-1' aria-labelledby='exampleModalLabel' aria-hidden='true'>
    <div class='modal-dialog modal-dialog-scrollable'>
        <div class='modal-content'>
            <div class='modal-header'>
                <h5 class='modal-title' id='exampleModalLabel'><b>Escaneie o QR Code da mesa:</b></h5>
                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
            </div>
            <div class='modal-body'>
                    <div id="reader"></div>
                    <div id="result"></div>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Fechar</button>
            </div>
        </div>
    </div>
</div>

<main role="main" class="mt-3">
    <section class="jumbotron text-center mt-5">
        <div class="container">
            <h2 class="jumbotron-heading ">Seja bem-vindo, <?php echo $nome; ?>.</h2>
            <h3>ao<h3>
                    <h1 class="logo text-danger">ServeJá</h1>
                    <p class="lead text-dark">Mude sua forma de fazer o seu pedido. Escaneie o código e aproveite!</p>
                    <p class="t">
                        <button data-bs-toggle='modal' data-bs-target='#scanQR' type='button' class="btn btn-danger my-2 bg-danger">Escaneie o código QR <i class="bi bi-camera"></i></button>
                    <p>ou</p>
                    <a href="#" class="btn btn-secondary my-2" data-bs-toggle='modal' data-bs-target='#verModal' type='button'>Digite o código da mesa <i class="bi bi-keyboard"></i></a>
                    </p>
        </div>
        <img src="/projeto-serveja/images/Hamburger-rafiki.png" alt="">
    </section>
    <script>
    const scanner = new Html5QrcodeScanner('reader', {
        // Scanner will be initialized in DOM inside element with id of 'reader'
        qrbox: {
            width: 250,
            height: 250,
        }, // Sets dimensions of scanning box (set relative to reader element width)
        fps: 20, // Frames per second to attempt a scan
    });


    scanner.render(success, error);
    // Starts scanner

    function success(result) {

        document.getElementById('result').innerHTML = `
        <h2>Código escaneado com sucesso!</h2>
        <p><a href="${result}">${result}</a></p>
        `;
        // Prints result as a link inside result element

        scanner.clear();
        // Clears scanning instance

        document.getElementById('reader').remove();
        // Removes reader element from DOM since no longer needed

    }

    function error(err) {
        console.error(err);
        // Prints any errors to the console
    }
</script>
    <style>
        #reader {
            width: 462px;
        }

        img {
            width: 13%;
        }

        .start-50 {
            left: 35% !important;
        }

        .w-25 {
            width: 30% !important;
        }
    </style>
</main>