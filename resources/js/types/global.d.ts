import type { Auth } from '@/types/auth';

declare module '@inertiajs/core' {
    export interface InertiaConfig {
        sharedPageProps: {
            name: string;
            auth: Auth;
            sidebarOpen: boolean;
            currentLocale: string;
            supportedLocales: Record<
                string,
                {
                    name: string;
                    native: string;
                }
            >;
            translations: Record<string, unknown>;
            [key: string]: unknown;
        };
    }
}
