<?php

namespace App\Models;

// 1. Нууц үг сэргээх мэдэгдэл
use App\Notifications\ResetPasswordNotification;
// 2. ЭНД НЭМЭХ: Имэйл баталгаажуулах мэдэгдэл (Шинээр нэмэгдсэн)
use App\Notifications\VerifyEmailNotification;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail; // Энийг устгаж болохгүй!

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'last_name',
        'first_name',
        'email',
        'password',
        'role',
        'phone',
        'city',
        'district',
        'address',
        'avatar',
        'cart_data',
        'favorites_data',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // --- НУУЦ ҮГ СЭРГЭЭХ ФУНКЦ (Хуучин байсан) ---
    public function sendPasswordResetNotification($token)
    {
        $url = 'http://localhost:3000/reset-password?token=' . $token . '&email=' . $this->email;
        $this->notify(new ResetPasswordNotification($url));
    }

    // --- ЭНД НЭМЭХ: ИМЭЙЛ БАТАЛГААЖУУЛАХ ФУНКЦ (Шинээр нэмэгдсэн) ---
    public function sendEmailVerificationNotification()
    {
        // React-ийн баталгаажуулах хуудас руу үсрэх линк
        $url = 'http://localhost:3000/verify-email?id=' . $this->id . '&hash=' . sha1($this->email);

        $this->notify(new VerifyEmailNotification($url));
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'user_id');
    }

    public function reviewsReceived()
    {
        return $this->hasMany(Review::class, 'seller_id');
    }

    // === ШИНЭЭР НЭМЭХ ХЭСЭГ (ЧАТНЫ ХОЛБООСУУД) ===

    // Энэ хэрэглэгчийн илгээсэн мессежүүд
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    // Энэ хэрэглэгчийн хүлээн авсан мессежүүд
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }
}
