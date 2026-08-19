import { Pipe, PipeTransform } from '@angular/core';

/**
 * Computes discount percentage from original/offer prices (API returns strings).
 * Usage: {{ offer.original_price | discount: offer.offer_price }} → "50%"
 */
@Pipe({ name: 'discount' })
export class DiscountPipe implements PipeTransform {
  transform(original: string | number | null, offer?: string | number | null): string {
    const o = Number(original);
    const p = Number(offer);
    if (!o || o <= 0 || isNaN(p) || p >= o) return '0%';
    const pct = Math.round(((o - p) / o) * 100);
    return `${pct}%`;
  }
}
