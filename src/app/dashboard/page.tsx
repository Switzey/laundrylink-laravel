import { redirect } from "next/navigation";
import { dashboardForRole, requireUser } from "@/lib/auth";

export default async function DashboardPage() {
  const user = await requireUser();
  redirect(dashboardForRole(user.role));
}

