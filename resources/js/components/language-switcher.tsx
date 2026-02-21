import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { useTranslation } from '@/lib/use-translations';
import { cn } from '@/lib/utils';
import { update } from '@/routes/locale';
import { router, usePage } from '@inertiajs/react';
import { Check, Globe } from 'lucide-react';
import { type ReactNode, useMemo, useState } from 'react';

interface LanguageSwitcherProps {
    children?: ReactNode;
    className?: string;
}

export function LanguageSwitcher({
    children,
    className,
}: LanguageSwitcherProps = {}) {
    const t = useTranslation();
    const { props } = usePage();
    const currentLocale = props.currentLocale || 'en';
    const supportedLocales = props.supportedLocales || {};
    const [open, setOpen] = useState(false);

    const locales = useMemo(
        () => Object.entries(supportedLocales),
        [supportedLocales],
    );

    const handleLocaleChange = (locale: string) => {
        router.put(
            update().url,
            { locale },
            {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => setOpen(false),
            },
        );
    };

    const renderTrigger = () => {
        if (children) return children;
        return (
            <Button
                variant="ghost"
                size={className ? undefined : 'icon'}
                className={cn(className || 'h-9 w-9')}
            >
                <Globe className="h-5 w-5" />
                <span className={className ? undefined : 'sr-only'}>
                    {t('language.title')}
                </span>
            </Button>
        );
    };

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogTrigger asChild>{renderTrigger()}</DialogTrigger>
            <DialogContent className="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>{t('language.title')}</DialogTitle>
                    <DialogDescription>
                        {t('language.description')}
                    </DialogDescription>
                </DialogHeader>
                <div className="grid gap-2 py-4">
                    {locales.map(([code, info]) => {
                        const isActive = currentLocale === code;
                        return (
                            <button
                                key={code}
                                type="button"
                                onClick={() => handleLocaleChange(code)}
                                className={cn(
                                    'flex items-center justify-between rounded-lg border p-4 text-left transition-colors hover:bg-accent',
                                    isActive
                                        ? 'border-primary bg-accent'
                                        : 'border-border',
                                )}
                            >
                                <div className="flex flex-col items-start gap-1">
                                    <span className="font-medium">
                                        {info.native}
                                    </span>
                                    {info.name !== info.native && (
                                        <span className="text-sm text-muted-foreground">
                                            {info.name}
                                        </span>
                                    )}
                                </div>
                                {isActive && (
                                    <Check className="h-5 w-5 text-primary" />
                                )}
                            </button>
                        );
                    })}
                </div>
            </DialogContent>
        </Dialog>
    );
}
