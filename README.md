<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TEMPQUEST</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
     <!-- Icono -->
    <link rel="icon" type="image/png" href="mini logo.png">
    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        darkBg: '#050811',
                        cardBg: 'rgba(15, 23, 42, 0.75)',
                        cardBorder: 'rgba(56, 189, 248, 0.2)',
                        neonCyan: '#00f3ff',
                        neonGreen: '#10b981',
                        neonYellow: '#f59e0b',
                        neonRed: '#ef4444',
                        neonPurple: '#8b5cf6'
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #050811;
            color: #f3f4f6;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-image: 
                radial-gradient(circle at 50% 0%, rgba(56, 189, 248, 0.08) 0%, transparent 75%),
                linear-gradient(rgba(255, 255, 255, 0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
            background-size: 100% 100%, 30px 30px, 30px 30px;
        }

   .glass-panel {
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(56, 189, 248, 0.15);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
        }

  .glass-card {
            background: rgba(30, 41, 59, 0.5);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

   .pulse-online {
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            animation: pulse-green 2s infinite;
        }
     .pulse-offline {
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
            animation: pulse-red 1.5s infinite;
        }
    @keyframes pulse-green {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

  @keyframes pulse-red {
            0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
            100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }

 ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: rgba(15, 23, 42, 0.6); }
        ::-webkit-scrollbar-thumb { background: rgba(56, 189, 248, 0.3); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(0, 243, 255, 0.6); }
    </style>
</head>
<body class="min-h-screen text-slate-200 antialiased flex flex-col">
  <!-- TOP HEADER -->
    <header class="glass-panel sticky top-0 z-40 px-6 py-3 border-b border-cyan-500/20 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-cyan-500/10 rounded-lg border border-cyan-500/30">
                <i class="fa-solid fa-server text-2xl text-cyan-400"></i>
            </div>
            <div>
                <h1 class="font-bold text-lg tracking-wider text-white flex items-center gap-2">
                    TEMPQUEST <span class="text-xs px-2 py-0.5 rounded bg-cyan-500/20 text-cyan-300 font-mono border border-cyan-500/40">v4.4-LAB</span>
                </h1>
                <p class="text-xs text-slate-400">Infraestructura Proxmox VE, Red & Telemetría IoT</p>
            </div>
        </div>
       <div class="flex items-center gap-3">
            <button id="mode-toggle-btn" onclick="toggleMode()" class="px-3 py-1.5 rounded-lg border text-xs font-mono font-semibold transition flex items-center gap-2 bg-emerald-500/10 border-emerald-500/40 text-emerald-400 hover:bg-emerald-500/20">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse" id="mode-indicator-dot"></span>
                <span id="mode-text">[ MODO: OPERACIÓN REAL ]</span>
            </button>
            <button onclick="triggerSimulatedFault()" class="px-3 py-1.5 rounded-lg bg-red-500/10 hover:bg-red-500/20 border border-red-500/40 text-red-400 text-xs font-mono font-semibold transition flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation animate-bounce"></i> Simular Falla
            </button>
        </div>
    </header>
 <!-- LAB CONTROL PANEL -->
    <div id="lab-control-panel" class="hidden glass-panel border-b border-cyan-500/40 px-6 py-3 bg-slate-900/90 text-xs transition-all">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-2 font-mono text-cyan-400 font-bold">
                <i class="fa-solid fa-flask-vial"></i> CONTROLES DE SIMULACIÓN DE LABORATORIO:
            </div>
            <div class="flex flex-wrap items-center gap-6">
                <div class="flex items-center gap-2">
                    <label class="text-slate-300 font-mono">Temp Rack (°C):</label>
                    <input type="range" id="sim-temp-range" min="15" max="45" value="22" oninput="updateLabSimulation()" class="accent-cyan-400 cursor-pointer">
                    <span id="sim-temp-val" class="font-mono text-cyan-400 font-bold w-8">22°C</span>
                </div>
                <div class="flex items-center gap-2">
                    <label class="text-slate-300 font-mono">Carga CPU Cluster (%):</label>
                    <input type="range" id="sim-cpu-range" min="5" max="100" value="38" oninput="updateLabSimulation()" class="accent-purple-400 cursor-pointer">
                    <span id="sim-cpu-val" class="font-mono text-purple-400 font-bold w-8">38%</span>
                </div>
                <button onclick="resetLabSimulation()" class="px-2 py-1 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 font-mono text-xs border border-slate-600">
                    <i class="fa-solid fa-rotate-left"></i> Restablecer
                </button>
            </div>
        </div>
    </div>
  <!-- MAIN DASHBOARD CONTENT -->
    <div class="flex-1 flex flex-col xl:flex-row p-4 md:p-6 gap-6">
  <!-- SIDEBAR -->
        <aside class="w-full xl:w-80 flex flex-col gap-4 shrink-0">
            <div class="glass-panel rounded-xl p-4">
                <h2 class="text-sm font-semibold text-slate-300 mb-3 uppercase tracking-wider flex items-center justify-between">
                    <span>Estado del Nodo NOC</span>
                    <i class="fa-solid fa-signal text-cyan-400"></i>
                </h2>
                <div class="space-y-2">
                    <div class="flex justify-between items-center p-2 rounded bg-slate-800/50">
                        <span class="text-xs text-slate-400">Uptime General</span>
                        <span class="font-mono text-xs text-cyan-400 font-semibold">48d 14h 22m</span>
                    </div>
                    <div class="flex justify-between items-center p-2 rounded bg-slate-800/50">
                        <span class="text-xs text-slate-400">Clúster PVE</span>
                        <span class="font-mono text-xs text-emerald-400 font-semibold" id="cluster-status-badge">4/4 Online</span>
                    </div>
                    <div class="flex justify-between items-center p-2 rounded bg-slate-800/50">
                        <span class="text-xs text-slate-400">Latencia Gateway</span>
                        <span class="font-mono text-xs text-cyan-400 font-semibold" id="gateway-latency">1.2 ms</span>
                    </div>
                </div>
            </div>

  <div class="glass-panel rounded-xl p-4">
                <h2 class="text-sm font-semibold text-slate-300 mb-3 uppercase tracking-wider flex items-center justify-between">
                    <span>Protocolos Rápidos (SOP)</span>
                    <i class="fa-solid fa-list-check text-indigo-400"></i>
                </h2>
                <div class="space-y-2 text-xs">
                    <button onclick="openSOPModal('high-temp')" class="w-full text-left p-2 rounded bg-slate-800/40 hover:bg-slate-800 border border-slate-700/50 flex items-center justify-between group">
                        <span class="group-hover:text-cyan-300"><i class="fa-solid fa-temperature-arrow-up text-amber-400 mr-1.5"></i> Temp Alta en Rack</span>
                        <i class="fa-solid fa-chevron-right text-slate-500 group-hover:text-cyan-400"></i>
                    </button>
                    <button onclick="openSOPModal('pve-node-offline')" class="w-full text-left p-2 rounded bg-slate-800/40 hover:bg-slate-800 border border-slate-700/50 flex items-center justify-between group">
                        <span class="group-hover:text-cyan-300"><i class="fa-solid fa-server text-red-400 mr-1.5"></i> Nodo PVE Desconectado</span>
                        <i class="fa-solid fa-chevron-right text-slate-500 group-hover:text-cyan-400"></i>
                    </button>
                </div>
            </div>
        </aside>
 <!-- MAIN CONTENT AREA -->
        <main class="flex-1 flex flex-col gap-6 overflow-x-hidden">
  <!-- SECTION 1: PROXMOX VE CLUSTER -->
            <section class="glass-panel rounded-2xl p-5 border border-cyan-500/20">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-base font-bold text-white tracking-wide flex items-center gap-2">
                        <i class="fa-solid fa-cubes text-cyan-400"></i> Racks & Proxmox VE Cluster (4 Nodos PVE)
                    </h2>
                    <span class="text-xs text-slate-400 font-mono">Cluster HA: <span class="text-emerald-400">QUORUM OK</span></span>
                </div>

   <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div id="card-pve-01" class="glass-card rounded-xl p-4 relative border border-slate-700/60 transition duration-300 hover:border-cyan-500/40">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="font-bold text-sm text-white font-mono">pve-01</h3>
                                <p class="text-[10px] text-slate-400">Dell PowerEdge R740</p>
                            </div>
                            <span id="badge-pve-01" class="w-3 h-3 rounded-full bg-emerald-500 pulse-online"></span>
                        </div>
                        <div class="space-y-1.5 text-xs font-mono my-3">
                            <div class="flex justify-between text-slate-300"><span>CPU:</span><span id="pve1-cpu-val" class="text-purple-300">28%</span></div>
                            <div class="flex justify-between text-slate-300"><span>RAM:</span><span id="pve1-ram-val" class="text-amber-300">42%</span></div>
                        </div>
                        <button onclick="openSOPModal('pve-01')" class="w-full mt-2 py-1 px-2 rounded bg-slate-800 hover:bg-cyan-500/20 text-cyan-300 text-[11px] font-mono border border-cyan-500/30 transition">Inspeccionar Nodo</button>
                    </div>
    <div id="card-pve-02" class="glass-card rounded-xl p-4 relative border border-slate-700/60 transition duration-300 hover:border-cyan-500/40">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="font-bold text-sm text-white font-mono">pve-02</h3>
                                <p class="text-[10px] text-slate-400">HP ProLiant DL380</p>
                            </div>
                            <span id="badge-pve-02" class="w-3 h-3 rounded-full bg-emerald-500 pulse-online"></span>
                        </div>
                        <div class="space-y-1.5 text-xs font-mono my-3">
                            <div class="flex justify-between text-slate-300"><span>CPU:</span><span id="pve2-cpu-val" class="text-purple-300">45%</span></div>
                            <div class="flex justify-between text-slate-300"><span>RAM:</span><span id="pve2-ram-val" class="text-amber-300">58%</span></div>
                        </div>
                        <button onclick="openSOPModal('pve-02')" class="w-full mt-2 py-1 px-2 rounded bg-slate-800 hover:bg-cyan-500/20 text-cyan-300 text-[11px] font-mono border border-cyan-500/30 transition">Inspeccionar Nodo</button>
                    </div>

   <div id="card-pve-03" class="glass-card rounded-xl p-4 relative border border-slate-700/60 transition duration-300 hover:border-cyan-500/40">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="font-bold text-sm text-white font-mono">pve-03</h3>
                                <p class="text-[10px] text-slate-400">Custom Storage Node</p>
                            </div>
                            <span id="badge-pve-03" class="w-3 h-3 rounded-full bg-emerald-500 pulse-online"></span>
                        </div>
                        <div class="space-y-1.5 text-xs font-mono my-3">
                            <div class="flex justify-between text-slate-300"><span>CPU:</span><span id="pve3-cpu-val" class="text-purple-300">32%</span></div>
                            <div class="flex justify-between text-slate-300"><span>RAM:</span><span id="pve3-ram-val" class="text-amber-300">38%</span></div>
                        </div>
                        <button onclick="openSOPModal('pve-03')" class="w-full mt-2 py-1 px-2 rounded bg-slate-800 hover:bg-cyan-500/20 text-cyan-300 text-[11px] font-mono border border-cyan-500/30 transition">Inspeccionar Nodo</button>
                    </div>
     <div id="card-pve-04" class="glass-card rounded-xl p-4 relative border border-slate-700/60 transition duration-300 hover:border-cyan-500/40">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="font-bold text-sm text-white font-mono">pve-04</h3>
                                <p class="text-[10px] text-slate-400">Dell PowerEdge R640</p>
                            </div>
                            <span id="badge-pve-04" class="w-3 h-3 rounded-full bg-emerald-500 pulse-online"></span>
                        </div>
                        <div class="space-y-1.5 text-xs font-mono my-3">
                            <div class="flex justify-between text-slate-300"><span>CPU:</span><span id="pve4-cpu-val" class="text-purple-300">19%</span></div>
                            <div class="flex justify-between text-slate-300"><span>RAM:</span><span id="pve4-ram-val" class="text-amber-300">29%</span></div>
                        </div>
                        <button onclick="openSOPModal('pve-04')" class="w-full mt-2 py-1 px-2 rounded bg-slate-800 hover:bg-cyan-500/20 text-cyan-300 text-[11px] font-mono border border-cyan-500/30 transition">Inspeccionar Nodo</button>
                    </div>
                </div>
            </section>
<!-- SECTION 2: TOPOLOGÍA DE RED AMPLIADA Y VISUALMENTE MÁS CLARA -->
            <section class="glass-panel rounded-2xl p-5 border border-cyan-500/20 flex flex-col">
                <div class="flex flex-wrap items-center justify-between mb-4 gap-2">
                    <h2 class="text-base font-bold text-white tracking-wide flex items-center gap-2">
                        <i class="fa-solid fa-diagram-project text-cyan-400"></i> Topología de Red & Nodos Conectados (Esquema Ampliado)
                    </h2>
                </div>

   <div class="w-full bg-slate-950/80 rounded-xl p-6 border border-slate-800 relative overflow-hidden flex justify-center items-center">
                    <svg id="topology-svg" viewBox="0 0 900 380" class="w-full h-auto max-h-[440px]">
                        <defs>
                            <linearGradient id="grad-green" x1="0%" y1="0%" x2="100%" y2="0%">
                                <stop offset="0%" stop-color="#10b981" stop-opacity="0.9"/>
                                <stop offset="100%" stop-color="#00f3ff" stop-opacity="0.9"/>
                            </linearGradient>
                            <filter id="glow" x="-20%" y="-20%" width="140%" height="140%">
                                <feGaussianBlur stdDeviation="4" result="blur" />
                                <feComposite in="SourceGraphic" in2="blur" operator="over"/>
                            </filter>
                        </defs>
                        <!-- Líneas de Conexión más amplias y claras -->
                        <g stroke-width="2.5">
                            <line x1="450" y1="55" x2="250" y2="160" stroke="url(#grad-green)" stroke-dasharray="6,4"/>
                            <line x1="450" y1="55" x2="650" y2="160" stroke="url(#grad-green)" stroke-dasharray="6,4"/>
                            <line x1="250" y1="160" x2="130" y2="290" stroke="#00f3ff" stroke-opacity="0.6"/>
                            <line x1="250" y1="160" x2="310" y2="290" stroke="#00f3ff" stroke-opacity="0.6"/>
                            <line x1="650" y1="160" x2="590" y2="290" stroke="#00f3ff" stroke-opacity="0.6"/>
                            <line x1="650" y1="160" x2="770" y2="290" stroke="#00f3ff" stroke-opacity="0.6"/>
                            <line x1="250" y1="160" x2="450" y2="290" stroke="#f59e0b" stroke-dasharray="4,4"/>
                        </g>
    <!-- Core Router -->
                        <g class="cursor-pointer" onclick="openSOPModal('core-router')" transform="translate(450, 55)">
                            <circle r="32" fill="#0f172a" stroke="#00f3ff" stroke-width="2.5" filter="url(#glow)"/>
                            <text text-anchor="middle" dy="-38" fill="#38bdf8" font-size="12" font-weight="bold" font-family="monospace">CORE-ROUTER-01</text>
                            <text text-anchor="middle" dy="6" fill="#00f3ff" font-size="18"><tspan font-family="FontAwesome">&#xf6ff;</tspan></text>
                        </g>
    <!-- Switch Alpha -->
                        <g class="cursor-pointer" onclick="openSOPModal('switch-alpha')" transform="translate(250, 160)">
                            <rect x="-32" y="-22" width="64" height="44" rx="8" fill="#0f172a" stroke="#10b981" stroke-width="2.5"/>
                            <text text-anchor="middle" dy="-28" fill="#34d399" font-size="11" font-weight="bold" font-family="monospace">SW-ALPHA (SFP+)</text>
                            <text text-anchor="middle" dy="5" fill="#10b981" font-size="16"><tspan font-family="FontAwesome">&#xf6ff;</tspan></text>
                        </g>
     <!-- Switch Beta -->
                        <g class="cursor-pointer" onclick="openSOPModal('switch-beta')" transform="translate(650, 160)">
                            <rect x="-32" y="-22" width="64" height="44" rx="8" fill="#0f172a" stroke="#10b981" stroke-width="2.5"/>
                            <text text-anchor="middle" dy="-28" fill="#34d399" font-size="11" font-weight="bold" font-family="monospace">SW-BETA (SFP+)</text>
                            <text text-anchor="middle" dy="5" fill="#10b981" font-size="16"><tspan font-family="FontAwesome">&#xf6ff;</tspan></text>
                        </g>
   <!-- PVE End Nodes -->
                        <g transform="translate(130, 290)" class="cursor-pointer" onclick="openSOPModal('pve-01')">
                            <circle r="20" fill="#0f172a" stroke="#38bdf8" stroke-width="2"/>
                            <text text-anchor="middle" dy="28" fill="#cbd5e1" font-size="10" font-weight="bold" font-family="monospace">PVE-01</text>
                            <text text-anchor="middle" dy="5" fill="#38bdf8" font-size="12"><tspan font-family="FontAwesome">&#xf233;</tspan></text>
                        </g>
                        <g transform="translate(310, 290)" class="cursor-pointer" onclick="openSOPModal('pve-02')">
                            <circle r="20" fill="#0f172a" stroke="#38bdf8" stroke-width="2"/>
                            <text text-anchor="middle" dy="28" fill="#cbd5e1" font-size="10" font-weight="bold" font-family="monospace">PVE-02</text>
                            <text text-anchor="middle" dy="5" fill="#38bdf8" font-size="12"><tspan font-family="FontAwesome">&#xf233;</tspan></text>
                        </g>
                        <g transform="translate(590, 290)" class="cursor-pointer" onclick="openSOPModal('pve-03')">
                            <circle r="20" fill="#0f172a" stroke="#38bdf8" stroke-width="2"/>
                            <text text-anchor="middle" dy="28" fill="#cbd5e1" font-size="10" font-weight="bold" font-family="monospace">PVE-03</text>
                            <text text-anchor="middle" dy="5" fill="#38bdf8" font-size="12"><tspan font-family="FontAwesome">&#xf233;</tspan></text>
                        </g>
                        <g transform="translate(770, 290)" class="cursor-pointer" onclick="openSOPModal('pve-04')">
                            <circle r="20" fill="#0f172a" stroke="#38bdf8" stroke-width="2"/>
                            <text text-anchor="middle" dy="28" fill="#cbd5e1" font-size="10" font-weight="bold" font-family="monospace">PVE-04</text>
                            <text text-anchor="middle" dy="5" fill="#38bdf8" font-size="12"><tspan font-family="FontAwesome">&#xf233;</tspan></text>
                        </g>
 <!-- ESP8266 IoT Sensor Node -->
                        <g transform="translate(450, 290)" class="cursor-pointer" onclick="openSOPModal('esp8266-sensor')">
                            <circle r="22" fill="#0f172a" stroke="#f59e0b" stroke-width="2.5" filter="url(#glow)"/>
                            <text text-anchor="middle" dy="32" fill="#fbbf24" font-size="10" font-weight="bold" font-family="monospace">ESP8266-IoT</text>
                            <text text-anchor="middle" dy="5" fill="#f59e0b" font-size="13"><tspan font-family="FontAwesome">&#xf2c7;</tspan></text>
                        </g>
                    </svg>
                </div>
            </section>
   <!-- SECTION 3: TELEMETRÍA ESP8266 (ÚLTIMA MEDICIÓN + BOTONES AMPLIAR Y HISTORIAL) -->
            <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">

  <div class="lg:col-span-2 glass-panel rounded-2xl p-5 border border-cyan-500/20 flex flex-col justify-between">
                    <div>
                        <div class="flex flex-wrap justify-between items-center mb-3 gap-2">
                            <h2 class="text-base font-bold text-white tracking-wide flex items-center gap-2">
                                <i class="fa-solid fa-temperature-full text-amber-400"></i> Telemetría Ambiental ESP8266 + DHT22
                            </h2>
                            <div class="flex items-center gap-2">
                                <button onclick="openChartModal()" class="px-3 py-1 rounded bg-cyan-500/20 hover:bg-cyan-500/30 text-cyan-300 border border-cyan-500/40 text-xs font-mono transition flex items-center gap-1.5">
                                    <i class="fa-solid fa-chart-line"></i> Ver Gráfico de Tendencia
                                </button>
                                <button onclick="openHistoryModal()" class="px-3 py-1 rounded bg-indigo-500/20 hover:bg-indigo-500/30 text-indigo-300 border border-indigo-500/40 text-xs font-mono transition flex items-center gap-1.5">
                                    <i class="fa-solid fa-clock-rotate-left"></i> Historial
                                </button>
                            </div>
                        </div>
  <!-- ÚLTIMA MEDICIÓN RECIBIDA -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
                            <div class="glass-card p-3 rounded-xl border border-cyan-500/30 flex items-center gap-3">
                                <div class="p-2.5 bg-cyan-500/10 rounded-lg text-cyan-400"><i class="fa-solid fa-clock text-lg"></i></div>
                                <div>
                                    <div class="text-[10px] uppercase font-mono text-slate-400">Hora Última Medición</div>
                                    <div id="last-read-time" class="font-mono text-base font-bold text-cyan-300">--:--:--</div>
                                </div>
                            </div>
                            <div class="glass-card p-3 rounded-xl border border-amber-500/30 flex items-center gap-3">
                                <div class="p-2.5 bg-amber-500/10 rounded-lg text-amber-400"><i class="fa-solid fa-temperature-high text-lg"></i></div>
                                <div>
                                    <div class="text-[10px] uppercase font-mono text-slate-400">Temperatura Actual</div>
                                    <div id="last-read-temp" class="font-mono text-base font-bold text-amber-300">22.0 °C</div>
                                </div>
                            </div>
                            <div class="glass-card p-3 rounded-xl border border-emerald-500/30 flex items-center gap-3">
                                <div class="p-2.5 bg-emerald-500/10 rounded-lg text-emerald-400"><i class="fa-solid fa-droplet text-lg"></i></div>
                                <div>
                                    <div class="text-[10px] uppercase font-mono text-slate-400">Humedad Relativa</div>
                                    <div id="last-read-hum" class="font-mono text-base font-bold text-emerald-300">48.0 %</div>
                                </div>
                            </div>
                        </div>
                    </div>
 <!-- ASHRAE Indicator -->
                    <div class="pt-3 border-t border-slate-800">
                        <div class="flex justify-between items-center text-xs mb-1.5">
                            <span class="font-bold text-slate-300">Estándar Térmico ASHRAE Datacenter</span>
                            <span id="ashrae-status" class="font-mono text-emerald-400 font-semibold">ZONA ÓPTIMA (22°C)</span>
                        </div>
      <div class="relative w-full h-3 rounded-full bg-slate-800 overflow-hidden mb-2">
                            <div class="absolute inset-0 flex">
                                <div class="w-[30%] bg-blue-500/40 border-r border-slate-900"></div>
                                <div class="w-[35%] bg-emerald-500/60 border-r border-slate-900"></div>
                                <div class="w-[15%] bg-amber-500/60 border-r border-slate-900"></div>
                                <div class="w-[20%] bg-red-500/80"></div>
                            </div>
                            <div id="ashrae-pointer" class="absolute top-0 bottom-0 w-1 bg-white shadow-[0_0_8px_#ffffff] transition-all duration-500" style="left: 42%;"></div>
                        </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-[11px] text-slate-400 bg-slate-900/60 p-2.5 rounded-lg border border-slate-800">
                            <div>
                                <span class="text-cyan-400 font-semibold">Punto de Rocío:</span>
                                <span id="dew-point-val" class="font-mono text-white">11.4 °C</span>
                            </div>
                            <div class="text-slate-300">
                                <i class="fa-solid fa-shield-halved text-emerald-400 mr-1"></i> Humedad Relativa: <span id="env-hum-val" class="font-mono text-cyan-300">48%</span>
                            </div>
                        </div>
                    </div>
                </div>
  <!-- MySQL Log Container -->
                <div class="glass-panel rounded-2xl p-5 border border-cyan-500/20 flex flex-col">
                    <div class="flex justify-between items-center mb-3">
                        <h2 class="text-xs font-bold text-slate-300 tracking-wider uppercase flex items-center gap-2">
                            <i class="fa-solid fa-database text-cyan-400"></i> Logs MySQL (<code class="text-cyan-300">dht22_logs</code>)
                        </h2>
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    </div>

   <div id="sql-logs-container" class="flex-1 bg-slate-950 font-mono text-[10px] p-3 rounded-xl border border-slate-800 overflow-y-auto max-h-80 space-y-2 text-slate-300 leading-tight"></div>
                </div>
            </section>
        </main>
    </div>
 <!-- MODAL GRÁFICO AMPLIADO -->
    <div id="chart-modal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="glass-panel w-full max-w-4xl rounded-2xl border border-cyan-500/40 p-6 space-y-4 shadow-2xl relative flex flex-col max-h-[85vh]">
            <div class="flex items-center justify-between border-b border-slate-700/80 pb-3 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 rounded-lg bg-cyan-500/20 text-cyan-400 border border-cyan-500/30">
                        <i class="fa-solid fa-chart-line text-xl"></i>
                    </div>
                    <div>
                    <h3 class="text-base font-bold text-white font-mono">Gráfico de Tendencia Histórica - Temperatura</h3>
                        <p class="text-xs text-slate-400">Monitoreo ampliado en tiempo real del sensor ESP8266 + DHT22</p>
                    </div>
                </div>
                <button onclick="closeChartModal()" class="text-slate-400 hover:text-white text-xl p-1 transition"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="flex-1 h-80 w-full relative">
                <canvas id="expandedTempChart"></canvas>
   </div>
            <div class="flex justify-end pt-3 border-t border-slate-800 shrink-0">
                <button onclick="closeChartModal()" class="px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-white font-mono text-xs border border-slate-600 transition">Cerrar Gráfico</button>
            </div>
        </div>
    </div>
  <!-- MODAL HISTORIAL COMPLETO -->
  <div id="history-modal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="glass-panel w-full max-w-3xl rounded-2xl border border-indigo-500/40 p-6 space-y-4 shadow-2xl relative flex flex-col max-h-[85vh]">
            <div class="flex items-center justify-between border-b border-slate-700/80 pb-3 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 rounded-lg bg-indigo-500/20 text-indigo-400 border border-indigo-500/30">
                        <i class="fa-solid fa-clock-rotate-left text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-white font-mono">Historial Completo de Mediciones ESP8266</h3>
                        <p class="text-xs text-slate-400">Registro histórico cronológico de telemetría</p>
                    </div>
                </div>
                <button onclick="closeHistoryModal()" class="text-slate-400 hover:text-white text-xl p-1 transition"><i class="fa-solid fa-xmark"></i></button>
            </div>

   <div class="overflow-y-auto flex-1 pr-1 border border-slate-800 rounded-xl bg-slate-950/70">
                <table class="w-full text-left font-mono text-xs border-collapse">
                    <thead class="bg-slate-900 sticky top-0 border-b border-slate-800 text-slate-400 uppercase text-[10px]">
                        <tr>
                            <th class="p-3"># Reg</th>
                            <th class="p-3">Hora de Medición</th>
                            <th class="p-3">Temperatura</th>
                            <th class="p-3">Humedad</th>
                            <th class="p-3">Punto Rocío</th>
                            <th class="p-3">Estado ASHRAE</th>
                        </tr>
                    </thead>
                    <tbody id="history-table-body" class="divide-y divide-slate-800/60 text-slate-300"></tbody>
                </table>
            </div>

   <div class="flex justify-between items-center pt-3 border-t border-slate-800 text-xs text-slate-400 font-mono shrink-0">
                <span>Total de registros: <b id="history-total-count" class="text-cyan-400">0</b></span>
                <button onclick="closeHistoryModal()" class="px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-white font-mono text-xs border border-slate-600 transition">Cerrar Historial</button>
            </div>
        </div>
    </div>
 <!-- MODAL SOP -->
   <div id="sop-modal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="glass-panel w-full max-w-2xl rounded-2xl border border-cyan-500/40 p-6 space-y-4 shadow-2xl relative">
            <div class="flex items-center justify-between border-b border-slate-700/80 pb-3">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 rounded-lg bg-indigo-500/20 text-indigo-400 border border-indigo-500/30">
                        <i id="sop-icon" class="fa-solid fa-book-bookmark text-xl"></i>
                    </div>
                    <div>
                        <h3 id="sop-title" class="text-base font-bold text-white font-mono">Guía Táctica de Diagnóstico (SOP)</h3>
                        <p id="sop-subtitle" class="text-xs text-slate-400">Procedimiento Estándar de Operación</p>
                    </div>
                </div>
                <button onclick="closeSOPModal()" class="text-slate-400 hover:text-white text-xl p-1 transition"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="space-y-4 text-xs">
                <div class="bg-amber-500/10 border border-amber-500/30 rounded-xl p-3 text-amber-200">
                    <div class="font-bold flex items-center gap-2 mb-1 text-amber-400"><i class="fa-solid fa-triangle-exclamation"></i> Impacto & Consecuencia Técnica:</div>
                    <p id="sop-impact" class="text-slate-300 leading-relaxed">Selecciona un nodo o evento.</p>
                </div>
                <div>
                    <h4 class="font-bold text-cyan-300 mb-2 flex items-center gap-2"><i class="fa-solid fa-square-check text-emerald-400"></i> Checklist:</h4>
                    <div id="sop-checklist" class="space-y-2 bg-slate-900/80 p-3 rounded-xl border border-slate-800"></div>
                </div>
            </div>
            <div class="flex justify-end pt-3 border-t border-slate-800">
                <button onclick="closeSOPModal()" class="px-4 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-white font-mono text-xs border border-slate-600 transition">Cerrar Protocolo</button>
            </div>
        </div>
    </div>
  <!-- SCRIPTS -->
  <script>
        let isTrainingMode = false;
        let currentTemp = 22;
        let currentHumidity = 48;
        let currentCPU = 38;
        let currentRAM = 45;
        let expandedChartInstance = null;
        let isFaultActive = false;

        const telemetryHistory = [];

   const sopData = {
            'high-temp': { title: 'PROTOCOLO: Temperatura Crítica en Rack', impact: 'Riesgo de Thermal Shutdown.', checklist: ['Inspeccionar CRAC/HVAC.', 'Verificar sensor DHT22.'] },
            'pve-node-offline': { title: 'PROTOCOLO: Nodo PVE Inaccesible', impact: 'Interrupción del hipervisor.', checklist: ['Revisar iDRAC/iLO.'] },
            'core-router': { title: 'DIAGNÓSTICO: Core Router', impact: 'Gateway central.', checklist: ['Ping wan'] },
            'switch-alpha': { title: 'DIAGNÓSTICO: Switch Alpha', impact: 'Rack A trunk.', checklist: ['STP check'] },
            'switch-beta': { title: 'DIAGNÓSTICO: Switch Beta', impact: 'Rack B trunk.', checklist: ['LACP summary'] },
            'pve-01': { title: 'DIAGNÓSTICO: pve-01', impact: 'Nodo principal.', checklist: ['ZFS status'] },
            'pve-02': { title: 'DIAGNÓSTICO: pve-02', impact: 'Nodo secundario.', checklist: ['Journal logs'] },
            'pve-03': { title: 'DIAGNÓSTICO: pve-03', impact: 'Storage node.', checklist: ['I/O stats'] },
            'pve-04': { title: 'DIAGNÓSTICO: pve-04', impact: 'Staging node.', checklist: ['LVS check'] },
            'esp8266-sensor': { title: 'DIAGNÓSTICO: ESP8266 IoT', impact: 'Telemetría ambiental.', checklist: ['Wi-Fi RSSI', 'HTTP POST'] }
        };

   window.onload = function() {
            initExpandedChart();
            startMySQLRealtimeFeed();
            recordMeasurement(currentTemp.toFixed(1), currentHumidity.toFixed(1));
            setInterval(periodicTelemetryUpdate, 3000);
        };

  function recordMeasurement(temp, hum) {
            const now = new Date();
            const timeStr = now.toLocaleTimeString();
            const dewPoint = (temp - ((100 - hum) / 5)).toFixed(1);
    let ashraeStatus = 'ZONA ÓPTIMA';
            let statusColor = 'text-emerald-400';
            if (temp < 18) { ashraeStatus = 'SUB-ENFRIAMIENTO'; statusColor = 'text-blue-400'; }
            else if (temp > 27 && temp <= 32) { ashraeStatus = 'PRECAUCIÓN'; statusColor = 'text-amber-400'; }
            else if (temp > 32) { ashraeStatus = 'CRÍTICO'; statusColor = 'text-red-400 font-bold'; }

   const record = {
                id: telemetryHistory.length + 1,
                time: timeStr,
                temp: temp,
                hum: hum,
                dewPoint: dewPoint,
                status: ashraeStatus,
                statusColor: statusColor
            };

  telemetryHistory.unshift(record);

  document.getElementById('last-read-time').innerText = record.time;
            document.getElementById('last-read-temp').innerText = record.temp + ' °C';
            document.getElementById('last-read-hum').innerText = record.hum + ' %';
            document.getElementById('env-hum-val').innerText = record.hum + '%';

  renderHistoryTable();
        }

   function openHistoryModal() {
            renderHistoryTable();
            document.getElementById('history-modal').classList.remove('hidden');
        }

   function closeHistoryModal() {
            document.getElementById('history-modal').classList.add('hidden');
        }

  function openChartModal() {
            document.getElementById('chart-modal').classList.remove('hidden');
            if (expandedChartInstance) { expandedChartInstance.update(); }
        }

  function closeChartModal() {
            document.getElementById('chart-modal').classList.add('hidden');
        }

   function renderHistoryTable() {
            const tbody = document.getElementById('history-table-body');
            document.getElementById('history-total-count').innerText = telemetryHistory.length;

  tbody.innerHTML = telemetryHistory.map(item => `
   <tr class="hover:bg-slate-900/60 transition">
                    <td class="p-3 text-slate-500">#${item.id}</td>
                    <td class="p-3 text-cyan-300 font-bold">${item.time}</td>
                    <td class="p-3 text-amber-300">${item.temp} °C</td>
                    <td class="p-3 text-emerald-300">${item.hum} %</td>
                    <td class="p-3 text-purple-300">${item.dewPoint} °C</td>
                    <td class="p-3 ${item.statusColor}">${item.status}</td>
                </tr>
            `).join('');
        }

  function toggleMode() {
            isTrainingMode = !isTrainingMode;
            const btn = document.getElementById('mode-toggle-btn');
            const dot = document.getElementById('mode-indicator-dot');
            const text = document.getElementById('mode-text');
            const labPanel = document.getElementById('lab-control-panel');

  if (isTrainingMode) {
                btn.className = "px-3 py-1.5 rounded-lg border text-xs font-mono font-semibold transition flex items-center gap-2 bg-amber-500/10 border-amber-500/40 text-amber-400 hover:bg-amber-500/20";
                dot.className = "w-2 h-2 rounded-full bg-amber-400 animate-ping";
                text.innerText = "[ MODO: ENTRENAMIENTO / LAB ]";
                labPanel.classList.remove('hidden');
            } else {
                btn.className = "px-3 py-1.5 rounded-lg border text-xs font-mono font-semibold transition flex items-center gap-2 bg-emerald-500/10 border-emerald-500/40 text-emerald-400 hover:bg-emerald-500/20";
                dot.className = "w-2 h-2 rounded-full bg-emerald-400 animate-pulse";
                text.innerText = "[ MODO: OPERACIÓN REAL ]";
                labPanel.classList.add('hidden');
                resetLabSimulation();
            }
        }

   function updateLabSimulation() {
            if (!isTrainingMode) return;
            currentTemp = parseInt(document.getElementById('sim-temp-range').value);
            currentCPU = parseInt(document.getElementById('sim-cpu-range').value);
            document.getElementById('sim-temp-val').innerText = currentTemp + '°C';
            document.getElementById('sim-cpu-val').innerText = currentCPU + '%';
            applyTelemetryChanges();
        }

  function resetLabSimulation() {
            document.getElementById('sim-temp-range').value = 22;
            document.getElementById('sim-cpu-range').value = 38;
            document.getElementById('sim-temp-val').innerText = '22°C';
            document.getElementById('sim-cpu-val').innerText = '38%';
            currentTemp = 22;
            currentCPU = 38;
            if (isFaultActive) clearFault();
            applyTelemetryChanges();
        }

   function applyTelemetryChanges() {
            document.getElementById('pve1-cpu-val').innerText = Math.max(5, currentCPU - 10) + '%';
            document.getElementById('pve2-cpu-val').innerText = currentCPU + '%';
            document.getElementById('pve3-cpu-val').innerText = Math.min(100, currentCPU + 5) + '%';
            document.getElementById('pve4-cpu-val').innerText = Math.max(5, currentCPU - 15) + '%';

   const rh = currentHumidity;
            const dewPoint = (currentTemp - ((100 - rh) / 5)).toFixed(1);
            document.getElementById('dew-point-val').innerText = dewPoint + ' °C';

   let pointerPercent = ((currentTemp - 15) / (45 - 15)) * 100;
            pointerPercent = Math.max(0, Math.min(100, pointerPercent));
            document.getElementById('ashrae-pointer').style.left = pointerPercent + '%';

  const ashraeText = document.getElementById('ashrae-status');
            if (currentTemp < 18) {
                ashraeText.innerText = `SUB-ENFRIAMIENTO (${currentTemp}°C)`;
                ashraeText.className = "font-mono text-blue-400 font-semibold";
            } else if (currentTemp <= 27) {
                ashraeText.innerText = `ZONA ÓPTIMA ASHRAE (${currentTemp}°C)`;
                ashraeText.className = "font-mono text-emerald-400 font-semibold";
            } else if (currentTemp <= 32) {
                ashraeText.innerText = `PRECAUCIÓN TÉRMICA (${currentTemp}°C)`;
                ashraeText.className = "font-mono text-amber-400 font-semibold";
            } else {
                ashraeText.innerText = `PELIGRO CRÍTICO (${currentTemp}°C)`;
                ashraeText.className = "font-mono text-red-500 font-bold animate-pulse";
            }

   recordMeasurement(Number(currentTemp).toFixed(1), Number(currentHumidity).toFixed(1));

   if (expandedChartInstance) {
                const now = new Date().toLocaleTimeString();
                expandedChartInstance.data.labels.push(now);
                expandedChartInstance.data.datasets[0].data.push(currentTemp);
    if (expandedChartInstance.data.labels.length > 15) {
                    expandedChartInstance.data.labels.shift();
                    expandedChartInstance.data.datasets[0].data.shift();
                }
                expandedChartInstance.update('none');
            }
        }

   function triggerSimulatedFault() {
            isFaultActive = true;
            currentTemp = 38;
            currentCPU = 96;
            if (isTrainingMode) {
                document.getElementById('sim-temp-range').value = 38;
                document.getElementById('sim-cpu-range').value = 96;
                document.getElementById('sim-temp-val').innerText = '38°C';
                document.getElementById('sim-cpu-val').innerText = '96%';
            }
            document.getElementById('badge-pve-02').className = "w-3 h-3 rounded-full bg-red-500 pulse-offline";
            document.getElementById('card-pve-02').classList.add('border-red-500/80', 'bg-red-950/20');
            document.getElementById('cluster-status-badge').innerText = "3/4 DEGRADADO";
            document.getElementById('cluster-status-badge').className = "font-mono text-xs text-red-400 font-bold animate-pulse";
            applyTelemetryChanges();
            openSOPModal('high-temp');
        }
    function clearFault() {
            isFaultActive = false;
            document.getElementById('badge-pve-02').className = "w-3 h-3 rounded-full bg-emerald-500 pulse-online";
            document.getElementById('card-pve-02').classList.remove('border-red-500/80', 'bg-red-950/20');
            document.getElementById('cluster-status-badge').innerText = "4/4 Online";
            document.getElementById('cluster-status-badge').className = "font-mono text-xs text-emerald-400 font-semibold";
        }

   function initExpandedChart() {
            const ctx = document.getElementById('expandedTempChart').getContext('2d');
            expandedChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [new Date().toLocaleTimeString()],
                    datasets: [{
                        label: 'Temperatura Rack (°C)',
                        data: [22.0],
                        borderColor: '#00f3ff',
                        backgroundColor: 'rgba(0, 243, 255, 0.08)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.35,
                        pointRadius: 3,
                        pointBackgroundColor: '#00f3ff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#64748b', font: { size: 10 } } },
                        y: { min: 15, max: 45, grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#64748b', font: { size: 10 } } }
                    }
                }
            });
        }

  async function loadDatabaseData() {
            const container = document.getElementById('sql-logs-container');
            try {
                const response = await fetch('api.php?limit=30', { headers: { 'Accept': 'application/json' } });
                const result = await response.json();
                if (!response.ok || !result.ok) throw new Error(result.error || 'No se pudo consultar MySQL');
                const rows = result.measurements || [];
                if (!rows.length) { container.innerHTML = '<div class="text-amber-300">La base respondió, pero todavía no hay mediciones.</div>'; return; }
                telemetryHistory.splice(0, telemetryHistory.length, ...rows.map(row => {
                    const temp=Number(row.temperatura), hum=Number(row.humedad);
                    const status=temp<18?['SUB-ENFRIAMIENTO','text-blue-400']:temp>32?['CRÍTICO','text-red-400 font-bold']:temp>27?['PRECAUCIÓN','text-amber-400']:['ZONA ÓPTIMA','text-emerald-400'];
                    return {id:row.id,time:new Date(row.fecha_hora.replace(' ','T')).toLocaleString(),temp:temp.toFixed(1),hum:hum.toFixed(1),dewPoint:(temp-((100hum)/5)).toFixed(1),status:status[0],statusColor:status[1]};
                }));
                const latest=telemetryHistory[0]; currentTemp=Number(latest.temp); currentHumidity=Number(latest.hum);
                document.getElementById('last-read-time').innerText=latest.time;
                document.getElementById('last-read-temp').innerText=latest.temp+' °C';
                document.getElementById('last-read-hum').innerText=latest.hum+' %';
                document.getElementById('env-hum-val').innerText=latest.hum+'%'; renderHistoryTable();
                if(expandedChartInstance){const ordered=[...telemetryHistory].reverse();expandedChartInstance.data.labels=ordered.map(r=>new Date(r.time).toLocaleTimeString());expandedChartInstance.data.datasets[0].data=ordered.map(r=>Number(r.temp));expandedChartInstance.update('none');}
                container.innerHTML=rows.slice(0,15).map(row=>'<div class="p-1.5 rounded bg-slate-900 border border-slate-800 font-mono"><div class="flex justify-between text-slate-400 mb-0.5"><span class="text-cyan-400">['+row.fecha_hora+']</span><span class="text-emerald-400 font-bold">DB OK</span></div><div class="text-slate-300">SELECT mediciones · dispositivo #'+row.id_dispositivo+' · '+row.temperatura+' °C · '+row.humedad+'% · '+row.presion+' hPa</div></div>').join('');
            } catch(error) { container.innerHTML='<div class="text-red-300">Error de conexión: '+error.message+'. Abrí este dashboard desde un servidor PHP.</div>'; }
   }
        function periodicTelemetryUpdate() { /* Las lecturas reales se cargan desde api.php. */ }
      function openSOPModal(key) {
            const modal = document.getElementById('sop-modal');
            const data = sopData[key] || sopData['high-temp'];
            document.getElementById('sop-title').innerText = data.title;
            document.getElementById('sop-impact').innerText = data.impact;
    const checklistContainer = document.getElementById('sop-checklist');
            checklistContainer.innerHTML = '';
            data.checklist.forEach((item, index) => {
                const itemDiv = document.createElement('label');
                itemDiv.className = "flex items-start gap-2.5 p-2 rounded hover:bg-slate-800/80 cursor-pointer text-slate-300 transition";
                itemDiv.innerHTML = `<input type="checkbox" class="mt-0.5 rounded border-slate-700 bg-slate-950 text-cyan-400"><span><b>Paso ${index + 1}:</b> ${item}</span>`;
                checklistContainer.appendChild(itemDiv);
            });
            modal.classList.remove('hidden');
        }

  function closeSOPModal() { document.getElementById('sop-modal').classList.add('hidden'); }
    </script>
</body>
</html>
