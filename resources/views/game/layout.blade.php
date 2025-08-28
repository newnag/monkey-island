<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Online Quiz Game</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .game-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .card-game {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .option-button {
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        .option-button:hover {
            background: rgba(79, 172, 254, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .correct-answer {
            background: rgba(34, 197, 94, 0.9) !important;
            color: white !important;
        }

        .wrong-answer {
            background: rgba(239, 68, 68, 0.9) !important;
            color: white !important;
        }

        .helper-button {
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        }

        .helper-button:hover:not(:disabled) {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.05);
        }

        .helper-button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .timer {
            background: linear-gradient(45deg, #ff6b6b, #ffa500);
            background-size: 200% 200%;
            animation: gradient 2s ease infinite;
        }

        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .pulse {
            animation: pulse 1s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="game-container">
        @yield('content')
    </div>

    <script>
        // Global game functions
        window.gameHelpers = {
            fiftyFifty: function() {
                // Remove 2 wrong options
                const correctAnswer = window.currentQuestion?.correct_answer;
                const options = ['a', 'b', 'c', 'd'];
                const wrongOptions = options.filter(opt => opt !== correctAnswer);
                
                // Randomly select 2 wrong options to hide
                const toHide = wrongOptions.sort(() => Math.random() - 0.5).slice(0, 2);
                
                toHide.forEach(option => {
                    const button = document.querySelector(`[data-answer="${option}"]`);
                    if (button) {
                        button.style.opacity = '0.3';
                        button.disabled = true;
                    }
                });
                
                // Disable helper button
                document.getElementById('helper-fifty-fifty').disabled = true;
                this.markHelperUsed('fifty_fifty');
            },
            
            askAudience: function() {
                // Show audience poll (simulated)
                const correctAnswer = window.currentQuestion?.correct_answer;
                const options = ['a', 'b', 'c', 'd'];
                
                // Generate fake audience votes (correct answer gets 40-70%)
                const correctPercent = Math.floor(Math.random() * 30) + 40; // 40-70%
                const remaining = 100 - correctPercent;
                const wrongPercents = [];
                
                options.forEach(opt => {
                    if (opt === correctAnswer) {
                        wrongPercents.push(correctPercent);
                    } else {
                        wrongPercents.push(Math.floor(Math.random() * (remaining / 3)));
                    }
                });
                
                let pollHtml = '<div class="bg-white p-4 rounded-lg mb-4"><h3 class="font-bold mb-2">👥 ผลโหวตจากผู้ชม</h3>';
                options.forEach((opt, index) => {
                    pollHtml += `<div class="flex items-center mb-1">
                        <span class="w-8">${opt.toUpperCase()}:</span>
                        <div class="flex-1 bg-gray-200 rounded-full h-4 ml-2">
                            <div class="bg-blue-500 h-4 rounded-full" style="width: ${wrongPercents[index]}%"></div>
                        </div>
                        <span class="ml-2 w-12 text-right">${wrongPercents[index]}%</span>
                    </div>`;
                });
                pollHtml += '</div>';
                
                document.getElementById('audience-poll').innerHTML = pollHtml;
                document.getElementById('helper-ask-audience').disabled = true;
                this.markHelperUsed('ask_audience');
            },
            
            extraTime: function() {
                // Add 30 seconds
                if (window.gameTimer) {
                    window.timeLeft += 30;
                    document.getElementById('time-display').textContent = window.timeLeft;
                }
                
                document.getElementById('helper-extra-time').disabled = true;
                this.markHelperUsed('extra_time');
            },
            
            skipQuestion: function() {
                // Skip to next question
                if (confirm('คุณแน่ใจหรือไม่ที่จะข้ามข้อนี้? (จะไม่ได้คะแนนสำหรับข้อนี้)')) {
                    document.getElementById('helper-skip').disabled = true;
                    this.markHelperUsed('skip');
                    this.nextQuestion();
                }
            },
            
            markHelperUsed: function(helper) {
                if (!window.helpersUsed) {
                    window.helpersUsed = [];
                }
                window.helpersUsed.push(helper);
            },
            
            nextQuestion: function() {
                // This will be implemented in game logic
                if (window.nextQuestionCallback) {
                    window.nextQuestionCallback();
                }
            }
        };
    </script>

    @stack('scripts')
</body>
</html>
