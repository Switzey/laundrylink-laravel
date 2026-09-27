import Link from "next/link";
import { BrandLogo } from "@/components/brand-logo";

export function SiteFooter() {
  return (
    <footer className="mt-auto border-t border-zinc-200 bg-white">
      <div className="shell flex flex-col gap-5 py-8 sm:flex-row sm:items-center sm:justify-between">
        <div><BrandLogo compact /><p className="mt-2 text-sm text-zinc-500">Clean. Reliable. Convenient. Connected.</p></div>
        <div className="flex gap-5 text-sm font-medium text-zinc-600">
          <Link href="/cleaners" className="hover:text-brand-blue">Cleaners</Link>
          <Link href="/register" className="hover:text-brand-blue">Join LaundryLink</Link>
          <Link href="/login" className="hover:text-brand-blue">Sign in</Link>
        </div>
      </div>
    </footer>
  );
}
