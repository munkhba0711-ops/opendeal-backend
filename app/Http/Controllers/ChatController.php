<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    // 1. УНШААГҮЙ МЕССЕЖИЙН НИЙТ ТОО
    public function getUnreadCount()
    {
        $count = Message::where('receiver_id', Auth::id())->where('is_read', false)->count();
        return response()->json(['unread' => $count]);
    }

    // 2. МЕССЕЖ ТАТАХ БОЛОН УНШСАН БОЛГОХ
    public function getMessages($userId)
    {
        $authId = Auth::id();

        // Надад ирсэн уншаагүй мессежүүдийг 'Уншсан' (is_read = true) болгох
        Message::where('sender_id', $userId)
            ->where('receiver_id', $authId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // with('product') ашиглан барааны мэдээллийг давхар татна
        $messages = Message::with('product')
            ->where(function ($q) use ($authId, $userId) {
                $q->where('sender_id', $authId)->where('receiver_id', $userId);
            })->orWhere(function ($q) use ($authId, $userId) {
                $q->where('sender_id', $userId)->where('receiver_id', $authId);
            })->orderBy('created_at', 'asc')->get();

        return response()->json($messages);
    }

    // 3. ЗУРВАС ИЛГЭЭХ
    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
            'product_id' => 'nullable|exists:products,id' // Барааны ID нэмэлтээр ирж болно
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
            'product_id' => $request->product_id
        ]);

        return response()->json(Message::with('product')->find($message->id));
    }

    // 4. ЧАТНЫ ЖАГСААЛТ АВАХ
    public function getConversations()
    {
        $authId = Auth::id();
        $users = User::whereHas('sentMessages', function ($q) use ($authId) {
            $q->where('receiver_id', $authId);
        })->orWhereHas('receivedMessages', function ($q) use ($authId) {
            $q->where('sender_id', $authId);
        })->get();

        // Хүн тус бүрээс хэдэн уншаагүй мессеж ирснийг тоолох
        foreach ($users as $user) {
            $user->unread_count = Message::where('sender_id', $user->id)
                                         ->where('receiver_id', $authId)
                                         ->where('is_read', false)->count();
        }

        return response()->json($users);
    }

    // 5. ЧАТ УСТГАХ
    public function deleteConversation($userId)
    {
        $authId = Auth::id();
        Message::where(function ($q) use ($authId, $userId) {
            $q->where('sender_id', $authId)->where('receiver_id', $userId);
        })->orWhere(function ($q) use ($authId, $userId) {
            $q->where('sender_id', $userId)->where('receiver_id', $authId);
        })->delete();

        return response()->json(['message' => 'Чат амжилттай устгагдлаа']);
    }
}
