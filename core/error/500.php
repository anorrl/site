<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>500 - ANORRL</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="stylesheet" href="/css/new/main.css">
    <link rel="stylesheet" href="/css/new/error.css">
    <script src="/js/core/jquery.js"></script>
    <script src="/js/main.js?t=1771413807"></script>
</head>
<body>
    <div id="Container">
        <?php include $_SERVER['DOCUMENT_ROOT'].'/core/ui/header.php'; ?>
        <div id="Body">
            <div id="BodyContainer">
                <div id="ErrorContainer">
                    <img src="/images/icons/img-alert-transparent.png" alt="Error">
                    <h1>Uh oh!</h1>
                    <b><?php echo "A fucky wucky occurred! (Do NOT spam refresh). Tell grace to FIX IT!"; ?></b>
                    <div class="buttons">
                        <button id="BackSubmit" onclick="window.history.back();">Back</button>
                        <form action="/my/home" method="get">
                            <input id="HomeSubmit" type="submit" value="Home">
                        </form>
                    </div>
                </div>
            </div>
            <?php include $_SERVER['DOCUMENT_ROOT'].'/core/ui/footer.php'; ?>
        </div>
    </div>
</body>
</html>
