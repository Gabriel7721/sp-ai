const BRANDS = ['Apple','Samsung','Sony','Google','Philips','Breville','Zojirushi','Lodge','Anker',
                "O'Reilly",'Pearson','Manning','Independent','php[architect]'];
const CATS = ['Electronics','Kitchen','Books'];
const PRICES = ['under 50', '50-100', '100-300', '300-600', '600-1200'];

export default function QuickFilters({ onPick }: { onPick: (text:string)=>void }) {
  return (
    <div className="flex flex-wrap gap-2">
      {CATS.map(c => (
        <button key={c} onClick={() => onPick(`Tôi muốn ${c}`)}
          className="px-3 py-1 text-xs rounded-full border bg-white hover:bg-gray-50">{c}</button>
      ))}
      {BRANDS.slice(0,8).map(b => (
        <button key={b} onClick={() => onPick(`Tìm ${b}`)}
          className="px-3 py-1 text-xs rounded-full border bg-white hover:bg-gray-50">{b}</button>
      ))}
      {PRICES.map(p => (
        <button key={p} onClick={() => onPick(`tầm giá ${p}`)}
          className="px-3 py-1 text-xs rounded-full border bg-white hover:bg-gray-50">{p}</button>
      ))}
    </div>
  );
}
