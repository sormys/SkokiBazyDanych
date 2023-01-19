CREATE TABLE Kraj (
    id_kraju SERIAL PRIMARY KEY,
    nazwa VARCHAR(20) NOT NULL
);
CREATE TABLE Konkurs (
    id_konkursu SERIAL PRIMARY KEY,
    nazwa VARCHAR(40) NOT NULL,
    termin_zgloszen date,
    organizator INTEGER NOT NULL REFERENCES Kraj
);
CREATE TABLE Zawodnik (
    id_zawodnika SERIAL PRIMARY KEY,
    imie VARCHAR(20) NOT NULL,
    nazwisko VARCHAR(20) NOT NULL,
    id_kraju INTEGER NOT NULL REFERENCES Kraj,
    punkty NUMERIC(6)
);
CREATE TABLE KwotaStartowa (
    id_kwoty SERIAL PRIMARY KEY,
    id_kraju INTEGER NOT NULL REFERENCES Kraj,
    id_konkursu INTEGER NOT NULL REFERENCES Konkurs,
    UNIQUE (id_kraju, id_konkursu),
    kwota_startowa NUMERIC(6) NOT NULL
);
CREATE TABLE Zgloszenie (
    id_zgloszenia SERIAL PRIMARY KEY,
    id_konkursu INTEGER NOT NULL REFERENCES Konkurs,
    id_zawodnika INTEGER NOT NULL REFERENCES Zawodnik,
    numer_startowy NUMERIC(6) NOT NULL
);
CREATE TABLE Skok (
    id_skoku SERIAL PRIMARY KEY,
    odleglosc NUMERIC(6) NOT NULL,
    ocena NUMERIC(6) NOT NULL,
    numer_serii NUMERIC(6) NOT NULL,
    id_zgloszenia INTEGER REFERENCES Zgloszenie,
    UNIQUE(numer_serii, id_zgloszenia)
);
CREATE TABLE Konto (
    nazwa_uzytkownika VARCHAR(20) PRIMARY KEY,
    hash_hasla VARCHAR(256) NOT NULL
);
SELECT id_zawodnika,
    imie,
    nazwisko
FROM zawodnik Z
where Z.id_zawodnika not in (
        SELECT id_zawodnika
        FROM zgloszenie
        where id_konkursu = $1
    )
    and (
        select kwota_startowa
        from kwotastartowa KS
        where id_konkursu = $1
            and KS.id_kraju = Z.id_kraju
    ) > (
        select count(id_zawodnika)
        from zgloszenie
        where id_konkursu = $1
            and id_kraju = Z.id_kraju
    )