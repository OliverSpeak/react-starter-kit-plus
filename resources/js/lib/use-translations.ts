import { type PageProps, type TranslationObject } from '@/types';
import { usePage } from '@inertiajs/react';
import { useMemo } from 'react';

/**
 * Hook for accessing translations in React components.
 *
 * @returns A memoized translation function that accepts a dot-notation key
 *          and optional replacements for placeholders.
 *
 * @example
 * ```tsx
 * const __ = useTranslations();
 * const message = __('ui.main.dashboard');
 * const greeting = __('welcome.message', { name: 'John' });
 * ```
 */
export function useTranslations() {
    const { props } = usePage<PageProps>();
    const translations = props.translations || {};

    return useMemo(
        () =>
            function __(
                key: string,
                replacements: Record<string, string | number> = {},
            ): string {
                if (!key || typeof key !== 'string') {
                    return key;
                }

                const keys = key.split('.');
                let value: string | TranslationObject | undefined =
                    translations;

                for (const k of keys) {
                    if (value && typeof value === 'object' && k in value) {
                        value = value[k];
                    } else {
                        // Return the key if translation not found (helps identify missing translations)
                        return key;
                    }
                }

                if (typeof value !== 'string') {
                    return key;
                }

                // Replace placeholders (e.g., :name, :count) with provided values
                return Object.entries(replacements).reduce(
                    (str, [placeholder, replacement]) => {
                        // Escape special regex characters in placeholder name
                        const escapedPlaceholder = placeholder.replace(
                            /[.*+?^${}()|[\]\\]/g,
                            '\\$&',
                        );
                        return str.replace(
                            new RegExp(`:${escapedPlaceholder}`, 'g'),
                            String(replacement),
                        );
                    },
                    value,
                );
            },
        [translations],
    );
}
