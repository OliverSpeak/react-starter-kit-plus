import { usePage } from '@inertiajs/react';
import { useMemo } from 'react';

/**
 * Translation object type for nested translation structures.
 */
type TranslationObject = Record<string, string | TranslationObject>;

/**
 * Translation options compatible with react-i18next patterns.
 */
export interface TranslationOptions {
    /** Default value if translation key is not found */
    defaultValue?: string;
    /** Interpolation values for placeholders (e.g., { name: 'John' }) */
    [key: string]: string | number | undefined;
}

/**
 * Translation function signature compatible with react-i18next.
 * Supports both simple replacements object and options object.
 */
export type TranslationFunction = (
    key: string,
    options?: TranslationOptions | Record<string, string | number>,
) => string;

/**
 * Hook for accessing translations in React components.
 *
 * Compatible with react-i18next patterns for easier future migration.
 *
 * @returns A memoized translation function that accepts a dot-notation key
 *          and optional options/replacements.
 *
 * @example
 * ```tsx
 * const t = useTranslation();
 * const message = t('main.dashboard');
 * const greeting = t('welcome.message', { name: 'John' });
 * const withDefault = t('missing.key', { defaultValue: 'Fallback text' });
 * ```
 */
export function useTranslation(): TranslationFunction {
    const { props } = usePage();
    const translations = props.translations || {};

    return useMemo(
        () =>
            function t(
                key: string,
                options?: TranslationOptions | Record<string, string | number>,
            ): string {
                if (!key || typeof key !== 'string') {
                    return key;
                }

                // Normalize options - support both simple object and options object
                const opts = options || {};
                const defaultValue =
                    'defaultValue' in opts &&
                    typeof opts.defaultValue === 'string'
                        ? opts.defaultValue
                        : undefined;
                const replacements = { ...opts };
                delete (replacements as Partial<TranslationOptions>)
                    .defaultValue;

                // Navigate nested translation object using dot notation
                const keys = key.split('.');
                let value: string | TranslationObject | undefined =
                    translations;

                for (const k of keys) {
                    if (value && typeof value === 'object' && k in value) {
                        value = value[k];
                    } else {
                        // Return defaultValue if provided, otherwise return key
                        return defaultValue ?? key;
                    }
                }

                if (typeof value !== 'string') {
                    return defaultValue ?? key;
                }

                // Replace placeholders (e.g., :name, :count) with provided values
                return Object.entries(replacements).reduce(
                    (str, [placeholder, replacement]) => {
                        if (replacement === undefined) return str;
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
