Create TYPE statusK as ENUM ('zgloszenia', 'rozpoczety', 'skoki', 'wyniki');
CREATE TABLE Kraj (
    id_kraju SERIAL PRIMARY KEY,
    nazwa VARCHAR(20) NOT NULL
);
CREATE TABLE Konkurs (
    id_konkursu SERIAL PRIMARY KEY,
    nazwa VARCHAR(40) NOT NULL,
    termin_zgloszen date NOT NULL,
    status_konkursu statusK NOT NULL,
    organizator INTEGER NOT NULL REFERENCES Kraj,
    UNIQUE (nazwa)
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
    id_zawodnika INTEGER NOT NULL REFERENCES Zawodnik
);
CREATE TABLE Skok (
    id_skoku SERIAL PRIMARY KEY,
    odleglosc NUMERIC(6),
    ocena NUMERIC(6),
    numer_serii NUMERIC(6) NOT NULL,
    zdyskwalifikowany BOOLEAN NOT NULL,
    id_zgloszenia INTEGER REFERENCES Zgloszenie,
    numer_startowy NUMERIC(6) NOT NULL,
    UNIQUE(numer_serii, id_zgloszenia)
);
CREATE TABLE Konto (
    nazwa_uzytkownika VARCHAR(20) PRIMARY KEY,
    hash_hasla VARCHAR(256) NOT NULL
);
SELECT s.id_zgloszenia
from skok s
    join zgloszenie zg on s.id_zgloszenia = zg.id_zgloszenia
where zg.id_konkursu = 3
    and (s.odleglosc + s.ocena) in (
        SELECT s.odleglosc + s.ocena
        from skok s
            join zgloszenie zg on zg.id_zgloszenia = s.id_zgloszenia
        where s.numer_serii = $1
            and zg.id_konkursu = 3
            and s.zdyskwalifikowany <> true
        ORDER BY s.odlegosc + s.ocena DESC
        limit 30
    )
ORDER BY s.odleglosc + s.ocena asc