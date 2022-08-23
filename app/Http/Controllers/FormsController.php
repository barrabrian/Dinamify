<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\Question;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Inertia\Inertia;

class FormsController extends Controller
{
    public function index()
    {
        return Inertia::render('Forms/Index', [
            'filters' => Request::all('search', 'trashed'),
            'forms' => Auth::user()->account->forms()
                ->orderBy('id')
                ->filter(Request::only('search', 'trashed'))
                ->get(),
        ]);
    }

    public function edit(Form $plugin)
    {
        dd($plugin);
        return Inertia::render('Typeform/Edit', [
            'typeform_plugin' => [
                'id' => $plugin->id,
                'name' => $plugin->title,
                'token' => $plugin->fid,
                'deleted_at' => $plugin->deleted_at,
            ]
        ]);
    }

    public function view(Form $selected_form)
    {
        // dd($selected_form);
        return Inertia::render('Forms/View', [
            'questions' => $selected_form->questions()->get() ? $selected_form->questions()
                ->orderBy('id')
                ->filter(Request::only('search', 'trashed'))
                ->paginate(6)
                ->withQueryString()
                ->through(fn ($question) => [
                    'id' => $question->id,
                    'title' => $question->title,
                    'description' => $question->description,
                    'type' => $question->type,
                    'is_required' => $question->is_required,
                    'qid' => $question->qid,
                    'form_id' => $question->form_id,
                    'deleted_at' => $question->deleted_at,
                ]) : null,
            'filters' => Request::all('search', 'trashed'),
            'forms' => Auth::user()->account->forms()
                ->orderBy('id')
                ->filter(Request::only('search', 'trashed'))
                ->get(),
        ]);
    }
}
