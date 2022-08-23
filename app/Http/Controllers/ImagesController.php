<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use League\Glide\Responses\LaravelResponseFactory;
use League\Glide\ServerFactory;

use Inertia\Inertia;
use App\Models\Image;
use Illuminate\Support\Facades\URL;


class ImagesController extends Controller
{
    public function show(Filesystem $filesystem, Request $request, $path)
    {
        $server = ServerFactory::create([
            'response' => new LaravelResponseFactory($request),
            'source' => $filesystem->getDriver(),
            'cache' => $filesystem->getDriver(),
            'cache_path_prefix' => '.glide-cache',
        ]);

        return $server->getImageResponse($path, $request->all());
    }

    public function index()
    {
        return Inertia::render('Images/Index', [
            'images' => Auth::user()->account->images()
                ->orderBy('id')
                ->paginate(12)
                ->withQueryString()
                ->through(fn ($image) => [
                    'id' => $image->id,
                    'image_path' => URL::route('image', ['path' => $image->image_path]),
                    'deleted_at' => $image->deleted_at,
                ]),
        ]);
    }

    public function create()
    {
        return Inertia::render('Images/Create');
    }

    public function store()
    {
        Request::validate([
            'name' => ['nullable', 'max:45'],
            'image' => ['required', 'image'],
        ]);

        Auth::user()->account->images()->create([
            'name' => Request::get('name'),
            'image_path' => Request::file('image') ? Request::file('image')->store('/public/images') : null,
        ]);

        return Redirect::route('Images')->with('success', 'Imagem Adicionada.');
    }
}
