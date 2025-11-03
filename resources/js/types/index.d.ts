import { InertiaLinkProps } from '@inertiajs/react';
import { LucideIcon } from 'lucide-react';

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

export interface SharedData {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
    [key: string]: unknown;
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    two_factor_enabled?: boolean;
    created_at: string;
    updated_at: string;
    [key: string]: unknown; // This allows for additional properties...
}

const routes = { dashboard, login, register };
export default routes;

export type Product = {
    id: number;
    sku: string;
    name: string;
    brand: string;
    category: string;
    price: number;
    currency: string;
    url: string;
    attributes?: Record<string, any>;
};

export type ChatReply = {
    chat_id: number;
    reply: string;
    matched_products: Product[];
};

export type ChatBubble = {
    id?: string;
    role: 'user' | 'assistant';
    content: string;
    meta?: any;
};
