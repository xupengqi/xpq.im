<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Aaron | Pengqi Xu</title>
        <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css"
        <!-- Optional theme -->
        <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
        </head>
        <body><nav class="navbar navbar-default" role="navigation">
        <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="#">Aaron | Pengqi Xu</a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav">
        <li>
        <a href="/">Resume</a>
        </li>
        <li>
        <a href="repo.php">GitHub</a>
        </li>
        <li class="active"><a href="widgets.php">Widgets</a></li>
        </ul>
        </ul>
        </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
        </nav>

        <div class="container-fluid">
        <div class="row">
        <div class="col-xs-4 text-center">
        </div>
        <div class="col-xs-4 text-center">
        <div class="dd-progressbar"><div class="dd-progressbar-light"></div></div>
        </div>
        <div class="col-xs-4 text-center">
        <button type="button" class="btn btn-primary" onclick="testProgress();">Test Progress Bar</button>
        </div>
        </div>
        </div>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
        <script>
                function testProgress() {
                    $(".dd-progressbar").css({width: "0%"});
                    $(".dd-progressbar").animate({width: "100%"}, 6000, null);
                };
            $(function() {
                function animateProgress() {
                    $(".dd-progressbar-light").css("left", "-10%");
                    $(".dd-progressbar-light").animate({left: "100%"}, 1000, animateProgress);
                };
                animateProgress();
            });
        </script>
        <style>
        .dd-progressbar {
            width: 100%;
            height: 3px;
            background: #0064D0;
            -webkit-border-radius: 4px;
            -moz-border-radius: 4px;
            border-radius: 4px;
            position: relative;
            overflow: hidden;
        }
        
        .dd-progressbar-light {
            position: absolute;
            top: 0;
            left: -10%;
            height: 100%;
            width: 30px;
            opacity: 60%;
            background: #FFFFFF; /* Old browsers */
            background: -moz-linear-gradient(left, #0064d0 1%, #ffffff 50%, #0064d0 100%);
            /* FF3.6+ */
            background: -webkit-gradient(linear, left top, right top, color-stop(1%, #0064d0),
                color-stop(50%, #ffffff), color-stop(100%, #0064d0));
            /* Chrome,Safari4+ */
            background: -webkit-linear-gradient(left, #0064d0 1%, #ffffff 50%, #0064d0 100%);
            /* Chrome10+,Safari5.1+ */
            background: -o-linear-gradient(left, #0064d0 1%, #ffffff 50%, #0064d0 100%);
            /* Opera 11.10+ */
            background: -ms-linear-gradient(left, #0064d0 1%, #ffffff 50%, #0064d0 100%);
            /* IE10+ */
            background: linear-gradient(to right, #0064d0 1%, #ffffff 50%, #0064d0 100%);
        }
        </style>
        </body>
</html>