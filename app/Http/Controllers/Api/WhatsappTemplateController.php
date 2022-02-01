<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

class WhatsappTemplateController extends Controller
{
    public function index() {
        $data = config('whatsapptemplates.templates');
        return response(['data' => $data], 200);
    }
}
