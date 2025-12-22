<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Merry Christmas & Happy New Year, My Love</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Poppins:wght@300;400;600&display=swap"
        rel="stylesheet">
    <style>
        /* === Base Styles === */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            overflow-x: hidden;
            background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
            min-height: 100vh;
            font-family: 'Poppins', sans-serif;
        }

        /* === Snow Animation === */
        .snow {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            pointer-events: none;
            z-index: 1000;
        }

        .snowflake {
            position: absolute;
            top: -10px;
            color: white;
            font-size: 1em;
            opacity: 0.8;
            animation: fall linear infinite;
            text-shadow: 0 0 5px rgba(255, 255, 255, 0.5);
        }

        @keyframes fall {
            to {
                transform: translateY(100vh) rotate(360deg);
            }
        }

        /* === Christmas Lights === */
        .lights-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 60px;
            z-index: 999;
            display: flex;
            justify-content: space-around;
            align-items: center;
            padding: 15px;
            background: linear-gradient(180deg, rgba(0, 0, 0, 0.3) 0%, transparent 100%);
        }

        .light {
            width: 15px;
            height: 15px;
            border-radius: 50%;
            position: relative;
        }

        .light::before {
            content: '';
            position: absolute;
            top: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 2px;
            height: 8px;
            background: #333;
        }

        .light:nth-child(4n+1) {
            background: #ff6b6b;
            box-shadow: 0 0 20px #ff6b6b, 0 0 40px #ff6b6b;
            animation: twinkle 1.4s infinite;
        }

        .light:nth-child(4n+2) {
            background: #4ecdc4;
            box-shadow: 0 0 20px #4ecdc4, 0 0 40px #4ecdc4;
            animation: twinkle 1.4s 0.35s infinite;
        }

        .light:nth-child(4n+3) {
            background: #ffd93d;
            box-shadow: 0 0 20px #ffd93d, 0 0 40px #ffd93d;
            animation: twinkle 1.4s 0.7s infinite;
        }

        .light:nth-child(4n+4) {
            background: #a8e6cf;
            box-shadow: 0 0 20px #a8e6cf, 0 0 40px #a8e6cf;
            animation: twinkle 1.4s 1.05s infinite;
        }

        @keyframes twinkle {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.4;
                transform: scale(0.8);
            }
        }

        /* === Main Container === */
        .main-container {
            position: relative;
            z-index: 2;
            padding-top: 80px;
        }

        /* === Hero Section === */
        .hero-section {
            min-height: 90vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 60px 20px;
            background:
                radial-gradient(circle at 20% 50%, rgba(255, 107, 107, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 50%, rgba(78, 205, 196, 0.1) 0%, transparent 50%);
            position: relative;
        }

        .hero-section::before {
            content: '✨';
            position: absolute;
            font-size: 3rem;
            top: 15%;
            left: 10%;
            animation: float 3s ease-in-out infinite;
        }

        .hero-section::after {
            content: '💝';
            position: absolute;
            font-size: 3rem;
            bottom: 20%;
            right: 10%;
            animation: float 3s ease-in-out infinite 1.5s;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        .hero-title {
            font-family: 'Great Vibes', cursive;
            font-size: 4.5rem;
            font-weight: 400;
            color: #fff;
            text-shadow: 0 0 20px rgba(255, 107, 107, 0.5), 0 0 40px rgba(255, 107, 107, 0.3);
            margin-bottom: 20px;
            animation: fadeInScale 1.5s ease-out;
        }

        .hero-subtitle {
            font-size: 1.8rem;
            color: #ffd700;
            text-shadow: 0 0 10px rgba(255, 215, 0, 0.5);
            margin-bottom: 30px;
            animation: fadeInScale 1.5s ease-out 0.3s backwards;
        }

        .hero-message {
            font-size: 1.2rem;
            color: #e0e0e0;
            max-width: 600px;
            line-height: 1.8;
            margin-bottom: 40px;
            animation: fadeInScale 1.5s ease-out 0.6s backwards;
        }

        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.9) translateY(20px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .heart-pulse {
            display: inline-block;
            color: #ff6b6b;
            font-size: 2rem;
            animation: heartbeat 1.5s infinite;
        }

        @keyframes heartbeat {

            0%,
            100% {
                transform: scale(1);
            }

            10%,
            30% {
                transform: scale(1.2);
            }

            20%,
            40% {
                transform: scale(1);
            }
        }

        /* === Countdown Timer === */
        .countdown-section {
            background: linear-gradient(135deg, rgba(255, 107, 107, 0.2) 0%, rgba(78, 205, 196, 0.2) 100%);
            backdrop-filter: blur(10px);
            border-radius: 30px;
            padding: 50px 30px;
            margin: 60px auto;
            max-width: 900px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: fadeInUp 1s ease-out;
        }

        .countdown-title {
            font-family: 'Great Vibes', cursive;
            font-size: 3rem;
            color: #ffd700;
            margin-bottom: 30px;
            text-shadow: 0 0 10px rgba(255, 215, 0, 0.5);
        }

        .countdown-timer {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .time-unit {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            padding: 25px;
            min-width: 120px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .time-unit:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(255, 107, 107, 0.4);
        }

        .time-value {
            font-size: 3rem;
            font-weight: 600;
            color: #fff;
            display: block;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
        }

        .time-label {
            font-size: 1rem;
            color: #ffd700;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: 10px;
        }

        /* === Memory Cards === */
        .memories-section {
            padding: 80px 20px;
        }

        .section-title {
            font-family: 'Great Vibes', cursive;
            font-size: 4rem;
            color: #ffd700;
            text-align: center;
            margin-bottom: 60px;
            text-shadow: 0 0 20px rgba(255, 215, 0, 0.5);
        }

        .memory-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 25px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
            transition: transform 0.4s ease, box-shadow 0.4s ease;
            margin-bottom: 30px;
            animation: fadeInUp 1s ease-out;
        }

        .memory-card:hover {
            transform: translateY(-15px) rotate(1deg);
            box-shadow: 0 20px 60px rgba(255, 107, 107, 0.4);
        }

        .memory-card img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .memory-card:hover img {
            transform: scale(1.1);
        }

        .memory-card-body {
            padding: 30px;
        }

        .year-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 10px 25px;
            border-radius: 25px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 15px;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .memory-title {
            font-family: 'Great Vibes', cursive;
            color: #c0392b;
            font-size: 2.2rem;
            margin-bottom: 15px;
        }

        .memory-text {
            color: #2c3e50;
            line-height: 1.8;
            font-size: 1.05rem;
        }

        /* === Surprise Button === */
        .surprise-section {
            text-align: center;
            padding: 60px 20px;
        }

        .surprise-btn {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border: none;
            padding: 20px 50px;
            font-size: 1.3rem;
            font-weight: 600;
            border-radius: 50px;
            cursor: pointer;
            box-shadow: 0 10px 30px rgba(245, 87, 108, 0.4);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .surprise-btn::before {
            content: '🎁';
            position: absolute;
            left: 20px;
            font-size: 1.5rem;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .surprise-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 15px 40px rgba(245, 87, 108, 0.6);
        }

        .surprise-btn:active {
            transform: scale(0.98);
        }

        /* === Surprise Message Modal === */
        .surprise-message {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            padding: 60px 40px;
            border-radius: 30px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
            z-index: 1001;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            animation: popIn 0.5s ease-out;
        }

        @keyframes popIn {
            from {
                opacity: 0;
                transform: translate(-50%, -50%) scale(0.5);
            }

            to {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1);
            }
        }

        .surprise-message.active {
            display: block;
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);

            backdrop-filter: blur(5px);
        }

        .overlay.active {
            display: block;
        }

        .surprise-message h3 {
            font-family: 'Great Vibes', cursive;
            font-size: 3rem;
            color: white;
            margin-bottom: 20px;
        }

        .surprise-message p {
            color: white;
            font-size: 1.2rem;
            line-height: 1.8;
            margin-bottom: 30px;
        }

        .close-btn {
            background: white;
            color: #f5576c;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .close-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(255, 255, 255, 0.3);
        }

        /* === Final Message === */
        .final-message {
            background: linear-gradient(135deg, rgba(255, 107, 107, 0.3) 0%, rgba(78, 205, 196, 0.3) 100%);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 80px 40px;
            border-radius: 30px;
            text-align: center;
            margin: 80px auto;
            max-width: 900px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: pulse 3s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            }

            50% {
                box-shadow: 0 25px 70px rgba(255, 107, 107, 0.4);
            }
        }

        .final-message h2 {
            font-family: 'Great Vibes', cursive;
            font-size: 3.5rem;
            margin-bottom: 30px;
            color: #ffd700;
            text-shadow: 0 0 20px rgba(255, 215, 0, 0.5);
        }

        .final-message p {
            font-size: 1.3rem;
            line-height: 2;
            margin-bottom: 25px;
        }

        .signature {
            font-family: 'Great Vibes', cursive;
            font-size: 2.5rem;
            color: #ff6b6b;
            margin-top: 40px;
        }

        /* === Responsive Design === */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 3rem;
            }

            .hero-subtitle {
                font-size: 1.3rem;
            }

            .hero-message {
                font-size: 1rem;
            }

            .countdown-title {
                font-size: 2rem;
            }

            .section-title {
                font-size: 2.5rem;
            }

            .time-unit {
                min-width: 80px;
                padding: 15px;
            }

            .time-value {
                font-size: 2rem;
            }

            .final-message {
                padding: 50px 25px;
            }

            .final-message h2 {
                font-size: 2.5rem;
            }

            .surprise-message {
                padding: 40px 25px;
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <!-- Background Music (Optional - Replace with your music file) -->
    <audio id="bg-music" loop muted>
        <source src="music.mp3" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>

    <!-- Christmas Lights -->
    <div class="lights-container">
        <div class="light"></div>
        <div class="light"></div>
        <div class="light"></div>
        <div class="light"></div>
        <div class="light"></div>
        <div class="light"></div>
        <div class="light"></div>
        <div class="light"></div>
        <div class="light"></div>
        <div class="light"></div>
        <div class="light"></div>
        <div class="light"></div>
    </div>

    <!-- Falling Snow -->
    <div class="snow" id="snow"></div>

    <!-- Overlay for Surprise Message -->
    <div class="overlay" id="overlay"></div>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Hero Section -->
        <div class="hero-section">
            <h1 class="hero-title">Merry Christmas, My Love</h1>
            <p class="hero-subtitle">& Happy New Year <span class="heart-pulse">❤️</span></p>
            <p class="hero-message">
                To the one who makes every day feel like Christmas, who fills my life with warmth, laughter,
                and endless love. This season, and always, you are my greatest gift.
            </p>
        </div>

        <div class="container">
            <!-- Memory Cards Section -->
            <div class="memories-section">
                <h2 class="section-title">Our Journey Through the Years</h2>

                <div class="row">
                    <!-- 2018 Card - CUSTOMIZE: Change year, image, title, and text -->
                    <div class="col-md-6 col-lg-4">
                        <div class="memory-card">
                            <img src="2019.jpg" alt="Christmas 2018">
                            <div class="memory-card-body">
                                <span class="year-badge">2019</span>
                                <h5 class="memory-title">Where It All Began</h5>
                                <p class="memory-text">Our first Christmas together, the first year of ‘I love you,’
                                    whispered just for us.
                                    When the universe gave me the greatest gift I could ever have, a memory and an
                                    experience I
                                    will treasure forever
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- 2019 Card - CUSTOMIZE: Change year, image, title, and text -->
                    <div class="col-md-6 col-lg-4">
                        <div class="memory-card">
                            <img src="https://images.unsplash.com/photo-1482517967863-00e15c9b44be?w=600"
                                alt="Christmas 2019">
                            <div class="memory-card-body">
                                <span class="year-badge">2020</span>
                                <h5 class="memory-title">Growing Closer</h5>
                                <p class="memory-text">With every sunset over the islands and every laugh we shared, our
                                    love blossomed. You became my partner, my best friend, my heart's home.</p>
                            </div>
                        </div>
                    </div>

                    <!-- 2020 Card - CUSTOMIZE: Change year, image, title, and text -->
                    <div class="col-md-6 col-lg-4">
                        <div class="memory-card">
                            <img src="https://images.unsplash.com/photo-1544273677-ac73e3fef4ed?w=600"
                                alt="Christmas 2020">
                            <div class="memory-card-body">
                                <span class="year-badge">2021</span>
                                <h5 class="memory-title">Love Through Challenges</h5>
                                <p class="memory-text">Even when the world felt uncertain. Through challenges,
                                    through ups and downs. Hand in hand, we proved that love conquers all.</p>
                            </div>
                        </div>
                    </div>

                    <!-- 2021 Card - CUSTOMIZE: Change year, image, title, and text -->
                    <div class="col-md-6 col-lg-4">
                        <div class="memory-card">
                            <img src="2022.jpg" alt="Christmas 2021">
                            <div class="memory-card-body">
                                <span class="year-badge">2022</span>
                                <h5 class="memory-title">Tropical Adventures</h5>
                                <p class="memory-text">From beach trips and quiet evenings to watching beautiful sunsets
                                    like you, with you.
                                    Every moment, every smile on your face makes me fall in love with you even more</p>
                            </div>
                        </div>
                    </div>

                    <!-- 2023 Card - CUSTOMIZE: Change year, image, title, and text -->
                    <div class="col-md-6 col-lg-4">
                        <div class="memory-card">
                            <img src="2023.jpg" alt="Christmas 2023">
                            <div class="memory-card-body">
                                <span class="year-badge">2023</span>
                                <h5 class="memory-title">Stronger Every Year</h5>
                                <p class="memory-text">Five Christmases with you, and my heart still races. It proves
                                    that love can be painful, but loving you is beautiful.</p>
                            </div>
                        </div>
                    </div>

                    <!-- 2022 Card - CUSTOMIZE: Change year, image, title, and text -->
                    <div class="col-md-6 col-lg-4">
                        <div class="memory-card">
                            <img src="2024.jpg" alt="Christmas 2022">
                            <div class="memory-card-body">
                                <span class="year-badge">2024</span>
                                <h5 class="memory-title">Building Our Dreams</h5>
                                <p class="memory-text">Together, we dreamed, built, and graduated. Nothing was ever
                                    easy, but everything was bearable because it's you that here by my side. Your love
                                    gives me
                                    courage, and our life together is my favorite story.</p>
                            </div>
                        </div>
                    </div>



                    <!-- 2024 Card - CUSTOMIZE: Change year, image, title, and text -->
                    <div class="col-md-6 col-lg-4">
                        <div class="memory-card">
                            <img src="https://images.unsplash.com/photo-1514160945036-8c814d6e8e61?w=600"
                                alt="Christmas 2024">
                            <div class="memory-card-body">
                                <span class="year-badge">2025</span>
                                <h5 class="memory-title">Our Bright Today</h5>
                                <p class="memory-text">Here we are still laughing, still loving, and still choosing each
                                    other every single day. Through everything, here we stand; still you, still us.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Surprise Button Section -->


            <!-- Surprise Message (Hidden Initially) -->
            <div class="surprise-message" id="surpriseMessage">
                <h3>To My Darling 💝</h3>
                <p>
                    I want you to know that I love you every single day, even though our relationship is not perfect and
                    is sometimes full of struggles.
                    In our seven christmas together, we have cried, lost hope, and even felt like giving up at times.
                    But most importantly, we learned to love each other even more and proved that nothing is impossible
                    when you truly love someone.
                </p>
                <p>
                    Thank you for being you—beautiful, kind, strong, and endlessly amazing. I’m sorry for the times when
                    I can be stubborn, immature, doubtful, and always seeking reassurance. Those things are proof of how
                    deeply I love you.

                    I promise to love you more each day, to support your dreams, and to stand by your side through
                    everything. This Christmas and every Christmas, I couldn’t ask for anything more, because I know
                    that your love is the greatest gift of all.
                </p>
                <p style="font-size: 1.4rem; margin-top: 30px;">
                    <strong>You are my forever. Merry Christmas, my asbag! ❤️✨</strong>
                </p>
                <button class="close-btn" id="closeBtn">Close</button>
            </div>

            <!-- Final Love Message -->
            <div class="final-message">
                <h2>To My Beautiful Asbag <span class="heart-pulse">💝</span></h2>
                <p>
                    Seven Christmases together, like a beautiful sunset. Seven years of shared laughter, seven years of
                    love that keeps growing stronger, and seven wonderful Christmases. Countless memories I will cherish
                    forever.
                </p>
                <p>
                    As we celebrate this Christmas and welcome the New Year, know that my love for you blooms brighter
                    each day. You are my today and all of my tomorrows.
                </p>
                <p style="font-size: 1.5rem; font-weight: 600; margin-top: 30px;">
                    Merry Christmas and a Happy New Year! 🎄✨
                </p>
                <p style="font-size: 1.3rem;">
                    Here's to more laughter, love, and sun-kissed memories together.
                </p>
                <p class="signature">I love You ❤️</p>
            </div>

            <div class="surprise-section">
                <button class="surprise-btn" id="surpriseBtn">Click Me</button>
            </div>
        </div>
    </div>

    <!-- Background Music -->
    <div id="loveModal" style="
    position: fixed;
    top:0; left:0;
    width:100%; height:100%;
    background: rgba(0,0,0,0.8);
    display:flex;
    justify-content:center;
    align-items:center;
    z-index: 9999;
">
        <div style="
        background: #fff;
        padding: 30px;
        border-radius: 15px;
        text-align:center;
        max-width: 300px;
    ">
            <h2>Do you love me? ❤️</h2>
            <div style="margin-top:20px;">
                <button class="loveBtn" style="padding:10px 20px; margin:5px;">YES</button>
                <button class="loveBtn" style="padding:10px 20px; margin:5px;">YES</button>
            </div>
        </div>
    </div>

    <script>
        /* ===============================
           BACKGROUND MUSIC
        =============================== */
        const music = document.getElementById('bg-music');

        if (music) {
            music.muted = true; // start muted
            music.volume = 0.3;
            music.play().catch(() => console.log('Muted autoplay attempted'));
        }

        /* ===============================
           LOVE PROMPT MODAL
        =============================== */
        const loveModal = document.getElementById('loveModal');
        const loveBtns = document.querySelectorAll('.loveBtn');

        loveBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                // Unmute and play music
                if (music) {
                    music.muted = false;
                    music.play().catch(() => console.log('Music blocked'));
                }
                // Hide modal
                loveModal.style.display = 'none';
            });
        });

        /* ===============================
           SNOWFLAKE ANIMATION
        =============================== */
        function createSnowflake() {
            const snowflake = document.createElement('div');
            snowflake.className = 'snowflake';
            snowflake.textContent = '❄';
            snowflake.style.left = Math.random() * window.innerWidth + 'px';
            snowflake.style.fontSize = Math.random() * 12 + 10 + 'px';
            snowflake.style.opacity = Math.random() * 0.6 + 0.4;
            snowflake.style.animationDuration = Math.random() * 5 + 5 + 's';
            document.getElementById('snow').appendChild(snowflake);
            setTimeout(() => snowflake.remove(), 10000);
        }
        setInterval(createSnowflake, 200);

        /* ===============================
           MEMORY CARD SCROLL ANIMATION
        =============================== */
        const observer = new IntersectionObserver(entries => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }, index * 120);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15 });

        document.querySelectorAll('.memory-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(40px)';
            card.style.transition = 'all 0.6s ease';
            observer.observe(card);
        });

        /* ===============================
           FLOATING HEARTS
        =============================== */
        function createFloatingHeart() {
            const heart = document.createElement('div');
            heart.textContent = '❤️';
            heart.style.cssText = `
                position: fixed;
                bottom: -40px;
                left: ${Math.random() * 100}%;
                font-size: ${Math.random() * 20 + 20}px;
                opacity: ${Math.random() * 0.5 + 0.5};
                pointer-events: none;
                z-index: 999;
                animation: floatUp ${Math.random() * 3 + 4}s linear;
            `;
            document.body.appendChild(heart);
            setTimeout(() => heart.remove(), 7000);
        }
        setInterval(createFloatingHeart, 3000);

        const heartStyle = document.createElement('style');
        heartStyle.textContent = `
            @keyframes floatUp {
                to {
                    bottom: 120%;
                    transform: translateX(${Math.random() * 100 - 50}px);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(heartStyle);

        /* ===============================
           SURPRISE MODAL
        =============================== */
        const surpriseBtn = document.getElementById('surpriseBtn');
        const surpriseMessage = document.getElementById('surpriseMessage');
        const overlayModal = document.getElementById('overlay');
        const closeBtn = document.getElementById('closeBtn');

        function openSurprise() {
            surpriseMessage.classList.add('active');
            overlayModal.classList.add('active');

            if (music && music.paused) {
                music.play().catch(() => console.log('Music blocked until user interacts'));
            }

            surpriseBtn.disabled = true;
            setTimeout(() => surpriseBtn.disabled = false, 1000);
        }

        function closeSurprise() {
            surpriseMessage.classList.remove('active');
            overlayModal.classList.remove('active');
        }

        surpriseBtn.addEventListener('click', openSurprise);
        closeBtn.addEventListener('click', closeSurprise);
        overlayModal.addEventListener('click', closeSurprise);

        /* ===============================
           KEYBOARD SHORTCUTS
        =============================== */
        document.addEventListener('keydown', e => {
            if (e.key.toLowerCase() === 'l') openSurprise();
            if (e.key.toLowerCase() === 'm' && music) {
                music.paused ? music.play() : music.pause();
            }
        });

        /* ===============================
           CONSOLE MESSAGE
        =============================== */
        console.log('%c❤️ Merry Christmas, My Love! ❤️', 'color:#ff6b6b;font-size:24px;font-weight:bold;');
        console.log('%cEvery line of this page was written with love 💝', 'color:#ffd700;font-size:16px;');
    </script>

</body>

</html>