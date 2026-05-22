<x-filament-panels::layout.base :livewire="$this">
    <div class="flex min-h-screen">
        
        <div class="flex flex-1 flex-col justify-center px-4 py-12 sm:px-6 lg:flex-none lg:px-20 xl:px-24 bg-white dark:bg-zinc-900 shadow-2xl z-10">
            <div class="mx-auto w-full max-w-sm lg:w-96">
                
                <div class="text-center lg:text-left mb-8">
                    <div class="flex justify-center lg:justify-start mb-6">
                        <img src="{{ url('/images/topbar_icon.png') }}" alt="Logo" class="h-16 w-auto">
                    </div>
                    <h2 class="text-2xl font-bold leading-9 tracking-tight text-gray-900 dark:text-white">
                        Bem-vindo(a) de volta!
                    </h2>
                    <p class="mt-2 text-sm leading-6 text-gray-500 dark:text-gray-400">
                        Acesse o painel inteligente da sua clínica.
                    </p>
                </div>

                <x-filament-panels::form wire:submit="authenticate">
                    {{ $this->form }}

                    <x-filament-panels::form.actions
                        :actions="$this->getCachedFormActions()"
                        :full-width="$this->hasFullWidthFormActions()"
                    />
                </x-filament-panels::form>
                
            </div>
        </div>

        <div class="relative hidden w-0 flex-1 lg:block bg-sky-600 dark:bg-sky-900">
            
            <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1606811841689-23dfddce3e95?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80'); opacity: 0.3;"></div>
            
            <div class="absolute inset-0 bg-gradient-to-t from-sky-900/90 to-sky-600/70 mix-blend-multiply"></div>

            <div class="absolute inset-0 flex flex-col items-center justify-center p-12 text-center">
                <img src="{{ url('/images/topbar_icon.png') }}" alt="Logo" class="h-32 w-auto mb-8 drop-shadow-2xl opacity-90">
                
                <h1 class="text-4xl font-bold tracking-tight text-white sm:text-5xl drop-shadow-lg mb-4">
                    A sua clínica,<br>no próximo nível.
                </h1>
                
                <p class="text-lg text-sky-100 max-w-lg drop-shadow leading-relaxed">
                    Gestão inteligente, faturamento automatizado e controle total de estoque em um único lugar.
                </p>
            </div>
            
        </div>
    </div>
</x-filament-panels::layout.base>