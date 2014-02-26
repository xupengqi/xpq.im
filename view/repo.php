<!DOCTYPE html>
<html lang="en">
    <head>
        <?php echo $this->renderModule('head'); ?>
    </head>
    <body>
        <?php echo $this->renderModule('nav'); ?>

        <div class="container-fluid">
        <div class="row">
        <div class="col-xs-12 text-center">
        <div id="repos" class="list-group"></div>
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
                        items.push("<a class='list-group-item' id='" + key + "'>" + val.name + "</a>");
                    });

                    $("<ul/>", {
                        "class" : "my-new-list",
                        html : items.join("")
                    }).appendTo("#repos");
                });
            });
        </script>
        </body>
</html>