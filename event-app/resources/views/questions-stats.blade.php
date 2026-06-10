<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Stats par question — Quiz</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
        header .nav-link {
            display: inline-block;
            margin-top: 10px;
            color: #818cf8;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: color .2s;
            position: relative;
        }
        header .nav-link:hover { color: #a5b4fc; }

        .stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
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
        .stat-card .value.purple { color: #a78bfa; }

        .question-card {
            background: linear-gradient(145deg, rgba(30,27,75,.5), rgba(15,23,42,.7));
            border: 1px solid rgba(99,102,241,.12);
            border-radius: 16px;
            padding: 18px 20px;
            margin-bottom: 10px;
            transition: border-color .2s;
        }
        .question-card:hover { border-color: rgba(99,102,241,.25); }
        .question-card .q-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 10px;
            flex-wrap: wrap;
        }
        .question-card .q-number {
            font-size: 11px;
            font-weight: 700;
            color: #818cf8;
            background: rgba(99,102,241,.1);
            padding: 3px 12px;
            border-radius: 20px;
            white-space: nowrap;
        }
        .question-card .q-text {
            flex: 1;
            font-size: 15px;
            font-weight: 600;
            color: #e2e8f0;
            line-height: 1.4;
        }
        .question-card .q-stats {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }
        .question-card .q-percentage {
            font-size: 24px;
            font-weight: 800;
        }
        .question-card .q-percentage.good { color: #34d399; }
        .question-card .q-percentage.medium { color: #fbbf24; }
        .question-card .q-percentage.bad { color: #fb7185; }
        .question-card .q-count {
            font-size: 12px;
            color: #64748b;
        }
        .question-card .q-bar-track {
            flex: 1;
            min-width: 100px;
            height: 8px;
            background: rgba(15,23,42,.8);
            border-radius: 6px;
            overflow: hidden;
        }
        .question-card .q-bar-fill {
            height: 100%;
            border-radius: 6px;
            transition: width .6s ease;
            background: linear-gradient(90deg, #818cf8, #6366f1);
        }
        .question-card .q-bar-fill.good { background: linear-gradient(90deg, #10b981, #34d399); }
        .question-card .q-bar-fill.medium { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
        .question-card .q-bar-fill.bad { background: linear-gradient(90deg, #e11d48, #fb7185); }

        .best-card { border-color: rgba(52,211,153,.3); }
        .best-card:hover { border-color: rgba(52,211,153,.5) !important; }
        .best-card .q-number { background: rgba(52,211,153,.15); color: #34d399; }
        .worst-card { border-color: rgba(251,113,133,.3); }
        .worst-card:hover { border-color: rgba(251,113,133,.5) !important; }
        .worst-card .q-number { background: rgba(251,113,133,.15); color: #fb7185; }

        .question-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 18px;
        }
        @media (max-width: 768px) {
            .question-grid { grid-template-columns: 1fr; }
        }

        .empty-state {
            text-align: center; padding: 60px 40px;
            background: linear-gradient(145deg, rgba(30,27,75,.4), rgba(15,23,42,.6));
            border: 1px solid rgba(99,102,241,.1);
            border-radius: 16px; color: #64748b;
        }
        .empty-state p { font-size: 15px; margin-bottom: 6px; }
        .empty-state .sub { font-size: 13px; color: #475569; }

        .footer {
            margin-top: 24px; text-align: center;
            font-size: 11px; color: #334155; letter-spacing: .5px;
        }

        @media (max-width: 768px) {
            .question-card .q-header { flex-direction: column; align-items: flex-start; }
        }
        @media (max-width: 600px) {
            .container { padding: 10px; }
            .stats { grid-template-columns: 1fr 1fr; gap: 6px; }
            .stat-card { padding: 12px; }
            .stat-card .value { font-size: 22px; }
            header { padding: 18px; }
            header h1 { font-size: 20px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Stats par question</h1>
            <p>Résultats moyens de chaque question du quiz</p>
            <a href="{{ route('event') }}" class="nav-link">&larr; Retour au dashboard des logs</a>
        </header>

        <div class="stats">
            <div class="stat-card">
                <div class="label">Questions posées</div>
                <div class="value">{{ $stats['total_questions_asked'] }}</div>
            </div>
            <div class="stat-card">
                <div class="label">Taux de bonne réponse moyen</div>
                @php
                    $overallPct = $questions ? round(array_sum(array_column($questions, 'percentage')) / count($questions), 1) : 0;
                @endphp
                <div class="value" style="color: {{ $overallPct >= 80 ? '#34d399' : ($overallPct >= 50 ? '#fbbf24' : '#fb7185') }}">
                    {{ $overallPct }}%
                </div>
            </div>
        </div>

        @forelse ($questions as $q)
            @if ($loop->first)
                <div class="question-grid">
                    @if ($best)
                        @php $level = $best['percentage'] >= 80 ? 'good' : ($best['percentage'] >= 50 ? 'medium' : 'bad'); @endphp
                        <div class="question-card best-card">
                            <div class="q-header">
                                <span class="q-number">Meilleure — Question #{{ $best['question_index'] + 1 }}</span>
                                <span class="q-text">{{ $best['question'] }}</span>
                            </div>
                            <div class="q-stats">
                                <span class="q-percentage {{ $level }}">{{ $best['percentage'] }}%</span>
                                <span class="q-count">{{ $best['correct_count'] }}/{{ $best['total_answers'] }} bonnes réponses</span>
                                <div class="q-bar-track"><div class="q-bar-fill {{ $level }}" style="width: {{ $best['percentage'] }}%"></div></div>
                            </div>
                        </div>
                    @endif
                    @if ($worst)
                        @php $level = $worst['percentage'] >= 80 ? 'good' : ($worst['percentage'] >= 50 ? 'medium' : 'bad'); @endphp
                        <div class="question-card worst-card">
                            <div class="q-header">
                                <span class="q-number">Plus mauvaise — Question #{{ $worst['question_index'] + 1 }}</span>
                                <span class="q-text">{{ $worst['question'] }}</span>
                            </div>
                            <div class="q-stats">
                                <span class="q-percentage {{ $level }}">{{ $worst['percentage'] }}%</span>
                                <span class="q-count">{{ $worst['correct_count'] }}/{{ $worst['total_answers'] }} bonnes réponses</span>
                                <div class="q-bar-track"><div class="q-bar-fill {{ $level }}" style="width: {{ $worst['percentage'] }}%"></div></div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            @php
                $pct = $q['percentage'];
                $level = $pct >= 80 ? 'good' : ($pct >= 50 ? 'medium' : 'bad');
            @endphp
            <div class="question-card">
                <div class="q-header">
                    <span class="q-number">Question #{{ $q['question_index'] + 1 }}</span>
                    <span class="q-text">{{ $q['question'] }}</span>
                </div>
                <div class="q-stats">
                    <span class="q-percentage {{ $level }}">{{ $pct }}%</span>
                    <span class="q-count">{{ $q['correct_count'] }}/{{ $q['total_answers'] }} bonnes réponses</span>
                    <div class="q-bar-track">
                        <div class="q-bar-fill {{ $level }}" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <p>Aucune donnée de quiz disponible</p>
                <p class="sub">Les résultats apparaîtront après que des quiz auront été soumis.</p>
            </div>
        @endforelse

        <div class="footer">
            Dashboard des Logs — Stats par question
        </div>
    </div>
</body>
</html>
