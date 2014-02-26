<!DOCTYPE html>
<html lang="en">
    <head>
        <?php echo $this->renderModule('head'); ?>
    </head>
    <body>
        <?php echo $this->renderModule('nav'); ?>

        <div class="container-fluid">
        <div class="row">
        <div class="col-xs-4 text-center">
        </div>
        <div class="col-xs-4 text-center" id="repos">
        </div>
        <div class="col-xs-4 text-center">
        </div>
        </div>
        </div>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
        <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
        <script>
            $(function() {
                $.getJSON("https://api.github.com/users/xupengqi/repos", function(data) {
                    var items = [];
                    $.each(data, function(key, val) {
                        items.push("<a class='list-group-item' href='" + val.html_url + "'>" + val.name + "</a>");
                    });

                    $("<ul/>", {
                        "class" : "list-group",
                        html : items.join("")
                    }).appendTo("#repos");
                });
            });
        </script>
        <?php echo $this->renderModule('debug'); ?>
        </body>
</html>