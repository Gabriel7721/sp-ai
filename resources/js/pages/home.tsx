import { Link, usePage } from '@inertiajs/react';
import AppLayout from './layouts/applayout';

type Product = {
  id: number;
  name: string;
  brand: string;
  price: number;
  url: string;
  category: string;
};

export default function Home() {
  const { props } = usePage<{ featured: Product[] }>();
  const featured = props.featured || [];

  return (
    <AppLayout>
      <div className="p-6 max-w-5xl mx-auto">
        <h2 className="text-xl font-semibold mb-3">Featured products</h2>
        <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
          {featured.map(p => (
            <a key={p.id} href={p.url} target="_blank" rel="noreferrer"
              className="border rounded p-4 hover:shadow">
              <div className="text-sm text-gray-500">{p.category} • {p.brand}</div>
              <div className="font-medium">{p.name}</div>
              <div className="mt-2">
                {new Intl.NumberFormat('en-US', {
                  style: 'currency',
                  currency: 'USD',
                }).format(Number(p.price))}
              </div>
            </a>
          ))}
        </div>

        <div className="mt-8">
          <Link href="/chat" className="px-4 py-2 bg-black text-white rounded">
            Open AI Chat Support
          </Link>
        </div>
      </div>
    </AppLayout>
  );
}
