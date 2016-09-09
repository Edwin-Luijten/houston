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
    <link rel="stylesheet" href="./assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="./assets/css/animate.css">
    <meta name="problems" data-problems='<?= json_encode($fileList) ?>' id="problems-source">
</head>
<body>
<nav class="navbar navbar-fixed-top navbar-dark bg-inverse">
    <a class="navbar-brand" href="#">Houston</a>
</nav>

<div class="container">
    <div class="row">
        <input type="text" name="search" placeholder="Search..." class="form-control input-lg" data-search>
    </div>

    <div class="row">
        <h3>We've got <span class="problem-counter">0</span> problems
            <small> but a bitch ain't one</small>
        </h3>
    </div>

    <div class="row">
        <div class="pull-left">
            <div class="btn-group">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-history"></i> History <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li><a href="#" class="btn-filter-history" data-filter="10">Today</a></li>
                    <li><a href="#" class="btn-filter-history" data-filter="11">Yesterday</a></li>
                    <li><a href="#" class="btn-filter-history" data-filter="12">This week</a></li>
                    <li><a href="#" class="btn-filter-history" data-filter="13">Older</a></li>
                </ul>
            </div>
        </div>
        <div class="pull-right">
            <div class="btn-group">
                <button type="button" class="btn btn-sm btn-danger btn-filter-level" data-filter="2">Critical</button>
                <button type="button" class="btn btn-sm btn-danger btn-filter-level" data-filter="3">Error</button>
                <button type="button" class="btn btn-sm btn-warning btn-filter-level" data-filter="4">Warning</button>
                <button type="button" class="btn btn-sm btn-primary btn-filter-level" data-filter="5">Info</button>
                <button type="button" class="btn btn-sm btn-primary btn-filter-level" data-filter="6">Debug</button>
                <button type="button" class="btn btn-sm btn-success btn-filter-level" data-filter="1">All</button>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">&nbsp;</div>
    </div>
    <div class="row problems-container" id="problems">
    </div>
</div>

<div id="problem-template" class="hidden">
    <div class="panel filtr-item">
        <div class="panel-heading">

        </div>
        <div class="panel-body">
            <p></p>
            <div class="collapse">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="active tab-traceback"><a href="" data-toggle="tab">Traceback</a></li>
                    <li class="tab-browser-os"><a href="" data-toggle="tab">Browser/OS</a></li>
                    <li class="tab-related"><a href="" data-toggle="tab">Related</a></li>
                    <li class="tab-stack-overflow"><a href="" target="_blank"><i class="fa fa-stack-overflow"></i>&nbsp;
                        </a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active pane-traceback">

                    </div>
                    <div class="tab-pane pane-browser-os">...</div>
                    <div class="tab-pane pane-related">...</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
        integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
        crossorigin="anonymous"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.6.0/highlight.min.js"></script>
<script src="./assets/js/moment.js"></script>
<script src="./assets/js/storage.js"></script>
<script src="./assets/js/countTo.js"></script>
<script src="./assets/js/filter.js"></script>
<script src="./assets/js/houston.js"></script>
<script>
    Houston.init();
</script>
</body>

</html>
