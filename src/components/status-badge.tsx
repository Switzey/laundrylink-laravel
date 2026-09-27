import { humanize } from "@/lib/format";

const colors: Record<string, string> = {
  pending: "border-amber-200 bg-amber-50 text-amber-800",
  accepted: "border-blue-200 bg-blue-50 text-blue-700",
  picked_up: "border-sky-200 bg-sky-50 text-sky-700",
  in_cleaning: "border-violet-200 bg-violet-50 text-violet-700",
  ready: "border-teal-200 bg-teal-50 text-teal-700",
  out_for_delivery: "border-cyan-200 bg-cyan-50 text-cyan-700",
  completed: "border-emerald-200 bg-emerald-50 text-emerald-700",
  cancelled: "border-rose-200 bg-rose-50 text-rose-700",
  paid: "border-emerald-200 bg-emerald-50 text-emerald-700",
  unpaid: "border-zinc-200 bg-zinc-50 text-zinc-700",
};

export function StatusBadge({ status }: { status: string }) {
  return <span className={`badge ${colors[status] ?? "border-zinc-200 bg-zinc-50 text-zinc-700"}`}>{humanize(status)}</span>;
}

