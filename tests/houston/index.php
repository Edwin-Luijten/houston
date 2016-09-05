<?php

$fileList = [];

foreach (glob('./*.problem') as $file) {
    $fileList[] = str_replace('./', '', $file);
}

?>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Houston we have a problem</title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link href="./assets/css/paper.css" rel="stylesheet">
    <link href="./assets/css/houston.css" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/hightlight-tomorrow-night.css">
    <meta name="problems" data-problems='<?= json_encode($fileList) ?>' id="problems-source">
</head>
<body>
<nav class="navbar navbar-fixed-top navbar-dark bg-inverse">
    <a class="navbar-brand" href="#">Houston</a>
    <ul class="nav navbar-nav">
        <li class="nav-item active">
            <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">About</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#">Contact</a>
        </li>
    </ul>
</nav>

<div class="container">
    <div class="row">

            <div class="pull-right">
                <div class="btn-group">
                    <button type="button" class="btn btn-warning btn-filter-level" data-value="debug">Debug</button>
                    <button type="button" class="btn btn-danger btn-filter-level" data-value="error">Error</button>
                    <button type="button" class="btn btn-success btn-filter-level" data-value="all">All</button>
                </div>
            </div>

    </div>
    <div class="row"><div class="col-md-12">&nbsp;</div></div>
    <div class="row" id="problems">
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
        integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
        crossorigin="anonymous"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.6.0/highlight.min.js"></script>
<script src="./assets/js/houston.js"></script>
<script>
    Houston.problemsResource = $('#problems-source');
    Houston.init();
</script>
</body>

</html>
