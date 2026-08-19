import { HttpInterceptorFn } from '@angular/common/http';
import { environment } from '../../../environments/environment';

/**
 * Prefixes relative API URLs with the configured backend base URL.
 * Absolute URLs (http/https) pass through untouched.
 */
export const apiBaseUrlInterceptor: HttpInterceptorFn = (req, next) => {
  if (/^https?:\/\//i.test(req.url)) {
    return next(req);
  }
  const cloned = req.clone({
    url: `${environment.apiUrl}/${req.url.replace(/^\//, '')}`,
  });
  return next(cloned);
};
