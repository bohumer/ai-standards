<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait HasJsonTranslations
{
    /**
     * Get list of translatable JSON attributes defined on the model.
     */
    public function getTranslatableAttributes(): array
    {
        return property_exists($this, 'translatable') && is_array($this->translatable)
            ? $this->translatable
            : [];
    }

    /**
     * Determine whether a translatable attribute stores an array value per locale (e.g. product highlights, content).
     */
    protected function isTranslatableArrayAttribute(string $key): bool
    {
        return property_exists($this, 'translatableArray')
            && is_array($this->translatableArray)
            && in_array($key, $this->translatableArray, true);
    }

    /**
     * Check whether a translated value is considered non-empty.
     */
    protected function isTranslationFilled(mixed $value): bool
    {
        if (is_array($value)) {
            if (empty($value)) {
                return false;
            }

            foreach ($value as $item) {
                if ($this->isTranslationFilled($item)) {
                    return true;
                }
            }

            return false;
        }

        if (is_bool($value) || is_numeric($value)) {
            return true;
        }

        return $value !== null && is_scalar($value) && trim((string) $value) !== '';
    }

    /**
     * Get a translatable attribute value for the active application locale.
     */
    public function getAttribute($key)
    {
        if (in_array($key, $this->getTranslatableAttributes(), true)) {
            return $this->getTranslation($key, app_locale());
        }

        return parent::getAttribute($key);
    }

    /**
     * Set a translatable attribute value (accepts locale map array or value for active locale).
     */
    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->getTranslatableAttributes(), true)) {
            if (is_array($value)) {
                // For array-valued translatable attributes (e.g. highlights), a list array means value for active locale
                if ($this->isTranslatableArrayAttribute($key) && array_is_list($value)) {
                    return $this->setTranslation($key, app_locale(), $value);
                }

                $this->attributes[$key] = json_encode($value, JSON_UNESCAPED_UNICODE);

                return $this;
            }

            return $this->setTranslation($key, app_locale(), $value);
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * Get translation for a specific locale with fallback to default locales ('sk', 'en', or first filled).
     */
    public function getTranslation(string $key, ?string $locale = null, bool $useFallback = true): mixed
    {
        $locale ??= app_locale();
        $translations = $this->getTranslations($key);
        $emptyDefault = $this->isTranslatableArrayAttribute($key) ? [] : '';

        if (isset($translations[$locale]) && $this->isTranslationFilled($translations[$locale])) {
            return $translations[$locale];
        }

        if (! $useFallback) {
            return $translations[$locale] ?? $emptyDefault;
        }

        foreach (['sk', 'en'] as $fallbackLocale) {
            if (isset($translations[$fallbackLocale]) && $this->isTranslationFilled($translations[$fallbackLocale])) {
                return $translations[$fallbackLocale];
            }
        }

        foreach ($translations as $candidate) {
            if ($this->isTranslationFilled($candidate)) {
                return $candidate;
            }
        }

        return $emptyDefault;
    }

    /**
     * Set translation for a specific locale.
     */
    public function setTranslation(string $key, string $locale, mixed $value): static
    {
        $translations = $this->getTranslations($key);
        $translations[$locale] = $value;

        $this->attributes[$key] = json_encode($translations, JSON_UNESCAPED_UNICODE);

        return $this;
    }

    /**
     * Get all translations for a key as an associative array.
     */
    public function getTranslations(string $key): array
    {
        $value = $this->attributes[$key] ?? null;

        if (empty($value)) {
            return [];
        }

        if (is_array($value)) {
            return $value;
        }

        $decoded = json_decode((string) $value, true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Ensure array serialization resolves localized attribute values.
     */
    public function attributesToArray(): array
    {
        $attributes = parent::attributesToArray();

        foreach ($this->getTranslatableAttributes() as $key) {
            if (array_key_exists($key, $this->attributes)) {
                $attributes[$key] = $this->getTranslation($key, app_locale());
            }
        }

        return $attributes;
    }

    /**
     * Scope a query to match a JSON translatable column using LIKE across active or specific locale.
     */
    public function scopeWhereTranslatedLike(Builder $query, string $column, string $term, ?string $locale = null): Builder
    {
        $cleanTerm = trim($term);
        if ($cleanTerm === '') {
            return $query;
        }

        if ($locale !== null) {
            return $query->where("{$column}->{$locale}", 'like', "%{$cleanTerm}%");
        }

        $langs = array_keys(function_exists('app_langs') ? app_langs() : ['sk' => 'SK', 'en' => 'EN']);

        return $query->where(function (Builder $q) use ($column, $cleanTerm, $langs) {
            foreach ($langs as $lang) {
                $q->orWhere("{$column}->{$lang}", 'like', "%{$cleanTerm}%");
            }
        });
    }
}
