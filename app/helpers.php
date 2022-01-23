<?php

use App\Models\Shop;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

function get_select_options($name, $field = 'text', $all = FALSE)
{
    switch ($name) {
        case 'user_id':
            $users = User::getSelectOptions();
            $users[0] = 'Выбрать';
            ksort($users);
            return $users;
            break;
        case 'shop_id':
            $shops = Shop::getSelectOptions();
            $shops[0] = 'Выбрать';
            ksort($shops);
            return $shops;
            break;
        case 'task_list_id':
            return TaskList::getSelectOptions();
            break;
        case 'task_id':
            return Task::getSelectOptions($field, $all);
            break;
        default:
            return [];
    }
}

function phone($phone)
{
    if (strlen(clearPhone($phone)) == 10) {
        $phone = clearPhone($phone);
        return '+7 (' . $phone[0] . $phone[1] . $phone[2] . ') ' . $phone[3] . $phone[4] . $phone[5] . ' ' . $phone[6] . $phone[7] . $phone[8] . $phone[9];
    }
    return $phone;
}

function get_date($datetime)
{
    return Carbon::parse($datetime)->toDateString();
}

function get_minute($datetime)
{
    return Carbon::parse($datetime)->minute;
}

function get_hour($datetime)
{
    return Carbon::parse($datetime)->hour;
}

function get_status_name($status)
{
    switch ($status) {
        case 1:
            return 'Активен';
            break;
        case 0:
            return 'Отключен';
            break;
        default:
            return 'Неизвестен';
    }
}

function get_status_button($status)
{
    switch ($status) {
        case 1:
            return 'Уволить';
            break;
        case 0:
            return 'Оформить';
            break;
        default:
            return 'Неизвестен';
    }
}

function get_attachment_link($fileId)
{
    $token = config('telegram.bots.milavitsa.token');
    if (empty($token) || empty($fileId)) {
        return '';
    }
    return 'https://api.telegram.org/bot' . $token . '/getFile?file_id=' . $fileId;
}

function changeTextToLink($text)
{
    $text = preg_replace("/(^|[\n ])([\w]*?)((www|ftp)\.[^ \,\"\t\n\r<]*[^ \.])/is", "$1$2<a href=\"http://$3\" target=\"_blank\">файл</a>", $text);
    $text = preg_replace("/(^|[\n ])([\w]*?)((ht|f)tp(s)?:\/\/[\w]+[^ \,\"\n\r\t<]*[^ \.])/is", "$1$2<a href=\"$3\" target=\"_blank\">файл</a>", $text);
    return ($text);
}

function changeTextToLink2($text)
{
    $text = preg_replace("/(^|[\n ])([\w]*?)((www|ftp)\.[^ \,\"\t\n\r<]*[^ \.])/is", "$1$2<a href=\"http://$3\" target=\"_blank\">ссылка</a>", $text);
    $text = preg_replace("/(^|[\n ])([\w]*?)((ht|f)tp(s)?:\/\/[\w]+[^ \,\"\n\r\t<]*[^ \.])/is", "$1$2<a href=\"$3\" target=\"_blank\">ссылка</a>", $text);
    return ($text);
}

function changeTextToLink3($text)
{
    $text = preg_replace("/(^|[\n ])([\w]*?)((www|ftp)\.[^ \,\"\t\n\r<]*[^ \.])/is", "$1$2<a href=\"http://$3\" target=\"_blank\">$3</a>", $text);
    $text = preg_replace("/(^|[\n ])([\w]*?)((ht|f)tp(s)?:\/\/[\w]+[^ \,\"\n\r\t<]*[^ \.])/is", "$1$2<a href=\"$3\" target=\"_blank\">$3</a>", $text);
    return ($text);
}

function TelegramSendMessage($array, $delete = FALSE, $dataBase = TRUE)
{
    if (in_array(config('app.env'), ['local', 'testing']))
        return;
    $message = 0;
    try {
        $message = Telegram\Bot\Laravel\Facades\Telegram::sendMessage($array);
        $data = [
            'chat_id' => $array['chat_id'],
            'text' => trim($array['text']),
            'message_id' => $message->getMessageId(),
        ];
        if ($delete == TRUE)
            $data['delete_message'] = 1;
        if ($dataBase == TRUE)
            Telegram_message::create($data);
    } catch (Throwable $e) {
        logger($e);
    }
}

function getStoreAuthLink($phone)
{
    return 'https://milavitsa.store/loginStoreMaster?user_phone=' . substr($phone, -10) . '&token=' . config('app.store.token');
}

function money($m)
{
    return number_format($m, 2, '.', ' ') . 'р.';
}

function ball($m)
{
    return number_format($m, 0, '.', ' ') . ' б.';
}

function numb($m, $decimals = 2)
{
    return number_format($m, $decimals, '.', ' ');
}

function num2str($num)
{
    $nul = 'ноль';
    $ten = array(
        array('', 'один', 'два', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'),
        array('', 'одна', 'две', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'),
    );
    $a20 = array('десять', 'одиннадцать', 'двенадцать', 'тринадцать', 'четырнадцать', 'пятнадцать', 'шестнадцать', 'семнадцать', 'восемнадцать', 'девятнадцать');
    $tens = array(2 => 'двадцать', 'тридцать', 'сорок', 'пятьдесят', 'шестьдесят', 'семьдесят', 'восемьдесят', 'девяносто');
    $hundred = array('', 'сто', 'двести', 'триста', 'четыреста', 'пятьсот', 'шестьсот', 'семьсот', 'восемьсот', 'девятьсот');
    $unit = array(
        array('копейка', 'копейки', 'копеек', 1),
        array('рубль', 'рубля', 'рублей', 0),
        array('тысяча', 'тысячи', 'тысяч', 1),
        array('миллион', 'миллиона', 'миллионов', 0),
        array('миллиард', 'миллиарда', 'миллиардов', 0),
    );
    list($rub, $kop) = explode('.', sprintf("%015.2f", floatval($num)));
    $out = array();
    if (intval($rub) > 0) {
        foreach (str_split($rub, 3) as $uk => $v) {
            if (!intval($v)) continue;
            $uk = sizeof($unit) - $uk - 1;
            $gender = $unit[$uk][3];
            list($i1, $i2, $i3) = array_map('intval', str_split($v, 1));
            // mega-logic
            $out[] = $hundred[$i1]; // 1xx-9xx
            if ($i2 > 1) $out[] = $tens[$i2] . ' ' . $ten[$gender][$i3]; // 20-99
            else $out[] = $i2 > 0 ? $a20[$i3] : $ten[$gender][$i3]; // 10-19 | 1-9
            // units without rub & kop
            if ($uk > 1) $out[] = morph($v, $unit[$uk][0], $unit[$uk][1], $unit[$uk][2]);
        }
    } else {
        $out[] = $nul;
    }
    $out[] = morph(intval($rub), $unit[1][0], $unit[1][1], $unit[1][2]); // rub
    $out[] = $kop . ' ' . morph($kop, $unit[0][0], $unit[0][1], $unit[0][2]); // kop
    return trim(preg_replace('/ {2,}/', ' ', join(' ', $out)));
}

/**
 * Склоняем словоформу
 * @author runcore
 */
function morph($n, $f1, $f2, $f5)
{
    $n = abs(intval($n)) % 100;
    if ($n > 10 && $n < 20) return $f5;
    $n = $n % 10;
    if ($n > 1 && $n < 5) return $f2;
    if ($n == 1) return $f1;
    return $f5;
}

function file_store($request, string $path)
{
    $files = [];
    if ($request->hasFile('files'))
        foreach ($request->file('files') as $key => $file) {
            $files[] = $file->store($path);
        }
    return $files;
}

function autoLoadLink($link, $user_id = 0)
{
    $autoLoadLink = Redirect::create([
        'link' => $link,
        'active' => 1,
        'user_id' => $user_id,
    ]);

    return route('autoLoad', md5($autoLoadLink->id));
}

function dmDay($date = false)
{
    if (!$date)
        $date = now();
    $date = Carbon::parse($date);
    $dm = TimetableDm::query()->where('date', 'LIKE', '%' . $date->format('Y-m-d') . '%')->first();

    if ($dm)
        return $dm->user;
    else
        return (object)[
            "id" => 0,
            "name" => "не установлен"
        ];

}

function formatContent($content)
{
    $url_filter_protocol = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
    $url_filter_www = "/(www)\.[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";

    if (preg_match($url_filter_protocol, $content, $url)) {
        return preg_replace($url_filter_protocol, "<a href='$url[0]' target='_blank'>$url[0]</a> ", $content);
    } elseif (preg_match($url_filter_www, $content, $url)) {
        return preg_replace($url_filter_www, "<a href='https://$url[0]' target='_blank'>$url[0]</a> ", $content);
    } else {
        return $content;
    }
}

function sendObserver(string $model, string $event, array $data)
{
    print_r($data);
    $url = 'https://milavitsa.store/api/exchange/' . $model . '/' . $event;
    $response = Http::post($url, $data)->body();
    return $response;
}

function getJson(string $file)
{
    return json_decode($file, true);
}

function clearPhone($phoneInput)
{
    $phone = '';
    foreach (str_split($phoneInput) as $p_number)
        if (is_numeric($p_number))
            $phone .= $p_number;

    if (strlen($phone) == 11)
        $phone = substr($phone, 1, 10);
    return $phone;
}

function phoneToBase($phoneInput)
{
    $phone = '';
//    $phone = '+';
    foreach (str_split($phoneInput) as $p_number)
        if (is_numeric($p_number))
            $phone .= $p_number;

    if (strlen($phone) == 10)
        $phone = '+7' . $phone;
    elseif (strlen($phone) == 11)
        $phone = '+7' . substr($phone, 1, 10);
    return $phone;
}

function words($string, $word_limit = 20)
{
    $words = explode(" ", $string);

    if (sizeof($words) < 11)
        return $string;

    return implode(" ", array_splice($words, 0, $word_limit)) . '...';
}

Carbon::macro('isValid', function ($string) {
    if (is_object($string)) {
        if (get_class($string) == "Illuminate\Support\Carbon") {
            return true;
        }
    }

    $d1 = \DateTime::createFromFormat('Y-m-d', $string);
    $d2 = \DateTime::createFromFormat('Y-m-d H:i:s', $string);
    return
        ($d1 && $d1->format('Y-m-d') === $string)
        ||
        ($d2 && $d2->format('Y-m-d H:i:s') === $string);
});

function say($text, $stop = false)
{
    logger($text);
    if (!is_string($text)) {
        $text = json_encode($text, JSON_UNESCAPED_UNICODE);
    }
//    echo $text . '<br>' . PHP_EOL;
    if ($stop)
        exit;
}

function getFloat($string)
{
    $result = '';
    foreach (str_split($string) as $item) {
        if (in_array($item, ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', ',', '.', '-'], true)) {
            $result .= $item;
        }
    }
    $result = str_replace(',', '.', $result);
    return $result;
}
