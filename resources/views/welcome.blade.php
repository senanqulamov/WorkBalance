<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'WorkBalance') }} - Private wellbeing space</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=space-grotesk:400,500,600,700|inter:300,400,500,600,700&display=swap" rel="stylesheet"/>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --bg-primary: #050814;
            --bg-secondary: #0a0f1e;
            --bg-tertiary: #141b2e;
            --bg-card: rgba(20, 27, 46, 0.6);
            --accent-primary: #6366f1;
            --accent-secondary: #8b5cf6;
            --accent-tertiary: #ec4899;
            --accent-glow: rgba(99, 102, 241, 0.4);
            --text-primary: #ffffff;
            --text-secondary: #cbd5e1;
            --text-muted: #94a3b8;
            --border-color: rgba(148, 163, 184, 0.08);
            --border-glow: rgba(99, 102, 241, 0.2);
        }

        /* Respect user preference for reduced motion */
        @media (prefers-reduced-motion: reduce) {
            * { animation-duration: 0.001ms !important; animation-iteration-count: 1 !important; transition-duration: 0.001ms !important; scroll-behavior: auto !important; }
            .particles, .animated-bg, .grid-overlay { display: none !important; }
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            overflow-x: hidden;
            line-height: 1.6;
            position: relative;
        }

        /* Advanced animated background with mesh gradient */
        .animated-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            background: radial-gradient(ellipse 80% 50% at 20% 40%, rgba(99, 102, 241, 0.15), transparent),
            radial-gradient(ellipse 60% 40% at 80% 30%, rgba(139, 92, 246, 0.12), transparent),
            radial-gradient(ellipse 50% 60% at 50% 70%, rgba(236, 72, 153, 0.08), transparent),
            radial-gradient(ellipse 70% 50% at 90% 80%, rgba(59, 130, 246, 0.1), transparent);
            animation: meshGradient 25s ease-in-out infinite;
            filter: blur(60px);
        }

        @keyframes meshGradient {
            0%, 100% {
                transform: translate(0, 0) scale(1) rotate(0deg);
                opacity: 1;
            }
            33% {
                transform: translate(30px, -30px) scale(1.1) rotate(5deg);
                opacity: 0.9;
            }
            66% {
                transform: translate(-20px, 20px) scale(0.95) rotate(-3deg);
                opacity: 0.85;
            }
        }

        /* Reduce mesh animation intensity when user prefers reduced motion */
        @media (prefers-reduced-motion: reduce) {
            .animated-bg { animation-duration: 120s !important; filter: blur(30px); }
        }

        /* Dynamic grid overlay with glow */
        .grid-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            background-image: linear-gradient(rgba(99, 102, 241, 0.05) 1.5px, transparent 1.5px),
            linear-gradient(90deg, rgba(99, 102, 241, 0.05) 1.5px, transparent 1.5px);
            background-size: 60px 60px;
            pointer-events: none;
            mask-image: radial-gradient(ellipse 100% 100% at 50% 50%, black 20%, transparent 80%);
        }

        /* Floating particles */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
            will-change: transform, opacity;
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: radial-gradient(circle, var(--accent-primary), transparent);
            border-radius: 50%;
            opacity: 0.25;
            /* use CSS variable for horizontal drift */
            --dx: 0px;
            animation: float-particle 18s infinite linear;
            will-change: transform, opacity;
        }

        @keyframes float-particle {
            0% {
                transform: translate3d(0, 0, 0);
                opacity: 0;
            }
            6% { opacity: 0.22; }
            94% { opacity: 0.22; }
            100% {
                transform: translate3d(var(--dx), -110vh, 0);
                opacity: 0;
            }
        }

        .container {
            position: relative;
            z-index: 1;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        /* Glassmorphic Header with enhanced blur */
        header {
            padding: 1.5rem 0;
            border-bottom: 1px solid var(--border-color);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            background: rgba(5, 8, 20, 0.75);
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.75rem;
            font-weight: 700;
            font-family: 'Space Grotesk', sans-serif;
            background: linear-gradient(135deg, #ffffff 0%, var(--accent-primary) 50%, var(--accent-secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.03em;
            position: relative;
            text-shadow: 0 0 30px var(--accent-glow);
        }

        .logo::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--accent-primary), transparent);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .logo:hover::after {
            opacity: 1;
        }

        .nav-links {
            display: flex;
            gap: 2.5rem;
            align-items: center;
        }

        .nav-link {
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-link:hover {
            color: var(--text-primary);
            transform: translateY(-2px);
        }

        .nav-link::before {
            content: '';
            position: absolute;
            bottom: -6px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--accent-primary), var(--accent-secondary));
            transition: width 0.3s ease;
            border-radius: 2px;
            box-shadow: 0 0 10px var(--accent-glow);
        }

        .nav-link:hover::before {
            width: 100%;
        }

        .btn {
            padding: 0.85rem 2rem;
            border-radius: 1rem;
            font-weight: 600;
            font-size: 0.95rem;
            text-decoration: none;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            color: white;
            box-shadow: 0 8px 30px var(--accent-glow), inset 0 1px 0 rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 45px var(--accent-glow), inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }

        .btn-secondary {
            background: var(--bg-card);
            backdrop-filter: blur(10px);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-secondary:hover {
            background: rgba(20, 27, 46, 0.8);
            border-color: var(--border-glow);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.15);
        }

        /* Enhanced Hero Section with parallax */
        .hero {
            padding: 10rem 0 8rem;
            text-align: center;
            position: relative;
            will-change: transform;
            transform: translate3d(0,0,0);
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1.5rem;
            background: var(--bg-card);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border-glow);
            border-radius: 3rem;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 2.5rem;
            animation: fadeInDown 0.8s ease, pulse 3s ease-in-out infinite;
            box-shadow: 0 0 30px var(--accent-glow), inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }

        @keyframes pulse {
            0%, 100% {
                box-shadow: 0 0 30px var(--accent-glow), inset 0 1px 0 rgba(255, 255, 255, 0.1);
            }
            50% {
                box-shadow: 0 0 45px var(--accent-glow), inset 0 1px 0 rgba(255, 255, 255, 0.2);
            }
        }

        .badge-icon {
            width: 20px;
            height: 20px;
            animation: spin 3s linear infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero-title {
            font-size: 5.5rem;
            font-weight: 700;
            font-family: 'Space Grotesk', sans-serif;
            line-height: 1.05;
            margin-bottom: 2rem;
            letter-spacing: -0.04em;
            animation: fadeInUp 0.8s ease 0.2s both;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero-title .gradient-text {
            background: linear-gradient(135deg,
            var(--accent-primary) 0%,
            var(--accent-secondary) 40%,
            var(--accent-tertiary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            background-size: 200% 200%;
            animation: gradientShift 5s ease infinite;
            display: inline-block;
            position: relative;
        }

        @keyframes gradientShift {
            0%, 100% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
        }

        .hero-title .gradient-text::after {
            content: attr(data-text);
            position: absolute;
            left: 0;
            top: 0;
            z-index: -1;
            background: linear-gradient(135deg,
            var(--accent-primary) 0%,
            var(--accent-secondary) 40%,
            var(--accent-tertiary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            filter: blur(20px);
            opacity: 0.5;
        }

        .hero-description {
            font-size: 1.35rem;
            color: var(--text-secondary);
            max-width: 750px;
            margin: 0 auto 3.5rem;
            line-height: 1.8;
            animation: fadeInUp 0.8s ease 0.4s both;
            font-weight: 400;
        }

        .hero-actions {
            display: flex;
            gap: 1.25rem;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeInUp 0.8s ease 0.6s both;
        }

        /* Advanced Features Section with scroll reveal */
        .features {
            padding: 8rem 0;
            position: relative;
        }

        .section-header {
            text-align: center;
            margin-bottom: 5rem;
        }

        .section-badge {
            display: inline-block;
            padding: 0.5rem 1.2rem;
            background: var(--bg-card);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border-color);
            border-radius: 2rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--accent-primary);
            margin-bottom: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .section-title {
            font-size: 3.5rem;
            font-weight: 700;
            font-family: 'Space Grotesk', sans-serif;
            margin-bottom: 1.5rem;
            letter-spacing: -0.03em;
            background: linear-gradient(135deg, var(--text-primary) 0%, var(--text-secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .section-subtitle {
            font-size: 1.25rem;
            color: var(--text-secondary);
            max-width: 650px;
            margin: 0 auto;
            line-height: 1.7;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin-top: 4rem;
        }

        .feature-card {
            background: var(--bg-card);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border-color);
            border-radius: 2rem;
            padding: 3rem;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary), var(--accent-tertiary));
            opacity: 0;
            transition: opacity 0.5s ease;
        }

        .feature-card::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: radial-gradient(circle, var(--accent-glow), transparent);
            transform: translate(-50%, -50%);
            transition: width 0.6s ease, height 0.6s ease;
        }

        .feature-card:hover {
            transform: translateY(-12px) scale(1.02);
            border-color: var(--border-glow);
            box-shadow: 0 30px 80px rgba(99, 102, 241, 0.3),
            inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }

        .feature-card:hover::before {
            opacity: 0.08;
        }

        .feature-card:hover::after {
            width: 300px;
            height: 300px;
        }

        .feature-icon {
            width: 4rem;
            height: 4rem;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary));
            border-radius: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 2rem;
            position: relative;
            z-index: 1;
            box-shadow: 0 10px 40px var(--accent-glow);
            transition: all 0.4s ease;
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 15px 60px var(--accent-glow);
        }

        .feature-title {
            font-size: 1.65rem;
            font-weight: 700;
            font-family: 'Space Grotesk', sans-serif;
            margin-bottom: 1rem;
            position: relative;
            z-index: 1;
            color: var(--text-primary);
        }

        .feature-description {
            color: var(--text-secondary);
            line-height: 1.8;
            position: relative;
            z-index: 1;
            font-size: 1.05rem;
        }

        /* Enhanced Stats Section with counters */
        .stats {
            padding: 6rem 0;
            background: var(--bg-secondary);
            border-top: 1px solid var(--border-color);
            border-bottom: 1px solid var(--border-color);
            position: relative;
            overflow: hidden;
        }

        .stats::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 30% 50%, rgba(99, 102, 241, 0.05), transparent 60%);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 4rem;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .stat-item {
            padding: 2rem;
            transition: transform 0.3s ease;
        }

        .stat-item:hover {
            transform: scale(1.05);
        }

        .stat-number {
            font-size: 4rem;
            font-weight: 800;
            font-family: 'Space Grotesk', sans-serif;
            background: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-secondary) 50%, var(--accent-tertiary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 1rem;
            display: inline-block;
            position: relative;
        }

        .stat-number::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: linear-gradient(90deg, transparent, var(--accent-primary), transparent);
            border-radius: 2px;
        }

        .stat-label {
            color: var(--text-secondary);
            font-size: 1.15rem;
            font-weight: 500;
            margin-top: 1rem;
        }

        /* Premium CTA Section */
        .cta {
            padding: 10rem 0;
            text-align: center;
            position: relative;
        }

        .cta-content {
            background: var(--bg-card);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border-glow);
            border-radius: 3rem;
            padding: 6rem 4rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 30px 100px rgba(99, 102, 241, 0.2);
        }

        .cta-content::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: conic-gradient(
                from 0deg at 50% 50%,
                var(--accent-primary) 0deg,
                var(--accent-secondary) 120deg,
                var(--accent-tertiary) 240deg,
                var(--accent-primary) 360deg
            );
            animation: rotateBg 20s linear infinite;
            opacity: 0.1;
        }

        @keyframes rotateBg {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        .cta-content::after {
            /* Simplified gradient border for broader compatibility (removed mask-composite which caused IDE and cross-browser issues) */
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 3rem;
            padding: 2px;
            background: linear-gradient(135deg, var(--accent-primary), var(--accent-secondary), var(--accent-tertiary));
            opacity: 0.25;
            pointer-events: none;
            mix-blend-mode: overlay;
        }

        .cta-title {
            font-size: 3.5rem;
            font-weight: 700;
            font-family: 'Space Grotesk', sans-serif;
            margin-bottom: 2rem;
            position: relative;
            z-index: 1;
            letter-spacing: -0.03em;
            background: linear-gradient(135deg, var(--text-primary) 0%, var(--text-secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .cta-description {
            font-size: 1.35rem;
            color: var(--text-secondary);
            max-width: 650px;
            margin: 0 auto 3rem;
            position: relative;
            z-index: 1;
            line-height: 1.8;
        }

        .cta-actions {
            display: flex;
            gap: 1.25rem;
            justify-content: center;
            flex-wrap: wrap;
            position: relative;
            z-index: 1;
        }

        /* Enhanced Footer */
        footer {
            padding: 4rem 0 2rem;
            border-top: 1px solid var(--border-color);
            background: var(--bg-secondary);
            position: relative;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 2rem;
        }

        .footer-text {
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        .footer-links {
            display: flex;
            gap: 2.5rem;
        }

        .footer-link {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            position: relative;
        }

        .footer-link::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 0;
            width: 0;
            height: 1px;
            background: var(--accent-primary);
            transition: width 0.3s ease;
        }

        .footer-link:hover {
            color: var(--text-primary);
        }

        .footer-link:hover::after {
            width: 100%;
        }

        /* Scroll progress indicator */
        .scroll-progress {
            position: fixed;
            top: 0;
            left: 0;
            width: 0%;
            height: 3px;
            background: linear-gradient(90deg, var(--accent-primary), var(--accent-secondary), var(--accent-tertiary));
            z-index: 1000;
            transition: width 0.1s ease;
            box-shadow: 0 0 10px var(--accent-glow);
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .hero-title {
                font-size: 4rem;
            }

            .section-title {
                font-size: 2.75rem;
            }
        }

        @media (max-width: 768px) {
            .nav-links {
                gap: 1.5rem;
            }

            .hero {
                padding: 6rem 0 5rem;
            }

            .hero-title {
                font-size: 3rem;
            }

            .hero-description {
                font-size: 1.125rem;
            }

            .features {
                padding: 5rem 0;
            }

            .section-title {
                font-size: 2.25rem;
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .cta-content {
                padding: 4rem 2rem;
                border-radius: 2rem;
            }

            .cta-title {
                font-size: 2.25rem;
            }

            .footer-content {
                flex-direction: column;
                text-align: center;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 2rem;
            }

            .stat-number {
                font-size: 3rem;
            }
        }

        @media (max-width: 480px) {
            .hero-title {
                font-size: 2.25rem;
            }

            .hero-actions {
                flex-direction: column;
                width: 100%;
            }

            .hero-actions .btn {
                width: 100%;
                justify-content: center;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .stat-number {
                font-size: 2.5rem;
            }

            .section-title {
                font-size: 1.875rem;
            }

            .cta-title {
                font-size: 1.875rem;
            }
        }

        /* Smooth reveal animations on scroll */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }

        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* Hover glow effect */
        .glow-on-hover {
            position: relative;
        }

        .glow-on-hover::before {
            content: '';
            position: absolute;
            inset: -2px;
            background: linear-gradient(45deg, var(--accent-primary), var(--accent-secondary), var(--accent-tertiary));
            border-radius: inherit;
            opacity: 0;
            filter: blur(15px);
            transition: opacity 0.3s ease;
            z-index: -1;
        }

        .glow-on-hover:hover::before {
            opacity: 0.7;
        }

        /* Floating animation for cards */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .feature-card:nth-child(1) { animation: float 6s ease-in-out infinite; }
        .feature-card:nth-child(2) { animation: float 6s ease-in-out infinite 0.5s; }
        .feature-card:nth-child(3) { animation: float 6s ease-in-out infinite 1s; }
        .feature-card:nth-child(4) { animation: float 6s ease-in-out infinite 1.5s; }
        .feature-card:nth-child(5) { animation: float 6s ease-in-out infinite 2s; }
        .feature-card:nth-child(6) { animation: float 6s ease-in-out infinite 2.5s; }

        /* Small CSS tweaks to improve parallax performance and particle animation */
        .hero {
            will-change: transform;
            transform: translate3d(0,0,0);
        }
    </style>
</head>
<body>
<div class="scroll-progress" id="scrollProgress"></div>
<div class="animated-bg" aria-hidden="true"></div>
<div class="grid-overlay" aria-hidden="true"></div>
<div class="particles" id="particles" aria-hidden="true"></div>
<noscript>
    <style>.particles, .animated-bg, .grid-overlay { display: none !important; }</style>
</noscript>

<!-- Header -->
<header>
    <div class="container">
        <div class="header-content">
            <div class="logo">
                <img src="{{ asset('/assets/images/fluxa_transparent.png') }}" style="height: 6vh" alt="Logo dark"/>
            </div>
            <nav class="nav-links">
                <a href="#overview">Care</a>
                <a href="#features">Wellbeing</a>
                <a href="#workflow">Flow</a>
                <a href="#cta" class="cta-button">Enter WorkBalance</a>
            </nav>
        </div>
    </div>
</header>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-badge">
            <span class="badge-icon">✨</span>
            WorkBalance • Calm, private wellbeing
        </div>
        <h1 class="hero-title">
            A calm space for<br>
            <span class="gradient-text" data-text="everyday care">everyday care</span>
        </h1>
        <p class="hero-description">
            A therapist-inspired companion for employees to check in, process feelings, and take gentle steps forward. Employers see only aggregated signals—never personal notes.
        </p>
        <div class="hero-actions">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-primary glow-on-hover">
                    Enter HumanOps Intelligence →
                </a>
            @else
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn btn-primary glow-on-hover">
                        Start WorkBalance →
                    </a>
                @endif
                <a href="{{ route('login') }}" class="btn btn-secondary">
                    Sign in
                </a>
            @endauth
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-badge">Wellbeing</span>
            <h2 class="section-title">HumanOps & Care Flows</h2>
            <p class="section-subtitle">
                Gentle employee support with privacy-first HumanOps insights for employers.
            </p>
        </div>
        <div class="features-grid">
            <div class="feature-card reveal glow-on-hover">
                <div class="feature-icon">🧘‍♀️</div>
                <h3 class="feature-title">Daily Check-ins</h3>
                <p class="feature-description">Mood + energy check-ins that stay private to each employee.</p>
            </div>
            <div class="feature-card reveal glow-on-hover">
                <div class="feature-icon">🛡️</div>
                <h3 class="feature-title">HumanOps Insights</h3>
                <p class="feature-description">Aggregated stress and engagement signals by team—no individual data.</p>
            </div>
            <div class="feature-card reveal glow-on-hover">
                <div class="feature-icon">🌿</div>
                <h3 class="feature-title">Therapeutic Paths</h3>
                <p class="feature-description">Guided flows with validation, regulation, and micro-steps.</p>
            </div>
            <div class="feature-card reveal glow-on-hover">
                <div class="feature-icon">💬</div>
                <h3 class="feature-title">Anonymous Feedback</h3>
                <p class="feature-description">Employees can share signals without exposing identity.</p>
            </div>
            <div class="feature-card reveal glow-on-hover">
                <div class="feature-icon">📈</div>
                <h3 class="feature-title">Burnout Guardrails</h3>
                <p class="feature-description">Cohort-level burnout thresholds and calm nudges for managers.</p>
            </div>
            <div class="feature-card reveal glow-on-hover">
                <div class="feature-icon">🔒</div>
                <h3 class="feature-title">Privacy by Default</h3>
                <p class="feature-description">Strict separation: employee journaling never leaves their space.</p>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item reveal">
                <div class="stat-number">25K+</div>
                <div class="stat-label">Daily check-ins</div>
            </div>
            <div class="stat-item reveal">
                <div class="stat-number">180</div>
                <div class="stat-label">Cohorts protected</div>
            </div>
            <div class="stat-item reveal">
                <div class="stat-number">99%</div>
                <div class="stat-label">Privacy adherence</div>
            </div>
            <div class="stat-item reveal">
                <div class="stat-number">12m</div>
                <div class="stat-label">Avg. relief time</div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta">
    <div class="container">
        <div class="cta-content reveal">
            <h2 class="cta-title">Ready to support your people?</h2>
            <p class="cta-description">
                Invite employees into a private, therapist-inspired space while you receive calm, anonymized HumanOps signals to guide care.
            </p>
            <div class="cta-actions">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary glow-on-hover">
                        Open HumanOps Intelligence →
                     </a>
                @else
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-primary glow-on-hover">
                            Start WorkBalance →
                         </a>
                    @endif
                    <a href="{{ route('login') }}" class="btn btn-secondary">
                        Sign in
                     </a>
                @endauth
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer>
    <div class="container">
        <div class="footer-content">
            <div class="footer-text">
                © {{ date('Y') }} {{ config('app.name', 'DPanel') }}. All rights reserved.
            </div>
            <div class="footer-links">
                <a href="#" class="footer-link">Privacy Policy</a>
                <a href="#" class="footer-link">Terms of Service</a>
                <a href="#" class="footer-link">Contact</a>
            </div>
        </div>
    </div>
</footer>

<!-- Minimal, robust JS to fix scroll jank, reveal timing, particle positioning and stats animation -->
<script>
    (function(){
        'use strict';

        // Scroll progress bar (optimized)
        const progressEl = document.getElementById('scrollProgress');
        function updateProgress(){
            if(!progressEl) return;
            const scrollTop = document.documentElement.scrollTop || document.body.scrollTop || 0;
            const docHeight = Math.max(document.documentElement.scrollHeight - document.documentElement.clientHeight, 1);
            const pct = Math.max(0, Math.min(100, (scrollTop / docHeight) * 100));
            progressEl.style.width = pct + '%';
        }
        // Use rAF for smooth updates
        let progressTick = false;
        window.addEventListener('scroll', function(){
            if(!progressTick){
                progressTick = true;
                requestAnimationFrame(function(){ updateProgress(); progressTick = false; });
            }
        }, {passive:true});
        updateProgress();

        // Reveal elements using IntersectionObserver (fallback included)
        const reveals = Array.from(document.querySelectorAll('.reveal'));
        function onReveal(entries, obs){
            entries.forEach(entry => {
                if(entry.isIntersecting){
                    entry.target.classList.add('active');
                    obs.unobserve(entry.target);
                    // Animate stat numbers inside revealed block
                    const nums = entry.target.querySelectorAll && entry.target.querySelectorAll('.stat-number') || [];
                    nums.forEach(animateStat);
                }
            });
        }
        if(window.IntersectionObserver){
            const io = new IntersectionObserver(onReveal, { root:null, rootMargin: '0px 0px -120px 0px', threshold: 0.08 });
            reveals.forEach(el => io.observe(el));
        } else {
            // fallback
            const checkReveal = function(){
                reveals.forEach(el => {
                    const r = el.getBoundingClientRect();
                    if(r.top < window.innerHeight - 120){
                        el.classList.add('active');
                    }
                });
            };
            window.addEventListener('scroll', checkReveal, {passive:true});
            checkReveal();
        }

        // Stat number animation utility
        function parseMetric(text){
            text = (text||'').trim();
            if(!text) return null;
            // Handle K+ formats and %
            if(/k\+$/i.test(text)){
                const num = parseFloat(text.replace(/k\+$/,''));
                if(isNaN(num)) return null;
                return {value: Math.round(num*1000), format: v => Math.round(v/1000)+'K+'};
            }
            if(/\+$/i.test(text)){
                const num = parseFloat(text.replace(/\+$/,''));
                if(isNaN(num)) return null;
                return {value: Math.round(num), format: v => Math.round(v)+'+'};
            }
            if(/%$/.test(text)){
                const num = parseFloat(text.replace(/%$/,''));
                if(isNaN(num)) return null;
                return {value: Math.round(num), format: v => Math.round(v)+'%'};
            }
            // plain number
            const num = parseFloat(text.replace(/[,^\s]/g, ''));
            if(isNaN(num)) return null;
            return {value: Math.round(num), format: v => String(Math.round(v))};
        }

        function animateStat(el){
            try{
                if(!el || el.dataset.animated) return;
                const parsed = parseMetric(el.textContent || el.innerText || '');
                if(!parsed) return;
                el.dataset.animated = '1';
                const {value, format} = parsed;
                const duration = 1100;
                const start = performance.now();
                const startVal = 0;
                function step(now){
                    const t = Math.min(1, (now - start)/duration);
                    // easeOutCubic
                    const eased = 1 - Math.pow(1 - t, 3);
                    const cur = Math.round(startVal + (value - startVal) * eased);
                    el.textContent = format(cur);
                    if(t < 1) requestAnimationFrame(step);
                    else el.textContent = format(value);
                }
                requestAnimationFrame(step);
            }catch(e){console.error(e)}
        }

        // Setup stats to animate when visible even if not inside a reveal block
        document.querySelectorAll('.stat-number').forEach(el => {
            // If stat is already visible - animate immediately
            const rect = el.getBoundingClientRect();
            if(rect.top < window.innerHeight - 80){
                animateStat(el);
            }
        });

        // Improved particle generation (constrained for performance)
        function generateParticles(){
            // Do not generate particles if user prefers reduced motion
            if(window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
            const container = document.getElementById('particles');
            if(!container) return;
            container.innerHTML = '';
            // smaller particle density: ~1 per 80px width, capped 24
            const count = Math.min(24, Math.max(8, Math.round((window.innerWidth || 1024)/80)));
            for(let i=0;i<count;i++){
                const p = document.createElement('div');
                p.className = 'particle';
                p.style.left = (Math.random()*100)+'%';
                // start below viewport to avoid sudden popping
                p.style.top = (100 + Math.random()*25)+'%';
                // horizontal drift
                const dx = (Math.random()*220 - 110).toFixed(2) + 'px';
                p.style.setProperty('--dx', dx);
                p.style.animationDelay = (Math.random()*6)+'s';
                p.style.animationDuration = (7 + Math.random()*10)+'s';
                p.style.opacity = (0.06 + Math.random()*0.22).toString();
                container.appendChild(p);
            }
        }
        // initial generation
        generateParticles();

        // Parallax hero - constrained and GPU accelerated (desktop only)
        (function setupParallax(){
            const hero = document.querySelector('.hero');
            if(!hero) return;
            // disable on small screens for better UX
            if(window.innerWidth < 920) return;
            let raf = null;
            function onScroll(){
                if(raf) return;
                raf = requestAnimationFrame(()=>{
                    const sc = window.scrollY || window.pageYOffset || 0;
                    // subtle effect and limit maximum offset
                    const max = 60;
                    const offset = Math.round(Math.max(-max, Math.min(max, sc * 0.04)));
                    hero.style.transform = 'translate3d(0,'+offset+'px,0)';
                    raf = null;
                });
            }
            window.addEventListener('scroll', onScroll, {passive:true});
            // initial
            onScroll();
            // keep parallax responsive to resize (enable/disable)
            window.addEventListener('resize', function(){
                if(window.innerWidth < 920){ hero.style.transform = ''; }
            }, {passive:true});
        })();

        // Ensure all interactive features are initiated on resize change (recreate particles)
        let resizeTimer; window.addEventListener('resize', function(){
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function(){
                // recreate particles
                const container = document.getElementById('particles');
                if(container){ container.innerHTML = ''; }
                // small delay to avoid layout thrashing
                setTimeout(function(){
                    const evt = new Event('load'); window.dispatchEvent(evt);
                    // regenerate particles after resize
                    generateParticles();
                }, 150);
            }, 200);
        }, {passive:true});

    })();
</script>
</body>
</html>
