# 🏥 Sistema de Gestão de Clínicas (SaaS Multi-Tenant)

Um sistema completo de gestão de clínicas e consultórios operando no modelo SaaS (Software as a Service). Desenvolvido com a TALL Stack (Tailwind, Alpine, Laravel, Livewire) e focado em alta performance, UX imersiva e automação de processos clínicos e financeiros.

## 🚀 Tecnologias Utilizadas

* **Framework:** Laravel 11 / PHP 8.4
* **Painel Administrativo:** FilamentPHP v3
* **Frontend:** Livewire 3, Alpine.js, Tailwind CSS
* **Banco de Dados:** MySQL / PostgreSQL
* **Arquitetura:** Multi-Tenancy (Escopo global por Clínica)

---

## 📦 Módulos Principais

### 1. 🏢 Multi-Tenancy & Administração
O sistema separa completamente os dados entre diferentes clínicas (Tenants), garantindo privacidade e segurança.
* **Painel Super Admin:** Acesso exclusivo para gestão das clínicas assinantes, planos e permissões.
* **Painéis de Clínica:** Área de trabalho isolada onde cada clínica visualiza apenas os seus dados (Pacientes, Agendamentos, Financeiro).
* **Feature Toggling:** Módulos inteiros (como o Financeiro) são ativados ou ocultados dinamicamente com base no plano contratado pela clínica.

### 2. 📅 Gestão de Agenda e Atendimentos
Fluxo completo de receção e cadeira do profissional, com UI otimizada para produtividade.
* **Lembretes e Dashboard:** Widgets inteligentes listando as próximas consultas do dia na tela inicial.
* **Ciclo de Vida da Consulta:** Controle rigoroso de status (`Agendada`, `Confirmada`, `Realizada`, `Cancelada`) com indicadores visuais (badges).
* **Filtros Avançados:** Busca assíncrona por paciente, procedimento, situação e intervalo dinâmico de datas com indicadores visuais ativos (`indicateUsing`).
* **Agendamento de Retorno:** Ação em massa que duplica os dados clínicos e projeta um retorno automático (ex: +6 meses) com um único clique.

### 3. 💸 Faturamento Automático
Módulo inteligente que cruza o atendimento clínico com o contas a receber.
* **Ação de Faturamento:** Ao finalizar uma consulta, o sistema gera automaticamente a cobrança baseada no valor do procedimento.
* **Planos de Pagamento Flexíveis:** Suporte a múltiplas formas (PIX, Boleto, Cartão de Crédito, Dinheiro e Carnê da Clínica).
* **Parcelamento Automático:** Cálculo em tempo real do valor da parcela (`installment_amount`) com base na quantidade escolhida.
* **Regras de Visibilidade:** O botão de faturar só aparece se o paciente tiver um procedimento vinculado, se a consulta não estiver cancelada/finalizada e se a clínica possuir o módulo ativado.

### 4. 📦 Controle de Estoque Integrado
Baixa automatizada de insumos atrelada aos procedimentos clínicos.
* **Receituário de Procedimento:** Cada procedimento (ex: Limpeza, Extração) possui uma "receita" dos materiais utilizados (`ProcedureProducts`).
* **Baixa Silenciosa:** Ao faturar e finalizar a consulta, o sistema reduz o estoque (`current_stock`) de todos os itens vinculados ao procedimento automaticamente.
* **Alertas Persistentes de Ruptura:** Se a baixa automática zerar ou negativar o estoque de um item, o sistema dispara uma notificação persistente (`Warning`) exigindo a atenção do utilizador para solicitação de compra.
* **Histórico de Consumo:** Registo detalhado na tabela `ProductConsumption`, vinculando o item gasto à consulta, ao paciente e ao profissional que realizou a baixa.

### 5. 👥 Cadastro de Pacientes (Prontuário)
* **Quick Create:** Criação de novos pacientes (nome, telefone, etc.) diretamente pelo modal de agendamento, sem necessidade de navegar para outra tela.
* **Pesquisa Global:** Busca otimizada (`searchable` e `preload`) para encontrar pacientes em frações de segundo.

---

## 💡 Padrões de Código & Boas Práticas

* **Evitando Lazy Loading:** Relacionamentos carregados estrategicamente (`with()`) para evitar N+1 queries.
* **Bypassing Scopes Seguros:** Utilização de `withoutGlobalScopes()` apenas quando estritamente necessário (ex: carregar nomes de procedimentos isolados em colunas), mantendo a segurança do Tenant principal intacta.
* **UX Premium:** Uso extensivo de Modais de Confirmação, Notificações Toast, Badges de Status coloridas e navegação SPA (Single Page Application).