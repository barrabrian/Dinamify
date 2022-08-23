<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Form;
use App\Models\Deliverable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

use App\Http\Resources\QuestionsResource;


class QuestionsController extends Controller
{
    public function index()
    {
        $form_id = request('form_id');
        $deliverable_id = request('deliverable_id');

        if ($form_id !== 'null' && $form_id !== null) {
            $fid = Form::where('id', $form_id)->first()->fid;
            $questions = Question::where('form_id', $fid)->get()
                ->transform(fn ($question) => [
                    'id' => $question->id,
                    'title' => $question->title,
                    'type' => $question->type,
                    'qid' => $question->qid,
                    'alternatives' => $question->alternatives()->get()
                        ->transform(fn ($alternatives) => [
                            'id' => $alternatives->id,
                            'label' => $alternatives->label,
                            'aid' => $alternatives->aid,
                        ]),
                ]);
            // $questions = Question::first();
        } else if ($deliverable_id !== 'null' && $deliverable_id !== null) { 
            $form = Deliverable::where('id', $deliverable_id)->first()->form()->first();
            // dd($form);

            if ($form !== null) {
                $questions = $form->questions()->get();
                // dd($questons);
            } else {
                $questions = [];
            }

        } else {
            $questions = [];
        }
        return $questions;
    }

    public function view(Form $selected_form , Question $question)
    {
        // dd($question);

        return Inertia::render('Questions/View', [
            'alternatives' => $question->alternatives()->get() ? $question->alternatives()->get() : null,
            'answers' => $question->answers()->get() ? $question->answers()
                ->orderBy('id')
                ->paginate(6)
                ->withQueryString()
                ->through(fn ($ans) => [
                    'id' => $ans->id,
                    'value' => $ans->value,
                    'type' => $ans->type,
                    'deleted_at' => $ans->deleted_at,
                    ]) : null,
            'filters' => Request::all('search', 'trashed'),
            'forms' => Auth::user()->account->forms()
                ->orderBy('id')
                ->filter(Request::only('search', 'trashed'))
                ->get(),
            'question' => $question,
        ]);
    }

    // public function pickBy()
    // {
    //     return Inertia::render('Contacts/Index', [
    //         'contacts' => Auth::user()->account->forms()
    //             ->filter(Request::only('search', 'trashed'))
    //             ->paginate(10)
    //             ->withQueryString()
    //             ->through(fn ($contact) => [
    //                 'id' => $contact->id,
    //                 'name' => $contact->fid,
    //                 'phone' => $contact->is_public,
    //                 'city' => $contact->type,
    //                 'deleted_at' => $contact->deleted_at,
    //                 'organization' => $contact->account ? $contact->account->only('name') : null,
    //             ]),
    //     ]);
    // }

}
