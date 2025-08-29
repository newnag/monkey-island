<?php

namespace App\Http\Controllers;

use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Validator;

class PlayerController extends Controller
{
    public function showNicknameSetup()
    {
        return view('player.nickname-setup');
    }

    public function setupNickname(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nickname' => 'required|string|max:20|min:1',
        ], [
            'nickname.required' => 'กรุณาใส่ชื่อเล่น',
            'nickname.max' => 'ชื่อเล่นต้องไม่เกิน 20 ตัวอักษร',
            'nickname.min' => 'ชื่อเล่นต้องมีอย่างน้อย 1 ตัวอักษร',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Check if player already exists with cookie
        $playerCode = Cookie::get('player_code');
        $player = null;

        if ($playerCode) {
            $player = Player::where('player_code', $playerCode)->first();
        }

        // Create new player or update existing
        if ($player) {
            $player->update([
                'nickname' => $request->nickname,
                'ip_address' => $request->ip(),
            ]);
        } else {
            $player = Player::create([
                'player_code' => Player::generatePlayerCode(),
                'nickname' => $request->nickname,
                'ip_address' => $request->ip(),
            ]);
        }

        // Set cookie for 30 days
        Cookie::queue('player_code', $player->player_code, 43200);
        Cookie::queue('player_nickname', $player->nickname, 43200);

        return redirect()->route('home')->with('success', 'ยินดีต้อนรับ '.$player->nickname.'!');
    }

    public function editNickname()
    {
        $playerCode = Cookie::get('player_code');

        if (! $playerCode) {
            return redirect()->route('player.nickname-setup');
        }

        $player = Player::where('player_code', $playerCode)->first();

        if (! $player) {
            return redirect()->route('player.nickname-setup');
        }

        return view('player.edit-nickname', compact('player'));
    }

    public function updateNickname(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nickname' => 'required|string|max:20|min:1',
        ], [
            'nickname.required' => 'กรุณาใส่ชื่อเล่น',
            'nickname.max' => 'ชื่อเล่นต้องไม่เกิน 20 ตัวอักษร',
            'nickname.min' => 'ชื่อเล่นต้องมีอย่างน้อย 1 ตัวอักษร',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $playerCode = Cookie::get('player_code');

        if (! $playerCode) {
            return redirect()->route('player.nickname-setup');
        }

        $player = Player::where('player_code', $playerCode)->first();

        if (! $player) {
            return redirect()->route('player.nickname-setup');
        }

        $player->update([
            'nickname' => $request->nickname,
        ]);

        // Update cookie
        Cookie::queue('player_nickname', $player->nickname, 43200);

        return redirect()->route('home')->with('success', 'เปลี่ยนชื่อเล่นเป็น '.$player->nickname.' แล้ว!');
    }
}
