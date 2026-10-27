<?php

use App\Models\Setting;
use App\Models\Translation;

if (! function_exists('t')) {
    /**
     * Translate a static UI key from the database dictionary.
     * Never pass a fallback string; missing keys intentionally return "#Trans.{key}".
     */
    function t(string $key, array $replace = []): string
    {
        return Translation::translate($key, $replace);
    }
}

if (! function_exists('app_locale')) {
    /**
     * Return active application locale code.
     */
    function app_locale(): string
    {
        return (string) (session('locale') ?? config('app.locale', 'sk'));
    }
}

if (! function_exists('app_langs')) {
    /**
     * Return list of active language codes (e.g. ['sk', 'en']).
     */
    function app_langs(): array
    {
        $configured = Setting::getCodes('system', 'language');

        return ! empty($configured) ? $configured : ['sk', 'en'];
    }
}

if (! function_exists('clean_html')) {
    /**
     * Strip dangerous HTML tags, event attributes, and javascript: URIs before unescaped rendering.
     */
    function clean_html(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        $allowedTags = '<p><br><strong><b><em><i><u><ul><ol><li><h2><h3><h4><blockquote><a><img><table><thead><tbody><tr><th><td><hr><span><div><figure><figcaption>';
        $clean = strip_tags($html, $allowedTags);

        // Remove inline event handlers (onclick, onerror, onload, etc.)
        $clean = preg_replace('/\s+on[a-z]+\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', (string) $clean);

        // Neutralize javascript:, vbscript:, and data:text/html URIs in href/src attributes
        $clean = preg_replace('/(href|src)\s*=\s*(["\']?)\s*(?:javascript|vbscript|data\s*:\s*text\/html)\s*:[^"\'>\s]*/i', '$1=$2#', (string) $clean);

        return (string) $clean;
    }
}
