var points = [], curveLine;
var intersectionSize = 50;
var getCurveLine = function(pts, sub) {
    var spline = new THREE.Spline(pts);
    var geometrySpline = new THREE.Geometry();
    for ( var i = 0; i < points.length * sub; i++) {
        var index = i / (points.length * sub);
        var position = spline.getPoint(index);
        geometrySpline.vertices[i] = new THREE.Vector3(position.x, position.y,
                position.z);
    }
    geometrySpline.computeLineDistances();
    curveLine = new THREE.Line(geometrySpline, new THREE.LineDashedMaterial({
        color : 0x000000,
        dashSize : 1,
        gapSize : 0.5
    }), THREE.LineStrip);
};

var roadStart, roadEnd, roadMesh, jointMesh, jointMeshEnd;
var roads = [];
var joints = [];
var roadsByVertices = {};
var roadVertices = [];
var getRoadVertices = function(start, end, maxLength) {
    var roadSize = 20;
    var v = end.clone().sub(start);
    if (maxLength) {
        v.normalize();
        v.setLength(maxLength);
    }
    var v1 = new THREE.Vector3(-v.z, 1, v.x);
    v1.setLength(roadSize / 2);
    v1.y = 1;
    var v11 = new THREE.Vector3(v1.x + v.x, 1, v1.z + v.z);
    var v2 = new THREE.Vector3(v.z, 1, -v.x);
    v2.setLength(roadSize / 2);
    v2.y = 1;
    var v22 = new THREE.Vector3(v2.x + v.x, 1, v2.z + v.z);
    return [ v1, v11, v22, v2 ];
};
var getAngle = function(x, y) {
    return Math.atan2(y,x) / Math.PI * 180;
};
var getRoadsInOrder = function(start, ends) {
    var ordered = [];
    var v0 = ends[0].clone().sub(start);
    v0.normalize();
    ordered.push([getAngle(v0.x, v0.z), ends[0]]);
    
    for (var i=1; i<ends.length; i++) {
        var vv = ends[i].clone().sub(start);
        vv.normalize();
        var inserted = false;
        var d = getAngle(vv.x, vv.z);
        for (var j=0; j<ordered.length; j++) {
            if (d < ordered[j][0]) {
                ordered.splice(j, 0, [d,ends[i]]);
                inserted = true;
                break;
            }
        }
        if (!inserted) {
            ordered.push([d,ends[i]]);
        }
    }
    return ordered;
};
var drawRoadNoRotation = function(start, end) {
    var color = 0x333333;
    var s = new THREE.Shape();
    var vs = getRoadVertices(start, end);

    s.moveTo(vs[0].x, -vs[0].z);
    s.lineTo(vs[1].x, -vs[1].z);
    s.lineTo(vs[2].x, -vs[2].z);
    s.lineTo(vs[3].x, -vs[3].z);
    var roadGeo = new THREE.ShapeGeometry(s);
    sw.getScene().remove(roadMesh);
    roadMesh = new THREE.Mesh(roadGeo, new THREE.MeshBasicMaterial({
        color : color
    }));
    roadMesh.position.set(start.x, 1, start.z);
    roadMesh.rotation.x = -Math.PI / 2;
    sw.getScene().add(roadMesh);
};
var nearJoint = function(pt) {
    var detectDist = 20;
    var minX = pt.x - detectDist;
    var maxX = pt.x + detectDist;
    var minZ = pt.z - detectDist;
    var maxZ = pt.z + detectDist;
    var result = null;
    $(roadVertices).each(function() {
        if (!result && minX <= this.x && this.x <= maxX
                && minZ <= this.z && this.z <= maxZ) {
            result = this;
        }
    });
    return result;
};
var drawPartialJoint = function(shape, joint, end1, end2, close) {
    var va = getRoadVertices(joint, end1, intersectionSize);
    var vb = getRoadVertices(joint, end2, intersectionSize);

    var line1 = new THREE.Line3(new THREE.Vector3(va[1].x, 0, -va[1].z),new THREE.Vector3(va[0].x, 0, -va[0].z));
    var int1 = line1.closestPointToPoint(new THREE.Vector3(vb[3].x, 0, -vb[3].z));
    
    shape.quadraticCurveTo(int1.x, int1.z, vb[2].x, -vb[2].z);
    if (close) {
        shape.lineTo(vb[1].x, -vb[1].z);
    }
};
var createJoint = function(joint, newEnd, isStart) {
    var color = 0xFFFFFF;
    var sa = new THREE.Shape();
    
    var ordered = getRoadsInOrder(joint, roadsByVertices[joint.x][joint.z].concat(newEnd));
    var va = getRoadVertices(joint, ordered[0][1], intersectionSize);
    sa.moveTo(va[1].x, -va[1].z);
    for (var i = 0; i < ordered.length-1; i++) {
        drawPartialJoint(sa, joint, ordered[i][1], ordered[i+1][1], true);
    }
    drawPartialJoint(sa, joint, ordered[i][1], ordered[0][1], false);
    
    var ga = new THREE.ShapeGeometry(sa);

    var mesh = jointMesh;
    if (!isStart) {
    	mesh = jointMeshEnd;
    }
    sw.getScene().remove(mesh);
    mesh = new THREE.Mesh(ga, new THREE.MeshBasicMaterial({
        color : color
    }));
    mesh.position.set(joint.x, 2, joint.z);
    mesh.rotation.x = -Math.PI / 2;
    sw.getScene().add(mesh);
    if (isStart) {
    	jointMesh = mesh;
    }
    else {
    	jointMeshEnd = mesh;
    }
};

Object.getPrototypeOf(sw).startLine = function() {
    if (points.length == 0) {
        points.push(sw.getVoxelPosition().clone());
    }
    roadStart = sw.getVoxelPosition().clone();
};
sw.addCallback('onMouseDown', sw.startLine, []);

Object.getPrototypeOf(sw).renderTempLine = function() {
    roadEnd = sw.getVoxelPosition().clone();
    var rl = roadEnd.clone().sub(roadStart);
    if (rl.length() < 50) {
    	rl.setLength(50);
    	roadEnd = rl.add(roadStart);
    }
    
    var joint = nearJoint(roadStart);
    if (joint) {
        roadStart = joint;
        createJoint(joint, roadEnd, true);
    }
    var endJoint = nearJoint(roadEnd);
    if (endJoint && (endJoint != joint)) {
    	
    	console.log("end"+rl.length());
    	roadEnd = endJoint;
        createJoint(endJoint, roadStart, false);
    }
    drawRoadNoRotation(roadStart, roadEnd);
    
};
sw.addCallback('onMouseDrag', sw.renderTempLine, []);

Object.getPrototypeOf(sw).endLine = function() {
    roads.push(roadMesh);
    roadVertices.push(roadStart.clone()); //TODO: IF HAS JOINT, DON'T PUSH START
    roadVertices.push(roadEnd.clone());
    
    if (jointMesh) {
        joints.push(jointMesh);
    }
    if (jointMeshEnd) {
        joints.push(jointMeshEnd);
    }

    (roadsByVertices[roadStart.x] == null) ? roadsByVertices[roadStart.x] = []
            : true;
    (roadsByVertices[roadStart.x][roadStart.z] == null) ? roadsByVertices[roadStart.x][roadStart.z] = []
            : true;
    (roadsByVertices[roadEnd.x] == null) ? roadsByVertices[roadEnd.x] = []
            : true;
    (roadsByVertices[roadEnd.x][roadEnd.z] == null) ? roadsByVertices[roadEnd.x][roadEnd.z] = []
            : true;
    roadsByVertices[roadStart.x][roadStart.z].push(roadEnd);
    roadsByVertices[roadEnd.x][roadEnd.z].push(roadStart);

    roadMesh = null;
    jointMesh = null;
    jointMeshEnd = null;
};
sw.addCallback('onMouseDragUp', sw.endLine, []);

function addShape(group, shape, color, x, y, z) {
    // flat shape
    var geometry = new THREE.ShapeGeometry(shape);
    var mesh = THREE.SceneUtils.createMultiMaterialObject(geometry, [
            new THREE.MeshLambertMaterial({
                color : color
            }), new THREE.MeshBasicMaterial({
                color : 0x000000,
                wireframe : false,
                transparent : false
            }) ]);
    mesh.position.set(x, y, z);
    mesh.rotation.x = -Math.PI / 2;
    group.add(mesh);
}

Object.getPrototypeOf(sw).initLine = function() {
    // Rounded rectangle
    /*
     * var roundedRectShape = new THREE.Shape(); var x = 0, y = 0, radius = 5,
     * height = 150; roundedRectShape.moveTo(x, y + radius);
     * roundedRectShape.lineTo(x, y + height - radius);
     * roundedRectShape.quadraticCurveTo(x, y + height, x + radius, y + height);
     * addShape(sw.getScene(), roundedRectShape, 0xFF0000, 0, 1, 0);
     */

    sw.setVoxelPositionGrid(false);
};
sw.addCallback('init', sw.initLine, []);