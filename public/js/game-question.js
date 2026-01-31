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
                    opt.classList.remove('selected', 'border-primary', 'bg-primary/20');
                    opt.classList.add('border-gray-200');
                });

                // Select new option
                option.classList.remove('border-gray-200');
                option.classList.add('selected');
                
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
            const quitModal = document.getElementById('quit-confirm-modal');
            if (quitModal) {
                quitModal.showModal();
            } else {
                if (confirm('คุณต้องการออกจากเกมหรือไม่?')) {
                    this.quitGame();
                }
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
        const timerContainer = document.getElementById('timer');
        
        this.timerInterval = setInterval(() => {
            this.timeLeft--;
            timerElement.textContent = this.timeLeft;
            
            // Change color when time is running out
            if (this.timeLeft <= 10) {
                timerContainer.classList.add('timer-warning');
                timerContainer.classList.remove('timer-bg');
                timerContainer.classList.add('timer-danger');
                timerElement.classList.add('text-red-600');
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
        hiddenOptions.forEach(optionKey => {
            const option = document.querySelector(`[data-answer="${optionKey}"]`);
            if (option) {
                option.classList.add('disabled-option');
                option.disabled = true;
            }
        });
    }

    showAudienceModal(percentages) {
        const modal = document.getElementById('audience-modal');
        
        // Update the percentage bars
        Object.entries(percentages).forEach(([option, percentage]) => {
            const letter = option.replace('option_', '');
            const bar = document.querySelector(`[data-audience-bar="${letter}"]`);
            const percentLabel = document.querySelector(`[data-audience-percent="${letter}"]`);
            
            if (bar) {
                bar.style.width = `${percentage}%`;
            }
            if (percentLabel) {
                percentLabel.textContent = `${percentage}%`;
            }
        });
        
        modal.showModal();
    }

    showAudienceModalOld(percentages) {
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
        
        // Disable all options immediately
        const options = document.querySelectorAll('.answer-option');
        options.forEach(option => {
            option.style.pointerEvents = 'none';
        });
        
        // Show immediate visual feedback (optimistic UI)
        const correctAnswer = window.gameData.correctAnswer;
        const isCorrect = this.selectedAnswer === correctAnswer;
        
        // Pre-render correct/wrong styling while waiting for server
        options.forEach(option => {
            const answer = option.dataset.answer;
            if (answer === correctAnswer) {
                option.classList.remove('selected');
                option.classList.add('correct');
            } else if (answer === this.selectedAnswer && !isCorrect) {
                option.classList.remove('selected');
                option.classList.add('wrong');
            }
        });
        
        try {
            const response = await fetch('/game/submit-answer', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.gameData.csrfToken,
                    'Accept': 'application/json'
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
                // UI already updated optimistically, just show result modal
                this.showAnswerResultFast(data);
            } else {
                this.showMessage('เกิดข้อผิดพลาด', data.message, 'error');
            }
        } catch (error) {
            this.showMessage('เกิดข้อผิดพลาด', 'ไม่สามารถส่งคำตอบได้', 'error');
        }
    }

    showAnswerResultFast(data) {
        // Update score with animation
        const scoreEl = document.getElementById('current-score');
        const oldScore = parseInt(scoreEl.textContent);
        const newScore = data.total_score;
        
        if (data.correct && newScore > oldScore) {
            this.animateScore(scoreEl, oldScore, newScore);
        } else {
            scoreEl.textContent = newScore;
        }
        
        // Show result modal after short delay (reduced from 1000ms to 500ms)
        setTimeout(() => {
            this.showResultModal(data);
        }, 500);
    }
    
    animateScore(element, start, end) {
        const duration = 500;
        const startTime = performance.now();
        
        const animate = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            const current = Math.round(start + (end - start) * progress);
            element.textContent = current;
            
            if (progress < 1) {
                requestAnimationFrame(animate);
            }
        };
        
        requestAnimationFrame(animate);
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
        toast.className = `fixed top-4 left-1/2 transform -translate-x-1/2 z-50 animate-slide-up`;
        
        const styles = {
            'success': {
                bg: 'bg-gradient-to-r from-emerald-500 to-green-600',
                icon: 'fa-check-circle'
            },
            'error': {
                bg: 'bg-gradient-to-r from-red-500 to-rose-600',
                icon: 'fa-times-circle'
            },
            'warning': {
                bg: 'bg-gradient-to-r from-amber-500 to-orange-600',
                icon: 'fa-exclamation-circle'
            },
            'info': {
                bg: 'bg-gradient-to-r from-blue-500 to-indigo-600',
                icon: 'fa-info-circle'
            }
        }[type] || { bg: 'bg-gradient-to-r from-blue-500 to-indigo-600', icon: 'fa-info-circle' };
        
        toast.innerHTML = `
            <div class="${styles.bg} text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-4 min-w-[280px]">
                <div class="w-12 h-12 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center">
                    <i class="fas ${styles.icon} text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-lg">${title}</h3>
                    <div class="text-sm text-white/90">${description}</div>
                </div>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.classList.add('opacity-0', 'transition-opacity', 'duration-300');
            setTimeout(() => toast.remove(), 300);
        }, 2700);
    }

    showResultModal(data) {
        const modal = document.getElementById('result-modal');
        const header = document.getElementById('result-header');
        const icon = document.getElementById('result-icon');
        const title = document.getElementById('result-title');
        const message = document.getElementById('result-message');
        const scoreValue = document.getElementById('score-value');
        const nextBtn = document.getElementById('next-question-btn');
        const nextBtnText = document.getElementById('next-btn-text');
        
        if (data.correct) {
            header.className = 'bg-gradient-to-r from-emerald-500 to-green-600 p-6 text-center';
            icon.className = 'w-20 h-20 bg-white/20 backdrop-blur rounded-full flex items-center justify-center mx-auto mb-4 animate-scale-in';
            icon.querySelector('i').className = 'fas fa-check text-white text-3xl';
            title.className = 'font-bold text-2xl mb-2 text-white';
            title.textContent = 'ถูกต้อง!';
            message.className = 'text-lg text-emerald-100';
            message.textContent = 'เยี่ยมมาก! คุณตอบถูก';
            scoreValue.className = 'text-3xl font-bold text-emerald-600';
            scoreValue.textContent = `+${data.points_earned}`;
        } else {
            header.className = 'bg-gradient-to-r from-red-500 to-rose-600 p-6 text-center';
            icon.className = 'w-20 h-20 bg-white/20 backdrop-blur rounded-full flex items-center justify-center mx-auto mb-4 animate-scale-in';
            icon.querySelector('i').className = 'fas fa-times text-white text-3xl';
            title.className = 'font-bold text-2xl mb-2 text-white';
            title.textContent = 'ผิด!';
            message.className = 'text-lg text-red-100';
            const correctLetter = data.correct_answer || window.gameData.correctAnswer.replace('option_', '').toUpperCase();
            message.textContent = `คำตอบที่ถูกต้องคือ ${correctLetter}`;
            scoreValue.className = 'text-3xl font-bold text-red-600';
            scoreValue.textContent = '0';
        }
        
        if (data.game_completed) {
            nextBtnText.textContent = 'ดูผลลัพธ์';
            nextBtn.onclick = () => {
                window.location.href = `/game/results/${window.gameData.sessionId}`;
            };
        } else {
            nextBtnText.textContent = 'คำถามถัดไป';
            nextBtn.onclick = () => {
                window.location.href = `/game/next-question/${window.gameData.sessionId}`;
            };
        }
        
        modal.showModal();
    }
}

// Initialize game when page loads
document.addEventListener('DOMContentLoaded', () => {
    window.gameQuestion = new GameQuestion();
});
