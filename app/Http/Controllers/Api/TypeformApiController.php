<?php

namespace App\Http\Controllers\Api;

use App\Models\TypeformPlugin;
use App\Models\Form;
use App\Models\Question;
use App\Models\Alternative;
use App\Models\Answer;
use App\Models\Response;
use App\Models\Ebook;
use App\Models\TypeformRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Hash;

use App\Http\Controllers\AutomationsController;
use App\Http\Controllers\Controller;

use Barryvdh\DomPDF\Facade\Pdf;


class TypeformApiController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $headers = $request->headers->all();
        if (array_key_exists('typeform-signature', $headers)) {

            $event_type = $request->input('event_type');

            if ($event_type == 'form_response') {
                $response = new Response();
                $response->form_id = $request->input('form_response.form_id');
                $response->token = $request->input('form_response.token');
                $response->landing_id = $request->input('form_response.token');
                $response->rid = $request->input('form_response.token');
                $response->hidden_fields = implode("&", $request->input('form_response.hidden'));                        ;
                $response->submitted_at = $request->input('form_response.submitted_at');
                $response->score = $request->input('form_response.calculated.score');

                // return $response;

                $form = Form::where('fid', $response->form_id)->first();

                $dbresponse = Response::firstOrCreate([
                    'account_id' => $form->account_id,
                    'form_id' => $response->form_id,
                    'token' => $response->token,
                    'landing_id' => $response->landing_id,
                    'rid' => $response->rid,
                    'hidden_fields' => $response->hidden_fields,
                    'submitted_at' => $response->submitted_at,
                    'score' => $response->score,
                ]);

                foreach ($request->input('form_response.answers') as &$question_ans) {
                    $answer = new Answer();
                    $answer->question_id = $question_ans['field']['id'];
                    $answer->question_ref = $question_ans['field']['ref'];
                    $answer->type = $question_ans['type'];
                    $answer->answer_id = $response->token;
                    $answer->response_id = $response->token;

                    if (array_key_exists("text",$question_ans)) {
                        $answer->value = $question_ans['text'];
                    } else if (array_key_exists("email",$question_ans)) {
                        $answer->value = $question_ans['email'];
                    } else if (array_key_exists("choice",$question_ans)) {
                        $answer->aid = $question_ans['choice']['id'];
                        $answer->aref = $question_ans['choice']['ref'];
                        $answer->value = $question_ans['choice']['label'];
                    } 

                    // return $answer;

                    
                    if (array_key_exists("choices",$question_ans)) {
                        $choice_index = 0;
                        foreach ($question_ans['choices']['ids'] as &$ans_choice) {
                            $answer->aid = $ans_choice;
                            $answer->value = $question_ans['choices']['labels'][$choice_index];
                            if (array_key_exists("refs",$question_ans['choices'])) {
                                $answer->aref = $question_ans['choices']['refs'][$choice_index];
                            }

                            $dbanswer = Answer::firstOrCreate([
                                'account_id' => $form->account_id,
                                'question_id' => $answer->question_id,
                                'question_ref' => $answer->question_ref,
                                'answer_id' => $answer->answer_id,
                                'response_id' => $answer->response_id,
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

                        $dbanswer = Answer::firstOrCreate([
                            'account_id' => $form->account_id,
                            'question_id' => $answer->question_id,
                            'question_ref' => $answer->question_ref,
                            'answer_id' => $answer->answer_id,
                            'response_id' => $answer->response_id,
                            'type' => $answer->type,
                            'value' => $answer->value,
                            'aid' => $answer->aid ? $answer->aid : null,
                            'aref' => $answer->aref ? $answer->aref : null,
                        ]);
                    }
                }
                unset($question_ans);


                $deliverables = $form->deliverables()->get();
                // return $deliverables;

                foreach ($deliverables as &$deliverable) {
                    $automations = $deliverable->automations()->get();
                    // return $automations;
                    foreach ($automations as &$flow) {
                        // return Question::find($flow->email_question_id);

                        $ans_id = $response->token;
                        $email = Answer::where('question_id', $flow->email_question->qid)->where('answer_id', $response->token)->first() ? Answer::where('question_id', $flow->email_question->qid)->where('answer_id', $response->token)->first()->value : null;
                        // return $email;

                        if ($email) {
                            $html = "<html><head><style>body{font-family: sans-serif;} .page-break{page-break-after: always;} @page {margin: 120px 60px;} header {position: fixed;top: -60px;left: 0px;right: 0px;height: 50px;
                                line-height: 35px;} footer {position: fixed; bottom: -80px; left: 0px; right: 0px;height: 30px; padding: 0 100px;
                                text-align: center;font-size: 12px;font-family: serif;}</style></head><body>";
                
                            $html_aux = '';
                
                            $html_aux = explode('<div class="page-break"></div>', $deliverable->html);
                            for ($i=0; $i < sizeof($html_aux); $i++) { 
                                if ($i == 0) {
                                    $html_whf = $html_aux[$i] . '<div class="page-break"></div>' . $deliverable->header . $deliverable->footer . '<main>';
                                } else {
                                    $html_whf = $html_whf . $html_aux[$i] . '<div class="page-break"></div>';
                                }
                            }
                
                            $html_aux = '';
                            $html_wifs = '';
                            $html_aux = explode('{{end-if}}', $html_whf);
                            
                            for ($i=0; $i < sizeof($html_aux); $i++) { 
                                if ($i !== sizeof($html_aux) - 1) {
                                    $split_aux = explode('{{if(question[', $html_aux[$i]);
                                    $html_wifs = $html_wifs . $split_aux[0];
                
                                    $cond = explode(']=="', explode('")}}', $split_aux[1])[0]);
                                    $cond_content =  explode('")}}', $split_aux[1])[1];
                
                                    // dd($cond);
                
                                    if ($ans_id == '') {
                                        $ans = Answer::where('question_id', $cond[0])->first();
                                        $ans_id = $ans->answer_id;
                                    } else { 
                                        $ans = Answer::where('question_id', $cond[0])->where('answer_id', $ans_id)->first();
                                    }
                
                                    // dd($ans);
                                    // dd($cond);
                
                                    if ($ans->value == $cond[1]) {
                                        $html_wifs = $html_wifs . $cond_content;
                                    } 
                
                
                                } else {
                                    $html_wifs = $html_wifs.$html_aux[$i];
                                }
                            }
                
                            // dd($html_wifs);
                
                
                            $html_aux = '';
                
                            $split1 = explode('}}', $html_wifs);
                            // dd($split1);
                
                            foreach ($split1 as $split_part) {
                                $subs = explode('{{', $split_part);
                                $html_aux = $html_aux.$subs[0];
                                // dd($subs);
                                if (array_key_exists(1, $subs)){
                                    $dinamic_aux = explode('[', $subs[1]);
                                    $dinamic = explode(']', $dinamic_aux[1]);
                                    // dd($dinamic_aux);
                                    // dd($dinamic);
                
                                    if ($dinamic_aux[0] == 'question') {
                                        // if (!str_contains($subs[1], 'if(')) {}
                
                                        if ($ans_id == '') {
                                            $ans = Answer::where('question_id', $dinamic[0])->first();
                                            $ans_id = $ans->answer_id;
                                        } else { 
                                            $ans = Answer::where('question_id', $dinamic[0])->where('answer_id', $ans_id)->first();
                                        }
                                        
                                        // dd($ans);
                                        
                                        $html_aux = $html_aux.$ans->value;
                
                
                                    } elseif ($dinamic_aux[0] == 'vbar-chart') {
                
                                        // dd($dinamic_aux);
                                        
                                        $score_conditions = explode(',', explode(')]',explode("'(", $dinamic_aux[1])[1])[0]);
                                        // dd($score_conditions);
                
                                        $chart_name = explode("'(", explode(",'", $dinamic_aux[1])[1])[0];
                                        // dd($chart_name);
                
                                        $chart_color = explode(",'", $dinamic_aux[1])[0];
                                        // dd($chart_color);
                
                                        $score = 0;
                
                                        foreach ($score_conditions as $condition) {
                                            if ($ans_id == '') {
                                                $ans = Answer::where('aid', $condition)->first();
                                                $ans_id = $ans->answer_id;
                                            } else { 
                                                $ans = Answer::where('aid', $condition)->where('answer_id', $ans_id)->first();
                                            }
                                            if ( $ans !== null ) {
                                                $score++;
                                            }
                                        }
                
                                        $normalized_score = $score * (10 / sizeof($score_conditions));
                                        $bar_height = $normalized_score * 20;
                                        // dd($ans);
                                        // dd($score);
                                        // dd($bar_height);
                
                                        // dd(sizeof($score_conditions));
                
                                        $svg = '<svg viewBox="0 0 60 180" style="font-family:sans-serif;">
                                            <g transform="translate(40,-40)">
                                            <g class="x axis" transform="translate(0,203)">
                                                    <g class="tick" transform="translate(40,0)" style="opacity: 1;">
                                                    <text dy=".71em" y="9" x="0" style="text-anchor: middle;">'.$chart_name.'</text>
                                                </g>
                                                <path class="domain" d="M0,6V0H900V6"></path>
                                            </g>
                                                <g class="y axis">
                                                    <g class="tick" transform="translate(0,200)" style="opacity: 1;"><line x2="-6" y2="0" fill="none" stroke="#000" shape-rendering="crispEdges"></line>
                                                    <text dy=".32em" x="-9" y="0" style="text-anchor: end;">0</text>
                                                </g>
                                                    <g class="tick" transform="translate(0,160)" style="opacity: 1;"><line x2="-6" y2="0" fill="none" stroke="#000" shape-rendering="crispEdges"></line>
                                                    <text dy=".32em" x="-9" y="0" style="text-anchor: end;">2</text>
                                                </g>
                                                    <g class="tick" transform="translate(0,120)" style="opacity: 1;"><line x2="-6" y2="0" fill="none" stroke="#000" shape-rendering="crispEdges"></line>
                                                    <text dy=".32em" x="-9" y="0" style="text-anchor: end;">4</text>
                                                </g>
                                                    <g class="tick" transform="translate(0,80)" style="opacity: 1;"><line x2="-6" y2="0" fill="none" stroke="#000" shape-rendering="crispEdges"></line>
                                                    <text dy=".32em" x="-9" y="0" style="text-anchor: end;">6</text>
                                                </g>
                                                    <g class="tick" transform="translate(0,40)" style="opacity: 1;"><line x2="-6" y2="0" fill="none" stroke="#000" shape-rendering="crispEdges"></line>
                                                    <text dy=".32em" x="-9" y="0" style="text-anchor: end;">8</text>
                                                </g>
                                                    <g class="tick" transform="translate(0,0)" style="opacity: 1;"><line x2="-6" y2="0" fill="none" stroke="#000" shape-rendering="crispEdges"></line>
                                                    <text dy=".32em" x="-9" y="0" style="text-anchor: end;">10</text>
                                                </g>
                                        
                                                <path class="domain" d="M-6,0H0V200H-6" fill="none" stroke="#000" shape-rendering="crispEdges"></path>
                                            </g>';
                                        if ($score !== 0) {
                                            $svg = $svg. '<rect class="bar" x="10" width="60" y="'. 200 - $bar_height.'" height="'.$bar_height.'" fill="'.$chart_color.'"></rect>';
                                        }
                                        $svg = $svg.'</g>
                                        </svg>';
                
                                        // dd($svg);
                
                                        $chart = '<img src="data:image/svg+xml;base64,'.base64_encode($svg).'"  style="width:30%; margin-top:10px"/>';
                                        $html_aux = $html_aux . $chart;
                
                                    } elseif ($dinamic_aux[0] == 'radar-chart') {
                
                                        $svg_pattern = [
                                            'min' => [
                                                'x' => 150,
                                                'y' => 150,
                                            ],
                                            'max' => [
                                                ['x' => 150, 'y' => 50],
                                                ['x' => 233, 'y' => 94],
                                                ['x' => 245, 'y' => 179],
                                                ['x' => 192, 'y' => 240],
                                                ['x' => 81, 'y' => 222],
                                                ['x' => 51, 'y' => 162],
                                                ['x' => 65, 'y' => 96],
                                            ],
                                        ];
                
                                        // dd($dinamic_aux[1]);
                
                                        $score_raw_options = explode(",'", explode(']', $dinamic_aux[1])[0]);
                                        // dd($score_raw_options);
                
                                        $chart_color = $score_raw_options[0];
                                        // dd($chart_color);
                
                                        $score_options = [];
                
                                        for ($i=1; $i < sizeof($score_raw_options); $i++) { 
                                            $score_options[$i-1]['name'] = explode("'(", $score_raw_options[$i])[0];
                                            $score_options[$i-1]['score_conditions'] = explode(',', explode(')',explode("'(", $score_raw_options[$i])[1])[0]);
                                            $score_options[$i-1]['score'] = 0;
                                            $score_options[$i-1]['x'] = 150;
                                            $score_options[$i-1]['y'] = 150;
                                        }
                
                                        // dd($score_options);
                                        
                
                                        $score = 0;
                
                                        for ($i=0; $i < sizeof($score_options); $i++) { 
                                            $score = 0;
                                            foreach ($score_options[$i]['score_conditions'] as $condition) {
                                                if ($ans_id == '') {
                                                    $ans = Answer::where('aid', $condition)->first();
                                                    $ans_id = $ans->answer_id;
                                                } else { 
                                                    $ans = Answer::where('aid', $condition)->where('answer_id', $ans_id)->first();
                                                }
                                                if ( $ans !== null ) {
                                                    $score++;
                                                }
                                            }
                                            $normalized_score = $score * (10 / sizeof($score_options[$i]['score_conditions']));
                                            $score_options[$i]['score'] = $normalized_score;
                
                                            $newX = ((($svg_pattern['max'][$i]['x'] - $svg_pattern['min']['x']) / 10 ) * $normalized_score) + $svg_pattern['min']['x'];
                
                                            $score_options[$i]['x'] = $newX;
                                            
                                            // $newY = (94 - 150) / (233 - 150)* $newX + (94 - ((94 - 150) / (233 - 150)) * 233);
                                            $newY = ((($svg_pattern['max'][$i]['y'] - $svg_pattern['min']['y']) / 10 ) * $normalized_score) + $svg_pattern['min']['y'];
                                            $score_options[$i]['y'] = $newY;
                
                                        }
                                        // dd($score_options);
                
                                        // dd($ans);
                
                                        $svg = '<svg viewBox="0 0 300 300" style="font-family:sans-serif;">
                                        <g transform="translate(0,0)">
                                            <g class="x axis" transform="translate(0,0)">
                                                <g stroke="#c9c9c985" fill="none" stroke-width="1" transform="translate(0,0)" style="opacity: .65;">
                                                    <text font-size="6px" x="150" y="50" style="text-anchor: end;">10</text>
                                                    <text font-size="6px" x="150" y="70" style="text-anchor: end;">8</text>
                                                    <text font-size="6px" x="150" y="90" style="text-anchor: end;">6</text>
                                                    <text font-size="6px" x="150" y="110" style="text-anchor: end;">4</text>
                                                    <text font-size="6px" x="150" y="130" style="text-anchor: end;">2</text>
                                                    <text font-size="6px" x="150" y="150" style="text-anchor: end;">0</text>
                                                    
                                                    <circle cx="150" cy="150" r="100" ></circle>
                                                    <circle cx="150" cy="150" r="80" ></circle>
                                                    <circle cx="150" cy="150" r="60" ></circle>
                                                    <circle cx="150" cy="150" r="40" ></circle>
                                                    <circle cx="150" cy="150" r="20" ></circle>
                
                                                    <line x1="150" y1="150" x2="150" y2="50" ></line>
                                                    <line x1="150" y1="150" x2="233" y2="94" ></line>
                                                    <line x1="150" y1="150" x2="245" y2="179" ></line>
                                                    <line x1="150" y1="150" x2="192" y2="240" ></line>
                                                    <line x1="150" y1="150" x2="81" y2="222" ></line>
                                                    <line x1="150" y1="150" x2="51" y2="162" ></line>
                                                    <line x1="150" y1="150" x2="65" y2="96" ></line>
                                                </g>
                                                    <polygon stroke="'.$chart_color.'" stroke-width="2" fill="'.$chart_color.'" fill-opacity=".1" points="';
                
                                        foreach ($score_options as $chart_score) {
                                            $svg = $svg . $chart_score['x'] . ',' . $chart_score['y'] . ' ';
                                        }
                                        // $svg = $svg.'" /></svg>';
                                        $svg = $svg.'" ></polygon>
                                                <g stroke="#000" fill="none" stroke-width="1" transform="translate(0,0)" style="opacity: 1; color: #000;">
                                                    <text font-size="7px" x="150" y="35" style="text-anchor: middle;">'.$score_options[0]['name'].'</text>
                                                    <text font-size="7px" x="240" y="90" style="text-anchor: start;">'.$score_options[1]['name'].'</text>
                                                    <text font-size="7px" x="255" y="185" style="text-anchor: start;">'.$score_options[2]['name'].'</text>
                                                    <text font-size="7px" x="197" y="255" style="text-anchor: middle;">'.$score_options[3]['name'].'</text>
                                                    <text font-size="7px" x="75" y="232" style="text-anchor: end;">'.$score_options[4]['name'].'</text>
                                                    <text font-size="7px" x="41" y="165" style="text-anchor: end;">'.$score_options[5]['name'].'</text>
                                                    <text font-size="7px" x="55" y="95" style="text-anchor: end;">'.$score_options[6]['name'].'</text>
                                                </g></g>
                                            </g></svg>';
                                        
                
                                        // dd($svg);
                                        
                                        $chart = '<img src="data:image/svg+xml;base64,'.base64_encode($svg).'" style="width:100%;"/>';
                                        $html_aux = $html_aux . $chart;
                                    } elseif ($dinamic_aux[0] == 'now-date') {
                                        date_default_timezone_set('America/Sao_Paulo');
                                        // dd(date('m/d/Y h:i:s a', time()));
                    
                                        if ($dinamic[0] == "dia") {
                                            $date = date('d', time());
                                            $html_aux = $html_aux.$date;
                                        } else if ($dinamic[0] == "mes,toString()") {
                                            $date_aux = date('m', time());
                                            switch ($date_aux) {
                                                case 1:
                                                    $date = "Janeiro";
                                                    break;
                                                case 2:
                                                    $date = "Fevereiro";
                                                    break;
                                                case 3:
                                                    $date = "Março";
                                                    break;
                                                case 4:
                                                    $date = "Abril";
                                                    break;
                                                case 5:
                                                    $date = "Maio";
                                                    break;
                                                case 6:
                                                    $date = "Junho";
                                                    break;
                                                case 7:
                                                    $date = "Julho";
                                                    break;
                                                case 8:
                                                    $date = "Agosto";
                                                    break;
                                                case 9:
                                                    $date = "Setembro";
                                                    break;
                                                case 10:
                                                    $date = "Outubro";
                                                    break;
                                                case 11:
                                                    $date = "Novembro";
                                                    break;
                                                case 12:
                                                    $date = "Dezembro";
                                                    break;
                                            }
                                            $html_aux = $html_aux.$date;
                                        } else if ($dinamic[0] == "mes") {
                                            $date = date('m', time());
                                            $html_aux = $html_aux.$date;
                                        } else if ($dinamic[0] == "ano") {
                                            $date = date('Y', time());
                                            $html_aux = $html_aux.$date;
                                        }
                                    }
                
                                }
                            }
                
                            // dd($subs[1]);
                
                            $html = $html . $html_aux;
                            $html = $html.'</main></body>';
                
                            // dd($html);
                
                            $html_aux = explode('<img stored src="', $html);
                            // dd($html_aux);
                            for ($i=0; $i < sizeof($html_aux); $i++) { 
                                if ($i == 0) {
                                    $html = $html_aux[$i];
                                    // dd($html);
                                } else {
                                    $img_name = explode('/', explode('" ', $html_aux[$i])[0]);
                                    // dd(explode(' src="',$html_aux[$i]));
                
                                    $img_name = $img_name[sizeof($img_name)-1];
                                    // dd($img_name);
                                    $img_code = base64_encode(file_get_contents(storage_path('app/public/images/'.$img_name)));
                                    $html = $html . '<img src="data:image/png;base64,'. $img_code .'" ';
                                    // dd($html);
                
                                    $after_img = explode('" ', $html_aux[$i]);
                                    // dd($after_img);
                
                                    for ($j=1; $j < sizeof($after_img); $j++) { 
                                        if ($j > 1) {
                                            $html = $html. '" ' .$after_img[$j];
                                        } else {
                                            $html = $html . $after_img[$j];
                                        }
                                    }
                
                                    // dd($html);
                                }
                            }
                            // dd($html);
                
                
                            Pdf::setOption(['dpi' => 150, 'defaultFont' => 'sans-serif']);
                            $contxt = stream_context_create([
                                'ssl' => [
                                'verify_peer' => FALSE,
                                'verify_peer_name' => FALSE,
                                'allow_self_signed'=> TRUE
                                ]
                                ]);
                
                            $pdf = Pdf::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadHTML($html)->setPaper('a4');
                            $pdf->getDomPDF()->setHttpContext($contxt);
                            $options = $pdf->getOptions();
                            $options->setDefaultFont('sans-serif');

                            $path = $pdf->save(storage_path('app/public/pdfs/'.$deliverable->name.'_'.$ans_id.'.pdf'));
                            // dd($path);

                            $ebook = Ebook::updateOrCreate([
                                'account_id' => $flow->account_id,
                                'response_id' => $ans_id,
                                'name' => $deliverable->name,
                                'pdf_path' => $deliverable->name.'_'.$ans_id.'.pdf',
                            ]);
            
                            // dd($ebook);
            
                            $ebook_public_url = URL::route('ebook', ['path' => $ebook->pdf_path ]);

                            


                            $plugin = $flow->active_campaign_plugin;
                            // dd($plugin);
                    
                            $responseSearch = Http::withHeaders([
                                'Api-Token' => $plugin->api_key,
                                'Content-Type' => 'application/json',
                            ])
                            ->get($plugin->api_url.'/api/3/contacts?email='.$email);
                    
                            if (sizeof($responseSearch['contacts']) > 0) {
                                $contact_id = $responseSearch['contacts'][0]['id'];
                                // dd($contact_id);

                                $responseUpdateContact = Http::withHeaders([
                                    'Api-Token' => $plugin->api_key,
                                    'Content-Type' => 'application/json',
                                ])->withBody('
                                    {
                                        "contact": {
                                            "email": "'.$email.'",
                                            "fieldValues": [{
                                                    "field": "'.$plugin->deliverable_link_field_id.'",
                                                    "value": "'.$ebook_public_url.'"
                                                }
                                            ]
                                        }
                                    }
                                ', 'application/json')->put($plugin->api_url.'/api/3/contacts/'.$contact_id);
                    
                                // dd($responseUpdateContact['contact']);

                            } else {
                                
                                $responseCreateContact = Http::withHeaders([
                                    'Api-Token' => $plugin->api_key,
                                    'Content-Type' => 'application/json',
                                ])->withBody('
                                    {
                                        "contact": {
                                            "email": "'.$email.'",
                                            "fieldValues": [{
                                                    "field": "'.$plugin->deliverable_link_field_id.'",
                                                    "value": "'.$ebook_public_url.'"
                                                }
                                            ]
                                        }
                                    }
                                ', 'application/json')->post($plugin->api_url.'/api/3/contacts/');
                    
                                // dd($responseUpdateContact['contact']);

                                $contact_id = $responseCreateContact['contact']['id'];
                            }
                            
            
                            $responseAddTag = Http::withHeaders([
                                'Api-Token' => $plugin->api_key,
                                'Content-Type' => 'application/json',
                            ])->withBody('
                                {
                                    "contactTag": {
                                        "contact": "'.$contact_id.'",
                                        "tag": "'.$plugin->deliverable_tag_id.'"
                                    }
                                }
                            ', 'application/json')->post($plugin->api_url.'/api/3/contactTags');
                    
                
                            // return $pdf->save(storage_path('app/public/pdfs/'.$deliverable->name.'.pdf'))->stream('download.pdf');
                            // return [ $contact_id, $responseAddTag, $ebook_public_url];
                        }


                        $tr = TypeformRequest::firstOrCreate([
                            'account_id' => $flow->account_id,
                            'event_id' => $request->input('event_id'),
                            'typeform_signature' => $headers['typeform-signature'][0],
                            'form_id' => $form->fid,
                            'response_id' => $response->rid,
                            'value' => utf8_decode(utf8_encode($request->getContent())),
                            'ebook_id' => isset($ebook) ? $ebook->id : null,
                        ]);
                        // dd($tr);


                    }
                    unset($flow);

                }
                unset($deliverable);


                return [ "message" => "Done!" ];
            }

            // base64_encode
            // return  'sha256=' . base64_encode(hash_hmac('sha256',utf8_decode(utf8_encode($request->getContent())),'tfp_7sNjzpiKNECHZz22XwMp1xv7owVS9JPz43Stj57iXmzP_eoBbZtMAAwe5', false));
            // return [utf8_encode($request->getContent())];
            // return utf8_decode(utf8_encode($request->getContent()));
        }

        return [ "message" => "Unauthorized" ];
    }


}





