import { Link } from '@inertiajs/react';
import React from 'react';

export default function AppLayout({ children }: { children: React.ReactNode }) {
    return (
        <div className="min-h-screen bg-gray-50 text-gray-900">
            <header className="border-b bg-white">
                <div className="mx-auto flex max-w-5xl justify-between p-4">
                    <Link href="/" className="font-semibold">
                        AI Chat Support
                    </Link>
                    <nav className="space-x-4">
                        <Link href="/" className="underline">
                            Home
                        </Link>
                        <Link href="/chat" className="underline">
                            Chat
                        </Link>
                    </nav>
                </div>
            </header>
            <main className="mx-auto max-w-5xl p-6">{children}</main>
        </div>
    );
}
