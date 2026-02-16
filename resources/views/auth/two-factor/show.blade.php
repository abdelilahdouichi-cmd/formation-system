@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Authentification à deux facteurs</h2>

                @if ($twoFactorEnabled)
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <p class="text-green-800">
                            ✓ L'authentification à deux facteurs est activée sur ce compte.
                        </p>
                    </div>

                    <form method="DELETE" action="{{ route('two-factor.disable') }}" class="space-y-6">
                        @csrf
                        @method('DELETE')

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                Entrez votre mot de passe pour désactiver 2FA
                            </label>
                            <input
                                type="password"
                                name="password"
                                id="password"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                        </div>

                        <button
                            type="submit"
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                        >
                            Désactiver 2FA
                        </button>
                    </form>
                @else
                    <p class="text-gray-600 mb-4">
                        Activez l'authentification à deux facteurs pour sécuriser votre compte avec un code unique.
                    </p>

                    <button
                        @click="setupTwoFactor()"
                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700"
                    >
                        Activer 2FA
                    </button>

                    <div id="setupModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4">
                        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Configurer l'authentification à deux facteurs</h3>

                            <div class="mb-4">
                                <p class="text-sm text-gray-600 mb-2">Code secret :</p>
                                <code id="secretDisplay" class="block p-3 bg-gray-100 rounded text-center font-mono text-lg break-words"></code>
                            </div>

                            <div class="mb-4">
                                <p class="text-sm text-gray-600 mb-2">Codes de secours :</p>
                                <div id="backupCodesDisplay" class="bg-gray-50 p-3 rounded max-h-32 overflow-y-auto">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="verifyCode" class="block text-sm font-medium text-gray-700">
                                    Entrez le code de 6 chiffres de votre authenticateur
                                </label>
                                <input
                                    type="text"
                                    id="verifyCode"
                                    maxlength="6"
                                    inputmode="numeric"
                                    placeholder="000000"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                />
                            </div>

                            <div class="flex gap-2">
                                <button
                                    @click="confirmTwoFactor()"
                                    class="flex-1 inline-flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white hover:bg-indigo-700"
                                >
                                    Confirmer
                                </button>
                                <button
                                    @click="cancelSetup()"
                                    class="flex-1 inline-flex justify-center items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-sm text-gray-700 hover:bg-gray-300"
                                >
                                    Annuler
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    let twoFactorData = {};

    function setupTwoFactor() {
        document.getElementById('setupModal').classList.remove('hidden');

        fetch('{{ route("two-factor.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
        })
        .then(r => r.json())
        .then(data => {
            twoFactorData = data;
            document.getElementById('secretDisplay').textContent = data.secret;

            const codesHtml = data.backup_codes
                .map(code => `<div class="text-sm font-mono">${code}</div>`)
                .join('');
            document.getElementById('backupCodesDisplay').innerHTML = codesHtml;
        });
    }

    function confirmTwoFactor() {
        const code = document.getElementById('verifyCode').value;
        if (code.length !== 6) {
            alert('Le code doit contenir 6 chiffres');
            return;
        }

        fetch('{{ route("two-factor.confirm") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ code }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
            } else {
                alert('2FA configuré avec succès! Veuillez enregistrer vos codes de secours');
                location.reload();
            }
        });
    }

    function cancelSetup() {
        document.getElementById('setupModal').classList.add('hidden');
    }
</script>
@endsection
