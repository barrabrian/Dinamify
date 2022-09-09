<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\Facade\Options;

use App\Models\Deliverable;
use App\Models\Answer;
use App\Models\Form;
use App\Models\Image;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class DeliverablesController extends Controller
{
    public function index()
    {
        return Inertia::render('Deliverables/Index', [
            'filters' => Request::all('search', 'trashed'),
            'deliverables' => Auth::user()->account->deliverables()
                ->orderBy('name')
                ->filter(Request::only('search', 'trashed'))
                ->paginate(10)
                ->withQueryString()
                ->through(fn ($deliverable) => [
                    'id' => $deliverable->id,
                    'name' => $deliverable->name,
                    'deleted_at' => $deliverable->deleted_at,
                ]),
        ]);
    }

    public function createPDF(Deliverable $deliverable, $response_id)
    {
        // $deliverable = Auth::user()->account->deliverables()->first();

        // dd($deliverable);

        if (!$deliverable || !$response_id) {return Redirect::back()->with('error', "Erro: Parametros Incorretos!");}

        $html = "<html><head><style>body{font-family: sans-serif;} .page-break{page-break-after: always;} @page {margin: 120px 60px;} header {position: fixed;top: -60px;left: 0px;right: 0px;height: 50px;
                line-height: 35px;} footer {position: fixed; bottom: -80px; left: 0px; right: 0px;height: 30px; padding: 0 100px;
                text-align: center;font-size: 12px;font-family: serif;}</style></head><body>";

        // $html = $html.'<img src="'.storage_path('app/users/Os28BZhpNA9F31GmbqOHs6VA6DTnclwTpeUa1ows.png').'" width="100px" height="100px">';

        // foreach ($teste as $org) {
        //     $html = $html.'<h1>'.$org->name.'</h1>';
        // }

        // $html = $html.$deliverable->html;
        $html_aux = '';
        $ans_id = $response_id;


        $html_aux = explode('<div class="page-break"></div>', $deliverable->html);
        for ($i=0; $i < sizeof($html_aux); $i++) { 
            if ($i == 0) {
                $html_whf = $html_aux[$i] . '<div class="page-break"></div>' . $deliverable->header . $deliverable->footer . '<main>';
            } else {
                $html_whf = $html_whf . $html_aux[$i] . '<div class="page-break"></div>';
            }
        }

        // dd($html_whf);

        $html_aux = '';
        $html_wifs = '';
        $html_aux = explode('{{end-if}}', $html_whf);
        // dd($html_aux);
        
        
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
                // dd($cond);
                if (!$ans) {
                    return "Este modelo possui variáveis de outra Origem de Dados.";
                }

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

                    if (!$ans) {
                        return "Este modelo possui variáveis de outra Origem de Dados.";;
                    }    
                    
                    // dd($ans);
                    
                    $html_aux = $html_aux.$ans->value;


                } elseif ($dinamic_aux[0] == 'vbar-chart') {
                    // $html_aux = $html_aux.'[GRÁFICO EM BARRA]';

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
                                <text dy=".71em" y="11" x="3" style="text-anchor: middle;">'.$chart_name.'</text>
                            </g>
                            <path class="domain" d="M0,6V0H900V6"></path>
                        </g>
                            <g class="y axis">
                                <g class="tick" transform="translate(0,200)" style="opacity: 1;"><line x2="90" y2="0" fill="none" stroke="#000" shape-rendering="crispEdges"></line>
                                <text dy=".32em" x="-9" y="0" style="text-anchor: end;">0</text>
                            </g>
                                <g class="tick" transform="translate(0,160)" style="opacity: 1;"><line x2="90" y2="0" fill="none" stroke="#000" shape-rendering="crispEdges" style="opacity: .2;"></line>
                                <text dy=".32em" x="-9" y="0" style="text-anchor: end;">2</text>
                            </g>
                                <g class="tick" transform="translate(0,120)" style="opacity: 1;"><line x2="90" y2="0" fill="none" stroke="#000" shape-rendering="crispEdges" style="opacity: .2;"></line>
                                <text dy=".32em" x="-9" y="0" style="text-anchor: end;">4</text>
                            </g>
                                <g class="tick" transform="translate(0,80)" style="opacity: 1;"><line x2="90" y2="0" fill="none" stroke="#000" shape-rendering="crispEdges" style="opacity: .2;"></line>
                                <text dy=".32em" x="-9" y="0" style="text-anchor: end;">6</text>
                            </g>
                                <g class="tick" transform="translate(0,40)" style="opacity: 1;"><line x2="90" y2="0" fill="none" stroke="#000" shape-rendering="crispEdges" style="opacity: .2;"></line>
                                <text dy=".32em" x="-9" y="0" style="text-anchor: end;">8</text>
                            </g>
                                <g class="tick" transform="translate(0,0)" style="opacity: 1;"><line x2="90" y2="0" fill="none" stroke="#000" shape-rendering="crispEdges" style="opacity: .2;"></line>
                                <text dy=".32em" x="-9" y="0" style="text-anchor: end;">10</text>
                            </g>
                    
                        </g>';
                    if ($score !== 0) {
                        $svg = $svg. '<rect class="bar" x="15" width="60" y="'. 200 - $bar_height.'" height="'.$bar_height.'" fill="'.$chart_color.'"></rect>';
                    }
                    $svg = $svg.'</g>
                    </svg>';

                    // $svg = '<svg viewBox="0 0 60 180" style="font-family:sans-serif;">
                    //     <g transform="translate(40,-40)">
                    //     <g class="x axis" transform="translate(0,203)">
                    //             <g class="tick" transform="translate(40,0)" style="opacity: 1;">
                    //             <text dy=".71em" y="9" x="0" style="text-anchor: middle;">TESTE</text>
                    //         </g>
                    //         <path class="domain" d="M0,6V0H900V6"></path>
                    //     </g>
                    //         <g class="y axis">
                    //             <g class="tick" transform="translate(0,200)" style="opacity: 1;"><line x2="-6" y2="0" fill="none" stroke="#000" shape-rendering="crispEdges"></line>
                    //             <text dy=".32em" x="-9" y="0" style="text-anchor: end;">0</text>
                    //         </g>
                    //             <g class="tick" transform="translate(0,160)" style="opacity: 1;"><line x2="-6" y2="0" fill="none" stroke="#000" shape-rendering="crispEdges"></line>
                    //             <text dy=".32em" x="-9" y="0" style="text-anchor: end;">2</text>
                    //         </g>
                    //             <g class="tick" transform="translate(0,120)" style="opacity: 1;"><line x2="-6" y2="0" fill="none" stroke="#000" shape-rendering="crispEdges"></line>
                    //             <text dy=".32em" x="-9" y="0" style="text-anchor: end;">4</text>
                    //         </g>
                    //             <g class="tick" transform="translate(0,80)" style="opacity: 1;"><line x2="-6" y2="0" fill="none" stroke="#000" shape-rendering="crispEdges"></line>
                    //             <text dy=".32em" x="-9" y="0" style="text-anchor: end;">6</text>
                    //         </g>
                    //             <g class="tick" transform="translate(0,40)" style="opacity: 1;"><line x2="-6" y2="0" fill="none" stroke="#000" shape-rendering="crispEdges"></line>
                    //             <text dy=".32em" x="-9" y="0" style="text-anchor: end;">8</text>
                    //         </g>
                    //             <g class="tick" transform="translate(0,0)" style="opacity: 1;"><line x2="-6" y2="0" fill="none" stroke="#000" shape-rendering="crispEdges"></line>
                    //             <text dy=".32em" x="-9" y="0" style="text-anchor: end;">10</text>
                    //         </g>
                    
                    //         <path class="domain" d="M-6,0H0V200H-6" fill="none" stroke="#000" shape-rendering="crispEdges"></path>
                    //     </g>
                    //         <rect class="bar" x="10" width="60" y="100" height="100" fill="#00d"></rect>
                    //     </g>
                    // </svg>';

                    

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


                    // $svg = '<svg viewBox="0 0 300 300" style="font-family:sans-serif;">
                    //     <g transform="translate(0,0)">
                    //         <g class="x axis" transform="translate(0,0)">
                    //             <g stroke="#c9c9c985" fill="none" stroke-width="1" transform="translate(0,0)" style="opacity: .65;">
                    //                 <text font-size="6px" x="150" y="50" style="text-anchor: end;">10</text>
                    //                 <text font-size="6px" x="150" y="70" style="text-anchor: end;">8</text>
                    //                 <text font-size="6px" x="150" y="90" style="text-anchor: end;">6</text>
                    //                 <text font-size="6px" x="150" y="110" style="text-anchor: end;">4</text>
                    //                 <text font-size="6px" x="150" y="130" style="text-anchor: end;">2</text>
                    //                 <text font-size="6px" x="150" y="150" style="text-anchor: end;">0</text>
                    //                 <circle cx="150" cy="150" r="100" ></circle>
                    //                 <circle cx="150" cy="150" r="80" ></circle>
                    //                 <circle cx="150" cy="150" r="60" ></circle>
                    //                 <circle cx="150" cy="150" r="40" ></circle>
                    //                 <circle cx="150" cy="150" r="20" ></circle>
                    //                 <line x1="150" y1="150" x2="150" y2="50" ></line>
                    //                 <line x1="150" y1="150" x2="233" y2="94" ></line>
                    //                 <line x1="150" y1="150" x2="245" y2="179" ></line>
                    //                 <line x1="150" y1="150" x2="192" y2="240" ></line>
                    //                 <line x1="150" y1="150" x2="81" y2="222" ></line>
                    //                 <line x1="150" y1="150" x2="51" y2="162" ></line>
                    //                 <line x1="150" y1="150" x2="65" y2="96" ></line>
                    //                 <text font-size="7px" x="150" y="30" style="text-anchor: middle;">Teste1</text>
                    //                 <text font-size="7px" x="253" y="90" style="text-anchor: middle;">Teste2</text>
                    //                 <text font-size="7px" x="265" y="185" style="text-anchor: middle;">Teste3</text>
                    //                 <text font-size="7px" x="197" y="255" style="text-anchor: middle;">Teste4</text>
                    //                 <text font-size="7px" x="58" y="232" style="text-anchor: middle;">Teste5</text>
                    //                 <text font-size="7px" x="31" y="162" style="text-anchor: middle;">Teste6</text>
                    //                 <text font-size="7px" x="45" y="100" style="text-anchor: middle;">Teste7</text>
                    //             </g>
                    //             <polygon points="150,130 216.4,105.2 245,179 183.6,222 81,222 71,160 82,106.8" stroke="#50E2C2" stroke-width="2" fill="#50E2C2" fill-opacity=".1"  ></polygon>
                    //         </g>
                    //     </g>
                    // </svg>';
                    

                    // dd($svg);
                    
                    $chart = '<img src="data:image/svg+xml;base64,'.base64_encode($svg).'" style="width:100%;"/>';
                    // $chart = '<img alt="" style="width:300px; margin-top:10px" src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMSIgaGVpZ2h0PSIxIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InJlZCIvPjwvc3ZnPg==" /> ';
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
        // $html = $html.'<p><b>'. $ans_id .'</b></p></main></body>';


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
                // $html = $html . '<img src="data:image/png;base64,"';
                // dd($html);

                // $html = $html . explode(' src="',$html_aux[$i])[0]. ' ';

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

                // dd($html_aux[$i]);
                // dd(explode(' src="',$html_aux[$i]));
                // dd(explode('" ', explode(' src="',$html_aux[$i])[1]));

                // for ($j=0; $j < sizeof(explode(' src="',$html_aux[$i])); $j++) { 
                //     if ($j > 1) {
                //         $html = $html . ' src="' . explode(' src="',$html_aux[$i])[$j];
                //     }
                //     for ($k=0; $k < sizeof(explode(' src="',$html_aux[$i])); $k++) { 
                //         $html = $html . ' src="' . explode(' src="',$html_aux[$i])[$k];
                //     }
                // }

            }
        }

        // dd($html);


        


        // $html = '<img src="data:image/png;base64,'.base64_encode(file_get_contents(storage_path('app/public/images/v8bIyrptfjRW7OH6Ck5x1swUJsuhQPbem1PxQcR3.png'))).'" style="width:30%; margin-top:10px"/>';

        // return PDF::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('reports.invoiceSell')->stream();


        Pdf::setOption(['dpi' => 150, 'defaultFont' => 'sans-serif']);
        $contxt = stream_context_create([
            'ssl' => [
            'verify_peer' => FALSE,
            'verify_peer_name' => FALSE,
            'allow_self_signed'=> TRUE
            ]
            ]);

        // $pdf = Pdf::loadHTML('<h1>Teste</h1>')->setPaper('a4');
        $pdf = Pdf::setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadHTML($html)->setPaper('a4');
        $pdf->getDomPDF()->setHttpContext($contxt);
        $options = $pdf->getOptions();
        $options->setDefaultFont('sans-serif');
        // $options->set('isRemoteEnabled', TRUE);

        // dd($options);

        // $pdf->setOptions($options);
        // $pdf->set_option('isRemoteEnabled', TRUE);
        
        return $pdf->stream('download.pdf');

    }

    public function create()
    {
        return Inertia::render('Deliverables/Create',[
            'forms' => Auth::user()->account->forms()
                ->orderBy('title')
                ->get(),
            'images' => Auth::user()->account->images()
                ->orderBy('id')
                ->get()
                ->transform(fn ($image) => [
                    'id' => $image->id,
                    'name' => $image->name,
                    'image_path' => URL::route('image', ['path' => $image->image_path]),
                ]),
        ]);
    }

    public function store()
    {
        Auth::user()->account->deliverables()->create(
            Request::validate([
                'name' => ['required', 'max:100'],
                'html' => ['required'],
                'form_id' => ['required'],
                'header' => ['nullable'],
                'footer' => ['nullable'],
            ])
        );

        return Redirect::route('deliverables')->with('success', 'Entregável adicionado!');
    }

    public function edit(Deliverable $deliverable)
    {
        return Inertia::render('Deliverables/Edit', [
            'deliverable' => [
                'id' => $deliverable->id,
                'name' => $deliverable->name,
                'html' => $deliverable->html,
                'header' => $deliverable->header,
                'footer' => $deliverable->footer,
                'form_id' => $deliverable->form_id,
                'deleted_at' => $deliverable->deleted_at,
            ],
            'forms' => Auth::user()->account->forms()
                ->orderBy('name')
                ->get(),
            'images' => Auth::user()->account->images()
                ->orderBy('id')
                ->get()
                ->transform(fn ($image) => [
                    'id' => $image->id,
                    'name' => $image->name,
                    'image_path' => URL::route('image', ['path' => $image->image_path]),
                ]),
        ]);
    }

    public function update(Deliverable $deliverable)
    {
        // dd(Request::collect('form_id')[0]);
        Request::validate([
            'name' => ['required', 'max:100'],
            'html' => ['required'],
            'form_id' => ['required'],
            'header' => ['nullable'],
            'footer' => ['nullable'],
        ]);

        // dd($deliverable->automations()->get());

        if (Request::collect('form_id')[0] !== $deliverable->form_id) {
            $flows = $deliverable->automations()->get();

            foreach ($flows as $flow) {
                $plugin = $flow->typeform_plugin;
                $form = Auth::user()->account->forms()->find(Request::collect('form_id')[0]);
                // dd($form);

                $responseWebhook = Http::withHeaders([
                    'Authorization' => 'Bearer '.$plugin->token,
                    'Content-Type' => 'application/json',
                ])->withBody('
                    {
                        "enabled" : true,
                        "secret" : "'.$plugin->token.'",
                        "url" : "http://google.com",
                        "verify_ssl" : false
                    }
                ', 'application/json')->put('https://api.typeform.com/forms/'. $form->fid . '/webhooks/"dinamify' . $flow->id.'"');

                if(array_key_exists('code',$responseWebhook->json())){
                    if ($responseWebhook->json()['code'] == 'AUTHENTICATION_FAILED') {
                        return Redirect::back()->with('error', 'A Autenticação do Plugin '. $plugin->name .' Falhou.');
                    }
                }

                $responseWebhook = Http::withHeaders([
                    'Authorization' => 'Bearer '.$plugin->token,
                    'Content-Type' => 'application/json',
                ])->delete('https://api.typeform.com/forms/'. $form->fid . '/webhooks/"dinamify' . $flow->id.'"');

            }
            unset($flow);
        }

        $deliverable->update(Request::all());


        return Redirect::back()->with('success', 'Entregável atualizado!');
    }

    public function destroy(Organization $organization)
    {
        $organization->delete();

        return Redirect::back()->with('success', 'Entregável deletado.');
    }

    public function restore(Organization $organization)
    {
        $organization->restore();

        return Redirect::back()->with('success', 'Entregável restaurado!');
    }

    public function storeImage()
    {
        Request::validate([
            'name' => ['nullable', 'max:45'],
            'image' => ['required', 'image'],
        ]);

        Auth::user()->account->images()->create([
            'name' => Request::get('name'),
            'image_path' => Request::file('image') ? Request::file('image')->store('public/images') : null,
        ]);

        return Redirect::back()->with('success', 'Imagem Adicionada.');
    }
}
