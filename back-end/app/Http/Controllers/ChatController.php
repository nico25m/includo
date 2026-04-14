<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    public function chat(Request $request)
    {
        $datiRequest = $request->validate([
            'session_id' => 'required|string',
            'message' => 'required|string',
        ]);

        $sid = $datiRequest['session_id'];
        $messaggioUtente = $datiRequest['message'];

        Conversation::create([
            'session_id' => $sid,
            'role' => 'user',
            'content' => $messaggioUtente,
        ]);

        $messaggiPerOpenAI = [];
        $messaggiPerOpenAI[] = [
            'role' => 'system',
            'content' => "Sei Indo, l'assistente di IncluDO. Aiuti persone in difficoltà a trovare corsi artigianali. Fai una domanda alla volta. Chiedi interessi, tempo disponibile e se preferiscono remoto o presenza. Solo dopo 3 o 4 messaggi, usa searchCourses per trovare i corsi."
        ];

        $storicoDB = Conversation::where('session_id', $sid)->orderBy('created_at')->get();

        foreach ($storicoDB as $riga) {
            if ($riga->role == 'tool') {
                $messaggiPerOpenAI[] = [
                    'role' => 'tool',
                    'tool_call_id' => $riga->tool_call_id,
                    'content' => $riga->content
                ];
            } else if ($riga->role == 'assistant' && $riga->tool_name == 'searchCourses') {
                $messaggiPerOpenAI[] = [
                    'role' => 'assistant',
                    'content' => null,
                    'tool_calls' => json_decode($riga->content, true)
                ];
            } else {
                $messaggiPerOpenAI[] = [
                    'role' => $riga->role,
                    'content' => $riga->content
                ];
            }
        }

        $strumenti = [
            [
                'type' => 'function',
                'function' => [
                    'name' => 'searchCourses',
                    'description' => 'Cerca i corsi artigianali nel database di IncluDO.',
                    'parameters' => [
                        'type' => 'object',
                        'properties' => [
                            'query' => [
                                'type' => 'string',
                                'description' => 'Testo della ricerca basato sulle preferenze utente.'
                            ]
                        ],
                        'required' => ['query']
                    ]
                ]
            ]
        ];

        $chiaveOpenAI = config('services.openai.key');

        $risposta = Http::withToken($chiaveOpenAI)->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o-mini',
            'messages' => $messaggiPerOpenAI,
            'tools' => $strumenti,
            'tool_choice' => 'auto'
        ]);

        $risultatoOpenAI = $risposta->json('choices.0.message');

        if (isset($risultatoOpenAI['tool_calls'])) {
            $chiamataTool = $risultatoOpenAI['tool_calls'][0];
            $idChiamata = $chiamataTool['id'];
            $argomenti = json_decode($chiamataTool['function']['arguments'], true);
            $testoCerca = $argomenti['query'];

            Conversation::create([
                'session_id' => $sid,
                'role' => 'assistant',
                'content' => json_encode($risultatoOpenAI['tool_calls']),
                'tool_call_id' => $idChiamata,
                'tool_name' => 'searchCourses'
            ]);

            $controllerCorsi = new CourseController();
            $risultatiRAG = $controllerCorsi->semanticSearch($testoCerca);

            Conversation::create([
                'session_id' => $sid,
                'role' => 'tool',
                'content' => json_encode($risultatiRAG),
                'tool_call_id' => $idChiamata,
                'tool_name' => 'searchCourses'
            ]);

            $messaggiPerOpenAI[] = $risultatoOpenAI;
            $messaggiPerOpenAI[] = [
                'role' => 'tool',
                'tool_call_id' => $idChiamata,
                'content' => json_encode($risultatiRAG)
            ];

            $rispostaFinaleOb = Http::withToken($chiaveOpenAI)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => $messaggiPerOpenAI
            ]);

            $testoFinale = $rispostaFinaleOb->json('choices.0.message.content');

            Conversation::create([
                'session_id' => $sid,
                'role' => 'assistant',
                'content' => $testoFinale
            ]);

            return response()->json(['reply' => $testoFinale]);
        }

        $testoRispostaSemplice = $risultatoOpenAI['content'];

        Conversation::create([
            'session_id' => $sid,
            'role' => 'assistant',
            'content' => $testoRispostaSemplice
        ]);

        return response()->json(['reply' => $testoRispostaSemplice]);
    }
}
