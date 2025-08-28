<!-- Ask Audience Modal -->
<dialog id="audience-modal" class="modal">
    <div class="modal-box w-11/12 max-w-2xl">
        <h3 class="font-bold text-lg mb-4">
            <i class="fas fa-users text-blue-500 mr-2"></i>
            ผลการสำรวจจากผู้ชม
        </h3>
        
        <div id="audience-chart" class="mb-6">
            <!-- Chart will be populated by JavaScript -->
        </div>
        
        <div class="text-center text-gray-600 text-sm mb-4">
            <i class="fas fa-info-circle mr-1"></i>
            ผลการสำรวจจากผู้ชม 100 คน
        </div>
        
        <div class="modal-action">
            <form method="dialog">
                <button class="btn btn-primary">ปิด</button>
            </form>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>ปิด</button>
    </form>
</dialog>

<!-- Confirmation Modal -->
<dialog id="quit-confirm-modal" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg">
            <i class="fas fa-exclamation-triangle text-warning mr-2"></i>
            ยืนยันการออกจากเกม
        </h3>
        <p class="py-4">คุณต้องการออกจากเกมหรือไม่? คะแนนที่ได้จะถูกบันทึก</p>
        <div class="modal-action">
            <form method="dialog">
                <button class="btn btn-outline">ยกเลิก</button>
                <button class="btn btn-error ml-2" onclick="window.gameQuestion.quitGame()">
                    ออกจากเกม
                </button>
            </form>
        </div>
    </div>
</dialog>

<!-- Loading Modal -->
<dialog id="loading-modal" class="modal">
    <div class="modal-box text-center">
        <div class="loading loading-spinner loading-lg text-primary mb-4"></div>
        <h3 class="font-bold text-lg">กำลังโหลด...</h3>
        <p class="text-gray-600">กรุณารอสักครู่</p>
    </div>
</dialog>
