# Haber Kartı Standardı

Sitede kullanılan tüm haber kartları **tek bir component** (`x-news-card`) ile sunulur. Tutarlı görünüm ve bakım için bu standarda uyulur.

## Yapı

| Bölüm    | Kurallar |
|----------|----------|
| Görsel   | Oran 16:10 (`aspect-video`), `object-cover`, `loading="lazy"` |
| Kategori | `text-xs`, uppercase, gri (`text-gray-500`), başlığın hemen üstünde |
| Başlık   | `font-bold`, siyah, en fazla 2 satır (`line-clamp-2`), hover’da kırmızı |

## Kullanım

```blade
{{-- Varsayılan (sadece kategori + başlık) --}}
<x-news-card :news="$news" />

{{-- Tarih ve okunma sayısı ile --}}
<x-news-card :news="$news" variant="withMeta" />

{{-- Sadece tarih --}}
<x-news-card :news="$news" :showDate="true" />

{{-- Kompakt (sidebar, tek satır başlık) --}}
<x-news-card :news="$news" variant="compact" />
```

## Varyantlar

- **default**: Görsel + kategori + başlık (2 satır).
- **withMeta**: default + alt kısımda tarih ve okunma sayısı.
- **compact**: Görsel + kategori + başlık (1 satır).

## Grid kuralları

- Ana sayfa / kategori listesi: `grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6`
- Kartlar her zaman aynı component ile kullanılır; sayfa bazında sadece grid sütun sayısı değişebilir.
