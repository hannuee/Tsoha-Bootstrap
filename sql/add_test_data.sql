-- Lisää INSERT INTO lauseet tähän tiedostoon
INSERT INTO Yritysasiakas (yrityksen_nimi, y_tunnus, osoite, sahkoposti, salasana, tyontekija)
VALUES ('Moi Oy', '123456-7', 'Koodikuja 1, 01011 Helsinki', 'se@on.se', 'passu', 0);
INSERT INTO Yritysasiakas (yrityksen_nimi, y_tunnus, osoite, sahkoposti, salasana, tyontekija)
VALUES ('Pienpanimo', '123456-8', 'Koodikuja 1, 01011 Helsinki', 'admin@pani.mo', 'passu', 1);

INSERT INTO Olutera (oluen_nimi, valmistuminen, eran_koko, vapaana, hinta)
VALUES ('Pale Ale', NOW(), 100000, 96000, 675);
INSERT INTO Olutera (oluen_nimi, valmistuminen, eran_koko, vapaana, hinta)
VALUES ('IPA', NOW(), 80000, 79604, 895);
INSERT INTO Olutera (oluen_nimi, valmistuminen, eran_koko, vapaana, hinta)
VALUES ('Brown Ale', NOW(), 90000, 90000, 775);

INSERT INTO Pakkaustyyppi (pakkaustyypin_nimi, vetoisuus, hinta, pantti)
VALUES ('Keg', 2000, 1000, 1000);
INSERT INTO Pakkaustyyppi (pakkaustyypin_nimi, vetoisuus, hinta, pantti)
VALUES ('Sixpack', 198, 150, 0);

INSERT INTO Tilaus (tilausajankohta, toimitusohjeet, olutera_id, yritysasiakas_id)
VALUES (NOW(), 'Soita baarimikolle kun saavut paikalle', 1, 1);
INSERT INTO Tilaus (tilausajankohta, toimitusohjeet, olutera_id, yritysasiakas_id)
VALUES (NOW(), 'Varattu laadunvalvontaan', 2, 2);

INSERT INTO TilausPakkaustyyppi (tilaus_id, pakkaustyyppi_id, lukumaara)
VALUES (1, 1, 2);
INSERT INTO TilausPakkaustyyppi (tilaus_id, pakkaustyyppi_id, lukumaara)
VALUES (2, 2, 2);