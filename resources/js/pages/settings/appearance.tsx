import type { PageProps } from '@inertiajs/core';
import { Head } from '@inertiajs/react';
import AppearanceTabs from '@/components/appearance-tabs';
import Heading from '@/components/heading';
import {
    createTranslator,
    
    useTranslation
} from '@/lib/use-translations';
import type {TranslationObject} from '@/lib/use-translations';
import { edit as editAppearance } from '@/routes/appearance';

export default function Appearance() {
    const t = useTranslation();

    return (
        <>
            <Head title={t('settings.appearance.settingsTitle')} />

            <h1 className="sr-only">
                {t('settings.appearance.settingsTitle')}
            </h1>

            <div className="space-y-6">
                <Heading
                    variant="small"
                    title={t('settings.appearance.settingsTitle')}
                    description={t('settings.appearance.description')}
                />
                <AppearanceTabs />
            </div>
        </>
    );
}

Appearance.layout = (props: PageProps) => {
    const t = createTranslator(props.translations as TranslationObject);

    return {
        breadcrumbs: [
            {
                title: t('settings.appearance.settingsTitle'),
                href: editAppearance(),
            },
        ],
    };
};
