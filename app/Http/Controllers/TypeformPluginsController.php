<?php

namespace App\Http\Controllers;

use App\Models\TypeformPlugin;
use App\Models\Form;
use App\Models\Question;
use App\Models\Alternative;
use App\Models\Answer;
use App\Models\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Http;

use Inertia\Inertia;

class TypeformPluginsController extends Controller
{

    public function sync(TypeformPlugin $plugin)
    {
        $responseForms = Http::withHeaders([
            'Authorization' => 'Bearer '.$plugin->token,
            'Content-Type' => 'application/json',
        ])
        ->get('https://api.typeform.com/forms?page=1&page_size=25');

        // dd($responseForms->json());
        // dd($responseForms->json()["code"]);


        if (array_key_exists("code",$responseForms->json())) {
            return Redirect::back()->with('error', 'Token inválido!')->withErrors(['token' => 'Token inválido']);
            // dd($responseForms->json()["code"]);
        } else {

            foreach ($responseForms->json()['items'] as &$form_item) {
                $form = new Form();
                $form->fid = $form_item['id'];
                $form->title = $form_item['title'];
                $form->type = $form_item['type'];
                $form->link = $form_item['_links']['display'];
                $form->is_public = $form_item['settings']['is_public'];
                
                // dd($form);

                $dbform = Auth::user()->account->forms()->firstOrCreate([
                    'title' => $form->title,
                    'fid' => $form->fid,
                    'type' => $form->type,
                    'link' => $form->link,
                    'is_public' => $form->is_public,
                ]);

                
                // ------------ retrieving questions --------------
                
                $responseQuestions = Http::withHeaders([
                    'Authorization' => 'Bearer '.$plugin->token,
                    'Content-Type' => 'application/json',
                ])
                ->get('https://api.typeform.com/forms/'.$form_item['id']);

                foreach ($responseQuestions->json()['fields'] as &$form_question) {
                    $question = new Question();
                    $question->form_id = $form_item['id'];
                    $question->qid = $form_question['id'];
                    $question->title = $form_question['title'];
                    $question->ref = $form_question['ref'];
                    $question->type = $form_question['type'];
                    if (array_key_exists("properties",$form_question)) {
                        $question->description = array_key_exists("description",$form_question['properties']) ? $form_question['properties']['description'] : '';
                    } else {
                        $question->description = '';
                    }
                    if (array_key_exists("validations",$form_question)) {
                        $question->is_required = array_key_exists("required",$form_question['validations']) ? $form_question['validations']['required'] : false;
                    } else {
                        $question->is_required = false;
                    }

                    // dd($question);
                    $dbquestion = Auth::user()->account->questions()->firstOrCreate([
                        'form_id' => $question->form_id,
                        'title' => $question->title,
                        'description' => $question->description,
                        'qid' => $question->qid,
                        'ref' => $question->ref,
                        'type' => $question->type,
                        'is_required' => $question->is_required,
                    ]);

                    if (array_key_exists("properties",$form_question)) {
                        if (array_key_exists("choices",$form_question['properties'])) {
                            foreach ($form_question['properties']['choices'] as &$question_choice) {
                                $alternative = new Alternative();
                                $alternative->question_id = $question->qid;
                                $alternative->aid = $question_choice['id'];
                                $alternative->label = $question_choice['label'];
                                $alternative->ref = $question_choice['ref'];

                                // dd($alternative);

                                $dbalternative = Auth::user()->account->alternatives()->firstOrCreate([
                                    'question_id' => $alternative->question_id,
                                    'label' => $alternative->label,
                                    'aid' => $alternative->aid,
                                    'ref' => $alternative->ref,
                                ]);
                            }
                            unset($question_choice);
                        }
                    }

                    // handling group type 

                    if (array_key_exists("properties",$form_question)) {
                        if (array_key_exists("fields",$form_question['properties'])) {
                            foreach ($form_question['properties']['fields'] as &$group_field) {

                                $question->qid = $group_field['id'];
                                $question->title = $group_field['title'];
                                $question->ref = $group_field['ref'];
                                $question->type = $group_field['type'];

                                if (array_key_exists("validations",$group_field)) {
                                    $question->is_required = array_key_exists("required",$group_field['validations']) ? $group_field['validations']['required'] : false;
                                } else {
                                    $question->is_required = false;
                                }

                                $dbquestion = Auth::user()->account->questions()->firstOrCreate([
                                    'form_id' => $question->form_id,
                                    'title' => $question->title,
                                    'description' => $question->description,
                                    'qid' => $question->qid,
                                    'ref' => $question->ref,
                                    'type' => $question->type,
                                    'is_required' => $question->is_required,
                                ]);

                                if (array_key_exists("properties",$group_field)) {
                                    if (array_key_exists("choices",$group_field['properties'])) {
                                        foreach ($group_field['properties']['choices'] as &$group_field_choice) {
                                            $alternative = new Alternative();
                                            $alternative->question_id = $question->qid;
                                            $alternative->aid = $group_field_choice['id'];
                                            $alternative->label = $group_field_choice['label'];
                                            $alternative->ref = $group_field_choice['ref'];
            
                                            // dd($alternative);
            
                                            $dbalternative = Auth::user()->account->alternatives()->firstOrCreate([
                                                'question_id' => $alternative->question_id,
                                                'label' => $alternative->label,
                                                'aid' => $alternative->aid,
                                                'ref' => $alternative->ref,
                                            ]);
                                        }
                                        unset($group_field_choice);
                                    }
                                }
                            }
                            unset($group_field);
                        }
                    }


                }
                unset($form_question);


                // ------------ retrieving answers --------------

                $ra_page = 1;
                $ra_totalpages = 1;

                for ($i=0; $i < $ra_totalpages; $i++) { 
                    $responseAnswers = Http::withHeaders([
                        'Authorization' => 'Bearer '.$plugin->token,
                        'Content-Type' => 'application/json',
                    ])
                    ->get('https://api.typeform.com/forms/'.$form_item['id'].'/responses?page='.$ra_page.'&page_size=50');

                    $ra_totalpages = $responseAnswers->json()['page_count'];
                    $ra_page++;
    
                    foreach ($responseAnswers->json()['items'] as &$response_item) {

                        $response = new Response();
                        $response->form_id = $form->fid;
                        $response->token = $response_item['token'];
                        $response->landing_id = $response_item['landing_id'];
                        $response->rid = $response_item['response_id'];
                        $response->hidden_fields = implode("&", $response_item['hidden']);                        ;
                        $response->submitted_at = $response_item['submitted_at'];
                        $response->score = $response_item['calculated']['score'];

                        // dd($response);

                        $dbresponse = Auth::user()->account->responses()->firstOrCreate([
                            'form_id' => $response->form_id,
                            'token' => $response->token,
                            'landing_id' => $response->landing_id,
                            'rid' => $response->rid,
                            'hidden_fields' => $response->hidden_fields,
                            'submitted_at' => $response->submitted_at,
                            'score' => $response->score,
                        ]);

                        foreach ($response_item['answers'] as &$question_ans) {
                            $answer = new Answer();
                            $answer->question_id = $question_ans['field']['id'];
                            $answer->question_ref = $question_ans['field']['ref'];
                            $answer->type = $question_ans['type'];
                            $answer->answer_id = $response_item['response_id'];

                            if (array_key_exists("text",$question_ans)) {
                                $answer->value = $question_ans['text'];
                            } else if (array_key_exists("email",$question_ans)) {
                                $answer->value = $question_ans['email'];
                            } else if (array_key_exists("choice",$question_ans)) {
                                $answer->aid = $question_ans['choice']['id'];
                                $answer->aref = $question_ans['choice']['ref'];
                                $answer->value = $question_ans['choice']['label'];
                            } 
                            
                            if (array_key_exists("choices",$question_ans)) {
                                $choice_index = 0;
                                foreach ($question_ans['choices']['ids'] as &$ans_choice) {
                                    $answer->aid = $ans_choice;
                                    $answer->aref = $question_ans['choices']['refs'][$choice_index];
                                    $answer->value = $question_ans['choices']['labels'][$choice_index];

                                    $dbanswer = Auth::user()->account->answers()->firstOrCreate([
                                        'question_id' => $answer->question_id,
                                        'question_ref' => $answer->question_ref,
                                        'answer_id' => $answer->answer_id,
                                        'type' => $answer->type,
                                        'value' => $answer->value,
                                        'aid' => $answer->aid ? $answer->aid : null,
                                        'aref' => $answer->aref ? $answer->aref : null,
                                    ]);
                                    $choice_index++;
                                }
                                unset($ans_choice);
                            } else {

                                // dd($answer);

                                $dbanswer = Auth::user()->account->answers()->firstOrCreate([
                                    'question_id' => $answer->question_id,
                                    'question_ref' => $answer->question_ref,
                                    'answer_id' => $answer->answer_id,
                                    'type' => $answer->type,
                                    'value' => $answer->value,
                                    'aid' => $answer->aid ? $answer->aid : null,
                                    'aref' => $answer->aref ? $answer->aref : null,
                                ]);
                            }
                        }
                        unset($question_ans);
                    }
                    unset($response_item);

                }
            }
            unset($form_item);

        }



        return Redirect::back()->with('success', 'Integração sincronizada!');
    }

    public function create()
    {
        return Inertia::render('Typeform/Create');
    }

    public function store()
    {
        
        Request::validate([
            'name' => ['required', 'max:50'],
            'token' => ['required', 'max:350'],
        ]);

        $responseForms = Http::withHeaders([
            'Authorization' => 'Bearer '. Request::collect('token')[0],
            'Content-Type' => 'application/json',
        ])
        ->get('https://api.typeform.com/forms?page=1&page_size=25');

        if(array_key_exists('code',$responseForms->json())){
            if ($responseForms->json()['code'] == 'AUTHENTICATION_FAILED') {
                return Redirect::back()->with('error', 'Token inválido.')->withErrors(['token' => 'Token inválido']);
            }
        }

        Auth::user()->account->typeform_plugins()->create(Request::all());

        return Redirect::route('plugins')->with('success', 'Integração adicionada!');
    }

    public function edit(TypeformPlugin $plugin)
    {
        return Inertia::render('Typeform/Edit', [
            'typeform_plugin' => [
                'id' => $plugin->id,
                'name' => $plugin->name,
                'token' => $plugin->token,
                'deleted_at' => $plugin->deleted_at,
            ]
        ]);
    }

    public function update(TypeformPlugin $plugin)
    {
        
        Request::validate([
            'name' => ['required', 'max:50'],
            'token' => ['required', 'max:350'],
        ]);

        $responseForms = Http::withHeaders([
            'Authorization' => 'Bearer '. Request::collect('token')[0],
            'Content-Type' => 'application/json',
        ])
        ->get('https://api.typeform.com/forms?page=1&page_size=25');

        if(array_key_exists('code',$responseForms->json())){
            if ($responseForms->json()['code'] == 'AUTHENTICATION_FAILED') {
                return Redirect::back()->with('error', 'Token inválido.')->withErrors(['token' => 'Token inválido']);
            }
        }

        $plugin->update(Request::all());

        return Redirect::back()->with('success', 'Integração atualizada.');
    }

    public function destroy(TypeformPlugin $plugin)
    {
        $plugin->delete();

        return Redirect::back()->with('success', 'Integração desativada.');
    }

    public function restore(TypeformPlugin $plugin)
    {
        $plugin->restore();

        return Redirect::back()->with('success', 'Integração ativada.');
    }

}
