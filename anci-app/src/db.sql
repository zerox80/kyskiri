-- Runned in hosted database (supabase.com)
-- DO NOT TOUCH THE DATABASE !!!!!
CREATE TABLE korisnici (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    username TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL,
    is_admin BOOLEAN DEFAULT FALSE
);

CREATE TABLE usluge (
    id SERIAL PRIMARY KEY,
    naziv TEXT NOT NULL,
    opis TEXT,
    cena NUMERIC(10,2) NOT NULL,
    trajanje INT NOT NULL -- in minutes
);

CREATE TABLE rezervacije (
    id SERIAL PRIMARY KEY,
    ime TEXT NOT NULL,
    telefon TEXT NOT NULL,
    usluga_id INT NOT NULL REFERENCES usluge(id) ON DELETE CASCADE,
    datum TIMESTAMP NOT NULL,
    vreme_rezervacije TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO usluge (naziv, opis, cena, trajanje) VALUES
('Muško šišanje', 'Klasično šišanje sa mašinicom i makazama', 800.00, 30),
('Brijanje', 'Brijanje sa toplom peskirnom i negom kože', 600.00, 20),
('Kraljevsko šišanje', 'Ekskluzivno šišanje sa detaljnim oblikovanjem', 1500.00, 45),
('Fade šišanje', 'Postepeno skraćivanje od temena ka vratu', 1200.00, 40),
('Šišanje makazama', 'Precizno šišanje isključivo makazama', 1000.00, 35);

