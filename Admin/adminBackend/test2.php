<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Merry Christmas & Happy New Year</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            font-family: 'Georgia', serif;
        }

        .snow {
            position: fixed;
            top: -10px;
            width: 100%;
            height: 100vh;
            pointer-events: none;
            z-index: 1;
        }

        .snowflake {
            position: absolute;
            top: -10px;
            color: white;
            font-size: 1em;
            opacity: 0.8;
            animation: fall linear infinite;
        }

        @keyframes fall {
            to {
                transform: translateY(100vh);
            }
        }

        .main-container {
            position: relative;
            z-index: 2;
            padding: 40px 20px;
        }

        .hero-section {
            text-align: center;
            padding: 60px 20px;
            color: white;
            animation: fadeInDown 1s ease-out;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: bold;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.5);
            margin-bottom: 20px;
            background: linear-gradient(45deg, #ff6b6b, #feca57, #48dbfb, #ff9ff3);
            background-size: 300% 300%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: gradientShift 3s ease infinite;
        }

        @keyframes gradientShift {

            0%,
            100% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }
        }

        .hero-subtitle {
            font-size: 1.5rem;
            color: #ffd700;
            text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.5);
        }

        .card-custom {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            margin-bottom: 30px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            animation: fadeInUp 1s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card-custom:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 50px rgba(255, 107, 107, 0.4);
        }

        .card-img-top {
            height: 300px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .card-custom:hover .card-img-top {
            transform: scale(1.05);
        }

        .card-body {
            padding: 30px;
        }

        .card-title {
            color: #c0392b;
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .card-text {
            color: #2c3e50;
            line-height: 1.8;
            font-size: 1.1rem;
        }

        .year-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 15px;
        }

        .lights {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 50px;
            z-index: 3;
            display: flex;
            justify-content: space-around;
            padding: 10px;
        }

        .light {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            animation: blink 1s infinite;
        }

        .light:nth-child(odd) {
            background: #ff6b6b;
            animation-delay: 0s;
        }

        .light:nth-child(even) {
            background: #4ecdc4;
            animation-delay: 0.5s;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1;
                box-shadow: 0 0 20px currentColor;
            }

            50% {
                opacity: 0.3;
                box-shadow: 0 0 5px currentColor;
            }
        }

        .heart {
            color: #e74c3c;
            font-size: 2rem;
            animation: heartbeat 1.5s infinite;
            display: inline-block;
        }

        @keyframes heartbeat {

            0%,
            100% {
                transform: scale(1);
            }

            25% {
                transform: scale(1.2);
            }

            50% {
                transform: scale(1);
            }
        }

        .final-message {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 60px 30px;
            border-radius: 20px;
            text-align: center;
            margin: 40px 0;
            box-shadow: 0 10px 40px rgba(245, 87, 108, 0.4);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.02);
            }
        }

        .final-message h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .final-message p {
            font-size: 1.3rem;
            line-height: 1.8;
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .hero-subtitle {
                font-size: 1.2rem;
            }
        }
    </style>
</head>

<body>
    <audio id="bg-music" autoplay loop>
        <source src="your-music-file.mp3" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>

    <div class="lights">
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

    <div class="snow" id="snow"></div>

    <div class="main-container">
        <div class="hero-section">
            <h1 class="hero-title">Merry Christmas & Happy New Year!</h1>
            <p class="hero-subtitle">To My Beautiful Girlfriend <span class="heart">❤️</span></p>
        </div>

        <div class="container">
            <div class="row">
                <!-- 2018 -->
                <div class="col-md-6 col-lg-4">
                    <div class="card-custom">
                        <img src="https://images.unsplash.com/photo-1512389142860-9c449e58a543?w=600"
                            class="card-img-top" alt="Christmas 2018">
                        <div class="card-body">
                            <span class="year-badge">2019</span>
                            <h5 class="card-title">Where Our Love Began</h5>
                            <p class="card-text">Our first Christmas together, under twinkling parols and . The first
                                christmas "I love you" just for us, a memory I’ll treasure
                                forever.</p>
                        </div>
                    </div>
                </div>

                <!-- 2019 -->
                <div class="col-md-6 col-lg-4">
                    <div class="card-custom">
                        <img src="https://images.unsplash.com/photo-1482517967863-00e15c9b44be?w=600"
                            class="card-img-top" alt="Christmas 2019">
                        <div class="card-body">
                            <span class="year-badge">2019</span>
                            <h5 class="card-title">Growing Closer</h5>
                            <p class="card-text">With every sunset over the islands and every laugh we shared, our love
                                blossomed. You became my partner, my best friend, my heart’s home.</p>
                        </div>
                    </div>
                </div>

                <!-- 2020 -->
                <div class="col-md-6 col-lg-4">
                    <div class="card-custom">
                        <img src="https://images.unsplash.com/photo-1544273677-ac73e3fef4ed?w=600" class="card-img-top"
                            alt="Christmas 2020">
                        <div class="card-body">
                            <span class="year-badge">2020</span>
                            <h5 class="card-title">Love Through Challenges</h5>
                            <p class="card-text">Even when the world felt uncertain, your smile was my sun. Hand in
                                hand, we proved that love conquers all.</p>
                        </div>
                    </div>
                </div>

                <!-- 2021 -->
                <div class="col-md-6 col-lg-4">
                    <div class="card-custom">
                        <img src="https://images.unsplash.com/photo-1576919228236-a097c32a5cd4?w=600"
                            class="card-img-top" alt="Christmas 2021">
                        <div class="card-body">
                            <span class="year-badge">2021</span>
                            <h5 class="card-title">Tropical Adventures</h5>
                            <p class="card-text">From beach trips to quiet evenings watching the sunset, every moment
                                with you was a new adventure, and I fell more in love with you every day.</p>
                        </div>
                    </div>
                </div>

                <!-- 2022 -->
                <div class="col-md-6 col-lg-4">
                    <div class="card-custom">
                        <img src="https://images.unsplash.com/photo-1511447333015-45b65e60f6d5?w=600"
                            class="card-img-top" alt="Christmas 2022">
                        <div class="card-body">
                            <span class="year-badge">2022</span>
                            <h5 class="card-title">Building Our Dreams</h5>
                            <p class="card-text">Together we dreamed, built, and laughed. Your love gives me courage,
                                and our life together is my favorite story.</p>
                        </div>
                    </div>
                </div>

                <!-- 2023 -->
                <div class="col-md-6 col-lg-4">
                    <div class="card-custom">
                        <img src="https://images.unsplash.com/photo-1543589077-47d81606c1bf?w=600" class="card-img-top"
                            alt="Christmas 2023">
                        <div class="card-body">
                            <span class="year-badge">2023</span>
                            <h5 class="card-title">Stronger Every Year</h5>
                            <p class="card-text">Seven years together, and my heart still races for you. You are my
                                everyday miracle, my endless Christmas joy.</p>
                        </div>
                    </div>
                </div>

                <!-- 2024 -->
                <div class="col-md-6 col-lg-4">
                    <div class="card-custom">
                        <img src="https://images.unsplash.com/photo-1514160945036-8c814d6e8e61?w=600"
                            class="card-img-top" alt="Christmas 2024">
                        <div class="card-body">
                            <span class="year-badge">2024</span>
                            <h5 class="card-title">Our Bright Today</h5>
                            <p class="card-text">Here we are, laughing, loving, and choosing each other every single
                                day. You make my heart feel like home and my life a paradise.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="final-message">
                <h2>My Dearest Love <span class="heart">💝</span></h2>
                <p>
                    Seven years of sunsets, seven years of shared laughter, seven years of love that keeps growing. You
                    are my greatest gift, and I thank the stars every night for you.
                </p>
                <p>
                    As we celebrate this Christmas and welcome the New Year, know that my love for you blooms brighter
                    each day. You are my today and all of my tomorrows.
                </p>
                <p style="font-size: 1.5rem; margin-top: 30px;">
                    <strong>Merry Christmas and a Happy New Year 2025, My Love!</strong>
                </p>
                <p style="font-size: 1.3rem;">
                    Here’s to more laughter, love, and sun-kissed memories together. ✨
                </p>
                <p style="font-size: 2rem; margin-top: 20px;">
                    Forever Yours ❤️
                </p>
            </div>
        </div>

    </div>

    <script>
        window.addEventListener('load', () => {
            const music = document.getElementById('bg-music');
            music.volume = 0.5; // Set volume from 0.0 to 1.0
            music.play().catch(e => console.log("Autoplay blocked:", e));
        });
    </script>


    <script>
        // Create snowflakes
        function createSnowflake() {
            const snowflake = document.createElement('div');
            snowflake.classList.add('snowflake');
            snowflake.innerHTML = '❄';
            snowflake.style.left = Math.random() * window.innerWidth + 'px';
            snowflake.style.animationDuration = Math.random() * 3 + 2 + 's';
            snowflake.style.fontSize = Math.random() * 10 + 10 + 'px';
            snowflake.style.opacity = Math.random();

            document.getElementById('snow').appendChild(snowflake);

            setTimeout(() => {
                snowflake.remove();
            }, 5000);
        }

        setInterval(createSnowflake, 200);
    </script>
</body>

</html>