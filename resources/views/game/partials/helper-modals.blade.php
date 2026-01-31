<!-- Ask Audience Modal -->
<dialog id="audience-modal" class="modal modal-bottom sm:modal-middle">
    <div class="modal-box w-full sm:w-11/12 max-w-2xl bg-white rounded-t-3xl sm:rounded-3xl p-0 overflow-hidden">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-4 sm:p-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-white/20 backdrop-blur rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-lg sm:text-xl text-white">ผลการสำรวจจากผู้ชม</h3>
                    <p class="text-blue-100 text-sm">จากผู้ชม 100 คน</p>
                </div>
            </div>
        </div>
        
        <!-- Chart Content -->
        <div id="audience-chart" class="p-4 sm:p-6">
            <!-- Chart will be populated by JavaScript -->
            <div class="space-y-4">
                @foreach(['A', 'B', 'C', 'D'] as $opt)
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-100 to-indigo-200 flex items-center justify-center font-bold text-indigo-600">
                        {{ $opt }}
                    </div>
                    <div class="flex-1">
                        <div class="w-full bg-gray-200 rounded-full h-6 overflow-hidden">
                            <div class="bg-gradient-to-r from-blue-400 to-indigo-500 h-full rounded-full transition-all duration-1000 flex items-center justify-end pr-2" 
                                 style="width: 0%" data-audience-bar="{{ strtolower($opt) }}">
                                <span class="text-white text-xs font-bold" data-audience-percent="{{ strtolower($opt) }}">0%</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        
        <div class="p-4 sm:p-6 pt-0">
            <div class="text-center text-gray-500 text-sm mb-4">
                <i class="fas fa-info-circle mr-1"></i>
                ข้อมูลนี้เป็นเพียงการประมาณการจากผู้ชม
            </div>
            
            <div class="modal-action justify-center mt-0">
                <form method="dialog">
                    <button class="w-full sm:w-auto px-8 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                        <i class="fas fa-check mr-2"></i>
                        เข้าใจแล้ว
                    </button>
                </form>
            </div>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button class="cursor-default">ปิด</button>
    </form>
</dialog>

<!-- Confirmation Modal -->
<dialog id="quit-confirm-modal" class="modal modal-bottom sm:modal-middle">
    <div class="modal-box w-full sm:w-96 bg-white rounded-t-3xl sm:rounded-3xl p-0 overflow-hidden">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-red-500 to-orange-500 p-6 text-center">
            <div class="w-16 h-16 bg-white/20 backdrop-blur rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-exclamation-triangle text-white text-2xl"></i>
            </div>
            <h3 class="font-bold text-xl text-white">ออกจากเกม?</h3>
        </div>
        
        <div class="p-6">
            <p class="text-center text-gray-600 mb-6">
                คุณต้องการออกจากเกมหรือไม่?<br>
                <span class="text-sm text-gray-500">คะแนนที่ได้จะถูกบันทึก</span>
            </p>
            
            <div class="flex flex-col sm:flex-row gap-3">
                <form method="dialog" class="flex-1">
                    <button class="w-full py-3 px-6 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-all duration-300">
                        <i class="fas fa-arrow-left mr-2"></i>
                        กลับไปเล่นต่อ
                    </button>
                </form>
                <button class="flex-1 py-3 px-6 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300" 
                        onclick="window.gameQuestion.quitGame()">
                    <i class="fas fa-sign-out-alt mr-2"></i>
                    ออกจากเกม
                </button>
            </div>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button class="cursor-default">ปิด</button>
    </form>
</dialog>

<!-- Loading Modal -->
<dialog id="loading-modal" class="modal">
    <div class="modal-box w-72 bg-white rounded-3xl text-center p-8">
        <div class="relative w-20 h-20 mx-auto mb-4">
            <div class="absolute inset-0 border-4 border-indigo-200 rounded-full"></div>
            <div class="absolute inset-0 border-4 border-indigo-500 rounded-full border-t-transparent animate-spin"></div>
        </div>
        <h3 class="font-bold text-lg text-gray-800 mb-1">กำลังโหลด...</h3>
        <p class="text-gray-500 text-sm">กรุณารอสักครู่</p>
    </div>
</dialog>

<!-- Result Modal -->
<dialog id="result-modal" class="modal modal-bottom sm:modal-middle">
    <div class="modal-box w-full sm:w-96 bg-white rounded-t-3xl sm:rounded-3xl p-0 overflow-hidden">
        <!-- Success Header -->
        <div id="result-header" class="p-6 text-center">
            <div id="result-icon" class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4 animate-scale-in">
                <i class="text-3xl"></i>
            </div>
            <h3 id="result-title" class="font-bold text-2xl mb-2"></h3>
            <p id="result-message" class="text-lg"></p>
        </div>
        
        <!-- Score Display -->
        <div class="px-6 pb-6">
            <div id="score-change" class="bg-gray-50 rounded-2xl p-4 mb-4 text-center">
                <div class="text-gray-500 text-sm mb-1">คะแนนที่ได้</div>
                <div id="score-value" class="text-3xl font-bold"></div>
            </div>
            
            <button id="next-question-btn" 
                    class="w-full py-4 px-6 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                <span id="next-btn-text">คำถามถัดไป</span>
                <i class="fas fa-arrow-right ml-2"></i>
            </button>
        </div>
    </div>
</dialog>
