<!DOCTYPE html>
<html lang="en">
    <head>
        <?php echo $this -> renderModule('head'); ?>
        <link rel="stylesheet" href="/css/bootstrap-slider.css">
        <style>
            #game {
                position: absolute;
                left: 0;
                top: 0;
            }
        </style>
    </head>
    <body style="overflow: hidden;">
        <?php echo $this -> renderModule('nav'); ?>
        <div id="game"></div>

        <?php echo $this -> renderModule('js'); ?>
        <?php echo $this -> renderModule('debug'); ?>
        <script src="/js/three.min.js"></script>
        <script src="/js/stats.min.js"></script>
        <script src="/js/simworld.js"></script>
        <script src="/js/simworld_debug_info.js"></script>
        <script src="/js/simworld_road.js"></script>
        <?php echo $this -> renderModule('camera_controls'); ?>
    </body>
</html>
