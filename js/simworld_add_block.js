var rollOverMesh, cubeGeo, cubeMaterial;

Object.getPrototypeOf(sw).initCubes = function() {
    // Cubes
    cubeGeo = new THREE.BoxGeometry(sw.getGridStep(), sw.getGridStep(), sw.getGridStep());
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
        cube.position.x = Math.floor((Math.random() * sw.getGridSize() - sw.getGridSize() / 2 ) / sw.getGridStep()) * sw.getGridStep() + sw.getGridStep() / 2;
        cube.position.z = Math.floor((Math.random() * sw.getGridSize() - sw.getGridSize() / 2 ) / sw.getGridStep()) * sw.getGridStep() + sw.getGridStep() / 2;
        cube.position.y = (cube.scale.z * sw.getGridStep() ) / 2;

        if (cubes[cube.position.x] == undefined || cubes[cube.position.x][cube.position.z] == undefined) {
            if (cubes[cube.position.x] == undefined) {
                cubes[cube.position.x] = {};
            }
            cubes[cube.position.x][cube.position.z] = true;
            sw.getScene().add(cube);
            sw.getObjects().push(cube);
        }
    }

    // roll-over helpers
    var rollOverMaterial = new THREE.MeshLambertMaterial({
        color : 0xfeb74c,
        ambient : 0x00ff80,
        shading : THREE.FlatShading,
        opacity: 0.5,
        transparent : true,
        map : THREE.ImageUtils.loadTexture("/img/square-outline-textured.png")
    });
    rollOverMesh = new THREE.Mesh(cubeGeo, rollOverMaterial);
    sw.getScene().add(rollOverMesh);
};
sw.addCallback('init', sw.initCubes, []);

Object.getPrototypeOf(sw).updateRolloverPosition = function() {
    rollOverMesh.position = sw.getVoxelPosition();
};
sw.addCallback('voxelPositionChange', sw.updateRolloverPosition, []);

Object.getPrototypeOf(sw).hideRollOverMesh = function() {
    rollOverMesh.visible = false;
};
Object.getPrototypeOf(sw).showRollOverMesh = function() {
    rollOverMesh.visible = true;
};
sw.addCallback('onMouseDrag', sw.hideRollOverMesh, []);
sw.addCallback('onKeyDown', sw.hideRollOverMesh, []);
sw.addCallback('onKeyUp', sw.showRollOverMesh, []);
sw.addCallback('onMouseClickUp', sw.showRollOverMesh, []);

Object.getPrototypeOf(sw).getDirt = function(intersector) {
    var initColor = new THREE.Color(0x497f13);
    var initTexture = THREE.ImageUtils.generateDataTexture(1, 1, initColor);
    var groundMaterial = new THREE.MeshPhongMaterial({
        color : 0xffffff,
        specular : 0x111111,
        map : initTexture
    });
    var groundTexture = THREE.ImageUtils.loadTexture("/img/backgrounddetailed6.jpg", undefined, function() {
        groundMaterial.map = groundTexture;
    });
    groundTexture.wrapS = groundTexture.wrapT = THREE.RepeatWrapping;
    groundTexture.repeat.set(25, 25);
    groundTexture.anisotropy = 16;

    var dirt = new THREE.Mesh(new THREE.PlaneGeometry(sw.getGridStep(), sw.getGridStep()), groundMaterial);
    dirt.rotation.x = -Math.PI / 2;
    return dirt;
};
Object.getPrototypeOf(sw).addBlock = function(intersector) {
    if (sw.getIsShiftDown()) {
        if (intersector.object != sw.getPlane()) {
            sw.getScene().remove(intersector.object);
            sw.getObjects().splice(sw.getObjects().indexOf(intersector.object), 1);
        }
    } else {
        sw.setVoxelPosition(intersector);
        //console.log(sw.getVoxelPosition());
        var voxel = new THREE.Mesh(cubeGeo, cubeMaterial);
        //var voxel = sw.getDirt();
        voxel.position.copy(sw.getVoxelPosition());
        //voxel.position.y = 1;
        voxel.matrixAutoUpdate = false;
        voxel.updateMatrix();
        sw.getScene().add(voxel);
        sw.getObjects().push(voxel);
    }
};
sw.addCallback('onMouseClickUp', sw.addBlock, []);
