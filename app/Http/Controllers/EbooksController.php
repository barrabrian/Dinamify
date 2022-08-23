<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\Request;
use League\Glide\Responses\LaravelResponseFactory;
use League\Glide\ServerFactory;

class EbooksController extends Controller
{
    public function show($path)
    {
        return response()->file(storage_path('app/public/pdfs/'.$path));
    }
}
