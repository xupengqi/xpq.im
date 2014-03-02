<div style="position: absolute; left: 50%; display: none;" id="screen-lock-wrapper">
    <div style="position: relative; left: -50%; border: dotted red 1px; padding: 10px; font-size: 20px;">
        <a href="#" id="lock" style="padding: 20px; font-size: 40px;">Sceen Lock</a>
    </div>
</div>
<div id="camera-position-wrapper" style="top: 20px;left: 100px; position: absolute;">
    x<div id="camerax" data-slider-min="-500"
    data-slider-max="500" data-slider-step="1"
    data-slider-value="50"></div>
    <br/>
    <br/>
    y<div id="cameray" data-slider-min="-500"
    data-slider-max="500" data-slider-step="1"
    data-slider-value="50"></div>
    <br/>
    <br/>
    z<div id="cameraz" data-slider-min="-500"
    data-slider-max="500" data-slider-step="1"
    data-slider-value="50"></div>
</div>
<script src="/js/PointerLockControls.js"></script>
<script src="/js/bootstrap-slider.js"></script>

<script>
    var controls, time = Date.now();

    Object.getPrototypeOf(sw).initCameraControls = function() {
        // PointerLockControl
        controls = new THREE.PointerLockControls(sw.getCamera());
        sw.getScene().add(controls.getObject());

        var pointerLockError = function(event) {
            console.log(event);
        };

        var pointerLockChange = function(event) {
            if (document.pointerLockElement === document.body || document.mozPointerLockElement === document.body || document.webkitPointerLockElement === document.body) {
                controls.enabled = true;
            } else {
                controls.enabled = false;
            }
        };
        document.addEventListener('pointerlockchange', pointerLockChange, false);
        document.addEventListener('mozpointerlockchange', pointerLockChange, false);
        document.addEventListener('webkitpointerlockchange', pointerLockChange, false);
        document.addEventListener('pointerlockerror', pointerLockError, false);
        document.addEventListener('mozpointerlockerror', pointerLockError, false);
        document.addEventListener('webkitpointerlockerror', pointerLockError, false);
        ;
        sw.addCallback('anim', sw.showCameraControls, []);
    };
    Object.getPrototypeOf(sw).showCameraControls = function() {
        controls.update(Date.now() - time);
        time = Date.now();
    };
    Object.getPrototypeOf(sw).updateCameraPosition = function(x, y, z) {
        if (x !== null) {
            sw.getCamera().position.x = x;
        }
        if (y !== null) {
            sw.getCamera().position.y = y;
        }
        if (z !== null) {
            sw.getCamera().position.z = z;
        }
    }
    sw.addCallback('init', sw.initCameraControls, [])
    
    
    Object.getPrototypeOf(sw).showLockControl = function() {
        if (sw.getIsCtrlDown()) {
            $("#screen-lock-wrapper").show();
        }
    };
    sw.addCallback('onKeyDown', sw.showLockControl, []);
    Object.getPrototypeOf(sw).hideLockControl = function() {
        if (sw.getIsCtrlDown()) {
            $("#screen-lock-wrapper").hide();
        }
    };
    sw.addCallback('onKeyUp', sw.hideLockControl, []);


    $(function() {
        $('#camerax').slider().on('slide', function(ev) {
            Object.getPrototypeOf(sw).updateCameraPosition(ev.value, null, null)
        });
        $('#cameray').slider().on('slide', function(ev) {
            Object.getPrototypeOf(sw).updateCameraPosition(null, ev.value, null)
        });
        $('#cameraz').slider().on('slide', function(ev) {
            Object.getPrototypeOf(sw).updateCameraPosition(null, null, ev.value)
        });
        $("#lock").click(function() {
            // Ask the browser to lock the pointer
            document.body.requestPointerLock = document.body.requestPointerLock || document.body.mozRequestPointerLock || document.body.webkitRequestPointerLock;
            document.body.requestPointerLock();
        });
    }); 
</script>