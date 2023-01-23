Create TYPE statusK as ENUM ('zgloszenia', 'rozpoczety', 'skoki', 'wyniki');
create TYPE rodzajSerii as ENUM('kwalifikacyjna', 'pierwsza', 'druga');
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
    id_kraju INTEGER NOT NULL REFERENCES Kraj
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
    seria rodzajSerii NOT NULL,
    zdyskwalifikowany BOOLEAN NOT NULL,
    id_zgloszenia INTEGER REFERENCES Zgloszenie,
    numer_startowy NUMERIC(6) NOT NULL,
    UNIQUE(seria, id_zgloszenia)
);
CREATE TABLE Konto (
    nazwa_uzytkownika VARCHAR(20) PRIMARY KEY,
    hash_hasla VARCHAR(256) NOT NULL
);
CREATE OR REPLACE FUNCTION czyKwalifikacyjna(id_konkurusu INTEGER) RETURNS BOOLEAN AS $$
DECLARE countT INTEGER;
BEGIN
select count(*) into countT
from zgloszenie
where id_konkursu = NEW.id_konkursu;
if countT > 50 then return true;
else return false;
end if;
END;
$$ LANGUAGE plpgsql;
CREATE OR REPLACE FUNCTION sprawdzZgloszenie() RETURNS TRIGGER AS $$
DECLARE statusT statusK;
BEGIN
select status_konkursu into statusT
from konkurs
where id_konkursu = NEW.id_konkursu;
if statusT <> 'zgloszenia' then Raise exception 'Ten konkurs nie przyjmuje juz zgloszen!';
end if;
return NEW;
END;
$$ LANGUAGE plpgsql;
CREATE TRIGGER pilnujZgloszen BEFORE
INSERT ON zgloszenie FOR EACH ROW EXECUTE PROCEDURE sprawdzZgloszenie();