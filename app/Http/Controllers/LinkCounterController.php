<?php

namespace App\Http\Controllers;
use App\Models\LinkCounter;
use App\Models\MailingMessage;
use Illuminate\Http\Request;

class LinkCounterController extends Controller
{
    public function shortUrl($slug, $message) {
        $link = LinkCounter::where([
            'slug' => $slug,
            'mailing_message_id' => $message
            ])->firstOrFail();
        $link->opened++;
        $link->save();
        return redirect($link->url);
    }
}
