<?php

namespace App\Livewire\Traits;

use App\Models\Draft;
use Livewire\Attributes\Locked;

trait HasDrafts
{
    #[Locked]
    public bool $hasDraft = false;

    /**
     * Load saved draft payload for the authenticated user and fill component properties.
     */
    protected function loadDraft(string $modelType, ?int $modelId = null, string $contextKey = ''): bool
    {
        $draft = Draft::query()
            ->where('user_id', auth()->id())
            ->where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->where('context_key', $contextKey)
            ->first();

        if ($draft && is_array($draft->payload)) {
            $payload = $draft->payload;
            foreach ($payload as $key => $val) {
                if (property_exists($this, $key)) {
                    $ref = new \ReflectionProperty($this, $key);
                    $type = $ref->getType();
                    if ($type instanceof \ReflectionNamedType && $type->getName() === 'array' && ! is_array($val)) {
                        $payload[$key] = [app_locale() => (string) $val];
                    }
                }
            }

            $this->fill($payload);
            $this->hasDraft = true;

            return true;
        }

        return false;
    }

    /**
     * Persist non-empty form state into the drafts table on blur updates.
     */
    protected function saveDraft(string $modelType, ?int $modelId, array $payload, string $contextKey = ''): void
    {
        $ignoredDefaultKeys = ['status', 'is_active', 'is_approved', 'role', 'type', 'order'];
        $hasContent = false;

        foreach ($payload as $key => $value) {
            if (in_array($key, $ignoredDefaultKeys, true)) {
                continue;
            }

            if (is_array($value)) {
                if (! empty(array_filter($value, static fn ($v) => $v !== null && $v !== ''))) {
                    $hasContent = true;
                    break;
                }
            } elseif ($value !== null && $value !== '' && $value !== 0 && $value !== '0') {
                $hasContent = true;
                break;
            }
        }

        if ($hasContent) {
            Draft::query()->updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'model_type' => $modelType,
                    'model_id' => $modelId,
                    'context_key' => $contextKey,
                ],
                [
                    'payload' => $payload,
                ]
            );
            $this->hasDraft = true;
        } else {
            $this->clearDraft($modelType, $modelId, $contextKey);
        }
    }

    /**
     * Delete the draft after final persistence or explicit discard.
     */
    public function clearDraft(string $modelType, ?int $modelId = null, string $contextKey = ''): void
    {
        Draft::query()
            ->where('user_id', auth()->id())
            ->where('model_type', $modelType)
            ->where('model_id', $modelId)
            ->where('context_key', $contextKey)
            ->delete();

        $this->hasDraft = false;
    }
}
