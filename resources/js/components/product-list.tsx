import { Product } from '../types';

export default function ProductList({ items }: { items: Product[] }) {
  if (!items?.length) return null;
  return (
    <div className="mt-4 border rounded-xl bg-white">
      <div className="px-4 py-2 border-b font-semibold">Sản phẩm khớp tìm kiếm</div>
      <ul className="divide-y">
        {items.map(p => (
          <li key={p.id} className="p-4 flex items-start justify-between gap-4">
            <div>
              <div className="text-sm text-gray-500">{p.category} • {p.brand}</div>
              <div className="font-medium">{p.name}</div>
              <div className="text-sm mt-1">{new Intl.NumberFormat('en-US', {
                    style: 'currency',
                    currency: 'USD',
                  })
                  .format(Number(p.price))} {p.currency}
              </div>
              {p.attributes ? (
                <div className="text-xs text-gray-600 mt-1">
                  {Object.entries(p.attributes).slice(0,3).map(([k,v]) => (
                    <span key={k} className="mr-3">{k}: {String(v)}</span>
                  ))}
                </div>
              ) : null}
            </div>
            <div className="shrink-0">
              <a href={p.url} target="_blank" rel="noreferrer"
                 className="px-3 py-2 text-sm rounded bg-black text-white">
                Xem
              </a>
            </div>
          </li>
        ))}
      </ul>
    </div>
  );
}
