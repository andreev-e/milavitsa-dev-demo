<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LinkCounter extends Model
{
    protected $fillable = ['url', 'mailing_message_id'];

    public static function getShortLink(?string $url, int $messid): ?string
    {
        $link = self::create([
            'url' => $url,
            'mailing_message_id' => $messid,
        ]);
        $link->slug = base_convert($link->id, 10, 36);
        $link->save();
        return config('app.url') . '/shorturl/' . $link->slug . '/' . $messid;
    }

    public static function shortenLinks($message, $messid) {
        if (strpos($message, 'https://')) {
            $link = substr($message, strpos($message, 'https://'));
            $link = substr($link, 0, strpos($link . ' ', ' '));
            $shortlink = LinkCounter::getShortLink($link, $messid);
            $message = str_replace($link, $shortlink, $message);
        }
        if (strpos($message, 'http://')) {
            $link = substr($message, strpos($message, 'http://'));
            $link = substr($link, 0, strpos($link . ' ', ' '));
            $shortlink = self::getShortLink($link, $messid);
            $message = str_replace($link, $shortlink, $message);
        }
        return $message;
    }
}
