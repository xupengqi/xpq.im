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
                    <iframe style="min-width: 860px; min-height: 1300px; border: none;" src="https://docs.google.com/document/d/1g5qcuRvHUylF-87-TtzEKurTwguRtzdcK49tZ6UlAKY/pub?embedded=true"></iframe>
                </div>

            </div>
        </div>

        <?php echo $this->renderModule('js'); ?>
        <?php echo $this->renderModule('debug'); ?>
        </body>
</html>