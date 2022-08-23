<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Alternative;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;



class AlternativesController extends Controller
{
    public function index()
    {
        $qid = request('qid');

        if ($qid !== 'null' && $qid !== null) {
            $alternatives = Alternative::where('question_id', $qid)->get();
        } else {
            $alternatives = [];
        }
        return $alternatives;
    }

}
