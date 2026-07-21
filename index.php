<?php
// Configuration file paths
$dbInfoFile = 'pages/dbInfo.php';
$configFile = 'auth/config.php';

// Check if the setup has been done
if (!file_exists($dbInfoFile) || !file_exists($configFile)) {
    header('Location: install.php');
    exit;
}

// If setup is done, include the database connection file
include 'pages/dbInfo.php';

// You can now use the connect_database function if needed
$con = connect_database();

// Now you can display your index page content
include 'auth/function.php';

?>

<!doctype html>
<html lang="en-US">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $site_settings['brand_name']; ?> - UPI Payment Solutions</title>
    <meta name="description" content="<?php echo $site_settings['brand_name']; ?> - The Smart Way for Online Payment Solution. Collect UPI Payments Easy & Fastest Solutions." />
    <meta name="robots" content="max-image-preview:large" />
    <link rel="icon" href="<?php echo $site_settings['logo_url']; ?>" sizes="32x32" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@4.1.0/fonts/remixicon.css" />

    <style>
        /* ============================================
           PAY - Ultra Premium Landing Page Stylesheet
           ============================================ */

        :root {
            --primary: #5b21b6;
            --primary-dark: #4c1d95;
            --primary-light: #8b5cf6;
            --primary-glow: rgba(139, 92, 246, 0.4);
            
            --secondary: #ec4899;
            --secondary-light: #f472b6;
            
            --accent: #14b8a6;
            --accent-light: #2dd4bf;
            
            --bg-dark: #030014;
            --bg-section: #050520;
            --bg-card: rgba(255, 255, 255, 0.02);
            --bg-glass: rgba(255, 255, 255, 0.04);
            --bg-glass-hover: rgba(255, 255, 255, 0.07);
            
            --text-primary: #f8fafc;
            --text-secondary: #cbd5e1;
            --text-muted: #64748b;
            
            --border-glass: rgba(255, 255, 255, 0.08);
            --border-highlight: rgba(139, 92, 246, 0.3);
            
            --shadow-lg: 0 20px 40px rgba(0, 0, 0, 0.5);
            --shadow-glow: 0 0 30px rgba(139, 92, 246, 0.3);
            
            --radius-md: 14px;
            --radius-lg: 20px;
            --radius-xl: 28px;
            --radius-full: 9999px;
            --transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg-dark);
            color: var(--text-primary);
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
            line-height: 1.6;
        }

        a { text-decoration: none; color: inherit; transition: all 0.3s ease; }
        img { max-width: 100%; height: auto; }
        .container { max-width: 1280px; margin: 0 auto; padding: 0 24px; }

        /* ---- Animated BG Effect ---- */
        .page-bg { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 0; pointer-events: none; background: radial-gradient(circle at center, #050520 0%, #030014 100%); }
        .grid-overlay { position: absolute; inset: 0; background-image: linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px); background-size: 50px 50px; opacity: 0.5; mask-image: linear-gradient(to bottom, rgba(0,0,0,1) 0%, rgba(0,0,0,0) 80%); -webkit-mask-image: linear-gradient(to bottom, rgba(0,0,0,1) 0%, rgba(0,0,0,0) 80%); }
        .page-bg .orb { position: absolute; border-radius: 50%; filter: blur(120px); opacity: 0.5; }
        .page-bg .orb-1 { width: 600px; height: 600px; background: var(--primary); top: -100px; left: -200px; animation: floatOrb 25s ease-in-out infinite alternate; }
        .page-bg .orb-2 { width: 500px; height: 500px; background: var(--secondary); bottom: 10%; right: -150px; animation: floatOrb 30s ease-in-out infinite alternate-reverse; }

        @keyframes floatOrb { 0% { transform: translate(0, 0) scale(1); } 100% { transform: translate(100px, 50px) scale(1.2); } }

        /* ---- NAVBAR ---- */
        .navbar {
            position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
            padding: 20px 0; transition: all 0.4s ease; border-bottom: 1px solid transparent;
        }
        .navbar.scrolled {
            padding: 12px 0;
            background: rgba(3, 0, 20, 0.7); backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px);
            border-bottom: 1px solid var(--border-glass);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        .navbar .container { display: flex; align-items: center; justify-content: space-between; }
        .navbar .logo img { height: 44px; width: auto; transition: var(--transition); }
        .navbar .logo:hover img { transform: scale(1.05); }
        
        .nav-links { display: flex; align-items: center; gap: 40px; list-style: none; }
        .nav-links a { font-size: 1rem; font-weight: 500; color: var(--text-secondary); position: relative; padding: 5px 0; }
        .nav-links a::after { content: ''; position: absolute; bottom: -4px; left: 50%; transform: translateX(-50%); width: 0; height: 2px; background: linear-gradient(90deg, var(--primary-light), var(--secondary-light)); border-radius: 2px; transition: width 0.3s ease; }
        .nav-links a:hover { color: #fff; }
        .nav-links a:hover::after { width: 100%; }
        
        .nav-cta {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 12px 28px; border-radius: var(--radius-full);
            background: linear-gradient(135deg, var(--primary-light), var(--primary));
            color: #fff; font-weight: 600; font-size: 0.95rem;
            box-shadow: var(--shadow-glow);
            transition: var(--transition);
            border: 1px solid rgba(255,255,255,0.1);
        }
        .nav-cta:hover { transform: translateY(-3px); box-shadow: 0 15px 30px rgba(139, 92, 246, 0.4); color: #fff; border-color: rgba(255,255,255,0.3); }

        .mobile-toggle { display: none; background: none; border: none; color: #fff; font-size: 1.8rem; cursor: pointer; }

        /* ---- HERO ---- */
        .hero {
            position: relative; z-index: 1;
            min-height: 100vh; display: flex; align-items: center;
            padding: 160px 0 100px;
        }
        .hero .container { display: grid; grid-template-columns: 1.1fr 0.9fr; gap: 40px; align-items: center; }
        
        .hero-badge {
            display: inline-flex; align-items: center; gap: 10px;
            padding: 8px 20px; border-radius: var(--radius-full);
            background: rgba(255,255,255,0.05); border: 1px solid var(--border-highlight);
            font-size: 0.85rem; color: var(--text-secondary); font-weight: 600;
            margin-bottom: 30px; letter-spacing: 1px; text-transform: uppercase;
            backdrop-filter: blur(10px);
            animation: slideDown 0.8s ease-out;
        }
        .hero-badge i { color: var(--accent-light); font-size: 1.2rem; animation: pulseIcon 2s infinite; }
        
        .hero h1 {
            font-size: 4.5rem; font-weight: 800; line-height: 1.05; letter-spacing: -0.04em;
            margin-bottom: 24px; text-shadow: 0 10px 30px rgba(0,0,0,0.5);
            animation: slideUp 0.8s ease-out 0.2s both;
        }
        .hero h1 .gradient-text {
            background: linear-gradient(135deg, var(--primary-light), var(--secondary-light), var(--accent-light));
            background-size: 200% auto;
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
            animation: shine 5s linear infinite;
        }
        
        .hero-desc {
            font-size: 1.25rem; color: var(--text-secondary); line-height: 1.6; margin-bottom: 40px; max-width: 550px;
            animation: slideUp 0.8s ease-out 0.4s both; font-weight: 300;
        }
        
        .hero-actions { display: flex; gap: 20px; flex-wrap: wrap; animation: slideUp 0.8s ease-out 0.6s both; }
        
        .btn-primary {
            display: inline-flex; align-items: center; justify-content: center; gap: 10px;
            padding: 16px 36px; border-radius: var(--radius-full);
            background: linear-gradient(135deg, var(--primary-light), var(--primary-dark));
            color: #fff; font-weight: 600; font-size: 1.05rem;
            box-shadow: var(--shadow-glow);
            transition: var(--transition); border: 1px solid rgba(255,255,255,0.1); cursor: pointer;
            position: relative; overflow: hidden;
        }
        .btn-primary::after { content:''; position:absolute; top:0; left:-100%; width:50%; height:100%; background:linear-gradient(90deg,transparent,rgba(255,255,255,0.3),transparent); transform:skewX(-20deg); transition:all 0.5s ease; }
        .btn-primary:hover { transform: translateY(-5px) scale(1.02); box-shadow: 0 20px 40px rgba(139, 92, 246, 0.5); color: #fff; }
        .btn-primary:hover::after { left: 150%; }

        .btn-outline {
            display: inline-flex; align-items: center; justify-content: center; gap: 10px;
            padding: 16px 36px; border-radius: var(--radius-full);
            background: rgba(255,255,255,0.03); border: 1px solid var(--border-glass);
            color: #fff; font-weight: 500; font-size: 1.05rem; backdrop-filter: blur(10px);
            transition: var(--transition); cursor: pointer;
        }
        .btn-outline:hover { background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2); transform: translateY(-5px); color: #fff; box-shadow: 0 10px 20px rgba(0,0,0,0.2); }

        .hero-image {
            position: relative; display: flex; justify-content: center; align-items: center;
            animation: fadeIn 1.2s ease-out 0.6s both; perspective: 1000px;
        }
        .hero-image img {
            width: 100%; max-width: 550px; border-radius: var(--radius-xl);
            box-shadow: 0 30px 60px rgba(0,0,0,0.6), 0 0 40px rgba(139, 92, 246, 0.2);
            transform: rotateY(-10deg) rotateX(5deg);
            transition: transform 0.5s ease;
            animation: float3D 6s ease-in-out infinite alternate;
        }
        .hero-image img:hover { transform: rotateY(0deg) rotateX(0deg); }

        @keyframes float3D { 0% { transform: translateY(0) rotateY(-10deg) rotateX(5deg); } 100% { transform: translateY(-20px) rotateY(-5deg) rotateX(2deg); } }
        @keyframes slideUp { from { opacity: 0; transform: translateY(40px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes shine { to { background-position: 200% center; } }
        @keyframes pulseIcon { 0%, 100% { transform: scale(1); opacity: 1; } 50% { transform: scale(1.2); opacity: 0.7; } }

        /* ---- CLIENTS / PARTNERS ---- */
        .partners {
            position: relative; z-index: 1; padding: 40px 0 80px; border-bottom: 1px solid var(--border-glass);
            background: linear-gradient(180deg, transparent, rgba(255,255,255,0.01));
        }
        .partners p { text-align: center; font-size: 0.9rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 2px; margin-bottom: 30px; font-weight: 600; }
        .partners-logos { display: flex; align-items: center; justify-content: center; gap: 60px; flex-wrap: wrap; opacity: 0.6; }
        .partners-logos img { height: 35px; width: auto; filter: grayscale(1) brightness(2); transition: all 0.4s ease; cursor: pointer; }
        .partners-logos img:hover { filter: grayscale(0) brightness(1); transform: scale(1.1); }

        /* ---- STATS ---- */
        .stats-bar { position: relative; z-index: 1; margin-top: -50px; margin-bottom: 50px; }
        .stats-bar .container { display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; }
        .stat-card {
            text-align: center; padding: 30px 20px;
            background: rgba(10, 10, 30, 0.6); backdrop-filter: blur(20px); border: 1px solid var(--border-glass);
            border-radius: var(--radius-xl); box-shadow: 0 20px 40px rgba(0,0,0,0.4);
            transition: var(--transition);
        }
        .stat-card:hover { transform: translateY(-10px); border-color: var(--border-highlight); box-shadow: var(--shadow-glow); background: rgba(20, 15, 40, 0.8); }
        .stat-number { font-size: 2.5rem; font-weight: 800; background: linear-gradient(135deg, #fff, var(--text-secondary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 5px; }
        .stat-label { font-size: 0.9rem; color: var(--primary-light); font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }

        /* ---- SECTION COMMON ---- */
        .section { position: relative; z-index: 1; padding: 120px 0; }
        .section-label {
            display: inline-flex; align-items: center; gap: 8px;
            font-size: 0.85rem; color: var(--accent-light); font-weight: 700;
            text-transform: uppercase; letter-spacing: 2px; margin-bottom: 16px;
            padding: 6px 16px; background: rgba(45, 212, 191, 0.1); border-radius: var(--radius-full);
        }
        .section-title { font-size: 3rem; font-weight: 800; letter-spacing: -0.03em; line-height: 1.1; margin-bottom: 24px; text-shadow: 0 5px 15px rgba(0,0,0,0.3); }
        .section-desc { font-size: 1.15rem; color: var(--text-secondary); max-width: 650px; line-height: 1.7; font-weight: 300; }
        .section-header { text-align: center; margin-bottom: 80px; display: flex; flex-direction: column; align-items: center; }

        /* ---- FEATURES ---- */
        .features-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; }
        .feature-card {
            padding: 50px 40px;
            background: linear-gradient(145deg, rgba(255,255,255,0.03), rgba(255,255,255,0.01));
            border: 1px solid var(--border-glass); border-radius: var(--radius-xl);
            position: relative; overflow: hidden; transition: var(--transition);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            backdrop-filter: blur(10px);
        }
        .feature-card::before {
            content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: radial-gradient(circle at 50% 0%, rgba(139, 92, 246, 0.15), transparent 70%);
            opacity: 0; transition: opacity 0.4s ease;
        }
        .feature-card:hover { transform: translateY(-10px); border-color: rgba(139, 92, 246, 0.4); box-shadow: 0 20px 40px rgba(0,0,0,0.4), 0 0 20px rgba(139, 92, 246, 0.1); }
        .feature-card:hover::before { opacity: 1; }
        
        .feature-icon {
            width: 70px; height: 70px; border-radius: var(--radius-lg);
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            display: flex; align-items: center; justify-content: center;
            font-size: 2rem; color: #fff; margin-bottom: 30px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.3); position: relative; z-index: 1;
        }
        .feature-card h3 { font-size: 1.4rem; font-weight: 700; margin-bottom: 15px; position: relative; z-index: 1; }
        .feature-card p { font-size: 1rem; color: var(--text-secondary); line-height: 1.6; position: relative; z-index: 1; font-weight: 300; }

        /* ---- ABOUT ---- */
        .about-section { background: var(--bg-section); position: relative; border-top: 1px solid var(--border-glass); border-bottom: 1px solid var(--border-glass); }
        .about-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 80px; align-items: center; }
        .about-image { position: relative; border-radius: var(--radius-xl); padding: 20px; background: linear-gradient(135deg, rgba(255,255,255,0.05), transparent); border: 1px solid var(--border-glass); }
        .about-image img { border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); transition: var(--transition); }
        .about-image:hover img { transform: scale(1.02); }
        .about-image .float-card { position: absolute; bottom: -30px; right: -30px; background: rgba(10,10,20,0.9); backdrop-filter: blur(10px); padding: 20px; border-radius: var(--radius-lg); border: 1px solid var(--border-glass); display: flex; align-items: center; gap: 15px; box-shadow: 0 20px 40px rgba(0,0,0,0.5); animation: floatOrb 4s ease-in-out infinite alternate; }
        .about-image .float-card i { font-size: 2rem; color: var(--success); }
        
        .about-features { margin-top: 40px; display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .about-feature { display: flex; flex-direction: column; gap: 10px; padding: 20px; background: rgba(255,255,255,0.02); border-radius: var(--radius-md); border: 1px solid var(--border-glass); transition: var(--transition); }
        .about-feature:hover { background: rgba(255,255,255,0.05); border-color: var(--primary-light); transform: translateY(-5px); }
        .about-feature i { color: var(--accent-light); font-size: 1.5rem; }
        .about-feature span { font-weight: 600; font-size: 1rem; }

        /* ---- PRICING ---- */
        .pricing-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 40px; max-width: 900px; margin: 0 auto; }
        .pricing-card {
            padding: 50px 40px; background: linear-gradient(180deg, rgba(255,255,255,0.03), rgba(0,0,0,0.2));
            border: 1px solid var(--border-glass); border-radius: var(--radius-xl); position: relative; overflow: hidden;
            transition: var(--transition); backdrop-filter: blur(10px);
        }
        .pricing-card.featured { border-color: rgba(139, 92, 246, 0.5); background: linear-gradient(180deg, rgba(139, 92, 246, 0.05), rgba(0,0,0,0.4)); box-shadow: 0 20px 50px rgba(0,0,0,0.5), 0 0 30px rgba(139, 92, 246, 0.15); transform: scale(1.05); z-index: 2; }
        .pricing-card.featured::before { content:''; position:absolute; top:0; left:0; width:100%; height:4px; background:linear-gradient(90deg, var(--primary-light), var(--accent-light)); }
        
        .pricing-card:hover { transform: translateY(-10px); border-color: rgba(255,255,255,0.2); }
        .pricing-card.featured:hover { transform: scale(1.05) translateY(-10px); border-color: var(--primary-light); }
        
        .pricing-header { border-bottom: 1px solid var(--border-glass); padding-bottom: 30px; margin-bottom: 30px; }
        .pricing-badge { display:inline-block; padding:6px 16px; border-radius:var(--radius-full); background:rgba(139, 92, 246, 0.2); color:var(--primary-light); font-size:0.8rem; font-weight:700; margin-bottom:20px; letter-spacing: 1px; text-transform: uppercase; border: 1px solid rgba(139,92,246,0.3); }
        
        .pricing-card h3 { font-size: 1.8rem; font-weight: 800; margin-bottom: 10px; }
        .pricing-card .price { font-size: 1.2rem; color: var(--accent-light); font-weight: 600; }
        
        .pricing-list { list-style:none; display:flex; flex-direction:column; gap:16px; margin-bottom:40px; }
        .pricing-list li { display:flex; align-items:flex-start; gap:12px; font-size:1rem; color:var(--text-secondary); font-weight: 300; }
        .pricing-list li i { color:var(--accent-light); font-size:1.2rem; background: rgba(45, 212, 191, 0.1); border-radius: 50%; padding: 2px; }

        /* ---- HOW IT WORKS ---- */
        .steps-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 40px; position: relative; }
        .steps-grid::before { content: ''; position: absolute; top: 40px; left: 10%; right: 10%; height: 2px; background: dashed 2px var(--border-glass); z-index: -1; }
        
        .step-card { text-align: center; padding: 0 20px; }
        .step-number {
            width: 80px; height: 80px; border-radius: 50%; margin: 0 auto 30px;
            background: var(--bg-dark); border: 2px solid var(--primary-light);
            display: flex; align-items: center; justify-content: center;
            font-size: 2rem; font-weight: 800; color: #fff;
            box-shadow: 0 0 30px rgba(139, 92, 246, 0.3); position: relative;
        }
        .step-number::after { content: ''; position: absolute; inset: -10px; border-radius: 50%; border: 1px solid rgba(139, 92, 246, 0.3); animation: pulse 2s infinite; }
        
        .step-card h3 { font-size: 1.5rem; font-weight: 700; margin-bottom: 15px; }
        .step-card p { font-size: 1.05rem; color: var(--text-secondary); line-height: 1.6; font-weight: 300; }

        /* ---- CTA SECTION ---- */
        .cta-section { position: relative; z-index: 1; padding: 120px 0; }
        .cta-card {
            padding: 80px 40px; border-radius: var(--radius-xl); text-align: center;
            background: linear-gradient(135deg, rgba(91, 33, 182, 0.8), rgba(3, 0, 20, 0.9)), url('common/img/banner_01.png') center/cover;
            border: 1px solid var(--border-highlight); position: relative; overflow: hidden;
            box-shadow: 0 30px 60px rgba(0,0,0,0.6);
        }
        .cta-card::before { content:''; position:absolute; inset:0; background: radial-gradient(circle at center, rgba(139, 92, 246, 0.3) 0%, transparent 70%); mix-blend-mode: overlay; }
        .cta-card h2 { font-size: 3.5rem; font-weight: 800; margin-bottom: 20px; position: relative; z-index: 1; text-shadow: 0 5px 15px rgba(0,0,0,0.5); }
        .cta-card p { font-size: 1.2rem; color: #e2e8f0; max-width: 600px; margin: 0 auto 40px; line-height: 1.6; position: relative; z-index: 1; }
        .cta-actions { display: flex; gap: 20px; justify-content: center; flex-wrap: wrap; position: relative; z-index: 1; }

        /* ---- FOOTER ---- */
        .footer {
            position: relative; z-index: 1; padding: 80px 0 0;
            background: #02000a; border-top: 1px solid var(--border-glass);
        }
        .footer-grid { display: grid; grid-template-columns: 2.5fr 1fr 1fr 1.5fr; gap: 60px; margin-bottom: 60px; }
        .footer-brand img { height: 50px; margin-bottom: 24px; }
        .footer-brand p { font-size: 1rem; color: var(--text-secondary); line-height: 1.7; max-width: 350px; font-weight: 300; }
        
        .footer-col h4 { font-size: 1.1rem; font-weight: 700; margin-bottom: 24px; color: #fff; position: relative; display: inline-block; }
        .footer-col h4::after { content: ''; position: absolute; left: 0; bottom: -8px; width: 30px; height: 2px; background: var(--primary-light); }
        
        .footer-col ul { list-style: none; display: flex; flex-direction: column; gap: 15px; }
        .footer-col ul a { font-size: 0.95rem; color: var(--text-secondary); font-weight: 300; transition: var(--transition); }
        .footer-col ul a:hover { color: var(--primary-light); transform: translateX(5px); display: inline-block; }
        
        .footer-social { display: flex; gap: 15px; margin-top: 30px; }
        .footer-social a {
            width: 45px; height: 45px; border-radius: 50%;
            background: rgba(255,255,255,0.05); border: 1px solid var(--border-glass);
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 1.2rem; transition: var(--transition);
        }
        .footer-social a:hover { background: var(--primary); transform: translateY(-5px) rotate(10deg); box-shadow: 0 10px 20px rgba(139, 92, 246, 0.4); border-color: transparent; }
        
        .footer-bottom {
            padding: 24px 0; border-top: 1px solid rgba(255,255,255,0.05);
            display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;
        }
        .footer-bottom p { font-size: 0.9rem; color: var(--text-muted); }

        /* ---- RESPONSIVE ---- */
        @media (max-width: 1024px) {
            .hero .container { grid-template-columns: 1fr; text-align: center; }
            .hero h1 { font-size: 3.5rem; }
            .hero-desc { margin: 0 auto 30px; }
            .hero-actions { justify-content: center; }
            .hero-image { display: none; } /* Hide 3D image on mobile for cleaner look or adjust */
            .nav-links { display: none; }
            .mobile-toggle { display: block; }
            .features-grid { grid-template-columns: repeat(2, 1fr); }
            .about-grid { grid-template-columns: 1fr; }
            .about-image { order: 2; }
            .about-content { order: 1; text-align: center; display: flex; flex-direction: column; align-items: center; }
            .about-features { grid-template-columns: 1fr; width: 100%; max-width: 500px; }
            .stats-bar .container { grid-template-columns: repeat(2, 1fr); }
            .pricing-grid { grid-template-columns: 1fr; max-width: 500px; }
            .pricing-card.featured { transform: none; }
            .pricing-card.featured:hover { transform: translateY(-10px); }
            .steps-grid { grid-template-columns: 1fr; gap: 60px; }
            .steps-grid::before { display: none; }
            .footer-grid { grid-template-columns: 1fr 1fr; }
        }
        
        @media (max-width: 768px) {
            .hero h1 { font-size: 2.8rem; }
            .section-title { font-size: 2.2rem; }
            .features-grid { grid-template-columns: 1fr; max-width: 500px; margin: 0 auto; }
            .footer-grid { grid-template-columns: 1fr; text-align: center; }
            .footer-col h4::after { left: 50%; transform: translateX(-50%); }
            .footer-social { justify-content: center; }
            .footer-bottom { justify-content: center; flex-direction: column; text-align: center; }
            .cta-card { padding: 40px 20px; }
            .cta-card h2 { font-size: 2.2rem; }
        }

        /* ---- NAV MOBILE MENU ---- */
        .mobile-menu {
            display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 1001;
            background: rgba(3, 0, 20, 0.98); backdrop-filter: blur(30px);
            flex-direction: column; align-items: center; justify-content: center; gap: 30px;
            opacity: 0; transition: opacity 0.3s ease;
        }
        .mobile-menu.active { display: flex; opacity: 1; animation: fadeIn 0.3s forwards; }
        .mobile-menu a { font-size: 1.5rem; font-weight: 600; color: var(--text-secondary); transition: all 0.3s ease; }
        .mobile-menu a:hover { color: #fff; transform: scale(1.1); }
        .mobile-menu .close-menu { position: absolute; top: 30px; right: 30px; background: rgba(255,255,255,0.1); border: none; border-radius: 50%; width: 50px; height: 50px; color: #fff; font-size: 1.5rem; cursor: pointer; transition: all 0.3s ease; }
        .mobile-menu .close-menu:hover { background: var(--primary); transform: rotate(90deg); }

        /* Scroll reveal */
        .reveal { opacity: 0; transform: translateY(50px); transition: all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94); }
        .reveal.visible { opacity: 1; transform: translateY(0); }
    </style>
</head>

<body>

<!-- Ultra Premium Background -->
<div class="page-bg">
    <div class="grid-overlay"></div>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
</div>

<!-- NAVBAR -->
<nav class="navbar" id="navbar">
    <div class="container">
        <a href="" class="logo">
            <img src="<?php echo $site_settings['logo_url']; ?>" alt="<?php echo $site_settings['brand_name']; ?>" />
        </a>
        <ul class="nav-links">
            <li><a href="#hero">Overview</a></li>
            <li><a href="#features">Features</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#pricing">Pricing</a></li>
        </ul>
        <a href="auth/index" class="nav-cta"><i class="ri-dashboard-3-line"></i> Dashboard</a>
        <button class="mobile-toggle" onclick="document.getElementById('mobileMenu').classList.add('active')">
            <i class="ri-menu-4-line"></i>
        </button>
    </div>
</nav>

<!-- Mobile Menu -->
<div class="mobile-menu" id="mobileMenu">
    <button class="close-menu" onclick="document.getElementById('mobileMenu').classList.remove('active')">
        <i class="ri-close-line"></i>
    </button>
    <a href="#hero" onclick="document.getElementById('mobileMenu').classList.remove('active')">Overview</a>
    <a href="#features" onclick="document.getElementById('mobileMenu').classList.remove('active')">Features</a>
    <a href="#about" onclick="document.getElementById('mobileMenu').classList.remove('active')">About</a>
    <a href="#pricing" onclick="document.getElementById('mobileMenu').classList.remove('active')">Pricing</a>
    <a href="auth/index" class="nav-cta" style="margin-top:20px; font-size: 1.1rem;"><i class="ri-login-circle-line"></i> Sign In</a>
</div>

<!-- HERO SECTION -->
<section class="hero" id="hero">
    <div class="container">
        <div class="hero-content">
            <div class="hero-badge">
                <i class="ri-flashlight-fill"></i>
                <?php echo $site_settings['brand_name']; ?> - Next Gen Payments
            </div>
            <h1>
                Future of Finance.<br/>
                <span class="gradient-text">Seamless UPI</span> Gateway.
            </h1>
            <p class="hero-desc">
                Elevate your business with state-of-the-art payment solutions. Collect via UPI Apps, dynamic QR, and intelligent payment links instantly with 0% hassle.
            </p>
            <div class="hero-actions">
                <a href="https://wp.Dezo.com/Register" class="btn-primary"><i class="ri-rocket-2-fill"></i> Get Started Free</a>
                <a href="#how-it-works" class="btn-outline"><i class="ri-play-circle-line"></i> See How It Works</a>
            </div>
        </div>
        <div class="hero-image">
            <img src="common/img/banner_01.png" alt="Premium Payment Dashboard" onerror="this.src='https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?auto=format&fit=crop&w=800&q=80'" />
        </div>
    </div>
</section>

<!-- PARTNERS -->
<div class="partners reveal">
    <div class="container">
        <p>Trusted by industry leaders and powered by</p>
        <div class="partners-logos">
            <img src="https://upload.wikimedia.org/wikipedia/commons/e/e1/UPI-Logo-vector.svg" alt="UPI" style="height: 25px;" />
            <img src="https://upload.wikimedia.org/wikipedia/commons/7/71/PhonePe_Logo.svg" alt="PhonePe" style="height: 30px;" />
            <img src="https://upload.wikimedia.org/wikipedia/commons/2/24/Paytm_Logo_%28standalone%29.svg" alt="Paytm" style="height: 25px;" />
            <img src="https://upload.wikimedia.org/wikipedia/commons/f/f2/Google_Pay_Logo.svg" alt="GPay" style="height: 30px;" />
        </div>
    </div>
</div>

<!-- STATS -->
<div class="stats-bar reveal">
    <div class="container">
        <div class="stat-card">
            <div class="stat-number">10K+</div>
            <div class="stat-label">Merchants</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">₹50Cr</div>
            <div class="stat-label">Processed</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">99.9%</div>
            <div class="stat-label">Success Rate</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">0%</div>
            <div class="stat-label">MDR Fee</div>
        </div>
    </div>
</div>

<!-- FEATURES -->
<section class="section" id="features">
    <div class="container">
        <div class="section-header reveal">
            <div class="section-label"><i class="ri-magic-line"></i> Capabilities</div>
            <h2 class="section-title">Engineered for Massive Growth</h2>
            <p class="section-desc">Experience an ecosystem designed to optimize conversions, simplify settlements, and scale your operations effortlessly.</p>
        </div>
        <div class="features-grid">
            <div class="feature-card reveal">
                <div class="feature-icon"><i class="ri-qr-scan-2-line"></i></div>
                <h3>Dynamic UPI QR</h3>
                <p>Generate intelligent QR codes for physical stores or online checkouts. Auto-verify payments in milliseconds.</p>
            </div>
            <div class="feature-card reveal">
                <div class="feature-icon" style="background: linear-gradient(135deg, #ec4899, #f472b6);"><i class="ri-link-m"></i></div>
                <h3>Smart Payment Links</h3>
                <p>Share payment links via WhatsApp, SMS, or Email. Get paid instantly with automated reminders and tracking.</p>
            </div>
            <div class="feature-card reveal">
                <div class="feature-icon" style="background: linear-gradient(135deg, #14b8a6, #2dd4bf);"><i class="ri-code-block-3-line"></i></div>
                <h3>Developer APIs</h3>
                <p>Robust RESTful APIs with comprehensive documentation. Integrate seamless checkouts into any tech stack.</p>
            </div>
        </div>
    </div>
</section>

<!-- ABOUT -->
<section class="section about-section" id="about">
    <div class="container">
        <div class="about-grid">
            <div class="about-image reveal">
                <img src="common/img/banner_02.png" alt="Analytics Dashboard" onerror="this.src='https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=800&q=80'" />
                <div class="float-card">
                    <i class="ri-checkbox-circle-fill"></i>
                    <div>
                        <div style="font-weight: 700; color: #fff;">Instant Settlement</div>
                        <div style="font-size: 0.8rem; color: var(--text-secondary);">To your bank account</div>
                    </div>
                </div>
            </div>
            <div class="about-content reveal">
                <div class="section-label"><i class="ri-shield-star-line"></i> The Advantage</div>
                <h2 class="section-title" style="text-align: left;">Direct to Bank.<br/>Zero Intermediaries.</h2>
                <p class="section-desc" style="text-align: left;">
                    <?php echo $site_settings['brand_name']; ?> eliminates the middleman. By leveraging the power of UPI infrastructure, we route payments directly from your customer's bank to yours, reducing fraud and eliminating MDR fees entirely.
                </p>
                <div class="about-features">
                    <div class="about-feature">
                        <i class="ri-lock-password-line"></i>
                        <span>Bank-Grade Security & Encryption</span>
                    </div>
                    <div class="about-feature">
                        <i class="ri-flashlight-line"></i>
                        <span>Real-time Webhook Notifications</span>
                    </div>
                    <div class="about-feature">
                        <i class="ri-pie-chart-line"></i>
                        <span>Advanced Analytics Dashboard</span>
                    </div>
                    <div class="about-feature">
                        <i class="ri-customer-service-fill"></i>
                        <span>Priority 24/7 Technical Support</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- HOW IT WORKS -->
<section class="section" id="how-it-works">
    <div class="container">
        <div class="section-header reveal">
            <div class="section-label"><i class="ri-route-line"></i> Workflow</div>
            <h2 class="section-title">Go Live in Minutes</h2>
            <p class="section-desc">We've removed the friction from payments integration. Start accepting transactions today.</p>
        </div>
        <div class="steps-grid">
            <div class="step-card reveal">
                <div class="step-number">1</div>
                <h3>Create Account</h3>
                <p>Sign up instantly with basic details. No physical paperwork or lengthy approval processes.</p>
            </div>
            <div class="step-card reveal" style="transition-delay: 0.2s;">
                <div class="step-number" style="border-color: var(--secondary); box-shadow: 0 0 30px rgba(236, 72, 153, 0.3);">2</div>
                <h3>Link Bank Account</h3>
                <p>Securely connect your existing current or savings bank account to receive direct settlements.</p>
            </div>
            <div class="step-card reveal" style="transition-delay: 0.4s;">
                <div class="step-number" style="border-color: var(--accent); box-shadow: 0 0 30px rgba(20, 184, 166, 0.3);">3</div>
                <h3>Start Collecting</h3>
                <p>Generate API keys, create payment links, or display QR codes to get paid instantly.</p>
            </div>
        </div>
    </div>
</section>

<!-- PRICING -->
<section class="section" id="pricing">
    <div class="container">
        <div class="section-header reveal">
            <div class="section-label"><i class="ri-vip-diamond-line"></i> Plans</div>
            <h2 class="section-title">Transparent, Fair Pricing</h2>
            <p class="section-desc">Choose the perfect plan tailored to your business scale. No hidden setup fees.</p>
        </div>
        <div class="pricing-grid">
            <div class="pricing-card reveal">
                <div class="pricing-header">
                    <h3>Starter</h3>
                    <div class="price">Free Registration</div>
                </div>
                <ul class="pricing-list">
                    <li><i class="ri-check-line"></i> Standard UPI QR Generation</li>
                    <li><i class="ri-check-line"></i> Basic Payment Links</li>
                    <li><i class="ri-check-line"></i> Direct Bank Settlement</li>
                    <li><i class="ri-check-line"></i> Standard Support</li>
                    <li style="opacity: 0.5;"><i class="ri-close-line" style="background: rgba(255,255,255,0.1); color: #fff;"></i> Developer APIs</li>
                </ul>
                <a href="https://wp.Dezo.com/Register" class="btn-outline" style="width: 100%;"><i class="ri-user-add-line"></i> Create Account</a>
            </div>
            
            <div class="pricing-card featured reveal">
                <div class="pricing-badge">Business Pro</div>
                <div class="pricing-header">
                    <h3>Enterprise</h3>
                    <div class="price" style="color: var(--primary-light);">Custom Subscription</div>
                </div>
                <ul class="pricing-list">
                    <li><i class="ri-check-line" style="color: var(--primary-light); background: rgba(139, 92, 246, 0.2);"></i> Unlimited Transactions</li>
                    <li><i class="ri-check-line" style="color: var(--primary-light); background: rgba(139, 92, 246, 0.2);"></i> Full API Access & Webhooks</li>
                    <li><i class="ri-check-line" style="color: var(--primary-light); background: rgba(139, 92, 246, 0.2);"></i> Advanced Analytics & Reports</li>
                    <li><i class="ri-check-line" style="color: var(--primary-light); background: rgba(139, 92, 246, 0.2);"></i> White-label Checkout</li>
                    <li><i class="ri-check-line" style="color: var(--primary-light); background: rgba(139, 92, 246, 0.2);"></i> Dedicated Account Manager</li>
                </ul>
                <a href="https://wp.Dezo.com/Register" class="btn-primary" style="width: 100%;"><i class="ri-rocket-line"></i> Upgrade to Pro</a>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section">
    <div class="container">
        <div class="cta-card reveal">
            <h2>Ready to Transform Your Business?</h2>
            <p>Join the thousands of modern merchants who trust <?php echo $site_settings['brand_name']; ?> to process payments instantly, securely, and without extra fees.</p>
            <div class="cta-actions">
                <a href="https://wp.Dezo.com/Register" class="btn-primary" style="background: #fff; color: var(--primary-dark);"><i class="ri-user-star-line"></i> Create Free Account</a>
                <a href="auth/apidetails" class="btn-outline" style="border-color: rgba(255,255,255,0.5);"><i class="ri-book-read-line"></i> View API Docs</a>
            </div>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <img src="<?php echo $site_settings['logo_url']; ?>" alt="<?php echo $site_settings['brand_name']; ?>" />
                <p>Empowering Indian businesses with next-generation UPI payment infrastructure. Seamless integrations, instant settlements, zero hassle.</p>
                <div class="footer-social">
                    <a href="#"><i class="ri-twitter-x-line"></i></a>
                    <a href="#"><i class="ri-linkedin-fill"></i></a>
                    <a href="#"><i class="ri-github-fill"></i></a>
                    <a href="#"><i class="ri-instagram-line"></i></a>
                </div>
            </div>
            <div class="footer-col">
                <h4>Platform</h4>
                <ul>
                    <li><a href="#features">Features</a></li>
                    <li><a href="#pricing">Pricing</a></li>
                    <li><a href="auth/apidetails">API Documentation</a></li>
                    <li><a href="#">Status</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Company</h4>
                <ul>
                    <li><a href="#about">About Us</a></li>
                    <li><a href="#">Careers</a></li>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Legal & Support</h4>
                <ul>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms of Service</a></li>
                    <li><a href="#">Refund Policy</a></li>
                    <li><a href="#">Help Center</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> <?php echo $site_settings['brand_name']; ?>. All rights reserved.</p>
            <p>Developed with <i class="ri-heart-fill" style="color: var(--secondary);"></i> by <a href="https://tarikislam.in" style="color: #fff; font-weight: 600;">tarikislam.in</a> | Made in India</p>
        </div>
    </div>
</footer>

<script>
// Navbar Scroll Effect
const navbar = document.getElementById('navbar');
window.addEventListener('scroll', () => {
    if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
});

// Scroll Reveal Animations
const revealOptions = {
    threshold: 0.15,
    rootMargin: "0px 0px -50px 0px"
};

const revealObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            observer.unobserve(entry.target); // Run once
        }
    });
}, revealOptions);

document.querySelectorAll('.reveal').forEach(el => {
    revealObserver.observe(el);
});

// Counter Animation for Stats
const animateValue = (obj, start, end, duration) => {
    let startTimestamp = null;
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        obj.innerHTML = Math.floor(progress * (end - start) + start) + "+";
        if (progress < 1) {
            window.requestAnimationFrame(step);
        }
    };
    window.requestAnimationFrame(step);
}

// Just a small simulation for the 10K+ stat
// animateValue(document.querySelector('.stat-number'), 0, 10000, 2000);
</script>

</body>
</html>