# Reha Gazetesi - Yerel Haber Portalı

Laravel framework kullanılarak geliştirilmiş modern, ölçeklenebilir ve profesyonel yerel haber portalı.

## Gereksinimler

- PHP 8.1+
- Composer
- MySQL 5.7+
- XAMPP (veya Apache + PHP + MySQL)

## Kurulum

1. **Veritabanı**: phpMyAdmin'de `rehagazetesi` adında veritabanı oluşturun (zaten varsa atlayın).

2. **Ortam Ayarları**: `.env` dosyasındaki veritabanı ayarlarını kontrol edin:
   ```
   DB_DATABASE=rehagazetesi
   DB_USERNAME=root
   DB_PASSWORD=
   ```

3. **URL Ayarları**: XAMPP ile çalışıyorsanız `.env` içinde:
   ```
   APP_URL=http://localhost/projeler/rehagazetesi/public
   ```

4. **Migration ve Seeder**:
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **Storage Link** (görsel yüklemeleri için):
   ```bash
   php artisan storage:link
   ```

## Varsayılan Giriş Bilgileri

| Rol | E-posta | Şifre |
|-----|---------|-------|
| Admin | admin@rehagazetesi.com | password |
| Editör | ahmet@rehagazetesi.com | password |
| Editör | ayse@rehagazetesi.com | password |
| Editör | mehmet@rehagazetesi.com | password |

## Erişim Adresleri

- **Ana Sayfa**: `http://localhost/projeler/rehagazetesi/public`
- **Admin Panel**: `http://localhost/projeler/rehagazetesi/public/admin`
- **Admin Giriş**: `http://localhost/projeler/rehagazetesi/public/admin/giris`

## Özellikler

### Frontend
- Ana sayfa: Manşet slider, kategori bazlı haber blokları, en çok okunanlar
- Son dakika (breaking news) kayan haber şeridi
- Haber detay sayfası (SEO uyumlu, Open Graph)
- Kategori sayfaları
- Editör profilleri ve haberleri
- Arama fonksiyonu
- Responsive tasarım

### Admin Panel
- Dashboard (istatistikler, son haberler, popüler haberler)
- Haber CRUD (ekleme, düzenleme, silme)
- Haber onay/red mekanizması (editör haberleri admin onayına tabi)
- Kategori yönetimi
- Editör listesi
- Rol bazlı erişim (admin / editör)

### Teknik
- MVC mimarisi
- Repository Pattern
- Service katmanı
- Form Request validation
- Blade components
