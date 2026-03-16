<?php
//note from skyler: /images/noassets.png is temporary until a new image is made
//ignore this php tag btw it's for this exact comment itself 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>404 - ANORRL</title>
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
                    <img src="/images/noassets.png" alt="Error">
                    <h1>Ahhh Shucks!</h1>
                    <b><?php echo "You tried to access \"" . $_SERVER['REQUEST_URI'] . "\" and that failed."; ?></b>
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
