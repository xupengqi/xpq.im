<!DOCTYPE html>
<html lang="en">
    <head>
        <?php echo $this -> renderModule('head'); ?>
        <link rel="stylesheet" href="/css/bootstrap-slider.css">
        <style>
            #camerax-wrapper {
                top: 220px;
                left: 30px;
                position: absolute;
                width: 100px;
            }

            #cameraz-wrapper {
                top: 10px;
                left: 100px;
                position: absolute;
                height: 100px;
            }
        </style>
    </head>
    <body>
        <?php echo $this -> renderModule('nav'); ?>

        <div class="container-fluid">
            <div class="row" style="position: relative;">
                <div class="col-xs-12 text-center" id="game"></div>
                <div id="camerax-wrapper">
                    <div id="camerax" data-slider-min="0"
                    data-slider-max="200" data-slider-step="1"
                    data-slider-value="50"
                    data-slider-orientation="horizontal"></div>
                </div>
                <div id="cameraz-wrapper">
                    <div id="cameraz" data-slider-min="0"
                    data-slider-max="200" data-slider-step="1"
                    data-slider-value="50"
                    data-slider-orientation="vertical"></div>
                </div>
            </div>
        </div>

        <?php echo $this -> renderModule('js'); ?>
        <?php echo $this -> renderModule('debug'); ?>
        <script src="/js/three.min.js"></script>

        <script src="/js/stats.min.js"></script>
        <script src="/js/bootstrap-slider.js"></script>

        <script>
            $('#camerax').slider().on('slide', function(ev) {
                camerax = ev.value;
            });
            $('#cameraz').slider().on('slide', function(ev) {
                cameraz = ev.value;
            });

            var stats;
            var camera, scene, renderer;
            var camerax = 50, cameraz = 50;
            var gridSize = 500, step = 20;
            var windowHalfX = window.innerWidth / 2;
            var windowHalfY = window.innerHeight / 2;
            var lookAt = new THREE.Vector3(0, 0, 0);

            init();
            animate();

            function init() {
                // Camera & Scene
                camera = new THREE.OrthographicCamera(window.innerWidth / -2, window.innerWidth / 2, window.innerHeight / 2, window.innerHeight / -2, -500, 1000);
                scene = new THREE.Scene();

                // Grid
                var geometry = new THREE.Geometry();

                for (var i = -gridSize; i <= gridSize; i += step) {
                    geometry.vertices.push(new THREE.Vector3(-gridSize, 0, i));
                    geometry.vertices.push(new THREE.Vector3(gridSize, 0, i));
                    geometry.vertices.push(new THREE.Vector3(i, 0, -gridSize));
                    geometry.vertices.push(new THREE.Vector3(i, 0, gridSize));
                }

                var material = new THREE.LineBasicMaterial({
                    color : 0x000000,
                    opacity : 0.2
                });
                var line = new THREE.Line(geometry, material);
                line.type = THREE.LinePieces;
                scene.add(line);

                // Cubes
                var geometry = new THREE.BoxGeometry(step, step, step);
                var material = new THREE.MeshLambertMaterial({
                    color : 0xffffff,
                    shading : THREE.FlatShading,
                    overdraw : 0.5
                });

                var cubes = {};
                for (var i = 0; i < 200; i++) {
                    var cube = new THREE.Mesh(geometry, material);
                    cube.scale.y = Math.floor(Math.random() * 2 + 1);
                    cube.position.x = Math.floor((Math.random() * gridSize - gridSize / 2 ) / step) * step + step / 2;
                    cube.position.y = (cube.scale.y * step ) / 2;
                    cube.position.z = Math.floor((Math.random() * gridSize - gridSize / 2 ) / step) * step + step / 2;

                    if (cubes[cube.position.x] == undefined || cubes[cube.position.x][cube.position.z] == undefined) {
                        if (cubes[cube.position.x] == undefined) {
                            cubes[cube.position.x] = {};
                        }
                        cubes[cube.position.x][cube.position.z] = true;
                        scene.add(cube);
                    } else {
                        console.log("cube exists");
                    }
                }
                console.log(cubes);

                // Lights
                var ambientLight = new THREE.AmbientLight(Math.random() * 0x10);
                scene.add(ambientLight);

                var directionalLight = new THREE.DirectionalLight(Math.random() * 0xffffff);
                directionalLight.position.x = Math.random() - 0.5;
                directionalLight.position.y = Math.random() - 0.5;
                directionalLight.position.z = Math.random() - 0.5;
                directionalLight.position.normalize();
                scene.add(directionalLight);

                var directionalLight = new THREE.DirectionalLight(Math.random() * 0xffffff);
                directionalLight.position.x = Math.random() - 0.5;
                directionalLight.position.y = Math.random() - 0.5;
                directionalLight.position.z = Math.random() - 0.5;
                directionalLight.position.normalize();
                scene.add(directionalLight);

                renderer = new THREE.CanvasRenderer();
                renderer.setClearColor(0xf0f0f0);
                renderer.setSize(window.innerWidth - 100, window.innerHeight - 100);

                $("#game").append(renderer.domElement);

                stats = new Stats();
                stats.domElement.style.position = 'absolute';
                stats.domElement.style.top = '0px';
                $("#game").append(stats.domElement);

                window.addEventListener('resize', onWindowResize, false);
            }

            function onWindowResize() {
                camera.left = window.innerWidth / -2;
                camera.right = window.innerWidth / 2;
                camera.top = window.innerHeight / 2;
                camera.bottom = window.innerHeight / -2;
                camera.updateProjectionMatrix();
                //TODO: WHAT IS THIS
                renderer.setSize(window.innerWidth - 100, window.innerHeight - 100);
            }

            function animate() {
                requestAnimationFrame(animate);
                //TODO: WHAT IS THIS
                camera.position.x = camerax;
                camera.position.z = cameraz;
                camera.position.y = 100;
                camera.lookAt(lookAt);
                //TODO: on mouse drag move this
                renderer.render(scene, camera);
                stats.update();
            }


            document.addEventListener('mousedown', onDocumentMouseDown, false);

            function onDocumentMouseDown(event) {
                event.preventDefault();
                document.addEventListener('mousemove', onDocumentMouseMove, false);
                document.addEventListener('mouseup', onDocumentMouseUp, false);
                document.addEventListener('mouseout', onDocumentMouseOut, false);
                mouseXOnMouseDown = event.clientX - windowHalfX;
                mouseYOnMouseDown = event.clientY - windowHalfY;

            }

            function onDocumentMouseMove(event) {
                mouseX = event.clientX - windowHalfX;
                mouseY = event.clientY - windowHalfY;
                lookAt.z += (mouseX - mouseXOnMouseDown) * .03;
                lookAt.x -= (mouseX - mouseXOnMouseDown) * .03;
                cameraz += (mouseX - mouseXOnMouseDown) * .03;
                camerax -= (mouseX - mouseXOnMouseDown) * .03;

                lookAt.z -= (mouseY - mouseYOnMouseDown) * .03;
                lookAt.x -= (mouseY - mouseYOnMouseDown) * .03;
                cameraz -= (mouseY - mouseYOnMouseDown) * .03;
                camerax -= (mouseY - mouseYOnMouseDown) * .03;
            }

            function onDocumentMouseUp(event) {
                document.removeEventListener('mousemove', onDocumentMouseMove, false);
                document.removeEventListener('mouseup', onDocumentMouseUp, false);
                document.removeEventListener('mouseout', onDocumentMouseOut, false);
            }

            function onDocumentMouseOut(event) {
                document.removeEventListener('mousemove', onDocumentMouseMove, false);
                document.removeEventListener('mouseup', onDocumentMouseUp, false);
                document.removeEventListener('mouseout', onDocumentMouseOut, false);
            }

            var debugaxis = function(axisLength) {
                //Shorten the vertex function
                function v(x, y, z) {
                    return new THREE.Vector3(x, y, z);
                }

                //Create axis (point1, point2, colour)
                function createAxis(p1, p2, color) {
                    var line, lineGeometry = new THREE.Geometry(), lineMat = new THREE.LineBasicMaterial({
                        color : color,
                        lineWidth : 1
                    });
                    lineGeometry.vertices.push(p1, p2);
                    line = new THREE.Line(lineGeometry, lineMat);
                    scene.add(line);
                }

                createAxis(v(-axisLength, 0, 0), v(axisLength, 0, 0), 0xFF0000);
                createAxis(v(0, -axisLength, 0), v(0, axisLength, 0), 0x00FF00);
                createAxis(v(0, 0, -axisLength), v(0, 0, axisLength), 0x0000FF);
            };

            //To use enter the axis length
            debugaxis(10000);
        </script>
    </body>
</html>
