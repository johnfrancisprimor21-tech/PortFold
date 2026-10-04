import * as THREE from 'three';

const motionPreference = window.matchMedia('(prefers-reduced-motion: reduce)');
const coarsePointer = window.matchMedia('(pointer: coarse)');

document.querySelectorAll('[data-creative-scene]').forEach((sceneHost) => {
    const hero = sceneHost.closest('.hero');

    if (!hero) {
        return;
    }

    let renderer;

    try {
        renderer = new THREE.WebGLRenderer({
            alpha: true,
            antialias: !coarsePointer.matches,
            powerPreference: 'low-power',
        });
    } catch {
        return;
    }

    const canvas = renderer.domElement;
    canvas.className = 'hero-canvas';
    canvas.setAttribute('aria-hidden', 'true');
    sceneHost.appendChild(canvas);
    renderer.setClearColor(0x000000, 0);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, coarsePointer.matches ? 1.2 : 1.5));
    renderer.outputColorSpace = THREE.SRGBColorSpace;

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(42, 1, 0.1, 80);
    camera.position.z = 9;

    const ribbonSpecs = [
        { y: -0.9, z: 0.1, phase: 0.4, intensity: 0.56, color: [0x7650e8, 0xc276ff] },
        { y: 0.18, z: -0.5, phase: 2.1, intensity: 0.46, color: [0x6c50dc, 0xa369ff] },
        { y: 1.28, z: -0.1, phase: 4.8, intensity: 0.52, color: [0xa45cff, 0x7478f2] },
    ];

    const ribbons = ribbonSpecs.map((spec, index) => {
        const uniforms = {
            uTime: { value: 0 },
            uPhase: { value: spec.phase },
            uSpan: { value: 14 },
            uIntensity: { value: spec.intensity },
            uColorA: { value: new THREE.Color(spec.color[0]) },
            uColorB: { value: new THREE.Color(spec.color[1]) },
        };

        const geometry = new THREE.PlaneGeometry(1, 0.34, 240, 8);
        const material = new THREE.ShaderMaterial({
            uniforms,
            transparent: true,
            depthWrite: false,
            blending: THREE.AdditiveBlending,
            side: THREE.DoubleSide,
            toneMapped: false,
            vertexShader: `
                uniform float uTime;
                uniform float uPhase;
                uniform float uSpan;
                varying vec2 vUv;
                varying float vWave;

                void main() {
                    vUv = uv;
                    float worldX = (uv.x - 0.5) * uSpan;
                    float broadWave = sin(worldX * 0.62 + uTime * 0.34 + uPhase) * 0.36;
                    float detailWave = sin(worldX * 1.18 - uTime * 0.22 + uPhase * 1.6) * 0.12;
                    float sweep = sin(worldX * 0.27 + uTime * 0.18 + uPhase) * 0.2;
                    vec3 p = position;
                    p.x = worldX;
                    p.y += broadWave + detailWave + sweep;
                    p.z += cos(worldX * 0.36 - uTime * 0.2 + uPhase) * 0.28;
                    vWave = broadWave + detailWave;

                    gl_Position = projectionMatrix * modelViewMatrix * vec4(p, 1.0);
                }
            `,
            fragmentShader: `
                uniform float uTime;
                uniform float uPhase;
                uniform float uIntensity;
                uniform vec3 uColorA;
                uniform vec3 uColorB;
                varying vec2 vUv;
                varying float vWave;

                void main() {
                    float distanceFromCenter = abs(vUv.y - 0.5);
                    float softEdge = 1.0 - smoothstep(0.02, 0.5, distanceFromCenter);
                    float leftFade = mix(0.18, 1.0, smoothstep(0.08, 0.76, vUv.x));
                    float colorShift = smoothstep(0.05, 0.92, vUv.x + sin(vUv.x * 8.0 + uPhase) * 0.12);
                    float shimmer = pow(max(0.0, sin(vUv.x * 18.0 + uTime * 0.58 + uPhase)), 7.0);
                    float breathing = 0.68 + 0.32 * sin(uTime * 0.35 + uPhase + vWave * 1.5);
                    vec3 color = mix(uColorA, uColorB, colorShift);
                    color += vec3(0.3, 0.24, 0.48) * shimmer;
                    float alpha = (0.12 + shimmer * 0.22) * softEdge * leftFade * breathing * uIntensity;

                    gl_FragColor = vec4(color, alpha);
                    #include <colorspace_fragment>
                }
            `,
        });

        const ribbon = new THREE.Mesh(geometry, material);
        ribbon.position.set(0, spec.y, spec.z - index * 0.04);
        scene.add(ribbon);

        return { uniforms, ribbon, spec };
    });

    const particleCount = coarsePointer.matches ? 440 : 760;
    const particlePositions = new Float32Array(particleCount * 3);
    const particleColors = new Float32Array(particleCount * 3);
    const particleSeeds = new Float32Array(particleCount * 3);
    const particlePhases = new Float32Array(particleCount);
    const warm = new THREE.Color(0xe7a0ff);
    const cool = new THREE.Color(0x9caaff);

    for (let index = 0; index < particleCount; index += 1) {
        const offset = index * 3;
        const x = Math.random();
        const y = Math.random();
        const depth = Math.random();
        const color = warm.clone().lerp(cool, x * 0.85);
        particleSeeds[offset] = x;
        particleSeeds[offset + 1] = y;
        particleSeeds[offset + 2] = depth;
        particlePhases[index] = Math.random() * Math.PI * 2;
        particleColors[offset] = color.r;
        particleColors[offset + 1] = color.g;
        particleColors[offset + 2] = color.b;
    }

    const particleGeometry = new THREE.BufferGeometry();
    const positionAttribute = new THREE.BufferAttribute(particlePositions, 3);
    positionAttribute.setUsage(THREE.DynamicDrawUsage);
    particleGeometry.setAttribute('position', positionAttribute);
    particleGeometry.setAttribute('color', new THREE.BufferAttribute(particleColors, 3));

    const particles = new THREE.Points(
        particleGeometry,
        new THREE.PointsMaterial({
            size: coarsePointer.matches ? 0.032 : 0.027,
            sizeAttenuation: true,
            vertexColors: true,
            transparent: true,
            opacity: 0.78,
            depthWrite: false,
            blending: THREE.AdditiveBlending,
            toneMapped: false,
        }),
    );
    scene.add(particles);

    const blackHoleUniforms = {
        uTime: { value: 0 },
        uAspect: { value: 1 },
    };
    const blackHoleMaterial = new THREE.ShaderMaterial({
        uniforms: blackHoleUniforms,
        transparent: true,
        depthWrite: false,
        side: THREE.DoubleSide,
        toneMapped: false,
        vertexShader: `
            varying vec2 vUv;

            void main() {
                vUv = uv;
                gl_Position = vec4(position.xy * 2.0, 0.0, 1.0);
            }
        `,
        fragmentShader: `
            uniform float uTime;
            uniform float uAspect;
            varying vec2 vUv;

            float hash21(vec2 p) {
                p = fract(p * vec2(123.34, 456.21));
                p += dot(p, p + 45.32);
                return fract(p.x * p.y);
            }

            float noise(vec2 p) {
                vec2 cell = floor(p);
                vec2 local = fract(p);
                local = local * local * (3.0 - 2.0 * local);
                float a = hash21(cell);
                float b = hash21(cell + vec2(1.0, 0.0));
                float c = hash21(cell + vec2(0.0, 1.0));
                float d = hash21(cell + vec2(1.0, 1.0));
                return mix(mix(a, b, local.x), mix(c, d, local.x), local.y);
            }

            float fbm(vec2 p) {
                float value = 0.0;
                float amplitude = 0.5;
                mat2 turn = mat2(1.62, -1.18, 1.18, 1.62);
                for (int octave = 0; octave < 4; octave++) {
                    value += noise(p) * amplitude;
                    p = turn * p + vec2(9.7);
                    amplitude *= 0.5;
                }
                return value;
            }

            void main() {
                vec2 center = vec2(0.44, 0.91);
                vec2 offset = vec2((vUv.x - center.x) * uAspect, vUv.y - center.y);
                float radius = length(offset);
                if (radius > 0.39) discard;

                float angle = atan(offset.y, offset.x);
                float time = uTime;
                float flow = fbm(vec2(angle * 3.0 + log(max(radius, 0.012)) * 3.8 - time * 0.07,
                                      radius * 34.0 - time * 0.16));
                float plasma = fbm(vec2(angle * 7.5 - time * 0.11 + flow * 1.8,
                                        radius * 104.0 - time * 0.42 + flow * 2.6));
                float detail = fbm(vec2(angle * 17.0 + plasma * 2.4 - time * 0.2,
                                        radius * 210.0 - time * 0.7));
                float distortion = (flow - 0.5) * 0.022
                    + (plasma - 0.5) * 0.009
                    + sin(angle * 4.0 + time * 0.12) * 0.006;

                float horizonRadius = 0.112;
                float horizonEdge = horizonRadius + distortion * 0.18;
                float core = 1.0 - smoothstep(horizonEdge * 0.985, horizonEdge * 1.045, radius);
                vec3 color = mix(vec3(0.003, 0.001, 0.01), vec3(0.012, 0.003, 0.028), smoothstep(0.035, horizonRadius, radius));
                float alpha = core * 0.995;

                float photonRadius = horizonRadius + 0.015 + distortion * 0.25;
                float photonRing = exp(-pow((radius - photonRadius) / 0.0042, 2.0));
                float photonGlow = exp(-pow((radius - (photonRadius + 0.012)) / 0.035, 2.0));
                float lowerSide = 1.0 - smoothstep(-0.88, 0.18, sin(angle));
                float brightSide = 0.68 + lowerSide * 0.32;

                vec2 diskOffset = vec2(offset.x, offset.y * 1.28);
                float diskRadius = length(diskOffset);
                float diskDistance = abs(diskRadius - (0.171 + distortion));
                float diskWidth = 0.018 + plasma * 0.009;
                float diskBand = exp(-pow(diskDistance / diskWidth, 2.0));
                float gaps = smoothstep(0.16, 0.44, flow) * (0.68 + 0.32 * smoothstep(0.18, 0.7, detail));
                float brokenArc = 0.58 + 0.42 * smoothstep(0.12, 0.94, 0.5 + 0.5 * sin(angle * 5.0 + flow * 8.0 - time * 0.12));
                float filament = pow(max(0.0, sin(angle * 15.0 + flow * 11.0 - time * 0.25)), 9.0);
                float accretion = diskBand * gaps * brokenArc * (0.7 + 0.3 * brightSide);
                float diskAlpha = clamp(accretion * (0.74 + 0.25 * plasma) + photonRing * brightSide, 0.0, 1.0);

                vec3 deepViolet = vec3(0.28, 0.025, 0.82);
                vec3 hotViolet = vec3(0.78, 0.2, 1.0);
                vec3 plasmaColor = mix(deepViolet, hotViolet, smoothstep(0.24, 0.72, plasma));
                plasmaColor = mix(plasmaColor, vec3(1.0, 0.78, 1.0), clamp(0.2 + brightSide * 0.3 + filament * 0.48, 0.0, 0.92));
                plasmaColor = mix(plasmaColor, vec3(1.0, 0.94, 1.0), clamp(photonRing * 0.98, 0.0, 0.98));
                color = mix(color, plasmaColor, diskAlpha);
                color += vec3(1.16, 0.62, 1.5) * photonGlow * (0.9 + lowerSide * 0.5);
                alpha = max(alpha, max(diskAlpha, photonGlow * 0.72));

                float cloudWarp = (flow - 0.5) * 0.052 + (detail - 0.5) * 0.024;
                float cloudRadius = exp(-pow((diskRadius - (0.235 + cloudWarp)) / 0.082, 2.0));
                float cloudCut = smoothstep(horizonRadius + 0.046, horizonRadius + 0.112, diskRadius);
                float cloudWisps = smoothstep(0.3, 0.72, flow) * (0.32 + 0.68 * smoothstep(0.22, 0.76, plasma));
                float cloudFilaments = smoothstep(0.32, 0.82, detail) * (0.56 + 0.44 * filament);
                float cloudAlpha = cloudRadius * cloudCut * cloudWisps * cloudFilaments * 1.02;
                vec3 cloudColor = mix(vec3(0.22, 0.025, 0.72), vec3(0.72, 0.25, 1.0), smoothstep(0.3, 0.76, plasma));
                color = mix(color, cloudColor, cloudAlpha * (1.0 - core));
                color += vec3(0.72, 0.28, 1.2) * cloudAlpha * 0.74;
                alpha = max(alpha, cloudAlpha * (1.0 - core));

                gl_FragColor = vec4(color, alpha);
                #include <colorspace_fragment>
            }
        `,
    });
    const blackHole = new THREE.Mesh(new THREE.PlaneGeometry(1, 1), blackHoleMaterial);
    blackHole.position.z = 0.8;
    blackHole.scale.set(1, 1, 1);
    blackHole.renderOrder = 2;
    scene.add(blackHole);

    let frameTime = 0;
    let lastFrame = 0;
    let isInView = true;
    let targetX = 0;
    let targetY = 0;
    let particleWorldHeight = 0;
    let disposed = false;

    function resize() {
        const { width, height } = hero.getBoundingClientRect();

        if (!width || !height) {
            return;
        }

        camera.aspect = width / height;
        camera.updateProjectionMatrix();
        renderer.setSize(width, height, false);

        const worldHeight = 2 * camera.position.z * Math.tan(THREE.MathUtils.degToRad(camera.fov / 2));
        const worldWidth = worldHeight * camera.aspect;
        particleWorldHeight = worldHeight;
        blackHoleUniforms.uAspect.value = width / height;
        blackHole.scale.set(worldWidth * 1.08, worldHeight * 1.08, 1);
        ribbons.forEach(({ uniforms }) => {
            uniforms.uSpan.value = worldWidth * 1.12;
        });

        for (let index = 0; index < particleCount; index += 1) {
            const offset = index * 3;
            particlePositions[offset] = (particleSeeds[offset] - 0.5) * worldWidth;
            particlePositions[offset + 1] = (particleSeeds[offset + 1] - 0.5) * worldHeight;
            particlePositions[offset + 2] = -1.8 - particleSeeds[offset + 2] * 5;
        }

        positionAttribute.needsUpdate = true;
        renderStaticFrame();
    }

    function renderStaticFrame() {
        if (!disposed) {
            renderer.render(scene, camera);
        }
    }

    function animate(time) {
        const delta = Math.min((time - lastFrame) / 1000 || 0, 0.05);
        lastFrame = time;
        frameTime += delta;

        ribbons.forEach(({ uniforms, ribbon, spec }, index) => {
            uniforms.uTime.value = frameTime;
            ribbon.position.y = spec.y + Math.sin(frameTime * 0.14 + spec.phase) * 0.1;
            ribbon.rotation.z = Math.sin(frameTime * 0.11 + spec.phase) * 0.018;
            uniforms.uPhase.value = spec.phase + Math.sin(frameTime * 0.09 + index) * 0.12;
        });

        blackHoleUniforms.uTime.value = frameTime;

        for (let index = 0; index < particleCount; index += 1) {
            const offset = index * 3;
            particlePositions[offset + 1] = (particleSeeds[offset + 1] - 0.5) * particleWorldHeight
                + Math.sin(frameTime * 0.18 + particlePhases[index]) * 0.045;
        }

        positionAttribute.needsUpdate = true;
        scene.rotation.y += (targetY - scene.rotation.y) * 0.018;
        scene.rotation.x += (targetX - scene.rotation.x) * 0.018;
        renderer.render(scene, camera);
    }

    function syncAnimation() {
        if (disposed) {
            return;
        }

        if (motionPreference.matches || !isInView || document.hidden) {
            renderer.setAnimationLoop(null);
            renderStaticFrame();
            return;
        }

        renderer.setAnimationLoop(animate);
    }

    function updatePointer(event) {
        if (motionPreference.matches) {
            return;
        }

        const bounds = hero.getBoundingClientRect();
        targetX = ((event.clientY - bounds.top) / bounds.height - 0.5) * 0.035;
        targetY = ((event.clientX - bounds.left) / bounds.width - 0.5) * 0.045;
    }

    function resetPointer() {
        targetX = 0;
        targetY = 0;
    }

    function dispose() {
        disposed = true;
        renderer.setAnimationLoop(null);
        visibilityObserver.disconnect();
        sizeObserver.disconnect();
        document.removeEventListener('visibilitychange', syncAnimation);
        motionPreference.removeEventListener('change', syncAnimation);
        hero.removeEventListener('pointermove', updatePointer);
        hero.removeEventListener('pointerleave', resetPointer);
        scene.traverse((object) => {
            if (object.geometry) {
                object.geometry.dispose();
            }

            if (object.material) {
                const materials = Array.isArray(object.material) ? object.material : [object.material];
                materials.forEach((material) => material.dispose());
            }
        });
        renderer.dispose();
    }

    const visibilityObserver = new IntersectionObserver((entries) => {
        isInView = entries[0]?.isIntersecting ?? true;
        syncAnimation();
    });
    const sizeObserver = new ResizeObserver(resize);

    sizeObserver.observe(hero);
    visibilityObserver.observe(hero);
    document.addEventListener('visibilitychange', syncAnimation);
    motionPreference.addEventListener('change', syncAnimation);
    hero.addEventListener('pointermove', updatePointer, { passive: true });
    hero.addEventListener('pointerleave', resetPointer, { passive: true });
    window.addEventListener('pagehide', dispose, { once: true });

    resize();
    syncAnimation();
});
