import { inject, Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { Observable, map } from 'rxjs';
import {
  ApiEnvelope,
  Category,
  LiveStream,
  Offer,
  Paginated,
  Store,
  ViewerToken,
} from '../models/api.types';

/**
 * Thin typed wrapper over the public (no-auth) API.
 * Routes are relative; apiBaseUrlInterceptor prepends the base URL.
 */
@Injectable({ providedIn: 'root' })
export class PublicApiService {
  private readonly http = inject(HttpClient);

  /** GET /public/categories */
  categories(): Observable<Category[]> {
    return this.unwrap<Category[]>('public/categories');
  }

  /** GET /public/offers (paginated). */
  offers(filters: {
    category_id?: number;
    subcategory_id?: number;
    store_id?: number;
    featured?: boolean;
    search?: string;
    page?: number;
  } = {}): Observable<Paginated<Offer>> {
    let params = new HttpParams();
    for (const [key, value] of Object.entries(filters)) {
      if (value === undefined || value === null) continue;
      params = params.set(key, String(value));
    }
    return this.unwrap<Paginated<Offer>>('public/offers', params);
  }

  /** GET /public/offers/{id} */
  offer(id: number | string): Observable<Offer> {
    return this.unwrap<Offer>(`public/offers/${id}`);
  }

  /** GET /public/stores */
  stores(): Observable<Store[]> {
    return this.unwrap<Store[]>('public/stores');
  }

  /** GET /public/stores/{id} */
  store(id: number | string): Observable<Store> {
    return this.unwrap<Store>(`public/stores/${id}`);
  }

  /** GET /public/live-streams */
  liveStreams(): Observable<LiveStream[]> {
    return this.unwrap<LiveStream[]>('public/live-streams');
  }

  /** GET /public/live-streams/{id} */
  liveStream(id: number | string): Observable<LiveStream> {
    return this.unwrap<LiveStream>(`public/live-streams/${id}`);
  }

  /** POST /public/live-streams/{id}/viewer-token — fresh subscriber token + app_id. */
  viewerToken(id: number | string): Observable<ViewerToken> {
    return this.http
      .post<ApiEnvelope<ViewerToken>>(`public/live-streams/${id}/viewer-token`, {})
      .pipe(map((res) => res.data));
  }

  private unwrap<T>(path: string, params?: HttpParams): Observable<T> {
    return this.http
      .get<ApiEnvelope<T>>(path, { params })
      .pipe(map((res) => res.data));
  }
}
