# Eski Veritabanı Şeması — Kapsamlı Analiz Raporu

> Kaynak: `database/migrations_old/` (tenant / lessor / describing + kök dosyalar)
> Tarih: 2026-07-06 · Amaç: Enterprise seviyede Laravel yeniden tasarımına girdi sağlamak. Bu rapor **analiz** içerir, geliştirme yapılmamıştır.

---

## 1. Yönetici Özeti

Eski şema, **üç ayrı veritabanı bağlamına** bölünmüş, OpenCart'tan esinlenmiş, Türkiye odaklı (e-fatura, MERSİS, KEP, ÖTV alanları içeren) çok kiracılı (multi-tenant) bir e-ticaret/ERP SaaS platformudur:

| Bağlam | Dosya | Tablo | Rol |
|---|---|---|---|
| `tenant/` | 20 | **235** | Kiracı başına iş veritabanı (e-ticaret + muhasebe + CMS + destek) |
| `lessor/` | 15 | **146** | Platform sahibi ("landlord") veritabanı — `Schema::connection('lessor')` |
| `describing/` | 12 | **67** | Küresel tanım/metadata veritabanı — `Schema::connection('describing')` |
| kök | 3 | ~7 | Stok Laravel (users, cache, jobs) |
| **Toplam** | 50 | **~455** | |

**En kritik bulgular:**

1. **Sıfır foreign key** — 455 tabloda tek bir `->foreign()` yok. Tüm ilişkiler `integer('x_id')->default(0)` (nullable yerine `0` sentinel). Referans bütünlüğü tamamen uygulama katmanına bırakılmış.
2. **Index yok denecek kadar az** — ~450 tabloda yalnızca ~80 index; `order.account_id`, `order_item.order_id`, tüm `*_translation.entity_id` ve `language_code` sütunları indexsiz. Ölçekte full-scan garantili.
3. **`utf8` (utf8mb3) + `utf8_general_ci`** — emoji/4-byte desteği yok; `general_ci` Türkçe noktalı/noktasız i'yi yanlış sıralar. Hiçbir yerde `utf8mb4` yok.
4. **Devasa şema tekrarı** — lessor şeması tenant şemasının ~%80 kopyası; `dfntn_*` tanım tabloları üç bağlamda (tenant/lessor/describing) üçüncü kopyaya kadar birebir tekrarlanıyor (`dfntn_spprt_priority` 3 kez, vb.). Tek kaynak-of-truth yok, elle senkron.
5. **`down()` metotları sistematik olarak bozuk** — lessor ve describing'de TÜM `down()`'lar bağlantı belirtmeden (`Schema::` düz) çalışıyor → rollback yanlış DB'yi hedefler. Ayrıca hiç yaratılmamış tabloları düşüren / yaratılmış tabloları düşürmeyen çok sayıda dosya var (aşağıda liste).
6. **Laravel konvansiyonlarının tamamen dışında** — `id()` yerine `bigIncrements('entity_id')`, `timestamps()` yerine nullable olmayan/defaultsuz `date_modified`/`date_created` (MySQL strict modda INSERT patlatır), `softDeletes()` hiç yok, boolean yerine `tinyInteger`, JSON yerine serialized `text`/`longText` bloblar.

**Sonuç:** Bu şema olduğu gibi taşınamaz; **yeniden tasarım** (re-design) gerektirir, birebir port değil. Bölüm 7'de hedef mimari önerileri, Bölüm 8'de fazlı yol haritası var.

---

## 2. Mimari Genel Bakış

### 2.1 Üç veritabanı modeli

- **tenant** — her kiracının kendi DB'si (database-per-tenant multi-tenancy). Varsayılan bağlantı üzerinden `Schema::create()`.
- **lessor** — platformun kendi ticari sistemi. Kiracılar burada "müşteri"dir: `wbst_account` (kiracı firma) + `wbst_account_access` (kiracı provizyonu: subdomain, DB/host/storage kimlik bilgileri, tema, varsayılan dil/para birimi) + `accntng_recurring` (abonelik) + `ctlg_product` (satılan planlar/eklentiler) üçlüsü abonelik faturalamasını yürütür. Ek olarak `prtnr_*` bayi/partner dizini içerir.
- **describing** — tüm platformun paylaştığı, seed edilen referans/master-data katmanı: uygulama mağazası kataloğu (`dfntn_app_*`), tema/tasarım sistemi (`dfntn_dsgn_*`), eklenti kayıt defteri (`dfntn_xtnsn_*`), modül kayıt defteri (`dfntn_systm_component`), banka/kargo/dil/para birimi listeleri.

### 2.2 Modül (domain) haritası

**Tenant tarafı:**

| Prefix | Domain | Öne çıkan tablolar |
|---|---|---|
| `wbst_account*` | Müşteri/Hesap (B2B+B2C) | `wbst_account` (vergi no, MERSİS, KEP), authorized (alt kullanıcı), contact, bank_account, wishlist, reward, token_access (Sanctum benzeri) |
| `ctlg_` | Katalog | `ctlg_product` (~90 sütun!), 22 product uydusu, variant/stock, option/attribute/filter (EAV), category, brand, warehouse, review |
| `accntng_` | Muhasebe/Satış/İK | cart → order → shipment → invoice → restitute (iade) → recurring (abonelik) → demand (teklif); kasa (`safe`), kredi, çek; personel + bordro |
| `mrktng_` | Pazarlama | campaign, coupon (ürün/kategori/marka/grup kapsamlı), gift_voucher |
| `mrktplc_` | Pazaryeri entegrasyonu | Trendyol/HB tarzı kanal eşleştirme (`pairing_*`), serialized response bloblarıyla |
| `spply_` | Tedarik | genel kaynak↔hedef eşleştirme köprüsü |
| `spprt_` | Destek | FAQ, bilgi bankası, ticket + meeting (thread), feedback |
| `wbst_` | Website/CMS | page, layout+widget, banner, menu, SEO url yönlendirme, form + form_incoming |
| `blg_` | Blog | category, post (+media, ilişkili ürün), comment |
| `systm_` | Ayarlar | setting/company/application/variable — option/slug/value + `serialized` bayraklı KV depoları |
| `dfntn_*` | Tanım/lookup katmanı | 84 tablo: lokalizasyon (dil, para, ülke/il/ilçe, vergi), katalog sözlükleri, sipariş/iade durumları, destek sözlükleri, depo topolojisi (block→area→shelf), form elemanları |

**Lessor'a özgü:** `wbst_account_access` (kiracı altyapı provizyonu), `prtnr_*` (partner dizini), `systm_user*` (platform back-office personeli). Gerisi tenant'ın kopyası; tenant'taki blog/marketplace/supply/form/repository lessor'da yok.

**Describing'e özgü:** `dfntn_app_*` (uygulama mağazası, 10 tablo), `dfntn_dsgn_*` (tema/cihaz/pozisyon/blok, 12 tablo), `dfntn_xtnsn_*` (method/plugin/widget registry), `dfntn_systm_component` (modül/route/controller metadata), `dfntn_mrktplc_category`, `dfntn_gnrl_account_sector`.

### 2.3 İlişki grafı (çıkarımsal — DB'de zorlanmıyor)

Merkez hub'lar: `account_id` (~20 tablo referanslar), `product_id` (~30 tablo), `order_id` (~12 tablo), `invoice_id`, `category_id`, `brand_id`, `variant_stock_id`, `layout_id`. Lokalizasyon referansları **string kod** üzerinden: `language_code`, `currency_code` (id değil). Polimorfik-vari referanslar tip belirtilmeden int üçlüsüyle: `module_group_id`/`module_type_id`/`module_id` (`accntng_safe_activity`).

---

## 3. Gözlemlenen Şema Konvansiyonları (mevcut hali)

- **PK:** `bigIncrements('<entity>_id')` — `id()` değil. Tutarsızlıklar: `systm_company` → `definition_id`, `systm_application` → `application_value_id`, `ctlg_product_variant_variable` → `product_stock_variant_id`, `dfntn_app_application_campaign` → `application_related_id` (copy-paste hatası).
- **Zaman damgaları:** her tabloda `timestamp('date_modified', 0)` + `timestamp('date_created', 0)` — nullable değil, default yok, `useCurrent()` yok. Bir tabloda `date_Created` (büyük C) yazım hatası (`dfntn_spprt_ticket_status`).
- **Charset:** her tabloda satır içi `engine=InnoDB; charset=utf8; collation=utf8_general_ci` (config yerine elle, 444 kez).
- **Çeviri deseni:** her çevrilebilir `X` için `X_translation` (PK `X_translation_id`, `X_id`, `language_code`, name/description/meta_*). ~110+ çeviri tablosu — şemanın en baskın deseni.
- **Pivot deseni:** `x_y` tabloları surrogate PK + iki int FK + tarih sütunu taşır; **çift üzerinde unique constraint yok** → mükerrer ilişki mümkün.
- **EAV:** katalog attribute/option/filter üçlüsü tanım (dfntn) → değer (ctlg_product_*) şeklinde EAV kurar.
- **Serialized bloblar:** `systm_*.value/setting`, `order_item.options/variants/groupeds/bundles`, `mrktplc_*.matched_*`, `layout_widget.widgets`, `permission` — hiçbir yerde native `json()` yok.
- **Bayraklar:** `tinyInteger('status')->default(0)` her yerde; üründe 4 ayrı `status_*`; `enum()` yalnızca birkaç yerde (gender, frequency, warranty) — tutarsız.
- **Para:** tutarlar `decimal(19,2)` (tutarlı, iyi), kur `double(15,8)`, vergi oranı `decimal(15,4)`.

---

## 4. Anti-Pattern ve Kusur Envanteri (öncelik sıralı)

### KRİTİK (veri bütünlüğü / çalışmayı engeller)

| # | Sorun | Kanıt / Kapsam |
|---|---|---|
| K1 | Foreign key yok | 455 tabloda 0 `->foreign()`; tek `foreignId` stok `sessions` tablosunda |
| K2 | FK sütun tipi uyumsuz | PK'lar `bigIncrements` (UNSIGNED BIGINT), referans sütunları `integer` (SIGNED INT) `->default(0)` — FK eklemek için bile tip migrasyonu gerekir |
| K3 | Nullable olmayan, defaultsuz timestamp'ler | MySQL strict / `NO_ZERO_DATE` altında INSERT hatası |
| K4 | `down()` yanlış bağlantı | lessor + describing'in **tüm** dosyalarında `dropIfExists` connection'sız → rollback varsayılan DB'yi hedefler |
| K5 | `down()` içerik hataları | account: hiç yaratılmamış `wbst_account_authorized_token_reset`/`_access_tokens` düşürülüyor, yaratılanlar düşürülmüyor · catalog: `variant_variable` 2 kez düşürülüyor, `account_price` hiç · lessor marketing: yaratılmamış 7 coupon tablosu düşürülüyor (dosya truncate edilmiş) · describing catalog: hayalet `stck_entry` düşürülüyor · dfntn_localization & system benzer |
| K6 | Tip hataları | `wbst_banner_item.banner_id` **tinyInteger** (max 127!) → bigint banner'a referans; `sort_order` bazı yerlerde **string**; `country_id/city_id/district_id` bazı tablolarda string(255) bazılarında integer; `slug` bir yerde integer |

### YÜKSEK (performans / ölçeklenebilirlik)

| # | Sorun |
|---|---|
| Y1 | FK-benzeri sütunlarda index yok (order.account_id, order_item.order_id, tüm translation.entity_id, language_code, pivot sütunları); describing'de index'ler sütun tanımından **önce** çağrılmış |
| Y2 | `utf8` yerine `utf8mb4` gerekir; `utf8_general_ci` yerine Türkçe veri için `utf8mb4_turkish_ci` / `utf8mb4_0900_ai_ci` değerlendirilmeli |
| Y3 | `code`, `sku`, `barcode`, `slug`, `email` gibi doğal anahtarlarda `unique` yok (toplam yalnızca 12 unique) |
| Y4 | Serialized text bloblar sorgulanamaz/validate edilemez — `json()` veya normalize tabloya dönmeli |

### ORTA (bakım / konvansiyon)

| # | Sorun |
|---|---|
| O1 | Üçlü şema tekrarı (tenant/lessor/describing) — tek kaynak yok |
| O2 | Sesli harfsiz kısaltma prefixleri tutarsız ve belgesiz: `ctlg`, `accntng`, `wbst`, `spprt`, `mrktng`, `blg`, `mrktplc`, `prtnr`, `spply`, `lclztn`, `gnrl`, `dfntn`, `dsgn`, `xtnsn`, `rpstry` |
| O3 | Dosya adı ≠ içerik: account modülü `wbst_` prefix kullanıyor; describing "website" dosyası `dfntn_dsgn_url` üretiyor |
| O4 | `softDeletes()` hiç yok — muhasebe/ERP verisinde kayıt saklama zorunluluğuna aykırı |
| O5 | Tüm migrationlar aynı timestamp'te (`2014_10_12_000000`) — sıralama alfabetik, bağımlılık sırası garanti değil |
| O6 | Auth verisi iş tablosuna gömülü (`wbst_account` içinde password/rememberToken/token'lar) |
| O7 | `string(255)` evrensel tip — ISO kodu, telefon, IBAN hepsi 255 |
| O8 | Denormalize snapshot'lar (order'da account_name/product_price) meşru ama bütünlük garantisi yok |
| O9 | Ölü kod: yorum satırına alınmış tablolar, `// Kalkacak` işaretli sütun, `;;` |

---

## 5. Neyin Korunmaya Değer Olduğu

- **Domain ayrıştırması sağlam:** katalog / satış / muhasebe / destek / CMS / pazarlama modül sınırları net ve DDD bounded-context'lerine iyi haritalanıyor.
- **Çeviri sidecar deseni** meşru bir i18n modeli — FK + index eklenerek yaşatılabilir (veya JSON sütunlu `spatie/laravel-translatable`'a geçilebilir).
- **Sipariş/fatura snapshot denormalizasyonu** (fiyat/isim kopyalama) e-ticarette doğru pratik — korunmalı.
- **`decimal(19,2)` para tutarlığı** iyi.
- **Türkiye'ye özgü alan bilgisi** (e-fatura, irsaliye, MERSİS, KEP, ÖTV, tevkifat benzeri alanlar) değerli iş bilgisi — yeni şemada kaybolmamalı.
- **Activity/history tabloları** (order_activity, product_activity, safe_activity) audit ihtiyacını gösteriyor — yeni tasarımda sistematikleştirilmeli.

---

## 6. `dfntn_` (Tanım) Katmanı İçin Özel Değerlendirme

141 tanım tablosu üç kategoriye ayrılıyor; her biri farklı hedef ister:

1. **Statik enum'lar** (order_status, priority, restitute_reason, safe_type, address_type…): yapıları birebir aynı (PK + code/icon/color/sort/status + translation). → PHP **backed enum** + çeviri dosyası (`lang/`) veya tek bir `lookups` + `lookup_translations` çiftine konsolidasyon. DB tablosu olarak kalacaksa bile 60+ mikro tablo yerine konsolide olmalı.
2. **Gerçek referans katalogları** (dil, para birimi + kur, ülke/il/ilçe, vergi sınıf/oranları, banka, kargo): canlı veri (kur güncellenir, vergi oranı değişir) → **düzgün kısıtlı ilişkisel tablolar** olarak kalmalı.
3. **Zengin, yönetilebilir içerik** (describing'deki app-store kataloğu, tema/blok sistemi, component registry): bunlar config değil gerçek entity → ilişkisel kalmalı; bir kısmı (component/route metadata) belki config/kod tarafına taşınabilir.

Üç kopyalı yapı yerine: paylaşılan referans veri **tek merkezi DB'de** (landlord) tutulup tenant'lara cache/sync ile servis edilmeli, ya da single-DB mimaride tek kopyaya inmeli.

---

## 7. Hedef Mimari Önerileri (Enterprise Laravel)

### 7.1 Önce verilecek stratejik kararlar

1. **Tenancy modeli:** DB-per-tenant devam mı (→ `stancl/tenancy` fiili standart; `tenant/` ve `landlord/` migration dizinlerini native destekler), yoksa tek DB + `tenant_id` scoping mi (→ daha basit operasyon, daha zayıf izolasyon)? Eski mimari DB-per-tenant; `wbst_account_access`'teki alan seti (subdomain, DB kimlik bilgileri) bunu doğruluyor.
2. **describing katmanının kaderi:** ayrı 3. DB gereksiz görünüyor — landlord DB'ye katılabilir veya kısmen config/enum'a inebilir (Bölüm 6).
3. **Kapsam tespiti:** 455 tablonun tamamı gerçekten kullanılıyor mu? (Lessor'daki İK/bordro, loan/check gibi modüller platform tarafında gerçekten işliyor muydu?) Kullanılmayan modüller porta dahil edilmemeli.

### 7.2 Şema standartları (yeni projede zorunlu kılınacak)

| Konu | Eski | Yeni standart |
|---|---|---|
| PK | `bigIncrements('entity_id')` | `$table->id()` (dış API'lerde ek olarak `ulid`/`uuid` public identifier düşünülebilir) |
| FK | `integer('x_id')->default(0)` | `$table->foreignId('x_id')->constrained()->cascadeOnDelete()` / `->nullOnDelete()`; "ilişki yok" = `NULL`, asla `0` |
| Zaman | `date_modified/date_created` | `$table->timestamps()`; gerekli yerde `softDeletes()` |
| Charset | tablo başına utf8 | `config/database.php`'de global `utf8mb4` + uygun collation; migration'larda satır içi charset YOK |
| Boolean | `tinyInteger` | `$table->boolean()`; çoklu durum → PHP backed enum + `string`/`tinyInteger` cast |
| Serialized | `text` + `serialized` bayrağı | `$table->json()` + Eloquent `array`/`AsArrayObject`/value-object cast |
| Pivot | surrogate PK, unique yok | `$table->primary(['a_id','b_id'])` veya `unique(['a_id','b_id'])` |
| Index | yok | tüm FK'ler otomatik (constrained), ayrıca `language_code`, `code`, `sku`, `slug` unique/index |
| Enum sütun | ham `enum()` | PHP backed enum + cast (DB'de string), DB enum'dan kaçın |
| Para | decimal(19,2) | korunur; `currency_code` `char(3)` + FK |
| Migration adları | tek timestamp | gerçek sıralı timestamp'ler, modül bazlı bağımlılık sırası |

### 7.3 Uygulama mimarisi

- **Modüler monolit:** modül başına `app/Domain/{Catalog,Sales,Accounting,Support,CMS,Marketing,…}` (veya `nwidart/laravel-modules`) — eski prefix'ler bounded-context listesini zaten veriyor. Kısaltılmış prefix'ler yerine **tam kelimeler**: `catalog_products`, `sales_orders`, `support_tickets`…
- **Tenancy:** `stancl/tenancy` ile `database/migrations/tenant/` + `database/migrations/landlord/`; describing → landlord'a katılır.
- **Auth ayrışması:** platform personeli / kiracı yöneticisi / son müşteri ayrı guard+model; token'lar Sanctum'a (eski `*_token_access` tabloları zaten Sanctum kopyası).
- **Yetki:** serialized `permission` blobu → `spatie/laravel-permission`.
- **Ayarlar:** 4 farklı KV tablosu → `spatie/laravel-settings` (typed settings) tek yapı.
- **Audit:** `*_activity`/`_history` elle tabloları → `spatie/laravel-activitylog` + alan bazlı state machine (`spatie/laravel-model-states` sipariş durumları için).
- **Çeviri:** iki seçenek — (a) `astrotomic/laravel-translatable` ile mevcut sidecar deseninin FK'li devamı (SQL'de sorgulanabilir, çok dilli içerik büyükse iyi), (b) `spatie/laravel-translatable` JSON sütunlar (tablo sayısını ~110 azaltır). Ürün/kategori gibi sorgulanan içerikte (a), etiket/lookup çevirilerinde `lang/` dosyaları önerilir.
- **EAV katmanı:** ürün attribute/option/filter EAV'ı gerçek ihtiyaç (e-ticaret standardı) — korunmalı ama FK'li, indexli, tanımları konsolide edilmiş halde.
- **Sipariş kalemindeki serialized options/variants/groupeds/bundles** → `json()` sütunlar (snapshot semantiği korunur) veya normalize `order_item_options` tablosu.

### 7.4 Boyut hedefi

Konsolidasyonlarla (enum'a inen ~60 mikro lookup, JSON çeviriye inen sidecar'ların bir kısmı, KV ayar tablolarının tekilleşmesi, lessor-tenant tekrarının kalkması) **~455 tablo gerçekçi olarak ~150–200 tabloya** iner; kalan her tablo FK'li, indexli, konvansiyonel olur.

---

## 8. Önerilen Yol Haritası (fazlı, geliştirme başlamadan onaylanacak)

1. **Faz 0 — Kapsam & karar:** kullanılan/ölü modül tespiti; tenancy modeli kararı; describing'in kaderi; çeviri stratejisi kararı. *(Bu rapor girdi.)*
2. **Faz 1 — Çekirdek referans veri (landlord):** localization (dil/para/ülke/vergi), lookup konsolidasyonu, enum sınıfları.
3. **Faz 2 — Kimlik & tenancy:** tenants (eski `wbst_account_access`), tenant kullanıcıları, guard'lar, spatie/permission.
4. **Faz 3 — Katalog:** product + variant/stock + EAV + category/brand (en büyük modül, 37+33 tablo → hedef ~25).
5. **Faz 4 — Satış & muhasebe:** cart→order→shipment→invoice→return zinciri, state machine'ler, kasa.
6. **Faz 5 — Destek, CMS, blog, pazarlama, pazaryeri entegrasyonu.**
7. **Faz 6 — Veri göçü:** eski DB'lerden yeni şemaya ETL (tip dönüşümleri: `0` sentinel → NULL, serialized → JSON, utf8 → utf8mb4, `date_Created` gibi anomaliler).
8. **Sürekli:** her fazda factory + seeder + migration testleri (`migrate:fresh --seed` CI'da), `down()` metotlarının gerçekten test edilmesi.

---

## 9. Açık Sorular (geliştirme öncesi netleşmeli)

1. DB-per-tenant devam edecek mi? Beklenen kiracı sayısı/ölçek nedir?
2. Eski sistemde **canlı veri var mı** — veri göçü kapsam dahilinde mi, yoksa temiz başlangıç mı?
3. Lessor'daki İK/bordro, loan/check modülleri gerçekten kullanılıyor mu?
4. Uygulama mağazası (`dfntn_app_*`) ve tema sistemi (`dfntn_dsgn_*`) yeni üründe var mı?
5. Hedef veritabanı MySQL mi kalacak, PostgreSQL'e geçiş düşünülüyor mu? (stackvo.json'da pdo_mysql + pdo_pgsql ikisi de var.)
6. API-first mi (public ID olarak ULID/UUID gerekir), yoksa klasik web app mi?
