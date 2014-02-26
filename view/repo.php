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

        <?php echo $this->renderModule('js'); ?>
        <?php echo $this->renderModule('debug'); ?>
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
        </body>
</html>