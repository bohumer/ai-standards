<?php

namespace App\Livewire\Traits;

use App\Models\Draft;

trait HasDrafts
{
    // Pomocná funkcia na načítanie
    protected function loadDraft(string $modelType, ?int $modelId = null): bool
    {
        $draft = Draft::where('user_id', auth()->id())
            ->where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->first();

        if ($draft) {
            $this->fill($draft->payload);
            return true;
        }

        return false;
    }

    // Pomocná funkcia na uloženie (zavolá sa v updated() hooku)
    protected function saveDraft(string $modelType, ?int $modelId, array $payload): void
    {
        Draft::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'model_type' => $modelType,
                'model_id' => $modelId,
            ],
            [
                'payload' => $payload
            ]
        );
    }

    // Odstránenie po ostrom uložení
    protected function clearDraft(string $modelType, ?int $modelId): void
    {
        Draft::where('user_id', auth()->id())
            ->where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->delete();
    }
}
