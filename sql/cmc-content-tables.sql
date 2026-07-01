-- CMC Organik – İçerik Yönetim Sistemi Tabloları
-- Bu dosyayı phpMyAdmin veya mysql CLI ile çalıştırın: mysql -u root cmc < cmc-content-tables.sql

-- ============================================================
-- 1. site_content: Sayfa metinleri için genel key-value tablosu
-- ============================================================
CREATE TABLE IF NOT EXISTS `site_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `section` varchar(100) NOT NULL COMMENT 'Bölüm adı: homepage_mission, about_welcome, vb.',
  `field_key` varchar(100) NOT NULL COMMENT 'Alan adı: title, text, image_path, vb.',
  `field_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `section_field` (`section`, `field_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 2. roadmap_items: Anasayfadaki "Yol Haritamız" maddeleri
-- ============================================================
CREATE TABLE IF NOT EXISTS `roadmap_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `icon_id` varchar(100) DEFAULT 'gear-fill' COMMENT 'SVG sprite xlink href id',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 3. business_partners: İş ortakları CRUD tablosu
-- ============================================================
CREATE TABLE IF NOT EXISTS `business_partners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(500) DEFAULT NULL COMMENT 'Detay sayfasındaki büyük görsel yolu',
  `logo_path` varchar(500) DEFAULT NULL COMMENT 'Carousel\'daki küçük logo yolu',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Varsayılan İçerik Verileri (site_content)
-- ============================================================

INSERT IGNORE INTO `site_content` (`section`, `field_key`, `field_value`) VALUES

-- Anasayfa: Misyon
('homepage_mission', 'title', 'Misyonumuz'),
('homepage_mission', 'text', 'Firmamız tarımda daha kârlı mahsul üretmek için bitki besleme alanlarındaki teknolojik yenilikleri takip ederek, eş zamanlı ülkemiz koşullarına uygun bir şekilde üreticilerine sunan, doğaya saygılı yenilikçi, büyüme odaklı olmayı kendine görev edinmiştir.'),
('homepage_mission', 'image_path', 'public/img/aboutus.jpg'),

-- Anasayfa: Vizyon
('homepage_vision', 'title', 'Vizyonumuz'),
('homepage_vision', 'text', 'Bitki besleme alanında çözüm odaklı çalışan, ülkemizde lider ve aranılır firmalar arasında yer almaktır.'),
('homepage_vision', 'image_path', 'public/img/aboutus2.JPG'),

-- Anasayfa: Yol Haritası
('homepage_roadmap', 'slogan', '"Bilimden Doğaya"'),
('homepage_roadmap', 'description', '"Bilimden Doğaya" sloganı, CMC Organik Tarım\'ın bilimsel yenilikleri ve teknolojik gelişmeleri doğayla uyum içinde tarımsal üretime entegre etme vizyonunu yansıtmaktadır. Bu slogan, şirketin araştırma ve geliştirme faaliyetleriyle elde ettiği modern çözümleri, çevre dostu uygulamalarla birleştirerek sürdürülebilir ve verimli tarım için çalıştığını vurgular.'),

-- Hakkımızda: Hoşgeldiniz
('about_welcome', 'title', 'CMC Organik\'e Hoş Geldiniz'),
('about_welcome', 'text', 'Doğadan gelen gücü toprakla buluşturarak sürdürülebilir tarımı destekleyen CMC Organik, 2010 yılından bu yana organik ve konvansiyonel tarım alanında üreticilerin güvenilir çözüm ortağı olmuştur. İzmir merkezli firmamız, kalite odaklı üretim anlayışı ve geniş ürün portföyüyle tarım sektörünün öncülerindendir.'),
('about_welcome', 'image_path', 'public/img/aboutus.jpg'),

-- Hakkımızda: Misyon
('about_mission', 'title', 'Misyonumuz'),
('about_mission', 'text', 'Tarımda daha kârlı ve sürdürülebilir mahsul üretimi için bitki besleme alanındaki teknolojik yenilikleri takip eden, doğaya saygılı, üretici dostu ve büyüme odaklı bir yaklaşımı benimsemek.'),
('about_mission', 'image_path', 'public/img/aboutus2.JPG'),

-- Hakkımızda: Vizyon
('about_vision', 'title', 'Vizyonumuz'),
('about_vision', 'text', 'Bitki besleme çözümlerinde güvenilir, yenilikçi ve çözüm odaklı hizmet anlayışıyla, Türkiye\'nin lider ve tercih edilen organik tarım markaları arasında yer almak.'),
('about_vision', 'image_path', 'public/img/products.JPG'),

-- Hakkımızda: Alt Bölüm
('about_bottom', 'title', 'CMC Organik ile Tarımda Güvenin ve Kalitenin Adresi'),
('about_bottom', 'paragraph1', 'Doğadan gelen gücü toprakla buluşturarak sürdürülebilir tarımı destekleyen CMC Organik, 2010 yılından bu yana hem organik hem de konvansiyonel tarım alanında üreticilerin güvenilir çözüm ortağı olmayı başarmıştır. İzmir merkezli olarak kurulan firmamız, ülkemizin tarım potansiyelini en verimli şekilde değerlendirmeyi amaçlayan yenilikçi ve doğa dostu bir yaklaşımla faaliyet göstermektedir.'),
('about_bottom', 'paragraph2', 'Kuruluşumuzdan bu yana, yalnızca kaliteli ürün sunmayı değil; aynı zamanda üreticilerimize bilgi, teknik destek ve güven duygusu aşılamayı da hedefliyoruz. Çünkü biz biliyoruz ki; sağlıklı toprak, bilinçli üretici ve doğru tarım girdileriyle birleştiğinde, verimli ve sürdürülebilir bir gelecek inşa edilebilir.'),
('about_bottom', 'paragraph3', 'CMC Organik olarak bitki besleme alanında sunduğumuz geniş ürün yelpazesiyle, farklı iklim ve toprak koşullarına uygun çözümler geliştirmekteyiz. Ar-Ge yatırımlarımızla sürekli gelişen ürün portföyümüz, her geçen gün daha fazla üreticiye ulaşıyor.'),
('about_bottom', 'paragraph4', 'Tarımın geleceğini korumak, sadece günümüz için değil; çocuklarımızın yarınları için de büyük bir sorumluluktur. Bu bilinçle hareket eden CMC Organik, doğaya saygılı üretim anlayışı, müşteri memnuniyetine dayalı hizmet politikası ve sürdürülebilir kalkınmaya olan katkısıyla tarım sektörünün öncü markalarından biri olmaya devam etmektedir.'),

-- Şirketimiz
('company', 'title', 'CMC Organik Hakkında'),
('company', 'paragraph1', '2010 yılında İzmir\'de kurulan CMC Organik Tarım Sanayi ve Ticaret Ltd. Şti., organik ve konvansiyonel tarım ürünleri ile çiftçilere sürdürülebilir tarımın kapılarını açmayı hedefleyen köklü bir kuruluştur. Kurulduğumuz günden bu yana, sektördeki değişim ve gelişimi yakından takip ederek, üreticilerimize yalnızca ürün değil, aynı zamanda bilgi ve teknik destek de sunmayı ilke edindik.'),
('company', 'paragraph2', 'Firmamız; güçlü satış ağı, deneyimli teknik kadrosu ve sürekli gelişen ürün yelpazesiyle Türkiye\'nin dört bir yanındaki üreticilere ulaşmakta, onların verimliliğini artırmayı ve çevreye duyarlı üretimi teşvik etmeyi amaçlamaktadır.'),
('company', 'paragraph3', 'Tarım sektörünün zorlu koşullarına çözüm üretebilen profesyonel bir yaklaşımla, ürünlerimizin etkinliği ve doğruluğu konusunda teknik departmanımızla sahada aktif olarak yer alıyor; satış sonrası hizmetlerde de üreticinin yanında olmaya devam ediyoruz.'),

-- İş Ortakları Giriş
('partners_intro', 'title', 'Gücümüzü Paylaştığımız İş Ortaklarımız'),
('partners_intro', 'paragraph1', 'Başarımızın temelinde yalnızca kaliteli ürünler değil, güçlü iş birliklerimiz de yer alıyor. CMC Organik olarak, sektörün önde gelen üretici ve distribütörleriyle uzun soluklu ve güvene dayalı iş ortaklıkları kurduk.'),
('partners_intro', 'paragraph2', 'Yerli ve uluslararası tedarikçilerimizle gerçekleştirdiğimiz stratejik iş birlikleri sayesinde, üreticilerimize her zaman yüksek kaliteli, etkili ve güvenilir bitki besleme çözümleri sunuyoruz.'),
('partners_intro', 'paragraph3', 'Bizi tercih eden iş ortaklarımıza teşekkür eder, birlikte büyümeye ve tarım sektörünü daha ileriye taşımaya devam edeceğimizi taahhüt ederiz.');

-- ============================================================
-- Varsayılan Roadmap Maddeleri
-- ============================================================

INSERT IGNORE INTO `roadmap_items` (`id`, `title`, `description`, `icon_id`, `sort_order`, `is_active`) VALUES
(1, 'Çevre Dostu ve Sürdürülebilirlik', 'Şirket, organik ve konvansiyonel tarımda kullanılabilir ürünler üreterek hem ürün kalitesini artırmayı hem de çevre, toprak ve doğal kaynakları korumayı hedeflemektedir.', 'recycle', 1, 1),
(2, 'Araştırma ve Geliştirme', 'Tarımsal bitki besleme ürünlerinde zengin bir ürün yelpazesi sunmak için sürekli Ar-Ge çalışmaları yapmaktadır. Bu durum, inovasyona verdikleri önemi ortaya koymaktadır.', 'book', 2, 1),
(3, 'Geniş Ürün Yelpazesi ve Teknik Destek', 'Ürün portföyü, bitkilerin çeşitli ihtiyaçlarına yönelik biyostimülanlar, organik gübreler, iz elementler ve yayıcı-yapıştırıcılar gibi birçok ürünü kapsamaktadır.', 'gear-fill', 3, 1),
(4, 'Türkiye Geneline Ulaşım', 'Güçlü satış ve teknik ekipleri sayesinde Türkiye\'nin her bölgesindeki çiftçilere ulaşmayı ve onları çözüm ortağı olarak desteklemeyi amaçlamaktadır.', 'speedometer', 4, 1);

-- ============================================================
-- Varsayılan İş Ortakları (mevcut statik verilerden migrate)
-- ============================================================

INSERT IGNORE INTO `business_partners` (`id`, `name`, `description`, `image_path`, `logo_path`, `sort_order`, `is_active`) VALUES
(1, 'Hefe Fertilizer', 'Hefe Fertilizer, yüksek kaliteli gübreleriyle modern tarıma sürdürülebilir çözümler sunan köklü bir markadır. Geniş ürün yelpazesi, farklı bitki ve iklim koşullarına uyum sağlar.\nCMC Organik olarak Hefe ile uzun süredir güçlü bir iş birliği yürütüyoruz. Kaliteli ürünleri ve bizim saha tecrübemiz sayesinde çiftçilere verimli ve sürdürülebilir üretim imkânı sunuyoruz.', 'public/img/bussiness/Hefe-1.png', 'public/img/bussiness/hefe.jpg', 1, 1),
(2, 'Bioris', 'Bioris, bitki sağlığını destekleyen biyoteknolojik ürünleriyle çevre dostu çözümler sunan öncü bir firmadır. Bitki koruma ve toprak düzenleme alanında güvenilir ürünleriyle tanınır.\nUzun yıllardır süren iş birliğimizle, Bioris\'in güçlü Ar-Ge altyapısı ve bizim sahadaki uygulama deneyimimiz üreticilere büyük değer katmaktadır.', 'public/img/bussiness/cmc.jpg', 'public/img/bussiness/cmc.jpg', 2, 1),
(3, 'Ufuk Zirai İlaç', 'Ufuk Zirai İlaç, kaliteli zirai ilaç ve tarım girdileriyle sektörde güvenilirliğini kanıtlamış bir firmadır. Doğru ürün ve uygulama prensibiyle çiftçilere verimli üretim sağlar.\nCMC Organik olarak yıllardır süregelen ortaklığımız, sahada elde ettiğimiz başarılı sonuçlarla devam ediyor.', 'public/img/bussiness/ufuk.png', 'public/img/bussiness/ufuk.png', 3, 1);
