<?php

namespace App\Http\Controllers;

use App\Models\TypeformPlugin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class PluginsController extends Controller
{
    public function index()
    {
        return Inertia::render('Plugins/Index', [
            'filters' => Request::all('search', 'trashed'),
            'typeform_plugins' => Auth::user()->account->typeform_plugins()
                ->orderBy('name')
                ->filter(Request::only('search', 'trashed'))
                ->paginate(5)
                ->withQueryString()
                ->through(fn ($typeform_plugin) => [
                    'id' => $typeform_plugin->id,
                    'name' => $typeform_plugin->name,
                    'deleted_at' => $typeform_plugin->deleted_at,
                ]),
            'active_campaign_plugins' => Auth::user()->account->active_campaign_plugins()
                ->orderBy('name')
                ->filter(Request::only('search', 'trashed'))
                ->paginate(5)
                ->withQueryString()
                ->through(fn ($active_campaign_plugin) => [
                    'id' => $active_campaign_plugin->id,
                    'name' => $active_campaign_plugin->name,
                    'deleted_at' => $active_campaign_plugin->deleted_at,
                ]),
        ]);
    }
}
