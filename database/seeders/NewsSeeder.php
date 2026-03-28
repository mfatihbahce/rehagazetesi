<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\News;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NewsSeeder extends Seeder
{
    private const NEWS_PER_CATEGORY = 10;

    private array $titlesByCategory = [
        'gundem' => [
            'Mevsimlik Tarım İşçilerine Sosyal Destek Paketi',
            'Yerel Seçimlerde Son Durum',
            'Şehir Meclisi Toplantısı Yapıldı',
            'Valilik Açıklaması: Altyapı Yatırımları',
            'Belediye Bütçesi Görüşüldü',
            'Köylerde İnternet Altyapısı Tamamlandı',
            'Çevre Düzenlemesi Projesi Onaylandı',
            'Kültür ve Turizm Bakanı İlçemizi Ziyaret Etti',
            'Deprem Bölgesinde Yeni Konutlar Teslim Edildi',
            'Yerel Sanayi Zirvesi Düzenlendi',
        ],
        'ekonomi' => [
            'Ekonomide Yeni Vergi Düzenlemesi',
            'Merkez Bankası Faiz Kararı Açıklandı',
            'Yerel Sanayi İhracat Rekoru Kırdı',
            'KOBİ\'lere Uygun Kredi Desteği',
            'Enflasyon Verileri Açıklandı',
            'Borsa Haftalık Değerlendirme',
            'Döviz Piyasasında Son Gelişmeler',
            'Tarım Kredilerinde Erteleme',
            'Yatırım Teşvikleri Genişletildi',
            'Yerel Esnaf Odası Açıklaması',
        ],
        'spor' => [
            'Futbol Takımımız Şampiyonlar Ligi\'ne Katıldı',
            'Basketbol Ligi\'nde Kritik Maç',
            'Yerel Spor Kulübü Yeni Transferi Duyurdu',
            'Voleybol Takımı Play-off\'a Yükseldi',
            'Atletizm Şampiyonası Sonuçlandı',
            'Gençlik ve Spor Bakanlığı Projesi',
            'Yüzme Havuzu Yenilendi',
            'Futbol Federasyonu Yerel Lig Kararı',
            'Sporcu Bursları Açıklandı',
            'Okul Sporları Final Maçları',
        ],
        'kultur-sanat' => [
            'Yeni Kütüphane Hizmete Açıldı',
            'Yerel Tiyatro Sezonu Açılışı',
            'Kitap Fuarı Düzenlendi',
            'Müze Ziyaretçi Rekoru Kırdı',
            'Sinema Festivali Başlıyor',
            'Geleneksel El Sanatları Sergisi',
            'Konser Sezonu Programı Açıklandı',
            'Çocuk Tiyatrosu Ücretsiz Gösterimde',
            'Yerel Yazar Ödül Aldı',
            'Fotoğraf Yarışması Sonuçları',
        ],
        'saglik' => [
            'Sağlık Bakanlığı Aşı Kampanyası Başlattı',
            'Yeni Hastane Binası Temeli Atıldı',
            'Aile Hekimliği Randevu Sistemi Güncellendi',
            'Kanser Tarama Programı Genişletildi',
            '112 Acil Hatları Güçlendirildi',
            'Ruh Sağlığı Destek Hattı Açıldı',
            'Sağlık Çalışanlarına Ek Ödeme',
            'Diyabet Farkındalık Etkinliği',
            'Evde Sağlık Hizmeti Yaygınlaştı',
            'Eczane Nöbet Listesi Güncellendi',
        ],
        'egitim' => [
            'Okullarda Tablet Dağıtımı Başladı',
            'Yeni Okul Binası Açıldı',
            'Öğretmen Atamaları Yapıldı',
            'Üniversite Sınav Sonuçları Açıklandı',
            'Meslek Lisesi Proje Yarışması',
            'Okul Öncesi Eğitim Yaygınlaşıyor',
            'Burs Başvuruları Başladı',
            'Eğitim Bakanı İlçemizi Ziyaret Etti',
            'STEM Eğitimi Projesi Tamamlandı',
            'Kütüphanesiz Okul Kalmayacak',
        ],
        'teknoloji' => [
            'Yapay Zeka Destekli Sürücüsüz Otobüs Denemesi',
            'Akıllı Şehir Uygulaması Devreye Alındı',
            'İnternet Hızı Artırıldı',
            'Siber Güvenlik Eğitimi Verildi',
            'Start-up Yarışması Düzenlendi',
            'E-Devlet Yeni Hizmetler Eklendi',
            '5G Altyapı Çalışmaları Sürüyor',
            'Yazılım Geliştirici Bootcamp Başlıyor',
            'Veri Merkezi Açıldı',
            'Dijital Okur-yazarlık Kursu',
        ],
        'yerel' => [
            'Şehrimizde Yeni Park Projesi Başladı',
            'Trafik Düzenlemesi Yapıldı',
            'Belediye Otobüs Hatları Güncellendi',
            'Çarşı Düzenlemesi Tamamlandı',
            'Kent Ormanı Açıldı',
            'Toplu Taşıma Saatleri Değişti',
            'Park ve Bahçeler Yenilendi',
            'Yaya Kaldırımları Genişletildi',
            'Çöp Toplama Saatleri Duyuruldu',
            'Şehir Merkezi Dekorasyonu',
        ],
    ];

    public function run(): void
    {
        News::query()->delete();

        $categories = Category::all();
        $editors = User::where('role', 'editor')->get();
        $admin = User::where('role', 'admin')->first();

        if ($editors->isEmpty() || !$admin) {
            return;
        }

        $usedSlugs = [];

        foreach ($categories as $category) {
            $slugKey = $category->slug;
            $titles = $this->titlesByCategory[$slugKey] ?? $this->generateGenericTitles($category->name, self::NEWS_PER_CATEGORY);

            for ($i = 0; $i < self::NEWS_PER_CATEGORY; $i++) {
                $title = is_array($titles) ? ($titles[$i] ?? $category->name . ' haber ' . ($i + 1)) : $category->name . ' haber ' . ($i + 1);
                $slug = Str::slug($title);
                $baseSlug = $slug;
                $counter = 0;
                while (in_array($slug, $usedSlugs)) {
                    $counter++;
                    $slug = $baseSlug . '-' . $counter;
                }
                $usedSlugs[] = $slug;

                $author = $editors->random();
                if ($i === 0) {
                    $author = $admin;
                }

                $isFirst = ($i === 0);
                $isSecond = ($i === 1);

                News::create([
                    'title' => $title,
                    'slug' => $slug,
                    'excerpt' => $this->excerptFor($title, $category->name),
                    'content' => $this->contentFor($title, $category->name),
                    'category_id' => $category->id,
                    'user_id' => $author->id,
                    'status' => 'published',
                    'tags' => $category->slug . ', yerel, haber',
                    'is_breaking' => $isFirst && in_array($slugKey, ['gundem', 'ekonomi']),
                    'is_featured' => $isFirst || $isSecond,
                    'views' => rand(200, 5500),
                    'published_at' => now()->subDays(rand(0, 30)),
                ]);
            }
        }
    }

    private function excerptFor(string $title, string $categoryName): string
    {
        $excerpts = [
            'İlgili açıklama yapıldı. Detaylar belli oldu.',
            'Konuya dair gelişmeler yaşandı. Yetkililer bilgi verdi.',
            'Süreç devam ediyor. Vatandaşlar bilgilendirildi.',
            'Karar sonrası açıklama yapıldı.',
            'Proje kapsamında çalışmalar sürüyor.',
        ];
        return ($excerpts[array_rand($excerpts)] ?? $excerpts[0]) . ' ' . $categoryName . ' kategorisinde güncel gelişme.';
    }

    private function contentFor(string $title, string $categoryName): string
    {
        $p1 = '<p>' . $title . ' başlıklı habere ilişkin yetkililerden açıklama geldi. Konuyla ilgili çalışmaların sürdüğü belirtildi.</p>';
        $p2 = '<p>' . $categoryName . ' kapsamında yapılan değerlendirmeler sonucunda yeni adımlar atılacağı ifade edildi. Vatandaşlarımız bilgilendirilmeye devam edecek.</p>';
        $p3 = '<p>İlgili kurum ve kuruluşlar konuya dair açıklama yapmaya devam edecek. Gelişmeler anlık olarak paylaşılacaktır.</p>';
        return $p1 . $p2 . $p3;
    }

    private function generateGenericTitles(string $categoryName, int $count): array
    {
        $titles = [];
        for ($i = 1; $i <= $count; $i++) {
            $titles[] = $categoryName . ' kapsamında gelişme ' . $i;
        }
        return $titles;
    }
}
