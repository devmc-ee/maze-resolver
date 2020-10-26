<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Maze Solver</title>
</head>
<style>
    .amountSelect select {
        padding: 10px;
    }
    .container, .header{
        max-width: 960px;
        width: 100%;
        margin: 0 auto
    }
    .header{
        text-align: center;
        padding: 20px;
    }
    .lds-dual-ring {
        display: inline-block;
        width: 80px;
        height: 80px;
    }

    .lds-dual-ring:after {
        content: " ";
        display: block;
        width: 64px;
        height: 64px;
        margin: 8px;
        border-radius: 50%;
        border: 6px solid #000;
        border-color: #000 transparent #000 transparent;
        animation: lds-dual-ring 1.2s linear infinite;
    }

    @keyframes lds-dual-ring {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }

    .maze td {
        padding: 10px 20px;
        font-size: 20px;
        font-family: sans-serif;
        font-weight: bold;
        cursor: pointer;
    }

    #selectMazeSizeForm, .container, .mazeSolverResultContainer {
        padding: 10px;
        display: flex;

    }

    .mazeSolverResultContainer {
        flex-direction: column;
        padding: 10px 20px;
    }

    .mazeSolverResultContainer button {
        padding: 20px;
    }

    .amountSelect {
        margin-right: 10px;
    }

    .route-marker {
        background-color: greenyellow;
    }

    #mazeSubmitBtn {
        padding: 10px;
    }
</style>
<body>
<?php
/**
 * @var $context
 */

?>
<div class="header">
    <h1><?php
        echo $context['title']; ?></h1>
    <a href="https://github.com/devmc-ee/maze-resolver.git">GitHub</a>
</div>
<hr>
<div class="container">
    <div class="mazeCreateForm">

        <form method="post" action="ajax.php" id="selectMazeSizeForm">
            <div class="amountSelect">
                <label for="rowsAmount">Rows:</label>
                <select name="rowsAmount" id="rowsAmount">
                    <option value=""></option>

                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>

                </select>
            </div>
            <div class="amountSelect">
                <label for="columnsAmountSelect">Columns:</label>
                <select name="columnsAmount" id="columnsAmount" disabled="disabled">
                    <option value=""></option>

                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>

                </select>
            </div>

            <div class="submitBtn">
                <button id="mazeSubmitBtn" type="submit" disabled>Find Optimal Routes</button>
            </div>
        </form>

        <div class="mazeTableContainer"></div>
    </div>
    <div class="mazeSolverResultContainer">
        Select number of rows and columns, and set walls. Then sumbit.
    </div>
</div>
<hr>
<div>
</div>
<script>
    window.MazeSolver = {
        ajaxurl: "//<?php echo $_SERVER['SERVER_NAME'].dirname($_SERVER['SCRIPT_NAME']); ?>/app/ajax.php"
    }
</script>
<script src="//<?php
echo $_SERVER['SERVER_NAME'].dirname($_SERVER['SCRIPT_NAME']); ?>/assets/js/scripts.js?v=<?= APP_VERSION ?>"></script>

</body>
</html>