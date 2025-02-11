<?php

namespace DoubleThreeDigital\DigitalProducts\Mail;

use DoubleThreeDigital\SimpleCommerce\Facades\Customer;
use DoubleThreeDigital\SimpleCommerce\Facades\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Statamic\Entries\Entry;

class CustomerDownload extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public function __construct(Entry $order)
    {
        $this->order = $order;
    }

    public function build()
    {
        $order = Order::find($this->order->id());

        return $this->markdown('sc-digital-products::customer-download')
            ->subject("Downloads for Order {$order->title}")
            ->with('order', $order->data)
            ->with('cart', $order->data)
            ->with('customer', Customer::find($order->data['customer'])->data());
    }
}
