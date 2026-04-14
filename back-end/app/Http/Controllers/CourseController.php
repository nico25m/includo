<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CourseController extends Controller
{
    public function embed(Request $request)
    {
        $dati = $request->validate([
            'id' => 'required|string',
            'title' => 'required|string',
            'description' => 'required|string',
            'skills' => 'required|array',
            'duration' => 'required|string',
            'remote' => 'required|boolean',
        ]);

        $testoPerEmbedding = $dati['title'] . ". " . $dati['description'] . " Skills: " . implode(', ', $dati['skills']) . ". Durata: " . $dati['duration'];

        $chiaveOpenAI = config('services.openai.key');
        
        $rispostaEmbedding = Http::withToken($chiaveOpenAI)->post('https://api.openai.com/v1/embeddings', [
            'model' => 'text-embedding-3-small',
            'input' => $testoPerEmbedding,
        ]);

        $vettore = $rispostaEmbedding->json('data.0.embedding');

        $corso = Course::updateOrCreate(
            ['id' => $dati['id']],
            [
                'vector' => $vettore,
                'title' => $dati['title'],
                'description' => $dati['description'],
                'skills' => implode(', ', $dati['skills']),
                'duration' => $dati['duration'],
                'remote' => $dati['remote'],
            ]
        );

        return response()->json(['status' => 'success', 'id' => $corso->id]);
    }

    public function semanticSearch(string $testoCercato, int $quanti = 2)
    {
        $chiaveOpenAI = config('services.openai.key');

        $rispostaEmbedding = Http::withToken($chiaveOpenAI)->post('https://api.openai.com/v1/embeddings', [
            'model' => 'text-embedding-3-small',
            'input' => $testoCercato,
        ]);

        $vettoreQuery = $rispostaEmbedding->json('data.0.embedding');

        $tuttiICorsi = Course::all();
        $risultatiConPunteggio = [];

        foreach ($tuttiICorsi as $corso) {
            $vettoreCorso = $corso->vector;
            
            $prodottoScalare = 0;
            $normaA = 0;
            $normaB = 0;

            foreach ($vettoreQuery as $i => $valore) {
                $prodottoScalare += $valore * $vettoreCorso[$i];
                $normaA += $valore * $valore;
                $normaB += $vettoreCorso[$i] * $vettoreCorso[$i];
            }

            if ($normaA > 0 && $normaB > 0) {
                $similitudine = $prodottoScalare / (sqrt($normaA) * sqrt($normaB));
            } else {
                $similitudine = 0;
            }

            $risultatiConPunteggio[] = [
                'punteggio' => $similitudine,
                'corso' => $corso
            ];
        }

        usort($risultatiConPunteggio, function($a, $b) {
            return $b['punteggio'] <=> $a['punteggio'];
        });

        $topCorsi = array_slice($risultatiConPunteggio, 0, $quanti);
        
        $rispostaFinale = [];
        foreach ($topCorsi as $item) {
            $c = $item['corso'];
            $rispostaFinale[] = [
                'title' => $c->title,
                'description' => $c->description,
                'duration' => $c->duration,
                'skills' => $c->skills,
                'remote' => $c->remote ? 'Sì' : 'No'
            ];
        }

        return $rispostaFinale;
    }
}
