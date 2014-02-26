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
                    simworld
                </div>

            </div>
        </div>

        <?php echo $this->renderModule('js'); ?>
        <?php echo $this->renderModule('debug'); ?>
        </body>
</html>