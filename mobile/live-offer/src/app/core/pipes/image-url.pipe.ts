import { Pipe, PipeTransform } from '@angular/core';
import { environment } from '../../../environments/environment';

/**
 * Resolves a raw media path (image/logo/preview) to a full URL string.
 * - Absolute URLs (http/https) and data: URLs pass through.
 * - Bare paths are prefixed with mediaUrl + "/storage/".
 * - null/empty → '' (template can show a placeholder).
 *
 * Returns a plain string (not SafeUrl) so it works with ion-img's
 * shadow-DOM <img>. Angular sanitizes [src] string bindings.
 */
@Pipe({ name: 'imageUrl' })
export class ImageUrlPipe implements PipeTransform {
  transform(value?: string | null): string {
    if (!value) return '';
    if (/^(https?:|data:|assets\/)/i.test(value)) {
      return value;
    }
    const base = environment.mediaUrl.replace(/\/$/, '');
    const path = value.replace(/^\//, '');
    return `${base}/storage/${path}`;
  }
}
