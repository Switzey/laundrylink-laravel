import Link from "next/link";
import { Clock3, MapPin, Star } from "lucide-react";
import type { Cleaner } from "@/lib/types";

export function CleanerCard({ cleaner }: { cleaner: Cleaner }) {
  return (
    <article className="surface flex h-full flex-col overflow-hidden">
      <div className="h-1.5 bg-[linear-gradient(90deg,#2563eb,#60a5fa,#14b8a6)]" />
      <div className="flex flex-1 flex-col p-5">
        <div className="flex items-start justify-between gap-4">
          <div><p className="text-xs font-bold uppercase text-brand-teal">Verified cleaner</p><h2 className="mt-1 text-xl font-bold text-brand-navy">{cleaner.business_name}</h2></div>
          <span className="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-1 text-sm font-bold text-amber-700"><Star size={15} fill="currentColor" />{Number(cleaner.rating).toFixed(1)}</span>
        </div>
        <p className="mt-3 line-clamp-2 text-sm leading-6 text-zinc-600">{cleaner.description || "Professional laundry care with reliable pickup and delivery."}</p>
        <div className="mt-4 grid gap-2 text-sm text-zinc-600">
          <span className="flex items-center gap-2"><MapPin size={16} className="text-brand-blue" />{cleaner.city}</span>
          <span className="flex items-center gap-2"><Clock3 size={16} className="text-brand-teal" />{cleaner.turnaround_time || "Flexible turnaround"}</span>
        </div>
        <div className="mt-5 flex items-center justify-between border-t border-zinc-100 pt-4">
          <span className="text-xs font-semibold text-zinc-500">{cleaner.services_count ?? 0} services</span>
          <Link href={`/cleaners/${cleaner.id}`} className="btn-primary">View cleaner</Link>
        </div>
      </div>
    </article>
  );
}

