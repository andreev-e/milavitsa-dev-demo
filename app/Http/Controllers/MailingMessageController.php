<?php

namespace App\Http\Controllers;

use App\Models\MailingMessage;

class MailingMessageController extends Controller
{
    public function pixel($id)
    {
        $message = MailingMessage::findOrFail($id);
        $message->opened++;
        $message->save();
        return redirect()->secure(env('APP_URL') . '/images/pixel.png');
    }
}
