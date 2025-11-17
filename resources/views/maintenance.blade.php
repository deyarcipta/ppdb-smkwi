<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Mode - PPDB SMK WI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow: hidden;
            position: relative;
            padding: 20px;
        }

        /* Animated Background */
        .bg-animation {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.4) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 119, 198, 0.4) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(120, 219, 255, 0.3) 0%, transparent 50%);
            animation: gradientShift 8s ease-in-out infinite;
        }

        @keyframes gradientShift {
            0%, 100% { 
                transform: scale(1) rotate(0deg);
                filter: hue-rotate(0deg);
            }
            50% { 
                transform: scale(1.1) rotate(180deg);
                filter: hue-rotate(45deg);
            }
        }

        /* Particle Background */
        .particles {
            position: absolute;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 50%;
            animation: floatParticle 15s infinite linear;
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
        }

        @keyframes floatParticle {
            0% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateY(-100px) rotate(360deg); opacity: 0; }
        }

        .maintenance-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 480px;
            padding: 20px;
            perspective: 1000px;
        }

        .maintenance-card {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(25px);
            border-radius: 30px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 
                0 25px 50px rgba(0, 0, 0, 0.25),
                inset 0 1px 0 rgba(255, 255, 255, 0.3);
            overflow: hidden;
            position: relative;
            transform-style: preserve-3d;
            transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            padding: 40px 30px;
        }

        .maintenance-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            animation: shine 3s ease-in-out infinite;
        }

        @keyframes shine {
            0% { left: -100%; }
            100% { left: 100%; }
        }

        .card-glow {
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, #667eea, #764ba2, #f093fb, #667eea);
            border-radius: 32px;
            z-index: -1;
            filter: blur(20px);
            opacity: 0.7;
            animation: glowPulse 2s ease-in-out infinite alternate;
        }

        @keyframes glowPulse {
            0% { opacity: 0.5; transform: scale(0.98); }
            100% { opacity: 0.8; transform: scale(1.02); }
        }

        .maintenance-icon {
            font-size: 5.5rem;
            background: linear-gradient(135deg, #fff, #e2e8ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1.5rem;
            filter: drop-shadow(0 5px 15px rgba(0, 0, 0, 0.3));
            animation: iconFloat 3s ease-in-out infinite;
            text-align: center;
        }

        @keyframes iconFloat {
            0%, 100% { transform: translateY(0px) scale(1); }
            50% { transform: translateY(-10px) scale(1.05); }
        }

        .maintenance-title {
            font-size: 2.5rem;
            font-weight: 900;
            background: linear-gradient(135deg, #fff, #e2e8ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
            line-height: 1.1;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .maintenance-subtitle {
            font-size: 1.4rem;
            font-weight: 600;
            background: linear-gradient(135deg, #e2e8ff, #ffffff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .description {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.1rem;
            margin-bottom: 2rem;
            line-height: 1.6;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .progress-section {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .progress-container {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .progress-label {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1rem;
            margin-right: 15px;
            min-width: 140px;
            font-weight: 600;
        }

        .progress-bar-container {
            flex: 1;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            height: 12px;
            overflow: hidden;
            position: relative;
        }

        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
            border-radius: 10px;
            width: 75%;
            animation: progressAnimation 2s ease-in-out infinite alternate;
            position: relative;
        }

        .progress-bar::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            animation: progressShine 1.5s ease-in-out infinite;
        }

        @keyframes progressAnimation {
            0% { width: 70%; }
            100% { width: 80%; }
        }

        @keyframes progressShine {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .progress-percentage {
            font-weight: 700;
            color: white;
            margin-left: 15px;
            min-width: 40px;
            font-size: 1rem;
        }

        .status-list {
            margin-bottom: 2rem;
        }

        .status-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .status-item:last-child {
            border-bottom: none;
        }

        .status-label {
            color: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            font-weight: 600;
            font-size: 1rem;
        }

        .status-icon {
            margin-right: 10px;
            font-size: 1.3rem;
        }

        .performance-icon {
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .features-icon {
            background: linear-gradient(135deg, #f093fb, #f5576c);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .status-value {
            font-weight: 600;
            color: white;
            font-size: 1rem;
        }

        .countdown-section {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.3), rgba(118, 75, 162, 0.3));
            border-radius: 20px;
            padding: 1.5rem;
            text-align: center;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
        }

        .countdown-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: conic-gradient(transparent, rgba(255, 255, 255, 0.1), transparent);
            animation: rotate 4s linear infinite;
        }

        @keyframes rotate {
            100% { transform: rotate(360deg); }
        }

        .countdown-label {
            color: rgba(255, 255, 255, 0.8);
            font-size: 1rem;
            margin-bottom: 0.8rem;
            position: relative;
            z-index: 2;
            font-weight: 600;
        }

        .countdown-timer {
            font-size: 2.2rem;
            font-weight: 800;
            font-family: 'Courier New', monospace;
            color: white;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 2;
            letter-spacing: 3px;
        }

        /* Loading dots animation */
        .loading-dots::after {
            content: '';
            animation: dots 1.5s steps(4, end) infinite;
        }

        @keyframes dots {
            0%, 20% { content: '.'; }
            40% { content: '..'; }
            60% { content: '...'; }
            80%, 100% { content: ''; }
        }

        @media (max-width: 576px) {
            .maintenance-container {
                max-width: 350px;
                padding: 15px;
            }
            
            .maintenance-card {
                padding: 30px 20px;
            }
            
            .maintenance-title {
                font-size: 2rem;
            }
            
            .maintenance-subtitle {
                font-size: 1.2rem;
            }
            
            .progress-label {
                min-width: 120px;
                font-size: 0.9rem;
            }
            
            .countdown-timer {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div class="bg-animation"></div>
    
    <!-- Particle System -->
    <div class="particles" id="particles"></div>

    <!-- Main Content -->
    <div class="maintenance-container">
        <div class="card-glow"></div>
        <div class="maintenance-card">
            <div class="text-center">
                <!-- Animated Icon -->
                <div class="maintenance-icon">
                    <i class="bi bi-tools"></i>
                </div>
                
                <!-- Title -->
                <h1 class="maintenance-title">Maintenance</h1>
                <h2 class="maintenance-subtitle">In Progress</h2>
                
                <!-- Description -->
                <p class="description">
                    We're performing critical system upgrades<span class="loading-dots"></span>
                </p>
            </div>

            <!-- Progress Section -->
            <div class="progress-section">
                <div class="progress-container">
                    <div class="progress-label">System Update</div>
                    <div class="progress-bar-container">
                        <div class="progress-bar"></div>
                    </div>
                    <div class="progress-percentage">75%</div>
                </div>
            </div>

            <!-- Status List -->
            <div class="status-list">
                <div class="status-item">
                    <div class="status-label">
                        <i class="bi bi-lightning-charge status-icon performance-icon"></i>
                        Performance Boost
                    </div>
                    <div class="status-value">Security Update</div>
                </div>
                <div class="status-item">
                    <div class="status-label">
                        <i class="bi bi-rocket-takeoff status-icon features-icon"></i>
                        New Features
                    </div>
                    <div class="status-value">Optimization</div>
                </div>
            </div>

            <!-- Countdown -->
            <div class="countdown-section">
                <div class="countdown-label">Estimated Completion</div>
                <div class="countdown-timer" id="countdown">02:59:36</div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Create particles
        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            const particleCount = 15;
            
            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                
                // Random properties
                const size = Math.random() * 6 + 2;
                const left = Math.random() * 100;
                const animationDuration = Math.random() * 20 + 10;
                const animationDelay = Math.random() * 5;
                
                particle.style.width = `${size}px`;
                particle.style.height = `${size}px`;
                particle.style.left = `${left}%`;
                particle.style.animationDuration = `${animationDuration}s`;
                particle.style.animationDelay = `${animationDelay}s`;
                
                particlesContainer.appendChild(particle);
            }
        }

        // Countdown Timer
        function startCountdown() {
            let countdownTime = 3 * 60 * 60; // 3 hours in seconds
            const countdownElement = document.getElementById('countdown');
            
            function updateCountdown() {
                const hours = Math.floor(countdownTime / 3600);
                const minutes = Math.floor((countdownTime % 3600) / 60);
                const seconds = countdownTime % 60;
                
                countdownElement.textContent = 
                    `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                
                if (countdownTime > 0) {
                    countdownTime--;
                    setTimeout(updateCountdown, 1000);
                } else {
                    countdownElement.textContent = "00:00:00";
                    countdownElement.style.background = "linear-gradient(135deg, #28a745, #20c997)";
                    countdownElement.style.webkitBackgroundClip = "text";
                    countdownElement.style.webkitTextFillColor = "transparent";
                }
            }
            
            updateCountdown();
        }

        // 3D Mouse Effect
        function init3DEffect() {
            const card = document.querySelector('.maintenance-card');
            
            document.addEventListener('mousemove', (e) => {
                const xAxis = (window.innerWidth / 2 - e.pageX) / 25;
                const yAxis = (window.innerHeight / 2 - e.pageY) / 25;
                
                card.style.transform = `rotateY(${xAxis}deg) rotateX(${yAxis}deg)`;
            });
            
            // Reset on mouse leave
            document.addEventListener('mouseleave', () => {
                card.style.transform = 'rotateY(0deg) rotateX(0deg)';
            });
        }

        // Initialize everything
        document.addEventListener('DOMContentLoaded', () => {
            createParticles();
            startCountdown();
            init3DEffect();
        });
    </script>
</body>
</html>