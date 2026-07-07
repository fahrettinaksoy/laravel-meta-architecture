# `create_account_table.php` — Analiz ve Yeniden Tasarım Raporu

> Kaynak: `database/migrations_old/tenant/2014_10_12_000000_create_account_table.php` (267 satır, 14 tablo)
> Hedef: `database/migrations/tenant/` altında `mbr_` (membership) prefix'i ile yeniden oluşturma.
> Karar (2026-07-07): Altyapı tamamen e-ticaret olduğundan bu modül **membership → `mbr_`** olarak adlandırıldı; `acct_` prefix'i gerçek muhasebe modülüne (eski `accntng_`) ayrıldı.
> Standartlar: uuid sütunu, Türkçe comment'ler, Şablon1 (tablo başlangıcı), Şablon2 (denetim alanları), Şablon3 (bigIncrements PK).

---

## 1. Dosyanın Envanteri (14 tablo)

| # | Eski tablo | Amaç | Yeni ad (`mbr_`) |
|---|---|---|---|
| 1 | `wbst_account` | Üye/cari ana tablosu (müşteri/firma) + auth alanları | `mbr_account` |
| 2 | `wbst_account_password_reset` | Şifre sıfırlama (Laravel standardı kopyası) | Birleştir → `mbr_account_password_reset` |
| 3 | `wbst_account_authorized` | Üyeye bağlı yetkili kişiler | `mbr_account_authorized` |
| 4 | `wbst_account_contact` | Adres/iletişim kartları (fatura-sevk adresi) | `mbr_account_contact` |
| 5 | `wbst_account_bank_account` | Üyenin banka hesapları / IBAN | `mbr_account_bank_account` |
| 6 | `wbst_account_download` | Satın alınan dijital ürün indirme hakkı | `mbr_account_download` |
| 7 | `wbst_account_reward` | Sadakat puanı hareketleri | `mbr_account_reward` |
| 8 | `wbst_account_wishlist` | İstek listesi başlığı | `mbr_account_wishlist` |
| 9 | `wbst_account_wishlist_item` | İstek listesi kalemi | `mbr_account_wishlist_item` |
| 10 | `wbst_account_falling` | Fiyat düşüş alarmı | `mbr_account_price_alert` (isim netleşmeli) |
| 11 | `wbst_account_preorder` | Ön sipariş talebi | `mbr_account_preorder` |
| 12 | `wbst_account_token_reset` | Şifre sıfırlama **(2. kopya — #2 ile mükerrer)** | Kaldır / #2 ile birleştir |
| 13 | `wbst_account_token_access` | API erişim token'ları (Sanctum `personal_access_tokens` klonu) | Sanctum'un kendi tablosu |
| 14 | `wbst_account_verification` | E-posta/SMS doğrulama kodları | `mbr_account_verification` |

`mbr_` (membership) kararıyla wishlist/fiyat alarmı/ön sipariş/indirme/puan tablolarının modül aidiyeti sorunu çözüldü: hepsi üyelik davranışı olarak bu modülde kalır. `acct_` prefix'i ileride gerçek muhasebe modülü (eski `accntng_`: order→invoice→kasa) için ayrılmıştır.

## 2. Bu Dosyaya Özgü Tespit Edilen Sorunlar

**Yapısal:**
1. **İki ayrı şifre sıfırlama tablosu** (#2 ve #12) — aynı işi yapıyor; teki kalmalı.
2. **`wbst_account_token_access` birebir Sanctum** (`morphs('tokenable')`, `token` 64 unique, `abilities`) — özel tablo yerine Sanctum kullanılmalı.
3. **`email_verified_token` + `wbst_account_verification` çakışması** — doğrulama hem ana tabloda token sütunu hem ayrı tabloda kod olarak iki kez modellenmiş; tek mekanizma (verification tablosu) kalmalı.
4. **Döngüsel referanslar:** `account.default_authorized_id / default_contact_id / default_bank_account_id` alt tablolara, alt tablolar `account_id` ile ana tabloya işaret ediyor. FK'ler eklenirken alt tablolar oluştuktan **sonra ayrı migration ile** ve `nullOnDelete()` ile bağlanmalı.
5. **`down()` hataları:** `password_reset` ve `token_reset` hiç drop edilmiyor; hiç yaratılmamış `wbst_account_authorized_token_reset` drop ediliyor.
6. **Auth ile iş verisi karışık:** password/rememberToken cari kartın içinde. (Mevcut yapı korunacaksa en azından nullable olmalı — her cari'nin girişi olmayabilir.)

**Sütun düzeyinde:**
7. Tüm FK'ler `integer(...)->default(0)` — `unsignedBigInteger()->nullable()` olmalı, `0` sentinel kalkmalı.
8. Her string 255: `language_code` (2 olmalı), `iban_number` (34), `currency_code` (3), `citizenship_number` (11), `postcode` (~10), `phone`/`ip_address` (45, IPv6) — alan tipleri daraltılmalı.
9. `email`, `code` üzerinde **unique yok** — cari kodu ve giriş e-postası benzersiz olmalı. (Not: soft delete ile unique e-posta çakışabilir; silinen kaydın e-postası yeni kayda engel olur — bilinçli karar gerekli.)
10. `shape` (integer) belirsiz — bireysel/kurumsal ayrımı olduğu tahmin ediliyor; anlamlı enum'a dönüşmeli.
11. `gender` integer — enum/string olmalı; `abroad`, `safe`, `newsletter`, `entry`, `status` tinyint → `boolean()`.
12. `birth_date` `timestamp` — `date` olmalı (1970 öncesi doğumlular timestamp aralığına sığmaz!).
13. `wishlist_item(wishlist_id, product_id)` çiftinde unique yok — mükerrer kalem mümkün.
14. `account_id` içeren hiçbir alt tabloda index yok.

## 3. Yeni Standart Kalıp (tüm tablolara uygulanacak)

Sıra: **Şablon1 → Şablon3 (PK) → uuid → iş sütunları → Şablon2 → index/FK'ler**

```
Şablon1: engine/charset(utf8mb4)/collation(utf8mb4_unicode_ci) + $table->comment('...')
Şablon3: $table->bigIncrements('<entity>_id')->comment('...')
uuid   : $table->uuid('uuid')->unique()->comment('Dış sistemler ve API için benzersiz UUID')
Şablon2: created_by/updated_by/deleted_by + created_at(useCurrent)/updated_at(useCurrent+useCurrentOnUpdate)/deleted_at(nullable)
```

Notlar:
- `deleted_at` adı Eloquent `SoftDeletes` trait'i ile birebir uyumlu — modelde trait açmak yeterli.
- `useCurrentOnUpdate()` **MySQL'e özgüdür**; PostgreSQL'e geçilirse trigger gerekir (stackvo.json'da pdo_pgsql da açık — bilinçli karar).
- UUID'yi DB üretmez; modelde `HasUuids` trait + `uniqueIds(): ['uuid']` ile uygulama üretmeli.
- FK sütunları PK ile tip uyumu için daima `unsignedBigInteger` (Şablon2'deki `created_by` ile zaten tutarlı).
- Modelde: `$table = 'acct_account'`, `$primaryKey = 'account_id'` tanımlanmalı.

## 4. Örnek: `mbr_account` (tam migration)

`database/migrations/tenant/2026_07_07_000001_create_mbr_account_table.php` — bkz. sohbet çıktısı. Öne çıkan düzeltmeler: uuid, Türkçe comment'ler, daraltılmış alan tipleri, nullable FK'ler, unique(code, email), boolean bayraklar, `kind` enum'u (eski `shape`), Şablon2 denetim alanları, indexler; `email_verified_token` kaldırıldı (verification tablosuna devredildi), döngüsel `default_*` FK'leri ayrı migration'a bırakıldı. Tablo comment'i: `'Üye ana tablosu: müşteri/cari kartları (bireysel ve kurumsal)'`.

## 5. Geliştirme Öncesi Karar Soruları

1. ~~E-ticaret davranış tabloları hangi modülde?~~ → **Çözüldü:** `mbr_` (membership) kararıyla hepsi üyelik modülünde.
2. Tablo adları tekil mi (`mbr_account`) çoğul mu (`mbr_accounts`)? Laravel konvansiyonu çoğuldur; tekil seçilirse tüm modellerde `$table` tanımlanır (örnek tekil yazıldı).
3. Token/şifre sıfırlama: Sanctum + Laravel'in `password_reset_tokens` standardı mı, özel tablolar mı?
4. `shape` sütununun gerçek anlamı doğrulanmalı (bireysel/kurumsal varsayıldı).
5. Üye (`mbr_account`) giriş yapan bir kimlik mi olacak (password alanı kalsın mı), yoksa giriş yalnızca `authorized` kullanıcılarında mı?
6. Ana varlık adı `account` olarak mı kalacak (`mbr_account`), yoksa `member` mı (`mbr_member` — modül adıyla tekrar eder, önerilmez)? Örnekte `account` korundu, PK `account_id`.
