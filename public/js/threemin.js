var scene = new THREE.Scene();
var camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
var renderer = new THREE.WebGLRenderer({ alpha: true });
renderer.setSize(window.innerWidth, window.innerHeight);
document.body.appendChild(renderer.domElement);

var geometry = new THREE.SphereGeometry(2, 32, 32);
var material = new THREE.MeshBasicMaterial({ color: 0x00ffbf, wireframe: true });
var sphere = new THREE.Mesh(geometry, material);
scene.add(sphere);

camera.position.z = 5;

function render() {
    requestAnimationFrame(render);
    sphere.rotation.x += 0.01;
    sphere.rotation.y += 0.01;
    renderer.render(scene, camera);
}

window.addEventListener('resize', function() {
    camera.aspect = window.innerWidth / window.innerHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(window.innerWidth, window.innerHeight);
});

render();
