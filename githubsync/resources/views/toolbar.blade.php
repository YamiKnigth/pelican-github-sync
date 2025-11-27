<div class="mb-4">
    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-6">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800">
                    <svg class="h-6 w-6 text-orange-600" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-950 dark:text-white">GitHub Sync</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Sincroniza archivos con GitHub</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button wire:click="openConfigModal" type="button"
                        class="inline-flex items-center gap-1 px-3 py-2 text-sm font-semibold rounded-lg bg-white text-gray-950 hover:bg-gray-50 dark:bg-white/5 dark:text-white dark:hover:bg-white/10 ring-1 ring-gray-950/10 dark:ring-white/20 shadow-sm transition">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.34 1.804A1 1 0 019.32 1h1.36a1 1 0 01.98.804l.295 1.473c.497.144.971.342 1.416.587l1.25-.834a1 1 0 011.262.125l.962.962a1 1 0 01.125 1.262l-.834 1.25c.245.445.443.919.587 1.416l1.473.294a1 1 0 01.804.98v1.361a1 1 0 01-.804.98l-1.473.295a6.95 6.95 0 01-.587 1.416l.834 1.25a1 1 0 01-.125 1.262l-.962.962a1 1 0 01-1.262.125l-1.25-.834a6.953 6.953 0 01-1.416.587l-.294 1.473a1 1 0 01-.98.804H9.32a1 1 0 01-.98-.804l-.295-1.473a6.957 6.957 0 01-1.416-.587l-1.25.834a1 1 0 01-1.262-.125l-.962-.962a1 1 0 01-.125-1.262l.834-1.25a6.957 6.957 0 01-.587-1.416l-1.473-.294A1 1 0 011 10.68V9.32a1 1 0 01.804-.98l1.473-.295c.144-.497.342-.971.587-1.416l-.834-1.25a1 1 0 01.125-1.262l.962-.962A1 1 0 015.38 3.03l1.25.834a6.957 6.957 0 011.416-.587l.294-1.473zM13 10a3 3 0 11-6 0 3 3 0 016 0z" clip-rule="evenodd"/>
                    </svg>
                    Configurar
                </button>
                
                @if($this->hasSettings())
                    <button wire:click="gitClone" type="button"
                            class="px-3 py-2 text-sm font-semibold rounded-lg bg-red-600 text-white hover:bg-red-700 shadow-sm transition">
                        Clone
                    </button>
                    <button wire:click="gitPull" type="button"
                            class="px-3 py-2 text-sm font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700 shadow-sm transition">
                        Pull
                    </button>
                    <button wire:click="openPushModal" type="button"
                            class="px-3 py-2 text-sm font-semibold rounded-lg bg-green-600 text-white hover:bg-green-700 shadow-sm transition">
                        Push
                    </button>
                @else
                    <span class="text-sm text-gray-500">Configura primero</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal de Configuración --}}
    @if($showConfigModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-950 dark:text-white mb-4">Configurar GitHub</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">URL Repositorio</label>
                            <input wire:model="repo_url" type="url" 
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                                   placeholder="https://github.com/usuario/repo.git">
                            @error('repo_url') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Branch</label>
                            <input wire:model="branch" type="text" 
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                            @error('branch') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Usuario GitHub</label>
                            <input wire:model="git_username" type="text" 
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                            @error('git_username') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                            <input wire:model="git_email" type="email" 
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                            @error('git_email') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Token (PAT)</label>
                            <input wire:model="encrypted_token" type="password" 
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                            @error('encrypted_token') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    
                    <div class="flex justify-end gap-2 mt-6">
                        <button wire:click="closeConfigModal" type="button"
                                class="px-4 py-2 text-sm font-semibold rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300">
                            Cancelar
                        </button>
                        <button wire:click="saveConfiguration" type="button"
                                class="px-4 py-2 text-sm font-semibold rounded-lg bg-blue-600 text-white hover:bg-blue-700">
                            Guardar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal de Push --}}
    @if($showPushModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
            <div class="bg-white dark:bg-gray-900 rounded-xl shadow-xl max-w-md w-full">
                <div class="p-6">
                    <h2 class="text-xl font-semibold text-gray-950 dark:text-white mb-4">Mensaje de Commit</h2>
                    
                    <div>
                        <input wire:model="commit_message" type="text" 
                               class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                               placeholder="Update via Panel">
                    </div>
                    
                    <div class="flex justify-end gap-2 mt-6">
                        <button wire:click="showPushModal = false" type="button"
                                class="px-4 py-2 text-sm font-semibold rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300">
                            Cancelar
                        </button>
                        <button wire:click="gitPush" type="button"
                                class="px-4 py-2 text-sm font-semibold rounded-lg bg-green-600 text-white hover:bg-green-700">
                            Push
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>