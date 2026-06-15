<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PurchaseMail extends Mailable
{
    use Queueable, SerializesModels;

    private User $user;
    private string $phone;
    private string $appName;

    /**
     * Create a new message instance.
     */
    public function __construct(private readonly int $userId, $phone)
    {
        $this->user = User::find($userId);
        $this->phone = $phone;
        $this->appName = config('app.name', 'AIWOW');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Thanh Toán Thành Công - Thông Tin Tài Khoản '.$this->appName.' Của Bạn'
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $redirectLoginUrl = 'https://aitute.vn/login';
        Log::info('Get env app_purchase_redirect: '.$redirectLoginUrl);
        return new Content(
            view: 'mail.purchase',
            with: [
                'name' => $this->user->name,
                'email' => $this->user->email,
                'password' => $this->phone,
                'appName' => $this->appName,
                'redirectLoginUrl' => $redirectLoginUrl
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
