<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Response;
use App\Models\Answer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;


class ResponsesController extends Controller
{
    public function index()
    {
        $question_id = request('question_id');

        if ($question_id !== 'null' && $question_id !== null) { 
            $ans = Question::where('id', $question_id)->first()->answers()->get()
                ->transform(fn ($answer) => [
                    'id' => $answer->id,
                    'value' => $answer->value,
                    'response_id' => $answer->answer_id,
                    'type' => $answer->type,
                ]);


            if ($ans !== null) {
                $responses = $ans;
            } else {
                $responses = [];
            }

        } else {
            $responses = [];
        }

        return $responses;
    }

}
