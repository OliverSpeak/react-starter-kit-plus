import { InertiaLinkProps } from '@inertiajs/react';
import { LucideIcon } from 'lucide-react';

export type TranslationObject = {
    [key: string]: string | TranslationObject;
};

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavGroup {
    title: string;
    items: NavItem[];
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon | null;
    isActive?: boolean;
}

export interface LocaleInfo {
    name: string;
    native: string;
}

export interface PageProps {
    currentLocale?: string;
    supportedLocales?: Record<string, LocaleInfo>;
    translations?: TranslationObject;
    [key: string]: unknown;
}

export interface SharedData {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
    currentLocale?: string;
    supportedLocales?: Record<string, LocaleInfo>;
    translations?: TranslationObject;
    [key: string]: unknown;
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    locale?: string | null;
    two_factor_enabled?: boolean;
    created_at: string;
    updated_at: string;
    [key: string]: unknown; // This allows for additional properties...
}
