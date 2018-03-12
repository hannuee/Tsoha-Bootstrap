# Tilaussovellus

## Työn aihe

Tilaussovelluksella pienpanimon yritysasiakkaat(mm. ravintolat ja baarit) voivat tilata helposti ja vaivattomasti pienpanimon tuotteita netin kautta.

Yleisiä linkkejä:

* [Linkki sovellukseeni (Asiakastunnus: se@on.se/passu  Työntekijätunnus: admin@pani.mo/passu)](https://hawerala.users.cs.helsinki.fi/tsoha-Tilaussovellus/kirjautuminen)
* [Linkki dokumentaatiooni](https://github.com/hannuee/Tsoha-Bootstrap/blob/master/doc/dokumentaatio.pdf)

Ominaisuuksia:
* Kaikki mahdolliset turhat tietokantakyselyt karsittu pois, myös view:hin valmiiksi laitettu sisäänkirjautuneen käyttäjän tietojen hakeminen tietokannasta karsittu pois.
* POST ja GET datan laajat tarkastukset ja validoinnit.
* Tietokantaoperaatioiden onnistumisien varsin laajat tarkastukset ja transaktioiden käyttö.

Oluterä CRUD:
* C Työntekijän oluterän luominen.
* R Työntekijän ja Asiakkaan oluterien listaus.
* U Työntekijän oluterän valmistumispäivämäärän muutos.
* D Työntekijän oluterän poistaminen.

Tilaus CRUD:
* C Työntekijän ja Asiakkaan tilausten luominen.
* R Työntekijän oluterän tilausten listaus.
* U Työntekijän tilauksen merkitseminen toimitetuksi.
* D Työntekijän tilauksen poistaminen.

Pakkaustyyppi CRU:
* C Työntekijän pakkaustyypin luominen.
* R Työntekijän pakkaustyyppien listaus ja tilauksen yhteydessä työntekijän ja asiakkaan pakkaustyyppien listaus.
* U Työntekijän pakkaustyypin saatavuuden vaihtaminen.

Yritysasiakas CRU:
* C Työntekijän tunnusten luominen.
* R Työntekijän tunnusten listaus.
* U Työntekijän ja Asiakkaan tunnusten muokkaaminen.
