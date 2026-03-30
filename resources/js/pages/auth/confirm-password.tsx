import type { PageProps } from '@inertiajs/core';
import { Form, Head } from '@inertiajs/react';
import InputError from '@/components/input-error';
import PasswordInput from '@/components/password-input';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import {
    createTranslator,
    type TranslationObject,
    useTranslation,
} from '@/lib/use-translations';
import { store } from '@/routes/password/confirm';

export default function ConfirmPassword() {
    const t = useTranslation();
    return (
        <>
            <Head title={t('auth.confirmPasswordTitle')} />

            <Form {...store.form()} resetOnSuccess={['password']}>
                {({ processing, errors }) => (
                    <div className="space-y-6">
                        <div className="grid gap-2">
                            <Label htmlFor="password">
                                {t('auth.password')}
                            </Label>
                            <PasswordInput
                                id="password"
                                name="password"
                                placeholder={t('auth.password')}
                                autoComplete="current-password"
                                autoFocus
                            />

                            <InputError message={errors.password} />
                        </div>

                        <div className="flex items-center">
                            <Button
                                className="w-full"
                                disabled={processing}
                                data-test="confirm-password-button"
                            >
                                {processing && <Spinner />}
                                {t('auth.confirmPasswordTitle')}
                            </Button>
                        </div>
                    </div>
                )}
            </Form>
        </>
    );
}

ConfirmPassword.layout = (props: PageProps) => {
    const t = createTranslator(props.translations as TranslationObject);

    return {
        title: t('auth.confirmPasswordTitle'),
        description: t('auth.confirmPasswordDescription'),
    };
};
