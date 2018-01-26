-- Lisää INSERT INTO lauseet tähän tiedostoon
INSERT INTO Yritysasiakas (yrityksen_nimi, y_tunnus, osoite, sahkoposti, salasana)
VALUES ('Moi Oy', '123456-7', 'Koodikuja 1, 01011 Helsinki', 'se@on.se', 'passu');

INSERT INTO Olutera (oluen_nimi, valmistuminen, eran_koko, vapaana, hinta)
VALUES ('Pale Ale', NOW(), 100000, 96000, 8);

INSERT INTO Pakkaustyyppi (pakkaustyypin_nimi, vetoisuus, hinta, pantti)
VALUES ('Keg', 2000, 1000, 1000);

INSERT INTO Tilaus (tilausajankohta, toimitusohjeet, olutera_id, yritysasiakas_id)
VALUES (NOW(), 'Soita baarimikolle kun saavut paikalle', 1, 1);

INSERT INTO TilausPakkaustyyppi (tilaus_id, pakkaustyyppi_id, lukumaara)
VALUES (1, 1, 2);