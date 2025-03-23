<?php

namespace App\Http\Controllers;

use App\Policies\Controllers\Controller;use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        return Inertia::render('Home', [
            'features' => [
                [
                    'title' => 'Gerenciamento de Tarefas',
                    'description' => 'Organize suas tarefas com facilidade usando nossa interface intuitiva.',
                    'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'
                ],
                [
                    'title' => 'Projetos',
                    'description' => 'Agrupe suas tarefas em projetos e mantenha tudo organizado.',
                    'icon' => 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z'
                ],
                [
                    'title' => 'Calendário',
                    'description' => 'Visualize suas tarefas em um calendário interativo.',
                    'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'
                ],
                [
                    'title' => 'Colaboração',
                    'description' => 'Trabalhe em equipe, compartilhe tarefas e acompanhe o progresso.',
                    'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'
                ],
                [
                    'title' => 'Subtarefas',
                    'description' => 'Divida tarefas complexas em subtarefas gerenciáveis.',
                    'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'
                ],
                [
                    'title' => 'Anexos',
                    'description' => 'Adicione arquivos e documentos às suas tarefas.',
                    'icon' => 'M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13'
                ]
            ]
        ]);
    }
}
