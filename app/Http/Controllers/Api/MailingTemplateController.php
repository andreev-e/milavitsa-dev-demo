<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

class MailingTemplateController extends Controller
{
    public function index() {
        $data = [];
        $files = File::allFiles(resource_path('views/emails/mailing'));
        foreach ($files as $file)
        {
            $data[] =  str_replace('.blade.php', '', basename($file));
        }

        return response(['data' => $data], 200);
    }
}
