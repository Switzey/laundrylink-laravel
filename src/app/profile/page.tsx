import type { Metadata } from "next";
import { updateProfileAction } from "@/app/actions/account";
import { requireUser } from "@/lib/auth";

export const metadata: Metadata = { title: "Profile" };

export default async function ProfilePage({ searchParams }: { searchParams: Promise<{ success?: string; error?: string }> }) {
  const user = await requireUser();
  const message = await searchParams;
  return <div className="page"><div className="page-header"><div><p className="eyebrow">Account settings</p><h1 className="page-title">Your profile</h1><p className="page-copy">Keep your contact details accurate for account and delivery communication.</p></div></div>{message.success && <p className="mx-auto mb-5 max-w-2xl rounded-md border border-teal-200 bg-teal-50 p-3 text-sm text-teal-700">{message.success.replaceAll("+", " ")}</p>}{message.error && <p className="mx-auto mb-5 max-w-2xl rounded-md border border-rose-200 bg-rose-50 p-3 text-sm text-rose-700">{message.error.replaceAll("+", " ")}</p>}<form action={updateProfileAction} className="surface mx-auto max-w-2xl p-6 sm:p-8"><div className="form-grid"><label><span className="field-label">Full name</span><input className="field" name="name" defaultValue={user.name} required /></label><label><span className="field-label">Email address</span><input className="field bg-zinc-50" defaultValue={user.email} disabled /></label><label><span className="field-label">Phone</span><input className="field" name="phone" type="tel" defaultValue={user.phone ?? ""} /></label><label><span className="field-label">Primary address</span><input className="field" name="address" defaultValue={user.address ?? ""} /></label><button className="btn-primary" type="submit">Save profile</button></div></form></div>;
}
