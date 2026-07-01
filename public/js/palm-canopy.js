document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('palm-canopy-canvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');

    let width, height;
    function resize() {
        width = window.innerWidth;
        height = window.innerHeight;
        canvas.width = width;
        canvas.height = height;
    }
    window.addEventListener('resize', resize);
    resize();

    // Palm Tree Configuration
    const PALM_COLORS = ['#244a07', '#3b6d11', '#4a8a15', '#2c5909'];
    const NUM_TREES = 40; // Number of trees in the canopy forest

    class Leaf {
        constructor(frond, positionAlongFrond, length, angleOffset) {
            this.frond = frond;
            this.position = positionAlongFrond; // 0 to 1
            this.length = length;
            this.baseAngleOffset = angleOffset;
            this.windX = 0;
            this.windY = 0;
        }

        draw(ctx, startX, startY, cpX, cpY, stemTipX, stemTipY, frondAngle, swayOffset, mouse) {
            // Leaf attachment position using exact Quadratic Bezier curve of the stem
            const t = this.position;
            const invT = 1 - t;
            const lx = invT * invT * startX + 2 * invT * t * cpX + t * t * stemTipX;
            const ly = invT * invT * startY + 2 * invT * t * cpY + t * t * stemTipY;

            // Angle of the leaf
            const angle = frondAngle + this.baseAngleOffset + swayOffset;

            // Interactive brush effect based on local mouse distance
            const dx = lx - mouse.x;
            const dy = ly - mouse.y;
            const dist = Math.sqrt(dx * dx + dy * dy);
            
            // Radius of sweep increased (120 -> 200)
            if (dist < 200) {
                const force = (200 - dist) / 200;
                // Stronger push (0.15 -> 0.4)
                this.windX += mouse.vx * force * 0.4;
                this.windY += mouse.vy * force * 0.4;
            }

            // Limit max local wind to prevent chaotic stretching on very fast swipes
            const maxLocalWind = 80;
            const windMag = Math.sqrt(this.windX * this.windX + this.windY * this.windY);
            if (windMag > maxLocalWind) {
                this.windX = (this.windX / windMag) * maxLocalWind;
                this.windY = (this.windY / windMag) * maxLocalWind;
            }

            // Slower friction for longer spring back / sway (0.88 -> 0.94)
            this.windX *= 0.94;
            this.windY *= 0.94;

            // Apply leaf's local wind
            const leafWindX = this.windX * this.position;
            const leafWindY = this.windY * this.position;

            const tipX = lx + Math.cos(angle) * this.length + leafWindX;
            const tipY = ly + Math.sin(angle) * this.length + leafWindY;
            
            // Draw a simple bezier curve for the leaf
            ctx.beginPath();
            ctx.moveTo(lx, ly);
            // control point to make it curved gracefully
            const leafCpX = lx + Math.cos(angle - 0.3) * (this.length * 0.5) + leafWindX * 0.5;
            const leafCpY = ly + Math.sin(angle - 0.3) * (this.length * 0.5) + leafWindY * 0.5;
            ctx.quadraticCurveTo(leafCpX, leafCpY, tipX, tipY);
            ctx.stroke();
        }
    }

    class Frond {
        constructor(tree, baseAngle, length) {
            this.tree = tree;
            this.baseAngle = baseAngle;
            this.length = length;
            this.leaves = [];
            this.windX = 0; // Local frond wind
            this.windY = 0;
            
            // Generate leaves
            const numLeaves = Math.floor(length / 10); // Leaf density (reduced 50%)
            for (let i = 0; i < numLeaves; i++) {
                const pos = 0.15 + (i / numLeaves) * 0.85; // Leaves start a bit away from center
                const leafLength = (1 - Math.pow(Math.abs(pos - 0.5) * 2, 2)) * length * 0.3 + 10; // Parabolic length, longest in middle
                
                // Left leaf
                this.leaves.push(new Leaf(this, pos, leafLength, -1.0));
                // Right leaf
                this.leaves.push(new Leaf(this, pos, leafLength, 1.0));
            }
        }

        draw(ctx, time, ambientWind, mouse) {
            // Calculate base angle with idle sway based on time and spatial position
            const sway = Math.sin(time * 0.0002 + this.tree.x * 0.01 + this.baseAngle) * 0.015;
            const currentAngle = this.baseAngle + sway;

            // Frond base (tree center)
            const sx = this.tree.x;
            const sy = this.tree.y;

            // Wind effect on this frond (pushes the tip) - ONLY AMBIENT WIND initially
            const ambientForceX = ambientWind.x * this.tree.flexibility;
            const ambientForceY = ambientWind.y * this.tree.flexibility;

            let tipX = sx + Math.cos(currentAngle) * this.length + ambientForceX;
            let tipY = sy + Math.sin(currentAngle) * this.length + ambientForceY;

            // Local cursor wind for the frond tip (stiff reaction)
            const dx = tipX - mouse.x;
            const dy = tipY - mouse.y;
            const dist = Math.sqrt(dx * dx + dy * dy);
            
            if (dist < 200) {
                const force = (200 - dist) / 200;
                this.windX += mouse.vx * force * 0.2; // Increased flexibility (was 0.05)
                this.windY += mouse.vy * force * 0.2;
            }

            // Stiff spring back (decays faster, so it doesn't sway as wildly as leaves)
            this.windX *= 0.85;
            this.windY *= 0.85;

            // Apply frond wind to tip
            tipX += this.windX;
            tipY += this.windY;

            // Control point only takes ambient force, causing the tip to bow naturally when brushed
            const cpX = sx + Math.cos(currentAngle) * this.length * 0.5 + ambientForceX * 0.5;
            const cpY = sy + Math.sin(currentAngle) * this.length * 0.5 + ambientForceY * 0.5;

            // Draw stem with tapering thickness (wide at base, thin at tip)
            ctx.lineCap = 'round';
            ctx.lineJoin = 'round';
            const segments = 12;
            let lastX = sx;
            let lastY = sy;
            for (let i = 1; i <= segments; i++) {
                const t = i / segments;
                const invT = 1 - t;
                const curX = invT * invT * sx + 2 * invT * t * cpX + t * t * tipX;
                const curY = invT * invT * sy + 2 * invT * t * cpY + t * t * tipY;

                ctx.beginPath();
                ctx.moveTo(lastX, lastY);
                ctx.lineTo(curX, curY);
                ctx.lineWidth = 7 * (1 - t) + 1; // 8 at base, 1 at tip
                ctx.strokeStyle = lerpColor(this.tree.stemColor, '#cc0000', errorPulse);
                ctx.stroke();

                lastX = curX;
                lastY = curY;
            }

            // Draw leaves
            ctx.lineWidth = 1.2;
            ctx.strokeStyle = lerpColor(this.tree.leafColor, '#ff1111', errorPulse);
            
            for (const leaf of this.leaves) {
                leaf.draw(ctx, sx, sy, cpX, cpY, tipX, tipY, currentAngle, sway, mouse);
            }
        }
    }

    class Tree {
        constructor(x, y, radius, colorIndex) {
            this.x = x;
            this.y = y;
            this.radius = radius;
            this.flexibility = 0.3 + Math.random() * 0.4;
            
            // Adjust leaf color to be slightly lighter or vibrant than stem
            this.leafColor = PALM_COLORS[colorIndex]; 
            this.stemColor = '#1a3605'; // Darker stem
            
            this.fronds = [];
            // Sedikit variasi jumlah agar lebih organik (9-11)
            const numFronds = 9 + Math.floor(Math.random() * 3); 
            for (let i = 0; i < numFronds; i++) {
                // Sudut dasar rata, namun variasi acaknya (jitter) diperbesar agar tidak terlalu rapi
                const angle = (i / numFronds) * Math.PI * 2 + (Math.random() - 0.5) * 0.35;
                // Panjang bervariasi antara 80% hingga 105% dari radius
                const length = radius * (0.8 + Math.random() * 0.25);
                this.fronds.push(new Frond(this, angle, length));
            }
        }

        draw(ctx, time, ambientWind, mouse) {
            for (const frond of this.fronds) {
                frond.draw(ctx, time, ambientWind, mouse);
            }
            // Draw center crown (the heart of the palm tree)
            ctx.beginPath();
            ctx.arc(this.x, this.y, this.radius * 0.05, 0, Math.PI * 2);
            ctx.fillStyle = this.stemColor;
            ctx.fill();
        }
    }

    // Initialize forest
    const forest = [];
    function initForest() {
        forest.length = 0;
        
        // 1 Pohon di tengah layar
        const radius = Math.min(width, height) * 0.6 + 100; 
        
        // Tengah
        forest.push(new Tree(width / 2, height / 2, radius, 1));
    }
    
    // We need to wait for first resize to know width/height
    initForest();

    // Re-init on significant resize
    window.addEventListener('resize', () => {
        if (Math.abs(width - window.innerWidth) > 100 || Math.abs(height - window.innerHeight) > 100) {
            initForest();
        }
    });

    // Wind / Cursor Interaction
    let mouse = { x: width/2, y: height/2, vx: 0, vy: 0 };
    let wind = { x: 0, y: 0 };
    let lastMouse = { x: width/2, y: height/2 };
    
    // Login Error Pulse State
    let errorPulse = 0;
    let errorPulsePhase = 0; // 0: idle, 1: fade in, 2: fade out
    window.addEventListener('loginFailed', () => {
        errorPulsePhase = 1; // Start fade in from current value
    });

    // Color utility for blending hex colors
    function lerpColor(color1, color2, factor) {
        if (factor <= 0) return color1;
        if (factor >= 1) return color2;
        const c1 = [parseInt(color1.slice(1,3), 16), parseInt(color1.slice(3,5), 16), parseInt(color1.slice(5,7), 16)];
        const c2 = [parseInt(color2.slice(1,3), 16), parseInt(color2.slice(3,5), 16), parseInt(color2.slice(5,7), 16)];
        const r = Math.round(c1[0] + factor * (c2[0] - c1[0]));
        const g = Math.round(c1[1] + factor * (c2[1] - c1[1]));
        const b = Math.round(c1[2] + factor * (c2[2] - c1[2]));
        return `rgb(${r}, ${g}, ${b})`;
    }
    
    window.addEventListener('mousemove', (e) => {
        mouse.x = e.clientX;
        mouse.y = e.clientY;
        
        mouse.vx = mouse.x - lastMouse.x;
        mouse.vy = mouse.y - lastMouse.y;
        
        lastMouse.x = mouse.x;
        lastMouse.y = mouse.y;
        
        // Add cursor velocity to wind (Cursor = Wind)
        wind.x += mouse.vx * 0.15;
        wind.y += mouse.vy * 0.15;
    });

    function animate(time) {
        ctx.clearRect(0, 0, width, height);

        if (errorPulsePhase === 1) {
            errorPulse += 0.033; // Fade in to red (~0.5s at 60fps)
            if (errorPulse >= 1.0) {
                errorPulse = 1.0;
                errorPulsePhase = 2; // Move to fade out
            }
        } else if (errorPulsePhase === 2) {
            errorPulse -= 0.008; // Fade out back to green (~2s at 60fps)
            if (errorPulse <= 0) {
                errorPulse = 0;
                errorPulsePhase = 0; // Idle
            }
        }

        // Wind decay (friction)
        wind.x *= 0.92;
        wind.y *= 0.92;
        
        // Limit max wind to prevent chaotic explosion
        const maxWind = 150;
        const windMagnitude = Math.sqrt(wind.x * wind.x + wind.y * wind.y);
        if (windMagnitude > maxWind) {
            wind.x = (wind.x / windMagnitude) * maxWind;
            wind.y = (wind.y / windMagnitude) * maxWind;
        }
        
        const ambientWindX = Math.sin(time * 0.0001) * 1.5;
        const ambientWindY = Math.cos(time * 0.00015) * 1.5;

        const ambientWind = { x: ambientWindX, y: ambientWindY };
        
        for (const tree of forest) {
            tree.draw(ctx, time, ambientWind, mouse);
        }

        // Decay mouse velocity so it doesn't push infinitely
        mouse.vx *= 0.5;
        mouse.vy *= 0.5;

        requestAnimationFrame(animate);
    }
    
    requestAnimationFrame(animate);
});
