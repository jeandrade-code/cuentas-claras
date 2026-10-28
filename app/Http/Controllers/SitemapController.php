<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        // Lista de rutas públicas de tu aplicación
        $urls = [
            [
                'loc' => route('inicio'), // URL de la Landing
                'lastmod' => now()->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '1.0',
            ],
            [
                'loc' => route('politica'), // URL de Política de Privacidad
                'lastmod' => now()->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ],
            [
                'loc' => route('terminos'), // URL de Términos y Condiciones
                'lastmod' => now()->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ]
        ];

        // Retornamos la vista con el Content-Type correcto para XML
        return response()->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'text/xml');
    }
}