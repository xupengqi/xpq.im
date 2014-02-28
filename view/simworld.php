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
    <body style="overflow: hidden;">
        <?php echo $this -> renderModule('nav'); ?>
        <div id="game"></div>
        <div style="position: absolute; top: 0; left: 200px;"><a href="#" id="lock">lock</a></div>
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

        <?php echo $this -> renderModule('js'); ?>
        <?php echo $this -> renderModule('debug'); ?>
        <script src="/js/three.min.js"></script>
        <script src="/js/stats.min.js"></script>
        <script src="/js/PointerLockControls.js"></script>
        <script src="/js/bootstrap-slider.js"></script>

        <script>
            $('#camerax').slider().on('slide', function(ev) {
                camerax = ev.value;
            });
            $('#cameraz').slider().on('slide', function(ev) {
                cameraz = ev.value;
            });

            var stats;
            var camera, scene, renderer, controls,time = Date.now();

            var plane, rollOverMesh;

            var mouse2D, projector, raycaster, isMouseDown = false, isShiftDown = false, isDragging = false;

            var voxelPosition = new THREE.Vector3(), tmpVec = new THREE.Vector3(), normalMatrix = new THREE.Matrix3();
            var cubeGeo, cubeMaterial;

            var camerax = 50, cameraz = 50, cameray = 100;
            var gridSize = 500, step = 20;
            var windowHalfX = window.innerWidth / 2;
            var windowHalfY = window.innerHeight / 2;
            var lookAt = new THREE.Vector3(0, 0, 0);

            var objects = [];

            init();
            animate();

            function init() {
                // Camera & Scene
                camera = new THREE.OrthographicCamera(window.innerWidth / -2, window.innerWidth / 2, window.innerHeight / 2, window.innerHeight / -2, -500, 1000);

                //camera = new THREE.PerspectiveCamera( 75, window.innerWidth / window.innerHeight, 1, 10000 );
                //camera.position.y = 800;

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
                cubeGeo = new THREE.BoxGeometry(step, step, step);
                /*var material = new THREE.MeshLambertMaterial({
                 color : 0xffffff,
                 shading : THREE.FlatShading,
                 overdraw : 0.5
                 });*/
                cubeMaterial = new THREE.MeshLambertMaterial({
                    color : 0xfeb74c,
                    ambient : 0x00ff80,
                    shading : THREE.FlatShading,
                    map : THREE.ImageUtils.loadTexture("/img/square-outline-textured.png")
                });
                cubeMaterial.ambient = cubeMaterial.color;

                var cubes = {};
                for (var i = 0; i < 20; i++) {
                    var cube = new THREE.Mesh(cubeGeo, cubeMaterial);
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
                        objects.push(cube);
                    }
                }

                // ground
                var initColor = new THREE.Color(0x497f13);
                var initTexture = THREE.ImageUtils.generateDataTexture(1, 1, initColor);
                var groundMaterial = new THREE.MeshPhongMaterial({
                    color : 0xffffff,
                    specular : 0x111111,
                    map : initTexture
                });
                var groundTexture = THREE.ImageUtils.loadTexture("/img/grasslight-big.jpg", undefined, function() {
                    groundMaterial.map = groundTexture
                });
                groundTexture.wrapS = groundTexture.wrapT = THREE.RepeatWrapping;
                groundTexture.repeat.set(25, 25);
                groundTexture.anisotropy = 16;

                plane = new THREE.Mesh(new THREE.PlaneGeometry(1000, 1000), groundMaterial);
                plane.rotation.x = -Math.PI / 2;
                plane.visible = true;
                scene.add(plane);
                objects.push(plane);

                // roll-over helpers
                var rollOverGeo = new THREE.BoxGeometry(step, step, step);
                var rollOverMaterial = new THREE.MeshBasicMaterial({
                    color : 0xff0000,
                    opacity : 0.5,
                    transparent : true
                });
                rollOverMesh = new THREE.Mesh(rollOverGeo, rollOverMaterial);
                scene.add(rollOverMesh);

                // Lights
                var light, materials;
                scene.add(new THREE.AmbientLight(0x666666));
                light = new THREE.DirectionalLight(0xdfebff, 1.75);
                light.position.set(50, 200, 100);
                light.position.multiplyScalar(1.3);
                light.castShadow = true;
                //light.shadowCameraVisible = true;
                light.shadowMapWidth = 2048;
                light.shadowMapHeight = 2048;
                var d = 300;
                light.shadowCameraLeft = -d;
                light.shadowCameraRight = d;
                light.shadowCameraTop = d;
                light.shadowCameraBottom = -d;
                light.shadowCameraFar = 1000;
                light.shadowDarkness = 0.5;
                scene.add(light);

                light = new THREE.DirectionalLight(0x3dff0c, 0.35);
                light.position.set(0, -1, 0);
                scene.add(light);
                
                // PointerLockControl
                controls = new THREE.PointerLockControls( camera );
                scene.add( controls.getObject() );

                /*renderer = new THREE.CanvasRenderer();
                 renderer.setClearColor(0xf0f0f0);
                 renderer.setSize(window.innerWidth - 100, window.innerHeight - 100);*/

                renderer = new THREE.WebGLRenderer({
                    antialias : true
                });
                renderer.setClearColor(0xf0f0f0);
                renderer.setSize(window.innerWidth, window.innerHeight);

                $("#game").append(renderer.domElement);

                stats = new Stats();
                stats.domElement.style.position = 'absolute';
                stats.domElement.style.top = '0px';
                $("#game").append(stats.domElement);

                mouse2D = new THREE.Vector3(0, 10000, 0.5);
                projector = new THREE.Projector();

                window.addEventListener('resize', onWindowResize, false);
                
                $("#lock").click(function() {
                    // Ask the browser to lock the pointer
                    document.body.requestPointerLock = document.body.requestPointerLock || document.body.mozRequestPointerLock || document.body.webkitRequestPointerLock;
                    document.body.requestPointerLock();
                });
            }

            function onWindowResize() {
                camera.left = window.innerWidth / -2;
                camera.right = window.innerWidth / 2;
                camera.top = window.innerHeight / 2;
                camera.bottom = window.innerHeight / -2;
                camera.updateProjectionMatrix();
                //TODO: WHAT IS THIS
                renderer.setSize(window.innerWidth, window.innerHeight);
            }

            function animate() {
                requestAnimationFrame(animate);

                raycaster = projector.pickingRay(mouse2D.clone(), camera);
                var intersects = raycaster.intersectObjects(objects);
                if (intersects.length > 0) {
                    //console.log(intersects.length);
                    setVoxelPosition(intersects[0]);
                    rollOverMesh.position = voxelPosition;
                }

                camera.position.x = camerax;
                camera.position.z = cameraz;
                camera.position.y = cameray;
                camera.lookAt(lookAt);

                controls.update( Date.now() - time );
                renderer.render(scene, camera);
                stats.update();
                time = Date.now();
            }

                var pointerLockError = function ( event ) {
                    console.log(event);

                };

            document.addEventListener('mousedown', onDocumentMouseDown, false);
            document.addEventListener('mousemove', onDocumentMouseMove, false);
            document.addEventListener('keydown', onDocumentKeyDown, false);
            document.addEventListener('keyup', onDocumentKeyUp, false);
                document.addEventListener( 'pointerlockchange', pointerLockChange, false );
                document.addEventListener( 'mozpointerlockchange', pointerLockChange, false );
                document.addEventListener( 'webkitpointerlockchange', pointerLockChange, false );
                document.addEventListener( 'pointerlockerror', pointerLockError, false );
                document.addEventListener( 'mozpointerlockerror', pointerLockError, false );
                document.addEventListener( 'webkitpointerlockerror', pointerLockError, false );


            function onDocumentMouseDown(event) {
                event.preventDefault();
                document.addEventListener('mouseup', onDocumentMouseUp, false);
                document.addEventListener('mouseout', onDocumentMouseOut, false);
                mouseXOnMouseDown = event.clientX - windowHalfX;
                mouseYOnMouseDown = event.clientY - windowHalfY;
                isMouseDown = true;
            }

            function onDocumentMouseMove(event) {
                if (isMouseDown) {
                    /*mouseX = event.clientX - windowHalfX;
                    mouseY = event.clientY - windowHalfY;
                    lookAt.z += (mouseX - mouseXOnMouseDown) * .03;
                    lookAt.x -= (mouseX - mouseXOnMouseDown) * .03;
                    cameraz += (mouseX - mouseXOnMouseDown) * .03;
                    camerax -= (mouseX - mouseXOnMouseDown) * .03;

                    lookAt.z -= (mouseY - mouseYOnMouseDown) * .03;
                    lookAt.x -= (mouseY - mouseYOnMouseDown) * .03;
                    cameraz -= (mouseY - mouseYOnMouseDown) * .03;
                    camerax -= (mouseY - mouseYOnMouseDown) * .03;*/
                   
                    isDragging = true;
                    rollOverMesh.visible = false;

                }

                mouse2D.x = (event.clientX / window.innerWidth ) * 2 - 1;
                mouse2D.y = -(event.clientY / window.innerHeight ) * 2 + 1;

            }

            function onDocumentMouseUp(event) {
                document.removeEventListener('mouseup', onDocumentMouseUp, false);
                document.removeEventListener('mouseout', onDocumentMouseOut, false);

                if (!isDragging) {
                    var intersects = raycaster.intersectObjects(objects);
                    if (intersects.length > 0) {
                        var intersector = intersects[0];
                        // delete cube
                        if (isShiftDown) {
                            if (intersector.object != plane) {
                                scene.remove(intersector.object);
                                objects.splice(objects.indexOf(intersector.object), 1);
                            }
                        } else {
                            setVoxelPosition(intersector);
                            //var voxel = new THREE.Mesh(cubeGeo, cubeMaterial);
                            var voxel = getDirt();
                            console.log(voxelPosition);
                            voxel.position.copy(voxelPosition);
                            voxel.position.y = 1;
                            voxel.matrixAutoUpdate = false;
                            voxel.updateMatrix();
                            scene.add(voxel);
                            objects.push(voxel);
                        }
                    }
                }

                isMouseDown = false;
                isDragging = false;
                rollOverMesh.visible = true;
            }
            
            function getDirt() {
                
                var initColor = new THREE.Color(0x497f13);
                var initTexture = THREE.ImageUtils.generateDataTexture(1, 1, initColor);
                var groundMaterial = new THREE.MeshPhongMaterial({
                    color : 0xffffff,
                    specular : 0x111111,
                    map : initTexture
                });
                var groundTexture = THREE.ImageUtils.loadTexture("/img/backgrounddetailed6.jpg", undefined, function() {
                    groundMaterial.map = groundTexture
                });
                groundTexture.wrapS = groundTexture.wrapT = THREE.RepeatWrapping;
                groundTexture.repeat.set(25, 25);
                groundTexture.anisotropy = 16;

                var dirt = new THREE.Mesh(new THREE.PlaneGeometry(step, step), groundMaterial);
                dirt.rotation.x = -Math.PI / 2;
                return dirt;
            }

            function onDocumentMouseOut(event) {
                document.removeEventListener('mouseup', onDocumentMouseUp, false);
                document.removeEventListener('mouseout', onDocumentMouseOut, false);
            }

            function onDocumentKeyDown(event) {
                switch( event.keyCode ) {
                    case 16:
                        isShiftDown = true;
                        rollOverMesh.visible = false;
                        break;
                }
            }

            function onDocumentKeyUp(event) {
                switch ( event.keyCode ) {
                    case 16:
                        isShiftDown = false;
                        rollOverMesh.visible = true;
                        break;
                }
            }

            function setVoxelPosition(intersector) {
                normalMatrix.getNormalMatrix(intersector.object.matrixWorld);
                tmpVec.copy(intersector.face.normal);
                tmpVec.applyMatrix3(normalMatrix).normalize();
                voxelPosition.addVectors(intersector.point, tmpVec);
                voxelPosition.divideScalar(step).floor().multiplyScalar(step).addScalar(step / 2);
                //console.log(voxelPosition.z);
            }

            function pointerLockChange(event) {
                console.log("pointerLockChange");
                if (document.pointerLockElement === document.body || document.mozPointerLockElement === document.body || document.webkitPointerLockElement === document.body) {
                    controls.enabled = true;
                } else {
                    controls.enabled = false;
                }
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
