-- Update script to align About Us page sections with the latest design.
-- Run this in phpMyAdmin after importing the original CMS tables.

SET @about_page_id = (SELECT id FROM pages WHERE slug = 'about-us' LIMIT 1);

-- Helper table with desired sections
DROP TEMPORARY TABLE IF EXISTS tmp_about_sections;
CREATE TEMPORARY TABLE tmp_about_sections (
    section_key VARCHAR(100) PRIMARY KEY,
    section_type VARCHAR(50),
    section_label VARCHAR(255),
    display_order INT
);

INSERT INTO tmp_about_sections VALUES
('hero_kicker', 'text', 'Hero Section - Kicker', 1),
('hero_title', 'text', 'Hero Section - Main Title', 2),
('hero_subtitle', 'textarea', 'Hero Section - Subtitle', 3),
('hero_description', 'textarea', 'Hero Section - Description', 4),
('hero_primary_label', 'text', 'Hero Section - Primary Button Label', 5),
('hero_primary_url', 'text', 'Hero Section - Primary Button URL', 6),
('stats_1_value', 'text', 'Stats Card 1 - Value', 7),
('stats_1_label', 'text', 'Stats Card 1 - Label', 8),
('stats_2_value', 'text', 'Stats Card 2 - Value', 9),
('stats_2_label', 'text', 'Stats Card 2 - Label', 10),
('stats_3_value', 'text', 'Stats Card 3 - Value', 11),
('stats_3_label', 'text', 'Stats Card 3 - Label', 12),
('stats_4_value', 'text', 'Stats Card 4 - Value', 13),
('stats_4_label', 'text', 'Stats Card 4 - Label', 14),
('mission_title', 'text', 'Mission Section - Title', 15),
('mission_text', 'textarea', 'Mission Section - Description', 16),
('vision_title', 'text', 'Vision Section - Title', 17),
('vision_text', 'textarea', 'Vision Section - Description', 18),
('values_title', 'text', 'Values Section - Title', 19),
('values_intro', 'textarea', 'Values Section - Intro', 20),
('values_item_1_title', 'text', 'Values Card 1 - Title', 21),
('values_item_1_text', 'textarea', 'Values Card 1 - Description', 22),
('values_item_2_title', 'text', 'Values Card 2 - Title', 23),
('values_item_2_text', 'textarea', 'Values Card 2 - Description', 24),
('values_item_3_title', 'text', 'Values Card 3 - Title', 25),
('values_item_3_text', 'textarea', 'Values Card 3 - Description', 26),
('team_title', 'text', 'Team Section - Title', 27),
('team_text', 'textarea', 'Team Section - Description', 28),
('cta_title', 'text', 'CTA Section - Title', 29),
('cta_text', 'textarea', 'CTA Section - Description', 30),
('cta_primary_label', 'text', 'CTA Section - Primary Button Label', 31),
('cta_primary_url', 'text', 'CTA Section - Primary Button URL', 32),
('cta_secondary_label', 'text', 'CTA Section - Secondary Button Label', 33),
('cta_secondary_url', 'text', 'CTA Section - Secondary Button URL', 34);

-- Insert missing sections
INSERT INTO page_sections (page_id, section_key, section_type, section_label, display_order)
SELECT @about_page_id, s.section_key, s.section_type, s.section_label, s.display_order
FROM tmp_about_sections s
LEFT JOIN page_sections ps ON ps.page_id = @about_page_id AND ps.section_key = s.section_key
WHERE ps.id IS NULL AND @about_page_id IS NOT NULL;

-- Insert default content for sections that do not yet have values
INSERT INTO page_content (section_id, content_value)
SELECT ps.id,
CASE ps.section_key
    WHEN 'hero_kicker' THEN 'About Glass Market'
    WHEN 'hero_title' THEN 'Reimagining Glass Recycling for the Modern World'
    WHEN 'hero_subtitle' THEN 'We connect glass recyclers, factories, and collection partners with a marketplace built for clarity, trust, and speed.'
    WHEN 'hero_description' THEN 'From Rotterdam to Singapore, Glass Market keeps premium cullet moving. Discover reliable supply, verified partners, and transparent pricing in one curated platform.'
    WHEN 'hero_primary_label' THEN 'Explore Marketplace'
    WHEN 'hero_primary_url' THEN '/browse'
    WHEN 'stats_1_value' THEN '432K'
    WHEN 'stats_1_label' THEN 'Tons of glass traded'
    WHEN 'stats_2_value' THEN '68'
    WHEN 'stats_2_label' THEN 'Active partner locations'
    WHEN 'stats_3_value' THEN '24 hrs'
    WHEN 'stats_3_label' THEN 'Average listing approval'
    WHEN 'stats_4_value' THEN '98%'
    WHEN 'stats_4_label' THEN 'Fulfilment satisfaction rate'
    WHEN 'mission_title' THEN 'Our Mission'
    WHEN 'mission_text' THEN 'Glass Market accelerates the circular economy by helping recycling plants place high-grade cullet where it is needed most. We build tools that shorten lead times, improve quality assurance, and keep production lines supplied with confidence.'
    WHEN 'vision_title' THEN 'Our Vision'
    WHEN 'vision_text' THEN 'A fully traceable, data-rich glass ecosystem where every shard is recovered, certified, and returned to industry. By unifying stakeholders on a single platform, we make sustainability measurable and commercially viable.'
    WHEN 'values_title' THEN 'Principles We Operate By'
    WHEN 'values_intro' THEN 'We design technology and partnerships that make the glass value chain resilient. Every decision is guided by a shared responsibility to the planet and the people who keep materials in motion.'
    WHEN 'values_item_1_title' THEN 'Clarity First'
    WHEN 'values_item_1_text' THEN 'Live logistics, pricing transparency, and certified quality data keep buyers and sellers aligned from enquiry to delivery.'
    WHEN 'values_item_2_title' THEN 'Reliable Partnerships'
    WHEN 'values_item_2_text' THEN 'We vet every supplier and logistics partner to make sure glass arrives exactly as promised—no surprises, no delays.'
    WHEN 'values_item_3_title' THEN 'Sustainable Growth'
    WHEN 'values_item_3_text' THEN 'Our roadmap is focused on reducing waste, lowering emissions, and helping partners invest in greener operations.'
    WHEN 'team_title' THEN 'Dedicated Industry Specialists'
    WHEN 'team_text' THEN 'Our Amsterdam-based core team blends decades of glass recycling, supply chain, and product experience. We stay close to the yards, factories, and ports that rely on us every day.'
    WHEN 'cta_title' THEN 'Join the Circular Glass Movement'
    WHEN 'cta_text' THEN 'Whether you handle production cullet, collection streams, or manufacturing demand, Glass Market builds tools tailored to your business.'
    WHEN 'cta_primary_label' THEN 'Request a Demo'
    WHEN 'cta_primary_url' THEN '/demo'
    WHEN 'cta_secondary_label' THEN 'Talk to Operations'
    WHEN 'cta_secondary_url' THEN 'mailto:hello@glassmarket.com'
    ELSE ''
END
FROM page_sections ps
LEFT JOIN page_content pc ON pc.section_id = ps.id
WHERE ps.page_id = @about_page_id AND pc.id IS NULL AND @about_page_id IS NOT NULL;

-- Remove deprecated secondary hero CTA fields entirely
DELETE pc FROM page_content pc
JOIN page_sections ps ON ps.id = pc.section_id
WHERE ps.page_id = @about_page_id AND ps.section_key IN ('hero_secondary_label', 'hero_secondary_url');

DELETE FROM page_sections
WHERE page_id = @about_page_id AND section_key IN ('hero_secondary_label', 'hero_secondary_url');

DROP TEMPORARY TABLE IF EXISTS tmp_about_sections;
