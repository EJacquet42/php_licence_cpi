<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard des Logs</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Figtree', system-ui, sans-serif;
            background: #0b0d17;
            color: #cbd5e1;
            min-height: 100vh;
        }
        .container { max-width: 1280px; margin: 0 auto; padding: 20px; }

        header {
            margin-bottom: 20px;
            background: linear-gradient(135deg, #1e1b4b, #0b0d17 60%);
            border: 1px solid rgba(99,102,241,.2);
            border-radius: 20px;
            padding: 28px 32px;
            position: relative;
            overflow: hidden;
        }
        header::before {
            content: '';
            position: absolute;
            top: -50%; right: -20%;
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(99,102,241,.12), transparent 70%);
            border-radius: 50%;
        }
        header h1 {
            font-size: 26px; font-weight: 700;
            background: linear-gradient(135deg, #a5b4fc, #818cf8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            position: relative;
        }
        header p { color: #64748b; margin-top: 4px; font-size: 14px; position: relative; }

        .stats {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            margin-bottom: 18px;
        }
        .stat-card {
            background: linear-gradient(145deg, rgba(30,27,75,.6), rgba(15,23,42,.8));
            border: 1px solid rgba(99,102,241,.15);
            border-radius: 16px;
            padding: 18px;
            position: relative;
            overflow: hidden;
            transition: transform .2s, border-color .2s;
        }
        .stat-card::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, rgba(99,102,241,.4), transparent);
        }
        .stat-card:hover { transform: translateY(-3px) scale(1.01); border-color: rgba(99,102,241,.35); }
        .stat-card .label { font-size: 10px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }
        .stat-card .value { font-size: 28px; font-weight: 800; color: #e2e8f0; margin-top: 2px; line-height: 1.2; }
        .stat-card .value.green { color: #34d399; }
        .stat-card .value.red { color: #fb7185; }
        .stat-card .value.purple { color: #a78bfa; }

        .charts {
            display: grid;
            grid-template-columns: 1fr 1fr 1.2fr;
            gap: 10px;
            margin-bottom: 18px;
        }
        .chart-card {
            background: linear-gradient(145deg, rgba(30,27,75,.5), rgba(15,23,42,.7));
            border: 1px solid rgba(99,102,241,.12);
            border-radius: 16px;
            padding: 16px;
            transition: border-color .2s;
        }
        .chart-card:hover { border-color: rgba(99,102,241,.25); }
        .chart-card h3 {
            font-size: 10px; font-weight: 700; color: #64748b;
            text-transform: uppercase; letter-spacing: 1px;
            margin-bottom: 10px;
        }

        .bar-row { display: flex; align-items: center; gap: 8px; margin-bottom: 3px; font-size: 12px; }
        .bar-row .label { width: 56px; text-align: right; color: #94a3b8; flex-shrink: 0; font-weight: 500; }
        .bar-row .bar-track { flex: 1; height: 10px; background: rgba(15,23,42,.8); border-radius: 6px; overflow: hidden; }
        .bar-row .bar-fill { height: 100%; border-radius: 6px; transition: width .4s ease; }
        .bar-row .count { width: 28px; color: #64748b; text-align: right; flex-shrink: 0; font-weight: 600; }
        .bar-fill.emerg, .bar-fill.alert { background: linear-gradient(90deg, #e11d48, #fb7185); }
        .bar-fill.crit { background: linear-gradient(90deg, #f43f5e, #fb7185); }
        .bar-fill.error { background: linear-gradient(90deg, #d946ef, #e879f9); }
        .bar-fill.warning { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
        .bar-fill.notice { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
        .bar-fill.info { background: linear-gradient(90deg, #10b981, #34d399); }
        .bar-fill.debug { background: linear-gradient(90deg, #6b7280, #9ca3af); }
        .bar-fill.quiz { background: linear-gradient(90deg, #a78bfa, #c4b5fd); }
        .bar-fill.question { background: linear-gradient(90deg, #f472b6, #f9a8d4); }
        .bar-fill.auth { background: linear-gradient(90deg, #60a5fa, #93c5fd); }
        .bar-fill.manual { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
        .bar-fill.system { background: linear-gradient(90deg, #6b7280, #9ca3af); }

        .daily-chart { display: flex; align-items: flex-end; gap: 6px; height: 80px; }
        .daily-chart .col { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: flex-end; }
        .daily-chart .bar {
            width: 100%;
            border-radius: 4px 4px 0 0;
            background: linear-gradient(180deg, #818cf8, #6366f1);
            min-height: 3px;
            transition: all .3s ease;
        }
        .daily-chart .col:hover .bar {
            filter: brightness(1.3);
            transform: scaleY(1.05);
            transform-origin: bottom;
        }
        .daily-chart .lbl { font-size: 9px; color: #64748b; margin-top: 4px; font-weight: 600; }

        .filters { display: flex; gap: 6px; margin-bottom: 12px; flex-wrap: wrap; }
        .filter-btn {
            padding: 7px 16px;
            border: 1px solid rgba(99,102,241,.15);
            border-radius: 10px;
            background: rgba(30,27,75,.5);
            color: #94a3b8;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: all .2s;
        }
        .filter-btn:hover {
            border-color: rgba(99,102,241,.4);
            color: #a5b4fc;
            background: rgba(99,102,241,.1);
        }
        .filter-btn.active {
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            color: #fff;
            border-color: transparent;
            box-shadow: 0 2px 12px rgba(99,102,241,.3);
        }

        .log-entry {
            background: linear-gradient(135deg, rgba(30,27,75,.4), rgba(15,23,42,.6));
            border: 1px solid rgba(99,102,241,.1);
            border-radius: 14px;
            padding: 14px 18px;
            display: flex;
            align-items: flex-start;
            gap: 14px;
            transition: all .2s;
            font-size: 14px;
            margin-bottom: 6px;
            position: relative;
        }
        .log-entry::before {
            content: '';
            position: absolute;
            left: 0; top: 10%; bottom: 10%;
            width: 2px;
            border-radius: 2px;
            background: rgba(99,102,241,.2);
            transition: all .2s;
        }
        .log-entry:hover {
            border-color: rgba(99,102,241,.3);
            transform: translateX(4px);
            background: linear-gradient(135deg, rgba(30,27,75,.6), rgba(15,23,42,.8));
        }
        .log-entry:hover::before {
            top: 5%; bottom: 5%;
            background: linear-gradient(180deg, #818cf8, #6366f1);
        }
        .log-icon {
            flex-shrink: 0;
            width: 36px; height: 36px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            font-weight: 700;
            border: 1px solid transparent;
        }
        .log-icon.quiz { background: rgba(167,139,250,.1); color: #a78bfa; border-color: rgba(167,139,250,.15); }
        .log-icon.question { background: rgba(244,114,182,.1); color: #f472b6; border-color: rgba(244,114,182,.15); }
        .log-icon.auth { background: rgba(96,165,250,.1); color: #60a5fa; border-color: rgba(96,165,250,.15); }
        .log-icon.manual { background: rgba(245,158,11,.1); color: #f59e0b; border-color: rgba(245,158,11,.15); }
        .log-icon.system { background: rgba(107,114,128,.1); color: #9ca3af; border-color: rgba(107,114,128,.15); }
        .log-body { flex: 1; min-width: 0; }
        .log-header { display: flex; align-items: center; gap: 10px; margin-bottom: 3px; flex-wrap: wrap; }
        .log-action {
            font-size: 10px; font-weight: 700;
            padding: 3px 12px; border-radius: 20px;
            letter-spacing: .5px;
            text-transform: uppercase;
        }
        .log-action.quiz { background: rgba(167,139,250,.12); color: #a78bfa; }
        .log-action.question { background: rgba(244,114,182,.12); color: #f472b6; }
        .log-action.auth { background: rgba(96,165,250,.12); color: #60a5fa; }
        .log-action.manual { background: rgba(245,158,11,.12); color: #f59e0b; }
        .log-action.system { background: rgba(107,114,128,.12); color: #94a3b8; }
        .log-time { font-size: 11px; color: #475569; }
        .log-message { font-size: 14px; color: #cbd5e1; line-height: 1.5; margin-top: 2px; }
        .log-message strong { color: #e2e8f0; font-weight: 600; }
        .log-meta { margin-top: 5px; display: flex; gap: 10px; font-size: 11px; color: #64748b; flex-wrap: wrap; }
        .log-severity { padding: 2px 10px; border-radius: 20px; font-weight: 600; letter-spacing: .3px; font-size: 10px; }
        .log-severity.emerg, .log-severity.alert { background: rgba(225,29,72,.12); color: #fb7185; }
        .log-severity.crit { background: rgba(244,63,94,.12); color: #fb7185; }
        .log-severity.error { background: rgba(217,70,239,.12); color: #e879f9; }
        .log-severity.warning { background: rgba(245,158,11,.12); color: #fbbf24; }
        .log-severity.notice { background: rgba(59,130,246,.12); color: #60a5fa; }
        .log-severity.info { background: rgba(16,185,129,.12); color: #34d399; }
        .log-severity.debug { background: rgba(107,114,128,.12); color: #9ca3af; }

        .empty-state {
            text-align: center; padding: 40px;
            background: linear-gradient(145deg, rgba(30,27,75,.4), rgba(15,23,42,.6));
            border: 1px solid rgba(99,102,241,.1);
            border-radius: 16px; color: #64748b;
        }
        .pagination { margin-top: 18px; }
        .pagination-inner {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }
        .page-link {
            padding: 8px 20px;
            border: 1px solid rgba(99,102,241,.2);
            border-radius: 10px;
            background: rgba(30,27,75,.5);
            color: #94a3b8;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            transition: all .2s;
        }
        .page-link:hover { border-color: rgba(99,102,241,.4); color: #a5b4fc; background: rgba(99,102,241,.1); }
        .page-link.disabled { opacity: .4; pointer-events: none; }
        .page-info { font-size: 13px; color: #64748b; font-weight: 500; }

        .footer {
            margin-top: 24px; text-align: center;
            font-size: 11px; color: #334155; letter-spacing: .5px;
        }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #0b0d17; }
        ::-webkit-scrollbar-thumb { background: rgba(99,102,241,.2); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(99,102,241,.4); }

        @media (max-width: 900px) {
            .stats { grid-template-columns: repeat(3, 1fr); }
            .charts { grid-template-columns: 1fr; }
        }
        @media (max-width: 600px) {
            .container { padding: 10px; }
            .stats { grid-template-columns: 1fr 1fr; gap: 6px; }
            .stat-card { padding: 12px; }
            .stat-card .value { font-size: 22px; }
            .log-entry { flex-direction: column; gap: 6px; }
            header { padding: 18px; }
            header h1 { font-size: 20px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Dashboard des logs</h1>
            <p>Suivi des événements et des activités</p>
            <a href="{{ route('questions.stats') }}" style="display:inline-block;margin-top:10px;color:#818cf8;text-decoration:none;font-size:13px;font-weight:500;transition:color .2s;position:relative;">&rarr; Stats par question</a>
        </header>

        <div class="stats">
            <div class="stat-card">
                <div class="label">Total logs</div>
                <div class="value">{{ $stats['total_logs'] }}</div>
            </div>
            <div class="stat-card">
                <div class="label">Quiz terminés</div>
                <div class="value purple">{{ $stats['total_quizzes'] }}</div>
            </div>
            <div class="stat-card">
                <div class="label">Connexions</div>
                <div class="value green">{{ $stats['total_connections'] }}</div>
            </div>
            <div class="stat-card">
                <div class="label">Échecs de connexion</div>
                <div class="value red">{{ $stats['total_failed_logins'] }}</div>
            </div>
            <div class="stat-card">
                <div class="label">Score quiz moyen</div>
                <div class="value {{ $avgQuiz && $avgQuiz >= 80 ? 'green' : ($avgQuiz && $avgQuiz >= 50 ? '' : 'red') }}">
                    {{ $avgQuiz ? number_format($avgQuiz, 1) : 'N/A' }}%
                </div>
                <div style="font-size:14px;color:#8b949e;margin-top:2px">
                    {{ $avgScore20 !== null ? number_format($avgScore20, 1) : 'N/A' }} / 20
                </div>
            </div>
        </div>

        <div class="charts">
            <div class="chart-card">
                <h3>Priorité</h3>
                @php
                    $maxPriority = $priorityDistribution->max('count') ?: 1;
                    $priorityOrder = ['warning', 'notice', 'info'];
                    $sortedPriorities = $priorityDistribution->sortBy(function ($pd) use ($priorityOrder) {
                        $pos = array_search($pd->priority, $priorityOrder);
                        return $pos !== false ? $pos : 999;
                    });
                @endphp
                @foreach ($sortedPriorities as $pd)
                    <div class="bar-row">
                        <span class="label">{{ $pd->priority }}</span>
                        <div class="bar-track">
                            <div class="bar-fill {{ $pd->priority }}" style="width: {{ ($pd->count / $maxPriority) * 100 }}%"></div>
                        </div>
                        <span class="count">{{ $pd->count }}</span>
                    </div>
                @endforeach
            </div>
            <div class="chart-card">
                <h3>Type</h3>
                @php $maxType = $typeDistribution->max('count') ?: 1; @endphp
                @foreach ($typeDistribution as $td)
                    <div class="bar-row">
                        <span class="label">{{ $td->type }}</span>
                        <div class="bar-track">
                            <div class="bar-fill {{ $td->type }}" style="width: {{ ($td->count / $maxType) * 100 }}%"></div>
                        </div>
                        <span class="count">{{ $td->count }}</span>
                    </div>
                @endforeach
            </div>
            <div class="chart-card">
                <h3>Logs / jour (7 jours)</h3>
                @if ($logsPerDay->isNotEmpty())
                    @php $maxDay = $logsPerDay->max('count') ?: 1; @endphp
                    <div class="daily-chart">
                        @foreach ($logsPerDay as $day)
                            <div class="col">
                                <div class="bar" style="height: {{ max(2, ($day->count / $maxDay) * 60) }}px"></div>
                                <span class="lbl">{{ \Carbon\Carbon::parse($day->date)->format('d/m') }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p style="color:#6e7681;font-size:13px;text-align:center;padding:20px 0">Aucune donnée</p>
                @endif
            </div>
        </div>

        <div class="filters">
            <a href="{{ route('event') }}" class="filter-btn {{ !request('type') && !request('priority') ? 'active' : '' }}">Tous</a>
            @foreach ($types as $t)
                <a href="?type={{ $t }}" class="filter-btn {{ request('type') === $t ? 'active' : '' }}">{{ ucfirst($t) }}</a>
            @endforeach
        </div>

        <div class="logs" id="logs-list">
            @forelse ($logs as $log)
                <div class="log-entry">
                    <div class="log-icon {{ $log->type }}">
                        @if ($log->type === 'quiz') ?
                        @elseif ($log->type === 'question') Q
                        @elseif ($log->type === 'auth' && str_contains($log->message, 'réussie')) ✓
                        @elseif ($log->type === 'auth' && str_contains($log->message, 'échouée')) ✗
                        @elseif ($log->type === 'auth') ←
                        @else @
                        @endif
                    </div>
                    <div class="log-body">
                        <div class="log-header">
                            <span class="log-action {{ $log->type }}">
                                {{ $log->type === 'quiz' ? 'Quiz soumis' : ($log->type === 'question' ? 'Question' : ($log->type === 'auth' ? 'Authentification' : ($log->type === 'manual' ? 'Manuel' : 'Système'))) }}
                            </span>
                            <span class="log-time">{{ $log->created_at->format('d/m/Y H:i:s') }}</span>
                        </div>
                        <div class="log-message">{{ $log->message }}</div>
                        <div class="log-meta">
                            <span class="log-severity {{ $log->priority }}">{{ $log->priority }}</span>
                            <span>{{ $log->facility }}</span>
                            @if ($log->user_id)
                                <span>Utilisateur #{{ $log->user_id }}</span>
                            @endif
                            @if ($log->score !== null)
                                <span>Score : {{ $log->score }}/{{ $log->total }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <p>Aucun log pour le moment.</p>
                </div>
            @endforelse
        </div>

        <div class="pagination">
            @if ($logs->hasPages())
                <div class="pagination-inner">
                    <a href="{{ $logs->previousPageUrl() }}" class="page-link {{ $logs->onFirstPage() ? 'disabled' : '' }}">&larr; Précédente</a>
                    <span class="page-info">Page {{ $logs->currentPage() }} / {{ $logs->lastPage() }}</span>
                    <a href="{{ $logs->nextPageUrl() }}" class="page-link {{ $logs->hasMorePages() ? '' : 'disabled' }}">Suivante &rarr;</a>
                </div>
            @endif
        </div>

        <div class="footer">
            Dashboard des Logs
        </div>
    </div>

    <script>
        setInterval(function () {
            let params = new URLSearchParams(window.location.search);
            params.set('_t', Date.now());
            fetch('{{ route('event') }}?' + params.toString(), {
                headers: { 'Accept': 'text/html' }
            })
            .then(r => r.text())
            .then(html => {
                let parser = new DOMParser();
                let doc = parser.parseFromString(html, 'text/html');
                let newLogs = doc.querySelector('#logs-list');
                let currentLogs = document.querySelector('#logs-list');
                if (newLogs && currentLogs) {
                    currentLogs.innerHTML = newLogs.innerHTML;
                }
            })
            .catch(() => {});
        }, 10000);
    </script>
</body>
</html>
