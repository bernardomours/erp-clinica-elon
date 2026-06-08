<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Odontys - Gestão de Clínicas</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|playfair-display:600,700" rel="stylesheet" />

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif

    <style>
        .font-serif-elegant { font-family: 'Playfair Display', serif; }
    </style>
</head>
<body class="bg-gradient-to-b from-[#e0f2fe] to-white min-h-screen text-gray-800 font-sans antialiased overflow-x-hidden">

@if (session('success'))
        <div id="toast-success" class="fixed top-5 right-5 z-[9999] flex items-center w-full max-w-xs p-4 text-gray-500 bg-white rounded-xl shadow-2xl border-l-4 border-emerald-500 transform transition-all duration-500 translate-x-0" role="alert">
            
            <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-emerald-500 bg-emerald-100 rounded-lg">
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                </svg>
            </div>
            
            <div class="ms-3 text-sm font-medium text-gray-800">
                {{ session('success') }}
            </div>
            
            <button type="button" onclick="document.getElementById('toast-success').style.display='none'" class="ms-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8">
                <span class="sr-only">Fechar</span>
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
            </button>
        </div>

        <script>
            setTimeout(() => {
                const toast = document.getElementById('toast-success');
                if (toast) {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateX(100%)';
                    setTimeout(() => toast.remove(), 500);
                }
            }, 5000);
        </script>
@endif

    <header class="container mx-auto px-6 py-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/topbar_icon.png') }}" 
                 alt="Logo Odontys" 
                 class="h-16 w-auto object-contain mix-blend-multiply">
        </div>

        <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-600">
            <a href="#como-funciona" class="hover:text-[#0891b2] transition-colors">Como funciona</a>
            <a href="#funcionalidades" class="hover:text-[#0891b2] transition-colors">Funcionalidades</a>
            <a href="#financeiro" class="hover:text-[#0891b2] transition-colors">Financeiro</a>
        </nav>

        <div class="flex items-center gap-4 text-sm font-medium">
        
            <button onclick="toggleModal()" class="bg-[#0ea5e9] text-white px-5 py-2.5 rounded-lg shadow-md hover:bg-[#0284c7] transition-colors flex items-center gap-2">
                Começar agora
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
            </button>
        </div>
    </header>

    <main class="container mx-auto px-6 pt-16 pb-24 text-center">
        <div class="inline-flex items-center gap-2 bg-blue-100/50 text-[#0284c7] border border-blue-200 px-4 py-1.5 rounded-full text-xs font-semibold uppercase tracking-wider mb-8">
            <span class="w-2 h-2 rounded-full bg-[#0ea5e9] animate-pulse"></span>
            Plataforma Completa para Consultórios
        </div>

        <h1 class="text-5xl md:text-7xl font-bold text-[#0f172a] tracking-tight leading-tight mb-6 max-w-4xl mx-auto">
            O fluxo perfeito para a sua <br class="hidden md:block">
            <span class="text-[#0284c7] font-serif-elegant italic">clínica odontológica</span>
        </h1>

        <p class="text-lg md:text-xl text-gray-600 mb-10 max-w-2xl mx-auto leading-relaxed">
            O Odontys conecta agendamentos, financeiro e prontuários numa plataforma única. Simples de usar, poderoso para o seu crescimento.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-16">
            <button onclick="toggleModal()" class="w-full sm:w-auto bg-[#2563eb] text-white text-lg font-medium px-8 py-4 rounded-xl shadow-lg hover:bg-[#1d4ed8] hover:shadow-xl hover:-translate-y-0.5 transition-all">
                Começar gratuitamente
            </button>
            <a href="#sistema" class="w-full sm:w-auto bg-white text-gray-700 text-lg font-medium px-8 py-4 rounded-xl shadow-sm border border-gray-200 hover:bg-gray-50 transition-all">
                Ver como funciona
            </a>
        </div>

        <div class="flex items-center justify-center gap-3 text-sm text-gray-500 font-medium mb-20">
            <div class="flex -space-x-2">
                <div class="w-8 h-8 rounded-full bg-blue-500 border-2 border-white flex items-center justify-center text-white text-xs">CM</div>
                <div class="w-8 h-8 rounded-full bg-indigo-500 border-2 border-white flex items-center justify-center text-white text-xs">JR</div>
                <div class="w-8 h-8 rounded-full bg-cyan-500 border-2 border-white flex items-center justify-center text-white text-xs">OD</div>
            </div>
            Já usado por clínicas parceiras em beta
        </div>

        <div id="sistema" class="relative max-w-5xl mx-auto">
            <div class="absolute -inset-1 bg-gradient-to-r from-cyan-400 to-blue-500 rounded-2xl blur opacity-30"></div>
            
            <div class="relative bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden">
                <div class="bg-gray-50 border-b border-gray-100 px-4 py-3 flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full bg-red-400"></div>
                    <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                    <div class="w-3 h-3 rounded-full bg-green-400"></div>
                    <div class="ml-4 bg-white border border-gray-200 rounded text-xs text-gray-400 px-3 py-1 font-mono w-64 text-left">
                        app.odontys.com.br
                    </div>
                </div>
                
                <div id="carousel-container" class="aspect-[16/9] bg-gray-100 relative overflow-hidden group">
                    
                    <div id="carousel-track" class="flex w-full h-full transition-transform duration-500 ease-out" style="transform: translateX(0%);">
                        <img src="{{ asset('images/page-dashboard.png') }}" class="w-full h-full flex-shrink-0 object-cover object-top" alt="Dashboard Financeiro">
                        <img src="{{ asset('images/page-agendamentos.png') }}" class="w-full h-full flex-shrink-0 object-cover object-top" alt="Tela de Agendamentos">
                        <img src="{{ asset('images/page-entradas.png') }}" class="w-full h-full flex-shrink-0 object-cover object-top" alt="Tela de Entradas">
                        <img src="{{ asset('images/page-saidas.png') }}" class="w-full h-full flex-shrink-0 object-cover object-top" alt="Tela de Saídas">
                    </div>

                    <button onclick="prevSlide()" class="absolute left-4 top-1/2 -translate-y-1/2 bg-black/40 hover:bg-black/60 backdrop-blur-sm text-white p-2 rounded-full opacity-0 group-hover:opacity-100 transition-opacity">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </button>

                    <button onclick="nextSlide()" class="absolute right-4 top-1/2 -translate-y-1/2 bg-black/40 hover:bg-black/60 backdrop-blur-sm text-white p-2 rounded-full opacity-0 group-hover:opacity-100 transition-opacity">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>

                    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2">
                        <button onclick="goToSlide(0)" class="indicator w-2.5 h-2.5 rounded-full bg-gray-800 transition-colors"></button>
                        <button onclick="goToSlide(1)" class="indicator w-2.5 h-2.5 rounded-full bg-gray-400 transition-colors"></button>
                        <button onclick="goToSlide(2)" class="indicator w-2.5 h-2.5 rounded-full bg-gray-400 transition-colors"></button>
                        <button onclick="goToSlide(3)" class="indicator w-2.5 h-2.5 rounded-full bg-gray-400 transition-colors"></button>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <section id="como-funciona" class="py-24 bg-white">
        <div class="container mx-auto px-6 max-w-6xl">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-[#082f49] mb-4">Como o Odontys funciona?</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">Um fluxo de trabalho desenhado para eliminar a papelada e focar no que realmente importa: o sorriso do seu paciente.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-12 relative">
                <div class="hidden md:block absolute top-12 left-[15%] right-[15%] h-0.5 bg-gradient-to-r from-cyan-200 to-blue-200 z-0"></div>

                <div class="relative z-10 flex flex-col items-center text-center">
                    <div class="w-24 h-24 rounded-2xl bg-[#e0f2fe] text-[#0891b2] flex items-center justify-center mb-6 shadow-sm border border-white">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-[#0f172a] mb-2">1. Agendamento</h3>
                    <p class="text-gray-600">O paciente é agendado rapidamente. O sistema organiza a sua agenda e envia lembretes automáticos.</p>
                </div>

                <div class="relative z-10 flex flex-col items-center text-center">
                    <div class="w-24 h-24 rounded-2xl bg-[#e0f2fe] text-[#0891b2] flex items-center justify-center mb-6 shadow-sm border border-white">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-[#0f172a] mb-2">2. Atendimento</h3>
                    <p class="text-gray-600">Acesse o prontuário eletrônico completo, histórico de imagens e plano de tratamento em poucos cliques.</p>
                </div>

                <div class="relative z-10 flex flex-col items-center text-center">
                    <div class="w-24 h-24 rounded-2xl bg-[#e0f2fe] text-[#0891b2] flex items-center justify-center mb-6 shadow-sm border border-white">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-[#0f172a] mb-2">3. Gestão</h3>
                    <p class="text-gray-600">O faturamento é gerado na hora. O sistema atualiza o caixa e o estoque de materiais instantaneamente.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="funcionalidades" class="py-24 bg-gray-50 border-y border-gray-100">
        <div class="container mx-auto px-6 max-w-6xl">
            <div class="mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-[#082f49] mb-4">Tudo o que a sua clínica precisa</h2>
                <p class="text-lg text-gray-600 max-w-2xl">Módulos integrados para você ter o controle total da operação, sem precisar de planilhas extras.</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Prontuário Digital</h3>
                    <p class="text-gray-600 leading-relaxed">Evolução do paciente, odontograma e anexos de exames radiográficos salvos com segurança na nuvem.</p>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 bg-cyan-50 text-cyan-600 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Agenda Inteligente</h3>
                    <p class="text-gray-600 leading-relaxed">Controle de retornos, faltas e encaixes com visualização por cadeira ou por profissional dentista.</p>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Planos de Tratamento</h3>
                    <p class="text-gray-600 leading-relaxed">Criação de orçamentos rápidos, aprovação de procedimentos e geração de contratos automaticamente.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="financeiro" class="py-24 bg-[#082f49] text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 -mt-20 -mr-20 w-96 h-96 bg-[#0ea5e9] rounded-full blur-3xl opacity-20"></div>
        
        <div class="container mx-auto px-6 max-w-6xl relative z-10">
            <div class="flex flex-col lg:flex-row items-center gap-16">
                <div class="lg:w-1/2">
                    <div class="inline-flex items-center gap-2 bg-white/10 border border-white/20 px-4 py-1.5 rounded-full text-xs font-semibold uppercase tracking-wider mb-6 text-cyan-300">
                        Gestão Completa
                    </div>
                    <h2 class="text-3xl md:text-5xl font-bold mb-6 leading-tight">O coração financeiro <br>da sua clínica</h2>
                    <p class="text-lg text-blue-100 mb-8 leading-relaxed">
                        Esqueça a dor de cabeça no fim do mês. Desenvolvemos um módulo financeiro e de suprimentos preciso, desenhado para que a saúde do seu caixa seja tão boa quanto a dos seus pacientes.
                    </p>
                    
                    <ul class="space-y-5">
                        <li class="flex items-start gap-4">
                            <div class="mt-1 w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <div>
                                <strong class="block text-white text-lg">Contas a Pagar e Receber</strong>
                                <span class="text-blue-200">Acompanhe recebimentos de pacientes e não perca nenhum vencimento de fornecedor ou despesa fixa.</span>
                            </div>
                        </li>
                        <li class="flex items-start gap-4">
                            <div class="mt-1 w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <div>
                                <strong class="block text-white text-lg">Controle de Estoque e Materiais</strong>
                                <span class="text-blue-200">Rastreie o consumo de resinas, luvas e anestésicos. Receba alertas antes que os materiais essenciais acabem.</span>
                            </div>
                        </li>
                        <li class="flex items-start gap-4">
                            <div class="mt-1 w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <div>
                                <strong class="block text-white text-lg">Dashboard Financeiro</strong>
                                <span class="text-blue-200">Gráficos dinâmicos com faturamento, despesas e lucros atualizados em tempo real.</span>
                            </div>
                        </li>
                    </ul>
                </div>
                
                <div class="lg:w-1/2 w-full">
                    <div class="bg-white/5 border border-white/10 p-6 rounded-2xl backdrop-blur-sm shadow-2xl">
                        <div class="flex justify-between items-center mb-6">
                            <h4 class="text-white font-medium">Fluxo de Caixa - Este Mês</h4>
                            <span class="text-emerald-400 font-bold text-xl">+ R$ 42.500</span>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-blue-200">Receitas (Tratamentos)</span>
                                    <span class="text-white">R$ 58.000</span>
                                </div>
                                <div class="w-full bg-white/10 rounded-full h-2.5">
                                    <div class="bg-emerald-400 h-2.5 rounded-full" style="width: 85%"></div>
                                </div>
                            </div>
                            
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-blue-200">Despesas e Contas a Pagar</span>
                                    <span class="text-white">R$ 15.500</span>
                                </div>
                                <div class="w-full bg-white/10 rounded-full h-2.5">
                                    <div class="bg-red-400 h-2.5 rounded-full" style="width: 35%"></div>
                                </div>
                            </div>
                            
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-blue-200">Custo de Materiais/Estoque</span>
                                    <span class="text-white">R$ 4.200</span>
                                </div>
                                <div class="w-full bg-white/10 rounded-full h-2.5">
                                    <div class="bg-amber-400 h-2.5 rounded-full" style="width: 15%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div id="interestModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4 bg-gray-900/40 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full relative p-8 border border-gray-100">
            
            <button onclick="toggleModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 text-2xl leading-none transition-colors">
                &times;
            </button>
            
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-1">Fale Conosco</h2>
                <p class="text-sm text-gray-500">Deixe seus dados e entraremos em contato para apresentar o sistema.</p>
            </div>
            
            <form action="/leads" method="POST" class="flex flex-col gap-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome Completo</label>
                    <input type="text" name="name" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-[#0ea5e9] focus:border-[#0ea5e9] outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                    <input type="email" name="email" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-[#0ea5e9] focus:border-[#0ea5e9] outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Telefone / WhatsApp</label>
                    <input type="tel" name="phone" required class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-[#0ea5e9] focus:border-[#0ea5e9] outline-none transition-all">
                </div>
                
                <button type="submit" class="mt-4 w-full bg-[#2563eb] hover:bg-[#1d4ed8] text-white py-3 rounded-xl font-semibold transition-all shadow-md hover:shadow-lg">
                    Solicitar Demonstração
                </button>
            </form>
        </div>
    </div>

    <script>
        function toggleModal() {
            const modal = document.getElementById('interestModal');
            modal.classList.toggle('hidden');
        }

        let currentSlide = 0;
        const track = document.getElementById('carousel-track');
        const indicators = document.querySelectorAll('.indicator');
        
        const totalSlides = track ? track.children.length : 0;

        function updateSlider() {
            track.style.transform = `translateX(-${currentSlide * 100}%)`;
            
            indicators.forEach((ind, index) => {
                if (index === currentSlide) {
                    ind.classList.remove('bg-gray-400');
                    ind.classList.add('bg-gray-800');
                } else {
                    ind.classList.remove('bg-gray-800');
                    ind.classList.add('bg-gray-400');
                }
            });
        }

        function nextSlide() {
            if (totalSlides > 0) {
                currentSlide = (currentSlide + 1) % totalSlides;
                updateSlider();
            }
        }

        function prevSlide() {
            if (totalSlides > 0) {
                currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
                updateSlider();
            }
        }

        function goToSlide(index) {
            currentSlide = index;
            updateSlider();
        }
    </script>
</body>
</html>