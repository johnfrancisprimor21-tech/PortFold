import * as THREE from 'three';
import { RoundedBoxGeometry } from 'three/addons/geometries/RoundedBoxGeometry.js';

const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
const coarsePointer = window.matchMedia('(pointer: coarse)');

document.querySelectorAll('[data-creative-scene]').forEach((host) => {
    const hero = host.closest('[data-creative-splash]');
    if (!hero) return;

    let renderer;
    try {
        renderer = new THREE.WebGLRenderer({
            alpha: true,
            antialias: !coarsePointer.matches,
            powerPreference: 'low-power',
        });
    } catch (error) {
        console.warn('PortFold creative 3D scene could not start; using the accessible fallback.', error);
        hero.querySelector('[data-camera-controls]')?.setAttribute('hidden', '');
        return;
    }

    const canvas = renderer.domElement;
    canvas.className = 'hero-canvas';
    canvas.setAttribute('aria-hidden', 'true');
    host.appendChild(canvas);
    renderer.setClearColor(0x000000, 0);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, coarsePointer.matches ? 1.15 : 1.5));
    renderer.outputColorSpace = THREE.SRGBColorSpace;
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.toneMappingExposure = 1.1;

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(34, 1, 0.1, 100);
    const initialCamera = new THREE.Vector3(8.8, 6.6, 13.2);
    const initialTarget = new THREE.Vector3(0, 1.55, 0);
    const cameraOffset = initialCamera.clone().sub(initialTarget);
    const baseCameraRadius = cameraOffset.length();
    let cameraRadius = baseCameraRadius;
    const cameraPitch = Math.asin(cameraOffset.y / cameraRadius);
    const frontAzimuth = Math.atan2(cameraOffset.x, cameraOffset.z);
    const rearAzimuth = frontAzimuth + Math.PI;
    let currentAzimuth = frontAzimuth;
    let targetAzimuth = frontAzimuth;

    function cameraPositionAt(azimuth, pitch = cameraPitch, radius = cameraRadius) {
        const horizontalRadius = radius * Math.cos(pitch);
        return new THREE.Vector3(
            initialTarget.x + Math.sin(azimuth) * horizontalRadius,
            initialTarget.y + Math.sin(pitch) * radius,
            initialTarget.z + Math.cos(azimuth) * horizontalRadius,
        );
    }

    camera.position.copy(initialCamera);
    camera.lookAt(initialTarget);

    const studio = new THREE.Group();
    scene.add(studio);

    const palette = {
        'studio-bg': 0x292b32,
        'studio-accent': 0xa36cff,
        'desk-top': 0x282b33,
        'desk-edge': 0x11141b,
        'desk-front': 0x1c2028,
        'desk-side': 0x171a21,
        'desk-detail': 0x8053df,
        'monitor-frame': 0x11141a,
        'monitor-side': 0x252a34,
    };
    const surfaces = new Map();

    function surface(key, roughness = 0.62, metalness = 0.04) {
        const material = new THREE.MeshStandardMaterial({
            color: palette[key] ?? 0xffffff,
            roughness,
            metalness,
        });
        material.userData.paletteKey = key;
        surfaces.set(key, [...(surfaces.get(key) || []), material]);
        return material;
    }

    function mesh(geometry, material, parent = studio, position = [0, 0, 0]) {
        const object = new THREE.Mesh(geometry, material);
        object.position.set(...position);
        object.castShadow = true;
        object.receiveShadow = true;
        parent.add(object);
        return object;
    }

    function block(size, material, position, parent = studio, radius = 0) {
        const geometry = new THREE.BoxGeometry(...size);
        const object = mesh(geometry, material, parent, position);
        if (radius) object.userData.roundedHint = radius;
        return object;
    }

    function roundedBlock(size, radius, material, position, parent = studio, segments = 3) {
        return mesh(new RoundedBoxGeometry(...size, segments, radius), material, parent, position);
    }

    function cylinder(radiusTop, radiusBottom, height, material, position, parent = studio, segments = 20) {
        return mesh(new THREE.CylinderGeometry(radiusTop, radiusBottom, height, segments), material, parent, position);
    }

    function sphere(size, material, position, parent = studio) {
        const object = mesh(new THREE.SphereGeometry(1, 20, 14), material, parent, position);
        object.scale.set(...size);
        return object;
    }

    const hemi = new THREE.HemisphereLight(0xe7e9ff, 0x20232b, 2.0);
    scene.add(hemi);
    const keyLight = new THREE.DirectionalLight(0xf4f1ff, 3.1);
    keyLight.position.set(-5, 9, 7);
    scene.add(keyLight);
    const rimLight = new THREE.PointLight(0x9b5cff, 15, 13, 2);
    rimLight.position.set(0, 3.4, -2.4);
    scene.add(rimLight);
    const fillLight = new THREE.PointLight(0x45cfff, 9, 12, 2);
    fillLight.position.set(3.2, 3.4, 2.8);
    scene.add(fillLight);
    const towerGlow = new THREE.PointLight(0x8d4dff, 5.5, 6, 2);
    towerGlow.position.set(2.2, 2.65, -0.4);
    scene.add(towerGlow);

    const floorMaterial = surface('studio-bg', 0.98, 0);
    const floor = mesh(new THREE.PlaneGeometry(160, 160), floorMaterial, scene, [0, -0.035, 0]);
    floor.rotation.x = -Math.PI / 2;
    floor.castShadow = false;

    const woodTop = surface('desk-top', 0.36, 0.12);
    const woodEdge = surface('desk-edge', 0.45, 0.08);
    const woodFront = surface('desk-front', 0.5, 0.03);
    const woodSide = surface('desk-side', 0.56, 0.02);
    const trim = surface('desk-detail', 0.34, 0.14);
    const metal = surface('monitor-frame', 0.26, 0.48);
    const metalSide = surface('monitor-side', 0.3, 0.56);
    const darkPlastic = new THREE.MeshStandardMaterial({ color: 0x12151b, roughness: 0.36, metalness: 0.28 });
    const hardwareMetal = new THREE.MeshStandardMaterial({ color: 0x424954, roughness: 0.32, metalness: 0.76 });
    const keyMaterial = new THREE.MeshStandardMaterial({ color: 0x252a34, roughness: 0.54, metalness: 0.08 });
    const chairFabric = new THREE.MeshStandardMaterial({ color: 0x171b23, roughness: 0.86 });
    const chairInset = new THREE.MeshStandardMaterial({ color: 0x292e38, roughness: 0.78 });
    const temperedGlass = new THREE.MeshPhysicalMaterial({ color: 0x283340, roughness: 0.2, metalness: 0.24, transparent: true, opacity: 0.44, side: THREE.DoubleSide });
    const ledViolet = new THREE.MeshStandardMaterial({ color: 0x9b5cff, emissive: 0x792cff, emissiveIntensity: 2.4, roughness: 0.32, toneMapped: false });
    const ledCyan = new THREE.MeshStandardMaterial({ color: 0x43d5ff, emissive: 0x13a9ff, emissiveIntensity: 2.1, roughness: 0.3, toneMapped: false });
    const ledBlue = new THREE.MeshStandardMaterial({ color: 0x6086ff, emissive: 0x3c62ff, emissiveIntensity: 1.5, roughness: 0.32, toneMapped: false });

    // The floor glow and contact shadow anchor the desk without requiring expensive real-time shadows.
    const shadowCanvas = document.createElement('canvas');
    shadowCanvas.width = 256;
    shadowCanvas.height = 128;
    const shadowContext = shadowCanvas.getContext('2d');
    const shadowGradient = shadowContext.createRadialGradient(128, 64, 4, 128, 64, 64);
    shadowGradient.addColorStop(0, 'rgba(4,2,10,.3)');
    shadowGradient.addColorStop(0.55, 'rgba(4,2,10,.17)');
    shadowGradient.addColorStop(1, 'rgba(4,2,10,0)');
    shadowContext.fillStyle = shadowGradient;
    shadowContext.fillRect(0, 0, 256, 128);
    const shadowTexture = new THREE.CanvasTexture(shadowCanvas);
    const groundShadow = mesh(
        new THREE.PlaneGeometry(10.5, 5.2),
        new THREE.MeshBasicMaterial({ map: shadowTexture, transparent: true, depthWrite: false }),
        scene,
        [0, 0.008, 0.35],
    );
    groundShadow.rotation.x = -Math.PI / 2;
    groundShadow.renderOrder = 1;

    // Ultrawide gaming display, lit portfolio screen, and a finished rear shell.
    const display = new THREE.Group();
    display.position.set(-0.28, 3.1, -0.82);
    display.rotation.y = 0.1;
    studio.add(display);
    roundedBlock([3.46, 1.96, 0.16], 0.1, darkPlastic, [0, 0, 0], display, 4);
    roundedBlock([3.34, 1.84, 0.035], 0.065, metal, [0, 0, 0.088], display, 4);
    roundedBlock([3.17, 1.78, 0.018], 0.035, darkPlastic, [0, 0, 0.111], display, 4);

    const screenCanvas = document.createElement('canvas');
    screenCanvas.width = 1280;
    screenCanvas.height = 720;
    const screenContext = screenCanvas.getContext('2d');
    const screenTexture = new THREE.CanvasTexture(screenCanvas);
    screenTexture.colorSpace = THREE.SRGBColorSpace;
    screenTexture.anisotropy = Math.min(renderer.capabilities.getMaxAnisotropy(), 4);
    const screenMaterial = new THREE.MeshBasicMaterial({ map: screenTexture, toneMapped: false });
    mesh(new THREE.PlaneGeometry(3.08, 1.73), screenMaterial, display, [0, 0.015, 0.124]);
    const statusLed = new THREE.MeshBasicMaterial({ color: 0x85e9bd, toneMapped: false });
    sphere([0.023, 0.023, 0.012], statusLed, [1.62, -0.91, 0.11], display);

    roundedBlock([0.22, 0.52, 0.16], 0.045, hardwareMetal, [0, -1.12, -0.035], display, 3);
    roundedBlock([1.24, 0.085, 0.56], 0.04, hardwareMetal, [0, -1.4, 0.08], display, 3);
    roundedBlock([0.72, 0.028, 0.016], 0.012, ledViolet, [0, -0.925, 0.094], display, 2);
    roundedBlock([3.04, 0.025, 0.018], 0.01, ledCyan, [0, -0.925, 0.104], display, 2);

    // The reverse is modeled too, so the rear camera shows the monitor shell and its cable.
    roundedBlock([3.12, 1.74, 0.045], 0.12, darkPlastic, [0, 0, -0.105], display, 4);
    roundedBlock([1.32, 0.78, 0.055], 0.16, metalSide, [0, 0.04, -0.145], display, 4);
    roundedBlock([0.92, 0.13, 0.025], 0.05, darkPlastic, [0, -0.59, -0.18], display, 3);
    for (let vent = 0; vent < 9; vent += 1) {
        roundedBlock([0.58, 0.018, 0.012], 0.008, hardwareMetal, [0, -0.18 + vent * 0.055, -0.179], display, 2);
    }

    const workSurfaceY = 1.86;
    // A clean metal-frame gaming desk, with a thin RGB strip instead of a toy-colored slab.
    roundedBlock([6.1, 0.14, 2.8], 0.075, woodTop, [0, workSurfaceY, 0], studio, 4);
    block([6.02, 0.045, 2.74], woodEdge, [0, workSurfaceY - 0.09, 0]);
    roundedBlock([5.6, 0.26, 0.12], 0.04, woodFront, [0, 1.64, 1.23], studio, 3);
    block([0.11, 0.28, 2.38], woodSide, [-2.72, 1.62, -0.02]);
    block([0.11, 0.28, 2.38], woodSide, [2.72, 1.62, -0.02]);
    roundedBlock([5.7, 0.028, 0.032], 0.012, ledViolet, [0, 1.78, 1.408], studio, 2);
    roundedBlock([5.42, 0.014, 0.02], 0.008, ledCyan, [0, 1.805, 1.414], studio, 2);
    [-2.68, 2.68].forEach((x) => [-1.18, 1.18].forEach((z) => {
        roundedBlock([0.085, 1.61, 0.085], 0.025, hardwareMetal, [x, 0.82, z], studio, 3);
        roundedBlock([0.2, 0.045, 0.2], 0.025, darkPlastic, [x, 0.035, z], studio, 3);
    }));
    block([5.15, 0.07, 0.08], woodFront, [0, 1.42, -1.02]);
    block([1.1, 0.045, 1.08], darkPlastic, [-1.18, workSurfaceY + 0.095, 0.52]);
    roundedBlock([0.88, 0.018, 0.026], 0.01, ledViolet, [-1.18, workSurfaceY + 0.119, 1.06], studio, 2);

    // Compact mechanical keyboard and mouse on a proper desk mat.
    const keyboard = new THREE.Group();
    keyboard.position.set(-0.64, workSurfaceY + 0.13, 0.45);
    keyboard.rotation.x = -0.025;
    studio.add(keyboard);
    roundedBlock([1.7, 0.07, 0.54], 0.055, darkPlastic, [0, 0, 0], keyboard, 3);
    roundedBlock([1.54, 0.025, 0.016], 0.008, ledCyan, [0, -0.007, 0.271], keyboard, 2);
    for (let row = 0; row < 4; row += 1) {
        const count = row === 3 ? 7 : 14;
        for (let column = 0; column < count; column += 1) {
            const x = count === 14 ? -0.72 + column * 0.106 : -0.55 + column * 0.18;
            const width = row === 3 && column === 3 ? 0.38 : 0.074;
            const key = block([width, 0.028, 0.075], keyMaterial, [x, 0.05, -0.17 + row * 0.105], keyboard);
            key.material = column % 5 === 0 ? ledViolet : (row === 0 && column % 2 === 0 ? ledCyan : keyMaterial);
        }
    }
    const mouse = roundedBlock([0.2, 0.075, 0.28], 0.075, darkPlastic, [0.72, workSurfaceY + 0.11, 0.52], studio, 4);
    mouse.rotation.y = -0.08;
    roundedBlock([0.028, 0.012, 0.17], 0.006, ledViolet, [0.72, workSurfaceY + 0.151, 0.52], studio, 2);

    // Tempered-glass gaming PC tower with three RGB intake fans and a detailed rear panel.
    const tower = new THREE.Group();
    tower.position.set(2.18, workSurfaceY + 0.62, -0.56);
    studio.add(tower);
    roundedBlock([0.8, 1.2, 0.84], 0.09, darkPlastic, [0, 0, 0], tower, 4);
    roundedBlock([0.72, 1.11, 0.036], 0.055, metalSide, [0, 0, 0.433], tower, 3);
    roundedBlock([0.68, 1.06, 0.022], 0.045, darkPlastic, [0, 0, 0.456], tower, 3);
    const fanLights = [];
    [-0.37, 0, 0.37].forEach((y, index) => {
        const ringMaterial = index === 1 ? ledCyan : ledViolet;
        const fanRing = new THREE.Mesh(new THREE.TorusGeometry(0.135, 0.018, 8, 32), ringMaterial);
        fanRing.position.set(0, y, 0.474);
        tower.add(fanRing);
        fanRing.castShadow = true;
        sphere([0.075, 0.075, 0.02], ledBlue, [0, y, 0.478], tower);
        fanLights.push(ringMaterial);
    });
    const temperedWindow = roundedBlock([0.018, 1.02, 0.66], 0.035, temperedGlass, [0.41, 0, -0.015], tower, 3);
    temperedWindow.castShadow = false;
    roundedBlock([0.04, 0.08, 0.48], 0.018, ledCyan, [0.435, -0.2, -0.015], tower, 2);
    roundedBlock([0.035, 0.22, 0.42], 0.025, ledViolet, [0.435, -0.32, 0.035], tower, 2);
    roundedBlock([0.03, 0.06, 0.3], 0.015, hardwareMetal, [0.435, 0.31, -0.08], tower, 2);
    roundedBlock([0.68, 1.04, 0.025], 0.012, metalSide, [0, 0, -0.44], tower, 3);
    const rearFan = new THREE.Mesh(new THREE.TorusGeometry(0.14, 0.018, 8, 28), ledViolet);
    rearFan.position.set(0, 0.38, -0.46);
    rearFan.rotation.y = Math.PI;
    tower.add(rearFan);
    for (let slot = 0; slot < 6; slot += 1) {
        roundedBlock([0.26, 0.018, 0.014], 0.007, hardwareMetal, [0.18, -0.05 - slot * 0.055, -0.462], tower, 2);
    }
    roundedBlock([0.2, 0.12, 0.025], 0.02, darkPlastic, [-0.18, -0.38, -0.462], tower, 2);
    roundedBlock([0.66, 0.025, 0.018], 0.008, ledCyan, [0, -0.57, 0], tower, 2);
    roundedBlock([0.17, 0.06, 0.035], 0.014, hardwareMetal, [0, 0.63, 0.28], tower, 2);
    const powerLed = new THREE.MeshBasicMaterial({ color: 0x79efff, toneMapped: false });
    sphere([0.018, 0.018, 0.012], powerLed, [0.04, 0.635, 0.305], tower);

    // Compact speakers and an ergonomic gaming chair finish the current-gen setup.
    [-2.38, 1.64].forEach((x) => {
        roundedBlock([0.34, 0.5, 0.34], 0.07, darkPlastic, [x, workSurfaceY + 0.31, 0.54], studio, 3);
        const speakerRing = new THREE.Mesh(new THREE.TorusGeometry(0.075, 0.012, 8, 24), x < 0 ? ledViolet : ledCyan);
        speakerRing.position.set(x, workSurfaceY + 0.33, 0.72);
        studio.add(speakerRing);
        sphere([0.055, 0.055, 0.018], hardwareMetal, [x, workSurfaceY + 0.33, 0.724]);
    });

    // Gaming chair with a high back, bolsters, armrests, gas lift and a five-star base.
    const chair = new THREE.Group();
    chair.position.set(0.82, 0, 2.42);
    chair.rotation.y = -0.1;
    studio.add(chair);
    const chairBack = roundedBlock([0.88, 1.38, 0.22], 0.18, chairFabric, [0, 1.72, 0.43], chair, 5);
    chairBack.rotation.x = -0.1;
    roundedBlock([0.56, 0.88, 0.06], 0.12, chairInset, [0, 1.74, 0.56], chair, 4);
    roundedBlock([0.09, 1.04, 0.045], 0.035, ledViolet, [-0.34, 1.72, 0.54], chair, 2);
    roundedBlock([0.09, 1.04, 0.045], 0.035, ledCyan, [0.34, 1.72, 0.54], chair, 2);
    roundedBlock([0.82, 0.2, 0.58], 0.1, chairFabric, [0, 0.98, -0.02], chair, 4);
    roundedBlock([0.52, 0.13, 0.38], 0.07, chairInset, [0, 1.08, 0.08], chair, 3);
    [-0.55, 0.55].forEach((x) => {
        roundedBlock([0.11, 0.42, 0.12], 0.045, darkPlastic, [x, 1.23, 0.05], chair, 3);
        roundedBlock([0.15, 0.07, 0.42], 0.035, chairFabric, [x, 1.42, 0.16], chair, 3);
    });
    cylinder(0.08, 0.1, 0.66, hardwareMetal, [0, 0.53, 0], chair, 14);
    cylinder(0.29, 0.2, 0.08, darkPlastic, [0, 0.2, 0], chair, 18);
    for (let index = 0; index < 5; index += 1) {
        const angle = (Math.PI * 2 * index) / 5;
        const spoke = block([0.68, 0.07, 0.095], hardwareMetal, [Math.cos(angle) * 0.31, 0.14, Math.sin(angle) * 0.31], chair);
        spoke.rotation.y = -angle;
        sphere([0.1, 0.09, 0.1], darkPlastic, [Math.cos(angle) * 0.66, 0.07, Math.sin(angle) * 0.66], chair);
    }

    const monitorCableCurve = new THREE.CatmullRomCurve3([
        new THREE.Vector3(0, 2.15, -0.96),
        new THREE.Vector3(0.05, 1.98, -0.98),
        new THREE.Vector3(0.54, 1.95, -1.02),
        new THREE.Vector3(1.08, 1.42, -1.06),
    ]);
    mesh(new THREE.TubeGeometry(monitorCableCurve, 28, 0.025, 7, false), darkPlastic);

    // Draw a live portfolio preview on the screen, including its animated black hole.
    const stars = Array.from({ length: 115 }, () => ({
        x: Math.random() * screenCanvas.width,
        y: Math.random() * screenCanvas.height,
        r: Math.random() * 1.7 + 0.3,
        alpha: Math.random() * 0.48 + 0.12,
    }));
    const screenName = host.dataset.profileName || 'Your Name';
    const screenRole = host.dataset.profileRole || 'Creative portfolio';
    let screenTick = 0;

    function roundRect(ctx, x, y, width, height, radius) {
        ctx.beginPath();
        ctx.roundRect(x, y, width, height, radius);
    }

    function drawScreen(time) {
        const ctx = screenContext;
        const width = screenCanvas.width;
        const height = screenCanvas.height;
        ctx.clearRect(0, 0, width, height);
        const background = ctx.createLinearGradient(0, 0, width, height);
        background.addColorStop(0, '#111022');
        background.addColorStop(0.54, '#15112b');
        background.addColorStop(1, '#080a18');
        ctx.fillStyle = background;
        ctx.fillRect(0, 0, width, height);

        const nebula = ctx.createRadialGradient(940, 410, 22, 940, 410, 440);
        nebula.addColorStop(0, 'rgba(142,73,255,.32)');
        nebula.addColorStop(0.4, 'rgba(95,48,192,.17)');
        nebula.addColorStop(1, 'rgba(62,39,119,0)');
        ctx.fillStyle = nebula;
        ctx.fillRect(450, 0, 830, 800);

        stars.forEach((star, index) => {
            ctx.globalAlpha = star.alpha * (0.72 + 0.28 * Math.sin(time * 0.9 + index));
            ctx.fillStyle = index % 3 ? '#dad5ff' : '#c697ff';
            ctx.beginPath();
            ctx.arc(star.x, star.y, star.r, 0, Math.PI * 2);
            ctx.fill();
        });
        ctx.globalAlpha = 1;

        // The black core and glowing accretion rings stay visible as part of the desk's 3D monitor.
        const cx = 950;
        const cy = 405;
        const pulse = Math.sin(time * 0.7) * 3;
        ctx.save();
        ctx.translate(cx, cy);
        ctx.rotate(time * 0.07);
        ctx.shadowBlur = 74;
        ctx.shadowColor = '#9f52ff';
        ctx.strokeStyle = 'rgba(173,103,255,.58)';
        ctx.lineWidth = 45;
        ctx.beginPath();
        ctx.ellipse(0, 0, 179 + pulse, 101 + pulse * 0.35, -0.12, Math.PI * 0.03, Math.PI * 1.88);
        ctx.stroke();
        ctx.shadowBlur = 32;
        ctx.strokeStyle = '#efb6ff';
        ctx.lineWidth = 13;
        ctx.beginPath();
        ctx.ellipse(0, 0, 146 + pulse, 73 + pulse * 0.35, -0.12, Math.PI * 0.05, Math.PI * 1.83);
        ctx.stroke();
        ctx.restore();
        const holeGlow = ctx.createRadialGradient(cx, cy, 36, cx, cy, 156);
        holeGlow.addColorStop(0, 'rgba(5,3,14,1)');
        holeGlow.addColorStop(0.47, 'rgba(7,4,17,1)');
        holeGlow.addColorStop(0.66, 'rgba(13,7,28,.97)');
        holeGlow.addColorStop(0.75, 'rgba(161,75,255,.32)');
        holeGlow.addColorStop(1, 'rgba(161,75,255,0)');
        ctx.fillStyle = holeGlow;
        ctx.beginPath();
        ctx.ellipse(cx, cy, 155, 117, 0, 0, Math.PI * 2);
        ctx.fill();
        ctx.fillStyle = '#03020a';
        ctx.beginPath();
        ctx.ellipse(cx, cy, 88, 72, 0, 0, Math.PI * 2);
        ctx.fill();

        // Miniature browser chrome and portfolio typography make the screen read as a real display.
        ctx.fillStyle = 'rgba(8,7,19,.82)';
        roundRect(ctx, 34, 30, 1212, 60, 16);
        ctx.fill();
        ctx.strokeStyle = 'rgba(185,144,255,.28)';
        ctx.lineWidth = 2;
        roundRect(ctx, 34, 30, 1212, 60, 16);
        ctx.stroke();
        ctx.fillStyle = '#b794ff';
        ctx.beginPath();
        ctx.moveTo(64, 70); ctx.lineTo(78, 43); ctx.lineTo(92, 70); ctx.lineTo(83, 70); ctx.lineTo(78, 60); ctx.lineTo(73, 70);
        ctx.closePath(); ctx.fill();
        ctx.font = '600 19px Inter, Arial, sans-serif';
        ctx.fillStyle = '#f4edff';
        ctx.fillText(screenName.toUpperCase().slice(0, 32), 112, 67);
        ctx.font = '15px Inter, Arial, sans-serif';
        ctx.fillStyle = '#d5c8ed';
        ctx.fillText('HOME     ABOUT     SKILLS     PROJECTS     CONTACT', 756, 66);
        ctx.fillStyle = '#b794ff';
        ctx.fillRect(755, 78, 62, 3);

        ctx.font = '700 17px Inter, Arial, sans-serif';
        ctx.fillStyle = '#c29cff';
        ctx.fillText('PORTFOLIO  /  HOME', 75, 173);
        ctx.font = '800 54px Inter, Arial, sans-serif';
        ctx.fillStyle = '#faf8ff';
        ctx.fillText(screenName.slice(0, 24), 74, 245);
        ctx.font = '600 25px Inter, Arial, sans-serif';
        ctx.fillStyle = '#d1c4eb';
        ctx.fillText(screenRole.slice(0, 35), 76, 292);
        ctx.font = '18px Inter, Arial, sans-serif';
        ctx.fillStyle = '#aaa1bd';
        ctx.fillText('Building thoughtful digital experiences.', 76, 343);
        ctx.fillStyle = '#6b35db';
        roundRect(ctx, 76, 378, 191, 49, 13); ctx.fill();
        ctx.fillStyle = '#fff';
        ctx.font = '600 17px Inter, Arial, sans-serif';
        ctx.fillText('VIEW PROJECTS  →', 95, 409);

        ctx.strokeStyle = 'rgba(197,157,255,.22)';
        ctx.lineWidth = 2;
        roundRect(ctx, 72, 475, 504, 207, 12); ctx.stroke();
        ctx.fillStyle = '#d7c3f4';
        ctx.font = '600 21px Inter, Arial, sans-serif';
        ctx.fillText('SELECTED SKILLS', 96, 515);
        ['HTML', 'CSS', 'JS', 'REACT', 'PHP', 'JAVA'].forEach((label, index) => {
            const x = 96 + (index % 3) * 142;
            const y = 544 + Math.floor(index / 3) * 57;
            ctx.fillStyle = 'rgba(157,107,238,.22)';
            roundRect(ctx, x, y, 119, 39, 10); ctx.fill();
            ctx.strokeStyle = 'rgba(191,152,255,.48)';
            roundRect(ctx, x, y, 119, 39, 10); ctx.stroke();
            ctx.fillStyle = '#eee6ff';
            ctx.font = '600 15px Inter, Arial, sans-serif';
            ctx.fillText(label, x + 16, y + 25);
        });
        screenTexture.needsUpdate = true;
    }

    function readCssColor(name, fallback) {
        const value = getComputedStyle(document.documentElement).getPropertyValue(`--${name}`).trim();
        try {
            return value ? new THREE.Color(value) : new THREE.Color(fallback);
        } catch {
            return new THREE.Color(fallback);
        }
    }

    function syncTheme() {
        palette['studio-bg'] = readCssColor('studio-bg', 0x1d1e28);
        palette['studio-accent'] = readCssColor('studio-accent', 0xa36cff);
        ['desk-top', 'desk-edge', 'desk-front', 'desk-side', 'desk-detail', 'monitor-frame', 'monitor-side'].forEach((key) => {
            palette[key] = readCssColor(key, palette[key]);
            (surfaces.get(key) || []).forEach((material) => material.color.copy(palette[key]));
        });
        floorMaterial.color.copy(palette['studio-bg']);
        hemi.groundColor.copy(palette['studio-bg']).multiplyScalar(0.45);
        rimLight.color.copy(palette['studio-accent']);
        renderer.render(scene, camera);
    }

    function resize() {
        const { width, height } = hero.getBoundingClientRect();
        if (!width || !height) return;
        const aspect = width / height;
        // Portrait screens need a much wider view of the desk. Increase the
        // camera distance progressively so both the desk and tower stay in frame.
        const nextFitScale = Math.min(2.8, 1 + Math.max(0, 1 - aspect) * 3.8);
        if (nextFitScale !== activeFitScale) {
            const radiusRatio = nextFitScale / activeFitScale;
            activeFitScale = nextFitScale;
            cameraRadius = baseCameraRadius * activeFitScale;
            targetRadius = THREE.MathUtils.clamp(targetRadius * radiusRatio, cameraRadius * 0.68, cameraRadius * 1.58);
            currentRadius = targetRadius;
        }
        camera.aspect = width / height;
        camera.updateProjectionMatrix();
        renderer.setSize(width, height, false);
        renderer.render(scene, camera);
    }

    let targetX = 0;
    let targetY = 0;
    let currentX = 0;
    let currentY = 0;
    let targetPitch = cameraPitch;
    let currentPitch = cameraPitch;
    let targetRadius = cameraRadius;
    let currentRadius = cameraRadius;
    let activeFitScale = 1;
    let isExploring = false;
    let isDraggingCamera = false;
    const activePointers = new Map();
    let pinchStartDistance = 0;
    let pinchStartRadius = 0;
    let presetView = 'front';
    let lastFrame = 0;
    let elapsed = 0;
    let launchedAt = 0;
    let launchStarted = false;
    let launchStartAzimuth = frontAzimuth;
    let launchStartPitch = cameraPitch;
    let launchStartRadius = cameraRadius;
    let launchNeedsCameraReturn = false;
    let launchDuration = 1350;
    let isInView = true;
    let disposed = false;
    let pausedForDesktop = false;

    function finishLaunch() {
        hero.dispatchEvent(new CustomEvent('creative:launch-complete', { bubbles: true }));
        pausedForDesktop = true;
        renderer.setAnimationLoop(null);
    }

    function launch() {
        if (launchStarted) return;
        if (document.fullscreenElement === hero) document.exitFullscreen?.();
        launchStarted = true;
        launchStartAzimuth = currentAzimuth;
        launchStartPitch = currentPitch;
        launchStartRadius = currentRadius;
        launchNeedsCameraReturn = Math.abs(launchStartAzimuth - frontAzimuth) > 0.04;
        launchDuration = launchNeedsCameraReturn ? 2050 : 1350;
        if (cameraToggle) cameraToggle.disabled = true;
        if (reducedMotion.matches) {
            finishLaunch();
            return;
        }
        launchedAt = performance.now();
        renderer.setAnimationLoop(animate);
    }

    function animate(now) {
        if (disposed) return;
        const delta = Math.min((now - lastFrame) / 1000 || 0, 0.05);
        lastFrame = now;
        elapsed += delta;
        screenTick += delta;
        currentX += (targetX - currentX) * 0.035;
        currentY += (targetY - currentY) * 0.035;
        studio.rotation.x = currentX;
        studio.rotation.y = currentY;
        ledViolet.emissiveIntensity = 2.25 + Math.sin(elapsed * 1.6) * 0.34;
        ledCyan.emissiveIntensity = 2.0 + Math.sin(elapsed * 1.6 + 1.4) * 0.3;
        ledBlue.emissiveIntensity = 1.45 + Math.sin(elapsed * 1.2 + 2.1) * 0.2;

        if (screenTick > 1 / 24) {
            drawScreen(elapsed);
            screenTick = 0;
        }

        if (launchStarted) {
            const progress = Math.min((now - launchedAt) / launchDuration, 1);
            const monitorCenter = display.getWorldPosition(new THREE.Vector3());
            const closePosition = monitorCenter.clone().add(new THREE.Vector3(0, 0.12, 3.72));

            if (launchNeedsCameraReturn && progress < 0.38) {
                const orbitProgress = progress / 0.38;
                const easedOrbit = orbitProgress * orbitProgress * (3 - 2 * orbitProgress);
                currentAzimuth = launchStartAzimuth + (frontAzimuth - launchStartAzimuth) * easedOrbit;
                currentPitch = launchStartPitch + (cameraPitch - launchStartPitch) * easedOrbit;
                currentRadius = launchStartRadius + (cameraRadius - launchStartRadius) * easedOrbit;
                camera.position.copy(cameraPositionAt(currentAzimuth, currentPitch, currentRadius));
                camera.lookAt(initialTarget);
            } else {
                const zoomProgress = launchNeedsCameraReturn ? (progress - 0.38) / 0.62 : progress;
                const easedZoom = 1 - Math.pow(1 - zoomProgress, 3);
                camera.position.lerpVectors(initialCamera, closePosition, easedZoom);
                const target = initialTarget.clone().lerp(monitorCenter, easedZoom);
                camera.lookAt(target);
            }
            if (progress >= 1) finishLaunch();
        } else {
            currentAzimuth += (targetAzimuth - currentAzimuth) * Math.min(1, delta * 3.4);
            currentPitch += (targetPitch - currentPitch) * Math.min(1, delta * 3.4);
            currentRadius += (targetRadius - currentRadius) * Math.min(1, delta * 3.4);
            camera.position.copy(cameraPositionAt(currentAzimuth, currentPitch, currentRadius));
            const look = initialTarget.clone();
            look.y += Math.sin(elapsed * 0.25) * 0.025;
            camera.lookAt(look);
        }

        renderer.render(scene, camera);
    }

    function pointerMove(event) {
        if (reducedMotion.matches || isExploring || launchStarted) return;
        const bounds = hero.getBoundingClientRect();
        targetY = ((event.clientX - bounds.left) / bounds.width - 0.5) * 0.16;
        targetX = ((event.clientY - bounds.top) / bounds.height - 0.5) * -0.09;
    }

    function resetPointer() {
        targetX = 0;
        targetY = 0;
    }

    function updateCameraViewLabel(label) {
        if (cameraViewLabel) cameraViewLabel.textContent = label;
    }

    function renderCameraImmediately() {
        currentAzimuth = targetAzimuth;
        currentPitch = targetPitch;
        currentRadius = targetRadius;
        camera.position.copy(cameraPositionAt(currentAzimuth, currentPitch, currentRadius));
        camera.lookAt(initialTarget);
        renderer.render(scene, camera);
    }

    function setExploreMode(enabled) {
        isExploring = enabled;
        hero.classList.toggle('is-exploring', enabled);
        exploreToggle?.setAttribute('aria-pressed', String(enabled));
        exploreToggle?.setAttribute('aria-label', enabled ? 'Stop exploring the 3D workstation' : 'Explore the 3D workstation');
        if (exploreActionLabel) exploreActionLabel.textContent = enabled ? 'Done exploring' : 'Explore 3D';
        if (exploreHint) exploreHint.hidden = !enabled;
        if (!enabled) {
            isDraggingCamera = false;
            activePointers.forEach((_, pointerId) => {
                if (canvas.hasPointerCapture(pointerId)) canvas.releasePointerCapture(pointerId);
            });
            activePointers.clear();
            canvas.classList.remove('is-dragging');
        }
    }

    function handleCameraPointerDown(event) {
        if (!isExploring || launchStarted || event.button !== 0) return;
        event.preventDefault();
        isDraggingCamera = true;
        activePointers.set(event.pointerId, { x: event.clientX, y: event.clientY });
        if (activePointers.size === 2) {
            const points = [...activePointers.values()];
            pinchStartDistance = Math.hypot(points[1].x - points[0].x, points[1].y - points[0].y);
            pinchStartRadius = targetRadius;
        }
        canvas.classList.add('is-dragging');
        canvas.setPointerCapture(event.pointerId);
    }

    function handleCameraPointerMove(event) {
        if (!isExploring || !activePointers.has(event.pointerId) || launchStarted) return;
        const prior = activePointers.get(event.pointerId);
        activePointers.set(event.pointerId, { x: event.clientX, y: event.clientY });
        if (activePointers.size > 1) {
            const points = [...activePointers.values()];
            const distance = Math.hypot(points[1].x - points[0].x, points[1].y - points[0].y);
            if (pinchStartDistance > 0 && distance > 0) {
                targetRadius = THREE.MathUtils.clamp(pinchStartRadius * pinchStartDistance / distance, cameraRadius * 0.68, cameraRadius * 1.58);
                if (reducedMotion.matches) renderCameraImmediately();
            }
            return;
        }
        targetAzimuth -= (event.clientX - prior.x) * 0.008;
        targetPitch = THREE.MathUtils.clamp(targetPitch + (event.clientY - prior.y) * 0.006, 0.12, 1.12);
        updateCameraViewLabel('Explore view');
        if (reducedMotion.matches) renderCameraImmediately();
    }

    function handleCameraPointerUp(event) {
        activePointers.delete(event.pointerId);
        isDraggingCamera = activePointers.size > 0;
        if (activePointers.size < 2) {
            pinchStartDistance = 0;
            pinchStartRadius = targetRadius;
        }
        canvas.classList.toggle('is-dragging', isDraggingCamera);
        if (canvas.hasPointerCapture(event.pointerId)) canvas.releasePointerCapture(event.pointerId);
    }

    function handleCameraWheel(event) {
        if (!isExploring || launchStarted) return;
        event.preventDefault();
        targetRadius = THREE.MathUtils.clamp(targetRadius + event.deltaY * 0.012, cameraRadius * 0.68, cameraRadius * 1.58);
        if (reducedMotion.matches) renderCameraImmediately();
    }

    function syncAnimation() {
        if (disposed || pausedForDesktop) return;
        if (document.hidden || !isInView || reducedMotion.matches) {
            renderer.setAnimationLoop(null);
            drawScreen(elapsed);
            renderer.render(scene, camera);
            return;
        }
        renderer.setAnimationLoop(animate);
    }

    function dispose() {
        disposed = true;
        renderer.setAnimationLoop(null);
        visibilityObserver.disconnect();
        sizeObserver.disconnect();
        themeObserver.disconnect();
        document.removeEventListener('visibilitychange', syncAnimation);
        reducedMotion.removeEventListener('change', syncAnimation);
        document.removeEventListener('fullscreenchange', syncFullscreenLabel);
        hero.removeEventListener('pointermove', pointerMove);
        hero.removeEventListener('pointerleave', resetPointer);
        hero.removeEventListener('creative:launch', launch);
        hero.removeEventListener('creative:return-to-scene', returnToScene);
        cameraToggle?.removeEventListener('click', toggleCameraView);
        exploreToggle?.removeEventListener('click', toggleExploreMode);
        canvas.removeEventListener('pointerdown', handleCameraPointerDown);
        canvas.removeEventListener('pointerup', handleCameraPointerUp);
        canvas.removeEventListener('pointercancel', handleCameraPointerUp);
        canvas.removeEventListener('wheel', handleCameraWheel);
        scene.traverse((object) => {
            object.geometry?.dispose();
            if (object.material) {
                const materials = Array.isArray(object.material) ? object.material : [object.material];
                materials.forEach((material) => material.dispose());
            }
        });
        screenTexture.dispose();
        shadowTexture.dispose();
        renderer.dispose();
    }

    const visibilityObserver = new IntersectionObserver((entries) => {
        isInView = entries[0]?.isIntersecting ?? true;
        syncAnimation();
    });
    const sizeObserver = new ResizeObserver(resize);
    const themeObserver = new MutationObserver(syncTheme);
    sizeObserver.observe(hero);
    visibilityObserver.observe(hero);
    themeObserver.observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });
    document.addEventListener('visibilitychange', syncAnimation);
    reducedMotion.addEventListener('change', syncAnimation);
    hero.addEventListener('pointermove', pointerMove, { passive: true });
    hero.addEventListener('pointerleave', resetPointer, { passive: true });
    hero.addEventListener('creative:launch', launch);

    const cameraToggle = hero.querySelector('[data-camera-toggle]');
    const cameraViewLabel = hero.querySelector('[data-camera-view]');
    const cameraActionLabel = hero.querySelector('[data-camera-action]');
    const exploreToggle = hero.querySelector('[data-camera-explore]');
    const exploreActionLabel = hero.querySelector('[data-explore-action]');
    const exploreHint = hero.querySelector('[data-explore-hint]');
    const fullscreenToggle = hero.querySelector('[data-workstation-fullscreen]');

    function toggleCameraView(event) {
        event.preventDefault();
        event.stopPropagation();
        if (launchStarted) return;

        const showRear = presetView !== 'rear';
        presetView = showRear ? 'rear' : 'front';
        targetAzimuth = showRear ? rearAzimuth : frontAzimuth;
        targetPitch = cameraPitch;
        targetRadius = cameraRadius;
        cameraToggle.setAttribute('aria-pressed', String(showRear));
        cameraToggle.setAttribute('aria-label', showRear ? 'Switch to front camera view' : 'Switch to rear camera view');
        if (cameraViewLabel) cameraViewLabel.textContent = showRear ? 'Rear view' : 'Front view';
        if (cameraActionLabel) cameraActionLabel.textContent = showRear ? 'View front' : 'View rear';

        if (reducedMotion.matches) {
            renderCameraImmediately();
        }
    }

    function toggleExploreMode(event) {
        event.preventDefault();
        event.stopPropagation();
        setExploreMode(!isExploring);
        if (reducedMotion.matches) renderCameraImmediately();
    }

    function returnToScene() {
        launchStarted = false;
        pausedForDesktop = false;
        if (cameraToggle) cameraToggle.disabled = false;
        targetAzimuth = frontAzimuth;
        targetPitch = cameraPitch;
        targetRadius = cameraRadius;
        presetView = 'front';
        if (cameraToggle) {
            cameraToggle.setAttribute('aria-pressed', 'false');
            cameraToggle.setAttribute('aria-label', 'Switch to rear camera view');
        }
        if (cameraActionLabel) cameraActionLabel.textContent = 'View rear';
        updateCameraViewLabel('Front view');
        renderCameraImmediately();
        syncAnimation();
    }

    function syncFullscreenLabel() {
        const isFullscreen = document.fullscreenElement === hero;
        if (!fullscreenToggle) return;
        fullscreenToggle.setAttribute('aria-pressed', String(isFullscreen));
        fullscreenToggle.setAttribute('aria-label', isFullscreen ? 'Exit fullscreen workstation view' : 'View workstation fullscreen');
        const label = fullscreenToggle.querySelector('[data-fullscreen-action]');
        if (label) label.textContent = isFullscreen ? 'Exit fullscreen' : 'Fullscreen';
    }

    function toggleFullscreen(event) {
        event.preventDefault();
        event.stopPropagation();
        if (document.fullscreenElement === hero) {
            if (document.exitFullscreen) document.exitFullscreen().catch(() => {});
            return;
        }
        if (hero.requestFullscreen) hero.requestFullscreen().catch(() => {});
    }

    cameraToggle?.addEventListener('click', toggleCameraView);
    exploreToggle?.addEventListener('click', toggleExploreMode);
    canvas.addEventListener('pointerdown', handleCameraPointerDown);
    canvas.addEventListener('pointermove', handleCameraPointerMove, { passive: true });
    canvas.addEventListener('pointerup', handleCameraPointerUp);
    canvas.addEventListener('pointercancel', handleCameraPointerUp);
    canvas.addEventListener('wheel', handleCameraWheel, { passive: false });
    hero.addEventListener('creative:return-to-scene', returnToScene);
    fullscreenToggle?.addEventListener('click', toggleFullscreen);
    document.addEventListener('fullscreenchange', syncFullscreenLabel);
    window.addEventListener('pagehide', dispose, { once: true });

    drawScreen(0);
    resize();
    syncTheme();
    hero.dataset.threeReady = 'true';
    syncAnimation();
});
