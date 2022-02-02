<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MailingMessage;

class IdgtlController extends Controller
{
    public function callback($id, Request $request) {
        $message = MailingMessage::find($id);
        if (is_object($message)) {
            $message->status = $request->input('status');
            $message->save();
            if (in_array($message->status, ['undelivered', 'unsent'])) {
                $message->queueNext();
            }
        }
    }
}
