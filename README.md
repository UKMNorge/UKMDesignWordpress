# UKMDesignWordpress
 UKMDesign for Wordpress-tema


Her er det mye å forklare, så vi får ta det litt etter hvert. Feel free to contribute!

## Templates
Wordpress-siden bruker mange ulike templates, som alle er definert i dette temaet.


### Opprett template

#### Opprett en testside
1. Opprett en side i wordpress
2. Legg til "tilpasset felt" på siden <br />
Du finner dette under innstillinger for dokumentet (tannhjul oppe til høyre), og "tilpassede felter" <br />
I feltet `navn` skriver du `UKMviseng`.<br />
I feltet `verdi` skriver du navnet på templaten du vil lage. Dette navnet må være det samme som nøkkel-verdien i punkt 7 (i eksempelet er dette `mitt_nye_template`)
3. Nå har du en testside du kan bruke.

#### Opprett template-filer
4. Lag, eller velg deg en mappe i [Controller/](/Controller/), hvor du oppretter Controller-filen din.
5. I [Views/](/Views/) oppretter du en `.twig.html`-fil. <br />
Filen plasseres i en mappe med samme navn som mappen du puttet controlleren din i.
6. Skriv hei eller noe i template-filen, så du kan se at du har fått opp denne (se standard-template)

#### Registrer template
7. Legg til template i [templates.yml](Environment/Templates/templates.yml)
<br />
Du kan registrere template under `controllers:` for en av følgende keys:

Key | System
--- | --- 
`system`| Systemfunksjoner som innlogging, personvern osv.
`norge` | Brukes på hoved-site'n ukm.no/.
`oppsett` | Disse er tilgjengelige i wordpress, under "Designmal".
`organisasjonen` | Templates for organisasjonssiden (vi bak ukm).
`arrangement` | Templates for arrangement. Typisk kontaktpersoner, program, påmeldte osv.
`statistikk` | De ulike statistikk-sidene våre.
`festivalen` | Templates brukt for den nasjonale festivalen

```yaml
templates:
  system:
    name: Systemfunksjoner
    controllers:
      autologin:
        name: Auto-innlogging fra UKM Delta
        file: Delta/autologin
        description: >
          Skal alltid være plassert på ukm.no/autologin/ og er den som logger inn
          deltakere når de ønsker å logge inn til arrangørsystemet fra UKM Delta.

# FOR EKSEMPEL
      mitt_nye_template:
        name: Et navn du velger selv
        file: Path/Til/Controllerfil/I/Controller-mappen
        description: >
          En hyggelig beskrivelse av hvor det er tenkt brukt, 
          eller hvordan det fungerer.
```

8. [Sjekk syntax](https://www.yamllint.com) på templates.yml, så du er sikker på at denne er innafor.

9. Sjekk ut siden du opprettet i steg 1.


### Standard-template
Vanligvis ønsker vi at template skal extende designet vårt, slik at menyen og footeren automatisk blir lagt til riktig. Derfor vil de fleste templates extende 
[Page/Fullpage.html.twig](https://github.com/UKMNorge/UKMDesign/blob/master/Resources/views/UKMDesign/Page/Page.html.twig).
Hvis du jobber med en template som skal bryte helt ut av det tradisjonelle deisgnet, vil det kunne være aktuelt å extende 
[UKMDesign/Layout/base.html.twig](https://github.com/UKMNorge/UKMDesign/blob/master/Resources/views/UKMDesign/Layout/base.html.twig)
eller 
[UKMDesign/Layout/framework.html.twig](https://github.com/UKMNorge/UKMDesign/blob/master/Resources/views/UKMDesign/Layout/framework.html.twig).


```twig
{% extends "Page/Fullpage.html.twig"|UKMpath %}

{% block page_content %}
	<h1>Wow, nytt template!</h1>
    <p>Og her kommer innholdet</p>

    <!-- Du vil mest sannsynlig bruke en bootstrap-grid -->
    <div class="container">
        <div class="row">
            <div class="col-12">Template i bootstrap-grid</div>
        </div>
    </div>
{% endblock %}

{% block javascripts %}
	{{ parent() }}
	<script type="text/javascript" src="url_til_din_js-fil"></script>
{% endblock %}


{% block css %}
    {{ parent() }}
{% endblock %}
```

# Lansering av nye versjoner av tema

Husk: utplassering først må skje på DEV-miljø og etterpå på produksjon.

## Lansering av nye versjoner av tema på DEV-miljø

UKMDesign:
1. Commit alle endringer på master på https://github.com/UKMNorge/UKMDesign
2. Update pakken `ukmnorge/design` på https://packagist.org
3. Gå på VM gjennom SSH `vagrant ssh main`
4. `cd /var/www/wordpress/wp-content/themes` 
5. `git clone` dette repoet i en ny mappe med navn `UKMDesignWordpress_vX`
6. `cd UKMDesignWordpress_vX`
7. `composer install`
8. `composer update`
9. Skriv navn og versjon av tema i styles.css (for små endringer kan versjon format X.Y.Z brukes. For eks. 5.0.1)
10. Legg til bildet med navn `screenshot.png` som skal brukes som thumbnail (valgfritt)
11. `git commit`. Commit nye endringer i `composer.lock` og `styles.css`
12. `git push`
13. Gjør tema tilgjengelig på https://ukm.dev/wp-admin/network/themes.php
14. Aktiver det på https://ukm.dev/wp-admin/themes.php

Husk å hente riktig branch på assets https://github.com/UKMNorge/UKMDesignAssets. Assets ligger på `/var/www/assets` på VM

## Lansering av nye versjoner av tema på produksjon

OBS: Utplassering på DEV-miljø må skje først!

1. Kjør `git clone` av dette repoet på wordpress/wp-content/themes/
2. Endre navn av mappen til `UKMDesignWordpress_vX` (X betyr versjon)
3. Kjør `cd UKMDesignWordpress_vX`
4. `composer update` (det er sterkt anbefalt å ikke kjøre composer install på produksjon. composer install MÅ kjøres på DEV-miljø og filen composer.lock commites)
5. Gjør tema tilgjengelig på https://ukm.no/wp-admin/network/themes.php
6. Aktiver Tema på https://ukm.no/wp-admin/themes.php

Husk å hente riktig branch på assets https://github.com/UKMNorge/UKMDesignAssets.

VIKTIG: i tilfelle det nye temaet gir feilmelding da kan det gamle temaet aktiveres igjen.
