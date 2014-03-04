var points = [], curveLine;
var getCurveLine = function(pts, sub) {
    var spline = new THREE.Spline(pts);
    var geometrySpline = new THREE.Geometry();
    for (var i = 0; i < points.length * sub; i++) {
        var index = i / (points.length * sub );
        var position = spline.getPoint(index);
        geometrySpline.vertices[i] = new THREE.Vector3(position.x, position.y, position.z);
    }
    geometrySpline.computeLineDistances();
    curveLine = new THREE.Line(geometrySpline, new THREE.LineDashedMaterial({
        color : 0x000000,
        dashSize : 1,
        gapSize : 0.5
    }), THREE.LineStrip);
};

var roadStart, roadEnd, roadMesh, roadGeo;
var roads = [];
var roadsByVertices = {};
var roadVertices = [];
var drawRoad = function() {
    var r = new THREE.Shape(), color = 0x333333;
    var x = roadStart.x, y = 1, z = roadStart.z, radius = 5, height = 150, roadSize = 20;
    var line = new THREE.Line3(roadStart, roadEnd);

//TODO: CALCULATE POSITION WITHOUT ROTATION
    r.moveTo(0, 0);
    r.lineTo(0, -roadSize / 2);
    //console.log(line.distance()+","+(roadEnd.x-roadStart.x));
    r.lineTo(-line.distance(), -roadSize / 2);
    r.lineTo(-line.distance(), 0);
    r.lineTo(-line.distance(), roadSize / 2);
    r.lineTo(0, roadSize / 2);

    roadGeo = new THREE.ShapeGeometry(r);
    sw.getScene().remove(roadMesh);
    roadMesh = new THREE.Mesh(roadGeo, new THREE.MeshBasicMaterial( { color: color } ));
    roadMesh.position.set(x, y, z);
    roadMesh.rotation.x = -Math.PI / 2;

    var axis = new THREE.Vector3();
    axis.subVectors(roadEnd, roadStart);
    axis.normalize();
    var theta = Math.acos(axis.dot(new THREE.Vector3(-1, 0, 0)));
    if (axis.z < 0) {
        theta = -theta;
    }
    roadMesh.rotation.z = theta;

    sw.getScene().add(roadMesh);
};
var nearJoint = function() {
    var detectDist = 10;
    var minX = roadStart.x-detectDist;
    var maxX = roadStart.x+detectDist;
    var minZ = roadStart.z-detectDist;
    var maxZ = roadStart.z+detectDist;
    var result = null;
    $(roadVertices).each(function() {
        if (!result && minX <= this.x && this.x <= maxX && minZ <= this.z && this.z <= maxZ) {
            result = this;
        }
    });
    return result;
};
var createJoint = function(joint) {
    var existingRoadMesh = roadsByVertices[joint.x][joint.z][0][0].clone();
    var existingRoadGeo = roadsByVertices[joint.x][joint.z][0][1].clone();
    newRoadMesh = roadMesh.clone();
    var newRoadGeo = roadGeo.clone();
    THREE.GeometryUtils.merge(existingRoadGeo,newRoadGeo);
    console.log("roadStart:" + roadStart.x + "," + roadStart.z);
    console.log("joint:" + joint.x + "," + joint.z);
    console.dir(existingRoadMesh);
};

Object.getPrototypeOf(sw).startLine = function() {
    if (points.length == 0) {
        points.push(sw.getVoxelPosition().clone());
    }
    roadStart = sw.getVoxelPosition().clone();
};
sw.addCallback('onMouseDown', sw.startLine, []);

Object.getPrototypeOf(sw).renderTempLine = function() {
    // var tempPoints = points.concat(sw.getVoxelPosition().clone());
    // sw.getScene().remove(curveLine);
    // getCurveLine(points.concat(sw.getVoxelPosition().clone()), 6);
    // sw.getScene().add(curveLine);

    roadEnd = sw.getVoxelPosition().clone();
    drawRoad();
    
    var joint = nearJoint();
    if (joint) {
        roadStart = joint;
        createJoint(joint);
    }
};
sw.addCallback('onMouseDrag', sw.renderTempLine, []);

Object.getPrototypeOf(sw).endLine = function() {
    // points.push(sw.getVoxelPosition().clone());
    // sw.getScene().remove(curveLine);
    // getCurveLine(points, 6);
    // sw.getScene().add(curveLine);

    roads.push(roadMesh);
    roadVertices.push(roadStart);
    roadVertices.push(roadEnd);
    
    (roadsByVertices[roadStart.x] == null) ? roadsByVertices[roadStart.x] = [] : true;
    (roadsByVertices[roadStart.x][roadStart.z] == null) ? roadsByVertices[roadStart.x][roadStart.z] = [] : true;
    (roadsByVertices[roadEnd.x] == null) ? roadsByVertices[roadEnd.x] = [] : true;
    (roadsByVertices[roadEnd.x][roadEnd.z] == null) ? roadsByVertices[roadEnd.x][roadEnd.z] = [] : true;
    roadsByVertices[roadStart.x][roadStart.z].push([roadMesh, roadGeo]);
    roadsByVertices[roadEnd.x][roadEnd.z].push([roadMesh, roadGeo]);
    
    roadMesh = null;
};
sw.addCallback('onMouseDragUp', sw.endLine, []);

function addShape(group, shape, color, x, y, z) {
    // flat shape
    var geometry = new THREE.ShapeGeometry(shape);
    var mesh = THREE.SceneUtils.createMultiMaterialObject(geometry, [new THREE.MeshLambertMaterial({
        color : color
    }), new THREE.MeshBasicMaterial({
        color : 0x000000,
        wireframe : false,
        transparent : false
    })]);
    mesh.position.set(x, y, z);
    mesh.rotation.x = -Math.PI / 2;
    group.add(mesh);
}


Object.getPrototypeOf(sw).initLine = function() {
    // Rounded rectangle
    /*var roundedRectShape = new THREE.Shape();
    var x = 0, y = 0, radius = 5, height = 150;
    roundedRectShape.moveTo(x, y + radius);
    roundedRectShape.lineTo(x, y + height - radius);
    roundedRectShape.quadraticCurveTo(x, y + height, x + radius, y + height);
    addShape(sw.getScene(), roundedRectShape, 0xFF0000, 0, 1, 0);*/

    sw.setVoxelPositionGrid(false);
};
sw.addCallback('init', sw.initLine, []);
