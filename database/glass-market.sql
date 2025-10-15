-- Glass Market Database (MySQL 8+)

-- Maak de database aan
CREATE DATABASE IF NOT EXISTS glass_market CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE glass_market;

-- ==================================================
-- 1. ENUMS / lookup tables
-- ==================================================

-- company_type
CREATE TABLE company_types (
    type VARCHAR(50) PRIMARY KEY
);
INSERT INTO company_types (type) VALUES
('Glass Factory'),
('Glass Recycle Plant'),
('Collection Company'),
('Trader'),
('Other');

-- listing_side
CREATE TABLE listing_sides (
    side VARCHAR(10) PRIMARY KEY
);
INSERT INTO listing_sides (side) VALUES
('WTS'), -- Want to Sell
('WTB'); -- Want to Buy

-- recycled_status
CREATE TABLE recycled_statuses (
    status VARCHAR(20) PRIMARY KEY
);
INSERT INTO recycled_statuses (status) VALUES
('recycled'),
('not_recycled'),
('unknown');

-- tested_status
CREATE TABLE tested_statuses (
    status VARCHAR(20) PRIMARY KEY
);
INSERT INTO tested_statuses (status) VALUES
('tested'),
('untested'),
('unknown');

-- currency_iso
CREATE TABLE currency_iso (
    currency VARCHAR(3) PRIMARY KEY
);
INSERT INTO currency_iso (currency) VALUES
('EUR'),('USD'),('GBP'),('JPY'),('CNY');

-- ==================================================
-- 2. Companies
-- ==================================================
CREATE TABLE companies (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    company_type VARCHAR(50) NOT NULL,
    website VARCHAR(255),
    phone VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_type) REFERENCES company_types(type)
) ENGINE=InnoDB;

-- ==================================================
-- 3. Locations
-- ==================================================
CREATE TABLE locations (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT NOT NULL,
    name VARCHAR(255),
    address_line1 VARCHAR(255),
    address_line2 VARCHAR(255),
    postal_code VARCHAR(20),
    city VARCHAR(100),
    region VARCHAR(100),
    country_code CHAR(2),
    contact_email_broadcast VARCHAR(255),
    contact_email_personal VARCHAR(255),
    phone VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ==================================================
-- 4. Users
-- ==================================================
CREATE TABLE users (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    company_id BIGINT,
    email VARCHAR(255) NOT NULL UNIQUE,
    name VARCHAR(255),
    phone VARCHAR(50),
    roles JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- user_locations (many-to-many)
CREATE TABLE user_locations (
    user_id BIGINT NOT NULL,
    location_id BIGINT NOT NULL,
    can_edit BOOLEAN DEFAULT FALSE,
    PRIMARY KEY(user_id, location_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ==================================================
-- 5. Subscriptions
-- ==================================================
CREATE TABLE subscriptions (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    location_id BIGINT NOT NULL,
    start_date DATE NOT NULL,
    duration_years INT NOT NULL DEFAULT 1,
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- View voor expiry (eerste 3 maanden gratis)
CREATE VIEW subscription_expiry AS
SELECT
    s.*,
    DATE_ADD(DATE_ADD(s.start_date, INTERVAL 3 MONTH), INTERVAL s.duration_years YEAR) AS expiry_date,
    DATE_ADD(s.start_date, INTERVAL 3 MONTH) AS paid_from_date
FROM subscriptions s;

-- ==================================================
-- 6. Listings
-- ==================================================
CREATE TABLE listings (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    location_id BIGINT,
    company_id BIGINT,
    side VARCHAR(10) NOT NULL,
    glass_type VARCHAR(255) NOT NULL,
    glass_type_other VARCHAR(255),
    quantity_tons DECIMAL(12,2),
    quantity_note VARCHAR(255),
    recycled VARCHAR(20) DEFAULT 'unknown',
    tested VARCHAR(20) DEFAULT 'unknown',
    storage_location VARCHAR(255),
    price_text VARCHAR(255),
    currency VARCHAR(3) DEFAULT 'EUR',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    valid_until DATE,
    published BOOLEAN DEFAULT TRUE,
    quality_notes TEXT,
    accepted_by_contract BOOLEAN DEFAULT FALSE,
    FULLTEXT KEY ft_search (glass_type, glass_type_other, storage_location, price_text, quality_notes),
    FOREIGN KEY (location_id) REFERENCES locations(id),
    FOREIGN KEY (company_id) REFERENCES companies(id),
    FOREIGN KEY (side) REFERENCES listing_sides(side),
    FOREIGN KEY (recycled) REFERENCES recycled_statuses(status),
    FOREIGN KEY (tested) REFERENCES tested_statuses(status),
    FOREIGN KEY (currency) REFERENCES currency_iso(currency)
) ENGINE=InnoDB;

-- ==================================================
-- 7. Broadcasts
-- ==================================================
CREATE TABLE broadcasts (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    listing_id BIGINT,
    subject VARCHAR(255),
    body TEXT,
    from_email VARCHAR(255),
    reply_to_email VARCHAR(255),
    recipients_count INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    sent_at TIMESTAMP NULL,
    FOREIGN KEY (listing_id) REFERENCES listings(id)
) ENGINE=InnoDB;

-- ==================================================
-- 8. Contracts
-- ==================================================
CREATE TABLE contracts (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    grp_company_id BIGINT,
    gf_company_id BIGINT,
    grp_location_id BIGINT,
    gf_location_id BIGINT,
    start_date DATE,
    end_date DATE,
    weekly_quantity_tons DECIMAL(12,2),
    price_text VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (grp_company_id) REFERENCES companies(id),
    FOREIGN KEY (gf_company_id) REFERENCES companies(id),
    FOREIGN KEY (grp_location_id) REFERENCES locations(id),
    FOREIGN KEY (gf_location_id) REFERENCES locations(id)
) ENGINE=InnoDB;

-- ==================================================
-- 9. Maintenance Events
-- ==================================================
CREATE TABLE maintenance_events (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    location_id BIGINT,
    start_datetime DATETIME,
    end_datetime DATETIME,
    reason TEXT,
    planned BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (location_id) REFERENCES locations(id)
) ENGINE=InnoDB;

-- ==================================================
-- 10. Capacities
-- ==================================================
CREATE TABLE capacities (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    location_id BIGINT,
    date_recorded DATE DEFAULT CURRENT_DATE,
    weekly_capacity_tons DECIMAL(12,2),
    notes TEXT,
    FOREIGN KEY (location_id) REFERENCES locations(id)
) ENGINE=InnoDB;

-- ==================================================
-- 11. Sample Data
-- ==================================================

-- Companies
INSERT INTO companies (name, company_type, website, phone) VALUES
('GlassRecycle BV','Glass Recycle Plant','https://grb.example','+31 10 123 4567'),
('GlassFactory NL','Glass Factory','https://gfnl.example','+31 20 987 6543'),
('CollectionCo BE','Collection Company','https://ccbe.example','+32 2 555 1234');

-- Locations
INSERT INTO locations (company_id,name,address_line1,postal_code,city,country_code,contact_email_broadcast,contact_email_personal)
VALUES
(1,'Rotterdam Plant','Harbour 1','3011AA','Rotterdam','NL','broadcast@grb.example','sales@grb.example'),
(2,'Amsterdam Factory','Factory Street 10','1000AA','Amsterdam','NL','broadcast@gfnl.example','person@gfnl.example'),
(3,'Brussels Collection','Glass Road 5','1000','Brussels','BE','broadcast@ccbe.example','person@ccbe.example');

-- Listings
INSERT INTO listings (location_id,company_id,side,glass_type,quantity_tons,recycled,tested,storage_location,price_text,currency,quality_notes)
VALUES
(1,1,'WTS','Clear Cullet',250.00,'recycled','tested','Rotterdam yard','€120/ton CIF','EUR','Low Fe content'),
(2,2,'WTB','Brown Cullet',150.00,'recycled','tested','Amsterdam warehouse','€110/ton CIF','EUR','High purity required'),
(3,3,'WTS','Mixed Cullet',100.00,'not_recycled','untested','Brussels yard','€80/ton EXW','EUR','Unsorted mix');

-- Subscriptions
INSERT INTO subscriptions (location_id,start_date,duration_years)
VALUES
(1,'2025-01-01',1),
(2,'2025-02-01',1),
(3,'2025-03-01',1);

-- Users
INSERT INTO users (company_id,email,name,roles)
VALUES
(1,'alice@grb.example','Alice','["admin"]'),
(2,'bob@gfnl.example','Bob','["broker"]'),
(3,'carol@ccbe.example','Carol','["operator"]');

-- User locations
INSERT INTO user_locations (user_id,location_id,can_edit)
VALUES
(1,1,TRUE),(2,2,TRUE),(3,3,FALSE);

-- Contracts
INSERT INTO contracts (grp_company_id,gf_company_id,grp_location_id,gf_location_id,start_date,end_date,weekly_quantity_tons,price_text)
VALUES
(1,2,1,2,'2025-01-01','2025-12-31',200,'€115/ton CIF');

-- Maintenance
INSERT INTO maintenance_events (location_id,start_datetime,end_datetime,reason,planned)
VALUES
(1,'2025-12-24 08:00:00','2025-12-26 18:00:00','Christmas shutdown',TRUE);

-- Capacities
INSERT INTO capacities (location_id,date_recorded,weekly_capacity_tons,notes)
VALUES
(1,'2025-10-01',500,'Normal capacity'),
(2,'2025-10-01',300,'Normal capacity'),
(3,'2025-10-01',150,'Normal capacity');
