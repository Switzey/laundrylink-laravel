export type Role = "customer" | "cleaner" | "admin";

export interface User {
  id: number;
  name: string;
  email: string;
  role: Role;
  phone: string | null;
  address: string | null;
}

export interface Cleaner {
  id: number;
  user_id: number | null;
  business_name: string;
  description: string | null;
  address: string;
  city: string;
  phone: string;
  rating: number;
  turnaround_time: string | null;
  opening_hours: string | null;
  is_available: number;
  is_approved: number;
  services_count?: number;
  reviews_count?: number;
}

export interface Service {
  id: number;
  cleaner_id: number;
  name: string;
  description: string | null;
  price: number;
  unit: string;
  is_active: number;
}

export interface OrderSummary {
  id: number;
  customer_id: number | null;
  cleaner_id: number;
  status: string;
  subtotal: number;
  total: number;
  payment_status: string;
  pickup_date: string | null;
  pickup_time_window: string | null;
  delivery_date: string | null;
  delivery_time_window: string | null;
  created_at: string | null;
  business_name: string;
  customer_name?: string | null;
}

