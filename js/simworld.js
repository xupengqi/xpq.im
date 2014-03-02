var sw = new function() {
    var cameraPosition = new THREE.Vector3(0, 400, -150);
    var gridSize = 500, step = 20;
    var windowHalfX = window.innerWidth / 2;
    var windowHalfY = window.innerHeight / 2;
    var lookAt = new THREE.Vector3(0, -500, 150);
    var voxelPositionGrid = true;
    
    var gameDom = "#game";
    var camera, scene, renderer;

    var plane;

    var mouse2D, projector, raycaster, isMouseDown = false, isShiftDown = false, isCtrlDown = false, isDragging = false;
    var voxelPosition = new THREE.Vector3(), tmpVec = new THREE.Vector3(), normalMatrix = new THREE.Matrix3();
    
    var objects = [];

    this.getGameDom = function () {
        return gameDom;
    };
    this.getScene = function () {
        return scene;
    };
    this.getCamera = function () {
        return camera;
    };
    this.getPlane = function () {
        return plane;
    };
    this.getGridSize = function () {
        return gridSize;
    };
    this.getGridStep = function () {
        return step;
    };
    this.getObjects = function () {
        return objects;
    };
    this.getVoxelPosition = function () {
        return voxelPosition;
    };
    this.getIsShiftDown = function () {
        return isShiftDown;
    };
    this.getIsCtrlDown = function () {
        return isCtrlDown;
    };
    this.setVoxelPositionGrid = function (vp) {
        voxelPositionGrid = vp;
    };

    this.init = function() {
        // Camera & Scene
        //camera = new THREE.OrthographicCamera(window.innerWidth / -2, window.innerWidth / 2, window.innerHeight / 2, window.innerHeight / -2, -500, 1000);
        camera = new THREE.PerspectiveCamera( 75, window.innerWidth / window.innerHeight, 1, 10000 );
        //camera.position.y = 800;
        camera.position.x = cameraPosition.x;
        camera.position.z = cameraPosition.z;
        camera.position.y = cameraPosition.y;
        scene = new THREE.Scene();



        // ground
        var initColor = new THREE.Color(0x497f13);
        var initTexture = THREE.ImageUtils.generateDataTexture(1, 1, initColor);
        var groundMaterial = new THREE.MeshPhongMaterial({
            color : 0xffffff,
            specular : 0x111111,
            map : initTexture
        });
        var groundTexture = THREE.ImageUtils.loadTexture("/img/grasslight-big.jpg", undefined, function() {
            groundMaterial.map = groundTexture;
        });
        groundTexture.wrapS = groundTexture.wrapT = THREE.RepeatWrapping;
        groundTexture.repeat.set(25, 25);
        groundTexture.anisotropy = 16;

        plane = new THREE.Mesh(new THREE.PlaneGeometry(1000, 1000), groundMaterial);
        plane.rotation.x = -Math.PI / 2;
        plane.visible = true;
        scene.add(plane);
        objects.push(plane);


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

        renderer = new THREE.WebGLRenderer({
            antialias : true
        });
        //renderer.setClearColor(0xf0f0f0);
                renderer.setClearColor( 0x111111, 1 );
        renderer.setSize(window.innerWidth, window.innerHeight);

        $(gameDom).append(renderer.domElement);


        mouse2D = new THREE.Vector3(0, 10000, 0.5);
        projector = new THREE.Projector();

        window.addEventListener('resize', sw.onWindowResize, false);
        document.addEventListener('mousedown', sw.onDocumentMouseDown, false);
        document.addEventListener('mousemove', sw.onDocumentMouseMove, false);
        document.addEventListener('keydown', sw.onDocumentKeyDown, false);
        document.addEventListener('keyup', sw.onDocumentKeyUp, false);
        
        sw.executeCallbacks('init', []);
    };

    this.animate = function() {
        requestAnimationFrame(sw.animate);

        raycaster = projector.pickingRay(mouse2D.clone(), camera);
        var intersects = raycaster.intersectObjects(objects);
        if (intersects.length > 0) {
            //console.log(intersects.length);
            sw.setVoxelPosition(intersects[0]);
            sw.executeCallbacks('voxelPositionChange', []);
        }

        camera.lookAt(lookAt);
        renderer.render(scene, camera);
        
        sw.executeCallbacks('anim', []);
    };
    
    this.onWindowResize = function() {
        camera.left = window.innerWidth / -2;
        camera.right = window.innerWidth / 2;
        camera.top = window.innerHeight / 2;
        camera.bottom = window.innerHeight / -2;
        camera.updateProjectionMatrix();
        renderer.setSize(window.innerWidth, window.innerHeight);
    };

    this.onDocumentMouseDown = function(event) {
        event.preventDefault();
        document.addEventListener('mouseup', sw.onDocumentMouseUp, false);
        document.addEventListener('mouseout', sw.onDocumentMouseOut, false);
        mouseXOnMouseDown = event.clientX - windowHalfX;
        mouseYOnMouseDown = event.clientY - windowHalfY;
        isMouseDown = true;
        sw.executeCallbacks('onMouseDown', []);
    };

    this.onDocumentMouseMove = function(event) {
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
            sw.executeCallbacks('onMouseDrag', []);
        }

        mouse2D.x = (event.clientX / window.innerWidth ) * 2 - 1;
        mouse2D.y = -(event.clientY / window.innerHeight ) * 2 + 1;
    };

    this.onDocumentMouseUp = function(event) {
        document.removeEventListener('mouseup', sw.onDocumentMouseUp, false);
        document.removeEventListener('mouseout', sw.onDocumentMouseOut, false);

        var intersects = raycaster.intersectObjects(objects);
        if (intersects.length > 0) {
            var intersector = intersects[0];
            if (!isDragging) {
                sw.executeCallbacks('onMouseClickUp', [intersector]);
            }
            else {
                sw.executeCallbacks('onMouseDragUp', [intersector]);
            }
        }

        isMouseDown = false;
        isDragging = false;
    };

    this.onDocumentMouseOut = function(event) {
        document.removeEventListener('mouseup', sw.onDocumentMouseUp, false);
        document.removeEventListener('mouseout', sw.onDocumentMouseOut, false);
    };

    this.onDocumentKeyDown = function(event) {
        switch( event.keyCode ) {
            case 16:
                isShiftDown = true;
                break;
            case 17:
                isCtrlDown = true;
                break;
        }
        sw.executeCallbacks('onKeyDown', []);
    };

    this.onDocumentKeyUp = function(event) {
        sw.executeCallbacks('onKeyUp', []);
        switch ( event.keyCode ) {
            case 16:
                isShiftDown = false;
                break;
            case 17:
                isCtrlDown = false;
                break;
        }
    };

    this.setVoxelPosition = function(intersector) {
        normalMatrix.getNormalMatrix(intersector.object.matrixWorld);
        tmpVec.copy(intersector.face.normal);
        tmpVec.applyMatrix3(normalMatrix).normalize();
        voxelPosition.addVectors(intersector.point, tmpVec);
        if (voxelPositionGrid) {
            voxelPosition.divideScalar(step).floor().multiplyScalar(step).addScalar(step / 2);
        }
    };

    var callbacks = {};
    this.addCallback = function (event, callback, params) {
        if (!callbacks[event]) {
            callbacks[event] = [];
        }
        callbacks[event].push([callback, params]);
    };
    this.executeCallbacks = function (event, params) {
        $(callbacks[event]).each(function() {
            this[0].apply(sw, $.merge(params, this[1]));
        });
    };
};

$(function() {
    sw.init();
    sw.animate();
});