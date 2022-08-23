<?php

namespace App\Http\Controllers;

use App\Models\ActiveCampaignPlugin;
use App\Models\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Http;

use Inertia\Inertia;

class ActiveCampaignPluginsController extends Controller
{
    public function callApi()
    {
        $api_url = request('api_url');
        $api_key = request('api_key');

        $responseFields = Http::withHeaders([
            'Api-Token' => $api_key,
            'Content-Type' => 'application/json',
        ])
        ->get($api_url);

        return $responseFields;
    }

    public function create()
    {
        return Inertia::render('ActiveCampaign/Create');
    }

    public function store()
    {
        Auth::user()->account->active_campaign_plugins()->create(
            Request::validate([
                'name' => ['required', 'max:50'],
                'api_url' => ['required', 'max:350'],
                'api_key' => ['required', 'max:350'],
                'deliverable_link_field_id' => ['required'],
                'deliverable_tag_id' => ['required'],
            ])
        );

        return Redirect::route('plugins')->with('success', 'Integração adicionada!');
    }

    public function edit(ActiveCampaignPlugin $plugin)
    {
        return Inertia::render('ActiveCampaign/Edit', [
            'active_campaign_plugin' => [
                'id' => $plugin->id,
                'name' => $plugin->name,
                'api_url' => $plugin->api_url,
                'api_key' => $plugin->api_key,
                'deliverable_link_field_id' => $plugin->deliverable_link_field_id,
                'deliverable_tag_id' => $plugin->deliverable_tag_id,
                'deleted_at' => $plugin->deleted_at,
            ]
        ]);
    }

    public function update(ActiveCampaignPlugin $plugin)
    {
        $plugin->update(
            Request::validate([
                'name' => ['required', 'max:50'],
                'api_url' => ['required', 'max:350'],
                'api_key' => ['required', 'max:350'],
                'deliverable_link_field_id' => ['required'],
                'deliverable_tag_id' => ['required'],
            ])
        );

        return Redirect::back()->with('success', 'Integração atualizada.');
    }

    public function destroy(ActiveCampaignPlugin $plugin)
    {
        $plugin->delete();

        return Redirect::back()->with('success', 'Integração desativada.');
    }

    public function restore(ActiveCampaignPlugin $plugin)
    {
        $plugin->restore();

        return Redirect::back()->with('success', 'Integração ativada.');
    }

}
