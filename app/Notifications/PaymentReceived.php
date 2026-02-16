<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReceived extends Notification
{
    use Queueable;

    public $details;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'amount' => $this->details['amount'],
            'buyer_name' => $this->details['buyer_name'],
            'product_name' => $this->details['product_name'],
            'order_id' => $this->details['order_id'],
            'payment_id' => $this->details['payment_id'] ?? 'N/A',
            'internal_order_id' => $this->details['internal_order_id'] ?? null,
            'shipping_status' => 'pending', // Default status for new orders
            'message' => 'Payment received of ₹' . $this->details['amount'] . ' for ' . $this->details['product_name'] . ' from ' . $this->details['buyer_name']
        ];
    }
}
