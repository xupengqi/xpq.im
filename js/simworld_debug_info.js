Object.getPrototypeOf(sw).showDebugAxis = function(axisLength) {
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
        sw.getScene().add(line);
    }

    createAxis(v(-axisLength, 0, 0), v(axisLength, 0, 0), 0xFF0000);
    createAxis(v(0, -axisLength, 0), v(0, axisLength, 0), 0x00FF00);
    createAxis(v(0, 0, -axisLength), v(0, 0, axisLength), 0x0000FF);
};
sw.addCallback('init', sw.showDebugAxis, [10000]);

Object.getPrototypeOf(sw).initDebugStats = function() {
    var stats;
    stats = new Stats();
    stats.domElement.style.position = 'absolute';
    stats.domElement.style.top = '0px';
    $(sw.getGameDom()).append(stats.domElement);
    sw.addCallback('anim', sw.showDebugStats, [stats]);
};
Object.getPrototypeOf(sw).showDebugStats = function(stats) {
    stats.update();
};
sw.addCallback('init', sw.initDebugStats, [10000]);

Object.getPrototypeOf(sw).initGrid = function() {
    // Grid
    var geometry = new THREE.Geometry();
    for (var i = -sw.getGridSize(); i <= sw.getGridSize(); i +=  sw.getGridStep()) {
        geometry.vertices.push(new THREE.Vector3(-sw.getGridSize(), 0, i));
        geometry.vertices.push(new THREE.Vector3(sw.getGridSize(), 0, i));
        geometry.vertices.push(new THREE.Vector3(i, 0, -sw.getGridSize()));
        geometry.vertices.push(new THREE.Vector3(i, 0, sw.getGridSize()));
    }
    var material = new THREE.LineBasicMaterial({
        color : 0x000000,
        opacity : 0.2
    });
    var line = new THREE.Line(geometry, material);
    line.type = THREE.LinePieces;
    sw.getScene().add(line);
};
sw.addCallback('init', sw.initGrid, []);
