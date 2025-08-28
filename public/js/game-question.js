// Game Question Logic
class GameQuestion {
    constructor() {
        this.selectedAnswer = null;
        this.timeLeft = window.gameData.timeLimit;
        this.timerInterval = null;
        this.gameEnded = false;
        
        this.init();
        this.startTimer();
    }

    init() {
        // Answer option handlers
        this.setupAnswerOptions();
        
        // Helper button handlers
        this.setupHelperButtons();
        
        // Submit and quit button handlers
        this.setupActionButtons();
        
        // Keyboard shortcuts
        this.setupKeyboardShortcuts();
    }

    setupAnswerOptions() {
        const options = document.querySelectorAll('.answer-option');
        const submitBtn = document.getElementById('submit-answer');

        options.forEach(option => {
            option.addEventListener('click', () => {
                if (this.gameEnded) return;

                // Remove previous selection
                options.forEach(opt => {
                    opt.classList.remove('border-primary', 'bg-primary/20');
                    opt.classList.add('border-gray-200');
                });

                // Select new option
                option.classList.remove('border-gray-200');
                option.classList.add('border-primary', 'bg-primary/20');
                
                this.selectedAnswer = option.dataset.answer;
                submitBtn.disabled = false;
            });
        });
    }

    setupHelperButtons() {
        const helperButtons = document.querySelectorAll('[data-helper]');
        
        helperButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                if (btn.disabled || this.gameEnded) return;
                
                const helperType = btn.dataset.helper;
                this.useHelper(helperType, btn);
            });
        });
    }

    setupActionButtons() {
        const submitBtn = document.getElementById('submit-answer');
        const quitBtn = document.getElementById('quit-game');

        submitBtn.addEventListener('click', () => {
            if (this.selectedAnswer && !this.gameEnded) {
                this.submitAnswer();
            }
        });

        quitBtn.addEventListener('click', () => {
            if (confirm('คุณต้องการออกจากเกมหรือไม่?')) {
                this.quitGame();
            }
        });
    }

    setupKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            if (this.gameEnded) return;

            // A, B, C, D keys for answers
            if (['KeyA', 'KeyB', 'KeyC', 'KeyD'].includes(e.code)) {
                const index = ['KeyA', 'KeyB', 'KeyC', 'KeyD'].indexOf(e.code);
                const options = document.querySelectorAll('.answer-option');
                if (options[index]) {
                    options[index].click();
                }
            }
            
            // Enter to submit
            if (e.code === 'Enter' && this.selectedAnswer) {
                this.submitAnswer();
            }
            
            // Escape to quit
            if (e.code === 'Escape') {
                document.getElementById('quit-game').click();
            }
        });
    }

    startTimer() {
        const timerElement = document.getElementById('time-left');
        
        this.timerInterval = setInterval(() => {
            this.timeLeft--;
            timerElement.textContent = this.timeLeft;
            
            // Change color when time is running out
            const timerContainer = document.getElementById('timer');
            if (this.timeLeft <= 10) {
                timerContainer.classList.add('animate-pulse');
                timerContainer.classList.remove('text-error');
                timerContainer.classList.add('text-red-600');
            }
            
            // Time's up
            if (this.timeLeft <= 0) {
                this.timeUp();
            }
        }, 1000);
    }

    stopTimer() {
        if (this.timerInterval) {
            clearInterval(this.timerInterval);
            this.timerInterval = null;
        }
    }

    timeUp() {
        this.gameEnded = true;
        this.stopTimer();
        
        // Auto submit with no answer
        this.submitAnswer(true);
        
        this.showMessage('หมดเวลา!', 'เวลาหมดแล้ว กำลังไปคำถามถัดไป...', 'warning');
    }

    async useHelper(helperType, button) {
        button.disabled = true;
        button.classList.add('opacity-50');
        
        try {
            const response = await fetch('/game/use-helper', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.gameData.csrfToken
                },
                body: JSON.stringify({
                    session_id: window.gameData.sessionId,
                    helper_type: helperType,
                    question_id: window.gameData.questionId
                })
            });

            const data = await response.json();
            
            if (data.success) {
                this.applyHelper(helperType, data.data);
                this.showMessage('ใช้ตัวช่วยสำเร็จ!', data.message, 'success');
            } else {
                button.disabled = false;
                button.classList.remove('opacity-50');
                this.showMessage('เกิดข้อผิดพลาด', data.message, 'error');
            }
        } catch (error) {
            button.disabled = false;
            button.classList.remove('opacity-50');
            this.showMessage('เกิดข้อผิดพลาด', 'ไม่สามารถใช้ตัวช่วยได้', 'error');
        }
    }

    applyHelper(helperType, data) {
        switch (helperType) {
            case 'fifty_fifty':
                this.applyFiftyFifty(data.hidden_options);
                break;
                
            case 'ask_audience':
                this.showAudienceModal(data.percentages);
                break;
                
            case 'extra_time':
                this.addExtraTime(data.extra_seconds);
                break;
                
            case 'skip':
                this.skipQuestion();
                break;
        }
    }

    applyFiftyFifty(hiddenOptions) {
        const options = document.querySelectorAll('.answer-option');
        
        hiddenOptions.forEach(optionKey => {
            const option = document.querySelector(`[data-answer="${optionKey}"]`);
            if (option) {
                option.style.opacity = '0.3';
                option.disabled = true;
                option.classList.add('pointer-events-none');
            }
        });
    }

    showAudienceModal(percentages) {
        const modal = document.getElementById('audience-modal');
        const chartContainer = modal.querySelector('#audience-chart');
        
        // Create simple percentage bars
        let chartHTML = '';
        Object.entries(percentages).forEach(([option, percentage]) => {
            const letter = option.replace('option_', '').toUpperCase();
            chartHTML += `
                <div class="mb-3">
                    <div class="flex justify-between items-center mb-1">
                        <span class="font-semibold">ตัวเลือก ${letter}</span>
                        <span class="text-primary font-bold">${percentage}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-4">
                        <div class="bg-primary h-4 rounded-full transition-all duration-1000" 
                             style="width: ${percentage}%"></div>
                    </div>
                </div>
            `;
        });
        
        chartContainer.innerHTML = chartHTML;
        modal.showModal();
    }

    addExtraTime(extraSeconds) {
        this.timeLeft += extraSeconds;
        this.showMessage('เพิ่มเวลาสำเร็จ!', `เพิ่มเวลา ${extraSeconds} วินาที`, 'success');
    }

    skipQuestion() {
        this.gameEnded = true;
        this.stopTimer();
        
        // Go to next question
        setTimeout(() => {
            window.location.href = `/game/next-question/${window.gameData.sessionId}`;
        }, 1500);
    }

    async submitAnswer(timeUp = false) {
        if (this.gameEnded && !timeUp) return;
        
        this.gameEnded = true;
        this.stopTimer();
        
        const submitBtn = document.getElementById('submit-answer');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>กำลังตรวจสอบ...';
        
        // Disable all options
        document.querySelectorAll('.answer-option').forEach(option => {
            option.disabled = true;
        });
        
        try {
            const response = await fetch('/game/submit-answer', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.gameData.csrfToken
                },
                body: JSON.stringify({
                    session_id: window.gameData.sessionId,
                    question_id: window.gameData.questionId,
                    selected_answer: this.selectedAnswer || null,
                    time_left: this.timeLeft,
                    time_up: timeUp
                })
            });

            const data = await response.json();
            
            if (data.success) {
                this.showAnswerResult(data);
            } else {
                this.showMessage('เกิดข้อผิดพลาด', data.message, 'error');
            }
        } catch (error) {
            this.showMessage('เกิดข้อผิดพลาด', 'ไม่สามารถส่งคำตอบได้', 'error');
        }
    }

    showAnswerResult(data) {
        const correctAnswer = window.gameData.correctAnswer;
        const options = document.querySelectorAll('.answer-option');
        
        // Show correct/incorrect answers
        options.forEach(option => {
            const answer = option.dataset.answer;
            
            if (answer === correctAnswer) {
                option.classList.add('border-green-500', 'bg-green-100');
                option.querySelector('.option-label').classList.add('bg-green-500');
            } else if (answer === this.selectedAnswer) {
                option.classList.add('border-red-500', 'bg-red-100');
                option.querySelector('.option-label').classList.add('bg-red-500');
            }
        });
        
        // Update score
        document.getElementById('current-score').textContent = data.total_score;
        
        // Show result message
        const message = data.correct ? 'ถูกต้อง!' : 'ผิด!';
        const description = data.correct ? 
            `ได้คะแนน +${data.points_earned}` : 
            `คำตอบที่ถูกต้องคือ ${correctAnswer.replace('option_', '').toUpperCase()}`;
        
        this.showMessage(message, description, data.correct ? 'success' : 'error');
        
        // Go to next question or results
        setTimeout(() => {
            if (data.game_completed) {
                window.location.href = `/game/results/${window.gameData.sessionId}`;
            } else {
                window.location.href = `/game/next-question/${window.gameData.sessionId}`;
            }
        }, 3000);
    }

    quitGame() {
        this.gameEnded = true;
        this.stopTimer();
        
        fetch('/game/quit', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.gameData.csrfToken
            },
            body: JSON.stringify({
                session_id: window.gameData.sessionId
            })
        }).finally(() => {
            window.location.href = '/game';
        });
    }

    showMessage(title, description, type = 'info') {
        // Create toast notification
        const toast = document.createElement('div');
        toast.className = `toast toast-top toast-center z-50`;
        
        const alertClass = {
            'success': 'alert-success',
            'error': 'alert-error', 
            'warning': 'alert-warning',
            'info': 'alert-info'
        }[type] || 'alert-info';
        
        toast.innerHTML = `
            <div class="alert ${alertClass}">
                <div>
                    <h3 class="font-bold">${title}</h3>
                    <div class="text-xs">${description}</div>
                </div>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }
}

// Initialize game when page loads
document.addEventListener('DOMContentLoaded', () => {
    window.gameQuestion = new GameQuestion();
});
