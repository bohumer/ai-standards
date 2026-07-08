<!-- TODO PRE AI: Táto šablóna slúži ako potvrdzovací dialóg na zmazanie. V tvojom projekte môžeš využiť existujúce modály alebo iný spôsob potvrdenia. Toto je iba zástupný HTML kód bez dizajnu. -->
@if($showConfirmDelete)
    <div class="border p-4 mt-4 bg-red-50">
        <h2>{{ t('confirm_delete') }}</h2>
        <p>{{ t('delete_confirmation_text', ['item' => $keyToDelete]) }}</p>
        
        <div class="mt-4 flex space-x-2">
            <button wire:click="delete" class="p-2 border bg-red-500 text-white">{{ t('delete') }}</button>
            <button wire:click="cancelDelete" class="p-2 border">{{ t('cancel') }}</button>
        </div>
    </div>
@endif
