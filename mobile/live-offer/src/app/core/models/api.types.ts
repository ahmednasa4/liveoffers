// API contract types — mirror the Laravel public endpoints exactly.
// See CONTEXT.md §7.2. All snake_case (Laravel default).

/** Standard envelope for every public endpoint. */
export interface ApiEnvelope<T> {
  success: boolean;
  data: T;
  message?: string;
}

/** Laravel paginator object (returned as the `data` of /public/offers). */
export interface Paginated<T> {
  data: T[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
  from: number | null;
  to: number | null;
  next_page_url: string | null;
  prev_page_url: string | null;
  path: string;
  first_page_url: string;
  last_page_url: string;
  links: { url: string | null; label: string; active: boolean }[];
}

export interface Subcategory {
  id: number;
  category_id: number;
  name: string;
  is_active: boolean;
  created_at: string;
  updated_at: string;
}

export interface Category {
  id: number;
  name: string;
  icon: string | null;
  sort_order: number;
  is_active: boolean;
  created_at: string;
  updated_at: string;
  subcategories: Subcategory[];
}

export interface OfferStoreBrief {
  id: number;
  name: string;
  logo: string | null;
  phone?: string;
  whatsapp_number?: string | null;
  address?: string;
}

export interface OfferCategoryBrief {
  id: number;
  name: string;
}

export interface Offer {
  id: number;
  store_id: number;
  category_id: number;
  subcategory_id: number | null;
  title: string;
  description: string;
  original_price: string; // decimal cast → string
  offer_price: string; // decimal cast → string
  image: string | null;
  is_active: boolean;
  is_featured: boolean;
  is_ai_generated: boolean;
  view_count: number;
  start_date: string;
  end_date: string;
  created_at: string;
  updated_at: string;
  store?: OfferStoreBrief;
  category?: OfferCategoryBrief;
  subcategory?: OfferCategoryBrief | null;
}

export interface LiveStream {
  id: number;
  store_id: number;
  channel_name: string;
  agora_token: string;
  app_id?: string;
  preview_image: string | null;
  max_viewers: number;
  is_active: boolean;
  started_at: string;
  ended_at: string | null;
  created_at: string;
  updated_at: string;
  store?: OfferStoreBrief;
}

/** Fresh per-join viewer (subscriber) token from /public/live-streams/{id}/viewer-token. */
export interface ViewerToken {
  token: string;
  app_id: string;
  channel_name: string;
  uid: number;
}

export interface Store {
  id: number;
  owner_id: number;
  name: string;
  description: string | null;
  logo: string | null;
  address: string;
  latitude: string | null; // decimal cast → string
  longitude: string | null; // decimal cast → string
  phone: string;
  whatsapp_number: string | null;
  is_active: boolean;
  created_at: string;
  updated_at: string;
  offers_count?: number; // only on /public/stores list (withCount)
  offers?: Offer[]; // only on /public/stores/{id}
}
