<!-- ============================================================
     DASHBOARD ADMINISTRATOR v2 — Datos estáticos + IA (Módulo 2.8)
     Acceso: dashboard_administrator2
============================================================ -->

<!-- ── ESTILOS EXCLUSIVOS DE ESTA VISTA ── -->
<style>
/* ------- AI Insights Widget ------- */
.ai-hero-card {
    background: linear-gradient(135deg, #0c204c 0%, #223976 50%, #274b7f 80%, #0dadd9 100%);
    border-radius: 14px;
    padding: 26px 30px;
    color: #fff;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 28px rgba(34,57,118,.35);
}
.ai-hero-card::before {
    content:'';position:absolute;top:-50px;right:-50px;
    width:220px;height:220px;border-radius:50%;
    background:rgba(47,189,224,.12);pointer-events:none;
}
.ai-hero-card h4 { color:#fff !important; font-size:1.05rem; font-weight:800; margin-bottom:4px; }
.ai-hero-card .ai-meta { font-size:.75rem; color:rgba(255,255,255,.6); }
.ai-model-tag {
    font-size:.68rem; font-weight:600; padding:2px 9px; border-radius:12px;
    background:rgba(47,189,224,.25); border:1px solid rgba(47,189,224,.45); color:#a0f0ff;
}
.ai-kpi-strip {
    background:rgba(255,255,255,.09);border:1px solid rgba(255,255,255,.14);
    border-radius:10px;padding:10px 14px;
}
.ai-kpi-strip .ak-label { font-size:.68rem; color:rgba(255,255,255,.6); margin-bottom:2px; }
.ai-kpi-strip .ak-value { font-size:1.2rem; font-weight:800; color:#fff; line-height:1.1; }
.ai-kpi-strip .ak-trend { font-size:.67rem; margin-top:3px; }
.ak-up   { color:#6ef9bc; }
.ak-down { color:#ffaaaa; }
.ak-flat { color:rgba(255,255,255,.5); }

/* Observation & alert blocks */
.obs-block { border-radius:9px; padding:12px 15px; margin-bottom:10px; border:1px solid transparent; display:flex; gap:10px; }
.obs-block:last-child { margin-bottom:0; }
.obs-info    { background:#f0f8ff; border-color:#bee3f8; }
.obs-success { background:#f0fdf4; border-color:#bbf7d0; }
.obs-warning { background:#fffbeb; border-color:#fde68a; }
.obs-danger  { background:#fff4f4; border-color:#fca5a5; }
.obs-icon { font-size:1.1rem; flex-shrink:0; margin-top:1px; }
.obs-info    .obs-icon { color:#0673b9; }
.obs-success .obs-icon { color:#1a7d43; }
.obs-warning .obs-icon { color:#d28000; }
.obs-danger  .obs-icon { color:#c0392b; }
.obs-block p { font-size:.82rem; color:#344054; margin:0; line-height:1.5; }

/* Recommendation items */
.rec-item    { display:flex; gap:12px; padding:12px 0; border-bottom:1px solid #f0f2f5; }
.rec-item:last-child { border-bottom:none; padding-bottom:0; }
.rec-num {
    width:30px;height:30px;border-radius:50%;flex-shrink:0;
    background:linear-gradient(135deg,#223976,#0dadd9);
    color:#fff;font-size:.76rem;font-weight:800;
    display:flex;align-items:center;justify-content:center;
}
.rec-content h5 { font-size:.85rem; font-weight:700; color:#1d2939 !important; margin:0 0 3px; }
.rec-content p  { font-size:.79rem; color:#667085; margin:0; line-height:1.45; }
.rec-tag { font-size:.66rem; font-weight:600; border-radius:12px; padding:2px 8px; display:inline-block; margin-top:4px; }
.rec-tag.mk { background:#f0f4ff; color:#223976; }
.rec-tag.gr { background:#e5f8fb; color:#0dadd9; }
.rec-tag.rt { background:#fffbeb; color:#d28000; }

/* Specialist activity table */
.spec-activity-table { width:100%; border-collapse:collapse; }
.spec-activity-table thead th {
    font-size:.7rem; font-weight:600; color:#667085; text-transform:uppercase;
    letter-spacing:.4px; padding:7px 10px; border-bottom:1px solid #eaecf0; background:#f9fafb;
}
.spec-activity-table tbody td { font-size:.8rem; color:#344054; padding:9px 10px; border-bottom:1px solid #f0f2f5; }
.spec-activity-table tbody tr:last-child td { border-bottom:none; }
.spec-activity-table tbody tr:hover { background:#f7f9fc; }
.act-dot  { width:7px;height:7px;border-radius:50%;display:inline-block;margin-right:4px; }
.act-high { background:#12b76a; }
.act-med  { background:#f79009; }
.act-low  { background:#f04438; }

/* metric bar rows */
.met-row { display:flex; align-items:center; gap:8px; margin-bottom:8px; }
.met-row:last-child { margin-bottom:0; }
.met-label { font-size:.77rem; color:#344054; width:140px; flex-shrink:0; }
.met-bar-wrap { flex:1; background:#f0f2f5; border-radius:5px; height:7px; overflow:hidden; }
.met-bar { height:100%; border-radius:5px; }
.met-pct { font-size:.75rem; font-weight:700; color:#344054; width:38px; text-align:right; flex-shrink:0; }

/* AI disclaimer strip */
.ai-disclaimer-strip {
    background:#f7f9fc; border:1px solid #eaecf0; border-radius:9px;
    padding:12px 16px; font-size:.76rem; color:#667085;
    display:flex; gap:9px; align-items:flex-start;
}

/* Demo banner */
.demo-banner {
    background:linear-gradient(90deg,rgba(13,173,217,.1),rgba(47,189,224,.07));
    border-left:4px solid #0dadd9; border-radius:0 8px 8px 0;
    padding:9px 16px; font-size:.81rem; color:#476588; margin-bottom:20px;
}

@keyframes geminipulse { 0%,100%{transform:scale(1);opacity:1} 50%{transform:scale(1.15);opacity:.75} }
.gemini-icon {
    display:inline-flex;align-items:center;justify-content:center;
    width:30px;height:30px;border-radius:50%;flex-shrink:0;
    background:linear-gradient(135deg,#223976,#0dadd9);
    animation:geminipulse 2.5s ease-in-out infinite;
}
.gemini-icon i { color:#fff; font-size:.85rem; }
</style>

<!-- Start Content -->
<div class="container-fluid" id="dashboard-view">
    <div class="content">
        <div class="container-fluid">

            <!-- ── PAGE TITLE ── -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box mb-2">
                        <h4 class="page-title" style="line-height:2.3">Dashboard</h4>
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                            <span class="fw-bold">Hola, Admin Rafael · <small class="text-muted fw-normal">Administrador</small></span>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge" style="background:#e5f8fb;color:#0dadd9;font-size:.72rem;padding:4px 10px">
                                    <i class="mdi mdi-star-four-points me-1"></i>Vista Demo — Datos Estáticos
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Demo notice -->
            <div class="demo-banner">
                <i class="mdi mdi-information-outline me-1"></i>
                <strong>Vista Prototipo:</strong> Este dashboard muestra datos estáticos de ejemplo que ilustran cómo se verá la integración de IA (Módulo 2.8) y las métricas actuales de la plataforma.
            </div>

            <!-- ── KPI CARDS (estáticos) ── -->
            <div class="row" id="admin-kpi-cards">
                <?php
                $kpis_static = [
                    ['kpi-total-users',       '1,284', 'account-group-outline', 'border-kpi-person',    'bg-white-light', 'text-kpi-person',    'Total Usuarios'],
                    ['kpi-total-specialists', '74',    'doctor',                'border-kpi-calendar',  'bg-white-light', 'text-kpi-calendar',  'Total Especialistas'],
                    ['kpi-standard-verif',    '312',   'shield-check-outline',  'border-kpi-view',      'bg-white-light', 'text-kpi-view',      'Verif. Standard'],
                    ['kpi-plus-verif',        '89',    'shield-star-outline',   'border-kpi-calendar',  'bg-white-light', 'text-kpi-calendar',  'Verif. Plus'],
                ];
                foreach ($kpis_static as [$id, $val, $icon, $border, $bg, $color, $label]): ?>
                <div class="col-md-6 col-xl-3">
                    <div class="widget-rounded-circle card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="avatar-lg justify-content-center align-items-center d-flex rounded-circle <?= $border ?> <?= $bg ?>">
                                        <span class="mdi mdi-<?= $icon ?> <?= $color ?>" style="font-size:24px"></span>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center flex-column col-6">
                                    <div class="text-end">
                                        <h3 class="mt-1 mb-0"><?= $val ?></h3>
                                        <p class="text-muted mb-0 text-truncate"><?= $label ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- ── CHARTS ROW: Donut + Bar ── -->
            <div class="row">
                <!-- Donut país -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mb-0">
                                <i class="mdi mdi-earth me-1 text-accent"></i>Distribución por País
                            </h4>
                            <div class="widget-chart text-center" dir="ltr">
                                <div id="donut-chart-admin2" class="mt-2"></div>
                            </div>
                            <ul class="list-unstyled mb-0 mt-1" id="country-donut-legend2" style="max-height:160px;overflow-y:auto;font-size:.82rem;">
                                <?php
                                $countries_static = [
                                    ['🇻🇪','Venezuela',39.1,  482,'#3EBBD0'],
                                    ['🇨🇴','Colombia', 25.4,  313,'#2fbde0'],
                                    ['🇲🇽','México',   15.2,  187,'#1a8ea3'],
                                    ['🇦🇷','Argentina',10.1,  124,'#0d6e80'],
                                    ['🌍','Otros',     10.2,  126,'#95a5a6'],
                                ];
                                foreach ($countries_static as [$flag,$name,$pct,$total,$color]): ?>
                                <li class="d-flex align-items-center justify-content-between py-1 border-bottom">
                                    <span>
                                        <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:<?= $color ?>;margin-right:5px"></span>
                                        <?= $flag ?> <?= $name ?>
                                    </span>
                                    <span class="fw-bold"><?= $pct ?>% <small class="text-muted">(<?= $total ?>)</small></span>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Bar usuarios por país -->
                <div class="col-lg-8">
                    <div class="card pb-2">
                        <div class="card-body">
                            <h4 class="header-title m-0">
                                <i class="mdi mdi-account-group me-1 text-primary"></i>Usuarios y Especialistas por País
                            </h4>
                            <div dir="ltr">
                                <div id="barlines-chart-admin2" class="mt-4"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── TABLES ROW: Top Usuarios + Top Especialistas ── -->
            <div class="row">
                <!-- Top Usuarios -->
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mb-3">
                                <i class="mdi mdi-trophy-outline me-1 text-yellow-text"></i>Top Usuarios con más Exámenes
                            </h4>
                            <div class="table-responsive">
                                <table class="table table-borderless table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Usuario</th>
                                            <th>Email</th>
                                            <th class="text-center">Exámenes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $top_users = [
                                            ['María González', 'maria.g@email.com', 47],
                                            ['Juan Pérez',     'juan.p@email.com',  39],
                                            ['Ana Rodríguez',  'ana.r@email.com',   35],
                                            ['Carlos López',   'carlos.l@email.com',31],
                                            ['Laura Martínez', 'laura.m@email.com', 28],
                                        ];
                                        $badges = ['bg-primary-app','bg-accent','bg-electric-blue','bg-sapphire-blue','bg-sapphire-blue'];
                                        foreach ($top_users as $i => [$name,$email,$exams]): ?>
                                        <tr>
                                            <td><span class="badge <?= $badges[$i] ?> text-white"><?= $i+1 ?></span></td>
                                            <td class="fw-semibold"><?= $name ?></td>
                                            <td class="text-muted small"><?= $email ?></td>
                                            <td class="text-center fw-bold"><?= $exams ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Especialistas -->
                <div class="col-xl-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mb-3">
                                <i class="mdi mdi-star-circle-outline me-1 text-accent"></i>Top Especialistas con más Consultas
                            </h4>
                            <div class="table-responsive">
                                <table class="table table-borderless table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Especialista</th>
                                            <th>Título</th>
                                            <th class="text-center">Consultas</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $top_specs = [
                                            ['Dra. Ana Rodríguez',  'Cardióloga',        28],
                                            ['Dr. Luis Perez',      'Nutricionista',     24],
                                            ['Dra. María López',    'Endocrinóloga',     21],
                                            ['Dr. Carlos Ruiz',     'Internista',        17],
                                            ['Dra. Sofía Herrera',  'Nefróloga',         11],
                                        ];
                                        foreach ($top_specs as $i => [$name,$title,$cons]): ?>
                                        <tr>
                                            <td><span class="badge <?= $badges[$i] ?> text-white"><?= $i+1 ?></span></td>
                                            <td class="fw-semibold"><?= $name ?></td>
                                            <td class="text-muted small"><?= $title ?></td>
                                            <td class="text-center fw-bold"><?= $cons ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ════════════════════════════════════════════════════
                 ★  SECCIÓN NUEVA — IA PARA ADMINISTRADORES (2.8)  ★
            ════════════════════════════════════════════════════ -->

            <!-- Section divider -->
            <div class="d-flex align-items-center gap-3 my-4">
                <div style="flex:1;height:1px;background:linear-gradient(90deg,transparent,#d0d5dd,transparent)"></div>
                <span style="font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#98a2b3;white-space:nowrap">
                    <i class="mdi mdi-star-four-points me-1" style="color:#0dadd9"></i>
                    ★ NUEVO — IA para Administradores (Módulo 2.8)
                </span>
                <div style="flex:1;height:1px;background:linear-gradient(90deg,transparent,#d0d5dd,transparent)"></div>
            </div>

            <!-- AI HERO CARD -->
            <div class="ai-hero-card mb-4">
                <div class="d-flex flex-wrap align-items-start justify-content-between gap-3" style="position:relative;z-index:1">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <div class="gemini-icon"><i class="mdi mdi-star-four-points"></i></div>
                            <div>
                                <h4 class="mb-0">Análisis Inteligente de Plataforma</h4>
                                <div class="ai-meta mt-1">
                                    <span style="background:rgba(255,255,255,.1);border-radius:10px;padding:2px 9px">📅 Semana 24 Feb – 2 Mar, 2026</span>
                                    <span class="ai-model-tag ms-2">gemini-2.5-pro</span>
                                </div>
                            </div>
                        </div>
                        <p style="font-size:.84rem;color:rgba(255,255,255,.75);max-width:560px;line-height:1.55;margin:0">
                            Reporte semanal generado automáticamente. La IA analizó los KPIs y detectó tendencias, alertas y oportunidades de mejora.
                            Generado el <strong style="color:#7ef0ff">2 Mar 2026 · 06:00 AM</strong>.
                        </p>
                    </div>
                    <div style="font-size:.71rem;color:rgba(255,255,255,.4);text-align:right;padding-top:4px">
                        Próxima actualización:<br><strong style="color:rgba(255,255,255,.65)">Lun 9 Mar · 06:00 AM</strong>
                    </div>
                </div>

                <!-- KPI strip -->
                <div class="row g-2 mt-3" style="position:relative;z-index:1">
                    <?php
                    $ai_kpis = [
                        ['Usuarios Totales',     '1,284', '+38 esta semana',   'up'],
                        ['Especialistas',        '74',    '+3 nuevos',         'up'],
                        ['Ingresos (semana)',    '$2,610','↑ +12.4%',          'up'],
                        ['Rating promedio',      '4.7★',  'Sin cambios',       'flat'],
                        ['Usuarios Inact. 30d', '127',   '↑ 18 vs anterior',  'down'],
                        ['Consultas (semana)',   '318',   '↑ +22.3%',          'up'],
                    ];
                    foreach ($ai_kpis as [$lbl,$val,$trend,$dir]): ?>
                    <div class="col-6 col-md-4 col-lg-2">
                        <div class="ai-kpi-strip">
                            <div class="ak-label"><?= $lbl ?></div>
                            <div class="ak-value"><?= $val ?></div>
                            <div class="ak-trend ak-<?= $dir ?>"><?= $trend ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- ROW: Observaciones + Alertas -->
            <div class="row g-4 mb-4">

                <!-- 3 Observaciones -->
                <div class="col-lg-7">
                    <div class="card h-100">
                        <div class="card-body">
                            <h4 class="header-title mb-3">
                                <i class="mdi mdi-lightbulb-outline me-1" style="color:#0673b9"></i>
                                3 Observaciones Clave
                                <span class="badge ms-2" style="background:#223976;color:#fff;font-size:.68rem">IA</span>
                            </h4>

                            <div class="obs-block obs-success">
                                <div class="obs-icon"><i class="mdi mdi-trending-up"></i></div>
                                <p><strong>📈 Crecimiento sostenido de usuarios:</strong> La plataforma registró <strong>38 nuevos usuarios</strong> esta semana, un <strong>+14.3%</strong> respecto al promedio de las 4 semanas anteriores. El mayor flujo provino de Venezuela (12) y Colombia (9). Las estrategias de visibilidad están funcionando.</p>
                            </div>

                            <div class="obs-block obs-info">
                                <div class="obs-icon"><i class="mdi mdi-clipboard-text-outline"></i></div>
                                <p><strong>💼 Dominancia de Segunda Opinión:</strong> Los servicios de segunda opinión representaron el <strong>89% de las 318 consultas</strong> de la semana. La tasa de conversión (solicitudes aceptadas vs. enviadas) se mantiene en <strong>78%</strong>, indicando alta satisfacción en el flujo de solicitudes.</p>
                            </div>

                            <div class="obs-block obs-warning">
                                <div class="obs-icon"><i class="mdi mdi-account-clock-outline"></i></div>
                                <p><strong>⏳ Aumento de usuarios inactivos:</strong> Se detectaron <strong>127 usuarios sin actividad en 30 días</strong> (9.9% de la base). Subió 18 vs. la semana anterior. El mayor grupo corresponde a usuarios registrados hace 60-90 días que nunca completaron un perfil de biomarcadores.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2 Alertas -->
                <div class="col-lg-5">
                    <div class="card h-100">
                        <div class="card-body">
                            <h4 class="header-title mb-3">
                                <i class="mdi mdi-alert-outline me-1" style="color:#c0392b"></i>
                                2 Alertas Detectadas
                                <span class="badge ms-2" style="background:#fff1f1;color:#c0392b;font-size:.68rem">⚠ Atención</span>
                            </h4>

                            <div class="obs-block obs-danger">
                                <div class="obs-icon"><i class="mdi mdi-account-off-outline"></i></div>
                                <p><strong>🔴 Especialistas con baja actividad:</strong> <strong>6 especialistas</strong> no han aceptado ninguna consulta en los últimos 21 días (8.1% del total). De ellos, 4 no han completado su disponibilidad horaria. Podrían afectar la percepción de oferta de la plataforma.</p>
                            </div>

                            <div class="obs-block obs-warning">
                                <div class="obs-icon"><i class="mdi mdi-currency-usd-off"></i></div>
                                <p><strong>🟡 Pagos abandonados en checkout:</strong> El tiempo de pago subió a <strong>8.2 min</strong> (era 3.1 min). Se detectaron <strong>14 transacciones abandonadas</strong> — impacto estimado de <strong>$420</strong> en ingresos no capturados esta semana.</p>
                            </div>

                            <!-- Métricas de riesgo -->
                            <div class="mt-3 p-3" style="background:#f9fafb;border-radius:9px;border:1px solid #f0f2f5">
                                <div style="font-size:.74rem;font-weight:600;color:#344054;margin-bottom:8px">
                                    <i class="mdi mdi-chart-bar me-1 text-accent"></i>Métricas de Riesgo
                                </div>
                                <?php
                                $risk_metrics = [
                                    ['Usuarios en riesgo churn', 49, '#0dadd9'],
                                    ['Especialistas inactivos',  40, '#f04438'],
                                    ['Pagos abandonados',        22, '#f79009'],
                                    ['Consultas sin respuesta',  14, '#6c38c9'],
                                ];
                                foreach ($risk_metrics as [$lbl,$w,$color]): ?>
                                <div class="met-row">
                                    <div class="met-label"><?= $lbl ?></div>
                                    <div class="met-bar-wrap"><div class="met-bar" style="width:<?= $w ?>%;background:<?= $color ?>"></div></div>
                                    <div class="met-pct"><?= round($w/5,1) ?>%</div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ROW: Gráfico Tendencia + Tipos de Consulta -->
            <div class="row g-4 mb-4">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mb-0">
                                <i class="mdi mdi-chart-line me-1 text-accent"></i>Tendencia Semanal — Consultas vs. Usuarios Nuevos
                                <small class="text-muted fw-normal" style="font-size:.72rem"> · Últimas 8 semanas</small>
                            </h4>
                            <div id="ai-trend-chart" class="mt-2"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h4 class="header-title mb-0">
                                <i class="mdi mdi-chart-donut me-1" style="color:#6c38c9"></i>Tipos de Consulta
                                <small class="text-muted fw-normal" style="font-size:.72rem"> · Esta semana</small>
                            </h4>
                            <div id="ai-donut-chart" class="mt-2"></div>
                            <?php
                            $consult_types = [
                                ['2da Opinión Estándar', 194, '#223976'],
                                ['2da Opinión Completa', 89,  '#0dadd9'],
                                ['Solo Chat',            35,  '#2fbde0'],
                            ];
                            foreach ($consult_types as [$lbl,$val,$color]): ?>
                            <div class="d-flex align-items-center justify-content-between py-1" style="border-bottom:1px solid #f0f2f5;font-size:.79rem">
                                <span><span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:<?= $color ?>;margin-right:6px"></span><?= $lbl ?></span>
                                <span class="fw-bold"><?= $val ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ROW: Actividad Especialistas + Recomendaciones -->
            <div class="row g-4 mb-4">

                <!-- Actividad -->
                <div class="col-lg-5">
                    <div class="card h-100">
                        <div class="card-body p-0">
                            <div class="p-3 pb-1 border-bottom">
                                <h4 class="header-title mb-0">
                                    <i class="mdi mdi-doctor me-1" style="color:#1a7d43"></i>Actividad de Especialistas
                                </h4>
                                <small class="text-muted">Top 8 por consultas esta semana</small>
                            </div>
                            <div class="table-responsive">
                                <table class="spec-activity-table">
                                    <thead>
                                        <tr>
                                            <th>#</th><th>Especialista</th>
                                            <th>Consult.</th><th>Rating</th><th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $spec_activity = [
                                            [1,'Dra. Ana Rodríguez','Cardiología',28,4.9,'high'],
                                            [2,'Dr. Luis Perez','Nutrición',24,4.8,'high'],
                                            [3,'Dra. María López','Endocrinología',21,4.7,'high'],
                                            [4,'Dr. Carlos Ruiz','Med. Interna',17,4.6,'high'],
                                            [5,'Dra. Sofía Herrera','Nefrología',11,4.5,'med'],
                                            [6,'Dr. Tomás Vargas','Med. General',8,4.4,'med'],
                                            [7,'Dra. Camila Torres','Cardiología',4,4.3,'med'],
                                            [8,'Dr. Ricardo Méndez','Neurología',1,4.0,'low'],
                                        ];
                                        foreach ($spec_activity as [$i,$name,$spec,$cons,$rat,$act]): ?>
                                        <tr>
                                            <td style="color:#98a2b3;font-size:.72rem;font-weight:700"><?= $i ?></td>
                                            <td>
                                                <div style="font-size:.8rem;font-weight:600;color:#1d2939"><?= $name ?></div>
                                                <div style="font-size:.7rem;color:#98a2b3"><?= $spec ?></div>
                                            </td>
                                            <td><strong><?= $cons ?></strong></td>
                                            <td><span style="color:#f79009">★</span> <?= $rat ?></td>
                                            <td>
                                                <span class="act-dot act-<?= $act ?>"></span>
                                                <span style="font-size:.7rem;color:#667085"><?= $act==='high'?'Alta':($act==='med'?'Media':'Baja') ?></span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recomendaciones -->
                <div class="col-lg-7">
                    <div class="card h-100">
                        <div class="card-body">
                            <h4 class="header-title mb-3">
                                <i class="mdi mdi-rocket-launch-outline me-1" style="color:#d28000"></i>
                                2 Recomendaciones Accionables
                                <span class="badge ms-2" style="background:#fff8e6;color:#d28000;font-size:.68rem">Acción requerida</span>
                            </h4>

                            <div class="rec-item">
                                <div class="rec-num">1</div>
                                <div class="rec-content">
                                    <h5>Campaña de re-engagement para usuarios inactivos</h5>
                                    <p>Se detectaron <strong>127 usuarios</strong> sin actividad en 30 días. Se recomienda enviar una notificación push + email personalizado: <em>"¿Cómo va tu salud? Tienes nuevas funciones disponibles en Vitakee"</em>. Históricamente este tipo de campaña recupera entre el 22–35% de usuarios inactivos.</p>
                                    <span class="rec-tag rt">Retención</span>
                                    <span class="rec-tag mk ms-1">Marketing</span>
                                    <span style="font-size:.69rem;color:#667085;margin-left:6px">Impacto est.: +28 usuarios activos</span>
                                </div>
                            </div>

                            <div class="rec-item">
                                <div class="rec-num">2</div>
                                <div class="rec-content">
                                    <h5>Programa de activación para especialistas con baja actividad</h5>
                                    <p>Los <strong>6 especialistas inactivos</strong> poseen un potencial de ~24 consultas adicionales/semana. Se recomienda: (1) enviarles un checklist de configuración, (2) ofrecer onboarding de 15 min vía videollamada, (3) activar notificaciones de nuevas solicitudes en su área.</p>
                                    <span class="rec-tag gr">Crecimiento</span>
                                    <span style="font-size:.69rem;color:#667085;margin-left:6px">Potencial: +$480/semana</span>
                                </div>
                            </div>

                            <!-- Impact summary -->
                            <div class="p-3 mt-2" style="background:linear-gradient(135deg,#f0f8ff,#e5f8fb);border-radius:9px;border:1px solid #bee3f8">
                                <div style="font-size:.73rem;font-weight:700;color:#223976;margin-bottom:8px">
                                    <i class="mdi mdi-chart-areaspline me-1"></i>Impacto potencial (ambas recomendaciones)
                                </div>
                                <div class="row g-2 text-center">
                                    <div class="col-4">
                                        <div style="font-size:1.2rem;font-weight:800;color:#223976">+$1,200</div>
                                        <div style="font-size:.69rem;color:#667085">Ingreso estimado</div>
                                    </div>
                                    <div class="col-4">
                                        <div style="font-size:1.2rem;font-weight:800;color:#0dadd9">+52</div>
                                        <div style="font-size:.69rem;color:#667085">Usuarios reactivados</div>
                                    </div>
                                    <div class="col-4">
                                        <div style="font-size:1.2rem;font-weight:800;color:#1a7d43">+24</div>
                                        <div style="font-size:.69rem;color:#667085">Consultas adicionales</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- AI Disclaimer -->
            <div class="ai-disclaimer-strip mb-4">
                <i class="mdi mdi-shield-account-outline" style="color:#98a2b3;font-size:1rem;flex-shrink:0;margin-top:1px"></i>
                <div>
                    <strong>Aviso IA:</strong> Este análisis es generado por <strong>Google Gemini 2.5 Pro</strong> con carácter orientativo.
                    Los datos enviados a la API están anonimizados (UUIDs + métricas numéricas, sin nombres ni emails).
                    No sustituye el criterio del administrador. Solo usuarios con rol administrador tienen acceso a esta sección.
                </div>
            </div>

        </div><!-- /container-fluid -->
    </div><!-- /content -->
</div>

<!-- ── SCRIPTS (estáticos, sin fetch ni AJAX) ── -->
<script src="public/assets/js/logout.js"></script>

<script>
const language2 = 'ES';
const userId    = 'demo-admin-001';
const userRole  = 0;

// ── Donut país (C3 — biblioteca ya cargada por el layout) ──
document.addEventListener('DOMContentLoaded', function () {

    // Donut países — datos estáticos
    c3.generate({
        bindto: '#donut-chart-admin2',
        data: {
            columns: [
                ['🇻🇪 Venezuela', 482],
                ['🇨🇴 Colombia',  313],
                ['🇲🇽 México',    187],
                ['🇦🇷 Argentina', 124],
                ['🌍 Otros',      126],
            ],
            type: 'donut',
            colors: {
                '🇻🇪 Venezuela': '#3EBBD0',
                '🇨🇴 Colombia':  '#2fbde0',
                '🇲🇽 México':    '#1a8ea3',
                '🇦🇷 Argentina': '#0d6e80',
                '🌍 Otros':      '#95a5a6',
            },
        },
        donut: { title:'1,232 total', width:22, label:{ show:false } },
        size:  { height: 220 },
    });

    // Bar usuarios por país — datos estáticos
    new ApexCharts(document.querySelector('#barlines-chart-admin2'), {
        series: [{ name:'Usuarios', data:[420,285,168,112,105] }],
        chart:  { type:'bar', height:340, toolbar:{ show:false } },
        colors: ['#3EBBD0'],
        plotOptions: { bar:{ borderRadius:3, columnWidth:'55%' } },
        dataLabels: { enabled:false },
        xaxis: { categories:['🇻🇪 Venezuela','🇨🇴 Colombia','🇲🇽 México','🇦🇷 Argentina','🌍 Otros'], labels:{ rotate:-15, style:{ fontSize:'11px' } } },
        yaxis: { labels:{ formatter: v => Math.round(v) } },
        grid:  { borderColor:'#f0f2f5', strokeDashArray:4 },
        tooltip: { y:{ formatter: v => `${v} personas` } },
    }).render();

    // AI Trend Chart — datos estáticos
    new ApexCharts(document.querySelector('#ai-trend-chart'), {
        chart: { type:'line', height:220, toolbar:{ show:false }, fontFamily:'inherit' },
        series: [
            { name:'Consultas',       data:[198,221,210,245,262,289,305,318] },
            { name:'Usuarios Nuevos', data:[24, 29, 27, 31, 35, 33, 36, 38]  },
        ],
        xaxis:  { categories:['7 Ene','14 Ene','21 Ene','28 Ene','4 Feb','11 Feb','18 Feb','25 Feb'], labels:{ style:{ fontSize:'11px' } } },
        yaxis: [
            { labels:{ style:{ fontSize:'11px' } } },
            { opposite:true, labels:{ style:{ fontSize:'11px' } } },
        ],
        colors: ['#223976','#0dadd9'],
        stroke: { curve:'smooth', width:[3,2.5] },
        markers:{ size:[4,4] },
        legend: { position:'top', horizontalAlign:'right', fontSize:'12px' },
        grid:   { borderColor:'#f0f2f5', strokeDashArray:4 },
        tooltip:{ shared:true, intersect:false },
    }).render();

    // AI Donut tipos consulta — datos estáticos
    new ApexCharts(document.querySelector('#ai-donut-chart'), {
        chart:  { type:'donut', height:200, fontFamily:'inherit' },
        series: [194, 89, 35],
        labels: ['2da Opinión Estándar','2da Opinión Completa','Solo Chat'],
        colors: ['#223976','#0dadd9','#2fbde0'],
        legend: { show:false },
        plotOptions: { pie:{ donut:{ size:'65%', labels:{ show:true, total:{ show:true, label:'Total', formatter:()=>'318' } } } } },
        dataLabels: { enabled:false },
    }).render();

});
</script>
