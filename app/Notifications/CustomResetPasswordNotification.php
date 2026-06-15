<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPasswordNotification extends ResetPasswordNotification
{
    /**
     * Tùy chỉnh nội dung email.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $resetUrl = $this->resetUrl($notifiable);

        return (new MailMessage)
            ->subject('Yêu cầu đặt lại mật khẩu')
            ->greeting('Xin chào!')
            ->line('Chúng tôi nhận được yêu cầu đặt lại mật khẩu từ bạn.')
            ->action('Đặt lại mật khẩu', $resetUrl)
            ->line('Nếu bạn không yêu cầu, vui lòng bỏ qua email này.')
            ->salutation('Trân trọng, đội ngũ hỗ trợ của chúng tôi.');
    }

    /**
     * Tạo liên kết đặt lại mật khẩu.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function resetUrl($notifiable)
    {
        return url(route('settings.password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));
    }
}
