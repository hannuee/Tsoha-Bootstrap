-- Lisää CREATE TABLE lauseet tähän tiedostoon
CREATE TABLE Yritysasiakas (
    id SERIAL PRIMARY KEY,
    yrityksen_nimi varchar(100) NOT NULL,
    y_tunnus varchar(20),
    osoite varchar(250),
    toimitusosoite varchar(250),
    laskutusosoite varchar(250),
    puhelinnumero varchar(20),
    sahkoposti varchar(100),
    salasana varchar(100) NOT NULL,
    aktiivinen boolean DEFAULT TRUE,
    tyontekija boolean DEFAULT FALSE
);

CREATE TABLE Olutera (
    id SERIAL PRIMARY KEY,
    oluen_nimi varchar(100) NOT NULL, 
    valmistuminen date NOT NULL,
    eran_koko INTEGER NOT NULL,
    vapaana INTEGER NOT NULL,
    hinta INTEGER NOT NULL
);

CREATE TABLE Pakkaustyyppi (
    id SERIAL PRIMARY KEY,
    pakkaustyypin_nimi varchar(100) NOT NULL, 
    vetoisuus INTEGER NOT NULL,
    hinta INTEGER NOT NULL,
    pantti INTEGER NOT NULL,
    saatavilla boolean DEFAULT TRUE
);

CREATE TABLE Tilaus (
    id SERIAL PRIMARY KEY,
    tilausajankohta date,
    toimitettu boolean DEFAULT FALSE,
    toimitusohjeet text,
    olutera_id INTEGER REFERENCES Olutera(id) ON DELETE CASCADE,
    yritysasiakas_id INTEGER REFERENCES Yritysasiakas(id)
);

CREATE TABLE TilausPakkaustyyppi (
    tilaus_id INTEGER REFERENCES Tilaus(id) ON DELETE CASCADE,
    pakkaustyyppi_id INTEGER REFERENCES Pakkaustyyppi(id),
    lukumaara INTEGER NOT NULL,
    PRIMARY KEY (tilaus_id, pakkaustyyppi_id)
);
